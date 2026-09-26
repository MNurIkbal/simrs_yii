<?php

namespace app\modules\rm\models;

use Yii;

/**
 * This is the model class for table "subrak_m".
 *
 * @property int $subrak_id
 * @property string $subrak_nama
 * @property string $subrak_namalainnya
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
 * @property int $lokasirak_id
 *
 * @property DokrekammedisM[] $dokrekammedisMs
 * @property LokasirakM $lokasirak
 */
class SubRakForm extends \yii\base\Model
{
    public $subrak_nama;
    public $subrak_namalainnya;
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
    public $lokasirak_id;
    public $lokasirak_nama;
    public $lokasirak_m;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subrak_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subrak_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','lokasirak_m'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'lokasirak_id'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'lokasirak_id'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['subrak_nama', 'subrak_namalainnya'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'subrak_id' => 'Subrak ID',
            'subrak_nama' => \Yii::t('fe', 'No Sub Rak'),
            'subrak_namalainnya' => \Yii::t('fe', 'Keterangan'),
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
            'lokasirak_id' => 'No Rak',
        ];
    }
}
