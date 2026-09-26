<?php
//author: ijal

namespace app\modules\igd\models;

use Yii;

class KesimpulanPulangForm extends \yii\base\Model
{
    public $kesimpulanrd_id;
    public $pendaftaran_id;
    public $pasienpulang_id;

    public $instruksi_lanjutan;
    public $tgl_lanjut_rawat;
    public $poliklinik_id;
    public $dokter_id;
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id', 
                    'instruksi_lanjutan', 'tgl_lanjut_rawat', 'poliklinik_id',
                    'dokter_id' 
                ], 
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'instruksi_lanjutan' => Yii::t('fe', 'Instruksi lanjutan'),
            'tgl_lanjut_rawat' => Yii::t('fe', 'Perawatan lanjutan'),
            'poliklinik_id' => Yii::t('fe', 'Poliklinik'),
            'dokter_id' => Yii::t('fe', 'Dokter'),
        ];
    }
}
