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
    public function start($label = null)
    {
        $nativeUrl = Url::fromNative($this->originSystem->getSearchUrl());
        if ($label) {
            $nativeUrl = str_replace('owner-omd-2107', $label, $nativeUrl);
        }

        $contents = file_get_contents($nativeUrl);
        $contents = utf8_encode($contents);
        $results = json_decode($contents, true);

        $itemsPerPage = $results['itemsPerPage'];
        $totalItems = $results['totalItems'];

        // temp solution until I figure out why pagination does not work.
        if ($totalItems > $itemsPerPage) {
            $limit = $totalItems;
        } else {
            $limit = 30;
        }

        $start = 0;
        while ($start < $totalItems) {
            $paginatedSearchUrl = (string) $this->originSystem->getSearchUrl() . 'start=' . $start . '&limit=' . $limit;
            $contents = file_get_contents($paginatedSearchUrl);
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

            if ($member['@context'] == '/contexts/event') {
                if (isset($member['location']['address']['postalCode'])) {
                    $postalCode = $member['location']['address']['postalCode'];
                } elseif (isset($member['location']['address']['nl']['postalCode'])) {
                    $postalCode = $member['location']['address']['nl']['postalCode'];
                } else {
                    $postalCode = 'NULL';
                }
            } elseif ($member['@context'] == '/contexts/place') {
                if (isset($member['address']['postalCode'])) {
                    $postalCode = $member['address']['postalCode'];
                } elseif (isset($member['address']['nl']['postalCode'])) {
                    $postalCode = $member['address']['nl']['postalCode'];
                } else {
                    $postalCode = 'NULL';
                }
            }

            if (isset($member['mediaObject'])) {
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
