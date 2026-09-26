<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenistarifpenjamin_mp".
 *
 * @property int $jenistarif_id
 * @property int $penjamin_id
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
 * @property JenisTarif $jenistarif
 * @property Penjamin $penjamin
 */
class JenisTarifPenjamin extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenistarifpenjamin_mp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenistarif_id', 'penjamin_id'], 'required'],
            [['jenistarif_id', 'penjamin_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jenistarif_id', 'penjamin_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenistarif_id', 'penjamin_id'], 'unique', 'targetAttribute' => ['jenistarif_id', 'penjamin_id']],
            // [['jenistarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisTarif::className(), 'targetAttribute' => ['jenistarif_id' => 'jenistarif_id']],
            // [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Penjamin::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenistarif_id' => 'Jenistarif ID',
            'penjamin_id' => 'Penjamin ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenistarif()
    {
        return $this->hasOne(JenisTarif::className(), ['jenistarif_id' => 'jenistarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjamin()
    {
        return $this->hasOne(Penjamin::className(), ['penjamin_id' => 'penjamin_id']);
    }

    
    public function extraFields()
    {
        return [
            'penjamin_m' => function($item){
                return $item->penjamin;
            },
            'jenistarif_m' => function($item){
                return $item->jenistarif;
            },
        ];
    }
}
