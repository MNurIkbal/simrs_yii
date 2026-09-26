<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rekomendasibarang_t".
 *
 * @property int $rekomendasibarang_id
 * @property string $no_rekomendasibarang
 * @property string $tgl_rekomendasibarang
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property int $status_po lookup_type='status_po'
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
class RekomendasiBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rekomendasibarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'no_rekomendasibarang', 
                'tgl_rekomendasibarang', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'catatan'
            ], 'safe'],
            [['ruangan_id', 'pegawai_id'], 'required'],
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'status_po', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_rekomendasibarang'], 'string', 'max' => 255],
            [['status_po'], 'default', 'value' => 316],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rekomendasibarang_id' => 'Rekomendasibarang ID',
            'no_rekomendasibarang' => 'No Rekomendasibarang',
            'tgl_rekomendasibarang' => 'Tgl Rekomendasibarang',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'status_po' => 'Status Po',
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
