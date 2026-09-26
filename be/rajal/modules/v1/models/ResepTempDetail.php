<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "reseptempdetail_m".
 *
 * @property int $reseptempdetail_id
 * @property int $reseptemp_id
 * @property int $racikan_id
 * @property int $rke
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $qty
 * @property int $signa_id
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
class ResepTempDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reseptempdetail_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reseptempdetail_id', 'reseptemp_id', 'racikan_id', 'rke', 'obatalkes_id', 'satuankecil_id', 'qty', 'signa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['reseptempdetail_id', 'reseptemp_id', 'racikan_id', 'rke', 'obatalkes_id', 'satuankecil_id', 'signa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'signa'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['reseptempdetail_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reseptempdetail_id' => 'Reseptempdetail ID',
            'reseptemp_id' => 'Reseptemp ID',
            'racikan_id' => 'Racikan ID',
            'rke' => 'Rke',
            'obatalkes_id' => 'Obatalkes ID',
            'satuankecil_id' => 'Satuankecil ID',
            'qty' => 'Qty',
            'signa_id' => 'Signa ID',
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
