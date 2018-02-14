<?php

namespace CultuurNet\MediaDownloadManager\Parser;

interface ParserInterface
{
    /**
     * Starts the parser
     * @param string | null $label
     * @param string | null $createdSince
     * @return void
     */
    public function start($label = null, $createdSince = null);
}
