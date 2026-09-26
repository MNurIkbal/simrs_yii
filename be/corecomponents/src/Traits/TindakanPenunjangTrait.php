<?php
/**
* @author : Budi (budi@sirs.co.id) 
* Powered by Sirs
*/

namespace Doco\Traits;

use Doco\models\RujukanKeluar;
use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use SirsCore\features\FeatureTindakanBmhp;
use SirsCore\features\IntegrasiAkunting;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\models\radiologi\InfoPasienRadDetailView;
use Doco\models\InfoPasienPenunjangView;
use Doco\models\radiologi\PemeriksaanPasienRadiologiView;
use Doco\models\PegawaiView;
use Doco\models\InfoStokObatAlkesFnr;
use Doco\models\ObatAlkesPasien;
use Doco\models\Pendaftaran;
use Doco\models\TindakanPelayanan;
use Doco\models\PemakaianTindakanObatPenunjangView;
use Doco\models\Laboratorium\InfoPasienLabDetailView;
use Doco\models\TarifTotalFn;
use Doco\models\TindakanRuanganView;
use Doco\models\RuanganView;
use Doco\models\PermintaanKepenunjangan;
use Doco\models\PasienKirimUnitlain;
use Doco\payload\TarifTindakanPayload;
use Doco\Services\KasirService;
use Doco\components\DocoMessages;
use Doco\models\CaraBayar;
use Doco\models\Penjamin;
use Doco\models\Ruangan;
use Doco\models\Pegawai;
use Doco\models\DiagnosaView;
use Doco\models\PasienDirujukKeluar;
use Doco\models\radiologi\InfoOrderanRadView;
use Doco\models\radiologi\InfoOrderanRadDetailView;
use Doco\models\Laboratorium\InfoOrderanLabDetailView;
use Doco\models\PasienMasukPenunjang;
use Doco\models\Laboratorium\InfoOrderanLabView;
use Doco\components\DocoPrint;
use Doco\models\DokterView;
use Doco\exceptions\ValidationException;
use Doco\models\FgetKetersediaanobatFn;
use Doco\models\InfoStokObatAlkesFn;
use Doco\models\InfoStokObatAlkesFnrNew;
use Doco\Services\PlafonBpjsService;
use Doco\models\radiologi\HasilPemeriksaanRad;

trait TindakanPenunjangTrait
{
	public function actionBundleDataAttributes()
	{
		try {
			$request = Yii::$app->request;
			$pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id', null);
			$ruangan_id = Yii::$app->jwt->ruangan_id;
			$instalasi_id = Yii::$app->jwt->instalasi_id;
			$listPemeriksaan = [];
			if($instalasi_id == DocoConstants::INST_ID_RAD) {
				$listPemeriksaan = InfoPasienRadDetailView::find()
				->where([
					'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
				])
				->asArray()->all();
			}
			elseif($instalasi_id == DocoConstants::INST_ID_LAB) {
				$listPemeriksaan = InfoPasienLabDetailView::find()
				->where([
					'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
			  	])
				->andWhere(['IS', 'tindakanpelayananasal_id', NULL])
				->all();
			}
			$listInfo = InfoPasienPenunjangView::find()->select([
				'pasienmasukpenunjang_id',
				'pendaftaran_id',
				'tgl_pendaftaran',
				'nama_pasien',
				'alamat_pasien',
				'no_masukpenunjang',
				'kelaspelayanan_id',
				'penjamin_id',
                'instalasiasal_id'
			])->andWhere([
				'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
			])->asArray()->one();
            
			if($listInfo == null){
				$pendaftaran_id = PasienMasukPenunjang::find()->select('pendaftaran_id')->andWhere([
					'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
				])->asArray()->one()['pendaftaran_id'];
				$listInfo = InfoPasienPenunjangView::find()->select([
					'pasienmasukpenunjang_id',
					'pendaftaran_id',
					'tgl_pendaftaran',
					'nama_pasien',
					'alamat_pasien',
					'no_masukpenunjang',
					'kelaspelayanan_id',
					'penjamin_id',
                    'instalasiasal_id'
				])->andWhere([
					'pendaftaran_id' => $pendaftaran_id
				])->asArray()->one();

				if ($listInfo == null) {
					$listInfo = Pendaftaran::find()->select(['tgl_pendaftaran', 'kelaspelayanan_id', 'penjamin_id', 'instalasi_id'])->andWhere(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
					$listInfo['pasienpenunjang_id'] = $pasienmasukpenunjang_id;
					$listInfo['pendaftaran_id'] = $pendaftaran_id;
					$listInfo['instalasiasal_id'] = isset($listInfo['instalasi_id']) ? $listInfo['instalasi_id'] : null;
				}
			}
			$listOpt = [];
			$status = null;
			foreach ($listPemeriksaan as $value) {
				$listOpt[] = [
					'tindakanpelayanan_id' => !empty($value['tindakanpelayanan_id']) ? $value['tindakanpelayanan_id'] : null,
					'daftartindakan_nama' => !empty($value['tipepaket_nama']) ? $value['tipepaket_nama'] : $value['daftartindakan_nama'],
					'daftartindakan_id' => !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null,
				];
				$status = !empty($value['status_periksa']) ? $value['status_periksa'] : null;
			}

			$listPegawai = $this->getListPegawaiRuangan($ruangan_id);
			return [
				'list_pemeriksaan' => $listOpt,
				'list_pegawai' => $listPegawai,
				'status_periksa' => $status,
				'listInfo' => $listInfo
			];
		} catch (\yii\db\Exception $e) {
			\Yii::$app->response->statusCode = 500;
				$this->logError($e);
				return [
					 'message' => 'Terjadi kesalahan pada server'
				];
		} catch (\Exception $e) {
				\Yii::$app->response->statusCode = 500;
				$this->logError($e);
				return [
					 'message' => 'Terjadi kesalahan pada server'
				];
		  }
	}

	private function getListPegawaiRuangan($ruangan_id)
	{
		return Yii::$app->cache->getOrSet(DocoConstants::CACHE_PEGAWAI_RUANGAN .'-'. $ruangan_id, 
									function ($cache) use ($ruangan_id) {
			$data = PegawaiView::find()
			->andWhere(['is_active' => true]);

			if ($ruangan_id) {
					$data->andWhere(['ruangan_id' => $ruangan_id]);
			}

			$data->orderBy('nama_pegawai');
			return $data->asArray()->all();
		});
	}

	public function actionSimpan($id)
	{
		$request = Yii::$app->request;
		$post = $request->post();
		$jenis = $request->post('jenis', 'tindakan');
		$instalasi_id = Yii::$app->jwt->instalasi_id;
		$parentId = isset($post['pemeriksaan_id']) ? $post['pemeriksaan_id'] : null;
		$daftartindakan_id = isset($post['daftartindakan_id']) ? $post['daftartindakan_id'] : null;
		$info = [];
		if($instalasi_id ==  DocoConstants::INST_ID_RAD) {
			$info = PemeriksaanPasienRadiologiView::find()->andWhere([
				'pasienmasukpenunjang_id' => $id,
				// 'tindakanpelayanan_id' => $parentId
			])->asArray()->one();
		}
		else {
			$info = InfoPasienLabDetailView::find()->where([
				'pasienmasukpenunjang_id' => $id,
				// 'tindakanpelayanan_id' => $parentId
        	])->asArray()->one();
		}
		if(empty($info)) {
			return [
				'title' => 'Proses Gagal',
				'text' => 'Data pasien tidak ditemukan',
				'status' => 422
			];
		}
		$pendaftaran_id = isset($info['pendaftaran_id']) ? $info['pendaftaran_id'] : null;
		$ruangan_id = isset($info['ruangan_id']) ? $info['ruangan_id'] : null;
		$depo_id = isset($post['depo_id']) ? $post['depo_id'] : null;
		$connection = Yii::$app->db;
		$transaction = $connection->beginTransaction();

		if($jenis == 'tindakan') {
			$qty_tindakan = isset($post['qty_tindakan']) ? $post['qty_tindakan'] : 1;
			$qty = isset($post['qty']) ? $post['qty'] : 1;
			$newQty = ($instalasi_id ==  DocoConstants::INST_ID_RAD) ? $qty : $qty_tindakan;
			$postBill = [
				'kelaspelayanan_id' => isset($info['kelaspelayanan_id']) ? $info['kelaspelayanan_id'] : null,
				'penjamin_id' => isset($info['penjamin_id']) ? $info['penjamin_id'] : null,
				'pendaftaran_id' => isset($info['pendaftaran_id']) ? $info['pendaftaran_id'] : null,
				'no_pendaftaran' => isset($info['no_pendaftaran']) ? $info['no_pendaftaran'] : null,
				'ruangan_id' => $ruangan_id,
				'instalasi_id' => $instalasi_id,
				'tgl_transaksi' => date('Y-m-d H:i:s'),
				'detail_tindakan' => [
					[
						'dokter_id' => isset($info['pegawai_id']) ? $info['pegawai_id'] : null,
						'perawat_id' => isset($post['petugas_satu']) ? $post['petugas_satu'] : null,
						'perawat2_id' => isset($post['petugas_dua']) ? $post['petugas_dua'] : null,
						'tipepaket_id' => null,
						'pemeriksaan_id' => !empty($parentId) ? $parentId : null,
						'daftartindakan_id' => isset($post['tindakan_id']) ? $post['tindakan_id'] : null,
						'is_cyto' => false,
						'qty' => $newQty,
						'pasienmasukpenunjang_id' => $id,
					]
				]
			];
			$billKasir = (new KasirService)->post('api/billing', [
				'form_params' => $postBill,
				'failed' => function($data) {
					\Yii::error([
						"Message-Error" => $data
					]);
					return [
						'failed' => true,
						'message' => [
							'status' => 422,
							'text' => isset($data['message']) ? $data['message'] : 'Billing tindakan gagal disimpan'
						]
					];
				}
			]);
			$statusLunas = DocoConstants::LUNAS;
			$statusBelumLunas = DocoConstants::BELUM_LUNAS;
			$pendaftaran = Pendaftaran::findOne($pendaftaran_id);
			if($pendaftaran) {
				if($pendaftaran->status_bayar == $statusLunas) {
					$pendaftaran->status_bayar = $statusBelumLunas;
				}
				$pendaftaran->save();
			} 
			if (isset($billKasir['failed'])) {
				$transaction->rollBack();
				return isset($billKasir['message']) ? $billKasir['message'] : [];
			}
		}
		else {
			$_POST['ruangan_id'] = $ruangan_id;
			$idObatAlkes = $post['obatalkes_id'];
			$tagihkan = $post['is_tagihkan'];
			$kelasPelayananId = isset($info['kelaspelayanan_id']) ? $info['kelaspelayanan_id'] : 0;
			$penjaminId = isset($info['penjamin_id']) ? $info['penjamin_id'] : 0;
			$pendaftaran_id = isset($info['pendaftaran_id']) ? $info['pendaftaran_id'] : null;
			$pendaftaran = Pendaftaran::findOne($pendaftaran_id);
			$instalasiId = ArrayHelper::getValue($pendaftaran, 'instalasi_id');

			/**
			 * Validasi status pulang dan stop akomodasi ketika simpan obat
			 * validasi ditempatkan disini untuk handling input bmhp ke ruangan lain yang perlu approve dlu dan tidak motong stok
			 */
			$validasiPasien = $this->validateStatusPasien($pendaftaran_id);
			if($validasiPasien && is_array($validasiPasien)) {
				return $validasiPasien;
			}
			
			$validasiPasien = $this->validateStatusPasien($pendaftaran_id);
			if($validasiPasien && is_array($validasiPasien)) {
				return $validasiPasien;
			}
			/**
			* ! kondisi ketika depo yg dipilih di luar ruangan login, maka nge hit ke farmasi, jadi orderan bmhp
			* @author : Budi
			* ?? No Issue US2066
			*/
			$tindakanPelayanan = TindakanPelayanan::findOne($parentId);
			$daftartindakan_id = isset($tindakanPelayanan['daftartindakan_id']) ? $tindakanPelayanan['daftartindakan_id'] : $daftartindakan_id;
			$tindakanpelayanan_id = $parentId;
			$stokRuangan = ($depo_id == $ruangan_id) ? true : false;
			if(!$stokRuangan) {
				if(!empty($request->post('daftartindakan_id'))) {
					$daftartindakan_id = $request->post('daftartindakan_id');
					$ruangan_id = $depo_id;
				}
			}
			
			$infoObat = (new InfoStokObatAlkesFnrNew(['extParam'=>[$penjaminId, $kelasPelayananId,$ruangan_id,$instalasiId]]))->find()
			->andWhere([
				'obatalkes_id' => $idObatAlkes
			])
			->asArray()->one();
			if (empty($infoObat)) {
				return [
					'title' => 'Proses Gagal',
					'text' => 'Obat tidak ditemukan',
					'status' => 422
				];
			}

			/**
			 * Perubahan Pengambilan Get Stok Obat.
			 */
			$getStok = (new FgetKetersediaanobatFn(['extParam'=>[$ruangan_id, $idObatAlkes]]))
                    ->find()->select(['obatalkes_id', 'qty_tersedia'])
                    ->asArray()
                    ->one();
            $qty = $request->post('qty', 1);
            $hargaDipakai = ArrayHelper::getValue($infoObat, 'hargaygdipakai', 0);
            $hargaDipakai = $tagihkan ? $hargaDipakai : 0;
            $totalTarif = $tagihkan ? $qty * $hargaDipakai : 0;
            $validasiPlafon = new PlafonBpjsService($pendaftaran_id, $totalTarif);
            $result = $validasiPlafon->validasiPlafon();
            if (!$result['isValid']) {
                return [
					'title' => 'Proses Gagal',
					'text' => $result['message'] ? $result['message'] : 'Validasi Plafon Gagal',
					'status' => 422
				];
            }

			$model = new ObatAlkesPasien;
			$model->obatalkes_id = $idObatAlkes;
			$model->pasienmasukpenunjang_id = $id;
			$model->stok_obat = isset($getStok['qty_tersedia']) ? $getStok['qty_tersedia'] : 0;
			$model->perawat1_id = $request->post('petugas_satu');
			$model->perawat2_id = $request->post('petugas_dua');
			$model->tipepaket_id = isset($tindakanPelayanan['tipepaket_id']) ? $tindakanPelayanan['tipepaket_id'] : null;
			$model->ruangan_id = $ruangan_id;
			$model->carabayar_id = isset($info['carabayar_id']) ? $info['carabayar_id'] : null;
			$model->pegawai_id = isset($info['pegawai_id']) ? $info['pegawai_id'] : null;
			$model->daftartindakan_id = $daftartindakan_id;
			$model->satuankecil_id = isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null;
			$model->pendaftaran_id = $pendaftaran_id;
			$model->pasien_id = isset($info['pasien_id']) ? $info['pasien_id'] : null;
			$model->penjamin_id = isset($info['penjamin_id']) ? $info['penjamin_id'] : null;
			$model->kelaspelayanan_id = isset($info['kelaspelayanan_id']) ? $info['kelaspelayanan_id'] : null;
			$model->pasienadmisi_id = isset($info['pasienadmisi_id']) ? $info['pasienadmisi_id'] : null;
			$model->tglpelayanan = date('Y-m-d H:i:s');
			$model->qty_oa = $request->post('qty');
			$model->hargasatuan_oa = isset($infoObat['hargaygdipakai']) && $tagihkan ? ceil($infoObat['hargaygdipakai']) : 0;
			$model->harganetto_oa = isset($infoObat['harganetto']) ? $infoObat['harganetto'] : 0;
			$model->hargajual_oa = $tagihkan ? $model->qty_oa * $model->hargasatuan_oa : 0;
			$model->tindakanpelayanan_id = $tindakanpelayanan_id;
			$model->status_bmhp = $stokRuangan ? DocoConstants::BMHP_SUDAH_VERIFIKASI : DocoConstants::BMHP_BELUM_VERIFIKASI;
			if ($model->validate() && $model->save()) {
				if($stokRuangan) {
					$oaId = $model->obatalkespasien_id;
					$detailTrans[] = [
						'obatalkes_id' => $idObatAlkes,
						'qty_satuanpakai' => $request->post('qty'),
						'satuankecil_id' => isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null,
						'obatalkespasien_id' => $oaId,
					];

					$listObatAlkes[] = $idObatAlkes;
					$data = [
						'detail_trans' => $detailTrans,
						'list_obat' => $listObatAlkes,
						'header' => [
							'pendaftaran_id' => $pendaftaran_id,
							'kelaspelayanan_id' => $kelasPelayananId,
							'penjamin_id' => $penjaminId,
						]
					];
					try {
						FeatureTindakanBmhp::tindakanBmhp($data,false);
					} catch (\Exception $e) {
						$transaction->rollBack();
						return [
							'title' => 'Proses Gagal',
							'text' => $e->getMessage(),
							'status' => 422
						];
					}
					$pendaftaran = Pendaftaran::findOne($pendaftaran_id);
					$statusLunas = DocoConstants::LUNAS;
					$statusBelumLunas = DocoConstants::BELUM_LUNAS;
					if($pendaftaran) {
						if($pendaftaran->status_bayar == $statusLunas) {
							$pendaftaran->status_bayar = $statusBelumLunas;
						}
						$pendaftaran->save();
					}
					$return_integrate = IntegrasiAkunting::integrateTindakanBmhp($pendaftaran['no_pendaftaran'],$instalasi_id);
				}
			}
			else {
				return [
					'data' => $model->errors,
					'status' => 422
				];
			}
		}

		$transaction->commit();
		return [
			'messages' => 'Data berhasil di simpan'
		];
	}

	public function actionGetDataTindakanObat()
	{
		try {
			$request = Yii::$app->request;
			$pendaftaran_id = $request->get('pendaftaran_id', null);
			$jenis = $request->get('jenis', 'tindakan');
			$instalasi_id = Yii::$app->jwt->instalasi_id;
			$model = new PemakaianTindakanObatPenunjangView;
			$query = $model::find();
			$query->where(['pendaftaran_id' => $pendaftaran_id]);
			if($instalasi_id == DocoConstants::VAR_I_RAD) {
				$query->andWhere(['jenis' => $jenis]);
				$query->andWhere(['IS NOT', 'tindakanpelayananasal_id', NULL]);
			}
			else {
				$query->andWhere(['IS NOT', 'tindakanpelayananasal_id', NULL]);
			}
			$query = DocoRestActiveFilter::advancedFilter($model, $query);
			return new ActiveDataProvider([
				 'query' => $query,
			]);
	  } catch (\yii\db\Exception $e) {
			\Yii::$app->response->statusCode = 500;
			return [
				 'message' => $e->getMessage()
			];
	  } catch (\Exception $e) {
			\Yii::$app->response->statusCode = 500;
			return [
				 'message' => $e->getMessage()
			];
	  }
	}

	public function actionDeleteTindakanObat()
	{
		$request = Yii::$app->request;
		$tindakanpelayanan_id = $request->get('tindakanpelayanan_id', null);
		$obatalkespasien_id = $request->get('obatalkespasien_id', null);
		$connection = Yii::$app->db;
		$transaction = $connection->beginTransaction();
		try {
			if(!empty($obatalkespasien_id)) {
				$getData = ObatAlkesPasien::findOne($obatalkespasien_id);
				$pendaftaranId = $getData->pendaftaran_id;
				$this->validateStatusPasien($pendaftaranId);
				if (!empty($getData)) {
					$integrateHapusBmhhp = FeatureTindakanBmhp::hapusTindakanBmhp($obatalkespasien_id);
					// $model = (new ObatAlkesPasien)->delete([
					// 	'obatalkespasien_id' => $obatalkespasien_id
					// ]);
					// $alkes = StokObatAlkes::find()->where(['obatalkespasien_id' => $obatalkespasien_id])->one();
					// if (!empty($alkes)) {
					// 	$arrData[] = [
					// 		'ruangan_id' => $alkes->ruangan_id,
					// 		'obatalkespasien_id' => $getData->obatalkespasien_id,
					// 		'obatalkes_id' => $alkes->obatalkes_id,
					// 		'tglkadaluarsa' => $alkes->tglkadaluarsa,
					// 		'nobatch' => $alkes->nobatch,
					// 		'tglstok_in' => date('Y-m-d H:i:s'),
					// 		'qtystok_in' => $alkes->qtystok_out,
					// 		'qtystok_out' => 0,
					// 		'harganetto' => $alkes->harganetto,
					// 		'persendiscount' => $alkes->persendiscount,
					// 		'jmldiscount' => $alkes->jmldiscount,
					// 		'persenppn' => $alkes->persenppn,
					// 		'jmlppn' => $alkes->jmlppn,
					// 		'persenmargin' => $alkes->persenmargin,
					// 		'jmlmargin' => $alkes->jmlmargin,
					// 		'stokoa_aktif' => $alkes->stokoa_aktif,
					// 		'stokobatalkesasal_id' => $alkes->stokobatalkesasal_id,
					// 		'satuankecil_id' => $alkes->satuankecil_id,
					// 		'persenpph' => $alkes->persenpph,
					// 	];
					// 	StokObatAlkes::batchInsert($arrData);
					// }
					$instalasi = Yii::$app->jwt->instalasi_id;
					$oaId = $obatalkespasien_id;
					$return_integrate = IntegrasiAkunting::integrateRevertBmhp($oaId,$instalasi,'DELETE');
				}
				else {
					$result = [
						'title' => 'Proses Gagal!',
						'text' => 'Obat gagal dihapus.',
						'status' => 422,
					];
				}
			}
			else {
				$getData = TindakanPelayanan::findOne($tindakanpelayanan_id);
				$pendaftaranId = $getData->pendaftaran_id;
				$this->validateStatusPasien($pendaftaranId);
				if (!empty($getData)) {
					$model = (new TindakanPelayanan)->delete([
						'tindakanpelayanan_id' => $tindakanpelayanan_id
					]);
				}
				else {
					$result = [
						'title' => 'Proses Gagal!',
						'text' => 'Obat gagal dihapus.',
						'status' => 422,
					];
				}
			}
			$transaction->commit();
			$result = [
				'title' => 'Proses Berhasil !',
				'text' => 'Data berhasil dihapus.',
			];
			return $result;
        } catch (ValidationException $e) {
            $transaction->rollBack();
            return $e->getOptions();
		} catch (\yii\db\Exception $e) {
			$transaction->rollBack();
			return ['messages' => $e->getMessage(),'status' => 422];
		} catch (\Exception $e) {
			$transaction->rollBack();
			return ['messages' => $e->getMessage(),'status' => 422];
		}
	}

	public function actionListTindakan()
	{
		$request = Yii::$app->request;
		$kelaspelayanan_id = $request->get('kelaspelayanan_id');
		$penjamin_id = $request->get('penjamin_id');
		$term = $request->get('q');
		$ruangan_id = Yii::$app->jwt->ruangan_id;
		$kategori = 'tindakan';
		$type = 'pelayanan';

		$model = (new TarifTotalFn([
			'extParam' => [
				$ruangan_id,
				$penjamin_id,
				$kelaspelayanan_id, 
				$type
			]
	  	]));
		
		$_query = [
			'_SELECT' => [
				 'daftartindakan_id',
				 'CONCAT(kode,\' - \',daftartindakan_nama) as daftartindakan_nama',
				 'tariftindakan_id',
				 'harga_tariftindakan',
				 'persencyto_tindakan',
				 'penjamin_id',
				 'persen_penyulit',
				 'kode',
			],
			'OTHER' => [
				 ['ILIKE', 'LOWER(CONCAT(kode,\' - \',daftartindakan_nama))', strtolower($term)],
			],
			'DEFAULT' => [
				 ['jenis', $kategori],
			]
	  	];
		$helpers = new DocoHelpers;
		return $helpers->getDataPaginationSelect2($model, $_query);
	}

	public function actionListObat()
	{
		$request = Yii::$app->request;
		$kelaspelayanan_id = $request->get('kelaspelayanan_id');
		$penjamin_id = $request->get('penjamin_id');
		$term = $request->get('q');
		$ruangan_id = Yii::$app->jwt->ruangan_id;
		$model = (new InfoStokObatAlkesFn([
			'extParam' => [
				 $penjamin_id,
				 $kelaspelayanan_id, 
			]
	  	]));
		
		$_query = [
			'_SELECT' => [
				'obatalkes_id',
				'CONCAT(obatalkes_kode,\' - \',obatalkes_nama,\' - stok \', qty_tersedia) as obatalkes_nama',
				'qty_tersedia',
				'satuankecil_id',
				'satuankecil_nama',
				'satuanbesar_id',
				'satuanbesar_nama',
				'hargaygdipakai',
			],
			'OTHER' => [
				 ['ILIKE', 'LOWER(CONCAT(obatalkes_kode,\' - \',obatalkes_nama,\' - stok \', qty_tersedia))', strtolower($term)],
			],
			'DEFAULT' => [
				 ['ruangan_id', $ruangan_id],
			]
	  	];
		$helpers = new DocoHelpers;
		return $helpers->getDataPaginationSelect2($model, $_query);
	}

	public function actionTindakanRuangan()
   {
		$request = Yii::$app->request;
		$id = $request->get('ruangan_id');
		$q = $request->get('q');
		$model = TindakanRuanganView::find()->where(['ruangan_id' => $id]); 
		$penunjang_id = $request->get('penunjang_id');
		$page = $request->get('page', 0);
		$limit = $request->get('limit', 10);
		$offset = $request->get('offset', ($page - 1) * 10);
		$where = "";
		if(!empty($penunjang_id)) {
			$tindakanId = [];
			$listPemeriksaan = InfoPasienRadDetailView::find()
			->where([
				'pasienmasukpenunjang_id' => $penunjang_id
			])
			->asArray()->all();
			foreach ($listPemeriksaan as $key => $value) {
				$tindakanId[] = (int) $value['daftartindakan_id'];
			}
			$inCondition = "(" . implode(",", $tindakanId) . ")";
			$where .= "AND daftartindakan_id NOT IN $inCondition ";
		}

		if(!empty($q)) {
			$where .= "AND LOWER(daftartindakan_nama) ILIKE '%$q%' ";
		}
		$sql = "SELECT ruangan_id, daftartindakan_id, daftartindakan_nama, daftartindakan_kode, daftartindakan_namalainnya 
		FROM tindakanruangan_v 
		WHERE ruangan_id = $id AND tindakanruangan_v.is_deleted = FALSE $where
		ORDER BY daftartindakan_nama LIMIT $limit OFFSET $offset";
		return Yii::$app->db->createCommand($sql)->queryAll();
   }

	public function actionGetTarif()
	{
		$request = Yii::$app->request;
		$getData = $request->get();
		$result = $this->getMasterTarif($getData, true);
		if(!$result) {
			$result = $this->getMasterTarif($getData);
		}
		if(!$result) {
			$result = [];
		}
		return $result;
	}
	
	private function getMasterTarif($getData, $withDokter = false)
   {
		$ruangan_id = isset($getData['ruangan_id']) ? $getData['ruangan_id'] : null;
		$penjamin_id = isset($getData['penjamin_id']) ? $getData['penjamin_id'] : null;
		$kelaspelayanan_id = isset($getData['kelaspelayanan_id']) ? $getData['kelaspelayanan_id'] : null;
		$type = isset($getData['type']) ? $getData['type'] : 'pelayanan';
		$daftartindakan_id = isset($getData['daftartindakan_id']) ? $getData['daftartindakan_id'] : null;
		$dokter_id = isset($getData['dokter_id']) ? $getData['dokter_id'] : null;
		$jenis_pelayanan = 'tindakan';
		$model = (new TarifTotalFn([
			'extParam' => [
					$ruangan_id,
					$penjamin_id,
					$kelaspelayanan_id, 
					$type
			]
		]));
		$query = $model::find()
		->select([
			'daftartindakan_id',
			'tipepaket_id',
			'CONCAT(kode,\' - \',daftartindakan_nama) as daftartindakan_nama',
			'tariftindakan_id',
			'harga_tariftindakan',
			'persencyto_tindakan',
			'penjamin_id',
			'persen_penyulit',
			'kode',
			'dokter_id'
		]);
		if($jenis_pelayanan == 'tindakan') {
			$query->andWhere(['daftartindakan_id' => $daftartindakan_id]);
		}
		else {
			$query->andWhere(['tipepaket_id' => $daftartindakan_id]);
		}

		if($withDokter) {
		if(!empty($dokter_id)) {
			$query->andWhere(['dokter_id' => $dokter_id]);
		}
		}
		else {
			$query->andWhere(['IS', 'dokter_id', NULL]);
		}
		return $query->asArray()->one();
   }

	public function actionGetDepo()
	{
		$request = Yii::$app->request;
		$instalasi_id = $request->get('instalasi_id');
		$q = $request->get('q');
		$model = new RuanganView();
		$instalasiFarmasi = DocoConstants::INSTALASI_FARMASI;
		$_query = [
			'_SELECT' => [
				'ruangan_id',
				'ruangan_nama',
			],
			'ILIKE' => [
				'ruangan_nama',
			],
			'DEFAULT' => [
				['instalasi_id', [$instalasiFarmasi, $instalasi_id]],
			],
			'OTHER' => [
				['ILIKE', 'LOWER(ruangan_nama)', strtolower($q)]
			],
			'ORDERBY' => [
				['ruangan_nama', 'SORT_ASC']
			]
		];
		return $this->helper->getDataPaginationSelect2($model, $_query);
	}

	public function actionPemakaianTindakan()
   {
		$request = Yii::$app->request;
		$q = $request->get('q');
		$model = PemakaianTindakanObatPenunjangView::find()
		->select([
			'daftartindakan_id',
			'nama_tindakan AS daftartindakan_nama',
			'tindakanpelayanan_id'
		]); 
		
		$penunjang_id = $request->get('penunjang_id');
		$pendaftaran_id = $request->get('pendaftaran_id');
		if(!empty($penunjang_id) && !empty($pendaftaran_id)) {
			$model->andWhere([
				'pendaftaran_id' => $pendaftaran_id,
				'jenis' => 'tindakan',
				'tipe' => 'PENUNJANG'
			]);
			$model->andWhere([
				'IS NOT', 'tindakanpelayananasal_id', NULL
			]);
		}
		if(!empty($q)) {
			$model->andWhere(['LOWER(daftartindakan_nama)' => $q]);
		}
		return $model->asArray()->all();
   }

	public function actionGetListDataPemeriksaan()
   {
	   $request = Yii::$app->request;
    	$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
    	$data = PermintaanKepenunjangan::find()->select(['daftartindakan_id'])->where(['pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id])->all();
    	return $data;
   }

   public function actionDeletePemeriksaan()
   {
   	$request = Yii::$app->request;
    	$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
    	$postData = $request->post();
    	$daftartindakan_id = isset($postData['daftartindakan_id']) ? $postData['daftartindakan_id'] : null;
    	$connection = Yii::$app->db;
    	$transaction = $connection->beginTransaction();
    	try {
			$data = PermintaanKepenunjangan::find()->where([
			'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
			'daftartindakan_id' => $daftartindakan_id
			])->count();

			if(!empty($data)) {
				PermintaanKepenunjangan::find()->where([
					'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
					'daftartindakan_id' => $daftartindakan_id
				])->one()->delete();
				$transaction->commit();
				$result = [
					'status' => 200,
					'title' => 'Input Berhasil',
					'text' => 'Pemeriksaan Berhasil Dihapus'
				];
			}
			else {
				$result = [
					'title' => 'Proses Gagal!',
					'text' => 'Pemeriksaan gagal dihapus.',
					'status' => 422,
				];
			}
			return $result;
    } catch (\Exception $e) {
			$this->logError($e);
			$transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			return [
			'message' => $e->getMessage()
			];
    	}
   }

	public function actionGetTarifTindakan()
	{
		$request = Yii::$app->request;
		$jenisId = $request->get('jenispemeriksaanlab_id');
		$namaPemeriksaan = $request->get('daftartindakan_nama');
		$payload = new TarifTindakanPayload;
		$payload->attributes = $request->get();
		$helpers = new DocoHelpers;
		if(!$payload->validate()) {
			return $helpers->callback(DocoMessages::KEY_ERR_SYSTEM, [
				'data' => $payload->errors
			]);
			
		}
		
		switch ($payload->instalasi_id) {
			case $this->constans->actionGetId('LAB'):
				$type = 'penunjang';
				$kategori = 'lab';
				break;
			case $this->constans->actionGetId('RAD'):
				$type = 'penunjang';
				$kategori = 'rad';
				break;
			case $this->constans->actionGetId('IBS'):
				$type = 'penunjang';
				$kategori = 'operasi';
				break;
			default:
				$type = 'pelayanan';
				$kategori = null;
				break;
		}

		$model = (new TarifTotalFn([
			'extParam' => [
				$payload->ruangan_id,
				$payload->penjamin_id,
				$payload->kelaspelayanan_id, 
				$type
			]
		]));

		$query = $model::find()
		->select([
			'tariftindakan_id', 'jenispemeriksaanlab_id', 'jenispemeriksaanlab_nama', 'pemeriksaanlab_id', 
			'pemeriksaanlab_nama', 'daftartindakan_id', 'daftartindakan_nama', 'harga_tariftindakan', 
			'persencyto_tindakan', 'persen_penyulit', 'kode'
		]);
		
		if (!empty($kategori)) {
			$query->andWhere([
				'jenis' => $kategori
			]);
		}

		if(!empty($jenisId)){
			$query->andWhere(['jenispemeriksaanlab_id' => $jenisId]);
		}

		if(!empty($namaPemeriksaan)){
			$query->andFilterWhere(['or',
			['OR LIKE', 'LOWER(daftartindakan_nama)', strtolower($namaPemeriksaan)], 
			['OR LIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaPemeriksaan)], 
			['OR LIKE', 'LOWER(kode)', strtolower($namaPemeriksaan)]]
			);
		}

		$result = [
			'data' => $query->asArray()->all()
		];

		return $result;
	}

   public function actionSimpanTindakan()
	{
		$request = Yii::$app->request;
		$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
		$postData = $request->post();
		$connection = Yii::$app->db;
		$transaction = $connection->beginTransaction();
		try {
			$pasienKirimUnitLain = PasienKirimUnitlain::findOne($pasienkirimkeunitlain_id);
			$instalasi_id = ($pasienKirimUnitLain) ? $pasienKirimUnitLain['instalasi_id'] : null;
			$cekDataExist = PermintaanKepenunjangan::find()->where([
				'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
				'daftartindakan_id' => $postData['daftartindakan_id']
			])->count();
			
			if($cekDataExist > 0) {
				$result = [
				'title' => 'Proses Gagal',
				'text' => 'Pemeriksaan <b>'.$postData['daftartindakan_nama'].'</b> sudah diinputkan!',
				'status' => 422
				];
			}
			else {
				$model = new PermintaanKepenunjangan;
				$model->pasienkirimkeunitlain_id = $pasienkirimkeunitlain_id;
				$model->daftartindakan_id = $postData['daftartindakan_id'];
				$model->tglpermintaankepenunjang = date('Y-m-d H:i:s');
				if($instalasi_id == DocoConstants::VAR_I_RAD) {
				$model->pemeriksaanrad_id = $postData['jenispemeriksaanlab_id'];
				}
				else {
				$model->pemeriksaanlab_id = $postData['pemeriksaanlab_id'];
				}

				$model->qtypermintaan = 1;
				$model->tarif_pelayanan = $postData['harga_tariftindakan'];
				$model->is_cyto = false;
				$model->tarif_cytotindakan = 0;
				if($model->validate()) {
				$model->save();
				$transaction->commit();
				$result = [
					'status' => 200,
					'title' => 'Input Berhasil',
					'text' => 'Pemeriksaan Berhasil Disimpan'
				];
				
				}
			}
			return $result;
		} catch (\Exception $e) {
			$this->logError($e);
			$transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			return [
				'message' => $e->getMessage()
			];
		}
	}

	public function actionGetDataPemeriksaan()
	{
		$request = Yii::$app->request;
		$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id');
		try {
			$pasienKirimUnitLain = PasienKirimUnitlain::findOne($pasienkirimkeunitlain_id);
			$instalasi_id = ($pasienKirimUnitLain) ? $pasienKirimUnitLain['instalasi_id'] : null;
			$model = ($instalasi_id == DocoConstants::VAR_I_RAD) ? new InfoOrderanRadDetailView : new InfoOrderanLabDetailView;
			$query = $model::find()->where(['pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id, 'is_approve' => false])
				->andWhere(['or', ['is_referred' => null], ['is_referred' => false]]);
			
			$query = DocoRestActiveFilter::advancedFilter($model, $query);
			return new ActiveDataProvider([
				'query' => $query,
				'pagination' => false,
			]);
		} catch (\yii\db\Exception $e) {
		\Yii::$app->response->statusCode = 500;
		return [
			'message' => $e->getMessage()
		];
		} catch (\Exception $e) {
		\Yii::$app->response->statusCode = 500;
		return [
			'message' => $e->getMessage()
		];
		}
	}

	public function actionListDokter()
	{
		$page = Yii::$app->request->get('page', 1);
		$query = DokterView::find()
		->select([
			'pegawai_id as id',
			'nama_pegawai as text'
		])
		->andWhere([
			'ruangan_id' => Yii::$app->jwt->ruangan_id
		]);
		$term = Yii::$app->request->get('term');
		if (!empty($term)) {
			$query = $query->andWhere([
				'like',
				'nama_pegawai',
				$term
			]);
		}
		return $query
		->limit(11)
		->offset(($page - 1) * 10)
		->asArray()
		->all();
	}

	private function insertBmhp($id, $instalasiId, $penunjangId, $dokterPenunjangId, $listTindakan)
   {
		$db = Yii::$app->db;
		$view = ($instalasiId == DocoConstants::INST_ID_LAB) ? 'infoorderanlab_v' : 'infoorderanrad_v';
		$infoOrderan = $db->createCommand(
			"SELECT no_pendaftaran, pasienkirimkeunitlain_id, kelaspelayanan_id, jeniskasuspenyakit_id, pasienadmisi_id, ruanganpenunjang_id, 
			pasien_id, pendaftaran_id, ruangan_id, kunjungan, instalasi_id, carabayar_id, penjamin_id, status_pasien, groupcarabayar_id, instalasipen_id
			FROM {$view} WHERE pasienkirimkeunitlain_id = {$id}"
		)->queryOne();

		$insertBmhp = $listObatId = $stokObatOut = $tindakanId = $listObat = $listObatTindakan = [];
		if(!empty($listTindakan)) {
			foreach ($listTindakan as $key => $value) {
				$tindakanId[] = ArrayHelper::getValue($value, 'daftartindakan_id');
			}
			if(!empty($tindakanId)) {
				$listTindakanId = "(" . implode(",", $tindakanId) . ")";
				$getBmhp = Yii::$app->db->createCommand(
					"SELECT * FROM tindakanbmhp_mp WHERE daftartindakan_id IN {$listTindakanId} 
					AND is_deleted = FALSE AND is_active = TRUE")
				->queryAll();
				
				if(!empty($getBmhp)) {
					$penjaminId = ArrayHelper::getValue($infoOrderan, 'penjamin_id');
					$kelasPelayananId = ArrayHelper::getValue($infoOrderan, 'kelaspelayanan_id');
					$ruanganId = ArrayHelper::getValue($infoOrderan, 'ruanganpenunjang_id');
					$pendaftaranId = ArrayHelper::getValue($infoOrderan, 'pendaftaran_id');
					$caraBayarId = ArrayHelper::getValue($infoOrderan, 'carabayar_id');
					$pasienId = ArrayHelper::getValue($infoOrderan, 'pasien_id');
					$pasienAdmisiId = ArrayHelper::getValue($infoOrderan, 'pasienadmisi_id');

					foreach ($getBmhp as $key => $value) {
						$obatAlkesId = ArrayHelper::getValue($value, 'obatalkes_id');
						$listObatId[] = $obatAlkesId;
					}
					
					$infoObat = (new InfoStokObatAlkesFn(['extParam'=>[$penjaminId, $kelasPelayananId]]))->find()
						->andWhere([
							'ruangan_id' => $ruanganId,
							'obatalkes_id' => $listObatId,
						])
						->asArray()->all();
					
					if(!empty($infoObat)) {
						$tindakanPelayanan = Yii::$app->db->createCommand("SELECT * FROM tindakanpelayanan_t 
							WHERE pendaftaran_id = {$pendaftaranId} AND instalasi_id = {$instalasiId} 
							AND daftartindakan_id IN {$listTindakanId} AND is_deleted = FALSE AND is_active = TRUE")
							->queryAll();
						
						$listTindakanPelayananId = $arrayTindakanPelayanan = [];
						if(!empty($tindakanPelayanan)) {
							foreach ($tindakanPelayanan as $key => $value) {
								$daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
								$tindakanPelayananId = ArrayHelper::getValue($value, 'tindakanpelayanan_id');
								$listTindakanPelayananId[$daftarTindakanId] = $tindakanPelayananId;
								$arrayTindakanPelayanan[$daftarTindakanId] = $value;
							}
						}
						foreach ($infoObat as $key => $value) {
							$obatAlkesId = ArrayHelper::getValue($value, 'obatalkes_id');
							$listObat[$obatAlkesId] = $value;
						}

						$arrQtyInput = [];
						foreach ($getBmhp as $key => $value) {
							$obatAlkesId = ArrayHelper::getValue($value, 'obatalkes_id');
							$daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
							$dokterpenanggungjawab_id = !empty($arrayTindakanPelayanan[$daftarTindakanId]['dokterpenanggungjawab_id']) ? $arrayTindakanPelayanan[$daftarTindakanId]['dokterpenanggungjawab_id'] : null;
							$dokterPenunjangId = !empty($dokterPenunjangId) ? $dokterPenunjangId : $dokterpenanggungjawab_id;
							$qtyInput = (int) ArrayHelper::getValue($value, 'qty_input');
							$is_available = 1;

							/**Blok untuk menampung jumlah qty hasil mapping dengan mappingan obat alkes yang sama. */
							$tmpQtyInput = !empty($arrQtyInput[$obatAlkesId]) ? $arrQtyInput[$obatAlkesId] : 0;
							$arrQtyInput[$obatAlkesId] = $qtyInput + $tmpQtyInput;
							/** End of blok */
							
							if (!isset($listObat[$obatAlkesId])) {
								$is_available = 0;
							}
							
							$qty_tersedia = !empty($listObat[$obatAlkesId]['qty_tersedia']) ? $listObat[$obatAlkesId]['qty_tersedia'] : 0;
							
							if ($is_available) {
								/** qtyInput + tmpQtyInput => untuk handling obatalkespasien yang sama dan ketika dijumlahkan qty inputnya melebihi stok tersedia*/
								if (($qtyInput + $tmpQtyInput) > $qty_tersedia) {
									$is_available = 0;
								}
							}

							if($is_available){
								$insertBmhp[] = [
									'obatalkes_id' => $obatAlkesId,
									'pasienmasukpenunjang_id' => $penunjangId,
									'stok_obat' => isset($listObat[$obatAlkesId]) ? (int) $listObat[$obatAlkesId]['qty_tersedia'] : 0,
									'perawat1_id' => null,
									'perawat2_id' => null,
									'tipepaket_id' => null,
									'ruangan_id' => $ruanganId,
									'carabayar_id' => $caraBayarId,
									'pegawai_id' => $dokterPenunjangId,
									'daftartindakan_id' => $daftarTindakanId,
									'satuankecil_id' => ArrayHelper::getValue($value, 'satuanunit_id'),
									'pendaftaran_id' => $pendaftaranId,
									'pasien_id' => $pasienId,
									'penjamin_id' => $penjaminId,
									'kelaspelayanan_id' => $kelasPelayananId,
									'pasienadmisi_id' => $pasienAdmisiId,
									'tglpelayanan' => date('Y-m-d H:i:s'),
									'qty_oa' => $qtyInput,
									// 'hargasatuan_oa' => isset($listObat[$obatAlkesId]) ? $listObat[$obatAlkesId]['hargaygdipakai'] : 0,
									'hargasatuan_oa' => 0,
									'harganetto_oa' => isset($listObat[$obatAlkesId]) ? $listObat[$obatAlkesId]['harganetto'] : 0,
									'hargajual_oa' => 0,
									'tindakanpelayanan_id' => isset($listTindakanPelayananId[$daftarTindakanId]) ? $listTindakanPelayananId[$daftarTindakanId] : null,
									'status_bmhp' => DocoConstants::BMHP_SUDAH_VERIFIKASI
								];
							}
						}
						ObatAlkesPasien::batchInsert($insertBmhp);
						$obatAlkesPasien = Yii::$app->db->createCommand("SELECT * FROM obatalkespasien_t 
							WHERE pendaftaran_id = {$pendaftaranId} 
							AND daftartindakan_id IN {$listTindakanId} AND is_deleted = FALSE AND is_active = TRUE")
							->queryAll();

						if(!empty($obatAlkesPasien)) {
							foreach ($obatAlkesPasien as $key => $value) {
								$obatAlkesId = ArrayHelper::getValue($value, 'obatalkes_id');
								$qty_satuanpakai = ArrayHelper::getValue($value, 'qty_oa');
								$daftartindakan_id = ArrayHelper::getValue($value, 'daftartindakan_id');
								$listObatTindakan[$daftartindakan_id][$obatAlkesId] = [ 
									'obatalkes_id' => $obatAlkesId,
									'satuankecil_id' => ArrayHelper::getValue($value, 'satuankecil_id'),
									'obatalkespasien_id' => ArrayHelper::getValue($value, 'obatalkespasien_id'),
									'qty_satuanpakai' => $qty_satuanpakai,
									'harganetto' => ArrayHelper::getValue($value, 'harganetto_oa'),
									'persendiscount' => 0,
									'persenppn' => 0,
									'persenmargin' => 0,
									'jmlmargin' => 0,
									'jmldiscount' => 0,
									'jmlppn' => 0
								];
							}
							/**
							 * grouping obat berdasarkan tindakan
							 * untuk handling obat yang sama jika ada dua tindakan yang diinputkan mempunyai mapping obat alkes yang sama 
							 * karena saat ini 20-06-2022 FeatureTindakanBmhp::stokObatAlkes jika ada obatalkes id yang sama akan digabung qty_satuanpakainya
							 * sedangkan di table obat alkes pasien sudah diinput dua row dengan daftartindakan yang berbeda
							 * dan ini akan menjadi issue ketika hapus bmhp karenaa kalau salah satu dihapus tidak akan membalikan stok
							 * maka dari itu pengurangan stok akan dilakukan per tindakan. 
							 */
							if(!empty($listObatTindakan)){
								foreach($listObatTindakan as $key => $value){
									$stokObatOut = $value;
									FeatureTindakanBmhp::stokObatAlkes($stokObatOut, false);
								}
							}
						}
					}
				}
			}
		}
	}

	public function actionFilters()
	{
		$request = Yii::$app->request;
		$type = $request->get('type', []);
		$payload = Yii::$app->request->get('payload', $request->get());
		$page = isset($payload['page']) ? $payload['page'] : 1;
		$limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
		$term = isset($payload['term']) ? $payload['term'] : null;
		$result = $resultData = [];
		$carabayar_id = isset($payload['carabayar_id']) ? $payload['carabayar_id'] : null;
		$ruanganId = Yii::$app->jwt->ruangan_id;
		switch ($type) {
			case 'carabayar':
				$result = CaraBayar::find()
					->select(['carabayar_id as id', 'carabayar_nama as text'])
					->where(['is_active' => true]);

				if(!empty($term)) {
					$result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
				}
				$result->orderBy(['carabayar_nama' => SORT_ASC]);
				break;
			
			case 'penjamin':
				$result = Penjamin::find()
					->select(['penjamin_id as id', 'penjamin_nama as text'])
					->where(['is_active' => true]);

					if(!empty($carabayar_id)) {
						$result->andWhere(['carabayar_id' => $carabayar_id]);
					}

					if(!empty($term)) {
						$result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
					}
					$result->orderBy(['penjamin_nama' => SORT_ASC]);
				break;

			case 'ruangan':
				$result = Ruangan::find()
					->select(['ruangan_id as id', 'ruangan_nama as text'])
					->where(['is_active' => true, 'is_modul' => true]);

				if(!empty($term)) {
					$result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
				}
				$result->orderBy(['ruangan_nama' => SORT_ASC]);
				break;

			case 'dokter':
				$result = Pegawai::find()
					->select(['pegawai_id as id', 'nama_pegawai as text'])
					->where(['is_active' => true, 'is_deleted' => false]);

				if(!empty($term)) {
					$result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
				}
				$result->orderBy(['nama_pegawai' => SORT_ASC]);
				break;

			case 'diagnosa':
				$result = DiagnosaView::find()
					->select(['diagnosa_id as id', 'diagnosa_nama as text'])
					->where(['is_active' => true, 'is_deleted' => false, 'tabularlist_versi' => DocoConstants::ICD_10]);

				if(!empty($term)) {
					$result->andWhere(['ilike', 'LOWER(diagnosa_nama)', strtolower($term)]);
				}
				$result->orderBy(['diagnosa_nama' => SORT_ASC]);
				break;
			
			case 'pegawai_menyetujui':
				$result = PegawaiView::find()
					->select(['pegawai_id as id', 'nama_pegawai as text'])
					->where(['is_active' => true, 'is_deleted' => false, 'ruangan_id' => $ruanganId]);

				if(!empty($term)) {
					$result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
				}
				$result->orderBy(['nama_pegawai' => SORT_ASC]);
				break;

			case 'rs_tujuan':
				$result = RujukanKeluar::find()
					->select(['rujukankeluar_id as id', 'rumahsakit_rujukan as text'])
					->where(['is_active' => true, 'is_deleted' => false]);

				if(!empty($term)) {
					$result->andWhere(['like', 'LOWER(rumahsakit_rujukan)', strtolower($term)]);
				}
				$result->orderBy(['rumahsakit_rujukan' => SORT_ASC]);
				break;

			case 'dokter_ruangan':
				$result = DokterView::find()
					->select(['pegawai_id as id', 'nama_pegawai as text'])
					->where(['is_active' => true, 'is_deleted' => false, 'ruangan_id' => $ruanganId]);

				if(!empty($term)) {
					$result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
				}
				$result->orderBy(['nama_pegawai' => SORT_ASC]);
				break;

			default:
					
			break;
		}

		if(!empty($result)) {
			$result = $result->limit($limit + 1)
				 ->offset(($page - 1) * $limit)
				 ->asArray()
				 ->all();
	  	}
		return $result;
	}

	public function actionProsesRujuk()
	{
		$request = Yii::$app->request;
		$instalasiId = Yii::$app->jwt->instalasi_id;
		$connection = Yii::$app->db;
		$transaction = $connection->beginTransaction();
		$result = [];
		$pasienKirimUnitLainId = $request->post('pasienkirimkeunitlain_id', null);
		if(empty($pasienKirimUnitLainId)) {
			return [
				 'status' => 500,
				 'title' => 'Terjadi kesalahan',
				 'text' => 'Data tidak ditemukan'
			];
	  	}

		$rujukanKeluarId = $request->post('rs_tujuan', null);
		$detailTindakan = $request->post('detail_tindakan', []);
		$listTindakanDirujuk = $dataDirujuk = [];
		if(!empty($detailTindakan)) {
			foreach ($detailTindakan as $val) {
				$val = json_decode($val, true);
				$permintaanKePenunjangId = ArrayHelper::getValue($val, 'permintaankepenunjang_id');
				$daftarTindakanId = ArrayHelper::getValue($val, 'daftartindakan_id');
				if(!empty($daftarTindakanId)) {
					$listTindakanDirujuk[] = $daftarTindakanId;
					$dataDirujuk[$permintaanKePenunjangId] = $val;
				}
			}
		}
		if(empty($dataDirujuk)) {
			return [
				 'status' => 500,
				 'title' => 'Terjadi kesalahan',
				 'text' => 'Gagal Insert'
			];
	  	}
		try {
			$pasienKirimUnitLain = PasienKirimUnitlain::findOne(
				['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId]
		  	);
			if (!empty($pasienKirimUnitLain)) {
				$modelOrder = ($instalasiId == DocoConstants::INST_ID_RAD) ? new InfoOrderanRadView : new InfoOrderanLabView;
				$modelOrder = $modelOrder->find()->where(['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId])->one();
				if(!empty($modelOrder)) {
					$pasienId = $modelOrder->pasien_id;
					$pendaftaranId = $modelOrder->pendaftaran_id;
					$pasienAdmisiId = $modelOrder->pasienadmisi_id;
					$ruanganPenunjangId = $modelOrder->ruanganpenunjang_id;
					$noPendaftaran = $modelOrder->no_pendaftaran;
					$inputPasienMasuk = [
						'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId,
						'kelaspelayanan_id' => $modelOrder->kelaspelayanan_id,
						'jeniskasuspenyakit_id' => $modelOrder->jeniskasuspenyakit_id,
						'pasienadmisi_id' => $pasienAdmisiId,
						'ruangan_id' => $ruanganPenunjangId,
						'pasien_id' => $modelOrder->pasien_id,
						'pendaftaran_id' => $pendaftaranId,
						'ruanganasal_id' => $modelOrder->ruangan_id,
						'tglmasukpenunjang' => date('Y-m-d H:i:s'),
						'kunjungan' => $modelOrder->kunjungan,
						'panggil_antrian' => false,
						'instalasiasal_id' => $modelOrder->instalasi_id,
						'pegawai_id' => $request->post('pegawai_menyetujui', null)
					];
					
					/** bila pembayaran nya perorangan. maka status periksa akn menjadi null **/
					if (($modelOrder->instalasi_id == DocoConstants::INST_ID_RI
					|| $modelOrder->instalasi_id == DocoConstants::VAR_CM_IGD)
						|| $modelOrder->penjamin_id != DocoConstants::PENJAMIN_ID
					) {
						$inputPasienMasuk['status_periksa'] = DocoConstants::BLM_PERIKSA;
					}
					
					$tanggalRujukan = $request->post('tanggal_rujukan', null);
					$tanggalRujukan = !empty($tanggalRujukan) ? date('Y-m-d H:i:s', strtotime($tanggalRujukan)) : null;
					$dataInsert = [];
					$diagnosa = json_encode(['text' => '-']);
					if(!empty($dataDirujuk)) {
						foreach ($dataDirujuk as $key => $value) {
							$permintaanKePenunjangId = $key;
							$diagnosaId = ArrayHelper::getValue($value, 'diagnosa');
							$diagnosaNama = ArrayHelper::getValue($value, 'diagnosa_nama');
							if(!empty($diagnosaId) && !empty($diagnosaNama)) {
								$defaultDiagnosa = ['id' => $diagnosaId, 'nama' => $diagnosaNama];
								$diagnosa = json_encode($defaultDiagnosa);
							}
							
							$dataInsert[] = [
								'pasien_id' => $pasienId,
								'rujukankeluar_id' => $rujukanKeluarId,
								'pasienadmisi_id' => $pasienAdmisiId,
								'pegawai_id' => $request->post('pegawai_menyetujui', null),
								'pendaftaran_id' => $pendaftaranId,
								'tgldirujuk' => $tanggalRujukan,
								'alasandirujuk' => $request->post('alasan_rujukan', null),
								'ruanganasal_id' => $ruanganPenunjangId,
								'nosuratrujukan' => '-',
								'tglberlakusurat' => date('Y-m-d H:i:s'),
								'sampaidengan' => date('Y-m-d H:i:s'),
								'permintaankepenunjang_id' => $permintaanKePenunjangId,
								'diagnosa' => $diagnosa,
							];
						}

						$getPasienMasukPenunjang =  PasienMasukPenunjang::find()
							->where(['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId])
							->one();
							
						$modelPasienMasukPenunjang = empty($getPasienMasukPenunjang) ? new PasienMasukPenunjang : $getPasienMasukPenunjang;
						$attributes = empty($getPasienMasukPenunjang) ? $inputPasienMasuk : $getPasienMasukPenunjang->attributes;
						$modelPasienMasukPenunjang->attributes = $attributes;
						if ($modelPasienMasukPenunjang->validate()) {
							$modelPasienMasukPenunjang->save();
							$pasienMasukPenunjangId = $modelPasienMasukPenunjang->pasienmasukpenunjang_id;
							$jumlahPemeriksaan = PermintaanKepenunjangan::find()
							->where([
								'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId,
								'is_approve' => false
							])->count();
							
							$tindakanArray = $filterTindakanId = [];
							$modelOrderDetail = ($instalasiId == DocoConstants::INST_ID_RAD) ? new InfoOrderanRadDetailView : new InfoOrderanLabDetailView;
							$modelOrderDetail = $modelOrderDetail->find()->where(['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId])->asArray()->all();
							if(!empty($modelOrderDetail)) {
								foreach ($modelOrderDetail as $kt => $vt) {
									$permintaanKePenunjangId = ArrayHelper::getValue($vt, 'permintaankepenunjang_id');
									if (!empty($dataDirujuk)) {
										if(isset($dataDirujuk[$permintaanKePenunjangId])) {
											$data = $dataDirujuk[$permintaanKePenunjangId];
											$daftarTindakanId = ArrayHelper::getValue($data, 'daftartindakan_id');
											if(!empty($daftarTindakanId)) {
												$tindakanArray[] = [
													'pasienmasukpenunjang_id' => $pasienMasukPenunjangId,
													'dokter_id' => ArrayHelper::getValue($data, 'dokter_id'),
													'daftartindakan_id' => $daftarTindakanId,
													'tipepaket_id' => ArrayHelper::getValue($vt, 'tipepaket_id'),
													'is_cyto' => ArrayHelper::getValue($vt, 'is_cyto', false),
													'qty' => ArrayHelper::getValue($vt, 'qtypermintaan'),
													'permintaankepenunjang_id' => $permintaanKePenunjangId,
												];
												$filterTindakanId[] = $daftarTindakanId;
											}
										}
									}
								}
							}

							if(empty($tindakanArray)) {
								return [
									'status' => 500,
									'title' => 'Terjadi kesalahan',
									'text' => 'Gagal Insert'
							  ];
							}

							$payloadHeader = [
								'no_pendaftaran' => $noPendaftaran,
								'kelaspelayanan_id' => $modelOrder->kelaspelayanan_id,
								'penjamin_id' => $modelOrder->penjamin_id,
								'carabayar_id' => $modelOrder->carabayar_id,
								'ruangan_id' => $modelOrder->ruanganpenunjang_id,
								'instalasi_id' => $modelOrder->instalasipen_id,
							];
							$integrateKasir = (new KasirService)->tagihanPenunjang($payloadHeader, $tindakanArray);
							if(isset($integrateKasir['meta']['result'])) {
								$result = $integrateKasir['meta']['result'];
								if($result == 'failed') {
									$message = isset($integrateKasir['message']) ? $integrateKasir['message'] : 'Terjadi kesalahan saat integerasi dengan kasir';
									throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
										'text' => $message
									]);
								}
							}
							PasienDirujukKeluar::batchInsert($dataInsert);
							PermintaanKepenunjangan::updateAll(['is_referred' => true, 'is_approve' => true, 'tgl_approve' => date('Y-m-d H:i:s')], [
								'and', 
								['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId], 
								['IN', 'daftartindakan_id', $listTindakanDirujuk] 
							]);

							if($jumlahPemeriksaan == 0) {
								$pasienKirimUnitLain->pasienmasukpenunjang_id = $pasienMasukPenunjangId;
								$pasienKirimUnitLain->status_penunjang = DocoConstants::DISETUJUI;
								$pasienKirimUnitLain->save();
							}

							$getTindakanPelayanan = TindakanPelayanan::find()->select([
								'tindakanpelayanan_id',
								'daftartindakan_id',
							])->where([
								'pasienmasukpenunjang_id' => $pasienMasukPenunjangId,
								'daftartindakan_id' => $filterTindakanId
							])->asArray()->all();

							if (!empty($getTindakanPelayanan)) {
								foreach ($getTindakanPelayanan as $index => $tindakanPelayanan) {
									$daftarTindakanId = ArrayHelper::getValue($tindakanPelayanan, 'daftartindakan_id');
									$condition = 'pasienkirimkeunitlain_id = ' . $pasienKirimUnitLainId . ' and daftartindakan_id = ' . $daftarTindakanId;
									PermintaanKepenunjangan::updateAll(
										[
											'tindakanpelayanan_id' => ArrayHelper::getValue($tindakanPelayanan, 'tindakanpelayanan_id')
										],
										$condition
									);
								}
							}

							// jika bukan rawat darurat, is karcis true & status bayar belum lunas
							// jika rawat darurat, status bayar belum lunas
							$statusLunas = DocoConstants::BELUM_LUNAS;
							if ($modelOrder->instalasi_id != DocoConstants::VAR_CM_IGD) {
								$updateArray = ['is_karcis' => true, 'status_bayar' => $statusLunas];
							} else {
								$updateArray = ['status_bayar' => $statusLunas];
							}
							Pendaftaran::updateAll($updateArray, "pendaftaran_id = {$pendaftaranId}");
							$transaction->commit();
							$result = [
								'status' => 200,
								'title' => 'Input Berhasil',
								'text' => 'Rujukan Keluar Berhasil',
								'rujukankeluar_id' => DocoHelpers::encrypt($rujukanKeluarId),
							];
						}
						else {
							$result['status'] = 500;
							$result['title'] = 'Gagal Insert';
							$result['text'] = $modelPasienMasukPenunjang->getErrors();
						}
					}
				}
			}
			else {
				$transaction->rollBack();
				$result = [
					'status' => 500,
					'title' => 'Terjadi kesalahan',
					'text' => 'Data tidak ditemukan'
				];
			}
			return $result;
        } catch (ValidationException $e) {
            $transaction->rollBack();
            return $e->getOptions();
		} catch (\Exception $e) {
			$this->logError($e);
			$transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			return [
				'message' => $e->getMessage()
			];
		}
	}

	protected function getKonfigSistem(){
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        });
    }

	protected function validateStatusPasien($pendaftaran_id)
	{
		if(!empty($pendaftaran_id)){
			$errorMessage = '';
			$pendaftaran = Pendaftaran::find()->select([
				'pendaftaran_t.status_bayar', 'pendaftaran_t.pasienadmisi_id', 'pendaftaran_t.is_stopakomodasi', 
				'pasienadmisi_t.pasienpulang_id', 'pasienadmisi_t.status_ranap', 'pendaftaran_t.is_close_bill'
			])
			->leftJoin('pasienadmisi_t', 'pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id')
			->where(['pendaftaran_t.pendaftaran_id' => $pendaftaran_id])->one();
			$konfigSystem = self::getKonfigSistem();
			$pasienAdmisiId = !empty($pendaftaran['pasienadmisi_id']) ? $pendaftaran['pasienadmisi_id'] : null;
			$pasienPulangId = !empty($pendaftaran['pasienpulang_id']) ? $pendaftaran['pasienpulang_id'] : null;
			$statusRanap = !empty($pendaftaran['status_ranap']) ? $pendaftaran['status_ranap'] : null;
			$isStopAkomodasi = !empty($pendaftaran['is_stopakomodasi']) ? $pendaftaran['is_stopakomodasi'] : false;
			$isValidasiStokAkomodasi = !empty($konfigSystem['is_kasir_validasi_stop_akomodasi']) ? $konfigSystem['is_kasir_validasi_stop_akomodasi'] : false;
			$isValidasiStatusPulang = !empty($konfigSystem['is_kasir_validasi_status_pulang']) ? $konfigSystem['is_kasir_validasi_status_pulang'] : false;
			$isCloseBill = !empty($pendaftaran['is_close_bill']) ? $pendaftaran['is_close_bill'] : false;
			
			/** Penambahan validasi pasien pulang GB-666 */
			if($isValidasiStatusPulang){
				if(!empty($pasienAdmisiId) && $statusRanap == DocoConstants::STATUS_RANAP_PULANG){ //untuk handling RD rujuk ranap
					$errorMessage = 'Tidak bisa diproses pasien sudah pulang.';

				}elseif(empty($pasienAdmisiId) && !empty($pasienPulangId)){ // jika dia bukan ranap maka langsung cek dari pasienpulang_id
					$errorMessage = 'Tidak bisa diproses pasien sudah pulang.';
				}
			}

			/** Penambahan validasi stop akomodasi GB-664 */
			if($isValidasiStokAkomodasi && $isStopAkomodasi && !empty($pasienAdmisiId)){
				$errorMessage = 'Tidak bisa diproses pasien sudah stop akomodasi.';
			}

			/**
			 * penambahan validasi lock bill
			 */
			if($isCloseBill) {
				$errorMessage = 'Pasien sudah dilakukan proses Lock Bill.';
			}

			if(!empty($errorMessage)) {
				return [
					'title' => 'Proses Gagal',
					'text' => $errorMessage,
					'status' => 422
				];
			}
		}
	}

	public static function getListPenunjangRadiologi($pendaftaran_id, $pasienadmisi_id = null, $no_rekam_medik = null, $instalasi_id = null, $type = null, $is_cppt = true)
	{
		$temp_rad = $temp_order = $conditions = [];
		$conditions['infopasienradiologi_v.pendaftaran_id'] = $pendaftaran_id;
		if ($type == 'riwayat') {
			$conditions['infopasienradiologi_v.pendaftaran_id'] = $pendaftaran_id;
			if (!is_null($pasienadmisi_id)) {
				$conditions['infopasienradiologi_v.pasienadmisi_id'] = $pasienadmisi_id;
			} else if (!is_null($instalasi_id) && is_null($pasienadmisi_id)) {
				// $conditions['asalrujukan_id'] = Yii::$app->request->get('instalasi_id');
			}
		}
		$data_radiologi = (new \yii\db\Query())
                ->select([
                    'infopasienradiologi_v.pendaftaran_id',
                    'infopasienradiologi_v.pasienmasukpenunjang_id',
                    'infopasienradiologi_v.daftartindakan_id',
                    'infopasienradiologi_v.tindakanpelayanan_id',
                    'infopasienradiologi_v.tglmasukpenunjang',
                    'infopasienradiologi_v.no_rujukan',
                    'infopasienradiologi_v.daftartindakan_nama',
                    'infopasienradiologi_v.tgl_verifikasi',
                    'hasilpemeriksaanrad_t.is_deleted',
                    'hasilpemeriksaanrad_t.hasilpemeriksaanrad_id',
                    'hasilpemeriksaanrad_t.is_hasilkritis',
                    'hasilpemeriksaanrad_t.no_hasilrad',
                    'infopasienradiologi_v.status_penunjang',
                    'statusperiksa_penunjangan.lookup_name AS stat_penunjang',
                    'infopasienradiologi_v.status_periksa',
                    'statusperiksa_rad.lookup_name AS stat_periksa',
                    'infopasienradiologi_v.no_pendaftaran',
                    'infopasienradiologi_v.dokter_penunjang',
                    'infopasienradiologi_v.ruangan_nama',
                    'infopasienradiologi_v.is_hasil',
                    'infopasienradiologi_v.tgl_verifikasi',
                    'infopasienradiologi_v.status_batal',
                    'infopasienradiologi_v.is_read',
                    'hasilbridgingradiologi_t.image_link',
                ])
                ->from('infopasienradiologi_v')
                ->leftJoin('hasilpemeriksaanrad_t','infopasienradiologi_v.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND infopasienradiologi_v.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id')
                ->leftJoin('lookup_m statusperiksa_rad','infopasienradiologi_v.status_periksa::integer = statusperiksa_rad.lookup_id')
                ->leftJoin('lookup_m statusperiksa_penunjangan','infopasienradiologi_v.status_penunjang::integer = statusperiksa_penunjangan.lookup_id')
                ->leftJoin('hasilbridgingradiologi_t','hasilbridgingradiologi_t.order_no = (infopasienradiologi_v.no_masukpenunjang || \'-\' || infopasienradiologi_v.tindakanpelayanan_id)')
                ->leftJoin('pasienmasukpenunjang_t', 'infopasienradiologi_v.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
                ->andWhere($conditions)
                ->groupBy(
                    'infopasienradiologi_v.pendaftaran_id,
                    infopasienradiologi_v.pasienmasukpenunjang_id,
                    infopasienradiologi_v.daftartindakan_id,
                    infopasienradiologi_v.tindakanpelayanan_id,
                    infopasienradiologi_v.tglmasukpenunjang,
                    infopasienradiologi_v.no_rujukan,
                    infopasienradiologi_v.tgl_verifikasi,
                    infopasienradiologi_v.daftartindakan_nama,
                    hasilpemeriksaanrad_t.is_deleted,
                    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                    hasilpemeriksaanrad_t.is_hasilkritis,
                    infopasienradiologi_v.status_penunjang,
                    infopasienradiologi_v.status_penunjang ,
                    infopasienradiologi_v.status_periksa,
                    statusperiksa_rad.lookup_name ,
                    infopasienradiologi_v.no_pendaftaran,
                    infopasienradiologi_v.dokter_penunjang,
                    infopasienradiologi_v.ruangan_nama,
                    infopasienradiologi_v.is_hasil,
                    infopasienradiologi_v.tgl_verifikasi,
                    infopasienradiologi_v.status_batal,
                    statusperiksa_penunjangan.lookup_name,
                    hasilbridgingradiologi_t.image_link,
                    infopasienradiologi_v.is_read'
            )
            ->orderBy([
                'infopasienradiologi_v.tglmasukpenunjang' => SORT_DESC,
                'infopasienradiologi_v.daftartindakan_nama' => SORT_ASC
			])
			->all();
        $total_belum_baca = 0;
        foreach($data_radiologi as $key => $val){
            array_push($temp_rad,$val['no_rujukan']);
            if($val['is_hasil'] && $val['is_read'] == false && $val['tgl_verifikasi'] != null){
                $total_belum_baca++;
            }
        }
        if($is_cppt){
            return [
                'total_belum_baca' => $total_belum_baca,
            ];
        }

        $data_order_rad = (new \yii\db\Query())
        ->select([
            'pendaftaran_id',
            'no_rujukan',
            'tgl_rujukan as tglmasukpenunjang',
            'nama_pemeriksaan as daftartindakan_nama',
            'status_periksa',
            'status_penunjang',
            'stat_penunjang',
            'no_pendaftaran',
            'ruangan_nama',
        ])
        ->andWhere(['infoorderanrad_v.pendaftaran_id' => $pendaftaran_id])
        ->andWhere(['NOT IN', 'no_rujukan', $temp_rad])
        ->from('infoorderanrad_v')
        ->all();

        foreach ($data_order_rad as $key => $v) {
            $v['pasienmasukpenunjang_id'] = isset($v['pasienmasukpenunjang_id']) ? $v['pasienmasukpenunjang_id'] : null;
            $v['daftartindakan_id'] = isset($v['daftartindakan_id']) ? $v['daftartindakan_id'] : null;
            $v['tindakanpelayanan_id'] = isset($v['tindakanpelayanan_id']) ? $v['tindakanpelayanan_id'] : null;
            $v['tgl_verifikasi'] = isset($v['tgl_verifikasi']) ? $v['tgl_verifikasi'] : null;
            $v['is_deleted'] = isset($v['is_deleted']) ? $v['is_deleted'] : false;
            $v['no_hasilrad'] = isset($v['no_hasilrad']) ? $v['no_hasilrad'] : null;
            $v['is_hasilkritis'] = isset($v['is_hasilkritis']) ? $v['is_hasilkritis'] : null;
            $v['hasilpemeriksaanrad_id'] = isset($v['hasilpemeriksaanrad_id']) ? $v['hasilpemeriksaanrad_id'] : null;
            $v['stat_periksa'] = isset($v['stat_periksa']) ? $v['stat_periksa'] : ' - ';
            $v['dokter_penunjang'] = isset($v['dokter_penunjang']) ? $v['dokter_penunjang'] : ' - ';
            $v['image_link'] = isset($v['image_link']) ? $v['image_link'] : null;
            $temp_order[$key] = $v;
         }

        $data = array_merge($temp_order,$data_radiologi);
        return [
            'data_radiologi' => $data,
            'total_belum_baca' => $total_belum_baca,
        ];
	}

	public function getTotalHasilRadiologi($pendaftaran_id, $pasienadmisi_id)
	{
		$query = HasilPemeriksaanRad::find()
			->andWhere(['not', ['tgl_verifikasi' => null]])
			->andWhere(['is_read' => false, 'pendaftaran_id' => $pendaftaran_id]);
		
		//disable due to igd and ranap have same rad results
		// if (!empty($pasienadmisi_id)) { 
		// 	$query->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
		// }

		$query = $query->count();
		
		return $query;

	}
}
