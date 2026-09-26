<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Tindakan Ruangan
 * @copyright 26 April 2018 aweutist
 */

namespace app\modules\master\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\master\models\TindakanRuanganForm;

trait TindakanRuanganTrait
{
    public function actionTindakanRuangan()
    {
        $title = Yii::t('fe', 'Master kategori');
        $data = $this->getDataTindakanRuangan();
        $data_ruangan = $data_jenis_tindakan = $data_kelompok_tindakan = $data_kategori_tindakan = [];
        if (!empty($data['data_ruangan'])) {
            $data_ruangan = ArrayHelper::map($data['data_ruangan'], 'ruangan_id', 'ruangan_nama');
        }
        return $this->renderAjax('components/tindakan-ruangan/index', get_defined_vars());
    }

    public function actionGetDataTindakanRuangan()
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
            $response = $this->_restMaster->get('tindakan-ruangan/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = $this->helper->encrypt($value['ruangan_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", ['class' => 'btn btn-sm btn-info', 'data-source' => "/master/tindakan/detail-tindakan-ruangan?id=" . $primaryKey . "&nama=" . $value['ruangan_nama'], 'onclick' => 'docoHelper.detail(this)']);
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

    public function actionGetDetailTindakanRuangan($id, $detail = false)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = DocoHelpers::decrypt($id);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            if ($detail) {
                $response = $this->_restMaster->get('tindakan-ruangan/view-detail?id=' . DocoHelpers::decrypt($id), ['form_params' => []]);
            } else {
                $response = $this->_restMaster->get('tindakan-ruangan/view?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            }

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $checked = '';
                if ($value['is_default']) {
                    $checked = 'checked';
                }
                $primary = DocoHelpers::encrypt($value['daftartindakan_id']);
                $url_delete = Url::to(['tindakan/delete-tindakan-ruangan', 'daftartindakan_id' => $primary, 'ruangan_id' => $value['ruangan_id']]);
                $url_update = Url::to(['tindakan/update-tindakan-ruangan', 'daftartindakan_id' => $primary, 'ruangan_id' => $value['ruangan_id']]);
                $value['rowNum'] = $no;
                $value['default'] = '<input type="checkbox" ' . $checked . ' class="check-tindakan-ruangan" action="' . $url_update . '">';
                $value['hapus'] = '<a class="btn btn-danger btn-sm btn-hapus-tr" action="' . $url_delete . '"><i class="fa fa-trash"></i></a>';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : count($data);
            $result['recordsFiltered'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : count($data);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetailTindakanRuangan($id, $nama)
    {
        // $request = $this->_restMaster->request('GET', 'tindakan-ruangan/view?id='.DocoHelpers::decrypt($id));
        // $response = json_decode($request->getBody(), true);
        // $attributes = $response['response'];
        $ruangan = $nama;
        $id = $id;
        return $this->renderAjax('components/tindakan-ruangan/_detail', get_defined_vars());
    }

    public function actionMapTindakanRuangan($id)
    {
        $model = new TindakanRuanganForm;
        $id = $id;
        try {
            $response = $this->_restMaster->get('tindakan-ruangan/view-detail?id=' . DocoHelpers::decrypt($id), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            $body = [];
        }

        $data = isset($body['response']['data']) ? $body['response']['data'] : [];
        $ruangan_nama = isset($data[0]['ruangan_nama']) ? $data[0]['ruangan_nama'] : '';
        return $this->renderAjax('components/tindakan-ruangan/create', get_defined_vars());
    }

    private function getDataTindakanRuangan()
    {
        try {
            $response = $this->_restMaster->get('tindakan-ruangan/generate-api');
            $body = json_decode($response->getBody(), true);

            $return = [
                'data_ruangan' => $body['response']['ruangan'],
            ];
            return $return;
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionAddTindakanRuangan()
    {
        $request = Yii::$app->request;
        $model = new TindakanRuanganForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $post = $request->post();
            $model->attributes = $post;
            $model->ruangan_id = DocoHelpers::decrypt($model->ruangan_id);
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('tindakan-ruangan/create', [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);
                    return DocoHelpers::response($body, false);
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
    }

    public function actionDeleteTindakanRuangan($daftartindakan_id, $ruangan_id)
    {
        $request = Yii::$app->request;
        $params = ['daftartindakan_id' => DocoHelpers::decrypt($daftartindakan_id), 'ruangan_id' => $ruangan_id];
        try {
            $response = $this->_restMaster->post('tindakan-ruangan/delete', [
                'query' => $params
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response'], 200);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionUpdateTindakanRuangan($daftartindakan_id, $ruangan_id)
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $post = $request->post();
            $get = $request->get();
            $param = ['daftartindakan_id' => DocoHelpers::decrypt($get['daftartindakan_id']), 'ruangan_id' => $ruangan_id, 'is_default' => $post['is_default']];
            try {
                $response = $this->_restMaster->post('tindakan-ruangan/update-default', [
                    'form_params' => $param
                ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body, false);
            } catch (RequestException $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }
    }

    public function actionGetTindakanRuanganById()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $response = $this->_restMaster->request('POST', 'tindakan-ruangan/index?ruangan_id=' . $post['ruangan_id']);
        $body = json_decode($response->getBody(), true);

        return DocoHelpers::response($body, false, true);
    }

    public function actionSalinTindakanRuangan($id = null)
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $post = $request->post();
            try {
                $response = $this->_restMaster->post('tindakan-ruangan/salin-tindakan-ruangan', [
                    'form_params' => $post
                ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body, false);
            } catch (RequestException $e) {
                return DocoHelpers::response(json_decode($e->getMessage()), 422);
                // return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::response(json_decode($e->getMessage()), 422);
            }
            return DocoHelpers::response($request->post());
        }
        $ruangan = $this->getDataTindakanRuangan();
        $ruangan = isset($ruangan['data_ruangan']) ? ArrayHelper::map($ruangan['data_ruangan'], 'ruangan_id', 'ruangan_nama') : [];
        $id = DocoHelpers::decrypt($id);
        $ruangan_nama = isset($ruangan[$id]) ? $ruangan[$id] : '';
        $title = Yii::t('fe', 'Salin tindakan ruangan');
        return $this->renderAjax('components/tindakan-ruangan/_salinruangan', get_defined_vars());
    }

    public function actionPdfTindakanRuangan()
    {
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/tindakan-ruangan.pdf";
            $response = $this->_restMaster->get('tindakan-ruangan/export-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    // Export excel
    public function actionExcelTindakanRuangan()
    {
        // Convert to json format
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/Master - Tindakan - tindakan-ruangan.xlsx";
        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('tindakan-ruangan/export-excel?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            // Return download file
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return
            return $result;
        } catch (RequestException $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return
            return $result;
        }
    }
}
