<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kecamatan_m".
 *
 * @property integer $kecamatan_id
 * @property integer $kabupaten_id
 * @property string $kecamatan_nama
 * @property string $kecamatan_namalainnya
 * @property string $longitude
 * @property string $latitude
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
class PenanggungJawab extends \Doco\components\DocoActiveRecord
{
    
    // public $penanggungjawab_id;
    // public $pengantar;
    // public $jenisidentitas;
    // public $no_identitas;
    // public $hubungankeluarga;
    // public $penanggungjawab_nama;
    // public $penanggungjawab_tempatlahir;
    // public $penanggungjawab_tgllahir;
    // public $penanggungjawab_jeniskelamin;
    // public $penanggungjawab_alamat;
    // public $penanggungjawab_notelp;
    // public $penanggungjawab_nohp;
    // public $additional_data;
    // public $created_date;
    // public $created_by;
    // public $modified_count;
    // public $last_modified_date;
    // public $last_modified_by;
    // public $is_deleted;
    // public $is_active;
    // public $deleted_date;
    // public $deleted_by;
    // public $umur;
    // public $pasien_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penanggungjawab_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['kabupaten_id', 'kecamatan_nama'], 'required'],
            // [['kabupaten_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            // [['longitude', 'latitude', 'additional_data'], 'string'],
            // [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            // [['is_deleted', 'is_active'], 'boolean'],
            // [['kecamatan_nama', 'kecamatan_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penanggungjawab_id' => \Yii::t('app','penanggungjawab_id'),
            'pengantar' => \Yii::t('app','pengantar'),
            'jenisidentitas' => \Yii::t('app','jenisidentitas'),
            'no_identitas' => \Yii::t('app','no_identitas'),
            'pasien_id' => \Yii::t('app','Pasien ID'),
            'hubungankeluarga' => \Yii::t('app','hubungankeluarga'),
            'penanggungjawab_nama' => \Yii::t('app','penanggungjawab_nama'),
            'penanggungjawab_tempatlahir' => \Yii::t('app','penanggungjawab_tempatlahir'),
            'penanggungjawab_tgllahir' => \Yii::t('app','penanggungjawab_tgllahir'),
            'penanggungjawab_jeniskelamin' => \Yii::t('app','penanggungjawab_jeniskelamin'),
            'penanggungjawab_alamat' => \Yii::t('app','penanggungjawab_alamat'),
            'penanggungjawab_notelp' => \Yii::t('app','penanggungjawab_notelp'),
            'penanggungjawab_nohp' => \Yii::t('app','penanggungjawab_nohp'),
            'additional_data' => \Yii::t('app','Additional data'),
            'created_date' => \Yii::t('app','Created date'),
            'created_by' => \Yii::t('app','Created by'),
            'modified_count' => \Yii::t('app','Modified count'),
            'last_modified_date' => \Yii::t('app','Last modified date'),
            'last_modified_by' => \Yii::t('app','Last modified by'),
            'is_deleted' => \Yii::t('app','Is deleted'),
            'is_active' => \Yii::t('app','Status'),
            'deleted_date' => \Yii::t('app','Deleted date'),
            'deleted_by' => \Yii::t('app','Deleted by'),
        ];
    }
}
