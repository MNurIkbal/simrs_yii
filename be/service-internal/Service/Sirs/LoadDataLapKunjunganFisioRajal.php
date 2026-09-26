<?php

namespace Integrasi\Service\Sirs;

use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Service\Sirs\Models\LaporanKunjunganPasienFisioterapiRj;

class LoadDataLapKunjunganFisioRajal extends \Integrasi\Contracts\DocoImplement
{
    public static function queryLoadData($filter)
    {
        $advancedFilterParams = ArrayHelper::getValue($filter, 'advanced-filter');
        $model = new LaporanKunjunganPasienFisioterapiRj();
        $query = $model::find();
        $startDateLahir = null;
        $endDateLahir = null;
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';
        if ($advanceFilter = $advancedFilterParams) {
            if (isset($advanceFilter['tgl_pendaftaran'])) {
                $tglPendaftaran      = $advanceFilter['tgl_pendaftaran'];
                $tglPendaftaranRange = DocoHelpers::parsingRangeDate($tglPendaftaran);
                $startDate           = $tglPendaftaranRange['startDate'];
                $endDate             = $tglPendaftaranRange['endDate'];
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
            if (isset($advanceFilter['tanggal_lahir'])) {
                $tanggal_lahir      = $advanceFilter['tanggal_lahir'];
                $tglPendaftaranRangeLahir = DocoHelpers::parsingRangeDate($tanggal_lahir);
                $startDateLahir           = $tglPendaftaranRangeLahir['startDate'];
                $endDateLahir             = $tglPendaftaranRangeLahir['endDate'];
                unset($filter['advanced-filter']['tanggal_lahir']);
            }
            if (isset($advanceFilter['nama_pasien'])) {
                $query->andWhere(['or', ['ILIKE',  'nama_pasien', $advanceFilter['nama_pasien']], ['ILIKE', 'no_rekam_medik', $advanceFilter['nama_pasien']]]);
                unset($filter['advanced-filter']['nama_pasien']);
            }
            if (isset($advanceFilter['carabayar_id'])) {
                if ($advanceFilter['carabayar_id'] != 'Semua') {
                    $query->andWhere(['carabayar_id' => $advanceFilter['carabayar_id']]);
                }
                unset($filter['advanced-filter']['carabayar_id']);
            }
            if (isset($advanceFilter['penjamin_id'])) {
                if ($advanceFilter['penjamin_id'] != 'Semua') {
                    $query->andWhere(['penjamin_id' => $advanceFilter['penjamin_id']]);
                }
                unset($filter['advanced-filter']['penjamin_id']);
            }
            if (isset($advanceFilter['instalasi_id'])) {
                if ($advanceFilter['instalasi_id'] != 'Semua') {
                    $instalasi_id = $advanceFilter['instalasi_id'];
                    $query->andWhere(['instalasi_id' => $instalasi_id]);
                }
                unset($filter['advanced-filter']['instalasi_id']);
            }
            if (isset($advanceFilter['ruangan_id'])) {
                if ($advanceFilter['ruangan_id'] != 'Semua') {
                    $ruangan_id = $advanceFilter['ruangan_id'];
                    $query->andWhere(['ruangan_id' => $ruangan_id]);
                }
                unset($filter['advanced-filter']['ruangan_id']);
            }
            if (isset($advanceFilter['dokterdpjp_id'])) {
                if ($advanceFilter['dokterdpjp_id'] != 'Semua') {
                    $query->andWhere(['dokterdpjp_id' => $advanceFilter['dokterdpjp_id']]);
                }
                unset($filter['advanced-filter']['dokterdpjp_id']);
            }
            if (isset($advanceFilter['status_periksa_id'])) {
                if ($advanceFilter['status_periksa_id'] != 'Semua') {
                    $status_periksa_id = $advanceFilter['status_periksa_id'];
                    $query->andWhere(['status_periksa_id' => $status_periksa_id]);
                }
                unset($filter['advanced-filter']['status_periksa_id']);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $startDate, $endDate]);
        if (isset($advanceFilter['tanggal_lahir'])) {
            $query->andWhere(['between', 'tanggal_lahir', $startDateLahir, $endDateLahir]);
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query, $filter);
        return $query;
    }
}
