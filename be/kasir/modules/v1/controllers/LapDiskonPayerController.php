<?php

/****
 * * @author: Budi
 * ? @email: budi@sirs.co.id 
 * ! Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LapDiskonPayerView;
use app\modules\v1\models\LapDiskonView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use Doco\components\DocoConstants;

class LapDiskonPayerController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\LapDiskonPayerView';
	public function verbs()
	{
		$verbs = parent::verbs();
		return $verbs;
	}

	public function actions()
	{
		$actions = parent::actions();
		unset($actions['index']);
		return $actions;
	}

	public function actionIndex()
	{
		$request = Yii::$app->request;
		$model = new LapDiskonView;
		$query = $model::find();
		$this->dateFilter($query, $request);
		$query = DocoRestActiveFilter::advancedFilter($model, $query);
		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

	public function actionExportExcel()
	{
		try {
			$title = "Laporan Diskon";
			$request = Yii::$app->request;
			$advanced_filter = $request->get('advanced-filter');
			
			$model = new LapDiskonView;
			$query = $model::find();
			$this->dateFilter($query, $request);
			$query = DocoRestActiveFilter::advancedFilter($model, $query);
			
			$header = [];
			if (!is_null($advanced_filter)) {
				$header = $model->setHeaderExcel($advanced_filter);
			} 
			
			$result = $model->mappingDataExcel($query);
			$filePath = DocoHelpers::exportExcel($title, $result, $header, [], [], [], true);
			$filePath->save('php://output');
			die;
		} catch (\Yii\db\Exception $e) {
			return $e->getMessage();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	/****
	* TODO : Endpoint get cara bayar
	* ? @author: Budi (budi@sirs.co.id)
	*/
	public function actionGetCaraBayar()
	{
		return CaraBayar::find()
			->select(['carabayar_id as id', 'carabayar_nama as text'])
			->where(['is_active' => true])
			// ->andWhere(['<>', 'groupcarabayar_id', DocoConstants::GROUP_UMUM])
			->orderBy(['carabayar_nama' => SORT_ASC])
			->asArray()->all();
	}

	/****
	* TODO : Endpoint get penjamin
	* ? @author: Budi (budi@sirs.co.id)
	*/
	public function actionGetPenjamin()
	{
		$resultData = [];
		$request = Yii::$app->request;
		$payload = $request->get('payload', []);
		$term = isset($payload['term']) ? $payload['term'] : null;
		$page = isset($payload['page']) ? $payload['page'] : 1;
		$limit = isset($payload['limit']) ? $payload['limit'] : 10;
		$carabayar_id = isset($payload['carabayar_id']) ? $payload['carabayar_id'] : null;
		if (!empty($carabayar_id)) {
			$result = Penjamin::find()
				->select(['penjamin_id as id', 'penjamin_nama as text'])
				->where(['carabayar_id' => $carabayar_id, 'is_active' => true]);

			if (!empty($term)) {
				$result->andWhere(['like', 'LOWER(penjamin_nama)', $term]);
			}
			$result->limit(($limit + 1))->offset($limit * ($page - 1));
			$result->orderBy(['penjamin_nama' => SORT_ASC]);
			$resultData = $result->asArray()->all();
		}
		return $resultData;
	}

	private function dateFilter($query, $request)
	{
		$start = date('Y-m-d 00:00:00');
		$end = date('Y-m-d 23:59:59');
		$advancedFilter = $request->get('advanced-filter');
		$field = 'tgl_diskon';
		if(isset($advancedFilter)) {
			if(isset($advancedFilter[$field])) {
				$explode = explode(" - ", $advancedFilter[$field]);
				if(count($explode) == 2) {
					$start = date('Y-m-d 00:00:00', strtotime($explode[0]));
					$end = date('Y-m-d 23:59:59', strtotime($explode[1]));
				}
				unset($advancedFilter[$field]);
			}
		}
		$query->andWhere(['between', $field, $start, $end]);
	}
}
