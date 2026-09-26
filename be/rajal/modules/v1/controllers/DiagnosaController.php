<?php

/**
 * @Author: afil
 * @Date:   2018-01-10 10:01:35
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-10 18:00:23
 * @Description: backend request proses model Diagnosa
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Diagnosa;

class DiagnosaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\diagnosa';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["ajax"] = ["GET"];


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        // here
        

        return $actions;
    }

    /**
    *
    * @see Fungsi get list data diagnosa untuk ajax request
    * @return array
    *
    */
    public function actionAjax()
    {
        try {
            $request = Yii::$app->request;
            $find = $this->getDiagnosa();

            $data_diagnosa = $find->asArray()->all();

            return [
                'data-diagnosa' => $data_diagnosa
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
    * @see Fungsi get data diagnosa
    * @return array, activeQueryRecords
    *
    */
    private function getDiagnosa()
    {
        $jenis_kasus = Diagnosa::find()->select([
                "diagnosa_id",
                "CONCAT(diagnosa_kode, ' - ', diagnosa_nama) as diagnosa_nama",
            ]);
        
        return $jenis_kasus;

    }
}