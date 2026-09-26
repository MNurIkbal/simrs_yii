<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 13:50:49
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-10 09:55:47
 * @Description: controller untuk master jenis kasus penyakit rajal
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\JenisKasusPenyakit;

class JenisKasusPenyakitController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\jenisKasusPenyakit';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["ajax"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionAjax()
    {
        try {
            $request = Yii::$app->request;
            $find = $this->getDataJenisKasus();
            
            if ($id_ruangan = $request->get('ruangan_id')){
                $find->andWhere(['kasuspenyakitruangan_mp.ruangan_id' => $id_ruangan]);
            }

            $data_kasus = $find->asArray()->all();

            return [
                'data-kasus' => $data_kasus
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

    private function getDataJenisKasus()
    {
        $jenis_kasus = JenisKasusPenyakit::find()->select([
                'jeniskasuspenyakit_m.jeniskasuspenyakit_id as jeniskasuspenyakit_id',
                'jeniskasuspenyakit_nama',
            ])->joinWith(['kasuspenyakitruangan']);
        
        return $jenis_kasus;

    }
}