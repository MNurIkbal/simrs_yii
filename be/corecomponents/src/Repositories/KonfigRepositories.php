<?php

/**
 * @author Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Repositories;

use Yii;
use Doco\components\DocoConstants;
use Doco\models\KonfigFarmasi;
use Doco\models\KonfigGudang;

class KonfigRepositories {
    public static function getKonfigFarmasi() {
        return KonfigFarmasi::getDb()->cache(function(){
            return KonfigFarmasi::find()->one();
        }, DocoConstants::DURATION, new \yii\caching\TagDependency(['tags'=>'konfig_farmasi']));
    }

    public static function getKonfigGudang() {
        return KonfigGudang::getDb()->cache(function(){
            return KonfigGudang::find()->one();
        }, DocoConstants::DURATION, new \yii\caching\TagDependency(['tags'=>'konfig_gudang']));
    }
}

?>