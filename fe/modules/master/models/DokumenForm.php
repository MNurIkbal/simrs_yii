<?php

namespace app\modules\master\models;

use Yii;

class DokumenForm extends \yii\base\Model
{
    public $jenis_dokumen_id;
    public $nama_dokumen;
    public $nama_dokumen_lainnya;
    public $is_eklaim;
    public $is_active;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenis_dokumen_id'], 'integer'],
            [['jenis_dokumen_id', 'nama_dokumen'], 'required'],
            [['nama_dokumen_lainnya', 'nama_dokumen'], 'string'],
            [['jenis_dokumen_id', 'nama_dokumen', 'nama_dokumen_lainnya', 'is_eklaim', 'is_active'], 'safe'],
            [['is_eklaim'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenis_dokumen_id' => Yii::t('fe', 'Jenis Dokumen'),
        ];
    }
}
