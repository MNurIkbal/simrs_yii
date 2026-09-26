<?php

namespace Doco\radiologi\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\laboratorium\models\InfoOrderanLabView;
use app\modules\radiologi\models\PasienMasukPenunjangForm;
use app\modules\laboratorium\models\BatalOrderPenunjangForm;
use Doco\radiologi\models\InfoOrderanRadV;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\Traits\TindakanPenunjangTrait;

class InfPasienRujukanRadController extends DocoController
{
    use TindakanPenunjangTrait;
    protected $_restRad;
    protected $backendUrl;
    protected $serviceRest;

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi;
        $this->backendUrl = 'inf-pasien-rujukan-rad';
        $this->serviceRest = Yii::$app->docoRest->radiologi;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    // Action index
    public function actionIndex()
    {
        $rujukan = $penjamin = $cara_bayar = $status = $options = [];

        $status[0] = array(
            'kode' => 470,
            'status_nama' => 'BELUM DISETUJUI'
        );
        $status[1] = array(
            'kode' => 471,
            'status_nama' => 'SUDAH DISETUJUI'
        );
        $status[2] = array(
            'kode' => 472,
            'status_nama' => 'BATAL'
        );

        $status = ArrayHelper::map($status, 'status_nama', 'status_nama');

        try {
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/get-options');
            $body = json_decode($response->getBody(), true);
            $cara_bayar = $body['response']['cara_bayar'];
            $penjamin = $body['response']['penjamin'];
            $rujukan = $body['response']['rujukan'];
            $asalrujukan = $body['response']['asalRujukan'];

            $rujukan = ArrayHelper::map($rujukan, 'ruangan_nama', 'ruangan_nama');
            $asalrujukan = ArrayHelper::map($asalrujukan, 'asalrujukan_nama', 'asalrujukan_nama');
            $arrAsalRujukan = array_merge($rujukan,$asalrujukan);

        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }

        $instalasiId = DocoHelpers::encrypt(DocoConstants::INSTALASI_ID_RAD);
        return $this->render('index', get_defined_vars());
    }

    public function actionFormAproval($id)
    {
        $id = DocoHelpers::decrypt($id);
        $model = new PasienMasukPenunjangForm;
        $detail = $pemeriksaan = [];
        $res = $this->guzzleExec($this->_restRad, [
            'url' => 'inf-pasien-rujukan-rad/generate-api',
            'payload' => [
                'query' => [
                    'id' => $id,
                ]
            ]
        ]);
        $detail = ArrayHelper::getValue($res, 'labDetail', []);
		$pemeriksaan = ArrayHelper::getValue($res, 'listPemeriksaan', []);
        $dokterRujukId = ArrayHelper::getValue($detail, 'pegawai_id');
		$dokterPerujuk = ArrayHelper::getValue($detail, 'dokter_perujuk');
        $pendaftaranId = !empty($detail) ? DocoHelpers::encrypt($detail['pendaftaran_id']) : null;
        $penjaminId = !empty($detail) ? DocoHelpers::encrypt($detail['penjamin_id']) : null;
        $kelasPelayananId = !empty($detail) ? DocoHelpers::encrypt($detail['kelaspelayanan_id']) : null;
        $model->catatan_dokterpengirim = $detail['catatan_dokterpengirim'];

        return $this->render('form_approval', get_defined_vars());
    }

    public function actionApprove()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = new PasienMasukPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $formData = $request->post();
                $response = $this->_restRad->post('inf-pasien-rujukan-rad/proses-approve', [
                    'form_params' => $formData,
                    'query' => [
                        'pasienkirimkeunitlain_id' => $id,
                    ]
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response, false, $formName);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionFormBatal($id)
    {
        $id = DocoHelpers::decrypt($id);
        $model = new BatalOrderPenunjangForm;
        $detail = $pemeriksaan = [];
        try {
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/generate-api?id=' . $id);
            $body = json_decode($response->getBody(), true);

            $data = $body['response']['labDetail'];
            $detail = $body['response']['labDetail'];
            $pemeriksaan = $body['response']['listPemeriksaan'];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }

        return $this->render('form_batal', get_defined_vars());
    }

    public function actionBatalOrder()
    {
        $request = Yii::$app->request;
        $model = new BatalOrderPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {

                $formData = $request->post();
                $form_params['pasienkirimkeunitlain_id'] = DocoHelpers::decrypt($formData['pasienkirimkeunitlain_id']);
                $form_params['disetujui_oleh'] = $formData['BatalOrderPenunjangForm']['peg_menyetujui_id'];
                $form_params['tanggal_batal'] = $formData['BatalOrderPenunjangForm']['tgl_batalorder'];
                $form_params['alasan_pembatalan'] = $formData['BatalOrderPenunjangForm']['alasan'];
                return $this->helper->guzzleExec($this->_restRad, [
                    'url' => 'inf-pasien-rujukan-rad/proses-batal',
                    'method' => 'POST',
                    'returnResponse' => true,
                    'payload' => [
                        'form_params' => $form_params
                    ]
                ]);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            // Return form
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $type = $request->get('type', null);
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['type'] = $type;
        $draw = $request->get('draw', 1);
        $data = [];

        try {
            $response = $this->_restRad->get('inf-pasien-rujukan-rad', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                // Manage data for datatables
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasienkirimkeunitlain_id']);
                $pendaftaranIdEncrypt = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;
                $value['pendaftaran_id_encrypt'] = $pendaftaranIdEncrypt;
                unset($value['pasienkirimkeunitlain_id']);
                $value['rowNum'] = $no;
                $value['tgl_rujukan'] = DocoHelpers::convDateTime($value['tgl_rujukan'], true, false);
                $tgl_lahir = !empty($value['tanggal_lahir']) ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])), false, false) : "-";
                $value['tanggal_lahir'] = $tgl_lahir;

                $pemeriksaan = "<li>";
                $pemeriksaan .= isset($value['nama_pemeriksaan']) ? str_replace(",", "</li><li>", $value['nama_pemeriksaan']) : "-";
                $pemeriksaan .= "</li>";
                $value['pemeriksaan'] = $pemeriksaan;
                $value['jk'] = isset($value['jenis_kelamin_kode']) ? $value['jenis_kelamin_kode'] : "-";
                
                $no_sep = "-";
                if(!empty($value['no_sep'])) {
                  $no_sep = explode('##', $value['no_sep']);
                  $arr_nosep = [];
                  for ($i=0; $i < count($no_sep); $i++) { 
                    $arr_nosep[] = $no_sep[$i];
                  }

                  $no_sep = implode('<br>', $arr_nosep);
                }
                $value['no_sep'] = $no_sep;
                $value['detail_diagnosa'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/radiologi/inf-pasien-rujukan-rad/detail-diagnosa?pasienkirimkeunitlain_id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);

                $data[$key] = $value;
            }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionEditTanggalRujukan($id)
    {
        $title = Yii::t('fe', 'Edit Tanggal Rujukan Radiologi');
        $subTitle = Yii::t('fe', 'Rencana Pemeriksaan Radiologi');
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $modelOrder = new InfoOrderanRadV;

        if (!empty($request->post())) {
            $modelOrder->load($request->post());
            $response = $this->_restRad->post('inf-pasien-rujukan-rad/update-tanggal-rujukan', [
                'query' => [
                    'id' => $id
                ],
                'form_params' => $modelOrder->attributes
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::response($body);
        }

        $response = $this->_restRad->get('inf-pasien-rujukan-rad/pasien-rujukan', [
            'query' => [
                'id' => $id,
            ]
        ]);
        $body = json_decode($response->getBody(), True);
        $modelOrder->tgl_rujukan = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($body['response']['tgl_rujukan'])), false, false);

        return $this->renderAjax('edit_tanggal', get_defined_vars());
    }

    private function getDataRujukan()
    {
        try {
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/get-options');
            $body = json_decode($response->getBody(), true);

            $result = $body['response']['data'];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPemeriksaan($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/get-pemeriksaan-view', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasienkirimkeunitlain_id']);
                $value['primary'] = $primaryKey;
                unset($value['pasienkirimkeunitlain_id']);
                $value['rowNum'] = $no;
                $value['is_checkbox'] = '';
                if ($value['is_cyto']) {
                    $value['is_checkbox'] = '<input type="checkbox" class="checkbox" checked disabled >';
                }
                $data[$key] = $value;
            }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDokter($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/get-dokter?advanced-filter[nama_pegawai]=' . $q);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array(); // array dokter temp
            foreach ($body['response']['data'] as $value)
                if (!array_key_exists($value['pegawai_id'], $temp_dokter)) {
                    $result['results'][] = [
                        'id' => $value['nama_pegawai'],
                        'text' => $value['nama_pegawai']
                    ];
                    $temp_dokter[$value['pegawai_id']] = $value['nama_pegawai'];
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

    /**
     * List dropdown of doctor
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionDokterList()
    {
        return $this->helper->guzzleExec($this->_restRad, [
            'url' => 'inf-pasien-rujukan-rad/dokter-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    public function actionGetDokterRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id");
        try {
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/get-dokter?advanced-filter[nama_pegawai]=' . $q . '&advanced-filter[ruangan_id]=' . $ruanganid);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPegawaiRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id");
        try {
            $response = $this->_restRad->get('allow/data-pegawai?advanced-filter[nama_pegawai]=' . $q . '&advanced-filter[ruangan_id]=' . $ruanganid);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportExcel()
    {
        // Convert to json format
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Get request
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        // Try catch
        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-rujukan.xlsx";
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/export-excel', [
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
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

    public function actionExportPdf()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/pasien-rujukan-radiologi.pdf";
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/export-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return $path;
            // return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDetailDiagnosa()
    {
        $request = Yii::$app->request;
        $pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id');

        try {
            $response = $this->_restRad->get('inf-pasien-rujukan-rad/detail-diagnosa?pasienkirimkeunitlain_id='.$pasienkirimkeunitlain_id);
            $body = json_decode($response->getBody(), true);
            $result = ArrayHelper::getValue($body, 'response', []);
            $diagnosa_utama = ArrayHelper::getValue($result, 'diagnosa_utama', '-');
            $diagnosa_penyerta = ArrayHelper::getValue($result, 'diagnosa_penyerta', []);
            $catatan_dokterpengirim = ArrayHelper::getValue($result, 'catatan_dokterpengirim', '');

            return $this->renderAjax('_detail_diagnosa', compact('diagnosa_utama', 'diagnosa_penyerta', 'catatan_dokterpengirim'));
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
