<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-11 12:49:34
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-06-28 18:27:14
 */

namespace app\modules\v1\controllers;

// Yii
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

// App
use app\modules\v1\models\Asesmen;

// Doco
use Doco\components\DocoActiveController;

// Class
class AsesmenController extends DocoActiveController
{
	// Model class
	public $modelClass = 'app\modules\v1\models\Asesmen';

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

		// Return actions
		return $actions;
	}

	// Action get asesmen parent
	public function actionGetAsesmenParent()
	{
		// Try catch
		try {
			// Mendapatkan semua parameter
			$params = Yii::$app->request->get();

			// Find model
			$model = Asesmen::find();

			// Cek params
			if (isset($params['instalasi_id']) && $params['instalasi_id'] != '') {
				// Tambahkan kondisi
				$model->andWhere(['instalasi_id' => $params['instalasi_id']]);
			}

			// Cek params
			if (isset($params['menu_header']) && $params['menu_header'] != '') {
				// Tambahkan kondisi
				$model->andWhere(['menu_header' => $params['menu_header']]);
			}

			// Cek params
			if (isset($params['tabulasi']) && $params['tabulasi'] != '') {
				// Tambahkan kondisi
				$model->andWhere(['tabulasi' => $params['tabulasi']]);
			}

			// Cek params
			if (isset($params['wizard']) && $params['wizard'] != '') {
				// Tambahkan kondisi
				$model->andWhere(['wizard' => $params['wizard']]);
			}

			// Cek params
			// if (isset($params['parent_id']) && $params['parent_id'] != '') {
				// Tambahkan kondisi
			// }
			// $model->andWhere('level > 2');
			$model->orderBy('asesmen_id');

			// Returnta
			return $model->all();
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

	// Action get asesmen child
	public function actionGetAsesmenChild()
	{
		// Try catch
		try {
			// Mendapatkan semua parameter
			$params = Yii::$app->request->get();

			// Find model
			$model = Asesmen::find();

			// Cek params
			if (isset($params['parent_id']) && $params['parent_id'] != '') {
				// Tambahkan kondisi
				$model->andWhere(['parent_id' => $params['parent_id']]);
			}

			// Returnta
			return $model->all();
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