<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kategori
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

use app\modules\master\models\JenisKegiatanForm;

trait KegiatanTrait
{
    
    public function actionKegiatan()
    {
        $title = Yii::t('fe', 'Master kegiatan');
        return $this->renderAjax('components/kegiatan/index', get_defined_vars());
    }

    public function actionGetDataKegiatan()
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
            $response = $this->_restMaster->get('jenis-kegiatan-tindakan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['jeniskegiatantindakan_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success',
                    'data-source' => "/master/tindakan/detail-kegiatan?id=".$primaryKey,
                    'onclick' => 'docoHelper.detail(this)'
                ]);
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

    /**
     * @todo Fungsi untuk create kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCreateKegiatan()
    {
        try {
            $title = Yii::t('fe', 'Tambah Kegiatan');
            $model = new JenisKegiatanForm;
            $request = Yii::$app->request;
            $post = $request->post('JenisKegiatanForm');

            if ($post) {
                $model->load($post, '');

                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'jenis-kegiatan-tindakan/create', [
                        'form_params' => $model,
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                } else {
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } else {
                $model->is_active = 1;
                $action = '/master/tindakan/create-kegiatan';
                return $this->renderAjax('components/kegiatan/form', get_defined_vars());
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
     * @todo Fungsi untuk update kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUpdateKegiatan($id)
    {
        try {
            $title = Yii::t('fe', 'Ubah Kegiatan');
            $model = new JenisKegiatanForm;
            $request = Yii::$app->request;
            $post = $request->post('JenisKegiatanForm');
            $decryptedId = DocoHelpers::decrypt($id);

            if ($post) {
                $model->load($post, '');

                if ($model->validate()) {
                    $restMaster = $this->_restMaster->request('POST', 'jenis-kegiatan-tindakan/update?id='.$decryptedId, [
                        'form_params' => $model,
                    ]);
                    $response = json_decode($restMaster->getBody(), true);
                    return DocoHelpers::response($response);
                } else {
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } else {
                $restMaster = $this->_restMaster->request('POST', 'jenis-kegiatan-tindakan/view?id='.$decryptedId);
                $response = json_decode($restMaster->getBody(), true);
                $data = $response['response'];
                $model->load($data, '');

                $action = '/master/tindakan/update-kegiatan?id='.$id;
                return $this->renderAjax('components/kegiatan/form', get_defined_vars());
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
     * @todo Fungsi untuk hapus kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionDeleteKegiatan($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $restMaster = $this->_restMaster->POST('jenis-kegiatan-tindakan/delete?id='.$id);
            $response = json_decode($restMaster->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk melakukan pengecekan transaksi kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCekTransaksiKegiatan()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($request->get('id'));
            $response = false;

            $restMaster = $this->_restMaster->get('jenis-kegiatan-tindakan/cek-transaksi-kegiatan', [
                'query' => ['id' => $id]
            ]);
            $body = json_decode($restMaster->getBody(), true);
            $count = $body['response'];

            if ($count > 0) {
                $response = true;
            }
            
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @todo Fungsi untuk melakukan cetak pdf
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdfKegiatan()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
        
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/Master Kegiatan.pdf";
            $response = $this->_restMaster->get('jenis-kegiatan-tindakan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    /**
     * @todo Fungsi untuk melakukan export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcelKegiatan()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $url = 'jenis-kegiatan-tindakan/export-excel?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/Master Kegiatan.xlsx";
            
            $restMaster = $this->_restMaster->get($url,[
                'save_to' => $path,
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @todo Fungsi untuk melihat detail kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionDetailKegiatan($id = null)
    {
        try {
            $id_encrypt = $id;
            $id = DocoHelpers::decrypt($id);

            return $this->renderAjax('components/kegiatan/detail', get_defined_vars());
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data detail kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataDetailKegiatan($id = null)
    {
        try {
            $id = DocoHelpers::decrypt($id);
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
            
            $response = $this->_restMaster->get('jenis-kegiatan-tindakan/get-data-detail-kegiatan?id='.$id.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
                $value['primary'] = $primaryKey;
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
}
