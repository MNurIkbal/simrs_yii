<?php 
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\components;

class DocoBaseModel extends \yii\base\Model
{
    protected $xssProtected = [];

    public function beforeValidate()
    {
        if (!empty($this->xssProtected)) {
            $is_xss = false;
            foreach ($this->xssProtected as $value) {
                if(!empty($this->$value)) {
                    if (is_array($this->$value)){
                        foreach ($this->$value as $key => $carabayar) {
                            $tmpCarBay = strip_tags(\yii\helpers\HtmlPurifier::process($carabayar));
                            if(empty($tmpCarBay)) {
                                DocoHelpers::multipleParseError($this, 'Data Tidak Valid', $value, $key);
                                $is_xss = true;
                            }
                        }
                    }else{
                        $this->$value = strip_tags(\yii\helpers\HtmlPurifier::process($this->$value));
                        if(empty($this->$value)) {
                            $this->addError($value, 'Data Tidak Valid');
                            $is_xss = true;
                        }
                    }
                    
                }
            }

            if($is_xss) return false;
         }

        return true;
    }
}