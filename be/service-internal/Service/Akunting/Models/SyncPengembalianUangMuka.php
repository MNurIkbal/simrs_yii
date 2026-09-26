<?php

namespace Integrasi\Service\Akunting\Models;

use Yii;

/**
 * This is the model class for table "pengembalianuangmuka_t".
 *
 * @property int $pengembalianuangmuka_id
 * @property int $pendaftaran_id
 * @property int $tandabuktikeluar_id
 * @property int $ruangan_id
 * @property string $tgl_pengembalian
 * @property double $total_pengembalian
 * @property double $biaya_administrasi
 * @property double $pembulatan
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class SyncPengembalianUangMuka extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pengembalianuangmuka_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'tandabuktikeluar_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'tandabuktikeluar_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pengembalianuangmuka_id'], 'integer'],
            [['tgl_pengembalian', 'created_date', 'deleted_date', 'pendaftaran_id'], 'safe'],
            [['total_pengembalian', 'biaya_administrasi', 'pembulatan'], 'number'],
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
            'pengembalianuangmuka_id' => 'Pengembalianuangmuka ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'tandabuktikeluar_id' => 'Tandabuktikeluar ID',
            'ruangan_id' => 'Ruangan ID',
            'tgl_pengembalian' => 'Tgl Pengembalian',
            'total_pengembalian' => 'Total Pengembalian',
            'biaya_administrasi' => 'Biaya Administrasi',
            'pembulatan' => 'Pembulatan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
