<?php

/**
 * @author : Dede Herdiana (dede.herdiana@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfGabungInvoiceView;
use app\modules\v1\models\InvoiceGabungDetail;
use app\modules\v1\models\InfoDataPendaftaran;
use Doco\models\kasir\InfoPasienRiView;
use Doco\models\kasir\PasienView;
use Doco\models\ProfilRsView;
use Doco\components\DocoConstansId;
use Doco\models\KonfigSystem;
use Doco\models\kasir\InvoiceObatView;

class CetakInvoiceGabungKramat extends \Doco\processes\CetakInvoiceGabungProcess
{
	protected $kode_doc = 'invoice-kramat';
	protected $dokPath = '../tagihan-pasien/invoice-kramat';
	private $_mappObatAlkes = [
		'Drugs & Consumables' => 'Obat Alkes',
		'kelompok_obat' => 'Obat Alkes',
		'kelompok_paket' => 'Paket',
		'kelompok_paket_mcu' => 'Paket MCU',
		'Consultation' => 'Konsultasi'
	];

	protected $is_admisi = false;
	protected $headerGabung = [];
	protected $detailGabung = [];
	protected $dataPendaftaran = [];
	protected $isObat = false;
	protected $konfigSystem;
	protected $pegawai_login;
	protected $pembayaranid = [];
	protected $dataTindakan = [];
	
	protected $jenis_invoice;
	protected $kelompok;
	protected $pasienadmisi_id;
	protected $instalasi_id;
	protected $nama_pegawai;
	protected $id;
	protected $model = [];
	protected $header = [];
	protected $detail = [];
	protected $pembayaran = [];
	protected $dataPayer = [];
	protected $detailPasien = [];
	protected $arrDokter = [];
	protected $dataRoomRent = [];
	protected $profilRs = [];
	protected $roomrent = [];
	protected $dataDokter = [];

   private function setAttrPrintNonRanap()
	{
		$headerGabung = $this->headerGabung;
		$dataPendaftaran = $this->dataPendaftaran;
      $no_pendaftaran = !empty($headerGabung['no_pendaftaran']) ? $headerGabung['no_pendaftaran'] : '-';
		$penjamin_nama = !empty($headerGabung['penjamin']) ? $headerGabung['penjamin'] : null;
		$tglInvoice = !empty($headerGabung['tgl_invoicegabung']) ? date('d-m-Y', strtotime($headerGabung['tgl_invoicegabung'])) : null;
		$tgl_invoicegabung_cetak = !empty($headerGabung['tgl_invoicegabung_cetak']) ? date('d M Y', strtotime($headerGabung['tgl_invoicegabung_cetak'])) : $tglInvoice;
		$tglLahir = !empty($dataPendaftaran['tanggal_lahir']) ? date('d-M-Y', strtotime($dataPendaftaran['tanggal_lahir'])) : '-';
		$dokNama = !empty($dataPendaftaran['nama_dok_rj_rd']) ? $dataPendaftaran['nama_dok_rj_rd'] : '-';
		$tgl_pendaftaran = !empty($dataPendaftaran['tgl_pendaftaran']) ? date('d-M-Y ', strtotime($dataPendaftaran['tgl_pendaftaran'])) : '-';
		$no_resep = '';
		if($this->isObat) {
			$header = InvoiceObatView::find()
				->where(['pembayaran_id' => $this->invoice_id])
				->one();
			
			$penjamin_nama = isset($qSummary['penjamin_nama']) ? $qSummary['penjamin_nama'] : '';
			$tglLahir = !empty($header['tgl_lahir']) ? date('d-M-Y', strtotime($header['tgl_lahir'])) : '-';
			$dokNama = !empty($header['dok_resep']) ? $header['dok_resep'] : $header['dok_pendaftaran'];
			$no_resep = !empty($header['no_resep']) ? $header['no_resep'] : '';
			$tgl_pendaftaran = !empty($header['tgl_pembayaran']) ? date('d-M-Y ', strtotime($header['tgl_pembayaran'])) : '-';
		}

		$total_administrasi = isset($this->pembayaran['total_administrasi']) ? $this->pembayaran['total_administrasi'] : 0;
		$total_discount = isset($this->pembayaran['discount']) ? $this->pembayaran['discount'] : 0;
		$total_dijamin = isset($this->pembayaran['total_dijamin']) ? $this->pembayaran['total_dijamin'] : 0;
		$penggunaan_uangmuka = isset($this->pembayaran['penggunaan_uangmuka']) ? $this->pembayaran['penggunaan_uangmuka'] : 0;
		$subTotal = $this->grandTotal + $this->totalAkomodasi;
		$total_ditagihkan = ($subTotal + $total_administrasi) - $total_discount - $total_dijamin - $penggunaan_uangmuka;

		$pembulatanPenjamin = $this->getPembulatan($total_dijamin);
		$total_dijamin = isset($pembulatanPenjamin['total_ditagihkan']) ? (int) $pembulatanPenjamin['total_ditagihkan'] : (int) $total_dijamin;
		$pembulatan = $this->getPembulatan($total_ditagihkan);
		$nominalPembulatan = isset($pembulatan['nominalPembulatan']) ?  $pembulatan['nominalPembulatan'] : 0;
		$total_ditagihkan = isset($pembulatan['total_ditagihkan']) ? (int) $pembulatan['total_ditagihkan'] : (int) $total_ditagihkan;
		$no_rekam_medik = isset($dataPendaftaran['no_rekam_medik']) ? $dataPendaftaran['no_rekam_medik'] : null;
		$nama_pasien = isset($dataPendaftaran['nama_pasien']) ? $dataPendaftaran['nama_pasien'] : null;
		$attributes = [
			'#tgl_invoice#' => $tglInvoice,
			'#no_transkasi#' => isset($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-',
			'#nama_pasien#' => $no_rekam_medik .' '. $nama_pasien,
			'#nama_pasien_real#' => $nama_pasien,
			'#no_rekam_medik#' => $no_rekam_medik,
			'#tgl_lahir#' => $tglLahir,
			'#dokter#' => $dokNama,
			'#tgl_pelayanan#' => $tgl_pendaftaran,
			'#penjamin#' => $penjamin_nama,
			'#nama_kasir#' => isset($headerGabung['kasir']) ? $headerGabung['kasir'] : '-',
			'#title#' => 'REKAPITULASI PEMBAYARAN',
			'#no_resep#' => $no_resep,
			'#ruangan_nama#' => isset($dataPendaftaran['ruangan_nama']) ? $dataPendaftaran['ruangan_nama'] : '',
			'#no_pendaftaran#' => $no_pendaftaran,
			'#printed_by#' => $this->pegawai_login,
			'#subTotal#' => DocoHelpers::formatNumber($subTotal),
			'#total_administrasi#' => DocoHelpers::formatNumber($total_administrasi),
			'#total_discount#' => DocoHelpers::formatNumber($total_discount),
			'#penggunaan_uangmuka#' => DocoHelpers::formatNumber($penggunaan_uangmuka),
			'#total_dijamin#' => DocoHelpers::formatNumber($total_dijamin),
			'#pembulatan#' => DocoHelpers::formatNumber($nominalPembulatan),
			'#total_ditagihkan#' => DocoHelpers::formatNumber($total_ditagihkan),
			'#data_tabel#' => Yii::$app->controller->renderPartial($this->dokPath, [
				'data' => $this->dataTindakan,
				'grandTotal' => $this->grandTotal
			]),
		];
		return $attributes;
	}

   protected function setAttrPrintRanap()
	{
		$headerGabung = $this->headerGabung;
		$dataPendaftaran = $this->dataPendaftaran;
		$data_admin = [];
		$historyKamar = $this->getHistoryPindahKamar($this->id);
		$umur = !empty($dataPendaftaran['umur']) ? $dataPendaftaran['umur'] : '-';
		$billDate = !empty($headerGabung['tgl_invoicegabung']) ? date('d/M/Y', strtotime($headerGabung['tgl_invoicegabung'])) : null;
		$billTime = null;
		$admission = isset($this->header['tgl_admisi']) ? date('d/M/Y H:i', strtotime($this->header['tgl_admisi'])) : '-';
		$discharge_date = isset($this->header['tgl_stopakomodasi']) ? date('d/M/Y H:i', strtotime($this->header['tgl_stopakomodasi'])) : '-';
		$tgl_masuk = isset($this->header['tgl_pendaftaran']) ? date('d M Y', strtotime($this->header['tgl_pendaftaran'])) : '-';
		$tgl_keluar = isset($this->header['tgl_pasienpulang']) ? date('d M Y', strtotime($this->header['tgl_pasienpulang'])) : '-';
		$address = isset($this->detailPasien['alamat_pasien']) ? $this->detailPasien['alamat_pasien'] : '-';
		$address1 = isset($this->header['alamat']) ? $this->header['alamat'] : '-';
		$address2 = isset($this->detailPasien['kelurahan_nama']) ? $this->detailPasien['kelurahan_nama'] : '-';
		$address3 = isset($this->detailPasien['kecamatan_nama']) ? $this->detailPasien['kecamatan_nama'] : '-';
		$bed_type = isset($this->header['kelas_pelayanan']) ? $this->header['kelas_pelayanan'] : '-';
		$ward = isset($this->header['ruangan_nama']) ? $this->header['ruangan_nama'] : '-';
		$address4 = (isset($this->detailPasien['kabupaten_nama']) && isset($this->detailPasien['propinsi_nama']) && isset($this->detailPasien['warganegara'])) 
			? $this->detailPasien['kabupaten_nama'].' '.$this->detailPasien['propinsi_nama'].' '.$this->detailPasien['warganegara'] : '-';
		
		$kamarruangan_nokamar = isset($this->header['kamarruangan_nokamar']) ? $this->header['kamarruangan_nokamar'] : '-';
		$no_tempattidur = isset($this->header['no_tempattidur']) ? $this->header['no_tempattidur'] : '-';
		$dokter = '-';
		if(!empty($this->pembayaran['total_administrasi']) ){
			$data_admin = parent::getDataAdmin();
		}
		if(isset($historyKamar['dokter_admisi']) && !empty($historyKamar['dokter_admisi'])) {
			$dokter = $historyKamar['dokter_admisi'];
		}
		if(isset($historyKamar['kelas_ditagihkan_nama']) && !empty($historyKamar['kelas_ditagihkan_nama'])){
			$bed_type = $historyKamar['kelas_ditagihkan_nama'];
		}
		if(isset($historyKamar['ruangan_pindah']) && !empty($historyKamar['ruangan_pindah'])){
			$ward = $historyKamar['ruangan_pindah'];
		}
		if(isset($historyKamar['kamar_pindah']) && !empty($historyKamar['kamar_pindah'])){
			$kamarruangan_nokamar = $historyKamar['kamar_pindah'];
		}
		if(isset($historyKamar['tempattidur_pindah']) && !empty($historyKamar['tempattidur_pindah'])){
			$no_tempattidur = $historyKamar['tempattidur_pindah'];
		}

		$kunjunganRanap = $this->getKunjunganRanap();
		$hakKelas = isset($kunjunganRanap['kelas_hak']) ? $kunjunganRanap['kelas_hak'] : '-';
		$statusKelas = isset($kunjunganRanap['status_kelas']) ? $kunjunganRanap['status_kelas'] : '';
		$bed_no = $kamarruangan_nokamar.'/'.$no_tempattidur;
		$helpers = new DocoHelpers;
		$total_administrasi = isset($this->pembayaran['total_administrasi']) ? $this->pembayaran['total_administrasi'] : 0;
		$total_discount = isset($this->pembayaran['discount']) ? $this->pembayaran['discount'] : 0;
		$total_dijamin = isset($this->pembayaran['total_dijamin']) ? $this->pembayaran['total_dijamin'] : 0;
		$penggunaan_uangmuka = isset($this->pembayaran['penggunaan_uangmuka']) ? $this->pembayaran['penggunaan_uangmuka'] : 0;
		$subTotal = $this->grandTotal + $this->totalAkomodasi;

		$dataPembulatanPenjamin = $this->getPembulatan($total_dijamin);
		$nominalPembulatanPenjamin = isset($dataPembulatanPenjamin['nominalPembulatan']) ? $dataPembulatanPenjamin['nominalPembulatan'] : 0;
		$total_dijamin = isset($dataPembulatanPenjamin['total_ditagihkan']) ? $dataPembulatanPenjamin['total_ditagihkan'] : 0;

		$total_ditagihkan = ($subTotal + $total_administrasi) - $total_discount - $total_dijamin - $penggunaan_uangmuka;
		$dataPembulatan = $this->getPembulatan($total_ditagihkan);
		$nominalPembulatan = isset($dataPembulatan['nominalPembulatan']) ? $dataPembulatan['nominalPembulatan'] : 0;
		$total_ditagihkan = isset($dataPembulatan['total_ditagihkan']) ? $dataPembulatan['total_ditagihkan'] : 0;
      $no_pendaftaran = !empty($headerGabung['no_pendaftaran']) ? $headerGabung['no_pendaftaran'] : '-';
		$tgl_invoicegabung_cetak = !empty($headerGabung['tgl_invoicegabung_cetak']) ? date('d M Y', strtotime($headerGabung['tgl_invoicegabung_cetak'])) : $billDate;
		$penjamin = !empty($headerGabung['penjamin']) ? $headerGabung['penjamin'] : null;
		$jenis_kelamin = !empty($dataPendaftaran['jenis_kelamin']) ? $dataPendaftaran['jenis_kelamin'] : '-';
		
		return [
			'#printed_by#' => $this->nama_pegawai,
			'#printed_date#' => date('d m Y g:i A'),
			'#no_pendaftaran#' => $no_pendaftaran,
			'#no_rekam_medik#' => isset($dataPendaftaran['no_rekam_medik']) ? $dataPendaftaran['no_rekam_medik'] : null,
			'#nama_pasien#' => !empty($headerGabung['nama_pasien']) ? $headerGabung['nama_pasien'] : '-',
			'#gender#' => $jenis_kelamin,
			'#invoice_no#' => isset($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-',
			'#invoice_date#' => $billDate,
			'#visit_no#' => $no_pendaftaran,
			'#main_payer#' => $penjamin,
			'#cara_bayar#' => $penjamin,
			'#mr_no#' => isset($dataPendaftaran['no_rekam_medik']) ? $dataPendaftaran['no_rekam_medik'] : null,
			'#nobuktibayar#' => isset($this->pembayaran['nobuktibayar']) ? $this->pembayaran['nobuktibayar'] : '-',
			'#address#' => $address,
			'#address1#' => $address1,
			'#address2#' => $address2,
			'#address3#' => $address3,
			'#address4#' => $address4,
			'#alamat#' => $address1,
			'#payer#' => $this->dataPayer['payer'],
			'#age#' => $umur,
			'#bill_date#' => $billDate,
			'#bill_time#' => $billTime,
			'#ward#' => $ward,
			'#bed_type#' => $bed_type,
			'#kamarruangan_nokamar#' => $kamarruangan_nokamar,
			'#no_tempattidur#' => $no_tempattidur,
			'#bed_no#' => $bed_no,
			'#primary_doctor#' => isset($this->header['dokter_admisi']) ? $this->header['dokter_admisi'] : '-',
			'#admission#' => $admission,
			'#discharge_date#' => $discharge_date,
			'#lokasi#' => $this->profilRs['kota']. ', ' .date('d M Y', strtotime($billDate)),
			'#rs_name#' => !empty($this->profilRs['namaRs']) ? $this->profilRs['namaRs'] : '-',
			'#bill_no#' => !empty($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-',
			'#tgl_masuk#' => $tgl_masuk,
			'#tgl_keluar#' => $tgl_keluar,
			'#dokter#' => $dokter,
			'#doctor_name#' => isset($this->header['dok_resep']) && !empty($this->header['dok_resep']) ? $helpers->namaPasien($this->header['dok_resep']) : '-',
			'#doctor_speciality#' => isset($this->header['ruangan_nama']) ? $this->header['ruangan_nama'] : '-',
			'#no_resep#' => isset($this->header['no_resep']) ? $this->header['no_resep'] : '-',
			'#remarks#' => isset($this->header['komponen']) ? $this->header['komponen'] : '-',
			'#hakKelas#' => $hakKelas,
			'#statusKelas#' => $statusKelas,
			'#subtotal#' => DocoHelpers::formatNumber($subTotal),
			'#biaya_admin#' => DocoHelpers::formatNumber($total_administrasi),
			'#diskon#' => DocoHelpers::formatNumber($total_discount),
			'#dp#' => DocoHelpers::formatNumber($penggunaan_uangmuka),
			'#dijamin#' => DocoHelpers::formatNumber($total_dijamin),
			'#pembulatan#' => DocoHelpers::formatNumber($nominalPembulatan),
			'#dibayar_pasien#' => DocoHelpers::formatNumber($total_ditagihkan),
			'#nominalPembulatanPenjamin#' => $nominalPembulatanPenjamin,
			'#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, [
				'data' => $this->dataTindakan,
				'nama_pasien' => $this->header['nama_pasien'],
				'received' => $this->header['nama_pasien'],
				'list_dokter' => $this->arrDokter,
				'pembayaran' => $this->pembayaran,
				'isPayer' => $this->dataPayer['isPayer'],
				'listPayer' => $this->dataPayer['listPayer'],
				'listMetode' => $this->dataPayer['listMetode'],
				'bill_date' => $billDate,
				'tgl_masuk' => $tgl_masuk,
				'tgl_keluar' => $tgl_keluar,
				'data_admin' => $data_admin,
				'header' => $this->header,
				'jenis_invoice' => $this->jenis_invoice,
				'konfigSystem' => $this->konfigSystem,
				'balance' => $this->pembayaran['total_kembalian'],
				'detailAkomodasi' => $this->detailAkomodasi,
			]),
		];
	}
    
	protected function getHistoryPindahKamar($pendaftaran_id)
	{
		$data = Yii::$app->db->createCommand("
			SELECT * FROM infopindahkamar_v WHERE pendaftaran_id = {$pendaftaran_id} 
			ORDER BY pindahkamar_id DESC LIMIT 1
		")->queryOne();

		return $data;
	}
    
	protected function getPembulatan($total_ditagihkan)
	{
		$helpers = new DocoHelpers;
		$konfigSystem = $this->konfigSystem;
		$isPembulatan = isset($konfigSystem['is_pembulatankeatas']) ? $konfigSystem['is_pembulatankeatas'] : false;
		$satuanPembulatan = !empty($konfigSystem['satuanpembulatan']) ? $konfigSystem['satuanpembulatan'] : 0;
		$pembulatanTagihan = $helpers->pembulatan(round($total_ditagihkan,2), $isPembulatan, $satuanPembulatan);
		$nominalPembulatan = isset($pembulatanTagihan['pembulatan']) ? $pembulatanTagihan['pembulatan'] : 0;
		$total_ditagihkan = isset($pembulatanTagihan['total']) ? (int) $pembulatanTagihan['total'] : (int) $total_ditagihkan;
		return [
			'nominalPembulatan' => $nominalPembulatan,
			'total_ditagihkan' => $total_ditagihkan,
		];
	}

   protected function getDetailTindakan()
	{
		$data = [];
		$grandTotal = 0;
		$newData = $detailAkomodasi = [];
      $pembayaranId = $this->pembayaranId;
		$pembayaranId = "(" . implode(",", $pembayaranId) . ")";

		// $data = $this->getDetailObat();
		$totalAkomodasi = 0;
		if(!$this->isObat) {
			if(!empty($this->pasienadmisi_id)) {
					$detailAkomodasi = Yii::$app->db->createCommand("
						SELECT *  
						FROM infodetailakomodasi_v 
						WHERE pembayaran_id IN {$pembayaranId} AND tipe = 'akomodasi_tagihan'
					")->queryAll();
					if(!empty($detailAkomodasi)) {
						foreach ($detailAkomodasi as $key => $value) {
							$lamaRawat = isset($value['lama_rawat']) ? $value['lama_rawat'] : 0;
							$tarifKamar = isset($value['tarif_kamar']) ? $value['tarif_kamar'] : 0;
							$totalAkomodasi += $lamaRawat * $tarifKamar;
						}
					}
					$detail = Yii::$app->db->createCommand("
						SELECT 
							kelompoktindakan_nama,
							SUM(qty) AS qty,
							SUM(tarif_dijamin) as total_dijamin,
							SUM(tarif_dibayarkan) as total_dibayar,
							SUM(tarif_diskon) as total_diskon,
							SUM(sub_total) as sub_total
						FROM invoicesudahbayardetail_v 
						WHERE pembayaran_id IN {$pembayaranId} AND is_akomodasi = FALSE
						GROUP BY kelompoktindakan_nama
						ORDER BY kelompoktindakan_nama ASC
					")->queryAll();
			}else {
                $detail = Yii::$app->db->createCommand("
                    SELECT 
                    a.kelompoktindakan_nama,
                    SUM(a.qty) as qty,
                    SUM(a.sub_total) AS sub_total
                    FROM invoicesudahbayardetail_v a
                    WHERE pembayaran_id IN {$pembayaranId}
                    GROUP BY a.kelompoktindakan_id, a.kelompoktindakan_nama
                    ORDER BY kelompoktindakan_nama ASC
                ")->queryAll();
			}
		}else {
			$detail = Yii::$app->db->createCommand("
				SELECT 
				'OBAT ALKES' AS kelompoktindakan_nama,
				SUM(a.qty) as qty,
				SUM(a.tarif) AS sub_total
				FROM invoiceobatdetail_v a
				WHERE pembayaran_id = {$this->invoice_id}
				GROUP BY kelompoktindakan_nama
				ORDER BY kelompoktindakan_nama ASC
			")->queryAll();
		}
		if(!empty($detail)) {
			foreach ($detail as $key => $value) {
				$kelompok = isset($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : '';
				$grandTotal += isset($value['sub_total']) ? $value['sub_total'] : 0;
				$value['kelompok'] = isset($this->_mappObatAlkes[$kelompok]) ? $this->_mappObatAlkes[$kelompok] : $kelompok;
				$newData[] = $value;
			}
			$this->grandTotal = $grandTotal;
		}
		$data = $newData;
		$this->dataTindakan = $data;
		$this->detailAkomodasi = $detailAkomodasi;
		$this->totalAkomodasi = $totalAkomodasi;
	}
    
	protected function getKunjunganRanap()
	{
		$result = [];
		if($this->is_admisi){
			$result = Yii::$app->db->createCommand("
			SELECT no_pendaftaran,no_rekam_medik,nama_pasien,nama_depan,alamat_pasien,rt,rw,tgl_pendaftaran,tgl_admisi,tgl_pulang,tgl_stopakomodasi,
			jenis_kelamin,tanggal_lahir,umur,pasienadmisi_id,carabayar_id,klsrawat,is_aps,is_pasientitipan,is_pasientitipan_pk,penjamin_id,
			is_stoppasientitipan,bpjs_kelas,kelaspelayanan_id,kelaspelayanan_nama,nama_pegawai,
			carabayar_nama,penjamin_nama,ruangan_nama,kamarruangan_nokamar,no_tempattidur,
			kelas_hak,status_kelas
			FROM infokunjunganri_v 
			WHERE pasienadmisi_id = {$this->pasienadmisi_id}")
			->queryOne();
		}
		return $result;
	}

   protected function populateData()
	{
      $request = $this->_requestData;
		$db = Yii::$app->db;
		$nama_pegawai = $request->get('nama_pegawai', null);
      $this->pegawai_login = $nama_pegawai;
		$invoicegabung_id = $request->get('invoicegabung_id', null);
		$headerGabung = InfGabungInvoiceView::findOne($invoicegabung_id);
      $this->headerGabung = $headerGabung;
		$detailGabung = InvoiceGabungDetail::find()->where(['invoicegabung_id' => $invoicegabung_id])->asArray()->all();
      $this->detailGabung = $detailGabung;
		$pembayaranId = [];
		$pendaftaranId = null;
		if(!empty($detailGabung)) {
			foreach ($detailGabung as $key => $value) {
				$pendaftaranId = isset($value['pendaftaran_id']) ? $value['pendaftaran_id'] : '';
				$pembayaranId[] = isset($value['pembayaran_id']) ? $value['pembayaran_id'] : '';
			}
		}
      
		$this->pembayaranId = $pembayaranId;
		$pendaftaranId = !empty($headerGabung['pendaftaran_id_cetak']) ? $headerGabung['pendaftaran_id_cetak'] : $pendaftaranId;
      $this->id = $pendaftaranId;
		$dataPendaftaran = InfoDataPendaftaran::find()->where(['pendaftaran_id' => $pendaftaranId])->one();
      $this->dataPendaftaran = $dataPendaftaran;
		$pasienadmisi_id = isset($dataPendaftaran['pasienadmisi_id']) ? $dataPendaftaran['pasienadmisi_id'] : null;
      $this->pasienadmisi_id = $pasienadmisi_id;
		$this->getDetailTindakan();
		$no_rekam_medik = isset($dataPendaftaran['no_rekam_medik']) ? $dataPendaftaran['no_rekam_medik'] : null;
		$pembayaranId = "(" . implode(",", $pembayaranId) . ")";
		$header = $this->getHeader($pembayaranId, $pendaftaranId, $pasienadmisi_id);
      $this->header = $header;
		$pembayaran = $this->getPembayaran($pembayaranId);
      $this->pembayaran = $pembayaran;
		$nobuktibayar = '';
		$detailPasien = $this->getDetailPasien($no_rekam_medik);
      $this->detailPasien = $detailPasien;
		$dataPayer = $this->getPayer($pembayaranId);
		$this->dataPayer = $dataPayer;
		$profilRs = $this->getProfileRs();
		$this->profilRs = $profilRs;
		$this->konfigSystem = $this->getKonfigSistem();
   }

	protected function processFlow()
	{
		$attributes = [];
		$this->populateData();
		if(!empty($this->pasienadmisi_id)){
			$this->is_admisi = true;
			$this->dokPath = '../tagihan-pasien/invoice-kramat-ranap';
			$this->kode_doc = 'summary-invoice-ranap';
			$attributes = $this->setAttrPrintRanap();

		}else{
			$this->is_admisi = false;
			$attributes = $this->setAttrPrintNonRanap();
		}
	
		$print = new DocoPrint($this->kode_doc);
		$print->attributes = $attributes;
		$print->Output();
	}
}