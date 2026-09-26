<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use app\components\DocoActiveController;
use app\components\DocoRestActiveFilter;
use app\modules\v1\models\Menumodul;

class MenuController extends \app\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Menumodul';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /*
    * @attributes %dadang%
    * @attributes %asep%
    * @attributes %bento%
    * @attributes %asep_bedog%
    */

    public function actionIndex()
    {
        $model = new Menumodul;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}

?>