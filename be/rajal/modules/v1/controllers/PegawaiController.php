<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 11:56:40
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-15 12:17:46
 * @Description: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\RuanganPegawai;

class PegawaiController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\RuanganPegawai';

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
    * @see Fungsi get list data penjamin untuk ajax request
    * @return array
    *
    */
    public function actionAjax()
    {
        try {
            $request = Yii::$app->request;
            $find = $this->getPegawaiRuangan($request->get('id_ruangan'));

            $data_pegawai = $find->asArray()->all();

            return [
                'data-pegawai' => $data_pegawai
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
    * @see Fungsi get data penjamin
    * @return array, activeQueryRecords
    *
    */
    private function getPegawaiRuangan($id_ruangan)
    {
        $sql = 'SELECT ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai as nama_pegawai FROM ruanganpegawai_mp JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id WHERE ruanganpegawai_mp.ruangan_id=:ruangan_id';
        $result = RuanganPegawai::findBySql($sql, [':ruangan_id' => $id_ruangan]);

        return $result;

    }
}