<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * This is the model class for table "sep_t".
 *
 * @property int $sep_id
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
class SepForm extends \yii\base\Model
{
    public $sep_id;
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
    public $nomr;
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

    public $namapeserta;
    public $kdjenispeserta;
    public $nmjenispeserta;
    public $kdkelastanggungan;
    public $nmkelastanggungan;
    public $nmppkrujukan;
    public $lakalantas;
    public $lokasilaka;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sep_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tglsep', 'nosep', 'nokartuasuransi', 'tglrujukan', 'norujukan', 'ppkrujukan', 'ppkpelayanan', 'jnspelayanan', 'catatansep', 'diagnosaawal', 'politujuan'], 'required'],
            [['tglsep', 'tglrujukan', 'tglpulang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['jnspelayanan', 'klsrawat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jnspelayanan', 'klsrawat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['catatansep', 'diagnosaawal', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nosep', 'politujuan', 'lakalantas', 'lokasilaka', 'user', 'nomr'], 'string', 'max' => 100],
            [['nokartuasuransi', 'norujukan', 'ppkrujukan', 'ppkpelayanan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sep_id' => Yii::t('fe','Sep ID'),
            'tglsep' => Yii::t('fe','Tglsep'),
            'nosep' => Yii::t('fe','Nosep'),
            'nokartuasuransi' => Yii::t('fe','Nokartuasuransi'),
            'tglrujukan' => Yii::t('fe','Tglrujukan'),
            'norujukan' => Yii::t('fe','Norujukan'),
            'ppkrujukan' => Yii::t('fe','Ppkrujukan'),
            'ppkpelayanan' => Yii::t('fe','Ppkpelayanan'),
            'jnspelayanan' => Yii::t('fe','Jnspelayanan'),
            'catatansep' => Yii::t('fe','Catatansep'),
            'diagnosaawal' => Yii::t('fe','Diagnosaawal'),
            'politujuan' => Yii::t('fe','Politujuan'),
            'klsrawat' => Yii::t('fe','Klsrawat'),
            'tglpulang' => Yii::t('fe','Tglpulang'),
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
        ];
    }
}
