<?php

namespace app\modules\v1\payload;

use Yii;


class Penunjang extends \yii\base\Model
{
    public $kelaspelayanan_id;
    public $jeniskasuspenyakit_id;
    public $pasienadmisi_id;
    public $pegawai_id;
    public $ruangan_id;
    public $pasien_id;
    public $pendaftaran_id;
    public $ruanganasal_id;
    public $no_masukpenunjang;
    public $tglmasukpenunjang;
    public $no_antrian;
    public $kunjungan;
    public $status_periksa;

    public function rules()
    {
        return [
            [[
                'kelaspelayanan_id', 
                'jeniskasuspenyakit_id', 
                'ruangan_id', 
                'pasien_id', 
                'ruanganasal_id', 
                'tglmasukpenunjang',
                'kunjungan'
            ], 'required'],
            [[
                'kelaspelayanan_id',
                'jeniskasuspenyakit_id',
                'pasienadmisi_id',
                'pegawai_id',
                'ruangan_id',
                'pasien_id',
                'pendaftaran_id',
                'ruanganasal_id',
                'no_masukpenunjang',
                'tglmasukpenunjang',
                'no_antrian',
                'kunjungan',
                'status_periksa'
            ], 'safe'],
            [['no_masukpenunjang'], 'string', 'max' => 20],
            [['kunjungan', 'status_periksa'], 'string', 'max' => 50]
        ];
    }

}