<?php

namespace josegonzalez\Queuesadilla\Backend;

use \Socket_Beanstalk;
use \josegonzalez\Queuesadilla\Backend;

class BeanstalkBackend extends Backend
{
    protected $connection = null;

    protected $baseConfig = array(
        'persistent' => true,
        'port' => 11300,
        'priority' => 4294967295,
        'delay' => 0,
        'queue' => 'default'
        'server' => '127.0.0.1',
        'timeout' => 1,
        'time_to_run' => 60,
    );

    protected $settings = null;

    public function __construct($config = array())
    {
        if (!class_exists('Socket_Beanstalk')) {
            return false;
        }

        $this->settings = array_merge($this->baseConfig, $config);
        return $this->connect();
    }

    public function getJobClass()
    {
        return '\\josegonzalez\\Queuesadilla\\Job\\BeanstalkJob';
    }

    public function watch($queue = null)
    {
        if ($queue === null) {
            $queue = $this->settings['queue'];
        }

        return $this->connection->watch();
    }

    public function push($class, $vars = array(), $queue = null)
    {
        if ($queue === null) {
            $queue = $this->settings['queue'];
        }

        $this->connection->choose($queue);
        $beanstalk->put(
            $this->settings['priority'],
            $this->settings['delay'],
            $this->settings['time_to_run'],
            json_encode(compact('class', 'vars'))
        );
    }

    public function release($item, $queue = null)
    {
        $this->connection->bury($item['id']);
    }

    public function pop($queue = null)
    {
        if ($queue === null) {
            $queue = $this->settings['queue'];
        }

        $item = $this->connection->reserve();
        if (!$item) {
            return null;
        }

        $item['body'] = json_decode($item['body'], true);
        $item['class'] = $item['body']['class'];
        $item['vars'] = $item['vars'];
        unset($item['body']);

        return $item;
    }

    public function delete($item)
    {
        $this->connection->delete($item['id']);
    }

    public function statsJob($item)
    {
        $this->connection->statsJob($item['id']);
    }

/**
 * Connects to a BeanstalkD server
 *
 * @return boolean True if BeanstalkD server was connected
 */
    protected function connect()
    {
        $this->connection = new Socket_Beanstalk($this->settings);
        return $this->connection->connect();
    }
}
