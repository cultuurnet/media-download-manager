<?php

namespace CultuurNet\MediaDownloadManager\Download;

use ValueObjects\Web\Url;

interface DownloaderInterface
{
    /**
     * @param Url $url
     * @return resource|false The path resource or false on failure
     */
    public function fetchStreamFromHttp(Url $url);
}
