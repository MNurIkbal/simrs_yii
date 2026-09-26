<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Resep Form
 * @copyright 3 January 2018 aweutist
 */

namespace Doco\apotek\models;

use Yii;

class TransaksiResepForm extends \yii\db\ActiveRecord
{
    public $no_resep;
    public $nama_pasien;
    public $dokter_resep;
    public $iter;
    public $obatalkes;
    public $cara_bayar;
    public $penjamin;
    public $nama_pembeli;
    public $catatan;
    public $biayaadministrasi;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjualanresep_t';
    }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['no_resep'], 'required'],
            [['no_resep', 'iter', 'nama_pembeli', 'nama_pasien', 'dokter_resep', 'catatan', 'biayaadministrasi', 'obatalkes'], 'safe'],
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'no_resep' => Yii::t('fe','No Resep'),
        ];
    }
}


