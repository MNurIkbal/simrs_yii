<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-21 15:18:41
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-21 16:03:44
 */

namespace Doco\igd\models;

class PostTambahInfusForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $jeniscairan_id;
    public $jeniscairan_nama;
    public $tgl_pemasangan;
    public $jumlah_tetes;

    public function rules()
    {
        return [
            [['jeniscairan_id'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id','jeniscairan_nama','tgl_pemasangan', 'jumlah_tetes'], 'safe'],
            [['jumlah_tetes'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'jeniscairan_id'=>\Yii::t('fe', 'Jenis cairan infus'),
            'tgl_pemasangan'=>\Yii::t('fe', 'Tanggal pemasangan'),
            'jumlah_tetes'=>\Yii::t('fe', 'Jumlah tetesan'),
        ];
    }
}