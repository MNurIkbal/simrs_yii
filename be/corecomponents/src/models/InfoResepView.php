<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "inforesep_v".
 *
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 */
class InfoResepView extends \Doco\components\DocoActiveRecord
{
    public $tgl_resep;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inforesep_v';
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
