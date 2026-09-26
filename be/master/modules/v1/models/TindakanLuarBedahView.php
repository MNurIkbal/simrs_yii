<?php

/**
 * @Author: Ripan
 * @Date:   19 Mei 2022
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tindakanluarbedah_v".
 *
 * @property int $daftartindakan_id
 * @property int $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property int $tindakanluarbedah_id
 * @property string $tindakanluarbedah_nama
 */
class TindakanLuarBedahView extends \Doco\components\DocoActiveRecord
{
    public static function primaryKey()
	{
		return ['daftartindakan_id'];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakanluarbedah_v';
    }
}
