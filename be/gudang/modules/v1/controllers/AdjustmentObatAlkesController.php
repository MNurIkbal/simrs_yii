<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\AdjusmenObat;
use app\modules\v1\models\AdjusmenObatMasuk;
use app\modules\v1\models\AdjusmenObatKeluar;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\ObatAlkesView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\SatuanUnit;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\KetersediaanObatView;
use SirsCore\features\IntegrasiAkunting;

use SirsCore\businessLogic\StokObatAlkes as BLStokObatAlkes;
use app\modules\v1\businessLogic\UpdateHargaNetto;

class AdjustmentObatAlkesController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\AdjusmenObat';

    const FEFO = 'FEFO';
    const FIFO = 'FIFO';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new AdjusmenObat;
            $query = $model::find();
            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['daftartindakan_nama'])) {
                    $query->andWhere(['ILIKE', 'daftartindakan_nama', $advancedFilter['daftartindakan_nama']]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $model = new AdjusmenObat;

        try {
            $dataJson = $request->post('data',"{}");
            $data = json_decode($dataJson,true);
            $postAdjustment = $post['post']['AdjusmenObatForm'];
            $tgl_adjusmen = date('Y-m-d',strtotime($postAdjustment['tgl_adjusmen']));
            $date = date_create($tgl_adjusmen);
            date_time_set($date,date('H'),date('i'),date('s'));
            $tgl_adjusmen = date_format($date,'Y-m-d H:i:s');
            $model->attributes = $postAdjustment;
            $model->jenis_adjusmen = ($post['post']['tab-aktif'] == 'masuk') ? 0 : 1;
            $model->ruangan_adjusmen_id = Yii::$app->jwt->ruangan_id;
            $_POST['ruangan_id'] = Yii::$app->jwt->ruangan_id;

            if($model->validate() && $model->save()) {

                if(!empty($data)) {
                    $dataInsert = [];
                    $dataInsertStok = [];
                    $idParent = $model->adjusmenobat_id;
                    foreach ($data as $key => $value) {
                        $total_konversi = $value['qty_besar'] * $value['nilai_konversi'];
                        if($post['post']['tab-aktif'] == 'masuk') {

                            $dataInsert[] = [
                                'adjusmenobat_id' => $idParent,
                                'obatalkes_id' => $value['obatalkes_id'],
                                'tgl_kadaluarsa' => $value['tgl_kadaluarsa'],
                                'qty' => $value['qty_besar'],
                                'satuankecil_id' => $value['satuankecil_id'],
                                'satuanbesar_id' => $value['satuanbesar_id'],
                                'harga_netto' => $value['harga_netto'],
                                'harga_netto_satuan' => $value['harga_netto'] / $value['qty_besar'],
                                'satuankonversi_id' => $value['satuankonversi_id'],
                                'qty_konversi' => $total_konversi,
                                'no_batch' => $value['no_batch'],
                                'keterangan' => $value['keterangan'],
                            ];

                            $dataInsertStok[] = [
                                'tglstok_in' => $tgl_adjusmen,
                                'stokoa_aktif' => true,
                                'ruangan_id' => Yii::$app->jwt->ruangan_id,
                                'obatalkes_id' => $value['obatalkes_id'],
                                'tglkadaluarsa' => $value['tgl_kadaluarsa'],
                                'qtystok_in' => $total_konversi,
                                'harganetto' => $value['harga_netto'],
                                'satuankecil_id' => $value['satuankecil_id'],
                                'adjusmenobatmasuk_id' => $idParent
                            ];

                        }
                        else {
                            $dataInsert[] = [
                                'adjusmenobat_id' => $idParent,
                                'obatalkes_id' => $value['obatalkes_id'],
                                'satuankecil_id' => $value['satuankecil_id'],
                                'satuanbesar_id' => $value['satuanbesar_id'],
                                'satuankonversi_id' => $value['satuankonversi_id'],
                                'qty_konversi' => $total_konversi,
                                'alasan' => $value['alasan'],
                                'qty' => $value['qty_besar'],
                                'no_batch' => $value['no_batch'],
                                'keterangan' => $value['keterangan']
                            ];
                            $dataInsertStok[] = [
                                'ruangan_id' => Yii::$app->jwt->ruangan_id,
                                'obatalkes_id' => $value['obatalkes_id'],
                                'qtystok_out' => $total_konversi,
                                'satuankecil_id' => $value['satuankecil_id'],
                                'adjusmenobatkeluar_id' => $idParent
                            ];

                            $qty_sisa = $this->actionCekStok($_POST['ruangan_id'],$value['obatalkes_id']);
                            if($total_konversi > $qty_sisa) {
                                return [
                                    'text' => 'Maaf, Stok Obat '.$value['obatalkes_nama'].' tidak mencukupi.',
                                    'status' => 422,
                                    'title' => 'Proses Gagal!',
                                    'no_adjusmen' => null
                                ];
                            }
                        }
                    }
                    
                    $tanggalBerlaku = date('Y-m-d');
                    $konfig = $connection->createCommand("
                        SELECT metodeantrian, hargaygdigunakan FROM konfigfarmasi_k
                        WHERE tglberlaku >= '{$tanggalBerlaku}'
                        AND konfigfarmasi_aktif = true
                        AND is_active = true
                    ")->queryOne();

                    if($post['post']['tab-aktif'] == 'masuk') {
                        AdjusmenObatMasuk::batchInsert($dataInsert);
                        $dataAdjustment = $connection->createCommand("
                            SELECT * FROM adjusmenobatmasuk_t
                            WHERE adjusmenobat_id = {$idParent}
                        ")->queryAll();

                        $dataInsertStok = [];
                        foreach ($dataAdjustment as $v) {
                            $dataInsertStok[] = [
                                'tglstok_in' => $tgl_adjusmen,
                                'stokoa_aktif' => true,
                                'ruangan_id' => Yii::$app->jwt->ruangan_id,
                                'obatalkes_id' => $v['obatalkes_id'],
                                'tglkadaluarsa' => date('Y-m-d', strtotime($v['tgl_kadaluarsa'])),
                                'qtystok_in' => $v['qty_konversi'],
                                'harganetto' => $v['harga_netto'] / $v['qty_konversi'],
                                'satuankecil_id' => $v['satuankecil_id'],
                                'adjusmenobatmasuk_id' => $v['adjusmenobatmasuk_id']
                            ];
                        }

                        StokObatAlkes::batchInsert($dataInsertStok);
                        
                        if($konfig['hargaygdigunakan'] == DocoConstants::HARGA_FIX_RATE) {
                            Yii::$app->runAction(
                                'v1/allow/update-base-price',
                                [
                                    'transaksi_id' => $idParent,
                                    'tipe' => 'ADJ_MASUK',
                                    'detail' => json_encode($dataInsertStok)
                                ]
                            );
                        }
                    }
                    else {
                        AdjusmenObatKeluar::batchInsert($dataInsert);

                        // Mencari Metode dengan nilai default FEFO
                        $currentMetode = BLStokObatAlkes::FEFO;
                        if ($konfig) {
                            $currentMetode = isset($konfig['metodeantrian'])
                                                ? strtoupper($konfig['metodeantrian']) : BLStokObatAlkes::FEFO;
                        }

                        $dataAdjustment = $connection->createCommand("
                            SELECT * FROM adjusmenobatkeluar_t
                            WHERE adjusmenobat_id = {$idParent}
                        ")->queryAll();

                        $detailTrans = [];
                        foreach ($dataAdjustment as $k => $v) {
                             $detailTrans[] = [
                                'obatalkes_id' => $v['obatalkes_id'],
                                'qty_satuanpakai' => $v['qty_konversi'],
                                'satuankecil_id' => isset($v['satuankecil_id']) ? $v['satuankecil_id'] : null,
                                'adjusmenobatkeluar_id' => $v['adjusmenobatkeluar_id'],
                            ];
                        }

                        // Execute By Condition
                        if ($currentMetode === BLStokObatAlkes::FEFO) {
                           $methode = BLStokObatAlkes::methodeFEFO($detailTrans,$tgl_adjusmen);
                        } else {
                           $methode = BLStokObatAlkes::methodeFIFO($detailTrans,$tgl_adjusmen);
                        }
                    }
                    $transaction->commit();

                    $adjusmen = AdjusmenObat::find()
                        ->select(['MAX(adjusmenobat_id)'])
                        ->scalar();

                    $adjusmen = AdjusmenObat::find()
                        ->select(['no_adjusmen', "adjusmenobat_id"])
                        ->where(['adjusmenobat_id' => $adjusmen])
                        ->one();

                    \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');

                    IntegrasiAkunting::integratePenerimaanSupplier($adjusmen->no_adjusmen, DocoConstants::ADJ.DocoConstants::JENIS_OBAT);

                    $response = [
                        'text' => 'Adjustment Obat Alkes berhasil disimpan',
                        'title' => 'Proses berhasil !',
                        'no_adjusmen' => $adjusmen->no_adjusmen,
                        'id_adjustment' => DocoHelpers::encrypt($adjusmen->adjusmenobat_id),
                    ];

                    return $response;
                }
            }
            else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            $this->logError($e);
            return ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()];
        }
    }

    public function actionCekStok($ruangan_id, $obatalkes_id)
    {
        $query = KetersediaanObatView::find()
            ->where([
                'ruangan_id' => $ruangan_id,
                'obatalkes_id' => $obatalkes_id
            ])->one();

        $qty_sisa = ($query) ? $query->qty_tersedia : 0;

        return $qty_sisa;
    }


     /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tgl_adjusmen# => tanggal adjustment
    * @attribute #no_adjusmen# => nomor adjustment
    * @attribute #title# => title adjustment
    * @attribute #pegawai_mengetahui# => pegawai mengetahui
    * @attribute #pegawai_menyetujui# => pegawai menyetujui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $no_adjusmen = $request->get('no_adjusmen');
        $tipe = $request->get('tipe');

        $jenis = ($tipe == 0) ? 'Masuk' : 'Keluar';
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::findOne($ruangan_id);
        $ruangan_nama = ($ruangan) ? $ruangan->ruangan_nama : '';
        $title = 'Adjustment Obat Alkes '.$jenis.' '.$ruangan_nama;
        $model = new AdjusmenObat;

        $header = $model::find()->where(['no_adjusmen' => $no_adjusmen])->one();

        $pegawai_mengetahui = Pegawai::findOne($header->peg_mengetahui_id);
        $pegawai_menyetujui = Pegawai::findOne($header->peg_menyetujui_id);
        $query = $this->getDetail($tipe, $header->adjusmenobat_id);

        $print = new DocoPrint();
        $print->attributes = [
            '#no_adjusmen#' => $no_adjusmen,
            '#tgl_adjusmen#' => date('d M Y', strtotime($header->tgl_adjusmen)),
            '#title#' => $title,
            '#pegawai_mengetahui#' => ($pegawai_mengetahui) ? $pegawai_mengetahui->nama_pegawai : '',
            '#pegawai_menyetujui#' => ($pegawai_menyetujui) ? $pegawai_menyetujui->nama_pegawai : '',
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
                'tipe' => $tipe,
            ]),
        ];

        $print->Output();
    }

    private function getDetail($tipe, $adjusmenobat_id)
    {
        if($tipe == 0) {
            $query = AdjusmenObatMasuk::find()
                ->select(['obatalkes_m.obatalkes_nama', 'obatalkes_m.obatalkes_kode', 'adjusmenobatmasuk_t.qty',
                    'besar.satuanunit_nama as besar', 'kecil.satuanunit_nama as kecil',
                    'adjusmenobatmasuk_t.tgl_kadaluarsa', 'adjusmenobatmasuk_t.harga_netto',
                    'konversi.nilai_konversi', 'adjusmenobatmasuk_t.no_batch', 'adjusmenobatmasuk_t.keterangan'])
                ->leftJoin('satuankonversi_m konversi', 'konversi.satuankonversi_id = adjusmenobatmasuk_t.satuankonversi_id')
                ->leftJoin('satuanunit_m besar', 'besar.satuanunit_id = konversi.satuanbesar_id')
                ->leftJoin('satuanunit_m kecil', 'kecil.satuanunit_id = konversi.satuankecil_id')
                ->leftJoin('obatalkes_m', 'obatalkes_m.obatalkes_id = adjusmenobatmasuk_t.obatalkes_id')
                ->where(['adjusmenobat_id' => $adjusmenobat_id]);
        }
        else {
            $query = AdjusmenObatKeluar::find()
                ->select(['obatalkes_m.obatalkes_nama', 'obatalkes_m.obatalkes_kode', 'adjusmenobatkeluar_t.qty',
                    'besar.satuanunit_nama as besar', 'kecil.satuanunit_nama as kecil',
                    'adjusmenobatkeluar_t.alasan', 'konversi.nilai_konversi', 'adjusmenobatkeluar_t.no_batch', 'adjusmenobatkeluar_t.keterangan'])
                ->innerJoin('satuankonversi_m konversi', 'konversi.satuankonversi_id = adjusmenobatkeluar_t.satuankonversi_id')
                ->innerJoin('satuanunit_m besar', 'besar.satuanunit_id = konversi.satuanbesar_id')
                ->innerJoin('satuanunit_m kecil', 'kecil.satuanunit_id = konversi.satuankecil_id')
                ->innerJoin('obatalkes_m', 'obatalkes_m.obatalkes_id = adjusmenobatkeluar_t.obatalkes_id')
                ->where(['adjusmenobatkeluar_t.adjusmenobat_id' => $adjusmenobat_id]);
        }

        return $query;
    }

    public function actionGenerateApi($satuankonversi_id = null)
    {
        $satuan_konversi = SatuanKonversi::findOne($satuankonversi_id);

        $satuanbesar_id = '';
        $satuankecil_id = '';
        $satuanBesar = '';
        $satuanKecil = '';
        $nilai_konversi = '';

        if($satuan_konversi) {
            $satuanbesar_id = $satuan_konversi->satuanbesar_id;
            $satuankecil_id = $satuan_konversi->satuankecil_id;
            $satuanBesar = SatuanUnit::findOne($satuan_konversi->satuanbesar_id);
            $satuanBesar = $satuanBesar->satuanunit_nama;
            $satuanKecil = SatuanUnit::findOne($satuan_konversi->satuankecil_id);
            $satuanKecil = $satuanKecil->satuanunit_nama;
            $nilai_konversi = $satuan_konversi->nilai_konversi;
        }

        return [
            'satuanbesar_id' => $satuanbesar_id,
            'satuankecil_id' => $satuankecil_id,
            'satuanunit_nama_besar' => $satuanBesar,
            'satuanunit_nama_kecil' => $satuanKecil,
            'nilai_konversi' => $nilai_konversi,
        ];
    }

    public function actionGetAlertHargaObat($id)
    {
        $connection = Yii::$app->db;

        $list_obat = $connection->createCommand("
            SELECT m.obatalkes_nama, m.obatalkes_id, m.harga_sugesstion, m.harganetto_ygdipakai, (adj.harga_netto / adj.qty_konversi) as harga_transaksi, m.satuankecil_nama 
            FROM obatalkes_v m
            JOIN obatalkes_v s ON m.obatalkes_id = s.obatalkes_id
            JOIN adjusmenobatmasuk_t adj on adj.obatalkes_id = m.obatalkes_id 
            WHERE m.harganetto_ygdipakai <> s.harga_sugesstion
            AND adj.adjusmenobat_id = {$id}
            ;
        ")->queryAll();

        return $list_obat;
    }

    public function actionSaveUpdateHarga()
    {
        $request = Yii::$app->request;
        $data["list_obat"] = json_decode($request->post()["toPost"], true);
        $transaksi = "adjustment";

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
                "text" => "Proses penyimpanan harga gagal"
            ];
        }
    }
}
