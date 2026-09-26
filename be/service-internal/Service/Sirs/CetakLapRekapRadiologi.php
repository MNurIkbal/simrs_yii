<?php
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use SirsCore\models\DaftarTindakan;
use Doco\models\KelasPelayanan;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoPrint;
use Integrasi\Components\DocoHelpers;

class CetakLapRekapRadiologi extends \Integrasi\Contracts\DocoImplement
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
      $advancedFilters = ArrayHelper::getValue($filter, 'advanced-filter', []);
		$row = $footer = [];
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

      $start = $end = date('Y-m-d');
		$jenisPemeriksaan = ArrayHelper::getValue($advancedFilters, 'daftartindakan_id');
		$kelasPelayanan = ArrayHelper::getValue($advancedFilters, 'kelaspelayanan_id');
      $tipeProsedur = ArrayHelper::getValue($advancedFilters, 'tipe_prosedur');
		if(!empty($advancedFilters)) {
         if (isset($advancedFilters['tgl_persetujuan']) && !empty($advancedFilters['tgl_persetujuan'])) {
				$tgl = ArrayHelper::getValue($advancedFilters, 'tgl_persetujuan');
				$rangeDate = DocoHelpers::parsingRangeDate($tgl);
         	$start = ArrayHelper::getValue($rangeDate, 'startDate');
         	$end = ArrayHelper::getValue($rangeDate, 'endDate');
			}
			if(!empty($jenisPemeriksaan)) {
            $data = DaftarTindakan::findOne($jenisPemeriksaan);
				$jenisPemeriksaan = ArrayHelper::getValue($data, 'daftartindakan_nama');
         }
			if(!empty($kelasPelayanan)) {
            $data = KelasPelayanan::findOne($kelasPelayanan);
				$kelasPelayanan = ArrayHelper::getValue($data, 'kelaspelayanan_nama');
         }
      }
      $header = array(
         "Tanggal Persetujuan" => date('d M Y', strtotime($start)).' Sampai Dengan '.date('d M Y', strtotime($end)),
         "Kelas Pelayanan" => $kelasPelayanan,
			"Nama Pemeriksaan" => $jenisPemeriksaan,
         "Tipe Prosedur" => $tipeProsedur,
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
			'service' => 'Sirs-CetakLapRekapRadiologi',
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
		$attributes = ArrayHelper::getValue($data, 'attributes', []);
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
			$print = new DocoPrint('lap-rekap-rad');
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
			'service' => 'Sirs-CetakLapRekapRadiologi',
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
               'label' => 'Tanggal Persetujuan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Kelas Pelayanan',
               'rowspan' => 2,
            ],
				[
               'label' => 'Pemeriksaan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tipe Prosedur',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jumlah Pemeriksaan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Harga Total',
               'rowspan' => 2,
            ],
         ]
      ];
      return $column;
   }
}
