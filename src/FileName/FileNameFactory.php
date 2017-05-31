<?php

namespace CultuurNet\MediaDownloadManager\File;

use ValueObjects\StringLiteral\StringLiteral;

class FileNameFactory implements FileNameFactoryInterface
{

    /**
     * @inheritdoc
     */
    public function generateFileName(
        StringLiteral $originalFileName,
        StringLiteral $itemName,
        StringLiteral $zipCode,
        StringLiteral $copyright
    )
    {

    }
}
