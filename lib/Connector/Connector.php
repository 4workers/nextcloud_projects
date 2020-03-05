<?php

declare(strict_types=1);

namespace OCA\Projects\Connector;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use OCA\NextcloudConnectorSync\Event;

class Connector
{

    private $connection;

    public function __construct(Client $connection)
    {
        $this->connection = $connection;
    }

    public function send(\OCA\Projects\Connector\Event\General $event)
    {
        if ($event->type()) {
            $this->connection->request(
                'POST', '', [
                RequestOptions::JSON => $event->toArray()
                ]
            );
        }
    }
}