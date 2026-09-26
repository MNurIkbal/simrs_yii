<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "closing_kasir_view".
 *
 * @property int $tandabuktibayar_id
 * @property int $ruangan_id
 * @property int $bayaruangmuka_id
 * @property int $closingkasir_id
 * @property int $pembayaranpelayanan_id
 * @property int $shift_id
 * @property int $nourutkasir
 * @property string $nobuktibayar
 * @property string $tglbuktibayar
 * @property double $uangditerima
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $nama_pasien
 */
class ClosingKasirView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'closing_kasir_view';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tandabuktibayar_id', 'ruangan_id', 'bayaruangmuka_id', 'closingkasir_id', 'pembayaranpelayanan_id', 'shift_id', 'nourutkasir', 'pendaftaran_id'], 'default', 'value' => null],
            [['tandabuktibayar_id', 'ruangan_id', 'bayaruangmuka_id', 'closingkasir_id', 'pembayaranpelayanan_id', 'shift_id', 'nourutkasir', 'pendaftaran_id'], 'integer'],
            [['tglbuktibayar','pegawai1_id','is_deleted'], 'safe'],
            [['uangditerima'], 'number'],
            [['nobuktibayar', 'nama_pasien'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tandabuktibayar_id' => 'Tandabuktibayar ID',
            'ruangan_id' => 'Ruangan ID',
            'bayaruangmuka_id' => 'Bayaruangmuka ID',
            'closingkasir_id' => 'Closingkasir ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'shift_id' => 'Shift ID',
            'nourutkasir' => 'Nourutkasir',
            'nobuktibayar' => 'Nobuktibayar',
            'tglbuktibayar' => 'Tglbuktibayar',
            'uangditerima' => 'Uangditerima',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'nama_pasien' => 'Nama Pasien',
        ];
    }
}
