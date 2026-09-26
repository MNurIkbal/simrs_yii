<?php

namespace app\modules\v1\payload;

use Yii;

class PasienAdmisi extends \yii\base\Model
{
    public $pegawai_id;
    public $kamarruangan_id;
    public $kamartempattidur_id;
    public $kamarruangan_nokamar;
    public $tgl_admisi;
    public $keterangan;
    public $is_pasientitipan;
    public $is_aps;
    public $kamar_titipan_id;
    public $kelas_ditagihkan_id;
    public $ruangan_titipan_id;
    public $tempattidur_titipan_id;
    // ranap v2
    public $dokterpengirim_id;
    public $hakkelas_id;
    public $kelaspermintaan_id;
    public $dokterkonsul_id;
    public $prosedurmasuk_id;
    public $diagnosa_awal;

    public function rules()
    {
        return [
            [

                [
                    'pegawai_id', 'kamarruangan_id', 'kamartempattidur_id', 'tgl_admisi', 'keterangan', 
                    'is_pasientitipan', 'is_aps', 'kelas_ditagihkan_id', 'ruangan_titipan_id', 'kamar_titipan_id',
                    'dokterpengirim_id', 'hakkelas_id', 'kelaspermintaan_id', 'dokterkonsul_id', 'prosedurmasuk_id', 'diagnosa_awal', 'tempattidur_titipan_id'
                ],
                'safe',
            ],
            [
                [
                    'pegawai_id', 'kamarruangan_nokamar', 'tgl_admisi'
                ],
                'required',
                'on' => 'default',
                'message' => '{attribute} Tidak Boleh Kosong',
            ],
            [
                [
                    'pegawai_id', 'kamarruangan_nokamar', 'tgl_admisi', 'dokterpengirim_id'
                ],
                'required', 
                'on' => 'pendaftaran-styp',
                'message' => '{attribute} Tidak Boleh Kosong',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Pegawai',
            'kamarruangan_id' => 'Kamar Ruangan',
            'kamartempattidur_id' => 'No Tempat Tidur',
            'kamarruangan_nokamar' => 'No Tempat Tidur',
            'tgl_admisi' => 'Tanggal Admisi',
            'prosedurmasuk_id' => 'Prosedur Masuk',
            'diagnosa_awal' => 'Diagnosa Awal',
        ];
    }

}
