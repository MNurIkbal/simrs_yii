<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 18:00:01
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-22 11:53:44
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class SkriningRajalForm extends \yii\base\Model
{
    public $skrining_pasien_rj_id;
    public $pendaftaran_id;
    public $kesadaran;
    public $pernapasan;
    public $resiko_jatuh;
    public $nyeri_dada;
    public $nyeri;
    public $batuk;
    public $keputusan;
    public $bahasa;
    public $bahasa_daerah;
    public $bahasa_asing;
    public $poli_tujuan;
    public $asal_rujukan;
    public $petugas_loket_pendaftaran;
    public $pasien_keluarga_pasien;
    public $bahasa_daerah_text;
    public $bahasa_asing_text;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'kesadaran', 'pernapasan', 'resiko_jatuh', 'nyeri_dada', 'batuk',
                    'keputusan'
                ], 
                'required', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'pendaftaran_id', 'petugas_loket_pendaftaran', 'pasien_keluarga_pasien', 'poli_tujuan', 'asal_rujukan', 'nyeri'
                ], 'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' =>  Yii::t('fe', 'Pendaftaran ID'),
            'kesadaran' =>  Yii::t('fe', 'Kesadaran'),
            'pernapasan' =>  Yii::t('fe', 'Pernapasan'),
            'resiko_jatuh' =>  Yii::t('fe', 'Resiko Jatuh'),
            'nyeri_dada' =>  Yii::t('fe', 'Nyeri Dada'),
            'nyeri' =>  Yii::t('fe', 'Nyeri'),
            'batuk' =>  Yii::t('fe', 'Batuk'), 
            'keputusan' =>  Yii::t('fe', 'Keputusan'),
            'bahasa' =>  Yii::t('fe', 'Bahasa Sehari-hari'),
            'bahasa_daerah' =>  Yii::t('fe', 'Daerah'),
            'bahasa_asing' =>  Yii::t('fe', 'Asing'),
            'poli_tujuan' =>  Yii::t('fe', 'Poli Tujuan'),       
            'asal_rujukan' =>  Yii::t('fe', 'Asal Rujukan'),   
            'nama_pasien' =>  Yii::t('fe', 'Nama Pasien')   
        ];
    }
}
