<?php

namespace app\modules\penjaminasuransi\models;

use Yii;

class InformasiPengajuanForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $tgl_jatuhtempo;
    public $catatan;
    public $total_pengajuan;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_jatuhtempo'], 'required'],
            [['catatan'], 'string', 'max' => 200],
            [[
                'tgl_jatuhtempo',
                'catatan',
                'total_pengajuan'
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_jatuhtempo' => 'Tanggal jatuh tempo',
            'catatan' => 'Catatan',
            'total_pengajuan' => 'Total Pengajuan',
        ];
    }
}
