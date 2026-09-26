<?php

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

use app\modules\v1\models\InfoPenerimaanBarang;
use app\modules\v1\models\InfoPenerimaanBarangDetail;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\StokBarang;
use app\modules\v1\models\ReturPenerimaanBarang;
use app\modules\v1\models\ReturPenerimaanBarangDetail;
use app\modules\v1\models\InfoReturPenerimaanBarang;
use app\modules\v1\models\InfoReturPenerimaanBarangDetail;

use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\PenerimaanBarang;
use app\modules\v1\models\PenerimaanBarangDetail;
use app\modules\v1\models\PenerimaanBarangDoc;

class InformasiPenerimaanPoBarangController extends \Doco\components\DocoActiveController
{

    public $modelClass = '';
    protected $range;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function updateStokBarang($data = [])
    {
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $no_batch = isset($data["no_batch"]) ? $data["no_batch"] : null;
        $connection = Yii::$app->db;

        $no_batch = implode(",", $no_batch);
        $data_retur = $data["data_retur"];

        $model_stock = new StokBarang;

        if (!is_null($no_batch))
        {
            $query = Yii::$app->db->createCommand("
                SELECT barang_id,
                SUM(dudung.qtystok_in - dudung.qtystok_out) as total_stok,
                max(id_stok) as id_stok,
                sum(dudung.jmlpenerimaan) as jmlpenerimaan,
                dudung.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin, dudung.satuankecil_id
                FROM (
                  SELECT (CASE WHEN stokbarangasal_id IS NULL THEN stokbarang_id ELSE stokbarangasal_id END) as id_stok,
                              barang_id,
                              CASE WHEN penerimaandetail_id IS NOT NULL THEN qtystok_in else 0 END AS jmlpenerimaan,
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
                              tglkadaluarsa ,
                              satuankecil_id
                    FROM stokbarang_t
                              WHERE nobatch IN ({$no_batch}) AND (penerimaandetail_id IS NOT NULL OR returbarangdetail_id IS NOT NULL)
                ) as dudung
                GROUP BY dudung.tglkadaluarsa,barang_id,nobatch, dudung.satuankecil_id,
                harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
                ORDER BY dudung.tglkadaluarsa ASC
            ")->queryAll();

            $insert = [];
            $id_minus = [];
            foreach ($query as $row => $value)
            {
                $retur = $data_retur[$value["nobatch"]];

                $total_stok = $value["total_stok"];
                $qty_retur = $retur["qty_retur"];
                $stok_after_retur = $total_stok - $qty_retur;

                if ($stok_after_retur <= 0)
                {
                    $id_minus[] = "'".$value["id_stok"]."'";
                }

                $insert[] = [
                    "qtystok_out" => $qty_retur,
                    "qtystok_in" => 0,
                    "ruangan_id" => $ruangan_id,
                    "returbarangdetail_id" => $retur["returpenerimaanbarangdetail_id"],
                    "barang_id" => $value["barang_id"],
                    "tglkadaluarsa" => $value["tglkadaluarsa"],
                    "nobatch" => $value["nobatch"],
                    "tglstok_out" => date("Y-m-d H:i:s"),
                    "harganetto" => $value["harganetto"],
                    "persendiscount" => $value["persendiscount"],
                    "jmldiscount" => $value["jmldiscount"],
                    "persenppn" => $value["persenppn"],
                    "persenmargin" => $value["persenmargin"],
                    "jmlmargin" => $value["jmlmargin"],
                    "stokobatalkesasal_id" => $value["id_stok"],
                    "satuankecil_id" => $value["satuankecil_id"],
                    "persenpph" => $value["persenpph"],
                    "stokoa_aktif" => true,
                ];
            }

            $transaction = $connection->beginTransaction();
            try {
                if (!empty($insert))
                {
                    $batch = StokBarang::batchInsert($insert, false);

                    if(!empty($id_minus))
                    {
                        $str_id_minus = implode(",", $id_minus);
                        $update_false = Yii::$app->db->createCommand("
                            UPDATE stokbarang_t SET stokbarang_aktif = false
                            WHERE stokbarangasal_id IN ($str_id_minus) OR stokbarang_id IN ($str_id_minus)
                        ")->queryAll();
                    }

                    $transaction->commit();
                }else
                {
                    return [
                        "status" => false,
                        "msg" => "Stok barang tidak ditemukan"
                    ];
                }
            } catch (Exception $e) {
                $transaction->rollback();
                return [
                    "status" => false,
                    "msg" => $e->getMessage()
                ];
            }
        }

        return [
            "status" => true,
            "msg" => "Berhasil",
            "data" => [
                "insert" => $insert
            ]
        ];
    }

    public function actionIndex()
    {
        $query = $this->getData();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDetail($id)
    {
        $query_penerimaan = InfoPenerimaanBarang::find()->where([
            "penerimaanbarang_id" => $id
        ])->one();

        $detail_penerimaan = InfoPenerimaanBarangDetail::find()->where([
            "penerimaanbarang_id" => $id
        ])->orderby([
            "penerimaanbarangdetail_id" => SORT_ASC
        ])->all();

        $detail_rows = [];
        foreach ($detail_penerimaan as $row => $value) {
            $detail_rows[$value["barang_id"]][] = $value;
        }

        $query_doc = PenerimaanBarangDoc::find()->where([
            "penerimaanbarang_id" => $id
        ])->all();

        return [
            'header' => $query_penerimaan,
            'detail' => $detail_rows,
            'document'=> $query_doc,
            'detail_penerimaan'=> $detail_penerimaan,
        ];
    }

    /**
    * @controller actionExportPdf
    * @attribute #nama_ruangan# => Untuk mengganti data di table
    * @attribute #tabel_penerimaan# => Untuk mengganti data di table
    * @attribute #tanggal# => Untuk mengganti data di table
    * @attribute #nama_pegawai# => Untuk mengganti data di table
    */
    public function actionExportPdf()
    {
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $pegawai = PegawaiView::find()->where([
            'ruangan_id' => $ruangan_id,
            'jabatan_id' => DocoConstants::VAR_J_K_R
        ])->one();

        $ruangan = Ruangan::find()->where([
            'ruangan_id' => $ruangan_id
        ])->one();

        $query = $this->getData();

        $print = new DocoPrint();
        $print->attributes = [
            '#nama_ruangan#' => !empty($ruangan->ruangan_nama) ? $ruangan->ruangan_nama : null,
            '#tabel_penerimaan#' => $this->renderPartial('index',[
                'detail' => $query->all()
            ]),
            '#tanggal#' => date('d-M-Y'),
            '#nama_pegawai#' => !empty($pegawai->nama_pegawai) ? $pegawai->nama_pegawai : null,
        ];

        $print->Output();
    }

    public function actionExportExcel()
    {
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $filter_nopenerimaan = $filter_nopo = $filter_supplier = $filter_barang = $filter_status = '-';
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
            if(isset($advancedFilters['barang_nama'])) {
                $filter_barang = $advancedFilters['barang_nama'];
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
        $query = $this->getData();
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::find()->where([
            'ruangan_id' => $ruangan_id
        ])->one();
        $nama_ruangan = !empty($ruangan) ? ucfirst($ruangan->ruangan_nama) : "";
        $title = "Penerimaan Barang Supplier {$nama_ruangan }";

        $result = [];
        foreach ($query->all() as $key => $value) {
            $tanggal = date('d-M-Y',strtotime($value['tgl_penerimaan']));
            $status_penerimaan = DocoConstants::$statusPenerimaan[$value['status_invoice']];
            $result[] = [
                'Tanggal Penerimaan' => $tanggal,
                'Nomor Penerimaan' => $value['no_penerimaan'],
                'Nomor PO' => $value['nomor_po'],
                'Supplier' => $value['supplier_nama'],
                'Nama Barang' => $value['barang_nama'],
                'Qty Terima' => $value['qty_diterima']." ".$value['satuan_besar'],
                'Total Harga (Rp.)' => $value['harga_total'],
                'Status' => $status_penerimaan,
            ];
        }

        $header = array(
            Yii::t("app", "Tanggal Cetak") => (date("d-M-Y")),
            Yii::t('app', "Tanggal Penerimaan") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir)))),
            Yii::t('app', "Nomor Penerimaan") => $filter_nopenerimaan,
            Yii::t('app', "Nomor PO") => $filter_nopo,
            Yii::t('app', "Supplier") => $filter_supplier,
            Yii::t('app', "Barang") => $filter_barang,
            Yii::t('app', "Status") => $filter_status
        );

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionCetakDetail
    * @attribute #no_penerimaan# => Menampilkan Nomor Penerimaan
    * @attribute #no_faktur# => Menampilkan Nomor Faktur
    * @attribute #no_po# => Menampilkan Nomor PO
    * @attribute #no_sj# => Menampilkan Nomor Surat Jalan
    * @attribute #tgl_sj# => Menampilkan Tanggal Surat Jalan
    * @attribute #tgl_penerimaan# => Menampilkan Tanggal Penerimaan
    * @attribute #nama_supplier# => Menampilkan Nama Supplier
    * @attribute #mengetahui# => Menampilkan Nama Pegawai Mengetahui
    * @attribute #menyetujui# => Menampilkan Nama Pegawai Menyetujui
    * @attribute #tabel_detail_penerimaan# => Menampilkan Data Detail Penerimaan
    * @attribute #catatan# => Menampilkan Catatan
    **/
    public function actionCetakDetail($id)
    {
        $print = new DocoPrint;

        $data_detail = self::actionDetail($id);

        $print_attributes = [
            '#no_penerimaan#' => $data_detail["header"]["no_penerimaan"] ,
            '#no_faktur#' => $data_detail["header"]["no_faktur"] ,
            '#no_po#' => $data_detail["header"]["no_pobarang"] ,
            '#no_sj#' => $data_detail["header"]["no_suratjalan"] ,
            '#tgl_sj#' => date("d-M-Y", strtotime($data_detail["header"]["tgl_suratjalan"])) ,
            '#tgl_penerimaan#' => date("d-M-Y", strtotime($data_detail["header"]["tgl_penerimaan"])) ,
            '#nama_supplier#' => $data_detail["header"]["supplier_nama"] ,
            '#mengetahui#' => $data_detail["header"]["mengetahui"] ,
            '#menyetujui#' => $data_detail["header"]["menyetujui"] ,
            '#penerima#' => $data_detail["header"]["menerima"] ,
            '#tabel_detail_penerimaan#' => $this->renderPartial("detail", [
                "detail" => $data_detail["detail"]
            ]),
            '#catatan#' => $data_detail["header"]["catatan"],
        ];


        $print->attributes = $print_attributes;

        $print->Output();
    }

    public function actionDeleteDoc($id)
    {
        $delete = (new PenerimaanBarangDoc)->delete([
            'penerimaanbarangdoc_id' => $id
        ]);

        return [
            'title' => 'Proses Berhasil!',
            'text' => 'Hapus Document Berhasil.',
        ];
    }

    public function actionSave($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = PenerimaanBarang::find()->where([
                'penerimaanbarang_id' => $id
            ])->one();

            if($model->is_verifikasi == 1){
                $model->no_faktur = $request->post('no_faktur');
                if($model->save()) {
                    $transaction->commit();
                    return [
                        'no_penerimaan' => $model->no_penerimaan,
                        'id_parent' => DocoHelpers::encrypt($model->penerimaanbarang_id)
                    ];
                } else {
                    return [
                        'status' => 422,
                        'data' => $model->errors
                    ];
                }
            }

            $detail = (new PenerimaanBarangDetail)->delete([
                'penerimaanbarang_id' => $id
            ]);
            $upload = new PenerimaanBarangDoc;

            $validasi = ValidasiPoBarang::find()->where([
                'validasipobarang_id' => $model->validasipobarang_id
            ])->one();

            if (empty($validasi)) {
                return [
                    'status' => 422,
                    'text' => 'Transaksi tidak dikenali',
                    'title' => 'Proses Gagal!'
                ];
            }


            $ruanganId = Yii::$app->jwt->ruangan_id;
            $tglSuratJalan = $request->post('tgl_suratjalan',null);
            $tglSuratJalan = !empty($tglSuratJalan) ? date('Y-m-d H:i:s',strtotime($tglSuratJalan)) : null;

            $model->supplier_id = $request->post('supplier_id');
            $model->no_suratjalan = $request->post('no_suratjalan');
            $model->tgl_suratjalan = $tglSuratJalan;
            $model->no_faktur = $request->post('no_faktur');
            $model->no_faktur_sementara = $request->post('no_faktur_sementara');
            $model->diterima_oleh = $request->post('diterima_oleh');
            $model->ruanganpenerima_id = $ruanganId;
            $model->peg_mengetahui = $request->post('peg_mengetahui');
            $model->peg_menyetujui = $request->post('peg_menyetujui');
            $model->catatan = $request->post('catatan');

            if(empty($model->no_faktur_sementara) && empty($model->no_faktur)) {
                return [
                    'status' => 422,
                    'text' => "No Faktur / No Faktur Sementara harus di isi",
                    'title' => 'Proses Gagal!'
                ];
            }

            if ($model->save()) {
                $idParent = $model->penerimaanbarang_id;
                $listData = $request->post('list_data',"{}");
                $listUpload = $request->post('file_upload',"{}");
                $dataDetail = json_decode($listData,true);
                $dataUpload = json_decode($listUpload,true);
                $tmpData = $tmpFile = [];

                /** Menyimpan Data ke file upload **/
                foreach ($dataUpload as $key => $value) {
                    $tmpFile[] = [
                        'penerimaanbarang_id' => $idParent,
                        'upload_berkas' => $value['upload_berkas'],
                        'catatan_berkas' => $value['catatan_berkas']
                    ];
                }

                if (!empty($tmpFile)) {
                    $upload::batchInsert($tmpFile);
                }

                /** Menyimpan ke penerimaan detail **/
                $sisaPenerimaan = 0;
                foreach ($dataDetail as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $attr) {
                            $qtyTerima = $attr['qty_diterima'];
                            $poBalance = $attr['po_balance'];
                            if ($qtyTerima > $poBalance) {
                                $transaction->rollBack();
                                return [
                                    'status' => 422,
                                    'text' => 'Penerimaan tidak boleh melebihi PO Balance',
                                    'title' => 'Proses Gagal!'
                                ];
                            }
                            $sisaPenerimaan += $poBalance - $qtyTerima;
                            if (empty($attr['qty_diterima'])) continue;
                            $row = [
                                'penerimaanbarang_id' => $idParent,
                                'validasipobarangdetail_id' => $attr['id_detail'],
                                'barang_id' => $attr['barang_id'],
                                'qty_po' => $attr['qty_po'],
                                'po_balance' => $attr['po_balance'],
                                'qty_diterima' => $qtyTerima,
                                's_konversibrg_id' => $attr['konversi_id'],
                                'tgl_kadaluarsa' => !empty($attr['tgl_kadaluarsa'])
                                    ? date('Y-m-d',strtotime($attr['tgl_kadaluarsa'])) : DocoConstants::DEFAULT_EXPIRED,
                                'no_batch' => !empty($attr['no_batch']) ? $attr['no_batch'] : null,
                                'harga' => $attr['harga'],
                                'discount' => $attr['discount'],
                                'discount_rp' => $attr['discount_rp'],
                                'jumlah' => $attr['jumlah'],
                                'keterangan' => !empty($attr['keterangan']) ? $attr['keterangan'] : null,
                            ];
                            $tmpData[] = $row;
                        }
                    }
                }
                PenerimaanBarangDetail::batchInsert($tmpData);
                $validasi->status_penerimaan = $sisaPenerimaan
                    ? DocoConstants::BELUM_SELESAI_PO : DocoConstants::SUDAH_DITERIMA_PO;
                $validasi->is_verifikasi = true;

                if ($validasi->save()) {
                    $transaction->commit();
                }

                return [
                    'no_penerimaan' => $model->no_penerimaan,
                    'id_parent' => DocoHelpers::encrypt($idParent)
                ];
            } else {
                return [
                    'status' => 422,
                    'data' => $model->errors
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionVerifikasiPenerimaan($id)
    {
        $model = new PenerimaanBarang;
        $model_validasi = new ValidasiPoBarang;
        try {
            $penerimaanBarang = $model->find()->where([
                "penerimaanbarang_id" => $id
            ])->one();

            $validasiObat = $model_validasi->find()->where([
                "validasipobarang_id" => $penerimaanBarang->validasipobarang_id
            ])->one();

            $penerimaanBarang->is_verifikasi = DocoConstants::PENERIMAAN_VERIF;
            $penerimaanBarang->save();

            $validasiObat->is_verifikasi = false;
            $validasiObat->save();

            IntegrasiAkunting::integratePenerimaanSupplier($penerimaanBarang->no_penerimaan, DocoConstants::JENIS_BARANG);

            return [
                "status" => 200,
                "title" => "Proses Berhasil",
                "text" => "Proses Verifikasi berhasil"
            ];
        } catch (Exception $e) {
            return [
                "status" => 500,
                "title" => "Proses Gagal",
                "text" => "Proses Verifikasi gagal"
            ];
        }
    }

    public function actionBatalPenerimaan($id)
    {
        $model = new PenerimaanBarang;
        $model_validasi = new ValidasiPoBarang;

        try {
            $penerimaanobat = PenerimaanBarang::find()->where([
                "penerimaanbarang_id" => $id
            ])->one();

            $penerimaanobat->is_verifikasi = DocoConstants::PENERIMAAN_BATAL;
            $penerimaanobat->save();

            // find all penerimaan
            $status_po = DocoConstants::BELUM_SELESAI_PO;
            $penerimaan = PenerimaanBarang::find()
                ->where(['validasipobarang_id' => $penerimaanobat->validasipobarang_id])
                ->andWhere(['NOT', ['is_verifikasi' => 2]])
                ->all();
            
            if(empty($penerimaan) || $penerimaan == null) {
                $status_po = DocoConstants::PO_BELUM_DITERIMA;
            }

            $validasi_poobat = $model_validasi->find()->where([
                "validasipobarang_id" => $penerimaanobat->validasipobarang_id
            ])->one();

            $validasi_poobat->is_verifikasi = false;
            $validasi_poobat->status_penerimaan = $status_po;
            $validasi_poobat->save();


            return [
                "status" => 200,
                "title" => "Proses Berhasil",
                "text" => "Proses Pembatalan berhasil"
            ];
        } catch (Exception $e) {
            return [
                "status" => 500,
                "title" => "Proses Gagal",
                "text" => "Proses Pembatalan gagal"
            ];
        }
    }

    private function getData()
    {
        $model = new InfoPenerimaanBarangDetail;
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
                $statusInvoice = (int) $_GET['advanced-filter']['status_invoice'];
                $query->andWhere(['status_invoice' => $statusInvoice]);
            }
        }

        $this->range = date('d-M-Y',strtotime($start)) . ' - ' . date('d-M-Y',strtotime($end));
        $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    public function actionGetBarangSupplier($supplier_id = 0, $pajak_id = 0)
    {
        $model = new InfoPenerimaanBarangDetail;
        $query = $model->find()->where([
            "supplier_id" => $supplier_id,
            "pajak_id" => $pajak_id
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionRetur()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $model_head = new ReturPenerimaanBarang;
        
        try {
            $tgl_retur = $request->post("tgl_retur");
            $model_head->tgl_retur = $tgl_retur;
            $model_head->pegawairetur_id = $request->post("pegawairetur_id");
            $model_head->alasan_retur = $request->post("alasan_retur");

            if ($model_head->save()) {
                $detail_retur = json_decode($request->post("detail_retur"), 1);
                $tmp_detail = [];
                foreach ($detail_retur as $det)
                {
                    $tmp_detail[$det["no_batch"]] = [
                        "returpenerimaanbarang_id" => $model_head->returpenerimaanbarang_id ,
                        "barang_id" => $det["barang_id"] ,
                        "satuanbesar_id" => $det["satuanbesar_id"],
                        "tgl_kadaluarsa" => empty($det["tgl_kadaluarsa"]) ? null : $det["tgl_kadaluarsa"],
                        "qty_retur" => $det["qty_retur"],
                        "qty_input" => $det["qty_input"],
                        "penerimaanbarang_id" => $det["penerimaanbarang_id"],
                        "penerimaanbarangdetail_id" => $det["penerimaanbarangdetail_id"],
                    ];
                    $batch[] = "'".$det["no_batch"]."'";
                }
            }else{
                return [
                    "status" => 422,
                    "data" => $model_head->errors
                ];
            }

            $batch_detail = ReturPenerimaanBarangDetail::batchInsert($tmp_detail, false);

            $inforetur = new InfoReturPenerimaanBarang;
            $inforetur = $inforetur->find()->where([
                "returpenerimaanbarang_id" => $model_head->returpenerimaanbarang_id
            ])->one();

            $inforeturdetail =  new InfoReturPenerimaanBarangDetail;
            $inforeturdetail = $inforeturdetail->find()->where([
                "returpenerimaanbarang_id" => $model_head->returpenerimaanbarang_id
            ])
            ->all();
            $validationDetails = [];
            foreach ($inforeturdetail as $row)
            {
                $data_retur[$row["no_batch"]] = [
                    "returpenerimaanbarangdetail_id" => $row["returpenerimaanbarangdetail_id"],
                    "barang_id" => $row["barang_id"],
                    "tglkadaluarsa" => $row["tgl_kadaluarsa"],
                    "nobatch" => $row["no_batch"],
                    "qty_retur" => $row["qty_retur"],
                ];

                $penerimaandetail = new InfoPenerimaanBarangDetail;
                $penerimaandetail = $penerimaandetail->find()->where([
                    "penerimaanbarangdetail_id" => $row["penerimaanbarangdetail_id"]
                ])->one();

                if (empty($penerimaandetail)){
                    return [
                        "status" => 422,
                        "title" => "Error!",
                        "text" => "Data Penerimaan Barang tidak dapat ditemukan"
                    ];
                }

                $validasipo = new ValidasiPoBarangDetail;
                $validasipo = $validasipo->find()->where([
                    "validasipobarangdetail_id" => $penerimaandetail->validasipobarangdetail_id
                ])->one();
                if (empty($validasipo)){
                    return [
                        "status" => 422,
                        "title" => "Error!",
                        "text" => "Data PO Barang tidak dapat ditemukan"
                    ];
                }
                $validasipo->qty_retur = $validasipo->qty_retur + $row["qty_input"];
                $validasipo->save(false);
                $cek[] = $validasipo;
                $validationDetails[] = $validasipo->attributes;
            }
            // Execute Set Status Validation
            $validasiPoBarangId = ArrayHelper::getValue($validationDetails, '0.validasipobarang_id');
            ValidasiPoBarang::checkAndSetStatus($validasiPoBarangId);

            $data["no_batch"] = $batch;
            $data["data_retur"] = $data_retur;

            $update_stok_barang = $this->updateStokBarang($data);
            if ($update_stok_barang["status"] == false)
            {
                return [
                    "status" => 402,
                    "title" => "Proses gagal",
                    "text" => $update_stok_barang["msg"],
                ];
            }

            $transaction->commit();
            IntegrasiAkunting::integrateReturSupplier($inforetur->no_returpenerimaanbarang, DocoConstants::JENIS_BARANG);
            return [
                "status" => 200,
                "title" => "Proses Sukses",
                "text" => "Retur Penerimaan berhasil disimpan",
                "no_retur" => empty($inforetur) ? "" : $inforetur->no_returpenerimaanbarang,
                "id_retur" => empty($inforetur) ? "" : DocoHelpers::encrypt($inforetur->returpenerimaanbarang_id),
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

    /**
    * @controller actionReturPdf
    * @attribute #no_retur# => Menampilkan Nomor Retur
    * @attribute #tgl_retur# => Menampilkan Tanggal Retur
    * @attribute #nama_supplier# => Menampilkan Nama Supplier
    * @attribute #petugas_retur# => Menampilkan Nama Petugas Retur
    * @attribute #alasan_retur# => Menampilkan Alasan Retur
    * @attribute #detail_retur# => Data Detail Retur

    **/

    public function actionReturPdf($id)
    {
        $print = new DocoPrint;

        $model = new InfoReturPenerimaanBarang;
        $model_detail = new InfoReturPenerimaanBarangDetail;

        $head = $model->find()->where([
            "returpenerimaanbarang_id" => $id
        ])->one();

        if (!empty($head))
        {
            $detail = $model_detail->find()->where([
                "returpenerimaanbarang_id" => $head["returpenerimaanbarang_id"]
            ])->all();

            if (!empty($detail))
            {
                $print_attributes = [
                    "#no_retur#" => $head["no_returpenerimaanbarang"] ,
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

}
