<?php
namespace Doco\Services;

use Yii;

class InternalService
{
    /**
     * untuk kebutuhan service internal
     * @param  array $actions 
     * @return bool
     */
    public function sendTo($actions, $blocking = false)
    {
        foreach ($actions as $service => $value) {
            $asService = strtolower($service);
            Yii::$app->docoIntegrasi->{$asService}->publish('integerasi', function ($request) use ($value) {
                if (is_array($value)) {
                    $listSend = [];
                    foreach ($value as $plugin => $params) {
                        $default = [
                            'is_send' => true
                        ];
                        $listSend[$plugin] = !empty($params) ? $params : $default;
                    }
                    $request->sendTo($listSend);
                }
            })->execute($blocking);
        }

        return true;
    }
}
