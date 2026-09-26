<?php
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoPrint;
use Integrasi\Components\DocoHelpers;

class CetakLapKunjunganRawatInap extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
	{
		ini_set('memory_limit', '-1');
		set_time_limit(0);
      $cacheFiles = Yii::$app->cacheFiles;
		$isExcel = $this->isExcel;
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$path = $dir.'/'.$rootPath.'/'.$this->unique_str;
		if($isExcel) {
			return $this->generateExcel($cacheFiles, $path);
		}
		else {
			return $this->generatePdf($cacheFiles, $path);
		}
	}

	private function generateExcel($cacheFiles, $path)
	{
		$totalPerPage = $this->totalPerPage;
		$getData = $this->getData;
		$filter = isset($getData['params']) ? $getData['params'] : [];
		$advancedFilters = isset($filter['advanced-filter']) ? $filter['advanced-filter'] : [];
		$row = $footer = [];
		$no = 1;
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish',
				'messageProcess' => 'Sedang mengekstrak data',
				'progress' => 80
			]),
		]);

		for ($x = 0; $x < $totalPerPage; $x++) {
			$data = $cacheFiles->get($this->unique_str .'-'. $x);
			if (!empty($data)) {
				foreach ($data as $key => $value) {
					$row[] = $value;
				}
			}
			$cacheFiles->delete($this->unique_str .'-'. $x);
		}

		$start   = date('Y-m-d');
		$end     = date('Y-m-d');
		$namaPasien = isset($advancedFilters['nama_pasien']) ? $advancedFilters['nama_pasien'] : '-';
		$no_rekam_medik = isset($advancedFilters['no_rekam_medik']) ? $advancedFilters['no_rekam_medik'] : '-';
		$no_pendaftaran = isset($advancedFilters['no_pendaftaran']) ? $advancedFilters['no_pendaftaran'] : '-';
		$carabayarNama = $penjaminNama = $ruangan_nama = $kamarruangan_nokamar = $no_tempattidur = $nama_pegawai = '-';

		if(!empty($advancedFilters)) {
				if (isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
						$start = $advancedFilters['tgl_pendaftaran_awal'];
						$end = $advancedFilters['tgl_pendaftaran_akhir'];
				}
				if(isset($advancedFilters['carabayar_id'])) {
						$carabayar_id = $advancedFilters['carabayar_id'];
						$data = Yii::$app->db->createCommand("SELECT * FROM carabayar_m WHERE carabayar_id = {$carabayar_id}")->queryOne();
						$carabayarNama = isset($data['carabayar_nama']) ? $data['carabayar_nama'] : '-';
				}
				if(isset($advancedFilters['penjamin_id'])) {
						$penjamin_id = $advancedFilters['penjamin_id'];
						$data = Yii::$app->db->createCommand("SELECT * FROM penjamin_m WHERE penjamin_id = {$penjamin_id}")->queryOne();
						$penjaminNama = isset($data['penjamin_nama']) ? $data['penjamin_nama'] : '-';
				}
				if(isset($advancedFilters['ruangan_id'])) {
						$ruangan_id = $advancedFilters['ruangan_id'];
						$data = Yii::$app->db->createCommand("SELECT * FROM ruangan_m WHERE ruangan_id = {$ruangan_id}")->queryOne();
						$ruangan_nama = isset($data['lookup_name']) ? $data['lookup_name'] : '-';
				}
				if(isset($advancedFilters['kamarruangan_id'])) {
						$kamarruangan_id = $advancedFilters['kamarruangan_id'];
						$data = Yii::$app->db->createCommand("SELECT * FROM kamarruangan_m WHERE kamarruangan_id = {$kamarruangan_id}")->queryOne();
						$kamarruangan_nokamar = isset($data['kamarruangan_nokamar']) ? $data['kamarruangan_nokamar'] : '-';
				}
				if(isset($advancedFilters['kamartempattidur_id'])) {
						$kamartempattidur_id = $advancedFilters['kamartempattidur_id'];
						$data = Yii::$app->db->createCommand("SELECT * FROM kamartempattidur_m WHERE kamartempattidur_id = {$kamartempattidur_id}")->queryOne();
						$no_tempattidur = isset($data['no_tempattidur']) ? $data['no_tempattidur'] : '-';
				}
				if(isset($advancedFilters['pegawai_id'])) {
						$pegawai_id = $advancedFilters['pegawai_id'];
						$data = Yii::$app->db->createCommand("SELECT * FROM pegawai_m WHERE pegawai_id = {$pegawai_id}")->queryOne();
						$nama_pegawai = isset($data['nama_pegawai']) ? $data['nama_pegawai'] : '-';
				}
		}

		$header = [];
		$dataHeader = $this->customHeader();
		$custHeader = isset($dataHeader['header']) ? $dataHeader['header'] : [];
		$title = 'Laporan Kunjungan Rawat Inap';
		$path = 'uploads/'. $this->unique_str .'.xlsx';
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish',
				'messageProcess' => 'Sedang mengimport data ke dalam excel',
				'progress' => 85
			]),
		]);

		$filePath = DocoHelpers::exportExcel($title, $row, $header,[
			"skipIncrement" => true,
			'customHeader' => $custHeader,
	  	], $footer, [], true);

	  	$filePath->save($path);
	  	Yii::$app->redis->executeCommand('PUBLISH', [
		  'channel' => 'export-excel:'.$this->unique_str,
		  'message' => json_encode([
				 'status' => 'finish',
				 'messageProcess' => 'Proses import excel berhasil',
				 'progress' => 90
			]),
	  	]);

	  	return json_encode([
			'service' => 'Sirs-CetakLapKunjunganRawatInap',
			'timestamp' => date('Y-m-d H:i:s'),
	  	]);
	}

	// private function generatePdf($cacheFiles, $path)
	// {
	// 	$multiple = false;
	// 	Yii::$app->redis->executeCommand('PUBLISH', [
	// 		'channel' => 'export-pdf:'.$this->unique_str,
	// 		'message' => json_encode([
	// 			'status' => 'finish',
  //               'messageProcess' => 'Sedang mengekstrak data pdf',
  //               'progress' => 80
	// 		]),
	// 	]);
	// 	$data = $cacheFiles->get($this->unique_str);
	// 	$attributes = isset($data['attributes']) ? $data['attributes'] : null;
	// 	if(empty($attributes)) {
	// 		Yii::$app->redis->executeCommand('PUBLISH', [
	// 			'channel' => 'export-pdf:'.$this->unique_str,
	// 			'message' => json_encode([
	// 				 'status' => 'failed',
	// 				 'messageProcess' => 'Proses import PDF Gagal',
	// 				 'progress' => 0
	// 			 ]),
	// 		]);
	// 	}
	// 	else {
	// 		$print = new DocoPrint('informasi-kunjungan-pasien');
	// 		$print->attributes = $attributes;
	// 		Yii::$app->redis->executeCommand('PUBLISH', [
	// 			'channel' => 'export-pdf:'.$this->unique_str,
	// 			'message' => json_encode([
	// 				'status' => 'finish',
	// 				'messageProcess' => 'Sedang mengimport data ke dalam PDF',
	// 				'progress' => 85
	// 			]),
	// 		]);
	//
	// 		$print->Output($multiple, $path);
	// 		Yii::$app->redis->executeCommand('PUBLISH', [
	// 			'channel' => 'export-pdf:'.$this->unique_str,
	// 			'message' => json_encode([
	// 				 'status' => 'finish',
	// 				 'messageProcess' => 'Proses import PDF berhasil',
	// 				 'progress' => 90
	// 			 ]),
	// 		]);
	// 	}
	// 	return json_encode([
	// 		'service' => 'Sirs-CetakInformasiPendaftaran',
	// 		'timestamp' => date('Y-m-d H:i:s'),
	// 	]);
	// }

	private function customHeader()
	{
		$custHeader = [
			[
				[
					'label'=>'No',
					'rowspan'=>2,
				],
				[
					'label'=>'Tanggal Pendaftaran',
					'rowspan'=>2,
				],
				[
					'label'=>'Info Kunjungan',
					'rowspan'=>2,
				],
				[
					'label'=>'Jenis Kelamin',
					'rowspan'=>2,
				],
				[
					'label'=>'Umur',
					'rowspan'=>2,
				],
				[
					'label'=>'Golongan Umur',
					'rowspan'=>2,
				],
				[
					'label'=>'Agama',
					'rowspan'=>2,
				],
				[
					'label'=> 'Status Perkawinan',
					'rowspan'=>2,
				],
				[
					'label'=>'Pekerjaan',
					'rowspan'=>2,
				],
				[
					'label'=> 'Kota/Kab',
					'rowspan'=>2,
				],
				[
					'label'=>'Kunjungan',
					'rowspan'=>2,
				],
				[
					'label'=>'Jenis Kasus Penakit',
					'rowspan'=>2,
				],
				[
					'label'=>'Cara Bayar / Penjamin',
					'rowspan'=>2,
				],
				[
					'label' => 'Rujukan',
					'rowspan' => 2
 				],
				[
					'label'=>'Ruangan',
					'rowspan'=>2,
				],
				[
					'label'=>'Kamar - Bed',
					'rowspan'=>2,
				],
				[
					'label'=>'Dokter',
					'rowspan'=>2,
				],
				[
					'label'=>'Kelas Pelayanan / Kelas Tagihan',
					'rowspan'=>2,
				],
				[
					'label'=>'Status Periksa',
					'rowspan'=>2,
				],
				[
					'label'=>'Status Pulang',
					'rowspan'=>2,
				],
				[
					'label'=>'Diagnosa',
					'rowspan'=>2,
				],
				[
					'label'=>'Tanggal Keluar',
					'rowspan'=>2,
				],
			]
		];

		return [
			'header' => $custHeader,
		];
	}
}
