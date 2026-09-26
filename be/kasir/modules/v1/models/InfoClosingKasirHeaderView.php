<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoclosingkasirheader_v".
 *
 * @property int $closingkasir_id
 * @property string $tgl_closingkasir
 * @property string $no_closingkasir
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $shift_id
 * @property string $shift_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property double $total_setoran
 * @property int $setorbank_id
 * @property string $no_struksetor
 * @property string $tgl_disetor
 * @property string $nama_bank
 * @property string $no_rekening
 * @property double $jumlah_setoran
 */
class InfoClosingKasirHeaderView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoclosingkasirheader_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['closingkasir_id', 'pegawai_id', 'shift_id', 'ruangan_id', 'instalasi_id', 'setorbank_id'], 'default', 'value' => null],
            [['closingkasir_id', 'pegawai_id', 'shift_id', 'ruangan_id', 'instalasi_id', 'setorbank_id'], 'integer'],
            [['tgl_closingkasir', 'tgl_disetor'], 'safe'],
            [['total_setoran', 'jumlah_setoran'], 'number'],
            [['no_closingkasir', 'no_struksetor', 'nama_bank', 'no_rekening'], 'string', 'max' => 100],
            [['nama_pegawai', 'shift_nama', 'ruangan_nama', 'instalasi_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'closingkasir_id' => 'Closingkasir ID',
            'tgl_closingkasir' => 'Tgl Closingkasir',
            'no_closingkasir' => 'No Closingkasir',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'shift_id' => 'Shift ID',
            'shift_nama' => 'Shift Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'total_setoran' => 'Total Setoran',
            'setorbank_id' => 'Setorbank ID',
            'no_struksetor' => 'No Struksetor',
            'tgl_disetor' => 'Tgl Disetor',
            'nama_bank' => 'Nama Bank',
            'no_rekening' => 'No Rekening',
            'jumlah_setoran' => 'Jumlah Setoran',
        ];
    }
}
