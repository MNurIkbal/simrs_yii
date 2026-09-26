<?php

/**
 * @author: [Fajar Supriadi][fajar.supriadi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class PenanggungBiayaForm extends \Doco\components\DocoBaseModel
{
    public $penanggungbiaya_nama;
    public $namabagian;
    public $noindukkaryawan;
    public $jpkm;
    public $instansi;
    public $pasien_id;
    public $carabayar_id;
    public $penanggungbiaya_id;
    public $ruangcarabayar_id;

    public function rules()
    {
         return [
            [[
                'penanggungbiaya_nama', 
                'namabagian', 
                'noindukkaryawan', 
                'jpkm', 
                'instansi',
                'pasien_id',
                'carabayar_id',
                'penanggungbiaya_id',
                'ruangcarabayar_id',
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'penanggungbiaya_nama' => \Yii::t('fe', 'Nama'),
            'namabagian' => \Yii::t('fe', 'Nama Bagian'),
            'noindukkaryawan' => \Yii::t('fe', 'NIK'),
            'jpkm' => \Yii::t('fe', 'JPKM'),
            'instansi' => \Yii::t('fe', 'instansi'),
            'pasien_id' => \Yii::t('fe', 'Pasien ID'),
            'penanggungbiaya_id' => \Yii::t('fe', 'Penanggung Biaya ID'),
            'carabayar_id' => \Yii::t('fe', 'Carabayar ID'),
            'ruangcarabayar_id' => \Yii::t('fe', 'Ruang Carabayar ID'),
        ];
    }
}
