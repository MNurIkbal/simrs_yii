<?php

namespace Integrasi\Service\Sirs\Rm\LapKunjunganPenunjangExcel;

use Exception;
use Integrasi\Service\Sirs\Models\LaporanKunjunganPasienFisioterapiRj;
use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\InfoKunjunganPenunjang;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class LoadDataLapKunjunganPenunjangExcel extends \Integrasi\Contracts\DocoImplement
{
    const LOADING_VALUE = 'Loading ...';

    public static function queryLoadData($filter)
    {
        $advancedFilters = ArrayHelper::getValue($filter, 'advanced-filter');
        $model = new InfoKunjunganPenunjang();
        $query = $model::find();

        $startTgl = date('Y-m-d') . ' 00:00:00';
        $endTgl = date('Y-m-d') . ' 23:59:59';

        $tglFilter = ArrayHelper::getValue($advancedFilters, 'tglmasukpenunjang');
        $instalasiFilter = ArrayHelper::getValue($advancedFilters, 'instalasi_nama');
        $ruanganFilter = ArrayHelper::getValue($advancedFilters, 'ruangan_nama');
        $noPendaftaranFilter = ArrayHelper::getValue($advancedFilters, 'no_pendaftaran'); // Auto Filter with helpers
        $caraBayarFilter = ArrayHelper::getValue($advancedFilters, 'carabayar_id'); // Auto Filter with helpers
        $penjaminFilter = ArrayHelper::getValue($advancedFilters, 'penjamin_nama');
        $jenisKegiatanFilter = ArrayHelper::getValue($advancedFilters, 'jeniskegiatantindakan_nama');
        $daftarTindakanFilter = ArrayHelper::getValue($advancedFilters, 'daftartindakan_nama');
        $unitFilter = ArrayHelper::getValue($advancedFilters, 'unit');
        
        if ($tglFilter) {
            $explode = explode(" - ", $filter['advanced-filter']['tglmasukpenunjang']);
            if (count($explode) == 2) {
                $startTgl = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $endTgl = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
            unset($filter['advanced-filter']['tglmasukpenunjang']);
        }
        if ($instalasiFilter) {
            $filter['advanced-filter']['instalasi_id'] = $filter['advanced-filter']['instalasi_nama'];
            unset($filter['advanced-filter']['instalasi_nama']);
        }
        if ($ruanganFilter) {
            $filter['advanced-filter']['ruangan_id'] = $filter['advanced-filter']['ruangan_nama'];
            unset($filter['advanced-filter']['ruangan_nama']);
        }
        if ($penjaminFilter) {
            $filter['advanced-filter']['penjamin_id'] = $filter['advanced-filter']['penjamin_nama'];
            unset($filter['advanced-filter']['penjamin_nama']);
        }
        if ($jenisKegiatanFilter) {
            $jeniskegiatantindakan_nama = $filter['advanced-filter']['jeniskegiatantindakan_nama'];
            $query->andFilterWhere([
                'and',
                ['ilike', 'jeniskegiatantindakan_nama', $jeniskegiatantindakan_nama],
            ]);
            unset($filter['advanced-filter']['jeniskegiatantindakan_nama']);
        }
        if ($daftarTindakanFilter) {
            $daftartindakan_nama = $filter['advanced-filter']['daftartindakan_nama'];
            $query->andFilterWhere([
                'and',
                ['ilike', 'daftartindakan_nama', $daftartindakan_nama],
            ]);
            unset($filter['advanced-filter']['daftartindakan_nama']);
        }
        if ($unitFilter) {
            $query->andFilterWhere([
                'and',
                ['ilike', 'unit', $filter['advanced-filter']['unit']],
            ]);
            unset($filter['advanced-filter']['unit']);
        }

        $query->andWhere(['between', 'tglmasukpenunjang', $startTgl, $endTgl]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query, $filter);
        return $query;
    }
}
