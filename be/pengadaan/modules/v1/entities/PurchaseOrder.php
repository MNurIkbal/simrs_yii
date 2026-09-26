<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use Doco\components\DocoConstants;
use app\components\PengadaanComponent;
use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\LogActivityR;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\SatuanKonversiView;
use yii\helpers\ArrayHelper;

class PurchaseOrder {
	protected $tipe;
	protected $info_po;
	protected $detail_po;
	protected $model_header;
	protected $model_detail;
	protected $pk_header;
	protected $pk_detail;
	protected $pk_item;
	protected $pk_satuankonversi;
	protected $no_po;
	protected $log_tipe;
	protected $datenow;
	protected $user_login;
	protected $merged_no_po;
	protected $po_ids;
	protected $pk_prdetail;
	protected $rekomendasidetail_id;

	public function setTipePO($type_po) {
		$this->datenow = date('Y-m-d H:i:s');
        $this->user_login = Yii::$app->user->identity->pegawai_id;

		if($type_po == 'obat') {
			$this->tipe = 'obat';
			$this->model_header = new ValidasiPoObat;
			$this->model_detail = new ValidasiPoObatDetail;
			$this->pk_header = 'validasipoobat_id';
			$this->pk_detail = 'validasipoobatdetail_id';
			$this->pk_item = 'obatalkes_id';
			$this->pk_satuankonversi = 's_konversiobt_id';
			$this->no_po = 'no_poobat';
			$this->pk_prdetail = 'purchasereqdetail_id';
			$this->rekomendasidetail_id = 'rekomendasiobatdetail_id';
			$this->log_tipe = DocoConstants::LA_TIPE_PO;
		} else {
			$this->tipe = 'barang';
			$this->model_header = new ValidasiPoBarang;
			$this->model_detail = new ValidasiPoBarangDetail;
			$this->pk_header = 'validasipobarang_id';
			$this->pk_detail = 'validasipobarangdetail_id';
			$this->pk_item = 'barang_id';
			$this->pk_satuankonversi = 's_konversibrg_id';
			$this->no_po = 'no_pobarang';
			$this->pk_prdetail = 'purchasereqbrgdetail_id';
			$this->rekomendasidetail_id = 'rekomendasibarangdetail_id';
			$this->log_tipe = DocoConstants::LA_TIPE_PO_NONMEDIS;
		}

		return $this;
	}

	public function getInfoPO($po_ids) {
		$this->info_po = $this->model_header->find()->where(['in', $this->pk_header, $po_ids])->asArray()->all();
		$this->po_ids = $po_ids;
		return $this;
	}

	public function isNotAllManual() {
		return in_array(false, array_column($this->info_po, 'is_manual'));
	}

	public function merge() {
		$merged_id = min($this->po_ids);
		$po_index = array_search($merged_id, array_column($this->info_po, $this->pk_header));
		$merged_no_po = $po_index !== false ? $this->info_po[$po_index][$this->no_po] : null;

		// header to be deleted
		$removed_ids = array_diff($this->po_ids, array($merged_id));
		$remove_header = $this->model_header->updateAll(
			[
				'status_penerimaan' => DocoConstants::STATUS_BATAL_PO,
				'is_deleted' => true,
				'is_active' => false,
				'deleted_date' => $this->datenow,
				'deleted_by' => $this->user_login,
				'catatan' => 'Merge ke PO nomor '.$merged_no_po,
				'tgl_batal_po' => $this->datenow
			],
			[ 'in', $this->pk_header, $removed_ids ]
		);

		if(!$remove_header) {            
			throw new \Exception("Merge PO gagal. Gagal hapus transaksi PO", 1);
		}
		
		// detail to be deleted
		$remove_detail = $this->model_detail->updateAll(
			[
				'is_deleted' => true,
				'is_active' => false,
				'deleted_date' => $this->datenow,
				'deleted_by' => $this->user_login
			],
			[ 'in', $this->pk_header, $removed_ids ]
		);

		if(!$remove_detail) {            
			throw new \Exception("Merge PO gagal. Gagal hapus detail PO", 1);
		}

		$new_detail = $this->mergeDetail($merged_id, array_values($removed_ids));
		$this->model_detail->batchInsert($new_detail);

		$merged_additional = [];
		$removed_po = $this->model_header->find(true)->select([$this->no_po])->where(['in', $this->pk_header, $removed_ids])->asArray()->all();
		$removed_po = array_column($removed_po, $this->no_po);
		foreach($this->info_po as $key => $val) {
			// logactivity payload
			$id = $val[$this->pk_header];
			if($id == $merged_id) {
				$no_po = $removed_po;
			} else {
				$no_po = [$merged_no_po];
			}

			$no_po = implode(',', $no_po);
			$logs[] = [
				'transaksi_id' => $id,
				'tgl' => $this->datenow,
				'tipe' => $this->log_tipe,
				'aksi' => $id == $merged_id ? DocoConstants::LA_AKSI_MERGE : DocoConstants::LA_AKSI_HAPUS,
				'keterangan' => 'MERGEPO',
				'alasan' => $id == $merged_id ? 'Merge dari PO nomor '.$no_po : 'Merge ke PO nomor '.$no_po
			];

			// additional_data payload
			$additional_data = json_decode($val['additional_data'], true);
			$additional_data = $additional_data['kontrak'];
			$merged_additional[] = $additional_data;
		}

		$detail = $this->model_detail->find()
				->select([$this->pk_item, 'qty_input', $this->pk_satuankonversi, 'harga', 'discount', 'discount_rp', 'jumlah'])
				->where([$this->pk_header => $merged_id])->asArray()->all();
		$new_subtotal = array_sum(array_column($detail, 'jumlah'));
		$new_total_disc = array_sum(array_column($detail, 'discount_rp'));
		$pajak = Pajak::find()->select(['pajak_persen'])->where(['pajak_id' => $this->info_po[$po_index]['pajak_id']])->one();
		$ppn_persen = $pajak['pajak_persen'];
		$new_total = $new_subtotal + ($new_subtotal * $ppn_persen / 100);
		$merged_header = $this->model_header->updateAll(
			[
				'ppn_persen' => $ppn_persen,
				'ppn_nilai' => round(($new_subtotal * ($ppn_persen / 100)), 2),
				'sub_total' => round($new_subtotal, 2),
				'total_discount' => round($new_total_disc, 2),
				'total' => round($new_total, 2),
				'additional_data' => json_encode([
					'kontrak' => $merged_additional
				])
			],
			[ $this->pk_header => $merged_id ]
		);

		if(!$merged_header) {
			throw new \Exception("Merge PO gagal. Gagal update transaksi PO", 1);
		}

		$this->saveLogs($logs);
		$this->merged_no_po = $merged_no_po;
		return $this;
	}

	public function mergeDetail($merged_id, $removed_ids) {
		$old_detail = $this->model_detail->find(true)->where(['in', $this->pk_header, $removed_ids])->orderBy([$this->pk_header => SORT_ASC])->asArray()->all();
		$existing_detail = $this->model_detail->find()->where(['in', $this->pk_header, $merged_id])->asArray()->all();
		$existing_detail = ArrayHelper::index($existing_detail, $this->pk_item);
		$existing_items = array_keys($existing_detail);
		$details = ArrayHelper::index($old_detail, null, function($val) {
			return $val[$this->pk_item];
		});
		$new_detail = [];
		$updateData = [];
		$updateCondition = [];
		foreach($old_detail as $key => $val) {
			$item_id = $val[$this->pk_item];
			if(in_array($item_id, $existing_items) && 
				( is_null($val[$this->pk_prdetail]) && is_null($existing_detail[$item_id][$this->pk_prdetail]) || // gabung row untuk PO manual
				  isset($val[$this->pk_prdetail]) && $existing_detail[$item_id][$this->pk_prdetail] == $val[$this->pk_prdetail] )// gabung row untuk PO dari PR yang memiliki purchasereqdetail_id sama
			) {
				// payload for merged items
				$qty_input = $existing_detail[$item_id]['qty_input'] + $val['qty_input'];
				$harga = ArrayHelper::getValue($val, 'harga');
				$discount = round(ArrayHelper::getValue($val, 'discount'), 2);
				if($existing_detail[$item_id][$this->pk_satuankonversi] != $val[$this->pk_satuankonversi]) {
					$nilai_konversi = $this->getNilaiKonversi($this->tipe, $existing_detail[$item_id][$this->pk_satuankonversi]);
					$nkonv_new = $this->getNilaiKonversi($this->tipe, $val[$this->pk_satuankonversi]);
					$qty_input_existing = $existing_detail[$item_id]['qty_input'] * $nilai_konversi;
					$qty_konversi_new = $val['qty_input'] * $nkonv_new;

					$qty_input = ceil(($qty_input_existing + $qty_konversi_new) / $nkonv_new);
				}
				
				$discount_rp = ($qty_input * $harga) * ($discount / 100);

				// payload for update
				$updateData['qty_input'][] = $qty_input;
				$updateData['qty_po'][] = $val['qty_po'];
				$updateData[$this->pk_satuankonversi][] = $val[$this->pk_satuankonversi];
				$updateData['harga'][] = round($harga, 2);
				$updateData['jumlah'][] = round((($harga * $qty_input) - $discount_rp), 2);
				$updateData['discount'][] = $discount;
				$updateData['discount_rp'][] = round($discount_rp, 2);
				$updateData['is_disc_nominal'][] = isset($val['is_disc_nominal']) && $val['is_disc_nominal'] ? true : false;
				$updateCondition[$this->pk_detail][] = $existing_detail[$item_id][$this->pk_detail];
				unset($new_detail[$key]);
			} else {
				// payload for new items
				$qty_kecil = 0;
				$satuankonversi_id = $val[$this->pk_satuankonversi];
				$harga = floatval($val['harga']);
				$discount = ArrayHelper::getValue($val, 'discount');
				$is_disc_nominal = ArrayHelper::getValue($val, 'is_disc_nominal');
				foreach($details[$item_id] as $value) {
					if(count($details[$item_id]) > 1 && is_null($value[$this->pk_prdetail])) {
						$nilai_konversi = $this->getNilaiKonversi($this->tipe, $value[$this->pk_satuankonversi]);
						$satuankonversi_id = $nilai_konversi > 1 ? $value[$this->pk_satuankonversi] : $satuankonversi_id;
						$qty_konversi = ceil($value['qty_input'] * $nilai_konversi);
						$qty_kecil += $qty_konversi;

						$qty_input = ceil($qty_kecil / $nilai_konversi);
						$discount_rp = ($qty_input * $harga) * ($discount/100);
						$key = $item_id;
					} else {
						$qty_input = $val['qty_input'];
						$discount_rp = $is_disc_nominal ? ArrayHelper::getValue($val, 'discount_rp') : ($qty_input * $harga) * ($discount/100);
					}
				}
				
				$new_detail[$key][$this->pk_item] = $item_id;
				$new_detail[$key]['qty_input'] = $qty_input;
				$new_detail[$key]['qty_po'] = $val['qty_po'];
				$new_detail[$key][$this->pk_satuankonversi] = $satuankonversi_id;
				$new_detail[$key]['harga'] = $harga;
				$new_detail[$key]['jumlah'] = floatval(($harga * $qty_input) - $discount_rp);
				$new_detail[$key]['discount'] = floatval($discount);
				$new_detail[$key]['discount_rp'] = floatval($discount_rp);
				$new_detail[$key][$this->pk_header] = $merged_id;
				$new_detail[$key][$this->pk_prdetail] = $val[$this->pk_prdetail];
				$new_detail[$key]['nilai_ro'] = $val['nilai_ro'];
				$new_detail[$key]['ro_stok'] = $val['ro_stok'];
				$new_detail[$key]['qty_tersedia'] = $val['qty_tersedia'];
				$new_detail[$key]['is_disc_nominal'] = $is_disc_nominal;
				$new_detail[$key][$this->rekomendasidetail_id] = $val[$this->rekomendasidetail_id];
			}
		}
		
		if(!empty($updateData)) {
			$update_detail = PengadaanComponent::batchUpdate($this->model_detail->getTableSchema()->name, $updateData, $updateCondition);
			if(!$update_detail) {
				throw new \Exception("Merge PO gagal. Gagal update detail PO", 1);
			}
		}

		$new_detail = array_values($new_detail);
		return $new_detail;
	}

	public function get_duplicates($array) {
		return array_unique(array_diff_assoc($array, array_unique($array)));
	}

	public function saveLogs($data) {
		$log_model = LogActivityR::batchInsert($data);
		if(!$log_model) {
			throw new \Exception("Tidak Dapat Menyimpan Log Transaksi", 1);
		}
	}

	public function getNilaiKonversi($jenis, $skonv_id) {
		$skonversi = SatuanKonversiView::find()->select(['nilai_konversi'])->where(['satuankonversi_id' => $skonv_id, 'jenis' => $jenis])->one();
		return ArrayHelper::getValue($skonversi, 'nilai_konversi');
	}

	public function getMergedNoPO() {
		return $this->merged_no_po;
	}
}