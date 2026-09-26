<?php

namespace app\modules\master\models;

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
 * @property string $
 * @property string $ruangan_image
 * @property integer $ruangan_urutan
 * @property string $ruangan_filesuara
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
 */
class RuanganForm extends \yii\base\Model
{
    public $ruangan_id;
    public $instalasi_id;
    public $ruangan_nama;
    public $ruangan_namalainnya;
    public $ruangan_jenispelayanan;
    public $ruangan_singkatan;
    public $ruangan_fasilitas;
    public $ruangan_image;
    public $ruangan_image_blob;
    public $ruangan_urutan;
    public $ruangan_filesuara;
    public $ruangan_filesuara_blob;
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
    public $is_modul;
    public $is_store;
    public $is_mainstore;
    public $is_substore;
    public $is_cartstore;
    public $lantairuangan_id;
    public $is_online;
    public $kode_ruangan_bpjs;
    public $nama_ruangan_bpjs;
    public $satusehat_ruangan;
    
    /**
     * @inheritdoc
     */
   /* public static function tableName()
    {
        return 'ruangan_m';
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'ruangan_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['instalasi_id', 'ruangan_nama'], 'required'],
            [['ruangan_fasilitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','is_modul', 'is_active','lantairuangan_id', 'ruangan_image_blob', 'ruangan_filesuara_blob', 'kode_ruangan_bpjs', 'nama_ruangan_bpjs', 'satusehat_ruangan'], 'safe'],
            [['is_deleted', 'is_active','is_modul','is_store','is_mainstore','is_substore','is_cartstore','is_online'], 'boolean'],
            [['ruangan_nama', 'ruangan_namalainnya', 'ruangan_jenispelayanan'], 'string', 'max' => 50],
            [['ruangan_singkatan'], 'string', 'max' => 3],
            [['kode_ruanganpoli'], 'string', 'max' => 15],
            [['ruangan_image'], 'string', 'max' => 100],
            [['ruangan_filesuara'], 'string', 'max' => 500],
            [['ruangan_image', 'ruangan_filesuara'],'file'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'is_modul' => Yii::t('fe', 'Status modul'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'ruangan_nama' => Yii::t('fe', 'Nama ruangan'),
            'ruangan_namalainnya' => Yii::t('fe', 'Nama lainnya'),
            'ruangan_jenispelayanan' => Yii::t('fe', 'Jenis pelayanan'),
            'ruangan_singkatan' => Yii::t('fe', 'Nama singkatan ruangan'),
            'ruangan_fasilitas' => Yii::t('fe', 'Fasilitas'),
            'ruangan_lokasi' => Yii::t('fe', 'Lokasi'),
            'ruangan_image' => Yii::t('fe', 'Gambar ruangan'),
            'ruangan_image_blob' => Yii::t('fe', 'Gambar ruangan'),
            'ruangan_urutan' => Yii::t('fe', 'Urutan'),
            'ruangan_filesuara' => Yii::t('fe', 'File suara'),
            'ruangan_filesuara_blob' => Yii::t('fe', 'File suara'),
            'estimasipelayanan' => Yii::t('fe', 'Estimasi pelayanan'),
            'image_mobile' => Yii::t('fe', 'Image mobile'),
            'warnadokrm_id' => Yii::t('fe', 'Warnadokrm ID'),
            'kode_ruanganpoli' => Yii::t('fe', 'Kode ruangan poli'),
            'additional_data' => \Yii::t('fe','Additional data'),
            'created_date' => \Yii::t('fe','Created date'),
            'created_by' => \Yii::t('fe','Created by'),
            'modified_count' => \Yii::t('fe','Modified count'),
            'last_modified_date' => \Yii::t('fe','Last modified date'),
            'last_modified_by' => \Yii::t('fe','Last modified by'),
            'is_deleted' => \Yii::t('fe','Is deleted'),
            'is_active' => \Yii::t('fe','Status'),
            'deleted_date' => \Yii::t('fe','Deleted date'),
            'deleted_by' => \Yii::t('fe','Deleted by'),
            'lantairuangan_id' => Yii::t('fe', 'Lantai'),
            'is_online' => Yii::t('fe', 'Ruangan Online'),
            'satusehat_ruangan' => Yii::t('fe', 'Satu Sehat Location ID'),
        ];
    }
}
