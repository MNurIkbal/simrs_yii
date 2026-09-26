<?php 

namespace app\components\Services\Master;

use Yii;
use app\components\Traits\ControllerHelperTrait;

class CaraBayarService {

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
        $additionalPayload = $request->get('additionalPayload', []);

        $response = $this->guzzleExec($this->service, [
            'url' => 'cara-bayar/get-list-data-cara-bayar',
            'payload' => [
               'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'additionalPayload' => $additionalPayload
                ]
            ]
        ]);

        $resData = [
            'result' => $response,
            'pagination' => [
                'more' => count($response) == 10 ? true : false,
            ],
        ];
        
        return $this->responseJson(200, 'Data berhasil diambil!', $resData);
    }
}