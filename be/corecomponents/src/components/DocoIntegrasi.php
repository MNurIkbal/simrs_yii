<?php

/**
 * @author yaya <[setyabudi@docotel.com]>
 * @todo Kebutuhan untuk manajemen integrasi
 */

namespace Doco\components;

use Yii;
use yii\base\Component;
use Doco\components\DocoHelpers;

class DocoIntegrasi extends Component
{
    const EMPTY_FUNC = 'dummy';
    const TASK_SPARATOR = '-';

    protected $iniFile;
    protected $channel;
    protected $task;
    protected $sendTo;
    protected $behavior;

    public function __construct()
    {
        $this->iniFile = @parse_ini_file(dirname(dirname(dirname(__DIR__))) . '/config/env/.api', true);
    }

    public function execute($blocking = false)
    {
        if ($this->task != self::EMPTY_FUNC && !empty(Yii::$app->redis)) {
            $this->sendTo['blocking'] = $blocking;
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => $this->channel,
                'message' =>  json_encode($this->sendTo)
            ]);
            return true;
        }
        return false;
    }

    /**
     * [sendTo transform data request]
     * @param  array  $request [request task]
     * @return void
     */
    public function sendTo(array $request)
    {
        $tmpRequest = [];
        foreach ($request as $key => $value) {
            $keyTask = $this->task . self::TASK_SPARATOR . $key;
            $tmpRequest[$keyTask] = $value;
        }

        $tmpRequest = [
            'send_to' => $tmpRequest
        ];

        $this->sendTo = $tmpRequest;
    }

    /**
     * [publish untuk seting env integrasi]
     * @param  string $channel   [channel redis]
     * @param  Closure $callback [callback untuk seting request]
     * @return void
     */
    public function publish($channel, Callable $callback)
    {
        $this->channel = $channel;
        $callback($this);
        return $this;
    }

    /**
    *  [membuat object yang di seting pada env/.api untuk menjadi task untuk service conector]
    **/

    public function __get($property)
    {
      $ini = isset($this->iniFile['external']) ? $this->iniFile['external'] : [];
      if (property_exists($this, $property)) {
        return $this->$property;
      } else {
        $this->task = isset($ini[$property]) ? ucfirst($ini[$property]) : self::EMPTY_FUNC;

        return $this->$property = $this;
      }
    }

    public function __set($name, $value) {}
}