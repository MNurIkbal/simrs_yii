<?php 

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

// Namespace
namespace Doco\master\controllers;

// Using Yii
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

// Using Guzzles
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoSelect2Trait;

// Using model
use Doco\master\models\PaketMcuForm;

use app\modules\v1\cache\Cache;

class PaketMcuController extends DocoController
{
    use DocoSelect2Trait;

    protected $_title = "Paket MCU";
    protected $_module = '/master/PaketMcuForm';
    protected $_restMaster;

    // allow sequa blok
    protected $allowAction = [ '*' ];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $status[0] = Yii::t('fe', 'Tidak Aktif');
        $status[1] = Yii::t('fe', 'Aktif');

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restMaster->request('get', 'paket-mcu/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['tipepaket_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                if ($value['is_active']) {
                    $value['is_active'] = "Aktif";
                }else{
                    $value['is_active'] = "Tidak Aktif";
                }
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success',
                    'data-source'=> "/master/paket-mcu/detail-paket-mcu?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)'
                    ]);
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataDetailPaketMcu($id = null)
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
            $response = $this->_restMaster->get('paket-mcu/detail-paket-mcu?tipepaket_id='.$id, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $_detail = $body['response']['data'];
            $tmp_detail = $_detail[0]['detail'];
            $no = $request->get('start', 1);
            // cek apakah datanya kosong
            if ($tmp_detail !== null) {
                foreach ($tmp_detail as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['paketdetail_id']);
                    $value['primary'] = $primaryKey;
                    $value['rowNum'] = $no;
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

    public function actionDetailPaketMcu($id = null)
    {
        $title = Yii::t('fe', 'Detail Paket MCU');
        $id = DocoHelpers::decrypt($id);
        $id_encrypt = $id;
        return $this->renderAjax('/paket-mcu/detail', get_defined_vars());
    }

    public function actionCreateMcu()
    {
        $title = Yii::t('fe', 'Tambah Paket MCU');
        $model = new PaketMcuForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->detail = $request->post('detail');
        $model->tipepaket_nama = $request->post('tipepaket_nama');
        $model->tipepaket_kode = $request->post('tipepaket_kode');
        $model->tipepaket_namalainnya = $request->post('tipepaket_namalainnya');
        $model->keterangan_tipepaket = $request->post('keterangan_tipepaket');
        $model->is_mcu = $request->post('is_mcu');
        $model->is_active = $request->post('is_active'); 
        $model->load($post);
        
        if ($post) {
            if ($model->validate()) {
                $response = $this->_restMaster->request('POST', 'paket-mcu/save-mcu',[
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(), true);

                return DocoHelpers::response($response,false,'PaketMcuForm');
            } else {
                $errors = DocoHelpers::parseError($model->errors,'PaketMcuForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        }

        return $this->render('form-mcu', get_defined_vars());
    }

    public function actionGetTindakan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        // $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $response = $this->_restMaster->get('paket-mcu/get-tindakan?ruangan_id=' . $parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value) {
                $result['output'][] = [
                    'id' => $value['daftartindakan_id'],
                    'name' => $value['daftartindakan_kode'].' - '.$value['daftartindakan_nama'],
                    'datavalue' => $value
                ];
            }

            if (count($result['output']) == 1) {
                $result['selected'] = $result['output'][0]["id"];
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

    public function actionGetPaket()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        // $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $response = $this->_restMaster->get('paket-mcu/get-paket?ruangan_id=' . $parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value) {
                $result['output'][] = [
                    'id' => $value['tipepaket_id'],
                    'name' => $value['tipepaket_kode'].' - '.$value['tipepaket_nama'],
                    'datavalue' => $value
                ];
            }

            if (count($result['output']) == 1) {
                $result['selected'] = $result['output'][0]["id"];
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
    
    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/carakeluar.pdf";
        try {
            $response = $this->_restMaster->get('cara-keluar/cetak-pdf?' . http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
            // return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/Master Cara Keluar.xlsx";

        try {
            $response = $this->_restMaster->get('cara-keluar/export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

}
