<?php

namespace app\components\Services\Master;

use Yii;
use app\components\Traits\ControllerHelperTrait;
use yii\web\Response;

class PenjaminDepDropService
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
        $queries['carabayar_ids'] = $request->post('depdrop_parents');
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        try {
            $response = $this->guzzleExec($this->service, [
                'url' => 'penjamin/get-list-penjamin-dep-drop',
                'payload' => [
                    'query' => $queries
                ]
            ]);
            foreach ($response as $value)
                $result['output'][] = [
                    'id' => $value['penjamin_id'],
                    'name' => $value['penjamin_nama']
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
