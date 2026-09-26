<?php

namespace Doco\rabbitmq;

use Yii;
use mikemadisonweb\rabbitmq\components\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use \Doco\rabbitmq\task\{
    DistributeTask
};

class BaseConsumer implements ConsumerInterface, IBaseConsumer
{
    protected $taskList = [];

    public function __construct()
    {
        $this->taskList = $this->register();
    }

    public function register()
    {
        return [];
    }

    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $route = $msg->getRoutingKey();
        if (isset($this->taskList[$route])) {
            try {
                (new DistributeTask(new $this->taskList[$route]))->run($msg);
                return ConsumerInterface::MSG_ACK;
            } catch (Exception $e) {
                return ConsumerInterface::MSG_REJECT;
            }
        }

        return ConsumerInterface::MSG_ACK;
    }
}
