<?php

namespace app\components\Services\Master;

use Yii;
use app\components\Traits\ControllerHelperTrait;

class CaraBayarDepDropService
{
    use ControllerHelperTrait;

    protected $service;

    public function __construct()
    {
        $this->service = Yii::$app->docoRest->master;
    }

    public function execute()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        try {
            $response = $this->guzzleExec($this->service, [
                'url' => 'cara-bayar/get-list-cara-bayar-dep-drop',
                'payload' => []
            ]);
            foreach ($response as $value)
                $result['output'][] = [
                    'id' => $value['carabayar_id'],
                    'name' => $value['carabayar_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
