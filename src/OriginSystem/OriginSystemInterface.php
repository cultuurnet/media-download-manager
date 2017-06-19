<?php

namespace CultuurNet\MediaDownloadManager\OriginSystem;

use ValueObjects\Web\Url;

interface OriginSystemInterface
{
    /**
     * @return Url
     */
    public function getSearchUrl();
}
