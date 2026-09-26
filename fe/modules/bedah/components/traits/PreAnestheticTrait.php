<?php

namespace app\modules\bedah\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;

use app\modules\bedah\models\PreAnestesiForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

use function Complex\ln;

trait PreAnestheticTrait
{
	public function actionPreAnesthetic($id)
	{
		$response = $this->PreAnestheticData($id);
		$dataAnestesi = ArrayHelper::getValue($response, 'data');	
		$dataStatus = ArrayHelper::getValue($response['addtional_data'], 'status_anesthetic');	
		$ruangan = ArrayHelper::getValue($response['addtional_data']['ruangan'], 0);	
		$statusAnesthetic = $this->PreAnestheticStatus($dataStatus);
		$tanggal_operasi = ArrayHelper::getValue($response['addtional_data']['tanggal_operasi'], 'tgl_operasi');	

		$model = new PreAnestesiForm();
		$model->attributes = $dataAnestesi;
		$model->pasienmasukpenunjang_id = $id;
        return $this->renderAjax('partial/_preanesthetic', get_defined_vars());
	}

	public function actionSavePreAnesthetic()
	{
		$request = Yii::$app->request;
		$model = new PreAnestesiForm;
		$model->load($request->post()); 
		$model->pasienmasukpenunjang_id = DocoHelpers::decrypt($request->post('PreAnestesiForm')["pasienmasukpenunjang_id"]);
		try {
			if($model->validate()) {
				$response = $this->_restBedah->post('inf-pasien-anestesi/save-pre-anesthetic', [
					'form_params' => $model->attributes,
				]);
	
				$response = json_decode($response->getBody(),true);
				$result = isset($response['response']) ? $response['response'] : [];		
	
				return DocoHelpers::response($response);
			}
		}catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @author Maulana Muhammmad Rizky (maulana.rizky@sirs.co.id)
	 * 
	 * Fungsi untuk mengambil data anestesi
	 */
	private function PreAnestheticData($id = null)
	{	
		$id = DocoHelpers::decrypt($id);
		$dataAnestesi = $this->_restBedah->get('inf-pasien-anestesi/get-pre-anesthetic?id='.$id);
		$response = json_decode($dataAnestesi->getBody(), TRUE);
		$result = isset($response['response']) ? $response['response'] : [];
		return $result;
	}

	/**
	 * Data default untuk surgical status
	 */
	private function PreAnestheticStatus($payload)
	{
		$surgical_status = array();
		$status_asa = array();
		foreach ($payload as $key => $value) {
			if (strtolower($key) == strtolower('surgical_status')){
				$surgical_status = ArrayHelper::map($value, 'lookup_id', 'lookup_name');
			}

			if (strtolower($key) == strtolower('status_asa')){
				$status_asa = ArrayHelper::map($value, 'lookup_id', 'lookup_value');
			}
		}
		
		return array('status_surgical' => $surgical_status,'status_asa' => $status_asa);
	}
}