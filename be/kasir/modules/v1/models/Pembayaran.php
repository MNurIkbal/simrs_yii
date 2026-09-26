<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\PembayaranPelayanan;
/**
 * This is the model class for table "pembayaran_t".
 *
 * @property int $pembayaran_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property double $total_tagihan
 * @property double $total_dibayar
 * @property double $total_dijamin
 * @property double $total_sisatagihan
 * @property double $total_administrasi
 * @property double $total_pembulatan
 * @property double $total_pembebasan
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $pembulatan_pasien
 */
class Pembayaran extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pembayaran_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pendaftaran_id', 
                'pasienadmisi_id', 
                'created_by', 
                'modified_count', 
                'last_modified_by', 
                'deleted_by',
            ], 'default', 'value' => null],
            [[
                'pendaftaran_id', 
                'pasienadmisi_id', 
                'created_by', 
                'modified_count', 
                'last_modified_by', 
                'deleted_by',
                'pemberianpiutang_id',
            ], 'integer'],
            [[
                'total_tagihan', 
                'total_dibayar', 
                'total_dijamin', 
                'total_sisatagihan', 
                'total_administrasi', 
                'total_pembulatan', 
                'total_pembebasan',
                'penggunaan_uangmuka',
                'total_ditagihkan',
                'total_nontunai',
                'total_tunai',
                'total_discountpembayaran',
                'sisa_uangmuka',
                'pembulatan',
                'total_discountadm',
            ], 'number'],
            [[
                'created_date', 
                'last_modified_date', 
                'deleted_date', 
                'penggunaan_uangmuka',
                'total_discountpembayaran',
                'catatan',
                'sisa_uangmuka',
                'no_pembayaran',
                'no_invoicepasien',
                'pembulatan',
                'total_discountadm',
                'is_plafon',
            ], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pembayaran_id' => 'Pembayaran ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'total_tagihan' => 'Total Tagihan',
            'total_dibayar' => 'Total Dibayar',
            'total_dijamin' => 'Total Dijamin',
            'total_sisatagihan' => 'Total Sisatagihan',
            'total_administrasi' => 'Total Administrasi',
            'total_pembulatan' => 'Total Pembulatan',
            'total_pembebasan' => 'Total Pembebasan',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'pembulatan_pasien' => 'Pembulatan Pasien',
        ];
    }

    public function getPembayaranPelayanan()
    {
        return $this->hasOne(PembayaranPelayanan::className(), ['pembayaran_id' => 'pembayaran_id']);
    }
}
