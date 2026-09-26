<?php

/**
 * @Author: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @Date:   2022-01-25 08:30:24
 */

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use app\modules\master\models\BankForm;
use yii\helpers\ArrayHelper;

class BankController extends DocoController
{
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

    public function getApi()
    {
        $request = $this->_restMaster->get('allow/get-api');
        $response = json_decode($request->getBody(), true);

        $data = [
            'provinsi'  => $response['response']['lookup']['provinsi'],
            'kabupaten' => $response['response']['lookup']['kabupaten']
        ];

        return $data;
    }

    public function actionIndex()
    {
        $is_active = $this->_options['status'];
        return $this->render('index', get_defined_vars());
    }

    public function actionCreate()
    {
        $model = new BankForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('app', 'Tambah Akun Bank');
        $list_provinsi  = ArrayHelper::map($this->getApi()['provinsi'], 'propinsi_id', 'propinsi_nama');
        $list_kabupaten = ArrayHelper::map($this->getApi()['kabupaten'], 'kabupaten_id', 'kabupaten_nama');
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());

                if ($model->validate()) {
                    $request = $this->_restMaster->post('bank/create', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($request->getBody(), true);
                    return DocoHelpers::responseTemplate(200, 'Success', $response);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    // Action update
    public function actionUpdate($id = null)
    {
        try {
            $model = new BankForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $title = Yii::t('app', 'Ubah Akun Bank');
            $list_provinsi  = ArrayHelper::map($this->getApi()['provinsi'], 'propinsi_id', 'propinsi_nama');
            $list_kabupaten = ArrayHelper::map($this->getApi()['kabupaten'], 'kabupaten_id', 'kabupaten_nama');

            $encryptedId = $id;
            $id = DocoHelpers::decrypt($id);

            $request = $this->_restMaster->get('bank/view?id='.$id);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            $model->attributes = $attributes;

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());

                if ($model->validate()) {
                    $request = $this->_restMaster->post('bank/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($request->getBody(), true);

                    return DocoHelpers::responseTemplate(200, 'Success', $response);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            }
            else {
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $request = $this->_restMaster->delete('bank/delete?id='.$id);
            $request = json_decode($request->getBody(),true);
            return DocoHelpers::response($request);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    // Action get data
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();


        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params);
        $draw = Yii::$app->request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $request = $this->_restMaster->get('bank/get-data?'.http_build_query($yiiRestfulParams));
            $response = json_decode($request->getBody(), true);

            $no = Yii::$app->request->get('start', 1);

            if (!empty($response['response']['data'])) {
                foreach ($response['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['bank_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['bank_id']);
                    $value['rowNum'] = $no;
                    $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
                    $value['status_aktif'] = $value['is_active'] ? "Aktif" : "Tidak Aktif";
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $response['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $response['response']['_meta']['totalCount'];

                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        } catch (\Exception $e) {
            // Error result
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        }
    }

    // function change status
    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->put('bank/update?id='.$id, [
                'form_params' => ["is_active" => $status]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
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
}
