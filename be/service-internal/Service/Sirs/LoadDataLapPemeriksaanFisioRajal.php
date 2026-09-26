<?php

namespace Integrasi\Service\Sirs;

use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Service\Sirs\Models\LaporanPemeriksaanPasienFisioterapiRj;

class LoadDataLapPemeriksaanFisioRajal extends DocoImplement
{
    public static function queryLoadData($filter)
    {
        $advancedFilterParams = ArrayHelper::getValue($filter, 'advanced-filter');
        $model = new LaporanPemeriksaanPasienFisioterapiRj();
        $query = $model::find();
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';
        if ($advanceFilter = $advancedFilterParams) {
            if (isset($advanceFilter['tgl_pendaftaran'])) {
                $tglPendaftaran = $advanceFilter['tgl_pendaftaran'];
                $tglPendaftaranRange = DocoHelpers::parsingRangeDate($tglPendaftaran);
                $startDate = $tglPendaftaranRange['startDate'];
                $endDate = $tglPendaftaranRange['endDate'];
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
            if (isset($advanceFilter['nama_pasien'])) {
                $query->andWhere(['or',
                    ['ILIKE', 'nama_pasien', $advanceFilter['nama_pasien']],
                    ['ILIKE', 'no_rekam_medik', $advanceFilter['nama_pasien']]
                ]);
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
            if (isset($advanceFilter['terapis_id'])) {
                if ($advanceFilter['terapis_id'] != 'Semua') {
                    $query->andWhere(['terapis_id' => $advanceFilter['terapis_id']]);
                }
                unset($filter['advanced-filter']['terapis_id']);
            }
            if (isset($advanceFilter['jenispemeriksaanfisio_id'])) {
                if ($advanceFilter['jenispemeriksaanfisio_id'] != 'Semua') {
                    $jenispemeriksaanfisio_id = $advanceFilter['jenispemeriksaanfisio_id'];
                    $query->andWhere(['jenispemeriksaanfisio_id' => $jenispemeriksaanfisio_id]);
                }
                unset($filter['advanced-filter']['jenispemeriksaanfisio_id']);
            }
            if (isset($advanceFilter['daftartindakan_id'])) {
                if ($advanceFilter['daftartindakan_id'] != 'Semua') {
                    $daftartindakan_id = $advanceFilter['daftartindakan_id'];
                    $query->andWhere(['daftartindakan_id' => $daftartindakan_id]);
                }
                unset($filter['advanced-filter']['daftartindakan_id']);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $startDate, $endDate]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query, $filter);
        return $query;
    }
}
