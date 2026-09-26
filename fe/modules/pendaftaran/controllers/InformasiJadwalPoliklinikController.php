<?php
// Author : Naufal Ziyad L

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\master\models\JadwalPoliklinikForm;
use GuzzleHttp\Exception\RequestException;

class InformasiJadwalPoliklinikController extends DocoController
{
    protected $_title = "Pendaftaran :: Informasi Jadwal Poliknik";
    protected $_module = 'pendaftaran/informasi-jadwal-poliklinik/';
    protected $_restMaster;
    protected $_restPendaftaran;
    const SINGKATAN_RJ = 'RJ';

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        try{
            $status = [
                1 => Yii::t('fe', 'Aktif'),
                0 => Yii::t('fe', 'Tidak Aktif')
            ];
            $model = new JadwalPoliklinikForm;
            $ruangan = [];
            $hari = [];
            $shift = [];

            $ruanganRequest = $this->_restPendaftaran->get('inf-jadwal-buka-poli/get-options',[
                    'query' => [
                        'singkatan' => self::SINGKATAN_RJ
                    ]
                ]);
            $body = json_decode($ruanganRequest->getBody(),true);
            $ruangan = $body['response']['ruangan'];
            $hari = $body['response']['hari'];
            $shift = ArrayHelper::map($body['response']['shift'],'shift_id','shift_nama');
            $konfigKuota = $body['response']['konfig']['kuota_antrian'];
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetDataJadwalPoliklinikOld()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = $this->convertToRestfulParamsPoli($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $result['rowData'] = $data;
        try {
            $response = $this->_restPendaftaran->get('inf-jadwal-buka-poli/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['jadwalbukapoli_id']);
                unset($value['jadwalbukapoli_id']);

                $jam_mulai = strlen($value['jam_mulai']) > 0 ? explode(":",$value['jam_mulai']) : ['00','00'];
                if(count($jam_mulai) > 2){
                    unset($jam_mulai[2]);
                }
                $value['jam_mulai'] = implode(':', $jam_mulai);
                $jam_tutup = strlen($value['jam_tutup']) > 0 ? explode(":",$value['jam_tutup']) : ['00','00'];
                if(count($jam_tutup) > 2){
                    unset($jam_tutup[2]);
                }
                $value['jam_tutup'] = implode(':', $jam_tutup);
                $value['kuota'] = $value['maxantiran_poli'];

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['rowData'] = $data;
            $result['recordsTotal'] = $body['response']['totalCount'];
            $result['countPoli'] = $body['response']['countPoli'];
            // var_dump($result);exit;
            return json_encode($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Lihat');
        $model = new JadwalPoliklinikForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('jadwal-poliklinik/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }


    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Tambah');
        $model = new JadwalPoliklinikForm;
        $status = $this->_status;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $hari = $model::getHari();

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('jadwal-poliklinik/create', [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('ruangan');
            $body = json_decode($response->getBody(), TRUE);
            $ruangan = $body['response']['data'];

            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Ubah Data');
        $model = new JadwalPoliklinikForm;
        $status = $this->_status;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $hari = $model::getHari();
        $ruangan = ['Poliknik Jantung'];

        return $this->renderPartial('form', get_defined_vars());

/*        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('jadwal-poliklinik/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('ruangan');
            $body = json_decode($response->getBody(), TRUE);
            $ruangan = $body['response']['data'];

            $response = $this->_restMaster->get('jadwal-poliklinik/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            return $this->renderPartial('form', get_defined_vars());
        }*/
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('jadwal-poliklinik/delete?id='.$id);
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK", [
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }


    public function actionExport($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        echo 'EXPORT BERHASIL';
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportPdfOld(){
        $request = Yii::$app->request;
        $_POST['col_data'] = urlencode($_POST['col_data']);
        $_POST['col_filter'] = urlencode($_POST['col_filter']);
        $path = Yii::getAlias("@download") . "/informasi-penjadwalan-poliklinik.pdf";
        try {
            $post = $request->post();
            $response = $this->_restPendaftaran
                        ->post('inf-jadwal-buka-poli/print-pdf',
                            [
                                'form_params' => $post,
                                'save_to' => $path
                            ]);
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
        // var_dump(json_decode($e->getResponse()->getBody()));exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // var_dump($e);exit;
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    protected function convertToRestfulParamsPoli($datatableParams)
    {
        $yiiRestfulParams = [];
        $f = [];
        // var_dump($datatableParams);exit;
        if(isset($datatableParams['JadwalPoliklinikForm'])){
            foreach ($datatableParams['JadwalPoliklinikForm'] as $key => $value) {
                $f[$key] = $value;
            }
        }
        $yiiRestfulParams['filters'] = $f;
        return $yiiRestfulParams;
    }

    /**
     * @todo Fungsi untuk mendapatkan data jadwal poliklinik
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataJadwalPoliklinik()
    {
        try {
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

            $restPendaftaran = $this->_restPendaftaran->get('inf-jadwal-buka-poli/get-data-jadwal-poliklinik?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restPendaftaran->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['jadwalbukapoli_id']);
                $value['primary'] = $primaryKey;
                unset($value['jadwalbukapoli_id']);
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
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

    /**
     * @todo Fungsi untuk export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-jadwal-buka-poli.xlsx";
            $response = $this->_restPendaftaran->get('inf-jadwal-buka-poli/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @todo Fungsi untuk export pdf
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/informasi-jadwal-poliklinik.pdf";
        try {
            $response = $this->_restPendaftaran->get('inf-jadwal-buka-poli/cetak-pdf?' . http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
