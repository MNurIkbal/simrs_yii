<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "paketdetail_v".
 *
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property string $daftartindakan_nama
 * @property int $daftartindakan_id
 * @property bool $is_deleted
 */
class PaketDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paketdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipepaket_id', 'daftartindakan_id'], 'default', 'value' => null],
            [['tipepaket_id', 'daftartindakan_id'], 'integer'],
            [['is_deleted'], 'boolean'],
            [['tipepaket_nama'], 'string', 'max' => 50],
            [['daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpptDetail()
    {
        return $this->hasOne(CpptDetailView::className(), ['tipepaket_id' => 'tipepaket_id']);
    }
}
