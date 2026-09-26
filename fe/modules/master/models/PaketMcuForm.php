<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\models;

use Yii;
use yii\validators\UniqueValidator;
use yii\db\Query;
use app\components\DocoBaseModel;

class PaketMcuForm extends DocoBaseModel
{
    public $tipepaket_id;
    public $tipepaket_kode;
    public $tipepaket_nama;
    public $tipepaket_namalainnya;
    public $keterangan_tipepaket;
    public $detail;
    public $is_mcu;
    public $jenis;
    public $paketdetail_id;
    public $daftartindakan_id;
    public $tindakanpelayanan_id;
    public $instalasi_id;
    public $ruangan_id;
    public $status;
    public $is_active;

    protected $xssProtected = [
        'tipepaket_kode',
        'tipepaket_nama',
        'tipepaket_namalainnya',
        'keterangan_tipepaket'
    ];

    public function rules()
    {
        return [
            [['tipepaket_kode', 'tipepaket_nama', 'tipepaket_namalainnya', 'keterangan_tipepaket', 'status'], 'string'],
            [['tipepaket_id', 'ruangan_id', 'paketdetail_id', 'daftartindakan_id'], 'integer'],
            [['tipepaket_kode', 'tipepaket_nama', 'tipepaket_namalainnya', 'keterangan_tipepaket', 'detail', 'tindakanpelayanan_id', 'jenis', 'instalasi_id', 'is_active', 'is_mcu', 'status'], 'safe'],
            [['tipepaket_kode', 'tipepaket_nama', 'tipepaket_namalainnya', 'detail', 'is_active'], 'required'],
        ];
    }

    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'tipepaket_id' => \Yii::t('fe', 'ID Paket'),
            'ruangan_id' => \Yii::t('fe', 'ID Ruangan'),
            'tipepaket_kode' => \Yii::t('fe', 'Kode Paket'),
            'tipepaket_nama' => \Yii::t('fe', 'Nama Paket'),
            'tipepaket_namalainnya' => \Yii::t('fe', 'Nama Lain Paket'),
            'keterangan_tipepaket' => \Yii::t('fe', 'Catatan'),
            'detail' => \Yii::t('fe', 'Catatan'),
            'is_active' => \Yii::t('fe', 'Status'),
        ];
    }

}
