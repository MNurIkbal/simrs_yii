<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\cache;

use Yii;
use yii\helpers\ArrayHelper;

use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\PerujukView;
use app\modules\v1\models\GolonganUmur;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\ProfilRumahSakitView;
use app\modules\v1\models\SyPrefixMp;
use app\modules\v1\models\PenomoranK;
use app\modules\v1\models\PropinsiV;
use app\modules\v1\models\KabupatenV;
use app\modules\v1\models\KecamatanV;
use app\modules\v1\models\KelurahanV;
use app\modules\v1\models\Negara;
use app\modules\v1\models\LookupTransaksi;
use Doco\components\constans\LookupConstans;
use Doco\models\KonfigTarif;
use Doco\Services\Cache as GeneralCache;

class Cache {

    const EXPIRED_CACHE = 3600;
    /**
     * Set Batch Lookup 
     * @param  [string/intger] $key
     * @param  [array] $types
     * @return array
     */
    public static function getLookup($key, $types)
    {
        return Yii::$app->cache->getOrSet($key, function ($cache) use ($types) {
            $results = [];
            $getLookup = Lookup::find()->select([
                'lookup_id',
                'lookup_type',
                'lookup_name',
                'lookup_value'
            ])->where([
                'lookup_type' => $types, 
                'is_active' => true, 
                'is_deleted' => false
            ])->orderBy([
                'lookup_type' => SORT_ASC, 
                'lookup_urutan' => SORT_ASC,
                'lookup_name' => SORT_ASC,
            ])->asArray()->all();
            foreach ($getLookup as $lookup) {
                $results[$lookup['lookup_type']][] = $lookup;
            }
            return $results;
        },self::EXPIRED_CACHE);
    }

    /**
     * Get Batch Cache Master
     * @param  [string/integer] $key
     * @param  [array] $listMaster [List Nama Model]
     * @return [array]
     */
    public static function getMaster($key, $listMaster)
    {
        return Yii::$app->cache->getOrSet($key, function ($cache) use ($listMaster) {
            foreach ($listMaster as $key => $value) {
                $condition = false;
                $select = isset($value['select']) ? $value['select'] : [];
                if (isset($value['model'])) {
                    $class = "app\modules\\v1\models\\" . $value['model'];
                }
                $model = new $class;
                $q = $model->find()->select($select);
                if ($condition) {
                    $q->andWhere($condition);
                }
                $q->andWhere(['is_active' => true, 'is_deleted' => false]);
                $order_by = isset($value['order_by']) ? $value['order_by'] : [];
                $q->orderBy($order_by);
                $results[$key] = $q->asArray()->all();
            }
            return $results;
        },self::EXPIRED_CACHE);
    }

    /**
     * Get Batch Cache Instalasi
     * @param  [string/integer] $id
     * @return [array]
     */
    public static function getRuanganByInstalasi($id)
    {
        $id = json_decode($id, true);
        $idIns = is_array($id) ? 'penunjang' : (int) $id;
        $key = "instalasi-{$idIns}";
        return Yii::$app->cache->getOrSet($key, function ($cache) use ($id) {
            if (is_array($id)) {
                $condition = [
                    'IN', 'instalasi_id', $id,
                    'is_active' => true, 'is_deleted' => false
                ];
            } else {
                $condition = ['instalasi_id' => $id, 'is_active' => true, 'is_deleted' => false];
            }
            $model = new Ruangan;
            $query = $model::find()->select([
                'ruangan_id',
                'instalasi_id',
                'ruangan_nama',
                'kode_ruangan_bpjs'
            ]);
            $query->andWhere($condition);
            $query->orderBy(['ruangan_nama' => SORT_ASC]);
            return $query->all();
        }, self::EXPIRED_CACHE);
    }

    /**
     * Get Batch Cache Cara bayar
     * @param  [string/integer] $default
     * @return [array]
     */
    public static function getCaraBayar($default = 1)
    {
        $getDataCaraBayar = (new GeneralCache)->getCaraBayar();
        $carabayar_id = Yii::$app->request->get('carabayar_id', null);
        if (!empty($carabayar_id)) {
            $result = ArrayHelper::index($getDataCaraBayar, 'carabayar_id');
            $getDataCaraBayar = [];
            if (isset($result[$carabayar_id])) {
                $getDataCaraBayar[] = $result[$carabayar_id];
            }
        }
        
        if ($default == 2) {
            $getCache = (object) $getDataCaraBayar;
        } else {
            $getCache = ArrayHelper::map($getDataCaraBayar, 'carabayar_id', 'carabayar_nama');
        }

        return $getCache;
    }

    /**
     * Get Asal Rujukan
     * @return [array]
     */
    public static function getAsalRujukan()
    {
        $key = 'pendaftaran-asal-rujuk';
        return Yii::$app->cache->getOrSet($key, function ($cache) {
            $model = new AsalRujukan;
            $query = $model::find()->select([
                'asalrujukan_id',
                'asalrujukan_nama'
            ]);
            $query->andWhere(['is_active' => true, 'is_deleted' => false]);
            $query->orderBy(['asalrujukan_nama' => SORT_ASC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return $query->asArray()->all();
        },self::EXPIRED_CACHE);
    }

    public static function getKelasPelayanan()
    {
        $getKelasPelayanan = GeneralCache::getKelasPelayanan();
        return $getKelasPelayanan;
    }

    public static function getPenjamin()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::PENDAFTARAN_PENJAMIN, function ($cache) {
            $model = Penjamin::find()->select([
                'penjamin_id',
                'carabayar_id',
                'penjamin_nama'
            ])->where([
                'is_active' => true
            ]);
            return $model->asArray()->all();
        },self::EXPIRED_CACHE);
    }

    public static function getListPenyakit($mapping = true)
    {
        $key = 'pendaftaran-list-penyakit';
        return Yii::$app->cache->getOrSet($key, function ($cache) use ($mapping) {
            $data = JenisKasusPenyakit::find()
                    ->select([
                        'jeniskasuspenyakit_id',
                        'jeniskasuspenyakit_nama'
                    ])->andWhere(['is_active' => 't'])->orderBy('jeniskasuspenyakit_nama');
            if ($mapping) {
                $items = ArrayHelper::map($data->all(), 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');
            } else {
                $items = $data->all();
            }

            return $items;
        },self::EXPIRED_CACHE);
    }

    public static function getInstalasi($isPenunjang = 'false')
    {
        $key = 'pendaftaran-instalasi-' . $isPenunjang;
        return Yii::$app->cache->getOrSet($key, function ($cache) use ($isPenunjang) {
            $model = new Instalasi;
            $model = $model->find()->select([
                'instalasi_id',
                'instalasi_nama'
            ])->andWhere(['is_pelayanan'=>true]);
            if($isPenunjang != 'false'){
                $model->andWhere('is_penunjang = '.$isPenunjang);
            }
            $data = $model
                ->orderBy('instalasi_id')
                ->asArray()
                ->all();

            return $data;
        },self::EXPIRED_CACHE);
    }

    public static function getRujukanDari()
    {
        $getDataRujukan = (new GeneralCache)->getRujukanDari();
        $result = [];
        if(!empty($getDataRujukan)) {
            foreach ($getDataRujukan as $key => $value) {
                $parenId = $value['asalrujukan_id'];
                $result[$parenId][] = [
                    'id' => $value['perujuk_id'],
                    'value' => $value['namaperujuk']
                ];
            }
        }

        return $result;
    }

    public static function getGolonganUmur()
    {
        $key = 'pendaftaran-golongan-umur';
        return Yii::$app->cache->getOrSet($key, function ($cache) {
            $golonganUmur = GolonganUmur::find()->orderBy('golonganumur_minimal ASC');
            return $golonganUmur->asArray()->all();
        },self::EXPIRED_CACHE);
    }

    public static function getKonfigSystem()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S, function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        },self::EXPIRED_CACHE);
    }

    public static function getConfigKelasPelayanan()
    {
        $konfig = self::getKonfigSystem();
        $data = json_decode($konfig['kelas_pelayanan'], true);
        $kelaspelayanan_id = [];
        $result = [];
        if(!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $kelaspelayanan_id[] = $value;
            }

            $inCondition = "{" . implode(",", $kelaspelayanan_id) . "}";
            $result = Yii::$app->db->createCommand("
            SELECT kelaspelayanan_id, kelaspelayanan_nama FROM kelaspelayanan_m
                JOIN unnest ('$inCondition'::int[])
                WITH ORDINALITY t(kelaspelayanan_id, ord) USING (kelaspelayanan_id)
                ORDER BY urutankelas
            ")->queryAll();
        }
        
        return $result;
    }

    public static function getPpkPelayanan()
    {
        $key = 'ppk-pelayanan-sirs';
        $modelBpjs = new Bpjs;
        $kode = $modelBpjs->ppkPelayanan;
        $data = $modelBpjs->referensiFaskes($kode, 2);
        $faskes = [];
        if(!empty($data)) {
            $faskes = $data['response']['faskes'][0];
        }
        return Yii::$app->cache->getOrSet($key, function ($cache) use($faskes) {
            return $faskes;
        });
    }

    /**
     * [getCaraBayarPenjamin untuk mendapatkan id carabayar]
     * @param  integer $penjamin_id
     * @return integer|null
     */
    public static function getCaraBayarPenjamin($penjamin_id)
    {
        $listPenjamin = self::getPenjamin();
        $result = null;
        foreach ($listPenjamin as $value) {
            if ($value['penjamin_id'] == $penjamin_id) {
                $result = $value['carabayar_id'];
                break;
            }
        }
        return $result;
    }

    /**
     * getAttrCaraBayar untuk mendapatkan group carabayar
     * @param  integer $carabayar_id 
     * @return array
     */
    public static function getAttrCaraBayar($carabayar_id)
    {
        $listCaraBayar = self::getCaraBayar(2);
        $attr = [];
        foreach ($listCaraBayar as $value) {
            if ( ( is_object($value) && $carabayar_id == $value->carabayar_id ) ||( isset($value['carabayar_id']) && $carabayar_id == $value['carabayar_id'] ) ) {
                $attr = is_object($value) ? (array) $value->attributes : $value;
                break;
            }
        }
        return $attr;
    }

    public static function getProfileRs()
    {
        return Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRumahSakitView::find()->asArray()->one();
        });
    }

    /**
     * getListBagianSty get list bagian ruangan styp
     * @return array
     */
    public static function getListBagianSty()
    {
        $key = 'pendaftaran-list-bagian';
        return Yii::$app->cache->getOrSet($key, function ($cache) {
            $listBagian = Ruangan::find()
                ->select(['instalasi_id','ruangan_id','ruangan_nama', 'additional_data'])
                ->where(['is_active' => true])
                ->orderBy(['ruangan_nama'=> SORT_ASC ]);
    
            return $listBagian->asArray()->all();
        },self::EXPIRED_CACHE);
    }

    public static function syPrefixMap()
    {
        $key = 'pendaftaran-prefix-mp';
        return Yii::$app->cache->getOrSet($key, function ($cache) {
            $model = SyPrefixMp::find()->select([
                'instalasi_id',
                'penomoran_id',
                'nama_prefix'
            ])->where([
                'is_active' => true
            ]);
            return $model->asArray()->all();
        },self::EXPIRED_CACHE);
    }

    public static function penomoran()
    {
        $key = 'pendaftaran-penomoran-konfig';
        return Yii::$app->cache->getOrSet($key, function ($cache) {
            $model = PenomoranK::find()->select([
                'penomoran_id',
                'penomoran_nama',
                'prefix'
            ]);
            return $model->asArray()->all();
        },self::EXPIRED_CACHE);
    }

    /**
     * [getPrefixMap get mapping instalasi]
     * @param  integer $instalasi_id
     * @return array|null
     */
    public static function getPrefixMap($insId)
    {
        $listPrefixMp = self::syPrefixMap();
        $konfigPenomoran = self::penomoran();
        $result = []; 
        $idKonfig = null;
        foreach ($listPrefixMp as $value) {
            if ($value['instalasi_id'] == $insId) {
                $idKonfig = $value['penomoran_id'];
                break;
            }
        }

        if(!is_null($idKonfig)) {
            foreach ($konfigPenomoran as $value) {
                if ($value['penomoran_id'] == $idKonfig) {
                    $result = [
                        'penomoran_id' => $value['penomoran_id'],
                        'prefix' => $value['prefix']
                    ];
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * [getRujukan get mapping rujukan di penunjang styp]
     * @return array
     */
    public static function getRujukanStyp()
    {
        return Yii::$app->cache->getOrSet('rujukandari_styp' , function ($cache) {
            return Instalasi::find()
                ->where(['IN', 'instalasi_id', [1,3,85]])
                ->andWhere([
                    'is_active' => true, 
                    'is_deleted' => false
                ])
                ->orderBy(['instalasi_namalainnya' => SORT_ASC])    
                ->asArray()
                ->all();
        });
    }

    public static function getConfigBpjs()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::LOOKUP_BPJS, function ($cache) {
            return Lookup::find()->where(['lookup_type'=>DocoConstants::LOOKUP_BPJS])
                                ->asArray()
                                ->all();
        });
    }

    /**
     * [propinsi get data propinsi]
     * @return array
     */
    public static function Propinsi()
    {
        return Yii::$app->cache->getOrSet('propinsi' , function ($cache) {
            return PropinsiV::find()
                ->orderBy([
                    'propinsi_nama' => SORT_ASC
                ])
                ->all();
        });
    }

    /**
     * [kabupaten get data kabupaten]
     * @return array
     */
    public static function Kabupatan()
    {
        return Yii::$app->cache->getOrSet('kabupaten' , function ($cache) {
            return KabupatenV::find()
                ->orderBy([
                    'kabupaten_nama' => SORT_ASC
                ])
                ->all();
        });
    }

    /**
     * [kecamatan get data kecamatan]
     * @return array
     */
    public static function Kecamatan()
    {
        return Yii::$app->cache->getOrSet('kecamatan' , function ($cache) {
            return KecamatanV::find()
                ->orderBy([
                    'kecamatan_nama' => SORT_ASC
                ])
                ->all();
        });
    }

    /**
     * [kelurahan get data kelurahan]
     * @return array
     */
    public static function Kelurahan($pId, $kabId, $kecId)
    {
        $key = 'kelurahan'.$pId.$kabId.$kecId;
        return Yii::$app->cache->getOrSet($key , function ($cache) use($pId, $kabId, $kecId) {
            return KelurahanV::find()
                ->where([
                    'propinsi_id' => $pId,
                    'kabupaten_id' => $kabId,
                    'kecamatan_id' => $kecId
                ])
                ->orderBy([
                    'kelurahan_nama' => SORT_ASC
                ])
                ->all();
        });
    }

    /**
     * [negara get data negara]
     * @return array
     */
    public static function Negara()
    {
        return Yii::$app->cache->getOrSet('negara' , function ($cache) {
            return Negara::find()
                ->orderBy([
                    'nama_negara' => SORT_ASC
                ])
                ->all();
        });
    }

    /**
     * [get data lookuptransaksi]
     * @return array
     */
    public static function Lookuptransaksi()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_LOOKUP_TRANSAKSI, function ($cache) {
            return LookupTransaksi::find()
                ->asArray()
                ->all();
        });
    }

    public static function getStatusPeriksa($singkatan)
    {
        if ($singkatan == LookupConstans::SINGKATAN_RI){
            $lookup_type = LookupConstans::STATUS_RANAP;
            return Yii::$app->cache->getOrSet(LookupConstans::STATUS_RANAP, function ($cache) {
                $lookup = new Lookup;
                $getLookup = $lookup->find()->where(['lookup_type'=> LookupConstans::STATUS_RANAP, 'is_active' => true, 'is_deleted' => false]);
                $items = ArrayHelper::map($getLookup->all(), 'lookup_id', 'lookup_name');    
                return $items;
            });
        }
        return Yii::$app->cache->getOrSet(LookupConstans::STATUS_PERIKSA, function ($cache) {
            $lookup = new Lookup;
            $getLookup = $lookup->find()->where(['lookup_type'=> LookupConstans::STATUS_PERIKSA, 'is_active' => true, 'is_deleted' => false]);
            $items = ArrayHelper::map($getLookup->all(), 'lookup_id', 'lookup_name');    
            return $items;
        });
    }

    public static function getKonfigTarif()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_CACHE_CONFIG_TARIF, function ($cache) {
            return KonfigTarif::find()->asArray()->one();
        });
    }

}