<?php

/**
 * @author: Rizal
 * A product of PT. Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\integrator\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\integrator\components\LisController;
use app\modules\integrator\models\HasilPemeriksaanLabRoche;
use app\modules\integrator\models\PasienMasukPenunjang;
use app\modules\integrator\models\BridgingOrderLabRocheView;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;

class ApiRocheController extends DocoActiveController
{
    /**
     * @todo Variabel penampung model class
     * @author Rizal Faidin <rizal.faidin@sirs.co.id>
     */
    public $modelClass = '';
    const PREFIX_CODE = 'LAB20';

    public $messageBroker = [
        'sync-integerasi' => [
            'services' => [
                'Roche' => [
                    'Order' => [
                        'payload' => ['pendaftaran_id', 'pasienmasukpenunjang_id']
                    ]
                ],
                'Lis' => [
                    'BridgingLis' => [
                        'payload' => ['pendaftaran_id','pasienmasukpenunjang_id']
                    ]
                ],
            ]
        ],
        'sync-patient' => [
            'services' => [
                'Roche' => [
                    'Patient' => [
                        'payload' => ['id']
                    ]
                ],
            ]
        ],
        'result' => [
            'services' => [
                'Roche' => [
                    'FtpResult' => [
                        'payload' => [
                            'order_no' => 'data.Data.order_no',
                            'result' => 'data.Data.results'
                        ],
                        'successProcess' => true
                    ]
                ],
                'Lis' => [
                    'BridgingFtpResult' => [
                        'payload' => [
                            'order_no' => 'data.Data.order_no',
                            'result' => 'data.Data.results'
                        ],
                        'successProcess' => true
                    ]
                ],
            ]
        ],
        'sync-integerasi-result' => [
            'services' => [
                'Lis' => [
                    'BridgingLisResult' => [
                        'payload' => ['pendaftaran_id', 'pasienmasukpenunjang_id']
                    ]
                ],
            ]
        ],
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    /**
     * @todo Fungsi untuk unset verbs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["sync-integerasi"] = ["POST"];
        $verbs["sync-patient"] = ["POST"];
        return $verbs;
    }

    /**
     * @todo Fungsi untuk unset actions
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = [
            'update-file' => \app\modules\integrator\actions\UpdateFile::class,
        ];
        return $actions;
    }

    /**
     * Function Result Bridging Roche
     * 
     * @return JSON
     * @author : Rizal Faidin (rizal.faidin@sirs.co.id)
     * A product of PT. Citra Raya Nusatama
     * Powered by Sirs
     */
    public function actionResult()
    {
        $dataPost = Yii::$app->request->post();
        Yii::error(
            'Message : Payload Accepted --||--Line : 55 --||--File : ApiRocheController.php --||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : POST --||--Payload : ' . json_encode($dataPost),
            'server-error'
        );
        $dataTs = ArrayHelper::getValue($dataPost, 'data');
        $dataLab = ArrayHelper::getValue($dataTs, 'Data');
        $dataResults = ArrayHelper::getValue($dataLab, 'results', []);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $orderNo = ArrayHelper::getValue($dataLab,'order_no');
        $oldResults = HasilPemeriksaanLabRoche::find()
            ->andWhere(['order_no' => $orderNo])
            ->asArray()
            ->all();

        $listOrder = BridgingOrderLabRocheView::find()
            ->select(['tests'])
            ->andWhere(['order_no' => $orderNo])
            ->asArray()
            ->one();
        $listTest = isset($listOrder['tests']) ? json_decode($listOrder['tests'],true) : [];
        $bulkData = $mappResult = $mappOrder = [];
        if (!empty($listTest)) {
            foreach ($listTest as $value) {
                $obvId = isset($value['id']) ? $value['id'] : null;
                $obvName = isset($value['name']) ? $value['name'] : null;

                if (empty($obvId) || empty($obvName)) continue;

                $bulkData[$obvId] = [
                    'obv_id' => $obvId,
                    'obv_name' => $obvName,
                    'value' => 'Hasil Menyusul',
                    'order_no' => $orderNo,
                ];
            }
        }

        if (!empty($oldResults)) {
            foreach ($oldResults as $value) {
                $obvId = isset($value['obv_id']) ? $value['obv_id'] : null;

                if (empty($obvId) || empty($obvName)) continue;

                if (isset($bulkData[$obvId])) {
                    $bulkData[$obvId] = $value;
                }

            }
        }
        try {
            HasilPemeriksaanLabRoche::deleteAll(['order_no' => $orderNo]);
            $resultTime = $dataLab['transaction_time'] ? date('Y-m-d H:i:s', time($dataLab['transaction_time'])) : date('Y-m-d H:i:s');
            foreach ($dataResults as $key => $result) {
                $obvId = isset($result['obv_id']) ? $result['obv_id'] : null;
                if (isset($bulkData[$obvId])) {
                    $bulkData[$obvId] = [
                        'logid'=>$dataPost['logid'],
                        'ts'=>$dataPost['ts'],
                        'key'=>$dataPost['key'],
                        'data_id'=>$dataTs['_id'],
                        'data_ts'=>$dataTs['Ts'],
                        'data_reqid'=>$dataTs['ReqId'],
                        'data_key'=>$dataTs['Key'],
                        'log'=>$dataLab['log'],
                        'patient_id'=>$dataLab['patient_id'],
                        'patient_name'=>$dataLab['patient_name'],
                        'date_of_birth'=>@$dataLab['date_of_birth'] ? : null,
                        'gender'=>$dataLab['gender'],
                        'address'=>$dataLab['address'],
                        'patient_class'=>$dataLab['patient_class'],
                        'case_no'=>$dataLab['case_no'],
                        'order_ctrl'=>$dataLab['order_ctrl'],
                        'order_no'=>$dataLab['order_no'], // Roche send only 10 last digit.
                        'placer_order_no'=>$dataLab['placer_order_no'],
                        'order_status'=>$dataLab['order_status'],
                        'transaction_time'=>$resultTime,
                        'result_time'=>$dataLab['result_time'] ? : null,
                        'result_status'=>$dataLab['result_status'],
                        'priority'=>$dataLab['priority'],
                        'set_id'=>$result['set_id'],
                        'value_type'=>$result['value_type'],
                        'obv_id'=>$result['obv_id'],
                        'obv_name'=>$result['obv_name'],
                        'value'=>$result['value'],
                        'unit_text'=>$result['unit_text'],
                        'ref_range_1'=>$result['ref_range_1'],
                        'ref_range_2'=>$result['ref_range_2'],
                        'abnormal_flag'=>$result['abnormal_flag'],
                        'obv_status'=>$result['obv_status'],
                        'obv_time'=>$result['obv_time'],
                        'observer'=>$result['observer'],
                        'method'=>$result['method'],
                        'specimen_type'=>$dataLab['specimen_type'],
                        'specimen_name'=>$dataLab['specimen_name'],
                        'specimen_collection_time'=>@$dataLab['specimen_collection_time'] ? : null,
                        'additional_data'=>json_encode($dataPost),
                    ];
                }
            }
            HasilPemeriksaanLabRoche::batchInsert($bulkData);

            PasienMasukPenunjang::updateAll([
                'status_periksa' => DocoConstants::ST_P_PEN_SELESAI,
                'tanggal_verifikasi' => $resultTime
            ], ['no_masukpenunjang' => $orderNo]);

            $transaction->commit();
            return $this->responseJson(200, 'OK');
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            var_dump($e->getMessage());die;
            Yii::error($e->getMessage());
            return $this->responseJson(500, 'Terjadi Kesalahan Pada Server');
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            $transaction->rollBack();
            return $this->responseJson(500, 'Terjadi Kesalahan Pada Server');
        }
    }

    public function actionSyncIntegerasi()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncPatient()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncIntegerasiResult()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }
}