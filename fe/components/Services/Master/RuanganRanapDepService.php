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

class RuanganRanapDepService
{
    use ControllerHelperTrait;

    protected $service;

    public function __construct()
    {
        $this->service = Yii::$app->docoRest->master;
    }

    public function execute()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $response = $this->guzzleExec($this->service, [
            'url' => 'ruangan/get-ruangan-ranap-dep',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                ]
            ]
        ]);
        $resData = [
            'result' => $response,
            'pagination' => [
                'more' => count($response) == 10 ? true : false,
            ],
        ];
        return $this->responseJson(200, 'Success', $resData);
    }
}
