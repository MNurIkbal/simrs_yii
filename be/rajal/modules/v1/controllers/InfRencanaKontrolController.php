<?php

/**
 * @Author: afil
 * @Date:   2018-01-17 15:09:21
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-25 18:15:00
 * @Description: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\BuatJanjiPoli;

class InfRencanaKontrolController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\buatJanjiPoli';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);
        unset($actions['view']);
        

        return $actions;
    }

    /**
    *
    * @see Fungsi override action index
    * @return array, activeQueryRecords data rencana kontrol
    *
    */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;

            $model = new BuatJanjiPoli;
            $query = $this->getData($request->get('ruangan_id'), $request->get('id', null));
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return [
                'data' => $query->asArray()->all(),
                'count' => $query->count()
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
    * @see Fungsi override action view
    * @return array, activeQueryRecords data rencana kontrol
    *
    */
    public function actionView()
    {
        try {
            $request = Yii::$app->request;

            $model = new BuatJanjiPoli;
            $query = $this->getData($request->get('ruangan_id'), $request->get('id', null));
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return [
                'data' => $query->asArray()->one(),
                'count' => $query->count()
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
    * @see Fungsi get data buatjanjipoli_t
    * @var params integer ruangan_id
    * @var params integer id = primary key buatjanjipoli_id
    * @return array, activeQueryRecords
    *
    */
    private function getData($ruangan_id = null, $id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    *, 
                    pasien_m.no_rekam_medik AS no_rekam_medik,
                    pasien_m.nama_pasien AS nama_pasien,
                    pasien_m.alamat_pasien AS alamat_pasien,
                    pasien_m.no_telepon_pasien AS no_telepon_pasien,
                    pendaftaran_t.no_pendaftaran AS no_pendaftaran
                FROM
                    buatjanjipoli_t
                LEFT JOIN pasien_m ON pasien_m.pasien_id = buatjanjipoli_t.pasien_id
                LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = buatjanjipoli_t.pendaftaran_id
                WHERE
                    buatjanjipoli_t.is_deleted = FALSE
            ";

        // filter
        if ($ruangan_id){
            $sql .= " AND buatjanjipoli_t.ruangan_id = :ruangan_id";
            $condition[':ruangan_id'] = $ruangan_id;
        }

        if ($id){
            $sql .= " AND buatjanjipoli_id = :buatjanjipoli_id";
            $condition[':buatjanjipoli_id'] = $id;
        }

        $result = BuatJanjiPoli::findBySql($sql, $condition);
        // var_dump($result->createCommand()->getRawSql());die; //dumping raw sql

        return $result;
    }

}