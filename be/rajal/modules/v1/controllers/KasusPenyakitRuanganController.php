<?php

/**
 * @Author: metafiliana-doco
 * @Date:   2018-01-04 14:23:26
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-11 11:02:24
 * @Description: backend request proses model KasusPenyakitRuangan
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KasusPenyakitRuangan;

class KasusPenyakitRuanganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KasusPenyakitRuangan';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);


        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $result = $this->getData($post)
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($nama_jenis_kasus = $request->post('nama_jenis_kasus')) {
                $result->andFilterWhere(['ILIKE', 'jeniskasuspenyakit_m.jeniskasuspenyakit_nama', $nama_jenis_kasus])
                    ->orFilterWhere(['ILIKE', 'jeniskasuspenyakit_m.jeniskasuspenyakit_namalainnya', $nama_jenis_kasus]);
            }

            if ($id_ruangan = $request->post('ruangan_id')) {
                $result->andWhere(['kasuspenyakitruangan_mp.ruangan_id' => $id_ruangan]);
            }

            $status = $request->post('is_active');

            $status = $status ? true : false;
            // $result->andWhere(['kasuspenyakitruangan_mp.is_active' => $status]);

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

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

    /**
    *
    * @see override action update
    * @var id_ruangan integer
    * @var id_jeniskasuspenyakit integer
    * @return array, return message
    *
    */
    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;

            if ($request->post()) {

                $data = $request->post();
                foreach ($data as $key => $value) {
                    $modelKasusPenyakitRuangan = new KasusPenyakitRuangan;
                    $modelKasusPenyakitRuangan->ruangan_id = $value['ruangan_id'];
                    $modelKasusPenyakitRuangan->jeniskasuspenyakit_id = $value['kasus_id'];

                    //cek data exist
                    if (!empty($modelKasusPenyakitRuangan->ruangan_id) && !empty($modelKasusPenyakitRuangan->jeniskasuspenyakit_id)){
                        $record_exist = $this->checkExist(
                            $modelKasusPenyakitRuangan->ruangan_id, 
                            $modelKasusPenyakitRuangan->jeniskasuspenyakit_id
                        );
                    }

                    if ($record_exist){
                        $modelKasusPenyakitRuangan = $record_exist;
                        $modelKasusPenyakitRuangan->is_deleted = false;
                        $modelKasusPenyakitRuangan->deleted_by = null;
                        $modelKasusPenyakitRuangan->modified_count++;
                        $modelKasusPenyakitRuangan->last_modified_date = date('Y-m-d H:i:s');
                        $modelKasusPenyakitRuangan->save();
                        
                        return ['message' => 'Data Berhasil di simpan'];
                    }

                    if ($modelKasusPenyakitRuangan->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($modelKasusPenyakitRuangan->errors, 'KasusPenyakitRuanganForm');
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

    /**
    *
    * @see override action update
    * @var id_ruangan integer
    * @var id_jeniskasuspenyakit integer
    * @return array, return message
    *
    */
    public function actionUpdate($id_ruangan = null, $id_jeniskasuspenyakit = null)
    {
        try {
            $request = Yii::$app->request;
            $modelKasusPenyakitRuangan = KasusPenyakitRuangan::findOne(['ruangan_id' => $id_ruangan, 'jeniskasuspenyakit_id' => $id_jeniskasuspenyakit]);
            if ($request->post() && !empty($modelKasusPenyakitRuangan)) {
                $modelKasusPenyakitRuangan->attributes = $request->post();
                if ($modelKasusPenyakitRuangan->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($modelKasusPenyakitRuangan->errors,'KasusPenyakitRuanganForm');
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

    /**
    *
    * @see override action delete, jika terdapat data exist, maka akan dilakukan update attributes
    * @var id_ruangan integer
    * @var id_jeniskasuspenyakit integer
    * @return array, return message
    *
    */
    public function actionDelete($id_ruangan = null, $id_jeniskasuspenyakit = null)
    {
        try {
            $modelKasusPenyakitRuangan = KasusPenyakitRuangan::findOne(['ruangan_id' => $id_ruangan, 'jeniskasuspenyakit_id' => $id_jeniskasuspenyakit, 'is_deleted' => false]);

            $modelKasusPenyakitRuangan->is_deleted = true;
            $modelKasusPenyakitRuangan->save();
            return $modelKasusPenyakitRuangan;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    *
    * @see Fungsi get data kasus penyakit diagnosa berdasarkan ruangan (poli) kasuspenyakitruangan_m
    * @return array, activeRecords
    *
    */
    private function getData()
    {
        $query = KasusPenyakitRuangan::find()
                        ->joinWith(['jenisKasusPenyakit' => function($query){
                            $query->from('jeniskasuspenyakit_m');
                        }])
                        ->joinWith(['ruangan' => function($query){
                            $query->from('ruangan_m');
                        }]);

        return $query;
    }

    /**
    *
    * @see Fungsi cek data exist, untuk keperluan create, update
    * @var id_ruangan integer
    * @var id_jeniskasuspenyakit integer
    * @return array, activeRecords
    *
    */
    private function checkExist($id_ruangan = null, $id_jeniskasuspenyakit = null)
    {
        $sql = 'SELECT * FROM kasuspenyakitruangan_mp WHERE ruangan_id=:ruangan_id AND jeniskasuspenyakit_id=:jeniskasuspenyakit_id';
        $result = KasusPenyakitRuangan::findBySql($sql, [':ruangan_id' => $id_ruangan, 'jeniskasuspenyakit_id' => $id_jeniskasuspenyakit])->one();

        return $result;
    }
}