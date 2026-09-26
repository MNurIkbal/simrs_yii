<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tempattidur_v".
 *
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $kettempattidur_nama
 * @property bool $status_isi
 */
class TempatTidurView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tempattidur_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id','no_tempattidur','kamarruangan_id','status_isi'], 'required'],
            [['kamarruangan_id', 'ruangan_id',], 'default', 'value' => null],
            [['kamarruangan_id', 'ruangan_id'], 'integer'],
            [['status_isi'], 'boolean'],
            [['ruangan_nama'], 'string', 'max' => 50],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur', 'kettempattidur_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kamarruangan_id' => 'Kamar',
            'ruangan_id' => 'Ruangan',
            'ruangan_nama' => 'Ruangan',
            'kamarruangan_nokamar' => 'Kamar',
            'no_tempattidur' => 'No Tempat Tidur',
            'kettempattidur_nama' => 'Keterangan Tempat Tidur',
            'status_isi' => 'Status Tempat Tidur',
        ];
    }
}
