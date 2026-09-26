<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "sep_t".
 *
 * @property int $bpjs_id
 * @property string $tglsep
 * @property string $nosep
 * @property string $nokartuasuransi
 * @property string $tglrujukan
 * @property string $norujukan
 * @property string $ppkrujukan
 * @property string $ppkpelayanan
 * @property int $jnspelayanan
 * @property string $catatansep
 * @property string $diagnosaawal
 * @property string $politujuan
 * @property int $klsrawat
 * @property string $tglpulang
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class BpjsForm extends \yii\base\Model
{
    public $bpjs_id;
    public $nosep;
    public $nokartuasuransi;
    public $tglsep;
    public $tglrujukan;
    public $norujukan;
    public $ppkrujukan;
    public $ppkpelayanan;
    public $jnspelayanan;
    public $catatansep;
    public $diagnosaawal;
    public $politujuan;
    public $klsrawat;
    public $tglpulang;
    public $user;
    public $no_rekam_medik;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    public $nama_peserta;
    public $kdjenispeserta;
    public $nmjenispeserta;
    public $kdkelastanggungan;
    public $nmkelastanggungan;
    public $nmppkrujukan;
    public $lakalantas;
    public $penjamin;
    public $lokasilaka;
    public $no_telepon;
    public $cob;

    public $isktp;

    public $eksekutif;
    public $jaminan;
    public $notelp;
    public $source_peserta;
    public $asal_rujukan;
    public $asal_rujukan_dummy;
    public $nomor; // no rujukan / bpjs yang akan dijadikan pencarian peserta

    public $jenis_rujukan;
    public $kelas_rawat;
    public $tanggal_sep;
    public $no_rujukan_f;
    public $jenis_pelayanan;
    public $jenis_kartu;
    public $no_kartu;
    public $poli_eksekutif;
    public $poli_tujuan;
    public $ppk_rujukan;
    public $no_rujukan;
    public $no_surat_kontrol;
    public $kode_dpjp;
    public $tanggal_rujukan;
    public $diagnosa_awal;
    public $katarak;
    public $no_telp;
    public $catatan_sep;
    public $kasus_kecelakaan;
    public $no_sep_suplesi;
    public $tanggal_kejadian;
    public $kode_provinsi;
    public $kode_kabupaten;
    public $kode_kecamatan;
    public $keterangan;
    public $pendaftaranol_id;
    public $tgl_lahir;
    public $allow_bpjs;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nosep'], 'required', 'on' => 'edit-pendaftaran'],
            [['tglsep', 'nokartuasuransi', 'tglrujukan', 'norujukan', 'ppkrujukan', 'jnspelayanan', 'diagnosaawal', 'politujuan', 'no_rekam_medik', 'notelp'], 'required', 'on' => 'default'],
            [['tglsep', 'nosep', 'tglrujukan', 'tglpulang', 'created_date', 'last_modified_date', 'deleted_date', 'is_ktp', 'eksekutif', 'penjamin', 'cob', 'nama_peserta' , 'ppkpelayanan', 'catatansep'], 'safe'],
            [['jnspelayanan', 'klsrawat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jnspelayanan', 'klsrawat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['catatansep', 'diagnosaawal', 'additional_data', 'notelp', 'penjamin'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nosep', 'politujuan', 'lakalantas', 'lokasilaka', 'user', 'no_rekam_medik'], 'string', 'max' => 100],
            [['nokartuasuransi', 'norujukan', 'ppkrujukan', 'ppkpelayanan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bpjs_id' => Yii::t('fe','Sep ID'),
            'tglsep' => Yii::t('fe','Tgl SEP'),
            'nosep' => Yii::t('fe','No SEP'),
            'nokartuasuransi' => Yii::t('fe','No Kartu Asuransi'),
            'tglrujukan' => Yii::t('fe','Tgl Rujukan'),
            'norujukan' => Yii::t('fe','No Rujukan'),
            'ppkrujukan' => Yii::t('fe','PPK Rujukan'),
            'ppkpelayanan' => Yii::t('fe','PPK Pelayanan'),
            'jnspelayanan' => Yii::t('fe','Jenis Pelayanan'),
            'catatansep' => Yii::t('fe','Catatan SEP'),
            'diagnosaawal' => Yii::t('fe','Diagnosa Awal'),
            'politujuan' => Yii::t('fe','Poli Tujuan'),
            'klsrawat' => Yii::t('fe','Kelas Rawat'),
            'tglpulang' => Yii::t('fe','Tanggal Pulang'),
            'additional_data' => Yii::t('fe','Additional data'),
            'created_date' => Yii::t('fe','Created date'),
            'created_by' => Yii::t('fe','Created by'),
            'modified_count' => Yii::t('fe','Modified count'),
            'last_modified_date' => Yii::t('fe','Last modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
            'source_peserta' => Yii::t('fe','Pilih '),
            'asal_rujukan_dummy' => Yii::t('fe','Asal rujukan '),
            'cob' => Yii::t('fe','Peserta CoB'),
            'no_rujukan_f' => Yii::t('fe','No. Rujukan'),
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->penjamin = json_encode($this->penjamin);
            return true;
        }
        return false;
    }
}
