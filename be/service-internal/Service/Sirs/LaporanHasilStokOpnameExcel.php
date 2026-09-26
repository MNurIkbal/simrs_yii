<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LaporanHasilSoView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanHasilStokOpnameExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->getDataExcel()->asArray()->all();
        
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $tmp[1]  = $no;
            $tmp[2]  = isset($value['tgl_form_so']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_form_so']))) : '-';
            $tmp[3]  = isset($value['tgl_validasi_so']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_validasi_so']))) : '-';
            $tmp[4]  = isset($value['tgl_implementasi']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_implementasi']))) : '-';
            $tmp[5]  = !empty($value['validasi_by']) ? $value['validasi_by'] : '-';
            $tmp[6]  = !empty($value['no_form_so']) ? $value['no_form_so'] : '-';
            $tmp[7]  = !empty($value['instalasi_ruangan']) ? $value['instalasi_ruangan'] : '-';
            $tmp[8]  = !empty($value['jenis_obatalkes']) ? $value['jenis_obatalkes'] : '-';
            $tmp[9]  = !empty($value['kode_obat']) ? $value['kode_obat'] : '-';
            $tmp[10]  = !empty($value['nama_obat']) ? $value['nama_obat'] : '-';
            $tmp[11]  = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '-';
            $tmp[12]  = !empty($value['weighted_avg']) ? $value['weighted_avg'] : '0';
            $tmp[13]  = !empty($value['stok_sistem']) ? $value['stok_sistem'] : '0';
            $tmp[14]  = !empty($value['stok_fisik']) ? $value['stok_fisik'] : '0';
            $tmp[15]  = !empty($value['selisih']) ? $value['selisih'] : '0';
            $tmp[16]  = !empty($value['total_harga_sistem']) ? $value['total_harga_sistem'] : '0';
            $tmp[17]  = !empty($value['weighted_avg'] * $value['stok_fisik']) ? $value['weighted_avg'] * $value['stok_fisik'] : '0';
            $tmp[18]  = !empty($value['total_harga_selisi']) ? $value['total_harga_selisi'] : '0';

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
            'service' => 'Sirs-LaporanHasilStokOpnameExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;

        $date = date('Y-m-d');
        $model = new LaporanHasilSoView;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_form_so']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($request['advanced-filter']['no_form_so'])){
                $form = $request['advanced-filter']['no_form_so'];
                $query->andWhere(['ILIKE', 'no_form_so', $form]);
            }

            if(isset($request['advanced-filter']['instalasi_ruangan'])) {
                $ruangan_id = $request['advanced-filter']['instalasi_ruangan'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($request['advanced-filter']['instalasi_ruangan']);
            }
        }
        $query->andWhere(['between', 'tgl_form_so', $start, $end]);
    
        return DocoRestActiveFilter::advancedFilter($model,$query,$request);
    }
}