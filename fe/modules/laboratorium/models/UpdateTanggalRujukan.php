<?php

/**
 * @author Randy Vianda Putra
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\laboratorium\models;

use Yii;


class UpdateTanggalRujukan extends \yii\base\Model
{
    public $tgl_rujukan;

    public function rules()
    {
        return [
            [['tgl_rujukan'], 'required'],
            [['tgl_rujukan'], 'safe']
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'tgl_rujukan' => \Yii::t('fe', 'Tanggal Rujukan'),
        ];
    }
}
