<?php

namespace app\modules\v1\models;

use Yii;

class PurchaseRequisitionBarang extends \Doco\components\DocoActiveRecord
{
	public $primaryKey = 'purchasereqbrg_id';

    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'purchasereqbrg_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
    	return [
    		[['purchasereqbrg_id'],'integer'],
    		[
                [
                    'tgl_pr',
                    'ruangan_id',
                    'pegawai_id',
                    'reference',
                    'status',
                    'created_date',
                    'is_deleted',
                    'is_active',
                    'is_prcyto',
                    'is_admin'
                ],'safe'
            ]
    	];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
    	return [];
    }

    public function getDetail()
    {
        return $this->hasMany(PurchaseRequisitionBarang::className(), ['purchasereqbrg_id' => 'purchasereqbrg_id']);
    }
}
