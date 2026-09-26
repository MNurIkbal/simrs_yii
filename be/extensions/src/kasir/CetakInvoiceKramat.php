<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\models\kasir\InvoiceSudahBayarDetailView;

class CetakInvoiceKramat extends \Doco\processes\CetakDetailInvoiceProcess
{
	public $grandTotal = 0;
	public $diskonSplit = 0;
	private $_mappObatAlkes = [
		'Drugs & Consumables' => 'Obat Alkes',
		'kelompok_obat' => 'Obat Alkes',
		'kelompok_paket' => 'Paket',
		'kelompok_paket_mcu' => 'Paket MCU',
		'Consultation' => 'Konsultasi'
	];

	protected function jenisCetakan()
	{
		if(!$this->isObat) {
         if (!empty($this->pasienadmisi_id)) {
				$this->dokPath = 'invoice-kramat-ranap';
				$this->dokTercetak = 'summary-invoice-ranap';
         }
			else {
				$this->dokPath = 'invoice-kramat';
				$this->dokTercetak = 'invoice-kramat';
			}
      }
      else {
			$this->dokTercetak = 'invoice-kramat-reseptur';
			$this->dokPath = 'invoice-kramat';
      }
	}

	protected function getDetailTindakan()
	{
		if(empty($this->invoice_id)) {
         return [];
      }
	  
      $konfigTarif = $this->getKonfigTarif();
      $isDiskonPasien = ArrayHelper::getValue($konfigTarif, 'is_diskon_pasien', false);

      $whereClause = '';
      $withPenjaminId = '';
	  if(!empty($this->penjaminId)) {
		  $withPenjaminId = ' AND penjamin_pelayanan_id = '.$this->penjaminId.'';
	   }
      if($this->jenis_invoice == self::INVOICE_PENJAMIN) {
         $whereClause = 'AND tarif_dijamin > 0 '.$withPenjaminId.' ';
      }
      elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
         $whereClause = 'AND tarif_dibayarkan > 0';
      }
	  $data = Yii::$app->db->createCommand("
				SELECT
				'OBAT ALKES' AS kelompoktindakan_nama,
				SUM(qty) as qty,
				SUM(tarif) AS sub_total,
				SUM(tarif_dijamin) as tarif_dijamin,
				SUM(tarif_dibayarkan) as tarif_dibayarkan,
				SUM(tarif_diskon) as tarif_diskon,
				penjamin_pelayanan_id
				FROM invoiceobatdetail_v
				WHERE pembayaran_id = {$this->invoice_id}
				{$whereClause}
				GROUP BY kelompoktindakan_nama, penjamin_pelayanan_id
				ORDER BY kelompoktindakan_nama ASC
			")->queryAll();

		$grandTotal = $diskonSplit = 0;
		$newData = $detailAkomodasi = [];
		$totalAkomodasi = 0;
		$totalDijamin = $totalDibayarkan = 0;
		if(!$this->isObat) {
			$data = InvoiceSudahBayarDetailView::find()
			->select([
				'kelompoktindakan_nama', 'SUM(qty) AS qty', 'SUM(tarif_dijamin) as tarif_dijamin',
				'SUM(tarif_dibayarkan) as tarif_dibayarkan', 'SUM(tarif_diskon) as tarif_diskon', 'SUM(sub_total) as sub_total'])
			->where(['pembayaran_id' => $this->invoice_id]);

			if(!empty($this->pasienadmisi_id)) {
				$data->andWhere(['is_akomodasi' => FALSE]);
				$detailAkomodasi = Yii::$app->db->createCommand("
					SELECT *
					FROM infodetailakomodasi_v
					WHERE pembayaran_id = {$this->invoice_id} AND tipe = 'akomodasi_tagihan'
					{$whereClause}
				")->queryAll();

				if(!empty($detailAkomodasi)) {
					foreach ($detailAkomodasi as $key => $value) {
						$lamaRawat = ArrayHelper::getValue($value, 'lama_rawat', 0);
						$tarifKamar = ArrayHelper::getValue($value, 'tarif_kamar', 0);
						$totalAkomodasi += $lamaRawat * $tarifKamar;
					}
				}
			}

			 $data->andWhere(['>', 'sub_total', 0]);
			
			if(!empty($this->penjaminId)) {
				$data->andWhere(['penjamin_pelayanan_id' => $this->penjaminId]);
			}
			if($this->jenis_invoice == self::INVOICE_PENJAMIN) {
				$data->andWhere(['>', 'tarif_dijamin', 0]);
			}
			elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
				$data->andWhere(['>', 'tarif_dibayarkan', 0]);
			}
			$data = $data->groupBy(['kelompoktindakan_nama'])
				->orderBy(['kelompoktindakan_nama' => SORT_ASC])
				->asArray()->all();
		}
		if(!empty($data)) {
			$tmpTarifDibayarkan = $tmpTarifDijamin = 0;
			foreach ($data as $key => $value) {
				$kelompok = ArrayHelper::getValue($value, 'kelompoktindakan_nama');
				$tarifDijamin = ArrayHelper::getValue($value, 'tarif_dijamin', 0);
				$tarifDibayarkan = ArrayHelper::getValue($value, 'tarif_dibayarkan', 0);
				$tarifDiskon = ArrayHelper::getValue($value, 'tarif_diskon', 0);

				if($tarifDibayarkan > 0 && $tarifDijamin > 0){
					$diskonSplit += $tarifDiskon;
				}
				
				if($this->jenis_invoice == self::INVOICE_PENJAMIN ) {
					$value['sub_total'] = $tarifDijamin;
				}
				elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
					$value['sub_total'] = $tarifDibayarkan;
				}
				$subTotal = ArrayHelper::getValue($value, 'sub_total', 0);
				$grandTotal += $subTotal;
				$totalDijamin += $tarifDijamin;
				$totalDibayarkan += $tarifDibayarkan;
				$value['kelompok'] = isset($this->_mappObatAlkes[$kelompok]) ? $this->_mappObatAlkes[$kelompok] : $kelompok;
				$newData[] = $value;
			}
			$this->grandTotal = $grandTotal;
			$this->diskonSplit = $diskonSplit;
		}

		$this->dataTindakan = $newData;
		$this->detailAkomodasi = $detailAkomodasi;
		$this->totalAkomodasi = $totalAkomodasi;
		$this->totalDijamin = $totalDijamin;
		$this->totalDibayarkan = $totalDibayarkan;
	}

	private function setAttrPrintNonRanap()
	{
		$printedBy = $this->nama_pegawai;
		if(empty($printedBy)) {
			$pegawaiId = Yii::$app->jwt->user->pegawai_id;
			$printedBy = $this->getDataPegawai($pegawaiId);
		}
		$attributeDefault = $this->setAttrPrint();
		$tglPendaftaran = ArrayHelper::getValue($this->header, 'tgl_pendaftaran');
		$tglPembayaran = ArrayHelper::getValue($this->header, 'tgl_pembayaran');
		$dataTagihan = $this->getDataTagihan(true);
		$renderTable = $this->getRenderTable();
		$dataSummary = ArrayHelper::getValue($renderTable, 'summary', []);
		$namaKasir = ArrayHelper::getValue($dataSummary, 'nama_pegawai');
		$tanggalLahir = ArrayHelper::getValue($this->header, 'tanggal_lahir');
		if($this->isObat) {
			$tanggalLahir = ArrayHelper::getValue($this->header, 'tgl_lahir');
		}
		// perhitungan tagihan
		$subTotal = $this->grandTotal + $this->totalAkomodasi;
		$dataBiayaAdmin = ArrayHelper::getValue($dataTagihan, 'dataBiayaAdmin', []);
		$biayaAdmin = ArrayHelper::getValue($dataBiayaAdmin, 'biaya_admin', 0);
		$totalDitagihkan = ArrayHelper::getValue($dataTagihan, 'totalDitagihkan', 0);
		$totalDibayar = ArrayHelper::getValue($dataTagihan, 'totalDibayar', 0);
		$totalDijamin = ArrayHelper::getValue($dataTagihan, 'totalDijamin', 0);
		$discount = ArrayHelper::getValue($dataTagihan, 'discount', 0);
		$dataDiskon = $this->getTotalDiskon();
		$totalDiskonDokter = ArrayHelper::getValue($dataDiskon, 'totalDiskonDokter', 0);
		$uangMuka = ArrayHelper::getValue($dataTagihan, 'uangMuka', 0);
		$pembulatanPenjamin = ArrayHelper::getValue($dataTagihan, 'totalPembulatan', 0);
		$diskonPayer = ArrayHelper::getValue($dataDiskon, 'diskonPayer', 0);
		$diskonPasien = ArrayHelper::getValue($dataDiskon, 'diskonPasien', 0);
		$groupCaraBayarId = ArrayHelper::getValue($dataDiskon, 'groupCaraBayarId');
		$totalKembalian = ArrayHelper::getValue($dataTagihan, 'totalKembalian', 0);
		$totalDibayar = $totalDibayar - $totalKembalian;

		if($totalDijamin > 0) {
			$totalDijamin = $this->totalDijamin + $pembulatanPenjamin;
		}
		
		$discDokterPasien = $discDokterPayer = 0;
		if($totalDitagihkan > 0 && $totalDijamin > 0){
			$discDokterPasien = 0;
			$discDokterPayer = $totalDiskonDokter;
		}elseif($totalDitagihkan > 0 && $totalDijamin == 0){
			$discDokterPayer = 0;
			$discDokterPasien = $totalDiskonDokter;
		}elseif($totalDitagihkan == 0 && $totalDijamin > 0){
			$discDokterPasien = 0;
			$discDokterPayer = $totalDiskonDokter;
		}
		
		if($this->jenis_invoice == self::INVOICE_LENGKAP) {
			$discount = $diskonPasien + $diskonPayer + $totalDiskonDokter;
			if($uangMuka > 0) {
				$totalDitagihkan = $totalDibayar - $uangMuka;
			}
		}elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
			/**
			 * subtotal dikurangin diskon dokter karena : 
			 * 	1. diskon dokter di kramat akan mengurangi pasien kalau sebagian dijamin sebagian dibayar pasien
			 *  2. Supaya balance
			 * discDokterPasien diset 0 karena di invoice pasien tidak akan dimunculkan nominal diskonnya
			 */
			$subTotal -= $discDokterPasien;
			$discDokterPasien = 0; 
			$discount = $diskonPasien + $discDokterPasien;
			if($uangMuka > 0) {
				$totalDitagihkan = $totalDibayar - $uangMuka;
			}
		}else {
			$discount = $diskonPayer + $discDokterPayer;
			$totalDitagihkan = 0;
			$uangMuka = 0;
		}

		$attributeNonRanap = [
			'#printed_by#' => $printedBy,
			'#title#' => 'REKAPITULASI PEMBAYARAN',
			'#nama_pasien#' => ArrayHelper::getValue($this->header, 'no_rekam_medik') .' '. ArrayHelper::getValue($this->header, 'nama_pasien'),
			'#no_transkasi#' => $this->getBillNo(),
			'#tgl_lahir#' => $tanggalLahir,
			'#penjamin#' => ArrayHelper::getValue($this->header, 'penjamin_nama'),
			'#no_resep#' => ArrayHelper::getValue($this->header, 'no_resep'),
			'#tgl_pelayanan#' => date('d-M-Y', strtotime($tglPendaftaran)),
			'#tgl_invoice#' => date('d-m-Y H:i', strtotime($tglPembayaran)),
			'#nama_kasir#' => $namaKasir,
			'#subTotal#' => DocoHelpers::formatNumber($subTotal),
			'#total_administrasi#' => DocoHelpers::formatNumber($biayaAdmin),
			'#total_discount#' => DocoHelpers::formatNumber($discount),
			'#penggunaan_uangmuka#' => DocoHelpers::formatNumber($uangMuka),
			'#total_dijamin#' => DocoHelpers::formatNumber($totalDijamin),
			'#total_ditagihkan#' => DocoHelpers::formatNumber($totalDitagihkan),
		];
		return array_merge($attributeDefault, $attributeNonRanap);
	}

	protected function setAttrPrintRanap()
	{
		$attributeDefault = $this->setAttrPrint();
		$kunjunganRanap = $this->getKunjunganRanap();
		$tanggalLahir = ArrayHelper::getValue($this->header, 'tanggal_lahir');
		$tglStopAkomodasi = ArrayHelper::getValue($this->header, 'tgl_stopakomodasi');
      $tglAdmisi = ArrayHelper::getValue($this->header, 'tgl_admisi');
		if($this->isObat) {
			$tanggalLahir = ArrayHelper::getValue($this->header, 'tgl_lahir');
		}

		// perhitungan tagihan
		$dataTagihan = $this->getDataTagihan(true);
		$subTotal = $this->grandTotal + $this->totalAkomodasi;
		$dataBiayaAdmin = ArrayHelper::getValue($dataTagihan, 'dataBiayaAdmin', []);
		$biayaAdmin = ArrayHelper::getValue($dataBiayaAdmin, 'biaya_admin', 0);
		$totalDitagihkan = ArrayHelper::getValue($dataTagihan, 'totalDitagihkan', 0);
		$totalDibayar = ArrayHelper::getValue($dataTagihan, 'totalDibayar', 0);
		$totalDijamin = ArrayHelper::getValue($dataTagihan, 'totalDijamin', 0);
		$discount = ArrayHelper::getValue($dataTagihan, 'discount', 0);
		$dataDiskon = $this->getTotalDiskon();
		$totalDiskonDokter = ArrayHelper::getValue($dataDiskon, 'totalDiskonDokter', 0);
		$uangMuka = ArrayHelper::getValue($dataTagihan, 'uangMuka', 0);
		$diskonPayer = ArrayHelper::getValue($dataDiskon, 'diskonPayer', 0);
		$diskonPasien = ArrayHelper::getValue($dataDiskon, 'diskonPasien', 0);
		
		
		$discDokterPasien = $discDokterPayer = 0;
		if($totalDitagihkan > 0 && $totalDijamin > 0){
			$discDokterPasien = 0;
			$discDokterPayer = $totalDiskonDokter;
		 }elseif($totalDitagihkan > 0 && $totalDijamin == 0){
			$discDokterPayer = 0;
			$discDokterPasien = $totalDiskonDokter;
		 }elseif($totalDitagihkan == 0 && $totalDijamin > 0){
			$discDokterPasien = 0;
			$discDokterPayer = $totalDiskonDokter;
		 }
		
		if($this->jenis_invoice == self::INVOICE_LENGKAP) {
			$discount = $diskonPasien + $diskonPayer + $totalDiskonDokter;
			if($uangMuka > 0) {
				$totalDitagihkan = $totalDibayar - $uangMuka;
			}
		}elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
			/**
			 * subtotal dikurangin diskon dokter karena : 
			 * 	1. diskon dokter di kramat akan mengurangi pasien kalau sebagian dijamin sebagian dibayar pasien
			 *  2. Supaya balance
			 * discDokterPasien diset 0 karena di invoice pasien tidak akan dimunculkan nominal diskonnya
			 */
			$subTotal -= $discDokterPasien;
			$discDokterPasien = 0; 
			$discount = $diskonPasien + $discDokterPasien;
			if($uangMuka > 0) {
				$totalDitagihkan = $totalDibayar - $uangMuka;
			}
		}else {
			$discount = $diskonPayer + $discDokterPayer;
			$totalDitagihkan = 0;
			$uangMuka = 0;
		}
		
		$pembulatanPenjamin = ArrayHelper::getValue($dataTagihan, 'totalPembulatan', 0);
		if($totalDijamin > 0) {
			$totalDijamin = $totalDijamin + $pembulatanPenjamin - $totalDiskonDokter;
		}
		
		$klsRawatHak = ArrayHelper::getValue($kunjunganRanap, 'klsrawathak', '-');
		if(!empty($klsRawatHak) && $klsRawatHak != '-') {
			$klsRawatHak = DocoHelpers::numberToRomanRepresentation($klsRawatHak);
		}
		$attributeRanap = [
			'#admission#' => date('d/M/Y H:i', strtotime($tglAdmisi)),
         '#discharge_date#' => date('d/M/Y H:i', strtotime($tglStopAkomodasi)),
			'#age#' => !empty($tanggalLahir) ? $this->getUmur($tanggalLahir) : '-',
			'#hakKelas#' => $klsRawatHak,
			'#statusKelas#' => ArrayHelper::getValue($kunjunganRanap, 'status_kelas'),
			'#subtotal#' => DocoHelpers::formatNumber($subTotal),
			'#biaya_admin#' => DocoHelpers::formatNumber($biayaAdmin),
			'#diskon#' => DocoHelpers::formatNumber($discount),
			'#dp#' => DocoHelpers::formatNumber($uangMuka),
			'#dijamin#' => DocoHelpers::formatNumber($totalDijamin),
			'#dibayar_pasien#' => DocoHelpers::formatNumber($totalDitagihkan),
		];
		return array_merge($attributeDefault, $attributeRanap);
	}

	protected function getRenderTable($shown = true)
	{
		if(empty($this->invoice_id)) {
         return [];
      }

		$summary = Yii::$app->db->createCommand("
			SELECT
				a.total_administrasi,
				(a.total_dijamin + COALESCE(b.total_piutang, 0)) as total_dijamin,
				a.total_tagihan,
				(a.total_discount + a.total_discountpembayaran) as total_discount,
				a.penggunaan_uangmuka,
				a.total_ditagihkan,
				d.nama_pegawai,
				a.total_pembulatan,
				f.penjamin_nama
			FROM pembayaran_t a
			LEFT JOIN pemberianpiutang_t b ON b.pemberianpiutang_id = a.pemberianpiutang_id
			JOIN loginpemakai_k c ON c.loginpemakai_id = a.created_by
			JOIN pegawai_m d ON c.pegawai_id = d.pegawai_id
			JOIN pembayaranpelayanan_t e ON e.pembayaran_id = a.pembayaran_id
			JOIN penjamin_m f ON f.penjamin_id = e.penjamin_id
			WHERE a.pembayaran_id = {$this->invoice_id}
			")->queryOne();

		return [
			'dataTindakan' => $this->dataTindakan,
			'summary' => $summary,
			'grandTotal' => $this->grandTotal,
			'detailAkomodasi' => $this->detailAkomodasi,
		];
	}

	protected function getUmur($tgl_lahir, $yearOnly = false)
	{
		$umur = '-';
		if (!empty($tgl_lahir)) {
			if($yearOnly) {
				$umur = DocoHelpers::getUmur($tgl_lahir, true, false).' Tahun';
			}
			else {
				$umur = DocoHelpers::getUmur($tgl_lahir);
			}
		}

		return $umur;
	}
	protected function cetakDetailInvoice()
	{
		error_reporting(0);
		$attributes = !empty($this->pasienadmisi_id) ? $this->setAttrPrintRanap() : $this->setAttrPrintNonRanap();
		$print = new DocoPrint($this->dokTercetak);
		$print->attributes = $attributes;
		$print->Output();
	}

	protected function getKunjunganRanap()
	{
		if(empty($this->pasienadmisi_id)) {
			return [];
		}
		return Yii::$app->db->createCommand("
			SELECT status_kelas,
			(((((additional_data::json)->>'sep')::json)->>'klsRawat')::json)->>'klsRawatHak' as klsRawatHak
			FROM infokunjunganri_v
			WHERE pasienadmisi_id = {$this->pasienadmisi_id}")
			->queryOne();
	}

	protected function getPembulatan($total_ditagihkan)
	{
		$helpers = new DocoHelpers;
		$konfigSystem = $this->konfigSystem;
		$isPembulatan = ArrayHelper::getValue($konfigSystem, 'is_pembulatankeatas', false);
		$satuanPembulatan = ArrayHelper::getValue($konfigSystem, 'satuanpembulatan', 0);
		$pembulatanTagihan = $helpers->pembulatan(round($total_ditagihkan, 2), $isPembulatan, $satuanPembulatan);
		$nominalPembulatan = ArrayHelper::getValue($pembulatanTagihan, 'pembulatan', 0);
		$total_ditagihkan = ArrayHelper::getValue($pembulatanTagihan, 'total', (int) $total_ditagihkan);

		return [
			'nominalPembulatan' => $nominalPembulatan,
			'total_ditagihkan' => $total_ditagihkan,
		];
	}

	protected function getTotalDiskon()
	{
      $tmpDiskon = 0;
      $db = Yii::$app->db;
      $pembayaran = $this->pembayaran;
      $additionalData = ArrayHelper::getValue($pembayaran, 'additional_data', []);
      $additionalData = json_decode($additionalData, true);
		$pembayaranPenjamin = ArrayHelper::getValue($additionalData, 'pembayaran_penjamin', []);
		$DataAdm = ArrayHelper::getValue($additionalData, 'adm_asuransi', []);
		$totalDiscountAdm = ArrayHelper::getValue($DataAdm, 'nominal_diskon', 0);
      //$totalDiscountAdm = ArrayHelper::getValue($pembayaran, 'total_discountadm', 0);
      $konfigTarif = $this->getKonfigTarif();
      $isInvoiceDiskon = ArrayHelper::getValue($konfigTarif, 'is_invoice_diskon', false);
      $isDiskonPasien = ArrayHelper::getValue($konfigTarif, 'is_diskon_pasien', false);

      $query = "SELECT sum(tarif_diskon) AS total_diskon 
         FROM invoicesudahbayardetail_v 
         WHERE pembayaran_id = $this->invoice_id ";

      if($isInvoiceDiskon){
         $query .= " AND sub_total < 0 ";
      }

      $diskonPayer = $diskonPasien = 0;
      if($this->jenis_invoice == self::INVOICE_PENJAMIN || $this->jenis_invoice == self::INVOICE_LENGKAP) {
			$diskonPasien = 0;
			if($this->jenis_invoice == self::INVOICE_PENJAMIN) {
				if(!empty($this->penjaminId)) {
					// $diskonPayer = $db
					// 	->createCommand("{$query} AND tarif_dijamin > 0 AND tarif_dibayarkan = 0 AND penjamin_pelayanan_id = {$this->penjaminId} ")
					// 	->queryScalar();
				}
			}
			else {
				$diskonPasien = $db
					->createCommand("{$query} AND tarif_dibayarkan > 0 
									AND tarif_dijamin <= 0") //untuk handling ada tindakan dibayar sebagian pasien dan sebgaian penjamin, saat ini diskonnya jadi double.
					->queryScalar();
					
				$diskonPayer = $db
            ->createCommand("{$query} AND tarif_dijamin > 0")
            ->queryScalar();
			}
      }
		else {
			$diskonPayer = 0;
			// $diskonPasien = $db
			// ->createCommand("{$query} AND tarif_dibayarkan > 0 AND tarif_dijamin <= 0 ")
			// ->queryScalar();
		}
		
      $listDiskonPayer = [];
      if($totalDiscountAdm > 0){
         if (is_array($pembayaranPenjamin) || is_object($pembayaranPenjamin)){
            foreach ($pembayaranPenjamin as $key => $value) {
               $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');
               $discountAdmPenjamin = $discountAdmPenjaminTotal = ArrayHelper::getValue($value, 'discount_adm_penjamin', 0);
               $listDiskonPayer[$penjaminId] = $value;
               $tmpDiskon += $discountAdmPenjamin;
            }
            if(!empty($this->penjaminId)) {
               if(isset($listDiskonPayer[$this->penjaminId])) {
                  $discountAdmPenjamin = isset($listDiskonPayer[$this->penjaminId]['discount_adm_penjamin']) ? $listDiskonPayer[$this->penjaminId]['discount_adm_penjamin'] : 0;
                  $diskonPayer += $discountAdmPenjamin;
               }
            }
            else {
               $diskonPayer += $tmpDiskon;
            }
         }
		 if($discountAdmPenjaminTotal == 0){
			$diskonPayer += $totalDiscountAdm;
		 }
      }
      
		$diskonDokter = ArrayHelper::getValue($additionalData, 'pembayaran_diskon', []);
      $totalDiskonDokter = $discDokterPayer = $discDokterPasien = 0;
      if(!empty($diskonDokter)) {
         foreach ($diskonDokter as $key => $value) {
            $totalDiscount = ArrayHelper::getValue($value, 'total_diskon', 0);
			/*
			if($this->jenis_invoice == self::INVOICE_LENGKAP || $this->jenis_invoice == self::INVOICE_PASIEN){
				$totalDiskonDokter += $totalDiscount;
			}
			*/
			$totalDiskonDokter += $totalDiscount;
         }
      }
		return [
			'diskonPayer' => (!$diskonPayer) ? 0 : $diskonPayer,
         'diskonPasien' => (!$diskonPasien) ? 0 : $diskonPasien,
			'discDokterPasien' => $discDokterPasien,
         'discDokterPayer' => $discDokterPayer,
         'totalDiskonDokter' => $totalDiskonDokter,
		];
	}

	protected function processFlow()
	{
		$this->populateData();
		$this->jenisCetakan();
		$this->getDetailTindakan();
		$this->cetakDetailInvoice();
	}
}
