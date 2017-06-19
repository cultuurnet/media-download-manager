<?php

namespace CultuurNet\MediaDownloadManager\FileName;

use ValueObjects\StringLiteral\StringLiteral;

interface FileNameFactoryInterface
{
    /**
     * @param StringLiteral $originalFileName
     * @param StringLiteral $itemName
     * @param StringLiteral $zipCode
     * @param StringLiteral $copyright
     * @return StringLiteral
     */
    public function generateFileName(
        StringLiteral $originalFileName,
        StringLiteral $itemName,
        StringLiteral $zipCode,
        StringLiteral $copyright
    );

    /**
     * @param $originalFileName
     * @return StringLiteral
     */
    public function generateExtension($originalFileName);
}
