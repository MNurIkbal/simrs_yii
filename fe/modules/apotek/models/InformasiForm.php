<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Form
 * @copyright 16 January 2018 aweutist
 */

namespace Doco\apotek\models;

use Yii;

class InformasiForm extends \yii\db\ActiveRecord
{
    public $startDate;
    public $endDate;
    public $cara_bayar;
    public $penjamin;
    public $no_resep;
    public $no_pendaftaran;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitobat_mp';
    }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['no_resep'], 'required'],
            [['no_resep'], 'safe'],
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'no_resep' => Yii::t('fe','No Resep'),
        ];
    }
}


