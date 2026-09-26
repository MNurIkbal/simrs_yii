<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pengajuanklaimdetail_t".
 *
 * @property int $pengajuanklaimdetail_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $pengajuanklaim_id
 * @property double $jumlah_piutang
 * @property double $jumlah_bayar
 * @property double $jumlah_telahbayar
 * @property double $jumlah_sisapiutang
 * @property int $pembayaranpelayanan_id
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
class PengajuanKlaimDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pengajuanklaimdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'pengajuanklaim_id', 'jumlah_bayar', 'jumlah_telahbayar', 'jumlah_sisapiutang', 'pembayaranpelayanan_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'pengajuanklaim_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'pengajuanklaim_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jumlah_piutang', 'jumlah_bayar', 'jumlah_telahbayar', 'jumlah_sisapiutang'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'pembayaranpelayanan_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pengajuanklaimdetail_id' => 'Pengajuanklaimdetail ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'pengajuanklaim_id' => 'Pengajuanklaim ID',
            'jumlah_piutang' => 'Jumlah Piutang',
            'jumlah_bayar' => 'Jumlah Bayar',
            'jumlah_telahbayar' => 'Jumlah Telahbayar',
            'jumlah_sisapiutang' => 'Jumlah Sisapiutang',
            'pembayaranpelayanan_id' => 'Pembayaran Pelayanan ID',
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
