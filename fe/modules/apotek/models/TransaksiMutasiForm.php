<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Resep Form
 * @copyright 15 January 2018 aweutist
 */

namespace Doco\apotek\models;

use Yii;

class TransaksiMutasiForm extends \yii\db\ActiveRecord
{
    public $instalasi_tujuan;
    public $ruangan_tujuan;
    public $tanggal_kirim;
    public $kode_obat;
    public $qty;
    public $pegawai_id;
    public $nama_pegawai;
    public $pesanobatalkes_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitobat_mp';
    }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['no_resep'], 'required'],
            [['no_resep'], 'safe'],
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


