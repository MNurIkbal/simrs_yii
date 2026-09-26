<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\InfoPenerimaanBarangManualView;
use app\modules\v1\models\InfoPenerimaanBarangDetailManualView;
use app\modules\v1\models\InfoReturPenerimaanBarang;
use app\modules\v1\models\InfoReturPenerimaanBarangDetail;
use app\modules\v1\models\PenerimaanSupplier;
use app\modules\v1\models\PenerimaanSupplierDetail;
use app\modules\v1\models\ReturPenerimaanBarang;
use app\modules\v1\models\ReturPenerimaanBarangDetail;
use app\modules\v1\models\StokBarang;
use app\modules\v1\models\SatuanKonversiBarang;
use app\modules\v1\models\InfoStokBarang;
use app\modules\v1\businessLogic\StokBarang as LogicStokBarang;
use app\modules\v1\businessLogic\ReturPenerimaan as LogicRetur;
use app\modules\v1\payload\PenerimaanSupplierDetailPayload;
use app\modules\v1\traits\PenerimaanBarangObatTrait;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\data\ActiveDataProvider;
use Yii;

class PenerimaanBarangManualController extends DocoActiveController
{
    use PenerimaanBarangObatTrait;
    public $modelClass = '';
    public $helper;

    /**
     * Init controller
     * @author Tsani Nashrullah
     **/
    public function init()
    {
        parent::init();
        $this->helper = new DocoHelpers;
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

        $model = new InfoPenerimaanBarangManualView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_penerimaan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_penerimaan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_penerimaan']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
        /**
         * End Special Condition date range
        **/
        
        // return [$start, $end];

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDetail($id)
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPenerimaanBarangDetailManualView;
            $query = $model::find();
            $query->where([
                'penerimaansupp_id' => $id
            ]);

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

    /**
     * Store data penerimaan barang manual to database
     *
     * @param Array $payload
     * @return JSON
     * @author Tsani Nashrullah
     **/
    public function actionStore()
    {
        $payload = Yii::$app->request->post();
        $headerModel = new PenerimaanSupplier;
        $headerModel->attributes = $payload['PenerimaanSupplier'];
        $headerModel->is_tipe = 1;
        $headerModel->ruanganpenerima_id = Yii::$app->jwt->ruangan_id;
        $headerModel->is_donasi = $headerModel->is_donasi ? true : false;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($headerModel->validate() && $headerModel->save()) {
                if (isset($payload['PenerimaanSupplierDetail']) && is_array($payload['PenerimaanSupplierDetail']) && !empty($payload['PenerimaanSupplierDetail'])) {
                    $arrayInsert = [];
                    $arrayDetail = array_values($payload['PenerimaanSupplierDetail']);
                    $harga_netto_satuan = 0;
                    for ($i = 0; $i < count($arrayDetail); $i++) {
                        $detailModel = new PenerimaanSupplierDetailPayload;
                        $detailModel->attributes = $arrayDetail[$i];
                        $detailModel->diskon = empty($detailModel->diskon) ? 0 : $detailModel->diskon;
                        $detailModel->scenario = 'barang';
                        if ($detailModel->validate()) {
                            $eachArray = $detailModel->attributes;
                            $total_harga_netto = str_replace(',','.',str_replace('.', '',$detailModel->harga_netto));
                            $harga_netto_satuan = floatval($total_harga_netto) / floatval($detailModel->qty_kecil);
                            $arrayInsert[] = array_merge($detailModel->attributes, [
                                'tgl_kadaluarsa' => !empty($detailModel['tgl_kadaluarsa']) ? date('Y-m-d', strtotime($detailModel['tgl_kadaluarsa'])) : null,
                                'penerimaansupp_id' => $headerModel->penerimaansupp_id,
                                'harga_netto_satuan' => @$harga_netto_satuan
                            ]);
                        } else {
                            $transaction->rollBack();
                            \Yii::$app->response->statusCode = 400;
                            return [
                                'status' => 400,
                                'message' => 'Terjadi kesalahan pada input, ' . $this->helper->mapMessageErrorValidation($detailModel->errors) . ' pada baris ke-' . ($i + 1),
                            ];
                        }
                    }
                    PenerimaanSupplierDetail::batchInsert($arrayInsert, false);
                    // get data penerimaan supplier detail because need penerimaansuppdetail_id to insert table stokbarang_t
                    $dataPenerimaanSupplierDetail = PenerimaanSupplierDetail::find()
                        ->select([
                            'barang_id',
                            'tgl_kadaluarsa',
                            'qty_kecil',
                            'harga_netto',
                            'satuankecil_id',
                            'no_batch',
                            'penerimaansuppdetail_id',
                        ])
                        ->andWhere([
                            'penerimaansupp_id' => $headerModel->penerimaansupp_id,
                        ])
                        ->asArray()
                        ->all();

                    /* pindah ke verifikasi
                    $arrayInsertStok = [];
                    foreach ($dataPenerimaanSupplierDetail as $valueDetail) {
                        $arrayInsertStok[] = [
                            'tglstok_in' => date('Y-m-d H:i:s'),
                            'stokbarang_aktif' => true,
                            'ruangan_id' => Yii::$app->jwt->ruangan_id,
                            'barang_id' => $valueDetail['barang_id'],
                            'tglkadaluarsa' => $valueDetail['tgl_kadaluarsa'],
                            'no_batch' => $valueDetail['no_batch'],
                            'qtystok_in' => $valueDetail['qty_kecil'],
                            'qtystok_out' => 0,
                            'persendiscount' => 0,
                            'jmldiscount' => 0,
                            'persenppn' => 0,
                            'persenpph' => 0,
                            'persenmargin' => 0,
                            'jmlmargin' => 0,
                            'harganetto' => $valueDetail['harga_netto'],
                            'satuankecil_id' => $valueDetail['satuankecil_id'],
                            'penerimaansuppdetail_id' => $valueDetail['penerimaansuppdetail_id'],
                        ];
                    }
                    StokBarang::batchInsert($arrayInsertStok, true);
                    */

                    $transaction->commit();
                    $penerimaan = PenerimaanSupplier::find()
                        ->select(['no_penerimaan', "penerimaansupp_id"])
                        ->where(['penerimaansupp_id' => $headerModel->penerimaansupp_id])
                        ->one();

                    $response = [
                        'text' => 'Penerimaan Barang berhasil disimpan',
                        'title' => 'Proses berhasil !',
                        'no_penerimaan' => $penerimaan->no_penerimaan,
                        'id_transaksi' => DocoHelpers::encrypt($penerimaan->penerimaansupp_id),
                    ];

                    return $response;
                } else {
                    \Yii::$app->response->statusCode = 400;
                    return [
                        'status' => 400,
                        'message' => 'Mohon inputkan detail penerimaan barang.',
                    ];
                }
            } else {
                \Yii::$app->response->statusCode = 400;
                return [
                    'status' => 400,
                    'message' => $this->helper->mapMessageErrorValidation($headerModel->errors),
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->helper->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan pada server.'];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->helper->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan pada server.'];
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
     * @attribute #nama_supplier# => nama supplier
     * @attribute #nomor_faktur# => nomor faktur
     * @attribute #no_suratjalan# => nomor surat jalan
     * @attribute #pajak_label# => pajak label
     * @attribute #payterm_nama# => payterm nama
     */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $noPenerimaan = $request->get('noPenerimaan',null);
        if(!empty($noPenerimaan)){
            $headerData = InfoPenerimaanBarangManualView::find()
                ->select([
                    'penerimaansupp_id',
                    'no_faktur',
                    'tgl_penerimaan',
                    'no_penerimaan',
                    'tgl_penerimaan',
                    'supplier_nama',
                    'peg_mengetahui_nama',
                    'peg_menyetujui_nama',
                    'no_suratjalan',
                    'pajak_label',
                    'payterm_nama'
                ])
                ->where(['no_penerimaan' => $noPenerimaan])
                ->asArray()
                ->one();
        }else{
            $penerimaansupp_id = $request->get('id');
            $headerData = InfoPenerimaanBarangManualView::find()
                ->select([
                    'penerimaansupp_id',
                    'no_faktur',
                    'tgl_penerimaan',
                    'no_penerimaan',
                    'tgl_penerimaan',
                    'supplier_nama',
                    'peg_mengetahui_nama',
                    'peg_menyetujui_nama',
                    'no_suratjalan',
                    'pajak_label',
                    'payterm_nama'
                ])
                ->where(['penerimaansupp_id' => $penerimaansupp_id])
                ->asArray()
                ->one();
        }
        
        $ruangan_nama = !empty($headerData['ruangan_nama']) ? $headerData['ruangan_nama'] : null;
        $title = 'Penerimaan Barang Manual ' . $ruangan_nama;

        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            '#no_penerimaan#' => !empty($headerData['no_penerimaan']) ? $headerData['no_penerimaan'] : null,
            '#ruangan_nama#' => $ruangan_nama,
            '#nomor_faktur#' => !empty($headerData['no_faktur']) ? $headerData['no_faktur'] : null,
            '#no_suratjalan#' => !empty($headerData['no_suratjalan']) ? $headerData['no_suratjalan'] : null,
            '#tgl_penerimaan#' => !empty($headerData['tgl_penerimaan']) ? $this->helper->convertDate($headerData['tgl_penerimaan'], 'd-m-Y') : null,
            '#nama_supplier#' => !empty($headerData['supplier_nama']) ? $headerData['supplier_nama'] : null,
            '#pegawai_mengetahui#' => !empty($headerData['peg_mengetahui_nama']) ? $headerData['peg_mengetahui_nama'] : '',
            '#pegawai_menyetujui#' => !empty($headerData['peg_menyetujui_nama']) ? $headerData['peg_menyetujui_nama'] : '',
            '#pajak_label#' => !empty($headerData['pajak_label']) ? $headerData['pajak_label'] : null,
            '#payterm_nama#' => !empty($headerData['payterm_nama']) ? $headerData['payterm_nama'] : null,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => InfoPenerimaanBarangDetailManualView::find()
                    ->select([
                        'barang_nama',
                        'qty_besar',
                        'satuan_besar as besar',
                        'qty_kecil',
                        'satuan_kecil as kecil',
                        'tgl_kadaluarsa',
                        'harga_netto',
                        'diskon',
                        'no_batch',
                        'keterangan',
                    ])
                    ->andWhere([
                        'penerimaansupp_id' => $headerData['penerimaansupp_id']
                    ])
                    ->asArray()
                    ->all(),
            ]),
        ];

        $print->Output();
    }

    /**
    * @controller actionPrintGrn
    * @attribute #dataTable# => Menampilkan Data cetak GRN
    * @attribute #no_penerimaan# => Menampilkan Nomor Penerimaan
    * @attribute #jenis_penerimaan# => Menampilkan Jenis Penerimaan
    * @attribute #no_faktur# => Menampilkan Nomor Faktur
    * @attribute #no_po# => Menampilkan Nomor PO
    * @attribute #no_sj# => Menampilkan Nomor Surat Jalan
    * @attribute #tgl_sj# => Menampilkan Tanggal Surat Jalan
    * @attribute #tgl_penerimaan# => Menampilkan Tanggal Penerimaan
    * @attribute #nama_supplier# => Menampilkan Nama Supplier
    * @attribute #mengetahui# => Menampilkan Nama Pegawai Mengetahui
    * @attribute #menyetujui# => Menampilkan Nama Pegawai Menyetujui
    * @attribute #menerima# => Menampilkan Nama Pegawai Menerima
    * @attribute #catatan# => Menampilkan Catatan
    * @attribute #alamat_supplier# => Menampilkan alamat supplier
    * @attribute #no_telp# => Menampilkan alamat no telp
    * @attribute #no_fax# => Menampilkan alamat no fax
    **/

    public function actionPrintGrn() {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $type = $request->get('type');
            $data_detail = self::getDataDetailPenerimaan($id, $type);

            // return $data_detail;
            // $no_po = isset($data_detail["header"]["no_poobat"]) ? $data_detail["header"]["no_poobat"] : $data_detail["header"]["no_pobarang"];

            $jenisPenerimaan = '-';
            if($data_detail['header']['is_consigment'] && $data_detail['header']['is_donasi']){
                $jenisPenerimaan = 'Consignment dan Donasi';
            }else if($data_detail['header']['is_consigment']){
                $jenisPenerimaan = 'Consignment';
            }else if($data_detail['header']['is_donasi']){
                $jenisPenerimaan = 'Donasi';
            }

            $subTotal = 0;
            $totalDiscount = 0;
            foreach ($data_detail["detail"] as $value) :
                $sum_qty_diterima = array_sum(array_column($value, "qty_diterima"));
                $subTotal += ($sum_qty_diterima * $value[0]['harga_input_satuan']);
                if ($value[0]['discount'] > 0) {
                    $totalDiscount += ($value[0]['harga_input_satuan'] * $sum_qty_diterima) * $value[0]['discount']/100;
                }
            endforeach;

            $subTotalDiscount = $subTotal - $totalDiscount;
            if(isset($data_detail['detail_penerimaan'][0])){
                $hargaPPN = $subTotalDiscount * $data_detail['detail_penerimaan'][0]['ppn']/100;
            }else{
                $hargaPPN = 0;
            }
            $totalNet = $subTotalDiscount + $hargaPPN;

            $print = new DocoPrint;
            $print->attributes = [
                '#dataTable#' => $this->renderPartial('cetak_grn',[
                    'data'=> $data_detail["detail"],
                    'pajak_persen'=> 0,
                    'subTotal' => @$subTotal,
                    'totalDiscount' => @$totalDiscount,
                    'subTotalDiscount' => @$subTotalDiscount,
                    'hargaPPN' => @$hargaPPN,
                    'totalNet' => @$totalNet,
                ]),
                '#nama_supplier#' => $data_detail["header"]["supplier_nama"] ,
                '#alamat_supplier#' => $data_detail['header']['supplier_alamat'],
                '#no_telp#' => $data_detail['header']['no_tlp'],
                '#no_fax#' => !empty($data_detail['header']['no_fax']) ? $data_detail['header']['no_fax'] : '-',
                '#no_po#' => '',
                '#no_faktur#' => $data_detail["header"]["no_faktur"] ,
                '#no_penerimaan#' => $data_detail["header"]["no_penerimaan"] ,
                '#jenis_penerimaan#' => $jenisPenerimaan,
                '#no_sj#' =>  '',
                '#tgl_sj#' =>  '',
                '#tgl_penerimaan#' => date("d-M-Y", strtotime($data_detail["header"]["tgl_penerimaan"])) ,
                '#mengetahui#' => !empty($data_detail["header"]["peg_created"]) ? $data_detail["header"]["peg_mengetahui_nama"] : '...' ,
                '#menyetujui#' => !empty($data_detail["header"]["peg_created"]) ? $data_detail["header"]["peg_menyetujui_nama"] : '...' ,
                '#menerima#' => !empty($data_detail["header"]["peg_created"]) ? $data_detail["header"]["peg_created"] : '...' ,
                '#catatan#' => !empty($data_detail["header"]["catatan"]) ? $data_detail["header"]["catatan"] : "-",
            ];

            $print->Output();

        }catch (\Yii\db\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionView($id)
    {
        $data = InfoPenerimaanBarangManualView::find()->where([
            'penerimaansupp_id' => $id
        ])->one();

        return [
            'data' => $data
        ];
    }

    public function actionGetDetailRetur($id)
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPenerimaanBarangDetailManualView;
            $query = $model::find();
            $query->where([
                'penerimaansupp_id' => $id
            ]);

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

    public function actionSaveRetur($id)
    {
        $request = Yii::$app->request;
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $jwt = Yii::$app->jwt;
        try {
            $model = new ReturPenerimaanBarang;
            $tanggalRetur = !empty($request->post('tanggal_retur')) 
                    ? date('Y-m-d H:i:s', strtotime($request->post('tanggal_retur'))) : null;
            $dataRetur = $request->post('data_retur');
            $model->penerimaansupp_id = $id;
            $model->tgl_retur = $tanggalRetur;
            $model->pegawairetur_id = $request->post('pegawai_retur');
            $model->alasan_retur = $request->post('alasan_retur');
            $model->ruanganretur_id = $jwt->ruangan_id;
            if ($model->validate() && $model->save()) {
                $idParent = $model->returpenerimaanbarang_id;
                $instDetail = [];
                $dataValid = [];
                $jsonToArray = json_decode($dataRetur,true);
                if (is_array($jsonToArray)) {
                    foreach ($jsonToArray as $id_penerimaan => $value) {
                        $idPenerimaan = DocoHelpers::decrypt($id_penerimaan);
                        $barangId  = $value['barang_id'];
                        // $dataValid[$idPenerimaan] = $value;
                        if (isset($dataValid[$barangId]['qty_retur'])) {
                            $dataValid[$barangId]['qty_retur'] += $value['qty_retur'];
                        } else {
                            $dataValid[$barangId] = $value;
                        }
                        $instDetail[] = [
                            'returpenerimaanbarang_id' => $idParent,
                            'barang_id' => $barangId,
                            'satuanbesar_id' => $value['satuanbesar_id'],
                            'tgl_kadaluarsa' => $value['tglkadaluarsa'],
                            'qty_retur' => $value['qty_retur'],
                            'penerimaansuppbrgdetail_id' => $idPenerimaan
                        ];
                    }

                    if ($instDetail) {
                        ReturPenerimaanBarangDetail::batchInsert($instDetail,false);
                    }
                    $listDataRetur = ReturPenerimaanBarangDetail::find()->select([
                        'barang_id',
                        'returpenerimaanbarangdetail_id',
                        'penerimaansuppbrgdetail_id'
                    ])->where([
                        'returpenerimaanbarang_id' => $idParent
                    ])->asArray()->all();

                    /** Untuk mendapatkan id retur detail **/
                    $listIdDetail = $listBarang = $listPenerimaanSupp = [];
                    foreach ($listDataRetur as $value) {
                        $listIdDetail[$value['barang_id']] = $value['returpenerimaanbarangdetail_id'];
                        $listPenerimaanSupp[$value['barang_id']] = $value['penerimaansuppbrgdetail_id'];
                        $listBarang[] = $value['barang_id'];
                    }

                    $satuanKonversi = SatuanKonversiBarang::find()->where([
                        'barang_id' => $listBarang
                    ])->asArray()->all();

                    $konversi = [];
                    foreach ($satuanKonversi as $value) {
                        $konversi[$value['barang_id']][$value['satuanbesar_id']] = $value['nilai_konversi'];
                    }

                    $dateNow = date('Y-m-d');
                    $infoStok = InfoStokBarang::find()->select([
                        'barang_id',
                        'satuankecil_id',
                        'qty_stok',
                        'barang_nama'
                    ])->where([
                        'ruangan_id' => $jwt->ruangan_id,
                        'barang_id' => $listBarang
                    ])->asArray()->all();

                    $detailTrans = [];

                    foreach ($infoStok as $value) {
                        if (isset($listIdDetail[$value['barang_id']]) 
                            && isset($dataValid[$value['barang_id']])) {
                            $respDetail = $listIdDetail[$value['barang_id']];
                            $dataTrans = $dataValid[$value['barang_id']];
                            $qtyRetur = $dataTrans['qty_retur'];
                            $qtyRetur = isset($konversi[$value['barang_id']][$dataTrans['satuanbesar_id']]) 
                              ? $qtyRetur * $konversi[$value['barang_id']][$dataTrans['satuanbesar_id']] :0;

                            if ($qtyRetur > $value['qty_stok']) {
                                $transaction->rollBack();
                                return [
                                    'title' => 'Proses Gagal!',
                                    'text' => 'Stok Barang '. $value['barang_nama'] . ' tidak mencukupi.',
                                    'status' => 422
                                ];
                            }

                            if ($qtyRetur) {
                                $detailTrans[] = [
                                    'barang_id' => $value['barang_id'],
                                    'qty_satuanpakai' => $qtyRetur,
                                    'satuankecil_id' => $value['satuankecil_id'],
                                    'returbarangdetail_id' => $respDetail,
                                    'penerimaansuppdetail_id' => $listPenerimaanSupp[$value['barang_id']]
                                ];
                            } else {
                                $transaction->rollBack();
                                return [
                                    'title' => 'Proses Gagal!',
                                    'text' => 'Terjadi kesalahan pada satuan konversi',
                                    'status' => 422
                                ];
                            }
                        }
                    }

                    $tanggalBerlaku = date('Y-m-d');
                    
                    LogicRetur::manual($jwt->ruangan_id,$detailTrans);

                    /*
                    $konfig = $connection->createCommand("
                        SELECT metodeantrian FROM konfiggudang_k
                        WHERE tglberlaku >= '{$tanggalBerlaku}'
                        AND is_active = true
                    ")->queryOne();

                    $currentMetode = LogicStokBarang::FEFO;
                    if ($konfig) {
                        $currentMetode = isset($konfig['metodeantrian']) 
                                            ? strtoupper($konfig['metodeantrian']) : LogicStokBarang::FEFO;
                    }

                    $tanggalPemakaian = date('Y-m-d H:i:s');
                    if ($currentMetode === LogicStokBarang::FEFO) {
                       $methode = LogicStokBarang::methodeFEFO($detailTrans,$tanggalPemakaian);
                    } else {
                       $methode = LogicStokBarang::methodeFIFO($detailTrans,$tanggalPemakaian);
                    }
                    */

                    $transaction->commit();
                    $transRetur = ReturPenerimaanBarang::find()->select([
                        'no_returpenerimaanbarang'
                        ])->where([
                            'returpenerimaanbarang_id' => $idParent
                            ])->asArray()->one();

                    return [
                        'id_parent' => DocoHelpers::encrypt($idParent),
                        'no_retur' => $transRetur['no_returpenerimaanbarang']
                    ];
                }
            } else {
                $transaction->rollBack();
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }

    public function actionVerifikasiPenerimaan($penerimaansupp_id) {
        try {
            $penerimaansupp_id = DocoHelpers::decrypt($penerimaansupp_id);
            $dateNow = date('Y-m-d');
            $jwt = Yii::$app->jwt;

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $model = PenerimaanSupplier::findOne($penerimaansupp_id);
            $model->is_verifikasi = true;
            $model->tgl_verifikasi = $dateNow;

            if($model->save()){
                $dataPenerimaan = $connection->createCommand("
                    SELECT * FROM penerimaansuppdetail_t
                    WHERE penerimaansupp_id = {$penerimaansupp_id}
                ")->queryAll();

                /**
                * Find harga netto average from master barang
                * 6 Juli 2020
                */
                $dataMasterBarang=[];
                if(is_array($dataPenerimaan) && count($dataPenerimaan)>0){
                    $inCondition = "(" . implode(",", array_column($dataPenerimaan, 'barang_id')) . ")";
                    $queryMasterBarang = "SELECT barang_id,barang_harganetto FROM barang_m WHERE barang_id IN {$inCondition}";
                    $dataMasterBarang = Yii::$app->db->createCommand($queryMasterBarang)->queryAll();
                }
                if(is_array($dataMasterBarang) && count($dataMasterBarang)>0) $dataMasterBarang = array_column($dataMasterBarang, 'hargaratarata','barang_id');

                foreach ($dataPenerimaan as $k => $v) {
                    $dataInsertStok[] = [
                        'tglstok_in' => $dateNow,
                        'stokbarang_aktif' => true,
                        'ruangan_id' => $jwt->ruangan_id,
                        'barang_id' => $v['barang_id'],
                        'tglkadaluarsa' => $v['tgl_kadaluarsa'],
                        'qtystok_in' => $v['qty_kecil'],
                        'qtystok_out' => 0,
                        'persendiscount' => 0,
                        'jmldiscount' => 0,
                        'persenppn' => 0,
                        'persenpph' => 0,
                        'persenmargin' => 0,
                        'jmlmargin' => 0,
                        'harganetto' => $v['harga_netto'],
                        'satuankecil_id' => $v['satuankecil_id'],
                        'penerimaansuppdetail_id' => $v['penerimaansuppdetail_id'],
                        'harga_netto_avg' => isset($dataMasterBarang[$v['barang_id']]) ? $dataMasterBarang[$v['barang_id']] : 0
                    ];
                }

                StokBarang::batchInsert($dataInsertStok, false);
                $transaction->commit();

                return [
                    'status' => 200,
                    'message' => 'Berhasil',
                    'text' => 'Status verifikasi berhasil di update',
                    'penerimaansupp' => $penerimaansupp_id
                ];
            }

            return [
                'status' => 422,
                'data' => $model->errors
            ];
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

    /**
     * @controller actionPrintRetur
     * @attribute #no_retur# => Menampilkan Nama Ruangan 
     * @attribute #no_penerimaan# => Menampilkan No Penerimaan
     * @attribute #no_faktur# => Menampilkan Tanggal Penerimaan
     * @attribute #tanggal_retur# => Manampilkan Nama Supplier
     * @attribute #nama_supplier# => Menampilkan nomor faktur
     * @attribute #tabel_detail# => Menampilkan list tabel detail penerimaan
     * @attribute #alasan# => Menampilkan pegawai Megetahui
     * @attribute #pegawai_retur# => Menampilkan pegawai menyetujui
     **/

    public function actionPrintRetur($id)
    {
        $request = Yii::$app->request;
        try {
            $data = InfoReturPenerimaanBarang::find()->where([
                'returpenerimaanbarang_id' => $id
            ])->one();
            $detail = InfoReturPenerimaanBarangDetail::find()->where([
                'returpenerimaanbarang_id' => $id
            ])->asArray()->all();

            $print = new DocoPrint();
            $print->attributes = [
                '#no_retur#' => $data->no_returpenerimaanbarang,
                '#no_penerimaan#' => $data->no_penerimaan,
                '#no_faktur#' => $data->no_faktur,
                '#tanggal_retur#' => date('d-M-Y',strtotime($data->tgl_retur)),
                '#nama_supplier#' => $data->supplier_nama,
                '#tabel_detail#' => $this->renderPartial('retur',[
                    'detail' => $detail
                ]),
                '#alasan#' => $data->alasan_retur,
                '#pegawai_retur#' => $data->pegawai_retur,
            ];

            $print->Output();

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function getDataDetailPenerimaan($id, $type) {
        $model_penerimaan = new InfoPenerimaanBarangManualView;
        $detail = new InfoPenerimaanBarangDetailManualView;
        // $doc = new PenerimaanBarangDoc;
        $pk_id = 'penerimaansupp_id';
        // $validasidetail_id = 'validasipobarangdetail_id';
        $item_id = 'barang_id';
        $item_nama = 'barang_nama';
        

        $query_penerimaan = $model_penerimaan->find()->where([$pk_id => $id])->one();

        

        $detail_penerimaan = $detail->find()->where([
            $pk_id => $id
        ])->orderby([$item_nama => SORT_ASC,"tgl_kadaluarsa" => SORT_ASC])->all();

        $detail_rows = [];
        foreach ($detail_penerimaan as $row => $value) {
            $detail_rows[$value[$item_id]][] = [
                $item_id => $value[$item_id],
                $item_nama => $value[$item_nama],
                "qty_diterima" => $value["qty_besar"],
                "satuan_besar" => $value["satuan_besar"],
                "satuan_kecil" => $value["satuan_kecil"],
                "tgl_kadaluarsa" => $value["tgl_kadaluarsa"],
                "no_batch" => $value["no_batch"],
                // "keterangan" => $value["keterangan"],
                // $s_konversi_id => $value[$s_konversi_id],
                "harga_netto" => $value["harga_netto"],
                "harga_input_satuan" => $value["harga_input_satuan"],
                "discount" => $value["diskon"],
                "ppn" => $value["ppn"],
                // "discount_rp" => $value["discount_rp"],
                // "jumlah" => $value["jumlah"],
                // $validasidetail_id => $value[$validasidetail_id],
                "kode_item" => $value["barang_kode"],
                "satuanunit_nama" => $value["satuanunit_nama"],
            ];
        }


        return [
            'header' => $query_penerimaan,
            'detail' => $detail_rows,
            "detail_penerimaan" => $detail_penerimaan
        ];
    }
}
