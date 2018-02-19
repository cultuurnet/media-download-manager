<?php

namespace CultuurNet\MediaDownloadManager\Fetcher;

use Guzzle\Http\Client;

class Fetcher implements FetcherInterface
{
    /**
     * @var
     */
    private $apiKey;

    /**
     * Fetcher constructor.
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param $url
     * @return array
     */
    public function getEvents($url)
    {
        $client = new Client();
        $request = $client->get(
            $url,
            [
                'content-type' => 'application/json',
                'x-api-key' => $this->apiKey,
            ],
            []
        );

        $response = $request->send();

        $body = $response->getBody();

        $eventList = json_decode($body, true);
        return $eventList;
    }
}
