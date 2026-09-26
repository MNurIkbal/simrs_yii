<?php

/**
* @author yaya
**/

namespace app\modules\v1\cache;

use Yii;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use Doco\components\DocoConstants;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Shift;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\ProfilRsView;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Pegawai;
use Doco\models\KonfigTarif;
use Doco\Services\Cache as GeneralCache;

class Cache 
{

    const CACHE_PEGAWAI = 'cache_pegawai';
    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getCaraBayar($duration = 3600)
    {
        return $getDataCaraBayar = (new GeneralCache)->getCaraBayar();
    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getPenjamin($duration = 3600)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_PENJAMIN, function ($cache) {
            return Penjamin::find()->select([
                'penjamin_id',
                'carabayar_id',
                'penjamin_nama',
                'penjamin_namalainnya',
                'penjamin_kode',
                'groupmargin_id',
            ])->where([
                'is_active' => true
            ])->all();
        });
    }

    /**
    * @return array
    **/
    public static function getNilaiUang()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_NILAI_UANG, function ($cache) {
            return Lookup::find()->where([
                'lookup_type' => 'nilai_uang'
            ])->orderBy('lookup_urutan ASC')->all();
        });
    }

    public static function getListRuangan($instalasi_id = '')
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_RUANGAN .'-'. $instalasi_id, 
                            function ($cache) use ($instalasi_id) {
            $query = Ruangan::find()->joinWith('instalasi')->where(['ruangan_m.is_active' => 't']);
            if ($instalasi_id) {
                $query->andWhere(['ruangan_m.instalasi_id' => $instalasi_id]);
            }
            return $query->all();
        });
    }

    public static function getListPegawaiRuangan($ruangan_id = '')
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_PEGAWAI_RUANGAN .'-'. $ruangan_id, 
                            function ($cache) use ($ruangan_id) {
            $data = PegawaiView::find();

            if ($ruangan_id) {
                $data->andWhere(['ruangan_id' => $ruangan_id]);
            }

            $data->orderBy('ruangan_id');
            return $data->asArray()->all();
        });
    }

    public static function getShift()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_SHIFT , function ($cache) {
            return Shift::find([
                'shift_id',
                'shift_nama',
                'shift_namalainnya',
                'shift_kode'
            ])->where([
                'is_active' => true
            ])->all();
        });
    }

    public static function getKonfigSistem()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        });
    }

    public static function getKonfigTarif()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_CACHE_CONFIG_TARIF , function ($cache) {
            return KonfigTarif::find()->asArray()->one();
        });
    }

    public static function getProfileRs()
    {
        return Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRsView::find()->asArray()->one();
        });
    }


    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getPenjaminCaraBayar($penjamin_id ='')
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_PENJAMIN.'-'. $penjamin_id, function ($cache) use ($penjamin_id) {
            return Penjamin::find()->select([ 
                'carabayar_id' 
            ])->where([
                'is_active' => true,
                'penjamin_id' => $penjamin_id 
            ])->asArray()->one();
        });
    }


    public static function getKelasPelayanan($kelasPerlayan = '')
    {   
        $attributes = [
            'kelaspelayanan_id',
            'kelaspelayanan_nama',
            'kelaspelayanan_namalainnya',
            'kelaspelayanan_kode'
        ];

        $cond = [
            'is_active' => true
        ];

        $cacheName = DocoConstants::VAR_C_KP;
        if (!empty($kelasPerlayan)) {
            $cacheName .= "-{$kelasPerlayan}";
            $cond['kelaspelayanan_id'] = $kelasPerlayan;
        }

        return Yii::$app->cache->getOrSet($cacheName, function ($cache) use ($attributes, $cond) {
            return KelasPelayanan::find()
                        ->select($attributes)
                        ->andWhere($cond)
                        ->orderBy(['kelaspelayanan_nama' => SORT_ASC])->all();
        });
    }

    public static function getPegawai($pegawaiId)
    {
        return Yii::$app->cache->getOrSet(self::CACHE_PEGAWAI, function ($cache) use($pegawaiId) {
            return Pegawai::find()->select([
                'pegawai_id',
                'nama_pegawai',
                'nomorindukpegawai',
            ])->andWhere([
                'is_active' => true,
                'pegawai_id' => $pegawaiId
            ])->one();
        });
    }
}