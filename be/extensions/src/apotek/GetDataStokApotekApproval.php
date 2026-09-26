<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\apotek;

use Yii;
use app\modules\v1\models\InfoStokObatAlkesApproveFnr;

class GetDataStokApotekApproval extends \Doco\processes\GetDataStokApotekProcess {
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
        $additionalFilters = [] // please, stop add another arguments into this fn, if u need different arguments, split this fn to smaller fn
    ) {
        $cacheDuration = 60 * 3; // insecond
        $result = (new InfoStokObatAlkesApproveFnr(['extParam'=>[$penjaminId_,$kelaspelayananId_,$ruanganId_]]))->getDb()->cache(function ($db) use(
            $ruanganId_,
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

                $query = (new InfoStokObatAlkesApproveFnr(['extParam'=>[$penjaminId_,$kelaspelayananId_,$ruanganId_]]))->find()
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
                
                $result = $query->all();
                
                return $result;
            }, $cacheDuration);

        return $result;
    }
    
    protected function processFlow() {
        $result = $this->getData();

        return [
            'data' => $result,
            'payload' => $this->_requestData->get(),
            'totalResult' => count($result)
        ];
    }
}