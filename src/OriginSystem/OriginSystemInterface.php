<?php

namespace CultuurNet\MediaDownloadManager\OriginSystem;

use ValueObjects\Web\Url;

interface OriginSystemInterface
{
    /**
     * @param $label
     * @param $createdFrom
     * @return Url
     */
    public function getSearchUrl($label, $createdFrom);
}
