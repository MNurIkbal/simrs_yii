<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "mutasiobatruangan_t".
 *
 * @property int $mutasiobatruangan_id
 * @property int $pesanobatalkes_id
 * @property int $terimamutasi_id
 * @property string $tglmutasioa
 * @property string $nomutasioa
 * @property int $ruanganasal_id
 * @property int $ruangantujuan_id
 * @property string $keteranganmutasi
 * @property double $totalharganettomutasi
 * @property double $totalhargajual
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
 * @property int $status_mutasi
 * @property int $pegawaimengetahui_id
 */
class MutasiObatRuangan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mutasiobatruangan_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['mutasiobatruangan_id'], 'required'],
            [['pesanobatalkes_id', 'terimamutasiobat_id', 'ruanganasal_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_mutasi', 'pegawaimengetahui_id'], 'default', 'value' => null],
            [['pesanobatalkes_id', 'terimamutasiobat_id', 'ruanganasal_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_mutasi', 'pegawaimengetahui_id'], 'integer'],
            [['tglmutasioa','pegawaimengetahui_id','pegawaimenyetujui_id', 'created_date'], 'safe'],
            [['nomutasioa', 'keteranganmutasi', 'additional_data'], 'string'],
            [['totalharganettomutasi', 'totalhargajual'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasiobatruangan_id' => 'Mutasiobatruangan ID',
            'pesanobatalkes_id' => 'Pesanobatalkes ID',
            'terimamutasiobat_id' => 'Terimamutasi ID',
            'tglmutasioa' => 'Tglmutasioa',
            'nomutasioa' => 'Nomutasioa',
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruangantujuan_id' => 'Ruangantujuan ID',
            'keteranganmutasi' => 'Keteranganmutasi',
            'totalharganettomutasi' => 'Totalharganettomutasi',
            'totalhargajual' => 'Totalhargajual',
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
            'status_mutasi' => 'Status Mutasi',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
        ];
    }
}
