<?php

namespace app\modules\v1\payloads;

use Yii;
use Doco\components\DocoBaseModel;

class ResepturPayload extends DocoBaseModel
{
    public $ruangan_id;
    public $pendaftaran_id;
    public $tglreseptur;
    public $ruanganreseptur_id;
    public $racikan_id;
    public $pasien_id;
    public $pegawai_id;
    public $diagnosa_id;
    public $berat_badan;
    public $tinggi_badan;
    public $luas_tubuh;
    public $is_hamil;
    public $catatan;
    public $penjamin_id;
    public $kelaspelayanan_id;
    public $carabayar_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'ruangan_id', 
                'pendaftaran_id', 
                'tglreseptur', 
                'ruanganreseptur_id',
                'racikan_id',
            ], 'required'],
            [['tglreseptur'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [[
                'ruangan_id', 
                'pendaftaran_id', 
                'tglreseptur', 
                'ruanganreseptur_id',
                'racikan_id',
                'pasien_id',
                'pegawai_id',
                'diagnosa_id',
                'berat_badan',
                'tinggi_badan',
                'luas_tubuh',
                'is_hamil',
                'catatan',
                'penjamin_id',
                'kelaspelayanan_id',
                'carabayar_id',
            ],'safe'],
            [[
                'ruangan_id', 
                'pendaftaran_id', 
                'ruanganreseptur_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'carabayar_id',
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
