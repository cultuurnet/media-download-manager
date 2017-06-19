<?php

namespace CultuurNet\MediaDownloadManager\Download;

use League\Flysystem\Filesystem;
use Twistor\Flysystem\Http\HttpAdapter;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\Url;

class Downloader implements DownloaderInterface
{
    /**
     * @inheritdoc
     */
    public function fetchStreamFromHttp(Url $url)
    {
        $baseLink = self::getBaseLink($url);
        $httpAdapter = new HttpAdapter((string) $baseLink);
        $httpSystem = new Filesystem($httpAdapter);
        $stream = $httpSystem->readStream($url->getPath()->toNative());

        return $stream;
    }

    /**
     * @param Url $url
     * @return StringLiteral
     */
    private function getBaseLink(Url $url){
        return new StringLiteral($url->getScheme() . '://' . $url->getDomain());
    }
}
