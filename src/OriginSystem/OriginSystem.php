<?php

namespace CultuurNet\MediaDownloadManager\OriginSystem;

use ValueObjects\Web\Url;

class OriginSystem implements OriginSystemInterface
{

    /**
     * @var Url
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * OriginSystem constructor.
     * @param Url $baseUrl
     * @param array $parameters
     */
    public function __construct(Url $baseUrl, array $parameters)
    {
        $this->baseUrl = $baseUrl;
        $this->parameters = $parameters;
    }

    /**
     * @return Url
     */
    public function getSearchUrl()
    {
        $urlString = $this->baseUrl . '/?';
        $parameterString = '';
        foreach ($this->parameters as $key => $value) {
            $parameterString .= $key . '=' . $value . '&';
        }
        return Url::fromNative($urlString . $parameterString);
    }
}
