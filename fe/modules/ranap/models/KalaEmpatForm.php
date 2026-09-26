<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class KalaEmpatForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public $persalinan_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $k4_keadaanumum;
    public $k4_td_systolic;
    public $k4_td_diastolic;
    public $k4_detaknadi;
    public $k4_pernapasan;
    public $k4_masalah;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'k4_keadaanumum',
                'k4_td_systolic',
                'k4_td_diastolic',
                'k4_detaknadi',
                'k4_pernapasan',
                'k4_masalah',
            ],'safe'],
            [[
                'k4_td_systolic',
                'k4_td_diastolic',
                'k4_detaknadi',
                'k4_pernapasan'
            ],'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'k4_keadaanumum' => Yii::t('fe', 'Keadaan Umum'),
            'k4_td_systolic' => Yii::t('fe', 'Tekanan Darah Systolic'),
            'k4_td_diastolic' => Yii::t('fe', 'Tekanan Darah Diastolic'),
            'k4_detaknadi' => Yii::t('fe', 'Detak Nadi'),
            'k4_pernapasan' => Yii::t('fe', 'Pernapasan'),
            'k4_masalah' => Yii::t('fe', 'Masalah'),
        ];
    }
}
