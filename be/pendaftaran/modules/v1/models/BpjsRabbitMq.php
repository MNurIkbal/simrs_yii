<?php

namespace app\modules\v1\models;

use app\modules\v1\models\Bpjs;
/* 
* this class is created due to Bpjs issues on RabbitMQ Task that not reset timestamp even the process is done
* it cause the Bpjs response is always "Service Expired"
* Just use this model if u get the same issues
* if u need to modify or add function related to Bpjs, make sure you created it at this parent model
*/

class BpjsRabbitMq extends Bpjs {
  public function __construct()
  {
      parent::__construct();
      self::$timestamp = '';
  }
}