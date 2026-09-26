<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "infostokopname_v".
 *
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property integer $ruangan_id
 * @property string $ruangan_nama
 * @property integer $formulirstokopname_id
 * @property string $tglformulir
 * @property string $noformulir
 * @property integer $stokopname_id
 * @property string $tglstokopname
 * @property string $nostokopname
 * @property boolean $isstokawal
 * @property string $jenisstokopname
 * @property string $keterangan_opname
 * @property double $totalharga
 * @property double $totalnetto
 * @property integer $petugas1_id
 * @property string $petugas1_nip
 * @property string $petugas1_noidentitas
 * @property string $petugas1_gelardepan
 * @property string $petugas1_nama
 * @property string $petugas1_gelarbelakang
 * @property integer $petugas2_id
 * @property string $petugas2_nip
 * @property string $petugas2_noidentitas
 * @property string $petugas2_gelardepan
 * @property string $petugas2_nama
 * @property string $petugas2_gelarbelakang
 * @property integer $pegawaimengetahui_id
 * @property string $pegawaimengetahui_nip
 * @property string $pegawaimengetahui_noidentitas
 * @property string $pegawaimengetahui_gelardepan
 * @property string $pegawaimengetahui_nama
 * @property string $pegawaimengetahui_gelarbelakang
 */
class InfoStokOpnameForm extends \yii\base\Model
{
    
    public $instalasi_id;
    public $instalasi_nama;
    public $ruangan_id;
    public $ruangan_nama;
    public $formulirstokopname_id;
    public $tglformulir;
    public $noformulir;
    public $stokopname_id;
    public $tglstokopname;
    public $nostokopname;
    public $isstokawal;
    public $jenisstokopname;
    public $keterangan_opname;
    public $totalharga;
    public $totalnetto;
    public $petugas1_id;
    public $petugas1_nip;
    public $petugas1_noidentitas;
    public $petugas1_gelardepan;
    public $petugas1_nama;
    public $petugas1_gelarbelakang;
    public $petugas2_id;
    public $petugas2_nip;
    public $petugas2_noidentitas;
    public $petugas2_gelardepan;
    public $petugas2_nama;
    public $petugas2_gelarbelakang;
    public $pegawaimengetahui_id;
    public $pegawaimengetahui_nip;
    public $pegawaimengetahui_noidentitas;
    public $pegawaimengetahui_gelardepan;
    public $pegawaimengetahui_nama;
    public $pegawaimengetahui_gelarbelakang;

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
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'instalasi_nama' => Yii::t('fe', 'Nama instalasi'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'ruangan_nama' => Yii::t('fe', 'Nama ruangan'),
            'formulirstokopname_id' => Yii::t('fe', 'Formulir stok opname'),
            'tglformulir' => Yii::t('fe', 'Tanggal formulir'),
            'noformulir' => Yii::t('fe', 'No formulir'),
            'stokopname_id' => Yii::t('fe', 'Stok opname'),
            'tglstokopname' => Yii::t('fe', 'Tanggal stok opname'),
            'nostokopname' => Yii::t('fe', 'No stok opname'),
            'isstokawal' => Yii::t('fe', 'Is stok awal'),
            'jenisstokopname' => Yii::t('fe', 'Jenis stok opname'),
            'keterangan_opname' => Yii::t('fe', 'Keterangan opname'),
            'totalharga' => Yii::t('fe', 'Total harga'),
            'totalnetto' => Yii::t('fe', 'Total netto'),
            'petugas1_id' => Yii::t('fe', 'Petugas 1'),
            'petugas1_nip' => Yii::t('fe', 'Nip Petugas 1'),
            'petugas1_noidentitas' => Yii::t('fe', 'Noidentitas petugas 1'),
            'petugas1_gelardepan' => Yii::t('fe', 'Gelar depan petugas 1'),
            'petugas1_nama' => Yii::t('fe', 'Nama petugas 1'),
            'petugas1_gelarbelakang' => Yii::t('fe', 'Gelar belakang petugas 1'),
            'petugas2_id' => Yii::t('fe', 'Petugas 2'),
            'petugas2_nip' => Yii::t('fe', 'Nip petugas 2'),
            'petugas2_noidentitas' => Yii::t('fe', 'No identitas petugas 2'),
            'petugas2_gelardepan' => Yii::t('fe', 'Gelardepan petugas 2'),
            'petugas2_nama' => Yii::t('fe', 'Nama petugas 2'),
            'petugas2_gelarbelakang' => Yii::t('fe', 'Gelar belakang petugas 2'),
            'pegawaimengetahui_id' => Yii::t('fe', 'Pegawai mengetahui'),
            'pegawaimengetahui_nip' => Yii::t('fe', 'Nip pegawai mengetahui'),
            'pegawaimengetahui_noidentitas' => Yii::t('fe', 'No identitas pegawai mengetahui'),
            'pegawaimengetahui_gelardepan' => Yii::t('fe', 'Gelar depan pegawai mengetahui'),
            'pegawaimengetahui_nama' => Yii::t('fe', 'Nama pegawai mengetahui'),
            'pegawaimengetahui_gelarbelakang' => Yii::t('fe', 'Gelar belakang pegawai mengetahui'),
        ];
    }
}
