<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\models\Pegawai;
use app\modules\v1\businessLogic\TagihanHelper;
use app\modules\v1\models\RincianPasienDetailView;
use app\modules\v1\models\PembayaranPelayanan;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\RincianPasienDetail2View;
use app\modules\v1\models\BayarUangMuka;
use app\modules\v1\models\PasienBelumBayar;
use app\modules\v1\models\PemberianPiutang;
use app\modules\v1\models\PendaftaranPenjamin;
use app\modules\v1\models\InfoDataPendaftaran;
use Doco\models\ProfilRsView;

class PathDetailRincianKramat extends \Doco\processes\PathDetailRincianProcess
{
	public $dokPath = 'detail-rincian-kramat-v2';
	protected $kode_doc = 'detail-invoice-pembayaran-ri-kramat';
	protected $header;
	protected $historyKamar;
	protected $grandTotal = 0;
	protected $data = [];
	protected $kelaspelayanan_nama;
	protected $kelas_ditagihkan_nama;
	protected $ruangan_nama;
	protected $kamar_pindah;
	protected $tempattidur_pindah;
	protected $dokter_admisi;
	protected $ruangan_titipan_nama;
	protected $hakKelas;
	protected $statusKelas;
	protected $kelas_ditagihkan_id;
	protected $detailPasien = [];
	
	protected function group_by($key, $data)
	{
		$result = array();
		foreach ($data as $val) {
			if (array_key_exists($key, $val)) {
				$result[$val[$key]][] = $val;
			} else {
				$result[""][] = $val;
			}
		}

		return $result;
	}

	protected function getData()
	{
		$request = $this->_requestData;
		$id = $request->get('id', null);
		$this->id = $id;
		$header = $this->getKunjunganRanap($id);
		$infoPasien = $this->getDataPendaftaranRincian($id);
		$historyKamar = $this->getHistoryPindahKamar($id);
		$this->infoPasien = $infoPasien;
		$this->header = $header;
		$this->historyKamar = $historyKamar;
		$pasienadmisi_id = isset($header['pasienadmisi_id']) ? $header['pasienadmisi_id'] : '';
		$is_admisi = !empty($pasienadmisi_id) ? true : false;
		$this->is_admisi = $is_admisi;
		$this->pembayaran = $this->getInvoice($id);
		$kelaspelayanan_id = isset($header['kelaspelayanan_id']) ? $header['kelaspelayanan_id'] : null;
		$kelaspelayanan_nama = isset($header['kelaspelayanan_nama']) ? $header['kelaspelayanan_nama'] : null;
		$penjamin_id = !empty($header['penjamin_id']) ? $header['penjamin_id'] : null;
		$dataTotal = $this->getTotalHeaderPembayaran($id, $header);
		$ruangan_nama = isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '';
		$kelas_ditagihkan_nama = $kamar_pindah = $tempattidur_pindah = $dokter_admisi = $ruangan_titipan_nama = $kelas_ditagihkan_id = '';
		if(!empty($historyKamar)) {
			$kamar_pindah = isset($historyKamar['kamar_pindah']) ? $historyKamar['kamar_pindah'] : '';
			$tempattidur_pindah = isset($historyKamar['tempattidur_pindah']) ? $historyKamar['tempattidur_pindah'] : '';
			$dokter_admisi = isset($historyKamar['dokter_admisi']) ? $historyKamar['dokter_admisi'] : '';
			$ruangan_pindah = isset($historyKamar['ruangan_pindah']) ? $historyKamar['ruangan_pindah'] : '';
			$ruangan_titipan_nama = isset($historyKamar['ruangan_pindah']) ? $historyKamar['ruangan_pindah'] : '';
			$kelaspelayanan_id = isset($historyKamar['kelaspelayanan_id']) ? $historyKamar['kelaspelayanan_id'] : '';
			$kelas_ditagihkan_id = isset($historyKamar['kelas_ditagihkan_id']) ? $historyKamar['kelas_ditagihkan_id'] : '';
			$kelas_ditagihkan_nama = isset($historyKamar['kelas_ditagihkan_nama']) ? $historyKamar['kelas_ditagihkan_nama'] : '';
			if(!empty($ruangan_pindah)) {
				$ruangan_nama = $ruangan_pindah;
			}
			if(!empty($dokter_admisi)) {
				$dokter_admisi = $dokter_admisi;
			}
			if(!empty($tempattidur_pindah)) {
				$tempattidur_pindah = $tempattidur_pindah;
			}
			if(!empty($kamar_pindah)) {
				$kamar_pindah = $kamar_pindah;
			}
			if(!empty($kelas_ditagihkan_id)) {
				$kelaspelayanan_id = $kelas_ditagihkan_id;
			}
			// if(!empty($kelas_ditagihkan_nama)) {
			// 	$kelaspelayanan_nama = $kelas_ditagihkan_nama;
			// }
			if(!empty($ruangan_titipan_nama)) {
				$ruangan_nama = $ruangan_titipan_nama;
			}
		}
		$biayaAdminSudahBayar = $this->getBiayaAdmSudahBayar($id);
		$biayaAdminSudahBayar = isset($biayaAdminSudahBayar['total_administrasi']) ? $biayaAdminSudahBayar['total_administrasi'] : 0;
		$biayaAdmin = TagihanHelper::getBiayaAdmin($id, $penjamin_id, $kelaspelayanan_id, $dataTotal['total_tagihan'], $pasienadmisi_id);
		// if($biayaAdmin > 0) {
		// 	$biayaAdmin = $biayaAdmin - $biayaAdminSudahBayar;
		// }
		$hakKelas = isset($header['klsrawathak']) ? $header['klsrawathak'] : '';
		$kelasHak = isset($header['kelas_hak']) ? $header['kelas_hak'] : '';
		$hakKelas = empty($hakKelas) ? $kelasHak : $hakKelas;
		$hakKelas = DocoHelpers::numberToRomanRepresentation($hakKelas);
		$statusKelas = isset($header['status_kelas']) ? $header['status_kelas'] : '';

		if(!$is_admisi){
			$this->dokPath = 'detail-invoice-non-ranap-kramat';
			$this->kode_doc = 'invoice-kramat';
			$qDetail = $this->getDataDetailTagihan($id);
			$akomodasi = [
				'tindakan_akomodasi' => null,
				'total_akomodasi' => null,
				'kelompok_tindakan' => null
			];
			if(!empty($admisiId)){
				$akomodasi = $this->getAkomodasi($admisiId);
			}
			$this->akomodasi = $akomodasi;
		}else{
			$gabungBilling = $this->gabungBilling($this->id);
			$pendaftaranId = isset($gabungBilling['pendaftaran_id']) ? $gabungBilling['pendaftaran_id'] : null;
			$qDetail = RincianPasienDetailView::find()
				->where(['tindakansudahbayar_id' => null]);

			$condPendaftaranId = !empty($pendaftaranId) ? [$this->id, $pendaftaranId] : $this->id;
			$qDetail = $qDetail
				->andWhere(['pendaftaran_id' => $condPendaftaranId])
				->andWhere(['>', 'jumlah_tarif', 0])
				->orderBy(['tgl_pelayanan' => SORT_ASC])
				->asArray()->all();

			$this->getPasienRanap($pasienadmisi_id);
		}
		$detail = $data_admin = [];
		$grandTotal = 0;
		foreach($qDetail as $value){
			if($is_admisi) {
				$sub_total = isset($value['jumlah_tarif']) ? $value['jumlah_tarif'] : 0;
				$ruangan = !empty($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : '';
			}
			else {
				$sub_total = isset($value['sub_total']) ? $value['sub_total'] : 0;
				$ruangan = !empty($value['pelayanan']) ? $value['pelayanan'] : '';
				$total_jpk = 0;
				$jpk_id =json_decode((new DocoConstansId)->actionGetAdditional("JPK"));
				if(!empty($daftarTindakanId) && in_array($daftarTindakanId, $jpk_id)) {
					$total_jpk += $sub_total;
				}
			}
			$daftarTindakanId = isset($value['tindakan_obat_id']) ? $value['tindakan_obat_id'] : '';
			$kelompok = !empty($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : null;
			$tindakan_obat_nama = !empty($value['tindakan_obat_nama']) ? $value['tindakan_obat_nama'] : null;
			if(!empty($ruangan)) {
				if($is_admisi) {
					if ($value['jumlah_tarif'] > 0) {
						$value['tarif_satuan'] = isset($value['tarif_satuan']) ? $value['tarif_satuan'] : 0;
						$detail[$ruangan][$kelompok][] = $value;
					}
				}
				else {
					if ($value['sub_total'] > 0) {
						$value['tindakan_obat'] = isset($value['tindakan_obat_nama']) ? $value['tindakan_obat_nama'] :'';
						$value['tarif'] = isset($value['sub_total']) ? $value['sub_total'] : 0;
						$detail[$ruangan][$kelompok][] = $value;
					}
				}
			}
			$grandTotal += $sub_total;
		}
		$this->data = $detail;
		$this->grandTotal = $grandTotal;
		$this->biayaAdmin = $biayaAdmin;
		$data_admin = '';
		if($biayaAdmin > 0 && !empty($admisiId)){
			$data_admin = $this->getDataAdmin();
		}
		$this->data_admin = $data_admin;
		$this->kelaspelayanan_nama = $kelaspelayanan_nama;
		$this->kelas_ditagihkan_nama = $kelas_ditagihkan_nama;
		$this->ruangan_nama = $ruangan_nama;
		$this->kamar_pindah = $kamar_pindah;
		$this->tempattidur_pindah = $tempattidur_pindah;
		$this->dokter_admisi = $dokter_admisi;
		$this->ruangan_titipan_nama = $ruangan_titipan_nama;
		$this->hakKelas = $hakKelas;
		$this->statusKelas = $statusKelas;
		$this->kelas_ditagihkan_id = $kelas_ditagihkan_id;
	}

	protected function getInvoice()
	{
		$pembayaran = PembayaranPelayanan::find()
			->where(['pendaftaran_id' => $this->id])
			->asArray()->one();
		$this->no_pembayaran = !empty($pembayaran['no_pembayaran']) ? $pembayaran['no_pembayaran'] : '-';
	}

	protected function getRender()
	{
		$tindakan_akomodasi = [];
		$total_akomodasi = 0;
		$header = $this->header;
		$pasienadmisi_id = isset($header['pasienadmisi_id']) ? $header['pasienadmisi_id'] : '';
		$tgl_pulang = '-';
		if(!empty($pasienadmisi_id)) {
			$data_akomodasi = $this->getAkomodasi($pasienadmisi_id);
			$tindakan_akomodasi = isset($data_akomodasi['tindakan_akomodasi']) ? $data_akomodasi['tindakan_akomodasi'] : [];
			$total_akomodasi = isset($data_akomodasi['total_akomodasi']) ? $data_akomodasi['total_akomodasi'] : 0;
			$newAkomodasi = [];
			if(!empty($tindakan_akomodasi)) {
				foreach ($tindakan_akomodasi as $key => $value) {
					$additionalData = isset($value['additional_data']) ? $value['additional_data'] : [];
					$additionalData = json_decode($additionalData, true);
					$detail_akomodasi = isset($additionalData['detail_akomodasi']) ? $additionalData['detail_akomodasi'] : [];
					$persentase = isset($detail_akomodasi['persentase']) ? $detail_akomodasi['persentase'] : 0;
					$qtyAkomodasi = 1;
					if($persentase == 50) {
						$qtyAkomodasi = 0.5;
					}
					$dokterpenanggungjawab_id = isset($value['dokterpenanggungjawab_id']) ? $value['dokterpenanggungjawab_id'] : '';
					$ruangan_id = isset($value['ruangan_id']) ? $value['ruangan_id'] : '';
					$daftartindakan_kode = $dokter_nama = '-';
					$tindakanId = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : '';
					if (!empty([$tindakanId])) {
						$tindakan = DaftarTindakan::find()->select([
							'daftartindakan_kode',
						])->where([
							'daftartindakan_id' => $tindakanId,
						])->asArray()->one();
						$daftartindakan_kode = isset($tindakan['daftartindakan_kode']) ? $tindakan['daftartindakan_kode'] : '';
					}
					if (!empty([$dokterpenanggungjawab_id])) {
						$dokter = Pegawai::findOne($dokterpenanggungjawab_id);
						$dokter_nama = isset($dokter['nama_pegawai']) ? $dokter['nama_pegawai'] : '';
					}
					if (!empty([$ruangan_id])) {
						$ruangan = Ruangan::findOne($ruangan_id);
						$ruangan_nama = isset($ruangan['ruangan_nama']) ? $ruangan['ruangan_nama'] : '';
					}
					$value['daftartindakan_kode'] = $daftartindakan_kode;
					$value['qty_tindakan'] = $qtyAkomodasi;
					$value['dokter_nama'] = $dokter_nama;
					$newAkomodasi[$ruangan_nama][] = $value;
				}
			}
			$data_ranap = $this->data_ranap;
			$tgl_pulang = !empty($data_ranap['tgl_stopakomodasi']) ? date('d/M/Y', strtotime($data_ranap['tgl_stopakomodasi'])) : '-';
		}

		$grandTotal = $this->grandTotal + $this->biayaAdmin + $total_akomodasi;
		$pembulatanTagihan = $this->getPembulatan($grandTotal);
		$total_pembulatan = isset($pembulatanTagihan['nominalPembulatan']) ?  $pembulatanTagihan['nominalPembulatan'] : 0;
		$total_ditagihkan = isset($pembulatanTagihan['total_ditagihkan']) ? (int) $pembulatanTagihan['total_ditagihkan'] : (int) $grandTotal;

		return Yii::$app->controller->renderPartial($this->dokPath, [
			'data' => $this->data,
			'grandTotal' => $total_ditagihkan,
			'biayaAdmin' => $this->biayaAdmin,
			'data_admin' => $this->data_admin,
			'pembayaran' => $this->pembayaran,
			'data_akomodasi' => $newAkomodasi,
			'total_akomodasi' => $total_akomodasi,
			'total_pembulatan' => $total_pembulatan,
			'tgl_pulang' => $tgl_pulang,
		]);
	}

	protected function generateAttr()
	{
		$is_admisi = $this->is_admisi;
		if(!$is_admisi){
			return $this->cetakDetailNonRanap();
		}
		$detailPasien = $this->detailPasien();
		$this->detailPasien = $detailPasien;
		$infoPasien = $this->infoPasien;
		$header = $this->header;
		$dokter = "nama_dok_rj_rd";
		$bed_no = !empty($header['kamarruangan_nokamar']) ? $header['kamarruangan_nokamar'] : '-';
		if(!empty($this->kamar_pindah)) {
			$bed_no = $this->kamar_pindah;
		}

		$bed = !empty($header['no_tempattidur']) ? $header['no_tempattidur'] : '-';
		if(!empty($this->tempattidur_pindah)) {
			$bed = $this->tempattidur_pindah;
		}

		$dokter_nama = !empty($header['nama_pegawai']) ? $header['nama_pegawai'] : '';
		if(!empty($this->dokter_admisi)) {
			$dokter_nama = $this->dokter_admisi;
		}
		$tgl_pulang = !empty($header['tgl_stopakomodasi']) ? date('d/M/Y H:i', strtotime($header['tgl_stopakomodasi'])) : '-';
		$tgl_admisi = !empty($header['tgl_admisi']) ? date('d/M/Y H:i', strtotime($header['tgl_admisi'])) : '-';
		$no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
		$no_rekam_medik = isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : null;
		$nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
		$nama_depan = isset($header['nama_depan']) ? $header['nama_depan'] : '-';
		$carabayar_nama = isset($header['carabayar_nama']) ? $header['carabayar_nama'] : '';
		$penjamin_nama = isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '';
		$alamat_pasien = isset($header['alamat_pasien']) ? $header['alamat_pasien'] : '';
		$umur = isset($header['umur']) ? $header['umur'] : '';
		$jenis_kelamin = isset($header['jenis_kelamin']) ? $header['jenis_kelamin'] : '';
		$strNamaDepan = '';
		if(!empty($nama_depan) && $nama_depan != '-') {
			$strNamaDepan = $nama_depan;
		}
		$dataRs = $this->getProfileRs();
		return [
			'#admission#' => $tgl_admisi,
			'#no_rekam_medik#' => $no_rekam_medik,
			'#no_pendaftaran#' => $no_pendaftaran,
			'#nama_pasien#' => $strNamaDepan.' '.$nama_pasien,
			'#nama_dok_rj_rd#'=> $infoPasien[$dokter],
			'#rua_nama#'=> $this->ruangan_nama,
			'#kelaspelayanan_nama#'=> $this->kelaspelayanan_nama,
			'#payer#'=> $penjamin_nama,
			'#carabayar_nama#'=> $carabayar_nama,
			'#status_bayar#'=> $infoPasien['status_bayar'],
			'#printed_by#' => Yii::$app->jwt->user->nama_pemakai,
			'#kasir#' => '',
			'#printed_date#' => date('d/M/Y H:i'),
			'#bill_no#' => '-',
			'#bill_date#' => '-',
			'#address#' => $alamat_pasien,
			'#address2#' => !empty($infoPasien['kelurahan_nama']) ? $infoPasien['kelurahan_nama'] : '-',
			'#address3#' => !empty($infoPasien['kecamatan_nama']) ? $infoPasien['kecamatan_nama'] : '-',
			'#address4#' => !empty($infoPasien['kabupaten_nama']) ? $infoPasien['kabupaten_nama'] : '-',
			'#age#' => $umur,
			'#gender#' => $jenis_kelamin,
			'#ward#' => $this->ruangan_nama,
			'#bed_no#' => $bed_no.'/' . $bed,
			'#bed_type#' => $this->kelaspelayanan_nama,
			'#primary_doctor#' => $dokter_nama,
			'#discharge_date#' => $tgl_pulang,
			'kode_doc' =>  $this->kode_doc,
			'#title#' => 'PERINCIAN BIAYA DAN PEMBAYARAN',
			'#hakKelas#' => $this->hakKelas,
			'#statusKelas#' => $this->statusKelas,
			'#lokasi#' => ArrayHelper::getValue($dataRs, 'kota') . ', ' .date('d M Y'),
         '#rs_name#' => ArrayHelper::getValue($dataRs, 'namaRs'),
			'#datatable#'=> $this->getRender(),
		];
	}

	private function cetakDetailNonRanap()
	{
		$infoPasien = $this->infoPasien;
		$header = InfoDataPendaftaran::find()
			->select([
				'tgl_pendaftaran',
				'no_pendaftaran',
				'pendaftaran_id',
				'no_rekam_medik',
				'nama_pasien',
				'tanggal_lahir',
				'nama_dok_rj_rd',
				'penjamin_nama',
				'pasienadmisi_id',
				'rua_nama',
				'ruangan_nama'
			])
			->andWhere(['pendaftaran_id' => $this->id])
			->asArray()
			->one();
		
		$bayarUangMuka = BayarUangMuka::find();
		$gabungBilling = $this->gabungBilling($this->id);
      $pendaftaranIdGabung = isset($gabungBilling['pendaftaran_id']) ? $gabungBilling['pendaftaran_id'] : null;
		if(!empty($pendaftaranIdGabung)) {
			$bayarUangMuka->select(['SUM(jumlah_uangmuka) AS jumlah_uangmuka']);
			$bayarUangMuka->andWhere(['pendaftaran_id' => [$this->id, $pendaftaranIdGabung]]);
		}
		else {
			$bayarUangMuka->andWhere(['pendaftaran_id' => $this->id]);
		}
		$bayarUangMuka = $bayarUangMuka->one();
		$uang_muka = ($bayarUangMuka) ? $bayarUangMuka->jumlah_uangmuka : 0;
		$ruangan_nama = isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '';
		$dataTotal = $this->getTotalHeaderPembayaran($this->id, $header);
		$total_discount = isset($dataTotal['total_discount']) ? $dataTotal['total_discount'] : 0;
		$total_dijamin = isset($dataTotal['total_dijamin']) ? $dataTotal['total_dijamin'] : 0;
		$tglLahir = !empty($infoPasien['tanggal_lahir']) ? date('d-M-Y', strtotime($infoPasien['tanggal_lahir'])) : '-';
		$dokNama = !empty($infoPasien['nama_dok_rj_rd']) ? $infoPasien['nama_dok_rj_rd'] : ' - ';
		$dateRegist = !empty($infoPasien['tgl_pendaftaran']) ? date('d-m-Y', strtotime($infoPasien['tgl_pendaftaran'])) : '-';
		$pegawai_id = Yii::$app->jwt->user->pegawai_id;
		$data_pegawai = $this->getDataPegawai($pegawai_id)->asArray()->one();
		$pegawai_login = isset($data_pegawai['nama_pegawai']) ? $data_pegawai['nama_pegawai'] : '';
		$pembulatanPenjamin = $this->getPembulatan($total_dijamin);
		$total_dijamin = isset($pembulatanPenjamin['total']) ? (int) $pembulatanPenjamin['total'] : (int) $total_dijamin;
		$totalTagihan = $this->grandTotal + $this->biayaAdmin - ($total_dijamin + $uang_muka);
		$pembulatanTagihan = $this->getPembulatan($totalTagihan);
		$nominalPembulatan = isset($pembulatanTagihan['nominalPembulatan']) ? $pembulatanTagihan['nominalPembulatan'] : 0;
		$total_ditagihkan = isset($pembulatanTagihan['total_ditagihkan']) ? (int) $pembulatanTagihan['total_ditagihkan'] : (int) $totalTagihan;
		
		return [
			'#tgl_invoice#' => date('d-m-Y H:i'),
			'#no_transkasi#' => '-',
			'#nama_pasien#' => $infoPasien['no_rekam_medik'] .' '. $infoPasien['nama_pasien'],
			'#no_rekam_medik#' => $infoPasien['no_rekam_medik'],
			'#tgl_lahir#' => $tglLahir,
			'#dokter#' => $dokNama,
			'#tgl_pelayanan#' => $dateRegist,
			'#penjamin#' => $infoPasien['penjamin_nama'],
			'#nama_kasir#' => !empty($qSummary['nama_pegawai']) ? $qSummary['nama_pegawai'] : '-',
			'#kasir#' => '',
			'#title#' => 'PERINCIAN BIAYA DAN PEMBAYARAN',
			'kode_doc' => $this->kode_doc,
			'#ruangan_nama#' => $ruangan_nama,
			'#no_pendaftaran#' => isset($infoPasien['no_pendaftaran']) ? $infoPasien['no_pendaftaran'] : '-',
			'#printed_by#' => $pegawai_login,
			'#subTotal#' => DocoHelpers::formatNumber($this->grandTotal),
			'#total_administrasi#' => DocoHelpers::formatNumber($this->biayaAdmin),
			'#total_discount#' => DocoHelpers::formatNumber($total_discount),
			'#penggunaan_uangmuka#' => DocoHelpers::formatNumber($uang_muka),
			'#total_dijamin#' => DocoHelpers::formatNumber($total_dijamin),
			'#nominalPembulatan#' => DocoHelpers::formatNumber($nominalPembulatan),
			'#total_ditagihkan#' => DocoHelpers::formatNumber($total_ditagihkan),
			'#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, [
				'dataTindakan' => $this->data,
				'nama_biaya_admin' => $this->data_admin,
				'biayaAdmin' => $this->biayaAdmin,
				'nominal_dijamin' => isset($infoPasien['nominal_dijamin']) ? $infoPasien['nominal_dijamin'] : 0,
				'sisa_uangmuka' => $uang_muka,
				'tagihan_belumbayar' => isset($infoPasien['tagihan_belumbayar']) ? $infoPasien['tagihan_belumbayar'] : 0,
			]),
		];
	}

	private function getDataPegawai($id = null)
	{
		$model = Pegawai::find();
		if ($id) {
			$model->andWhere(['pegawai_id' => $id]);
		}

		return $model;
	}

   protected function getKunjunganRanap($pendaftaran_id)
	{
		$result = [];
		$status_kamar = '-';
		if(!empty($pendaftaran_id)){
			$result = Yii::$app->db->createCommand("
				SELECT pendaftaran_id,no_pendaftaran,no_rekam_medik,nama_pasien,nama_depan,alamat_pasien,rt,rw,tgl_pendaftaran,tgl_admisi,tgl_pulang,tgl_stopakomodasi,
				jenis_kelamin,tanggal_lahir,umur,pasienadmisi_id,carabayar_id,klsrawat,is_aps,is_pasientitipan,is_pasientitipan_pk,penjamin_id,
				is_stoppasientitipan,bpjs_kelas,kelaspelayanan_id,kelaspelayanan_nama,nama_pegawai,
				carabayar_nama,penjamin_nama,ruangan_nama,kamarruangan_nokamar,no_tempattidur,
				kelas_hak,status_kelas,
				(((((additional_data::json)->>'sep')::json)->>'klsRawat')::json)->>'klsRawatHak' as klsrawathak
				FROM infokunjunganri_v 
				WHERE pendaftaran_id = {$pendaftaran_id}")
			->queryOne();
		}
		return $result;
	}

	private function getTotalHeaderPembayaran($pendaftaran_id, $header)
	{
		$result = [];
		$totalAkomodasi = 0;
		if($pendaftaran_id) {
			$pasienadmisi_id = isset($header['pasienadmisi_id']) ? $header['pasienadmisi_id'] : $pendaftaran_id;
			$gabungBilling = $this->gabungBilling($pendaftaran_id);
			$pendaftaranIdGabung = isset($gabungBilling['pendaftaran_id']) ? $gabungBilling['pendaftaran_id'] : null;
			$rincianDetail = RincianPasienDetail2View::find();
			$bayarUangMuka = BayarUangMuka::find();
			$piutang = PemberianPiutang::find();
			if(!empty($pendaftaranIdGabung)) {
				$rincianDetail->select(['SUM(total_tagihan) AS total_tagihan']);
				$rincianDetail->andWhere(['pendaftaran_id' => [$pendaftaran_id, $pendaftaranIdGabung]]);
				$bayarUangMuka->andWhere(['pendaftaran_id' => [$pendaftaran_id, $pendaftaranIdGabung]]);
				$piutang->andWhere(['pendaftaran_id' => [$pendaftaran_id, $pendaftaranIdGabung]]);
		  	}
		  	else {
				$rincianDetail->select(['total_tagihan']);
				$rincianDetail->andWhere(['pendaftaran_id' => $pendaftaran_id]);
				$bayarUangMuka->andWhere(['pendaftaran_id' => $pendaftaran_id]);
				$piutang->andWhere(['pendaftaran_id' => $pendaftaran_id]);
		  	}
		  	$rincianDetail = $rincianDetail->one();
		  	$bayarUangMuka = $bayarUangMuka->one();
		  	$piutang = $piutang->one();
		  	$total_tagihan = isset($rincianDetail['total_tagihan']) ? $rincianDetail['total_tagihan'] : 0;
			$modelPembayaran = Yii::$app->db->createCommand("SELECT 
					COALESCE(SUM(total_administrasi), 0) AS total_administrasi,
					COALESCE(SUM(total_tagihan), 0) AS total_tagihan,
					COALESCE(SUM(total_dijamin), 0) AS total_dijamin,
					COALESCE(SUM(penggunaan_uangmuka), 0) AS penggunaan_uangmuka
					FROM pembayaran_t
					WHERE pendaftaran_id = {$pendaftaran_id} AND pembayaran_t.is_deleted = false")->queryOne();
			
			// Uang Muka
			// $infoPasien = PasienBelumBayar::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
			$uang_muka = ($bayarUangMuka) ? $bayarUangMuka->jumlah_uangmuka : 0;

			$total_piutang = ($piutang) ? $piutang->total_piutang : 0;
			$total_administrasi = $modelPembayaran['total_administrasi'];
			$total_terbayar = $modelPembayaran['total_tagihan'];
			$total_asuransi = $modelPembayaran['total_dijamin'];
			$total_uang_muka = isset($header['is_pulang']) ? $modelPembayaran['penggunaan_uangmuka'] : $uang_muka;
			$total_asuransi = ($total_asuransi > $total_tagihan) ? $total_tagihan : $total_asuransi;
			$pendaftaranPenjamin = PendaftaranPenjamin::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
			$nominal_dijamin = ($pendaftaranPenjamin) ? $pendaftaranPenjamin['nominal_dijamin'] : 0;
			if (isset($header['status_bayar'])){
				$total_asuransi = ($header['status_bayar'] == DocoConstants::BELUM_LUNAS) ? $nominal_dijamin : $total_asuransi;
			}

			// Pastikan ini sudah sesuai
			$total_uang_masuk = $total_uang_muka + $total_terbayar + $total_administrasi + $total_piutang;
			$sisa_tagihan = ($total_tagihan - $total_asuransi - $total_uang_masuk) + $totalAkomodasi;
			$sisa_tagihan = ($sisa_tagihan < 0) ? 0 : $sisa_tagihan;
			$result = [
				'total_tagihan' => $total_tagihan,
				'total_asuransi' => $total_asuransi,
				// 'uang_masuk' => $infoPasien['uang_muka'],
				'uang_masuk' => $uang_muka,
				'sisa_tagihan' => $sisa_tagihan,
				'total_akomodasi' => $totalAkomodasi,
				'total_admin' => $total_administrasi
			];
		}
		
		return $result;
	}

	protected function getHistoryPindahKamar($pendaftaran_id)
	{
		$data = Yii::$app->db->createCommand("
			SELECT * FROM infopindahkamar_v WHERE pendaftaran_id = {$pendaftaran_id} 
			ORDER BY pindahkamar_id DESC LIMIT 1
		")->queryOne();

		return $data;
	}

	protected function getBiayaAdmSudahBayar($pendaftaran_id)
	{
		return Yii::$app->db->createCommand("
			SELECT COALESCE(SUM(total_administrasi), 0) AS total_administrasi FROM pembayaran_t WHERE pendaftaran_id = {$pendaftaran_id} 
		")->queryOne();
	}

	private function getPembulatan($total_ditagihkan)
	{
		$helpers = new DocoHelpers;
		$konfigSystem = Cache::getKonfigSistem();
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
			elseif($match = preg_match("/Kota /i", $profilRs['kota'])) {
				$pattern = "Kota ";
			}

			$kota = str_replace($pattern,"", $profilRs['kota']);
		}

		return [
         'namaRs' => $namaRs,
         'kota' => $kota,
         'alamat' => $profilRs['alamatlokasi_rumahsakit'],
         'no_telp' => $profilRs['no_telp_profilrs'],
      ];
	}

	private function getDataAdmin()
	{
		$confSistem = Cache::getKonfigSistem();
		$admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
		$model = new DaftarTindakan;
		$query = $model::find()->where(['daftartindakan_id'=>$admTindakanId])->asArray()->one();
		return isset($query['daftartindakan_nama']) ? $query['daftartindakan_nama'] : 'Biaya Administrasi';
	}

	private function gabungBilling($pendaftaran_id)
	{
		if(empty($pendaftaran_id)) {
			return [];
		}
		
		return Yii::$app->db->createCommand("
			SELECT * FROM gabungpelayanandetail_t WHERE ref_pendaftaran_id = {$pendaftaran_id} AND is_deleted = FALSE
		")->queryOne();
	}
}