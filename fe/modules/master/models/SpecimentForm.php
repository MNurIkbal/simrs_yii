<?php

namespace app\modules\master\models;

use Yii;

class SpecimentForm extends \yii\base\Model
{
    public $kode_sample;
    public $nama_sample;
    public $is_active;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kode_sample','nama_sample'], 'required'],
            [['nama_sample'], 'trimWhitespace'],
            [['kode_sample','nama_sample','is_active'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kode_sample' => Yii::t('fe','Kode sample'),
            'nama_sample' => Yii::t('fe','Nama Sample')
        ];
    }
    public function trimWhitespace(){
        $nama_sample = $this->nama_sample;
        if (strpos(substr($nama_sample, 0, 1), ' ') !== FALSE) {
            $this->addError('nama_sample', 'Nama sample mengandung spasi di awal kata');
            return false;
        }
        return true;
    }

}