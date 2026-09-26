<?php
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoPrint;
use Integrasi\Components\DocoHelpers;

class CetakInformasiPendaftaran extends \Integrasi\Contracts\DocoImplement
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
      	$jenis = isset($filter['jenis']) ? $filter['jenis'] : 'rajal';
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
		$noSep = isset($advancedFilters['nosep']) ? $advancedFilters['nosep'] : '-';
		$petugas = isset($advancedFilters['petugas']) ? $advancedFilters['petugas'] : '-';
		$carabayarNama = $penjaminNama = $statusPeriksa = $dokterPoli = '-';

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
			if(isset($advancedFilters['status_periksa'])) {
				$status_periksa_id = $advancedFilters['status_periksa'];
				$data = Yii::$app->db->createCommand("SELECT * FROM lookup_m WHERE lookup_id = {$status_periksa_id}")->queryOne();
				$statusPeriksa = isset($data['lookup_name']) ? $data['lookup_name'] : '-';
			}
			if(isset($advancedFilters['pegawai_id'])) {
				$pegawai_id = $advancedFilters['pegawai_id'];
				$data = Yii::$app->db->createCommand("SELECT pegawai_id, nama_pegawai FROM pegawai_m WHERE pegawai_id = {$pegawai_id}")->queryOne();
				$dokterPoli = isset($data['nama_pegawai']) ? $data['nama_pegawai'] : '-';
			}
		}

		$header = [
			'Tanggal Pendaftaran' => date('d M Y H:i:s', strtotime($start)).' Sampai Dengan '.date('d M Y H:i:s', strtotime($end)),
			'Nama Pasien' => strtoupper($namaPasien),
			'No Rekam Medik' => $no_rekam_medik,
			'No Pendaftaran' => $no_pendaftaran,
			'No SEP' => strtoupper($noSep),
			'Petugas' => strtoupper($petugas),
			'Cara Bayar' => $carabayarNama,
			'Penjamin' => $penjaminNama,
			'Status Periksa' => $statusPeriksa,
			'Dokter Poliklinik' => $dokterPoli,
		];
		$dataHeader = $this->customHeader();
		$custHeader = isset($dataHeader['header']) ? $dataHeader['header'] : [];
		$title = isset($dataHeader['title']) ? 'Informasi Pasien '. $dataHeader['title'] : '';
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
			'service' => 'Sirs-CetakInformasiPendaftaran',
			'timestamp' => date('Y-m-d H:i:s'),
	  	]);
	}

	private function generatePdf($cacheFiles, $path)
	{
		$multiple = false;
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-pdf:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data pdf',
                'progress' => 80
			]),
		]);
		$data = $cacheFiles->get($this->unique_str);
		$attributes = isset($data['attributes']) ? $data['attributes'] : null;
		if(empty($attributes)) {
			Yii::$app->redis->executeCommand('PUBLISH', [
				'channel' => 'export-pdf:'.$this->unique_str,
				'message' => json_encode([
					 'status' => 'failed', 
					 'messageProcess' => 'Proses import PDF Gagal',
					 'progress' => 0
				 ]),
			]);
		}
		else {
			$print = new DocoPrint('informasi-kunjungan-pasien');
			$print->attributes = $attributes;
			Yii::$app->redis->executeCommand('PUBLISH', [
				'channel' => 'export-pdf:'.$this->unique_str,
				'message' => json_encode([
					'status' => 'finish', 
					'messageProcess' => 'Sedang mengimport data ke dalam PDF',
					'progress' => 85
				]),
			]);
			
			$print->Output($multiple, $path);
			Yii::$app->redis->executeCommand('PUBLISH', [
				'channel' => 'export-pdf:'.$this->unique_str,
				'message' => json_encode([
					 'status' => 'finish', 
					 'messageProcess' => 'Proses import PDF berhasil',
					 'progress' => 90
				 ]),
			]);
		}
		return json_encode([
			'service' => 'Sirs-CetakInformasiPendaftaran',
			'timestamp' => date('Y-m-d H:i:s'),
		]);
	}

	private function customHeader()
	{
		$jenis_title = '';
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
					'label'=>'No Pendaftaran',
					'rowspan'=>2,
				],
				[
					'label'=>'No Rekam Medik',
					'rowspan'=>2,
				],
				[
					'label'=>'Nama Pasien',
					'rowspan'=>2,
				],
				[
					'label'=>'Alamat',
					'rowspan'=>2,
				],
				[
					'label'=>'Jenis Kelamin',
					'rowspan'=>2,
				],
				[
					'label'=> 'Poliklinik',
					'rowspan'=>2,
				],
				[
					'label'=>'Jenis Kasus Penyakit',
					'rowspan'=>2,
				],
				[
					'label'=> 'Kelas Pelayanan',
					'rowspan'=>2,
				],
				[
					'label'=>'Dokter Poliklinik',
					'rowspan'=>2,
				],
				[
					'label'=>'Cara Bayar',
					'rowspan'=>2,
				],
				[
					'label'=>'Penjamin',
					'rowspan'=>2,
				],
				[
					'label' => 'UNIT',
					'rowspan' => 2
 				],
				[
					'label'=>'Status Periksa',
					'rowspan'=>2,
				],
				[
					'label'=>'No SEP',
					'rowspan'=>2,
				],
				[
					'label'=>'Limit Tagihan',
					'rowspan'=>2,
				],
				[
					'label'=>'Petugas',
					'rowspan'=>2,
				],
			]
		];
		if($this->jenis == 'ranap') {
			$jenis_title = 'Rawat Inap';
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
						'label'=>'No Pendaftaran',
						'rowspan'=>2,
					],
					[
						'label'=>'No Rekam Medik',
						'rowspan'=>2,
					],
					[
						'label'=>'Nama Pasien',
						'rowspan'=>2,
					],
					[
						'label'=>'Alamat',
						'rowspan'=>2,
					],
					[
						'label'=>'Jenis Kelamin',
						'rowspan'=>2,
					],
					[
						'label'=> 'Ruangan-Kamar',
						'rowspan'=>2,
					],
					[
						'label'=>'Jenis Kasus Penyakit',
						'rowspan'=>2,
					],
					[
						'label'=> 'Kelas Pelayanan / Kelas Tagihan',
						'rowspan'=>2,
					],
					[
						'label' => 'Status Kamar',
						'rowspan'=>2,
					],
					[
						'label'=>'Dokter Poliklinik',
						'rowspan'=>2,
					],
					[
						'label'=>'Cara Bayar',
						'rowspan'=>2,
					],
					[
						'label'=>'Penjamin',
						'rowspan'=>2,
					],
					[
						'label' => 'UNIT',
						'rowspan' => 2
	 				],
					[
						'label'=>'Status Periksa',
						'rowspan'=>2,
					],
					[
						'label'=>'No SEP',
						'rowspan'=>2,
					],
					[
						'label'=>'Limit Tagihan',
						'rowspan'=>2,
					],
					[
						'label'=>'Petugas',
						'rowspan'=>2,
					],
				]
			];
		}
		elseif($this->jenis == 'rajal') {
			$jenis_title = 'Rawat Jalan';
		}
		elseif($this->jenis == 'igd') {
			$jenis_title = 'Rawat Darurat';
		}
		elseif($this->jenis == 'mcu') {
			$jenis_title = 'MCU';
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
						'label'=>'No Pendaftaran',
						'rowspan'=>2,
					],
					[
						'label'=>'No Rekam Medik',
						'rowspan'=>2,
					],
					[
						'label'=>'Nama Pasien',
						'rowspan'=>2,
					],
					[
						'label'=>'Alamat',
						'rowspan'=>2,
					],
					[
						'label'=>'Jenis Kelamin',
						'rowspan'=>2,
					],
					[
						'label'=> 'Kelas Pelayanan',
						'rowspan'=>2,
					],
					[
						'label'=>'Dokter Poliklinik',
						'rowspan'=>2,
					],
					[
						'label'=>'Cara Bayar',
						'rowspan'=>2,
					],
					[
						'label'=>'Penjamin',
						'rowspan'=>2,
					],
					[
						'label' => 'UNIT',
						'rowspan' => 2
	 				],
					[
						'label'=>'Status Periksa',
						'rowspan'=>2,
					],
					[
						'label'=>'Limit Tagihan',
						'rowspan'=>2,
					],
					[
						'label'=>'Petugas',
						'rowspan'=>2,
					],
				]
			];
		}
		else {
			$jenis_title = 'Penunjang';
		}

		return [
			'header' => $custHeader,
			'title' => $jenis_title,
		];
	}
}
