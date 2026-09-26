<?php

namespace app\modules\pengadaan\models;

use Yii;

class PurchaseRequisitionForm extends \yii\base\Model
{

    public $purchasereq_id;
    public $no_pr;
    public $tgl_pr;
    public $ruangan_id;
    public $pegawai_id;
    public $status;
    public $reference;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $instalasi_ruangan;
    public $nama_pegawai;
    public $is_cyto;
    public $is_consignment;
    public $is_admin;

    public static function tableName()
    {
        return 'purchasereq_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'purchasereq_id' => "PR ID",
            'no_pr' => "No. PR",
            'tgl_pr' => "Tanggal PR",
            'ruangan_id' => "Ruangan ID",
            'instalasi_ruangan' => 'Instalasi - Ruangan',
            'pegawai_id' => "Pegawai ID",
            'nama_pegawai' => "Nama Pegawai",
            'status' => "Status PR",
            'reference' => "Reference",
            'additional_data' => "Additional Data",
            'created_date' => "Created Date",
            'created_by' => "Created By",
            'modified_count' => "Modified Count",
            'last_modified_date' => "Last Modified Date",
            'last_modified_by' => "Last Modified By",
            'is_deleted' => "Is Deleted",
            'is_active' => "Is Active",
            'deleted_date' => "Deleted Date",
            'deleted_by' => "Deleted By",
            'is_cyto' => 'Cito',
            'is_consignment' => 'Consignment',
            'is_admin' => 'Admin'
        ];
    }
}










