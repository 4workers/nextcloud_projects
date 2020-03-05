<?php

declare(strict_types=1);

namespace OCA\Projects\Connector\Event;

class General
{

    /**
     * @var string
     */
    private $type;
    /**
     * @var array
     */
    private $params;

    public function __construct(string $type, array $params)
    {
        $this->type = $type;
        $this->params = $params;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'params' => $this->params()
        ];
    }

    public function type()
    {
        return $this->type;
    }

    public function params()
    {
        return $this->params;
    }

}