<?php

namespace app\modules\v1\models;

use Yii;

class PurchaseRequisition extends \Doco\components\DocoActiveRecord
{
	public $primaryKey = 'purchasereq_id';

    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'purchasereq_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
    	return [
    		[['purchasereq_id'],'integer'],
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
                    'is_consignment',
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
        return $this->hasMany(PurchaseRequisition::className(), ['purchasereq_id' => 'purchasereq_id']);
    }
}