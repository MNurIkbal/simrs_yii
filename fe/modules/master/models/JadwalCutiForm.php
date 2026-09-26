<?php

namespace app\modules\master\models;

use Yii;
use app\components\DocoBaseModel;

class JadwalCutiForm extends DocoBaseModel
{
    public $jadwalcuti_id;
    public $pegawai_id;
    public $spesialis_id;
    public $spesialis_nama;
    public $ruangan_id;
    public $tgl_cuti_awal;
    public $tgl_cuti_akhir;
    public $alasan_cuti;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'pegawai_id',
                'ruangan_id',
                'tgl_cuti_awal',
                'tgl_cuti_akhir',
                'alasan_cuti',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'jadwalcuti_id',
                'pegawai_id',
                'ruangan_id',
                'spesialis_id',
                'spesialis_nama',
                'tgl_cuti_awal',
                'tgl_cuti_akhir',
                'alasan_cuti',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwalcuti_id' => Yii::t('fe','Jadwal Cuti ID'),
            'pegawai_id' => Yii::t('fe','Dokter'),
            'ruangan_id' => Yii::t('fe','Ruangan'),
            'spesialis_id' => Yii::t('fe','Spesialis ID'),
            'spesialis_nama' => Yii::t('fe','Spesialis'),
            'tgl_cuti_awal' => Yii::t('fe','Tanggal Cuti Awal'),
            'tgl_cuti_akhir' => Yii::t('fe','Tanggal Cuti Akhir'),
            'alasan_cuti' => Yii::t('fe','Alasan Cuti'),
        ];
    }

}
