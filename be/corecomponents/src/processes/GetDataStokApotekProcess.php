<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;

use app\modules\v1\models\InfoStokObatAlkesFnrNew;
use app\modules\v1\models\InfoStokObatAlkesAllRuanganFnr;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoConstants;
use Doco\Repositories\KonfigRepositories;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\KetersediaanObatView;
use Doco\models\FgetKetersediaanobatFn;

class GetDataStokApotekProcess extends \Doco\components\DocoBaseProcessExtension {
    /**
     * getDataStokApotek
     *
     * documentation of this fn still not ready, if u have any spare time and know how this fn works, please update this, thx!
     *
     * Required @param
     * ================================================================================
     * @param $penjaminId_
     * @param $kelaspelayananId_
     * @param $ruanganId_
     * 
     * Additional @param
     * ================================================================================
     * @param Array $additionalFilters = parse the array with yii2 model builders format
     * 
     **/
    protected function getDataStokApotek(
        $ruanganId_,
        $penjaminId_,
        $kelaspelayananId_,
        $groupJenisObat_ = null,
        $jenisObatAlkesId_ = null,
        $keyword_ = null,
        $page_ = 0,
        $perpage_ = 10,
        $limit_ = 11,
        $withoutlimit_ = false,
        $isOnlyAvailable_ = false,
        $additionalFilters = []
    ) {

            $instalasiId = $this->_requestData->get('instalasi_id');
            $instalasiId = $instalasiId ? $instalasiId : 0;
            $query = (new InfoStokObatAlkesFnrNew(['extParam'=>[$penjaminId_,$kelaspelayananId_,$ruanganId_,$instalasiId]]))->find()
                            ->select([
                                    'instalasi_id',
                                    'obatalkes_id',
                                    'obatalkes_kode',
                                    'obatalkes_namalain',
                                    'obatalkes_nama',
                                    'qty_tersedia',
                                    'ppn',
                                    'hargaygdipakai as hargajual',
                                    'satuankecil_id',
                                    'satuankecil_nama',
                                    'satuansedang_id',
                                    'satuansedang_nama',
                                    'satuanbesar_id',
                                    'satuanbesar_nama',
                                    'harganetto_ygdipakai as harganetto',
                                    'ruangan_id',
                                    'instalasi_id',
                                    'hargaygdipakai',
                                    'hn_diskon',
                                    'hn_ppn',
                                    'hn_margin',
                                    'disc',
                                    'ppn',
                                    'margin',
                                    'group_jenisobat',
                                    'group_jenisobat_nama',
                                    'jenisobatalkes_id',
                                    'jenisobatalkes_nama'
                                ]
                        );
                
                if(!empty($keyword_)){
                    $query->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($keyword_) ]);
                    $query->orWhere(['like', 'LOWER(zat_aktif)', strtolower($keyword_) ]);
                }
                
                if(!empty($groupJenisObat_)){
                    $query->andWhere(['group_jenisobat' => $groupJenisObat_]);
                }
                
                if(!empty($jenisObatAlkesId_)){
                    $query->andWhere(['jenisobatalkes_id' => $jenisObatAlkesId_]);
                }
                
                if($isOnlyAvailable_ === TRUE || strtolower($isOnlyAvailable_) == 'true' || strtolower($isOnlyAvailable_) == 't' || $isOnlyAvailable_ == 1){
                    $query->andWhere(['>','qty_tersedia','0']);
                }

                if ( !empty($additionalFilters) ) {
                    $query->andWhere($additionalFilters);
                }

                if($withoutlimit_ == FALSE || strtolower($withoutlimit_) == 'false' || strtolower($withoutlimit_) == 'f'){
                    $query->offset(($page_-1)*$perpage_)->limit($limit_);
                }
                $result = $query->asArray()->all();
                $list_id_obat = ArrayHelper::getColumn($result,'obatalkes_id');
                $implodeIdObat = implode(',', $list_id_obat);
                
                $getStok = [];
                if (!empty($result)) {
                    $getStok = (new FgetKetersediaanobatFn(['extParam'=>[$ruanganId_, $implodeIdObat]]))->find()
                    ->select(['obatalkes_id', 'qty_tersedia'])->asArray()->all();
                    $getStok = ArrayHelper::index($getStok, 'obatalkes_id');
                }
                                
                foreach($result as $key => $value ){
                    $_stok = isset($getStok[$value['obatalkes_id']]) ? $getStok[$value['obatalkes_id']] : null;
                    if (!empty($_stok)) {
                        $result[$key]['qty_tersedia'] = isset($_stok['qty_tersedia']) ? $_stok['qty_tersedia'] : null; 
                    }
                }
                return $result;

        return $result;
    }

    /**
     * getDataStokRS
     *
     * documentation of this fn still not ready, if u have any spare time and know how this fn works, please update this, thx!
     *
     * Required @param
     * ================================================================================
     * @param $penjaminId_
     * @param $kelaspelayananId_
     * 
     * Additional @param
     * ================================================================================
     * @param Array $additionalFilters = parse the array with yii2 model builders format
     * 
     **/
    protected function getDataStokRS(
        $penjaminId_,
        $kelaspelayananId_,
        $groupJenisObat_ = null,
        $jenisObatAlkesId_ = null,
        $keyword_ = null,
        $page_ = 0,
        $perpage_ = 10,
        $limit_ = 11,
        $withoutlimit_ = false,
        $isOnlyAvailable_ = false,
        $additionalFilters = [] // please, stop add another arguments into this fn, if u need different arguments, split this fn to smaller fn
    ) {
        $cacheDuration = 60 * 3; // insecond
        $result = (new InfoStokObatAlkesAllRuanganFnr(['extParam'=>[$penjaminId_,$kelaspelayananId_]]))->getDb()->cache(function ($db) use(
            $penjaminId_,
            $kelaspelayananId_,
            $groupJenisObat_,
            $jenisObatAlkesId_,
            $keyword_,
            $page_,
            $perpage_,
            $limit_,
            $withoutlimit_,
            $isOnlyAvailable_,
            $additionalFilters ) {

                $query = (new InfoStokObatAlkesAllRuanganFnr(['extParam'=>[$penjaminId_,$kelaspelayananId_]]))->find()
                            ->select([
                                'obatalkes_id',
                                'obatalkes_kode',
                                'obatalkes_namalain',
                                'obatalkes_nama',
                                'ppn',
                                'hargaygdipakai as hargajual',
                                'satuankecil_id',
                                'satuankecil_nama',
                                'satuansedang_id',
                                'satuansedang_nama',
                                'satuanbesar_id',
                                'satuanbesar_nama',
                                'harganetto_ygdipakai as harganetto',
                                'hargaygdipakai',
                                'hn_diskon',
                                'hn_ppn',
                                'hn_margin',
                                'disc',
                                'ppn',
                                'margin',
                                'group_jenisobat',
                                'group_jenisobat_nama',
                                'jenisobatalkes_id',
                                'jenisobatalkes_nama',
                                'sum(qty_masuk) as qty_masuk',
                                'sum(qty_keluar) as qty_keluar',
                                'sum(qty_dipesan) as qty_dipesan',
                                'sum(qty_tersedia) as qty_tersedia',
                                'sum(qty_stok) as qty_stok'
                            ]);
                
                if(!empty($keyword_)){
                    $query->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($keyword_) ]);
                    $query->orWhere(['like', 'LOWER(zat_aktif)', strtolower($keyword_) ]);
                }
                
                if(!empty($groupJenisObat_)){
                    $query->andWhere(['group_jenisobat' => $groupJenisObat_]);
                }
                
                if(!empty($jenisObatAlkesId_)){
                    $query->andWhere(['jenisobatalkes_id' => $jenisObatAlkesId_]);
                }
                
                if($isOnlyAvailable_ === TRUE || strtolower($isOnlyAvailable_) == 'true' || strtolower($isOnlyAvailable_) == 't' || $isOnlyAvailable_ == 1){
                    $query->andWhere(['>','qty_tersedia','0']);
                }

                if ( !empty($additionalFilters) ) {
                    $query->andWhere($additionalFilters);
                }

                $query->groupBy([
                    'obatalkes_id',
                    'obatalkes_kode',
                    'obatalkes_namalain',
                    'obatalkes_nama',
                    'ppn',
                    'hargaygdipakai',
                    'satuankecil_id',
                    'satuankecil_nama',
                    'satuansedang_id',
                    'satuansedang_nama',
                    'satuanbesar_id',
                    'satuanbesar_nama',
                    'harganetto_ygdipakai',
                    'hargaygdipakai',
                    'hn_diskon',
                    'hn_ppn',
                    'hn_margin',
                    'disc',
                    'ppn',
                    'margin',
                    'group_jenisobat',
                    'group_jenisobat_nama',
                    'jenisobatalkes_id',
                    'jenisobatalkes_nama'
                ]);
                
                if($withoutlimit_ == FALSE || strtolower($withoutlimit_) == 'false' || strtolower($withoutlimit_) == 'f'){
                    $query->offset(($page_-1)*$perpage_)->limit($limit_);
                }
                
                $result = $query->all();
                
                return $result;
            }, $cacheDuration);

        return $result;
    }

    protected function getData() {
        $ruangan_id = $this->_requestData->get('ruangan_id', null);
        $get_konfig_stok = $this->_requestData->get('get_konfig_stok', false);
        $getAdditionalFilters = $this->_requestData->get('additional_filters', []);
        $ruangan = Ruangan::find()->select(['instalasi_id', 'ruangan_id'])->where(['ruangan_id' => $ruangan_id])->one();
        $is_depo_farmasi = $ruangan['instalasi_id'] == DocoConstants::INSTALASI_FARMASI ? true : false;

        if (!empty($ruangan_id) ) {
            $perpage = 10;
            $konfigFarmasi = KonfigRepositories::getKonfigFarmasi();
            if($get_konfig_stok && $konfigFarmasi['get_stok_rs'] && $is_depo_farmasi) {
                return $this->getDataStokRS(
                    /*Penjamin*/ $this->_requestData->get('penjamin_id', 0),
                    /*Kelas Pelayanan*/ $this->_requestData->get('kelaspelayanan_id', 0),
                    /*Group Jenis Obat*/ $this->_requestData->get('group_jenisobat', null),
                    /*Jenis Obat Alkes*/ $this->_requestData->get('jenisobatalkes_id', null),
                    /*Keyword*/ $this->_requestData->get('keyword', ''),
                    /*Page*/ $this->_requestData->get('page', 1),
                    /*Perpage*/ $perpage,
                    /*Limit*/ $this->_requestData->get('limit', 11),
                    /*Without Limit*/ $this->_requestData->get('withoutLimit', 'false'),
                    /*Only Available Stock*/ $this->_requestData->get('isOnlyAvailable', false),
                    /* Additional Query Filters */ $getAdditionalFilters
                );
            } else {
                return $this->getDataStokApotek(
                    /*Ruangan*/ $ruangan_id,
                    /*Penjamin*/ $this->_requestData->get('penjamin_id', 0),
                    /*Kelas Pelayanan*/ $this->_requestData->get('kelaspelayanan_id', 0),
                    /*Group Jenis Obat*/ $this->_requestData->get('group_jenisobat', null),
                    /*Jenis Obat Alkes*/ $this->_requestData->get('jenisobatalkes_id', null),
                    /*Keyword*/ $this->_requestData->get('keyword', ''),
                    /*Page*/ $this->_requestData->get('page', 1),
                    /*Perpage*/ $perpage,
                    /*Limit*/ $this->_requestData->get('limit', 11),
                    /*Without Limit*/ $this->_requestData->get('withoutLimit', 'false'),
                    /*Only Available Stock*/ $this->_requestData->get('isOnlyAvailable', false),
                    /* Additional Query Filters */ $getAdditionalFilters
                );
            }
        } else {
            return [];
        }
    }
    
    protected function processFlow() {
        $time = microtime(true);
        $result = $this->getData();

        return [
            'data' => $result,
            'payload' => $this->_requestData->get(),
            'totalResult' => count($result),
            'time' => number_format(microtime(true)-$time, 3),
        ];
    }
}