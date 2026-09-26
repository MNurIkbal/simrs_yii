<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "marginkhusus_v".
 *
 * @property int $marginkhusus_id
 * @property string $nama
 * @property string $perda
 * @property string $mulai_berlaku
 * @property bool $is_active
 */
class MarginKhususView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'marginkhusus_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['marginkhusus_id'], 'default', 'value' => null],
            [['marginkhusus_id'], 'integer'],
            [['mulai_berlaku'], 'safe'],
            [['is_active'], 'boolean'],
            [['perda','nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'marginkhusus_id' => 'Margin Khusus ID',
            'perda' => 'Perda Margin',
            'mulai_berlaku' => 'Tgl Berlaku',
            'is_active' => 'Is Active'
        ];
    }
}
