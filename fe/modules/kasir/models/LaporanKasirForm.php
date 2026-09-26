<?php

namespace app\modules\kasir\models;

use Yii;

class LaporanKasirForm extends \yii\base\Model
{

    public $start_date;
    public $end_date;
    public $jenisLaporan;

    public function rules()
    {
        return [
            [['start_date', 'end_date', 'jenisLaporan'],'safe'],
        ];
    }

    public function checkValidasi($params, $attributes)
    {
        
    }
}
