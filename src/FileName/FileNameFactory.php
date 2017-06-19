<?php

namespace CultuurNet\MediaDownloadManager\FileName;

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
    ) {
        $extension = $this->generateExtension($originalFileName);

        if (strlen($itemName) >= 100) {
            $itemName = StringLiteral::fromNative(substr($itemName->toNative(), 0, 100));
        }
        if (strlen($copyright) >= 100) {
            $copyright = StringLiteral::fromNative(substr($copyright->toNative(), 0, 100));
        }

        return StringLiteral::fromNative($itemName . ' - ' . $zipCode . ' - ' . $copyright . $extension);
    }

    /**
     * @inheritdoc
     */
    public function generateExtension($originalFileName)
    {
        return '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
    }
}
