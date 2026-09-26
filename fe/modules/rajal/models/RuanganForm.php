<?php

/**
 * @Author: afil
 * @Date:   2018-01-04 16:36:56
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 17:27:51
 * @Description: model validasi frontend ruangan_m
 */

namespace app\modules\rajal\models;

use Yii;

/**
 * This is the model class for table "ruangan_m".
 *
 * @property integer $ruangan_id
 * @property integer $instalasi_id
 * @property string $ruangan_nama
 * @property string $ruangan_namalainnya
 * @property string $ruangan_jenispelayanan
 * @property string $ruangan_singkatan
 * @property string $ruangan_fasilitas
 * @property string $ruangan_lokasi
 * @property string $ruangan_image
 * @property integer $ruangan_urutan
 * @property string $ruangan_filesuara
 * @property integer $estimasipelayanan
 * @property string $image_mobile
 * @property integer $warnadokrm_id
 * @property string $kode_ruanganpoli
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property integer $unitkerja_id
 */
class RuanganForm extends \yii\base\Model
{
    public $ruangan_id;
    public $instalasi_id;
    public $instalasi_nama;
    public $ruangan_nama;
    public $ruangan_namalainnya;
    public $ruangan_jenispelayanan;
    public $ruangan_singkatan;
    public $ruangan_fasilitas;
    public $ruangan_lokasi;
    public $ruangan_image;
    public $ruangan_urutan;
    public $ruangan_filesuara;
    public $estimasipelayanan;
    public $image_mobile;
    public $warnadokrm_id;
    public $kode_ruanganpoli;
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
    public $unitkerja_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruangan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'ruangan_urutan', 'estimasipelayanan', 'warnadokrm_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'unitkerja_id'], 'integer'],
            [['ruangan_nama', 'unitkerja_id'], 'required'],
            [['ruangan_fasilitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['ruangan_nama', 'ruangan_namalainnya', 'ruangan_jenispelayanan', 'ruangan_lokasi'], 'string', 'max' => 50],
            [['ruangan_singkatan', 'kode_ruanganpoli'], 'string', 'max' => 3],
            [['ruangan_image'], 'string', 'max' => 100],
            [['ruangan_filesuara', 'image_mobile'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'ruangan_nama' => Yii::t('fe', 'Nama ruangan'),
            'ruangan_namalainnya' => Yii::t('fe', 'Nama lainnya'),
            'ruangan_jenispelayanan' => Yii::t('fe', 'Jenis pelayanan'),
            'ruangan_singkatan' => Yii::t('fe', 'Singkatan'),
            'ruangan_fasilitas' => Yii::t('fe', 'Fasilitas'),
            'ruangan_lokasi' => Yii::t('fe', 'Lokasi'),
            'ruangan_image' => Yii::t('fe', 'Image'),
            'ruangan_urutan' => Yii::t('fe', 'Urutan'),
            'ruangan_filesuara' => Yii::t('fe', 'File suara'),
            'estimasipelayanan' => Yii::t('fe', 'Estimasi pelayanan'),
            'image_mobile' => Yii::t('fe', 'Image mobile'),
            'warnadokrm_id' => Yii::t('fe', 'Warnadokrm ID'),
            'kode_ruanganpoli' => Yii::t('fe', 'Kode ruangan poli'),
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
        ];
    }
}
