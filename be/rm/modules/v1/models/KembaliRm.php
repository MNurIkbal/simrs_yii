<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kembalirm_t".
 *
 * @property int $kembalirm_id
 * @property int $pengirimanrm_id
 * @property int $pendaftaran_id
 * @property int $peminjamanrm_id
 * @property int $pasien_id
 * @property int $dokrekammedis_id
 * @property string $petugaspenerima_id
 * @property int $ruanganasal_id
 * @property string $tglkembali
 * @property bool $status_indexing
 * @property string $keterangan_pengembalian
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
 * @property bool $status_assembling
 *
 * @property PengirimanrmT[] $pengirimanrmTs
 */
class KembaliRm extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kembalirm_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pengirimanrm_id', 'pendaftaran_id', 'peminjamanrm_id', 'pasien_id', 'dokrekammedis_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pengirimanrm_id', 'pendaftaran_id', 'peminjamanrm_id', 'pasien_id', 'dokrekammedis_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pendaftaran_id', 'dokrekammedis_id', 'tglkembali'], 'required'],
            [['tglkembali', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['status_indexing', 'is_deleted', 'is_active', 'status_assembling'], 'boolean'],
            [['keterangan_pengembalian', 'additional_data'], 'string'],
            [['petugaspenerima_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kembalirm_id' => 'Kembalirm ID',
            'pengirimanrm_id' => 'Pengirimanrm ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'peminjamanrm_id' => 'Peminjamanrm ID',
            'pasien_id' => 'Pasien ID',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'petugaspenerima_id' => 'Petugaspenerima ID',
            'ruanganasal_id' => 'Ruanganasal ID',
            'tglkembali' => 'Tglkembali',
            'status_indexing' => 'Status Indexing',
            'keterangan_pengembalian' => 'Keterangan Pengembalian',
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
            'status_assembling' => 'Status Assembling',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrmTs()
    {
        return $this->hasMany(PengirimanrmT::className(), ['kembalirm_id' => 'kembalirm_id']);
    }
}
