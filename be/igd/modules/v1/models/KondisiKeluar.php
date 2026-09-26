<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kondisikeluar_m".
 *
 * @property int $kondisikeluar_id
 * @property int $carakeluar_id
 * @property string $kondisikeluar_nama
 * @property string $kondisikeluar_namalain
 * @property string $kondisikeluar_kode
 * @property int $kondisikeluar_urutan
 * @property string $catatan
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
class KondisiKeluar extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kondisikeluar_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_id', 'kondisikeluar_nama', 'kondisikeluar_kode'], 'required'],
            [['carakeluar_id', 'kondisikeluar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['carakeluar_id', 'kondisikeluar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['catatan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kondisikeluar_nama', 'kondisikeluar_namalain', 'kondisikeluar_kode'], 'string', 'max' => 100],
            [['carakeluar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarakeluarM::className(), 'targetAttribute' => ['carakeluar_id' => 'carakeluar_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kondisikeluar_id' => 'Kondisikeluar ID',
            'carakeluar_id' => 'Carakeluar ID',
            'kondisikeluar_nama' => 'Kondisikeluar Nama',
            'kondisikeluar_namalain' => 'Kondisikeluar Namalain',
            'kondisikeluar_kode' => 'Kondisikeluar Kode',
            'kondisikeluar_urutan' => 'Kondisikeluar Urutan',
            'catatan' => 'Catatan',
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
