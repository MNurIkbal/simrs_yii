<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-27 17:40:53
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

use app\modules\master\models\JenisDietForm;

class JenisDietController extends DocoController
{
    /**
     * @todo Protected vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $_restMaster;
    protected $allowAction = ['*'];

    /**
     * @todo Init
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    /**
     * @todo Behaviors
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    /**
     * @todo Method untuk menampilkan halaman awal master jenis diet
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $listStatus = [
                1 => Yii::t('fe', 'Aktif'),
                0 => Yii::t('fe', 'Tidak Aktif'),
            ];
            
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk menampilkan popup create master jenis diet
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisDietForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if ($request->post()) {
                $post = $request->post('JenisDietForm');

                if (!$model->load($post, '')) {
                    $errors = DocoHelpers::parseError($model->errors, $formName);

                    return DocoHelpers::responseTemplate(422, 'Error', $errors); 
                }

                if (!$model->validate()) {
                    $errors = DocoHelpers::parseError($model->errors, $formName);

                    return DocoHelpers::responseTemplate(422, 'Error', $errors); 
                }

                $model->jenisdiet_kode = trim($model->jenisdiet_kode);
                $model->jenisdiet_nama = trim($model->jenisdiet_nama);
                $model->is_active = $model->is_active == true ? $model->is_active = true : false;

                $restMaster = $this->_restMaster->post('jenis-diet/create', [
                    'form_params' => $model
                ]);
                $response = json_decode($restMaster->getBody(), true);

                return DocoHelpers::responseTemplate($response['metadata']['status'], 'Error', $response['response']['data']);
            } else {
                $model->is_active = 1;

                return $this->renderPartial('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk melakukan proses ubah status
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUpdateStatus($id = null)
    {
        try {
            if ($id) {
                $id = DocoHelpers::decrypt($id);

                $restMaster = $this->_restMaster->post('jenis-diet/update-status', [
                    'form_params' => ['id' => $id]
                ]);
                $response = json_decode($restMaster->getBody(), true);

                echo json_encode($response);
                return;
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);

                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk melakukan proses export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $url = 'jenis-diet/export-excel?'.http_build_query($yiiRestfulParams);

            $restMaster = $this->_restMaster->get($url);
            $response = json_decode($restMaster->getBody(), true);

            return DocoHelpers::downloadFile($response['response']);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Method untuk mendapatkan data jenis diet
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataJenisDiet()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;

            $restMaster = $this->_restMaster->get('jenis-diet/get-data-jenis-diet?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restMaster->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['jenisdiet_id']);
                    $value['primary'] = $primaryKey;
                    $value['no'] = $no;
                    $value['jenisdiet_kode'] = $value['jenisdiet_kode'];
                    $value['jenisdiet_nama'] = $value['jenisdiet_nama'];
                    $value['jenisdiet_keterangan'] = $value['jenisdiet_keterangan'];
                    $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}