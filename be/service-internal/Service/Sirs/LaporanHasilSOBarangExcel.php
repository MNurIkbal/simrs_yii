<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LaporanHasilSoBarangView;
use Integrasi\Components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class LaporanHasilSOBarangExcel extends \Integrasi\Contracts\DocoImplement {
    public function execute() {
        $data = $this->getDataExcel()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $tmp[1]  = $no; 
            $tmp[2] = isset($value['tgl_form_so']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_form_so']))) : null;
            $tmp[3] = isset($value['tgl_validasi_so']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_validasi_so']))) : null;
            $tmp[4] = isset($value['tgl_implementasi']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_implementasi']))) : null;
            $tmp[5] = ArrayHelper::getValue($value, 'validasi_by');
            $tmp[6] = ArrayHelper::getValue($value, 'no_form_so');
            $tmp[7] = ArrayHelper::getValue($value, 'instalasi_ruangan');
            $tmp[8] = ArrayHelper::getValue($value, 'kelompokbarang_nama');
            $tmp[9] = ArrayHelper::getValue($value, 'subkelompok_nama');
            $tmp[10] = ArrayHelper::getValue($value, 'kode_barang');
            $tmp[11] = ArrayHelper::getValue($value, 'nama_barang');
            $tmp[12] = ArrayHelper::getValue($value, 'satuan_kecil');
            $tmp[13] = ArrayHelper::getValue($value, 'harganetto');
            $tmp[14] = ArrayHelper::getValue($value, 'stok_sistem');
            $tmp[15] = ArrayHelper::getValue($value, 'stok_fisik');
            $tmp[16] = ArrayHelper::getValue($value, 'selisih');
            $tmp[17] = ArrayHelper::getValue($value, 'stok_akhir');
            $tmp[18] = ArrayHelper::getValue($value, 'selisih_akhir');
            $tmp[19] = ArrayHelper::getValue($value, 'total_harga_netto');
            $tmp[20] = ArrayHelper::getValue($value, 'total_harga_selisih');
            $tmpCache[] = $tmp;
            if (($no%50) == 0) 
            {
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
            'service' => 'Sirs-LaporanHasilSOBarangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel() {
        $request = $this->filter;
        $model = new LaporanHasilSoBarangView;
        $query = $model::find(true);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if (isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgl_form_so'])){
                $explode = explode(" - ", $request['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_form_so']);
            }
        }

        $query->andWhere(['between', 'tgl_form_so', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}