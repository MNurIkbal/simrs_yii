<?php

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoActiveRecord;

/**
 * This is the model class for table "rekonsiliasiobat_t".
 *
 * @property int $rekonsiliasiobat_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $tgl_rekonsiliasi
 * @property string $nama_obat
 * @property int $qty_obat
 * @property string $satuan_kecil
 * @property string $rute_obat
 * @property string $signa
 * @property string $waktu_pemberian
 * @property bool $is_lanjut
 * @property string $catatan
 * @property int $dokter_id
 * @property int $qty_layak
 * @property int $qty_tidaklayak
 * @property string $terapi
 * @property string $signaterapi
 * @property string $rute_kelayakan
 * @property int $apoteker_id
 * @property string $tgl_kelayakan
 * @property int $obatalkes_id
 * @property int $is_alergi
 * @property string $obat_alergi
 * @property bool $is_hamil
 * @property int $sumber_informasi 0=pasien, 1=keluarga, 2=lain-lain
 * @property string $informasi
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
class RekonsiliasiObat extends DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rekonsiliasiobat_t';
    }

    // public function beforeSave($insert=null) {
    //     return parent::beforeSave($insert=null);
    // }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'nama_obat', 'qty_obat'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'qty_obat', 'dokter_id', 'qty_layak', 'qty_tidaklayak', 'apoteker_id', 'obatalkes_id', 'is_alergi', 'sumber_informasi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['rekonsiliasiobat_id', 'pendaftaran_id', 'pasienadmisi_id', 'qty_obat', 'dokter_id', 'qty_layak', 'qty_tidaklayak', 'apoteker_id', 'obatalkes_id', 'is_alergi', 'sumber_informasi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_by', 'tgl_rekonsiliasi', 'tgl_kelayakan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_lanjut', 'is_hamil', 'is_deleted', 'is_active'], 'boolean'],
            [['catatan', 'obat_alergi', 'additional_data'], 'string'],
            [['nama_obat', 'rute_obat', 'waktu_pemberian', 'terapi', 'rute_kelayakan', 'informasi'], 'string', 'max' => 255],
            [['satuan_kecil', 'signa', 'signaterapi'], 'string', 'max' => 100],
            [['rekonsiliasiobat_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rekonsiliasiobat_id' => Yii::t('app', 'Rekonsiliasiobat ID'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran ID'),
            'pasienadmisi_id' => Yii::t('app', 'Pasienadmisi ID'),
            'tgl_rekonsiliasi' => Yii::t('app', 'Tgl Rekonsiliasi'),
            'nama_obat' => Yii::t('app', 'Nama Obat'),
            'qty_obat' => Yii::t('app', 'Qty Obat'),
            'satuan_kecil' => Yii::t('app', 'Satuan Kecil'),
            'rute_obat' => Yii::t('app', 'Rute Obat'),
            'signa' => Yii::t('app', 'Signa'),
            'waktu_pemberian' => Yii::t('app', 'Waktu Pemberian'),
            'is_lanjut' => Yii::t('app', 'Is Lanjut'),
            'catatan' => Yii::t('app', 'Catatan'),
            'dokter_id' => Yii::t('app', 'Dokter ID'),
            'qty_layak' => Yii::t('app', 'Qty Layak'),
            'qty_tidaklayak' => Yii::t('app', 'Qty Tidaklayak'),
            'terapi' => Yii::t('app', 'Terapi'),
            'signaterapi' => Yii::t('app', 'Signaterapi'),
            'rute_kelayakan' => Yii::t('app', 'Rute Kelayakan'),
            'apoteker_id' => Yii::t('app', 'Apoteker ID'),
            'tgl_kelayakan' => Yii::t('app', 'Tgl Kelayakan'),
            'obatalkes_id' => Yii::t('app', 'Obatalkes ID'),
            'is_alergi' => Yii::t('app', 'Is Alergi'),
            'obat_alergi' => Yii::t('app', 'Obat Alergi'),
            'is_hamil' => Yii::t('app', 'Is Hamil'),
            'sumber_informasi' => Yii::t('app', 'Sumber Informasi'),
            'informasi' => Yii::t('app', 'Informasi'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
}
