<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "warnadokrekammedik_m".
 *
 * @property int $warnadokrm_id
 * @property string $warnadokrm_namawarna
 * @property string $warnadokrm_kodewarna
 * @property string $warnadokrm_fungsi
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
class WarnaDokumenForm extends \yii\base\Model
{
    public $warnadokrm_kodewarna;
    public $warnadokrm_namawarna;
    public $warnadokrm_fungsi;
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
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warnadokrekammedik_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warnadokrm_namawarna'], 'required','message'=>'Dokumen Warna tidak boleh kosong'],
            [['warnadokrm_kodewarna'],'required','message'=> 'Digit Pertama Nomor Primer tidak boleh kosong'],
            [['warnadokrm_fungsi', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['warnadokrm_namawarna', 'warnadokrm_kodewarna'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'warnadokrm_id' => Yii::t('fe', 'Warna Id'),
            'warnadokrm_namawarna' => Yii::t('fe', 'Nama Warna'),
            'warnadokrm_kodewarna' => Yii::t('fe', 'Kode Warna'),
            'warnadokrm_fungsi' => Yii::t('fe', 'Fungsi'),
            'additional_data' => Yii::t('fe','Additional data'),
            'created_date' => Yii::t('fe','Created date'),
            'created_by' => Yii::t('fe','Created by'),
            'modified_count' => Yii::t('fe','Modified count'),
            'last_modified_date' => Yii::t('fe','Last modified date'),
            'last_modified_by' => Yii::t('fe','Last modified by'),
            'is_deleted' => Yii::t('fe','Is deleted'),
            'is_active' => Yii::t('fe','Status'),
            'deleted_date' => Yii::t('fe','Deleted date'),
            'deleted_by' => Yii::t('fe','Deleted by'),
        ];
    }
}
