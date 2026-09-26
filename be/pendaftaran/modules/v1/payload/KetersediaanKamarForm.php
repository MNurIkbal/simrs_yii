<?php

/**
 * @author: [Fajar Supriad][fajar.supriadi@sirs.co.id]
 * A product of Sirs
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class KetersediaanKamarForm extends \Doco\components\DocoBaseModel
{
    public $pendaftaran_id;
    public $pasien_id;
    public $ruangan_id;
    public $kelaspelayanan_id;
    public $kamarruangan_id;
    public $kamartempattidur_id;

    public function rules()
    {
        return [
            [[
                'pendaftaran_id', 
                'pasien_id', 
                'ruangan_id', 
                'kelaspelayanan_id', 
                'kamarruangan_id',
                'kamartempattidur_id',
            ], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'ruangan_id' => 'Ruangan',
            'kelaspelayanan_id' => 'Kelas Pelayanan',
            'kamarruangan_id' => 'Kamar Ruangan',
            'kamartempattidur_id' => 'Kamar Tempat Tidur',
        ];
    }


}
