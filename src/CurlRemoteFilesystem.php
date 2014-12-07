<?php
namespace ngyuki\ComposerCurlPlugin;

use Composer\Composer;
use Composer\Downloader\TransportException;
use Composer\Util\RemoteFilesystem;
use Composer\IO\IOInterface;
use Composer\Config;

class CurlRemoteFilesystem extends RemoteFilesystem
{
    private $io;
    private $config;
    private $options;
    private $lastHeaders = array();

    protected $curl;

    /**
     * {@inheritdoc}
     */
    public function __construct(IOInterface $io, Config $config = null, array $options = array())
    {
        parent::__construct($io, $config, $options);

        $this->io = $io;
        $this->config = $config;
        $this->options = $options;

        $this->curl = curl_init();

        $version  = curl_version();

        // @see \Composer\Util\RemoteFilesystem::getOptionsForUrl()
        $userAgent = sprintf('Composer/%s (%s; %s; PHP %s.%s.%s; Curl: %s)',
            Composer::VERSION === '@package_version@' ? 'source' : Composer::VERSION,
            php_uname('s'),
            php_uname('r'),
            PHP_MAJOR_VERSION,
            PHP_MINOR_VERSION,
            PHP_RELEASE_VERSION,
            $version['version']
        );

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            //CURLOPT_FAILONERROR => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_ENCODING => "",
        );

        curl_setopt_array($this->curl, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents($originUrl, $fileUrl, $progress = true, $options = array())
    {
        return $this->curlGet($fileUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function copy($originUrl, $fileUrl, $fileName, $progress = true, $options = array())
    {
        $body = $this->curlGet($fileUrl);
        if ($body === false) {
            return false;
        }
        file_put_contents($fileName, $body);
        return true;
    }

    /**
     * @param string $url
     * @return string
     */
    private function curlGet($url)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);

        if ($this->io->isDebug()) {
            $this->io->write("<info>[Curl]</info> Downloading $url");
        }

        $resp = curl_exec($this->curl);

        if ($resp === false) {
            throw new \RuntimeException("Unable download \"$url\"");
        }

        list ($header, $body) = explode("\r\n\r\n", $resp, 2);
        $headers = explode("\r\n", $header);

        $info = curl_getinfo($this->curl);
        if ($info['http_code'] != 200) {
            if (isset($headers[0])) {
                $message = $headers[0];
            } else {
                $message = "Unknown error";
            }
            $ex = new TransportException("Unable download \"$url\" ... $message");
            $ex->setHeaders($headers);
            $ex->setResponse($body);
            throw $ex;
        }

        $this->lastHeaders = $headers;
        return $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastHeaders()
    {
        return $this->lastHeaders;
    }
}
