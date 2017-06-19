<?php

namespace CultuurNet\MediaDownloadManager\DestinationSystem;

use CultuurNet\MediaDownloadManager\FileName\FileNameFactoryInterface;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Filesystem;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\Url;

class DestinationSystem implements DestinationSystemInterface
{

    /**
     * @var FileNameFactoryInterface
     */
    protected $fileNameFactory;

    /**
     * @var AbstractAdapter
     */
    protected $adaptor;

    /**
     * DestinationSystem constructor.
     * @param FileNameFactoryInterface $fileNameFactory
     * @param AbstractAdapter $adaptor
     */
    public function __construct(
        FileNameFactoryInterface $fileNameFactory,
        AbstractAdapter $adaptor
    ) {
        $this->fileNameFactory = $fileNameFactory;
        $this->adaptor = $adaptor;
    }

    /**
     * @inheritdoc
     */
    public function saveFile(Url $url)
    {

    }
}
