<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-24 15:55:28
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-18 16:38:26
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\models\Pasien;
use app\modules\v1\models\InfoKunjunganRsView;
use app\modules\v1\models\PembayaranPelayanan;
use app\modules\v1\models\TandaBuktiBayar;
use app\modules\v1\models\BayarUangMuka;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\InfoBayarUangMukaView;
use app\modules\v1\models\InfoBayarUangMukaDetailView;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\GabungPelayananDetail;
use app\modules\v1\models\CetakKwitansiBkm;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\payload\BayaUangMukaPayload;

use app\modules\v1\cache\Cache;
use Doco\components\DocoMessages;

use SirsCore\features\IntegrasiAkunting;

class TraPembayaranUangMukaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\BayarUangMuka';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["create"] = ["POST"];
        $verbs["detail"] = ["GET"];
        $verbs["delete"] = ["DELETE"]; 
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $payload = new BayaUangMukaPayload;
            $request = Yii::$app->request;
            $jwt = Yii::$app->jwt;
            $ruangan_id = !empty($jwt->ruangan_id) ? $jwt->ruangan_id : null;
            $payload->attributes = $request->post();
            if ($payload->validate()) {
                $dataPendaftaran = Pendaftaran::find()->select([
                    'pendaftaran_t.pendaftaran_id',
                    'pendaftaran_t.pasienadmisi_id',
                    'pendaftaran_t.pasien_id',
                    'pasien_m.nama_pasien',
                ])
                ->leftJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
                ->andWhere([
                    'pendaftaran_t.pendaftaran_id' => $payload->pendaftaran_id
                ])->asArray()->one();

                if (empty($dataPendaftaran)) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => DocoMessages::ERR_MESSAGE_PENDAFTARAN_NOT_EXIST
                    ]);
                }

                $pegawai1_id = !empty($jwt->user->pegawai_id) ? $jwt->user->pegawai_id : null;

                $confSistem = Cache::getKonfigSistem();
                $isPembulatan = isset($confSistem['is_pembulatankeatas']) ? $confSistem['is_pembulatankeatas'] : false;
                $satuanPembulatan = !empty($confSistem['satuanpembulatan']) ? $confSistem['satuanpembulatan'] : 0;

                $totalTerima = $payload->jumlah_uangmuka + $payload->biayaadministrasi;

                $pembulatan = DocoHelpers::pembulatan($totalTerima, $isPembulatan, $satuanPembulatan);

                $carapembayaran = !empty($payload->carapembayaran) ? DocoConstants::BAYAR_NONTUNAI : DocoConstants::BAYAR_TUNAI;
                if($carapembayaran == DocoConstants::BAYAR_NONTUNAI){
                    $is_tunai = FALSE;
                }else{
                    $is_tunai = TRUE;
                }
                /** Jenis Non Tunai */
                if (empty($payload->carapembayaran)) $payload->jenisnontunai_id = null;
                $jmlpembulatan = isset($pembulatan['pembulatan']) ? $pembulatan['pembulatan'] : 0;
                $uangditerima = isset($pembulatan['total']) ? $pembulatan['total'] : 0;

                $model = new BayarUangMuka;
                $model->pasienadmisi_id = !empty($dataPendaftaran['pasienadmisi_id']) ? $dataPendaftaran['pasienadmisi_id'] : null;
                $model->ruangan_id = $ruangan_id;
                $model->pasien_id = !empty($dataPendaftaran['pasien_id']) ? $dataPendaftaran['pasien_id'] : null;
                $model->pendaftaran_id = !empty($dataPendaftaran['pendaftaran_id']) ? $dataPendaftaran['pendaftaran_id'] : null;
                $model->tgl_uangmuka = $payload->tanggal_pembayaran;
                $model->jumlah_uangmuka = $payload->jumlah_uangmuka;
                $model->metode_pembayaran = $carapembayaran;
                $model->is_tunai = $is_tunai;
                $model->jenisnontunai_id = $payload->jenisnontunai_id;

                if ($model->save()) {
                    $bum_id = $model->bayaruangmuka_id;
                    $model_tbb = new TandaBuktiBayar;
                    $model_tbb->ruangan_id = $ruangan_id;
                    $model_tbb->bayaruangmuka_id = $bum_id;
                    $model_tbb->tglbuktibayar = $payload->tanggal_pembayaran;
                    $model_tbb->carapembayaran = $carapembayaran;
                    $model_tbb->darinama_bkm = !empty($dataPendaftaran['nama_pasien']) ? $dataPendaftaran['nama_pasien'] : null;
                    $model_tbb->sebagaipembayaran_bkm = 'pembayaran uang muka';
                    $model_tbb->jmlpembulatan = $jmlpembulatan;
                    $model_tbb->jmlpembayaran = $uangditerima;
                    $model_tbb->biayaadministrasi = $payload->biayaadministrasi;
                    $model_tbb->uangditerima = $uangditerima;
                    $model_tbb->uangkembalian = 0;
                    $model_tbb->pegawai1_id = !empty($jwt->user->pegawai_id) ? $jwt->user->pegawai_id : null;
                    $model_tbb->namapemilik_rek = $payload->namapemilik_rek;
                    $model_tbb->no_rek = $payload->no_rek;

                    if ($model_tbb->save()) {
                        $get_bum = BayarUangMuka::find()->where([
                            'bayaruangmuka_id' => $bum_id
                        ])->one();

                        $get_bum->tandabuktibayar_id = $model_tbb->tandabuktibayar_id;
                        if ($get_bum->save()) {
                            $transaction->commit();
                            IntegrasiAkunting::integratePembayaranUangMuka($bum_id);
                            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                                 'additional' => [
                                    'id_transaksi' => DocoHelpers::encrypt($bum_id)
                                ]
                            ]);
                        } else {
                            $transaction->rollBack();
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                                'data' => $get_bum->errors
                            ]);
                        }
                    } else {
                        $transaction->rollBack();
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $model_tbb->errors
                        ]);
                    }
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
            }

            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);

        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }

    //handle get data ajax onchange pendaftaran 
    //params yg dikirim pendaftaran id
    public function actionGetData($id)
    {
        $total_tagihan = InfoTagihanPasien::find()
            ->select([new \yii\db\Expression("SUM(sub_total) AS total_tagihan")])
            ->where(['ref_pendaftaran_id'=>$id])->scalar();

        $data_pasien = Pasien::getInfoPasienByPendaftaranId($id);

        $data_pembayaran = [];
        $data_pembayaran['total_tagihan'] = $total_tagihan ? $total_tagihan : 0 ;
        $return = ['data_pasien'=>$data_pasien, 'data_pembayaran'=>(count($data_pembayaran) > 0 ) ? $data_pembayaran : 'kosong'];
        return $return;
    }

    //action buat handle select no pendaftaran
    public function actionGetDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = isset($post['term']) ? $post['term'] : '';
        // $start = date('Y-m-d 00:00:00');
        // $end = date('Y-m-d 23:59:59');
        // if(isset($post['date'])){
        //     $newData = explode(' - ', $post['date']);
        //     if(count($newData) == 2) {
        //         $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
        //         $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
        //     }
        // }

        $sql = "SELECT 
                    tgl_pendaftaran, 
                    pendaftaran_id, 
                    no_pendaftaran, 
                    no_rekam_medik, 
                    nama_pasien 
                FROM infokunjunganrs_v 
                WHERE (no_pendaftaran ILIKE '%{$term}%' 
                OR no_rekam_medik ILIKE '%{$term}%' 
                OR nama_pasien ILIKE '%{$term}%') 
                AND pasienpulang_id IS NULL 
                GROUP BY tgl_pendaftaran, no_pendaftaran,pendaftaran_id, no_rekam_medik, nama_pasien order by no_pendaftaran asc limit 10";

        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;

    }

    //ambil data pembayaran berdasarkan uang muka
    public function getDataPembayaran($id)
    {
        try {
            $sql = "SELECT 
                    bum.bayaruangmuka_id,
                    tbb.tandabuktibayar_id,tbb.jmlpembulatan,tbb.jmlpembayaran,tbb.biayaadministrasi,tbb.uangditerima,tbb.carapembayaran,tbb.namapemilik_rek,tbb.no_rek,
                    pp.tgl_pembayaran,pp.total_biayapelayanan,pd.pasienadmisi_id,pd.ruangan_id as ruangan_pd, ad.ruangan_id as ruangan_ad

                    FROM bayaruangmuka_t bum
                    LEFT JOIN pembayaranpelayanan_t pp on bum.pendaftaran_id = pp.pendaftaran_id
                    LEFT JOIN tandabuktibayar_t tbb on pp.tandabuktibayar_id = tbb.tandabuktibayar_id
                    LEFT JOIN pendaftaran_t pd on bum.pendaftaran_id = pd.pendaftaran_id
                    LEFT JOIN pasienadmisi_t ad on pd.pasienadmisi_id = ad.pasienadmisi_id
                    where bum.pendaftaran_id = '{$id}' and bum.is_deleted = false and pp.is_deleted = false and tbb.is_deleted = false";
            $data = \Yii::$app->db->createCommand($sql)->queryOne();
            return $data;
        }  catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }

    //action buat handle list data buat select2
    public function actionGetListData()
    {
        try {
            $data = $this->getDataPasien()->asArray()->all();
            $data_rm = $this->getNoRm();
            $return = ['data_pendaftaran'=>$data,'data_rm'=>$data_rm];
            return $return; 
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
        
    }
    //action buat handle modal data pendaftaran
    public function actionGetPendaftaran()
    {
        try {
            $data = $this->getDataPasien()->asArray()->all();
            return $data;
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
    //menampung data pasien
    public function getDataPasien($id = null)
    {
        $data = InfoKunjunganRsView::find()
                                     ->select([
                                        'nama_pasien',
                                        'no_rekam_medik',
                                        'no_pendaftaran',
                                        'pendaftaran_id',
                                        'carabayar_nama',
                                        'penjamin_nama',
                                        'kelaspelayanan_nama',
                                        'tgl_pendaftaran',   
                                        'pasien_id',
                                        'carabayar_id',
                                        'penjamin_id',
                                        'alamat_pasien',
                                     ]);
        if($id){
            $data->where(['pendaftaran_id'=>$id]);
        }                                    
        return $data;
    }

    //action generate penomoran
    //1 buat transaksi pembayaran
    //2 buat tanda bukti bayar
    public function generateId($lookup)
    {
        $sql = "select penomoran_nama,prefix,last_generate,last_number from penomoran_k where penomoran_id = '{$lookup}'";
        $id = \Yii::$app->db->createCommand($sql)->queryOne();
        $prefix = substr($id['last_generate'], 0,3);
        $date = substr($id['last_generate'], 3,8);
        $number = substr($id['last_generate'], -5);
        $now = date('Ymd');         
        $numPrefix = "";
        if($date != $now){
            $newDate = $now;
            $newNumber = '0001';
        }else{              
            $newDate = $date;
            $newNumber = $number+1;
            if(strlen($newNumber) == 1){
                $numPrefix = "000";
            }else if(strlen($newNumber) == 2){
                $numPrefix = "00";
            }else if(strlen($newNumber) == 3){
                $numPrefix = "0";                   
            }else{
                $numPrefix = "";
            }               
        }
        $newId = $prefix.$newDate.$numPrefix.$newNumber;
        $this->updateId($lookup, $newId, $numPrefix.$newNumber);
        return $newId;
    }
    //action buat update penomoran
    public function updateId($lookup, $newId, $last_number)
    {
        $sql = \Yii::$app->db->createCommand()->update('penomoran_k', ['last_generate'=>$newId,'last_number'=>$last_number], "penomoran_nama = '{$lookup}'")->execute();
    }

    //action buat ambil data pasien di modal
    public function actionDataPasien(){
        $model = new InfoKunjunganRsView;
        $query = $model::find(true);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }
    /**
    * @controller actionPrintKwitansi
    * @attribute #no_kwitansi# => no kwitansi 
    * @attribute #nama_pasien# => nama pasien 
    * @attribute #nama_kasir# => nama kasir 
    * @attribute #jumlah_diterima# => jumlah diterima 
    * @attribute #no_pendaftaran# => no pendaftaran 
    * @attribute #tgl_uangmuka# => tanggal uang muka 
    * @attribute #terbilang# => tanggal uang muka 
    **/
    public function actionPrintKwitansi()
    {
        try{
            $model = new CetakKwitansiBkm;
            $request = Yii::$app->request;
            $id = $request->post('id');
            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }

            $data = $model::find()->where(['bayaruangmuka_id'=>$id])->one();
            if(!$data){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#no_kwitansi#' => isset($data->no_kwitansi) ? $data->no_kwitansi : '',
                '#nama_pasien#' => isset($data->nama_pasien) ? $data->nama_pasien : '',
                '#nama_kasir#' => isset($data->kasir) ? $data->kasir:'',
                '#jumlah_diterima#' => isset($data->total_terbayar)? 'Rp. '.number_format($data->total_terbayar, 0, ',','.') :'',
                '#no_pendaftaran#'=>isset($data->no_pendaftaran) ? $data->no_pendaftaran:'',
                '#tgl_uangmuka#'=>isset($data->tgl_pembayaran) ? date('d-m-Y', strtotime($data->tgl_pembayaran)) : '',
                '#terbilang#' => isset($data->total_terbayar) ? DocoHelpers::Terbilang($data->total_terbayar). ' Rupiah' : '',
            ];
            $print->Output();
        }catch (Exception $e){
            // asd
        }
    }

    /**
    * @controller actionPrintBkm
    * @attribute #no_bkm# => no bkm 
    * @attribute #nama_pasien# => nama pasien 
    * @attribute #nama_kasir# => nama kasir 
    * @attribute #jumlah_diterima# => jumlah diterima 
    * @attribute #no_pendaftaran# => no pendaftaran 
    * @attribute #tgl_uangmuka# => tanggal uang muka 
    * @attribute #terbilang# => tanggal uang muka 
    **/
    public function actionPrintBkm()
    {
        try{
            $model = new CetakKwitansiBkm;

            $request = Yii::$app->request;
            $id = $request->post('id');
            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }

            $data = $model::find()->where(['bayaruangmuka_id'=>$id])->one();
            if(!$data){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#no_bkm#' => isset($data->no_bkm
                )?$data->no_bkm:'',
                '#nama_pasien#' => isset($data->nama_pasien
                )?$data->nama_pasien:'',
                '#nama_kasir#' => isset($data->kasir
                )?$data->kasir:'',
                '#jumlah_diterima#' => isset($data->total_terbayar)? 'Rp. '.number_format($data->total_terbayar, 0, ',','.') :'',
                '#no_pendaftaran#'=>isset($data->no_pendaftaran) ? $data->no_pendaftaran :'',
                '#tgl_uangmuka#'=>isset($data->tgl_pembayaran
                )? date('d-m-Y', strtotime($data->tgl_pembayaran)) : '',
                '#terbilang#' => isset($data->total_terbayar) ? DocoHelpers::Terbilang($data->total_terbayar). ' Rupiah' : '',

            ];
            $print->Output();
        }catch (Exception $e){
            // asd
        }
    }
}

