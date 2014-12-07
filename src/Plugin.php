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
     * @var CurlRemoteFilesystem
     */
    protected $curl;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->curl = new CurlRemoteFilesystem($this->io);

        if ($this->io->isVerbose()) {
            $this->io->write("<info>[Curl]</info> plugin activate");
        }
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

        if ($host === 'packagist.org' && ($protocol === 'http' || $protocol === 'https')) {
            //$orig = $event->getRemoteFilesystem();
            $event->setRemoteFilesystem($this->curl);
            if ($this->io->isDebug()) {
                $this->io->write("<info>[Curl]</info> Switch curl \"$url\"");
            }
        }
    }
}
