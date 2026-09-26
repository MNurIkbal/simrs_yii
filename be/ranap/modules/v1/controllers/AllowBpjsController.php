<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Rujukan;
use app\modules\v1\models\Diagnosa;

class AllowBpjsController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    // allow all method without authentication
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    /**
    * @author Rizal
    * @since 2018-04-25 10:11:55  
    * @desc ALL ABOUT BPJS BRIDGING
    */


    public function actionPeserta()
    {
        $request = Yii::$app->request;

        $post = $request->post();
        $model = new Bpjs;
        $peserta = $model->peserta($post['nokartu'], $post['tglSEP'], $post['isktp']);
        return $peserta;
    }

    public function actionRujukan()
    {
        $request = Yii::$app->request;

        $post = $request->post();
        $model = new Bpjs;
        $peserta = $model->rujukan($post['nomor'], $post['asal_rujukan']);
        return $peserta;
    }

    public function actionReferensiPoli()
    {
        try {
            $request = Yii::$app->request;
            $param = $request->post()['q'];
            $model = new Bpjs;
            $result = $model->referensiPoli($param);
            return $result;
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

    public function actionReferensiDiagnosa()
    {
        try {
            $request = Yii::$app->request;

            $param = $request->post()['q'];
            $model = new Bpjs;
            $result = $model->referensiDiagnosa($param);
            return $result;
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

    public function actionReferensiFaskes()
    {
        try {
            $request = Yii::$app->request;
            $param = $request->post()['q'];
            $model = new Bpjs;
            $result = $model->referensiFaskes($param);
            return $result;
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

    public function actionCreateSep()
    {
        try {
            $post = Yii::$app->request->post();
            $model = new Bpjs;
            $t_sep = [];
            foreach ($model->arr_sep as $key=>$each) {
                $t_sep[$each] = $post[$each];
            }
            $model->t_sep = $t_sep;
            $result = $model->createSep();
            $saved = false;
            if ($result['metaData']['code'] == 200) {
                $model->setManualAttribute();
                $model->nosep = $result['response']['sep']['noSep'];
                $model->additional_data = json_encode($result['response']);
                $saved = $model->save() ? $model : false;
            }

            // save to rujukan
            $rujukan = new Rujukan;
            $rujukan->asalrujukan_id = 2; // wip
            $diagnosa = new Diagnosa;
            $diagnosa = $diagnosa->find()->where(['diagnosa_kode'=>$t_sep['diagAwal']])->asArray()->one();
            $rujukan->diagnosa_id = $diagnosa ? $diagnosa['diagnosa_id'] : null;
            $rujukan->no_rujukan = $t_sep['noRujukan'];
            $rujukan->tanggal_rujukan = date('Y-m-d', strtotime($t_sep['tglRujukan']));
            $rujukan->kodediagnosa_rujukan = $t_sep['diagAwal'];
            $rujukan->save();

            return [
                'result' => $result,
                'model' => $saved
            ];
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

    public function actionDeleteSep()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $model->t_sep = $request->post();
            $result = $model->deleteSep();
            return $result;
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

    public function actionUpdateTanggalPulangSep()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $model->t_sep = $request->post();
            $result = $model->updateTanggalPulangSep();
            return $result;
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

}