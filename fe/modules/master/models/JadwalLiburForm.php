<?php

namespace app\modules\master\models;

use Yii;
use app\components\DocoBaseModel;

class JadwalLiburForm extends DocoBaseModel
{
    public $jadwallibur_id;
    public $tgl_libur;
    public $ket_libur;
    public $is_liburnasional;
    public $additional_data;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'tgl_libur',
                'ket_libur',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'jadwallibur_id',
                'is_liburnasional',
                'additional_data',
                'tgl_libur',
                'ket_libur',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwallibur_id' => Yii::t('fe','Jadwal Libur ID'),
            'is_liburnasional' => Yii::t('fe',''),
            'additional_data' => Yii::t('fe','Additional Data'),
            'tgl_libur' => Yii::t('fe','Tanggal Libur'),
            'ket_libur' => Yii::t('fe','Keterangan Libur'),
        ];
    }
}