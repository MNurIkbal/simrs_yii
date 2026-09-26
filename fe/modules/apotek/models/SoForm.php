<?php

/**
 * @author Randy Vianda Putra
 * @todo Stok Opname Form
 * @copyright 18 January 2018 aweutist
 */

namespace Doco\apotek\models;

use Yii;

class SoForm extends \yii\db\ActiveRecord
{
    public $instalasi;
    public $ruangan_tujuan;
    public $tanggal_kirim;
    public $kode_obat;
    public $qty;
    public $pegawai_id;

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


