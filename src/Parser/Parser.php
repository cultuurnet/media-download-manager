<?php

namespace CultuurNet\MediaDownloadManager\Parser;

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
     * @var ParserInterface
     */
    protected $originSystem;

    /**
     * @inheritdoc
     */
    public function start()
    {
        $contents = file_get_contents(Url::fromNative($this->originSystem->getSearchUrl()));
        $contents = utf8_encode($contents);
        $results = json_decode($contents, true);

        $ipp = $results['itemsPerPage'];
        $ti = $results['totalItems'];
        if ($ti < $ipp) {
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
                        echo $fileName;
                    }
                }
            }
        }
    }

    /**
     * Parser constructor.
     * @param FileNameFactoryInterface $fileNameFactory
     * @param OriginSystemInterface $originSystem
     */
    public function __construct(
        FileNameFactoryInterface $fileNameFactory,
        OriginSystemInterface $originSystem
    ) {
        $this->fileNameFactory = $fileNameFactory;
        $this->originSystem = $originSystem;
    }
}
