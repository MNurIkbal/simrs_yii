<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ruanganpegawai_mp".
 *
 * @property int $ruangan_id
 * @property int $pegawai_id
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
 * @property PegawaiM $pegawai
 * @property RuanganM $ruangan
 */
class RuanganPegawai extends \Doco\components\DocoActiveRecord
{

    public static function primaryKey()
    {
        return ['ruangan_id', 'pegawai_id'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruanganpegawai_mp';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'pegawai_id'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['ruangan_id', 'pegawai_id'], 'unique', 'targetAttribute' => ['ruangan_id', 'pegawai_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
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
    public function getPegawai()
    {
        return $this->hasMany(Pegawai::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasMany(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }
    public function extraFields()
    {
        return [
            'pegawai_m' => function($item){
                return $item->pegawai;
            }
        ];
    }
}
