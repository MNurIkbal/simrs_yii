<?php

namespace Doco\rabbitmq\task;

interface TaskInterface
{
     public function execute(array $data);

     public function setParams(array $data);
}
