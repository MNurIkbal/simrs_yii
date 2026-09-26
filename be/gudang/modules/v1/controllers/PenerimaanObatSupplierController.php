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
use app\modules\v1\models\PenerimaanSupplier;
use app\modules\v1\models\PenerimaanSupplierDetail;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Payterm;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\InfoPenerimaanSuplier;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\traits\PenerimaanBarangObatTrait;
// use app\modules\v1\businessLogic\StokObatAlkes as BLStokObatAlkes;
use SirsCore\businessLogic\StokObatAlkes as BLStokObatAlkes;
use app\modules\v1\businessLogic\UpdateHargaNetto;
use SirsCore\features\IntegrasiAkunting;
use Doco\Repositories\KonfigRepositories;

class PenerimaanObatSupplierController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PenerimaanSupplier';

    const FEFO = 'FEFO';
    const FIFO = 'FIFO';
    use PenerimaanBarangObatTrait;

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

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $konfigFarmasi = KonfigRepositories::getKonfigFarmasi();
        $model = new PenerimaanSupplier;
        try {
            $dataJson = $request->post('data',"{}");
            $data = json_decode($dataJson,true);
            $postPenerimaan = isset($post['post']['PenerimaanSupplierForm']) ? $post['post']['PenerimaanSupplierForm'] : [];
            $model->attributes = $postPenerimaan;
            if(date('Y-m-d',strtotime($postPenerimaan['tgl_penerimaan'])) == date('Y-m-d')){
                $model->tgl_penerimaan = date('Y-m-d H:i:s');
            }else{
                $model->tgl_penerimaan = date('Y-m-d H:i:s',strtotime($postPenerimaan['tgl_penerimaan']));
            }
            $model->ruanganpenerima_id = Yii::$app->jwt->ruangan_id;
            $model->is_verifikasi = isset($konfigFarmasi['is_verifpenerimaan']) ? !$konfigFarmasi['is_verifpenerimaan'] : false;
            $model->tgl_verifikasi = !$konfigFarmasi['is_verifpenerimaan'] ? date('Y-m-d') : null;
            $model->is_donasi = $model->is_donasi ? true : false;
            $cekNomorFaktur = $this->cekNomorFaktur($model->no_faktur, $model->supplier_id);

            $pajak_persen = Pajak::find()->select(['pajak_persen'])->where(['pajak_id' => $model->pajak_id])->asArray()->one();
            $pajak_persen = current($pajak_persen);

            if($cekNomorFaktur > 0) {
                return [
                    'text' => 'Maaf, Nomor Faktur '.$model->no_faktur.' sudah ada.',
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'no_penerimaan' => null
                ];
            }

            if($model->validate() && $model->save()) {
                if(!empty($data)) {
                    $dataInsert = [];
                    $dataInsertStok = [];
                    $dataKonversi = [];
                    $idParent = $model->penerimaansupp_id;
                    $harga_netto_satuan = 0;
                    foreach ($data as $key => $value) {
                        $total_konversi = $value['qty_besar'] * $value['nilai_konversi'];
                        $total_harga_netto = DocoHelpers::formatDatabaseNumber($value['harga_netto']);
                        $harga_netto_satuan = floatval($total_harga_netto) / floatval($value['qty_kecil']);
                        $dataInsert[] = [
                            'penerimaansupp_id' => $idParent,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'tgl_kadaluarsa' => date('Y-m-d', strtotime($value['tgl_kadaluarsa'])),
                            'satuanbesar_id' => $value['satuanbesar_id'],
                            'satuankecil_id' => $value['satuankecil_id'],
                            'qty_besar' => $value['qty_besar'],
                            'qty_kecil' => $value['qty_kecil'],
                            'harga_netto' => $total_harga_netto,
                            'harga_netto_satuan' => $harga_netto_satuan,
                            'diskon' => DocoHelpers::formatDatabaseNumber($value['diskon']),
                            'satuankonversi_id' => $value['satuankonversi_id'],
                            'no_batch' => $value['no_batch'],
                            'keterangan' => $value['keterangan'],
                            'is_donasi' => $value['is_donasi'] ? true : false,
                        ];

                        $data[$key]['harganetto'] = $total_harga_netto;
                    }

                    PenerimaanSupplierDetail::batchInsert($dataInsert, false);
                    $dataPenerimaan = $connection->createCommand("
                        SELECT * FROM penerimaansuppdetail_t
                        WHERE penerimaansupp_id = {$idParent}
                    ")->queryAll();

                    /**
                    * Find harga netto average from master obat alkes
                    * 6 Juli 2020
                    */
                    $dataMasterObatAlkes=[];
                    if(is_array($data) && count($data)>0){
                        $inCondition = "(" . implode(",", array_column($data, 'obatalkes_id')) . ")";
                        $queryMasterObatAlkes = "SELECT obatalkes_id,hargaratarata FROM obatalkes_m WHERE obatalkes_id IN {$inCondition}";
                        $dataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
                    }
                    if(is_array($dataMasterObatAlkes) && count($dataMasterObatAlkes)>0) $dataMasterObatAlkes = array_column($dataMasterObatAlkes, 'hargaratarata','obatalkes_id');

                    if (isset($konfigFarmasi['is_verifpenerimaan'])
                        && !$konfigFarmasi['is_verifpenerimaan']) {
                        foreach ($dataPenerimaan as $k => $v) {
                            $hn_diskon = 0;
                            if($konfigFarmasi['use_discount']) {
                                $hn_diskon = $v['harga_netto_satuan'] * ($v['diskon'] / 100);
                            }

                            $pajak = 0;
                            if($konfigFarmasi['use_ppn']) {
                                $pajak = ($v['harga_netto_satuan'] - $hn_diskon) * ($pajak_persen / 100);
                            }

                            $dataInsertStok[] = [
                                'tglstok_in' => date('Y-m-d',strtotime($postPenerimaan['tgl_penerimaan'])),
                                'stokoa_aktif' => true,
                                'ruangan_id' => Yii::$app->jwt->ruangan_id,
                                'obatalkes_id' => $v['obatalkes_id'],
                                'tglkadaluarsa' => $v['tgl_kadaluarsa'],
                                'qtystok_in' => $v['qty_kecil'],
                                'qtystok_out' => 0,
                                'persendiscount' => $v['diskon'],
                                'jmldiscount' => $hn_diskon,
                                'persenppn' => $pajak_persen,
                                'jmlppn' => $pajak,
                                'persenpph' => 0,
                                'persenmargin' => 0,
                                'jmlmargin' => 0,
                                'harganetto' => $v['harga_netto_satuan'],
                                'total_harganetto' => $v['harga_netto'],
                                'satuankecil_id' => $v['satuankecil_id'],
                                'penerimaansuppdetail_id' => $v['penerimaansuppdetail_id'],
                                'penerimaansupp_id' => $idParent,
                                'harga_netto_avg' => isset($dataMasterObatAlkes[$v['obatalkes_id']]) ? $dataMasterObatAlkes[$v['obatalkes_id']] : 0
                            ];
                        }

                        StokObatAlkes::batchInsert($dataInsertStok, false);
                        if(isset($konfigFarmasi['hargaygdigunakan']) && $konfigFarmasi['hargaygdigunakan'] == DocoConstants::HARGA_FIX_RATE) {
                            Yii::$app->runAction(
                                'v1/allow/update-base-price',
                                [
                                    'transaksi_id' => $idParent,
                                    'tipe' => 'PEN_SUPP',
                                    'detail' => json_encode($dataInsertStok)
                                ]
                            );
                        }
                    }

                    $transaction->commit();
                    $penerimaan = PenerimaanSupplier::find()
                        ->select(['no_penerimaan', "penerimaansupp_id"])
                        ->where(['penerimaansupp_id' => $idParent])
                        ->one();

                    IntegrasiAkunting::integratePenerimaanSupplier($penerimaan->no_penerimaan, DocoConstants::JENIS_OBAT);

                    $response = [
                        'text' => 'Penerimaan Obat Alkes berhasil disimpan',
                        'title' => 'Proses berhasil !',
                        'no_penerimaan' => $penerimaan->no_penerimaan,
                        'id_transaksi' => DocoHelpers::encrypt($penerimaan->penerimaansupp_id)
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
            return ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }
    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tgl_penerimaan# => tanggal adjustment
    * @attribute #no_penerimaan# => nomor adjustment
    * @attribute #title# => title adjustment
    * @attribute #pegawai_mengetahui# => pegawai mengetahui
    * @attribute #pegawai_menyetujui# => pegawai menyetujui
    * @attribute #nama_supplier# => pegawai menyetujui
    * @attribute #nomor_faktur# => pegawai menyetujui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $no_penerimaan = $request->get('no_penerimaan');
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $header = InfoPenerimaanSuplier::find()->where(['no_penerimaan' => $no_penerimaan])->one();
        $query = $this->getDetail($header->penerimaansupp_id);
        $ruangan_nama = !empty($header->ruangan_nama) ? $header->ruangan_nama : null;
        $title = 'Penerimaan Obat Alkes Supplier ' . $ruangan_nama;

        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            '#no_penerimaan#' => $no_penerimaan,
            '#ruangan_nama#' => $ruangan_nama,
            '#nomor_faktur#' => !empty($header->no_faktur) ? $header->no_faktur : null,
            '#tgl_penerimaan#' => !empty($header->tgl_penerimaan) ? date('d M Y', strtotime($header->tgl_penerimaan)) : null,
            '#nama_supplier#' => !empty($header->supplier_nama) ? $header->supplier_nama : null ,
            '#pegawai_mengetahui#' => !empty($header->peg_menyetujui_nama) ? $header->peg_menyetujui_nama : '',
            '#pegawai_menyetujui#' => !empty($header->peg_mengetahui_nama) ? $header->peg_mengetahui_nama : '',
            '#pajak#' => !empty($header->pajak_label) ? $header->pajak_label : null,
            '#payterm#' => !empty($header->payterm_nama) ? $header->payterm_nama : null,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];

        $print->Output();
    }

    private function getDetail($penerimaansupp_id)
    {
        $query = PenerimaanSupplierDetail::find()
                ->select(['obatalkes_m.obatalkes_nama',
                    'penerimaansuppdetail_t.qty_besar',
                    'penerimaansuppdetail_t.qty_kecil',
                    'besar.satuanunit_nama as besar',
                    'kecil.satuanunit_nama as kecil',
                    'penerimaansuppdetail_t.tgl_kadaluarsa',
                    'penerimaansuppdetail_t.harga_netto',
                    'penerimaansuppdetail_t.no_batch',
                    'penerimaansuppdetail_t.keterangan',
                    'penerimaansuppdetail_t.harga_netto_satuan',
                    'penerimaansuppdetail_t.ppn', 'penerimaansuppdetail_t.diskon'])
                ->innerJoin('satuankonversi_m konversi', 'konversi.satuankonversi_id = penerimaansuppdetail_t.satuankonversi_id')
                ->innerJoin('satuanunit_m besar', 'besar.satuanunit_id = konversi.satuanbesar_id')
                ->innerJoin('satuanunit_m kecil', 'kecil.satuanunit_id = konversi.satuankecil_id')
                ->leftJoin('obatalkes_m', 'obatalkes_m.obatalkes_id = penerimaansuppdetail_t.obatalkes_id')
                ->where(['penerimaansupp_id' => $penerimaansupp_id]);

        return $query;
    }

    private function cekNomorFaktur($no_faktur, $supplier_id)
    {
        $query = PenerimaanSupplier::find()
            ->where(['no_faktur' => $no_faktur, 'supplier_id' => $supplier_id])->count();

        return $query;
    }

    public function actionGetAlertHargaObat($id)
    {
        $connection = Yii::$app->db;

        $list_obat = $connection->createCommand("
            SELECT m.obatalkes_nama, m.obatalkes_id, m.harganetto_ygdipakai, s.harga_sugesstion, (pen.harga_netto / pen.qty_kecil) as harga_transaksi, m.satuankecil_nama
            FROM obatalkes_v m
              JOIN obatalkes_v s ON m.obatalkes_id = s.obatalkes_id
              join penerimaansuppdetail_t pen on pen.obatalkes_id = m.obatalkes_id
            WHERE m.harganetto_ygdipakai <> s.harga_sugesstion
              AND pen.penerimaansupp_id = {$id};
            ;
        ")->queryAll();

        return $list_obat;
    }

    public function actionSaveUpdateHarga()
    {
        $request = Yii::$app->request;
        $data["list_obat"] = json_decode($request->post()["toPost"], true);
        $transaksi = "penerimaan_manual";

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

    public function getKonfigFarmasi()
    {
        try {
            $konfig_farmasi = KonfigFarmasi::find()->asArray()->one();
            return $konfig_farmasi;
        } catch (\Exception $e) {
            return [];
        }
    }

}
