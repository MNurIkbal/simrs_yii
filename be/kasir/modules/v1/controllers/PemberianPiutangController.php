<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\Services\Cache;

use app\modules\v1\models\PemberianPiutang;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\TindakanPelayanan;
use Doco\actions\GetDataAction;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PemberianPiutangView;
use app\modules\v1\businessLogic\TagihanHelper;

use app\modules\v1\payload\PemberianPiutangPayload;

class PemberianPiutangController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\PemberianPiutang';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["save"] = ["POST"];
        return $verbs;
    }


    public function actions()
    {
        /**
         * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
         * 
         * DATA ATTRIBUTE YANG BISA DIGUNAKAN
         * 
         * ---------------------------------------------------------------------
         * selected : kolom yg akan ditampilkan
         * contoh penggunaan : 
         * selected => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * field_search : filter kolom berdasarkan pencarian / term equals 1 char
         * contoh penggunaan : 
         * field_search => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * is_where : filter kolom berdasarkan pencarian / term equals 1 word
         * contoh penggunaan : 
         * is_where => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * default_where : filter kolom berdasarkan 2 parameter (nama_kolom, value)
         * contoh penggunaan : 
         * default_where => ['nama_kolom', 'value'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * other_where : filter kolom berdasarkan 3 parameter (query filter, nama_kolom, value)
         * contoh penggunaan :
         * other_where => ['ILIKE/WHERE/LIKE/ETC', 'nama_kolom', 'value'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * orderby : sorting berdasarkan 2 parameter (nama kolom, ASC/DESC)
         * contoh penggunaan :
         * orderby => ['nama_kolom', ASC/DESC]
         * ---------------------------------------------------------------------
         * 
         */
        return [
            'get-data-karyawan' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Pegawai,
                'selected' => [
                    'pegawai_id AS id',
                    'nama_pegawai as text',
                    'pegawai_id',
                    'nama_pegawai',
                    'nomorindukpegawai'
                ],
                'field_search' => [
                    'nama_pegawai',
                    'nomorindukpegawai'
                ],
            ],
            'get-data-pendaftaran-reseptur' => [
                'class' => 'Doco\actions\GetRestDataAction',
                'modelClass' => 'app\modules\v1\models\PemberianPiutangFn',
                'selected' => [],
            ]
        ];
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        if ($post) {
            $konfig_system = Cache::getKonfigSystem();
            $payload = new PemberianPiutangPayload;
            $payload->attributes = $post;
            $payload->tgl_pemberianpiutang = date('Y-m-d', strtotime($payload->tgl_pemberianpiutang));
            if (!empty($payload->penjualanresep_id)) $payload->scenario = 'reseptur';
            if ($payload->validate()) {
                if (!empty($payload->penjualanresep_id)) {
                    $mResep = PenjualanResep::find()->select([
                        'pendaftaran_id',
                        'status_bayar',
                    ])->andWhere([
                        'penjualanresep_id' => $payload->penjualanresep_id
                    ])->asArray()->one();
                    /**
                     * Ini Kondisi ketika penjualan resep tidak di temukan
                     */
                    if (empty($mResep)) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => 'Penjualan Resep tidak ditemukan.'
                        ]);
                    }
                    /**
                     * Compare Penjualan dengan pendaftaran id jika ada
                     */
                    if (!empty($payload->pendaftaran_id) 
                        && (int) $payload->pendaftaran_id != $mResep['pendaftaran_id']) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => 'Penjualan Resep tidak sesuai dengan pendaftaran.'
                        ]);
                    }

                     $payload->pendaftaran_id = $mResep['pendaftaran_id'];
                    /**
                     * Reseptur yang bisa di transaksikan hanya yang bersatatis Belum lunas
                     */
                    if ($mResep['status_bayar'] === DocoConstants::LUNAS) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => 'Penjualan Resep tidak bisa ditransaksikan dengan status LUNAS.'
                        ]);
                    }
                    /** Get Total Tagihan Resep */
                    $dataPiutang = PemberianPiutangView::find()->select([
                        'total_tagihan',
                        'adm_persen',
                        'tarif_max',
                        'tagihan_ranap',
                        'total_tagihan',
                        'is_pembulatankeatas',
                        'satuanpembulatan',
                    ])->andWhere([
                        'jenis' => 'resep_bebas',
                        'pendaftaran_id' => $payload->penjualanresep_id,
                    ])->asArray()->one();
                    $cond_administrasi =  [
                        'penjualanresep_id' => $payload->penjualanresep_id
                    ];
                } else {
                    $dataPiutang = PemberianPiutangView::getPiutangByPendaftaranId($payload->pendaftaran_id);
                    $cond_administrasi = $payload->pendaftaran_id;
                }
                /** Validasi Tanggal */
                $dateNow = strtotime(date('Y-m-d'));
                $dateInput = strtotime($payload->tgl_pemberianpiutang);
                if ($dateNow < $dateInput) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Tanggal pemberian piutang tidak boleh besar dari tanggal sekarang.'
                    ]);
                }

                $totalTagihan = isset($dataPiutang['total_tagihan']) ? $dataPiutang['total_tagihan'] : 0;
                $totalPiutang = ArrayHelper::getValue($dataPiutang, 'total_piutang', 0);
                $totalTagihan = $totalTagihan - ArrayHelper::getValue($dataPiutang, 'total_jpk', 0);
                $isPembulatan = ArrayHelper::getValue($konfig_system, 'is_pembulatankeatas', 0);
                $pemSatuan = ArrayHelper::getValue($konfig_system, 'satuanpembulatan', 0);
                $biayaAdm =  TagihanHelper::getInstance($cond_administrasi, ArrayHelper::getValue($dataPiutang, 'penjamin_id'), ArrayHelper::getValue($dataPiutang, 'kelaspelayanan_id'),$totalTagihan, ArrayHelper::getValue($dataPiutang, 'pasienadmisi_id'));
                $totalTagihan = !empty($payload->penjualanresep_id) ? $totalTagihan : $totalTagihan + $biayaAdm->biayaAdm->totalBiayaAdm; // penjualan resep sudah include biaya admin
                $maksimalPiutang = $totalTagihan - $totalPiutang;
                $pembulatan = DocoHelpers::pembulatan($totalTagihan, $isPembulatan, $pemSatuan);
                $pembulatanPiutang = DocoHelpers::pembulatan($maksimalPiutang, $isPembulatan, $pemSatuan);
                if (isset($pembulatan['total'])) {
                    $totalTagihan = $pembulatan['total'];
                    $maksimalPiutang = $pembulatanPiutang['total'];
                }

                /** Validasi Ketika Pemberian Piurang sudah melebihi tagihan */
                if ($maksimalPiutang < $payload->total_piutang) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => "Pemberian piutang tidak bolah lebih besar dari tagihan. \r\n Total Tagihan ".DocoHelpers::rupiahDisplay($totalTagihan)." \r\n Maksimal Piutang ".DocoHelpers::rupiahDisplay($maksimalPiutang)
                    ]);
                }


                $validData = false;
                if (!empty($payload->pendaftaran_id)) {
                    $validData = PemberianPiutang::find()->select([
                        'pendaftaran_id'
                    ])->where([
                        'pendaftaran_id' => $payload->pendaftaran_id
                    ])->asArray()->one();
                } else if (!empty($payload->penjualanresep_id)) {
                    $validData = PemberianPiutang::find()->select([
                        'penjualanresep_id'
                    ])->where([
                        'penjualanresep_id' => $payload->penjualanresep_id
                    ])->asArray()->one();
                }

                if (!empty($validData)) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Transaksi ini sudah diberikan piutang.'
                    ]);
                }

                $model = new PemberianPiutang;
                $model->attributes = $payload->attributes;
                if (!empty($model->pendaftaran_id)) {
                    $model->penjualanresep_id = null;
                }
                $model->total_piutang = $payload->total_piutang;
                if ($model->save()) {
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
                }
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $model->errors
                ]);
            }
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }
    }

    public function actionGetTagihanRanap()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $model = TindakanPelayanan::find()
            ->select(['SUM(tarif_tindakan) as tagihan'])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(['IS NOT', 'pasienadmisi_id', null])
            ->scalar();

        return $model;
    }
}