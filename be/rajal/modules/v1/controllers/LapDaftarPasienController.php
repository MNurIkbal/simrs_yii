<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-26 10:21:09 
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-26 15:55:12
 * @Description: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanKunjunganRawatJalanView;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\CaraBayar;

class LapDaftarPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanKunjunganRawatJalanView';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["index"] = ["GET", "POST"];


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);
        

        return $actions;
    }

    /**
    *
    * @see Fungsi override action index
    * @return array, activeQueryRecords data daftar pasien rajal
    *
    */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;

            $model = new LaporanKunjunganRawatJalanView;
            $query = LaporanKunjunganRawatJalanView::find()
                ->where([
                    'ruangan_id' => $request->get('ruangan_id', null)
                ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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
    * @see Fungsi get list data
    * @return array
    *
    */
    public function actionGetListData()
    {
        try {
            $request = Yii::$app->request;

            $find_penjamin = $this->getPenjamin();
            $data_penjamin = $find_penjamin->asArray()->all();

            $find_pegawai = $this->getPegawaiRuangan($request->get('ruangan_id'));
            $data_pegawai = $find_pegawai->asArray()->all();

            $find_carabayar = $this->getCaraBayar();
            $data_carabayar = $find_carabayar->asArray()->all();

            return [
                'data-penjamin' => $data_penjamin,
                'data-pegawai' => $data_pegawai,
                'data-carabayar' => $data_carabayar,
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
    private function getPenjamin()
    {
        $data = Penjamin::find()->select([
                "penjamin_id",
                "penjamin_nama",
            ]);
        
        return $data;

    }

    /**
    *
    * @see Fungsi get data pegawai ruangan
    * @return array, activeQueryRecords
    *
    */
    private function getPegawaiRuangan($ruangan_id)
    {
        $sql = 'SELECT ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai as nama_pegawai FROM ruanganpegawai_mp JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id WHERE ruanganpegawai_mp.ruangan_id=:ruangan_id';
        $result = RuanganPegawai::findBySql($sql, [':ruangan_id' => $ruangan_id]);

        return $result;

    }

    /**
    *
    * @see Fungsi get data penjamin
    * @return array, activeQueryRecords
    *
    */
    private function getCaraBayar()
    {
        $data = CaraBayar::find()->select([
                "carabayar_id",
                "carabayar_nama",
            ]);
        
        return $data;

    }
}