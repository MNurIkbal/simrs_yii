<?php

namespace app\modules\master\models;

use Yii;

class SatuanForm extends \yii\base\Model
{
    public $satuanlab_kode;
    public $satuanlab_nama;
    public $is_active;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['satuanlab_kode','satuanlab_nama'], 'required'],
            [['satuanlab_nama'], 'trimWhitespace'],
            [['satuanlab_kode','satuanlab_nama','is_active'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'satuanlab_kode' => 'Kode',
            'satuanlab_nama' => Yii::t('fe','Nama Satuan'),
        ];
    }

    public function trimWhitespace(){
        $satuanlab_nama = $this->satuanlab_nama;
        if (strpos(substr($satuanlab_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('satuanlab_nama', 'Nama satuan mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
}