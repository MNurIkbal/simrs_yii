<?php

namespace app\components;

use Yii;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\InfoPasienRiView;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use Doco\models\InfoStokObatAlkesFnr;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\ResepturRacikan;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\CpptView;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\PindahKamar;
use Doco\Services\ResepUddService;

use GuzzleHttp\Client;

class CronUddComponent 
{
	protected static $keyConfig = 'auth_reseptur';

	public static function startGenerate($admisi_id = NULL) {
		ini_set('max_execution_time', 3600);
		ini_set('memory_limit', '-1');
		$timer = microtime(true);

		$tgl_reseptur = date('Y-m-d H:i:s', strtotime('NOW'));

		$pasienAdmisi = PasienAdmisi::find();
		$dataPasienRanap = $pasienAdmisi->select([
							   'pendaftaran_t.pendaftaran_id',
							   'pendaftaran_t.tgl_pendaftaran',
							   'pendaftaran_t.no_pendaftaran',
							   'reseptur_t.reseptur_id',
							   'reseptur_t.noresep',
							   'reseptur_t.kategori_resep',
							   'reseptur_t.luas_tubuh',
							   'reseptur_t.ruangan_id AS ruangan_depo_farmasi',
							   'resepturdetail_t.group_resepturdetail_id',
							   'resepturdetail_t.group_obatalkes_id',
							   'resepturdetail_t.group_obatalkes_nama',
							   'resepturdetail_t.group_qty_racikan',
							   'resepturdetail_t.group_qty_medis',
							   'resepturdetail_t.group_qty_reseptur',
							   'resepturdetail_t.group_det_medis',
							   'resepturdetail_t.group_signa_id',
							   'resepturdetail_t.group_signa_nama',
							   'resepturdetail_t.group_racikan_id',
							   'resepturdetail_t.group_rke',
							   'resepturdetail_t.group_r',
							   'resepturdetail_t.group_nama_racikan',
							   'resepturdetail_t.group_satuan_racikan_id',
							   'resepturdetail_t.group_etiket',
							   'resepturracikan_t.group_resepturracikan_id',
							   'pasienadmisi_t.pasienadmisi_id',
							   'pasienadmisi_t.pasien_id',
							   'pasienadmisi_t.penjamin_id',
							   'pasienadmisi_t.kelaspelayanan_id',
							   'pasienadmisi_t.ruangan_id AS ruangan_admisi_id',
							   'pasienadmisi_t.pegawai_id AS pegawai_admisi_id',
							   'pasienadmisi_t.ruangan_id',
							   'pasienadmisi_t.kamarruangan_id',
							   'pasienadmisi_t.kamartempattidur_id',
							   'pasienadmisi_t.is_pasientitipan',
							   'pasienadmisi_t.is_stoptitipan',
							   'pasienadmisi_t.kelas_ditagihkan_id',
							   'kamarruangan_m.kamarruangan_nokamar',
							   'kamartempattidur_m.no_tempattidur',
							   'asesmenmedis_t.tinggi_badan',
							   'asesmenmedis_t.berat_badan' 
							])
			->join('join', 'pendaftaran_t', 'pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id')
			->join('JOIN', '(
						SELECT 
							MAX(a.reseptur_id) AS reseptur_id,
							MAX(a.noresep) AS noresep,
							MAX(a.luas_tubuh) AS luas_tubuh,
							a.pasienadmisi_id,
							a.pendaftaran_id,
							a.kategori_resep,
							a.ruangan_id
						FROM
							reseptur_t a 
						WHERE 
							a.kategori_resep = '.DocoConstants::KATEGORI_RESEP_UDD.'
							AND
							a.status_reseptur <> '.DocoConstants::VAR_B_R.'
							AND
							a.is_active = true
							AND
							a.is_deleted = false
						GROUP BY
							a.pasienadmisi_id, a.pendaftaran_id, a.kategori_resep, a.ruangan_id
					) reseptur_t', 'reseptur_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND reseptur_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id')
			->join('LEFT JOIN', '(
					SELECT 
						string_agg(a.resepturdetail_id :: text, \'##\' order by a.resepturdetail_id ASC) as group_resepturdetail_id,
						string_agg(a.obatalkes_id :: text, \'##\' order by a.resepturdetail_id ASC) as group_obatalkes_id,
						string_agg(obatalkes_m.obatalkes_nama :: text, \'##\' order by a.resepturdetail_id ASC) as group_obatalkes_nama,
						string_agg(a.qty_medis :: text, \'##\' order by a.resepturdetail_id ASC) as group_qty_medis,
						string_agg(a.qty_reseptur :: text, \'##\' order by a.resepturdetail_id ASC) as group_qty_reseptur,
						string_agg(COALESCE(a.qty_racikan :: text :: text, \'NULL\'), \'##\' order by a.resepturdetail_id ASC) as group_qty_racikan,
						string_agg(COALESCE(a.det_medis :: text :: text, \'NULL\'), \'##\' order by a.resepturdetail_id ASC) as group_det_medis,
						string_agg(COALESCE(a.signa_id :: text, \'NULL\') :: text, \'##\' order by a.resepturdetail_id ASC) as group_signa_id,
						string_agg((a.signa :: json->>\'text\') :: text, \'##\' order by a.resepturdetail_id ASC) as group_signa_nama,
						string_agg(a.racikan_id :: text, \'##\' order by a.resepturdetail_id ASC) as group_racikan_id,
						string_agg(COALESCE(a.rke :: text, \'NULL\'), \'##\' order by a.resepturdetail_id ASC) as group_rke,
						string_agg(CASE WHEN a.r IS NOT NULL or a.r <> \'\' THEN a.r ELSE \'NULL\' END, \'##\' order by a.resepturdetail_id ASC) as group_r,
						string_agg(COALESCE(a.nama_racikan :: text, \'NULL\'), \'##\' order by a.resepturdetail_id ASC) as group_nama_racikan,
						string_agg(COALESCE(a.satuan_racikan_id :: text, \'NULL\'), \'##\' order by a.resepturdetail_id ASC) as group_satuan_racikan_id,
						string_agg(COALESCE(a.etiket :: text, \'NULL\'), \'##\' order by a.resepturdetail_id ASC) as group_etiket,
						a.reseptur_id
					FROM
						resepturdetail_t a 
					JOIN (
							SELECT
								obatalkes_id,
								obatalkes_nama
							FROM
								obatalkes_m
					) obatalkes_m on obatalkes_m.obatalkes_id = a.obatalkes_id
					WHERE 
						a.is_active = true
						AND
						a.is_deleted = false
					GROUP BY
						a.reseptur_id
				) resepturdetail_t', 'resepturdetail_t.reseptur_id = reseptur_t.reseptur_id')
			->join('LEFT JOIN', '(
						SELECT 
							string_agg(a.resepturracikan_id :: text, \'##\' order by a.resepturracikan_id ASC) as group_resepturracikan_id,
							a.reseptur_id
						FROM
							resepturracikan_t a 
						WHERE 
							a.is_active = true
							AND
							a.is_deleted = false
						GROUP BY
							a.reseptur_id
					) resepturracikan_t', 'resepturracikan_t.reseptur_id = reseptur_t.reseptur_id')
			->join('JOIN', '( 
						SELECT 
							a.kamarruangan_id,
   							a.kamarruangan_nokamar,
   							a.jeniskasuspenyakit_id
  						FROM kamarruangan_m a
  					) kamarruangan_m', 'pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id')
			->join('JOIN', '(
						SELECT 
							a.kamartempattidur_id,
								a.no_tempattidur
							FROM 
								kamartempattidur_m a
						) kamartempattidur_m', 'pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id')
			->join('LEFT JOIN', '(
						SELECT
							a.pendaftaran_id,
							a.tinggi_badan,
							a.berat_badan
						FROM
							asesmenmedis_t a
					) asesmenmedis_t', 'asesmenmedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id');

		$dataPasienRanap = $pasienAdmisi->andWhere(['pendaftaran_t.is_stopakomodasi' => false]);
		$dataPasienRanap = $pasienAdmisi->andWhere(['<>', 'pasienadmisi_t.status_ranap', DocoConstants::STATUS_RANAP_BATAL_RAWAT]);

		if(!empty($admisi_id)) {
			$dataPasienRanap = $pasienAdmisi->andWhere(['pasienadmisi_t.pasienadmisi_id' => $admisi_id]);
		}

		$dataPasienRanap = $pasienAdmisi->orderBy('pendaftaran_t.tgl_pendaftaran DESC')->asArray()->all();

		$returnAdmisi = $returnPendaftaran = $returnReseptur = $arrDataRanap = [];

		/* FETCH ALL DETAIL DATA */
		if(!empty($dataPasienRanap)) {
			try {
				foreach ($dataPasienRanap as $key => $value) {
					$returnAdmisi[] = $value['pasienadmisi_id'];
					$returnPendaftaran[] = $value['no_pendaftaran'];

					/* GET LAST CPPT ID */
					$lastCppt = self::getLastCppt($value['pendaftaran_id'], $value['pegawai_admisi_id'], $value['pasienadmisi_id']);

					/* ASESMEN MEDIS */
					$asesmenMedis = [
						'tinggi_badan' => $value['tinggi_badan'],
						'berat_badan' => $value['berat_badan']
					];

					/* DATA PASIEN RAWAT INAP */
					$ruanganRanap = $value['ruangan_id'] . '@#' . @$value['kamarruangan_id'] . '@#' . @$value['kamartempattidur_id'] . '@#' . $value['kamarruangan_nokamar'] . ' | ' . $value['no_tempattidur'];

					/* RESEPTUR */
					$dataReseptur = [
						'reseptur_id' => $value['reseptur_id'],
						'no_reseptur' => $value['noresep']
					];

					/* RESEPTUR DETAIL */
					$dataResepturDetail = !empty($value['group_resepturdetail_id']) ? explode('##', $value['group_resepturdetail_id']) : NULL;

					/* OBAT ALKES ID */
					$dataObatAlkes = !empty($value['group_obatalkes_id']) ? explode('##', $value['group_obatalkes_id']) : NULL;

					/* OBAT ALKES NAMA */
					$dataObatAlkesNama = !empty($value['group_obatalkes_nama']) ? explode('##', $value['group_obatalkes_nama']) : NULL;

					/* QTY MEDIS */
					$dataQtyMedis = !empty($value['group_qty_medis']) ? explode('##', $value['group_qty_medis']) : NULL;

					/* QTY RESEPTUR */
					$dataQtyReseptur = !empty($value['group_qty_reseptur']) ? explode('##', $value['group_qty_reseptur']) : NULL;

					/* QTY RACIKAN */
					$dataQtyRacikan = !empty($value['group_qty_racikan']) ? explode('##', $value['group_qty_racikan']) : NULL;

					/* DET MEDIS */
					$dataDetMedis = !empty($value['group_det_medis']) ? explode('##', $value['group_det_medis']) : NULL;

					/* SIGNA ID*/
					$dataSignaId = !empty($value['group_signa_id']) ? explode('##', $value['group_signa_id']) : NULL;
					
					/* SIGNA NAMA */
					$dataSignaNama = !empty($value['group_signa_nama']) ? explode('##', $value['group_signa_nama']) : NULL;
					
					$dataRacikanId = !empty($value['group_racikan_id']) ? explode('##', $value['group_racikan_id']) : NULL;
					$dataRke = !empty($value['group_rke']) ? explode('##', $value['group_rke']) : NULL;
					$dataR = !empty($value['group_r']) ? explode('##', $value['group_r']) : NULL;
					$dataNamaRacikan = !empty($value['group_nama_racikan']) ? explode('##', $value['group_nama_racikan']) : NULL;
					$dataSatuanRacikanId = !empty($value['group_satuan_racikan_id']) ? explode('##', $value['group_satuan_racikan_id']) : NULL;
					$dataEtiket = !empty($value['group_etiket']) ? explode('##', $value['group_etiket']) : NULL;

					/* RESEPTUR RACIKAN */
					$dataResepturRacikan = !empty($value['group_resepturracikan_id']) ? explode('##', $value['group_resepturracikan_id']) : NULL;


					$no_pendaftaran = $value['no_pendaftaran'];
					$arrDataRanap[$no_pendaftaran]['no_pendaftaran'] = $no_pendaftaran;
					$arrDataRanap[$no_pendaftaran]['last_cppt'] = $lastCppt;
					$arrDataRanap[$no_pendaftaran]['pendaftaran_id'] = $value['pendaftaran_id'];
					$arrDataRanap[$no_pendaftaran]['pasien_id'] = $value['pasien_id'];
					$arrDataRanap[$no_pendaftaran]['pasienadmisi_id'] = $value['pasienadmisi_id'];
					$arrDataRanap[$no_pendaftaran]['pegawai_admisi_id'] = $value['pegawai_admisi_id'];
					$arrDataRanap[$no_pendaftaran]['ruangan_admisi_id'] = $value['ruangan_admisi_id'];
					$arrDataRanap[$no_pendaftaran]['ruangan_depo_farmasi'] = $value['ruangan_depo_farmasi'];
					$arrDataRanap[$no_pendaftaran]['penjamin_id'] = $value['penjamin_id'];
					$arrDataRanap[$no_pendaftaran]['kelaspelayanan_id'] = $value['kelaspelayanan_id'];
					$arrDataRanap[$no_pendaftaran]['is_stoptitipan'] = $value['is_stoptitipan'];
					$arrDataRanap[$no_pendaftaran]['is_pasientitipan'] = $value['is_pasientitipan'];
					$arrDataRanap[$no_pendaftaran]['kelas_ditagihkan_id'] = $value['kelas_ditagihkan_id'];
					$arrDataRanap[$no_pendaftaran]['luas_tubuh'] = $value['luas_tubuh'];
					$arrDataRanap[$no_pendaftaran]['asesmen_medis'] = $asesmenMedis;
					$arrDataRanap[$no_pendaftaran]['ruangan_ranap'] = $ruanganRanap;
					$arrDataRanap[$no_pendaftaran]['reseptur'] = $dataReseptur;
					$arrDataRanap[$no_pendaftaran]['reseptur_detail'] = $dataResepturDetail;
					$arrDataRanap[$no_pendaftaran]['obat_alkes'] = [
						'obatalkes_id' => $dataObatAlkes,
						'obatalkes_nama' => $dataObatAlkesNama,
						'qty_racikan' => $dataQtyRacikan,
						'qty_medis' => $dataQtyMedis,
						'qty_reseptur' => $dataQtyReseptur,
						'det_medis' => $dataDetMedis,
						'signa_id' => $dataSignaId,
						'signa_nama' => $dataSignaNama,
						'racikan_id' => $dataRacikanId,
						'rke' => $dataRke,
						'r' => $dataR,
						'nama_racikan' => $dataNamaRacikan,
						'satuan_racikan_id' => $dataSatuanRacikanId,
						'etiket' => $dataEtiket,
					];
					$arrDataRanap[$no_pendaftaran]['reseptur_racikan'] = $dataResepturRacikan;
				} // end foreach data pasien ranap


				foreach($arrDataRanap as $key => $value) {
					$resepturDetail = $resepturRacikan = [];

					$penjamin_id = $value['penjamin_id'];
					$kelaspelayanan_id = $value['kelaspelayanan_id'];
					$detail_reseptur = $value['reseptur_detail'];
					$lastCpptId = $value['last_cppt'];
					$pendaftaran_id = $value['pendaftaran_id'];
					$pasien_id = $value['pasien_id'];
					$pasienadmisi_id = $value['pasienadmisi_id'];
					$pegawai_admisi_id = $value['pegawai_admisi_id'];
					$reseptur_id = $value['reseptur']['reseptur_id'];
					$ruangan_ranap = $value['ruangan_ranap'];
					$ruangan_admisi_id = $value['ruangan_admisi_id'];
					$ruangan_depo_farmasi = $value['ruangan_depo_farmasi'];
					$luas_tubuh = $value['luas_tubuh'];
					$tinggi_badan = $value['asesmen_medis']['tinggi_badan'];
					$berat_badan = $value['asesmen_medis']['berat_badan'];
					$no_pendaftaran = $value['no_pendaftaran'];
					$reseptur_racikan = $value['reseptur_racikan'];

					/* VAR TITIPAN */
					$is_stoptitipan = $value['is_stoptitipan'];
					$is_pasientitipan = $value['is_pasientitipan'];
					$kelas_ditagihkan_id = $value['kelas_ditagihkan_id'];

					$pindah_kamar = PindahKamar::find()
								->where(['pasienadmisi_id' => $pasienadmisi_id])
								->orderBy('created_date DESC')
								->limit(1)
								->asArray()->one();

					if(!empty($pindah_kamar)) {
						$kelasPelayananTagihan = ($pindah_kamar['is_pasientitipan'] == TRUE && $pindah_kamar['is_stoptitipan'] == FALSE) ? $kelas_ditagihkan_id : $kelaspelayanan_id;
					} else {
						$kelasPelayananTagihan = ($is_pasientitipan == TRUE && $is_stoptitipan == FALSE) ? $kelas_ditagihkan_id : $kelaspelayanan_id;
					}
					
					/* SECTION RESEPTUR */
					if(!empty($detail_reseptur)) {
						for($i = 0; $i < count($detail_reseptur); $i++) {
							$obatalkes_id = $value['obat_alkes']['obatalkes_id'][$i];
							$obatalkes_nama = $value['obat_alkes']['obatalkes_nama'][$i];
							$qty_racikan = $value['obat_alkes']['qty_racikan'][$i];
							$qty_medis = $value['obat_alkes']['qty_medis'][$i];
							$qty_reseptur = $value['obat_alkes']['qty_reseptur'][$i];
							$det_medis = $value['obat_alkes']['det_medis'][$i];
							$signaText = $value['obat_alkes']['signa_nama'][$i];
							$racikan_id = $value['obat_alkes']['racikan_id'][$i];
							$rke = $value['obat_alkes']['rke'][$i];
							$r = $value['obat_alkes']['r'][$i];
							$etiket = $value['obat_alkes']['etiket'][$i];
							$signa_id = $value['obat_alkes']['signa_id'][$i];
							$nama_racikan = $value['obat_alkes']['nama_racikan'][$i];
							$satuan_racikan_id = $value['obat_alkes']['satuan_racikan_id'][$i];

							/* PRICING OBAT ALKES */
							$paramsGetPrice = [
								'obatalkes_id' => $obatalkes_id,
								'penjamin_id' => $penjamin_id,
								'kelaspelayanan_id' => $kelasPelayananTagihan,
								'ruangan_id' => $ruangan_depo_farmasi
							];
							$priceObatAlkes = self::getPriceObatAlkes($paramsGetPrice)->asArray()->one();

							$qtyTransaksi = (!empty($qty_medis) && $qty_medis != 'NULL') ? $qty_medis : $qty_reseptur; 
							$detTransaksi = (!empty($det_medis) && $det_medis != 'NULL') ? $det_medis : 0;

							$additionalDataResep = [
								'satuaninput_id' 		=> $priceObatAlkes['satuankecil_id'],
								'satuan_input' 			=> $priceObatAlkes['satuankecil_nama'],
								'satuankonversi_id' 	=> $priceObatAlkes['satuankecil_id'],
								'satuan_konversi' 		=> $priceObatAlkes['satuankecil_nama'],
								'harga_reseptur' 		=> ($racikan_id == DocoConstants::ID_RACIKAN) ? $priceObatAlkes['hargajual'] : $priceObatAlkes['harganetto'],
								'nilai_konversi' 		=> 1
							];

							if($racikan_id == DocoConstants::ID_RACIKAN) {
								/* PAYLOAD RESEP RACIKAN */
								$resepturDetail[] = [
									'detail_type' 			=> 'racikan_detail',
									'racikan_id' 			=> 'OR',
									'obatalkes_id' 			=> $obatalkes_id,
									'obatalkes_nama' 		=> $obatalkes_nama,
									'rke'					=> (!empty($rke) && $rke != 'NULL') ? $rke : '',
									'r'						=> (!empty($r) && $r != 'NULL') ? $r : '',
									'qty_reseptur'			=> !empty($detTransaksi) ? (string) $detTransaksi : (string) $qtyTransaksi,
									'qty_konversi'			=> !empty($detTransaksi) ? (string) $detTransaksi : (string) $qtyTransaksi,
									'qty_racikan'			=> !empty($qty_racikan) ? $qty_racikan : NULL,
									'hargasatuan_reseptur'	=> $priceObatAlkes['hargajual'],
									'harganetto_reseptur'	=> $priceObatAlkes['harganetto'],
									'satuankecil_id'		=> $priceObatAlkes['satuankecil_id'],
									'satuankecil_text'		=> $priceObatAlkes['satuankecil_nama'],
									'satuaninput_id'		=> $priceObatAlkes['satuankecil_id'],
									'satuaninput_text'		=> $priceObatAlkes['satuankecil_nama'],
									'signa'					=> $signaText,
									'etiket'				=> (!empty($etiket) && $etiket != 'NULL') ? $etiket : NULL,
									'additional_data'		=> json_encode($additionalDataResep),
									'signa_id'				=> (!empty($signa_id) && $signa_id != 'NULL') ? $signa_id : NULL,
									'nama_racikan'			=> (!empty($nama_racikan) && $nama_racikan != 'NULL') ? $nama_racikan : NULL,
									'satuan_racikan_id'		=> (!empty($satuan_racikan_id) && $satuan_racikan_id != 'NULL') ? $satuan_racikan_id : NULL,
								];
							}

							if($racikan_id == DocoConstants::ID_NON_RACIKAN) {
								/* PAYLOAD RESEP NON RACIKAN */
								$resepturDetail[] = [
									'detail_type' 			=> 'non_racikan',
									'racikan_id' 			=> 'NR',
									'obatalkes_id' 			=> $obatalkes_id,
									'obatalkes_nama' 		=> $obatalkes_nama,
									'rke'					=> (!empty($rke) && $rke != 'NULL') ? $rke : '',
									'qty_reseptur'			=> !empty($detTransaksi) ? (string) $detTransaksi : (string) $qtyTransaksi,
									'qty_konversi'			=> !empty($detTransaksi) ? (string) $detTransaksi : (string) $qtyTransaksi,
									'hargasatuan_reseptur'	=> $priceObatAlkes['hargajual'],
									'harganetto_reseptur'	=> $priceObatAlkes['harganetto'],
									'satuankecil_id'		=> $priceObatAlkes['satuankecil_id'],
									'satuankecil_text'		=> $priceObatAlkes['satuankecil_nama'],
									'satuaninput_id'		=> $priceObatAlkes['satuankecil_id'],
									'satuaninput_text'		=> $priceObatAlkes['satuankecil_nama'],
									'signa'					=> $signaText,
									'etiket'				=> (!empty($etiket) && $etiket != 'NULL') ? $etiket : NULL,
									'additional_data'		=> json_encode($additionalDataResep),
									'signa_id'				=> (!empty($signa_id) && $signa_id != 'NULL') ? $signa_id : NULL,
									'racikan_text'		 	=> (!empty($nama_racikan) && $nama_racikan != 'NULL') ? $nama_racikan : NULL,
								];
							}
						} // end for reseptur

						/* RESEPTUR RACIKAN */
						$dataResepturRacikan = ResepturRacikan::find()
							->andWhere(['reseptur_id' => $reseptur_id])
							->andWhere(['is_deleted' => false])
							->andWhere(['is_active' => true])
							->orderBy('created_date DESC')
							->asArray()->all();

						foreach($dataResepturRacikan as $key_racikan_reseptur => $value_racikan_reseptur) {
							$resepturRacikan[] = [
					    	    'detail_type' => 'racikan_freetext',
					    	    'racikan_id' => $value_racikan_reseptur['type'],
					    	    'rke' => $value_racikan_reseptur['rke'],
					    	    'racikan_text' => $value_racikan_reseptur['racikan'],
					    	    'flag_stok_available' => '1'
					    	];
						}

						$detailResepturData = array_merge($resepturDetail, $resepturRacikan);

						$payload = [
						    'data_instruksi' => [
						        'catatan_instruksi' => '',
						        'cppt_id' => $lastCpptId,
						        'jenis_instruksi' => DocoConstants::J_INST_OBAT,
						        'is_puasa' => '0',
						        'tgl_instruksi' => $tgl_reseptur,
						        'pendaftaran_id' => $pendaftaran_id,
						        'pegawai_id' => $pegawai_admisi_id,
						        'pasien_id' => $pasien_id,
						        'admisi_id' => $pasienadmisi_id,
						        'ruangan' => $ruanganRanap,
						    ],
						    'data_reseptur' => [
						        'racikan_id' => 'NR',
						        'ruangan_id' => $ruangan_depo_farmasi,
						        'pasien_id' => $pasien_id,
						        'pegawai_id' => $pegawai_admisi_id,
						        'berat_badan' => $berat_badan,
						        'tinggi_badan' => $tinggi_badan,
						        'luas_tubuh' => $luas_tubuh,
						        'pendaftaran_id' => $pendaftaran_id,
						        'ruanganreseptur_id' => $ruangan_admisi_id,
						        'iter' => '',
						        'catatan' => '',
						        'is_puasa' => '0',
						        'kategori_resep' => DocoConstants::KATEGORI_RESEP_UDD,
						        'is_ranap' => '1',
						        'tglreseptur' => $tgl_reseptur,
						    ],
						    'data_resepturdetail' => $detailResepturData
						];

						$createResep = self::createResepProcess($payload);

						$returnReseptur[$no_pendaftaran] = $createResep;

					} else if(!empty($reseptur_racikan)) { 
						/* RESEPTUR RACIKAN */
						$dataResepturRacikan = ResepturRacikan::find()
							->andWhere(['reseptur_id' => $reseptur_id])
							->andWhere(['is_deleted' => false])
							->andWhere(['is_active' => true])
							->orderBy('created_date DESC')
							->asArray()->all();

						foreach($dataResepturRacikan as $key_racikan_reseptur => $value_racikan_reseptur) {
							$resepturRacikan[] = [
					    	    'detail_type' => 'racikan_freetext',
					    	    'racikan_id' => $value_racikan_reseptur['type'],
					    	    'rke' => $value_racikan_reseptur['rke'],
					    	    'racikan_text' => $value_racikan_reseptur['racikan'],
					    	    'flag_stok_available' => '1'
					    	];
						}

						$payload = [
						    'data_instruksi' => [
						        'catatan_instruksi' => '',
						        'cppt_id' => $lastCpptId,
						        'jenis_instruksi' => DocoConstants::J_INST_OBAT,
						        'is_puasa' => '0',
						        'tgl_instruksi' => $tgl_reseptur,
						        'pendaftaran_id' => $pendaftaran_id,
						        'pegawai_id' => $pegawai_admisi_id,
						        'pasien_id' => $pasien_id,
						        'admisi_id' => $pasienadmisi_id,
						        'ruangan' => $ruanganRanap,
						    ],
						    'data_reseptur' => [
						        'racikan_id' => 'NR',
						        'ruangan_id' => $ruangan_depo_farmasi,
						        'pasien_id' => $pasien_id,
						        'pegawai_id' => $pegawai_admisi_id,
						        'berat_badan' => $berat_badan,
						        'tinggi_badan' => $tinggi_badan,
						        'luas_tubuh' => $luas_tubuh,
						        'pendaftaran_id' => $pendaftaran_id,
						        'ruanganreseptur_id' => $ruangan_admisi_id,
						        'iter' => '',
						        'catatan' => '',
						        'is_puasa' => '0',
						        'kategori_resep' => DocoConstants::KATEGORI_RESEP_UDD,
						        'is_ranap' => '1',
						        'tglreseptur' => $tgl_reseptur,
						    ],
						    'data_resepturdetail' => $resepturRacikan
						];

						$createResep = self::createResepProcess($payload);

						$returnReseptur[$no_pendaftaran] = $createResep;
					} else {
						$returnReseptur[$no_pendaftaran] = [];
					}
				} // end for fetch data

				$foo = 'elapsed time: '. round(microtime(true) - $timer, 3). ' sec';

				return [
					'pasien_admisi_id' => $returnAdmisi,
					'no_pendaftaran' => $returnPendaftaran,
					'reseptur' => $returnReseptur,
					'time_process' => $foo,
				];
			} catch (\yii\db\Exception $e) {
				//\Yii::$app->response->statusCode = 500;
	        	Yii::error($e->getMessage());
	        	return $e->getMessage();
	        } catch (\Exception $e) {
	        	//\Yii::$app->response->statusCode = 500;
	        	Yii::error($e->getMessage());
	        	return $e->getMessage();
	        }
		}
	}

	protected static function getLastCppt($pendaftaran_id, $pegawai_id, $pasienadmisi_id) {
		$today = date('Y-m-d 00:00:00', strtotime('NOW'));
		$model = CpptView::find()
		    ->select([new \yii\db\Expression('CASE WHEN referred_id IS NOT NULL THEN referred_id ELSE cppt_id END AS cppt_id')])
		    ->where([
		        'pendaftaran_id' => $pendaftaran_id,
		        'pegawai_id' => $pegawai_id,
		        'pasienadmisi_id' => $pasienadmisi_id,
		        'is_verifikasi' => false,
		        'is_active' => true
		    ])
		    // START CONDITION VERBAL ORDER
		    ->andWhere(['IS', 'instruksi', null])
		    ->andWhere(['IS', 'pemberi_instruksi_id', null])
		    ->andWhere(['IS NOT', 'pasienadmisi_id', null])
		    // END CONDITION VERBAL ORDER
		    ->orderBy(['tgl_cppt' => SORT_DESC])
		    ->limit(1)
		    ->asArray()
		    ->one();

		if ($model) {
		    return $model['cppt_id'];
		} else {
		    return 0;
		}
	}

	protected static function getPriceObatAlkes($params = [])
	{
		$obatalkes_id = $params['obatalkes_id'];
		$penjaminId_ = $params['penjamin_id'];
		$kelaspelayananId_ = $params['kelaspelayanan_id'];
		$ruanganId_ = $params['ruangan_id'];


		$query = (new InfoStokObatAlkesFnr(['extParam' => [$penjaminId_, $kelaspelayananId_, $ruanganId_]]))->find()
		            ->select([
		                    'instalasi_id',
		                    'obatalkes_id',
		                    'obatalkes_kode',
		                    'obatalkes_namalain',
		                    'obatalkes_nama',
		                    'qty_tersedia',
		                    'ppn',
		                    'hargaygdipakai as hargajual',
		                    'satuankecil_id',
		                    'satuankecil_nama',
		                    'satuansedang_id',
		                    'satuansedang_nama',
		                    'satuanbesar_id',
		                    'satuanbesar_nama',
		                    'harganetto_ygdipakai as harganetto',
		                    'ruangan_id',
		                    'instalasi_id',
		                    'hargaygdipakai',
		                    'hn_diskon',
		                    'hn_ppn',
		                    'hn_margin',
		                    'disc',
		                    'ppn',
		                    'margin',
		                    'group_jenisobat',
		                    'group_jenisobat_nama',
		                    'jenisobatalkes_id',
		                    'jenisobatalkes_nama'
		                ]
		        );

		$query->andWhere(['obatalkes_id' => $obatalkes_id]);

		return $query;
	}

	protected function createResepProcess($params = []) {
		$configFile = Yii::$app->params['iniFile'];
		$baseConfig = isset($configFile[self::$keyConfig]) ? $configFile[self::$keyConfig] : [];
		$cacheToken = Yii::$app->cache->get('cache-token-reseptur');

		if (empty($cacheToken)) {
			$client = new Client();
			$request = $client->request(
				'POST', 
				$baseConfig['url_backend'] . "dcms/v1/auth/get-token",
				[
					'form_params' => [
					   'username' => $baseConfig['username'],
					   'password' => $baseConfig['password']
					]
				]
			);

			$response = json_decode($request->getBody(), true);
			$token = isset($response['response']['access_token']) ? $response['response']['access_token'] : null;
		} else {
			$token = $cacheToken;
		}

		$sentReseptur = [];
		if(!empty($token)) {
			Yii::$app->cache->set('cache-token-reseptur', $token, 3600);

			$header = [
				'Authorization' => 'Bearer '.$token,
				'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
			];

			$sentReseptur = (new ResepUddService)->saveResepUdd($params, $header);
		}

		return $sentReseptur;
	}
}