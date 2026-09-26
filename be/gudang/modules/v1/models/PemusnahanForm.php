<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-12-27 11:32:51
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-27 11:33:47
 */

namespace app\modules\v1\models;

use Yii;

class PemusnahanForm extends \yii\base\Model
{
    public $tanggal_pemusnahan;
    public $pegawai_meyetujui;
    public $pegawai_mengetahui;

    public $detail;

    public function rules()
    {
        return [
            [[
                'tanggal_pemusnahan',
                // 'pegawai_meyetujui',
                'pegawai_mengetahui',
            ], 'required'],
            [['tanggal_pemusnahan','pegawai_meyetujui','pegawai_mengetahui'],'safe']
        ];
    }
}