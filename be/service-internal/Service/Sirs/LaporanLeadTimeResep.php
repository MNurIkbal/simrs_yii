<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\LaporanLeadTimeResepView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanLeadTimeResep extends \Integrasi\Contracts\DocoImplement {
    public function execute() {
        $data = $this->loadData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value)  {
            $tmp[1] = $no;
            $tmp[2] = !empty($value['ruangan']) ? $value['ruangan'] : '';
            $tmp[3] = !empty($value['tgl_resep']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_resep']))) : '';
            $tmp[4] = !empty($value['no_resep']) ? $value['no_resep'] : '';
            $tmp[5] = !empty($value['jenis_resep']) ? $value['jenis_resep'] : '';
            $tmp[6] = !empty($value['jumlah_r']) ? $value['jumlah_r'] : '';
            $tmp[7] = !empty($value['dokter']) ? $value['dokter'] : '';
            $tmp[8] = !empty($value['jumlah_item']) ? $value['jumlah_item'] : '';
            $tmp[9] = !empty($value['jam_resep_masuk']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['jam_resep_masuk']))) : '';
            $tmp[10] = !empty($value['jam_resep_dibayar']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['jam_resep_dibayar']))) : '';
            $tmp[11] = !empty($value['jam_production']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['jam_production']))) : '';
            $tmp[12] = !empty($value['jam_diserahkan']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['jam_diserahkan']))) : '';
            $tmp[13] = !empty($value['waktu_tunggu_format']) ? $value['waktu_tunggu_format'] : ''; //sudah diformat di view;

            $tmpCache[] = $tmp;
            if (($no%50) == 0)  {
                Yii::$app->redis->executeCommand('PUBLISH', [
                   'channel' => 'export-excel:'.$this->unique_str,
                   'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
        }
        $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);

        return json_encode([
            'service' => 'Sirs-LaporanLeadTimeResep',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData() {
        $request = $this->filter;
        $model = new LaporanLeadTimeResepView;
        $query = $model::find();
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($request['advance_filter']) && isset($request['advance_filter']['tgl_resep'])) {
            $explode = explode(" - ", $request['advance_filter']['tgl_resep']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
        }
        
        if(isset($request['advance_filter']['ruangan_id']) &&  $request['advance_filter']['ruangan_id']!='') {
            $term = $request['advance_filter']['ruangan_id'];
            $query->andWhere(['ruangan_id' => $term]);
        }

        $query->andWhere(['between', 'tgl_resep', $start, $end]);
        $query->orderBy(['tgl_resep' => SORT_DESC]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
