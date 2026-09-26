<?php

namespace app\modules\v1\payload;

use Yii;


class Antrian extends \yii\base\Model
{
    public $pasien_id;
    public $ruangan_id;
    public $carabayar_id;
    public $pendaftaran_id;
    public $tgl_antrian;
    public $no_antrian;
    public $penjamin_id;
    public $pegawai_id;
    public $status_pasien;
    public $jenisantrian_id;
    public $jadwaldokter_id;
    public $jadwalbukapoli_id;
    public $groupcarabayar_id;
    public $estimasidilayani;
    public $slot_sequence;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'pasien_id',
                'ruangan_id',
                'carabayar_id',
                'pendaftaran_id',
                'tgl_antrian',
                'no_antrian',
                'penjamin_id',
                'pegawai_id',
                'status_pasien',
                'jenisantrian_id',
                'jadwaldokter_id',
                'jadwalbukapoli_id',
                'groupcarabayar_id',
                'estimasidilayani',
                'slot_sequence'
            ],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }
}
