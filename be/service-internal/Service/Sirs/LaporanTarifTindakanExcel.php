<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\MasterTarifTindakanView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LaporanTarifTindakanExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['jenis_tindakan_paket']) ? $value['jenis_tindakan_paket'] : '';
            $tmp[3]  = !empty($value['nama_tindakan_paket']) ? $value['nama_tindakan_paket'] : '';
            $tmp[4]  = !empty($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '';
            $tmp[5]  = !empty($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
            $tmp[6]  = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
            $tmp[7]  = !empty($value['perdanama_sk']) ? $value['perdanama_sk'] : '';
            $tmp[8]  = !empty($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : '';
            $tmp[9]  = !empty($value['persendiskon_tindakan']) ? $value['persendiskon_tindakan'] : '';
            $tmp[10]  = !empty($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : 0;
            $tmp[11]  = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';

            $tmpCache[] = $tmp;
            $no++;
        }

        $cacheFiles->set($this->unique_str . '-' . $prefix, $tmpCache);

        return json_encode([
            'service' => 'Sirs-LaporanTarifTindakanExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new MasterTarifTindakanView;
        $query = $model::find();
        $query->where(['komponentarif_id' => DocoConstants::KOMPONEN_TARIF]);

        if (isset($request['advanced-filter'])) {
            if (isset($request['advanced-filter']['is_active'])) {
                $query->andWhere(['is_active' =>  $request['advanced-filter']['is_active']]);
            }
        }
        $query->orderBy(['created_date' => SORT_DESC]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
