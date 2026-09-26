<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "inforesepdetail_v".
 *
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 */
class InfoResepDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inforesepdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }
}
