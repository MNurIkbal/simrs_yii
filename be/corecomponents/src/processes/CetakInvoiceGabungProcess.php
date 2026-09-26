<?php

/**
 * @author : Dede herdiana (dede.herdiana@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

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
use app\modules\v1\cache\Cache;
use app\modules\v1\models\DaftarTindakan;

class CetakInvoiceGabungProcess extends \Doco\components\DocoBaseProcessExtension
{
	protected $kode_doc = 'invoice-summary-gabung';
	protected $dokPath = 'invoice-gabung';
    
	protected function getHeader($pembayaranId, $pendaftaran_id, $pasienadmisi_id)
	{
		$result = Yii::$app->db->createCommand("
				SELECT * 
				FROM
				invoicesudahbayar_v pasienrj
				WHERE pasienrj.pembayaran_id IN {$pembayaranId}")
			->queryOne();
		
		if(!empty($pasienadmisi_id)) {
			$result = InfoPasienRiView::find()->select([
				'no_rekam_medik',
				'no_pendaftaran',
				'nama_pasien',
				'alamat_pasien',
				'umur',
				'jenis_kelamin',
				'ruangan_nama',
				'kelas_pelayanan',
				'kelas_ditagihkan_nama',
				'kamarruangan_nokamar',
				'no_tempattidur',
				'dokter_admisi',
				'tgl_admisi',
				'tgl_stopakomodasi',
				'tanggal_lahir'
			])->where([
					'pendaftaran_id' => $pendaftaran_id
			])->asArray()->one();
		}
		$kelas_ditagihkan = Yii::$app->db->createCommand("
			SELECT
			kelas_ditagihkan
			FROM
			infodatapendaftaran_v infodata
			WHERE infodata.pendaftaran_id = {$pendaftaran_id}")->queryOne();

		if(is_array($result)&& is_array($kelas_ditagihkan)){
			$result = array_merge($result,$kelas_ditagihkan);
		}

		return $result;
	}

    protected function getPembayaran($pembayaranId)
	{
		$data = Yii::$app->db->createCommand("
			SELECT
				total_tagihan + total_pembulatan as total_tagihan,
				total_administrasi,
				total_discount + total_discountpembayaran AS discount,
				sisa_uangmuka AS penggunaan_uangmuka,
				total_dijamin AS total_dijamin,
				total_sisatagihan,
				total_ditagihkan - penggunaan_uangmuka as total_ditagihkan,
				total_sisatagihan + total_ditagihkan as patient_amount,
				total_tunai,
				total_nontunai,
				total_kembalian,
				total_pembulatan,
				pembulatan,
				additional_data
			FROM pembayaran_t 			
			WHERE pembayaran_id IN {$pembayaranId}
			AND is_deleted = FALSE
		")->queryAll();
		
		$admAsuransi = $pembayaranPelayanan = $pembayaranPenjamin = $pembayaranJenisPembayaran = $pembayaranDiskon = $additional = $additional_data = [];
		$total_tagihan = $total_administrasi = $discount = $penggunaan_uangmuka = $total_dijamin = $total_sisatagihan = $total_ditagihkan = 0;
		$patient_amount = $total_tunai = $total_nontunai = $total_kembalian = $total_pembulatan = $pembulatan = 0;

		if(!empty($data)) {
			foreach ($data as $key => $value) {
				$additional = isset($value['additional_data']) ? $value['additional_data'] : [];
				$additional_data[] = $additional;
				if(!empty($additional)) {
					$additional = json_decode($additional, true);
					$admAsuransi[] = isset($additional['adm_asuransi']) ? $additional['adm_asuransi'] : [];
					$pembayaranPelayanan[] = isset($additional['pembayaran_pelayanan']) ? $additional['pembayaran_pelayanan'] : [];
					$pembayaranPenjamin[] = isset($additional['pembayaran_penjamin']) ? $additional['pembayaran_penjamin'] : [];
					$pembayaranJenisPembayaran[] = isset($additional['pembayaran_jenis_pembayaran']) ? $additional['pembayaran_jenis_pembayaran'] : [];
					$pembayaranDiskon[] = isset($additional['pembayaran_diskon']) ? $additional['pembayaran_diskon'] : [];
				}

				/** blok buat bikin summary */
				$tmp_total_tagihan = !empty($value['total_tagihan']) ? $value['total_tagihan'] : 0;
				$tmp_total_administrasi = !empty($value['total_administrasi']) ? $value['total_administrasi'] : 0;
				$tmp_discount = !empty($value['discount']) ? $value['discount'] : 0;
				$tmp_penggunaan_uangmuka = !empty($value['penggunaan_uangmuka']) ? $value['penggunaan_uangmuka'] : 0;
				$tmp_total_dijamin = !empty($value['total_dijamin']) ? $value['total_dijamin'] : 0;
				$tmp_total_sisatagihan = !empty($value['total_sisatagihan']) ? $value['total_sisatagihan'] : 0;
				$tmp_total_ditagihkan = !empty($value['total_ditagihkan']) ? $value['total_ditagihkan'] : 0;
				$tmp_patient_amount = !empty($value['patient_amount']) ? $value['patient_amount'] : 0;
				$tmp_total_tunai = !empty($value['total_tunai']) ? $value['total_tunai'] : 0;
				$tmp_total_nontunai = !empty($value['total_nontunai']) ? $value['total_nontunai'] : 0;
				$tmp_total_kembalian = !empty($value['total_kembalian']) ? $value['total_kembalian'] : 0;
				$tmp_total_pembulatan = !empty($value['total_pembulatan']) ? $value['total_pembulatan'] : 0;
				$tmp_pembulatan = !empty($value['pembulatan']) ? $value['pembulatan'] : 0;

				
				$total_tagihan += $tmp_total_tagihan;
				$total_administrasi += $tmp_total_administrasi;
				$discount += $tmp_discount;
				$penggunaan_uangmuka += $tmp_penggunaan_uangmuka;
				$total_dijamin += $tmp_total_dijamin;
				$total_sisatagihan += $tmp_total_sisatagihan;
				$total_ditagihkan += $tmp_total_ditagihkan;
				$patient_amount += $tmp_patient_amount;
				$total_tunai += $tmp_total_tunai;
				$total_nontunai += $tmp_total_nontunai;
				$total_kembalian += $tmp_total_kembalian;
				$total_pembulatan += ($tmp_total_pembulatan > 0 && $tmp_total_dijamin > 0) ? (($tmp_total_pembulatan - $tmp_pembulatan)) : 0;
				$pembulatan += ($tmp_patient_amount > 0 && $tmp_pembulatan <= 0 ) ? $tmp_total_pembulatan : $tmp_pembulatan;
			}
		}

		/** get total biaya administrasi dijamin dan dibayarkan pasien */
		$admPayer = $admPatient = 0;
		if(!empty($admAsuransi) && is_array($admAsuransi)){
			foreach($admAsuransi as $val){
				$admPayer += !empty($val['dijamin']) ? $val['dijamin'] : 0;
				$admPatient += !empty($val['harusbayar']) ? $val['harusbayar'] : 0;
			}
		}

		$data = [
			 'total_tagihan' => $total_tagihan,
			 'total_administrasi' => $total_administrasi,
			 'discount' => $discount,
			 'penggunaan_uangmuka' => $penggunaan_uangmuka,
			 'total_dijamin' => $total_dijamin,
			 'total_sisatagihan' => $total_sisatagihan,
			 'total_ditagihkan' => $total_ditagihkan,
			 'patient_amount' => $patient_amount,
			 'total_tunai' => $total_tunai,
			 'total_nontunai' => $total_nontunai,
			 'total_kembalian' => $total_kembalian,
			 'total_pembulatan' => $total_pembulatan,
			 'pembulatan' => $pembulatan,
			 'admAsuransi' => $admAsuransi,
			 'pembayaranPelayanan' => $pembayaranPelayanan,
			 'pembayaranPenjamin' => $pembayaranPenjamin,
			 'pembayaranJenisPembayaran' => $pembayaranJenisPembayaran,
			 'pembayaranDiskon' => $pembayaranDiskon,
			 'additional_data' => $additional_data,
			 'admPayer' => $admPayer,
			 'admPatient' => $admPatient,
		];

		return $data;
	}

    
	protected function getDetailPasien($no_rekam_medik)
	{
		return PasienView::find()->select([
			'alamat_pasien',
			'propinsi_nama', 
			'kabupaten_nama',
			'kecamatan_nama', 
			'kelurahan_nama', 
			'warganegara'
		])->where([
			'no_rekam_medik' => $no_rekam_medik
		])->asArray()->one();
	}

    protected function getPayer($pembayaranId)
	{
		$listPayer = $listMetode = $listNewPayer = [];
		$isPayer = false;
		$payer = '';
		$pembayaran = $this->pembayaran;
		$total_dijamin = !empty($pembayaran['total_dijamin']) ? $pembayaran['total_dijamin'] : 0;
		if ($total_dijamin > 0) {
			$isPayer = true;
			$listPayer = Yii::$app->db->createCommand("
				SELECT 
				penjamin_nama,
				SUM(total_dijamin) AS total_dijamin
				FROM pembayaranpenjamin_t
				WHERE pembayaran_id IN {$pembayaranId}
				GROUP BY penjamin_nama
			")->queryAll();

			if (!empty($listPayer)) {
				foreach ($listPayer as $value) {
					$penjaminNama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
					$listNewPayer[] = $penjaminNama;
				}
				$payer = implode(", ", $listNewPayer);
			}
		}

		if (!empty($pembayaran['total_nontunai'])) {
			$listMetode = Yii::$app->db->createCommand("
				SELECT 
					metode_bayar,
					total_dibayar,
					no_kartu
				FROM pembayaranmetode_t
				WHERE pembayaran_id IN {$pembayaranId}
			")->queryAll();
		}

		return [
			'payer' => $payer,
			'isPayer' => $isPayer,
			'listPayer' => $listPayer,
			'listMetode' => $listMetode,
		];
	}
    
	protected function getUmur($tgl_lahir, $yearOnly = false)
	{
		$umur = '-';
		if (!empty($tgl_lahir)) {
			if($yearOnly) {
				$umur = DocoHelpers::getUmur($tgl_lahir, true, false).' Years';
			}
			else {
				$umur = DocoHelpers::getUmur($tgl_lahir);
				$umur = str_replace("tahun", "Year(s)", $umur);
				$umur = str_replace("bulan", "Month(s)", $umur);
				$umur = str_replace("hari", "Day(s)", $umur);
			}
		}

		return $umur;
	}

	protected function getKonfigSistem()
	{
		return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
			return KonfigSystem::find()->asArray()->one();
		});
	}
    
	protected function getProfileRs()
	{
		$profilRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
			return ProfilRsView::find()->asArray()->one();
		});

		$kota = '-';
		$namaRs = '-';
		if (!empty($profilRs['nama_rumahsakit'])) {
		$namaRs = $profilRs['nama_rumahsakit'];
		}

		if (!empty($profilRs['kota'])) {
		if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
			$pattern = "KOTA ADM. ";
		}
		elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
			$pattern = "KAB. ADM. ";
		}
		elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
			$pattern = "KAB. ";
		}
		elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
			$pattern = "KOTA ";
		}

		$kota = str_replace($pattern,"", $profilRs['kota']);
		}
		
		return [
			'namaRs' => $namaRs,
			'kota' => $kota,
		];
	}

    protected function getDataAdmin(){
        $confSistem = Cache::getKonfigSistem();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $model = new DaftarTindakan;
        $query = $model::find()->where(['daftartindakan_id'=>$admTindakanId])->asArray()->one();

        return $query;
    }

    protected function processFlow()
    {
        $request = $this->_requestData;
		$db = Yii::$app->db;
		$nama_pegawai = $request->get('nama_pegawai', null);
		$invoicegabung_id = $request->get('invoicegabung_id', null);
		$headerGabung = InfGabungInvoiceView::findOne($invoicegabung_id);
		$detailGabung = InvoiceGabungDetail::find()->where(['invoicegabung_id' => $invoicegabung_id])->asArray()->all();
		$pembayaranId = [];
		$pendaftaranId = null;
		if(!empty($detailGabung)) {
			foreach ($detailGabung as $key => $value) {
				$pendaftaranId = isset($value['pendaftaran_id']) ? $value['pendaftaran_id'] : ''; //ini untuk handle data lama dimana yang digabungkan hanya satu pendaftaran aja
				$pembayaranId[] = isset($value['pembayaran_id']) ? $value['pembayaran_id'] : '';
			}
		}
		$pendaftaranId = !empty($headerGabung['pendaftaran_id_cetak']) ? $headerGabung['pendaftaran_id_cetak'] : $pendaftaranId; // pendaftaran_id tujuan untuk dicetakan. ini untuk override pendaftaran id dengan rules baru pendaftaran bisa dua no pendaftaran
		$dataPendaftaran = InfoDataPendaftaran::find()->where(['pendaftaran_id' => $pendaftaranId])->one();
		$pasienadmisi_id = isset($dataPendaftaran['pasienadmisi_id']) ? $dataPendaftaran['pasienadmisi_id'] : null;
		$no_rekam_medik = isset($dataPendaftaran['no_rekam_medik']) ? $dataPendaftaran['no_rekam_medik'] : null;
		$pembayaranId = "(" . implode(",", $pembayaranId) . ")";
		$header = $this->getHeader($pembayaranId, $pendaftaranId, $pasienadmisi_id);
		$pembayaran = $this->getPembayaran($pembayaranId);
		$this->pembayaran = $pembayaran;
		$nobuktibayar = '';
		$detailPasien = $this->getDetailPasien($no_rekam_medik);
		$dataPayer = $this->getPayer($pembayaranId);
		$address = isset($detailPasien['alamat_pasien']) ? $detailPasien['alamat_pasien'] : '-';
		$address1 = isset($header['alamat']) ? $header['alamat'] : '-';
		$address2 = isset($detailPasien['kelurahan_nama']) ? $detailPasien['kelurahan_nama'] : '-';
		$address3 = isset($detailPasien['kecamatan_nama']) ? $detailPasien['kecamatan_nama'] : '-';
		$bed_type = isset($header['kelas_pelayanan']) ? $header['kelas_pelayanan'] : '-';
		$bed_no = (isset($header['kamarruangan_nokamar']) && isset($header['no_tempattidur'])) ? $header['kamarruangan_nokamar'].' '.$header['no_tempattidur'] : '-';
		$address4 = (isset($detailPasien['kabupaten_nama']) && isset($detailPasien['propinsi_nama']) && isset($detailPasien['warganegara'])) 
			? $detailPasien['kabupaten_nama'].' '.$detailPasien['propinsi_nama'].' '.$detailPasien['warganegara'] : '-';

		$dataDetail = $db->createCommand("
			SELECT 
			kelompoktindakan_nama, 
			tarif_dijamin,
			tarif_dibayarkan,
			tarif_diskon,
			sub_total
			FROM invoicesudahbayardetail_v 
			WHERE pembayaran_id IN {$pembayaranId}
		")->queryAll();

		$detail = $kelompok = [];
		foreach($dataDetail as $val){
			$kelompoktindakan_nama = !empty($val['kelompoktindakan_nama']) ? $val['kelompoktindakan_nama'] : null;
			$tarif_dijamin = !empty($val['tarif_dijamin']) ? $val['tarif_dijamin'] : null;
			$tarif_dibayar = !empty($val['tarif_dibayarkan']) ? $val['tarif_dibayarkan'] : null;
			$tarif_diskon = !empty($val['tarif_diskon']) ? $val['tarif_diskon'] : null;
			$sub_total = !empty($val['sub_total']) ? $val['sub_total'] : null;
			$diskon_pasien = (!empty($tarif_dibayar)) ? $tarif_diskon : 0;
			$diskon_payer = (!empty($tarif_dijamin) && empty($tarif_dibayar)) ? $tarif_diskon : 0;

			$kelompok[$kelompoktindakan_nama][] = [
				'kelompoktindakan_nama' => $kelompoktindakan_nama,
				'tarif_dijamin' => $tarif_dijamin,
				'tarif_dibayar' => $tarif_dibayar,
				'tarif_diskon' => $tarif_diskon,
				'diskon_pasien' => $diskon_pasien,
				'diskon_payer' => $diskon_payer,
				'sub_total' => $sub_total,
			];
		}

		foreach($kelompok as $value){
			$total_dijamin = $total_dibayar = $total_diskon = $total_diskon_pasien = $total_diskon_payer = $sub_total = 0;
			foreach($value as $val){
				$kelompoktindakan_nama = !empty($val['kelompoktindakan_nama']) ? $val['kelompoktindakan_nama'] : null;
				$tarif_dijamin = !empty($val['tarif_dijamin']) ? $val['tarif_dijamin'] : null;
				$tarif_dibayar = !empty($val['tarif_dibayar']) ? $val['tarif_dibayar'] : null;
				$tarif_diskon = !empty($val['tarif_diskon']) ? $val['tarif_diskon'] : null;
				$sub_total = !empty($val['sub_total']) ? $val['sub_total'] : null;
				$diskon_pasien = (!empty($tarif_dibayar)) ? $tarif_diskon : 0;
				$diskon_payer = (!empty($tarif_dijamin) && empty($tarif_dibayar)) ? $tarif_diskon : 0;

				$total_dijamin += $tarif_dijamin;
				$total_dibayar += $tarif_dibayar;
				$total_diskon += $tarif_diskon;
				$total_diskon_pasien += $diskon_pasien;
				$total_diskon_payer += $diskon_payer;
				$sub_total += $sub_total;
			}
			$detail[] = [
				'kelompoktindakan_nama' => $kelompoktindakan_nama,
				'total_dijamin' => $total_dijamin,
				'total_dibayar' => $total_dibayar,
				'total_diskon' => $total_diskon,
				'total_diskon_pasien' => $total_diskon_pasien,
				'total_diskon_payer' => $total_diskon_payer,
				'sub_total' => $sub_total,
			];
		}
		
		$billDate = !empty($headerGabung['tgl_invoicegabung']) ? $headerGabung['tgl_invoicegabung'] : null;
		$tgl_invoicegabung_cetak = !empty($headerGabung['tgl_invoicegabung_cetak']) ? date('d/M/Y H:i:s', strtotime($headerGabung['tgl_invoicegabung_cetak'])) : '';
		$penjamin = !empty($headerGabung['penjamin']) ? $headerGabung['penjamin'] : null;
        $no_pendaftaran = !empty($headerGabung['no_pendaftaran']) ? $headerGabung['no_pendaftaran'] : '-';
		$admission = isset($header['tgl_admisi']) ? date('d/m/Y H:i', strtotime($header['tgl_admisi'])) : '-';
		$discharge_date = isset($header['tgl_stopakomodasi']) ? date('d/m/Y H:i', strtotime($header['tgl_stopakomodasi'])) : '-';
		$tgl_masuk = isset($header['tgl_pendaftaran']) ? date('d M Y', strtotime($header['tgl_pendaftaran'])) : '-';
		$tgl_keluar = isset($header['tgl_pasienpulang']) ? date('d M Y', strtotime($header['tgl_pasienpulang'])) : '-';
		$umur = isset($header['tanggal_lahir']) && !empty($header['tanggal_lahir']) ? $this->getUmur($header['tanggal_lahir']) : '-';
		$profilRs = $this->getProfileRs();
		$dokter = '-';
		if(isset($header['dok_ranap'])) {
			$dokter = $header['dok_ranap'];
		}
		elseif(isset($header['dok_pendaftaran'])) {
			$dokter = $header['dok_pendaftaran'];
		}

		if(isset($header['kelas_ditagihkan'])){
			$bed_type = $header['kelas_ditagihkan'];
		}
		$constantsId = new DocoConstansId;
		$tindakan_keperawatan = $constantsId->ActionGetAdditional('tindakan_keperawatan');
		$tindakan_keperawatan = json_decode($tindakan_keperawatan, true);
		$konfigSystem = $this->getKonfigSistem();
		
		if (!empty($pasienadmisi_id)) {
			$this->kode_doc = 'invoice-summary-gabung-ri';
		}
		$print = new DocoPrint($this->kode_doc);
		$total_dijamin = isset($pembayaran['total_dijamin']) ? $pembayaran['total_dijamin'] : 0;
		$total_pembulatan = isset($pembayaran['total_pembulatan']) ? $pembayaran['total_pembulatan'] : 0;
		$pembulatan = isset($pembayaran['pembulatan']) ? $pembayaran['pembulatan'] : 0;
		$penggunaan_uangmuka = isset($pembayaran['penggunaan_uangmuka']) ? $pembayaran['penggunaan_uangmuka'] : 0;
		$total_sisatagihan = isset($pembayaran['total_sisatagihan']) ? $pembayaran['total_sisatagihan'] : 0;
		$total_ditagihkan = isset($pembayaran['total_ditagihkan']) ? $pembayaran['total_ditagihkan'] : 0;
		$total_tagihan = isset($pembayaran['total_tagihan']) ? $pembayaran['total_tagihan'] : 0;
		$total_tunai = isset($pembayaran['total_tunai']) ? $pembayaran['total_tunai'] : 0;
		$total_dibayar = isset($pembayaran['total_dibayar']) ? $pembayaran['total_dibayar'] : 0;
		$total_kembalian = isset($pembayaran['total_kembalian']) ? $pembayaran['total_kembalian'] : 0;
		$roundeBillAmountPayer = $total_pembulatan;
		$roundeBillAmountPatient = $pembulatan;
		$additionalInvoice = !empty($pembayaran['additional_data']) ? $pembayaran['additional_data'] : [];
		$admDetail = isset($pembayaran['admAsuransi']) ? $pembayaran['admAsuransi'] : [];
		$nominalAdm = !empty($pembayaran['total_administrasi']) ? $pembayaran['total_administrasi'] : 0;
		$discount = !empty($pembayaran['discount']) ? $pembayaran['discount'] : 0;
		$admPatient = isset($pembayaran['admPatient']) ? $pembayaran['admPatient'] : 0;
		$admPayer = isset($pembayaran['admPayer']) ? $pembayaran['admPayer'] : 0;

		$print->attributes = [
			'#printed_by#' => $nama_pegawai,
			'#printed_date#' => date('d m Y g:i A'),
			'#no_pendaftaran#' => $no_pendaftaran,
			'#no_rekam_medik#' => !empty($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-',
			'#nama_pasien#' => $header['nama_pasien'],
			'#gender#' => $header['jenis_kelamin'],
			'#invoice_no#' => isset($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-',
			'#invoice_date#' => !empty($billDate) ? date('d/M/Y', strtotime($billDate)) : '-',
			'#visit_no#' => $header['no_pendaftaran'],
			'#main_payer#' => $penjamin,
			'#cara_bayar#' => isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '-',
			'#mr_no#' => !empty($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-',
			'#nobuktibayar#' => $nobuktibayar,
			'#address#' => $address,
			'#address1#' => $address1,
			'#address2#' => $address2,
			'#address3#' => $address3,
			'#address4#' => $address4,
			'#alamat#' => $address1,
			'#payer#' => isset($dataPayer['payer']) ? $dataPayer['payer'] : '-',
			'#age#' => $umur,
			'#bill_date#' => !empty($billDate) ? date('d/M/Y', strtotime($billDate)) : '-',
			'#tgl_invoicegabung_cetak#' => $tgl_invoicegabung_cetak,
			'#bill_time#' => !empty($billDate) ? date('d/M/Y', strtotime($billDate)) : '-',
			'#ward#' => isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '-',
			'#bed_type#' => $bed_type,
			'#bed_no#' => $bed_no,
			'#primary_doctor#' => isset($header['dokter_admisi']) ? $header['dokter_admisi'] : '-',
			'#admission#' => $admission,
			'#discharge_date#' => $discharge_date,
			'#lokasi#' => $profilRs['kota']. ', ' .(!empty($billDate) ? date('d M Y', strtotime($billDate)) : ''),
			'#rs_name#' => !empty($profilRs['namaRs']) ? $profilRs['namaRs'] : '-',
			'#bill_no#' => isset($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-',
			'#tgl_masuk#' => $tgl_masuk,
			'#tgl_keluar#' => $tgl_keluar,
			'#dokter#' => $dokter,
			'#doctor_name#' => isset($header['dok_resep']) && !empty($header['dok_resep']) ? DocoHelpers::namaPasien($header['dok_resep']) : '-',
			'#doctor_speciality#' => isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '-',
			'#no_resep#' => isset($header['no_resep']) ? $header['no_resep'] : '-',
			'#remarks#' => isset($header['komponen']) ? $header['komponen'] : '-',
			'#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, [
				'tindakan_keperawatan' => $tindakan_keperawatan,
				'data' => $detail,
				'nama_pasien' => $header['nama_pasien'],
				'received' => $header['nama_pasien'],
				'pembayaran' => $pembayaran,
				'isPayer' => isset($dataPayer['isPayer']) ? $dataPayer['isPayer'] : false,
				'listPayer' => isset($dataPayer['listPayer']) ? $dataPayer['listPayer'] : [],
				'listMetode' => isset($dataPayer['listMetode']) ? $dataPayer['listMetode'] : [],
				'bill_date' => $billDate,
				'tgl_masuk' => $tgl_masuk,
				'tgl_keluar' => $tgl_keluar,
				'header' => $header,
				'konfigSystem' => $konfigSystem,
				'balance' => !empty($pembayaran['total_kembalian']) ? $pembayaran['total_kembalian'] : 0,
				'total_dijamin' => $total_dijamin,
				'total_pembulatan' => $total_pembulatan,
				'pembulatan' => $pembulatan,
				'penggunaan_uangmuka' => $penggunaan_uangmuka,
				'total_sisatagihan' => $total_sisatagihan,
				'total_ditagihkan' => $total_ditagihkan,
				'total_tagihan' => $total_tagihan,
				'total_tunai' => $total_tunai,
				'total_dibayar' => $total_dibayar,
				'total_kembalian' => $total_kembalian,
				'roundeBillAmountPayer' => $roundeBillAmountPayer,
				'roundeBillAmountPatient' => $roundeBillAmountPatient,
				'additionalInvoice' => $additionalInvoice,
				'admDetail' => $admDetail,
				'nominalAdm' => $nominalAdm,
				'discount' => $discount,
				'admPatient' => $admPatient,
				'admPayer' => $admPayer,
			]),
		];
		$print->Output();
    }
}
