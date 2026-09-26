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

use app\components\Cache;
use app\modules\v1\businessLogic\StokBarang as BLStokBarang;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoPemesananBarangView;
use app\modules\v1\models\InfoDistribusiBarangView;
use app\modules\v1\models\DetailPemesananBarangView;
use app\modules\v1\models\InfoPemesananBarangDetailView;
use app\modules\v1\models\InfoMutasiBarangDetailView;
use app\modules\v1\models\PesanBarang;
use app\modules\v1\models\PesanBarangDetail;
use app\modules\v1\entities\StatusDistribusi;
use app\modules\v1\models\MutasiBarang;
use app\modules\v1\models\MutasiBarangDetail;
use app\modules\v1\models\TerimaMutasiBarang;
use app\modules\v1\models\TerimaMutasiBarangDetail;
use app\modules\v1\models\DetailMutasiBarangView;

class InformasiPemesananBarangKeluarController extends \Doco\components\DocoActiveController
{

    public $modelClass = '';

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

    public function actionBatalPemesanan() {
        return Yii::$app->docoPlugin->execute('batal_pemesanan_barang');
    }

    public function actionIndex()
    {
        $model = new InfoDistribusiBarangView;
        $query = $model::find();
        $ruangan_id = Yii::$app->jwt->ruangan_id;

        $between = false;
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
                $between = true;
            }

            if(isset($_GET['advanced-filter']['ruangan_pemesan_id'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_pemesan_id'];
            }

            if(isset($_GET['advanced-filter']['status_pengiriman'])) {
                $status_pengiriman = $_GET['advanced-filter']['status_pengiriman'];
                unset($_GET['advanced-filter']['status_pengiriman']); 
            }
        }

        if(isset($status_pengiriman)) {
            $query->andWhere(['or', ['status_pengiriman' => $status_pengiriman], ['status_distribusi' => $status_pengiriman]]);
        }

        $query->andWhere(['between', 'tgl_pesanbarang', $start, $end]);

        $query->andWhere([
            'ruanganpemesan_id' => $ruangan_id
        ]);

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

    public function actionGetDetail($id)
    {
        $model = new InfoDistribusiBarangView;
        $pesan_barang = $model->find()->where(['pesanbarang_id' => $id])->one();

        return [
            'data' => $pesan_barang
        ];
    }

    public function actionGetDataDetail($id)
    {
        $model = new DetailPemesananBarangView;
        $query = $model::find(true);

        $query->andWhere([
            'pesanbarang_id' => $id
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionCetakPdf 
    * @attribute #nama_ruangan# => Menampilkan Nama Ruangan 
    * @attribute #tanggal_pemesanan# => Menampilkan Tanggal Pemesanan
    * @attribute #tanggal_minta_dikirim# => Menampilkan Tanggal Minta dikirim
    * @attribute #ruangan_tujuan# => Menampilkan Ruangan Rujuan
    * @attribute #catatan# => Menampilkan Catatan
    * @attribute #tabel_detail# => Menampilkan Tabel Detail
    **/
    public function actionCetakPdf()
    {
        $model = new PesanBarang;
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        if (isset($get['id'])) {
            $id = $get['id'];
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $pesan_barang = $model->getDataPemesananById($id);
            $pesan_barang_detail = $model->getDetailPemesananById($id);
            
            $print = new DocoPrint();
            $print->attributes = [
                '#nama_ruangan#' => !empty($ruangan->ruangan_nama) ? $ruangan->ruangan_nama : null,
                '#tanggal_pemesanan#' => !empty($pesan_barang['tgl_pesanbarang']) 
                            ? date('d M Y H:i:s', strtotime($pesan_barang['tgl_pesanbarang'])) : '-',
                '#tanggal_minta_dikirim#' => !empty($pesan_barang['tgl_mintadikirim']) 
                            ? date('d M Y H:i:s', strtotime($pesan_barang['tgl_mintadikirim'])) : '-',
                '#nomer_pemesanan#' => !empty($pesan_barang['no_pemesanan']) ? $pesan_barang['no_pemesanan'] : '-',
                '#ruangan_tujuan#' => !empty($pesan_barang['ruangan_nama']) ? $pesan_barang['ruangan_nama'] : '-',
                '#instalasi_tujuan#' => !empty($pesan_barang['instalasi_nama']) ? $pesan_barang['instalasi_nama'] : '-',
                '#catatan#' => !empty($pesan_barang['keterangan_pesan']) ? $pesan_barang['keterangan_pesan'] : '-',
                '#tabel_detail#' => $this->renderPartial('index', [
                    'detail' => $pesan_barang_detail,
                ]),            
            ];
            $print->Output();
        }
    }

    public function actionDelete($id)
    {
        $header = PesanBarang::updateAll([
            'statuspesan' => DocoConstants::BATAL_PESAN
        ],"pesanbarang_id = {$id}");

        $detail = (new PesanBarangDetail)->delete([
            'pesanbarang_id' => $id
        ]);

        return [
            'title' => 'Proses Berhasil !',
            'text' => 'Transaksi berhasil dibatalkan'
        ];
    }

    public function actionProsesTerima()
    {
        $pesanbarang_id = Yii::$app->request->post('pesanbarang_id');

        $mutasi = MutasiBarang::find()->where(['pesanbarang_id'=>$pesanbarang_id])->one();
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model = new TerimaMutasiBarang;
                $model->mutasibarang_id = $mutasi->mutasibarang_id;
                $model->ruanganpenerima_id = $mutasi->ruangantujuan_id;
                $model->ruanganasalmutasi_id = $mutasi->ruanganasal_id;
                $model->tglterima = date('Y-m-d H:i:s');
                $model->pegawaipenerima_id = Yii::$app->jwt->user->pegawai_id;
                $model->save(false);

                $mutasi_detail = DetailMutasiBarangView::find()
                    ->where(['mutasibarang_id' => $mutasi->mutasibarang_id])
                    ->asArray()
                    ->all();

                $dataInsert = [];
                foreach ($mutasi_detail as $key => $row) {
                    
                    $dataInsert[] = [
                        'terimamutasibarang_id' => $model->terimamutasibarang_id,
                        'mutasibarangdetail_id' => $row['mutasibarangdetail_id'],
                        'satuankecil_id' => $row['satuankecil_id'],
                        'ruangan_id' => $row['ruangan_tujuan_id'],
                        'barang_id' => $row['barang_id'],
                        'jmlmutasi' => $row['qty_mutasi'],
                        'jmlterima' => $row['qty_mutasi'],
                        'is_active' => true
                    ];
                }

                TerimaMutasiBarangDetail::batchInsert($dataInsert);
                
                if (!empty($mutasi) && $mutasi->status_mutasi == DocoConstants::STATUS_MUTASI_DITERIMA) {
                    return [
                        "status" => 422,
                        "title" => "Proses Gagal !",
                        "message" => "Tidak bisa melakukan mutasi. Sudah Dilakukan Penerimaan."
                    ];
                }
                $mutasi->status_mutasi = DocoConstants::STATUS_MUTASI_DITERIMA;
                $mutasi->save(false);

                $modelPesanBarang = PesanBarang::findOne($mutasi->pesanbarang_id);
                $modelPesanBarang->statuspesan = DocoConstants::STATUS_PESAN_DITERIMA;
                $modelPesanBarang->save(false);

                //stok terima mutasi barang
                $detailTrans = $connection->createCommand("
                    SELECT * FROM terimamutasibarangdetail_t 
                    WHERE terimamutasibarang_id = {$model->terimamutasibarang_id}
                ")->queryAll();

                $mutasi_ = [];
                foreach ($detailTrans as $row) {
                    $mutasi_[$row["mutasibarangdetail_id"]] = [
                        'terimamutasidetail_id' => $row['terimamutasibarangdetail_id'],
                        'satuankecil_id' => $row["satuankecil_id"],
                        'mutasibarangdetail_id' => $row["mutasibarangdetail_id"],
                        'barang_id' => $row["barang_id"],
                        'qty_satuanpakai' => $row["jmlterima"],
                        'ruangan_id' => $mutasi->ruanganasal_id,
                        'ruangantujuan_id' => $mutasi->ruangantujuan_id
                    ];
                }
                
                $currDate = date('Y-m-d H:i:s');
                $currentMetode = Cache::methodAntrian();
                // Execute By Condition
                if ($currentMetode === DocoConstants::FEFO) {
                    $methode = BLStokBarang::methodeFEFO($mutasi_, $currDate, true);
                } else {
                    $methode = BLStokBarang::methodeFIFO($mutasi_, $currDate, true);
                }

                $transaction->commit();

                return [
                    'message' => 'Sukses'
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line'=>$e->getLine(),
                'file'=>$e->getFile()
            ];
        }
    }
}
