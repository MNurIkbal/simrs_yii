<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\CaraBayar;
use Integrasi\Service\Sirs\Models\Lookup;
use yii\helpers\ArrayHelper;

class ExportExcelLaporanObatAlkesExpired extends \Integrasi\Contracts\DocoImplement
{
	public function execute() {
		ini_set('memory_limit', '-1');
		$totalPerPage = $this->totalPerPage;
		$cacheFiles = Yii::$app->cacheFiles;
		$filter = $this->filter;
		$row = $footer = [];
		$no = 1;
		$expiry_range = "+1 month";
		$expiry_name = "1 Bulan";

		$nama_obatalkes = '-';

		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish',
				'messageProcess' => 'Sedang mengekstrak data Obatalkes Expired',
				'progress' => 80 
			]),
		]);

		for($i = 0; $i < $totalPerPage; $i++) {
			$data = $cacheFiles->get($this->unique_str . '-' . $i);

			if(!empty($data)) {
				foreach ($data as $key => $value) {
					$row[] = $value;
				}
			}

			$cacheFiles->delete($this->unique_str . '-' . $i);
		}

		$lookup_range_tanggal = Lookup::find()
		    ->where(["lookup_type" => "range_bulan"])
		    ->select("lookup_name, lookup_value, lookup_id")
		    ->asArray()->all();

		$range_date = ArrayHelper::map($lookup_range_tanggal, "lookup_id", "lookup_value");

		if(isset($filter['advanced-filter'])) {
		    if(isset($filter['advanced-filter']['tglkadaluarsa'])) {
		        $selected_range = $filter['advanced-filter']['tglkadaluarsa'];
		        if (array_key_exists($selected_range, $range_date)) {
		            $expiry_range = $range_date[$selected_range];
		            $expiry_name = Lookup::find()
		                ->where(["lookup_value" => $expiry_range])
		                ->select("lookup_name")->one()->lookup_name;
		        }
		        unset($filter['advanced-filter']['tglkadaluarsa']); // Unset Advanced Filter  date range
		    }

		    if(isset($filter['advanced-filter']['obatalkes_nama'])) {
		    	$nama_obatalkes = $filter['advanced-filter']['obatalkes_nama'];
		    }

		    if(isset($filter['advanced-filter']['instalasi_id'])) {
		        //$query->andWhere(['instalasi_id' => $filter['advanced-filter']['instalasi_id']]);
		    }

		    if(isset($filter['advanced-filter']['ruangan_id'])) {
		        //$query->andWhere(['ruangan_id' => $filter['advanced-filter']['ruangan_id']]);
		    }
		}

		$header = [
			'Tanggal Unduh' => date('d-M-Y H:i:s'),
			'Masa Kadaluarsa' => $expiry_name,
			'Nama Obat Alkes' => $nama_obatalkes
		];

		$custheader = $this->custHeader();
		$customFormatCode = $this->customFormatCode();
		$path = 'uploads/'.$this->unique_str.'.xlsx';

		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish',
				'messageProcess' => 'Sedang mengimport data ke dalam excel',
				'progress' => 85
			])
		]);

		$filePath = DocoHelpers::exportExcel('Laporan Obatalkes Expired', $row, $header, [
			'skipIncrement' => true,
			'customHeader' => $custheader,
			'customFormatCode' => $customFormatCode
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
			'service' => 'Sirs-ExportExcelLaporanObatAlkesExpired',
			'payload' => $this->attributes,
			'timestamp' => date('Y-m-d H:i:s')
		]);
	}

	private function custHeader() {
		return [
			[
				[
					'label' => 'No',
					'rowspan' => 2
				],
				[
					'label' => 'Nama Obat Alkes',
					'rowspan' => 2
				],
				[
					'label' => 'Tanggal Expired',
					'rowspan' => 2
				],
				[
					'label' => 'Qty',
					'rowspan' => 2
				],
				[
					'label' => 'Instalasi',
					'rowspan' => 2
				],
				[
					'label' => 'Ruangan',
					'rowspan' => 2
				],
				[
					'label' => 'Total Cost (WA)',
					'rowspan' => 2
				]
			]
		];
	}

	private function customFormatCode() {
		return [
			['selectColumn' => 'B', 'general'],
			['selectColumn' => 'C', 'date'],
			['selectColumn' => 'D', 'general'],
			['selectColumn' => 'E', 'general'],
			['selectColumn' => 'F', 'general'],
			['selectColumn' => 'G', 'number']
		];
	}
}