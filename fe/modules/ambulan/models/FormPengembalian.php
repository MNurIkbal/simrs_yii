<?php

namespace app\modules\ambulan\models;

use Yii;

class FormPengembalian extends \yii\base\Model
{
    
     public $pemakaianambulan_id;
     public $tgl_kembali;
     public $km_akhir;
     public $biaya_tambahan;
     public $total_biaya;

     /** Get Object From API */
     public $biaya_pemakaian;
     public $tgl_pemakaiandari;
     public $no_polisi;
     public $km_awal;
     public $nominal_tagihan;
     public $pendaftaran_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pemakaianambulan_id',
                'tgl_kembali',
                'km_akhir',
                'biaya_tambahan',
                'tgl_pemakaiandari',
                'no_polisi',
                'km_awal',
                'nominal_tagihan',
                'pendaftaran_id',
                'total_biaya',
                'biaya_pemakaian',
            ],'safe'],
            [['pemakaianambulan_id','tgl_kembali','km_akhir'],'required'],
            [['km_akhir'], 'validasiKm']
        ];
    }

    public function validasiKm($params, $attributes)
    {
        if ($this->km_akhir < $this->km_awal) {
            $this->addError('km_akhir', 'Km Akhir tidak boleh kecil dari Km Awal');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_kembali' => 'Tindakan Kembali',
            'km_akhir' => 'Km Akhir',
            'biaya_tambahan' => 'Biaya Tambahan',
        ];
    }
}
