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
     * @inheritdoc
     */
    public function getSearchUrl($label, $createdFrom)
    {
        $urlString = $this->baseUrl . '/?';
        $parameterString = '';
        foreach ($this->parameters as $key => $value) {
            if ($key == 'q') {
                $value = 'labels:"' . (isset($label) ? $label : $value) . '"';
            }
            if ($key == 'createdFrom' && isset($createdFrom)) {
                $value = $createdFrom . 'T00:00:00+02:00';
            }
            if ($key == 'createdFrom' && !isset($createdFrom)) {
                $value = $value . 'T00:00:00+02:00';
            }
            $parameterString .= $key . '=' . urlencode($value) . '&';
        }
        return Url::fromNative($urlString . $parameterString);
    }
}
