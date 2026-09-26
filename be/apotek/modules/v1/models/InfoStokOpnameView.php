<?php

namespace app\modules\v1\models;

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
class InfoStokOpnameView extends \Doco\components\DocoActiveRecord
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
            [['instalasi_id', 'ruangan_id', 'formulirstokopname_id', 'stokopname_id', 'petugas1_id', 'petugas2_id', 'pegawaimengetahui_id'], 'integer'],
            [['tglformulir', 'tglstokopname'], 'safe'],
            [['noformulir', 'nostokopname', 'jenisstokopname', 'keterangan_opname'], 'string'],
            [['isstokawal'], 'boolean'],
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
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'formulirstokopname_id' => Yii::t('app', 'Formulir stok opname'),
            'tglformulir' => Yii::t('app', 'Tanggal formulir'),
            'noformulir' => Yii::t('app', 'No formulir'),
            'stokopname_id' => Yii::t('app', 'Stok opname'),
            'tglstokopname' => Yii::t('app', 'Tanggal stok opname'),
            'nostokopname' => Yii::t('app', 'No stok opname'),
            'isstokawal' => Yii::t('app', 'Is stok awal'),
            'jenisstokopname' => Yii::t('app', 'Jenis stok opname'),
            'keterangan_opname' => Yii::t('app', 'Keterangan opname'),
            'petugas1_id' => Yii::t('app', 'Petugas 1'),
            'petugas1_nip' => Yii::t('app', 'Nip Petugas 1'),
            'petugas1_noidentitas' => Yii::t('app', 'Noidentitas petugas 1'),
            'petugas1_gelardepan' => Yii::t('app', 'Gelar depan petugas 1'),
            'petugas1_nama' => Yii::t('app', 'Nama petugas 1'),
            'petugas1_gelarbelakang' => Yii::t('app', 'Gelar belakang petugas 1'),
            'petugas2_id' => Yii::t('app', 'Petugas 2'),
            'petugas2_nip' => Yii::t('app', 'Nip petugas 2'),
            'petugas2_noidentitas' => Yii::t('app', 'No identitas petugas 2'),
            'petugas2_gelardepan' => Yii::t('app', 'Gelardepan petugas 2'),
            'petugas2_nama' => Yii::t('app', 'Nama petugas 2'),
            'petugas2_gelarbelakang' => Yii::t('app', 'Gelar belakang petugas 2'),
            'pegawaimengetahui_id' => Yii::t('app', 'Pegawai mengetahui'),
            'pegawaimengetahui_nip' => Yii::t('app', 'Nip pegawai mengetahui'),
            'pegawaimengetahui_noidentitas' => Yii::t('app', 'No identitas pegawai mengetahui'),
            'pegawaimengetahui_gelardepan' => Yii::t('app', 'Gelar depan pegawai mengetahui'),
            'pegawaimengetahui_nama' => Yii::t('app', 'Nama pegawai mengetahui'),
            'pegawaimengetahui_gelarbelakang' => Yii::t('app', 'Gelar belakang pegawai mengetahui'),
        ];
    }
}
