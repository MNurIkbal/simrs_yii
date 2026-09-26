<?php

namespace app\modules\master\components\traits;

use Yii;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoDatatableHelper;

use app\modules\master\models\TindakanSpesialisForm;

trait TindakanSpesialisTrait
{
    public function actionTindakanSpesialis()
    {
        $title = Yii::t('fe', 'Master kategori');
        $data = $this->getDataTindakanSpesialis();
        $data_spesialis = $data_jenis_tindakan = $data_kelompok_tindakan = $data_kategori_tindakan = [];
        if (!empty($data['data_spesialis'])) {
            $data_spesialis = ArrayHelper::map($data['data_spesialis'], 'spesialis_id', 'spesialis_nama');
        }
        return $this->renderAjax('components/tindakan-spesialis/index', get_defined_vars());
    }

    public function actionGetDataTindakanSpesialis()
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
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'tindakan-spesialis/index',
                'payload' => [
                    'query' => $yiiRestfulParams
                ]
            ]);
            $no = $request->get('start', 1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $primaryKey = $this->helper->encrypt($value['spesialis_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", ['class' => 'btn btn-sm btn-info', 'data-source' => "/master/tindakan/detail-tindakan-spesialis?id=" . $primaryKey . "&nama=" . $value['spesialis_nama'], 'onclick' => 'docoHelper.detail(this)']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDetailTindakanSpesialis($id, $detail = false)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['spesialis_id'] = $this->helper->decrypt($id);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            if ($detail) {
                $response = $this->guzzleExec($this->_restMaster, [
                    'url' => 'tindakan-spesialis/view-detail',
                    'payload' => [
                        'query' => [
                            'id' => $this->helper->decrypt($id)
                        ]
                    ]
                ]);
            } else {
                $response = $this->guzzleExec($this->_restMaster, [
                    'url' => 'tindakan-spesialis/view',
                    'payload' => [
                        'query' => $yiiRestfulParams
                    ]
                ]);
            }

            $no = $request->get('start', 1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $primary = $this->helper->encrypt($value['daftartindakan_id']);
                $url_delete = Url::to(['tindakan/delete-tindakan-spesialis', 'daftartindakan_id' => $primary, 'spesialis_id' => $value['spesialis_id']]);
                $value['rowNum'] = $no;
                $value['hapus'] = '<a class="btn btn-danger btn-sm btn-hapus-ts" action="' . $url_delete . '"><i class="fa fa-trash"></i></a>';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = isset($response['_meta']['totalCount']) ? $response['_meta']['totalCount'] : count($data);
            $result['recordsFiltered'] = isset($response['_meta']['totalCount']) ? $response['_meta']['totalCount'] : count($data);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetailTindakanSpesialis($id, $nama)
    {
        $id = $id;
        $spesialis = $nama;
        return $this->renderAjax('components/tindakan-spesialis/_detail', get_defined_vars());
    }

    public function actionMapTindakanSpesialis($id)
    {
        $model = new TindakanSpesialisForm;
        try {
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'tindakan-spesialis/view-detail',
                'payload' => [
                    'query' => [
                        'id' => $this->helper->decrypt($id)
                    ]
                ]
            ]);
        } catch (Exception $e) {
            $response = [];
        }

        $data = isset($response['data']) ? $response['data'] : [];
        $spesialis_nama = isset($data[0]['spesialis_nama']) ? $data[0]['spesialis_nama'] : '';
        return $this->renderAjax('components/tindakan-spesialis/create', get_defined_vars());
    }

    private function getDataTindakanSpesialis()
    {
        try {
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'tindakan-spesialis/generate-api'
            ]);

            $return = [
                'data_spesialis' => $response['spesialis'],
            ];
            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionAddTindakanSpesialis()
    {
        $request = Yii::$app->request;
        $model = new TindakanSpesialisForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($post = $request->post()) {
            $model->attributes = $post;
            $model->spesialis_id = $this->helper->decrypt($model->spesialis_id);
            if ($model->validate()) {
                try {
                    return $this->guzzleExec($this->_restMaster, [
                        'url' => 'tindakan-spesialis/create',
                        'method' => 'POST',
                        'payload' => [
                            'form_params' => $model->attributes
                        ],
                        'returnResponse' => true
                    ]);
                } catch (RequestException $e) {
                    return $this->helper->responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return $this->helper->responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = $this->helper->parseError($model->errors, $formName);
                return $this->helper->responseTemplate(422, 'Error', $errors);
            }
        }
    }

    public function actionDeleteTindakanSpesialis($daftartindakan_id, $spesialis_id)
    {
        $params = [
            'daftartindakan_id' => $this->helper->decrypt($daftartindakan_id),
            'spesialis_id' => $spesialis_id
        ];
        try {
            return $this->guzzleExec($this->_restMaster, [
                'url' => 'tindakan-spesialis/delete',
                'method' => 'POST',
                'payload' => [
                    'form_params' => $params
                ],
                'returnResponse' => true
            ]);
        } catch (RequestException $e) {
            $this->logError($e);
            return $this->helper->responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->helper->responseTemplate(500, $e->getMessage());
        }
    }
}
