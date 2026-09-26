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
use app\modules\v1\models\InfoPurchaseRequisition;
use app\modules\v1\models\InfoPurchaseReqDetailView;
use app\modules\v1\models\KontrakSupplierView;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\ObatAlkes;
use Doco\models\SatuanKonversiView;

class ByPrAction extends Action {
    public function run() {
    	$nomor = Yii::$app->request->post('nomor','1');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
    	try{
	    	//find pr by nomor PR
	    	$pr = InfoPurchaseRequisition::find()
	    					->where(['no_pr'=>$nomor])->asArray()->one();
	    	$detail_pr = InfoPurchaseReqDetailView::find()
	    					->where(['no_pr'=>$nomor])->asArray()->all();
	    	if(is_null($detail_pr) || is_null($pr)) 
	    		throw new \Exception("Data PR tidak ditemukan", 1);

	    	if($pr['status'] == 713)
	    		throw new \Exception("Sudah Dibuat PO", 1);
	    		
	    	$list_obat = array_column($detail_pr, 'obatalkes_id');

	    	$kontrak_supplier = KontrakSupplierView::find()
	    						->where(['IN','obatalkes_id',$list_obat])
	    						->asArray()->all();
	    	$kontrakObat = $kontrakSupplier = [];
	    	foreach ($kontrak_supplier as $_kontrak) {
	    		$kontrakSupplier[$_kontrak['supplier_id']] = $_kontrak;
	    		$kontrakObat[$_kontrak['obatalkes_id']] = $_kontrak;
	    	}

	    	$masterObat = ObatAlkes::find()
    						->where(['IN','obatalkes_id',$list_obat])
    						->asArray()->all();
    		$masterObatSupplier = array_column($masterObat,'supplier_id','obatalkes_id');

	    	foreach ($detail_pr as $k => $v) {
	    		if(isset($kontrakObat[$v['obatalkes_id']])){
		    		$detail_pr[$k]['kontrak_supplier_id'] = $kontrakObat[$v['obatalkes_id']]['supplier_id'];
		    		$detail_pr[$k]['kontrak'] = $kontrakObat[$v['obatalkes_id']];
		    	}else{
		    		$detail_pr[$k]['kontrak_supplier_id'] = isset($masterObatSupplier[$v['obatalkes_id']]) ? $masterObatSupplier[$v['obatalkes_id']] : 0;
		    		$detail_pr[$k]['kontrak'] = null;
		    	}
	    	}
	    	$purchasesBySupplier =[];
	    	foreach ($detail_pr as $_detail) {
	    		$purchasesBySupplier[$_detail['kontrak_supplier_id']][] = $_detail;
	    	}

	    	$currentDate = date('Y-m-d H:i:s');
	    	$detailPO=[];

	    	foreach ($purchasesBySupplier as $supplierId => $listObat) {
	    		$modelValidasiPoObat = new ValidasiPoObat;
		    	// $modelValidasiPoObat->tgl_validasi = $currentDate;
		        $modelValidasiPoObat->ruangan_id = $pr['ruangan_id'];
		        $modelValidasiPoObat->pegawai_id = $pr['pegawai_id'];
		        $modelValidasiPoObat->supplier_id = $supplierId;
		        $modelValidasiPoObat->diorder_oleh = Yii::$app->jwt->user->pegawai_id;
		        $modelValidasiPoObat->payterm_id = isset($kontrakSupplier[$supplierId]) ? ArrayHelper::getValue($kontrakSupplier[$supplierId],'payterm_id',null) : null;
		        $modelValidasiPoObat->pajak_id = isset($kontrakSupplier[$supplierId]) ? ArrayHelper::getValue($kontrakSupplier[$supplierId],'pajak_id',null) : null;
		        $modelValidasiPoObat->additional_data = json_encode([
		        	'kontrak' => isset($kontrakSupplier[$supplierId]) ? $kontrakSupplier[$supplierId] : null
		        ]);
		        if(!$modelValidasiPoObat->save())
		        	throw new \Exception("Gagal Membuat PO", 1);
		        
		        $po_subtotal = 0;
				$po_total_discount = 0;
				$po_ppn_persen = isset($kontrakSupplier[$supplierId]) ? ArrayHelper::getValue($kontrakSupplier[$supplierId],'persen_ppn',0) : 0;
				$po_total = 0;
		        foreach ($listObat as $_detailObat) {
		        	$_harga = isset($_detailObat['kontrak']) ? ArrayHelper::getValue($_detailObat['kontrak'],'harga',0) : 0;
		        	$_diskon = isset($_detailObat['kontrak']) ? ArrayHelper::getValue($_detailObat['kontrak'],'diskon',0) : 0;
		        	$_diskonharga = ($_diskon / 100) * ($_detailObat['qty_konversi'] * $_harga);
		        	$_jumlahSetelahDiskon = (($_detailObat['qty_konversi'] * $_harga)) - $_diskonharga;

		        	if($_detailObat['satuankonversi_id'] == null) {
				        $_detailObat['satuankonversi_id'] = $this->findSatuanTerbesar($_detailObat);
		        	}

		        	$detailPO[] = [
		        		'validasipoobat_id' => $modelValidasiPoObat->getPrimaryKey(),
		        		'obatalkes_id' => $_detailObat['obatalkes_id'],
		        		'qty_po' => ceil($_detailObat['qty_konversi']),
		        		'qty_input' => ceil($_detailObat['qty_input']),
		        		's_konversiobt_id' => $_detailObat['satuankonversi_id'],
		        		'harga' => $_harga,
		        		'discount' => $_diskon,
		        		'discount_rp' => $_diskonharga,
		        		'jumlah' => $_jumlahSetelahDiskon,
		        		'additional_data' => json_encode([
		        			'purchasereq_id' => $_detailObat['purchasereq_id'],
		        			'purchasereqdetail_id' => $_detailObat['purchasereqdetail_id'],
		        			'no_pr' => $_detailObat['no_pr'],
		        			'qty_input' => $_detailObat['qty_input'],
		        			'satuaninput_id' => $_detailObat['satuan_id'],
		        			'qty_konversi' => $_detailObat['qty_konversi'],
		        			'satuankonversi_id' => $_detailObat['satuankonversi_id'],
		        			'catatan' => $_detailObat['catatan'],
		        			'kontrak' => @$_detailObat['kontrak']
		        		])
		        	];

		        	$po_subtotal += $_jumlahSetelahDiskon;
		        	$po_total_discount += $_diskonharga;
		        }

				$po_ppn_nilai = $po_subtotal * ($po_ppn_persen/100);
				$po_total = $po_subtotal + $po_ppn_nilai;

		        $modelValidasiPoObat->sub_total = $po_subtotal;
		        $modelValidasiPoObat->total_discount = $po_total_discount;
		        $modelValidasiPoObat->ppn_persen = $po_ppn_persen;
		        $modelValidasiPoObat->ppn_nilai = $po_ppn_nilai;
		        $modelValidasiPoObat->total = $po_total;
		        if(!$modelValidasiPoObat->update())
		        	throw new \Exception("Gagal Simpan Subtotal", 1);
		        	
	    	}
	    	if(count($detailPO)>0)
	    		ValidasiPoObatDetail::batchInsert($detailPO);

	    	$objPr = PurchaseRequisition::findOne($pr['purchasereq_id']);
	    	$objPr->status = 713;
	    	if(!$objPr->save())
	    		throw new \Exception("Gagal Mengubah status PR", 1);
	    	
	    	$transaction->commit();

	    	return [
	    		'message' => 'Berhasil',
	    		'data' => []
	    	];
	    }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            Yii::error($e->getMessage());
	    	$transaction->rollback();
	    	return [
	    		'message' => $e->getMessage()
	    	];
	    }
    }

    public function findSatuanTerbesar($_detailObat)
    {
        $nilai_konversi_terbesar = SatuanKonversiView::find()->where([
                            'obatalkes_id' => $_detailObat['obatalkes_id']
                            ])->max('nilai_konversi');
        $satuan_terbesar = SatuanKonversiView::find()->where([
                            'obatalkes_id' => $_detailObat['obatalkes_id'],
                            'nilai_konversi' => $nilai_konversi_terbesar
                            ])->asArray()->one();

        return $satuan_terbesar;
    }
}
