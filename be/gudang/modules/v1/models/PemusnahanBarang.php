<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pemusnahanbarang_t".
 *
 * @property int $pemusnahanbarang_id
 * @property string $tglpemusnahan
 * @property string $nopemusnahan
 * @property string $keterangan
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property int $pegawaimengetahui_id
 * @property int $pegawaimenyetujui_id
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
class PemusnahanbarangT extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemusnahanbarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tglpemusnahan', 'nopemusnahan', 'ruangan_id', 'pegawai_id'], 'required'],
            [['tglpemusnahan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan', 'additional_data'], 'string'],
            [['ruangan_id', 'pegawai_id', 'pegawaimengetahui_id', 'pegawaimenyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'pegawaimengetahui_id', 'pegawaimenyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nopemusnahan'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemusnahanbarang_id' => 'Pemusnahanbarang ID',
            'tglpemusnahan' => 'Tglpemusnahan',
            'nopemusnahan' => 'Nopemusnahan',
            'keterangan' => 'Keterangan',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'pegawaimenyetujui_id' => 'Pegawaimenyetujui ID',
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
