<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisdarah_v".
 *
 * @property int $jenisdarah_id
 * @property string $jenisdarah_nama
 * @property int $lama_penyimpanan
 * @property int $suhu_penyimpanan
 * @property double $harga
 * @property bool $is_active
 * @property bool $is_deleted
 */
class JenisDarahView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenisdarah_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisdarah_id', 'lama_penyimpanan', 'suhu_penyimpanan'], 'default', 'value' => null],
            [['jenisdarah_id', 'lama_penyimpanan', 'suhu_penyimpanan'], 'integer'],
            [['harga'], 'number'],
            [['is_active', 'is_deleted'], 'boolean'],
            [['jenisdarah_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisdarah_id' => 'Jenisdarah ID',
            'jenisdarah_nama' => 'Jenisdarah Nama',
            'lama_penyimpanan' => 'Lama Penyimpanan',
            'suhu_penyimpanan' => 'Suhu Penyimpanan',
            'harga' => 'Harga',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
