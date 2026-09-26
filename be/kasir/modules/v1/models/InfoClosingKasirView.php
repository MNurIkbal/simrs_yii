<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoclosingkasir_v".
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
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 */
class InfoClosingKasirView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    
    public static function primaryKey(){
        return ['closingkasir_id'];
    }

    public static function tableName()
    {
        return 'infoclosingkasir_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['closingkasir_id', 'shift_id', 'pegawai_id', 'setorbank_id', 'jumlah_transaksi', 'ruangan_id', 'instalasi_id'], 'default', 'value' => null],
            [['closingkasir_id', 'shift_id', 'pegawai_id', 'setorbank_id', 'jumlah_transaksi', 'ruangan_id', 'instalasi_id'], 'integer'],
            [['tgl_disetor', 'tgl_closingkasir', 'closing_dari', 'sampai_dengan'], 'safe'],
            [['jumlah_setoran', 'closing_saldoawal', 'terima_uangmuka', 'terima_uangpelayanan', 'total_pengeluaran', 'total_setoran', 'jumlah_uanglogam', 'jumlah_uangkertas', 'piutang', 'nilai_closingtransaksi'], 'number'],
            [['keterangan_closing'], 'string'],
            [['shift_nama', 'nama_pegawai', 'ruangan_nama', 'instalasi_nama'], 'string', 'max' => 50],
            [['no_struksetor', 'no_closingkasir', 'nama_bank', 'no_rekening'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'closingkasir_id' => Yii::t('app', 'Closing kasir'),
            'shift_id' => Yii::t('app', 'Shift'),
            'shift_nama' => Yii::t('app', 'Nama shift'),
            'pegawai_id' => Yii::t('app', 'Pegawai'),
            'nama_pegawai' => Yii::t('app', 'Nama pegawai'),
            'setorbank_id' => Yii::t('app', 'Setor bank'),
            'no_struksetor' => Yii::t('app', 'No struk setor'),
            'no_closingkasir' => Yii::t('app', 'No closing kasir'),
            'tgl_disetor' => Yii::t('app', 'Tanggal disetor'),
            'nama_bank' => Yii::t('app', 'Nama bank'),
            'no_rekening' => Yii::t('app', 'No rekening'),
            'jumlah_setoran' => Yii::t('app', 'Jumlah setoran'),
            'tgl_closingkasir' => Yii::t('app', 'Tanggal closing kasir'),
            'closing_dari' => Yii::t('app', 'Closing dari'),
            'sampai_dengan' => Yii::t('app', 'Sampai dengan'),
            'closing_saldoawal' => Yii::t('app', 'Closing saldo awal'),
            'terima_uangmuka' => Yii::t('app', 'Terima uang muka'),
            'terima_uangpelayanan' => Yii::t('app', 'Terima uang pelayanan'),
            'total_pengeluaran' => Yii::t('app', 'Total pengeluaran'),
            'total_setoran' => Yii::t('app', 'Total setoran'),
            'keterangan_closing' => Yii::t('app', 'Keterangan closing'),
            'jumlah_uanglogam' => Yii::t('app', 'Jumlah uang logam'),
            'jumlah_uangkertas' => Yii::t('app', 'Jumlah uang kertas'),
            'jumlah_transaksi' => Yii::t('app', 'Jumlah transaksi'),
            'piutang' => Yii::t('app', 'Piutang'),
            'nilai_closingtransaksi' => Yii::t('app', 'Nilai closing transaksi'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
        ];
    }
}
