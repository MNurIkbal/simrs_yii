<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 *
 * @property string $no_kartu
 * @property string $jenis_pelayanan
 * @property string $jenis_pengajuan
 * @property string $keterangan
 * @property date $tgl_sep
 *
 */

class PengajuanSepForm extends \yii\base\Model
{
    public $no_kartu;
    public $tgl_sep;
    public $jenis_pelayanan;
    public $jenis_pengajuan;
    public $keterangan;
    public $username;
    public $pendaftaran_id;
    public $nama_pasien;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'no_kartu', 
                'tgl_sep', 
                'jenis_pelayanan',
                'jenis_pengajuan',
                'username',
                'keterangan'
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [[
                'no_kartu', 
                'tgl_sep', 
                'jenis_pelayanan',
                'jenis_pengajuan',
                'keterangan',
                'username',
                'pendaftaran_id'
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'no_kartu' => 'No Kartu',
            'tgl_sep' => 'Tanggal SEP',
            'jenis_pelayanan' => 'Jenis Pelayanan',
            'jenis_pengajuan' => 'Jenis Pengajuan',
            'keterangan' => 'Keterangan',
        ];
    }

}
