<?php

/**
 * @Author: Ripan
 * @Date:   19 Mei 2022
 */

namespace app\modules\master\components\traits;

use Yii;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\TindakanLuarBedahForm;

trait TindakanLuarBedahTrait
{
    // ------------ Get Data ------------

    public function actionGetDataTindakanLuarBedah()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request   = Yii::$app->request;
        $resParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result    = [
            'data'         => [],
            'draw'         => $request->get('draw', 1),
            'recordsTotal' => 0
        ];

        try {
            $response = $this->_restMaster->get('tindakan-luar-bedah/index?'.http_build_query($resParams), [
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            if(isset($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $daftartindakan_id   = ArrayHelper::getValue($value, 'daftartindakan_id', 0);
                    $daftartindakan_kode = ArrayHelper::getValue($value, 'daftartindakan_kode', '');
                    $daftartindakan_nama = ArrayHelper::getValue($value, 'daftartindakan_nama', '');
                    $primaryKey          = DocoHelpers::encrypt($daftartindakan_id);
                    
                    $data[$key] = [
                        'rowNum'                   => $key+1,
                        'primary'                  => $primaryKey,
                        'daftartindakan_id'        => $daftartindakan_id,
                        'daftartindakan_kode'      => $daftartindakan_kode,
                        'daftartindakan_nama'      => $daftartindakan_kode.' - '.$daftartindakan_nama,
                        'detail'                   => Html::button("<i class='fa fa-plus-square-o'></i>", [
                            'style'       => "margin-top: -4px; margin-bottom: 4px;",      
                            'class'       => 'btn btn-sm btn-success', 
                            'data-source' => "/master/tindakan/detail-tindakan-luar-bedah?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)'
                        ])
                    ];
                }
    
                $result['data'] = $data;
                $result['recordsTotal'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;
                $result['recordsFiltered'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;   
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    public function actionGetDataDetailTindakanLuarBedah($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $result  = [
            'data'         => [],
            'draw'         => $request->get('draw', 1),
            'recordsTotal' => 0
        ];

        try {
            $response = $this->_restMaster->get('tindakan-luar-bedah/detail-tindakan-luar-bedah?daftartindakan_id='.$id, [
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            if(isset($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $tindakanluarbedah_id   = ArrayHelper::getValue($value, 'tindakanluarbedah_id', 0);
                    $tindakanluarbedah_kode = ArrayHelper::getValue($value, 'tindakanluarbedah_kode', '');
                    $tindakanluarbedah_nama = ArrayHelper::getValue($value, 'tindakanluarbedah_nama', '');
                    $qty = ArrayHelper::getValue($value, 'qty', 0);
                    $ditagihkan = ArrayHelper::getValue($value, 'is_ditagihkan', false);

                    $data[$key] = [
                        'rowNum'                 => $key+1,
                        'tindakanluarbedah_id'   => $tindakanluarbedah_id,
                        'tindakanluarbedah_kode' => $tindakanluarbedah_kode,
                        'tindakanluarbedah_nama' => $tindakanluarbedah_kode .' - '. $tindakanluarbedah_nama,
                        'qty'                    => $qty,
                        'ditagihkan'             => $ditagihkan ? '&#10003;' : '&#10005;'
                    ];
                }

                $result['data'] = $data;
                $result['recordsTotal'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;
                $result['recordsFiltered'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;   
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    // ------------ ./Get Data ------------
    
    public function actionTindakanLuarBedah()
    {
        $title = Yii::t('fe', 'Master Mapping Tindakan Di Luar Bedah');

        return $this->renderAjax('components/tindakan-luar-bedah/index', get_defined_vars());
    }

    public function actionDetailTindakanLuarBedah($id = null)
    {
        $title = Yii::t('fe', 'Detail Mapping Tindakan Di Luar Bedah');
        $decId = DocoHelpers::decrypt($id);
        
        return $this->renderAjax('components/tindakan-luar-bedah/detail', get_defined_vars());
    }

    public function actionCreateTindakanLuarBedah()
    {
        $request = Yii::$app->request;
        $post    = Yii::$app->request->post();

        $title = Yii::t('fe', 'Tambah Mapping Tindakan Di Luar Bedah');

        $model = new TindakanLuarBedahForm;
        
        if ($post) {
            $model->data = $request->post('data');
            $model->daftartindakan_id = $request->post('tindakan');
            $model->scenario = 'create';

            $model->load($post);

            if ($model->validate()) {
                $response = $this->_restMaster->request('POST', 'tindakan-luar-bedah/create', [
                    'form_params' => $post,
                ]);
                $response = json_decode($response->getBody(), true);

                return DocoHelpers::response($response);
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;

                return DocoHelpers::response($response, 422, $formName);
            }
        } else {
            $data_detail = json_encode([]);
            $url = "/master/tindakan/create-tindakan-luar-bedah";

            return $this->renderAjax('components/tindakan-luar-bedah/form', get_defined_vars());
        }
    }

    public function actionUpdateTindakanLuarBedah($id)
    {
        $title = 'Ubah Mapping Tindakan Di Luar Bedah';
        
        try {
            $request = Yii::$app->request;
            $post    = Yii::$app->request->post();

            $model = new TindakanLuarBedahForm;
        
            if ($post) {
                $model->data = $request->post('data');
                $model->daftartindakan_id = $request->post('tindakan');
                $model->scenario = 'update';

                $model->load($post);

                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'tindakan-luar-bedah/update?id='.$id, [
                        'form_params' => $post,
                    ]);
                    $response = json_decode($response->getBody(), true);

                    return DocoHelpers::response($response);
                } else {
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $response = $model->errors;

                    return DocoHelpers::response($response, 422, $formName);
                }
            } else {
                $id = DocoHelpers::decrypt($id);
                $request = $this->_restMaster->request('GET', 'tindakan-luar-bedah/view?id='.$id);
                $response = json_decode($request->getBody(), true);
                $attributes = ArrayHelper::getValue($response, 'response', []);

                $tindakan = ArrayHelper::getValue($attributes, 'tindakan', []);
                $tindakan_id = ArrayHelper::getValue($tindakan, 'daftartindakan_id', 0);
                $tindakan_nama = ArrayHelper::getValue($tindakan, 'daftartindakan_kode', '') .' - '. ArrayHelper::getValue($tindakan, 'daftartindakan_nama', '');

                $get_tindakan_luar_bedah = ArrayHelper::getValue($attributes, 'data', []);
                $set_tindakan_luar_bedah = [];
                foreach ($get_tindakan_luar_bedah as $value) {
                    $set_tindakan_luar_bedah[] = [
                        'tindakanluarbedah_id'   => ArrayHelper::getValue($value, 'tindakanluarbedah_id', 0),
                        'tindakanluarbedah_nama' => ArrayHelper::getValue($value, 'tindakanluarbedah_kode', 0) .' - '. ArrayHelper::getValue($value, 'tindakanluarbedah_nama', 0), 
                        'qty'                    => ArrayHelper::getValue($value, 'qty', 0),
                        'is_ditagihkan'          => ArrayHelper::getValue($value, 'is_ditagihkan', 0),
                    ];
                }

                $data_detail = json_encode($set_tindakan_luar_bedah);
                $url = "/master/tindakan/update-tindakan-luar-bedah?id=".$id;

                return $this->renderAjax('components/tindakan-luar-bedah/form', get_defined_vars());
            }
        } catch (Exception $e) {
            $response = [
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        } catch (RequestException $e) {
            $response = [
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        }
    }

    public function actionDeleteTindakanLuarBedah($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $restMaster = $this->_restMaster->POST('tindakan-luar-bedah/delete?id='.$id);
            $response = json_decode($restMaster->getBody(), true);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }  

    // ------------ ./ ------------
    
    public function actionListTindakanLuarBedah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page-1)*5;
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $result = $this->_restMaster->get('tindakan-luar-bedah/list-tindakan-luar-bedah',[
                'query' => [
                    'term' => $request->get('q'),
                    'page'=> $page,
                    'offset'=> $offset,
                    'limit'=> $limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                            'id'        => $value['daftartindakan_id'],
                            'text'      => $value['daftartindakan_kode'].' - '.$value['daftartindakan_nama'], 
                            'datavalue' => $value 
                        ];
            }
        } catch (RequestException $e) {
            $response['message'] = $e->getMessage();
        }
        return DocoHelpers::response([
            'result' => $response,
            'pagination' => [ 'more' => !empty($data)?true:false ]
        ]);
    }
}
