<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class CetakDetailInvoiceKramat extends \Doco\processes\CetakDetailInvoiceProcess
{
	public $dokPath = 'detail-invoice-kramat';
	public $dokTercetak = 'detail-invoice-pembayaran-ri-kramat';
	protected $additionalTindakan = false;

	protected function jenisCetakan()
	{
		if(!$this->isObat) {
         if (empty($this->pasienadmisi_id)) {
				$this->dokPath = 'detail-invoice-non-ranap-kramat';
				$this->dokTercetak = 'invoice-kramat';
         }
      }
      else {
			$this->dokTercetak = 'invoice-kramat-reseptur';
			$this->dokPath = 'detail-invoice-reseptur';
      }
	}

	protected function getPdfAttributes()
	{
		return array_merge($this->setAttrPrint(), !empty($this->pasienadmisi_id) ? $this->getAttrRanap() : $this->getAttrNonRanap());
	}

	protected function getAttrNonRanap()
	{
		$printedBy = $this->nama_pegawai;
		if(empty($printedBy)) {
			$pegawaiId = Yii::$app->jwt->user->pegawai_id;
			$printedBy = $this->getDataPegawai($pegawaiId);
		}

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
		$subTotal = $this->grandTotal;
		$uangMuka = ArrayHelper::getValue($dataTagihan, 'uangMuka', 0);
		$dataBiayaAdmin = ArrayHelper::getValue($dataTagihan, 'dataBiayaAdmin', []);
		$biayaAdmin = ArrayHelper::getValue($dataBiayaAdmin, 'biaya_admin', 0);
		$totalDitagihkan = ArrayHelper::getValue($dataTagihan, 'totalDitagihkan', 0);
		$totalDibayar = ArrayHelper::getValue($dataTagihan, 'totalDibayar', 0);
		$totalKembalian = ArrayHelper::getValue($dataTagihan, 'totalKembalian', 0);
		$totalDibayar = $totalDibayar - $totalKembalian;
		$totalDijamin = ArrayHelper::getValue($dataTagihan, 'totalDijamin', 0);
		$discount = ArrayHelper::getValue($dataTagihan, 'discount', 0);
		$dataDiskon = $this->getTotalDiskon();
		
		$groupCaraBayarId = ArrayHelper::getValue($dataDiskon, 'groupCaraBayarId');
		$payerUtama = ArrayHelper::getValue($dataDiskon, 'payerUtama');
		$diskonPayer = ArrayHelper::getValue($dataDiskon, 'diskonPayer', 0);
		$diskonPasien = ArrayHelper::getValue($dataDiskon, 'diskonPasien', 0);
		
		$discDokterPasien = ArrayHelper::getValue($dataDiskon, 'discDokterPasien', 0);
		$discDokterPayer = ArrayHelper::getValue($dataDiskon, 'discDokterPayer', 0);
		$totalDiskonDokter = $discDokterPasien + $discDokterPayer;
		
		if($this->jenis_invoice == self::INVOICE_LENGKAP) {
			$discount = $diskonPasien + $diskonPayer + $discDokterPasien + $discDokterPayer;
			if($groupCaraBayarId != DocoConstants::GROUP_UMUM) {
				$totalDitagihkan += $totalDiskonDokter;
			}
			if($uangMuka > 0) {
				$totalDitagihkan = $totalDibayar - $uangMuka;
			}
		}
		elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
			$discount = $diskonPasien + $discDokterPasien;
			if($groupCaraBayarId != DocoConstants::GROUP_UMUM) {
				$totalDitagihkan += $totalDiskonDokter;
			}
			if($uangMuka > 0) {
				$totalDitagihkan = $totalDibayar - $uangMuka;
			}
		}
		else {
			$discount = $diskonPayer;
			$totalDitagihkan = 0;
			$uangMuka = 0;
		}
		$pembulatanPenjamin = ArrayHelper::getValue($dataTagihan, 'totalPembulatan', 0);
		if($totalDijamin > 0) {
			$totalDijamin = $totalDijamin + $pembulatanPenjamin;
			if($pembulatanPenjamin == 0) {
				$pembulatanPenjamin = $this->getPembulatan($totalDijamin);
				$totalDijamin = ArrayHelper::getValue($pembulatanPenjamin, 'total_ditagihkan', (int) $totalDijamin);
			}
			if($this->jenis_invoice == self::INVOICE_LENGKAP || $this->jenis_invoice == self::INVOICE_PENJAMIN && !empty($this->penjaminId) && $payerUtama == $this->penjaminId) {
				$totalDijamin -= $totalDiskonDokter;
			}
		}
		return [
			'#printed_by#' => $printedBy,
			'#title#' => 'PERINCIAN BIAYA',
			'#nama_pasien#' => ArrayHelper::getValue($this->header, 'no_rekam_medik') .' '. ArrayHelper::getValue($this->header, 'nama_pasien'),
			'#no_transkasi#' => $this->getBillNo(),
			'#tgl_lahir#' => $tanggalLahir,
			'#penjamin#' => ($this->jenis_invoice != self::INVOICE_PASIEN) ? ArrayHelper::getValue($this->dataPayer, 'payer') : '',
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
	}

	protected function getAttrRanap()
	{
		$tanggalLahir = ArrayHelper::getValue($this->header, 'tanggal_lahir');
		$tglStopAkomodasi = ArrayHelper::getValue($this->header, 'tgl_stopakomodasi');
      $tglAdmisi = ArrayHelper::getValue($this->header, 'tgl_admisi');
		$kunjunganRanap = $this->getKunjunganRanap();
		$klsRawatHak = ArrayHelper::getValue($kunjunganRanap, 'klsrawathak', '-');
		$kelaspelayanan_nama = ArrayHelper::getValue($kunjunganRanap, 'kelaspelayanan_nama');
		if(!empty($klsRawatHak) && $klsRawatHak != '-') {
			$klsRawatHak = DocoHelpers::numberToRomanRepresentation($klsRawatHak);
		}
		return [
			'#admission#' => date('d/M/Y H:i', strtotime($tglAdmisi)),
         '#discharge_date#' => date('d/M/Y H:i', strtotime($tglStopAkomodasi)),
			'#age#' => !empty($tanggalLahir) ? $this->getUmur($tanggalLahir) : '-',
			'#hakKelas#' => $klsRawatHak,
			'#statusKelas#' => ArrayHelper::getValue($kunjunganRanap, 'status_kelas'),
			// '#kelaspelayanan_nama#' => $kelaspelayanan_nama
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

	protected function getKunjunganRanap()
	{
		if(empty($this->pasienadmisi_id)) {
			return [];
		}
		return Yii::$app->db->createCommand("
			SELECT pendaftaran_id,no_pendaftaran,no_rekam_medik,nama_pasien,nama_depan,alamat_pasien,rt,rw,tgl_pendaftaran,tgl_admisi,tgl_pulang,tgl_stopakomodasi,
			jenis_kelamin,tanggal_lahir,umur,pasienadmisi_id,carabayar_id,klsrawat,is_aps,is_pasientitipan,is_pasientitipan_pk,penjamin_id,
			is_stoppasientitipan,bpjs_kelas,kelaspelayanan_id,kelaspelayanan_nama,nama_pegawai,
			carabayar_nama,penjamin_nama,ruangan_nama,kamarruangan_nokamar,no_tempattidur,
			kelas_hak,status_kelas,
			(((((additional_data::json)->>'sep')::json)->>'klsRawat')::json)->>'klsRawatHak' as klsrawathak
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
      $totalDiscountAdm = ArrayHelper::getValue($pembayaran, 'total_discountadm', 0);
      $konfigTarif = $this->getKonfigTarif();
      $isInvoiceDiskon = ArrayHelper::getValue($konfigTarif, 'is_invoice_diskon', false);
      $query = "SELECT sum(tarif_diskon) AS total_diskon 
         FROM invoicesudahbayardetail_v 
         WHERE pembayaran_id = $this->invoice_id ";

      if($isInvoiceDiskon){
         $query .= " AND sub_total < 0 ";
      }

      $diskonPayer = $diskonPasien = 0;
		if($this->jenis_invoice == self::INVOICE_LENGKAP) {
			$diskonPasien = $db
				->createCommand("{$query} AND tarif_dibayarkan > 0")
				->queryScalar();

			$diskonPayer = $db
				->createCommand("{$query} AND tarif_dijamin > 0 AND tarif_dibayarkan = 0")
				->queryScalar();
		}
		elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
			$diskonPayer = 0;
			$diskonPasien = $db
				->createCommand("{$query} AND tarif_dibayarkan > 0 AND tarif_dijamin = 0")
				->queryScalar();
		}
		else {
			$diskonPasien = 0;
			if(!empty($this->penjaminId)) {
				$diskonPayer = $db
					->createCommand("{$query} AND tarif_dijamin > 0 AND penjamin_pelayanan_id = {$this->penjaminId} ")
					->queryScalar();
			}
		}

      $listDiskonPayer = [];
      if($totalDiscountAdm > 0){
         if (is_array($pembayaranPenjamin) || is_object($pembayaranPenjamin)){
            foreach ($pembayaranPenjamin as $key => $value) {
               $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');
               $discountAdmPenjamin = ArrayHelper::getValue($value, 'discount_adm_penjamin', 0);
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
      }
      
		$diskonDokter = ArrayHelper::getValue($additionalData, 'pembayaran_diskon', []);
      $totalDiskonDokter = $discDokterPayer = $discDokterPasien = 0;
		$pendaftaran = Yii::$app->db->createCommand("
            SELECT pendaftaran_t.carabayar_id, carabayar_m.groupcarabayar_id, pendaftaran_t.penjamin_id
            FROM pembayaran_t 
            JOIN pendaftaran_t on pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
            JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
            WHERE pembayaran_t.pembayaran_id = {$this->invoice_id}
         ")->queryOne();

		$groupCaraBayarId = ArrayHelper::getValue($pendaftaran, 'groupcarabayar_id');
		$payerUtama = ArrayHelper::getValue($pendaftaran, 'penjamin_id');
      if(!empty($diskonDokter)) {
         foreach ($diskonDokter as $key => $value) {
            $totalDiscount = ArrayHelper::getValue($value, 'total_diskon', 0);
            $totalDiskonDokter += $totalDiscount;
         }
         
         if($groupCaraBayarId == DocoConstants::GROUP_UMUM) {
            $discDokterPasien = $totalDiskonDokter;
         }
         else {
				if($this->jenis_invoice == self::INVOICE_PENJAMIN && !empty($this->penjaminId) && $payerUtama == $this->penjaminId) {
					$diskonPayer += $totalDiskonDokter;
				}
            $discDokterPayer = $totalDiskonDokter;
         }
      }
		
		return [
			'diskonPayer' => (!$diskonPayer) ? 0 : $diskonPayer,
         'diskonPasien' => (!$diskonPasien) ? 0 : $diskonPasien,
			'discDokterPasien' => $discDokterPasien,
         'discDokterPayer' => $discDokterPayer,
			'payerUtama' => $payerUtama,
			'groupCaraBayarId' => $groupCaraBayarId
		];
	}

	protected function processFlow()
	{
		$request = $this->_requestData;
      $type = $request->get('invoice_type', 'invoice');
      $outputAttributes = $request->get('outputAttributes', false);
		if ($type == 'tmp') {
         $pathPenjamin = new InvoicePayerBelumBayar;
         $pathPenjamin->dokPath = 'invoice-penjamin-mhbg';
         return $pathPenjamin->execute();
      } else if ($type == 'invoice') {
         if($outputAttributes) {
            $this->populateData();
				$this->jenisCetakan();
            $this->getDetailInvoice();
            return [
               'attributes' => $this->getPdfAttributes(),
               'kode_doc' => $this->dokTercetak,
            ];
         }
         else {
            $this->populateData();
				$this->jenisCetakan();
            $this->getDetailInvoice();
            $this->cetakDetailInvoice();
         }
      }
	}
}
