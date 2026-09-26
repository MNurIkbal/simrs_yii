<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-27 16:12:16
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-05 16:10:58
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DaftarTindakan;

class DaftarTindakanController extends \Doco\components\DocoActiveController
{
	// Model class
	public $modelClass = 'app\modules\v1\models\DaftarTindakan';

	// Verbs
	public function verbs()
	{
		// Verbs parent
		$verbs = parent::verbs();

		// Return verbs
		return $verbs;
	}

	// Actions
	public function actions()
	{
		// Actions parent
		$actions = parent::actions();

		// Unset actions
		unset($actions['index']);

		// Return actions
		return $actions;
	}

	// Action index
	public function actionIndex()
	{
		// Try catch
		try {
			// Define model
			$model = new DaftarTindakan;

			// Query
			$query = $model::find()->where([DaftarTindakan::tableName().'.is_deleted' => false])->andWhere([DaftarTindakan::tableName().'.is_active' => true]);

			if(isset($_GET['kelompoktindakan_id'])) {
				$query->andWhere(['kelompoktindakan_id' => $_GET['kelompoktindakan_id']]);
			}

			$request = Yii::$app->request;
			$filters = $request->get('filters', '');
			if (!empty($filters)) {
				$q = trim($request->get('q', null));
				if (!empty($q)) {
					$filter = explode(',', $filters);
					$conds = ['OR'];
					foreach ($filter as $column) {
						$type = null;
                        $tableColumns = (array) $model->getTableSchema()->columns;
                        if (isset($tableColumns[$column])) {
                            $type = $tableColumns[$column]->type;
						}
						if ($type == 'string' || $type == 'text') {
							$conds[] = ['ILIKE', DaftarTindakan::tableName() . "." . $column, $q];
						} else {
							$conds[] = ['=', DaftarTindakan::tableName() . "." . $column, $q];
						}
					}
					$query->andWhere($conds);
				}
				unset($_GET['filters']);
			}

			// Doco active filter
			$query = DocoRestActiveFilter::advancedFilter($model, $query);

			// Return data
			return new ActiveDataProvider([
				'query' => $query,
			]);
		} catch (\yii\db\Exception $e) {
			// Change status code
			\Yii::$app->response->statusCode = 500;

			// Return message
			return [
				'message' => $e->getMessage()
			];
		} catch (\Exception $e) {
			// Change status code
			\Yii::$app->response->statusCode = 500;

			// Return message
			return [
				'message' => $e->getMessage()
			];
		}
	}
}
?>