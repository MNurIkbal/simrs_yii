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

class ReservasiPoliklinikDataPasienExport extends \Integrasi\Contracts\DocoImplement
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

		$startKunjungan = $endKunjungan = date('Y-m-d');

		if(!empty($advancedFilters)) {
			if (isset($advancedFilters['tgl_kunjungan'])) {
				$tglKunjungan = $advancedFilters['tgl_kunjungan'];
				$explode = explode(" - ", $tglKunjungan);
				if(count($explode) == 2) {
					$startKunjungan = date('Y-m-d 00:00:00', strtotime($explode[0]));
					$endKunjungan = date('Y-m-d 23:59:59', strtotime($explode[1]));
				}
			}
		}

		$header = array(
			"Tanggal Kunjungan" => date('d M Y', strtotime($startKunjungan)).' Sampai Dengan '.date('d M Y', strtotime($endKunjungan)),
		);

		$custHeader = $this->getColumns($this->columns);
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
			'service' => 'Sirs-ReservasiPoliklinikDataPasienExport',
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
				$print = new DocoPrint('lap-batal-lab');
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
				'service' => 'Sirs-ReservasiPoliklinikDataPasienExport',
				'timestamp' => date('Y-m-d H:i:s'),
			]);
	}

   private function getColumns($columns)
   {
		$temp = [];
		$result = [];

		$temp[] = [
			'label' => 'No',
			'rowspan' => 2,
		];

		foreach (json_decode('['.$columns.']') as $column) {
			$temp[] = [
				'label' => $column->title,
				'rowspan' => 2,
			];
		}

		$result[] = $temp;

      	return $result;
   }
}
