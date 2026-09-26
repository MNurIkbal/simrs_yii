<?php
namespace app\modules\v1\actions;

use Yii;
use yii\base\Action;

class SerahkanObatAction extends Action {
    public function run() {
        // to-do: ganti pake default panggil extension
        return Yii::$app->docoPlugin->execute('serahkan_obat');
    }
}
