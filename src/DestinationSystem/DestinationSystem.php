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
     * @var AbstractAdapter
     */
    protected $adaptor;

    /**
     * DestinationSystem constructor.
     * @param DownloaderInterface $downloader
     * @param AbstractAdapter $adaptor
     */
    public function __construct(
        DownloaderInterface $downloader,
        AbstractAdapter $adaptor
    ) {
        $this->downloader = $downloader;
        $this->adaptor = $adaptor;
    }

    /**
     * @inheritdoc
     */
    public function saveFile(Url $url, StringLiteral $destination)
    {
        $putStream = $this->downloader->fetchStreamFromHttp($url);
        $filesystem = new Filesystem($this->adaptor);
        $filesystem->putStream($destination->toNative(), $putStream);
        if (is_resource($putStream)) {
            fclose($putStream);
        }
    }
}
