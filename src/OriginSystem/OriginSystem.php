<?php

namespace CultuurNet\MediaDownloadManager\OriginSystem;

use ValueObjects\Number\Integer as IntegerLiteral;

class OriginSystem implements OriginSystemInterface
{

    /**
     * OriginSystem constructor.
     * @param IntegerLiteral $limit
     */
    public function __construct(IntegerLiteral $limit)
    {
    }
}
