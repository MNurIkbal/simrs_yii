<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\InfoPoDetailView;
use app\modules\v1\models\PurchaseRequisitionDetail;
use app\modules\v1\models\InfoPurchaseReqDetailView;
use app\modules\v1\models\InfoPurchaseReqBarangDetailView;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\KontrakSupplierView;
use app\modules\v1\models\KontrakSupplierBarangView;
use app\modules\v1\models\KontrakSupplierItemView;
use app\modules\v1\models\PurchaseRequisition;
use app\modules\v1\models\PurchaseRequisitionBarang;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Barang;
use app\modules\v1\models\GeneratedPO;
use app\modules\v1\models\LogActivityR;

class SaveSplitPOAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $validasipodetail_id = $request->post('po_detail');
        $pr_nomor = $request->post('pr_nomor');
        $details = $request->post('detail');
        $type_po = $request->post('type_po');
        $item_id = $request->post('item_id');
        $currentDate = date("Y-m-d H:i:s");
        $split_qty = 0;

        try{
            if($type_po == DocoConstants::JENIS_OBAT) {
                $data_purchase = $this->dataPurchaseObat($validasipodetail_id, $pr_nomor, $item_id);
            } else {
                $data_purchase = $this->dataPurchaseBarang($validasipodetail_id, $pr_nomor, $item_id);
            }

            $validasipoitemdetail   = $data_purchase['validasipoitemdetail'];
            $purchasereqdetail      = $data_purchase['purchasereqdetail'];
            $purchasereq            = $data_purchase['purchasereq'];
            $masterItem             = $data_purchase['master_item'];

            $masterPajak = Pajak::find()->where(['is_active' => true])->asArray()->all();
            $list_pajak = ArrayHelper::map($masterPajak, 'pajak_id', 'pajak_persen');

        	/* 
                Proses penyematan informasi supplier pada data detail.
                Nantinya pada detail akan memiliki property: has_supp_contract,
                dimana nilai nya berupa true/false sebagai penanda apakah data detail memiliki supplier atau tidak.
            */

        	foreach ($details as $key => $value) {
                $supplier = supplier::find()
                    ->where(["supplier_id" => $value["supplier_id"]])
                    ->one();

                $kontrak_supplier = KontrakSupplierItemView::find()
                        ->where(["supplier_id" => $value["supplier_id"]])
                        ->andWhere(["item_id" => $item_id])
                        ->andWhere(["tipe" => strtoupper($type_po)])
                        ->one();
        		
        		if($kontrak_supplier != []) {
        			$details[$key]['has_supp_contract'] = true;
                    $details[$key]['supplier_data'] = $supplier;
        			$details[$key]['kontrak_supplier'] = $kontrak_supplier;
        		} else {
        			$details[$key]['has_supp_contract'] = false;
                    $details[$key]['supplier_data'] = $supplier;
        		}
        	}

            $detailPO = [];

    		foreach ($details as $detail) {
                if($type_po == DocoConstants::JENIS_OBAT) {
                    $modelValidasiPoItem = new ValidasiPoObat;
                } else {
                    $modelValidasiPoItem = new ValidasiPoBarang;
                }

        		$modelValidasiPoItem->tgl_validasi = $currentDate;
        		$modelValidasiPoItem->ruangan_id = $purchasereq['ruangan_id'];
				$modelValidasiPoItem->pegawai_id = $purchasereq['pegawai_id'];
				$modelValidasiPoItem->diorder_oleh = $purchasereq['pegawai_id'];
				$modelValidasiPoItem->supplier_id = $detail['supplier_id'];
                $modelValidasiPoItem->payterm_id = null;
                $modelValidasiPoItem->pajak_id = null;
                $modelValidasiPoItem->additional_data = json_encode([
                    'kontrak' => null,
                    'pr' => $purchasereq
                ]);

                if($detail['has_supp_contract']) {
					$modelValidasiPoItem->payterm_id = $detail['kontrak_supplier']->payterm_id;
					$modelValidasiPoItem->pajak_id = $detail['kontrak_supplier']->pajak_id;
					$modelValidasiPoItem->additional_data = json_encode([
						'kontrak' => $detail['supplier_id']
					]);

					$satuanbesar_id = $detail['kontrak_supplier']['satuankonv1_id'];

                    $satuan_terbesar = $this->findSatuanTerbesar(
                        $type_po, 
                        $item_id,
                        $detail['kontrak_supplier']['satuankonv1_id']
                    );

                    if(is_null($satuan_terbesar)) {                    
                        throw new \Exception("Satuan konversi pada kontrak supplier tidak sesuai.", 1);
                    }

                    // convert qty ke satuan yang akan digunakan
                    $detail['qty_konversi'] = $this->convertQty(
                                                $type_po,
                                                $item_id, 
                                                $detail['qty'],
                                                $detail['satuan_id'],
                                                $satuan_terbesar['nilai_konversi']
                                            );

                    $harga = $detail['kontrak_supplier']['harga'];
	                $diskon = ArrayHelper::getValue($detail['kontrak_supplier'], 'diskon' ,0);
	                $diskonharga = ($diskon / 100) * ($detail['qty_konversi'] * $harga);
	                $jumlahSetelahDiskon = (($detail['qty_konversi'] * $harga)) - $diskonharga;
	                $qty_po = $validasipoitemdetail['qty_po'];

	                $additional_data = json_encode([
                        'purchasereq_id' => $purchasereq['purchasereq_id'],
                        'purchasereqdetail_id' => $purchasereqdetail['purchasereqdetail_id'],
                        'no_pr' => $purchasereq['no_pr'],
                        'qty_input' => $detail['qty_konversi'],
                        'qty_po' => ceil($qty_po),
                        'satuaninput_id' => $purchasereqdetail['satuan_id'],
                        'qty_konversi' => $purchasereqdetail['qty_konversi'],
                        'satuankonversi_id' => $purchasereqdetail['satuankonversi_id'],
                        'catatan' => $purchasereqdetail['catatan'],
                        'kontrak' => @$detail['kontrak_supplier']
                    ]);

                    $po_ppn_persen = $detail['kontrak_supplier']->persen_ppn;
                } else {
                    $pajak_id = $detail['supplier_data']->pajak_id;

					$modelValidasiPoItem->payterm_id = null;
					$modelValidasiPoItem->pajak_id = $pajak_id;
					$modelValidasiPoItem->additional_data = json_encode([
	                    'kontrak' => null,
	                    'pr' => $purchasereq
	                ]);

                	$satuan_terbesar = $this->findSatuanTerbesar(
                        $type_po, 
                        $item_id
                    );

                    $detail['qty_konversi'] = $this->convertQty(
                                                $type_po,
                                                $item_id,
                                                $detail['qty'],
                                                $detail['satuan_id'],
                                                $satuan_terbesar['nilai_konversi']
                                            );

                    $harga = 0;
	                $diskon = 0;
	                $diskonharga = 0;
	                $jumlahSetelahDiskon = (ceil($detail['qty_konversi']) * $harga) - $diskonharga;
	                $qty_po = $validasipoitemdetail['qty_po'];

	                $additional_data = null;

	                $po_ppn_persen = is_null($pajak_id) ? 0 : $list_pajak[$pajak_id];
                }

                /* Proses menyimpan transaksi header */

                if(!$modelValidasiPoItem->save()) {
                    throw new \Exception("Gagal Membuat PO", 1);
                }

                if($type_po == DocoConstants::JENIS_OBAT) {
                    $detailPO[] = [
                        'validasipoobat_id' => $modelValidasiPoItem->getPrimaryKey(),
                        'obatalkes_id' => $item_id,
                        'qty_po' => ceil($qty_po),
                        'qty_input' => ceil($detail['qty_konversi']),
                        's_konversiobt_id' => $satuan_terbesar['satuankonversi_id'],
                        'harga' => $harga,
                        'discount' => $diskon,
                        'discount_rp' => $diskonharga,
                        'jumlah' => $jumlahSetelahDiskon,
                        'purchasereqdetail_id' => $validasipoitemdetail['purchasereqdetail_id'],
                        'additional_data' => $additional_data
                    ];
                } else {
                    $detailPO[] = [
                        'validasipobarang_id' => $modelValidasiPoItem->getPrimaryKey(),
                        'barang_id' => $item_id,
                        'qty_po' => ceil($qty_po),
                        'qty_input' => ceil($detail['qty_konversi']),
                        's_konversibrg_id' => $satuan_terbesar['satuankonversi_id'],
                        'harga' => $harga,
                        'discount' => $diskon,
                        'discount_rp' => $diskonharga,
                        'jumlah' => $jumlahSetelahDiskon,
                        'purchasereqbrgdetail_id' => $validasipoitemdetail['purchasereqbrgdetail_id'],
                        'additional_data' => $additional_data
                    ];
                }
                
                $ppn_nilai = $jumlahSetelahDiskon * ($po_ppn_persen / 100);
                $total = $jumlahSetelahDiskon + $ppn_nilai;

                $modelValidasiPoItem->sub_total = $jumlahSetelahDiskon;
                $modelValidasiPoItem->total_discount = $diskonharga;
                $modelValidasiPoItem->ppn_persen = $po_ppn_persen;
                $modelValidasiPoItem->ppn_nilai = $ppn_nilai;
                $modelValidasiPoItem->total = $total;

                $split_qty += $detail['qty'];

                if(!$modelValidasiPoItem->update()) {
                    throw new \Exception("Gagal Simpan Subtotal", 1);
                }
    		}

            /* Proses menyimpan transaksi detail */

            if(count($detailPO) > 0) {
                if($type_po == DocoConstants::JENIS_OBAT) {
                    ValidasiPoObatDetail::batchInsert($detailPO);
                    $modelValidasiPoItemDetail = ValidasiPoObatDetail::findOne($validasipodetail_id);

                    $_poIds = array_column($detailPO, 'validasipoobat_id');
                    $_gDetailPO = ValidasiPoObatDetail::find()
                                    ->where(['validasipoobat_id' => $_poIds])
                                    ->asArray()
                                    ->all();
                } else {
                    ValidasiPoBarangDetail::batchInsert($detailPO);
                    $modelValidasiPoItemDetail = ValidasiPoBarangDetail::findOne($validasipodetail_id);

                    $_poIds = array_column($detailPO, 'validasipobarang_id');
                    $_gDetailPO = ValidasiPoBarangDetail::find()
                                    ->where(['validasipobarang_id' => $_poIds])
                                    ->asArray()
                                    ->all();
                }

                /* Proses pencatatan transaksi pada log */

                $generatedPOPR = $logPoDetail = [];
                $currentDate = date('Y-m-d H:i:s');

                foreach ($_gDetailPO as $value) {
                    if($type_po == DocoConstants::JENIS_OBAT) {
                        $prdetail_id = $value['purchasereqdetail_id'];
                        $podetail_id = $value['validasipoobatdetail_id'];
                    } else {
                        $prdetail_id = $value['purchasereqbrgdetail_id'];
                        $podetail_id = $value['validasipobarangdetail_id'];
                    }

                    $generatedPOPR[] = [
                        'tipe' => strtoupper($type_po),
                        'prdetail_id' => $prdetail_id,
                        'podetail_id' => $podetail_id,
                        'status' => 1018
                    ];

                    $logPoDetail[] = [
                        'transaksi_id' => $podetail_id,
                        'tgl' => $currentDate,
                        'tipe' => 'PODETAIL'.strtoupper($type_po),
                        'aksi' => DocoConstants::LA_AKSI_TAMBAH,
                        'keterangan' => 'POALIHSUPPLIER',
                        'alasan' => null,
                        'additional_detail' => null,
                        'created_by' => Yii::$app->user->identity->pegawai_id
                    ];
                }

                if(count($generatedPOPR) > 0){
                    GeneratedPO::batchInsert($generatedPOPR);
                }

                if(count($logPoDetail) > 0){
                    LogActivityR::batchInsert($logPoDetail);
                }
            }

            /* Proses update detail PO asal (Origin). */
    		
            $total = $split_qty * $validasipoitemdetail['harga'];
            $total_discount_deduction = ($validasipoitemdetail['discount'] / 100) * $total;
            $total_deduction = $total - $total_discount_deduction;

    		if($validasipoitemdetail['qty_input'] == $split_qty) {
                /* 
                    Hapus detail PO jika: 
                    qty input split PO sama dengan qty detail PO asal. 
                */

                if(!$modelValidasiPoItemDetail->delete()) {
                    throw new \Exception("Gagal Memperbaharui PO", 1);
                }
    		} else {
    			$new_qty_po = $validasipoitemdetail['qty_input'] - $split_qty;

    			$modelValidasiPoItemDetail->qty_input = $new_qty_po;
                $modelValidasiPoItemDetail->discount_rp = $validasipoitemdetail['discount_rp'] - $total_discount_deduction;
                // full price without discount
                $modelValidasiPoItemDetail->jumlah = $validasipoitemdetail['jumlah'] - $total; 

    			if(!$modelValidasiPoItemDetail->update()) {
                    throw new \Exception("Gagal Memperbaharui PO", 1);
                }
    		}

            if($type_po == DocoConstants::JENIS_OBAT) {
                $validasi_po_item = ValidasiPoObat::find()
                    ->where(["validasipoobat_id" => $validasipoitemdetail['validasipoobat_id']])
                    ->asArray()
                    ->one();

                $new_sub_total = $validasi_po_item['sub_total'] - $total_deduction; // full price without discount
                $new_ppn_nilai = ($validasi_po_item['ppn_persen'] / 100) * $new_sub_total;
                $new_total_discount = $validasi_po_item['total_discount'] - $total_discount_deduction;

                $modelValidasiPoItemOrigin = ValidasiPoObat::findOne($validasipoitemdetail['validasipoobat_id']);
                $modelValidasiPoItemOrigin->sub_total = $new_sub_total;
                $modelValidasiPoItemOrigin->ppn_nilai = $new_ppn_nilai;
                $modelValidasiPoItemOrigin->total_discount = $new_total_discount;
                $modelValidasiPoItemOrigin->total = $new_sub_total + $new_ppn_nilai;
            } else {
                $validasi_po_item = ValidasiPoBarang::find()
                    ->where(["validasipobarang_id" => $validasipoitemdetail['validasipobarang_id']])
                    ->asArray()
                    ->one();

                $new_sub_total = $validasi_po_item['sub_total'] - $total; // full price without discount
                $new_ppn_nilai = ($validasi_po_item['ppn_persen'] / 100) * ($new_sub_total - $total_discount_deduction);
                $new_total_discount = $validasi_po_item['total_discount'] - $total_discount_deduction;

                $modelValidasiPoItemOrigin = ValidasiPoBarang::findOne($validasipoitemdetail['validasipobarang_id']);
                $modelValidasiPoItemOrigin->sub_total = $new_sub_total;
                $modelValidasiPoItemOrigin->ppn_nilai = $new_ppn_nilai;
                $modelValidasiPoItemOrigin->total_discount = $new_total_discount;
                $modelValidasiPoItemOrigin->total = ($new_sub_total - $total_discount_deduction) + $new_ppn_nilai;
            }

            if(!$modelValidasiPoItemOrigin->update()) {
                throw new \Exception("Gagal Memperbaharui PO Asal", 1);
            }

            $transaction->commit();

    		$statusCode = 200;
            $message = "Data berhasil disimpan";

            return $this->controller->responseJson($statusCode, $message);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->controller->responseJson(422, $e->getMessage(), ['message' => $e->getMessage(), 'line'=>$e->getLine(), 'file' => $e->getFile()]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->controller->responseJson(500, 'Terjadi kesalahan pada sistem.');
        }
    }

    public function dataPurchaseObat($validasipodetail_id, $pr_nomor, $item_id) {
        /* 
            Perubahan pengambilan data berdasarkan nomor pr
            selanjutnya cari purchasereqdetail_id
            dan cari po detail menggunakan validasipodetail_id dan purchasereqdetail_id.

            Perubahan dilakukan karena pada detail po bisa terdapat lebih dari satu obat yang sama,
            bisa dibedakan dari purchasereqdetail_id.
        */

        $purchasereq = PurchaseRequisition::find()
            ->where(['no_pr' => $pr_nomor])
            ->asArray()
            ->one();

        $purchasereqdetail = InfoPurchaseReqDetailView::find()
            ->where([
                "purchasereq_id" => $purchasereq['purchasereq_id'],
                "obatalkes_id" => $item_id
            ])
            ->asArray()
            ->one();

        $validasipoitemdetail = ValidasiPoObatDetail::find()
                ->where([
                    "validasipoobatdetail_id" => $validasipodetail_id,
                    "purchasereqdetail_id" => $purchasereqdetail['purchasereqdetail_id']
                ])
                ->asArray()
                ->one();

        $master_item = ObatAlkes::find()
            ->where(['obatalkes_id' => $item_id])
            ->asArray()
            ->one();

        return [
            'validasipoitemdetail' => $validasipoitemdetail,
            'purchasereqdetail' => $purchasereqdetail,
            'purchasereq' => $purchasereq,
            'master_item' => $master_item
        ];
    }

    public function dataPurchaseBarang($validasipodetail_id, $pr_nomor, $item_id) {
        /* 
            Perubahan pengambilan data berdasarkan nomor pr
            selanjutnya cari purchasereqbrgdetail_id
            dan cari po detail menggunakan validasipobarangdetail_id dan purchasereqbrgdetail_id.

            Perubahan dilakukan karena pada detail po bisa terdapat lebih dari satu barang yang sama,
            bisa dibedakan dari purchasereqbrgdetail_id.
        */

        $purchasereq = PurchaseRequisitionBarang::find()
            ->where(['no_pr' => $pr_nomor])
            ->asArray()
            ->one();

        $purchasereqdetail = InfoPurchaseReqBarangDetailView::find()
            ->where([
                "purchasereqbrg_id" => $purchasereq['purchasereqbrg_id'],
                "barang_id" => $item_id
            ])
            ->asArray()
            ->one();

        $validasipoitemdetail = ValidasiPoBarangDetail::find()
                ->where([
                    "validasipobarangdetail_id" => $validasipodetail_id,
                    "purchasereqbrgdetail_id" => $purchasereqdetail['purchasereqbrgdetail_id']
                ])
                ->asArray()
                ->one();

        $master_item = Barang::find()
            ->where(['barang_id' => $item_id])
            ->asArray()
            ->one();

        if($master_item != []) {
            $master_item['harganetto'] = $master_item['barang_harganetto'];
        }

        return [
            'validasipoitemdetail' => $validasipoitemdetail,
            'purchasereqdetail' => $purchasereqdetail,
            'purchasereq' => $purchasereq,
            'master_item' => $master_item
        ];
    }

    public function findSatuanTerbesar($type_po, $item_id, $satuanbesar_id = null)
    {
        $nilai_konversi_terbesar = SatuanKonversiView::find()
                ->where(['obatalkes_id' => $item_id])
                ->andWhere(['jenis' => strtolower($type_po)])
                ->max('nilai_konversi');
        
        if($satuanbesar_id == null) {
            $satuan_terbesar = SatuanKonversiView::find()
                ->where([
                    'obatalkes_id' => $item_id,
                    'nilai_konversi' => $nilai_konversi_terbesar
                ])
                ->andWhere(['jenis' => strtolower($type_po)])
                ->asArray()
                ->one();
        } else {
            $satuan_terbesar = SatuanKonversiView::find()
                ->where([
                    'obatalkes_id' => $item_id,
                    'satuanbesar_id' => $satuanbesar_id
                ])
                ->andWhere(['jenis' => strtolower($type_po)])
                ->asArray()
                ->one();
        }

        return $satuan_terbesar;
    }

    public function nilaiKonversi($type_po, $item_id, $satuan) 
    {
        $nilai_konversi = SatuanKonversiView::find()
            ->where([
                'obatalkes_id' => $item_id, 
                'satuanbesar_id' => $satuan
            ])
            ->andWhere(['jenis' => strtolower($type_po)])
            ->asArray()
            ->one();

        return $nilai_konversi;
    }

    // $satuan_asal adalah satuan id yang dikirim dari fe.
    // $nilai_konversi_po adalah nilai konversi dari satuan yang akan digunakan untuk po baru.
    public function convertQty($type_po, $item_id, $qty_asal, $satuan_asal, $nilai_konversi_po) 
    {
        $nilai_konversi_asal = $this->nilaiKonversi($type_po, $item_id, $satuan_asal);
        $qty_konversi = $qty_asal / ($nilai_konversi_po / $nilai_konversi_asal['nilai_konversi']);

        return $qty_konversi;
    }
}
