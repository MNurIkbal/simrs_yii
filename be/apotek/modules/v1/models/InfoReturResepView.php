<?php

namespace app\modules\v1\models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;

/**
 * This is the model class for table "inforeturresep_v".
 *
 * @property int $returresep_id
 * @property string $tgl_retur
 * @property string $no_returresep
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property int $penjualanresep_id
 * @property string $noresep
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property bool $status_retur
 * @property string $no_pendaftaran
 * @property string $instalasi_asal
 * @property string $tgl_verif
 */
class InfoReturResepView extends DocoExcelActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'inforeturresep_v';
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('tgl_retur', self::DATE_TYPE, 'Tanggal Retur'),
            (array) new ExcelColumn('no_returresep', self::STRING_TYPE, 'Nomor Retur'),
            (array) new ExcelColumn('status_retur', self::STRING_TYPE, 'Status Retur'),
            (array) new ExcelColumn('noresep', self::STRING_TYPE, 'Nomor Resep'),
            (array) new ExcelColumn('no_pendaftaran', self::STRING_TYPE, 'No. Pendaftaran'),
            (array) new ExcelColumn('nama_pasien', self::STRING_TYPE, 'Nama Pasien'),
            (array) new ExcelColumn('instalasi_asal', self::STRING_TYPE, 'Instalasi Asal'),
            (array) new ExcelColumn('ruangan_nama', self::STRING_TYPE, 'Depo Asal'),
            (array) new ExcelColumn('tgl_verif', self::STRING_TYPE, 'Tanggal Verifikasi')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['returresep_id', 'pasien_id', 'penjualanresep_id', 'carabayar_id', 'penjamin_id'], 'default', 'value' => null],
            [['returresep_id', 'pasien_id', 'penjualanresep_id', 'carabayar_id', 'penjamin_id'], 'integer'],
            [['tgl_retur'], 'safe'],
            [['no_returresep', 'noresep'], 'string'],
            [['nama_pasien', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'returresep_id' => 'Returresep ID',
            'tgl_retur' => 'Tgl Retur',
            'no_returresep' => 'No Returresep',
            'pasien_id' => 'Pasien ID',
            'nama_pasien' => 'Nama Pasien',
            'penjualanresep_id' => 'Penjualanresep ID',
            'noresep' => 'Noresep',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'tgl_verif' => 'Tanggal Verif',
            'instalasi_asal' => 'Tanggal Verif',
            'no_pendaftaran' => 'Tanggal Verif',
            'status_retur' => 'Tanggal Verif',
        ];
    }
}
