<?php

namespace CultuurNet\MediaDownloadManager\DestinationSystem;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\Url;

interface DestinationSystemInterface
{
    /**
     * @param Url $url
     * @param StringLiteral $destination
     * @return void
     */
    public function saveFile(Url $url, StringLiteral $destination);
}
