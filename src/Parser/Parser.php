<?php

namespace CultuurNet\MediaDownloadManager\Parser;

use CultuurNet\MediaDownloadManager\DestinationSystem\DestinationSystemInterface;
use CultuurNet\MediaDownloadManager\Fetcher\FetcherInterface;
use CultuurNet\MediaDownloadManager\FileName\FileNameFactoryInterface;
use CultuurNet\MediaDownloadManager\OriginSystem\OriginSystemInterface;
use Monolog\Logger;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\Url;

class Parser implements ParserInterface
{

    /**
     * @var FetcherInterface
     */
    protected $fetcher;

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
     * @var Logger
     */
    protected $logger;

    /**
     * @inheritdoc
     */
    public function start($label = null, $createdSince = null)
    {
        $debugMessage = 'label is ';
        $debugMessage .= isset($label) ? $label : 'NULL';
        $debugMessage .= ' createdSince is ';
        $debugMessage .= isset($createdSince) ? $createdSince : 'NULL';
        $this->logger->log(Logger::DEBUG, $debugMessage);

        $nativeUrl = Url::fromNative($this->originSystem->getSearchUrl($label, $createdSince));
        $this->logger->log(Logger::DEBUG, (string) $nativeUrl);

        $results =  $this->fetcher->getEvents((string) $nativeUrl);

        $itemsPerPage = $results['itemsPerPage'];
        $totalItems = $results['totalItems'];
        $this->logger->log(Logger::DEBUG, 'Found ' . $totalItems . ' events');

        // temp solution until I figure out why pagination does not work.
        if ($totalItems > $itemsPerPage) {
            $limit = $totalItems;
        } else {
            $limit = 30;
        }

        $start = 0;
        while ($start < $totalItems) {
            $paginatedSearchUrl = (string) $this->originSystem->getSearchUrl($label, $createdSince) . 'start=' . $start . '&limit=' . $limit;
            $results = $this->fetcher->getEvents($paginatedSearchUrl);

            $this->processResults($results);

            $start = $start + $itemsPerPage;
        }
    }

    /**
     * Parser constructor.
     * @param FetcherInterface $fetcher
     * @param FileNameFactoryInterface $fileNameFactory
     * @param OriginSystemInterface $originSystem
     * @param DestinationSystemInterface $destinationSystem
     * @param Logger $logger
     */
    public function __construct(
        FetcherInterface $fetcher,
        FileNameFactoryInterface $fileNameFactory,
        OriginSystemInterface $originSystem,
        DestinationSystemInterface $destinationSystem,
        Logger $logger
    ) {
        $this->fetcher = $fetcher;
        $this->fileNameFactory = $fileNameFactory;
        $this->originSystem = $originSystem;
        $this->destinationSystem = $destinationSystem;
        $this->logger = $logger;
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
