<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "laporanclosingkasir_v".
 *
 * @property int $closingkasir_id
 * @property int $shift_id
 * @property string $shift_nama
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $setorbank_id
 * @property string $no_struksetor
 * @property string $no_closingkasir
 * @property string $tgl_disetor
 * @property string $nama_bank
 * @property string $no_rekening
 * @property double $jumlah_setoran
 * @property string $tgl_closingkasir
 * @property string $closing_dari
 * @property string $sampai_dengan
 * @property double $closing_saldoawal
 * @property double $terima_uangmuka
 * @property double $terima_uangpelayanan
 * @property double $total_pengeluaran
 * @property double $total_setoran
 * @property string $keterangan_closing
 * @property double $jumlah_uanglogam
 * @property double $jumlah_uangkertas
 * @property int $jumlah_transaksi
 * @property double $piutang
 * @property double $nilai_closingtransaksi
 */
class LaporanClosingKasirForm extends \yii\base\Model
{
    public $closingkasir_id;
    public $shift_id;
    public $shift_nama;
    public $instalasi_id;
    public $instalasi_nama;
    public $ruangan_id;
    public $ruangan_nama;
    public $pegawai_id;
    public $nama_pegawai;
    public $setorbank_id;
    public $no_struksetor;
    public $no_closingkasir;
    public $tgl_disetor;
    public $nama_bank;
    public $no_rekening;
    public $jumlah_setoran;
    public $tgl_closingkasir;
    public $closing_dari;
    public $sampai_dengan;
    public $closing_saldoawal;
    public $terima_uangmuka;
    public $terima_uangpelayanan;
    public $total_pengeluaran;
    public $total_setoran;
    public $keterangan_closing;
    public $jumlah_uanglogam;
    public $jumlah_uangkertas;
    public $jumlah_transaksi;
    public $piutang;
    public $nilai_closingtransaksi;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanclosingkasir_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['closingkasir_id', 'instalasi_id', 'ruangan_id', 'shift_id', 'pegawai_id', 'setorbank_id', 'jumlah_transaksi'], 'default', 'value' => null],
            [['closingkasir_id', 'shift_id', 'pegawai_id', 'setorbank_id', 'jumlah_transaksi'], 'integer'],
            [['ruangan_nama', 'instalasi_nama', 'tgl_disetor', 'tgl_closingkasir', 'closing_dari', 'sampai_dengan'], 'safe'],
            [['jumlah_setoran', 'closing_saldoawal', 'terima_uangmuka', 'terima_uangpelayanan', 'total_pengeluaran', 'total_setoran', 'jumlah_uanglogam', 'jumlah_uangkertas', 'piutang', 'nilai_closingtransaksi'], 'number'],
            [['keterangan_closing'], 'string'],
            [['shift_nama', 'nama_pegawai'], 'string', 'max' => 50],
            [['no_closingkasir', 'no_struksetor', 'nama_bank', 'no_rekening'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'closingkasir_id' => Yii::t('fe', 'Closing kasir'),
            'shift_id' => Yii::t('fe', 'Shift'),
            'shift_nama' => Yii::t('fe', 'Nama shift'),
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'instalasi_nama' => Yii::t('fe', 'Nama instalasi'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'ruangan_nama' => Yii::t('fe', 'Nama ruangan'),
            'pegawai_id' => Yii::t('fe', 'Pegawai'),
            'nama_pegawai' => Yii::t('fe', 'Nama pegawai'),
            'setorbank_id' => Yii::t('fe', 'Setor bank'),
            'no_struksetor' => Yii::t('fe', 'No struk setor'),
            'no_closingkasir' => Yii::t('fe', 'No clsoing kasir'),
            'tgl_disetor' => Yii::t('fe', 'Tanggal disetor'),
            'nama_bank' => Yii::t('fe', 'Nama bank'),
            'no_rekening' => Yii::t('fe', 'No rekening'),
            'jumlah_setoran' => Yii::t('fe', 'Jumlah setoran'),
            'tgl_closingkasir' => Yii::t('fe', 'Tanggal closing kasir'),
            'closing_dari' => Yii::t('fe', 'Closing dari'),
            'sampai_dengan' => Yii::t('fe', 'Sampai dengan'),
            'closing_saldoawal' => Yii::t('fe', 'Closing saldo awal'),
            'terima_uangmuka' => Yii::t('fe', 'Terima uang muka'),
            'terima_uangpelayanan' => Yii::t('fe', 'Terima uang pelayanan'),
            'total_pengeluaran' => Yii::t('fe', 'Total pengeluaran'),
            'total_setoran' => Yii::t('fe', 'Total setoran'),
            'keterangan_closing' => Yii::t('fe', 'Keterangan closing'),
            'jumlah_uanglogam' => Yii::t('fe', 'Jumlah uang logam'),
            'jumlah_uangkertas' => Yii::t('fe', 'Jumlah uang kertas'),
            'jumlah_transaksi' => Yii::t('fe', 'Jumlah transaksi'),
            'piutang' => Yii::t('fe', 'Piutang'),
            'nilai_closingtransaksi' => Yii::t('fe', 'Nilai closing transaksi'),
        ];
    }
}
