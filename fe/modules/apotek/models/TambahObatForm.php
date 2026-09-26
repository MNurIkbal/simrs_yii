<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\models;

use Yii;

class TambahObatForm extends \yii\db\ActiveRecord
{
    public $obatalkes;
    public $satuan;
    public $stok;
    public $qty;
    public $catatan;

    public function rules()
    {
        return [
            [['obatalkes', 'satuan', 'qty'], 'required'],
            [['obatalkes', 'satuan', 'qty', 'stok', 'catatan'], 'safe']
        ];
    }

    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'obatalkes' => \Yii::t('fe', 'Nama Obat Alkes'),
            'satuan' => \Yii::t('fe', 'Satuan'),
            'qty' => \Yii::t('fe', 'Qty Pesan'),
            'stok' => \Yii::t('fe', 'Stok'),
            'catatan' => \Yii::t('fe', 'Catatan')
        ];
    }
}


