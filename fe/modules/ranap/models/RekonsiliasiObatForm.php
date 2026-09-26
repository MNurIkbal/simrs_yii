<?php

namespace app\modules\ranap\models;

use Yii;

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
class RekonsiliasiObatForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    // public static function tableName()
    // {
    //     return 'rekonsiliasiobat_t';
    // }
    public $rekonsiliasiobat_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $is_alergi;
    public $obat_alergi;
    public $is_hamil;
    public $sumber_informasi;
    public $informasi;

    public $obatalkes_id;
    public $nama_obat;
    public $apoteker_id;
    public $qty_obat;
    public $satuan_kecil;
    public $signa;
    public $waktu_pemberian;
    public $dokter_id;

    public $is_lanjut;
    public $catatan;

    public $qty_layak;
    public $qty_tidaklayak;
    public $terapi;
    public $signaterapi;
    public $rute_obat;
    public $rute_kelayakan;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id', 'pasienadmisi_id', 'is_alergi',
                    'is_hamil','sumber_informasi',
                    'obatalkes_id', 'qty_obat', 'satuan_kecil', 
                    'signa', 'waktu_pemberian', 'rute_obat'
                ], 
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong'),
                'on'=>'input_obat'
            ],
            [
                [
                    'rekonsiliasiobat_id', 'pendaftaran_id', 'pasienadmisi_id', 
                    'qty_obat', 'dokter_id', 'qty_layak', 'qty_tidaklayak', 
                ], 
                'default', 
                'value' => null
            ],
            [
                [
                    'rekonsiliasiobat_id', 
                    'pendaftaran_id', 
                    'pasienadmisi_id', 
                    'qty_obat', 
                    'dokter_id', 
                    'qty_layak', 
                    'qty_tidaklayak', 
                    'apoteker_id', 
                    'obatalkes_id', 
                ],
                'integer'
            ],
            [
                [
                    'obat_alergi',
                    'informasi',
                    'tgl_kelayakan', 
                    'is_lanjut', 
                    'is_alergi', 
                    'nama_obat'
                ],
                'safe'
            ],
            [['catatan'], 'string'],
            [['rute_obat', 'waktu_pemberian', 'terapi'], 'string', 'max' => 255],
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
            'rekonsiliasiobat_id' => Yii::t('fe', 'rekonsiliasiobat_id'),
            'pendaftaran_id' => Yii::t('fe', 'pendaftaran_id'),
            'pasienadmisi_id' => Yii::t('fe', 'pasienadmisi_id'),
            'is_alergi' => Yii::t('fe', 'Alergi'),
            'obat_alergi' => Yii::t('fe', 'Obat penyebab alergi'),
            'is_hamil' => Yii::t('fe', 'Hamil / menyusui'),
            'sumber_informasi' => Yii::t('fe', 'Sumber informasi'),
            'informasi' => Yii::t('fe', 'Sumber hubungan'),

            'nama_obat' => Yii::t('fe', 'Nama obat'),
            'qty_obat' => Yii::t('fe', 'Jumlah'),
            'satuan_kecil' => Yii::t('fe', 'Satuan kecil'),
            'signa' => Yii::t('fe', 'Signa'),
            'waktu_pemberian' => Yii::t('fe', 'Waktu pemberian terakhir'),
            'dokter_id' => Yii::t('fe', 'dokter'),

            'is_lanjut' => Yii::t('fe', 'Lanjut'),
            'catatan' => Yii::t('fe', 'Catatan'),

            'qty_layak' => Yii::t('fe', 'Jumlah layak'),
            'qty_tidaklayak' => Yii::t('fe', 'Jumlah tidak layak'),
            'terapi' => Yii::t('fe', 'Terapi'),
            'signaterapi' => Yii::t('fe', 'Signa terapi'),
            'rute_obat' => Yii::t('fe', 'Rute obat'),
            'obatalkes_id' => Yii::t('fe', 'Obat alkes'),
        ];
    }
}
