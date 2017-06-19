<?php

namespace CultuurNet\MediaDownloadManager\Parser;

use CultuurNet\MediaDownloadManager\OriginSystem\OriginSystemInterface;
use ValueObjects\Web\Url;

class Parser implements ParserInterface
{
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
        if ($ti > $ipp) {

        }
    }

    /**
     * Parser constructor.
     * @param OriginSystemInterface $originSystem
     */
    public function __construct(OriginSystemInterface $originSystem)
    {
        $this->originSystem = $originSystem;
    }
}
