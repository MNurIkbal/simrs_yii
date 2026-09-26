<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class BayaUangMukaPayload extends \Doco\components\DocoBaseModel
{
    public $pendaftaran_id;
    public $ruangan_id;
    public $jumlah_uangmuka;
    public $biayaadministrasi;
    public $tanggal_pembayaran;
    public $namapemilik_rek;
    public $no_rek;
    public $jenisnontunai_id;
    public $carapembayaran;

    protected $xssProtected = [
        'pendaftaran_id',
        'namapemilik_rek',
        'no_rek',
    ];
    
    public function rules()
    {
         return [
            [[
                'pendaftaran_id',
                'jumlah_uangmuka',
                'ruangan_id',
            ], 'required','message'=>'{attribute} Tidak Boleh Kosong'],
            [[
                'pendaftaran_id',
                'jumlah_uangmuka',
                'biayaadministrasi',
                'tanggal_pembayaran',
                'namapemilik_rek',
                'jenisnontunai_id',
                'no_rek',
                'ruangan_id',
                'carapembayaran',
            ], 'safe'],
            [['pendaftaran_id'], 'checkJenisTunai'],
            ['tanggal_pembayaran', 'datetime', 'format' => 'php:Y-m-d'],
            [[
                'jenisnontunai_id',
                'jumlah_uangmuka',
                'biayaadministrasi',
            ], 'integer', 'min' => 0]
        ];
    }

    public function checkJenisTunai($attributes, $params)
    {
        if (!empty($this->carapembayaran) && empty($this->jenisnontunai_id)) {
            $this->addError('jenisnontunai_id', 'Jenis Non Tunai tidak boleh kosong');
        }
    }

    public function attributeLabels()
    {
        return [
            'saldo_awal' => 'Saldo Awal',
            'shift_id' => 'Shift',
            'catatan' => 'Catatan'
        ];
    }
}
