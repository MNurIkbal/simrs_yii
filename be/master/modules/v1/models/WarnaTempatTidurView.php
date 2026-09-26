<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kettempattidur_v".
 *
 * @property integer $kettempattidur_id
 * @property string $kettempattidur_nama
 * @property string $kettempattidur_warna
 * @property string $kode_warna
 * @property string $rgb
 * @property boolean $is_kosong
 * @property string $jenis
 */
class WarnaTempatTidurView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kettempattidur_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kettempattidur_id' , 'kettempattidur_nama', 'kettempattidur_warna' ,'kode_warna' ,'rgb', 'is_kosong','jenis'], 'default', 'value' => null],
            [['kettempattidur_id'], 'integer'],
            [['is_kosong'], 'boolean'],
            [['kettempattidur_nama', 'kettempattidur_warna', 'kode_warna' ,'rgb', 'kamarruangan_jenis'], 'string'],
            [['kettempattidur_warna', 'rgb'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kettempattidur_id' => Yii::t('app', 'Keterangan Tempat Tidur ID'),
            'kettempattidur_nama' => Yii::t('app', 'Nama Tempat Tidur'),
            'kettempattidur_warna' => Yii::t('app', 'Nama Warna Tempat Tidur'),
            'kode_warna' => Yii::t('app', 'Kode Warna'),
            'rgb' => Yii::t('app', 'Warna RGB'),
            'jenis' => Yii::t('app', 'Jenis'),
            'is_kosong' => Yii::t('app', 'Is Kosong'),
        ];
    }
}
