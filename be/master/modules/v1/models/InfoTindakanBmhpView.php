<?php

/**
 * @Author: Wahyu Saepuloh
 * @Date:   11 November 2019
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotindakanbmhp_v".
 *
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $detail_bmhp
 */
class InfoTindakanBmhpView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotindakanbmhp_v';
    }
}
