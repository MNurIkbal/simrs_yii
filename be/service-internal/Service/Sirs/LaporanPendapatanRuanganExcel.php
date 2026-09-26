<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanPendapatanRuanganView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use Doco\components\DocoConstansId;
use Mpdf\Tag\P;
use yii\helpers\ArrayHelper;

class LaporanPendapatanRuanganExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        // $data = $this->data;
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tmpCache[] = $value;
            $no++;
        }

        $cacheFiles->set($this->unique_str . '-' . $prefix, $tmpCache);
        return json_encode([
            'service' => 'Sirs-LaporanPendapatanRuanganExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new LaporanPendapatanRuanganView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $instalasiKasir = DocoConstansId::actionGetId('instalasi_ruangan');
        $advancedFilter = ArrayHelper::getValue($request,'advanced-filter');
        $instalasiId = ArrayHelper::getValue($request, 'instalasi_id');
        $ruanganId = ArrayHelper::getValue($request, 'ruangan_id');
        if ($advancedFilter) {
            $advancedFilter = $request['advanced-filter'];
            if (!empty($advancedFilter['tgl_pendaftaran'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_pendaftaran']);
            }
            if (isset($advancedFilter['instalasi_ruangan'])) {
                $instalasi_ruangan = $advancedFilter['instalasi_ruangan'];
                $query->andWhere(['ruangan_id' => $instalasi_ruangan]);
                unset($advancedFilter['instalasi_ruangan']);
            }
        }
        if ($instalasiId == $instalasiKasir) {
            $query->andWhere(['ruangan_id' => $ruanganId]);
        }else {
            $query->andWhere(['instalasi_id' => $instalasiId]);
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
