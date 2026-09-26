<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\businessLogic\StokBarang as BLStokBarang;
use app\modules\v1\models\InfoMutasiBarang;
use app\modules\v1\models\InfoMutasiBarangView;
use app\modules\v1\models\InfoDistribusiBarangView;
use app\modules\v1\models\InfoMutasiBarangDetailView;
use app\modules\v1\models\DetailPemesananBarangView;
use app\modules\v1\models\InfoPemesananBarangDetailView;
use app\modules\v1\models\InfoPemesananBarangView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\MutasiBarang;
use app\modules\v1\models\MutasiBarangDetail;
use app\modules\v1\models\PesanBarang;
use app\modules\v1\models\PesanBarangDetail;
use app\modules\v1\models\Ruangan;
use app\modules\v1\entities\StatusDistribusi;
use yii\helpers\ArrayHelper;

class InfPemesananBarangController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoPemesananBarangView';
    protected $_title = "Informasi Pemesanan Barang Masuk";

    const FEFO = 'FEFO';
    const FIFO = 'FIFO';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    private function getDetail($id)
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $model_head = new InfoPemesananBarangView;
        $model_detail = new InfoPemesananBarangDetailView;
        $query_head = $model_head->find()->where([
            "pesanbarang_id" => $id
        ])->one();
        $query_detail = $model_detail->find()->where([
            "pesanbarang_id" => $id
        ])
        ->orderBy([
            "barang_id" => SORT_ASC
        ])
        ->all();

        $ruangan = Ruangan::find()->where([
            "ruangan_id" => $ruangan_id
        ])->one();

        return [
            "header" => $query_head,
            "detail" => $query_detail,
            "ruangan" => $ruangan
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionBatalPemesanan() {
        return Yii::$app->docoPlugin->execute('batal_pemesanan_barang');
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPemesananBarangView;
            $query = $model::find()->where(['ruanganpemesan_id' => $request->get('ruangan_id', null)]);
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            if(isset($_GET['advanced-filter'])) {

                if(isset($_GET['advanced-filter']['tgl_pesanbarang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesanbarang']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pesanbarang']);
                }
                if(isset($_GET['advanced-filter']['instalasi_tujuan'])){
                    $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_tujuan'];
                    unset($_GET['advanced-filter']['instalasi_tujuan']);
                }

                if(isset($_GET['advanced-filter']['ruangan_tujuan'])){
                    $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_tujuan'];
                    unset($_GET['advanced-filter']['ruangan_tujuan']);
                }
            }

            $query->andWhere(['between', 'tgl_pesanbarang', $start, $end]);
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

    public function actionGenerateApi()
    {
        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
        ]);

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();

        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
        ]);

        // pemesanan
        $modelPemesanan = new PesanBarang;
        $queryPemesanan = $modelPemesanan::find();

        $queryPemesanan = DocoRestActiveFilter::advancedFilter($modelPemesanan, $queryPemesanan);
        $queryPemesanan = new ActiveDataProvider([
            'query' => $queryPemesanan,
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'pemesanan' => $queryPemesanan->getModels(),
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new InfoPemesananBarangView;
        $query = $model::find(true)->where(['ruanganpemesan_id' => $request->get('ruangan_id')]);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pesanbarang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesanbarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pesanbarang']);
            }

            if(isset($_GET['advanced-filter']['instalasi_tujuan'])){
                $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_tujuan'];
                unset($_GET['advanced-filter']['instalasi_tujuan']);
            }

            if(isset($_GET['advanced-filter']['ruangan_tujuan'])){
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_tujuan'];
                unset($_GET['advanced-filter']['ruangan_tujuan']);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pemesanan')] = $value['tgl_pesanbarang'];
            $newValue[\Yii::t('app', 'Nomor Pemesanan')] = $value['no_pemesanan'];
            $newValue[\Yii::t('app', 'Instalasi Tujuan')] = $value['instalasi_tujuan'];
            $newValue[\Yii::t('app', 'Ruangan Tujuan')] = $value['ruangan_tujuan'];
            $newValue[\Yii::t('app', 'Status')] = $value['status_pesan'];
            $result[$key] = $newValue;
        }

        $instalasi = new Instalasi;
        $instalasi_nama = '';
        $ruangan_nama = '';

        if(isset($_GET['advanced-filter']['instalasi_id'])) {
            $queryInstalasi = $instalasi->findOne($_GET['advanced-filter']['instalasi_id']);
            $instalasi_nama = ($queryInstalasi) ? $queryInstalasi->instalasi_nama : '';
        }

        $ruangan = new Ruangan;
        if(isset($_GET['advanced-filter']['ruangan_id'])) {
            $queryRuangan = $ruangan->findOne($_GET['advanced-filter']['ruangan_id']);
            $ruangan_nama = ($queryRuangan) ? $queryRuangan->ruangan_nama : '';
        }

        $header = array(
            Yii::t("app", "Tanggal Pemesanan") => (($start." - ".$end)),
            Yii::t("app", "Nomor Pemesanan") => isset($_GET['advanced-filter']['no_pemesanan']) ? $_GET['advanced-filter']['no_pemesanan'] : '',
            Yii::t("app", "Instalasi Tujuan") => ($instalasi_nama),
            Yii::t("app", "Ruangan Tujuan") => ($ruangan_nama),
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));

        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    public function actionDelete($id)
    {
        try {
            $result = (new PesanBarang)->delete($id);
            $result = (new PesanBarangDetail)->find()->where(['pesanbarang_id' => $id])->one();
            if($result) {
                $result->delete();
            }
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $model = new InfoDistribusiBarangView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pesanbarang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesanbarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pesanbarang']);
            }
            
            if(isset($_GET['advanced-filter']['instalasi_tujuan'])){
                $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_tujuan'];
                unset($_GET['advanced-filter']['instalasi_tujuan']);
            }

            if(isset($_GET['advanced-filter']['ruangan_tujuan'])){
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_tujuan'];
                unset($_GET['advanced-filter']['ruangan_tujuan']);
            }
        }

        $query->andWhere(['between', 'tgl_pesanbarang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetFillter()
    {
        $instalasi = ArrayHelper::map(Instalasi::find()->all(),'instalasi_id','instalasi_nama');
        $ruanganData = Ruangan::find()->leftJoin('instalasi_m','instalasi_m.instalasi_id = ruangan_m.instalasi_id')->select(['ruangan_id',"CONCAT(instalasi_nama,' - ',ruangan_nama) AS instalasi_ruangan"])->asArray()->all();
        $ruangan = ArrayHelper::map($ruanganData,'ruangan_id','instalasi_ruangan');
        $nopemesanan = ArrayHelper::map(InfoPemesananBarangView::find()->all(),'no_pemesanan','no_pemesanan');
        $statusdistribusi = StatusDistribusi::getlist();
        return [
            'instalasi' => $instalasi,
            'ruangan' => $ruangan,
            'nopemesanan' => $nopemesanan,
            'statusdistribusi' => $statusdistribusi
        ];
    }

    public function actionGetHeader($id)
    {
        $model = new InfoDistribusiBarangView;

        try {
            $data = $model->find()
                ->where(["pesanbarang_id" => $id])
                ->one();
            
            return $data;
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

    public function actionGetDetail($id)
    {
        $model = new DetailPemesananBarangView;    

        try {
            $query = $model->find(true);

            $query->andWhere(["pesanbarang_id" => $id]);
            $query->orderBy([
                "barang_nama" => SORT_ASC
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
    * @controller actionCetakPemesanan
    * @attribute #nama_ruangan# => Menampilkan Nama Ruangan
    * @attribute #tanggal_pemesanan# => Menampilkan Tanggal Pemesanan
    * @attribute #tanggal_minta_kirim# => Menampilkan Tanggal Minta Dikirim
    * @attribute #no_pemesanan# => Menampilkan No. Pemesanan
    * @attribute #ruangan_asal# => Menampilkan Nama Ruangan Asal Pemesan
    * @attribute #keterangan# => Menampilkan Keterangan Pesan
    * @attribute #tabel_pemesanan# => Data Tabel Pemesanan
    **/

    public function actionCetakPemesanan($id)
    {
        // define data
        $model_head = new InfoPemesananBarangView;
        $model_detail = new InfoPemesananBarangDetailView;
        
        $header = $model_head->find()->where([
            "pesanbarang_id" => $id
        ])->one();
        
        $detail = $model_detail->find()->where([
            "pesanbarang_id" => $id
        ])
        ->orderBy([
            "barang_nama" => SORT_ASC
        ])
        ->all();
        // end
        
        $print = new DocoPrint;

        $print->attributes = [
            "#nama_ruangan#" => isset($header["ruangan_tujuan"]) ? $header["ruangan_tujuan"] : "-",
            "#tanggal_pemesanan#" => isset($header["tgl_pesanbarang"]) ? date("d M Y H:i:s", strtotime($header["tgl_pesanbarang"])) : "-",
            "#tanggal_minta_kirim#" => isset($header["tgl_mintadikirim"]) ? date("d M Y H:i:s", strtotime($header["tgl_pesanbarang"])) : "-",
            "#no_pemesanan#" => isset($header["no_pemesanan"]) ? $header["no_pemesanan"] : "-",
            "#ruangan_asal#" => isset($header["ruangan_pemesan"]) ? $header["ruangan_pemesan"] : "-",
            "#keterangan#" => !is_null($header["keterangan_pesan"]) && isset($header["keterangan_pesan"]) ? $header["keterangan_pesan"] : "",
            "#tabel_pemesanan#" => $this->renderPartial("table-pemesanan", ["detail" => $detail]),
        ];

        $print->Output();
    }

    public function actionGetDataPemesanan($id)
    {
        $header = new InfoPemesananBarangView;
        $detail = new InfoPemesananBarangDetailView;

        try {
            $query_header = $header->find()->where([
                "pesanbarang_id" => $id
            ])->one();

            $query_detail = $detail->find()->where([
                "pesanbarang_id" => $id
            ])
            ->orderBy([
                "barang_nama" => SORT_ASC
            ])
            ->all();
        } catch (\Exception $e) {
            $query_header = $query_detail = [];
        }

        return [
            "data_header" => $query_header,
            "data_detail" => $query_detail,
        ];
    }


    /*
    * Masuki Table Header v
    * Masuki Table Detail v
    * Ubah Status Pemesanan v
    * Update Stok Ruangan
    */
    public function actionMutasi()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $tgl_mutasibarang = $request->post("tgl_mutasibarang");
        $tgl_mutasibarang = date('Y-m-d', strtotime($tgl_mutasibarang)). ' ' . date('H:i:s');

        $model_head = new MutasiBarang;

        try {
            $m_pemesanan = new PesanBarang;
            $pesanbarang = $m_pemesanan->find()->where([
                "pesanbarang_id" => $request->post("pesanbarang_id")
            ])->one();

            if (!empty($pesanbarang) && $pesanbarang->statuspesan == DocoConstants::SUDAH_DIKIRIM) {
                return [
                    "status" => 422,
                    "title" => "Proses Gagal !",
                    "message" => "Pemesanan Sudah Dikirim."
                ];
            }

            if (!empty($pesanbarang) && $pesanbarang->statuspesan == DocoConstants::BATAL_PESAN) {
                return [
                    "status" => 422,
                    "title" => "Proses Gagal !",
                    "message" => "Pemesanan Sudah Dibatalkan."
                ];
            }

            $model_head->pesanbarang_id = $request->post("pesanbarang_id");
            $model_head->pegawaimengetahui_id = $request->post("pegawaimengetahui_id");
            $model_head->pegawaipengirim_id = Yii::$app->jwt->user->pegawai_id;
            $model_head->ruangantujuan_id = $request->post("ruangantujuan_id");
            $model_head->tgl_mutasibarang = $tgl_mutasibarang;
            $model_head->totalhargamutasi = $request->post("totalhargamutasi");
            $model_head->ruanganasal_id = Yii::$app->jwt->ruangan_id;
            $model_head->status_mutasi = DocoConstants::STATUS_MUTASI_DIKIRIM;

            if (!$model_head->save()) {
                return [
                    "status" => 422,
                    "title" => "Proses Simpan Mutasi Barang Gagal!",
                    "error_msg" => $model_head->errors
                ];
            }

            $mutasi_detail = json_decode($request->post("mutasi_detail"), 1);
            $qty_count = array_sum(array_column($mutasi_detail, 'qty_mutasi_input'));
            if($qty_count === 0){
                return [
                    "status" => 422,
                    "title" => "Proses Simpan Mutasi Barang Gagal!",
                    "message" => "Qty Kirim harus lebih dari 0"
                ];
            }

            $tmp_detail = [];
            foreach ($mutasi_detail as $det) {
                $tmp_detail[] = [
                    "pesanbarangdetail_id" => $det["pesanbarangdetail_id"],
                    "barang_id" => $det["barang_id"],
                    "mutasibarang_id" => $model_head->mutasibarang_id,
                    "qty_mutasi" => $det["qty_mutasi"],
                    "qty_dipesan" => $det["qty_dipesan"],
                    "satuanbesar_id" => $det["satuanbesar_id"],
                    "jumlah_input" => $det["qty_mutasi_input"],
                    "satuankecil_id" => $det["satuankecil_id"],
                    "harga_netto" => $det["harga_netto"],
                ];
            }

            $batch_detail = MutasiBarangDetail::batchInsert($tmp_detail, false);

            if (!empty($pesanbarang)) {
                $pesanbarang->statuspesan = DocoConstants::SUDAH_DIKIRIM;
                if (!$pesanbarang->save()) {
                    return [
                        "status" => 422,
                        "title" => "Proses Simpan Mutasi Barang Gagal!",
                        "error_msg" => $pesanbarang->errors
                    ];
                }
            }

            $m_mutasi_detail = new MutasiBarangDetail;
            $mutasi_detail = $m_mutasi_detail->find()->where([
                "mutasibarang_id" => $model_head->mutasibarang_id
            ])->all();


            if (!empty($mutasi_detail)) {
                foreach ($mutasi_detail as $index => $row) {
                    $mutasi[$row["mutasibarangdetail_id"]] = [
                        'satuankecil_id' => $row["satuankecil_id"],
                        'mutasibarangdetail_id' => $row["mutasibarangdetail_id"],
                        'barang_id' => $row["barang_id"],
                        'qty_satuanpakai' => $row["qty_mutasi"],
                    ];
                }
            }

            $tanggalBerlaku = date('Y-m-d');
            $currentMetode = false;
            // Mencari Metode
            $konfig = $connection->createCommand("
                SELECT metodeantrian FROM konfiggudang_k
                WHERE tglberlaku >= '{$tanggalBerlaku}'
                AND is_active = true
            ")->queryOne();

            if ($konfig) {
                $currentMetode = isset($konfig['metodeantrian']) ? strtoupper($konfig['metodeantrian']) : self::FIFO;
            }

            $transaction->commit();

            $m = new InfoMutasiBarangView;
            $where = [
                "mutasibarang_id" => $model_head->mutasibarang_id
            ];
            $info_mutasi_barang = $m->find()->where($where)->one();

            return [
                "status" => 200,
                "title" => "Proses Sukses",
                "text" => "Retur Penerimaan berhasil disimpan",
                "no_transaksi" => !empty($info_mutasi_barang) ? $info_mutasi_barang->nomutasi_barang : null,
                "id_transkasi" => !empty($info_mutasi_barang) ? DocoHelpers::encrypt($info_mutasi_barang->mutasibarang_id) : null
            ];

        } catch (\Exception $e) {
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
    * @kode_dokumen_tercetak cetak-mutasi-inf-pemesanan-barang
    * @controller actionCetakMutasi
    * @attribute #no_pemesanan# => Menampilkan Nomor Pemesanan
    * @attribute #no_mutasi# => Menampilkan Nomor Mutasi
    * @attribute #tgl_cetak# => Menampilkan Tanggal Cetak
    * @attribute #tgl_dikirim# => Menampilkan Tanggal Kirim
    * @attribute #ruangan_asal# => Menampilkan Ruangan Asal
    * @attribute #ruangan_tujuan# => Menampilkan Ruangan Tujuan
    * @attribute #pegawai_mutasi# => Menampilkan Pegawai Mutasi
    * @attribute #pegawai_mengetahui# => Menampilkan Pegawai Mengetahui
    * @attribute #detail_mutasi# => Menampilkan Table Mutasi
    **/
    public function actionCetakMutasi($id)
    {
        return Yii::$app->docoPlugin->execute('cetak_mutasi');
    }

}
