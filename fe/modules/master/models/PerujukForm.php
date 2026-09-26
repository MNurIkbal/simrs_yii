<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "perujuk_m".
 *
 * @property integer $perujuk_id
 * @property integer $asalrujukan_id
 * @property string $namaperujuk
 * @property string $spesialis
 * @property string $kode_rujukan
 * @property string $alamatlengkap
 * @property string $notelp
 * @property string $kodeppk
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
 *
 * @property AsalrujukanM $asalrujukan
 */
class PerujukForm extends \yii\base\Model
{
    
    public $perujuk_id;
    public $asalrujukan_id;
    public $namaperujuk;
    public $spesialis;
    public $perujuk_kode;
    public $alamatlengkap;
    public $notelp;
    public $kodeppk;
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'perujuk_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
       return [
            [['asalrujukan_id', 'namaperujuk'], 'required'],
            [['asalrujukan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alamatlengkap', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['namaperujuk', 'notelp'], 'string', 'max' => 100],
            [['spesialis'], 'string', 'max' => 50],
            [['kodeppk'], 'string', 'max' => 20],
            [['perujuk_kode'], 'string', 'max' => 50]
        /*    [['asalrujukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsalRujukanForm::className(), 'targetAttribute' => ['asalrujukan_id' => 'asalrujukan_id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'perujuk_id' => Yii::t('fe', 'Perujuk ID'),
            'asalrujukan_id' => Yii::t('fe', 'Asal Perujuk'),
            'namaperujuk' => Yii::t('fe', 'Nama Perujuk'),
            'spesialis' => Yii::t('fe', 'Spesialis'),
            'alamatlengkap' => Yii::t('fe', 'Alamat Lengkap'),
            'notelp' => Yii::t('fe', 'No Telp'),
            'kodeppk' => Yii::t('fe','Kodeppk'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }
}
