<?php

namespace Doco\rabbitmq\task;

use PhpAmqpLib\Message\AMQPMessage;

use Yii;

class DistributeTask
{
    /**
     * @var TaskInterface
     */
    private $task;

    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
    }

    public function setTask(TaskInterface $task)
    {
        $this->task = $task;
    }

    public function run(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);
        $this->task->setParams($data);
        $result = $this->task->execute($data);
    }
}
