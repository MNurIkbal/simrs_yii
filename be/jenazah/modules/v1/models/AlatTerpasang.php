<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "alatterpasang_t".
 *
 * @property int $alatterpasang_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property string $tgl_alatterpasang
 * @property int $barang_id
 * @property int $obatalkes_id
 * @property int $qty
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
class AlatTerpasang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alatterpasang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'ruangan_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'barang_id', 'obatalkes_id', 'qty', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'barang_id', 'obatalkes_id', 'qty', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_alatterpasang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'alatterpasang_id' => 'Alatterpasang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'tgl_alatterpasang' => 'Tgl Alatterpasang',
            'barang_id' => 'Barang ID',
            'obatalkes_id' => 'Obatalkes ID',
            'qty' => 'Qty',
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
