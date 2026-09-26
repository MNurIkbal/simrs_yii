<?php

namespace Integrasi\Service\Sirs\LapPasienFisioterapiRanapExcel;

use Integrasi\Service\Sirs\Models\LaporanKunjunganPasienFisioterapiRj;
use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\PemeriksaanPasienRadiologiView;
use Integrasi\Service\Sirs\Models\LaporanPasienFisioterapiRanapV;
use Integrasi\Service\Sirs\Models\LaporanKunjunganPasienFisioterapiRi;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class LoadDataLapPasienFisioterapiRanapExcel extends \Integrasi\Contracts\DocoImplement
{
    public static function queryLoadData($filter)
    {
        $advancedFilters = ArrayHelper::getValue($filter, 'advanced-filter');
        $model = new LaporanPasienFisioterapiRanapV();
        $query = $model::find();
        $startTglPendaftaran = date('Y-m-d') . ' 00:00:00';
        $endTglPendaftaran = date('Y-m-d') . ' 23:59:59';
        $tglPendaftaran = ArrayHelper::getValue($advancedFilters, 'tgl_pendaftaran');
        $dataPasien = ArrayHelper::getValue($advancedFilters, 'nama_pasien');
        $dokterPerujukId = ArrayHelper::getValue($advancedFilters, 'dokterperujuk_id');
        $dokterDpjpId = ArrayHelper::getValue($advancedFilters, 'dokterdpjp_id');
        $statusProgramFisioId = ArrayHelper::getValue($advancedFilters, 'status_program_fisio_id');
        $terapisId = ArrayHelper::getValue($advancedFilters, 'terapis_id');
        if ($tglPendaftaran) {
            $explode = explode(" - ", $tglPendaftaran);
            if (count($explode) == 2) {
                $startTglPendaftaran = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $endTglPendaftaran = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
            unset($_GET['advanced-filter']['tgl_pendaftaran']);
        }
        if ($dataPasien) {
            $query->andWhere([
                'or',
                ['ILIKE', 'nama_pasien', $dataPasien],
                ['ILIKE', 'no_rekam_medik', $dataPasien],
            ]);
            unset($_GET['advanced-filter']['nama_pasien']);
        }
        if ($dokterPerujukId) {
            $query->andWhere(['=', 'dokterperujuk_id', $dokterPerujukId]);
            unset($_GET['advanced-filter']['dokterperujuk_id']);
        }
        if ($dokterDpjpId) {
            $query->andWhere(['=', 'dokterdpjp_id', $dokterDpjpId]);
            unset($_GET['advanced-filter']['dokterdpjp_id']);
        }
        if ($statusProgramFisioId) {
            $query->andWhere(['=', 'status_program_fisio_id', $statusProgramFisioId]);
            unset($_GET['advanced-filter']['status_program_fisio_id']);
        }
        if ($terapisId) {
            $query->andWhere(['=', 'terapis_id', $terapisId]);
            unset($_GET['advanced-filter']['terapis_id']);
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $startTglPendaftaran, $endTglPendaftaran]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query, $filter);
        return $query;
    }
}
