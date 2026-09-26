<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-29 15:53
 */

namespace app\modules\v1\controllers;

use Doco\rabbitmq\RabbitBgProcess;
use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfReservasiPoliklinik;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoPendaftaranOnlineView;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;

class InfReservasiPoliklinikController extends DocoActiveController
{
    protected $allowAction = [
        '*'
    ];
    public $modelClass = 'app\modules\v1\models\InfReservasiPoliklinik';

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

    public function actionIndex($isPagination=true)
    {
        $get = Yii::$app->request->get();
        try {
            $model = new InfoPendaftaranOnlineView;
            $query = $model::find();
            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:59');
            if(isset($get['advanced-filter'])){
                if(isset($get['advanced-filter']['tgl_kunjungan'])){
                    $explode = explode(' - ', $get['advanced-filter']['tgl_kunjungan']);
                    $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    unset($_GET['advanced-filter']['tgl_kunjungan']);
                }

                if(isset($get['advanced-filter']['nama_pasien'])){
                    $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                    unset($_GET['advanced-filter']['nama_pasien']);
                }

                if(isset($get['advanced-filter']['is_checkin'])){
                    $query->andWhere(['is_checkin' => $get['advanced-filter']['is_checkin']]);
                    unset($_GET['advanced-filter']['is_checkin']);
                }

                if(isset($get['advanced-filter']['no_bpjs'])){
                    $query->andWhere(['no_bpjs' => $get['advanced-filter']['no_bpjs']]);
                    unset($_GET['advanced-filter']['no_bpjs']);
                }

                if(isset($get['advanced-filter']['ruangan_id']) && is_array(explode(',', $get['advanced-filter']['ruangan_id'])) ){
                    $query->andWhere(['in', 'ruangan_id', explode(',', $get['advanced-filter']['ruangan_id'])]);
                    unset($_GET['advanced-filter']['ruangan_id']);
                }
            }
            if(isset($get['no_pendaftaranol']) && !empty($get['no_pendaftaranol'])){
                $query->andWhere(['LOWER(no_pendaftaranol)' => strtolower($get['no_pendaftaranol'])]);
            }

            if(isset($nama_pasien)){
                $query->andWhere(['or', ['like', 'LOWER(nama_pasien)', strtolower($nama_pasien)], ['like', 'LOWER(nama_pasien_ol)', strtolower($nama_pasien)]]);
            }

            $query->andWhere(['between', 'tgl_kunjungan', $tgl_awal, $tgl_akhir]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            if ($isPagination == false) {
                # code...
                return $query->all();
            } else {
                # code...
                return new ActiveDataProvider([
                    'query' => $query,
                ]);
            }
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionBundleInformasi()
    {
        $poliklinik = $dokter = $carabayar = $penjamin = $statusdaftar = $jenispendaftaran = $statuscheckin = [];
        $result = [
            'poliklinik' => [],
            'dokter' => [],
            'carabayar' => [],
            'penjamin' => [],
            'statusdaftar' => [],
            'jenispendaftaran' => [],
            'jeniskelamin' => [],
            'konfig_batal_hadir_reservasi' => false,
        ];
        try {
            $poliklinik = Yii::$app->runAction('/v1/allow/list-ruangan', ['instalasi_id' => DocoConstants::VAR_I_RJ, 'is_executive' => Yii::$app->request->get('is_executive')]);
            $dokter = Yii::$app->runAction('/v1/allow/list-dokter-rajal');
            $carabayar = Yii::$app->runAction('/v1/allow/list-cara-bayar');
            $penjamin = Yii::$app->runAction('/v1/allow/list-penjamin');
            $getLookup = Lookup::find()->where(['lookup_type' => ['status_daftar_ol', 'jenis_reservasi', 'status_checkin']])->asArray()->all();
            foreach ($getLookup as $key => $value) {
                if($value['lookup_type'] == 'status_daftar_ol'){
                    $statusdaftar[] = $value;
                }else if($value['lookup_type'] == 'status_checkin'){
                    $statuscheckin[] = $value;
                }else{
                    $jenispendaftaran[] = $value;
                }
            }
            $listJk = $this->lookup_type->actionGetLookupType('jenis_kelamin');
            $result = [
                'poliklinik' => ArrayHelper::map($poliklinik['response']['data'], 'ruangan_id', 'ruangan_nama'),
                'dokter' => $dokter['response'],
                'carabayar' => $carabayar['response'],
                'penjamin' => $penjamin['response'],
                'statusdaftar' => ArrayHelper::map($statusdaftar, 'lookup_value', 'lookup_value'),
                'jenispendaftaran' => ArrayHelper::map($jenispendaftaran, 'lookup_name', 'lookup_name'),
                'jeniskelamin' => ArrayHelper::map($listJk, 'lookup_id', 'lookup_value'),
                'statuscheckin' => ArrayHelper::map($statuscheckin, 'lookup_value', 'lookup_name'),
                'konfig_batal_hadir_reservasi' => DocoConstansId::actionGetId('batal_hadir_reservasi'),
            ];
        } catch (Exception $e) {
            $result = [
                'poliklinik' => [],
                'dokter' => [],
                'carabayar' => [],
                'statusdaftar' => [],
                'jenispendaftaran' => [],
                'jeniskelamin' => [],
                'statuscheckin' => [],
                'konfig_batal_hadir_reservasi' => false,
            ];
        }
        return $result;
    }
    public function actionExportExcel()
    {
        $get = Yii::$app->request->get();
        $model = new InfoPendaftaranOnlineView;
        $header = $footer = [];
        $query = $model::find();
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $tgl_periode_awal = date('d-M-Y');
        $tgl_periode_akhir = date('d-M-Y');
        if(isset($get['advanced-filter'])){
            if(isset($get['advanced-filter']['tgl_kunjungan'])){
                $explode = explode(' - ', $get['advanced-filter']['tgl_kunjungan']);
                $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $tgl_akhir= date('Y-m-d 23:59:59', strtotime($explode[1]));
                $tgl_periode_awal = date('d-M-Y', strtotime($explode[0]));
                $tgl_periode_akhir = date('d-M-Y', strtotime($explode[1]));
                unset($_GET['advanced-filter']['tgl_kunjungan']);
                $header['Periode'] = $tgl_periode_awal .' - '.$tgl_periode_akhir;
            }
            if(isset($get['advanced-filter']['no_antrian'])){
                $header['No. Antrian'] = $get['advanced-filter']['no_antrian'];
            }
            if(isset($get['advanced-filter']['no_rekam_medik'])){
                $header['Nomor Rekam Medik'] = $get['advanced-filter']['no_rekam_medik'];
            }
            if(isset($get['advanced-filter']['nama_pasien'])){
                $header['Nama Pasien'] = $get['advanced-filter']['nama_pasien'];
            }
            if(isset($get['advanced-filter']['no_pendaftaranol'])){
                $header['No. Pendaftaran'] = $get['advanced-filter']['no_pendaftaran'];
            }
        }
        $query->andWhere(['between', 'tgl_kunjungan', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        foreach ($data as $index => $value) {
            if(isset($get['advanced-filter'])){
                $filter = $get['advanced-filter'];
                if(isset($filter['ruangan_id'])){
                    $header['Poli Tujuan'] = $value->ruangan_nama;
                }
                if(isset($filter['pegawai_id'])){
                    $header['Dokter'] = $value->nama_pegawai;
                }
                if(isset($filter['carabayar_id'])){
                    $header['Cara Bayar'] = $value->carabayar_nama;
                }
                if(isset($filter['penjamin_id'])){
                    $header['Penjamin'] = $value->penjamin_nama;
                }
                if(isset($filter['status_daftar_ol'])){
                    $header['Status Pendaftaran'] = $value->status_daftar;
                }
                if(isset($filter['jenis_reservasi'])){
                    $header['Jenis Pendaftaran'] = $value->jenis_reservasinama;
                }
            }
            $newdata = [];
            $newdata['No. Antrian'] = $value->no_antrian;
            $newdata['Info Pasien'] = $value->no_pendaftaranol. ' - '.$value->no_rekam_medik. ' - '. $value->nama_depan . (($value->nama_pasien) ? $value->nama_pasien : $value->nama_pasien_ol);
            $newdata['Tanggal Lahir'] = date('d M Y', strtotime($value->tanggal_lahir));
            $newdata['Keterangan'] = $value->keterangan;
            $newdata['No. Asuransi'] = $value->no_asuransi;
            $newdata['Poli Tujuan'] = $value->ruangan_nama;
            $newdata['Dokter'] = $value->nama_pegawai;
            $newdata['Cara Bayar'] = $value->carabayar_nama;
            $newdata['Penjamin'] = $value->penjamin_nama;
            $newdata['Jam Pelayanan'] = date('H:i', strtotime($value->jam_mulai)) . ' - '.date('H:i', strtotime($value->jam_tutup));
            $newdata['Tanggal Daftar'] = DocoHelpers::convDateTime($value->tgl_pendaftaran);
            $newdata['Tanggal Kunjungan'] = DocoHelpers::convDateTime($value->tgl_kunjungan);
            $newdata['Status Pendaftaran'] = $value->status_daftar;
            $newdata['Jenis Pendaftaran'] = $value->jenis_reservasinama;
            $result[] = $newdata;
        }
        $filePath = DocoHelpers::exportExcel('Informasi Reservasi Poli', $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #table# => table
    * @attribute #periode# => periode
    **/
    public function actionExportPdf()
    {
        $get = Yii::$app->request->get();
        $model = new InfoPendaftaranOnlineView;
        $header = $footer = [];
        $query = $model::find();
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $tgl_periode_awal = date('d-M-Y');
        $tgl_periode_akhir = date('d-M-Y');
        if(isset($get['advanced-filter'])){
            if(isset($get['advanced-filter']['tgl_kunjungan'])){
                $explode = explode(' - ', $get['advanced-filter']['tgl_kunjungan']);
                $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $tgl_akhir= date('Y-m-d 23:59:59', strtotime($explode[1]));
                $tgl_periode_awal = date('d-M-Y', strtotime($explode[0]));
                $tgl_periode_akhir = date('d-M-Y', strtotime($explode[1]));
                unset($_GET['advanced-filter']['tgl_kunjungan']);
            }
            if(isset($get['advanced-filter']['no_antrian'])){
                $header['No. Antrian'] = $get['advanced-filter']['no_antrian'];
            }
            if(isset($get['advanced-filter']['no_rekam_medik'])){
                $header['Nomor Rekam Medik'] = $get['advanced-filter']['no_rekam_medik'];
            }
            if(isset($get['advanced-filter']['nama_pasien'])){
                $header['Nama Pasien'] = $get['advanced-filter']['nama_pasien'];
            }
            if(isset($get['advanced-filter']['no_pendaftaranol'])){
                $header['No. Pendaftaran'] = $get['advanced-filter']['no_pendaftaran'];
            }
        }
        $periode = $tgl_periode_awal .' - '.$tgl_periode_akhir;
        $query->andWhere(['between', 'tgl_kunjungan', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        $print = new DocoPrint();
        $print->attributes = [
            '#table#' => $this->renderPartial('index', [
                'data' => $data,
            ]),
            '#periode#' => $periode,
        ];
        $print->Output();
    }

    public function actionAutoRegister() {
        $request = Yii::$app->request;
        $payload = $request->post('reservationItems', []);
        $processKey = $request->post('processKey', '');
        
        if ($payload) {
            foreach($payload as $key => $value) {
                (new RabbitBgProcess())->send([
                    'reservationItem' => $value,
                    'processKey' => $processKey,
                ], 'bulk_register_reservasi', 'bulk_register');
            }
        }

        return [
            'message' => 'Success set Registration Items Queue',
        ];
    }
}
