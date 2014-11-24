<?php

namespace Bernard\Driver;

use Bernard\Driver;
use Pheanstalk\PheanstalkInterface;
use Pheanstalk\Job;

/**
 * Implements a Driver for use with https://github.com/pda/pheanstalk
 *
 * @package Bernard
 */
class PheanstalkDriver implements Driver
{
    /**
     * @var PheanstalkInterface
     */
    protected $pheanstalk;

    /**
     * @param PheanstalkInterface $pheanstalk
     */
    public function __construct(PheanstalkInterface $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * {@inheritDoc}
     */
    public function listQueues()
    {
        return $this->pheanstalk->listTubes();
    }

    /**
     * {@inheritDoc}
     */
    public function createQueue($queueName)
    {
        // not needed, auto-created on use
    }

    /**
     * {@inheritDoc}
     */
    public function countMessages($queueName)
    {
        $stats = $this->pheanstalk->statsTube($queueName);

        return $stats['current-jobs-ready'];
    }

    /**
     * {@inheritDoc}
     */
    public function pushMessage($queueName, $message)
    {
        $this->pheanstalk->putInTube($queueName, $message);
    }

    /**
     * {@inheritDoc}
     */
    public function popMessage($queueName, $interval = 5)
    {
        if ($job = $this->pheanstalk->reserveFromTube($queueName, $interval)) {
            return array($job->getData(), $job->getId());
        }

        return array(null, null);
    }

    /**
     * {@inheritDoc}
     */
    public function acknowledgeMessage($queueName, $receipt)
    {
        $receipt = new Job($receipt, null);
        $this->pheanstalk->delete($receipt);
    }

    /**
     * {@inheritDoc}
     */
    public function peekQueue($queueName, $index = 0, $limit = 20)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function removeQueue($queueName)
    {
        // not needed
    }

    /**
     * {@inheritDoc}
     */
    public function info()
    {
        return $this->pheanstalk->stats();
    }
}
