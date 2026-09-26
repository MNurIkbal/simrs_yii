<?php

namespace Doco\models\kasir;

use Yii;

class InfoPasienRiView extends \Doco\components\DocoActiveRecord
{
    public static function primaryKey()
    {
        return ['pendaftaran_id'];
    }

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
