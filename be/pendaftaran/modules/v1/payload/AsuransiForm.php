<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class AsuransiForm extends \Doco\components\DocoBaseModel
{
    public $nokartuasuransi;
    public $namapemilikasuransi;
    public $nomorpokokperusahaan;
    public $kelastanggungan_id;
    public $namaperusahaan;
    public $tgl_konfirmasi;
    public $penjamin_id;
    public $pasien_id;
    public $carabayar_id;
    public $status_konfirmasi;
    public $asuransipasien_id;
    public $masaberlakukartu;
    public $nama_asuransi;
    public $penjamingrade_id;

    protected $xssProtected = [
        'nokartuasuransi',
        'namapemilikasuransi',
        'nomorpokokperusahaan',
        'namaperusahaan'
    ];
    
    public function rules()
    {
         return [
            ['nokartuasuransi', 'filter', 'filter' => function ($value) {
                $exp = explode(" - ", $value);
                $noKartu = isset($exp[0]) ? $exp[0] : $value;
                return $noKartu;
            }],
            [[
                'namapemilikasuransi', 'nokartuasuransi'
            ], 'required','message'=>'{attribute} Tidak Boleh Kosong'],
            [[
                'nomorpokokperusahaan', 
                'kelastanggungan_id',
                'namaperusahaan',
                'tgl_konfirmasi',
                'status_konfirmasi',
                'penjamin_id',
                'pasien_id',
                'carabayar_id',
                'nokartuasuransi',
                'asuransipasien_id',
                'masaberlakukartu',
                'nama_asuransi',
                'penjamingrade_id'
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nokartuasuransi' => 'Nomor Asuransi',
            'namapemilikasuransi' => 'Nama Pemilik',
            'nomorpokokperusahaan' => 'Nomor Pokok Perusahaan',
            'kelastanggungan_id' => 'Kelas Tanggungan',
            'namaperusahaan' => 'Nama Perusahaan',
            'tgl_konfirmasi' => 'Tanggal Konfirmasi',
            'status_konfirmasi' => 'Telah konfirmasi',
            'masaberlakukartu' => 'Masa Berlaku',
            'nama_asuransi' => 'Nama Asuransi',
            'penjamingrade_id' => 'Penjamin Grade',
        ];
    }
}
