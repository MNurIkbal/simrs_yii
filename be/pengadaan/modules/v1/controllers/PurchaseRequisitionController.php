<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\Services\InternalService;

use app\modules\v1\models\PurchaseRequisition;
use app\modules\v1\models\PurchaseRequisitionDetail;
use app\modules\v1\models\payload\PurchaseRequisitionDetailPayload;
use app\modules\v1\models\PurchaseRequisitionBarang;
use app\modules\v1\models\PurchaseRequisitionBarangDetail;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\InfoPurchaseRequisition;
use app\modules\v1\models\InfoPurchaseReqDetailView;
use app\modules\v1\models\InfoPurchaseRequisitionGabung;
use app\modules\v1\models\InfoPurchaseReqGabungDetailView;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;
use app\modules\v1\models\KonfigFarmasi;

class PurchaseRequisitionController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PurchaseRequisition';

    const BELUM_PO = 712;
    const SUDAH_PO = 713;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-item"] = ["GET"];
        $verbs["cal-rekomendation"] = ["GET"];
        $verbs["ro-consignment"] = ["GET"];
        $verbs["get-stok-barang"] = ["GET"];
        $verbs["get-item-barang"] = ["GET"];
        $verbs["create"] = ["POST"];
        $verbs["get-bbject-data"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);

        $path = 'app\modules\v1\actions\PurchaseRequisition';

        $action = [
            'export-excel'      => $path . '\ExportExcelAction',
            'get-list-data'     => $path . '\GetListDataAction',
            'get-list-detail'   => $path . '\GetListDetailAction',
            'cancel-pr'         => $path . '\CancelPRAction',
            'edit-pr'           => $path . '\EditPRFillerAction',
            'get-obat'          => 'app\modules\v1\actions\Allow\GetMasterObatAction',
            'get-stok'          => $path . '\GetStockAction',
            'get-status-po'     => $path . '\GetStatusPoAction',
            'get-data-laporan'  => $path . '\LaporanAction',
            'update-pr'         => $path . '\UpdatePRAction',
            'pr-recommendation' => $path . '\PRRecommendationAction',
            'get-item'          => 'app\modules\v1\actions\Allow\GetItemAction',
            'pull-base-calc-ro' => $path . '\PullBaseCalcROAction',
            'cal-rekomendation' => $path . '\CalculationRekomendationAction',
            'approving-process' => $path . '\ApprovingProcessAction',
            'get-po-outstanding'=> $path . '\GetPoOutstandingAction',
            'get-pemakaian'     => $path . '\GetPemakaianAction',
            'ro-consignment' => $path . '\RecommendationConsignmentAction',
            'get-stok-barang' => $path . '\GetStockBarangAction',
            'get-item-barang' => 'app\modules\v1\actions\Allow\GetItemBarangAction',
            'ruangan-stok-obat' => $path . '\RuanganStokObatAction',
            'get-data-list-pemakaian' => $path . '\GetDataListPemakaianAction',
        ];
        $actions = array_merge($actions, $action);
        return $actions;
    }

    public function dateFilter($query, $request) {
        $advanced_filter = $request->get('advanced-filter');
        $dateKey = ['tgl_verifikasi', 'tanggal_pr'];
        foreach ($advanced_filter as $key => $value) {
            if(in_array($key, $dateKey)) {
                $explode = explode(" - ", $advanced_filter[$key]);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                $query->andWhere(['between', $key, $start, $end]);
            }
        }
    }

    public function actionCreate()
    {
    	$request = Yii::$app->request;
    	$pr = $request->post('pr',null);
        $type = ArrayHelper::getValue($pr, 'type');
        $is_consignment = ArrayHelper::getValue($pr, 'is_consignment', false);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try{
            if($type == "obat") {
                $model = new PurchaseRequisition;
                $model->is_consignment = $is_consignment;
            } else {
                $model = new PurchaseRequisitionBarang;
            }

	    	$model->tgl_pr = date('Y-m-d H:i:s');
	    	$model->pegawai_id = ArrayHelper::getValue($pr,'pegawaiID');
	    	$model->ruangan_id = ArrayHelper::getValue($pr,'ruanganID');
	    	$model->reference = ArrayHelper::getValue($pr,'reference');
	    	$model->status = DocoConstants::VAR_BELUM_APPROVED;
            $model->is_prcyto = ArrayHelper::getValue($pr,'cyto');
            $model->is_admin = ArrayHelper::getValue($pr, 'is_admin', false);
	    	if(!$model->save()) throw new \Exception("Error Processing Request", 1);

	    	$detail = ArrayHelper::getValue($pr,'detailItem');
	    	$items = [];
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
            $pkey = $model->getPrimaryKey();
	    	foreach ($detail as $_detail) {
                if($type == "obat") {             
                    $modelDetail = new PurchaseRequisitionDetailPayload;
                    $modelDetail->purchasereq_id = $pkey;
                    $modelDetail->obatalkes_id = ArrayHelper::getValue($_detail,'obatalkes_id');
                    $satuanKonversi = $this->getSatuanKonversi(
                        ArrayHelper::getValue($pr,'type'),
                        ArrayHelper::getValue($_detail,'obatalkes_id'),
                        ArrayHelper::getValue($_detail,'satuaninput_id'),
                        ArrayHelper::getValue($_detail,'qty'),
                        ArrayHelper::getValue($_detail,'nma_obat')
                    );
                    
                    $modelDetail->stok_gudang = empty($_detail['stok_gudang']) ? null : $this->setKonversiStok($_detail['stok_gudang'], $_detail, $satuanKonversi);
                    $modelDetail->stok_farmasi = empty($_detail['stok_farmasi']) ? null : $this->setKonversiStok($_detail['stok_farmasi'], $_detail, $satuanKonversi);
                    $modelDetail->stok_ruanganlain = empty($_detail['stok_lain']) ? null : $this->setKonversiStok($_detail['stok_lain'], $_detail, $satuanKonversi);
                    $modelDetail->last_7 = empty($_detail['last_7']) ? null : $_detail['last_7'];
                    $modelDetail->last_14 = empty($_detail['last_14']) ? null : $_detail['last_14'];
                    $modelDetail->last_30 = empty($_detail['last_30']) ? null : $_detail['last_30'];
                    $modelDetail->qty_outstanding = empty($_detail['qty_outstanding']) ? null : $this->setKonversiStok($_detail['qty_outstanding'], $_detail, $satuanKonversi);
                    $modelDetail->move_category_id = empty($_detail['move_category_id']) ? null : $_detail['move_category_id'];
                    unset($_detail['nilai_konversi']);
                } else {
                    \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'barang');
                    $modelDetail = new PurchaseRequisitionBarangDetail;
                    $modelDetail->purchasereqbrg_id = $model->getPrimaryKey();
                    $modelDetail->barang_id = ArrayHelper::getValue($_detail,'barang_id');
                    $satuanKonversi = $this->getSatuanKonversi(
                        ArrayHelper::getValue($pr,'type'),
                        ArrayHelper::getValue($_detail,'barang_id'),
                        ArrayHelper::getValue($_detail,'satuaninput_id'),
                        ArrayHelper::getValue($_detail,'qty'),
                        ArrayHelper::getValue($_detail,'barang_nama') 
                    );

                    $modelDetail->stok_gudang = empty($_detail['stok_gudang']) ? null : $this->setKonversiStok($_detail['stok_gudang'], $_detail, $satuanKonversi);
                    $modelDetail->stok_ruanganlain = empty($_detail['stok_lain']) ? null : $this->setKonversiStok($_detail['stok_lain'], $_detail, $satuanKonversi);
                    $modelDetail->last_7 = empty($_detail['last_7']) ? null : $_detail['last_7'];
                    $modelDetail->last_14 = empty($_detail['last_14']) ? null : $_detail['last_14'];
                    $modelDetail->last_30 = empty($_detail['last_30']) ? null : $_detail['last_30'];
                    $modelDetail->qty_outstanding = empty($_detail['qty_outstanding']) ? null : $this->setKonversiStok($_detail['qty_outstanding'], $_detail, $satuanKonversi);
                }

                // This arrIndex is a representation of values that listed in $_detail
                // Key are column name of the purchasereqdetail table
                $arrIndex = [
                    'satuan_id' => 'satuaninput_id',
                    'qty_input' => 'qty',
                    'qty_pr' => 'qty',
                    'qty_saatini' => 'stok_saatini',
                    'catatan' => 'catatan',
                    'doi' => 'doi',
                    'ssmin' => 'ss_min',
                    'qty_sugesstion' => 'qty_suggestion'
                ];

                foreach ($arrIndex as $key => $value) {
                    $modelDetail->$key = empty(ArrayHelper::getValue($_detail, $value)) ? null : ArrayHelper::getValue($_detail, $value);
                }
                $modelDetail->satuankonversi_id = ArrayHelper::getValue($satuanKonversi, 'satuankonversi_id');
	    		$modelDetail->qty_konversi = ArrayHelper::getValue($satuanKonversi, 'qty_konversi');
                $modelDetail->status = DocoConstants::VAR_BELUM_APPROVED;
	    		$modelDetail->additional_data = json_encode($_detail);
                
	    		$items[] = $modelDetail->attributes;
	    	}
            if($type == "obat") {
                $batchInsertItems = PurchaseRequisitionDetail::batchInsert($items);
            } else {
                $batchInsertItems = PurchaseRequisitionBarangDetail::batchInsert($items);
            }

	    	$transaction->commit();
            $data = [
                'ID' => $model->getPrimaryKey(),
                'itemsCount' => count($items)
            ];
            return $this->responseJson(200, 'success', $data);
	    }catch(\Exception $e){
            $this->logError($e);
	    	$transaction->rollback();
            return $this->responseJson(422, $e->getMessage());
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            $transaction->rollBack();
            return $this->responseJson(500, $e->getMessage());
        }
    }

    private function getSatuanKonversi($type, $obat_id, $satuan_id, $qty,$nama_obat)
    {
    	$cacheDuration = 60 * 60 * 12;

        if($type == "obat") {
            $listKonversi = $this->listKonversiObatCache($cacheDuration);
        } else {
            $listKonversi = $this->listKonversiBarangCache($cacheDuration);
        }

    	if(!isset($listKonversi[$obat_id][$satuan_id])) throw new \Exception("Satuan Konversi '".$nama_obat."' Tidak Ada", 1);

    	$qty_konversi = 0;
    	$satuankonversi_id = 0;
        $is_satuan_besar = false;
        
    	if($listKonversi[$obat_id][$satuan_id]['satuankecil_id'] == $satuan_id) {
    		$qty_konversi = $qty;
    		$satuankonversi_id = $listKonversi[$obat_id][$satuan_id]['satuankonversi_id'];
    	} else if($listKonversi[$obat_id][$satuan_id]['satuanbesar_id'] == $satuan_id) {
    		$nilai_konversi = $listKonversi[$obat_id][$satuan_id]['nilai_konversi'];
    		$qty_konversi = $qty * $nilai_konversi;
    		$satuankonversi_id =  $listKonversi[$obat_id][$satuan_id]['satuankonversi_id'];
            $is_satuan_besar = true;
    	}
		return [
			'qty_konversi' => $qty_konversi,
			'satuankonversi_id' => $satuankonversi_id,
            'is_satuan_besar' => $is_satuan_besar
		];
    }

    private function listKonversiObatCache($cacheDuration)
    {
        $cacheParam = Yii::$app->request->get('cache',null);
        if($cacheParam != null && $cacheParam == 0){
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
        }

        $listKonversiObat=[];
        $listKonversiObat = Yii::$app->cache->get('satuan-konversi-obat');
        if($listKonversiObat == false){
            $allSatuanObat = SatuanKonversiView::find()
                ->where(['is_active' => 't', 'jenis' => 'obat'])
                ->orderBy('satuan_besar')->asArray()->all();
            $listKonversiObat=[];

            foreach ($allSatuanObat as $_satuan) {
                $listKonversiObat[$_satuan['obatalkes_id']][$_satuan['satuanbesar_id']] = $_satuan;
            }

            Yii::$app->cache->set('satuan-konversi-obat',$listKonversiObat, $cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));
        }

        return $listKonversiObat;
    }

    private function listKonversiBarangCache($cacheDuration) {
        $cacheParam = Yii::$app->request->get('cache',null);
        if($cacheParam != null && $cacheParam == 0){
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'barang');
        }

        $listKonversiBarang = Yii::$app->cache->get('satuan-konversi-barang');
        if($listKonversiBarang == false) {
            $allSatuanBarang = SatuanKonversiView::find()
            ->where(['is_active' => 't', 'jenis' => 'barang'])
            ->orderBy('satuan_besar')->asArray()->all();
            $listKonversiBarang=[];

            foreach ($allSatuanBarang as $_satuan) {
                $listKonversiBarang[$_satuan['obatalkes_id']][$_satuan['satuanbesar_id']] = $_satuan;
            }

            Yii::$app->cache->set('satuan-konversi-barang', $listKonversiBarang, $cacheDuration, new \yii\caching\TagDependency(['tags'=>'barang']));
        }

        return $listKonversiBarang;
    }

    /**
     * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

    /**
    * @controller actionCetakDetailPdf
    * @attribute #judul# => Untuk menampilkan judul dokumen
    * @attribute #nomor_pr# => Untuk menampilkan nomor pr
    * @attribute #reference# => Untuk menampilkan reference
    * @attribute #nama_pegawai# => Untuk menampilkan nama pegawai
    * @attribute #instalasi# => Untuk menampilkan nama instalasi
    * @attribute #ruangan# => Untuk menampilkan nama ruangan
    * @attribute #tanggal_pr# => Untuk menampilkan tanggal pembuatan pr
    * @attribute #tanggal_cetak# => Untuk menampilkan tanggal cetak
    * @attribute #dibuat_oleh# => Untuk menampilkan dibuat oleh
    * @attribute #diperiksa_oleh# => Untuk menampilkan diperiksa
    * @attribute #disetujui_oleh# => Untuk menampilkan disetujui
    * @attribute #datatable# => Untuk menampilkan tabel
    */

    public function actionCetakDetailPdf($id,$type)
    {
        try {
            $type = strtoupper($type);
            $header = InfoPurchaseRequisitionGabung::find()
                ->where(['purchasereq_id' => $id,'tipe'=>$type])
                ->one();

            $details = InfoPurchaseReqGabungDetailView::find()
                ->where(['purchasereq_id' => $id,'tipe'=>$type])
                ->orderBy(['item_nama' => SORT_ASC])
                ->all();

            $content = [];
            $number = 1;

            foreach ($details as $key => $value) {

                $qty_input = "0 ".$value['satuan'];
                $qty_konv = "0 ".$value['satuan_konversi'];
                if($value['qty_input'] != 0 || $value['qty_konversi'] != 0){
                    if(($value['qty_input'] / $value['qty_konversi']) < 1) {
                        $qty_input = ($value['qty_input'] / $value['qty_input']) . " " . $value['satuan'];
                        $qty_konv  = ($value['qty_konversi'] / $value['qty_input']) . " " . $value['satuan_konversi'];
                    } else {
                        $qty_input = ($value['qty_input'] / $value['qty_konversi']) . " " . $value['satuan'];
                        $qty_konv  = ($value['qty_konversi'] / $value['qty_konversi']) . " " . $value['satuan_konversi'];
                    }
                }

                $doi = is_null($value['doi']) ? '-' : $value['doi'];
                $ssmin = is_null($value['ssmin']) ? '-' : number_format((int)$value['ssmin'], 0, ",", ".") . " " . $value['satuan_stok'];
                $sugesstion = is_null($value['qty_sugesstion']) ? '-' : number_format((int)$value['qty_sugesstion'], 0, ",", ".");

                $content[$number - 1] = [
                    'no'                => $number,
                    'kode'              => $value['item_kode'],
                    'nama'              => $value['item_nama'],
                    'satuan'            => $value['satuan'],
                    'konversi'          => $qty_input . " - " . $qty_konv,
                    'doi'               => $doi,
                    'ssmin'             => $ssmin,
                    'stok_sistem'       => number_format((int)$value['stok'], 0, ",", "."),
                    'stok_gudang'       => number_format((int)$value['stok_gudang'], 0, ",", "."),
                    'stok_farmasi'      => number_format((int)$value['stok_farmasi'], 0, ",", "."),
                    'stok_ruanganlain'  => number_format((int)$value['stok_ruanganlain'], 0, ",", "."),
                    'qty_sugesstion'    => $sugesstion,
                    'qty_pr'            => number_format((int)$value['qty_pr'], 0, ",", "."),
                    'qty_final'         => number_format((int)$value['qty_input'], 0, ",", "."),
                    'catatan'           => $value['catatan'],
                    'status'            => $value['status'],
                    'alasan_batal'      => $value['alasan'],
                    'last_7'            => number_format((int)$value['last_7'], 0, ",", "."),
                    'last_14'           => number_format((int)$value['last_14'], 0, ",", "."),
                    'last_30'           => number_format((int)$value['last_30'], 0, ",", "."),
                    'qty_outstanding'   => number_format((int)$value['qty_outstanding'], 0, ",", ".")
                ];

                $number++;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#judul#'           => "Purchase Requisition",
                '#nomor_pr#'        => $header['no_pr'],
                '#reference#'       => $header['reference'],
                '#nama_pegawai#'    => $header['pegawai'],
                '#ruangan_nama#'    => $header['ruangan'],
                '#tanggal_pr#'      => date('d-m-Y H:i:s', strtotime($header['created_date'])),
                '#tanggal_cetak#'   => date("d-m-Y H:i:s"),
                '#datatable#'       => $this->renderPartial('_detail', [
                    'content' => $content,
                ]),
            ];
            $print->Output();
        }catch (\Yii\db\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            $this->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetObjectData($id, $type, $order) {
        try {
            $type = strtoupper($type);
            $header = InfoPurchaseRequisitionGabung::find()
                ->where(['purchasereq_id' => $id,'tipe'=>$type])
                ->one();

            if ($header['created_date'] != null) {
                $header['created_date'] = date(
                    'd-m-Y H:i:s',
                    strtotime($header['created_date'])
                );
            }

            if ($header['tgl_approve'] != null) {
                $header['tgl_approve'] = date(
                    'd-m-Y H:i:s',
                    strtotime($header['tgl_approve'])
                );
            }

            if(!empty($order)) {
                if($order == 'sorting_asc') {
                    $cond_order = ['item_nama' => SORT_ASC];
                } else {
                    $cond_order = ['item_nama' => SORT_DESC];
                }
            } else {
                $cond_order = ['item_nama' => SORT_ASC];
            }

            $details = InfoPurchaseReqGabungDetailView::find()
                ->where(['purchasereq_id' => $id,'tipe'=>$type])
                ->orderBy($cond_order)
                ->all();

            $content = [];
            $number = 1;
            foreach ($details as $key => $value) {
                $qty_input = "0 ".$value['satuan'];
                $qty_konv = "0 ".$value['satuan_konversi'];
                
                if($value['qty_input'] != 0 || $value['qty_konversi'] != 0){
                    if(($value['qty_input'] / $value['qty_konversi']) < 1) {
                        $qty_input = ($value['qty_input'] / $value['qty_input']) . " " . $value['satuan'];
                        $qty_konv  = ($value['qty_konversi'] / $value['qty_input']) . " " . $value['satuan_konversi'];
                    } else {
                        $qty_input = ($value['qty_input'] / $value['qty_konversi']) . " " . $value['satuan'];
                        $qty_konv  = ($value['qty_konversi'] / $value['qty_konversi']) . " " . $value['satuan_konversi'];
                    }
                }

                $doi = is_null($value['doi']) ? '-' : $value['doi'];
                $ssmin = is_null($value['ssmin']) ? '-' : $this->castingNumber($value, $value['ssmin']);
                $sugesstion = is_null($value['qty_sugesstion']) ? '-' : number_format((int)$value['qty_sugesstion'], 0, ",", ".");

                $content[$number - 1] = [
                    'no'                => $number,
                    'kode'              => $value['item_kode'],
                    'nama'              => $value['item_nama'],
                    'satuan'            => $value['satuan'],
                    'konversi'          => $value['uom_last'],
                    'doi'               => $doi,
                    'ssmin'             => $ssmin,
                    'stok_sistem'       => number_format((int)$value['stok'], 0, ",", "."),
                    'stok_gudang'       => $this->castingNumber($value, $value['stok_gudang']),
                    'stok_farmasi'      => $this->castingNumber($value, $value['stok_farmasi']),
                    'stok_ruanganlain'  => $this->castingNumber($value, $value['stok_ruanganlain']),
                    'qty_sugesstion'    => $this->castingNumber($value, $value['qty_sugesstion']),
                    'qty_pr'            => DocoHelpers::formatNumber($value['qty_pr']),
                    'qty_final'         => DocoHelpers::formatNumber($value['qty_input']),
                    'catatan'           => $value['catatan'],
                    'status'            => $value['status'],
                    'alasan_batal'      => $value['alasan'],
                    'last_7'            => number_format((int)$value['last_7'], 0, ",", "."),
                    'last_14'           => number_format((int)$value['last_14'], 0, ",", "."),
                    'last_30'           => number_format((int)$value['last_30'], 0, ",", "."),
                    'qty_outstanding'   => $this->castingNumber($value, $value['qty_outstanding']),
                    'status'            => $value['status'],
                    'nomor_po'          => isset($value['nomor_po']) ? $value['nomor_po'] : '-',
                    'criteria' => is_null($value['criteria']) ? ' - ' : $value['criteria']
                ];

                $number++;
            }

            $attributes = [
                '#judul#'           => "Purchase Requisition",
                '#nomor_pr#'        => $header['no_pr'],
                '#reference#'       => $header['reference'],
                '#nama_pegawai#'    => $header['pegawai'],
                '#ruangan_nama#'    => $header['ruangan'],
                '#jenis_pr#'        => $header['pr_cyto'],
                '#admin_pr#'        => $header['pr_admin'],
                '#tanggal_pr#'      => ArrayHelper::getValue($header, 'created_date', '-'),
                '#tanggal_approve#' => ArrayHelper::getValue($header, 'tgl_approve', '-'),
                '#tanggal_cetak#'   => date("d-m-Y H:i:s"),
                '#jenis_pr#' => ArrayHelper::getValue($header, 'pr_cyto'),
                '#admin_pr#' => ArrayHelper::getValue($header, 'pr_admin'),
                '#pr_consignment#' => ArrayHelper::getValue($header, 'pr_consignment', '-'),
                '#datatable#'       => $this->renderPartial('_detail', [
                    'content' => $content,
                ]),
            ];
            
            return [
                'attributes' => $attributes
            ];
        } catch (\Yii\db\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            $this->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionSyncPdf() {
        $request = Yii::$app->request;
        $getData = $request->get();
        $id = $getData['params']['id'];
        $type = $getData['params']['type'];
        $order = $getData['params']['order'];
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 20;

        if(!empty($order)) {
            if($order == 'sorting_asc') {
                $cond_order = ['item_nama' => SORT_ASC];
            } else {
                $cond_order = ['item_nama' => SORT_DESC];
            }
        } else {
            $cond_order = ['item_nama' => SORT_ASC];
        }

        $details = InfoPurchaseReqGabungDetailView::find()
                ->where(['purchasereq_id' => $id, 'tipe' => $type])
                ->orderBy($cond_order)
                ->all();
        $countData = count($details);

        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData / $fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [
                'DetailPrPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => [
                        'id' => $id,
                        'type' => $type,
                        'order' => $order
                    ]
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'CetakDetailPrPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'type' => $type
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadDetailPrPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionSendFile() {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        $model = new UploadPayload;
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;
            $path = 'uploads/' . $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $nameFile = $path . '/' . $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'Upload File Berhasil'
                ];
            }
        }
    }

    public function actionDownloadPdf() {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.pdf';
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            die();
        }
    }

    public function castingNumber($item, $value) {
        $value = isset($value) ? $value : 0;

        if ($item['nilai_konversi'] > 1) {
            $value = $value / $item['nilai_konversi'];
        }

        return DocoHelpers::formatNumber($value);
    }

    public function setKonversiStok($stok, $_detail, $satuanKonversi) 
    {
        $konversi = isset($_detail['nilai_konversi']) ? $_detail['nilai_konversi'] : 1;
        $is_satuan_besar = ArrayHelper::getValue($satuanKonversi,'is_satuan_besar');
        
        return $is_satuan_besar ? ($stok * $konversi) : $stok;
    }
}
