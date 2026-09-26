<?php
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoPrint;
use Integrasi\Components\DocoHelpers;

class CetakLaporanWaktuTungguLab extends \Integrasi\Contracts\DocoImplement
{
   	public function execute()
	{
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		$cacheFiles = Yii::$app->cacheFiles;
		$filter = $this->filter;
		$tipe = ArrayHelper::getValue($filter, 'tipe', 1);
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$path = $dir.'/'.$rootPath.'/'.$this->unique_str;
		if($tipe == 1) {
			return $this->generateExcel($cacheFiles, $path);
		}
		else {
			return $this->generatePdf($cacheFiles, $path);
		}
	}

   private function generateExcel($cacheFiles, $path)
   {
		$totalPerPage = $this->totalPerPage;
		$filter = $this->filter;
		$advancedFilter = isset($filter['advanced-filter']) ? $filter['advanced-filter'] : [];
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

		$startDirujuk = $endDirujuk = date('Y-m-d');
		$startPersetujuan = $endPersetujuan = '';
		$noPendaftaran = isset($advancedFilter['no_pendaftaran']) ? $advancedFilter['no_pendaftaran'] : '-';
		$namaPasien = isset($advancedFilter['nama_pasien']) ? $advancedFilter['nama_pasien'] : '-';
		$jenisPemeriksaan = isset($advancedFilter['pemeriksaanlab_nama']) ? $advancedFilter['pemeriksaanlab_nama'] : '-';
		$asalRujukan = isset($advancedFilter['asal_rujukan']) ? $advancedFilter['asal_rujukan'] : '-';
		$rsRujukan = isset($advancedFilter['rs_rurjukan']) ? $advancedFilter['rs_rurjukan'] : '-';
		if(!empty($advancedFilter)) {
			if(isset($advancedFilter['tgl_dirujuk'])) {
                $tgl_dirujuk = $advancedFilter['tgl_dirujuk'];
                $explode = explode(" - ", $tgl_dirujuk);
                if(count($explode) == 2) {
                    $startDirujuk = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endDirujuk = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }

			if(isset($advancedFilter['tglmasukpenunjang'])) {
                $tglPersetujuan = $advancedFilter['tglmasukpenunjang'];
                $explode = explode(" - ", $tglPersetujuan);
                if(count($explode) == 2) {
                    $startPersetujuan = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endPersetujuan = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }
		}
		$header = array(
			"Tanggal Rujukan" => date('d M Y', strtotime($startDirujuk)).' Sampai Dengan '.date('d M Y', strtotime($endDirujuk)),
			"No Pendaftaran" => $noPendaftaran,
			"No RM / Nama Pasien" => $namaPasien,
			"Tanggal Persetujuan" => !empty($startPersetujuan) && !empty($endPersetujuan) ? ((date('d M Y', strtotime($startPersetujuan))." - ".date('d M Y', strtotime($endPersetujuan)))) : '-',
			"Jenis Pemeriksaan" => $jenisPemeriksaan,
			"Asal Rujukan" => $asalRujukan,
			"Nama RS Rujukan" => $rsRujukan
		);

		$custHeader = $this->getColumns();
		$title = $this->title;
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
			'service' => 'Sirs-CetakLaporanWaktuTungguLab',
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
		} else {
			$print = new DocoPrint('lap-waktu-tunggu-pasien-lab');
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
			'service' => 'Sirs-CetakLaporanWaktuTungguLab',
			'timestamp' => date('Y-m-d H:i:s'),
		]);
   }

   private function getColumns()
   {
      $column = [
        [
            [
               'label' => 'No',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tanggal Rujukan',
               'rowspan' => 2,
            ],
            [
               'label' => 'No Pendaftaran',
               'rowspan' => 2,
            ],
            [
               'label' => 'Nama Pasien',
               'rowspan' => 2,
            ],
			[
				'label' => 'No Rekam Medik',
				'rowspan' => 2,
			],
			[
				'label' => 'Tanggal Lahir',
				'rowspan' => 2,
			],
            [
               'label' => 'Dokter',
               'rowspan' => 2,
            ],
            [
               'label' => 'Specimen',
               'rowspan' => 2,
            ],
            [
               'label' => 'Pemeriksaan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jenis Rujukan',
               'rowspan' => 2,
            ],
			[
				'label' => 'Asal Rujukan',
				'rowspan' => 2,
			],
			[
				'label' => 'Nama RS',
				'rowspan' => 2,
			],
            [
               'label' => 'Tanggal Pendaftaran',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tanggal Persetujuan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tanggal Specimen',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tanggal Hasil Pemeriksaan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tanggal Expertise',
               'rowspan' => 2,
            ],
			[
				'label' => 'Waktu Tunggu (Specimen - Expertise)',
				'rowspan' => 2,
			 ],
			 [
				'label' => 'Waktu Tunggu (Pendaftaran - Expertise)',
				'rowspan' => 2,
			 ],
        ]
      ];
      return $column;
   }
}
