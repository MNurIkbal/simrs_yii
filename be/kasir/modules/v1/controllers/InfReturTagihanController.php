<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\InfoReturTagihanPasienView;
use app\modules\v1\models\ReturBayarPelayanan;
use app\modules\v1\models\TandaBuktiBayar;
use app\modules\v1\models\TandaBuktiBayarView;
use app\modules\v1\models\ProfilRumahSakit;
use app\modules\v1\models\TandaBuktiKeluar;
use app\modules\v1\models\PegawaiView;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\Traits\Select2Trait;
use Doco\components\DocoMessages;

class InfReturTagihanController extends DocoActiveController
{
    use Select2Trait;

    public $modelClass = 'app\modules\v1\models\InfoReturTagihanPasienView';
    protected static $range = '';

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
        unset($actions['view']);
        return $actions;
    }

    private function getPegawai($pegawai_id)
    {
        $pegawai = PegawaiView::find()->where(['pegawai_id' => $pegawai_id])->asArray()->one();
        return $pegawai;
    }

    public function actionIndex()
    {
        $model = new InfoReturTagihanPasienView;
        $query = $model::find(true);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_returpelayanan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_returpelayanan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_returpelayanan']);

            }
        }

        self::$range = date("d-M-Y", strtotime($start))." - ".date("d-M-Y", strtotime($end));

        $query->andWhere(['between', 'tgl_returpelayanan', $start, $end]);
        $query->andWhere(["carabayar_id" => 5]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSearchKwitansi()
    {
        try {
            $request = Yii::$app->request;
            $term = $request->get("term");
            $kwitansi = [];

            $model = new TandaBuktiBayarView;
            $query = $model::find(true);

            if (!empty($term)) {
                $query->andWhere(['ILIKE', 'LOWER(no_pembayaran)', $term]);
                // kondisi sementara tidak ada filter dengan cara bayar
                // $query->andWhere(['carabayar_id' => 5]);
                $query->andWhere(['is_returbayarpelayanan' => false]);
                $query->andWhere(['>', 'jmlpembayaran', 0]);
                $kwitansi = $query->asArray()->limit(10)->all();
                return $kwitansi;
            }
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionReturTagihanPasien()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $model = new ReturBayarPelayanan;
        $model_tandabuktibayar = new TandaBuktiBayar;
        $bkk = new Tandabuktikeluar;

        try {
            $model->ruangan_id = Yii::$app->jwt->ruangan_id;
            $model->tandabuktibayar_id = $request->post("tandabuktibayar_id");
            $model->tgl_returpelayanan = date("d-M-Y H:i:s");
            $model->total_biayaretur = $request->post("tunai");
            $model->total_nontunai = $request->post("nontunai");
            $model->keterangan_retur = $request->post("keterangan");
            $total_retur = $request->post("tunai") + $request->post("nontunai");

            if (($total_retur) < 1) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Total Retur tidak boleh Kosong / Nol'
                ]);
            }

            if (($total_retur) > $request->post("jmlpembayaran")) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Total Retur tidak boleh lebih dari Jumlah Pembayaran!'
                ]);
            }
            if (!$model->save()) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $model->errors
                ]);
            }

            $tandabuktibayar = $model_tandabuktibayar->find()->where([
                "tandabuktibayar_id" => $model->tandabuktibayar_id
            ])->one();

            if (empty($tandabuktibayar)) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Tanda Bukti bayar tidak ditemukan'
                ]);
            }

            $tandabuktibayar->returbayarpelayanan_id = $model->returbayarpelayanan_id;
            if (!$tandabuktibayar->save()) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Tanda Bukti Bayar tidak dapat disimpan'
                ]);
            }

            // $is_tunai = empty($request->post("no_rek")) && empty($request->post("namapemilik_rek"));
            // asumsi $is_tunai untuk proses saat ini ambil dari field tunai
            $is_tunai = empty($request->post("tunai"));

            $bkk->returbayarpelayanan_id = $model->returbayarpelayanan_id;
            $bkk->ruangan_id = Yii::$app->jwt->ruangan_id;
            $bkk->tgl_buktikeluar = date("d-M-Y");
            $bkk->jml_pembayaran = $total_retur;
            // $bkk->jml_pembulatan = $request->post("pembulatan");
            // $bkk->biaya_administrasi = $request->post("biaya_administrasi");
            $bkk->is_tunai = $is_tunai;
            // $bkk->namapemilik_rek = $request->post("namapemilik_rek");
            // $bkk->no_rek = $request->post("no_rek");

            if (!$bkk->save()) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Tanda Bukti Bayar tidak dapat disimpan'
                ]);
            }

            $transaction->commit();

            $returbayarpelayanan = ReturBayarPelayanan::find()->where([
                "returbayarpelayanan_id" => $model->returbayarpelayanan_id
            ])->one();

            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA, [
                'text' => 'Retur Tagihan Pasien berhasil disimpan',
                'data' => [
                    "no_transaksi" => empty($returbayarpelayanan->no_returbayar) ? "empty" : $returbayarpelayanan->no_returbayar,
                    "satuid" => empty($model->returbayarpelayanan_id) ? null : DocoHelpers::encrypt($model->returbayarpelayanan_id)
                ]
            ]);
        } catch (Exception $e) {
            $transaction->rollback();
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $e->getMessage()
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
    * @controller actionPrintKwitansi
    * @attribute #no_kwitansi# => Menampilkan nomor kwitansi
    * @attribute #terima_dari# => Menampilkan data menerima dari
    * @attribute #tgl_pembayaran# => Menampilkan data tanggal pembayaran
    * @attribute #no_pendaftaran# => Menampilkan No. Pendaftaran
    * @attribute #no_pembayaran# => Menampilkan No. Pembayaran
    * @attribute #jumlah_diterima# => Menampilkan jumlah yang diterima pasien
    * @attribute #terbilang# => Menampilkan jumlah terbilang
    * @attribute #tgl_cetak_kwitansi# => Menampilkan tanggal cetak transaksi
    * @attribute #nama_pasien# => Menampilkan nama pasien
    * @attribute #nama_kasir# => Menampilkan nama kasir
    **/
    public function actionPrintKwitansi($id)
    {
        $print = new DocoPrint;

        $m_retur = new ReturBayarPelayanan;
        $m_buktibayar = new TandaBuktiBayarView;

        $retur = $m_retur->find()->where([
            "returbayarpelayanan_id" => $id
        ])->one();

        $buktibayar = $m_buktibayar->find()->where([
            "tandabuktibayar_id" => $retur->tandabuktibayar_id
        ])->one();

        $nama_rs = ProfilRumahSakit::find()->where(["profilrs_id" => 1])->select(["nama_rumahsakit"])->one();

        $tgl_cetak = date("d-M-Y");
        $total_dibayar = $retur->total_biayaretur + $retur->total_nontunai;
        $pegawai = self::getPegawai(Yii::$app->jwt->user->pegawai_id);
        $kasir = is_null($pegawai) ? "---" : $pegawai["nama_pegawai"];
        $terima_dari = is_null($nama_rs) ? "-" : $nama_rs->nama_rumahsakit;
        $terbilang = DocoHelpers::terbilang($total_dibayar)." Rupiah";

        $print_attributes = [
            "#no_kwitansi#" => $retur->no_returbayar,
            "#terima_dari#" => $terima_dari,
            "#tgl_pembayaran#" => empty($buktibayar->tglbuktibayar) ? "-" : date("d-M-Y", strtotime($buktibayar->tglbuktibayar)),
            "#no_pendaftaran#" => $buktibayar->no_pendaftaran,
            "#no_pembayaran#" => $buktibayar->no_pembayaran,
            "#jumlah_diterima#" => DocoHelpers::rupiahDisplay($total_dibayar),
            "#terbilang#" => $terbilang,
            "#tgl_cetak_kwitansi#" => $tgl_cetak,
            "#nama_pasien#" => $buktibayar->nama_pasien,
            "#nama_kasir#" => $kasir,
        ];

        $print->attributes = $print_attributes;

        return $print->Output();
    }


    /**
    * @controller actionPrintBkk
    * @attribute #no_bkk# => Menampilkan Nomor Bukti Kasir Keluar
    * @attribute #nama_pasien# => Menampilkan Nama Pasien
    * @attribute #tgl_pelayanan# => Menampilkan Tanggal Pelayanan
    * @attribute #no_pendaftaran# => Menampilkan No. Pendaftaran
    * @attribute #no_pembayaran# => Menampilkan No. Pembayaran
    * @attribute #terbilang# => Menampilkan Jumlah uang terbilang
    * @attribute #tgl_cetak_kwitansi# => Menampilkan Tanggal Cetak Kwitansi
    * @attribute #nama_kasir# => Menampilkan Tanggal Cetak Kwitansi
    *
    **/
    public function actionPrintBkk($id)
    {
        $print = new DocoPrint;

        $m_buktibayar = new TandaBuktiBayarView;
        $m_retur = new ReturBayarPelayanan;

        $retur = $m_retur->find()->where([
            "returbayarpelayanan_id" => $id
        ])->one();

        $buktibayar = $m_buktibayar->find()->where([
            "tandabuktibayar_id" => $retur->tandabuktibayar_id
        ])->one();

        $nama_rs = ProfilRumahSakit::find()->where(["profilrs_id" => 1])->select(["nama_rumahsakit"])->one();
        $bkk = TandaBuktiKeluar::find()->where(["returbayarpelayanan_id" => $id])->one();

        $tgl_cetak = date("d-M-Y");
        $total_dibayar = $retur->total_biayaretur + $retur->total_nontunai;
        $pegawai = self::getPegawai(Yii::$app->jwt->user->pegawai_id);
        $kasir = is_null($pegawai) ? "---" : $pegawai["nama_pegawai"];

        $terima_dari = is_null($nama_rs) ? "-" : $nama_rs->nama_rumahsakit;
        $terbilang = DocoHelpers::terbilang($total_dibayar)." Rupiah";

        $print_attributes = [
            "#no_bkk#" => $bkk->no_buktikeluar,
            "#nama_pasien#" => $buktibayar->nama_pasien,
            "#tgl_pelayanan#" => empty($buktibayar->tglbuktibayar) ? "-" : date("d-M-Y", strtotime($buktibayar->tglbuktibayar)),
            "#no_pendaftaran#" => $buktibayar->no_pendaftaran,
            "#no_pembayaran#" => $buktibayar->no_pembayaran,
            "#terbilang#" => $terbilang,
            "#jumlah_diterima#" => DocoHelpers::rupiahDisplay($total_dibayar),
            "#tgl_cetak_kwitansi#" => $tgl_cetak,
            "#nama_kasir#" => $kasir,
        ];

        $print->attributes = $print_attributes;

        return $print->Output();
    }

    public function actionHapus($id)
    {
        $connection = Yii::$app->db;
        try {
            $transaction = $connection->beginTransaction();

            $delete = ( new ReturBayarPelayanan)->delete([
                "returbayarpelayanan_id" => $id
            ]);

            $transaction->commit();

            if ($delete == 1) {
                return [
                    "title" => "Proses berhasil",
                    "text" => "Data retur tagihan berhasil dihapus"
                ];
            }else{
                return [
                    'status' => 422,
                    "title" => "Proses gagal",
                    "text" => "Data retur tagihan berhasil gagal dihapus"
                ];
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return ['text' => $e->getMessage()];
        }
    }
}