<?php
//author: ijal

namespace app\modules\igd\models;

use Yii;

class KesimpulanKeluarForm extends \yii\base\Model
{
    public $kesimpulanrd_id;
    public $pendaftaran_id;
    public $pasienpulang_id;

    public $kondisi;
    public $hr;
    public $rr;
    public $spo2;
    public $t;
    public $gcs_eye_id;
    public $gcs_verbal_id;
    public $gcs_motorik_id;
    public $hasil_gcs;
    public $gcs_kategori;
    public $is_kapitis;
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id', 
                    'gcs_eye_id', 'gcs_verbal_id', 'gcs_motorik_id', 
                ], 
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'kondisi', 'hr', 'rr', 'spo2', 't', 'hasil_gcs', 
                    'gcs_kategori', 'is_kapitis'
                ], 
                'safe'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gcs_eye_id' => Yii::t('fe', 'GCS eye'),
            'gcs_verbal_id' => Yii::t('fe', 'GCS verbal'),
            'gcs_motorik_id' => Yii::t('fe', 'GCS motorik'),
            'hasil_gcs' => Yii::t('fe', ''),
            'gcs_kategori' => Yii::t('fe', 'Hasil metode GCS'),
            'is_kapitis' => Yii::t('fe', 'Kapitis'),
            'kondisi' => Yii::t('fe', 'Kondisi / masalah'),
            'hr' => Yii::t('fe', 'HR'),
            'rr' => Yii::t('fe', 'RR'),
            'spo2' => Yii::t('fe', 'SpO2'),
            't' => Yii::t('fe', 'T'),
            
        ];
    }
}
