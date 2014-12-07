<?php
namespace ngyuki\ComposerCurlPlugin;

use Composer\Util\RemoteFilesystem;
use Composer\IO\IOInterface;
use Composer\Config;
use Composer\Downloader\TransportException;

class CurlRemoteFilesystem extends RemoteFilesystem
{
    private $curlClient;
    private $io;

    /**
     * {@inheritdoc}
     */
    public function __construct(CurlClient $curlClient, IOInterface $io, Config $config = null, array $options = array())
    {
        parent::__construct($io, $config, $options);

        $this->curlClient = $curlClient;
        $this->io = $io;
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
        if ($this->io->isDebug()) {
            $this->io->write("<info>[Curl]</info> Downloading $url");
        }

        list ($code, $headers, $body) = $this->curlClient->get($url);

        if ($code != 200) {
            if (isset($headers[0])) {
                $message = $headers[0];
            } else {
                $message = "Unknown error";
            }
            $ex = new TransportException("Unable download $url ... $message");
            $ex->setHeaders($headers);
            $ex->setResponse($body);
            throw $ex;
        }

        return $body;
    }
}
