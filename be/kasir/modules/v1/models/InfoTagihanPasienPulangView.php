<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotagihanpasienpulang_v".
 *
 * @property int $pendaftaran_id
 * @property string $tglpasienpulang
 * @property string $no_pendaftaran
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property double $total_tindakan
 * @property double $total_obat
 * @property double $total_tagihan
 */
class InfoTagihanPasienPulangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotagihanpasienpulang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'instalasi_id', 'ruangan_id', 'carabayar_id', 'penjamin_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'instalasi_id', 'ruangan_id', 'carabayar_id', 'penjamin_id'], 'integer'],
            [['tglpasienpulang'], 'safe'],
            [['total_tindakan', 'total_obat', 'total_tagihan'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['instalasi_nama', 'ruangan_nama', 'nama_pasien', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'tglpasienpulang' => 'Tglpasienpulang',
            'no_pendaftaran' => 'No Pendaftaran',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'total_tindakan' => 'Total Tindakan',
            'total_obat' => 'Total Obat',
            'total_tagihan' => 'Total Tagihan',
        ];
    }
}
