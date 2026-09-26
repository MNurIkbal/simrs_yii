<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use app\modules\v1\models\StokObatAlkes;

class Pemusnahan {
    public static function PotongStokObat($data){
        $tmpInsert = StokObatAlkes::populateData($data);

        if ($tmpInsert) {
            StokObatAlkes::batchInsert($tmpInsert, false);
        }

        return true;
    }
}