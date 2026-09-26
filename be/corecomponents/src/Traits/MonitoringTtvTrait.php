<?php

namespace Doco\Traits;

use Yii;
use Doco\models\VitalSign;
use Doco\models\Lookup;
use Doco\models\Pendaftaran;
use yii\helpers\ArrayHelper;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

trait MonitoringTtvTrait
{
    
    // get monitoring TTV data
    public function actionGetData()
    {
        // default date range for TTV data
        // if no date range is provided, it will default to today
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id', null);
            $isFilterDate = $request->get('is_filter_date', true);

            if ($pendaftaranId === null) {
                Yii::$app->response->statusCode = 400;
                return [
                    'message' => 'Missing required parameters',
                    'payload' => [
                        'pendaftaran_id' => $pendaftaranId
                    ]
                ];
            }
            $model = new VitalSign;
            $query = $model::find()
                ->where(['pendaftaran_id' => $pendaftaranId])
                ->andWhere(['is_deleted' => false]);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tanggal_ttv'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_ttv']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_ttv']);
                }
            }

            if($isFilterDate) {
                $query->andWhere(['between', 'tanggal_ttv', $start, $end]);
            }
            
            $query->orderBy(['created_date' => SORT_DESC]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            return new ActiveDataProvider([
				 'query' => $query,
			]);
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDetail()
    {
        $request = Yii::$app->request;
        $vitalsignId = $request->get('vitalsign_id');
        $pendaftaranId = $request->get('pendaftaran_id');

        if ($vitalsignId === null || $pendaftaranId === null) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Missing required parameters',
                'payload' => [
                    'id' => $vitalsignId,
                    'pendaftaran_id' => $pendaftaranId
                ]
            ];
        }

        try {
            $model = new VitalSign;
            $query = $model::find()
                ->where(['vitalsign_id' => $vitalsignId, 'pendaftaran_id' => $pendaftaranId])
                ->andWhere(['is_deleted' => false]);

            $data = $query->asArray()->one();

            if ($data === null) {
                Yii::$app->response->statusCode = 404;
                return [
                    'message' => 'TTV data not found',
                    'payload' => [
                        'vitalsign_id' => $vitalsignId,
                        'pendaftaran_id' => $pendaftaranId
                    ]
                ];
            }

            return $data;
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetFilter()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $tingkatKesadaran = [];
        $jenisTtv = [];
        $sumberTtv = [];
        $lookupData = $this->monitoringTtvDataFilter();
        $pendaftaran = Pendaftaran::findOne($pendaftaranId);
        $tglPendaftaran = ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran');
        foreach ($lookupData as $item) {
            switch ($item->lookup_type) {
                case 'tingkat_kesadaran':
                    $tingkatKesadaran[$item->lookup_id] = $item->lookup_value;
                    break;
                case 'jenis_ttv':
                    $jenisTtv[$item->lookup_id] = $item->lookup_value;
                    break;
                case 'sumber_ttv':
                    $sumberTtv[$item->lookup_id] = $item->lookup_value;
                    break;
            }
        }

        return [
            'tingkatKesadaran' => $tingkatKesadaran,
            'jenisTtv' => $jenisTtv,
            'sumberTtv' => $sumberTtv,
            'tgl_pendaftaran' => $tglPendaftaran
        ];
    }

    private function monitoringTtvDataFilter()
    {
        // find lookup type tingkat_kesadaran, jenis_ttv, sumber_ttv
        $lookupData = Lookup::find()
            ->select(['lookup_id', 'lookup_name', 'lookup_type', 'lookup_value'])
            ->where(['lookup_type' => ['tingkat_kesadaran', 'jenis_ttv', 'sumber_ttv'], 'is_active' => true, 'is_deleted' => false])
            ->orderBy(['lookup_urutan' => SORT_ASC])
            ->all();
        
        return $lookupData;
    }

    // insert TTV data
    public function actionInsert()
    {
        $request = Yii::$app->request;
        $data = $request->post();

        if (empty($data['pendaftaran_id']) || empty($data['sumberttv_id'])) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Missing required parameters',
                'payload' => $data
            ];
        }

        if(empty($data['pasien_id'])) {
            $pasienId = Pendaftaran::find()
                ->select('pasien_id')
                ->where(['pendaftaran_id' => $data['pendaftaran_id']])
                ->scalar();

            $data['pasien_id'] = $pasienId;
        }

        $vitalSign = new VitalSign();
        $vitalSign->load($data, '');
        $vitalSign->created_date = date('Y-m-d H:i:s');
        $vitalSign->is_active = true;
        $vitalSign->is_deleted = false;
        $vitalSign->created_by = Yii::$app->user->id;
        $vitalSign->last_modified_by = null;
        $vitalSign->last_modified_date = null;
        $vitalSign->modified_count = 0;
        $vitalSign->deleted_date = null;
        $vitalSign->deleted_by = null;
        
        if ($vitalSign->save()) {
            return [
                'message' => 'TTV data inserted successfully',
                'payload' => $vitalSign
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Failed to insert TTV data',
                'errors' => $vitalSign->getErrors()
            ];
        }
    }

    // update TTV data
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $data = $request->getBodyParams(); // This will work for both POST and PUT
        $vitalsignId = isset($data['vitalsign_id']) ? $data['vitalsign_id'] : null;
        $userId = Yii::$app->user->id;

        if (empty($data['vitalsign_id'])) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Missing required parameters',
                'payload' => $data
            ];
        }

        $vitalSign = VitalSign::findOne($data['vitalsign_id']);
        if (!$vitalSign) {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'TTV data not found',
                'payload' => $data
            ];
        }

        $vitalSign->load($data, '');
        $vitalSign->last_modified_date = date('Y-m-d H:i:s');
        $vitalSign->last_modified_by = Yii::$app->user->id;
        $vitalSign->modified_count += 1;

        if ($vitalSign->save()) {
            return [
                'message' => 'TTV data updated successfully',
                'payload' => $vitalSign
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Failed to update TTV data',
                'errors' => $vitalSign->getErrors()
            ];
        }
    }
    
    // delete TTV data
    public function actionDelete()
    {
        $request = Yii::$app->request;
        $vitalsignId = $request->post('vitalsign_id', null);

        if ($vitalsignId === null) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Missing required parameters',
                'payload' => ['vitalsign_id' => $vitalsignId]
            ];
        }

        $vitalSign = VitalSign::findOne($vitalsignId);
        if (!$vitalSign) {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'TTV data not found',
                'payload' => ['vitalsign_id' => $vitalsignId]
            ];
        }

        $vitalSign->is_deleted = true;
        $vitalSign->deleted_date = date('Y-m-d H:i:s');
        $vitalSign->deleted_by = Yii::$app->user->id;

        if ($vitalSign->save()) {
            return [
                'message' => 'TTV data deleted successfully',
                'payload' => $vitalSign
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return [
                'status' => 'failed',
                'message' => 'Failed to delete TTV data',
                'errors' => $vitalSign->getErrors()
            ];
        }
    }
}