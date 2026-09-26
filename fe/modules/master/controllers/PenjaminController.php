<?php
// Author : Ramdhan Nurrachman

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\PenjaminForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;

class PenjaminController extends DocoController
{
    protected $_title = "Penjamin";
    protected $_module = 'master/penjamin/';
    protected $_restMaster;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        // Init
        $status = $this->_status;
        $options = $this->_options;
        $carabayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar');
        $body = json_decode($carabayarRequest->getBody(), TRUE);
        $carabayar = $body['response'];
        return $this->render('index', get_defined_vars());

        return $this->render('index', get_defined_vars());
    }

    public function actionGetDataPenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('penjamin/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penjamin_id']);
                $value['primary'] = $primaryKey;
                unset($value['penjamin_id']);

                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Lihat') . ' ' . \Yii::t('fe', $this->_title);
        $model = new PenjaminForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('penjamin/view?id=' . $id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        // Init
        $status = $this->_status;
        $options = $this->_options;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah') . ' ' . \Yii::t('fe', $this->_title);
        $model = new PenjaminForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);


        if ($request->post()) {
            $post = $request->post();
            $penjaminForm = $post['PenjaminForm'];
            $model->load($request->post());
            $model->type_method = 'create';
            $model->scenario = 'create';
            if ($model->validate()) {
                try {
                    for ($i = 0; $i < count($penjaminForm['penjamin_nama']); $i++) {

                        $attributes = [
                            'carabayar_id' => $penjaminForm['carabayar_id'],
                            'penjamin_nama' => $penjaminForm['penjamin_nama'][$i],
                            'penjamin_kode' => $penjaminForm['penjamin_kode'][$i],
                            'penjamin_namalainnya' => $penjaminForm['penjamin_namalainnya'][$i],
                            'groupmargin_id' => $penjaminForm['groupmargin_id'][$i],
                            'is_online' => 1
                        ];
                        $arrData['penjamin'][] = $attributes;
                    }
                    $response = $this->_restMaster->post('penjamin/create-penjamin', [
                        'form_params' => $arrData
                    ]);
                    $body = json_decode($response->getBody(), True);

                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            try {
                $response = $this->_restMaster->get('cara-bayar/get-list-cara-bayar');
                $body = json_decode($response->getBody(), TRUE);
                $caraBayar = $body['response'];

                $marginhargaRequest = $this->_restMaster->get('allow/get-list-group-margin?keyValue=groupmargin_id');
                $bodyMargin = json_decode($marginhargaRequest->getBody(), TRUE);
                $margingroup = $bodyMargin['response'];
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }

            return $this->renderAjax('form-tambah-penjamin', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        // Init
        $status = $this->_status;
        $options = $this->_options;

        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah') . ' ' . \Yii::t('fe', $this->_title);
        $model = new PenjaminForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            $model->type_method = 'update';
            $model->scenario = 'edit';
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('penjamin/update-penjamin?id=' . $id, [
                        'form_params' => $model->attributes
                    ]);

                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('penjamin/view?id=' . $id);
            $response = json_decode($response->getBody(), true);
            $response = $response['response'];
            $dataPenjamin = ArrayHelper::getValue($response, 'penjamin', []);
            $groupMargin = ArrayHelper::getValue($response, 'group_margin', []);
            $konfigAsuransi = ArrayHelper::getValue($response, 'konfig_asuransi', []);
            $model->attributes = $dataPenjamin;
            $model->groupmargin_id = ArrayHelper::getValue($dataPenjamin, 'groupmargin_id');
            $model->is_active = ($model->is_active) ? '1' : '0';
            $model->is_online = ($model->is_online) ? '1' : '0';
            $groupCaraBayarId = ArrayHelper::getValue($response, 'groupcarabayar_id');
            $groupJaminan = DocoConstants::GROUP_JAMINAN;
            $disabled = ($groupCaraBayarId == $groupJaminan) ? false : true;
            $defaultOptionsCaraBayar = [
                'id' => $model->carabayar_id,
                'text' => ArrayHelper::getValue($response, 'carabayar_nama'),
            ];
            $defaultOptionsCaraBayar = json_encode($defaultOptionsCaraBayar);
            $konfigAsuransiId = $model->konfigasuransi_id;
            return $this->renderAjax('form-update-penjamin', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $id = json_decode(DocoHelpers::decrypt($id));
        try {
            $response = $this->_restMaster->delete('penjamin/delete-penjamin', [
                'query' => [
                    'id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionExport($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionChangeStatusPenjamin($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->put('penjamin/update-online?id=' . $id . '&is_online=' . $status);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil') . " !",
                'text' => \Yii::t('fe', "Tampil di mobile berhasil diubah."),
                'response' => $response
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ') . " !",
                'text' => \Yii::t('fe', "Tampil di mobile tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }



    /**
     * @author Naufal Ziyad L
     * @since
     * @param
     * @return
     * @desc
     */
    public function actionListPenjamin()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $carabayar_id = empty($post['depdrop_parents'][0]) ? null : $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restMaster->get('allow/list-penjamin?carabayar_id=' . $carabayar_id);
        $body = json_decode($penjaminRequest->getBody(), TRUE);
        $responses = $body['response'];

        $out = [];
        foreach ($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        return json_encode(['output' => $out, 'selected' => '']);
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/penjamin.pdf";
        try {
            $response = $this->_restMaster->get('penjamin/export-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'penjamin/export-excel?' . http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master Penjamin.xlsx";
        try {
            $response = $this->_restMaster->get($url, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataSelect2()
    {
        return $this->guzzleExec($this->_restMaster, [
            'url' => 'penjamin/get-data-select2',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }
    
    public function actionFilters()
    {
        $request = Yii::$app->request;
        $payload = $request->get('payload', []);
        $term = ArrayHelper::getValue($payload, 'term');
        $limit = ArrayHelper::getValue($payload, 'limit');
        $page = $request->get('page', 1);
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'penjamin/filters',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'limit' => $limit,
                    'page' => $page,
                ]
            ],
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }
}
