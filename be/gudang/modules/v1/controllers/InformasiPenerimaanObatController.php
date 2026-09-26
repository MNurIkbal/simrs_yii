<?php

/**
 * @author: yaya
 * @since 22 March 2018
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use SirsCore\features\IntegrasiAkunting;

use app\modules\v1\models\InfoPenerimaanObatDetail;
use app\modules\v1\models\InfoPenerimaanObat;
use app\modules\v1\models\InfoReturPenerimaanObat;
use app\modules\v1\models\InfoReturPenerimaanObatDetail;
use app\modules\v1\models\ReturPenerimaanObat;
use app\modules\v1\models\ReturPenerimaanObatDetail;
use app\modules\v1\models\PenerimaanObatDoc;
use app\modules\v1\models\PenerimaanObat;
use app\modules\v1\models\PenerimaanObatDetail;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\KonfigFarmasi;

use app\modules\v1\businessLogic\StokObatAlkes as BLStokObatAlkes;
use app\modules\v1\businessLogic\UpdateHargaNetto;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\SatuanKonversi;

class InformasiPenerimaanObatController extends \Doco\components\DocoActiveController
{

    public $modelClass = '';
    protected static $range = '';

    const FEFO = 'FEFO';
    const FIFO = 'FIFO';

    private function getDataPenerimaanObat()
    {
        $model = new InfoPenerimaanObatDetail;
        $query = $model::find(true);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_penerimaan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_penerimaan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_penerimaan']);
            }
            if (isset($_GET['advanced-filter']['status_invoice'])) {
                $query->andWhere([
                    "status_invoice" => $_GET['advanced-filter']['status_invoice']
                ]);
                unset($_GET['advanced-filter']['status_invoice']);
            }
        }
        self::$range = date("d-M-Y", strtotime($start))." - ".date("d-M-Y", strtotime($end));
        $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    private function getDataDetailPenerimaanObat($id)
    {
        $model_penerimaan = new InfoPenerimaanObat;
        $query_penerimaan = $model_penerimaan->find()
        ->where([
            "penerimaanobat_id" => $id
        ])->one();

        $list_pegawai = [
            "menyetujui" => [$query_penerimaan->peg_menyetujui => $query_penerimaan->menyetujui],
            "mengetahui" => [$query_penerimaan->peg_mengetahui => $query_penerimaan->mengetahui],
        ];

        $detail = new InfoPenerimaanObatDetail;
        $detail_penerimaan = $detail->find()->where([
            "penerimaanobat_id" => $id
        ])->orderby([
            "obatalkes_id" => SORT_ASC,
            "tgl_kadaluarsa" => SORT_ASC
        ])
        ->all();

        $detail_rows = [];
        foreach ($detail_penerimaan as $row => $value) {
            $key = $value["obatalkes_id"]."-".$value["s_konversiobt_id"];
            $detail_rows[$key][] = [
                "obatalkes_id" =>$value["obatalkes_id"],
                "obatalkes_nama" =>$value["obatalkes_nama"],
                "qty_po" => $value["qty_po"],
                "po_balance" => $value["po_balance"],
                "qty_input" => $value["qty_diterima"],
                "qty_diterima" => $value["qty_diterima"],
                "satuan_besar" => $value["satuan_besar"],
                "satuan_kecil" => $value["satuan_kecil"],
                "tgl_kadaluarsa" => $value["tgl_kadaluarsa"],
                "no_batch" => $value["no_batch"],
                "keterangan" => $value["keterangan"],
                "s_konversiobt_id" => $value["s_konversiobt_id"],
                "harga" => $value["harga"],
                "discount" => $value["discount"],
                "discount_rp" => $value["discount_rp"],
                "jumlah" => $value["jumlah"],
                "validasipoobatdetail_id" => $value["validasipoobatdetail_id"],
                "kode_item" => $value["kode_item"],
                "satuanunit_nama" => $value["satuanunit_nama"],
                "ppn_nilai" => $value["ppn_nilai"],
                "ppn_persen" => $value["ppn_persen"],
            ];
        }

        $doc = new PenerimaanObatDoc;
        $query_doc = $doc->find()->where([
            "penerimaanobat_id" => $id,
            "is_deleted" => false
        ])->all();

        return [
            'header' => $query_penerimaan,
            'detail' => $detail_rows,
            'list_pegawai' => $list_pegawai,
            'document'=> $query_doc,
            "detail_penerimaan" => $detail_penerimaan
        ];
    }

    private function updateStokObatAlkes($data = []) {
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $no_batch = isset($data["no_batch"]) ? $data["no_batch"] : null;
        $connection = Yii::$app->db;

        $no_batch = implode(",", $no_batch);
        $data_retur = $data["data_retur"];

        $data_penerimaan_detail = $data["penerimaan_detail"];
        $penerimaan_detail_condition = "(" . implode(",", $data_penerimaan_detail) . ")";

        $model_stock = new StokObatAlkes;

        $data_obat = $data["data_obat"];
        $listObat = "(" . implode(",", $data_obat) . ")";
        $queryMasterObatAlkes = "SELECT obatalkes_id,obatalkes_nama,hargaratarata FROM obatalkes_m WHERE obatalkes_id IN {$listObat}";
        $dataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
        if(is_array($dataMasterObatAlkes) && count($dataMasterObatAlkes)>0) {
            $dataObat = array_column($dataMasterObatAlkes, 'obatalkes_nama', 'obatalkes_id');
            $dataMasterObatAlkes = array_column($dataMasterObatAlkes, 'hargaratarata','obatalkes_id');
        }

        if (!is_null($no_batch)) {
            $query = Yii::$app->db->createCommand("
                SELECT 
                    obatalkes_id,
                    id_stok,
                    SUM(dadang.qtystok_in - dadang.qtystok_out) as total_stok,
                    dadang.tglkadaluarsa,
                    nobatch,
                    harganetto,
                    persendiscount,
                    jmldiscount,
                    persenppn,
                    persenpph,
                    persenmargin,
                    jmlmargin,
                    jmlppn, 
                    dadang.satuankecil_id
                FROM (
                    SELECT (
                            CASE WHEN stokobatalkesasal_id IS NULL THEN stokobatalkes_id ELSE stokobatalkesasal_id END) as id_stok,
                            obatalkes_id,
                            qtystok_in,
                            qtystok_out,
                            nobatch,
                            harganetto,
                            persendiscount,
                            jmldiscount,
                            persenppn,
                            persenpph,
                            persenmargin,
                            jmlmargin,
                            jmlppn,
                            tglkadaluarsa ,
                            satuankecil_id
                    FROM stokobatalkes_t
                    WHERE nobatch IN ($no_batch) AND penerimaanobatdetail_id IN {$penerimaan_detail_condition}
                ) as dadang
                GROUP BY dadang.id_stok, dadang.tglkadaluarsa, obatalkes_id,nobatch, dadang.satuankecil_id,
                harganetto, persendiscount, jmldiscount, persenppn, persenpph, persenmargin, jmlmargin, jmlppn
                ORDER BY dadang.tglkadaluarsa ASC
            ")->queryAll();

            $insert = $total_stokout = [];
            $konfigFarmasi = KonfigFarmasi::find()->one();

            foreach ($query as $row => $value) {
                $retur = $data_retur[$value["nobatch"]];

                $kartu_stok = StokObatAlkes::find()->select([
                    'stokobatalkes_id', 'obatalkes_id', 'qtystok_out', 'stokobatalkesasal_id'
                ])->where([
                    'stokobatalkesasal_id' => $value['id_stok']
                ])->orderBy('stokobatalkes_id', SORT_ASC)->asArray()->all();
                $total_stokout = array_sum(array_column($kartu_stok, 'qtystok_out'));

                $total_stok = $value['total_stok'];

                if($konfigFarmasi->is_check_stokobatalkesasal) {
                    $total_stok -= $total_stokout;
                }

                $qty_retur = $retur["qty_retur"];
                $stok_after_retur = $total_stok - $qty_retur;

                if ($stok_after_retur < 0) {
                    return [
                        "status" => false,
                        "error" => "insufficient_stock",
                        "msg" => "Stok Obat ". $dataObat[$value['obatalkes_id']] . " tidak mencukupi.",
                    ];
                }

                $insert[] = [
                    "qtystok_out" => $qty_retur,
                    "qtystok_in" => 0,
                    "ruangan_id" => $ruangan_id,
                    "returpenerimaanobatdetail_id" => $retur["returpenerimaanobatdetail_id"],
                    "obatalkes_id" => $value["obatalkes_id"],
                    "tglkadaluarsa" => $value["tglkadaluarsa"],
                    "nobatch" => $value["nobatch"],
                    "tglstok_out" => date("d-M-Y H:i:s"),
                    "harganetto" => $value["harganetto"],
                    "persendiscount" => $value["persendiscount"],
                    "jmldiscount" => $value["jmldiscount"],
                    "persenppn" => $value["persenppn"],
                    "jmlppn" => (int)$value["jmlppn"],
                    "persenmargin" => $value["persenmargin"],
                    "jmlmargin" => $value["jmlmargin"],
                    "stokobatalkesasal_id" => $value["id_stok"],
                    "satuankecil_id" => $value["satuankecil_id"],
                    "persenpph" => $value["persenpph"],
                    "stokoa_aktif" => true,
                    "harga_netto_avg" => isset($dataMasterObatAlkes[$value['obatalkes_id']]) ? $dataMasterObatAlkes[$value['obatalkes_id']] : 0
                ];
            }

            $transaction = $connection->beginTransaction();

            try {
                if (!empty($insert)) {
                    $batch = StokObatAlkes::batchInsert($insert, false);

                    $response = [
                        "status" => true,
                        "msg" => "Berhasil"
                    ];

                    $transaction->commit();
                } else {
                    $response = [
                        "status" => false,
                        "error" => "stock_not_found",
                        "msg" => "Stok obat alkes tidak ditemukan"
                    ];
                }
            } catch (Exception $e) {
                $transaction->rollback();
                return [
                    "status" => false,
                    "msg" => $e->getMessage()
                ];
            }

            return $response;
        }
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-fillter"] = ["GET"];
        $verbs["get-data-detail"] = ["GET"];
        $verbs["get-detail"] = ["GET"];
        $verbs["delete"] = ["DELETE","POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $query = self::getDataPenerimaanObat();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionExportPdf
    * @attribute #nama_ruangan# => Menampilkan Nama Ruangan
    * @attribute #tabel_penerimaan# => Menampilkan Data Penerimaan
    * @attribute #tanggal# => Menampilkan tanggal
    * @attribute #nama_pegawai# => Menampilkan nama pegawai
    * @attribute #dicetak_oleh# => Menampilkan nama pegawai
    **/

    public function actionExportPdf()
    {
        $print = new DocoPrint;

        $query = self::getDataPenerimaanObat();

        $ruangan_id = Yii::$app->jwt->ruangan_id;

        $ruangan = Ruangan::find()->where([
            "ruangan_id" => $ruangan_id
        ])->one();

        $pegawai = PegawaiView::find()->where([
            "ruangan_id" => $ruangan_id,
            "jabatan_id" => DocoConstants::VAR_J_K_R
        ])->one();

        $print->attributes = [
            '#nama_ruangan#' => ucfirst($ruangan->ruangan_nama),
            '#tabel_penerimaan#' => $this->renderPartial("index", [
                "detail" => $query->all()
            ]),
            '#tanggal#' => date("d-M-Y"),
            '#nama_pegawai#' => !empty($pegawai) ? $pegawai->nama_pegawai : "",
            '#dicetak_oleh#' => "dicetak oleh",
        ];

        $print->Output();
    }

    public function actionExportExcel()
    {
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $filter_nopenerimaan = $filter_nopo = $filter_supplier = $filter_obat = $filter_status = '-';
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['tgl_penerimaan'])) {
                $exp = explode(' - ', $advancedFilters['tgl_penerimaan']);
                $tgl_awal_format = $exp[0];
                $tgl_akhir_format = $exp[1];
                $tgl_awal = date('Y-m-d H:i:s', strtotime($tgl_awal_format . ' 00:00:00'));
                $tgl_akhir = date('Y-m-d H:i:s', strtotime($tgl_akhir_format . ' 23:59:59'));
                
            }
            if(isset($advancedFilters['no_penerimaan'])) {
                $filter_nopenerimaan = $advancedFilters['no_penerimaan'];
            }
            if(isset($advancedFilters['nomor_po'])) {
                $filter_nopo = $advancedFilters['nomor_po'];
            }
            if(isset($advancedFilters['supplier_nama'])) {
                $filter_supplier = $advancedFilters['supplier_nama'];
            }
            if(isset($advancedFilters['obatalkes_nama'])) {
                $filter_obat = $advancedFilters['obatalkes_nama'];
            }
            if(isset($advancedFilters['status_invoice'])) {
                switch ($advancedFilters['status_invoice']) {
                    case 0:
                        $filter_status = "Belum Diverifikasi";
                        break;

                    case 1:
                        $filter_status = "Sudah Diverifikasi";
                        break;

                    case 2:
                        $filter_status = "Dibatalkan";
                        break;

                    default:
                        $filter_status = "-";
                        break;
                }
            }
        }

        $query = self::getDataPenerimaanObat();

        $ruangan_id = Yii::$app->jwt->ruangan_id;

        $ruangan = Ruangan::find()->where([
            "ruangan_id" => $ruangan_id
        ])->one();

        $rows = [];
        foreach ($query->all() as $key => $value) {
            $newRow = [];
            $newRow[\Yii::t('app', 'Tanggal Penerimaan')] = isset($value["tgl_penerimaan"]) ? date("d-M-Y", strtotime($value['tgl_penerimaan'])) : '';
            $newRow[\Yii::t('app', 'Nomer Penerimaan')] = isset($value["no_penerimaan"]) ? $value["no_penerimaan"] : '';
            $newRow[\Yii::t('app', 'Nomer PO')] = isset($value["nomor_po"]) ? $value["nomor_po"] : '';
            $newRow[\Yii::t('app', 'Supplier')] = isset($value["supplier_nama"]) ? $value["supplier_nama"] : '';
            $newRow[\Yii::t('app', 'Nama Obat')] = isset($value["obatalkes_nama"]) ? $value["obatalkes_nama"] : '';
            $newRow[\Yii::t('app', 'Qty Diterima')] = isset($value["qty_diterima"]) && isset($value["satuan_besar"])
                    ? $value["qty_diterima"]." ".$value["satuan_besar"] : '';
            $newRow[\Yii::t('app', 'Total Harga (Rp.)')] = isset($value["harga_total"]) ? $value["harga_total"] : '';
            switch ($value["status_invoice"]) {
                case '0':
                    $value["status_invoice"] = "Belum Diverifikasi";
                    break;

                case '1':
                    $value["status_invoice"] = "Sudah Diverifikasi";
                    break;

                case '2':
                    $value["status_invoice"] = "Dibatalkan";
                    break;

                default:
                    $value["status_invoice"] = "-";
                    break;
            }
            $newRow[\Yii::t('app', 'Status')] = isset($value["status_invoice"]) ? $value["status_invoice"] : '';
            $rows[$key] = $newRow;
        }
        $header = array(
            Yii::t("app", "Tanggal Cetak") => (date("d-M-Y")),
            Yii::t('app', "Tanggal Penerimaan") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir)))),
            Yii::t('app', "Nomor Penerimaan") => $filter_nopenerimaan,
            Yii::t('app', "Nomor PO") => $filter_nopo,
            Yii::t('app', "Supplier") => $filter_supplier,
            Yii::t('app', "Obat/Alkes") => $filter_obat,
            Yii::t('app', "Status") => $filter_status
        );

        $nama_ruangan = !empty($ruangan) ? ucfirst($ruangan->ruangan_nama) : "";


        $filePath = DocoHelpers::exportExcel("PENERIMAAN OBAT ALKES SUPPLIER ".$nama_ruangan, $rows, $header, [],[],[],true);
        $filePath->save('php://output');
        die;
    }

    public function actionGetDetail($id)
    {
        return self::getDataDetailPenerimaanObat($id);
    }

    /**
    * @controller actionDetailExportPdf
    * @attribute #no_penerimaan# => Menampilkan Nomor Penerimaan
    * @attribute #no_faktur# => Menampilkan Nomor Faktur
    * @attribute #no_po# => Menampilkan Nomor PO
    * @attribute #no_sj# => Menampilkan Nomor Surat Jalan
    * @attribute #tgl_sj# => Menampilkan Tanggal Surat Jalan
    * @attribute #tgl_penerimaan# => Menampilkan Tanggal Penerimaan
    * @attribute #nama_supplier# => Menampilkan Nama Supplier
    * @attribute #mengetahui# => Menampilkan Nama Pegawai Mengetahui
    * @attribute #menyetujui# => Menampilkan Nama Pegawai Menyetujui
    * @attribute #menerima# => Menampilkan Nama Pegawai Menerima
    * @attribute #tabel_detail_penerimaan# => Menampilkan Data Detail Penerimaan
    * @attribute #catatan# => Menampilkan Catatan
    **/

    public function actionDetailExportPdf($id)
    {

        $print = new DocoPrint;

        $data_detail = self::getDataDetailPenerimaanObat($id);



        $print_attributes = [
            '#no_penerimaan#' => $data_detail["header"]["no_penerimaan"] ,
            '#no_faktur#' => $data_detail["header"]["no_faktur"] ,
            '#no_po#' => $data_detail["header"]["no_poobat"] ,
            '#no_sj#' => $data_detail["header"]["no_suratjalan"] ,
            '#tgl_sj#' => date("d-M-Y", strtotime($data_detail["header"]["tgl_suratjalan"])) ,
            '#tgl_penerimaan#' => date("d-M-Y", strtotime($data_detail["header"]["tgl_penerimaan"])) ,
            '#nama_supplier#' => $data_detail["header"]["supplier_nama"] ,
            '#mengetahui#' => $data_detail["header"]["mengetahui"] ,
            '#menyetujui#' => $data_detail["header"]["menyetujui"] ,
            '#menerima#' => $data_detail["header"]["menerima"] ,
            '#tabel_detail_penerimaan#' => $this->renderPartial("table", [
                "detail" => $data_detail["detail"]
            ]),
            '#catatan#' => $data_detail["header"]["catatan"],
        ];


        $print->attributes = $print_attributes;

        $print->Output();
    }

    public function actionVerifikasiPenerimaan($id)
    {
        $model = new PenerimaanObat;
        $model_validasi = new ValidasiPoObat;
        $detailPenerimaan = new PenerimaanObatDetail;
        
        try {
            $penerimaanobat = $model->find()->where([
                "penerimaanobat_id" => $id
            ])->one();

            $validasi_poobat = $model_validasi->find()->where([
                "validasipoobat_id" => $penerimaanobat->validasipoobat_id
            ])->one();

            $pajak_persen = Pajak::find()->select(['pajak_persen'])->where(['pajak_id' => $validasi_poobat->pajak_id])->asArray()->one();
            $pajak_persen = current($pajak_persen);

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $penerimaanobat->is_verifikasi = DocoConstants::PENERIMAAN_VERIF;
            $penerimaanobat->save();

            $validasi_poobat->is_verifikasi = false;
            $validasi_poobat->save();

            $detail = $detailPenerimaan->find()
                ->select(['penerimaanobat_id', 'obatalkes_id', 'qty_diterima', 'harga', 's_konversiobt_id', 'discount'])
                ->where(['penerimaanobat_id' => $id])->asArray()->all();
                
            $konfig = KonfigFarmasi::find()->select(['hargaygdigunakan', 'use_ppn', 'use_discount'])->asArray()->one();
            $satuanKonversi = SatuanKonversi::find()->select(['satuankonversi_id', 'nilai_konversi'])->asArray()->all();
            $satuanKonversi = ArrayHelper::index($satuanKonversi, 'satuankonversi_id');
            foreach($detail as $key => $item) {
                $nilaiKonversi = isset($satuanKonversi[$item['s_konversiobt_id']]['nilai_konversi']) ? $satuanKonversi[$item['s_konversiobt_id']]['nilai_konversi'] : null;
                if(is_null($nilaiKonversi)) {
                    throw new \Exception("Nilai konversi tidak ditemukan.", 1);
                }
                
                $harga_netto_terkecil = $item['harga'] / $nilaiKonversi;
                $discount = 0;
                if($konfig['use_discount']) {
                    $discount = $item['discount'] / 100;
                }
                
                $ppn = 0;
                if($konfig['use_ppn']) {
                    $ppn = $pajak_persen / 100;
                }

                $hn_diskon = $harga_netto_terkecil * ($discount);
                $detail[$key]['harga'] = $harga_netto_terkecil;
                $detail[$key]['jmldiscount'] = $hn_diskon;
                $detail[$key]['jmlppn'] = ($harga_netto_terkecil - $hn_diskon) * ($ppn);
            }
            
            if($konfig['hargaygdigunakan'] == DocoConstants::HARGA_FIX_RATE) {
                Yii::$app->runAction(
                    'v1/allow/update-base-price',
                    [
                        'transaksi_id' => $id,
                        'tipe' => 'PO',
                        'detail' => json_encode($detail)
                    ]
                );
            }

            IntegrasiAkunting::integratePenerimaanSupplier($penerimaanobat->no_penerimaan, DocoConstants::JENIS_OBAT);
            $transaction->commit();

            return [
                "status" => 200,
                "title" => "Proses Berhasil",
                "text" => "Proses Verifikasi berhasil",
                "id_transaksi" => DocoHelpers::encrypt($id)
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            $this->responseJson(422, $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            $this->responseJson(500, "Terjadi kesalahan pada sistem.");
        }
    }

    public function actionEditPenerimaan($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $penerimaanobat = PenerimaanObat::find()->where([
                "penerimaanobat_id" => $id
            ])->one();

            if($penerimaanobat->is_verifikasi == 1) {
                $penerimaanobat->no_faktur = $request->post("no_faktur");
                if ($penerimaanobat->save()) {
                    \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                    $transaction->commit();
                    return [
                        "status" => 200,
                        "text" => "Perubahan Penerimaan Obat berhasil",
                        "title" => "Proses Berhasil",
                        "no_penerimaan" => $penerimaanobat->no_penerimaan
                    ];
                }else{
                    return [
                        "status" => 422,
                        "data" => $penerimaanobat->errors
                    ];
                }
            }

            $dataFaktur = PenerimaanObat::find()->where([
                'no_faktur' => $request->post("no_faktur")
            ])->one();

            $dataSJalan = PenerimaanObat::find()->where([
                'no_suratjalan' => $request->post("no_suratjalan")
            ])->one();

            if(!empty($dataFaktur) && $penerimaanobat->no_faktur != $request->post("no_faktur")) {
                $noFaktur = $request->post('no_faktur');
                return [
                    'status' => 422,
                    'text' => "Sudah terdapat transaksi dengan nomor faktur {$noFaktur}",
                    'title' => 'Proses Gagal!'
                ];
            }

            if(!empty($dataSJalan) && $penerimaanobat->no_suratjalan != $request->post("no_suratjalan")) {
                $noSurat = $request->post('no_suratjalan');
                return [
                    'status' => 422,
                    'text' => "Sudah terdapat transaksi dengan nomor surat jalan {$noSurat}",
                    'title' => 'Proses Gagal!'
                ];
            }

            $penerimaanobat->no_suratjalan = $request->post("no_suratjalan");
            $penerimaanobat->tgl_suratjalan = $request->post("tgl_suratjalan");
            $penerimaanobat->no_faktur = $request->post("no_faktur");
            $penerimaanobat->no_faktur_sementara = $request->post("no_faktur_sementara");
            $penerimaanobat->peg_mengetahui = $request->post("peg_mengetahui");
            $penerimaanobat->peg_menyetujui = $request->post("peg_menyetujui");

            if(empty($penerimaanobat->no_faktur_sementara) && empty($penerimaanobat->no_faktur)) {
                return [
                    'status' => 422,
                    'text' => "No Faktur / No Faktur Sementara harus di isi",
                    'title' => 'Proses Gagal!'
                ];
            }

            if ($penerimaanobat->save()) {
                # code...
            }else{
                return [
                    "status" => 422,
                    "data" => $penerimaanobat->errors
                ];
            }

            $detail = PenerimaanObatDetail::find()->where([
                "penerimaanobat_id" => $id
            ])->all();


            $delete_detail = ( new PenerimaanObatDetail)->delete([
                "penerimaanobat_id" => $id
            ]);

            $list_detail = $request->post("list_data");
            $data_detail = json_decode($list_detail, true);
            $tmp_detail = [];
            foreach ($data_detail as $obat) {
                foreach ($obat as $row_detail) {
                    $qtyTerima = $row_detail["qty_diterima"];
                    $jumlah = $qtyTerima * $row_detail['harga'];
                    $tmp_detail[] = [
                        "penerimaanobat_id" => $id,
                        "validasipoobatdetail_id" => $row_detail["validasipoobatdetail_id"],
                        "obatalkes_id" => $row_detail["obatalkes_id"],
                        "qty_po" => $row_detail["qty_po"],
                        "po_balance" => $row_detail["po_balance"],
                        "qty_diterima" => $qtyTerima,
                        "tgl_kadaluarsa" => $row_detail["tgl_kadaluarsa"],
                        "no_batch" => $row_detail["no_batch"],
                        "keterangan" => $row_detail["keterangan"],
                        "s_konversiobt_id" => $row_detail["s_konversiobt_id"],
                        "harga" => $row_detail["harga"],
                        "discount" => $row_detail["discount"],
                        "discount_rp" => $jumlah * ($row_detail['discount'] / 100),
                        "jumlah" => $jumlah,
                    ];
                }
            }

            $batch_detail = PenerimaanObatDetail::batchInsert($tmp_detail, false);

            $listData = $request->post('list_data',"{}");
            $listUpload = $request->post('file_upload',"{}");
            $dataDetail = json_decode($listData,true);
            $dataUpload = json_decode($listUpload,true);
            $tmpData = $tmpFile = [];

            foreach ($dataUpload as $key => $value)
            {
                $tmpFile[] = [
                    'penerimaanobat_id' => $id,
                    'upload_berkas' => $value['upload_berkas'],
                    'catatan_berkas' => $value['catatan_berkas']
                ];
            }

            if (!empty($tmpFile)) {
                $cek_penerimaan = PenerimaanObatDoc::batchInsert($tmpFile);
            }else{
                $cek_penerimaan = false;
            }

            $transaction->commit();

            return [
                "status" => 200,
                "text" => "Perubahan Penerimaan Obat berhasil",
                "title" => "Proses Berhasil",
                "no_penerimaan" => $penerimaanobat->no_penerimaan
            ];
        } catch (Exception $e) {
            $transaction->rollback();
            return [
                "status" => 422,
                "text" => $e->getMessage(),
                "title" => "Proses gagal"
            ];
        }
    }

    public function actionBatalPenerimaan($id)
    {
        $model = new PenerimaanObat;
        $model_detail = new PenerimaanObatDetail;
        $model_validasi = new ValidasiPoObat;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $penerimaanobat = $model->find()->where([
                "penerimaanobat_id" => $id
            ])->one();

            $penerimaanobat->is_verifikasi = DocoConstants::PENERIMAAN_BATAL;
            $penerimaanobat->save();

            $penerimaanobatdetail = $model_detail->find()->where(['penerimaanobat_id'=>$id])->all();
            if(is_array($penerimaanobatdetail) && count($penerimaanobatdetail)>0){
                foreach ($penerimaanobatdetail as $detail) {
                    $detail->is_batal = true;
                    $detail->save();
                }
            }

            $validasi_poobat = $model_validasi->find()->where([
                "validasipoobat_id" => $penerimaanobat->validasipoobat_id
            ])->one();

            $validasi_poobat->is_verifikasi = false;

            $penerimaanaktif = $model->find()->where(['validasipoobat_id'=>$penerimaanobat->validasipoobat_id])->andWhere(['is_verifikasi'=>1])->exists();
            if($penerimaanaktif == true){
                $validasi_poobat->status_penerimaan = DocoConstants::BELUM_SELESAI_PO;
            }else{
                $validasi_poobat->status_penerimaan = DocoConstants::PO_BELUM_DITERIMA;
            }
            $validasi_poobat->save();

            $transaction->commit();
            return [
                "status" => 200,
                "title" => "Proses Berhasil",
                "text" => "Proses Pembatalan berhasil"
            ];
        } catch (Exception $e) {
            $transaction->rollback();
            return [
                "status" => 401,
                "title" => "Proses Gagal",
                "text" => "Proses Pembatalan gagal"
            ];
        }
    }

    public function actionDeleteDoc($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;

        try {
            $transaction = $connection->beginTransaction();
            $delete_doc = ( new PenerimaanObatDoc)->delete([
                "penerimaanobatdoc_id" => $id
            ]);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            return [
                "error" => $e->getMessage()
            ];
        }

        return [
            "status" => 200,
            "title" => "Berhasil",
            "text" => "Fiel berhasil di delete",
            "data" => [
                $delete_doc,"id"=>$id
            ]
        ];
    }

    public function actionRetur()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $model_head = new ReturPenerimaanObat;

        try {
            $tgl_retur = $request->post("tgl_retur");
            $model_head->tgl_retur = $tgl_retur;
            $model_head->pegawairetur_id = $request->post("pegawairetur_id");
            $model_head->alasan_retur = $request->post("alasan_retur");
            $model_head->save();

            $detail_retur = json_decode($request->post("detail_retur"), 1);

            $penerimaan_details = $obatId = [];
            $tmp_detail = [];
            foreach ($detail_retur as $det) {
                $tmp_detail[$det["no_batch"]] = [
                    "returpenerimaanobat_id" => $model_head->returpenerimaanobat_id ,
                    "obatalkes_id" => $det["obatalkes_id"] ,
                    "satuanbesar_id" => $det["satuanbesar_id"],
                    "tgl_kadaluarsa" => $det["tgl_kadaluarsa"],
                    "qty_retur" => $det["qty_retur"],
                    "qty_input" => $det["qty_input"],
                    "penerimaanobat_id" => $det["penerimaanobat_id"],
                    "penerimaanobatdetail_id" => $det["penerimaanobatdetail_id"],
                ];

                $batch[] = "'".$det["no_batch"]."'";
                $penerimaan_details[] = $det['penerimaanobatdetail_id'];
                $obatId[] = $det['obatalkes_id'];
            }

            $batch_detail = ReturPenerimaanObatDetail::batchInsert($tmp_detail, false);

            $inforetur = new InfoReturPenerimaanObat;
            $inforetur = $inforetur->find()->where([
                "returpenerimaanobat_id" => $model_head->returpenerimaanobat_id
            ])->one();

            $inforeturdetail =  new InfoReturPenerimaanObatDetail;
            $inforeturdetail = $inforeturdetail->find()->where([
                "returpenerimaanobat_id" => $model_head->returpenerimaanobat_id
            ])->all();

            $validationDetails = [];
            foreach ($inforeturdetail as $row) {
                $data_retur[$row["no_batch"]] = [
                    "returpenerimaanobatdetail_id" => $row["returpenerimaanobatdetail_id"],
                    "obatalkes_id" => $row["obatalkes_id"],
                    "tglkadaluarsa" => $row["tgl_kadaluarsa"],
                    "nobatch" => $row["no_batch"],
                    "qty_retur" => $row["qty_retur"],
                ];
                $penerimaanObatDetailId = $row['penerimaanobatdetail_id'];
                $infoPenerimaanObatDetail = new InfoPenerimaanObatDetail;
                $infoPenerimaanObatDetail = $infoPenerimaanObatDetail->find()->where([
                    "penerimaanobatdetail_id" => $penerimaanObatDetailId
                ])->one();
                if (empty($infoPenerimaanObatDetail)){
                    return [
                        "status" => 422,
                        "title" => "Error!",
                        "text" => "Data Penerimaan Obat tidak dapat ditemukan"
                    ];
                }
                $validasiPoObatDetailId = $infoPenerimaanObatDetail->validasipoobatdetail_id;
                $validasiPoObatDetail = ValidasiPoObatDetail::find()->where([
                    'validasipoobatdetail_id' => $validasiPoObatDetailId
                ])->one();
                if (empty($validasiPoObatDetail)){
                    return [
                        "status" => 422,
                        "title" => "Error!",
                        "text" => "Data PO Obat tidak dapat ditemukan"
                    ];
                }
                // Harusnya selaras sama InformasiPenerimaanPoBarangController (actionRetur)
                // Code dibawah dicomment
                // Karena dari inforeturpenerimaanobatdetail_v qty_retur dari view tersebut sudah sesuai
                // $validasiPoObatDetail->qty_retur = $validasiPoObatDetail->qty_retur + $row["qty_input"];
                // $validasiPoObatDetail->save(false);
                $validationDetails[] = $validasiPoObatDetail->attributes;
            }
            // Execute Set Status Validation
            $validasiPoObatId = ArrayHelper::getValue($validationDetails, '0.validasipoobat_id');
            ValidasiPoObat::checkAndSetStatus($validasiPoObatId);
            $data["no_batch"] = $batch;
            $data["data_retur"] = $data_retur;
            $data["penerimaan_detail"] = $penerimaan_details;
            $data["data_obat"] = $obatId;
            $update_stok_obat_alkes = $this->updateStokObatAlkes($data);
            if ($update_stok_obat_alkes["status"] == false) {
                if(isset($update_stok_obat_alkes["error"]) && $update_stok_obat_alkes["error"] == "insufficient_stock") {
                    return [
                        'title' => 'Proses Gagal!',
                        'text' => $update_stok_obat_alkes["msg"],
                        'status' => 422
                    ];
                } else {
                    return [
                        "status" => 402,
                        "title" => "Proses gagal",
                        "text" => $update_stok_obat_alkes["msg"],
                    ];
                }
            }
            $transaction->commit();
            IntegrasiAkunting::integrateReturSupplier($inforetur->no_returpenerimaanobat , DocoConstants::JENIS_OBAT);

            return [
                "status" => 200,
                "title" => "Proses Sukses",
                "text" => "Retur Penerimaan berhasil disimpan",
                "no_retur" => empty($inforetur) ? "" : $inforetur->no_returpenerimaanobat,
            ];

        } catch (Exception $e) {
            $transaction->rollback();
            return [
                "status" => 402,
                "title" => "Error",
                "text" => "Proses gagal",
                "data" => [
                    "error" => $e->getMessage()
                ]
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetObatSupplier($supplier_id = 0, $pajak_id = 0)
    {
        $model = new InfoPenerimaanObatDetail;
        $query = $model->find()->where([
            "supplier_id" => $supplier_id,
            "pajak_id" => $pajak_id
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionReturPdf
    * @attribute #no_retur# => Menampilkan Nomor Retur
    * @attribute #tgl_retur# => Menampilkan Tanggal Retur
    * @attribute #nama_supplier# => Menampilkan Nama Supplier
    * @attribute #petugas_retur# => Menampilkan Nama Petugas Retur
    * @attribute #alasan_retur# => Menampilkan Alasan Retur
    * @attribute #detail_retur# => Data Detail Retur

    **/

    public function actionReturPdf($no_retur="")
    {
        $print = new DocoPrint;

        $model = new InfoReturPenerimaanObat;
        $model_detail = new InfoReturPenerimaanObatDetail;

        $head = $model->find()->where([
            "no_returpenerimaanobat" => $no_retur
        ])->one();

        if (!empty($head))
        {
            $detail = $model_detail->find()->where([
                "returpenerimaanobat_id" => $head["returpenerimaanobat_id"]
            ])->all();

            if (!empty($detail))
            {
                $print_attributes = [
                    "#no_retur#" => $head["no_returpenerimaanobat"] ,
                    "#tgl_retur#" => date("d-M-Y", strtotime($head["tgl_retur"])),
                    "#nama_supplier#" => $detail[0]["supplier_nama"],
                    "#petugas_retur#" => $head["pegawai_retur"],
                    "#alasan_retur#" => $head["alasan_retur"],
                    "#detail_retur#" => $this->renderPartial("table-retur", ["detail"=> $detail]),
                ];

                $print->attributes = $print_attributes;

                $print->Output();
            }else
            {
                return [
                    "status" => 402,
                    "text" => "No. Retur tidak ditemukan"
                ];
            }
        }else{
            return [
                "status" => 402,
                "text" => "No. Retur tidak ditemukan"
            ];
        }
    }

    public function actionGetAlertHargaObat($id)
    {
        $connection = Yii::$app->db;

        $list_obat = $connection->createCommand("
        SELECT m.obatalkes_nama, m.obatalkes_id, m.harga_sugesstion, m.harganetto_ygdipakai, val.harga as harga_transaksi, m.satuankecil_nama, sv.satuan_besar
        FROM obatalkes_v m
        JOIN obatalkes_v s ON m.obatalkes_id = s.obatalkes_id
        JOIN validasipoobatdetail_t val ON val.obatalkes_id = m.obatalkes_id
        JOIN penerimaanobatdetail_t terima ON terima.validasipoobatdetail_id = val.validasipoobatdetail_id
            AND terima.additional_data = 'is_verifikasi'
        JOIN satuankonversi_v sv on sv.satuankonversi_id = val.s_konversiobt_id
        WHERE m.harganetto_ygdipakai <> s.harga_sugesstion
        and terima.penerimaanobat_id = {$id}
        GROUP BY m.obatalkes_nama, m.obatalkes_id, m.harga_sugesstion, m.harganetto_ygdipakai, val.harga, m.satuankecil_nama, sv.satuan_besar
        ")->queryAll();

        return $list_obat;
    }

    public function actionSaveUpdateHarga()
    {
        $request = Yii::$app->request;
        $data["list_obat"] = json_decode($request->post()["toPost"], true);
        $transaksi = "penerimaaan_po";

        $simpan = UpdateHargaNetto::UpdateHargaObat($data, $transaksi);

        if ($simpan) {
            return [
                "status" => 200,
                "title" => "Proses Berhasil",
                "text" => "Proses penyimpanan harga berhasil"
            ];
        }else{
            return [
                "status" => 422,
                "title" => "Proses Gagal",
                "text" => "Proses penyimpanan harga gagal",
                "error" => $e->getMessage()
            ];
        }
    }
}
