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
use app\modules\master\models\RuanganForm;

trait PaketRuanganTindakanTrait
{
    public function actionPaketRuanganTindakan()
    {
        $title = Yii::t('fe', 'Master Paket Ruangan');
        $data = $this->getDataPaketRuangan();
        $data_ruangan = $data_jenis_tindakan = $data_kelompok_tindakan = $data_kategori_tindakan = [];
        if (!empty($data['data_ruangan'])) {
            $data_ruangan = ArrayHelper::map($data['data_ruangan'], 'ruangan_nama', 'ruangan_nama');
        }
        return $this->renderAjax('components/paket-ruangan/index', get_defined_vars());
    }

    public function actionGetDataPaketRuangan()
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
            $response = $this->_restMaster->get('paket-ruangan/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['ruangan_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", ['class' => 'btn btn-sm btn-success', 'data-source'=>"/master/tindakan/sub-paket-ruangan?id=".$primaryKey."&nama=".$value['ruangan_nama'],'onclick'=> 'docoHelper.detail(this)']);
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

    public function actionBuatPaketRuangan()
    {
        $model = new RuanganForm;
        $request = Yii::$app->request;
        $post = $request->post();
        return $this->renderAjax('components/paket-ruangan/create', get_defined_vars());
    }

    public function actionUbahPaketRuangan($id = null)
    {
        $model = new RuanganForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $id_encrypt = $id;
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restMaster->get('ruangan/view?id=' . $id);
        $body = json_decode($response->getBody(), true);
        $attributes = $body['response'];
        $model->attributes = $attributes;
      
        return $this->renderAjax('components/paket-ruangan/create', get_defined_vars());
    }


    public function actionAutoPaket($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $q = $request->get('search');
        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restMaster->get('tipe-paket/index?advanced-filter[tipepaket_nama]=' . $q);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array(); // array dokter temp
            foreach ($body['response']['data'] as $value)
                if (!array_key_exists($value['tipepaket_id'], $temp_dokter)) {
                $result['results'][] = [
                    'id' => $value['tipepaket_id'],
                    'text' => $value['tipepaket_nama']
                ];
                $temp_dokter[$value['tipepaket_id']] = $value['tipepaket_nama'];
            }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionAutoRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $q = $request->get('search');
        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restMaster->get('paket-ruangan/get-data-ruangan?advanced-filter[ruangan_nama]=' . $q);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array(); // array dokter temp
            foreach ($body['response']['data'] as $value)
                if (!array_key_exists($value['ruangan_id'], $temp_dokter)) {
                $result['results'][] = [
                    'id' => $value['ruangan_id'],
                    'text' => $value['ruangan_nama']
                ];
                $temp_dokter[$value['ruangan_id']] = $value['ruangan_nama'];
            }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDetailPaketRuangan($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $request_get = $request->get();
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['id'] = DocoHelpers::decrypt($id);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('paket-ruangan/get-ruangan-mp', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['is_default'] = Html::checkbox('is_default', $value['is_default'], ['onclick' => 'updateDefault(' . $value['ruangan_id'] . ',' . $value['tipepaket_id'] . ')','id'=>'paket_'. $value['ruangan_id'].'_'. $value['tipepaket_id']]);
                $value['hapus'] = Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger', 'onclick' => 'hapusPaketRuangan(' . $value['ruangan_id'] . ',' . $value['tipepaket_id'] . ')']);
                $primaryKey = DocoHelpers::encrypt($value['ruangan_id']);
                $value['primary'] = $primaryKey;
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

    public function actionSimpanPaketRuangan()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;

        // Get request
        $request = Yii::$app->request;
        $formName ='PaketRuangan';

        $formData = $request->post(); // tampung formdata
        // return $formData;
        $form_params = array(
            'ruangan_id' => $formData['ruangan_id'],
            'tipepaket_id' => $formData['tipepaket_id'],
        );

        // Try catch
        try {
            $response = $this->_restMaster->post('paket-ruangan/simpan-ruangan-mp', [
                'form_params' => $form_params
            ]);
            $r = json_decode($response->getBody(), true);
            // Return
            return DocoHelpers::response($r, false, $formName);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
           
    }

    public function actionUpdatePaketRuangan()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;

        // Get request
        $request = Yii::$app->request;
        $formName = '';

        $formData = $request->post(); // tampung formdata
        // return $formData;
        $form_params = array(
            'ruangan_id' => $formData['ruangan_id'],
            'tipepaket_id' => $formData['tipepaket_id'],
            'is_default' => $formData['is_default'],
            'is_deleted' => $formData['is_deleted'],
        );

        // Try catch
        try {
            $response = $this->_restMaster->post('paket-ruangan/ubah-ruangan-mp', [
                'form_params' => $form_params
            ]);
            $r = json_decode($response->getBody(), true);
            // Return
            return DocoHelpers::response($r, false, $formName);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }

    }

    // hapus ruangan
    public function actionHapusPaketRuangan($id = null)
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        // Get request
        $request = Yii::$app->request;
        $formName = '';
        $id = DocoHelpers::decrypt($id);
        $formData = $request->post(); // tampung formdata
 
        // Try catch
        try {
            $response = $this->_restMaster->get('paket-ruangan/hapus-ruangan-mp?id='.$id, [
                'form_params' => []
            ]);
            $r = json_decode($response->getBody(), true);
            // Return
            return DocoHelpers::response($r, false, $formName);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }

    }
    // hapus ruangan

     // view sub packet
    public function actionSubPaketRuangan($id = null)
    {
        $title = Yii::t('fe', 'Master Paket');
        $status = ['1' => Yii::t('fe', 'Aktif'), '0' => Yii::t('fe', 'Tidak aktif')];
        $id_encrypt = $id;
        $id = DocoHelpers::decrypt($id);
        return $this->renderAjax('components/paket-ruangan/subpaket', get_defined_vars());
    }
    // view sub packet


    // copy paket ruangan
    public function actionCopyPaketRuangan($id = null) 
    {
        $title = 'Salin Paket Ruangan';
        $request = Yii::$app->request;
        if($request->post()) {
            $post = $request->post();
            try {
                $response = $this->_restMaster->post('paket-ruangan/copy-ruangan', [
                    'form_params' => $post
                ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body, false);
            } catch (RequestException $e) {
                return DocoHelpers::response(json_decode($e->getMessage()), 422);
            } catch (\Exception $e) {
                return DocoHelpers::response(json_decode($e->getMessage()), 422);
            }
        }
        $ruangan = $this->getDataPaketRuangan();
        $ruangan = isset($ruangan['data_ruangan']) ? ArrayHelper::map($ruangan['data_ruangan'], 'ruangan_id', 'ruangan_nama') : [];
        $id = DocoHelpers::decrypt($id);
        $ruangan_nama = isset($ruangan[$id]) ? $ruangan[$id] : '';
        return $this->renderPartial('components/paket-ruangan/copy', get_defined_vars());
    }
    // copy paket ruangan

    public function actionProsesCopyPaketRuangan(){

        // Get request
        $request = Yii::$app->request;
        $formName = '';

        $formData = $request->post(); // tampung formdata
        // return $formData;
        $id = $formData['ruangan_id'];

        $form_params = array(
            'ruangan_id' => $formData['ruangan_tujuan']
        );

        // Try catch
        try {
            $response = $this->_restMaster->post('paket-ruangan/copy-ruangan?id='.$id, [
                'form_params' => $form_params
            ]);
            $r = json_decode($response->getBody(), true);
            // Return
            return DocoHelpers::response($r, false, $formName);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }

    }


    public function actionExportPdfPaketRuangan()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/paket-ruangan.pdf";
            $response = $this->_restMaster->get('paket-ruangan/export-pdf?' . http_build_query($yiiRestfulParams), [
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


    public function actionExportExcelPaketRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'paket-ruangan/export-excel?' . http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master - Tindakan - Paket Ruangan.xlsx";
        try {
            $response = $this->_restMaster->get($url, [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getDataPaketRuangan()
    {
        try {
            $response = $this->_restMaster->get('paket-ruangan/generate-api');
            $body = json_decode($response->getBody(), true);
            $return = [
                'data_ruangan' => $body['response']['ruangan'],
            ];
            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
