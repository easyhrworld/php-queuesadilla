<?php

namespace josegonzalez\Queuesadilla\Worker;

use Exception;
use josegonzalez\Queuesadilla\Worker\Base;

class CISequentialWorker extends Base
{

    protected $cig;

    /**
     * {@inheritDoc}
     */
    public function work()
    {
        if (!$this->connect()) {
            $this->logger()->alert(sprintf('Worker unable to connect, exiting'));
            $this->dispatchEvent('Worker.job.connectionFailed');
            return false;
        }

        $jobClass = $this->engine->getJobClass();
        while (true) {
            if (is_int($this->maxIterations) && $this->iterations >= $this->maxIterations) {
                $this->logger()->debug('Max iterations reached, exiting');
                $this->dispatchEvent('Worker.maxIterations');
                break;
            }

            $this->iterations++;
            $item = $this->engine->pop($this->queue);
            $this->dispatchEvent('Worker.job.seen', ['item' => $item]);
            if (empty($item)) {
                $this->logger()->debug('No job!');
                $this->dispatchEvent('Worker.job.empty');
                sleep(1);
                continue;
            }

            $success = false;
            $this->cig->load->library($item['class'][0]);
            $job = new $jobClass($item, $this->engine);
            if (!is_callable($item['class'], true)) {
                $this->logger()->alert('Invalid callable for job. Rejecting job from queue.');
                $job->reject();
                $this->dispatchEvent('Worker.job.invalid', ['job' => $job]);
                continue;
            }

            try {
                $success = $this->perform($item);
            } catch (Exception $e) {
                $this->logger()->alert(sprintf('Exception: "%s"', $e->getMessage()));
                $this->dispatchEvent('Worker.job.exception', [
                    'job' => $job,
                    'exception' => $e,
                ]);
            }

            if ($success) {
                $this->logger()->debug('Success. Acknowledging job on queue.');
                $job->acknowledge();
                $this->dispatchEvent('Worker.job.success', ['job' => $job]);
                continue;
            }

            $this->logger()->info('Failed. Releasing job to queue');
            $job->release();
            $this->dispatchEvent('Worker.job.failure', ['job' => $job]);
        }

        return true;
    }

    public function connect()
    {
        // Connect to Codeigniter
        $this->cig =& get_instance();

        $maxIterations = $this->maxIterations ? sprintf(', max iterations %s', $this->maxIterations) : '';
        $this->logger()->info(sprintf('Starting worker%s', $maxIterations));
        return (bool)$this->engine->connection();
    }

    public function perform($item)
    {
        $success = false;
        if (is_array($item['class']) && count($item['class']) == 2) {
            $className = strtolower($item['class'][0]);
            $methodName = $item['class'][1];
            $args = $item['args'][0];
            $success = $this->cig->$className->$methodName($args);
        }

        if ($success !== false) {
            $success = true;
        }

        return $success;
    }

    protected function disconnect()
    {
    }
}
