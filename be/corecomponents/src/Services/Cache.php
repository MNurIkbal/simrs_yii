<?php

namespace Doco\Services;

use app\modules\v1\models\PerujukView;
use Yii;
use Doco\models\Lookup;
use Doco\models\CaraBayar;
use Doco\models\Ruangan;
use Doco\components\DocoConstants;
use app\modules\v1\models\ProfilRumahSakit;
use Doco\models\ProfilRsView;
use app\modules\v1\models\KelasPelayanan;
use Doco\models\KonfigSystem;
use Doco\models\LookupTransaksi;
use Doco\components\constans\LookupConstans;
use Doco\models\KonfigLaporan;
use app\modules\v1\models\LookupKeperawatan;
use Doco\models\Penjamin;


class Cache
{

    public static function getLookupByType($type)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE .'-'. $type, function ($cache) use ($type) {
            return Lookup::find()->andWhere([
                'lookup_type' => $type,
                'is_active' => true,
            ])->asArray()->all();
        });
    }

    public static function getKonfigSystem()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S, function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        });
    }

    public static function getProfileRumahSakitById($id)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::GET_PROFILE_RS .'-'. $id, function ($cache) use ($id) {
            $profileRS = ProfilRumahSakit::find()->where(['profilrs_id' => $id])->one();
            return $profileRS;
        });
    }

    public static function getCaraBayar($duration = 3600)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_CB, function ($cache) {
            return CaraBayar::find()->where([
                'is_active' => true
            ])->asArray()->all();
        });
    }
    
    /**
     * function getProfileRs
     * 
     * param untuk mengambil data profile rs
     * @param Boolean $returnOne = true/false => jika returnOne di set true, maka akan langsung menghasilkan data tanpa key array lanjutan
     * 
     */
    public static function getProfileRs($returnOne = false)
    {
        $profileRs = Yii::$app->cache->getOrSet(DocoConstants::GET_PROFILE_RS_VIEW, function(){
            return ProfilRsView::find()->asArray()->all();
        });

        return $returnOne ? $profileRs[0] : $profileRs;
    }

    public static function getListRuangan()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_RUANGAN,function ($cache) {
                return Ruangan::find()->select([
                    'ruangan_id',
                    'instalasi_id',
                    'ruangan_nama'
                ])->where(['is_active' => true, 'is_deleted' => false])->asArray()->all();
            }
        );
    }

    public static function getKonfigSistem()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        });
    }

    public static function getRujukanDari()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_PERUJUK, function ($cache) {
            return PerujukView::find()->where([
                'perujuk_id',
                'asalrujukan_id',
                'namaperujuk'
            ])->where(['is_active' => true])->asArray()->all();
        });
    }

    public static function lookupTransaksi()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_LOOKUP_TRANSAKSI, function ($cache) {
            return LookupTransaksi::find()
                ->asArray()
                ->all();
        });
    }

    public static function lookTeleRoom()
    {
        $result = '';
        $key = 'ruangan_telekonsultasi';

        return Yii::$app->cache->getOrSet('MHG-' . $key , function ($cache) use($key, $result) {
            $data = self::lookupTransaksi();
            if(!empty($data)) {
                $results = array_filter($data, function($v, $k) use ($key) {
                    return $v['kode_transaksi'] == $key;
                }, ARRAY_FILTER_USE_BOTH);
            }

            if(isset($results)) {
                foreach($results as $k => $v) {
                        $result = (object) $v;
                }
            }

            return $result;
        }); 
    }

    public static function getKelasPelayanan()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_C_KP, function ($cache) {
            return KelasPelayanan::find()->select([
                'kelaspelayanan_id',
                'kelaspelayanan_nama'
            ])->where(['is_active' => true])->asArray()->all();
        });
    }
    
    public static function getStatusPeriksaRanap()
    {
        return Yii::$app->cache->getOrSet(LookupConstans::STATUS_PERIKSA_RANAP , function ($cache) {
            return Lookup::find()->select(['lookup_id', 'lookup_type', 'lookup_name', 'lookup_value'])->where(['lookup_type' => 'status_ranap'])->asArray()->all();
        });
    }
    
    public static function getStatusPeriksaRajal()
    {
        return Yii::$app->cache->getOrSet(LookupConstans::STATUS_PERIKSA_RAJAL, function ($cache) {
            return Lookup::find()->select(['lookup_id', 'lookup_type', 'lookup_name', 'lookup_value'])->where(['lookup_type' => 'status_periksa'])->asArray()->all();
        });
    }

    public static function getStatusBayar()
    {
        return Yii::$app->cache->getOrSet(LookupConstans::STATUS_BAYAR , function ($cache) {
            return Lookup::find()->select(['lookup_id', 'lookup_type', 'lookup_name', 'lookup_value'])->where(['lookup_type' => LookupConstans::STATUS_BAYAR])->asArray()->all();
        });
    }

    public static function getStatusProgram()
    {
        return Yii::$app->cache->getOrSet(LookupConstans::STATUS_PROGRAM, function ($cache) {
            return Lookup::find()->select(['lookup_id', 'lookup_type', 'lookup_name', 'lookup_value'])->where(['lookup_type' => LookupConstans::STATUS_PROGRAM])->asArray()->all();
        });
    }   

    public static function getKonfigLaporanKasir($key_laporan)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_LAPORAN_KASIR, function ($cache) use ($key_laporan) {
            return KonfigLaporan::find()->andWhere([
                'key_laporan' => $key_laporan,
                'is_active' => true,
            ])->orderBy(['konfiglaporan_id' => SORT_ASC])->asArray()->all();
        });
    }

    public static function getLookUpKeperawatan()
    {
        return Yii::$app->cache->getOrSet(LookupConstans::LookUpKeperawatan, function ($cache) {
            return LookupKeperawatan::find()->where([
                'is_active' => true,
                'is_deleted' => false
            ])->asArray()->all();
        });
    }

    public static function getPenjamin()
    {
        return Yii::$app->cache->getOrSet('cache_penjamin', function ($cache) {
            return Penjamin::find()->select([
                'penjamin_id',
                'carabayar_id',
                'penjamin_nama',
                'penjamin_kode',
            ])->where([
                'is_active' => true,
                'is_deleted' => false,
            ])->orderBy([
                'carabayar_id' => SORT_ASC
            ])->all();
        });
    }

    public static function getJenisIdentitasPasien()
    {
        return Yii::$app->cache->getOrSet('cache_jenis_identitas_pasien', function ($cache) {
            return Lookup::find()->select([
                'lookup_id',
                'lookup_type',
                'lookup_name',
                'lookup_value',
            ])->where([
                'lookup_type' => 'jenis_identitas',
            ])->orderBy([
                'lookup_urutan' => SORT_ASC
            ])->all();
        });
    }

}
