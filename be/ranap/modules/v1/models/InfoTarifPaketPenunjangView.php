<?php

/**
 * @Author: Rizal
 * @Date:   2018-08-03 10:03:03
 */

namespace app\modules\v1\models;

use Yii;

class InfoTarifPaketPenunjangView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tarifpaketpenunjang_v';
    }
    
    public function getPaketDetailView()
    {
        return $this->hasMany(PaketDetailView::className(), ['tipepaket_id'=>'tipepaket_id']);
    }
    public function extraFields() 
    {
        return [
          'paketdetail_v'=>function($model){
             return $model->paketDetailView;
           }
        ];
    }
}
?>