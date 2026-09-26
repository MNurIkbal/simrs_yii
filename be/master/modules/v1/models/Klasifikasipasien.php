<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "klasifikasipasien_m".
 *
 */
class Klasifikasipasien extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'klasifikasipasien_nama',
        'klasifikasipasien_kode'
    ];
    public $kode_klasifikasi_pasien;
    public $status;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'klasifikasipasien_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['klasifikasipasien_nama','klasifikasipasien_kode'], 'required'],
            // [['klasifikasipasien_nama'], 'unique'],
            [['additional_data'], 'string'],
            [['klasifikasipasien_id','kode_klasifikasi_pasien','status'], 'safe'],
            [[ 'is_active'], 'boolean'],
            [['klasifikasipasien_kode'], 'chkKode'],
            [['klasifikasipasien_nama'], 'chkNama'],
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
            // 'kode_klasifikasi_pasien' => 'Kode Klasifikasi Pasien',
            'is_active' => 'Status',
        ];
    }

    public function chkKode($params, $attributes)
    {
        $kode_klasifikasi_pasien = $this->klasifikasipasien_kode;
        $rest = substr($this->klasifikasipasien_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("klasifikasipasien_kode", "Kode Klasifikasi mengandung spasi di awal kata");
            return false;
        } else {
            $model = self::find()->where(['LOWER (klasifikasipasien_kode)' => strtolower($this->klasifikasipasien_kode), 'is_deleted' => false])->one();
            if (!empty($model) && $model->klasifikasipasien_id != $this->klasifikasipasien_id) {
                $this->addError("klasifikasipasien_kode", "Kode Klasifikasi Sudah Dipakai");
                return false;
            }
        }

        return true;
    }

    public function chkNama($params, $attributes)
    {
        $klasifikasipasien_nama = $this->klasifikasipasien_nama;
        $rest = substr($this->klasifikasipasien_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("klasifikasipasien_nama", "Kode Klasifikasi mengandung spasi di awal kata");
            return false;
        } else {
            $model = self::find()->where(['LOWER (klasifikasipasien_nama)' => strtolower($this->klasifikasipasien_nama), 'is_deleted' => false])->one();
            if (!empty($model) && $model->klasifikasipasien_id != $this->klasifikasipasien_id) {
                $this->addError("klasifikasipasien_nama", "Nama Klasifikasi Sudah Dipakai");
                return false;
            }
        }

        return true;
    }
}
