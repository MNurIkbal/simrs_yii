<?php
/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

/**
 * This is the model class for table "penjamindiskon_m".
 *
 * @property integer $penjamin_id
 * @property float $diskon_otomatis
 * @property boolean $is_active
 */
class PenjaminDiskonView extends \yii\db\ActiveRecord
{
   
    public static function tableName()
    {
        return 'penjamindiskon_v';
    }
}
?>