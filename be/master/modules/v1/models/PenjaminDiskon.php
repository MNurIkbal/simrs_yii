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
class PenjaminDiskon extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjamindiskon_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['penjamin_id', 'diskon_otomatis', 'is_active'], 'required'],
            [['penjamin_id'], 'integer'],
            [['diskon_otomatis'], 'number', 'min' => 0, 'max' => 100],
            [['is_active'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penjamin_id'          => \Yii::t('fe','Penjamin'),
            'diskon_otomatis'      => \Yii::t('fe','Diskon Otomatis'),
            'is_active'            => \Yii::t('fe','Status'),
        ];
    }
}
