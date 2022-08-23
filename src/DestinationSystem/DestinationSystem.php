<?php

namespace CultuurNet\MediaDownloadManager\DestinationSystem;

use CultuurNet\MediaDownloadManager\Download\DownloaderInterface;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Filesystem;
use Monolog\Logger;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\Url;

class DestinationSystem implements DestinationSystemInterface
{
    /**
     * @var DownloaderInterface
     */
    protected $downloader;

    /**
     * @var StringLiteral
     */
    protected $defaultFolder;

    /**
     * @var array
     */
    protected $adaptors;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * DestinationSystem constructor.
     * @param DownloaderInterface $downloader
     * @param StringLiteral $defaultFolder
     * @param array $adaptors
     * @param Logger $logger
     */
    public function __construct(
        DownloaderInterface $downloader,
        StringLiteral $defaultFolder,
        array $adaptors,
        Logger $logger
    ) {
        $this->downloader = $downloader;
        $this->defaultFolder = $defaultFolder;
        $this->adaptors = array();
        foreach ($adaptors as $adaptor) {
            if ($adaptor instanceof AbstractAdapter) {
                array_push($this->adaptors, $adaptor);
            }
        }
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function saveFile(Url $url, StringLiteral $destination)
    {
        $url = $this->refactorUrl($url);
        $this->logger->log(Logger::DEBUG, $destination->toNative());
        if (!empty($this->defaultFolder)) {
            $destination = new StringLiteral($this->defaultFolder . '/' . $destination);
        }
        foreach ($this->adaptors as $adaptor) {
            try {
                $filesystem = new Filesystem($adaptor);
                if (!$filesystem->has($destination)) {
                    $putStream = $this->downloader->fetchStreamFromHttp($url);
                    $filesystem->putStream($destination->toNative(), $putStream);
                    if (is_resource($putStream)) {
                        fclose($putStream);
                    }
                }
            } catch (\Exception $exception) {
                echo $exception;
            }
        }
        usleep(1000000);
    }

    /**
     * @param Url $url
     * @return Url
     */
    private function refactorUrl(Url $url)
    {
        $urlString = (string) $url;
        $urlString = str_replace(
            'https://images.uitdatabank.be/',
            'https://images-prod-uitdatabank.imgix.net/',
            $urlString
        );
        return Url::fromNative($urlString);
    }
}
