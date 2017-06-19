<?php

namespace CultuurNet\MediaDownloadManager\Parser;

use CultuurNet\MediaDownloadManager\DestinationSystem\DestinationSystemInterface;
use CultuurNet\MediaDownloadManager\FileName\FileNameFactoryInterface;
use CultuurNet\MediaDownloadManager\OriginSystem\OriginSystemInterface;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\Url;

class Parser implements ParserInterface
{
    /**
     * @var FileNameFactoryInterface
     */
    protected $fileNameFactory;

    /**
     * @var OriginSystemInterface
     */
    protected $originSystem;

    /**
     * @var DestinationSystemInterface
     */
    protected $destinationSystem;

    /**
     * @inheritdoc
     */
    public function start()
    {
        $contents = file_get_contents(Url::fromNative($this->originSystem->getSearchUrl()));
        $contents = utf8_encode($contents);
        $results = json_decode($contents, true);

        $itemsPerPage = 30;
        $totalItems = 95;

        $start = 0;
        while ($start < $totalItems) {
            $contents = file_get_contents(Url::fromNative($this->originSystem->getSearchUrl() . 'start=' . $start));
            $contents = utf8_encode($contents);
            $results = json_decode($contents, true);

            $this->processResults($results);

            $start = $start + $itemsPerPage;
        }

    }

    /**
     * Parser constructor.
     * @param FileNameFactoryInterface $fileNameFactory
     * @param OriginSystemInterface $originSystem
     * @param DestinationSystemInterface $destinationSystem
     */
    public function __construct(
        FileNameFactoryInterface $fileNameFactory,
        OriginSystemInterface $originSystem,
        DestinationSystemInterface $destinationSystem
    ) {
        $this->fileNameFactory = $fileNameFactory;
        $this->originSystem = $originSystem;
        $this->destinationSystem = $destinationSystem;
    }

    /**
     * @param array $results
     */
    private function processResults(array $results)
    {
        foreach ($results['member'] as $member) {
            $name = $member['name']['nl'];
            $postalCode = $member['location']['address']['postalCode'];
            if ($member['mediaObject']) {
                foreach ($member['mediaObject'] as $media) {
                    $contentUrl = $media['contentUrl'];
                    $copyrightHolder = $media['copyrightHolder'];
                    $fileName = $this->fileNameFactory->generateFileName(
                        new StringLiteral($contentUrl),
                        new StringLiteral($name),
                        new StringLiteral($postalCode),
                        new StringLiteral($copyrightHolder)
                    );
                    $this->destinationSystem->saveFile(
                        Url::fromNative($contentUrl),
                        $fileName
                    );
                }
            }
        }
    }
}
