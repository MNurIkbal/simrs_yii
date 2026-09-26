<?php
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "restriction_obat_m".
 *
 * @property int $restriction_obat_id
 * @property string $restriction_obat_nama
 * @property string $restriction_obat_namalainnya
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
class RestrictionObat extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'restriction_obat_m';
    }

    public function rules()
    {
        return [
            [['restriction_obat_nama', 'restriction_obat_namalainnya', 'additional_data'], 'default', 'value' => null],
            [['restriction_obat_nama'], 'required'],
            [['restriction_obat_nama'], 'uniqueActive'],
            [['restriction_obat_nama', 'restriction_obat_namalainnya', 'additional_data'], 'string'],
            [['created_by', 'created_date', 'modified_count', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * Custom validator untuk unique constraint dengan kondisi is_deleted = false
     */
    public function uniqueActive($attribute, $params)
    {
        $query = self::find()->where([$attribute => $this->$attribute, 'is_active' => true]);
        
        // Jika ini adalah update, exclude current record
        if (!$this->isNewRecord) {
            $query->andWhere(['!=', 'restriction_obat_id', $this->restriction_obat_id]);
        }
        
        if ($query->exists()) {
            $this->addError($attribute, 'Sudah terdapat kategori aktif lain dengan nama yang sama.');
        }
    }

    public function attributeLabels()
    {
        return [
            'restriction_obat_id' => 'ID',
            'restriction_obat_nama' => 'Nama Restriction',
            'restriction_obat_namalainnya' => 'Nama Lain',
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
        ];
    }
} 