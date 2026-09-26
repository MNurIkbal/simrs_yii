<?php
namespace app\modules\pendaftaran\models;

use Yii;

class PasienBpjsForm extends \yii\base\Model
{
    public $jenis_pencarian;
    public $no_kartu;
    public $jenis_kartu;
    public $jenis_pelayanan;
    public $tanggal_sep;
    public $asal_rujukan;
    public $no_rujukan_f;
    public $nama_peserta_bpjs;
    public $nomorpokokperusahaan_bpjs;
    public $namaperusahaan_bpjs;
    
    public function rules()
    {
        return [
            [[
                'jenis_pencarian',
                'tanggal_sep',
                'asal_rujukan',
                // 'no_rujukan_f',
            ],'required', 'on' => 'rujukan'],
            [[
                'jenis_pencarian',
                'tanggal_sep',
                'jenis_pelayanan',
                'jenis_kartu',
                'no_kartu',
            ],'required', 'on' => 'rujukan-manual'],
            [[
                'no_kartu',
            ],'required', 'on' => 'skip-bpjs'],
            [[
                'jenis_pencarian',
                'tanggal_sep',
                'jenis_pelayanan',
                'jenis_kartu',
                'no_kartu',
                'nama_peserta',
            ],'required', 'on' => 'rujukan-manual-penunjang'],
            [[
                'no_kartu',
            ],'required', 'on' => 'skip-bpjs'],
            [[
                'jenis_pencarian',
                'no_kartu',
                'jenis_kartu',
                'jenis_pelayanan',
                'tanggal_sep',
                'no_rujukan_f',
                'nama_peserta',
                'nomorpokokperusahaan',
                'namaperusahaan',
            ],'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'jenis_pencarian' => 'Jenis Pencarian',
            'tanggal_sep' => 'Tanggal SEP',
            'jenis_pelayanan' => 'Pelayanan',
            'jenis_kartu' => 'Jenis Kartu',
            'no_kartu' => 'No Kartu',
            'no_rujukan_f' => 'No Rujukan',
            'nama_peserta' => 'Nama Peserta',
            'nomorpokokperusahaan' => 'Nomor Pokok Perusahaan',
            'namaperusahaan' => 'Nama Perusahaan',
        ];
    }
}