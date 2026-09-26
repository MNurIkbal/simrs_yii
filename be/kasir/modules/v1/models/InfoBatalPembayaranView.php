<?php

/**
 * @Author: Dede Herdiana
 * @Date:   22-11-2021 15:42
 * @Last Modified by:   Dede Herdiana
 * @Last Modified time: 22-11-2021 15:42
 */

namespace app\modules\v1\models;


class InfoBatalPembayaranView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infobatalpembayaran_v';
    }
}
