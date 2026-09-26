<?php

namespace app\modules\rm\models;

use Yii;

/**
 * This is the model class for table "infostokopname_v".
 *
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $formulirstokopname_id
 * @property string $tglformulir
 * @property string $noformulir
 * @property int $stokopname_id
 * @property string $tglstokopname
 * @property string $nostokopname
 * @property bool $isstokawal
 * @property string $jenisstokopname
 * @property string $keterangan_opname
 * @property double $totalharga
 * @property double $totalnetto
 * @property int $petugas1_id
 * @property string $petugas1_nip
 * @property string $petugas1_noidentitas
 * @property string $petugas1_gelardepan
 * @property string $petugas1_nama
 * @property string $petugas1_gelarbelakang
 * @property int $petugas2_id
 * @property string $petugas2_nip
 * @property string $petugas2_noidentitas
 * @property string $petugas2_gelardepan
 * @property string $petugas2_nama
 * @property string $petugas2_gelarbelakang
 * @property int $pegawaimengetahui_id
 * @property string $pegawaimengetahui_nip
 * @property string $pegawaimengetahui_noidentitas
 * @property string $pegawaimengetahui_gelardepan
 * @property string $pegawaimengetahui_nama
 * @property string $pegawaimengetahui_gelarbelakang
 */
class InfoStokOpname extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infostokopname_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'ruangan_id', 'formulirstokopname_id', 'stokopname_id', 'petugas1_id', 'petugas2_id', 'pegawaimengetahui_id'], 'default', 'value' => null],
            [['instalasi_id', 'ruangan_id', 'formulirstokopname_id', 'stokopname_id', 'petugas1_id', 'petugas2_id', 'pegawaimengetahui_id'], 'integer'],
            [['tglformulir', 'tglstokopname'], 'safe'],
            [['noformulir', 'nostokopname', 'jenisstokopname', 'keterangan_opname'], 'string'],
            [['isstokawal'], 'boolean'],
            [['totalharga', 'totalnetto'], 'number'],
            [['instalasi_nama', 'ruangan_nama', 'petugas1_nama', 'petugas2_nama', 'pegawaimengetahui_nama'], 'string', 'max' => 50],
            [['petugas1_nip', 'petugas2_nip', 'pegawaimengetahui_nip'], 'string', 'max' => 30],
            [['petugas1_noidentitas', 'petugas2_noidentitas', 'pegawaimengetahui_noidentitas'], 'string', 'max' => 100],
            [['petugas1_gelardepan', 'petugas2_gelardepan', 'pegawaimengetahui_gelardepan'], 'string', 'max' => 10],
            [['petugas1_gelarbelakang', 'petugas2_gelarbelakang', 'pegawaimengetahui_gelarbelakang'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'formulirstokopname_id' => 'Formulirstokopname ID',
            'tglformulir' => 'Tglformulir',
            'noformulir' => 'Noformulir',
            'stokopname_id' => 'Stokopname ID',
            'tglstokopname' => 'Tglstokopname',
            'nostokopname' => 'Nostokopname',
            'isstokawal' => 'Isstokawal',
            'jenisstokopname' => 'Jenisstokopname',
            'keterangan_opname' => 'Keterangan Opname',
            'totalharga' => 'Totalharga',
            'totalnetto' => 'Totalnetto',
            'petugas1_id' => 'Petugas1 ID',
            'petugas1_nip' => 'Petugas1 Nip',
            'petugas1_noidentitas' => 'Petugas1 Noidentitas',
            'petugas1_gelardepan' => 'Petugas1 Gelardepan',
            'petugas1_nama' => 'Petugas1 Nama',
            'petugas1_gelarbelakang' => 'Petugas1 Gelarbelakang',
            'petugas2_id' => 'Petugas2 ID',
            'petugas2_nip' => 'Petugas2 Nip',
            'petugas2_noidentitas' => 'Petugas2 Noidentitas',
            'petugas2_gelardepan' => 'Petugas2 Gelardepan',
            'petugas2_nama' => 'Petugas2 Nama',
            'petugas2_gelarbelakang' => 'Petugas2 Gelarbelakang',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'pegawaimengetahui_nip' => 'Pegawaimengetahui Nip',
            'pegawaimengetahui_noidentitas' => 'Pegawaimengetahui Noidentitas',
            'pegawaimengetahui_gelardepan' => 'Pegawaimengetahui Gelardepan',
            'pegawaimengetahui_nama' => 'Pegawaimengetahui Nama',
            'pegawaimengetahui_gelarbelakang' => 'Pegawaimengetahui Gelarbelakang',
        ];
    }
}
