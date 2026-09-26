<?php

namespace app\modules\bankdarah\models;

use Yii;
use app\components\DocoHelpers;

class CustomTerimaDarahPmiDetail extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $item;
    public $no_kantongdarah;
    public $tgl_pengambilan;

    public function rules()
    {
        return [
            [['item'], 'required'],
            [['item', 'no_kantongdarah', 'tgl_pengambilan'], 'customValidation'],
            [['item', 'no_kantongdarah', 'tgl_pengambilan'], 'safe'],
        ];
    }

    public function customValidation()
    {
        $arrNoKantong = [];
        foreach ($this->item as $key => $value) {
            if(is_array($value)) {
                foreach ($value as $k => $v) {
                    if(empty($v['no_kantongdarah'])) {
                        $this->addError('no_kantongdarah-'.$k.'', 'No Kantong Darah tidak boleh kosong');
                    }
                    if(empty($v['tgl_pengambilan'])) {
                        $this->addError('tgl_pengambilan-'.$k.'', 'Tanggal Pengambilan tidak boleh kosong');
                    }
                    if(isset($arrNoKantong[$v['no_kantongdarah']])) {
                        $this->addError('no_kantongdarah-'.$k.'', 'No Kantong Darah tidak boleh sama.');
                    }
                    else {
                        $arrNoKantong[$v['no_kantongdarah']] = true; 
                    }
                }
            }
        }
    }
}
