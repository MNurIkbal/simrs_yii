<?php

namespace app\modules\v1\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KelompokPegawai;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use Doco\components\DocoHelpers;

class PegawaiRuanganController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\RuanganPegawai';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['index'] = ['POST', 'GET'];
        $verbs['get-ruangan'] = ['POST', 'GET'];
        $verbs['delete'] = ['DELETE'];
        $verbs['update'] = ['PUT'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['get-ruangan']);
        unset($actions['create']);
        unset($actions['delete']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex($ruangan_id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();

            $model = new RuanganPegawai;
            $result = $model->getList($post, $ruangan_id);

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
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

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $data = $request->post();
                foreach ($data as $value) {
                    $model = new RuanganPegawai;
                    $model->ruangan_id = (int) $value['ruangan_id'];
                    $model->pegawai_id = (int) $value['pegawai_id'];

                    if (!empty($model->ruangan_id) && !empty($model->pegawai_id)){
                        $record_exist = $this->checkExist(
                            $model->ruangan_id, 
                            $model->pegawai_id
                        );
                    }

                    if ($record_exist){
                        $model = $record_exist;
                        $model->is_deleted = false;
                        $model->deleted_by = null;
                        $model->modified_count++;
                        $model->last_modified_date = date('Y-m-d H:i:s');
                        $model->save(false);
                        
                        return ['message' => 'Berhasil di Simpan.'];
                    }

                    if ($model->save(false)) {
                        return ['message' => 'Berhasil di Simpan.'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors, 'RuanganPegawaiForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
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

    private function checkExist($ruangan_id = null, $pegawai_id = null)
    {
        $sql = 'SELECT * FROM ruanganpegawai_mp WHERE ruangan_id=:ruangan_id AND pegawai_id=:pegawai_id';
        $result = RuanganPegawai::findBySql($sql, [':ruangan_id' => $ruangan_id, ':pegawai_id' => $pegawai_id])->one();

        return $result;
    }

    public function actionDelete($ruangan_id = null, $pegawai_id = null)
    {
        try {
            $model = RuanganPegawai::deleteMapping($ruangan_id, $pegawai_id);
            return $model;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getDataPegawai()
    {
        $pegawai = Pegawai::find()
        ->leftJoin(KelompokPegawai::tableName(). ' kel', 'pegawai_m.kelompokpegawai_id = kel. kelompokpegawai_id')
        ->select([
            'pegawai_m.pegawai_id',
            'pegawai_m.nama_pegawai',
            'kel.kelompokpegawai_nama as kelompok_pegawai'
        ]);
        
        return $pegawai;
    }

    public function actionAjax()
    {
        $data_pegawai = $this->getDataPegawai()->asArray()->all();

        return [
            'data-pegawai' => $data_pegawai,
        ];
    }

    public function actionUpdate($ruangan_id, $pegawai_id)
    {
        try {
            $request = Yii::$app->request;
            $model = RuanganPegawai::find()
            ->where([
                'ruangan_id'=>$ruangan_id,
                'pegawai_id'=>$pegawai_id
            ])
            ->one();
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save(false)) {
                    return [
                        'message' => 'Data berhasil diubah.',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'RuanganPegawaiForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
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

    public function actionListKelompokPegawai()
    {
        $model = new KelompokPegawai;
        $query = $model->listKelompokPegawai();
        
        return $query;
    }
}