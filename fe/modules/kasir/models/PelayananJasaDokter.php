<?php

namespace app\modules\kasir\models;

use Yii;
use app\components\DocoBaseModel;

class PelayananJasaDokter extends DocoBaseModel
{
    public $tgl_transaksi;
    public $jasadokter_id;
    public $pegawai_id;
    public $total_jasa;
    public $deskripsi;

    protected $xssProtected = [
        'tgl_transaksi',
        'total_jasa',
        'deskripsi'
    ];


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_transaksi', 'jasadokter_id', 'pegawai_id', 'total_jasa', 'deskripsi'], 'required'],
            [['jasadokter_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_transaksi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['total_jasa'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [
                ['tgl_transaksi'],
                'date', 'format' =>  'yyyy-MM-dd'
            ],
            [
                ['tgl_transaksi'], 
                'validateTglTransaksi'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pelayananjasadokter_id' => Yii::t('app', 'Pelayanan Jasa Dokter'),
            'jasadokter_id' => Yii::t('app', 'Transaksi'),
            'pegawai_id' => Yii::t('app', 'Dokter'),
            'tgl_transaksi' => Yii::t('app', 'Tanggal Transaksi'),
            'no_transaksi' => Yii::t('app', 'No Transaksi'),
            'total_jasa' => Yii::t('app', 'Total Jasa'),
            'deskripsi' => Yii::t('app', 'Deskripsi'),
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
        ];
    }

    public function validateTglTransaksi($attribute, $params) 
    {
        $date = new \DateTime();
        $maxDate = date_format($date, 'Y-m-d');
        if ($this->$attribute > $maxDate) {
            $this->addError($attribute, 'Tanggal Transaksi tidak boleh lebih besar dari tanggal hari ini.');
        }
    }
}
