<?php

namespace app\modules\v1\models;

use Yii;
use DateTime;

class PembayaranTransaksi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    
    protected $xssProtected = [
        'deskripsi', 
        'referensi',
    ];

    public $dari_kepada;
    public static function tableName()
    {
        return 'pembayarantransaksi_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'jenis_transaksi',
                'tipe_transaksi', 
                'supplier_id',
                'pegawai_id',
                'pasien_id',
                'metode_pembayaran',
                'kategoritransaksi_id',
                'dari_kepada',
                'created_by',
                'modified_count',
                'last_modified_by',
                'deleted_by',
                'jenisnontunai_id',
                'pendaftaran_id',
            ], 'integer'],
            [[
                'jenis_transaksi',
                'tgl_transaksi',
                'tipe_transaksi',
                'metode_pembayaran',
                'jumlah',
                'dari_kepada'], 'required'],
            [['metode_pembayaran'], 'checkJenis'],
            [[
                'tgl_transaksi', 
                'dari_kepada', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'jenisnontunai_id',
                'pendaftaran_id',
            ], 'safe'],
            [['jumlah'], 'number'],
            [['no_transaksi', 'referensi', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_transaksi'], 'string', 'max' => 100],
            [['tgl_transaksi'], 'default', 'value' => null],
            ['tgl_transaksi', 'datetime', 'format' => 'php:Y-m-d'],
            ['tgl_transaksi', 'validateTanggalTransaksi'],
        ];
    }

    public function checkJenis($attribute, $params)
    {
        if ($this->metode_pembayaran == 28) {
            if (empty($this->jenisnontunai_id)) {
                $this->addError('jenisnontunai_id', 'Jenis Non Tunai Tidak boleh kosong');
            }
        }
    }

    public function validateTanggalTransaksi($attribute, $params)
    {
        $inputDate = new DateTime($this->{$attribute});
        $currentDate = new DateTime();
        $inputDate->setTime(0, 0, 0);
        $currentDate->setTime(0, 0, 0);

        if ($inputDate > $currentDate) {
            $this->addError($attribute, 'Tanggal transaksi tidak boleh melebihi tanggal hari ini.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembayarantransaksi_id' => Yii::t('app', 'Pembayaran Transaksi ID'),
            'jenis_transaksi' => Yii::t('app', 'Jenis Transaksi'),
            'tgl_transaksi' => Yii::t('app', 'Tanggal Transaksi'),
            'no_transaksi' => Yii::t('app', 'No Transaksi'),
            'tipe_transaksi' => Yii::t('app', 'Tipe Transaksi'),
            'supplier_id' => Yii::t('app', 'Supplier'),
            'pegawai_id' => Yii::t('app', 'Pegawai'),
            'pasien_id' => Yii::t('app', 'Nama Pasien'),
            'metode_pembayaran' => Yii::t('app', 'Metode Pembayaran'),
            'jumlah' => Yii::t('app', 'Jumlah'),
            'kategoritransaksi_id' => Yii::t('app', 'Kategori Transaksi'),
            'deskripsi' => Yii::t('app', 'Deskripsi'),
            'referensi' => Yii::t('app', 'Referensi'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'dari_kepada' => Yii::t('app', 'Dari/Kepada'),
            'jenisnontunai_id' => 'Jenis Non Tunai',
            'pendaftaran_id' => 'pendaftaran id',
        ];
    }
}
