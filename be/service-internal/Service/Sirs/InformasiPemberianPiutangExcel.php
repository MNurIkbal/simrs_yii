<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\InfoPemberianPiutangView;
use Integrasi\Components\DocoRestActiveFilter;

class InformasiPemberianPiutangExcel extends \Integrasi\Contracts\DocoImplement
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
            $tmp[2]  = !empty($value['tgl_pemberianpiutang']) ? date('d-M-Y', strtotime($value['tgl_pemberianpiutang'])) : '';
            $tmp[3]  = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '';
            $tmp[4]  = !empty($value['nama_pasien']) ? $value['pegawaidibebankan_nip'] . '-' . $value['pegawaidibebankan_nama'] : '';
            $tmp[5]  = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
            $tmp[6]  = !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
            $tmp[7]  = !empty($value['tgl_pendaftaran']) ? date('d-M-Y H:i', strtotime($value['tgl_pendaftaran'])) : '';
            $tmp[8]  = !empty($value['tglpasienpulang']) ? date('d-M-Y H:i', strtotime($value['tglpasienpulang'])) : '';
            $tmp[9]  = !empty($value['total_piutang']) ? $value['total_piutang'] : 0;
            $tmp[10]  = !empty($value['total_bayarpiutang']) ? $value['total_bayarpiutang'] : 0;
            $tmp[11]  = !empty($value['total_sisapiutang']) ? $value['total_sisapiutang'] : 0;
            $tmp[12] = !empty($value['status_piutang_nama']) ? $value['status_piutang_nama'] : '';
            $tmpCache[] = $tmp;
            $no++;
        }

        $cacheFiles->set($this->unique_str . '-' . $prefix, $tmpCache);
        return json_encode([
            'service' => 'Sirs-InformasiPemberianPiutangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new InfoPemberianPiutangView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00', strtotime('-1 months'));
        $end = date('Y-m-d 23:59:00');

        if (isset($request['advanced-filter'])) {
            if (isset($request['advanced-filter']['tgl_pemberianpiutang'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pemberianpiutang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_pemberianpiutang']);
            }
            
            if(isset($request['advanced-filter']['tglpasienpulang'])) {
                $explode = explode(" - ", $request['advanced-filter']['tglpasienpulang']);
                if(count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tglpasienpulang', $outStart, $outEnd]);
                unset($_GET['advanced-filter']['tglpasienpulang']);
            }

            if (isset($request['advanced-filter']['pegawaidibebankan_id'])) {
                $pegawaidibebankan_id = $request['advanced-filter']['pegawaidibebankan_id'];
                $query->andWhere(['pegawaidibebankan_id' => $pegawaidibebankan_id]);
                unset($_GET['advanced-filter']['pegawaidibebankan_id']);
            }

            if (isset($request['advanced-filter']['status_piutang'])) {
                $status_piutang = $request['advanced-filter']['status_piutang'];
                $query->andWhere(['status_piutang' => $status_piutang]);
                unset($_GET['advanced-filter']['status_piutang']);
            }
        }

        $query->andWhere(['between', 'tgl_pemberianpiutang', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
