<?php

namespace Integrasi\Service\Sirs;

use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Service\Sirs\Models\LaporanDropOutPasienFisio;

class LoadDataLapDropOutPasienFisio extends DocoImplement
{
    public static function queryLoadData($filter)
    {
        $advancedFilterParams = ArrayHelper::getValue($filter, 'advanced-filter');
        $model = new LaporanDropOutPasienFisio;
        $query = $model::find();
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';
        
        if ($advanceFilter = $advancedFilterParams) {
            if (isset($advanceFilter['tgl_rujukan'])) {
                $tglRujukan = $advanceFilter['tgl_rujukan'];
                $tglRujukanRange = DocoHelpers::parsingRangeDate($tglRujukan);
                $startDate = $tglRujukanRange['startDate'];
                $endDate = $tglRujukanRange['endDate'];
                unset($filter['advanced-filter']['tgl_rujukan']);
            }
            if (isset($advanceFilter['nama_pasien'])) {
                $namaPasien = $advanceFilter['nama_pasien'];
                $query->andWhere(['or', ['ILIKE',  'nama_pasien', $namaPasien], ['ILIKE', 'no_rekam_medik', $namaPasien]]);
                unset($filter['advanced-filter']['nama_pasien']);
            }
            if (isset($advanceFilter['status_program_fisio_id'])) {
                if ($advanceFilter['status_program_fisio_id'] != 'Semua') {
                    $status_program_fisio_id = $advanceFilter['status_program_fisio_id'];
                    $query->andWhere(['status_program_fisio_id' => $status_program_fisio_id]);
                }
                unset($filter['advanced-filter']['status_program_fisio_id']);
            }
        }

        $query->andWhere(['between', 'tgl_rujukan', $startDate, $endDate]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query, $filter);
        return $query;
    }
}
