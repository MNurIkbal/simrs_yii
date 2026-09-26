<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 10:26:24
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-21 10:30:53
 */

namespace Doco\bedah\models;

class IntraItemOperasiForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $operasi_id;
    public $operasi_nama;
    public $pegawai_id;
    public $pegawai_nama;
    public $daftartindakan_id;
    public $daftartindakan_nama;
    public $is_cyto;
    public $jenis_luka;
    public $jenis_luka_nama;
    public $golonganoperasi_id;
    public $golonganoperasi_nama;
    public $jenisanastesi_id;
    public $jenisanastesi_nama;
    public $tarif_satuan;
    public $tarif_tindakan;
    public $tarif_cyto;
    public $cyto;
    public $penyulit;
    public $default;
    public $kegiatanoperasi_nama;
    public $kegiatanoperasi_id;

    public function rules()
    {
        return [
            [['daftartindakan_id', 'pegawai_id'], 'required', 'message' => '{attribute} Tidak Boleh Kosong'],
            [[
                'pasienmasukpenunjang_id', 'operasi_id', 'inpostoperasi_id', 'daftartindakan_nama', 'operasi_nama', 'jenis_luka', 
                'jenisanastesi_id',  'jenis_luka_nama', 'golonganoperasi_nama', 'jenisanastesi_nama', 'is_cyto', 'tarif_satuan', 
                'tarif_tindakan', 'tarif_cyto', 'cyto', 'penyulit', 'default','daftartindakan_id', 'golonganoperasi_id', 'pegawai_nama', 
                'kegiatanoperasi_nama', 'kegiatanoperasi_id'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => \Yii::t('fe', 'Tindakan Operasi'),
            'pegawai_id' => \Yii::t('fe', 'Dokter operator'),
            'jenis_luka' => \Yii::t('fe', 'Jenis luka'),
            'jenisanastesi_id' => \Yii::t('fe', 'Jenis anastesi'),
            'golonganoperasi_id' => \Yii::t('fe', 'Klasifikasi operasi'),
            'operasi_id' => \Yii::t('fe', 'Nama operasi'),
            'is_cyto' => \Yii::t('fe', 'Cyto'),
            'penyulit' => \Yii::t('fe', 'Penyulit'),
        ];
    }
}
