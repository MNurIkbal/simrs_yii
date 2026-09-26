<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-26 10:51:04
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-26 11:13:24
 */

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use app\modules\v1\models\PaketPelayanan;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\data\ActiveDataProvider;

// Class paket pelayanan controller
class PaketPelayananController extends \Doco\components\DocoActiveController
{
	// Model class
	public $modelClass = 'app\modules\v1\models\PaketPelayanan';

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
			// Find model
			$query = $this->findModel();

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

	/**
	* @controller actionExportPdf
	* @attribute #table_exportpdf# => table 
	**/
	public function actionExportPdf()
	{
		// Try catch
		try {
			// Find model
			$query = $this->findModel();

			// Execute query
			$model = $query->all();

			// Check model
			if (!empty($model)) {
				// Print
				$print = new DocoPrint();

				// Assign attributes
				$print->attributes = [
					'#table_exportpdf#' => $this->renderPartial('pdf', [
						'header' => array(),
						'model' => $model,
					]),
				];

				// Print output
				$print->Output();
			}
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

	// Export excel
	public function actionExportExcel()
	{
		// Try catch
		try {
			// Declare empty variables
			$data = array();
			$header = array();

			// Find model
			$query = $this->findModel();

			// Execute query
			$model = $query->all();

			// Assign data
			if (!empty($model)) {
				// Declare counter
				$counter = 0;

				// Loop
				foreach ($model as $index => $value) {
					// Assign data
					$data[$counter]['kode_paket'] = $value->tipePaket->tipepaket_kode;
					$data[$counter]['nama_paket'] = $value->tipePaket->tipepaket_nama;
					$data[$counter]['nama_lainnya'] = $value->tipePaket->tipepaket_namalainnya;
					$data[$counter]['nama_tindakan'] = $value->daftarTindakan->daftartindakan_nama;
					$data[$counter]['status'] = $value->is_active;
					$data[$counter]['catatan'] = '';

					// Plus the counter
					$counter++;
				}
			}
			
			// File path
			$filePath = DocoHelpers::exportExcel('Paket Pelayanan', $data, $header, array("uploadPath" => "./uploads"));

			// Return
			return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
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

	// Find model
	private function findModel()
	{
		// Declare model
		$model = new PaketPelayanan;

		// Find model
		$query = $model::find()
		->where([PaketPelayanan::tableName().'.is_active' => true])
		->andWhere([PaketPelayanan::tableName().'.is_deleted' => false]);

		// Doco active filter
		$query = DocoRestActiveFilter::advancedFilter($model, $query);

		// Return query
		return $query;
	}
}
?>