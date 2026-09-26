<?php
// author: Ardi Pratama
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pembebasantarif_t".
 *
 * @property int $pembebasantarif_id
 * @property int $pendaftaran_id
 * @property int $pembayaranpelayanan_id
 * @property string $tgl_pembebasantarif
 * @property string $no_pembebasantarif
 * @property double $total_pembebasantarif
 * @property string $catatan
 * @property string $status_pembebasantarif
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
 * @property double $total_tarifpelayanan
 */
class PembebasanTarif extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pembebasantarif_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'total_tarifpelayanan'], 'required'],
            [['pendaftaran_id', 'pembayaranpelayanan_id', 'pegawaimengetahui_id', 'pegawaimenyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pembayaranpelayanan_id', 'pegawaimengetahui_id', 'pegawaimenyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pembebasantarif', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['total_pembebasantarif', 'total_tarifpelayanan'], 'number'],
            [['catatan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pembebasantarif'], 'string', 'max' => 255],
            [['status_pembebasantarif'], 'string', 'max' => 32],
            [['no_pembebasantarif'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembebasantarif_id' => 'Pembebasantarif ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'tgl_pembebasantarif' => 'Tgl Pembebasantarif',
            'no_pembebasantarif' => 'No Pembebasantarif',
            'total_pembebasantarif' => 'Total Pembebasantarif',
            'catatan' => 'Catatan',
            'status_pembebasantarif' => 'Status Pembebasantarif',
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
            'total_tarifpelayanan' => 'Total Tarifpelayanan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
    
    public function extraFields()
    {
        return [
            'pendaftaran_t' => function($item){
                return $item->pendaftaran;
            }
        ];
    }
}
