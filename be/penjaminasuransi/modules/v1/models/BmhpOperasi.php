<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "bmhpoperasi_t".
 *
 * @property int $bmhpoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi
 * @property int $obatalkes_id
 * @property int $persediaan
 * @property int $tambahan
 * @property int $terpakai
 * @property int $sisa
 * @property bool $is_ditagihkan
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
class BmhpOperasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bmhpoperasi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'obatalkes_id', 'persediaan', 'tambahan', 'terpakai', 'sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'obatalkes_id', 'persediaan', 'tambahan', 'terpakai', 'sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_ditagihkan', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bmhpoperasi_id' => 'Bmhpoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'inpostoperasi_id' => 'Inpostoperasi',
            'obatalkes_id' => 'Obatalkes ID',
            'persediaan' => 'Persediaan',
            'tambahan' => 'Tambahan',
            'terpakai' => 'Terpakai',
            'sisa' => 'Sisa',
            'is_ditagihkan' => 'Is Ditagihkan',
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
