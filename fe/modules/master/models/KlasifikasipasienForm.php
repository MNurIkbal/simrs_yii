<?php

namespace app\modules\master\models;

use Yii;
/**
 * This is the model class for table "klasifikasipasien_m".
 *
 */
class KlasifikasipasienForm extends \yii\base\Model
{

    public $klasifikasipasien_id;
    public $klasifikasipasien_nama;
    public $klasifikasipasien_kode;
    public $is_active;


    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'klasifikasipasien_m';
    }

    public function rules()
    {
        return [
            [['klasifikasipasien_nama','klasifikasipasien_kode'], 'required'],
            [['klasifikasipasien_id'], 'safe'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'klasifikasipasien_id' => 'Klasifikasi Pasien ID',
            'klasifikasipasien_nama' => 'Nama Klasifikasi Pasien',
            'klasifikasipasien_kode' => 'Kode Klasifikasi Pasien',
            'is_active' => 'Aktif',
        ];
    }
}
