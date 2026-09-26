<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * KamarRanapDepService digunakan untuk kebutuhan 
 * mengambil data ruangan
 */

namespace app\components\Services\Master;

use Yii;
use app\components\Traits\ControllerHelperTrait;
use yii\web\Response;

class RuanganByInstalasiDepService
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
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?instalasi_id='.$parent_label;
        }
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        try {
            $response = $this->service->get('ruangan/get-ruangan-by-instalasi-dep'.$params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value){
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            }
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
