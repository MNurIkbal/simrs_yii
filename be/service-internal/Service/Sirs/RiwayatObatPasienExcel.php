<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\InfoRiwayatResep;
use Integrasi\Components\DocoRestActiveFilter;

class RiwayatObatPasienExcel extends \Integrasi\Contracts\DocoImplement
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
            $tmp[2]  = isset($value['tgl_transaksi']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_transaksi']))) : null;
            $tmp[3]  = !empty($value['no_resep']) ? $value['no_resep'] : '';
            $tmp[4]  = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '';
            $tmp[5]  = !empty($value['signa_nama']) ? $value['signa_nama'] : '';
            $tmp[6]  = !empty($value['qty']) ? $value['qty'] : '';
            $tmp[7]  = !empty($value['satuanunit_nama']) ? $value['satuanunit_nama'] : '';
            $tmp[8]  = $value['instalasi_nama']. " - " .$value['ruangan_nama'];
            $tmp[9] = $value['carabayar_nama']. " - " .$value['penjamin_nama'];

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
            'service' => 'Sirs-RiwayatObatPasienExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;

        $start = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $end = date('Y-m-d 23:59:59');
        $model = new InfoRiwayatResep;
        $query = $model::find();

        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if(isset($request['advanced-filter']['tgl_transaksi'])){
                $explode = explode(" - ", $advancedFilter['tgl_transaksi']);
                if (count($explode) == 2) {
                  $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                  $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_transaksi']);
            }
        }
        $query->andWhere(['between', 'tgl_transaksi', $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model,$query,$request);
    }
}