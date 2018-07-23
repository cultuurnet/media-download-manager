<?php

namespace CultuurNet\MediaDownloadManager\Fetcher;

use Guzzle\Http\Client;
use Monolog\Logger;

class Fetcher implements FetcherInterface
{
    /**
     * @var
     */
    private $apiKey;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Fetcher constructor.
     * @param $apiKey
     * @param Logger $logger
     */
    public function __construct($apiKey, Logger $logger)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;

    }

    /**
     * @param $url
     * @return array
     */
    public function getEvents($url)
    {
        $this->logger->log(Logger::DEBUG, $url);
        $client = new Client();
        $this->logger->log(Logger::DEBUG, 'one');
        $request = $client->get(
            $url,
            [
                'content-type' => 'application/json',
                'x-api-key' => $this->apiKey,
            ],
            []
        );
        $this->logger->log(Logger::DEBUG, 'two');

        $response = $request->send();
        $this->logger->log(Logger::DEBUG, 'three');
        $body = $response->getBody();
        $this->logger->log(Logger::DEBUG, $body);

        $eventList = json_decode($body, true);
        return $eventList;
    }
}
