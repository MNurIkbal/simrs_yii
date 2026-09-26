<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "infostokbarang_v".
 *
 * @property integer $periodestok_id
 * @property string $periodestok_nama
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property integer $ruangan_id
 * @property string $ruangan_nama
 * @property integer $barang_id
 * @property string $barang_nama
 * @property integer $qty_masuk
 * @property integer $qty_keluar
 * @property integer $qty_dipesan
 * @property integer $qty_tersedia
 * @property integer $qty_stok
 * @property string $tglperiodestok_awal
 * @property string $tglperiodestok_akhir
 */
class InfoStokBarangForm extends \yii\base\Model
{
    public $periodestok_id;
    public $periodestok_nama;
    public $instalasi_id;
    public $instalasi_nama;
    public $ruangan_id;
    public $ruangan_nama;
    public $barang_id;
    public $barang_nama;
    public $qty_masuk;
    public $qty_keluar;
    public $qty_dipesan;
    public $qty_tersedia;
    public $qty_stok;
    public $tglperiodestok_awal;
    public $tglperiodestok_akhir;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infostokbarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['periodestok_id', 'instalasi_id', 'ruangan_id', 'barang_id', 'qty_masuk', 'qty_keluar', 'qty_dipesan', 'qty_tersedia', 'qty_stok'], 'integer'],
            [['tglperiodestok_awal', 'tglperiodestok_akhir'], 'safe'],
            [['periodestok_nama', 'barang_nama'], 'string', 'max' => 100],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'periodestok_id' => Yii::t('fe', 'Periode stok'),
            'periodestok_nama' => Yii::t('fe', 'Nama periode stok'),
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'instalasi_nama' => Yii::t('fe', 'Nama instalasi'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'ruangan_nama' => Yii::t('fe', 'Nama ruangan'),
            'barang_id' => Yii::t('fe', 'Barang'),
            'barang_nama' => Yii::t('fe', 'Nama barang'),
            'qty_masuk' => Yii::t('fe', 'Qty masuk'),
            'qty_keluar' => Yii::t('fe', 'Qty keluar'),
            'qty_dipesan' => Yii::t('fe', 'Qty dipesan'),
            'qty_tersedia' => Yii::t('fe', 'Qty tersedia'),
            'qty_stok' => Yii::t('fe', 'Qty stok'),
            'tglperiodestok_awal' => Yii::t('fe', 'Periode awal'),
            'tglperiodestok_akhir' => Yii::t('fe', 'Periode akhir'),
        ];
    }
}
