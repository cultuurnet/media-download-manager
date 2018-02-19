<?php

namespace CultuurNet\MediaDownloadManager\Fetcher;

interface FetcherInterface
{
    /**
     * @param $url
     * @return array
     */
    public function getEvents($url);
}
