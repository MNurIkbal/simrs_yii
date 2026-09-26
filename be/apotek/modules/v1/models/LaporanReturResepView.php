<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanreturresep_v".
 *
 * @property int $returresep_id
 * @property string $tgl_retur
 * @property string $no_returresep
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property int $penjualanresep_id
 * @property string $noresep
 * @property string $obatalkes_namalain
 * @property double $qty_retur
 * @property double $hargasatuan
 * @property double $jumlah
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 */
class LaporanReturResepView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanreturresep_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['returresep_id', 'pasien_id', 'penjualanresep_id', 'carabayar_id', 'penjamin_id', 'ruangan_id'], 'default', 'value' => null],
            [['returresep_id', 'pasien_id', 'penjualanresep_id', 'carabayar_id', 'penjamin_id', 'ruangan_id'], 'integer'],
            [['tgl_retur'], 'safe'],
            [['no_returresep', 'noresep', 'obatalkes_nama'], 'string'],
            [['qty_retur', 'hargasatuan', 'jumlah'], 'number'],
            [['nama_pasien', 'carabayar_nama', 'penjamin_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'returresep_id' => 'Returresep ID',
            'tgl_retur' => 'Tgl Retur',
            'no_returresep' => 'No Returresep',
            'pasien_id' => 'Pasien ID',
            'nama_pasien' => 'Nama Pasien',
            'penjualanresep_id' => 'Penjualanresep ID',
            'noresep' => 'Noresep',
            'obatalkes_nama' => 'Obatalkes Nama',
            'qty_retur' => 'Qty Retur',
            'hargasatuan' => 'Hargasatuan',
            'jumlah' => 'Jumlah',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
        ];
    }
}
