<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Worklist;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\WorklistView;

/**
 *
 */
class IndexAction extends Action
{
    public function run()
    {
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');
        $worklist = WorklistView::find()->where(['between', 'tanggal', $today_start, $today_end]);
        return $worklist->asArray()->all();
    }
}