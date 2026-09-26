<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "layarantrian_m".
 *
 * @property int $layarantrian_id
 * @property string $layarantrian_jenis
 * @property string $layarantrian_nama
 * @property string $layarantrian_judul
 * @property string $layarantrian_fungsi
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
 *
 * @property LayarruanganM[] $layarruanganMs
 * @property RuanganM[] $ruangans
 */
class Layarantrian extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'layarantrian_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['layarantrian_jenis', 'layarantrian_nama', 'layarantrian_judul'], 'required'],
            [['layarantrian_latarbelakang', 'additional_data'], 'string'],
            [['layarantrian_maxitem', 'layarantrian_itemhigh', 'layarantrian_itemwidth', 'layarantrian_intrefresh', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['layarantrian_maxitem', 'layarantrian_itemhigh', 'layarantrian_itemwidth', 'layarantrian_intrefresh', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['layarantrian_jenis', 'layarantrian_nama'], 'string', 'max' => 100],
            [['layarantrian_judul'], 'string', 'max' => 200],
            [['layarantrian_fungsi'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'layarantrian_id' => 'Layarantrian ID',
            'layarantrian_jenis' => 'Layarantrian Jenis',
            'layarantrian_nama' => 'Layarantrian Nama',
            'layarantrian_judul' => 'Layarantrian Judul',
            'layarantrian_fungsi' => 'Layarantrian Fungsi',
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
    public function getLayarruanganMs()
    {
        return $this->hasMany(LayarruanganM::className(), ['layarantrian_id' => 'layarantrian_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangans()
    {
        return $this->hasMany(RuanganM::className(), ['ruangan_id' => 'ruangan_id'])->viaTable('layarruangan_m', ['layarantrian_id' => 'layarantrian_id']);
    }
}
