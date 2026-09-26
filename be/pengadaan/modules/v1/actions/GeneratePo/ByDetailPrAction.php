<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\GeneratePo;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\PurchaseRequisition;
use app\modules\v1\models\PurchaseRequisitionDetail;
use app\modules\v1\models\PurchaseRequisitionBarang;
use app\modules\v1\models\PurchaseRequisitionBarangDetail;
use app\modules\v1\models\InfoPurchaseRequisition;
use app\modules\v1\models\InfoPurchaseReqDetailView;
use app\modules\v1\models\InfoPurchaseReqGabungView;
use app\modules\v1\models\InfoPurchaseReqGabungDetailView;
use app\modules\v1\models\KontrakSupplierView;
use app\modules\v1\models\KontrakSupplierItemView;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\ObatAlkes;
use Doco\models\SatuanKonversiView;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\GeneratedPO;
use app\modules\v1\models\LogActivityR;

class ByDetailPrAction extends Action {
    private function getTipe($tipe)
    {
        if (strtoupper($tipe) == 'BARANG') {
            return 'BARANG';
        }else{
            return 'OBAT';
        }
    }

    public function run() {
        $purchasereq_id = Yii::$app->request->post('purchasereq_id');
        $details = Yii::$app->request->post('details', []);
        $tipe = Yii::$app->request->post('type', 'OBAT');
        $tipe = $this->getTipe($tipe);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
            //find pr by purchasereq_id PR
            $pr = InfoPurchaseReqGabungView::find()
                            ->where(['purchasereq_id'=>$purchasereq_id,'tipe'=>$tipe])
                            ->asArray()->one();
            $details = InfoPurchaseReqGabungDetailView::find()
                            ->where(['IN', 'purchasereqdetail_id', $details])
                            ->andWhere(['tipe'=>$tipe])
                            ->asArray()->all();
            if(is_null($details) || is_null($pr))
                throw new \Exception("Data PR tidak ditemukan", 1);

            if($pr['status'] == DocoConstants::VAR_SUDAH_PO) {
                throw new \Exception("Sudah Dibuat PO", 1);
            } else if($pr['status'] == DocoConstants::VAR_CANCEL_PR) {
                throw new \Exception("PR sudah dibatalkan", 1);
            }

            $items = array_column($details, 'item_id');
            $kontrak_supplier = KontrakSupplierItemView::find()
                                ->where(['IN','item_id',$items])->andWhere(['tipe'=>$tipe])
                                ->asArray()->all();

            $kontrakItem = $kontrakSupplier = [];
            foreach ($kontrak_supplier as $_kontrak) {
                $kontrakSupplier[$_kontrak['supplier_id']] = $_kontrak;
                $kontrakItem[$_kontrak['item_id']] = $_kontrak;
            }

            $masterSupplier = Supplier::find()->asArray()->all();
            $list_pajak_supplier = array_column($masterSupplier, 'pajak_id', 'supplier_id');
            $masterPajak = Pajak::find()
                            ->where(['is_active' => true])
                            ->asArray()->all();
            $list_pajak = array_column($masterPajak, 'pajak_persen', 'pajak_id');

            $kontrakNullExist = false;
            foreach ($details as $k => $v) {
                if($v['status_id'] == DocoConstants::VAR_SUDAH_PO) {
                    throw new \Exception("Terdapat detail yang sudah dibuat PO", 1);
                } else if($v['status_id'] == DocoConstants::VAR_CANCEL_PR) {
                    throw new \Exception("Terdapat detail PR yang sudah dibatalkan", 1);
                } else {
                    if(isset($kontrakItem[$v['item_id']])){
                        $details[$k]['kontrak_supplier_id'] = $kontrakItem[$v['item_id']]['supplier_id'];
                        $details[$k]['kontrak'] = $kontrakItem[$v['item_id']];
                    }else{
                        $details[$k]['kontrak_supplier_id'] = isset($v['defaultsupplier_id']) ? $v['defaultsupplier_id'] : 0;
                        $details[$k]['kontrak'] = null;
                        $kontrakNullExist = true;
                    }
                }
            }

            $purchasesBySupplier = $purchasesNoSupplier =[];
            foreach ($details as $_detail) {
                if(!isset($_detail['kontrak_supplier_id']) || $_detail['kontrak_supplier_id'] == 0){
                    $purchasesNoSupplier[] = $_detail;
                }else{
                    $purchasesBySupplier[$_detail['kontrak_supplier_id']][] = $_detail;
                }
            }

            $currentDate = date('Y-m-d H:i:s');
            $detailPO=[];
            $countGeneratedPO = 0;
            foreach ($purchasesBySupplier as $supplierId => $listItemPR) {
                if($supplierId > 0) {
                    $countGeneratedPO++;
                }
                if($tipe == 'OBAT'){
                    $modelPO = new ValidasiPoObat;
                }else{
                    $modelPO = new ValidasiPoBarang;
                }
                // $modelPO->tgl_validasi = $currentDate;
                $modelPO->ruangan_id = $pr['ruangan_id'];
                $modelPO->pegawai_id = $pr['pegawai_id'];
                $modelPO->supplier_id = $supplierId;
                $modelPO->diorder_oleh = Yii::$app->jwt->user->pegawai_id;
                $modelPO->payterm_id = isset($kontrakSupplier[$supplierId]) ? ArrayHelper::getValue($kontrakSupplier[$supplierId],'payterm_id',null) : null;
                $modelPO->pajak_id = isset($kontrakSupplier[$supplierId]) ? ArrayHelper::getValue($kontrakSupplier[$supplierId],'pajak_id',null) : $list_pajak_supplier[$supplierId];
                $modelPO->additional_data = json_encode([
                    'kontrak' => isset($kontrakSupplier[$supplierId]) ? $kontrakSupplier[$supplierId] : null
                ]);
                if(!$modelPO->save()){
                    throw new \Exception("Gagal Membuat PO", 1);
                }

                $logPO = new LogActivityR;
                $logPO->attributes = [
                    'transaksi_id' => $modelPO->getPrimaryKey(),
                    'tgl' => $currentDate,
                    'tipe' => $tipe == 'OBAT' ? 'PO' : 'PONONMEDIS',
                    'aksi' => DocoConstants::LA_AKSI_TAMBAH,
                    'keterangan' => 'PRGENERATE',
                    'alasan' => null,
                    'additional_detail' => null,
                    'created_by' => Yii::$app->user->identity->pegawai_id
                ];
                $logPO->save(false);

                $po_subtotal = 0;
                $po_total_discount = 0;

                if(isset($kontrakSupplier[$supplierId])) {
                    $po_ppn_persen = ArrayHelper::getValue($kontrakSupplier[$supplierId],'persen_ppn',0);
                } else {
                    $po_ppn_persen = is_null($list_pajak_supplier[$supplierId]) ? 0 : $list_pajak[$list_pajak_supplier[$supplierId]];
                }

                $po_total = 0;

                foreach ($listItemPR as $_detailItemPR) {
                    // cari satuan terbesar dari kontrak supplier/satuan konversi
                    if($_detailItemPR['kontrak'] != null){
                        $satuanbesar_id = $_detailItemPR['kontrak']['satuankonv1_id'];
                        $satuan_terbesar = SatuanKonversiView::find()->where([
                                            'obatalkes_id' => $_detailItemPR['kontrak']['item_id'],
                                            'satuanbesar_id' => $_detailItemPR['kontrak']['satuankonv1_id'],
                                            'jenis' => $tipe
                                            ])->asArray()->one();
                    } else {
                        $satuan_terbesar = $this->controller->findSatuanTerbesar($_detailItemPR,$tipe);
                    }

                    if($_detailItemPR['kontrak'] != null && $satuan_terbesar == null) {
                        $satuan_terbesar = $this->controller->findSatuanTerbesar($_detailItemPR,$tipe);
                    }

                    if(isset($_detailItemPR['kontrak'])) {
                        $_harga = ArrayHelper::getValue($_detailItemPR['kontrak'], 'harga', 0);
                    } else {
                        $_harga = 0;
                    }

                    $_diskon = isset($_detailItemPR['kontrak']) ? ArrayHelper::getValue($_detailItemPR['kontrak'],'diskon',0) : 0;
                    $qty_po = ceil(($_detailItemPR['qty_input'] * $_detailItemPR['nilai_konversi']) / $satuan_terbesar['nilai_konversi']);
                    $_diskonharga = ($_diskon / 100) * ($_harga * $qty_po);
                    $_jumlahSetelahDiskon = ($_harga * $qty_po) - $_diskonharga;

                    $_detailPO = [
                        'qty_po' => $qty_po,
                        'qty_input' => $qty_po,
                        'harga' => $_harga,
                        'discount' => $_diskon,
                        'discount_rp' => $_diskonharga,
                        'jumlah' => $_jumlahSetelahDiskon,
                        'additional_data' => json_encode([
                            'purchasereq_id' => $_detailItemPR['purchasereq_id'],
                            'purchasereqdetail_id' => $_detailItemPR['purchasereqdetail_id'],
                            'no_pr' => $_detailItemPR['no_pr'],
                            'qty_input' => $_detailItemPR['qty_input'],
                            'qty_po' => $qty_po,
                            'satuaninput_id' => $_detailItemPR['satuan_id'],
                            'qty_konversi' => $_detailItemPR['qty_konversi'],
                            'satuankonversi_id' => $_detailItemPR['satuankonversi_id'],
                            'catatan' => $_detailItemPR['catatan'],
                            'kontrak' => @$_detailItemPR['kontrak']
                        ])
                    ];

                    if($tipe =='OBAT'){
                        $_detailtipe = [
                            'validasipoobat_id' => $modelPO->getPrimaryKey(),
                            'obatalkes_id' => $_detailItemPR['item_id'],
                            's_konversiobt_id' => $satuan_terbesar['satuankonversi_id'],
                            'purchasereqdetail_id' => $_detailItemPR['purchasereqdetail_id']
                        ];
                    }else{
                        $_detailtipe = [
                            'validasipobarang_id' => $modelPO->getPrimaryKey(),
                            'barang_id' => $_detailItemPR['item_id'],
                            's_konversibrg_id' => $satuan_terbesar['satuankonversi_id'],
                            'purchasereqbrgdetail_id' => $_detailItemPR['purchasereqdetail_id']
                        ];
                    }
                    $detailPO[] = array_merge($_detailPO,$_detailtipe);

                    $po_subtotal += $_jumlahSetelahDiskon;
                    $po_total_discount += $_diskonharga;
                }

                $po_ppn_nilai = $po_subtotal * ($po_ppn_persen/100);
                $po_total = $po_subtotal + $po_ppn_nilai;

                $modelPO->sub_total = $po_subtotal;
                $modelPO->total_discount = $po_total_discount;
                $modelPO->ppn_persen = $po_ppn_persen;
                $modelPO->ppn_nilai = $po_ppn_nilai;
                $modelPO->total = $po_total;
                if(!$modelPO->update())
                    throw new \Exception("Gagal Simpan Subtotal", 1);
            }

            if(count($detailPO)>0){
                if($tipe =='OBAT'){
                    ValidasiPoObatDetail::batchInsert($detailPO);
                    $_poId = array_column($detailPO, 'validasipoobat_id');
                    $_gDetailPO = ValidasiPoObatDetail::find()->where(['validasipoobat_id'=>$_poId])->asArray()->all();
                }else{
                    ValidasiPoBarangDetail::batchInsert($detailPO);
                    $_poId = array_column($detailPO, 'validasipobarang_id');
                    $_gDetailPO = ValidasiPoBarangDetail::find()->where(['validasipobarang_id'=>$_poId])->asArray()->all();
                }

                $generatedPOPR = $log_po_detail = [];
                foreach ($_gDetailPO as $_gDetailPOVal) {
                    $prdetail_id = $tipe == 'OBAT' ? $_gDetailPOVal['purchasereqdetail_id'] : $_gDetailPOVal['purchasereqbrgdetail_id'];
                    $podetail_id = $tipe == 'OBAT' ? $_gDetailPOVal['validasipoobatdetail_id'] : $_gDetailPOVal['validasipobarangdetail_id'];
                    $generatedPOPR[] = [
                        'tipe' => $tipe,
                        'prdetail_id' => $prdetail_id,
                        'podetail_id' => $podetail_id,
                        'status' => 1018
                    ];

                    $log_po_detail[] = [
                        'transaksi_id' => $tipe == 'OBAT' ? $_gDetailPOVal['validasipoobatdetail_id'] : $_gDetailPOVal['validasipobarangdetail_id'],
                        'tgl' => $currentDate,
                        'tipe' => 'PODETAIL'.$tipe,
                        'aksi' => DocoConstants::LA_AKSI_TAMBAH,
                        'keterangan' => 'PRGENERATE',
                        'alasan' => null,
                        'additional_detail' => null,
                        'created_by' => Yii::$app->user->identity->pegawai_id
                    ];
                }

                if(count($generatedPOPR)>0){
                    GeneratedPO::batchInsert($generatedPOPR);
                }

                if(count($log_po_detail)>0){
                    LogActivityR::batchInsert($log_po_detail);
                }
            }

            if(count($purchasesNoSupplier) >0){
                if($tipe =='OBAT'){
                    $modelPOnoSupplier = new ValidasiPoObat;
                }else{
                    $modelPOnoSupplier = new ValidasiPoBarang;
                }
                // $modelPOnoSupplier->tgl_validasi = $currentDate;
                $modelPOnoSupplier->ruangan_id = $pr['ruangan_id'];
                $modelPOnoSupplier->pegawai_id = $pr['pegawai_id'];
                $modelPOnoSupplier->diorder_oleh = Yii::$app->jwt->user->pegawai_id;
                $modelPOnoSupplier->supplier_id = null;
                $modelPOnoSupplier->payterm_id = null;
                $modelPOnoSupplier->pajak_id = null;
                $modelPOnoSupplier->additional_data = json_encode([
                    'kontrak' => null,
                    'pr' => $pr
                ]);
                
                if(!$modelPOnoSupplier->save()){
                    throw new \Exception("Gagal Membuat PO", 1);
                }
                $countGeneratedPO++;

                $logPOnosupplier = new LogActivityR;
                $logPOnosupplier->attributes = [
                    'transaksi_id' => $modelPOnoSupplier->getPrimaryKey(),
                    'tgl' => $currentDate,
                    'tipe' => $tipe == 'OBAT' ? 'PO' : 'PONONMEDIS',
                    'aksi' => DocoConstants::LA_AKSI_TAMBAH,
                    'keterangan' => 'PRGENERATE',
                    'alasan' => null,
                    'additional_detail' => null,
                    'created_by' => Yii::$app->user->identity->pegawai_id
                ];
                $logPOnosupplier->save(false);

                $detailPOnoSupplier=[];
                $po_ns_subtotal = 0;
                $po_ns_total_discount = 0;
                $po_ns_ppn_persen = 0;
                $po_ns_total = 0;
                foreach ($purchasesNoSupplier as $detailNoSupplier) {
                    $satuan_terbesar = $this->controller->findSatuanTerbesar($detailNoSupplier,$tipe);

                    $_harga = 0;
                    $_diskon = 0;
                    $_diskonharga = 0;
                    $qty_po = ceil(($detailNoSupplier['qty_input'] * $detailNoSupplier['nilai_konversi']) / $satuan_terbesar['nilai_konversi']);
                    $_jumlahSetelahDiskon = ($_harga * $qty_po) - $_diskonharga;
                    $_detailPOnoSupplier = [
                        'qty_po' => $qty_po,
                        'qty_input' => $qty_po,
                        'harga' => $_harga,
                        'discount' => $_diskon,
                        'discount_rp' => $_diskonharga,
                        'jumlah' => $_jumlahSetelahDiskon
                    ];
                    if($tipe == 'OBAT'){
                        $_detailtipenosupplier = [
                            'validasipoobat_id' => $modelPOnoSupplier->getPrimaryKey(),
                            'obatalkes_id' => $detailNoSupplier['item_id'],
                            's_konversiobt_id' => $satuan_terbesar['satuankonversi_id'],
                            'purchasereqdetail_id' => $detailNoSupplier['purchasereqdetail_id']
                        ];
                    }else{
                        $_detailtipenosupplier = [
                            'validasipobarang_id' => $modelPOnoSupplier->getPrimaryKey(),
                            'barang_id' => $detailNoSupplier['item_id'],
                            's_konversibrg_id' => $satuan_terbesar['satuankonversi_id'],
                            'purchasereqbrgdetail_id' => $detailNoSupplier['purchasereqdetail_id']
                        ];
                    }
                    $detailPOnoSupplier[] = array_merge($_detailPOnoSupplier,$_detailtipenosupplier);

                    $po_ns_subtotal += $_jumlahSetelahDiskon;
                    $po_ns_total_discount += $_diskonharga;
                }

                $po_ns_ppn_nilai = $po_ns_subtotal * ($po_ns_ppn_persen/100);
                $po_ns_total = $po_ns_subtotal + $po_ns_ppn_nilai;

                $modelPOnoSupplier->sub_total = $po_ns_subtotal;
                $modelPOnoSupplier->total_discount = $po_ns_total_discount;
                $modelPOnoSupplier->ppn_persen = $po_ns_ppn_persen;
                $modelPOnoSupplier->ppn_nilai = $po_ns_ppn_nilai;
                $modelPOnoSupplier->total = $po_ns_total;
                if(!$modelPOnoSupplier->update())
                    throw new \Exception("Gagal Simpan Subtotal", 1);

                if(count($detailPOnoSupplier)>0){
                    if($tipe =='OBAT'){
                        ValidasiPoObatDetail::batchInsert($detailPOnoSupplier);
                        $_poIdNosup = array_column($detailPOnoSupplier, 'validasipoobat_id');
                        $_gDetailPOnosup = ValidasiPoObatDetail::find()->where(['validasipoobat_id'=>$_poIdNosup])->asArray()->all();
                    }else{
                        ValidasiPoBarangDetail::batchInsert($detailPOnoSupplier);
                        $_poIdNosup = array_column($detailPOnoSupplier, 'validasipobarang_id');
                        $_gDetailPOnosup = ValidasiPoBarangDetail::find()->where(['validasipobarang_id'=>$_poIdNosup])->asArray()->all();
                    }

                    $generatedPOPRns = $log_po_detail_nosupp = [];
                    foreach ($_gDetailPOnosup as $_gDetailPOValnosup) {
                        $prdetail_idns = $tipe == 'OBAT' ? $_gDetailPOValnosup['purchasereqdetail_id'] : $_gDetailPOValnosup['purchasereqbrgdetail_id'];
                        $podetail_idns = $tipe == 'OBAT' ? $_gDetailPOValnosup['validasipoobatdetail_id'] : $_gDetailPOValnosup['validasipobarangdetail_id'];
                        $generatedPOPRns[] = [
                            'tipe' => $tipe,
                            'prdetail_id' => $prdetail_idns,
                            'podetail_id' => $podetail_idns,
                            'status' => 1018
                        ];

                        $log_po_detail_nosupp[] = [
                            'transaksi_id' => $tipe == 'OBAT' ? $_gDetailPOValnosup['validasipoobatdetail_id'] : $_gDetailPOValnosup['validasipobarangdetail_id'],
                            'tgl' => $currentDate,
                            'tipe' => 'PODETAIL'.$tipe,
                            'aksi' => DocoConstants::LA_AKSI_TAMBAH,
                            'keterangan' => 'PRGENERATE',
                            'alasan' => null,
                            'additional_detail' => null,
                            'created_by' => Yii::$app->user->identity->pegawai_id
                        ];
                    }

                    if(count($generatedPOPRns)>0){
                        GeneratedPO::batchInsert($generatedPOPRns);
                    }

                    if(count($log_po_detail_nosupp)>0){
                        LogActivityR::batchInsert($log_po_detail_nosupp);
                    }
                }

            }

            if($tipe == 'OBAT'){
                $objectPR =  new PurchaseRequisition;
                $objectPRdetail = new PurchaseRequisitionDetail;
            }else{
                $objectPR =  new PurchaseRequisitionBarang;
                $objectPRdetail = new PurchaseRequisitionBarangDetail;
            }
            foreach ($details as $key => $value) {
                $objPrDetail = $objectPRdetail::findOne($value['purchasereqdetail_id']);
                $objPrDetail->status = DocoConstants::VAR_SUDAH_PO;
                if(!$objPrDetail->save())
                    throw new \Exception("Gagal Mengubah status detail PR", 1);
            }

            // jika tidak ada status belum po, ubah status jadi Sudah PO, jika belum maka PO Sebagian
            $allProcessed = true;
            $queryValidDetail = $objectPRdetail::find();
            if($tipe == 'OBAT'){
                $queryValidDetail->where(['purchasereq_id' => $purchasereq_id]);
            }else{
                $queryValidDetail->where(['purchasereqbrg_id' => $purchasereq_id]);
            }
            
            $objValidasiDetail = $queryValidDetail->asArray()->all();
            foreach ($objValidasiDetail as $key => $value) {
                if($value['status'] == DocoConstants::VAR_BELUM_PO) {
                    $allProcessed = false;
                }
            }

            $objPr = $objectPR::findOne($purchasereq_id);
            if($allProcessed) {
                $objPr->status = DocoConstants::VAR_SUDAH_PO;
            } else {
                if($countGeneratedPO > 0) {
                    $objPr->status = DocoConstants::VAR_PO_SEBAGIAN;
                }
            }
            if(!$objPr->save())
                throw new \Exception("Gagal Mengubah status PR", 1);

            $transaction->commit();

            $statusCode = 200;
            $message = "Data berhasil disimpan";

            return [
                'message' => $message,
                'statusCode' => $statusCode,
                'data' => [
                    'generated_po' => $countGeneratedPO
                ]
            ];
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            $this->controller->logError($e);
            $transaction->rollback();
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
