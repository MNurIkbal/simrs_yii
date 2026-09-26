<?php


namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pembayaranalokasidetail_t".
 *
 * @property int $pembayaranalokasidetail_id
 * @property int $pembayaranalokasi_id
 * @property int $pengajuanklaimdetail_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property double $jumlah_piutang
 * @property double $jumlah_telahbayar
 * @property double $jumlah_bayar
 * @property double $jumlah_sisapiutang
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
class PembayaranAlokasiDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pembayaranalokasidetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pembayaranalokasi_id', 'pengajuanklaimdetail_id', 'pendaftaran_id', 'pasien_id'], 'required'],
            [['pembayaranalokasi_id', 'pengajuanklaimdetail_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pembayaranalokasi_id', 'pengajuanklaimdetail_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jumlah_piutang', 'jumlah_telahbayar', 'jumlah_bayar', 'jumlah_sisapiutang'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pembayaranalokasidetail_id' => 'Pembayaranalokasidetail ID',
            'pembayaranalokasi_id' => 'Pembayaranalokasi ID',
            'pengajuanklaimdetail_id' => 'Pengajuanklaimdetail ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'jumlah_piutang' => 'Jumlah Piutang',
            'jumlah_telahbayar' => 'Jumlah Telahbayar',
            'jumlah_bayar' => 'Jumlah Bayar',
            'jumlah_sisapiutang' => 'Jumlah Sisapiutang',
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
