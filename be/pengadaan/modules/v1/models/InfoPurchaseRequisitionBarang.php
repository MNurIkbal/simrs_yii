<?php

namespace app\modules\v1\models;

use Yii;

class InfoPurchaseRequisitionBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'infopurchasereqbrg_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_pr', 'no_pr', 'ruangan_id','ruangan', 'pegawai_id', 'pegawai','reference','status', 'status_pr'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_pr' => 'Tanggal Purchase Requisition',
            'no_pr' => 'Nomor Purchase Requisition',
            'ruangan_id' => 'ID Ruangan',
            'ruangan' => 'Ruangan',
            'pegawai_id' => 'ID Pegawai',
            'reference' => 'Reference',
            'status' => 'ID Status',
            'status_pr' => 'Status'
        ];
    }
}
