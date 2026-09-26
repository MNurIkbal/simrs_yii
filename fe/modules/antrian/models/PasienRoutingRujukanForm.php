<?php

namespace app\modules\antrian\models;

use Yii;

/**
 * @property int $nomor
 * @property int $ruangan_id
 * @property int $dokter_id
 * @property string $nama_poli
 * @property string $nama_dokter
 *
 */

class PasienRoutingRujukanForm extends \yii\base\Model
{
    public $nomor; // pendaftaran / rm
    public $dokter_id;
    public $ruangan_id;
    public $nama_poli;
    public $nama_dokter;
    public $konsulpoli_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'nomor',
                'dokter_id',
                'ruangan_id',
                'konsulpoli_id',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'nama_dokter',
                'nama_poli',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dokter_id' => 'Nama Dokter',
            'ruangan_id' => 'Poli',
            'nomor' => 'Nomor Rekam Medik / Nomor Pendaftaran / Nomor BPJS',
            'konsulpoli_id' => 'Konsul Poli',
        ];
    }

}
