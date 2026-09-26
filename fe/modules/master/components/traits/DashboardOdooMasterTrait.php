<?php

namespace app\modules\master\components\traits;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

trait DashboardOdooMasterTrait {

    public function actionProductTemplete($type)
    {
        if (in_array($type, ['tindakan', 'obatalkespasien', 'barang'])) {
            return $this->renderAjax("_{$type}", []);
        }

        throw new \yii\web\NotFoundHttpException();
    }

    public function actionGetDataProductTemplete($tipe)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $model = $request->get('model', null);
        try {
            if($tipe == self::PAKET || $tipe == self::TINDAKAN){
                $urlApi = 'odoo/get-data-tindakan';
            }elseif($tipe == self::OBAT){
                $urlApi = 'odoo/get-data-obat';
            }elseif($tipe == self::BARANG){
                $urlApi = 'odoo/get-data-barang';
            }else{
                throw new \Exception("Error Processing Request", 1);
            }
            $response = $this->_restMaster->get($urlApi, [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['sync_id_api']);
                    $value['primary'] = $primaryKey;
                    $value['select_item'] = Html::checkbox('select_item', false, [
                        'id' => 'select_item-'.$value['sync_id_api'],
                        'class' => 'select_item',
                        'value' => $value['sync_id_api'],
                    ]);
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/dashboard-odoo/detail-product-templete?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);
                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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

    public function actionDetailProductTemplete($id)
    {
        $syncId = DocoHelpers::decrypt($id);
        $detailId = $this->setIdRekap($syncId);
        switch ($detailId['tipe']) {
            case self::PAKET :
            case self::TINDAKAN :
                    $rest = $this->_restMaster->get('odoo/get-detail-product', [
                        'query' => [
                            'id' => $syncId
                        ]
                    ]);
                    $body = json_decode($rest->getBody(), true);
                    $restAdditional = isset($body['response']['data']['additional_data'])
                                    ? json_decode($body['response']['data']['additional_data'], true) : [];
                    $response = json_encode([
                        'payload' => isset($restAdditional['payload']) ? $restAdditional['payload'] : [],
                        'response' => isset($restAdditional['response']) ? $restAdditional['response'] : []
                    ]);
                break;
            case self::OBAT :
                    $rest = $this->_restMaster->get('odoo/get-detail-obat', [
                        'query' => [
                            'id' => $syncId
                        ]
                    ]);
                    $body = json_decode($rest->getBody(), true);
                    $restAdditional = isset($body['response']['data']['additional_data']) 
                                    ? json_decode($body['response']['data']['additional_data'], true) : [];
                    $response = json_encode([
                        'payload' => isset($restAdditional['payload']) ? $restAdditional['payload'] : [],
                        'response' => isset($restAdditional['response']) ? $restAdditional['response'] : []
                    ]);
                break;
            case self::BARANG :
                    $rest = $this->_restMaster->get('odoo/get-detail-barang', [
                        'query' => [
                            'id' => $syncId
                        ]
                    ]);
                    $body = json_decode($rest->getBody(), true);
                    $restAdditional = isset($body['response']['data']['additional_data']) 
                                    ? json_decode($body['response']['data']['additional_data'], true) : [];
                    $response = json_encode([
                        'payload' => isset($restAdditional['payload']) ? $restAdditional['payload'] : [],
                        'response' => isset($restAdditional['response']) ? $restAdditional['response'] : []
                    ]);
                break;
            default:
                $response = '<center><h3><strong>Tidak Ada Response</strong><h3></center>';
                break;
        }
        return $this->renderAjax('_detail', get_defined_vars());
    }

    public function actionResendProductTemplete()
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $listSync = $request->post('sync_id_api');
            if (is_array($listSync)) {
                $listPaket = $listTindakan = $listObat = $listBarang = [];
                foreach ($listSync as $value) {
                    $parseVal = $this->setIdRekap($value);
                    if ($parseVal['tipe'] == self::PAKET) {
                        $listPaket[] = $parseVal['id'];
                    } else if ($parseVal['tipe'] == self::TINDAKAN) {
                        $listTindakan[] = $parseVal['id'];
                    } else if ($parseVal['tipe'] == self::OBAT) {
                        $listObat[] = $parseVal['id'];
                    } else if ($parseVal['tipe'] == self::BARANG) {
                        $listBarang[] = $parseVal['id'];
                    }
                }

                if (!empty($listTindakan)) {
                    $response = $this->_restMaster->post('odoo/sync-tindakan', [
                        'form_params' => [
                            'id' => $listTindakan
                        ]
                    ]);
                }

                if (!empty($listPaket)) {
                    $response = $this->_restMaster->post('odoo/sync-paket', [
                        'form_params' => [
                            'id' => $listPaket
                        ]
                    ]);
                }

                if (!empty($listObat)) {
                    $response = $this->_restMaster->post('odoo/sync-obat', [
                        'form_params' => [
                            'id' => $listObat
                        ]
                    ]);
                }

                if (!empty($listBarang)) {
                    $response = $this->_restMaster->post('odoo/sync-barang', [
                        'form_params' => [
                            'id' => $listBarang
                        ]
                    ]);
                }

                return DocoHelpers::response([
                    'text' => 'Proses Berhasil !',
                    'message' => 'Data berhasil dikirim ulang.'
                ]);
            }
        }
    }

    private function setIdRekap($value)
    {
        preg_match('/^(\D+)(\d+)$/', $value, $match);
        array_shift($match);
        $tipe = isset($match[0]) ? $match[0] : null;
        $id = isset($match[1]) ? $match[1] : null;
        return compact('tipe', 'id');
    }

    public function actionMasterGetPartner()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get("odoo/get-partner?".http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['sync_id_api']);
                    $value['primary'] = $primaryKey;
                    $disabled = (empty($value['sync_id_api'])) ? false : true;
                    $value['select_item'] = Html::checkbox('select_item', false, [
                        'id' => 'select_item-'.$value['sync_id_api'],
                        'class' => 'select_item',
                        'value' => $value['sync_id_api'],
                    ]);
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/dashboard-odoo/master-detail-response?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);
                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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

    public function actionMasterDetailResponse($id) {
        $rest = $this->_restMaster->get('odoo/get-detail-partner', [
            'query' => [
                'id' => DocoHelpers::decrypt($id)
            ]
        ]);

        $body = json_decode($rest->getBody(), true);
        $restAdditional = isset($body['response']['data']['additional_data'])
                        ? json_decode($body['response']['data']['additional_data'], true) : [];
        $response = json_encode([
            'payload' => isset($restAdditional['payload']) ? $restAdditional['payload'] : [],
            'response' => isset($restAdditional['response']) ? $restAdditional['response'] : []
        ]);

        return $this->renderAjax('_detail', get_defined_vars());
    }

    public function actionResendPartner() {
        $request = Yii::$app->request;
        if ($request->post()) {
            $listSync = $request->post('sync_id_api');
            if (is_array($listSync)) {
                if (!empty($listSync)) {
                    $response = $this->_restMaster->post('odoo/sync-partner', [
                        'form_params' => [
                            'id' => $listSync
                        ]
                    ]);
                }

                return DocoHelpers::response([
                    'text' => 'Proses Berhasil !',
                    'message' => 'Data berhasil dikirim ulang.'
                ]);
            }
        }
    }

    public function actionMasterGetQuery() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $reader = isset($_GET["reader"]) ? $_GET["reader"] : null;
        $allow = [
            // key => primary
            'uom' => 'id',
            'ruangan' => 'sync_id_api'
        ];
        $result = [];
        $data = $cache = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        if (!array_key_exists($reader, $allow)) {
            $result['error'] = 'Not allowed type';
            return $result;
        }

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $response = $this->_restMaster->get("odoo/get-".$reader."?".http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value[$allow[$reader]]);
                    $value['primary'] = $primaryKey;
                    $disabled = (empty($value[$allow[$reader]])) ? false : true;
                    $value['select_item'] = Html::checkbox('select_item', false, [
                        'id' => 'select_item-'.$value[$allow[$reader]],
                        'class' => 'select_item',
                        'value' => $value[$allow[$reader]],
                    ]);
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/dashboard-odoo/master-detail-query?reader=".$reader."&id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);
                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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

    public function actionMasterDetailQuery($id, $reader = null) {
        $allow = ['uom', 'ruangan'];
        if (in_array($reader, $allow)) {

            $rest = $this->_restMaster->get('odoo/get-detail-' . $reader, [
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ]);

            $body = json_decode($rest->getBody(), true);
            if (isset($body['response']['data']['additional_data'])) {
                $restAdditional = isset($body['response']['data']['additional_data'])
                                ? json_decode($body['response']['data']['additional_data'], true) : [];
                $response = json_encode([
                    'payload' => isset($restAdditional['payload']) ? $restAdditional['payload'] : [],
                    'response' => isset($restAdditional['response']) ? $restAdditional['response'] : []
                ]);
            } else {
                $response = isset($body['response']['data']['sync_respon']) ? $body['response']['data']['sync_respon'] : json_encode($body['response']['data']);
            }
        } else {
            $response = '{}';
        }

        return $this->renderAjax('_detail', get_defined_vars());
    }

    public function actionResendQuery($reader = null) {
        $allow = [
            // Type => endpoint
            'uom' => 'satuan-barang/resend',
            'ruangan' => 'odoo/sync-ruangan'
        ];

        if (array_key_exists($reader, $allow)) {
            $request = Yii::$app->request;
            if ($request->post()) {
                $listSync = $request->post('sync_id_api');
                if (is_array($listSync)) {
                    if (!empty($listSync)) {
                        $response = $this->_restMaster->post($allow[$reader], [
                            'form_params' => [
                                'id' => $listSync
                            ]
                        ]);
                    }

                    return DocoHelpers::response([
                        'text' => 'Proses Berhasil !',
                        'message' => 'Data berhasil dikirim ulang.'
                    ]);
                }
            }
        } else {
            return DocoHelpers::response([
                'text' => 'Proses Gagal !',
                'message' => 'Data Gagal dikirim ulang.'
            ], 422);
        }
    }

}