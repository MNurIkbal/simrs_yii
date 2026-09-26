<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoclosingkasirdetail_v".
 *
 * @property int $closingkasir_id
 * @property string $tgl_closingkasir
 * @property string $no_closingkasir
 * @property string $nama_pegawai
 * @property string $tgl_pembayaran
 * @property string $shift_nama
 * @property string $no_pendaftaran
 * @property string $nama_pasien
 * @property double $total_terbayar
 * @property double $nilaiuang
 * @property int $banyakuang
 * @property double $jumlahuang
 */
class InfoClosingKasirDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infoclosingkasirdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['closingkasir_id', 'banyakuang'], 'default', 'value' => null],
            [['closingkasir_id', 'banyakuang'], 'integer'],
            [['tgl_closingkasir', 'tgl_pembayaran'], 'safe'],
            [['total_terbayar', 'nilaiuang', 'jumlahuang'], 'number'],
            [['no_closingkasir'], 'string', 'max' => 100],
            [['nama_pegawai', 'shift_nama', 'nama_pasien'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'closingkasir_id' => 'Closingkasir ID',
            'tgl_closingkasir' => 'Tgl Closingkasir',
            'no_closingkasir' => 'No Closingkasir',
            'nama_pegawai' => 'Nama Pegawai',
            'tgl_pembayaran' => 'Tgl Pembayaran',
            'shift_nama' => 'Shift Nama',
            'no_pendaftaran' => 'No Pendaftaran',
            'nama_pasien' => 'Nama Pasien',
            'total_terbayar' => 'Total Terbayar',
            'nilaiuang' => 'Nilaiuang',
            'banyakuang' => 'Banyakuang',
            'jumlahuang' => 'Jumlahuang',
        ];
    }
}
