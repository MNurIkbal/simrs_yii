<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\PemakaianBarang;
use app\modules\v1\models\PemakaianBarangDetail;
use app\modules\v1\models\StokBarang;

class InformasiPemakaianBarangController extends DocoActiveController
{
    public $modelClass = PemakaianBarang::class;

    const FIFO = 'FIFO';
    const LIFO = 'LIFO';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new PemakaianBarang;
            $query = $model::find()->joinWith([
                    'pegawai' => function ($query) {
                        $query->select([
                            'pegawai_m.pegawai_id',
                            'pegawai_m.nama_pegawai',
                        ]);
                    }
            ])->where(['ruangan_id' => $request->get('ruangan_id', null)])->asArray();
            $start = date('Y-m-01 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if(isset($_GET['advanced-filter'])) {
                // return $_GET['advanced-filter'];
                if(isset($_GET['advanced-filter']['tgl_pemakaianbarang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pemakaianbarang']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pemakaianbarang']);
                }
            }
            
            $query->andWhere(['BETWEEN', 'tgl_pemakaianbarang', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}