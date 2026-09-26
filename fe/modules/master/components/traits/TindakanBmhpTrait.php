<?php

/**
 * @Author: Wahyu Saepuloh
 * @Date:   5 November 2019
 */

namespace app\modules\master\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;
use yii\db\Query;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\master\models\TindakanBmhpForm;
use app\modules\master\models\InfoTindakanBmhpView;

trait TindakanBmhpTrait
{
    
    public function actionTindakanBmhp()
    {
        $title = Yii::t('fe', 'Master Mapping Tindakan dan BMHP');
        return $this->renderAjax('components/tindakan-bmhp/index', get_defined_vars());
    }

    public function actionGetDataTindakanBmhp()
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
            $response = $this->_restMaster->get('tindakan-bmhp/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // return DocoHelpers::response($body);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
                $value['primary'] = $primaryKey;
                $value['daftartindakan_nama'] = isset($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : null;
                $value['daftartindakan_id'] = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                $value['aksi'] = Html::button("<i class='fa fa-trash'></i>", [
                    'class' => 'btn btn-ls btn-danger', 
                    // 'data-source'=>"/master/tindakan/detail-tindakan-kategori?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)'
                    ]);
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 
                    // 'data-source'=>"/master/tindakan/detail-tindakan-kategori?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)'
                    'data-source'=> "/master/tindakan/detail-tindakan-bmhp?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)'
                    ]);
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

    // get data detail tindakan bmhp
    public function actionGetDataDetailTindakanBmhp($id = null)
    {
        // $id = DocoHelpers::decrypt($id);
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
            $response = $this->_restMaster->get('tindakan-bmhp/detail-tindakan-bmhp?daftartindakan_id='.$id, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $_detail = !empty($body['response']['data']) ? $body['response']['data'] : [];
            $tmp_detail = [];
            if(!empty($_detail)){
                foreach($_detail as $val){
                    $detail = !empty($val['detail_bmhp']) ? $val['detail_bmhp'] : [];
                    $tmp_detail = array_merge($detail, $tmp_detail);
                }
            }
            $no = $request->get('start', 1);
            // cek apakah datanya kosong
            if ($tmp_detail !== null) {
                foreach ($tmp_detail as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
                    $value['primary'] = $primaryKey;
                    $value['rowNum'] = $no;
                    // $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    //     'class' => 'btn btn-sm', 'data-source' => "/master/detail-paket?id=" . $primaryKey, 'onclick' => 'docoHelper.detail(this)'
                    // ]);
                    $data[$key] = $value;
                }
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

    // get detail tindakan bmhp
    public function actionDetailTindakanBmhp($id = null)
    {
        $title = Yii::t('fe', 'Detail Mapping Tindakan BMHP');
        $id = DocoHelpers::decrypt($id);
        $id_encrypt = $id;
        return $this->renderAjax('components/tindakan-bmhp/detail', get_defined_vars());
    }

    /**
     * @todo Fungsi untuk cari data tindakan
     * @author Wahyu
     */
    public function actionSearchTindakan()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-tindakan',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['daftartindakan_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    /**
     *
     * get data tindakan infinity scroll
     *
     */
    public function actionListTindakan()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page-1)*5;
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $result = $this->_restMaster->get('allow/list-tindakan-infinity',[
                'query' => [
                    'term' => $request->get('q'),
                    'page'=>$page,
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                            'id'=>$value['daftartindakan_id'],
                            'text'=>$value['daftartindakan_nama'], 
                            'datavalue'=>$value 
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

    /**
     * @todo Fungsi untuk cari data obat alkes
     * @author Wahyu
     */
    public function actionSearchObatAlkes()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-obat-alkes',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionListObatAlkes()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page-1)*5;
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $result = $this->_restMaster->get('allow/list-obat-alkes-infinity',[
                'query' => [
                    'term' => $request->get('q'),
                    'page'=>$page,
                    'group'=>$request->get('group', null),
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                            'id'=>$value['obatalkes_id'],
                            'text'=>$value['obatalkes_nama'], 
                            'datavalue'=>$value 
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

    // fungsi get list satuan besar dengan referensi dari obat alkes dengan depdrop
    public function actionListSatuanBesar()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $obatalkes_id = $post['depdrop_parents'][0];
        try {
            $request = $this->_restMaster->get('allow/list-satuan-besar?obatalkes_id='. $obatalkes_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            $out = [];
            $val_satuan = $response['0']['satuankecil_id'];
            foreach($response as $key => $value) {
                $out[] = [
                        'satuankonversi_id' => $value['satuankonversi_id'],
                        'id' => $value['satuanbesar_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'satuanbesar_id' => $value['satuanbesar_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'satuan_besar' => $value['satuan_besar'],
                        'satuan_kecil' => $value['satuan_kecil'],
                        'name' => $value['satuan_besar'],
                        'nilai_konversi' => $value['nilai_konversi'],
                    ];
            }
            return json_encode(['output'=>$out, 'selected'=>$val_satuan]);
        } catch (\Exception $e) {
            return json_encode(['output'=>[], 'selected'=>'']);;
        }
    }

    // fungsi get nilai konversi dari satuan yang dipilih
    public function actionGetNilaiKonversi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $satuanbesar_id = $get['satuanbesar_id'];
        $obatalkes_id = $get['obatalkes_id'];
        try {
            $request = $this->_restMaster->get('allow/get-konversi?obatalkes_id='. $obatalkes_id.'&satuanbesar_id='. $satuanbesar_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @todo Fungsi untuk create Mapping Tindakan BMHP
     * @author 
     */
    public function actionCreateTindakanBmhp()
    {
        // try {
            $title = Yii::t('fe', 'Tambah Mapping Tindakan dan BMHP / Alkes');
            $model = new TindakanBmhpForm;
            $request = Yii::$app->request;
            $post = Yii::$app->request->post();
            $model->data = $request->post('data');
            $model->daftartindakan_id = $request->post('tindakan');
            $model->scenario = 'create';
            if ($post) {
                $model->load($post);

                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'tindakan-bmhp/create', [
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
                $getAttr = $this->getAttributes();

                $group_jenisobat = !empty($getAttr['group_jenisobat']) ? $getAttr['group_jenisobat'] : [];
                $action = '/master/tindakan/create-tindakan-bmhp';
                return $this->renderAjax('components/tindakan-bmhp/create', get_defined_vars());
            }
        // } catch (Exception $e) {
        //     $response = [ 
        //         'response' => [
        //             'message'=>$e->getMessage(),
        //         ]
        //     ];
        //     return DocoHelpers::response($response, 500);
        // } catch (RequestException $e) {
        //     $response = [ 
        //         'response' => [
        //             'message'=>$e->getMessage(),
        //         ]
        //     ];
        //     return DocoHelpers::response($response, 500);
        // }
    }

    /**
     * @todo Fungsi untuk update 
     * @author 
     */
    public function actionUpdateTindakanBmhp($id)
    // public function actionUpdateTindakanBmhp()
    {
        $title = 'Ubah Mapping Tindakan dan BMHP / Alkes';
        try {
            // $id = DocoHelpers::decrypt($id);
            $model = new TindakanBmhpForm;
            $request = Yii::$app->request;
            $post = $request->post();
            $model->data = $request->post('data');
            $model->daftartindakan_id = $request->post('tindakan');
            $model->scenario = 'update';
            if ($post) {
                // melakukan delete data terlebih dahulu
                // $this->_restMaster->request('POST', 'tindakan-bmhp/delete?id='.$id);
                // melakukan insert ulang data update terbaru
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'tindakan-bmhp/update?id='.$id, [
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
                $request = $this->_restMaster->request('GET', 'tindakan-bmhp/view?id='.$id);
                $response = json_decode($request->getBody(), true);
                $attributes = !empty($response['response']['data']) ? $response['response']['data'] : [];
                $getAttr = $this->getAttributes();
                $group_jenisobat = !empty($getAttr['group_jenisobat']) ? $getAttr['group_jenisobat'] : [];

                $detail_data = $tmp_detail = [];
                if(!empty($attributes)){
                    foreach($attributes as $val){
                        $nama_tindakan = ArrayHelper::getValue($val, 'daftartindakan_nama', null);
                        $id_tindakan = ArrayHelper::getValue($val, 'daftartindakan_id', null);
                        $detail = !empty($val['detail_bmhp']) ?  json_decode($val['detail_bmhp'], true) : [];
                        $tmp_detail = array_merge($detail, $tmp_detail);
                    }
                }
                if (is_array($tmp_detail)) {
                    foreach ($tmp_detail as $key => $value) {
                        $row = [
                            'obatalkes_id' => isset($value['obatalkes_id']) ? strval($value['obatalkes_id']) : null,
                            'label_obat' => ArrayHelper::getValue($value, 'obatalkes_nama', null),
                            'satuanunit_id' => isset($value['satuanunit_id']) ? strval($value['satuanunit_id']) : null,
                            'label_satuan' => ArrayHelper::getValue($value, 'satuaninput_nama', null),
                            'qty_input' => isset($value['qty_input']) ? number_format((float)$value['qty_input'], 2, '.', '') : 0,
                            'qty_konversi' => isset($value['qty_konversi']) ? number_format((float)$value['qty_konversi'], 2, '.', '') : 0,
                            'satuaninput_id' => isset($value['satuaninput_id']) ? strval($value['satuaninput_id']) : '',
                            'satuanunit_nama' => ArrayHelper::getValue($value, 'satuanunit_nama', null),
                            'nilai_konversi' => ArrayHelper::getValue($value, 'nilai_konversi', null),
                            'daftartindakan_id' => isset($value['daftartindakan_id']) ? strval($value['daftartindakan_id']) : null,
                            'label_group' => !empty($value['nama_grup']) ? $value['nama_grup'] : '',
                        ];
                        $detail_data[] = $row;
                        
                    }
                }
                $detail_data = json_encode($detail_data);

                $action = '/master/tindakan/update-tindakan-bmhp?id='.$id;
                return $this->renderAjax('components/tindakan-bmhp/update', get_defined_vars());
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

    /**
     * @todo Fungsi untuk hapus 
     * @author 
     */
    public function actionDeleteTindakanBmhp($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $restMaster = $this->_restMaster->POST('tindakan-bmhp/delete?id='.$id);
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

    // /**
    //  * @todo Fungsi untuk melakukan pengecekan 
    //  * @author 
    //  */
    // public function actionCekTransaksiKegiatan()
    // {
    //     try {
    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         $request = Yii::$app->request;
    //         $id = DocoHelpers::decrypt($request->get('id'));
    //         $response = false;

    //         $restMaster = $this->_restMaster->get('jenis-kegiatan-tindakan/cek-transaksi-kegiatan', [
    //             'query' => ['id' => $id]
    //         ]);
    //         $body = json_decode($restMaster->getBody(), true);
    //         $count = $body['response'];

    //         if ($count > 0) {
    //             $response = true;
    //         }
            
    //         return DocoHelpers::response($response);
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    // /**
    //  * @todo Fungsi untuk melakukan cetak pdf
    //  * @author 
    //  */
    // public function actionExportPdfKegiatan()
    // {
    //     try {
    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         $request = Yii::$app->request;
        
    //         $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
    //         $path = Yii::getAlias("@download") . "/Master Kegiatan.pdf";
    //         $response = $this->_restMaster->get('jenis-kegiatan-tindakan/export-pdf?'.http_build_query($yiiRestfulParams),[
    //             'save_to' => $path
    //         ]);

    //         return DocoHelpers::previewPdf($path);
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return DocoHelpers::response($result);
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return DocoHelpers::response($result);
    //     }
    // }

    // /**
    //  * @todo Fungsi untuk melakukan export excel
    //  * @author 
    //  */
    // public function actionExportExcelKegiatan()
    // {
    //     try {
    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         $request = Yii::$app->request;
    //         $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
    //         $url = 'jenis-kegiatan-tindakan/export-excel?'.http_build_query($yiiRestfulParams);
    //         $path = Yii::getAlias("@download") . "/Master Kegiatan.xlsx";
            
    //         $restMaster = $this->_restMaster->get($url,[
    //             'save_to' => $path,
    //         ]);

    //         return DocoHelpers::downloadFile($path, true);
    //     } catch (RequestException $e) {
    //         var_dump($e->getMessage());exit();
    //         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
    //     } catch (\Exception $e) {
    //         var_dump($e->getMessage());exit();
    //         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
    //     }
    // }

    // /**
    //  * @todo Fungsi untuk melihat detail kegiatan
    //  * @author 
    //  */
    // public function actionDetailKegiatan($id = null)
    // {
    //     try {
    //         $id_encrypt = $id;
    //         $id = DocoHelpers::decrypt($id);

    //         return $this->renderAjax('components/kegiatan/detail', get_defined_vars());
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return DocoHelpers::response($result);
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return DocoHelpers::response($result);
    //     }
    // }

    // /**
    //  * @todo Fungsi untuk mendapatkan data detail kegiatan
    //  * @author 
    //  */
    // public function actionGetDataDetailKegiatan($id = null)
    // {
    //     try {
    //         $id = DocoHelpers::decrypt($id);
    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         $request = Yii::$app->request;
    //         $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
    //         $draw = $request->get('draw', 1);
    //         $data = [];

    //         $result = [];
    //         $result['data'] = $data;
    //         $result['draw'] = $draw;
    //         $result['recordsTotal'] = 0;
    //         $result['recordsTotal'] = 0;
            
    //         $response = $this->_restMaster->get('jenis-kegiatan-tindakan/get-data-detail-kegiatan?id='.$id.'&'.http_build_query($yiiRestfulParams));
    //         $body = json_decode($response->getBody(), true);
    //         $no = $request->get('start', 1);
    //         foreach ($body['response']['data'] as $key => $value) {
    //             $no++;
    //             $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
    //             $value['primary'] = $primaryKey;
    //             $value['rowNum'] = $no;
    //             $data[$key] = $value;
    //         }

    //         $result['data'] = $data;
    //         $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
    //         $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

    //         return $result;
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    public function getAttributes()
    {
        try {
            $response = $this->_restMaster->get('jenis-obat-alkes/get-attributes',[]);
            $getResponse = json_decode($response->getBody(), true);
            $res = $getResponse['response'];
            $result = [
                'group_jenisobat' => ArrayHelper::map($res['group_jenisobat'], 'lookup_id', 'lookup_name'),
                'service_group' => $res['service_group'],
                'service_category' => $res['service_category']
            ];
            return $result;
        } catch (Exception $e) {
            $result = [
                'group_jenisobat' => [],
                'service_group' => [],
                'service_category' => []
            ];
        }
    }
}
