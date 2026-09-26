<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanPenjualanObatalkesView;
use Integrasi\Service\Sirs\Models\Pegawai;
use Integrasi\Service\Sirs\Models\LaporanObatExpired;
use Integrasi\Service\Sirs\Models\Lookup;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class LaporanObatAlkesExpired extends \Integrasi\Contracts\DocoImplement
{
	public function execute() {
		$data = $this->loadData()->asArray()->all();
		$cacheFiles = Yii::$app->cacheFiles;
		$row = $tmpCache = [];
		$no = 1;
		$prefix = 0;

		foreach ($data as $value) {
			$value['tglkadaluarsa'] = date('d-M-Y', strtotime($value['tglkadaluarsa']));
			$value['stok_display'] = DocoHelpers::formatNumber($value['stok_exp']) . ' '. $value['satuan_kecil'];
			$value['cost_wa_display'] = DocoHelpers::formatNumber($value['cost_wa']);

			$tmp[1] = $no;
			$tmp[2] = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '';
			$tmp[3] = !empty($value['tglkadaluarsa']) ? $value['tglkadaluarsa'] : '';
			$tmp[4] = !empty($value['stok_display']) ? $value['stok_display'] : '';
			$tmp[5] = !empty($value['instalasi_nama']) ? $value['instalasi_nama'] : '';
			$tmp[6] = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
			$tmp[7] = !empty($value['cost_wa_display']) ? $value['cost_wa'] : '0';

			$tmpCache[] = $tmp;
			if(($no % 50) == 0) {
				Yii::$app->redis->executeCommand('PUBLISH', [
					'channel' => 'export-excel:' . $this->unique_str,
					'message' => json_encode(['unique_process' => $this->unique_str])
				]);

				$cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
				$prefix++;
				$tmpCache = [];
			}

			$no++;
		}

		$cacheFiles->set($this->unique_str . '-' . $prefix, $tmpCache);

		return json_encode([
			'service' => 'Sirs-LaporanObatAlkesExpired',
			'payload' => $this->attributes,
			'timestamp' => date('Y-m-d H:i:s'),
			'response' => $this->unique_str
		]);
	}

	private function loadData() {
		$request = $this->filter;
		$model = new LaporanObatExpired;
		$query = $model::find();

		$expiry_range = "+1 month";

		$lookup_range_tanggal = Lookup::find()
		    ->where(["lookup_type" => "range_bulan"])
		    ->select("lookup_name, lookup_value, lookup_id")
		    ->asArray()->all();

		$range_date = ArrayHelper::map($lookup_range_tanggal, "lookup_id", "lookup_value");

		if(isset($request['advanced-filter'])) {
		    if(isset($request['advanced-filter']['tglkadaluarsa'])) {
		        $selected_range = $request['advanced-filter']['tglkadaluarsa'];
		        if (array_key_exists($selected_range, $range_date)) {
		            $expiry_range = $range_date[$selected_range];
		        }
		        unset($request['advanced-filter']['tglkadaluarsa']); // Unset Advanced Filter  date range
		    }

		    if(isset($request['advanced-filter']['obatalkes_nama'])) {
		        $query->andWhere(['ILIKE','obatalkes_nama', $request['advanced-filter']['obatalkes_nama']]);
		    }

		    if(isset($request['advanced-filter']['instalasi_id'])) {
		        $query->andWhere(['instalasi_id' => $request['advanced-filter']['instalasi_id']]);
		    }

		    if(isset($request['advanced-filter']['ruangan_id'])) {
		        $query->andWhere(['ruangan_id' => $request['advanced-filter']['ruangan_id']]);
		    }
		}

		$query->andWhere(['<=', 'tglkadaluarsa', date('Y-m-d', strtotime($expiry_range))]);
		$query->andWhere(['>', 'stok_exp', 0]);

		return DocoRestActiveFilter::advancedFilter($model, $query, $request);
	}
}