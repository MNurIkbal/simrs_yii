<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigmargin_v".
 *
 * @property int $konfigmargin_id
 * @property int $konfigmargindetail_id
 * @property string $perda_margin
 * @property string $tgl_berlaku
 * @property bool $is_active
 * @property double $harga_min
 * @property double $harga_max
 * @property double $margin
 * @property bool $is_activedetail
 */
class KonfigMarginView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfigmargin_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['konfigmargin_id', 'konfigmargindetail_id'], 'default', 'value' => null],
            [['konfigmargin_id', 'konfigmargindetail_id'], 'integer'],
            [['tgl_berlaku'], 'safe'],
            [['is_active', 'is_activedetail'], 'boolean'],
            [['harga_min', 'harga_max', 'margin'], 'number'],
            [['perda_margin'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigmargin_id' => 'Konfigmargin ID',
            'konfigmargindetail_id' => 'Konfigmargindetail ID',
            'perda_margin' => 'Perda Margin',
            'tgl_berlaku' => 'Tgl Berlaku',
            'is_active' => 'Is Active',
            'harga_min' => 'Harga Min',
            'harga_max' => 'Harga Max',
            'margin' => 'Margin',
            'is_activedetail' => 'Is Activedetail',
        ];
    }
}
