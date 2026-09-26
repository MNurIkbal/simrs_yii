<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * TenagaMedisService digunakan untuk kebutuhan 
 * mengambil data pegawai dengan kelompok pegawai tenaga medis
 */

namespace app\components\Services\Master;

use Yii;
use app\components\Traits\ControllerHelperTrait;

class TenagaMedisService
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
            'url' => 'pegawai/get-tenaga-medis',
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
