<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kelompoktindakan_m".
 *
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property string $kelompoktindakan_namalainnya
 * @property double $kelompoktindakan_persencyto
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
 * @property double $kelompoktindakan_persendiskon
 * @property string $kelompoktindakan_kode
 * @property string $catatan
 *
 * @property DaftartindakanM[] $daftartindakanMs
 * @property KomponenjasaM[] $komponenjasaMs
 */
class KelompokTindakanForm extends \yii\base\Model
{
    public $detail;
    public $kelompoktindakan_id;
    public $kelompoktindakan_kode;
    public $kelompoktindakan_nama;
    public $kelompoktindakan_namalainnya;
    public $kelompoktindakan_persencyto;
    public $kelompoktindakan_persendiskon;
    public $catatan;
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
        return 'kelompoktindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompoktindakan_nama', 'kelompoktindakan_kode', 'kelompoktindakan_namalainnya'], 'required'],
            [['kelompoktindakan_persencyto', 'kelompoktindakan_persendiskon'], 'number'],
            [['additional_data', 'catatan'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompoktindakan_nama', 'kelompoktindakan_namalainnya'], 'string', 'max' => 50],
            [['kelompoktindakan_kode'], 'string', 'max' => 100],
            [['kelompoktindakan_persencyto', 'kelompoktindakan_persendiskon'], 'number', 'numberPattern' => '/^[0-9]{1,2}(\.([0-9]{0,2})){0,1}$/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'kelompoktindakan_nama' => 'Nama Kelompok',
            'kelompoktindakan_namalainnya' => 'Nama Lainnya',
            'kelompoktindakan_persencyto' => 'Persen Cyto',
            'kelompoktindakan_persendiskon' => 'Persen Diskon',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'kelompoktindakan_kode' => 'Kode Kelompok',
            'catatan' => 'Catatan',
        ];
    }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDaftartindakanMs()
    // {
    //     return $this->hasMany(DaftartindakanM::className(), ['kelompoktindakan_id' => 'kelompoktindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKomponenjasaMs()
    // {
    //     return $this->hasMany(KomponenjasaM::className(), ['kelompoktindakan_id' => 'kelompoktindakan_id']);
    // }
}
