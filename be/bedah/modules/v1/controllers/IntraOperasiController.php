<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-10 13:29:36
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 18:05:00
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoAkunting;
use Doco\models\TarifTotalFn;
use Doco\Services\KasirService;
use SirsCore\features\IntegrasiAkunting;

use app\modules\v1\models\InfoPasienOperasiView;
use app\modules\v1\models\InpostOperasi;
use app\modules\v1\models\PasangInfus;
use app\modules\v1\models\AlatDitubuh;
use app\modules\v1\models\PenggunaanCairan;
use app\modules\v1\models\PelayananOperasi;
use app\modules\v1\models\TindakanPelayananT;
use app\modules\v1\models\TimOperasi;
use app\modules\v1\models\KonsultasiTindakan;
use app\modules\v1\models\PemeriksaanPelengkap;
use app\modules\v1\models\BmhpOperasi;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\TarifPenunjangView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\InpostOperasiView;
use app\modules\v1\models\SyncTindakan;
use app\modules\v1\models\SyncPengeluaranobat;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\InstrumenOperasi;

// use app\modules\v1\models\TimOperasiView;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use app\modules\v1\models\InpostOperasiDetail;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\InfoInpostOperasiDetailView;
use app\modules\v1\models\MappingPosisiOperasi;
use app\modules\v1\models\TindakanLuarOperasi;
use Doco\models\Lookup;
// use app\modules\v1\traits\IntegrateKasirTrait;

class IntraOperasiController extends DocoActiveController
{
		// use IntegrateKasirTrait;
		public $modelClass = 'app\modules\v1\models\InpostOperasi';

		public function verbs()
		{
				$verbs = parent::verbs();
				$verbs["index"] = ["POST", "GET"];
				$verbs["ajax"] = ["POST", "GET"];
				$verbs["update"] = ["POST", "PUT"];
				return $verbs;
		}

		public function actions()
		{
				$actions = parent::actions();
				unset($actions['index']);
				unset($actions['delete']);
				unset($actions['view']);
				unset($actions['create']);
				unset($actions['update']);
				return $actions;
		}

		public function actionView($id)
		{
				try {
						$get = InpostOperasi::find()->with(
								[
										'setPenunjang' => function ($query) {
												$query->select(['obatalkes_nama as name', 'obatalkes_id as id']);
										},
										'setInstrumen' => function ($query) {
												$query->select(['obatalkes_nama as name', 'obatalkes_id as id']);
										},
										'pegawaiPenerima' => function ($query) {
												$query->select(['pegawai_id as id', 'nama_pegawai as name']);
										},
								]
						)->where(['pasienmasukpenunjang_id' => $id])->asArray()->one();
						return ['data' => $get];
				} catch (\yii\db\Exception $e) {
						return ['data' => []];
				}
		}
		public function actionInpostView($id)
		{
				$infopasien = [];
				$itemoperasi = [];
				$bmhp = [];
				$penggunaancairan = [];
				$alatditubuh = [];
				$pemeriksaanpelengkap = [];
				try {
						$get = InpostOperasiView::find()->where(['pasienmasukpenunjang_id' => $id])->asArray()->all();
						foreach ($get as $key => $value) {
								if (count($infopasien) < 1) {
										$infopasien = [
												'is_surgicalsavety' => ($value['is_surgicalsavety']) ? 'Ya' : 'Tidak',
												'masuk_kamar' => $value['masuk_kamar'],
												'mulai_anastesi' => $value['mulai_anastesi'],
												'selesai_anastesi' => $value['selesai_anastesi'],
												'mulai_operasi' => $value['mulai_operasi'],
												'selesai_operasi' => $value['selesai_operasi'],
												'dok_bedah' => $value['dok_bedah'],
												'dok_anastesi' => $value['dok_anastesi'],
												'set_instrumen' => $value['instrumen_nama'],
												'penunjang_khusus' => $value['penunjang_khusus'],
												'perlengkapan_pribadi' => $value['perlengkapan_pribadi'],
												'is_diathermy' => ($value['is_diathermy']) ? 'Ya' : 'Tidak',
												'pos_elektroda' => $value['pos_elektroda'],
												'kulit_sebelum' => $value['kulit_sebelum'],
												'kulit_setelah' => $value['kulit_setelah'],
												'posisi_op' => $value['posisi_op'],
												'kateter_urin' => $value['kateter_urin'],
												'cuci_operasi' => $value['cuci_operasi'],
												'fiksasi_balon' => $value['fiksasi_balon'],
												'pemakaian_implan' => $value['pemakaian_implan'],
												'lokasi_drainvacum' => $value['lokasi_drainvacum'],
												'lokasi_drainpenrose' => $value['lokasi_drainpenrose'],
												'lokasi_drainselang' => $value['lokasi_drainselang'],
												'is_jaringantubuh' => ($value['is_jaringantubuh']) ? 'Ya' : 'Tidak',
												'jenis_jaringan' => $value['jenis_jaringan'],
												'is_diserahkan' => ($value['is_diserahkan']) ? 'Ya' : 'Tidak',
												'penerima' => $value['penerima'],
												'pegawai_pemberi' => $value['pegawai_pemberi'],
												'is_recovery' => ($value['is_recovery']) ? 'Ya' : 'Tidak',
												'jam_masuk_rec' => $value['jam_masuk_rec'],
												'jam_keluar_rec' => $value['jam_keluar_rec'],
												'jam_keluar_rec' => $value['jam_keluar_rec'],
												'kembali_ruangan' => $value['ruang_kembali'],
												'kes_umum' => $value['kes_umum'],
												'kesadaran_umum_lain' => $value['kesadaran_umum_lain'],
												'tingkat_kes' => $value['tingkat_kes'],
												'tingkat_kesadaran_lain' => $value['tingkat_kesadaran_lain'],
												'jln_napas' => $value['jln_napas'],
												'jalan_napas_lain' => $value['jalan_napas_lain'],
												'terapi_oks' => $value['terapi_oks'],
												'terapi_oksigen_lain' => $value['terapi_oksigen_lain'],
												'l_mnt' => $value['l_mnt'],
												'kulit_dtg' => $value['kulit_dtg'],
												'kulit_datang_lain' => $value['kulit_datang_lain'],
												'kulit_klr' => $value['kulit_klr'],
												'kulit_keluar_lain' => $value['kulit_keluar_lain'],
												'sirkulasi_bdn' => $value['sirkulasi_bdn'],
												'sirkulasi_badan_lain' => $value['sirkulasi_badan_lain'],
												'area_luka' => $value['area_luka'],
												'is_skrining_nyeri' => ($value['is_skrining_nyeri']) ? 'Ya' : 'Tidak',
												'ket_skrining' => $value['ket_skrining'],
												'skala_nyeri' => $value['skala_nyeri'],
												'lokasi' => $value['lokasi'],
												'metod_nyeri' => $value['metod_nyeri'],
												'resiko_jatuh' => $value['resiko_jatuh'],
												'barang_pasien' => $value['barang_pasien'],
												'is_pasanginfus' => ($value['is_pasanginfus']) ? 'Ya' : 'Tidak',
												'keterangan_post' => $value['keterangan_post'],
												'pemberitahu_perawat' => $value['pemberitahu_perawat'],
												'perawat_datang' => $value['perawat_datang'],
										];
								}
						}
						return ['info' => $infopasien];
				} catch (Exception $e) {
						return ['info' => $infopasien];
				}
		}
		public function actionSave()
		{
			return Yii::$app->docoPlugin->execute('save_bills');
		}

		public function isPernah($arr_trx, $word)
		{
				$state = false;
				foreach ($arr_trx as $trx) {
						if ($trx['obatalkes_id'] == $word) {
								$state = true;
						}
				}
				return $state;
		}
		public function actionGetView()
		{
				$request = Yii::$app->request;
				$get = $request->get();
				try {
						$type = $get['type'];
						$id = $get['id'];
						$type = str_replace(' ', '', ucwords(str_replace('-', ' ', $type)));
						$model = 'app\modules\v1\models\\' . $type;
						$getData = $model::find()->where(['pasienmasukpenunjang_id' => $id])->asArray()->all();
						return $getData;
				} catch (\Exception $e) {
						return [];
				} catch (\yii\db\Exception $e) {
						return [];
				}
		}

		public function actionIntegrateTindakanBmhp($pendaftaran_id)
		{
				try {
						$this->integrateTindakan($pendaftaran_id);
						$this->integrateBmhp($pendaftaran_id);
				} catch (\Exception $e) {
						throw new \Exception($e->getMessage(), 1);
				}
		}

		public function integrateTindakan($pendaftaran_id)
		{
				try {
						$id = $pendaftaran_id;
						$pendaftaran = InpostOperasi::find()->where(['pasienmasukpenunjang_id' => $id])->one();
						$info = InfoPasienOperasiView::find()->where(['pasienmasukpenunjang_id' => $pendaftaran->pasienmasukpenunjang_id])->asArray()->one();

						$tindakan = SyncTindakan::find()->where(['pendaftaran_id' => $info['pendaftaran_id'], 'is_jurnal' => 'f'])->all();


						$config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

						if ($config->is_akunting != null) {
								if (!empty($tindakan)) {
										foreach ($tindakan as $key => $value) {
												$harga = $value->harga;
												$data[] = [
														'xtransaction_type' => $value->jenis_transaksi,
														'xcompany_id' => 1,
														'xinstalasi_id' => $value->instalasi_id,
														'xruangan_id' => $value->ruangan_id,
														'xref_number_id' => $value->id,
														'xref_number' => $value->no_pendaftaran,
														'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
														'xcategori_code' => $value->komponentarif_kode,
														'xtransaction_at' => $value->tanggal_transaksi,
														'xamount' => $harga,
														'xdiscount_amount' => $value->diskon,
														'xamount_netto' => 0,
														'xamount_ppn' => 0,
														'xmedical_number' => $value->rekam_medik,
														'xnotes' => $value->uraian,
														'xis_billing' => 'DITAGIHKAN',
												];
												$TindakanKomponen = TindakanKomponen::findOne($value->id);
												$TindakanKomponen->is_jurnal = true;
												$TindakanKomponen->update();
										}

										return $var = DocoAkunting::api('POST', 'integrations', $data);
								}
						} else {
								throw new \Exception("This application cannot be integrated to Akunting", 1);
						}
				} catch (\Exception $e) {
						throw new \Exception($e->getMessage(), 1);
				}
		}

		public function integrateBmhp($pendaftaran_id)
		{
				try {
						$id = $pendaftaran_id;
						$pasienpenunjang = InfoPasienOperasiView::find()->where(['pasienmasukpenunjang_id' => $id])->one();
						$pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pasienpenunjang->pendaftaran_id])->one();

						$bmhp = SyncPengeluaranobat::find()->where(['no_pendaftaran' => $pendaftaran['no_pendaftaran'], 'jenis' => 'BMHP', 'is_jurnal' => null])->all();

						$config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
						if ($config->is_akunting != null) {
								if (!empty($bmhp)) {
										foreach ($bmhp as $key => $value) {
												$data[] = [
														'xtransaction_type' => $value->jenis_transaksi,
														'xcompany_id' => 1,
														'xinstalasi_id' => $value->instalasi_id,
														'xruangan_id' => $value->ruangan_id,
														'xref_number_id' => $value->id,
														'xref_number' => $value->no_pendaftaran,
														'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
														'xcategori_code' => $value->jenisobatalkes_kode,
														'xtransaction_at' => $value->tgl_transaksi,
														'xamount' => $value->harga,
														'xdiscount_amount' => $value->discount,
														'xamount_netto' => $value->harga_netto,
														'xamount_ppn' => $value->jmlppn,
														'xmedical_number' => $value->no_rekam_medik,
														'xnotes' => $value->uraian,
														'xis_billing' => $value->is_ditagihkan,
												];
												$obatalkes = ObatAlkesPasien::findOne($value->id);
												$obatalkes->is_jurnal = true;
												$obatalkes->scenario = "jurnal";
												$obatalkes->update();
										}
										$var = DocoAkunting::api('POST', 'integrations', $data);
								}
						} else {
								throw new \Exception("This application cannot be integrated to Akunting", 1);
						}
				} catch (\Exception $e) {
						throw new \Exception($e->getMessage(), 1);
				}
		}
		public function actionGetCacheData()
		{
				$cacheName = Yii::$app->request->get('name', null);
				$penunjangId = Yii::$app->request->get('penunjangid', null);
				if( is_null($cacheName) ){
						return [];
				}
				$data = [];
				switch($cacheName) {
						case "penggunaancairan":
								$data = PenggunaanCairan::find()->select([
										'inpostoperasi_id',
										'pasienmasukpenunjang_id',
										'kegiatan',
										'keterangan',
										'cairan_masuk',
										'cairan_keluar',
										"pegawailogin.nama_pegawai as pegawai_input"
								])
								->leftJoin('loginpemakai_k', 'penggunaancairan_t.created_by = loginpemakai_k.loginpemakai_id')
								->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
								->where(['pasienmasukpenunjang_id' => $penunjangId])->asArray()->all();
						break;
						case "alatditubuh":
								$data = AlatDitubuh::find()
										->select([
												'alatditubuh_t.inpostoperasi_id',
												'alatditubuh_t.pasienmasukpenunjang_id',
												'alatditubuh_t.jenis_alat',
												'alatditubuh_t.jumlah',
												'alatditubuh_t.lokasi',
												'obatalkes_m.obatalkes_nama as jenis_alat_nama',
												"pegawailogin.nama_pegawai as pegawai_input"

										])
										->rightJoin('obatalkes_m', 'alatditubuh_t.jenis_alat = obatalkes_m.obatalkes_id')
										->leftJoin('loginpemakai_k', 'alatditubuh_t.created_by = loginpemakai_k.loginpemakai_id')
										->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
										->where(['alatditubuh_t.pasienmasukpenunjang_id' => $penunjangId])->asArray()->all();
						break;
						case "pemeriksaanpelengkap":
								$data = PemeriksaanPelengkap::find()
										->select([
												'pemeriksaanpelengkap_t.inpostoperasi_id',
												'pemeriksaanpelengkap_t.pasienmasukpenunjang_id',
												'pemeriksaanpelengkap_t.daftartindakan_id',
												'daftartindakan_m.daftartindakan_nama',
												'pemeriksaanpelengkap_t.is_cyto',
												'pemeriksaanpelengkap_t.nama_jaringan',
												'pemeriksaanpelengkap_t.qty',
												'pemeriksaanpelengkap_t.tarif_cyto',
												'pemeriksaanpelengkap_t.tarif_satuan',
												'pemeriksaanpelengkap_t.tarif_tindakan',
												'pemeriksaanpelengkap_t.ruangan_id',
												'pemeriksaanpelengkap_t.additional_data',
												"pegawailogin.nama_pegawai as pegawai_input"
										])
										->rightJoin('daftartindakan_m', 'pemeriksaanpelengkap_t.daftartindakan_id = daftartindakan_m.daftartindakan_id')
										->leftJoin('loginpemakai_k', 'pemeriksaanpelengkap_t.created_by = loginpemakai_k.loginpemakai_id')
										->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
										->where(['pemeriksaanpelengkap_t.pasienmasukpenunjang_id' => $penunjangId])->asArray()->all();
						break;
						case "konsultindakan":
								$data = KonsultasiTindakan::find()
										->select([
												'konsultasitindakan_t.inpostoperasi_id',
												'konsultasitindakan_t.pasienmasukpenunjang_id',
												'konsultasitindakan_t.alasan',
												'konsultasitindakan_t.bagian_tubuh',
												'konsultasitindakan_t.daftartindakan_id',
												'daftartindakan_m.daftartindakan_nama',
												'konsultasitindakan_t.dokter_id',
												'pegawai_m.nama_pegawai as dokter_nama',
												'konsultasitindakan_t.is_cyto',
												'konsultasitindakan_t.tarif_cyto',
												'konsultasitindakan_t.tarif_satuan',
												'konsultasitindakan_t.tarif_tindakan',
												"pegawailogin.nama_pegawai as pegawai_input"
										])
										->rightJoin('daftartindakan_m', 'konsultasitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id')
										->rightJoin('pegawai_m', 'pegawai_m.pegawai_id = konsultasitindakan_t.dokter_id')
										->leftJoin('loginpemakai_k', 'konsultasitindakan_t.created_by = loginpemakai_k.loginpemakai_id')
										->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
										->where(['konsultasitindakan_t.pasienmasukpenunjang_id' => $penunjangId])->asArray()->all();
						break;
						case "penggunaanbmhp":
								$data = BmhpOperasi::find()->select([
										'bmhpoperasi_t.bmhpoperasi_id',
										'bmhpoperasi_t.pasienmasukpenunjang_id',
										'bmhpoperasi_t.inpostoperasi_id',
										'bmhpoperasi_t.obatalkes_id',
										'bmhpoperasi_t.persediaan',
										'bmhpoperasi_t.tambahan',
										'bmhpoperasi_t.terpakai',
										'bmhpoperasi_t.sisa',
										'bmhpoperasi_t.is_ditagihkan',
										'ditagihkan' => new \yii\db\Expression("CASE WHEN bmhpoperasi_t.is_ditagihkan IS TRUE THEN '✓' ELSE NULL END"),
										'bmhpoperasi_t.daftartindakan_id',
										'obatalkes_m.obatalkes_nama',
										"pegawailogin.nama_pegawai as pegawai_input"
								])
								->rightJoin('obatalkes_m', 'bmhpoperasi_t.obatalkes_id = obatalkes_m.obatalkes_id')
								->leftJoin('loginpemakai_k', 'bmhpoperasi_t.created_by = loginpemakai_k.loginpemakai_id')
								->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
								->where(['pasienmasukpenunjang_id' => $penunjangId])->asArray()->all();
						break;
						case "pegawaioperasi":
								$data = TimOperasi::find()
														->select([
																"timoperasi_t.pasienmasukpenunjang_id",
																"timoperasi_t.inpostoperasi_id",
																"timoperasi_t.posisi_tim",
																"fgetnamalookup(timoperasi_t.posisi_tim) as posisi_tim_nama",
																"timoperasi_t.pegawai_id",
																"timoperasi_t.daftartindakan_id",
																"pegawai_m.nama_pegawai as pegawai_nama",
																"pegawailogin.nama_pegawai as pegawai_input",
														])
														->where(['pasienmasukpenunjang_id' => $penunjangId])
														->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = timoperasi_t.pegawai_id')
														->leftJoin('loginpemakai_k', 'timoperasi_t.created_by = loginpemakai_k.loginpemakai_id')
														->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
														->asArray()->all();
								$tmpData = [];
								foreach($data as $row){
									if(!empty($row['daftartindakan_id'])){
										$tmpData[$row['daftartindakan_id']][]= $row;
									}
								}
								$data = $tmpData;
						break;
						case "pemasanganinfus":
								$data = PasangInfus::find()
														->select([
																'pasanginfus_t.pasienmasukpenunjang_id',
																'pasanginfus_t.inpostoperasi_id',
																'obatalkes_m.obatalkes_nama as jeniscairan_nama',
																'pasanginfus_t.jeniscairan_id',
																'pasanginfus_t.tgl_pemasangan',
																'pasanginfus_t.jumlah_tetes',
																'pegawailogin.nama_pegawai as pegawai_input'
														])
														->rightJoin('obatalkes_m', 'pasanginfus_t.jeniscairan_id = obatalkes_m.obatalkes_id')
														->leftJoin('loginpemakai_k', 'pasanginfus_t.created_by = loginpemakai_k.loginpemakai_id')
														->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
														->where(['pasanginfus_t.pasienmasukpenunjang_id' => $penunjangId])
														->asArray()->all();
						break;
						case "instrumen":
								$data = InstrumenOperasi::find()
										->select([
												'instrumenoperasi_t.instrumenoperasi_id',
												'instrumenoperasi_t.pasienmasukpenunjang_id',
												'instrumenoperasi_t.obatalkes_id',
												'instrumenoperasi_t.satuan_id',
												'instrumenoperasi_t.persediaan',
												'instrumenoperasi_t.tambahan',
												'instrumenoperasi_t.terpakai',
												'instrumenoperasi_t.sisa',
												'obatalkes_m.obatalkes_nama',
												'pegawailogin.nama_pegawai as pegawai_input'
										])
										->rightJoin('obatalkes_m', 'obatalkes_m.obatalkes_id = instrumenoperasi_t.obatalkes_id')
										->leftJoin('loginpemakai_k', 'instrumenoperasi_t.created_by = loginpemakai_k.loginpemakai_id')
										->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
										->where(['pasienmasukpenunjang_id' => $penunjangId])
										->asArray()->all();
						break;
						case "tindakanluarbedah":
								$data = TindakanLuarOperasi::find()
										->select([
												'tindakanluaroperasi_t.pasienmasukpenunjang_id',
												'tindakanluaroperasi_t.tindakanluaroperasi_id',
												'tindakanluaroperasi_t.daftartindakan_id as tindakanluarbedah_id',
												'tindakanluaroperasi_t.qty as qtytindakan',
												'daftartindakan_m.daftartindakan_nama as tindakanluarbedah_nama',
												"pegawailogin.nama_pegawai as pegawai_input"
										])
										->rightJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tindakanluaroperasi_t.daftartindakan_id')
										->leftJoin('loginpemakai_k', 'tindakanluaroperasi_t.created_by = loginpemakai_k.loginpemakai_id')
										->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
										->where(['pasienmasukpenunjang_id' => $penunjangId])
										->asArray()->all();
						break;
						case "itemoperasi": 
								$data = InpostOperasiDetail::find()
										->select([
												'inpostoperasidetail_t.daftartindakan_id',
												'inpostoperasidetail_t.operasi_id',
												'inpostoperasidetail_t.golonganoperasi_id',
												'inpostoperasidetail_t.dokter_id',
												'inpostoperasidetail_t.is_cyto as cyto',
												'inpostoperasidetail_t.is_penyulit as penyulit',
												'inpostoperasi_t.pasienmasukpenunjang_id',
												'daftartindakan_m.daftartindakan_nama',
												'operasi_m.operasi_nama',
												'golonganoperasi_m.golonganoperasi_nama',
												'pegawai_m.nama_pegawai as pegawai_nama',
												'kegiatanoperasi_m.kegiatanoperasi_nama',
												'pegawailogin.nama_pegawai as pegawai_input'

										])
										->leftJoin('inpostoperasi_t', 'inpostoperasidetail_t.inpostoperasi_id = inpostoperasi_t.inpostoperasi_id')
										->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = inpostoperasidetail_t.daftartindakan_id')
										->leftJoin('operasi_m', 'operasi_m.operasi_id = inpostoperasidetail_t.operasi_id')
										->leftJoin('golonganoperasi_m', 'golonganoperasi_m.golonganoperasi_id = inpostoperasidetail_t.golonganoperasi_id')
										->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = inpostoperasidetail_t.dokter_id')
										->leftJoin('kegiatanoperasi_m', 'operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id')
										->leftJoin('loginpemakai_k', 'inpostoperasidetail_t.created_by = loginpemakai_k.loginpemakai_id')
										->leftJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')->where(['inpostoperasi_t.pasienmasukpenunjang_id' => $penunjangId])
										->asArray()->all();
						break;
						default:
								$data = [];
				}

				return [
						'message' => 'Data Berhasil Didapatkan!',
						'data' => $data
				];
		}
}
