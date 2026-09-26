<?php

namespace app\modules\master\models;

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
class PenanggungjawabForm extends \yii\base\Model
{
    
    public $penanggungjawab_id;
    public $pengantar;
    public $jenisidentitas;
    public $no_identitas;
    public $hubungankeluarga;
    public $penanggungjawab_nama;
    public $penanggungjawab_tempatlahir;
    public $penanggungjawab_tgllahir;
    public $penanggungjawab_jeniskelamin;
    public $penanggungjawab_alamat;
    public $penanggungjawab_notelp;
    public $penanggungjawab_nohp;
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
    public $umur;

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
            'penanggungjawab_id' => \Yii::t('fe','penanggungjawab_id'),
            'pengantar' => \Yii::t('fe','pengantar'),
            'jenisidentitas' => \Yii::t('fe','jenisidentitas'),
            'no_identitas' => \Yii::t('fe','no_identitas'),
            'hubungankeluarga' => \Yii::t('fe','hubungankeluarga'),
            'penanggungjawab_tempatlahir' => \Yii::t('fe','penanggungjawab_tempatlahir'),
            'penanggungjawab_tgllahir' => \Yii::t('fe','penanggungjawab_tgllahir'),
            'penanggungjawab_jeniskelamin' => \Yii::t('fe','penanggungjawab_jeniskelamin'),
            'penanggungjawab_alamat' => \Yii::t('fe','penanggungjawab_alamat'),
            'penanggungjawab_notelp' => \Yii::t('fe','penanggungjawab_notelp'),
            'penanggungjawab_nohp' => \Yii::t('fe','penanggungjawab_nohp'),
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
        ];
    }
}
