<?php

namespace CultuurNet\MediaDownloadManager\DestinationSystem;

use ValueObjects\Web\Url;

interface DestinationSystemInterface
{
    /**
     * @param Url $url
     */
    public function saveFile(Url $url);
}
