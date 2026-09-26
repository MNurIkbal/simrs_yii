<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "sy_adjusmentdetail".
 *
 * @property int $adjusmentdetail_id
 * @property int $adjusment_id
 * @property string $no_pendaftaran
 * @property string $no_buktiadjust
 * @property string $no_buktitrans
 * @property double $total_rs
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
 * @property string $kode_bagian
 * @property string $kode_layanan
 * @property string $kode_adjust
 * @property string $kode_nota
 * @property double $total_hakrs
 * @property double $total_dokter
 * @property double $total_hakdokter
 * @property string $status_bayar
 * @property string $status_batal
 * 
 */
class SyKunjunganAdjusmentDetail extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_adjusmentdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['adjusment_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['total_rs', 'total_hakrs', 'total_dokter', 'total_hakdokter'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'no_buktiadjust', 'adjusment_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pendaftaran', 'no_buktiadjust'], 'string', 'max' => 100],
            [['kode_bagian', 'kode_layanan', 'kode_adjust', 'kode_nota'], 'string', 'max' => 50],
            [['status_bayar', 'status_batal'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adjusmentdetail_id' => 'Adjusment Detail ID',
            'adjusment_id' => 'Adjusment ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_buktiadjust' => 'No Bukti Adj',
            'kode_bagian' => 'Kode Bagian',
            'kode_layanan' => 'Kode Layanan',
            'kode_adjust' => 'Kode Adjusment',
            'kode_nota' => 'Kode Nota',
            'total_rs' => 'Total RS',
            'total_hakrs' => 'Total Hak RS',
            'total_dokter' => 'Total Dokter',
            'total_hakdokter' => 'Total Hak Dokter',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'status_bayar' => 'Status Bayar',
            'status_batal' => 'Status Batal',
        ];
    }
}
