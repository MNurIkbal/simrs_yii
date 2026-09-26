<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-07 16:43:34
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-10 11:09:44
 */

namespace Doco\bedah\models;

class BatalOperasiForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $tgl_batalperiksa;
    public $peg_menyetujui_id;
    public $alasan;

    public function rules()
    {
        return [
            [['peg_menyetujui_id'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['pasienmasukpenunjang_id', 'alasan', 'tgl_batalperiksa'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'tgl_batalperiksa'=>\Yii::t('fe', 'Tanggal batal periksa'),
            'peg_menyetujui_id'=>\Yii::t('fe', 'Disetujui oleh'),
            'alasan'=>\Yii::t('fe', 'Alasan pembatalan'),
        ];
    }
}