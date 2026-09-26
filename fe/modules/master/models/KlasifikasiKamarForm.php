<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "ruangan_m".
 *
 * @property integer $klasifikasikamar_id
 * @property integer $sirsonline_id
 * @property string $eiscovid_id
 * @property string $applicare_id
 * @property string $spgdt_id
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
class KlasifikasiKamarForm extends \yii\base\Model
{
    public $klasifikasikamar_id;
    public $klasifikasikamar_nama;
    public $sirsonline_id;
    public $eiscovid_id;
    public $applicare_id;
    public $spgdt_id;
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
    public $kodekelas_aplicare;
    public $namakelas_aplicare;
    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['klasifikasikamar_nama'], 'required'],
            [['sirsonline_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            //[[''], 'required'],
            [['created_date', 'last_modified_date', 'deleted_date','is_modul', 'is_active','klasifikasikamar_nama','sirsonline_id','eiscovid_id','applicare_id','spgdt_id', 'kodekelas_aplicare', 'namakelas_aplicare'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'klasifikasikamar_id' => Yii::t('fe', 'Klasifikasi'),
            'klasifikasikamar_nama' => Yii::t('fe', 'Nama Klasifikasi'),
            'sirsonline_id' => Yii::t('fe', 'SIRS Online'),
            'eiscovid_id' => Yii::t('fe', 'EIS Covid'),
            'applicare_id' => Yii::t('fe', 'Applicare'),
            'spgdt_id' => Yii::t('fe', 'SPGDT'),
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
            'kodekelas_aplicare' => 'Applicare'
        ];
    }
}   
