<?php

namespace josegonzalez\Queuesadilla\Job;

use JsonSerializable;

class Base implements JsonSerializable
{
    const LOW = 4;
    const NORMAL = 3;
    const MEDIUM = 2;
    const HIGH = 1;
    const CRITICAL = 0;

    protected $engine;

    protected $item;

    public function __construct($item, $engine)
    {
        $this->engine = $engine;
        $this->item = $item;
        return $this;
    }

    public function attempts()
    {
        if (array_key_exists('attempts', $this->item)) {
            return $this->item['attempts'];
        }

        return $this->item['attempts'] = 0;
    }

    public function data($key = null, $default = null)
    {
        if ($key === null) {
            return $this->item['args'][0];
        }

        if (array_key_exists($key, $this->item['args'][0])) {
            return $this->item['args'][0][$key];
        }

        return $default;
    }

    public function delete()
    {
        return $this->engine->delete($this->item);
    }

    public function item()
    {
        return $this->item;
    }

    public function release($delay = 0)
    {
        if (!isset($this->item['attempts'])) {
            $this->item['attempts'] = 0;
        }

        $this->item['attempts'] += 1;
        $this->item['delay'] = $delay;
        return $this->engine->release($this->item);
    }

    public function __toString()
    {
        return $this->item;
    }

    public function jsonSerialize()
    {
        return $this->item;
    }
}
