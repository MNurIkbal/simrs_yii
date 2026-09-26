<?php
namespace Doco\rabbitmq;

use Yii;
use yii\helpers\ArrayHelper;

class RabbitBgProcess {

    protected $xOwner;
    protected $token;
    protected $properties = [];

    public function __construct()
    {
        $this->xOwner = Yii::$app->request->getHeaders()->get('X-Owner');
        $this->token = Yii::$app->request->getHeaders()->get('Authorization');
        if (!empty(Yii::$app->params['expiration'])) {
            $this->properties = ArrayHelper::merge($this->properties, ['expiration' => Yii::$app->params['expiration']]);
        }
    }
    
    /**
     * $attributes = params yang di perlukan untuk kebutuhan data
     * $task = task  
     */
    public function send($attributes, $task = 'import_data_laporan', $importData = 'import_data')
    {
        $attributes = ArrayHelper::merge($attributes, [
            'token' => $this->token, 
            'xOwner' => $this->xOwner,
            'jwtUser' => Yii::$app->jwt->user
        ]);

        
        \Yii::$app->rabbitmq->load();
        $producer = \Yii::$container->get(sprintf('rabbit_mq.producer.%s',  $importData));
        $producer->publish(serialize($attributes), $task, $this->properties);
    }
}
