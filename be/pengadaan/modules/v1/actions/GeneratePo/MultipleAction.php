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
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

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
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\GeneratedPO;
use app\modules\v1\models\LogActivityR;

class MultipleAction extends Action {
	private $_kontrakItem;

    public function run() {
    	$listPr = Yii::$app->request->post('listPr',[]);
        $tipe = Yii::$app->request->post('type', 'OBAT');
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

    	try{
            if(is_array($tipe) && count($tipe) > 1) throw new \Exception("Tidak Dapat Melakukan Generate PO Medis dan Non-Medis Secara Bersamaan.", 1);

    		if(!is_array($listPr)) throw new \Exception("Format Request Salah", 1);

            if(is_array($tipe)) $tipe = implode("", $tipe);

            $tipe = strtoupper($tipe);

	    	//find pr by nomor PR
	    	$list_pr = InfoPurchaseReqGabungView::find()
	    			->where(['IN','no_pr',$listPr])
	    			->andWhere(['OR',['status'=>712],['status'=>719]])
	    			->andWhere(['tipe'=>$tipe])->asArray()->all();

	    	if(count($list_pr)<1) throw new \Exception("Tidak Ada Data PR yang bisa diproses", 1);

	    	$list_no_pr = array_column($list_pr, 'no_pr');

	    	foreach ($list_pr as $_val) {
	    		$list_prs[$_val['purchasereq_id']] = $_val;
	    	}

	    	$list_detail_pr = InfoPurchaseReqGabungDetailView::find()
	    					->where(['IN','no_pr',$list_no_pr])
	    					->andWhere(['status_id'=>712])
	    					->andWhere(['tipe'=>$tipe])->asArray()->all();

	    	if(is_null($list_detail_pr)) 
	    		throw new \Exception("Data Detail PR tidak ditemukan", 1);
	    		
	    	$list_item = array_column($list_detail_pr, 'item_id');
	    	$list_no_pr_by_detail = array_column($list_detail_pr, 'no_pr','purchasereqdetail_id');
	    	$list_pr_detail_id = array_column($list_detail_pr, 'purchasereqdetail_id');

	    	$kontrak_supplier = KontrakSupplierItemView::find()
	    						->where(['IN','item_id',$list_item])
	    						->andWhere(['tipe'=>$tipe])
	    						->asArray()->all();
	    	$kontrakItem = $kontrakSupplier = [];
	    	$kontrakSupplier = ArrayHelper::index($kontrak_supplier, 'supplier_id');
	    	$kontrakItem = ArrayHelper::index($kontrak_supplier, 'item_id');

	    	$this->_kontrakItem = $kontrakItem;
    		$masterSupplier = Supplier::find()->asArray()->all();
    		$list_pajak_supplier = array_column($masterSupplier, 'pajak_id', 'supplier_id');
    		$masterPajak = Pajak::find()
    						->where(['is_active' => true])
    						->asArray()->all();
    		$list_pajak = array_column($masterPajak, 'pajak_persen', 'pajak_id');

    		$list_canceled_detail=[];
	    	foreach ($list_detail_pr as $k => $v) {
	    		if(isset($kontrakItem[$v['item_id']])){
		    		$list_detail_pr[$k]['kontrak_supplier_id'] = $kontrakItem[$v['item_id']]['supplier_id'];
		    		$list_detail_pr[$k]['kontrak'] = $kontrakItem[$v['item_id']];
		    	}else{
                    $list_detail_pr[$k]['kontrak_supplier_id'] = isset($v['defaultsupplier_id']) ? $v['defaultsupplier_id'] : 0;
                    $list_detail_pr[$k]['kontrak'] = null;
                    $kontrakNullExist = true;
                }
	    	}

	    	//map to supplier
	    	$purchasesBySupplier = $purchasesNoSupplier =[];
	    	foreach ($list_detail_pr as $_detail) {
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
                    $modelPO->is_consigment = isset($listItemPR[0]['is_consignment']) ? $listItemPR[0]['is_consignment'] : false;
                }else{
                    $modelPO = new ValidasiPoBarang;
                }
                $modelPO->supplier_id = $supplierId;
                $modelPO->payterm_id = isset($kontrakSupplier[$supplierId]) ? ArrayHelper::getValue($kontrakSupplier[$supplierId],'payterm_id',null) : null;
                $modelPO->pajak_id = isset($kontrakSupplier[$supplierId]) ? ArrayHelper::getValue($kontrakSupplier[$supplierId],'pajak_id',null) : (array_key_exists($supplierId,$list_pajak_supplier) ? $list_pajak_supplier[$supplierId] : null);
                $modelPO->additional_data = json_encode([
                    'kontrak' => isset($kontrakSupplier[$supplierId]) ? $kontrakSupplier[$supplierId] : null
                ]);

                /* notes:scenario */
                // $modelPO->tgl_validasi = $currentDate;

                /* notes: depend on detail */

                $modelPO->ruangan_id = isset($listItemPR[0]['no_pr']) ? $list_pr[array_search($listItemPR[0]['no_pr'],$list_no_pr)]['ruangan_id'] : Yii::$app->jwt->ruangan_id;
                $modelPO->pegawai_id =  Yii::$app->jwt->user->pegawai_id;
                $modelPO->diorder_oleh = Yii::$app->jwt->user->pegawai_id;
                $modelPO->is_cito = isset($listItemPR[0]['is_cito']) ? $listItemPR[0]['is_cito'] : false;
                $modelPO->is_admin = isset($listItemPR[0]['is_admin']) ? $listItemPR[0]['is_admin'] : false;
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
                    $_list_pajak_supplier = array_key_exists($supplierId,$list_pajak_supplier) ? $list_pajak_supplier[$supplierId] : null;
                    $_list = array_key_exists($_list_pajak_supplier,$list_pajak) ? $list_pajak[$_list_pajak_supplier] : null;
                    $po_ppn_persen = is_null($_list_pajak_supplier) ? 0 : $_list;
                }

                $po_total = 0;
                foreach ($listItemPR as $_detailItemPR) {
                    // cari satuan terbesar dari kontrak supplier/satuan konversi
                    if($_detailItemPR['kontrak'] != null){
                        $satuanbesar_id = $_detailItemPR['kontrak']['satuankonv1_id'];
                        $satuan_item = $this->controller->_skonversi[strtolower($tipe)][$_detailItemPR['kontrak']['item_id']];
                        $satuan_index = array_search($satuanbesar_id, array_column($satuan_item, 'satuanbesar_id'));
                        $satuan_terbesar = $satuan_item[$satuan_index];
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
                    $modelPOnoSupplier->is_consigment = isset($purchasesNoSupplier[0]['is_consignment']) ? $purchasesNoSupplier[0]['is_consignment'] : false;
                }else{
                    $modelPOnoSupplier = new ValidasiPoBarang;
                }
                $modelPOnoSupplier->supplier_id = null;
                $modelPOnoSupplier->payterm_id = null;
                $modelPOnoSupplier->pajak_id = null;
                $modelPOnoSupplier->additional_data = json_encode([
                    'kontrak' => null,
                    'pr' => null
                ]);
                
                /*notes: scenario*/
                // $modelPOnoSupplier->tgl_validasi = $currentDate;

                /*notes: depend on detail*/
				$modelPOnoSupplier->ruangan_id = isset($purchasesNoSupplier[0]['no_pr']) ? $list_pr[array_search($purchasesNoSupplier[0]['no_pr'],$list_no_pr)]['ruangan_id'] : Yii::$app->jwt->ruangan_id;
                $modelPOnoSupplier->pegawai_id = Yii::$app->jwt->user->pegawai_id;
                $modelPOnoSupplier->diorder_oleh = Yii::$app->jwt->user->pegawai_id;
                $modelPOnoSupplier->is_cito = isset($purchasesNoSupplier[0]['is_cito']) ? $purchasesNoSupplier[0]['is_cito'] : false;
                $modelPOnoSupplier->is_admin = isset($purchasesNoSupplier[0]['is_admin']) ? $purchasesNoSupplier[0]['is_admin'] : false;
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

                $detailPOnoSupplier = $generatedPOPRns = [];
                $po_ns_subtotal = 0;
                $po_ns_total_discount = 0;
                $po_ns_ppn_persen = 0;
                $po_ns_total = 0;

                foreach ($purchasesNoSupplier as $detailNoSupplier) {
                    $satuan_terbesar = $this->controller->findSatuanTerbesar($detailNoSupplier,$tipe);

                    $_harga = 0 * $satuan_terbesar['nilai_konversi'];
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
                            's_konversibrg_id' => $detailNoSupplier['satuankonversi_id'],
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

                $_table_name = 'validasipoobatdetail_t';
                $_table_header_id = 'validasipoobat_id';
                if(count($detailPOnoSupplier)>0){
                    if($tipe =='OBAT'){
                        ValidasiPoObatDetail::batchInsert($detailPOnoSupplier);
                        $_poIdNosup = array_column($detailPOnoSupplier, 'validasipoobat_id');
                        $podetail_key = 'validasipoobatdetail_id';
                        $prdetail_key = 'purchasereqdetail_id';
                    }else{
                        ValidasiPoBarangDetail::batchInsert($detailPOnoSupplier);
                        $_poIdNosup = array_column($detailPOnoSupplier, 'validasipobarang_id');
                        $podetail_key = 'validasipobarangdetail_id';
                        $prdetail_key = 'purchasereqbrgdetail_id';
                        $_table_name = 'validasipobarangdetail_t';
                        $_table_header_id = 'validasipobarang_id';
                    }

                    $inCondition = "(" . implode(",", array_unique($_poIdNosup)) . ")";
                    $command = $connection->createCommand("
                        SELECT
                            {$podetail_key} AS podetail_id,
                            {$prdetail_key} AS prdetail_id,
                            '{$tipe}' AS tipe,
                            1081 AS status
                        FROM {$_table_name} vt
                        WHERE {$_table_header_id} IN {$inCondition}
                        ");
                    $generatedPOPRns = $command->queryAll();

                    $commandLog = $connection->createCommand("
                        SELECT
                            {$podetail_key} as transaksi_id,
                            '{$currentDate}' as tgl,
                            'PODETAIL{$tipe}' as tipe,
                            '".DocoConstants::LA_AKSI_TAMBAH."' as aksi,
                            ".Yii::$app->user->identity->pegawai_id." as created_by
                        FROM {$_table_name} vt
                        WHERE {$_table_header_id} IN {$inCondition}
                        ");
                    $log_ns = $commandLog->queryAll();

	                if(count($generatedPOPRns)>0){
	                	GeneratedPO::batchInsert($generatedPOPRns);
	                }

	                if(count($log_ns)>0){
	                	LogActivityR::batchInsert($log_ns);
	                }
                }

            }

    		if($tipe == 'OBAT'){
                $objectPR =  new PurchaseRequisition;
                $objectPRdetail = new PurchaseRequisitionDetail;
                $detail_column = 'purchasereqdetail_id';
                $list_pr_detail_id = array_column($list_detail_pr, 'purchasereqdetail_id');
            }else{
                $objectPR =  new PurchaseRequisitionBarang;
                $objectPRdetail = new PurchaseRequisitionBarangDetail;
                $detail_column = 'purchasereqbrgdetail_id';
                $list_pr_detail_id = array_column($list_detail_pr, 'purchasereqdetail_id');
            }

    		$headerProcess = $objectPR::updateAll(['status'=>713],['IN','no_pr',$list_no_pr]);
    		$detailProcess = $objectPRdetail::updateAll(['status'=>713],['IN',$detail_column,$list_pr_detail_id]);
	    	
	    	/* notes:any throw could be skipped */
	    	$transaction->commit();

	    	return [
	    		'message' => 'Berhasil',
	    		'data' => [
	    			'processed' => $headerProcess,
	    			'skipped' => 0,//$list_canceled_detail,
	    			'numbers_of_item_processed' =>  $detailProcess
	    		]
	    	];
	    }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            Yii::error($e->getMessage());
	    	$transaction->rollback();
	    	return [
	    		'message' => $e->getMessage(),
				'line' => $e->getLine(),
				'file' => $e->getFile()
	    	];
	    }
    }

    protected function findTerbesar($obatalkes_id,$qty,$satuan)
    {
    	$_satuanBesar = $satuan;
    	
    	if(isset($this->_kontrakItem[$obatalkes_id]) && isset($this->_kontrakItem[$obatalkes_id]['satuankonv1_id'])){
    		$_satuanBesar = $this->_kontrakItem[$obatalkes_id]['satuankonv1_id'];
    		$_satuanKecil = $this->_kontrakItem[$obatalkes_id]['satuankecil_id'];
    		$findSatuan = SatuanKonversiView::find()->where([
    							'obatalkes_id'=>$obatalkes_id,
    							'satuankecil_id' => $_satuanKecil,
    							'satuanbesar_id' => $_satuanBesar
    						])->one();
    	} else {
    		$satuanDasar = $this->satuanDasar($obatalkes_id);
    		$findSatuan = $satuanDasar['findSatuan'];
    		$_satuanBesar = $satuanDasar['_satuanBesar'];
    	}

    	// this conditional block are used if obatalkes has kontrak supplier
    	// but satuankonversi_id is null
    	if($findSatuan['satuankonversi_id'] == null) {
    		$satuanDasar = $this->satuanDasar($obatalkes_id);
    		$findSatuan = $satuanDasar['findSatuan'];
    		$_satuanBesar = $satuanDasar['_satuanBesar'];
    	}

    	$_qtyBesar = !is_null($findSatuan) && isset($findSatuan['nilai_konversi']) && $qty>0 ? $qty / $findSatuan['nilai_konversi'] : $qty;

    	return [
    		'satuan_id' => $_satuanBesar,
    		'qty' => $_qtyBesar,
    		's_konversiobt_id' => $findSatuan['satuankonversi_id'],
    		'nilai_konversi' => $findSatuan['nilai_konversi']
    	];
    }

    protected function satuanDasar ($obatalkes_id) {
    	$findSatuan = SatuanKonversiView::find()
    		->where(['obatalkes_id'=>$obatalkes_id])
    		->orderBy(['nilai_konversi'=>SORT_DESC])
    		->one();
    	
    	if(!is_null($findSatuan)) {
    		$_satuanBesar = $findSatuan['satuanbesar_id'];
    	}

    	$data = ["findSatuan" => $findSatuan, "_satuanBesar" => $_satuanBesar];

    	return $data;
    }
}
