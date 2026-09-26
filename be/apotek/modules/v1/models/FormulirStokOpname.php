<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "formulirstokopname_t".
 *
 * @property int $formulirstokopname_id
 * @property int $stokopname_id
 * @property string $tglformulir
 * @property string $noformulir
 * @property double $totalvolume
 * @property double $totalharga
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
 * @property int $ruangan_id
 */
class FormulirStokOpname extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formulirstokopname_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id'], 'required'],
            [['stokopname_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangan_id'], 'default', 'value' => null],
            [['stokopname_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangan_id'], 'integer'],
            [['tglformulir', 'created_date', 'last_modified_date', 'deleted_date','formulirstokopname_id'], 'safe'],
            [['noformulir', 'additional_data'], 'string'],
            [['totalvolume', 'totalharga'], 'number'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formulirstokopname_id' => 'Formulirstokopname ID',
            'stokopname_id' => 'Stokopname ID',
            'tglformulir' => 'Tglformulir',
            'noformulir' => 'Noformulir',
            'totalvolume' => 'Totalvolume',
            'totalharga' => 'Totalharga',
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
            'ruangan_id' => 'Ruangan ID',
        ];
    }
}
