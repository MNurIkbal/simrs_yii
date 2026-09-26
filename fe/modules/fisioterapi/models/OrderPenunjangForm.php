<?php

namespace app\modules\fisioterapi\models;

use Yii;
use app\components\DocoConstants;

class OrderPenunjangForm extends \yii\base\Model
{
    public $programterapi_id;
    public $pegawai_id;
    public $instalasi_id;
    public $ruangan_id;
    public $tgl_kirimpasien;
    public $catatan_dokterpengirim;
    public $frekuensi_terapi;
    public $diagnosis;
    public $a_diag_penyerta;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'frekuensi_terapi'
                ],
                'required'
            ],
            [
                [
                    'frekuensi_terapi'
                ],
                'integer', 'max' => 10, 'min' => 1
            ],
            [
                [
                    'programterapi_id',
                    'pegawai_id',
                    'instalasi_id',
                    'ruangan_id',
                    'tgl_kirimpasien',
                    'catatan_dokterpengirim',
                    'frekuensi_terapi',
                    'diagnosis',
                    'a_diag_penyerta',
                ],
                'safe'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Pegawai',
            'instalasi_id' => 'Unit penunjang',
            'ruangan_id' => 'Ruangan tujuan',
            'tgl_kirimpasien' => 'Tanggal permintaan',
            'catatan_dokterpengirim' => 'Keterangan Klinis',
            'frekuensi_terapi' => 'Frekuensi Terapi',
            'a_diag_penyerta' => 'Diagnosa Penyerta',
        ];
    }
}
