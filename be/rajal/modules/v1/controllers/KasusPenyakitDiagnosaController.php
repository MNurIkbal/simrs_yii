<?php

/**
 * @Author: afil
 * @Date:   2018-01-09 13:28:56
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-11 11:51:37
 * @Description: backend request proses model KasusPenyakitDiagnosa
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KasusPenyakitDiagnosa;
use app\modules\v1\models\Diagnosa;

class KasusPenyakitDiagnosaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KasusPenyakitDiagnosa';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs['index'] = ['GET', 'POST'];
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

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;

            if ($request->post()) {

                $data = $request->post();
                foreach ($data as $key => $value) {
                    $modelKasusPenyakitDiagnosa = new KasusPenyakitDiagnosa;
                    $modelKasusPenyakitDiagnosa->jeniskasuspenyakit_id = $value['jeniskasuspenyakit_id'];
                    $modelKasusPenyakitDiagnosa->diagnosa_id = $value['diagnosa_id'];

                    //cek data exist
                    if (!empty($modelKasusPenyakitDiagnosa->diagnosa_id) && !empty($modelKasusPenyakitDiagnosa->jeniskasuspenyakit_id)){
                        $record_exist = $this->checkExist(
                            $modelKasusPenyakitDiagnosa->diagnosa_id, 
                            $modelKasusPenyakitDiagnosa->jeniskasuspenyakit_id
                        );
                    }

                    if ($record_exist){
                        $modelKasusPenyakitDiagnosa = $record_exist;
                        $modelKasusPenyakitDiagnosa->is_deleted = false;
                        $modelKasusPenyakitDiagnosa->deleted_by = null;
                        $modelKasusPenyakitDiagnosa->modified_count++;
                        $modelKasusPenyakitDiagnosa->last_modified_date = date('Y-m-d H:i:s');
                        $modelKasusPenyakitDiagnosa->save();
                        
                        return ['message' => 'Data Berhasil di simpan'];
                    }

                    if ($modelKasusPenyakitDiagnosa->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($modelKasusPenyakitDiagnosa->errors, 'KasusPenyakitDiagnosaForm');
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
    * @var id_diagnosa integer, id_diagnosa kasuspenyakitdiagnosa_mp
    * @var id_jeniskasuspenyakit integer, id_jeniskasuspenyakit kasuspenyakitdiagnosa_mp
    * @return array
    *
    */
    public function actionUpdate($id_diagnosa = null, $id_jeniskasuspenyakit = null)
    {
        try {
            $request = Yii::$app->request;
            $modelKasusPenyakitDiagnosa = KasusPenyakitDiagnosa::findOne(['diagnosa_id' => $id_diagnosa, 'jeniskasuspenyakit_id' => $id_jeniskasuspenyakit]);

            if ($request->post() && !empty($modelKasusPenyakitDiagnosa)) {
                $modelKasusPenyakitDiagnosa->load($request->post());

                if ($is_active = $request->post('is_active')){
                    $modelKasusPenyakitDiagnosa->is_active = $is_active;
                }

                if ($modelKasusPenyakitDiagnosa->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($modelKasusPenyakitDiagnosa->errors,'KasusPenyakitDiagnosaForm');
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
    * @var id_diagnosa integer, id_diagnosa kasuspenyakitdiagnosa_mp
    * @var id_jeniskasuspenyakit integer, id_jeniskasuspenyakit kasuspenyakitdiagnosa_mp
    * @return array
    *
    */
    public function actionDelete($id_diagnosa = null, $id_jeniskasuspenyakit = null)
    {
        try {
            $modelKasusPenyakitDiagnosa = KasusPenyakitDiagnosa::findOne(['diagnosa_id' => $id_diagnosa, 'jeniskasuspenyakit_id' => $id_jeniskasuspenyakit, 'is_deleted' => false]);

            $modelKasusPenyakitDiagnosa->is_deleted = true;

            if ($modelKasusPenyakitDiagnosa->save()){
                return [
                    'message' => 'sukses_hapus',
                ];
            }else{
                $errors = DocoHelpers::parseError($modelKasusPenyakitDiagnosa->errors, 'KasusPenyakitDiagnosaForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
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
    * @var params array
    * @return array, activeQueryRecords
    *
    */
    private function getData($params = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    kasuspenyakitdiagnosa_mp.*, 
                    kasuspenyakitruangan_mp.*, 
                    diagnosa_m.*, 
                    jeniskasuspenyakit_m.*, 
                    ruangan_m.*,
                    kasuspenyakitdiagnosa_mp.is_active as status_aktif,
                    CONCAT(diagnosa_m.diagnosa_kode, ' - ', diagnosa_m.diagnosa_nama) as diagnosa_kodenama
                FROM
                    kasuspenyakitdiagnosa_mp
                JOIN kasuspenyakitruangan_mp ON kasuspenyakitruangan_mp.jeniskasuspenyakit_id = kasuspenyakitdiagnosa_mp.jeniskasuspenyakit_id
                JOIN ruangan_m ON ruangan_m.ruangan_id = kasuspenyakitruangan_mp.ruangan_id
                JOIN diagnosa_m ON diagnosa_m.diagnosa_id = kasuspenyakitdiagnosa_mp.diagnosa_id
                JOIN jeniskasuspenyakit_m ON jeniskasuspenyakit_m.jeniskasuspenyakit_id = kasuspenyakitdiagnosa_mp.jeniskasuspenyakit_id
                WHERE kasuspenyakitdiagnosa_mp.is_deleted = false
            ";

        // filter
        if (isset($params['ruangan_id'])){
            $sql .= " AND kasuspenyakitruangan_mp.ruangan_id = :ruangan_id";
            $condition[':ruangan_id'] = $params['ruangan_id'];
        }

        if (!empty($params['jeniskasuspenyakit_nama']) && isset($params['jeniskasuspenyakit_nama'])){
            $sql .= " AND jeniskasuspenyakit_m.jeniskasuspenyakit_nama ILIKE :jeniskasuspenyakit_nama";
            $condition[':jeniskasuspenyakit_nama'] = "%".$params['jeniskasuspenyakit_nama']."%";
        }

        if (!empty($params['diagnosa_kode']) && isset($params['diagnosa_kode'])){
            $sql .= " AND diagnosa_m.diagnosa_kode ILIKE :diagnosa_kode";
            $condition[':diagnosa_kode'] = "%".$params['diagnosa_kode']."%";
        }

        if (!empty($params['diagnosa_nama']) && isset($params['diagnosa_nama'])){
            $sql .= " AND diagnosa_m.diagnosa_nama ILIKE :diagnosa_nama";
            $condition[':diagnosa_nama'] = "%".$params['diagnosa_nama']."%";
        }

        $result = KasusPenyakitDiagnosa::findBySql($sql, $condition);
        // var_dump($result->createCommand()->getRawSql());die; //dumping raw sql

        return $result;
    }

    /**
    *
    * @see Fungsi cek data exist, untuk keperluan create, update
    * @var id_jeniskasuspenyakit integer, id_jeniskasuspenyakit kasuspenyakitdiagnosa_mp
    * @return array, activeQueryRecords
    *
    */
    private function checkExist($id_jeniskasuspenyakit = null, $id_diagnosa = null)
    {
        $sql = 'SELECT * FROM kasuspenyakitdiagnosa_mp WHERE diagnosa_id=:diagnosa_id AND jeniskasuspenyakit_id=:jeniskasuspenyakit_id';
        $result = KasusPenyakitDiagnosa::findBySql($sql, [':diagnosa_id' => $id_diagnosa, 'jeniskasuspenyakit_id' => $id_jeniskasuspenyakit])->one();

        return $result;
    }
}