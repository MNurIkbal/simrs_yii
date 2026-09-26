<?php

namespace Integrasi\Service\Sirs;

use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Service\Sirs\Models\LaporanPasienFisioterapiRj;

class LoadDataLapPasienFisioRajal extends DocoImplement
{
    public static function queryLoadData($filter)
    {
        $advancedFilterParams = ArrayHelper::getValue($filter, 'advanced-filter');
        $model = new LaporanPasienFisioterapiRj();
        $query = $model::find();
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';
        if ($advance_filter = $advancedFilterParams) {
            if (isset($advance_filter['nama_pasien'])) {
                $namaPasien = ArrayHelper::getValue($advance_filter, 'nama_pasien');
                $query->andWhere(['or', ['ILIKE',  'nama_pasien', $namaPasien], ['ILIKE', 'no_rekam_medik', $namaPasien]]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }
            if (isset($advance_filter['tgl_pendaftaran'])) {
                $tglPendaftaran = $advance_filter['tgl_pendaftaran'];
                $tglPendaftaranRange = DocoHelpers::parsingRangeDate($tglPendaftaran);
                $startDate = $tglPendaftaranRange['startDate'];
                $endDate = $tglPendaftaranRange['endDate'];
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }
            if (isset($advance_filter['dokterperujuk_id'])) {
                if ($advance_filter['dokterperujuk_id'] != 'Semua') {
                    $dokterPerujukId = ArrayHelper::getValue($advance_filter, 'dokterperujuk_id');
                    $query->andWhere(['dokterperujuk_id' => $dokterPerujukId]);
                }
                unset($_GET['advanced-filter']['dokterperujuk_id']);
            }
            if (isset($advance_filter['ruangan_id'])) {
                if ($advance_filter['ruangan_id'] != 'Semua') {
                    $ruangan_id = ArrayHelper::getValue($advance_filter, 'ruangan_id');
                    $query->andWhere(['ruangan_id' => $ruangan_id]);
                }
                unset($_GET['advanced-filter']['ruangan_id']);
            }
            if (isset($advance_filter['jenispemeriksaanfisio_id'])) {
                if ($advance_filter['jenispemeriksaanfisio_id'] != 'Semua') {
                    $jenisPemeriksaanId = ArrayHelper::getValue($advance_filter, 'jenispemeriksaanfisio_id');
                    $query->andWhere(['jenispemeriksaanfisio_id' => $jenisPemeriksaanId]);
                }
                unset($_GET['advanced-filter']['jenispemeriksaanfisio_id']);
            }
            if (isset($advance_filter['status_program_fisio_id'])) {
                if ($advance_filter['status_program_fisio_id'] != 'Semua') {
                    $statusProgramFisioId = ArrayHelper::getValue($advance_filter, 'status_program_fisio_id');
                    $query->andWhere(['status_program_fisio_id' => $statusProgramFisioId]);
                }
                unset($_GET['advanced-filter']['status_program_fisio_id']);
            }
            if (isset($advance_filter['terapis_id'])) {
                if ($advance_filter['terapis_id'] != 'Semua') {
                    $terapis_id = ArrayHelper::getValue($advance_filter, 'terapis_id');
                    $query->andWhere(['terapis_id' => $terapis_id]);
                }
                unset($_GET['advanced-filter']['terapis_id']);
            }
        };
        $query->andWhere(['between', 'tgl_pendaftaran', $startDate, $endDate]);
        $query->orderby(['tgl_pendaftaran' => SORT_DESC]);
        return $query;
    }
}
