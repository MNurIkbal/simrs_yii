<?php

namespace Doco\bedah\models;

class JadwalOperasiForm extends \yii\base\Model
{
    public $catatan;
    public $kamarruangan_id;
    public $ruangan_id;
    public $pendaftaran_id;
    public $jenis_operasi_odc;
    const SCENARIO_TOLAK = 'tolak'; 
    const SCENARIO_APPROVE = 'approve'; 

    public function rules()
    {
        return [
            [[
                'catatan', 'kamarruangan_id', 'ruangan_id',
            ], 'required', 'on' => self::SCENARIO_APPROVE], 
            [['pendaftaran_id'], 'required','when' => function($model) {
                return $model->jenis_operasi_odc == 1;
            }, 'on' => self::SCENARIO_APPROVE],
            [[
                'catatan', 
            ], 'required', 'on' => self::SCENARIO_TOLAK], 
            [['catatan', 'kamarruangan_id', 'ruangan_id', 'pendaftaran_id', 'jenis_operasi_odc'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'catatan' => \Yii::t('fe', 'Catatan Persetujuan / Penolakan Jadwal Operasi'),
            'kamarruangan_id' => \Yii::t('fe', 'No Kamar Bedah'),
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'pendaftaran_id' => \Yii::t('fe', 'No Pendaftaran'),
        ];
    }
}
