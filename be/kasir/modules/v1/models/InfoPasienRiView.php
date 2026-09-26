<?php

namespace app\modules\v1\models;

use Yii;

class InfoPasienRiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienri_v';
    }

    public function getInvoice()
    {
        return $this->hasOne(InvoiceRiView::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
}
