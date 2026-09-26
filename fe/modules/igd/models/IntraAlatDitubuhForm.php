<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 17:14:48
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 10:06:13
 */

namespace Doco\igd\models;

class IntraAlatDitubuhForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $jenis_alat;
    public $jenis_alat_nama;
    public $jumlah;
    public $lokasi;

    public function rules()
    {
        return [
            [['jenis_alat'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['jumlah'], 'number', 'message'=>'{attribute} Harus berupa angka'],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id','jenis_alat_nama','jumlah', 'lokasi'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'jenis_alat'=>\Yii::t('fe', 'Jenis alat'),
            'jumlah'=>\Yii::t('fe', 'Jumlah'),
            'lokasi'=>\Yii::t('fe', 'Lokasi'),
        ];
    }
}