<?php

namespace CultuurNet\MediaDownloadManager\Parser;

interface ParserInterface
{
    /**
     * Starts the parser
     * @param string | null $label
     * @return
     */
    public function start($label = null);
}
