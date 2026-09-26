<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-26 14:55
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfPemesananKamar;
use app\modules\v1\models\TraPemesananKamar;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\Pekerjaan;
use Doco\components\DocoHelpers;

class InfPemesananKamarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\inf-pemesanan-kamar';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfPemesananKamar;
        $query = $model::find();
        $between = false;
        // $start = date('Y-m-d 00:00:00');
        $start = date("Y-m-d", strtotime('yesterday')) . ' ' . date('H:i:s');
        $end = date('Y-m-d H:i:s');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pesan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pesan']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        
        $query->andWhere(['between', 'tgl_pesan', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    *
    * @todo Fungsi get data ruangan
    * @return array, activeQueryRecords
    *
    */
    private function getRuangan()
    {
        // Query
        $sql = "SELECT r.ruangan_id, r.ruangan_nama, r.ruangan_nama as ruangan_nama FROM ruangan_m r 
            JOIN instalasi_m i ON i.instalasi_id = r.instalasi_id 
            WHERE i.instalasi_adakamar = true AND r.is_deleted = false AND r.is_active = true";
        // Result
        $result = Ruangan::findBySql($sql);

        return $result;
    }

    /**
    *
    * @todo Fungsi get data status booking kamar
    * @return array, activeQueryRecords
    *
    */
    private function getStatusBookingKamar()
    {
        // Query
        $sql = "SELECT lookup_id, lookup_name, lookup_name as status_booking_kamar FROM lookup_m WHERE lookup_type = 'status_bookingkamar' 
            AND is_deleted = false AND is_active = true
        ";
        
        // Result
        $result = Lookup::findBySql($sql);

        // Return
        return $result;
    }

    public function actionGenerateApi()
    {
        try {
            $request = Yii::$app->request;

            // Get ruangan
            $modelRuangan = $this->getRuangan();
            $dataRuangan = $modelRuangan->asArray()->all();

            // Get jenis kamar
            $modelStatusBookingKamar = $this->getStatusBookingKamar();
            $dataStatusBookingKamar = $modelStatusBookingKamar->asArray()->all();

            return [
                'data-ruangan' => $dataRuangan,
                'data-status' => $dataStatusBookingKamar,
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

    
    // Get bundle data
    public function actionGetBundleData()
    {
        // Get id
        $id = Yii::$app->request->get('id');

        // Declare some variables
        $pemesananKamar = [];
        $pasien = [];

        // Get pemesanan kamar
        $pemesananKamar = $this->getPemesananKamar($id);

        // Check pemesanan kamar
        if (!empty($pemesananKamar)) {
            // Check pasien
            if ($pemesananKamar->pasien_id != null) {
                // Get pasien
                $pasien = $this->getPasien($pemesananKamar->pasien_id);
            }
        }

        // Return
        return [
            'pemesanan-kamar' => $pemesananKamar,
            'pasien' => $pasien,
            'list-penyakit' => $this->getListPenyakit(),
            'list-kelas' => $this->getListKelas(),
            'list-status-perkawinan' => $this->getListLookup('status_perkawinan'),
            'list-jenis-kelamin' => $this->getListLookup('jenis_kelamin'),
            'list-jenis-identitas' => $this->getListLookup('jenis_identitas'),
            'list-pekerjaan' => $this->getListPekerjaan(),
            'list-agama' => $this->getListLookup('agama'),
            'list-nama-depan' => $this->getListLookup('nama_depan'),
        ];
    }

    // Function get pemesanan kamar
    private function getPemesananKamar($id)
    {
        // Declare model
        $model = [];

        // Get model
        $model = InfPemesananKamar::find()->where(['bookingkamar_id' => $id])->one();

        // Return model
        return $model;
    }

    // Function get pasien
    private function getPasien($id)
    {
        // Declare model
        $model = [];

        // Get model
        $model = PasienV::find()->where(['pasien_id' => $id])->one();

        // Return model
        return $model;
    }

    // Function list penyakit
    private function getListPenyakit()
    {
        $data = JenisKasusPenyakit::find()
        ->andWhere(['is_active' => 't','is_deleted'=>'f'])
        ->orderBy('jeniskasuspenyakit_nama');
        $items = ArrayHelper::map($data->all(), 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');

        return $items;
    }

    // Function list kelas
    private function getListKelas()
    {
        $data = KelasPelayanan::find()
        ->andWhere(['is_active' => 't','is_deleted'=>'f'])
        ->orderBy('kelaspelayanan_nama');
        $items = ArrayHelper::map($data->all(), 'kelaspelayanan_id', 'kelaspelayanan_nama');

        return $items;
    }

    // Function list lookup
    private function getListLookup($type)
    {
        $data = Lookup::find()
        ->andWhere(['is_active' => 't','is_deleted'=>'f','lookup_type'=>$type]);
        $items = ArrayHelper::map($data->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    private function getListPekerjaan()
    {
        $data = Pekerjaan::find()
        ->andWhere(['is_active' => 't','is_deleted'=>'f'])
        ->orderBy('pekerjaan_nama');
        $items = ArrayHelper::map($data->all(), 'pekerjaan_id', 'pekerjaan_nama');

        return $items; 
    }

    public function actionCheckKunjungan($no_rekam_medik)
    {
        $db = Yii::$app->db;
        // bila suatu saat kembali
        // $sql = "SELECT
        //         tgl_pendaftaran::timestamp AS tgl_pendaftaran,
        //         (DATE_PART('hour', tgl_pendaftaran::timestamp)::integer) as hrs
        //     FROM pasienpulangrdrj_v
        //     WHERE 
        //         no_rekam_medik = '".$no_rekam_medik."'
        //     AND DATE_PART('Day',now() - tgl_pendaftaran::timestamptz) < 1
        // ";
        $sql = "SELECT *
            FROM pasienpulangrdrj_v
            WHERE 
                no_rekam_medik = '{$no_rekam_medik}'
            AND pasienadmisi_id IS NULL
            ORDER BY pendaftaran_id DESC
        ";
        $data = $db->createCommand($sql)->queryOne();

        return !empty($data) ? $data : false;
        // if (!empty($data)) {
        //     if ($data['hrs'] <= 24) {
        //         $status = true;
        //     } else {
        //         $status = false;
        //     }
        // } else {
        //     $status = false;
            
        // }
        // return true;
    }
}