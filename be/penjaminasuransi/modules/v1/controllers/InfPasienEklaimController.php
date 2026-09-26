<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter; 
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\InfoKlaimKirimOlView;
use app\modules\v1\models\InfoPasienBpjsView;
use app\modules\v1\models\InfoPasienBpjsDiagnosaView;
use app\modules\v1\models\InfoPasienBpjsKlaimView;
use app\modules\v1\models\KoreksiDiagnosaView;
use app\modules\v1\models\KoreksiDiagnosa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\KlaimInacbg;
use app\modules\v1\models\KlaimInacbgDetail;
use app\modules\v1\models\KlaimInacbgGroup;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\InfoKlaimInacbg;
use app\modules\v1\models\InfoKlaimInacbgDetail;
use app\modules\v1\models\LogError;
use app\modules\v1\models\SyInfoKlaimInacbg;
use app\modules\v1\models\SyInfoKlaimKirimOlView;
use app\modules\v1\models\SyKlaimInacbg;
use app\modules\v1\models\SyKunjungan;
use Doco\Services\InternalService;

class InfPasienEklaimController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienEklaim';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }


    public function actionInitIndex()
    {
        $result['ruangan'] = [];
        $result['penjamin'] = [];
        $result['status_verif'] = [];
        try {
            $result['ruangan'] = Yii::$app->runAction('v1/allow/get-ruangan', ['id'=>DocoConstants::INST_ID_RJ]);
            $result['ruangan'] = isset($result['ruangan']['response']['ruangan']) ? $result['ruangan']['response']['ruangan'] : [];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin', ['id'=>DocoConstants::PENJAMIN_BPJS]);
            $result['penjamin'] = isset($result['penjamin']['response']) ? $result['penjamin']['response'] : [];
            $result['status_verif'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type'=>'status_verifikasi']);
            $result['status_verif'] = isset($result['status_verif']['response']) ? $result['status_verif']['response'] : [];
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $tipe_klaim = $request->get('tipe_klaim');
        $jenis_tanggal = $request->get('jenis_tanggal');
        $tanggal = $request->get('tanggal');

        $model = new SyInfoKlaimKirimOlView;
        $query = $model::find();

        if ($tipe_klaim && $tipe_klaim !== 'RJ-RI') {
            $query->andWhere(['tipe' => $tipe_klaim]);
        }
        if ($jenis_tanggal && $tanggal) {
            $tanggal = date('Y-m-d', strtotime($tanggal));
            if ($jenis_tanggal == 'tgl_keluar') {
                $query->andWhere(['tgl_keluar' => $tanggal]);
            } else {
                $query->andWhere(['tgl_group' => $tanggal]);
            }
        }

        $query = $query->asArray()->all();
        return $query;
    }

    public function actionGetList()
    {
        $request = Yii::$app->request;
        $tipe_klaim = $request->get('tipe_klaim');
        $jenis_tanggal = $request->get('jenis_tanggal');
        $tanggal = $request->get('tanggal');

        $model = new SyInfoKlaimInacbg;
        $query = $model::find();  

        if ($tipe_klaim && $tipe_klaim !== 'RJ-RI') {
            $query->andWhere(['tipe' => $tipe_klaim]);
        }
        if ($jenis_tanggal && $tanggal) {
            $start = date('Y-m-d 00:00:00', strtotime($tanggal));
            $end = date('Y-m-d 23:59:59', strtotime($tanggal));

            if ($jenis_tanggal == 'tgl_keluar') {
                $query->andWhere(['between', 'tgl_keluar', $start, $end]);   
            } else {
                $query->andWhere(['between','tgl_group', $start, $end]);
            }
        }
        // $query->andWhere(['status_klaim' => true]);
        $query = $query->asArray()->all();
        return $query;   
    }

    public function actionGetProsedur()
    {
        $request = Yii::$app->request;
        $klaiminacbg_id = $request->get('klaiminacbg_id');

        $model = new InfoKlaimInacbgDetail;
        $query = $model::find();  
        $query->where(['klaiminacbg_id' => $klaiminacbg_id]);
        $query = $query->orderBy(['is_icdprimer' => SORT_ASC])->asArray()->all();
        return $query;   
    }

    public function actionKirimKlaimOnline()
    {   
        $request = Yii::$app->request;
        $post = $request->post();
        $tgl_awal = $post['tgl_awal'];
        $tgl_akhir = $post['tgl_akhir'];
        $jenis_rawat = $post['jenis_rawat'];
        $tipe_tanggal = $post['tipe_tanggal'];

        $connection = Yii::$app->db;
        try {
            $data = [
                'metadata'=>[
                    'method'=>'send_claim',
                ],
                'data'=>[
                    'start_dt' => $tgl_awal,
                    'stop_dt'  => $tgl_akhir,
                    'jenis_rawat' => $jenis_rawat,
                    'date_type'   => $tipe_tanggal,
                ],
            ];

            if ($tgl_awal != $tgl_akhir) {
                $response = self::simpanDataInacbgAll($tgl_awal, $tgl_akhir, $jenis_rawat, $tipe_tanggal);
            }else {
                $response = json_decode(DocoHelpers::restInacbgs($data), true);
            }

            $model = new LogError;
            $model->nama = 'Kirim Online Multiple';
            $model->pesan = json_encode($response);
            $model->tgl_error = date('Y-m-d h:i:s');
            $model->save();

            if($response['metadata']['code'] != 200){
                throw new \Exception("Terjadi Kesalahan");
            }  

            $res = $response['response']['data'];
            $arr_nosep = [];
            $arr_kunjungan = [];
            if ($res) {
                foreach ($res as $key => $value) {
                    if ($value['kemkes_dc_status'] == 'sent') {
                        $nosep = $value['nomor_sep'];
                        if (!in_array($nosep, $arr_nosep)) {
                            $arr_nosep[] = $nosep;
                        }
                    } 
                }
                
                $dataKunjungan = self::getKunjunganId($arr_nosep);
                foreach ($dataKunjungan as $key => $kunjungan) {
                    if(isset($kunjungan['kunjungan_id'])) {
                        $kunjunganid = $kunjungan['kunjungan_id'];
                        if (!in_array($kunjunganid, $arr_kunjungan)) {
                            $arr_kunjungan[] = $kunjunganid;
                        }
                    }
                }
                
                if ($arr_nosep) {
                    $update = SyKlaimInacbg::updateAll(['is_terkirim' => true], ['in', 'kunjungan_id', $arr_kunjungan]);
                    if($update){
                        return [
                            'status' => 200,
                            'title' => 'Proses Berhasil !',
                            'text' => 'Kirim Klaim (Online) berhasil! <br> Klaim yang terkirim sejumlah: '.$update.'<br> Status pengiriman kemenkes : Received'
                        ];
                    } else {
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal !',
                            'text' => 'Kirim Klaim (Online) Gagal! <br> Terjadi kesalahan <br> Jumlah Pengiriman Klaim yang gagal : ' .( count($res) - count($arr_nosep))
                        ];
                    }
                } else {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Kirim Klaim (Online) Gagal! <br> Terjadi kesalahan <br> Jumlah Pengiriman Klaim yang gagal : ' .( count($res) - count($arr_nosep))
                    ];
                }
            }else{
                // Handle kondisi dari INACBG NULL
                $tanggal = $tgl_awal;
                $model = new SyInfoKlaimInacbg;
                $query = $model::find();  

                if ($tgl_awal == $tgl_akhir) {
                    $start = date('Y-m-d 00:00:00', strtotime($tanggal));
                    $end = date('Y-m-d 23:59:59', strtotime($tanggal));
                    $query->andWhere(['between', 'tgl_keluar', $start, $end]);   
                }else{
                    $start = date('Y-m-d 00:00:00', strtotime($tgl_awal));
                    $end = date('Y-m-d 23:59:59', strtotime($tgl_akhir));
                    $query->andWhere(['between', 'tgl_keluar', $start, $end]);    
                }

                $query = $query->asArray()->all();

                foreach ($query as $key => $value) {
                  if(isset($value['no_sep'])) {
                    $nosep = $value['no_sep'];
                    if (!in_array($nosep, $arr_nosep)) {
                        $arr_nosep[] = $nosep;
                    }
                  }
                }

                $dataKunjungan = self::getKunjunganId($arr_nosep);
                foreach ($dataKunjungan as $key => $kunjungan) {
                    if(isset($kunjungan['kunjungan_id'])) {
                        $kunjunganid = $kunjungan['kunjungan_id'];
                        if (!in_array($kunjunganid, $arr_kunjungan)) {
                            $arr_kunjungan[] = $kunjunganid;
                        }
                    }
                }

                if ($arr_nosep) {
                    $update = SyKlaimInacbg::updateAll(['is_terkirim' => true], ['in', 'kunjungan_id', $arr_kunjungan]);
                    if($update){
                        return [
                            'status' => 200,
                            'title' => 'Proses Berhasil !',
                            'text' => 'Kirim Klaim (Online) berhasil! <br> Klaim yang terkirim sejumlah: '.$update.'<br> Status pengiriman kemenkes : Received'
                        ];
                    } else {
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal !',
                            'text' => 'Kirim Klaim (Online) Gagal! <br> Terjadi kesalahan <br> Jumlah Pengiriman Klaim yang gagal : ' .( count($res) - count($arr_nosep))
                        ];
                    }
                } else {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Kirim Klaim (Online) Gagal! <br> Terjadi kesalahan <br> Jumlah Pengiriman Klaim yang gagal : ' .( count($res) - count($arr_nosep))
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            throw new \Exception("Terjadi Kesalahan");
        }  
    }

    private static function getKunjunganId($arr_nosep)
    {
        $connection = Yii::$app->db;
        $tmpNosep = implode("','", $arr_nosep);
        $result = $connection->createCommand("SELECT kunjungan_id FROM sy_kunjungan WHERE nosep IN ('$tmpNosep')")->queryAll();
        return $result;
    }

    /**
     * Fungsi untuk menghandle penyimpanan semua data tanpa filter sama sekali
     */
    private static function simpanDataInacbgAll($tgl_awal, $tgl_akhir, $jenis_rawat, $tipe_tanggal)
    {
        $model = new SyInfoKlaimInacbg;
        $query = $model::find();  
        
        if ($tipe_tanggal && $tgl_awal) {
            $start = date('Y-m-d 00:00:00', strtotime($tgl_awal));
            $end = date('Y-m-d 23:59:59', strtotime($tgl_akhir));
            $query->andWhere(['between', 'tgl_keluar', $start, $end]);   
            $query->andWhere(['is_terkirim' => false]);
        }
        $query = $query->asArray()->all();
        $tmpResponse = [];

        foreach ($query as $key => $value) {
            if(isset($value['tgl_keluar'])) {
                $data = [
                    'metadata'=>[
                        'method'=>'send_claim',
                    ],
                    'data'=>[
                        'start_dt' => isset($value['tgl_keluar']) ? $value['tgl_keluar'] : $tgl_awal,
                        'stop_dt'  => isset($value['tgl_keluar']) ? $value['tgl_keluar'] : $tgl_awal,
                        'jenis_rawat' => $jenis_rawat,
                        'date_type'   => $tipe_tanggal,
                    ],
                ];
        
                $response = json_decode(DocoHelpers::restInacbgs($data), true);
                $tmpResponse[] = isset($response['metadata']['code']) ? $response['metadata']['code'] : null;
            }
        }

        $valueResponse = array_unique($tmpResponse, SORT_NUMERIC);
        if (isset($valueResponse[0])) {
            if($valueResponse[0] === 200) {
                return [
                    "metadata" => [
                        "code" => 200,
                        "message" => "Ok"
                    ],
                    "response" => [
                        "data" => null
                    ]
                ];
            }
        }

        return null;
    }

    public function actionKirimKlaimOnlineBatch()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $tgl_awal = isset($post['tgl_awal']) ? $post['tgl_awal'] : null;
        $tgl_akhir = isset($post['tgl_akhir']) ? $post['tgl_akhir'] : null;
        $jenis_rawat = isset($post['jenis_rawat']) ? $post['jenis_rawat'] : null;
        $tipe_tanggal = isset($post['tipe_tanggal']) ? $post['tipe_tanggal'] : null;
        $randString = isset($post['randString']) ? $post['randString'] : null;
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $ini = @parse_ini_file('../config/env/.env', true);

        if (! empty($tgl_akhir) && ! empty($tgl_awal) && ! empty($jenis_rawat) && ! empty($tipe_tanggal)) {

            (new InternalService)->sendTo([
                'Sirs' => [
                    'SinkronDataBpjs\KirimKlaimOnline' => [
                        'tgl_awal' => $tgl_awal,
                        'tgl_akhir' => $tgl_akhir,
                        'jenis_rawat' => $jenis_rawat,
                        'tipe_tanggal' => $tipe_tanggal,
                        'token' => $auth,
                        'xOwner' => $xOwner,
                        'unique_str' => $randString,
                        'ini' => $ini
                    ]
                ]
            ], true);
        }
                    
        return [
            'totalPerpage' => 100,
            'randString' => $randString,
            'tgl_awal' => $tgl_awal,   
            'tgl_akhir' => $tgl_akhir,
            'token' => $auth,
            'xOwner' => $xOwner,
        ];
    }
}