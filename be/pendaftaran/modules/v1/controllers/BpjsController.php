<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Bpjs;
use Doco\components\DocoHelpers;

class BpjsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Bpjs';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["peserta"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate() 
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            if ($request->post()) {
                $model->attributes = $request->post();
                $model->tglsep = $model->tglsep . ' ' . date('H:i:s');
                $model->tglrujukan = $model->tglrujukan . ' ' . date('H:i:s');
                if ($model->save()) {
                    return [
                        'bpjs_id' => $model->bpjs_id,
                        'message' => 'Data Berhasil di simpan'
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'BpjsForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @author Rizal
    * @since 
    * @param 
    * @return 
    * @desc DEPRECATED 
    */
    public function actionAllowPeserta()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $peserta = [];

        try {
            // $model->attributes = $request->post();
            // $peserta = $model->peserta($model->nokartuasuransi, $model->isktp);
            $peserta = $model->peserta('123');

            return [
                'data' => $peserta,
                'message' => 'Data Berhasil di simpan'
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'data' => $peserta,
                'message' => $e->getMessage()
            ];
        }

        $model->attributes = $request->post();
        $model->isktp = $request->post()['isktp'];
        $tglsep = date('Y-m-d', strtotime($model->tglsep));
        $peserta = $model->peserta($model->nokartuasuransi, $tglsep, $model->isktp);
        return $peserta;
    }

    public function actionAllowRujukan()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $model->attributes = $request->post();
        $model->isrujukanrs = false;

        $data_rujukan = $model->rujukan($model->norujukan, $model->isrujukanrs);
        return $data_rujukan;
    }



    public function actionAllowRujukanPeserta()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $model->attributes = $request->post();
        $model->isrujukanrs = false;
        
        $data_rujukan = $model->rujukanPeserta($model->nokartuasuransi, $model->isrujukanrs);
        return $data_rujukan;
    }
    
    public function actionAllowListPoli()
    {
        $model = new Bpjs;
        $list_poli = $model->poli();
        return $list_poli;
    }
    public function actionAllowListFaskes()
    {
        $model = new Bpjs;
        $list_poli = $model->referensiFaskes('Bihbul');
        return $list_poli;
    }

    public function actionAllowCreateSep()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $model->attributes = $request->post();
        $model->tglsep = date('Y-m-d H:i:s', strtotime($model->tglsep . ' ' . date('H:i:s')));
        $model->tglrujukan = date('Y-m-d H:i:s', strtotime($model->tglrujukan . ' ' . date('H:i:s')));
        $model->nomr = $request->post('no_rekam_medik');
        $result = $model->createSep($model->attributes);
        return $result;
    }
    
    public function actionAllowDeleteSep()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $value = [
            'noSep'=>$request->post('nosep'),
            'ppkPelayanan'=>$request->post('ppkpelayanan'),
        ];

        $result = $model->deleteSep($value);
        return $result;
    }

    public function actionAllowGetDataReferensi($params = [])
    {
        $model = new Bpjs;
        $list_data = $model->referensi($params);

        return $list_data;
    }

    public function actionAllowUpdateTanggalPulangSep()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $value = [
            'noSep'=>$request->post('nosep'),
            'tglPulang' =>$request->post('tglpulang'),
            'ppkPelayanan'=>$request->post('ppkpelayanan'),
        ];

        $result = $model->updateTanggalPulangSep($value);
        return $result;
    }
}