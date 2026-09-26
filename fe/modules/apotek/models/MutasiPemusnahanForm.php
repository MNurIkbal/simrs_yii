<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-12-18 13:22:12
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-18 18:00:52
 */

namespace Doco\apotek\models;

use Yii;

class MutasiPemusnahanForm extends \yii\base\Model
{
    public $tglmutasi;
    public $pegawai_mengetahui;
    public $ruangan_penerima_id;
    public $ruangan_id;
    public $totalharga_jual;
    public $totalharganetto_mutasi;
    public $detail;

    public function rules()
    {
        return [
            [[
                'tglmutasi',
                'pegawai_mengetahui',
            ], 'required', 'message' => '{attribute} '.\Yii::t('fe', 'Tidak Boleh Kosong!')],
            [['tglmutasi','pegawai_mengetahui', 'ruangan_id', 'ruangan_penerima_id', 'totalharga_jual', 'totalharganetto_mutasi'],'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
                'pegawai_mengetahui' => \Yii::t('fe', 'Pegawai Mengetahui'),
                'tglmutasi' => \Yii::t('fe', 'Tanggal Mutasi')
            ];
    }
}