<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Laboratorium
 * @copyright 13 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use app\modules\integrator\models\HasilPemeriksaanLabWynacom;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\InfoPasienLabView;
use app\modules\v1\models\InputHasilLabView;
use app\modules\v1\models\HasilLabWynacomView;
use app\modules\v1\models\PasienMasukPenunjangT;

class HasilLabWynacomController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienLabView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    private function getPasienLab($id = null)
    {
        $model = InfoPasienLabView::find();
        if ($id) {
            $model->where(['pasienmasukpenunjang_id' => $id]);
            $data = $model->asArray()->one();
        } else {
            $data = $model->asArray()->all();
        }

        return $data;
    }

    private function getHasilLab($id = null)
    {
        $model = InputHasilLabView::find();
        if ($id) {
            $model->where(['pasienmasukpenunjang_id' => $id]);
            $data = $model->asArray()->one();
        } else {
            $data = $model->asArray()->all();
        }

        return $data;
    }

    public function actionGenerateApi($id)
    {
        $data_pasien = $this->getPasienLab($id);
        $data_hasil_lab = $this->getHasilLab($id);
        $count_data_expertise = $this->getCountHasilLab($id);

        $result = [
            'data-pasien' => $data_pasien,
            'data-hasil-lab' => $data_hasil_lab,
            'count-data-expertise' => $count_data_expertise,
        ];

        return $result;
    }

    public function actionIndex() 
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $model = new InputHasilLabView;
            $query = $model::find();
            $query->where(['pasienmasukpenunjang_id' => $id]);
            $data = $query->all();
            $list_data = $list_pemeriksaan = $list_sample = [];
            foreach ($data as $key => $value) {
                $list_pemeriksaan[$value['samplelab_id']][] = $value['daftartindakan_nama']; 
                $list_data['data'][$value['samplelab_id']] = [
                    'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                    'samplelab_id' => $value['samplelab_id'],
                    'nama_sample' => $value['nama_sample'],
                    'daftartindakan_nama' => implode('<br> ', $list_pemeriksaan[$value['samplelab_id']]),
                    'is_expertise' => $value['is_expertise'],
                ];
            }
            foreach ($list_data['data'] as $key => $value) {
                $list_sample['data'][] = $value;
            }

            return $list_sample;
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

    public function actionDataWynacom() 
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            // $model = new HasilLabWynacomView;
            // $query = $model::find();
            // $query->where(['pasienmasukpenunjang_id' => $id]);
            // $data = $query->all();
            
            // $sql = "
            //     WITH sort_hasil AS (
            //       SELECT m.*, ROW_NUMBER() OVER (PARTITION BY test_nama_lis ORDER BY tgl_pemeriksaan DESC) AS rn
            //       FROM laporanhasillab_v AS m
            //       WHERE pasienmasukpenunjang_id  = '{$id}'
            //     )
            //     SELECT * FROM sort_hasil";
            // $data = HasilLabWynacomView::findBySql($sql)->asArray()->all();

            $data = HasilPemeriksaanLabWynacom::latestRecordById($id);

            return $data;
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

    public function actionVerifikasi($id)
    {
        try {
            $modelPenunjang = PasienMasukPenunjangT::findOne($id);
            $request = Yii::$app->request;
            $model = new InputHasilLabView;
            $query = $model::find();
            $query->where(['pasienmasukpenunjang_id' => $id]);
            $query->andWhere(['or',
                ['is_expertise' => null],
                ['is_expertise' => 'f']
            ]);
            $data = $query->all();
            $count = count($data);
            if ($count < 1) {
                $modelPenunjang->status_periksa = DocoConstants::ST_SELESAI;
                $modelPenunjang->tanggal_verifikasi = date('Y-m-d H:i:s');
                $modelPenunjang->save();
                $verifikasi = true;
            } else {
                $verifikasi = false;
            }

            return $verifikasi;
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

    private function getCountHasilLab($id = null)
    {
        $model = InputHasilLabView::find();
        if ($id) {
            $model->where(['pasienmasukpenunjang_id' => $id]);
            $model->andWhere(['or',
                ['is_expertise' => null],
                ['is_expertise' => 'f']
            ]);
            $data = $model->asArray()->all();
        } else {
            $data = $model->asArray()->all();
        }

        $count = !empty($data) ? count($data) : 0;

        return $count;
    }
}