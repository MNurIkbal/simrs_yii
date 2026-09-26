<?php

/**
 * setup global variable
 *
 * @author ali.padilah@docotel.com
 * @return string
 */

namespace app\components;


use Yii;
use yii\base\Component;

class DocoVars extends Component
{


    public function identity($param = null)
    {
        $data_identity = Yii::$app->cache->get('app');

        if (!empty($data_identity[$param])) {
            return $data_identity[$param];
        } else if ($param == "all") {
            return $data_identity;
        } else {
            return "-";
        }
    }


    public function user($param = null)
    {
        if ($param != "uid") {
            return empty(Yii::$app->session->get('user_identity')[$param])
                    ? " - "
                    : Yii::$app->session->get('user_identity')[$param];
        } else {
            return Yii::$app->session->get($param);
        }
    }

    public function workspace($param = null, $return = false)
    {
        if ($return) {
            return $return;
        } else {
            return empty(Yii::$app->session->get('active_workspace')[$param])
                    ? '-'
                    : Yii::$app->session->get('active_workspace')[$param];
        }
    }
    
    public function all_rooms(){        
        $arr = Yii::$app->session->get('all_rooms');
        return !count($arr) ? [] : $arr;
    }

    public function konfig_system($param = null){        
        $konfig = Yii::$app->session->get('konfig_system');
        
        if (!empty($konfig[$param])) {
            return $konfig[$param];
        } else if ($param == "all") {
            return $konfig;
        } else {
            return "-";
        }
    }
}