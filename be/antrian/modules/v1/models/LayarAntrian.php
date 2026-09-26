<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "layarantrian_m".
 *
 * @property int $layarantrian_id
 * @property int $jenisantrian_id FK ke table konfigantrian_m (konfigantrian_id)
 * @property string $layarantrian_nama
 * @property string $layarantrian_judul
 * @property int $konfigantrian_id
 * @property string $layarantrian_latarbelakang
 * @property int $layarantrian_maxitem
 * @property int $layarantrian_itemhigh
 * @property int $layarantrian_itemwidth
 * @property int $layarantrian_intrefresh
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
class LayarAntrian extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'layarantrian_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisantrian_id', 'konfigantrian_id', 'layarantrian_maxitem', 'layarantrian_itemhigh', 'layarantrian_itemwidth', 'layarantrian_intrefresh', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jenisantrian_id', 'konfigantrian_id', 'layarantrian_maxitem', 'layarantrian_itemhigh', 'layarantrian_itemwidth', 'layarantrian_intrefresh', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['layarantrian_nama'], 'required'],
            [['layarantrian_latarbelakang', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['layarantrian_nama'], 'string', 'max' => 100],
            [['layarantrian_judul'], 'string', 'max' => 200],
            [['layarantrian_nama'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'layarantrian_id' => 'Layarantrian ID',
            'jenisantrian_id' => 'Jenisantrian ID',
            'layarantrian_nama' => 'Layarantrian Nama',
            'layarantrian_judul' => 'Layarantrian Judul',
            'konfigantrian_id' => 'Konfigantrian ID',
            'layarantrian_latarbelakang' => 'Layarantrian Latarbelakang',
            'layarantrian_maxitem' => 'Layarantrian Maxitem',
            'layarantrian_itemhigh' => 'Layarantrian Itemhigh',
            'layarantrian_itemwidth' => 'Layarantrian Itemwidth',
            'layarantrian_intrefresh' => 'Layarantrian Intrefresh',
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
    public function getLookup()
    {
        return $this->hasOne(Lookup::className(), ['lookup_id' => 'jenisantrian_id']);
    }

    // Extra fields
    public function extraFields()
    {
        // Return
        return [
            'lookup_m' => function($item) {
                // Return
                return $item->lookup;
            }
        ];
    }
}
