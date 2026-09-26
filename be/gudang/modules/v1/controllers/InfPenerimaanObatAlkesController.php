<?php

/**
* @author yaya
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\Services\StockService;
use SirsCore\features\IntegrasiAkunting;

use app\modules\v1\models\PenerimaanSupplier;
use app\modules\v1\models\InfoPenerimaanSuplier;
use app\modules\v1\models\InfoPenerimaanSuplierDetail;
use app\modules\v1\models\InfoPenerimaanReturSuplierDetail;
use app\modules\v1\models\ReturPenerimaanObat;
use app\modules\v1\models\ReturPenerimaanObatDetail;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\InfoReturPenerimaanObat;
use app\modules\v1\models\InfoReturPenerimaanObatDetail;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\UploadForm;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use app\modules\v1\models\Pajak;
use Doco\Services\InternalService;

class InfPenerimaanObatAlkesController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPenerimaanSuplier';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-detail"] = ["GET"];
        $verbs["get-attributes"] = ["GET"];
        $verbs["save-retur"] = ["POST"];
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
            $model = new InfoPenerimaanSuplier;
            $query = $model::find();

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
                if (isset($_GET['advanced-filter']['supplier_nama'])) {
                    $_GET['advanced-filter']['supplier_id'] = $_GET['advanced-filter']['supplier_nama'];
                    unset($_GET['advanced-filter']['supplier_nama']);
                }
            }

            $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);

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
            $model = new ReturPenerimaanObat;
            $tanggalRetur = !empty($request->post('tanggal_retur')) ? $request->post('tanggal_retur') : date('Y-m-d');
            $dataRetur = $request->post('data_retur');
            $model->panerimaanobatsupp_id = $id;
            $model->tgl_retur = $tanggalRetur;
            $model->pegawairetur_id = $request->post('pegawai_retur');
            $model->alasan_retur = $request->post('alasan_retur');
            $model->ruanganretur_id = $jwt->ruangan_id;
            if ($model->validate() && $model->save()) {
                $idParent = $model->returpenerimaanobat_id;
                $instDetail = [];
                $dataValid = [];
                $jsonToArray = json_decode($dataRetur,true);
                if (is_array($jsonToArray)) {
                    foreach ($jsonToArray as $id_penerimaan => $value) {
                        $idPenerimaan = DocoHelpers::decrypt($id_penerimaan);
                        $obatAlkesId  = $value['obatalkes_id'];
                        // $dataValid[$idPenerimaan] = $value;
                        if (isset($dataValid[$obatAlkesId]['qty_retur'])) {
                            $dataValid[$obatAlkesId]['qty_retur'] += $value['qty_retur'];
                        } else {
                            $dataValid[$obatAlkesId] = $value;
                        }
                        $instDetail[] = [
                            'returpenerimaanobat_id' => $idParent,
                            'obatalkes_id' => $obatAlkesId,
                            'satuanbesar_id' => $value['satuanbesar_id'],
                            'tgl_kadaluarsa' => $value['tglkadaluarsa'],
                            'qty_retur' => $value['qty_retur'],
                            'penerimaansuppdetail_id' => $idPenerimaan
                        ];
                    }

                    if ($instDetail) {
                        ReturPenerimaanObatDetail::batchInsert($instDetail,false);
                    }
                    $listDataRetur = ReturPenerimaanObatDetail::find()->select([
                        'obatalkes_id',
                        'returpenerimaanobatdetail_id'
                    ])->where([
                        'returpenerimaanobat_id' => $idParent
                    ])->asArray()->all();

                    /** Untuk mendapatkan id retur detail **/
                    $listIdDetail = $listObatAlkes = [];
                    foreach ($listDataRetur as $value) {
                        $listIdDetail[$value['obatalkes_id']] = $value['returpenerimaanobatdetail_id'];
                        $listObatAlkes[] = $value['obatalkes_id'];
                    }

                    $satuanKonversi = SatuanKonversi::find()->where([
                        'obatalkes_id' => $listObatAlkes
                    ])->asArray()->all();

                    $konversi = [];
                    foreach ($satuanKonversi as $value) {
                        $konversi[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['nilai_konversi'];
                    }

                    $dateNow = date('Y-m-d');
                    $infoStok = InfoStokObatAlkesView::find()->select([
                        'obatalkes_id',
                        'satuankecil_id',
                        'qty_stok',
                        'obatalkes_nama'
                    ])->where([
                        'ruangan_id' => $jwt->ruangan_id,
                        'obatalkes_id' => $listObatAlkes
                    ])->asArray()->all();
                    // ->andWhere(['<=', 'tglperiodestok_awal', "'{$dateNow}'"])
                    // ->andWhere(['>=','tglperiodestok_akhir', "'{$dateNow}'"])->asArray()->all();

                    $detailTrans = [];

                    foreach ($infoStok as $value) {
                        if (isset($listIdDetail[$value['obatalkes_id']])
                            && isset($dataValid[$value['obatalkes_id']])) {
                            $respDetail = $listIdDetail[$value['obatalkes_id']];
                            $dataTrans = $dataValid[$value['obatalkes_id']];
                            $qtyRetur = $dataTrans['qty_retur'];
                            $qtyRetur = isset($konversi[$value['obatalkes_id']][$dataTrans['satuanbesar_id']])
                              ? $qtyRetur * $konversi[$value['obatalkes_id']][$dataTrans['satuanbesar_id']] :0;

                            if ($qtyRetur > $value['qty_stok']) {
                                $transaction->rollBack();
                                return [
                                    'title' => 'Proses Gagal!',
                                    'text' => 'Stok Obat '. $value['obatalkes_nama'] . ' tidak mencukupi.',
                                    'status' => 422
                                ];
                            }

                            if ($qtyRetur) {
                                $detailTrans[] = [
                                    'obatalkes_id' => $value['obatalkes_id'],
                                    'qty_satuanpakai' => $qtyRetur,
                                    'satuankecil_id' => $value['satuankecil_id'],
                                    'returpenerimaanobatdetail_id' => $respDetail,
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
                    // Mencari Metode
                    $konfig = $connection->createCommand("
                        SELECT metodeantrian FROM konfigfarmasi_k
                        WHERE tglberlaku >= '{$tanggalBerlaku}'
                        AND konfigfarmasi_aktif = true
                        AND is_active = true
                    ")->queryOne();
                    // Mencari Metode dengan nilai default FEFO
                    $currentMetode = LogicStokObatAlkes::FEFO;
                    if ($konfig) {
                        $currentMetode = isset($konfig['metodeantrian'])
                                            ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
                    }

                   $tanggalPemakaian = date('Y-m-d H:i:s');
                    // Execute By Condition
                    if ($currentMetode === LogicStokObatAlkes::FEFO) {
                       $methode = LogicStokObatAlkes::methodeFEFO($detailTrans,$tanggalPemakaian);
                    } else {
                       $methode = LogicStokObatAlkes::methodeFIFO($detailTrans,$tanggalPemakaian);
                    }

                    $transaction->commit();
                    $transRetur = ReturPenerimaanObat::find()->select([
                        'no_returpenerimaanobat'
                        ])->where([
                            'returpenerimaanobat_id' => $idParent
                            ])->asArray()->one();
                    IntegrasiAkunting::integrateReturSupplier($transRetur['no_returpenerimaanobat'], DocoConstants::JENIS_OBAT);
                    return [
                        'id_parent' => DocoHelpers::encrypt($idParent),
                        'no_retur' => $transRetur['no_returpenerimaanobat']
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
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id)
    {
        $data = InfoPenerimaanSuplier::find()->where([
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
            $model = new InfoPenerimaanSuplierDetail;
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

    public function actionGetDetail($id)
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPenerimaanSuplierDetail;
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
     * @controller actionPrintPdf
     * @attribute #nama_ruangan# => Menampilkan Nama Ruangan
     * @attribute #no_penerimaan# => Menampilkan No Penerimaan
     * @attribute #tanggal_penerimaan# => Menampilkan Tanggal Penerimaan
     * @attribute #nama_supplier# => Manampilkan Nama Supplier
     * @attribute #no_faktur# => Menampilkan nomor faktur
     * @attribute #tabel_detail# => Menampilkan list tabel detail penerimaan
     * @attribute #peg_mengetahui# => Menampilkan pegawai Megetahui
     * @attribute #peg_meyetujui# => Menampilkan pegawai menyetujui
     **/

    public function actionPrintPdf($id)
    {
        try {
            $request = Yii::$app->request;
            $data = InfoPenerimaanSuplier::find()->where([
                'penerimaansupp_id' => $id
            ])->one();

            $detail = InfoPenerimaanSuplierDetail::find()->where([
                'penerimaansupp_id' => $id
            ])->asArray()->all();
            
            $type = $request->get('type');
            $data_detail = self::getDataDetailPenerimaan($id, $type);
            
            if(!empty($data_detail)){
                $jenisPenerimaan = '-';
                if($data_detail['header']['is_consigment'] && $data_detail['header']['is_donasi']){
                    $jenisPenerimaan = 'Consignment dan Donasi';
                }else if($data_detail['header']['is_consigment']){
                    $jenisPenerimaan = 'Consignment';
                }else if($data_detail['header']['is_donasi']){
                    $jenisPenerimaan = 'Donasi';
                }else{
                    $jenisPenerimaan = '-';
                }
            }else {
                throw new \Exception("Detail penerimaan tidak ditemukan", 1);
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#nama_ruangan#' => $request->get('ruangan_name'),
		        '#no_penerimaan#' => ArrayHelper::getValue($data, 'no_penerimaan', null),
		        '#tanggal_penerimaan#' => isset($data['tgl_penerimaan']) ? date('d-M-Y H:i:s', strtotime($data['tgl_penerimaan'])) : '-',
		        '#nama_supplier#' => ArrayHelper::getValue($data, 'supplier_nama', null),
                '#jenis_penerimaan#' => $jenisPenerimaan, 
		        '#no_faktur#' => ArrayHelper::getValue($data, 'no_faktur', null),
                '#tabel_detail#' => $this->renderPartial('index',[
                    'detail' => $detail
                ]),
		        '#peg_mengetahui#' => ArrayHelper::getValue($data, 'peg_mengetahui_nama', null),
		        '#peg_meyetujui#' => ArrayHelper::getValue($data, 'peg_menyetujui_nama', null),
            ];

            $print->Output();

        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
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
            $data = InfoReturPenerimaanObat::find()->where([
                'returpenerimaanobat_id' => $id
            ])->one();
            $detail = InfoReturPenerimaanObatDetail::find()->where([
                'returpenerimaanobat_id' => $id
            ])->asArray()->all();

            $print = new DocoPrint();
            $print->attributes = [
                '#no_retur#' => $data->no_returpenerimaanobat,
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

    /**
    * @controller actionPrintGrn
    * @attribute #dataTable# => Menampilkan Data cetak GRN
    * @attribute #no_penerimaan# => Menampilkan Nomor Penerimaan
    * @attribute #jenis_penerimaan# => Menampilkan Jenis Penerimaan`
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
            // $no_po = isset($data_detail["header"]["no_poobat"]) ? $data_detail["header"]["no_poobat"] : $data_detail["header"]["no_pobarang"];

            $jenisPenerimaan = '-';
            if($data_detail['header']['is_consigment'] && $data_detail['header']['is_donasi']){
                $jenisPenerimaan = 'Consignment dan Donasi';
            }else if($data_detail['header']['is_consigment']){
                $jenisPenerimaan = 'Consignment';
            }else if($data_detail['header']['is_donasi']){
                $jenisPenerimaan = 'Donasi';
            }

            $sub_total = 0;
            $total_discount = 0;
            $ppn_nilai = 0;
            $total = 0;
            $total_ppn_nilai = ($sub_total - $total_discount) * 0;
            $total_amount_discount = $sub_total - $total_discount;

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
            $hargaPPN = $subTotalDiscount * $data_detail['detail_penerimaan'][0]['ppn']/100;
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
                '#no_fax#' =>  $data_detail['header']['no_fax'],
                '#no_faktur#' => $data_detail["header"]["no_faktur"] ,
                '#no_penerimaan#' => $data_detail["header"]["no_penerimaan"] ,
                '#jenis_penerimaan#' => $jenisPenerimaan,
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

            $pajak_persen = Pajak::find()->select(['pajak_persen'])->where(['pajak_id' => $model->pajak_id])->asArray()->one();
            $pajak_persen = current($pajak_persen);

            if($model->save()){
                $dataPenerimaan = $connection->createCommand("
                    SELECT * FROM penerimaansuppdetail_t
                    WHERE penerimaansupp_id = {$penerimaansupp_id}
                ")->queryAll();

                $header = [
                    'no_transaksi'  => $model->no_penerimaan,
                    'type'          => 'penerimaan-obat-manual'
                ];
                $konfig = KonfigFarmasi::find()->select(['hargaygdigunakan', 'use_discount', 'use_ppn'])->asArray()->one();

                foreach ($dataPenerimaan as $k => $v) {
                    $hn_diskon = 0;
                    if($konfig['use_discount']) {
                        $hn_diskon = $v['harga_netto_satuan'] * ($v['diskon'] / 100);
                    }

                    $pajak = 0;
                    if($konfig['use_ppn']) {
                        $pajak = ($v['harga_netto_satuan'] - $hn_diskon) * ($pajak_persen / 100);
                    }

                    $dataInsertStok[] = [
                        'ruangan_id'    => $jwt->ruangan_id,
                        'obatalkes_id'  => $v['obatalkes_id'],
                        'tglkadaluarsa' => $v['tgl_kadaluarsa'],
                        'qty_kecil'     => $v['qty_kecil'],
                        'persendiscount'=> $v['diskon'],
                        'jmldiscount' => $hn_diskon,
                        'persenppn' => $pajak_persen,
                        'jmlppn' => $pajak,
                        'persenpph'     => 0,
                        'persenmargin'  => 0,
                        'jmlmargin'     => 0,
                        'nobatch'       => $v['no_batch'],
                        'harganetto'    => $v['harga_netto_satuan'],
                        'satuankecil_id'=> $v['satuankecil_id'],
                        'penerimaansupp_id' => $penerimaansupp_id,
                        'penerimaansuppdetail_id' => $v['penerimaansuppdetail_id'],
                    ];
                }

                $stockIn = (new StockService)->in($header, $dataInsertStok);
                if($stockIn['message'] == 'failed') {
                    $transaction->rollBack();
                    return $stockIn;
                }
                
                if($konfig['hargaygdigunakan'] == DocoConstants::HARGA_FIX_RATE) {
                    Yii::$app->runAction(
                        'v1/allow/update-base-price',
                        [
                            'transaksi_id' => $penerimaansupp_id,
                            'tipe' => 'PEN_SUPP',
                            'detail' => json_encode($dataInsertStok)
                        ]
                    );
                }

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

    private function getDataDetailPenerimaan($id, $type) {
        // if($type == 'obat') {

        $model_penerimaan = new InfoPenerimaanSuplier;
        $detail = new InfoPenerimaanSuplierDetail;
        // $doc = new PenerimaanObatDoc();
        $pk_id = 'penerimaansupp_id';
        // $validasidetail_id = 'validasipoobatdetail_id';
        $item_id = 'obatalkes_id';
        $item_nama = 'obatalkes_nama';
            // $s_konversi_id = 's_konversiobt_id';
        // } else {
            // $model_penerimaan = new InfoPenerimaanBarang;
            // $detail = new InfoPenerimaanBarangDetail;
            // $doc = new PenerimaanBarangDoc;
            // $pk_id = 'penerimaanbarang_id';
            // $validasidetail_id = 'validasipobarangdetail_id';
            // $item_id = 'barang_id';
            // $item_nama = 'barang_nama';
            // $s_konversi_id = 's_konversibrg_id';
        // }

        $query_penerimaan = $model_penerimaan->find()->where([$pk_id => $id])->one();

        // $list_pegawai = [
        //     "menyetujui" => [$query_penerimaan->peg_menyetujui => $query_penerimaan->peg_menyetujui_nama],
        //     "mengetahui" => [$query_penerimaan->peg_mengetahui => $query_penerimaan->peg_mengetahui_nama],
        // ];

        $detail_penerimaan = $detail->find()->where([
            $pk_id => $id
        ])->orderby([$item_id => SORT_ASC,"tgl_kadaluarsa" => SORT_ASC])->all();

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
                "kode_item" => $value["obatalkes_kode"],
                "satuanunit_nama" => $value["satuanunit_nama"],
            ];
        }

        // $query_doc = $doc->find()->where([
        //     $pk_id => $id,
        //     "is_deleted" => false
        // ])->all();

        return [
            'header' => $query_penerimaan,
            'detail' => $detail_rows,
            // 'list_pegawai' => $list_pegawai,
            // 'document'=> $query_doc,
            "detail_penerimaan" => $detail_penerimaan
        ];
    }

    //sync-excel

    public function getDataLaporanExcel()
    {
        $model = new InfoPenerimaanSuplier;
        $query = $model::find();
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
            if (isset($_GET['advanced-filter']['supplier_nama'])) {
                $_GET['advanced-filter']['supplier_id'] = $_GET['advanced-filter']['supplier_nama'];
                unset($_GET['advanced-filter']['supplier_nama']);
            }
        }
        $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        $limit = 100;
        $countData = $this->getDataLaporanExcel()->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$limit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'PenerimaanObatAlkesExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportPenerimaanObatAlkes' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadPenerimaanObatAlkesExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
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

            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

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

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Penerimaan Obat Alkes.xlsx';

        if (file_exists($fileName))
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }
}
