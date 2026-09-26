<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class KalaEmpatDetailForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public $persalinan_id;
    public $pendaftaran_id;
    public $jam_ke;
    public $waktu;
    public $td_systolic;
    public $td_diastolic;
    public $detak_nadi;
    public $suhu;
    public $tinggi_fundus;
    public $kontraksi_uterus;
    public $kandung_kemih;
    public $darah_keluar;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'persalinan_id',
                'pendaftaran_id',
                'jam_ke',
                'waktu',
                'td_systolic',
                'td_diastolic',
                'detak_nadi',
                'suhu',
                'tinggi_fundus',
                'kontraksi_uterus',
                'kandung_kemih',
                'darah_keluar',
            ],'safe'],
            [[
                'jam_ke',
                'pendaftaran_id',
                'waktu',
                'td_systolic',
                'detak_nadi',
                'suhu'
            ],'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jam_ke' => Yii::t('fe', 'Jam Ke'),
            'waktu' => Yii::t('fe', 'Waktu'),
            'td_systolic' => Yii::t('fe', 'Tekanan Darah Systolic'),
            'td_diastolic' => Yii::t('fe', 'Tekanan Darah Diastolic'),
            'detak_nadi' => Yii::t('fe', 'Detak Nadi'),
            'suhu' => Yii::t('fe', 'Suhu'),
            'tinggi_fundus' => Yii::t('fe', 'Tinggi Fundus Uteri'),
            'kontraksi_uterus' => Yii::t('fe', 'Kontraksi Uterus'),
            'kandung_kemih' => Yii::t('fe', 'Kantung Kemih'),
            'darah_keluar' => Yii::t('fe', 'Darah yang Keluar'),
        ];
    }
}
