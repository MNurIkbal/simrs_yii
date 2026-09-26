<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\FormSoBarangDetail;
/**
 * This is the model class for table "formsobarang_t".
 *
 * @property int $formsobarang_id
 * @property int $stokopnamebarang_id
 * @property int $ruangan_id
 * @property string $tglformulir
 * @property string $noformulir
 * @property double $total_harganetto
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
class FormSoBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formsobarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stokopnamebarang_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['stokopnamebarang_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['ruangan_id'], 'required'],
            [['tglformulir', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['noformulir', 'additional_data'], 'string'],
            [['total_harganetto'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['noformulir'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formsobarang_id' => 'Formsobarang ID',
            'stokopnamebarang_id' => 'Stokopnamebarang ID',
            'ruangan_id' => 'Ruangan ID',
            'tglformulir' => 'Tglformulir',
            'noformulir' => 'Noformulir',
            'total_harganetto' => 'Total Harganetto',
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

    public function getDetail()
    {
        return $this->hasMany(FormSoBarangDetail::className(),['formsobarang_id'=> 'formsobarang_id']);
    }
}
