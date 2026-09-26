<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-01 13:56:48
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-18 17:22:07
 */

use Yii;
use yii\data\ActiveDataProvider;
use  Doco\models\InfoKunjunganRsView;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPasienSudahBayarView;
use app\modules\v1\models\RincianTagihanPasienSudahBayar;
use app\modules\v1\models\PembayaranPelayanan;
use app\modules\v1\models\CetakKwitansiBkm;
use app\modules\v1\models\ReturTagihanR;
use app\modules\v1\models\ReturTagihanDetailR;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\Pembayaran;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\FreezeBilling;
use app\modules\v1\models\KonsulPoli;
use Doco\components\DocoConstansId;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\InfoPasienMcuView;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\LoginPemakai;
use Doco\models\kasir\InvoiceSudahBayarDetailView;
use Doco\models\DocMapping;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use Doco\components\NoCountDataProvider;
use Doco\rabbitmq\RabbitBgProcess;
use yii\web\UploadedFile;

class InfPasienSudahBayarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PembayaranPelayanan';

    /**
     * Untuk Kebutuhan Integerasi GE
     * @var array
     */
    public $messageBroker = [
        'cancel' => [
            'services' => [
                'Mhg' => [
                    'BslCancelBill' => [
                        'payload' => ['id']
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
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

    public function actionIndex()
    {
        $data = $this->getData();
        $query = isset($data['query']) ? $data['query'] : [];
        return new NoCountDataProvider([
            'query' => $query,
        ]);
    }


    public function actionCancel()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $pembayaran_id = $request->get('pembayaran_id', null);
        $penjualanresep_id = $request->get('penjualanresep_id', null);
        $alasan_batal = $request->get('alasan_batal', null);
        $password = $request->get('password', null);
        $post = $request->post();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $cacheItem = Yii::$app->cache;
        $helpers = new DocoHelpers;
        
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
         try {
            $pembayaranPelayananId = [];
            $pembayaranPelayanan = PembayaranPelayanan::find()->select(['pembayaranpelayanan_id'])->where(['pembayaran_id' => $pembayaran_id])->all();
            if(!empty($pembayaranPelayanan)) {
                foreach ($pembayaranPelayanan as $key => $value) {
                    $pembayaranPelayananId[] = $value['pembayaranpelayanan_id'];
                }
            }

            $cekRetur = InfoPasienSudahBayarView::find()->where(['returbayarpelayanan_id' => $pembayaranPelayananId])->all();
            if($cekRetur) {
                return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => 'Pembayaran Tidak Dapat Dibatalkan, karena Sudah Ada Retur.'
                ]);
            }
            if(empty($penjualanresep_id) && $penjualanresep_id != 'null') {
                $cekFreezBilling = FreezeBilling::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
                if(!empty($cekFreezBilling)){
                    $status_pendaftaran = isset($cekFreezBilling['status']) ? $cekFreezBilling['status'] : 0 ;
                    if($status_pendaftaran == 1 ){
                        $errorMessage = 'No Pendaftaran sudah dibekukan.';
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal',
                            'text' => 'Nomor Pendaftaran sudah dibekukan.'
                        ];
                    }
                }
            }

            // Cek pasien mcu
            if (!empty($pendaftaran_id)) {
                $cekMcu = Pendaftaran::find()->select(['instalasi_id as instalasi_id1'])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
                $mcuId = (new DocoConstansId)->actionGetId('MCU');
                if ($cekMcu['instalasi_id1'] == $mcuId) {
                    $cekPeriksa = InfoPasienMcuView::find()->select('status_periksa')->where(['pendaftaran_id' => $pendaftaran_id])->one();
                    if ($cekPeriksa['status_periksa'] == DocoConstants::STATUS_PERIKSA_MCU_SELESAI) {
                        return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                            'text' => 'Pembayaran Tidak Dapat Dibatalkan, karena pasien sudah selesai pemeriksaan.'
                        ]);
                    }
                }
            }
            
            $user = Yii::$app->jwt->user;
            $now = date('Y-m-d H:i:s');
            $tindakanid = [];
            $this->actionHistoryCancel($pembayaran_id, $pembayaranPelayananId);
            $deletedArr = "is_deleted = true, deleted_date = '{$now}', deleted_by = {$user->loginpemakai_id}";
            $inCondition = "(" . implode(",", $pembayaranPelayananId) . ")";
            // covered by trigger ada proses delete ke pembayaran_t
            $updateSudahBayar = $connection->createCommand("update pembayaranpelayanan_t set {$deletedArr} where pembayaranpelayanan_id IN {$inCondition} and is_deleted = false")->execute();
            $updatePembayaran = $connection->createCommand("update pembayaran_t set {$deletedArr} , alasan_batal = '{$alasan_batal}' where pembayaran_id = {$pembayaran_id}")->execute();
            $cekGabungBilling = $this->getGabungTagihan($pendaftaran_id);
            if(!empty($cekGabungBilling)) {
                $pendaftaranId = isset($cekGabungBilling['pendaftaran_id']) ? $cekGabungBilling['pendaftaran_id'] : null;
                // $conditionPendaftaranId = '('.$pendaftaranId.','.$pendaftaran_id.')';
                Pendaftaran::updateAll(['status_bayar' => DocoConstants::BELUM_LUNAS], [
                    'pendaftaran_id' => [$pendaftaranId, $pendaftaran_id],
                ]);
                // $connection->createCommand("update pendaftaran_t set status_bayar = {$statusBelumLunas} where pendaftaran_id IN {$conditionPendaftaranId}")->execute();
            }
            
            if(!is_null($pendaftaran_id)) {
                $cekPendaftaran = Pendaftaran::find('pendaftaran_id' )->where(['pendaftaran_id' => $pendaftaran_id])->one(); 
                $instalasiId = ($cekPendaftaran) ? $cekPendaftaran['instalasi_id'] : null;
                $constantID = new DocoConstansId;
                $instMcu = $constantID->actionGetId('MCU');

                // WIP nanti proses ini di pindahkan ke service internal
                if($instalasiId == $instMcu) {
                    $konsulPoli = KonsulPoli::find('pendaftaran_id')->where(['pendaftaran_id' => $pendaftaran_id])->all();
                    if(!empty($konsulPoli)) {
                        Konsulpoli::updateAll(['status_periksa' => DocoConstants::STATUS_PERIKSA_ANTR_KASIR], [
                            'pendaftaran_id' => $pendaftaran_id,
                        ]);
                    }
                }
            }

            $cacheItem->delete('tagihan-pasien-'.$pendaftaran_id);
            $cacheItem->delete('history-trans-'.$pendaftaran_id);
            $transaction->commit();
            
            (new RabbitBgProcess())->send([
                'type_sinkron' => "hapus",
                'pendaftaran_id' => $pendaftaran_id,
                'pembayaran_id' => $pembayaran_id
            ], 'integrasi_eklaim', 'sync_data');
            
            return [
                'status' => 200,
                'title' => 'Proses Berhasil',
                'text' => 'Batal Pembayaran Berhasil'
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionHistoryCancel($pembayaran_id, $pembayaranPelayananId)
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $user = Yii::$app->jwt->user->pegawai_id;
            $idParent = null;
            $header = Pembayaran::find()->select([
                'pembayaran_id',
                'no_pembayaran',
                'created_by',
                'total_ditagihkan'
            ])->where(['pembayaran_id' => $pembayaran_id])->one();
            
            $modelHeader = new ReturTagihanR;
            $modelDetail = new ReturTagihanDetailR;
            $modelHeader->pembayaran_id = isset($header->pembayaran_id) ? $header->pembayaran_id : null;
            $modelHeader->no_tagihan = isset($header->no_pembayaran) ? $header->no_pembayaran : null;
            $modelHeader->pegawaipembayaran_id = isset($header->created_by) ? $header->created_by : null;
            $modelHeader->pegawairetur_id = isset($user) ? $user : null;
            $modelHeader->tgl_returtagihan = date('Y-m-d H:i:s');
            $modelHeader->total_returtagihan = isset($header->total_ditagihkan) ? $header->total_ditagihkan : null;
            $modelHeader->keterangan = null;
            if ($modelHeader->save(false)) {
                $idParent = $modelHeader->returtagihan_id;
                $detail = RincianTagihanPasienSudahBayar::find()
                    ->where(['pembayaranpelayanan_id' => $pembayaranPelayananId])->all();
                
                $obatalkespasien_id = null;
                $tindakanpelayanan_id = null;
                $dataDetail = [];
                foreach ($detail as $key => $value) {
                    if ($value->is_obat == true) {
                        $obatalkespasien_id = $value->pelayanan_id;
                    }
                    if ($value->is_obat == false) {
                        $tindakanpelayanan_id = $value->pelayanan_id;
                    }
                    $dataDetail[] = [
                        'returtagihan_id' => $idParent,
                        'tindakanpelayanan_id' => $tindakanpelayanan_id,
                        'obatalkespasien_id' => $obatalkespasien_id,
                        'nama_tagihan' => $value['tindakan_obat_nama'],
                        'qty_tagihan' => $value['qty'],
                        'tarif_tagihan' => $value['sub_total'],
                    ];
                }
                if (!empty($dataDetail)) {
                    $modelDetail::batchInsert($dataDetail, false);
                }
                $transaction->commit();
                return [
                    'message' => 'tagihan berhasil di cancel'
                ];
            }else{
                return [
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if ($post['date']) {
            $newData = explode(' - ', $post['date']);
            if (count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select pendaftaran_id, no_pendaftaran from infopasiensudahbayar_v where no_pendaftaran LIKE '%{$term}%'
            and tgl_pembayaran BETWEEN '{$start}' AND'{$end}'
            group by no_pendaftaran, pendaftaran_id
            order by no_pendaftaran asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGetDataPembayaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if ($post['date']) {
            $newData = explode(' - ', $post['date']);
            if (count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select pendaftaran_id, no_pembayaran from infopasiensudahbayar_v where no_pembayaran LIKE '%{$term}%'
            and tgl_pembayaran BETWEEN '{$start}' AND'{$end}'
            group by no_pembayaran, pendaftaran_id
            order by no_pembayaran asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGetDetailPembayaran($id='', $pembayaranpelayanan_id='', $tipe_pasien = null)
    {
        $primary_field = 'pendaftaran_id';
        if ($tipe_pasien == 'pasien_bebas') {
            $primary_field = 'penjualanresep_id';
        }

        $pembayaranPelayanan = PembayaranPelayanan::find()->where([$primary_field => $id ])->all();
        if(!empty($pembayaranPelayanan)) {
            $listPembayaranPelayananId = [];
            foreach ($pembayaranPelayanan as $key => $value) {
                $listPembayaranPelayananId[] = $value['pembayaranpelayanan_id'];
            }
        }
        $inCondition = "(" . implode(",", $listPembayaranPelayananId) . ")";
        $query = Yii::$app->db->createCommand("
            SELECT * FROM invoicesudahbayardetail_v WHERE $primary_field = $id AND pembayaranpelayanan_id IN $inCondition
        ")->queryAll();
        $header = Yii::$app->db->createCommand("
            SELECT * FROM infopasiensudahbayar_v WHERE $primary_field = $id
        ")->queryOne();
        
        return [
            'detail' => $query,
            'header' => $header
        ];
    }

    public function actionDataDetailTindakan($id="", $type="tindakan",$ppId="", $tipe_pasien = null)
    {
        $model = new InvoiceSudahBayarDetailView;

        $primary_field = 'pendaftaran_id';
        if ($tipe_pasien == 'pasien_bebas') {
            $primary_field = 'penjualanresep_id';
        }
        
        $query = $model::find()->where([$primary_field => $id]);
        if($type == "tindakan"){
            $query->andWhere(['NOT IN','instalasi_id',[DocoConstants::INST_ID_APT,DocoConstants::INST_ID_LAB,DocoConstants::INST_ID_RAD]])->andWhere(['is_obat'=>false]);
        }else if($type == "lab"){
            $query->andWhere(['instalasi_id'=>DocoConstants::INST_ID_LAB])->andWhere(['is_obat'=>false]);
        }else if($type == "rad"){
            $query->andWhere(['instalasi_id'=>DocoConstants::INST_ID_RAD])->andWhere(['is_obat'=>false]);
        }else if($type == "obat"){
            $query->andWhere(['is_obat'=>true]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
          'query' => $query,
        ]);
    }
    public function actionGetApi()
    {
        $result['ruangan'] = [];
        $result['instalasi'] = [];
        $result['carabayar'] = [];
        $result['penjamin'] = [];
        try{
            $listInstalasi = [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD,DocoConstants::INST_ID_RI,DocoConstants::INST_ID_RAD, DocoConstants::INST_ID_LAB, DocoConstants::INST_ID_BEDAH,DocoConstants::INST_ID_APT];
            $listInstalasi = implode(',', $listInstalasi);
            $result['ruangan'] = Yii::$app->runAction('v1/allow/get-ruangan', [
                'listInstalasi'=> $listInstalasi,
            ]);
            $result['ruangan'] = $result['ruangan']['response'];
            $result['instalasi'] = Yii::$app->runAction('v1/allow/get-instalasi',[
                'listInstalasi'=> $listInstalasi,
            ]);
            $result['instalasi'] = $result['instalasi']['response'];
            $result['carabayar'] = Yii::$app->runAction('v1/allow/get-cara-bayar');
            $result['carabayar'] = $result['carabayar']['response'];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin');
            $result['penjamin'] = $result['penjamin']['response'];
            return $result;
        } catch(\Exception $e){
            return $result;
        }
    }
    /**
    * @controller actionExportPdf
    * @attribute #table_rincian# => table
    **/
    public function actionExportPdf()
    {
        $dataLab = [];
        $dataRadiologi = [];
        $dataObat = [];
        $dataTindakan = [];
        $value = 0;
        $request = Yii::$app->request;
        $id = $request->get('pdf_id',null);
        $tipe_pasien = trim($request->get('tipe_pasien',null));
        $pendaftaran_field = ($tipe_pasien == 'pasien_bebas') ? 'penjualanresep_id' : 'pendaftaran_id';
        $pembayaranPelayanan = PembayaranPelayanan::find()->where(['pendaftaran_id' => $id])->all();
        $listPembayaran = [];
        $inCondition = "";
        if(!empty($pembayaranPelayanan)) {
            foreach ($pembayaranPelayanan as $key => $value) {
                $listPembayaran[] = $value['pembayaranpelayanan_id'];
            }
            $inCondition = "AND pembayaranpelayanan_id IN (" . implode(",", $listPembayaran) . ")";
        }
        $detailPasien = InfoPasienSudahBayarView::find()->where([$pendaftaran_field => $id])->one();
        $detailTindakan = Yii::$app->db->createCommand("
            SELECT * FROM invoicesudahbayardetail_v
            WHERE $pendaftaran_field = $id $inCondition
        ")->queryAll();
        
        if(!empty($detailTindakan)) {
            foreach ($detailTindakan as $key => $value) {
                $isObat = isset($value['is_obat']) ? $value['is_obat'] : false;
                if(!$isObat) {
                    if($value['instalasi_id'] == DocoConstants::INST_ID_LAB) {
                        $dataLab[] = $value;
                    }
                    elseif($value['instalasi_id'] == DocoConstants::INST_ID_RAD) {
                        $dataRadiologi[] = $value;
                    }
                    else {
                        $dataTindakan[str_replace(' ', '-', $value['ruangan_pelayanan'])][] = $value;
                    }
                }
                else {
                    $dataObat[] = $value;
                }
            }
        }
        
        $print = new DocoPrint('print-inf-pasien-sudah-bayar');
        $print->attributes = [
            '#table_rincian#' => $this->renderPartial('print_pdf',[
                'detailPasien'=>$detailPasien,
                'detailobat' => $dataObat,
                'detailtindakan' => $dataTindakan,
                'detaillab' => $dataLab,
                'detailradiologi' => $dataRadiologi,
                'totalobat' => $value,
                'totaltindakan' => $value,
                'totallab' => $value,
                'totalradiologi' => $value,
            ]),
        ];
        
        $print->Output();
    }

    /**
    * @controller actionPrintKwitansi
    * @attribute #no_kwitansi# => no kwitansi
    * @attribute #nama_pasien# => nama pasien
    * @attribute #tgl_pembayaran# => tanggal pembayaran
    * @attribute #jumlah_diterima# => total pembayaran
    * @attribute #kasir# => kasir
    * @attribute #diterima_dari# => diterima dari
    * @attribute #keterangan# => keterangan pembayaran
    * @attribute #jenis_kwitansi# => nama jenis_kwitansi
    * @attribute #terbilang# => terbilang
    **/
    public function actionPrintKwitansi()
    {
        return Yii::$app->docoPlugin->execute('cetak_kwitansi');
    }

    /**
    * @controller actionPrintBkm
    * @attribute #no_bkm# => no bkm
    * @attribute #nama_pasien# => nama pasien
    * @attribute #no_pendaftaran# => no pendaftaran
    * @attribute #tgl_pendaftaran# => tanggal pendaftaran
    * @attribute #tgl_pembayaran# => tanggal pembayaran
    * @attribute #jumlah_diterima# => total pembayaran
    * @attribute #kasir# => kasir
    * @attribute #instalasi_nama# => instalasi nama
    * @attribute #terbilang# => terbilang
    **/
    public function actionPrintBkm()
    {
        try{
            $model = new CetakKwitansiBkm;
            $request = Yii::$app->request;
            $id = $request->post('id',null);
            $pdf_id = $request->post('pdf_id',null);
            $tipe_pasien = trim($request->post('tipe_pasien',null));
            $pendaftaran_field = ($tipe_pasien == 'pasien_bebas') ? 'penjualanresep_id' : 'pendaftaran_id';
            $data = $model::find()->where([$pendaftaran_field => $pdf_id]);
            $data = $data->one();
            $terbilangAngka = 0;
            if(isset($data->total_terbayar)) {
                $terbilangAngka = $data->total_terbayar;
            }
            $terbilang = ($terbilangAngka != 0) ? DocoHelpers::Terbilang($terbilangAngka) : 'Nol ';
            $print = new DocoPrint();
            $print->attributes = [
              '#no_bkm#'=>isset($data->no_bkm) ? $data->no_bkm : '',
              '#nama_pasien#' => isset($data->nama_pasien) ? $data->nama_pasien : '',
              '#no_pendaftaran#'=>isset($data->no_pendaftaran) ? $data->no_pendaftaran : '',
              '#tgl_pendaftaran#'=> isset($data->tgl_pendaftaran) ? date('d-m-Y', strtotime($data->tgl_pendaftaran)) : '',
              '#tgl_pembayaran#'=>isset($data->tgl_pembayaran) ? date('d-m-Y', strtotime($data->tgl_pembayaran)) : '',
              '#jumlah_diterima#'=> isset($data->total_terbayar)? 'Rp. '.number_format($data->total_terbayar, 0, ',','.') :'',
              '#kasir#'=>isset($data->kasir) ? $data->kasir : '',
              '#instalasi_nama#'=>isset($data->instalasi_nama) ? $data->instalasi_nama : '',
              '#terbilang#' => $terbilang. 'Rupiah',
            ];
            $print->Output();
        }catch (\Exception $e){

        }
    }

    public function actionGetPembayaran()
    {
        $request = Yii::$app->request;
        $result = [];
        $pembayaranpelayanan_id = $request->get('pembayaranpelayanan_id', null);
        if($pembayaranpelayanan_id) {
            $pembayaranPelayanan = PembayaranPelayanan::findOne($pembayaranpelayanan_id);
            $pembayaran_id = ($pembayaranPelayanan) ? $pembayaranPelayanan->pembayaran_id : null;
            if(!$pembayaran_id){
                throw new \yii\base\ErrorException("Pembayaran ID Tidak Ditemukan", 500);
            }

            $pembayaran = Pembayaran::findOne($pembayaran_id);
            $result = [
                'total_dijamin' => $pembayaran->total_dijamin,
                'total_tunai' => $pembayaran->total_tunai,
                'total_nontunai' => $pembayaran->total_nontunai,
                'penggunaan_uangmuka' => $pembayaran->penggunaan_uangmuka,
            ];
            return $result;
        }
    }

    protected $_title = "Informasi Pasien Sudah Bayar";

    public function actionExportExcel()
    {
        $result = [];
        $data = $this->getData();
        $query = isset($data['query']) ? $data['query'] : [];
        $header = isset($data['header']) ? $data['header'] : [];
        foreach ($query->asArray()->all() as $key => $value) {
            $biayaAdministrasi = ArrayHelper::getValue($value, 'biaya_administrasi', 0);
            $totalTagihan = ArrayHelper::getValue($value, 'total_tagihan', 0);
            $totalTagihan = $totalTagihan + $biayaAdministrasi;
            $totalDijamin = ArrayHelper::getValue($value, 'total_dijamin', 0);
            $totalDiskon = ArrayHelper::getValue($value, 'total_discountpembayaran', 0);
            $totalDibayar = $totalTagihan - $totalDijamin - $totalDiskon;
            $noPembayaran = ArrayHelper::getValue($value, 'no_pembayaran');
            $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama');
            $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
            $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
            $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
            $tglPembayaran = ArrayHelper::getValue($value, 'tgl_pembayaran');
            $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            $tglPulang = ArrayHelper::getValue($value, 'tgl_pulang', $tglPendaftaran);
            $pegawaiKasir = ArrayHelper::getValue($value, 'pegawai_kasir', '-');

            $newValue = [];
            $tglRegis = date('d-M-Y', strtotime($tglPendaftaran));
            $tglPulang = !empty($tglPulang) ? date('d-M-Y', strtotime($tglPulang)) : $tglRegis;
            $newValue['Tanggal Pembayaran'] = date('d-M-Y H:i:s', strtotime($tglPembayaran));
            $newValue['Tanggal Masuk - Keluar'] = $tglRegis .' - '. $tglPulang;
            $newValue['No Pembayaran'] = $noPembayaran;
            $newValue['Instalasi - Ruangan Akhir'] = $instalasiNama.' - '.$ruanganNama;
            $newValue['No Pendaftaran'] = $noPendaftaran;
            $newValue["Nama Pasien"] = $namaPasien.' - '.$noRekamMedik;
            $newValue["Cara Bayar - Penjamin"] = $caraBayarNama.' - '.$penjaminNama;
            $newValue["Jumlah Tagihan"] = number_format($totalTagihan, 2);
            $newValue["Jumlah Dibayar Penjamin"] = number_format($totalDijamin, 2);
            $newValue["Diskon"] = number_format($totalDiskon, 2);
            $newValue["Jumlah Dibayar Pasien"] = number_format($totalDibayar, 2);
            $newValue["Pegawai Kasir"] = $pegawaiKasir;
            // $newValue[""] = null;
            $result[$key] = $newValue;
        }
        $footer = [];
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    private function getData()
    {
        $model = new InfoPasienSudahBayarView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $carabayar_nama =  $listPenjamin = $instalasi_nama = '';
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tgl_pulang', $outStart, $outEnd]);
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $inStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $inEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tgl_pendaftaran', $inStart, $inEnd]);
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if (isset($_GET['advanced-filter']['total_dijamin'])) {
                if (!empty($_GET['advanced-filter']['total_dijamin'])) {
                    $query->andWhere(['>', 'total_dijamin', 0]);
                }
                unset($_GET['advanced-filter']['total_dijamin']);
            }
            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $dataCaraBayar = CaraBayar::findOne($carabayar_id);
                $carabayar_nama = isset($dataCaraBayar['carabayar_nama']) ? $dataCaraBayar['carabayar_nama'] : '';
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }
            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $penjamin_id = explode(",",$penjamin_id);
                $dataPenjamin = Penjamin::find()->select(['penjamin_id', 'penjamin_nama'])->where(['penjamin_id' => $penjamin_id])->all();
                if(!empty($dataPenjamin)) {
                    foreach ($dataPenjamin as $key => $value) {
                        $listPenjamin[] = isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
                    }
                    $listPenjamin = implode(', ', $listPenjamin);
                }
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }
            if(isset($_GET['advanced-filter']['instalasi_nama'])) {
                $instalasi_id = $_GET['advanced-filter']['instalasi_nama'];
                $dataInstalasi = Instalasi::findOne($instalasi_id);
                $instalasi_nama = isset($dataInstalasi['instalasi_nama']) ? $dataInstalasi['instalasi_nama'] : '';
                $query->andWhere(['instalasi_id1' => $instalasi_id]);
                unset($_GET['advanced-filter']['instalasi_nama']);
            }
        }
        $query->andWhere(['IS NOT', 'pembayaran_id', null]);
        $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
       

        $header = array(
            "Tanggal Pembayaran" => ((date('d-M-Y', strtotime($start))." - ".date('d-M-Y', strtotime($end)))),
            "No Pembayaran" => (@$_GET['advanced-filter']['no_pembayaran']),
            "No Rekam Medik" => (@$_GET['advanced-filter']['no_rekam_medik']),
            "Nama Pasien" => (@$_GET['advanced-filter']['nama_pasien']),
            "No Pendaftaran" => (@$_GET['advanced-filter']['no_pendaftaran']),
            "Ruangan Akhir" => (@$_GET['advanced-filter']['ruangan_nama']),
            "Instalasi Akhir" => $instalasi_nama,
            "Cara Bayar" => $carabayar_nama,
            "Penjamin" => $listPenjamin,
        );

        /**
         * End Special Condition date range
        **/
        return [
            'query' => DocoRestActiveFilter::advancedFilter($model, $query),
            'header' => $header,
        ];
    }

    public function actionGetPenjamin($pembayaran_id)
    {
        $pembayaran = Pembayaran::findOne($pembayaran_id);
        $pendaftaran_id = ($pembayaran) ? $pembayaran['pendaftaran_id'] : null;
        $whereCond = "";
        if(empty($pendaftaran_id)) {
            return Yii::$app->db->createCommand("
                SELECT
                    pp.penjamin_id,
                    penjamin_m.penjamin_nama,
                    TRUE AS is_penjaminutama
                FROM 
                    pembayaranpelayanan_t pp
                    join penjualanresep_t pr on pr.penjualanresep_id = pp.penjualanresep_id
                    join penjamin_m on penjamin_m.penjamin_id = pr.penjamin_id 
                WHERE pp.pembayaran_id = {$pembayaran_id} ")->queryAll();
        }
        return Yii::$app->db->createCommand("
            SELECT * FROM (
            SELECT 
              pendaftaran_t.pendaftaran_id,
			  COALESCE(pasienadmisi_t.penjamin_id,pendaftaran_t.penjamin_id) as penjamin_id,
              payer.penjamin_nama,
              TRUE AS is_penjaminutama
            FROM pendaftaran_t
            LEFT JOIN pasienadmisi_t on pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            JOIN penjamin_m payer ON payer.penjamin_id = COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) 
            UNION ALL
            SELECT 
              pendaftaran_t.pendaftaran_id,
              pendaftaran_multipayer_t.penjamin_id,
              subpayer.penjamin_nama,
              FALSE AS is_penjaminutama
            FROM pendaftaran_t
            JOIN pendaftaran_multipayer_t ON pendaftaran_t.pendaftaran_id = pendaftaran_multipayer_t.pendaftaran_id
            JOIN penjamin_m subpayer ON pendaftaran_multipayer_t.penjamin_id = subpayer.penjamin_id
            ) AS payer 
            WHERE pendaftaran_id = {$pendaftaran_id} ")->queryAll();

    }

    public function actionFilters()
    {
        $type = Yii::$app->request->get('type', null);
        $payload = Yii::$app->request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? $payload['term'] : null;
        $carabayarId = isset($payload['carabayar_id']) ? $payload['carabayar_id'] : null;
        $result = [];
        if($type == 'carabayar') {
            $result = CaraBayar::find()
                ->select(['carabayar_id AS id', 'carabayar_nama AS text'])
                ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
            }

            $result->orderBy(['carabayar_nama' => SORT_ASC]);
        }
        elseif($type == 'penjamin') {
            $result = Penjamin::find()
                    ->select(['penjamin_id AS id', 'penjamin_nama AS text'])
                    ->where(['is_active' => true]);

            if(!empty($carabayarId)) {
                $result->andWhere(['carabayar_id' => $carabayarId]);
            }

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
            }

            $result->orderBy(['penjamin_nama' => SORT_ASC]);
        }
        else {
            $listInstalasi = [
                DocoConstants::INST_ID_RJ, 
                DocoConstants::INST_ID_RD,
                DocoConstants::INST_ID_RI,
                DocoConstants::INST_ID_RAD, 
                DocoConstants::INST_ID_LAB, 
                DocoConstants::INST_ID_BEDAH,
                DocoConstants::INST_ID_APT,
                DocoConstants::INST_ID_MCU,
                DocoConstants::INSTALASI_FARMASI,
            ];
            $result = Instalasi::find()
                ->select(['instalasi_id AS id', 'instalasi_nama AS text'])
                ->where(['is_active' => true]);

            if(!empty($listInstalasi)) {
                $result->andWhere(['IN', 'instalasi_id', $listInstalasi]);
            }

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(instalasi_nama)', strtolower($term)]);
            }

            $result->orderBy(['instalasi_nama' => SORT_ASC]);
        }
        
        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }

    private function getheader(){
        $column = [];

      $column = [
      [
          'title' => 'No',
          'data' => 'no',
          'searchable' => false,
          'visible' => true,
      ],
      [
          'title' => 'Tanggal Pembayaran',
          'data' => 'tgl_pembayaran',
          'searchable' => false,
          'visible' => true,
      ],
      [
         'title' => 'Tanggal Masuk',
         'data' => 'tgl_pendaftaran',
         'searchable' => false,
         'visible' => true,
     ],
           [
         'title' => 'Tanggal Stop Akomodasi',
         'data' => 'tgl_stopakomodasi',
         'searchable' => false,
         'visible' => true,
     ],
           [
         'title' => 'Tanggal Pulang',
         'data' => 'tgl_pulang',
         'searchable' => false,
         'visible' => true,
     ],
      [
          'title' => 'No Pembayaran',
          'data' => 'no_pembayaran_header',
          'searchable' => false,
          'visible' => true,
      ],
      [
          'title' => 'Instalasi - Ruangan Akhir',
          'data' => 'instalasi_nama',
          'searchable' => false,
          'visible' => true,
      ],
      [
          'title' => 'No Pendaftaran',
          'data' => 'no_pendaftaran',
          'searchable' => false,
          'visible' => true,
      ],
      [
          'title' => 'No SEP',
          'data' => 'nosep',
          'searchable' => false,
          'visible' => true,
      ],
      [
          'title' => 'Nama Pasien',
          'data' => 'nama_pasien',
          'searchable' => false,
          'visible' => true,
      ],
      [
        'title' => 'Nomor Rekam Medik',
        'data' => 'no_rekam_medik',
        'searchable' => false,
        'visible' => true,
    ],
      [
          'title' => 'Cara Bayar',
          'data' => 'carabayar_nama',
          'searchable' => false,
          'visible' => true,
      ],
      [
        'title' => 'Penjamin',
        'data' => 'penjamin_nama',
        'searchable' => false,
        'visible' => true,
    ],
      [
          'title' => 'Jumlah Tagihan',
          'data' => 'tagihan',
          'searchable' => false,
          'visible' => true,
      ],
      [
        'title' => 'Diskon',
        'data' => 'total_discountpembayaran',
        'searchable' => false,
        'visible' => true,
      ],
      [
          'title' => 'Jumlah Dibayar Penjamin',
          'data' => 'total_dijamin',
          'searchable' => false,
          'visible' => true,
      ],
      [
          'title' => 'Jumlah Dibayar Pasien',
          'data' => 'total_dibayar',
          'searchable' => false,
          'visible' => true,
      ],
      [
          'title' => 'Pegawai Kasir',
          'data' => 'pegawai_kasir',
          'searchable' => false,
          'visible' => true,
      ],
      ];

      return $column;
    }

    public function actionExportExcelBgProses() 
  {
      $request = Yii::$app->request;
      $get = $request->get();
      $advancedFilter = $get['advanced-filter'];
      $xOwner = $request->getHeaders()->get('X-Owner');
      $auth = $request->getHeaders()->get('Authorization');
      $randString = isset($get['randString']) ? $get['randString'] : null;
      $headerExcel = [];
      if (isset($get['page'])) unset($get['page']);
      if (isset($get['per-page'])) unset($get['per-page']);
      /** set header excel */
      
      $startPembayaran = date('d-M-Y');
      $endPembayaran = date('d-M-Y');
      $startPulang = $endPulang = $startMasuk = $endMasuk =  '';

      if (isset($advancedFilter)) {
        if(isset($advancedFilter['tgl_pembayaran']) && !empty($advancedFilter['tgl_pembayaran'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pembayaran']);
            if(count($explode) == 2) {
               $startPembayaran = date('d-M-Y', strtotime($explode[0]));
               $endPembayaran = date('d-M-Y', strtotime($explode[1]));
            }
        }
        if(isset($advancedFilter['tgl_pulang']) && !empty($advancedFilter['tgl_pulang'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pulang']);
            if(count($explode) == 2) {
                $startPulang = date('d-M-Y', strtotime($explode[0]));
                $endPulang = date('d-M-Y', strtotime($explode[1]));
            }
        }
        if(isset($advancedFilter['tgl_pendaftaran']) && !empty($advancedFilter['tgl_pendaftaran'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pendaftaran']);
            if(count($explode) == 2) {
                $startMasuk = date('d-M-Y', strtotime($explode[0]));
                $endMasuk = date('d-M-Y', strtotime($explode[1]));
            }
        }  
      }

      $noPembayaranFilter = isset($advancedFilter['no_pembayaran']) ? $advancedFilter['no_pembayaran']:'-';
      $instalasiNamaFilter = isset($advancedFilter['instalasi_nama_text']) && !empty($advancedFilter['instalasi_id']) ? $advancedFilter['instalasi_nama_text']:'-';
      $ruanganNamaFilter = isset($advancedFilter['ruangan_nama']) ? $advancedFilter['ruangan_nama']:'-';
      $noPendaftaranFilter = isset($advancedFilter['no_pendaftaran']) ? $advancedFilter['no_pendaftaran']:'-';
      $noRekamMedikFilter = isset($advancedFilter['no_rekam_medik']) ? $advancedFilter['no_rekam_medik']:'-';
      $namaPasienFilter = isset($advancedFilter['nama_pasien']) ? $advancedFilter['nama_pasien']:'-';
      $carabayarNamaFilter = isset($advancedFilter['carabayar_nama_text']) && !empty($advancedFilter['carabayar_id']) ? $advancedFilter['carabayar_nama_text']:'-';
      $penjaminNamaFilter = isset($advancedFilter['penjamin_text']) ? str_replace('__',' | ',$advancedFilter['penjamin_text']):'-';
      
      $headerExcel = [
          "Tanggal Pembayaran" => $startPembayaran. ' - '.$endPembayaran,
          "Tanggal Masuk" => $startMasuk. ' - '.$endMasuk,
          "Tanggal Pulang" => $startPulang. ' - '.$endPulang,
          "No Pembayaran" => $noPembayaranFilter,
          "Nama Ruangan" => $ruanganNamaFilter,
          "No Pendaftaran" => $noPendaftaranFilter,
          "No Rekam Medik" => $noRekamMedikFilter,
          "Nama Pasien" => $namaPasienFilter,
          "Cara Bayar" => $carabayarNamaFilter,
          "Nama Penjamin" => $penjaminNamaFilter,
          "Instalasi Akhir" => $instalasiNamaFilter,
      ];

      $header = $this->getheader();
      $dataSet = $this->getData();
      $query = isset($dataSet['query']) ? $dataSet['query'] : [];
      $data = $query->asArray()->all();
      $countData = count($data);
      $totalPerPage =count($data);
      $options = [
          "skipIncrement" => true,
          "customHeader" => [],
      ];
      $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');
      $params = [
          'sendToUrl' => 'inf-pasien-sudah-bayar/drop-file',
          'getDataUrl' => 'inf-pasien-sudah-bayar/get-data-laporan',
          'base_uri' => $uri_kasir,
      ];
      (new InternalService)->sendTo([
          'Sirs' => [
              'DataExportExcel' => [
                  'token' => $auth,
                  'xOwner' => $xOwner,
                  'unique_str' => $randString,
                  'filter' => $get,
                  'params' => $params
              ]
          ]
      ], true);

      (new InternalService)->sendTo([
          'Sirs' => [ 
              'ExportExcel' => [
                  'token' => $auth,
                  'xOwner' => $xOwner,
                  'unique_str' => $randString,
                  'totalPerPage' => $totalPerPage,
                  'countData' => $countData,
                  'filter' => $get,
                  'title' => 'Informasi Pasien Sudah Bayar',
                  'headerExcel' => $headerExcel,
                  'footer' => [],
                  'options' => $options,
                  'header' => $header,
              ]
          ]
      ], true);

      (new InternalService)->sendTo([
          'Sirs' => [ 
              'UploadExcel' => [
                  'token' => $auth,
                  'xOwner' => $xOwner,
                  'unique_str' => $randString,
                  'totalPerPage' => $totalPerPage,
                  'countData' => $countData,
                  'params' => $params
              ]
          ]
      ], true);

      return [
          'totalPerPage' => $totalPerPage,
          'unique_str' => $randString,
          'countData' => $countData,
      ];
  }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;

            $path = "uploads/";
            // if (!file_exists($path)) mkdir($path, 0755, true);
            // $path = $filePath;   
        $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionGetDataLaporan()
    {
        try {
            $dataSet = $this->getData();
            $query = isset($dataSet['query']) ? $dataSet['query'] : [];
            $data = $query->asArray()->all();
            $result = [];
            $counter = 1;
            foreach($data as $key => $value){
                $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama');
                $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
                $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
                $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
                $noPembayaran = ArrayHelper::getValue($value, 'no_pembayaran_header');
                $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
                $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
                $tglPembayaran = ArrayHelper::getValue($value, 'tgl_pembayaran');
                $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
                $tglPulang = !empty($value['tgl_pulang']) ? $value['tgl_pulang'] : $tglPendaftaran;
                $tglStopAkomodasi = ArrayHelper::getValue($value, 'tgl_stopakomodasi');
                $biayaAdministrasi = ArrayHelper::getValue($value, 'biaya_administrasi', 0);
                $totalDitagihkan = ArrayHelper::getValue($value, 'total_ditagihkan', 0);
                $totalTagihan = ArrayHelper::getValue($value, 'tagihan', 0);
                // $totalTagihan = $totalTagihan + $biayaAdministrasi;
                $totalDijamin = ArrayHelper::getValue($value, 'total_dijamin', 0);
                $totalDiskon = ArrayHelper::getValue($value, 'total_discountpembayaran', 0);
                $totalDibayar = ArrayHelper::getValue($value, 'total_dibayar', 0);
                $pegawai_kasir = ArrayHelper::getValue($value, 'pegawai_kasir', '-');

                $value['no'] = $counter;
                $value['instalasi_nama'] = $instalasiNama.' - '.$ruanganNama;
                $value['no_pembayaran_header'] = $noPembayaran;
                $value['nama_pasien'] = $namaPasien;
                $value['no_rekam_medik'] = $noRekamMedik;
                $value['carabayar_nama'] = $caraBayarNama;
                $value['penjamin_nama'] = $penjaminNama;
                $value['tgl_pembayaran'] = date('d-M-Y H:i:s', strtotime($tglPembayaran));
                $value['tgl_pendaftaran'] = date('d-M-Y H:i:s', strtotime($tglPendaftaran));
                $value['tgl_pulang'] = !empty($tglPulang) ? date('d-M-Y H:i:s', strtotime($tglPulang)) : '';
                $value['tgl_stopakomodasi'] = !empty($tglStopAkomodasi) ? date('d-M-Y H:i:s', strtotime($tglStopAkomodasi)) : '';
                $value['tgl_masuk_keluar'] = date('d-M-Y', strtotime($tglPendaftaran)).' - '.date('d-M-Y', strtotime($tglPulang));
                $value['total_bayar'] = $totalDitagihkan;
                $value['tagihan'] = number_format($totalTagihan, 2);
                $value['total_discountpembayaran'] = $totalDiskon;
                $value['total_dibayar'] = number_format($totalDibayar, 2);
                $value['total_dijamin'] = number_format($totalDijamin, 2);
                $value['pegawai_kasir'] = $pegawai_kasir;

                $result[$key] = $value;
                $counter++;
            }
            return $result;
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

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        // $dir = $rootPath.'/'.$no_request;
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

    private function getGabungTagihan($pendaftaran_id)
    {
        if(empty($pendaftaran_id)) {
            return [];
        }
        
        return Yii::$app->db->createCommand("
            SELECT * FROM gabungpelayanandetail_t WHERE ref_pendaftaran_id = {$pendaftaran_id} AND is_deleted = FALSE
        ")->queryOne();
    }

}
