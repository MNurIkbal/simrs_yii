<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Laboratorium
 * @copyright 13 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use app\modules\integrator\models\HasilPemeriksaanLabWynacom;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\models\ProfilRsView;
// model
use app\modules\v1\models\InfoPasienLabView;
use app\modules\v1\models\InputHasilLabView;
use app\modules\v1\models\NilaiPemeriksaanLabView;
use app\modules\v1\models\NilaiPemeriksaanLabDetailView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\HasilPemeriksaanLab;
use app\modules\v1\models\HasilPemeriksaanLabDetail;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\HasilLabView;
use app\modules\v1\models\Pegawai;
use Doco\models\Pegawai AS NewPegawai;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\HasilLabWynacomView;

class InputHasilController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoPasienLabView';

	public $messageBroker = [
        'save' => [
            'services' => [
                'Satusehat' => [
                    'ObservationLab' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
    ];

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

	private function getPasienLab($id = null)
	{
		$model = InfoPasienLabView::find();


		//penambahan enter karena conflict
		
		if ($id) {
			$data = $model->where(['pasienmasukpenunjang_id' => $id])->asArray()->one();
		} 
		else {
			$data = $model->asArray()->all();
		}
		return $data;
	}

	private function getHasilLab($id = null, $samplelab_id)
	{
		$model = InputHasilLabView::find();
		if ($id) {
			$model->where([
					'pasienmasukpenunjang_id' => $id,
					'samplelab_id' => $samplelab_id
			]);
			$data = $model->asArray()->one();
		} else {
			$data = $model->asArray()->all();
		}

		return $data;
	}

	private function getDetailNilaiRujukan($id = null, $sample_id = null, $isCetak = false)
	{
		$header = NilaiPemeriksaanLabView::find();
		$header->where([
			'pasienmasukpenunjang_id' => $id,
			'samplelab_id' => $sample_id
		]);
		$data_header = $header->all();
		$data_header_one = $header->one();
		$where = "";
		if(!empty($sample_id)) {
			$where .= " AND samplelab_id = $sample_id ";
		}

		$queryDetail = "SELECT hasilpemeriksaanlab_id FROM hasilpemeriksaanlab_t WHERE pasienmasukpenunjang_id = {$id}";
		$pemeriksaanLabDetail = Yii::$app->db->createCommand($queryDetail)->queryAll();
		$listDetailId = [];

		if(!$isCetak) {
			$where .= '';
		}
		else {
            $conditionDetail = "";
            if(!empty($pemeriksaanLabDetail)) {
                foreach ($pemeriksaanLabDetail as $key => $value) {
                    $listDetailId[] = isset($value['hasilpemeriksaanlab_id']) ? $value['hasilpemeriksaanlab_id'] : null;
                }
                $conditionDetail = "(" . implode(",", $listDetailId) . ")";
            }
            
            if(!empty($conditionDetail)) {
                $where .= " AND hasilpemeriksaanlab_id IN $conditionDetail ";
            }
		}
		$list_pemeriksaan_id = $list_data = [];
		foreach ($data_header as $key => $value) {
			$list_pemeriksaan_id[] = $value['pemeriksaanlab_id'];
		}
		
		$umur = !empty($data_header_one['umur']) ? $data_header_one['umur'] : '0 tahun 0 bulan 1 hari';
		$replace = preg_match_all('/(\d+)/', $umur, $age);
		$tahun = isset($age[0][0]) ? $age[0][0] * 365 : 0;
		$bulan = isset($age[0][1]) ? $age[0][1] * 30 : 0;
		$hari = isset($age[0][2]) ? $age[0][2] : 1;
		$totalDays = $tahun + $bulan + $hari;

		$golonganUmur = "SELECT
			golonganumurlab_m.golonganumurlab_id,
			golonganumurlab_m.gol_umurlab_nama,
			golonganumurlab_m.gol_umurlab_minimal,
			golonganumurlab_m.gol_umurlab_maksimal
	  	FROM golonganumurlab_m
	  	WHERE golonganumurlab_m.gol_umurlab_maksimal >= $totalDays
	  	and golonganumurlab_m.gol_umurlab_minimal <= $totalDays";
		
		$data_golumur = Yii::$app->db->createCommand($golonganUmur)->queryAll();
		
		$golonganUmurId = ($data_golumur) ? $data_golumur : null;
		if(!empty($golonganUmurId)) {
			$tmpGolonganUmur = [];
			foreach ($golonganUmurId as $key => $value) {
				$tmpGolonganUmur[] = $value['golonganumurlab_id'];
			}

			$tmpGolonganUmur = implode(",", $tmpGolonganUmur);
			$where .= " AND golonganumur_id IN ($tmpGolonganUmur) ";
		}

		$jenis_kelamin = $data_header_one['jeniskelamin'];
		
		if ($isCetak == true) {
			$queryHasil = "SELECT * FROM nilaipemeriksaanlabdetail_v 
				WHERE pasienmasukpenunjang_id = {$id} AND jenis_kelamin = {$jenis_kelamin} AND is_deleted = FALSE AND is_verifikasi IS TRUE {$where}";
		} else {
			$queryHasil = "SELECT * FROM nilaipemeriksaanlabdetail_v 
			WHERE pasienmasukpenunjang_id = {$id} AND jenis_kelamin = {$jenis_kelamin} AND is_deleted = FALSE {$where}";
		}
            
		$data_detail = Yii::$app->db->createCommand($queryHasil)->queryAll();

		$golonganSemuaUmur = DocoConstants::GOL_UMUR_SEMUA_UMUR;
		$counterData = 0;
		foreach ($data_detail as $key => $value) {
			$nilai_rujukan = isset($value['nilai_rujukan']) ? $value['nilai_rujukan'] : '';
			if(empty($nilai_rujukan)) {
				$nilai_rujukan = $value['nilai_min'].' - '.$value['nilai_max'];
			}
			if(!$isCetak) {
				if (($totalDays >= $value['gol_umurlab_minimal']) && ($totalDays <= $value['gol_umurlab_maksimal'])) {
					// if (isset($list_data[$value['nama_rujukan']])) continue;
					$list_data[$value['nama_rujukan']][] = [
						'pemeriksaanlab_id' => $value['pemeriksaanlab_id'],
						'daftartindakan_id' => $value['daftartindakan_id'],
						'tipepaket_id' => $value['tipepaket_id'],
						'daftartindakan_nama' => $value['daftartindakan_nama'],
						'hasil' => $value['hasil'],
						'petugaslab_id' => $value['petugaslab_id'],
						'petugaslab_nama' => $value['petugaslab_nama'],
						'nama_rujukan' => $value['nama_rujukan'],
						'nilai_min' => $value['nilai_min'],
						'nilai_max' => $value['nilai_max'],
						'nilairujukan_id' => $value['nilairujukan_id'],
						'nilai_rujukan' => $nilai_rujukan,
						'satuan_hasillab' => $value['satuanlab_nama'],
						'satuanlab_nama' => $value['satuanlab_nama'],
						'keterangan' => $value['keterangan'],
						'gol_umurlab_nama' => $value['gol_umurlab_nama'],
						'gol_umurlab_minimal' => $value['gol_umurlab_minimal'],
						'gol_umurlab_maksimal' => $value['gol_umurlab_maksimal'],
						'metode' => $value['metode'],
						'no_urut' => $value['no_urut'],
						'jenispemeriksaanlab_nama' => $value['jenispemeriksaanlab_nama'],
						'is_verifikasi' => $value['is_verifikasi'],
						'tanggal_verifikasi' => $value['tanggal_verifikasi'],
					];
				}
			}
			else {
				$list_data[$value['nama_rujukan']][] = [
					'pemeriksaanlab_id' => $value['pemeriksaanlab_id'],
					'daftartindakan_id' => $value['daftartindakan_id'],
					'tipepaket_id' => $value['tipepaket_id'],
					'daftartindakan_nama' => $value['daftartindakan_nama'],
					'hasil' => $value['hasil'],
					'petugaslab_id' => $value['petugaslab_id'],
					'petugaslab_nama' => $value['petugaslab_nama'],
					'nama_rujukan' => $value['nama_rujukan'],
					'nilai_min' => $value['nilai_min'],
					'nilai_max' => $value['nilai_max'],
					'nilairujukan_id' => $value['nilairujukan_id'],
					'nilai_rujukan' => $nilai_rujukan,
					'satuan_hasillab' => $value['satuanlab_nama'],
					'satuanlab_nama' => $value['satuanlab_nama'],
					'keterangan' => $value['keterangan'],
					'gol_umurlab_nama' => $value['gol_umurlab_nama'],
					'gol_umurlab_minimal' => $value['gol_umurlab_minimal'],
					'gol_umurlab_maksimal' => $value['gol_umurlab_maksimal'],
					'metode' => $value['metode'],
					'no_urut' => $value['no_urut'],
					'jenispemeriksaanlab_nama' => $value['jenispemeriksaanlab_nama'],
					'is_verifikasi' => $value['is_verifikasi'],
					'tanggal_verifikasi' => $value['tanggal_verifikasi'],
				];
			}
		}
		return $list_data;
	}

	private function getNilaiPemeriksaan($id = null)
	{
		$model = NilaiPemeriksaanLabView::find();
		if ($id) {
			$model->where(['pasienmasukpenunjang_id' => $id]);
			$data = $model->asArray()->one();
		} else {
			$data = $model->asArray()->all();
		}

		return $data;
	}

	private function getDokter($ruangan_id)
	{
		$model = DokterView::find();
		$model->select(['pegawai_id', 'nama_pegawai']);
		if ($ruangan_id) {
			$model->where(['ruangan_id' => $ruangan_id]);
			$data = $model->asArray()->all();
		} else {
			$data = $model->asArray()->all();
		}

		$analyst = PegawaiView::find();
		$analyst->select(['pegawai_id', 'nama_pegawai']);
		$analyst->andWhere(['kelompokpegawai_id' => 13, 'instalasi_id'=>4]);
		if ($ruangan_id) {
			$analyst->andWhere(['ruangan_id' => $ruangan_id]);
			$data2 = $analyst->asArray()->all();
		} else {
			$data2 = $analyst->asArray()->all();
		}

		$data = array_merge($data, $data2);

		return $data;
	}

	private function getHasilPemeriksaan($id, $sample_id)
	{
		$model = HasilPemeriksaanLab::find();
		if ($id) {
			$model->where([
					'pasienmasukpenunjang_id' => $id,
					'samplelab_id' => $sample_id
			]);
			$data = $model->asArray()->one();
		}

		return $data;
	}

	public function actionGenerateApi($id, $ruangan_id, $sample_id)
	{
		$data_pasien = $this->getPasienLab($id);
		$data_hasil_lab = $this->getHasilLab($id, $sample_id);
		$header_gol_umur = $this->getNilaiPemeriksaan($id);
		$detail_gol_umur = $this->getDetailNilaiRujukan($id, $sample_id);
		$data_dokter = $this->getDokter($ruangan_id);
		$data_hasil = $this->getHasilPemeriksaan($id, $sample_id);

		$result = [
			'data-pasien' => $data_pasien,
			'data-hasil-lab' => $data_hasil_lab,
			'header-gol-umur' => $header_gol_umur,
			'detail-gol-umur' => $detail_gol_umur,
			'data-dokter' => $data_dokter,
			'data-hasil' => $data_hasil,
		];

		return $result;
	}

	public function actionUpload()
	{
		try {
			$request = Yii::$app->request;
			$post = $request->post();
			if (!empty($post['hasilpemeriksaanlab_id'])) {
					$model = HasilPemeriksaanLab::findOne($post['hasilpemeriksaanlab_id']);
			} else {
					$model = new HasilPemeriksaanLab;
			}
			if ($request->post()) {
					$model->attributes = $request->post();
					if ($model->save()) {
						return ['message' => 'Data Berhasil di simpan'];
					} else {
						$errors = DocoHelpers::parseError($model->errors, 'HasilPemeriksaanLab');
						return [
							'data' => $errors,
							'status' => 422
						];
					}
			}
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

	public function actionSave()
	{
		$connection = Yii::$app->db;
		$request    = Yii::$app->request;
		$post       = $request->post();
		$dataInsert = [];
		try {
			$transaction = $connection->beginTransaction();
			$hasilpemeriksaanlab_id = $post['hasilpemeriksaanlab_id'];
			if (!empty($hasilpemeriksaanlab_id)) {
					$model = HasilPemeriksaanLab::findOne($hasilpemeriksaanlab_id);
					$sql = "DELETE from hasilpemeriksaanlabdetail_t
						WHERE hasilpemeriksaanlab_id = ".$post['hasilpemeriksaanlab_id']."
					";
					$connection->createCommand($sql)->execute();
			} else {
					$model = null;
					$model = new HasilPemeriksaanLab;
			}
			$model->attributes = $post;
			$model->pegawailab_id = $post['pegawailab_id'];
			$model->tgl_hasilpemeriksaanlab = date('Y-m-d H:i:s', strtotime($post['tanggal']));
			if ($post['is_kritis']) {
					$model->is_kritis = ($post['is_kritis']) ? 1 : 0;
					$model->tgl_kritis = date('Y-m-d H:i:s');
			}
			if (!empty($post['expertise'])) {
					$model->tgl_expertise = date('Y-m-d H:i:s');
					$model->is_expertise = true;
			}
			if ($model->validate() && $model->save()) {
                $hasilpemeriksaan_id = !empty($post['hasilpemeriksaanlab_id'])
                    ? $post['hasilpemeriksaanlab_id']
                    : $model->hasilpemeriksaanlab_id;
                $count = !empty($post['nilairujukan_id']) ? count($post['nilairujukan_id']) : 0;
                
                if ($count >= 1) {
                    for ($i=0; $i < $count; $i++) {
                        $dataInsert[] = [
                            'hasilpemeriksaanlab_id' => $hasilpemeriksaan_id,
                            'samplelab_id'           => $post['samplelab_id'],
                            'tindakanpelayanan_id'   => $post['daftartindakan_id'][$i],
                            'pemeriksaanlab_id'      => $post['pemeriksaanlab_id'][$i],
                            'nilairujukan_id'        => $post['nilairujukan_id'][$i],
                            'hasil'                  => $post['hasil'][$i],
                            'petugaslab_id'          => $post['petugaslab_id'][$i],
                            'nilai_rujukan'          => $post['nilai_rujukan'][$i],
                            'satuan_hasil'           => $post['satuan_hasil'][$i],
                            'keterangan'             => $post['keterangan'][$i],
                            'tindakanpaket_id'       => $post['tindakanpaket_id'][$i],
                            'is_verifikasi'          => $post['is_verifikasi'][$i],
                            'tanggal_verifikasi'     => $post['is_verifikasi'][$i] ? date('Y-m-d H:i:s') : null,
                            'petugas_verifikasi'     => $post['is_verifikasi'][$i] ? $post['pegawailab_id'] : null,
                        ];
                    }
                    $datasnya = HasilPemeriksaanLabDetail::batchInsert($dataInsert);
                }
                $modelPenunjang = PasienMasukPenunjangT::findOne($post['pasienmasukpenunjang_id']);
                $modelPenunjang->status_periksa = DocoConstants::ST_PERIKSA;
                $modelPenunjang->save();
                $myDatas = HasilPemeriksaanLabDetail::find()->where(['hasilpemeriksaanlab_id' => '73']);
                $transaction->commit();
                return [
					'pasienkirimkeunitlain_id' => $modelPenunjang->pasienkirimkeunitlain_id,
					'pendaftaran_id' => $modelPenunjang->pendaftaran_id,
					'tanggal' => isset($post['tanggal']) ? date('Y-m-d H:i:s', strtotime($post['tanggal'])) : date('Y-m-d H:i:s'),
					'message' => 'Data Berhasil di simpan', 
				];
            }
		} catch (\yii\db\Exception $e) {
			// $transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			return [
					'message' => $e->getMessage()
			];
		} catch (\Exception $e) {
			// $transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			return [
					'message' => $e->getMessage()
			];
		}
	}

	/**
    * @controller actionCetakPdf
	* @attribute #cetak_pemeriksaan# => table
	**/
	public function actionCetakPdf()
	{
		$request = Yii::$app->request;
		$get = $request->get();
		$namaRs = $this->getDataRs();
		$namaRs = str_replace(' ', '_', $namaRs);
		$modul = $request->get('modul', null);
		if (isset($get['id'])) {
			$id = $get['id'];
			$penunjang_id = $get['penunjang_id'];
			$data_pasien = $this->getPasienLab($penunjang_id);
			$data_pasien['umur'] = DocoHelpers::getUmur($data_pasien['tanggal_lahir'], false, false, $data_pasien['tglmasukpenunjang']);
			$data_hasil_lab = $this->getHasilLab($penunjang_id, $id);
			$data_hasil = $this->getHasilPemeriksaan($penunjang_id, $id);
			$detail_gol_mur = $this->getDetailNilaiRujukan($penunjang_id, $id);
			$nama_sample = !empty($data_hasil_lab['nama_sample'])
					? $data_hasil_lab['nama_sample']
					: '';
			$tanggal_pemeriksaan = !empty($data_hasil['tgl_hasilpemeriksaanlab'])
					? $data_hasil['tgl_hasilpemeriksaanlab']
					: date('Y-m-d H:i:s');
			$nohasilperiksalab = !empty($data_hasil['nohasilperiksalab'])
					? $data_hasil['nohasilperiksalab']
					: '';
			if (!empty($data_hasil['pegawailab_id'])) {
					$pegawai = Pegawai::findOne($data_hasil['pegawailab_id']);
					$nama_pegawai = $pegawai->nama_pegawai;
			} else {
					$nama_pegawai = '';
			}
			$expertise = !empty($data_hasil['expertise'])
					? $data_hasil['expertise']
					: '';
			$print = new DocoPrint('chpl');
			$namaPasien = str_replace(' ', '_', $data_pasien['nama_pasien']);
			$formatDefault = $namaPasien.'_'.$data_pasien['no_rekam_medik'];
			$formatDocName = $formatDefault;
			if(!empty($tanggal_pemeriksaan)) {
					$formatDocName = $formatDefault.'_'.date('d-M-Y H:i:s', strtotime($tanggal_pemeriksaan));
			}
			$print->docName = $modul.'_'.$namaRs.'_'.$formatDocName;
			$print->attributes = [
					'#cetak_pemeriksaan#' => $this->renderPartial('index', [
						'header'=> $data_pasien,
						'data_hasil_lab'=> $data_hasil_lab,
						'detail' => $detail_gol_mur,
						'nama_sample' => $nama_sample,
						'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
						'nohasilperiksalab' => $nohasilperiksalab,
						'nama_pegawai' => $nama_pegawai,
						'expertise' => $expertise
					]),
			];
			$print->Output();
		}
	}

	private function getHasilLabPemeriksaan($id, $sample_id)
	{
		$model = HasilLabView::find();
		if ($id) {
			$model->where([
					'pasienmasukpenunjang_id' => $id,
					'samplelab_id' => $sample_id
			]);
			$data = $model->asArray()->one();
		} else {
			$data = $model->asArray()->all();
		}

		return $data;
	}

	/**
	* @controller actionCetakHasilPdf
	* @attribute #cetak_hasil_pemeriksaan# => table menampilkan hasil pemeriksaan
    * @attribute #header.no_rekam_medik# => untuk menampilkan No Rekam Medik pasien
    * @attribute #header.tgl_lahir# => untuk menampilkan Tanggal Lahir (d-m-Y) pasien
    * @attribute #header.nama_pasien# => untuk menampilkan Nama pasien
    * @attribute #header.usia# => untuk menampilkan Usia (Tahun Bulan Hari) pasien
    * @attribute #header.jenis_kelamin# => untuk menampilkan Jenis Kelamin pasien
    * @attribute #header.alamat# => untuk menampilkan Alamat pasien
    * @attribute #header.order_id# => untuk menampilkan Order ID / No Masuk Penunjang pasien
    * @attribute #header.tgl_order# => untuk menampilkan Tanggal Order (d-m-Y H:i:s) pasien
    * @attribute #header.tgl_hasil# => untuk menampilkan Tanggal Hasil (d-m-Y H:i:s) pasien
    * @attribute #header.ruangan# => untuk menampilkan Ruangan pasien
    * @attribute #header.dokter_pengirim# => untuk menampilkan Dokter Pengirim pasien
    */
	public function actionCetakHasilPdf()
	{
		$request = Yii::$app->request;
		$get = $request->get();
		$namaRs = $this->getDataRs();
		$kota = $namaRs['kota'];
		$namaRs = str_replace(' ', '_', $namaRs['nama_rumahsakit']);
		$modul = $request->get('modul', 'Laboratorium');
		$formate_datetime = 'd-m-Y H:i:s';
		
		if (isset($get['id'])) {
			$id = $get['id'];
			$pelayanan_id = isset($get['pelayanan_id']) ?$get['pelayanan_id']: null;
			$samplelab_id = isset($get['samplelab_id']) ?$get['samplelab_id']: null;
			$data_pasien = $this->getPasienLab($id);
			$tanggalLahir = !empty($data_pasien['tanggal_lahir']) ? date('d-m-Y', strtotime($data_pasien['tanggal_lahir'])) : '-';
			$umur = !empty($data_pasien['tanggal_lahir']) ? DocoHelpers::getUmur($data_pasien['tanggal_lahir']) : '-';
			$data_pasien['umur'] = $tanggalLahir.' / '.$umur;
			if(!empty($pelayanan_id)) {
					$data_hasil_lab = $this->getHasilLab($id, $pelayanan_id);
					$data_hasil = $this->getHasilPemeriksaan($id, $pelayanan_id);
					$detail_gol_mur = $this->getDetailNilaiRujukan($id, $pelayanan_id, true);
					$nama_sample = !empty($data_hasil_lab['nama_sample'])
					? $data_hasil_lab['nama_sample']
					: '';
					$tanggal_pemeriksaan = !empty($data_hasil['tgl_hasilpemeriksaanlab'])
						? $data_hasil['tgl_hasilpemeriksaanlab']
						: date('Y-m-d H:i:s');
					$nohasilperiksalab = !empty($data_hasil['nohasilperiksalab'])
						? $data_hasil['nohasilperiksalab']
						: '';
					if (!empty($data_hasil['pegawailab_id'])) {
						$pegawai = Pegawai::findOne($data_hasil['pegawailab_id']);
						$nama_pegawai = $pegawai->nama_pegawai;
						$nip_dokter = $pegawai->nomorindukpegawai;
					} else {
						$nama_pegawai = '';
						$nip_dokter = '';
					}
					$expertise = !empty($data_hasil['expertise'])
						? $data_hasil['expertise']
						: '';

					$print = new DocoPrint('chpl');
					$namaPasien = str_replace(' ', '_', $data_pasien['nama_pasien']);
					$formatDefault = $namaPasien.'_'.$data_pasien['no_rekam_medik'];
					$formatDocName = $formatDefault;
					if(!empty($tanggal_pemeriksaan)) {
						$formatDocName = $formatDefault.'_'.date('d-M-Y H:i:s', strtotime($tanggal_pemeriksaan));
					}
					$print->docName = $modul.'_'.$namaRs.'_'.$formatDocName;
					$signaturePath = NewPegawai::signatureEmployee($data_hasil['pegawailab_id']);
					$print->attributes = [
						'#lokasi#' => $kota .', '. date('d M Y'),
						'#ttd_dokter#' => $signaturePath,
						'#nama_dokter#' => $nama_pegawai,
						'#nama_pegawai#' => $nama_pegawai,
						'#nip_dokter#' => $nip_dokter,
						'#timestamps#' => date('d M Y H:i:s'),
						'#header.no_rekam_medik#' => !empty($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-',
						'#header.nama_pasien#' => !empty($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-',
						'#header.tgl_lahir#' => !empty($data_pasien['tanggal_lahir']) ? date('d-m-Y', strtotime($data_pasien['tanggal_lahir'])) : '-',
						'#header.usia#' => !empty($data_pasien['tanggal_lahir']) ? DocoHelpers::getUmur($data_pasien['tanggal_lahir']) : '-',
						'#header.jenis_kelamin#' => !empty($data_pasien['j_kelamin']) ? $data_pasien['j_kelamin'] : '-',
						'#header.alamat#' => !empty($data_pasien['alamat_pasien']) ? $data_pasien['alamat_pasien'] : '-',
						'#header.order_id#' => !empty($data_pasien['no_masukpenunjang']) ? $data_pasien['no_masukpenunjang'] : '-',
						'#header.tgl_order#' => !empty($data_pasien['tglmasukpenunjang']) ? date($formate_datetime, strtotime($data_pasien['tglmasukpenunjang'])) : '-',
						'#header.tgl_hasil#' => !empty($data_pasien['tanggal_verifikasi']) ? date($formate_datetime, strtotime($data_pasien['tanggal_verifikasi'])) : '-',
						'#header.ruangan#' => !empty($data_pasien['ruangan_nama']) ? $data_pasien['ruangan_nama'] : '-',
						'#header.dokter_pengirim#' => !empty($data_pasien['dokter_perujuk_nama']) ? $data_pasien['dokter_perujuk_nama'] : '-',
						'#cetak_hasil_pemeriksaan#' => $this->renderPartial('index', [
							'header'=> $data_pasien,
							'data_hasil_lab'=> $data_hasil_lab,
							'detail' => $detail_gol_mur,
							'nama_sample' => $nama_sample,
							'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
							'nohasilperiksalab' => $nohasilperiksalab,
							'nama_pegawai' => $nama_pegawai,
							'expertise' => $expertise
						]),
					];
					$print->Output();
			}
			else {
				$dpjpId = @$data_pasien['pegawai_id'];
				$tgl_header = !empty($data_pasien['tglmasukpenunjang']) ? $data_pasien['tglmasukpenunjang'] : null;
				$header = NilaiPemeriksaanLabView::find();
				$header->where([
					'pasienmasukpenunjang_id' => $id,
				]);
				if(!empty($samplelab_id)) {
					$header->andWhere([
						'samplelab_id' => $samplelab_id,
					]);
				}
				$data_header = $header->all();
				$list_pemeriksaan_id = $list_data = $list_detail = [];
				foreach ($data_header as $value) {
					$list_pemeriksaan_id[$value['samplelab_id']][] = $value['pemeriksaanlab_id'];
					$list_data[$value['samplelab_id']][] = $value;
				}
				foreach ($list_data as $sample_id => $data) {
					foreach ($data as $value) {
						$samplelab_id = $value['samplelab_id'];
						$list_id = isset($list_pemeriksaan_id[$value['samplelab_id']])
								? $list_pemeriksaan_id[$value['samplelab_id']]
								: [];
						$detail = NilaiPemeriksaanLabDetailView::find();
						$detail->where([
								'pemeriksaanlab_id' => $list_id,
								'jenis_kelamin' => $value['jeniskelamin'],
								'samplelab_id' => $samplelab_id,
								'pasienmasukpenunjang_id' => $id
						]);
					
						$hasil_lab = $this->getHasilPemeriksaan($id, $samplelab_id);
						$dpjpId = $hasil_lab['pegawailab_id'];
						if (!empty($hasil_lab['pegawailab_id'])) {
								$pegawai = Pegawai::findOne($hasil_lab['pegawailab_id']);
								$nama_pegawai = $pegawai->nama_pegawai;
								$nip_dokter = $pegawai->nomorindukpegawai;
						} else {
								$nama_pegawai = '';
								$nip_dokter = '';
						}
						
						if(!empty($hasil_lab['tgl_hasilpemeriksaanlab'])) {
								$tgl_header = $hasil_lab['tgl_hasilpemeriksaanlab'];
						}
						$list_detail[$samplelab_id]['data_hasil'] = [
								'tgl_hasilpemeriksaanlab' => !empty($hasil_lab['tgl_hasilpemeriksaanlab']) ? $hasil_lab['tgl_hasilpemeriksaanlab'] : '',
								'nohasilperiksalab' => !empty($hasil_lab['nohasilperiksalab']) ? $hasil_lab['nohasilperiksalab'] : '',
								'nama_pegawai' => $nama_pegawai,
								'expertise' => !empty($hasil_lab['expertise']) ? $hasil_lab['expertise'] : '',
								'nama_sample' => !empty($hasil_lab['nama_sample']) ? $hasil_lab['nama_sample'] : '',
						];
					}
				}
				
				if (!empty($list_detail)) {
					$print = new DocoPrint('chpl');
					$namaPasien = str_replace(' ', '_', $data_pasien['nama_pasien']);
					$nama_pasien = trim($namaPasien);
					$formatDefault = $nama_pasien.'_'.$data_pasien['no_rekam_medik'];
					$formatDocName = $formatDefault;
					if(!empty($tgl_header)) {
						$formatDocName = $formatDefault.'_'.date('d-M-Y H:i:s', strtotime($tgl_header));
					}
					$print->docName = $modul.'_'.$namaRs.'_'.$formatDocName;
					$no = 0;
					$details = $queries = [];
					foreach ($list_detail as $sample_id => $query) {
						$details[] = $this->getDetailNilaiRujukan($id, $sample_id, true);
						$queries[] = $query;
					}
					$signaturePath = NewPegawai::signatureEmployee($dpjpId);
					$print->attributes = [
						'#lokasi#' => $kota .', '. date('d M Y'),
						'#ttd_dokter#' => $signaturePath,
						'#nama_dokter#' => $nama_pegawai,
						'#nama_pegawai#' => $nama_pegawai,
						'#nip_dokter#' => $nip_dokter,
						'#timestamps#' => date('d M Y H:i:s'),
						'#header.no_rekam_medik#' => !empty($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-',
						'#header.nama_pasien#' => !empty($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-',
						'#header.tgl_lahir#' => !empty($data_pasien['tanggal_lahir']) ? date('d-m-Y', strtotime($data_pasien['tanggal_lahir'])) : '-',
						'#header.usia#' => !empty($data_pasien['tanggal_lahir']) ? DocoHelpers::getUmur($data_pasien['tanggal_lahir']) : '-',
						'#header.jenis_kelamin#' => !empty($data_pasien['j_kelamin']) ? $data_pasien['j_kelamin'] : '-',
						'#header.alamat#' => !empty($data_pasien['alamat_pasien']) ? $data_pasien['alamat_pasien'] : '-',
						'#header.order_id#' => !empty($data_pasien['no_masukpenunjang']) ? $data_pasien['no_masukpenunjang'] : '-',
						'#header.tgl_order#' => !empty($data_pasien['tglmasukpenunjang']) ? date($formate_datetime, strtotime($data_pasien['tglmasukpenunjang'])) : '-',
						'#header.tgl_hasil#' => !empty($data_pasien['tanggal_verifikasi']) ? date($formate_datetime, strtotime($data_pasien['tanggal_verifikasi'])) : '-',
						'#header.ruangan#' => !empty($data_pasien['ruangan_nama']) ? $data_pasien['ruangan_nama'] : '-',
						'#header.dokter_pengirim#' => !empty($data_pasien['dokter_perujuk_nama']) ? $data_pasien['dokter_perujuk_nama'] : '-',
						'#cetak_hasil_pemeriksaan#' => $this->renderPartial('cetak_hasil', [
							'header'=> $data_pasien,
							'details'=> $details,
							'queries' => $queries,
							'nama_pegawai' => $nama_pegawai,
						]),
					];
					$print->Output();
				}
			}
		}
	}

	public function actionCekDataWynacom() {
		$request = Yii::$app->request;
		$get = $request->get();
		if($get['id']) {
			$id = $get['id'];
			$model = HasilPemeriksaanLabWynacom::latestRecordById($id);
			
			$jml_data = count($model);
			return ['jml_data' => $jml_data];
		} else {
			return ['data' => []];
		}
	}

	/**
 	* @controller actionCetakHasilPdfWynacom
	* @attribute #cetak_hasil_pemeriksaan# => table
	**/
	public function actionCetakHasilPdfWynacom()
	{
		$get = Yii::$app->request->get();
		if (isset($get['id'])) {
			$id = $get['id'];
			// $header = HasilLabWynacomView::find();
			// $header->where([
			//     'pasienmasukpenunjang_id' => $id,
			// ]);
			// $data_detail = $header->all();
			// $sql = "
			//     WITH sort_hasil AS (
			//       SELECT m.*, ROW_NUMBER() OVER (PARTITION BY test_nama_lis ORDER BY tgl_pemeriksaan DESC) AS rn
			//       FROM laporanhasillab_v AS m
			//       WHERE pasienmasukpenunjang_id  = '{$id}'
			//     )
			//     SELECT * FROM sort_hasil WHERE rn = 1";
			
			// $data_detail = HasilLabWynacomView::findBySql($sql)->asArray()->all();
			$data_detail = HasilPemeriksaanLabWynacom::latestRecordById($id);
			$row = [];
			$print = new DocoPrint();
			$print->attributes = [
				'#cetak_hasil_pemeriksaan#' => $this->renderPartial('cetak_wynacom', [
					'detail'   => $data_detail,
				]),
			];
			$print->Output();
		}
	}

	private function getDataRs()
	{
		$dataRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
			return ProfilRsView::find()->asArray()->one();
		});
		return $dataRs; 
	}

	public function actionTest()
	{
		DokterView::find()->select(['nama_pegawai'])->where(['pegawai_id' => $data_hasil['pegawailab_id']])->one();
	}	

	public function actionGetPasienMasukPenunjang()
	{
		$ids = Yii::$app->request->get('id',null);
		if (!empty($ids)) {
			return PasienMasukPenunjangT::findOne($ids);
		}
		return null;
	}

	public function actionBatalInput()
	{
		$connection = Yii::$app->db;
		$request = Yii::$app->request;
		$post = $request->post();
		try {
			$transaction = $connection->beginTransaction();
			
            $pasienmasukpenunjang_id = DocoHelpers::decrypt($request->post('pasienmasukpenunjang_id', null));
            $pegawai_id = $request->post('pegawai_id', null);
            $samplelab_id = DocoHelpers::decrypt($request->post('samplelab_id', null));
            $date = date('Y-m-d H:i:s', time());
			$hasilpemeriksaanlab = $this->getHasilPemeriksaan($pasienmasukpenunjang_id, $samplelab_id);
			$hasilpemeriksaanlab_id = !empty($hasilpemeriksaanlab['hasilpemeriksaanlab_id']) ? $hasilpemeriksaanlab['hasilpemeriksaanlab_id'] : null;
			
            if (!empty($pasienmasukpenunjang_id) && !empty($samplelab_id) && !empty($hasilpemeriksaanlab_id)) {
                Yii::$app->db->createCommand("
					UPDATE hasilpemeriksaanlab_t SET is_deleted = true, deleted_by = {$pegawai_id}, deleted_date = '{$date}', is_expertise = false, expertise = NULL
					WHERE pasienmasukpenunjang_id = {$pasienmasukpenunjang_id} AND samplelab_id = {$samplelab_id}
				")->queryAll();

                Yii::$app->db->createCommand("
					UPDATE hasilpemeriksaanlabdetail_t SET is_deleted = true, deleted_by = {$pegawai_id}, deleted_date = '{$date}'
					WHERE hasilpemeriksaanlab_id = {$hasilpemeriksaanlab_id} AND samplelab_id = {$samplelab_id}
				")->queryAll();

				/** check apakah ada sample yang masih ada hasil */
				$model = HasilPemeriksaanLab::find();
				$model->where([
						'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
				]);
				$data = $model->asArray()->all();
				$count = count($data);
				if($count == 0) {
					$model = PasienMasukPenunjangT::findOne($pasienmasukpenunjang_id);
					$model->status_periksa = DocoConstants::ST_SMPL;
					$model->save();
				}
				$transaction->commit();
			}
			return ['message' => 'Data Berhasil di simpan'];
		} catch (\yii\db\Exception $e) {
			$transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			Yii::error([
				'message' => $e->getMessage()
			]);
			return [
					'message' => $e->getMessage()
			];
		} catch (\Exception $e) {
			$transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			return [
					'message' => $e->getMessage()
			];
		}
	}
}