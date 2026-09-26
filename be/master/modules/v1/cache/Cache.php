<?php

/**
* @author yaya
**/

namespace app\modules\v1\cache;

use Yii;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use Doco\components\DocoConstants;
use app\modules\v1\models\Klasifikasipasien;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\ServiceGroup;
use app\modules\v1\models\ServiceCategory;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\KonfigSystemDetail;
use app\modules\v1\models\Shift;
use Doco\Services\Cache as GeneralCache;
use Doco\components\constans\AntrianConstants;
use app\modules\v1\models\InfoJadwalDokterView;
use app\modules\v1\models\SummaryJadwalDokterView;
use yii\helpers\ArrayHelper;

class Cache {

    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getCaraBayar($duration = 3600)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_CB, function ($cache) {
            return CaraBayar::find()->where([
                'is_active' => true
            ])->all();
        });
    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getGroupCaraBayar($duration = 3600, $list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::CACHE_GROUP_CB, function ($cache) {
            return Lookup::find()->where([
                'is_active' => true,
                'lookup_type' => 'group_carabayar'
            ])->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'lookup_id','lookup_name');
        }

        return $result;
    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getPenjamin($duration = 3600)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_PENJAMIN, function ($cache) {
            return Penjamin::find()->where([
                'is_active' => true
            ])->all();
        });
    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getKlasifikasiPasien($duration = 3600)
    {
        // return Yii::$app->cache->getOrSet(DocoConstants::VAR_CACHE_KLASIFIKASI_PASIEN, function ($cache) {
            return Klasifikasipasien::find()->where([
                'is_active' => true
            ])->all();
        // });
    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array ex => [<id> => <name>]
    **/
    public function getJenisAntrian($duration = 3600, $list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::CACHE_JENIS_ANTRIAN, function ($cache) {
            return Lookup::find()->where([
                'is_active' => true,
                'lookup_type' => 'jenis_antrian'
            ])->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'lookup_id','lookup_name');
        }

        return $result;
    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array ex => [<id> => <name>]
    **/
    public function getFungsiAntrian($duration = 3600, $list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::CACHE_FUNGSI_ANTRIAN, function ($cache) {
            return Lookup::find()->where([
                'is_active' => true,
                'lookup_type' => 'fungsi_antrian'
            ])->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'lookup_id','lookup_name');
        }

        return $result;
    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array 
    **/
    public function getInstalasi($duration = 3600, $list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::CACHE_INSTALASI, function ($cache) {
            return Instalasi::find()->where([
                'is_active' => true
            ])->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'instalasi_id','instalasi_nama');
        }

        return $result;
    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array 
    **/
    public function getRuangan($duration = 3600, $list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::CACHE_RUANGAN, function ($cache) {
            return Ruangan::find()->where([
                'is_active' => true,
            ])->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'ruangan_id','ruangan_nama');
        }

        return $result;
    }

    /**
    * @var $key string untuk key di look up
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getLookUp($key, $duration = 3600)
    {
        return Yii::$app->cache->getOrSet($key, function ($cache) use($key) {
            return Lookup::find()->where([
                'is_active' => true,
                'lookup_type' => $key
            ])
            ->orderby(['lookup_name' => SORT_ASC])
            ->all();
        });
    }

    public static function getServiceGroup($duration = 3600, $list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::CACHE_SERVICE_GROUP, function ($cache) {
            return ServiceGroup::find()->where([
                'is_active' => true,
            ])->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'servicegroup_id','servicegroup_nama');
        }

        return $result;
    }

    public static function getServiceCategory($duration = 3600, $list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::CACHE_SERVICE_CATEGORY, function ($cache) {
            return ServiceCategory::find()->where([
                'is_active' => true,
            ])->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'servicecategory_id','servicecategory_nama');
        }

        return $result;
    }

    public static function getKelasPelayanan($list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::VAR_C_KP, function ($cache) {
            return KelasPelayanan::find()->andWhere(['is_active' => true])
                        ->orderby(['kelaspelayanan_nama' => SORT_ASC])->asArray()->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'kelaspelayanan_id','kelaspelayanan_nama');
        }

        return $result;
    }

    public static function getJenisObatAlkes($list = true, $findByGroup = '')
    {
        $prefixName = !empty($findByGroup) ? '-' . $findByGroup : '';
        $result = Yii::$app->cache->getOrSet(DocoConstants::VAR_J_OA . $prefixName, function ($cache) use ($findByGroup) {
            $data = JenisObatAlkes::find()->andWhere(['is_active' => true,]);
            if (!empty($findByGroup)) {
                $data->andWhere(['group_jenisobat' => $findByGroup]);
            }
            return $data->orderby(['jenisobatalkes_nama' => SORT_ASC])->asArray()->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'jenisobatalkes_id','jenisobatalkes_nama');
        }

        return $result;
    }

    public static function getShift()
    {
        return Yii::$app->cache->getOrSet('MHG-' . DocoConstants::CACHE_SHIFT , function ($cache) {
            return Shift::find([
                'shift_id',
                'shift_nama',
                'shift_namalainnya',
                'shift_kode'
            ])->where([
                'is_active' => true
            ])->asArray()->all();
        });
    }

    public static function getKonfigAntrian()
    {
        $konfig = GeneralCache::getKonfigSistem();
        return Yii::$app->cache->getOrSet(AntrianConstants::KONFIG_ANTRIAN, function ($cache) use($konfig) {
            if ($konfig['is_slider'] == 0) {
                $konfig_detail = KonfigSystemDetail::find()
                ->where(['is_foto' => true])
                ->all();
            } else {
                $konfig_detail = KonfigSystemDetail::find()
                ->where(['is_foto' => false])
                ->all();
            }
    
            return [
                'header' => $konfig['header'],
                'header_detail' => $konfig['header_detail'],
                'footer' => $konfig['footer'],
                'path_logoheader' => $konfig['path_logoheader'],
                'is_slider' => $konfig['is_slider'],
                'url_slider' => $konfig['url_slider'],
                'is_banyakloket' => $konfig['is_banyakloket'],
                'slides' => $konfig_detail
            ];
        });

    }

    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getPegawaiDokter($duration = 3600, $list = true)
    {
        $result = Yii::$app->cache->getOrSet(DocoConstants::VAR_CACHE_PEGAWAI_DOKTER, function ($cache) {
            return SummaryJadwalDokterView::find()->all();
        });

        if ($list) {
            $result = ArrayHelper::map($result,'pegawai_id','nama_pegawai');
        }

        return $result;
    }
}
