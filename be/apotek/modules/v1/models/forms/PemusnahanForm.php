<?php


namespace app\modules\v1\models\forms;

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
                'pegawai_meyetujui',
                'pegawai_mengetahui',
            ], 'required'],
            [['tanggal_pemusnahan','pegawai_meyetujui','pegawai_mengetahui'],'safe']
        ];
    }
}