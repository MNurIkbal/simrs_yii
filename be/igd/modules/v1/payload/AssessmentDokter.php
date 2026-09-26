<?php

namespace app\modules\v1\payload;

use Yii;
use Doco\components\DocoBaseModel;
use Doco\components\DocoConstants;

class AssessmentDokter extends DocoBaseModel
{
    public $dokter_id;
    public $pendaftaran_id;
    public $asesmenmedisrd_id;
    public $dokter_jaga;
    public $tgl_asesmen;
    public $jenis_asmenperawat;
    public $riwayat;
    public $riwayat_dahulu;
    public $gcseye_id;
    public $gcsverbal_id;
    public $gcsmotorik_id;
    public $jumlah_gcs;
    public $is_kapitis;
    public $hasil_gcs;
    public $anatomi;

    protected $xssProtected = [
        'dokter_jaga',
        'riwayat',
        'diagnosakerja_id',
        'hasil_gcs'
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dokter_id', 'pendaftaran_id', 'jenis_asmenperawat'], 'required'],
            [['tgl_asesmen'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [[
                'dokter_id',
                'pendaftaran_id',
                'asesmenmedisrd_id',
                'dokter_jaga',
                'tgl_asesmen',
                'jenis_asmenperawat',
                'riwayat',
                'riwayat_dahulu',
                'gcseye_id',
                'gcsverbal_id',
                'gcsmotorik_id',
                'jumlah_gcs',
                'is_kapitis',
                'hasil_gcs',
                'anatomi'
            ],'safe'],
            [[
                'dokter_id', 
                'pendaftaran_id', 
                'asesmenmedisrd_id', 
                'gcseye_id', 
                'gcsverbal_id', 
                'gcsmotorik_id', 
                'jumlah_gcs', 
                'is_kapitis', 
                'jenis_asmenperawat', 
            ], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tgl_batal' => 'Tanggal Batal',
            'tgl_selesaikonsul' => 'Tanggal Selesai Konsul'
        ];
    }
}
