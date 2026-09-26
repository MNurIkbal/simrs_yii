<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "carabayar_v".
 *
 * @property int $carabayar_id
 * @property int $groupcarabayar_id
 * @property string $group_carabayar
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $penjamin_m
 * @property string $penjamin_namalainnya
 * @property bool $is_active
 */
class PenjaminDiskonView extends \yii\db\ActiveRecord
{
   
    public static function tableName()
    {
        return 'penjamindiskon_v';
    }
}
?>