<?php

namespace app\modules\bedah\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

use app\modules\bedah\models\AnestesiForm;
use app\modules\bedah\models\AnestesiDetailForm;

trait AnestesiTrait
{
	public function actionAnestesi()
	{
		$model = new AnestesiForm();
        $request = Yii::$app->request;
        $resultList = $regionalList = $generalList = $detailAnestesi = [];

		if ($post = $request->post()) {
			$model->load($post);
            
            $detailAnestesi = $this->mapDetailAnestesi(json_decode($post['detailAnestesi'], true));
            $isUpdatePremed = $post['isUpdatePremed'];
            
	        $response = $this->helper->guzzleExec($this->_restBedah, [
	            'url' => 'inf-pasien-anestesi/save-anestesi',
	            'method' => 'POST'
	        ], [
	        	'form_params' => [
                    'anestesi' => $model->attributes,
                    'anestesi_detail' => $detailAnestesi,
                    'is_update' => $isUpdatePremed,
                ]
	        ]);
	        return DocoHelpers::response($response);
		}

        
        $pasienmasukpenunjang_id = DocoHelpers::decrypt($request->get('pasienmasukpenunjang_id'));
        $model->pasienmasukpenunjang_id = $pasienmasukpenunjang_id;

        $getPack = $this->helper->guzzleExec($this->_restBedah, [
            'url' => 'inf-pasien-anestesi/get-pack-anestesi',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
                ]
            ]
        ]);
        $model->attributes = $getPack['dataAnestesi'];
        $detailAnestesi = $this->mapDetailAnestesi($getPack['dataAnestesiDet']);

        $resultList = $this->mapLookKeperawatan($getPack['dataPack']['anestesi_result']);
        $regionalList = $this->mapLookKeperawatan($getPack['dataPack']['anestesi_regional']);
        $generalList = $this->mapLookKeperawatan($getPack['dataPack']['anestesi_general']);

        return $this->renderAjax('partial/_anesthetic', get_defined_vars());
	}

    public function actionTambahObatAnestesi()
    {
        $request = Yii::$app->request;
		$model = new AnestesiDetailForm;
		$title = 'Tambah Drug';
		$formName = substr(strrchr(get_class($model), "\\"), 1);

        return $this->renderAjax('partial/_tambah_obat_anestesi', get_defined_vars());
    }

    public function actionGetObatAnestesi($search = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $results = [];

        $response = $this->helper->guzzleExec($this->_restBedah, [
            'url' => 'allow/get-jenis-alat',
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'obatalkes_nama' => $search
                ]
            ]
        ]);
 
        if (!empty($response)) {
            foreach ($response as $key => $value) {
                $results[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                ];
            }
        }

        return ['results' => $results];
    }

    private function mapLookKeperawatan($data)
    {
        return ArrayHelper::map($data, 'lookupkeperawatan_id', 'lookup_name');
    }

    private function mapDetailAnestesi($data, $idAsIndex = false)
    {
        $result = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if ($idAsIndex) {
                    $result[$value['anestesi_id']] = [
                        'anestesi_id' => $value['anestesi_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'dose' => $value['dose'],
                        'time_delivery' => $value['time_delivery'],
                    ];
                } else {
                    $result[] = [
                        'anestesi_id' => $value['anestesi_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'dose' => $value['dose'],
                        'time_delivery' => $value['time_delivery'],
                    ];
                }
            }
        }

        return $result;
    }
}