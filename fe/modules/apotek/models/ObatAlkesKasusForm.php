<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Obat Alkes Jenis Kasus Penyakit
 * @copyright 3 January 2018 aweutist
 */

namespace Doco\apotek\models;

use Yii;

class ObatAlkesKasusForm extends \yii\base\Model
{
    public $obatalkes_id;
    public $jeniskasuspenyakit_id;
    public $selectObat;

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'obatalkes_id'], 'required'],
            [['jeniskasuspenyakit_id', 'obatalkes_id'], 'safe'],
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => Yii::t('fe','Obat Alkes'),
            'jeniskasuspenyakit_id' => Yii::t('fe','Jenis Kasus Penyakit'),
        ];
    }
}


