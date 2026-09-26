<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 17:03:24
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

use app\modules\master\models\MakananForm;

class MakananController extends DocoController
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
     * @todo Method untuk menampilkan halaman awal master makanan
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
     * @todo Action untuk menampilkan popup create master menu diet
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new MakananForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if ($request->post()) {
                $post = $request->post('MakananForm');

                if (!$model->load($post, '')) {
                    $errors = DocoHelpers::parseError($model->errors, $formName);

                    return DocoHelpers::responseTemplate(422, 'Error', $errors); 
                }

                if (!$model->validate()) {
                    $errors = DocoHelpers::parseError($model->errors, $formName);

                    return DocoHelpers::responseTemplate(422, 'Error', $errors); 
                }

                $model->makanandiet_kode = trim($model->makanandiet_kode);
                $model->makanandiet_nama = trim($model->makanandiet_nama);
                $model->is_active = $model->is_active == true ? $model->is_active = true : false;

                $restMaster = $this->_restMaster->post('makanan/create', [
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

                $restMaster = $this->_restMaster->post('makanan/update-status', [
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
     * @todo Action untuk melakukan proses export pdf
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/makanan.pdf";

            $restMaster = $this->_restMaster->get('makanan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
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
            $url = 'makanan/export-excel?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/makanan.xlsx";

            $restMaster = $this->_restMaster->get($url, ['save_to' => $path]);
            $response = json_decode($restMaster->getBody(), true);

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Method untuk mendapatkan data makanan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataMakanan()
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

            $restMaster = $this->_restMaster->get('makanan/get-data-makanan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restMaster->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['makanandiet_id']);
                    $value['primary'] = $primaryKey;
                    $value['no'] = $no;
                    $value['makanandiet_kode'] = $value['makanandiet_kode'];
                    $value['makanandiet_nama'] = $value['makanandiet_nama'];
                    $value['makanandiet_keterangan'] = $value['makanandiet_keterangan'];
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