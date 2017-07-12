<?php

namespace CultuurNet\MediaDownloadManager\DestinationSystem;

use CultuurNet\MediaDownloadManager\Download\DownloaderInterface;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Filesystem;
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
     * DestinationSystem constructor.
     * @param DownloaderInterface $downloader
     * @param StringLiteral $defaultFolder
     * @param array $adaptors
     */
    public function __construct(
        DownloaderInterface $downloader,
        StringLiteral $defaultFolder,
        array $adaptors
    ) {
        $this->downloader = $downloader;
        $this->defaultFolder = $defaultFolder;
        $this->adaptors = array();
        foreach ($adaptors as $adaptor) {
            if ($adaptor instanceof AbstractAdapter) {
                array_push($this->adaptors, $adaptor);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function saveFile(Url $url, StringLiteral $destination)
    {
        if (!empty($this->defaultFolder)) {
            $destination = new StringLiteral($this->defaultFolder . '/' . $destination);
        }
        foreach ($this->adaptors as $adaptor) {
            $putStream = $this->downloader->fetchStreamFromHttp($url);
            $filesystem = new Filesystem($adaptor);
            $filesystem->putStream($destination->toNative(), $putStream);
            if (is_resource($putStream)) {
                fclose($putStream);
            }
        }
        // usleep(1500000);
    }
}
