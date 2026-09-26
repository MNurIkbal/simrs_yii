<?php 

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DHtml;
use app\components\DocoDatatableHelper;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\master\models\ZatAktifObatForm;

class ZatAktifObatController extends DocoController {
    public $_title = "Zat Aktif Obat";
    public $_restMaster;

    public function init() {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex() {
        $filters = $this->guzzleExec($this->_restMaster, [
            'url' => 'zat-aktif-obat/filters',
            'payload' => [
                'query' => [
                    'types' => [
                        'jenisobatalkes',
                    ]
                ]
            ]
        ]);

        return $this->render('index', [
            'title' => $this->_title,
            'dropdown' => $filters
        ]);
    }

    public function actionDatatable() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'zat-aktif-obat/get-list',
            'method' => 'get',
            'payload' => [
                'query' => $payload
            ]
        ]);

        foreach ($response['data'] as $key => $record) {
            $response['data'][$key]['rowNum'] = $key+1;
            $response['data'][$key]['primary'] = $this->helper->encrypt($record['obatalkes_id']);
            $response['data'][$key]['checkbox'] = `<input type="checkbox" />`;
        }

        return $response;
    }

    public function actionFilters($type) {
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'zat-aktif-obat/filters',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'types' => $type,
                    'term' => Yii::$app->request->get('term'),
                    'additionalPayload' => Yii::$app->request->get('additionalPayload', []),
                    'page' => Yii::$app->request->get('page', 1),
                ]
            ]
        ]);

        return $this->responseJson(200, 'Data berhasil diambil!', $response[$type]);
    }

    public function actionDetail($id)
    {
        $title = "Detail Zat Aktif Obat";
        $id = DocoHelpers::decrypt($id);

        $response = $this->getDetailObat($id);

        return $this->render('detail', ['id' => $id, 'response' => $response, 'title' => $title]);
    }

    private function getDetailObat($obatalkes_id) {
        $payload = ['id' => $obatalkes_id];
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'zat-aktif-obat/detail',
            'method' => 'get',
            'payload' => [
                'query' => $payload
            ]
        ]);

        return $response;
    }

    public function actionGetListDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $request->get('id');
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('/master/v1/zat-aktif-obat/get-list-detail', 
                [
                    'query' => $yiiRestfulParams
                ]);

            $body = json_decode($response->getBody(), true);

            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $value['rowNum'] = $no;
                    $primaryKey = DocoHelpers::encrypt($value['zataktif_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['zataktif_id']);
                    $value['is_utama'] = $value['is_primary'];
                    $value['is_primary'] = DocoHelpers::switchStatus($value['is_primary'], $primaryKey, 'change-status', 'Ya', 'Tidak');

                    if($value['primary'] != null) {
                        $data[$key] = $value;
                    }
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionAssignPrimary()
    {
        $request = Yii::$app->request;

        $payload = [
            'obatalkes_id' => $request->get('id'),
            'zataktif_id' => DocoHelpers::decrypt($request->get('zataktif_id')),
            'is_primary' => $request->get('is_primary') == 1 ? 'true' : 'false'
        ];

        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'zat-aktif-obat/assign-primary',
            'method' => 'post',
            'payload' => [
                'form_params' => $payload
            ]
        ]);

        return $this->responseJson(200, 'Data berhasil disimpan!', $response);
    }

    public function actionCreate($id)
    {
        $request = Yii::$app->request;
        $title = "Tambah Zat Aktif Obat";
        $dataObat = $this->getDetailObat($id);
        $model = new ZatAktifObatForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        $listZatAktifObat = $this->guzzleExec($this->_restMaster, [
            'url' => 'allow/get-list-zat-aktif',
            'method' => 'get'
        ]);

        $listZatAktifObat = ArrayHelper::map($listZatAktifObat, 'zataktif_id', 'zataktif_nama');

        if($request->post()) {
            $model->attributes = $request->post();
            if($model->validate()) {
                try {
                    $response = $this->guzzleExec($this->_restMaster, [
                        'url' => 'zat-aktif-obat/create',
                        'method' => 'post',
                        'payload' => [
                            'form_params' => $model->attributes
                        ],
                    ]);

                    return $this->responseJson($response['code'], $response['message'], $response);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        return $this->renderAjax('_create', get_defined_vars());
    }

    public function actionRemove()
    {
        $request = Yii::$app->request;
        $arr_zataktif_id = $request->post('zataktif_ids', []);
        $zataktif_ids = [];

        foreach($arr_zataktif_id as $value) {
            $zataktif_ids[] = DocoHelpers::decrypt($value);
        }

        $payload = [
            'obatalkes_id' => $request->get('obatalkes_id'),
            'zataktif_id' => $zataktif_ids
        ];

        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'zat-aktif-obat/remove',
            'method' => 'post',
            'payload' => [
                'form_params' => $payload
            ]
        ]);

        return $this->responseJson(200, 'Data berhasil dihapus!', $response);
    }
}
