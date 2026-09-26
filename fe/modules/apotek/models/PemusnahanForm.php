<?php


namespace Doco\apotek\models;

use Yii;

class PemusnahanForm extends \yii\base\Model
{
    public $tanggal_pemusnahan;
    public $pegawai_meyetujui;
    public $pegawai_mengetahui;
    public $total_netto;

    public $detail;

    public function rules()
    {
        return [
            [[
                'tanggal_pemusnahan',
                'pegawai_meyetujui',
                'pegawai_mengetahui',
            ], 'required', 'message' => '{attribute} '.\Yii::t('fe', 'Tidak Boleh Kosong!')],
            [['tanggal_pemusnahan','pegawai_meyetujui','pegawai_mengetahui','total_netto'],'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
                'pegawai_meyetujui' => \Yii::t('fe', 'Pegawai Menyetujui'),
                'pegawai_mengetahui' => \Yii::t('fe', 'Pegawai Mengetahui')
            ];
    }
}