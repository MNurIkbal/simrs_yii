<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DataDokterView;
use app\modules\v1\models\InfoRiwayatPasienView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;

class DokterController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DataDokterView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new DataDokterView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['nama_pegawai' => SORT_ASC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDetail($id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $request = Yii::$app->request;
            $model = DataDokterView::find()->where(['pegawai_id' => $id])->one();

            if (!empty($model)) {
                return $result = [
                    'status' => 200,
                    'data' => $model
                ];
            } else {
                return $result = [
                    'status' => 500,
                    'data' => array()
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate($id)
    {
        $post = \Yii::$app->request->post();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $dokter = Pegawai::findOne($id);
            if (!empty($dokter)) {
                $dokter->photopegawai = !empty($post['photopegawai']) ? $post['photopegawai'] : null;

                if ($dokter->save()) {
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil Tersimpan'
                    ];
                } else {
                    $result['status'] = 500;
                    $result['title'] = 'Proses Gagal';
                    $result['text'] = $dokter->getErrors();
                }
            } else {
                $transaction->rollBack();
                // \Yii::$app->response->statusCode = 500;
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => ''
                ];
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @todo Fungsi get data ruangan
     * @return array, activeQueryRecords
     *
     */


    /**
     *
     * @todo Fungsi get data dokter
     * @return array, activeQueryRecords
     *
     */
    private function getDokter()
    {
        $result = DataDokterView::find();
        return $result;
    }



    public function actionGetByRiwayat($norm)
    {
        $datas = InfoRiwayatPasienView::find()
            ->select(['dok_rjrd_id', 'dok_rjrd', 'dok_ri_id', 'dok_ri'])
            ->andWhere([
                'no_rekam_medik' => $norm,
            ])
            ->distinct()->asArray()->all();


        return $datas;
    }
}
