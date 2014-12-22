<?php
namespace ngyuki\ComposerCurlPlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PreFileDownloadEvent;
use Composer\EventDispatcher\EventSubscriberInterface;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @var CurlClient
     */
    protected $curlClient;

    /**
     * @var array
     */
    protected $hosts = array('packagist.org');

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->curlClient = new CurlClient(self::generateUserAgent());

        if ($this->io->isVerbose()) {
            $this->io->write("<info>[Curl]</info> plugin activate");
        }

        $pluginConfig = $this->composer->getConfig()->get('curl-plugin');

        if (isset($pluginConfig['hosts']) && is_array($pluginConfig['hosts'])) {
            $this->hosts = array_merge($this->hosts, $pluginConfig['hosts']);
        }
    }

    /**
     * @return string
     * @see \Composer\Util\RemoteFilesystem::getOptionsForUrl()
     */
    public static function generateUserAgent()
    {
        $version  = curl_version();

        $userAgent = sprintf('Composer/%s (%s; %s; PHP %s.%s.%s; Curl: %s)',
            Composer::VERSION === '@package_version@' ? 'source' : Composer::VERSION,
            php_uname('s'),
            php_uname('r'),
            PHP_MAJOR_VERSION,
            PHP_MINOR_VERSION,
            PHP_RELEASE_VERSION,
            $version['version']
        );

        return $userAgent;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            PluginEvents::PRE_FILE_DOWNLOAD => array(
                array('onPreFileDownload', 0)
            ),
        );
    }

    public function onPreFileDownload(PreFileDownloadEvent $event)
    {
        $url = $event->getProcessedUrl();
        $host = parse_url($url, PHP_URL_HOST);
        $protocol = parse_url($url, PHP_URL_SCHEME);

        if (in_array($host, $this->hosts, true) && ($protocol === 'http' || $protocol === 'https')) {
            $orig = $event->getRemoteFilesystem();
            $curl = new CurlRemoteFilesystem(
                $this->curlClient,
                $this->io,
                $this->composer->getConfig(),
                $orig->getOptions()
            );
            $event->setRemoteFilesystem($curl);
        }
    }
}
