<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use app\modules\v1\services\Contracts\PanggilAntrianInterface;

class PanggilAntrianController extends DocoActiveController
{
    public $modelClass = '';
    protected $servicePoli;

    public function __construct($id, $module, $config = [], PanggilAntrianInterface $servicePoli)
    {
        $this->servicePoli = $servicePoli;
        parent::__construct($id, $module, $config);
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["poliklinik"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionPoliklinik()
    {
        $request = Yii::$app->request;
        $payload = $this->validatePayload([
            'payloadKey' => [
                'pendaftaran_id' => 'required',
            ]
        ]);

        if (isset($payload['errors'])) {
            return $this->responseJson(422, 'Param tidak lengkap', ['errors' => $payload['errors']]);
        }
        
        $data = [
            'antrian_id' => $request->post('antrian_id'),
            'pendaftaran_id' => $request->post('pendaftaran_id'),
        ];
        
        return $this->servicePoli->panggilAntrian($data);
    }
}
