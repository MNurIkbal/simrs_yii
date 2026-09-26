<?php

/**
* @author yaya
*
**/

namespace Doco\gudang\models;

use Yii;

class InfFormulirSoBarangForm extends \yii\base\Model
{
    public $tglformulir;
    public $noformulir;
    public $ruangan_id;
    public $instalasi_id;
    public $instalasi_nama;
    public $ruangan_nama;
    public $total_harganetto;
    public $stokopnamebarang_id;

    public $detail;

    public $inputan_so;
    public $total_harga_netto;
    public $total_harga_fisik;
    public $jenis_stok_opname;
    public $selisih_harga_netto;
    public $is_verifikasi;

    public function rules()
    {
        return [
            [[
                // 'total_harga_netto',
                // 'total_harga_fisik',
                // 'selisih_harga_netto',
                'inputan_so'
            ], 'required'],
            [[
                'tglformulir',
                'noformulir',
                'ruangan_id',
                'instalasi_id',
                'instalasi_nama',
                'ruangan_nama',
                'total_harganetto',
                'detail',
                'inputan_so',
                'total_harga_netto',
                'total_harga_fisik',
                'jenis_stok_opname',
                'selisih_harga_netto',
                'stokopnamebarang_id',
                'is_verifikasi'
            ],'safe']
        ];
    }

    public function validInputSo($attribute, $params)
    {
        $request = Yii::$app->request;
        $inputan_so = $request->post('inputan_so');
        if (!empty($inputan_so) && is_array($inputan_so)) {
            foreach ($inputan_so as $value) {
                $data = json_decode($value,true);
                if (empty($data['stok_fisik'])) {
                    $this->addError('inputan_so',Yii::t('fe','Stok Fisik tidak boleh kosong'));
                    return false;
                }

                if (empty($data['kondisi'])) {
                    $this->addError('inputan_so',Yii::t('fe','Kondisi tidak boleh kosong'));
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'total_harga_netto' => Yii::t('fe','Total harga netto sistem'),
            'total_harga_fisik' => Yii::t('fe','Total harga netto fisik'),
            'jenis_stok_opname' => Yii::t('fe','Jenis stok opname'),
            'selisih_harga_netto' => Yii::t('fe','Selisih harga netto'),
        ];
    }
}