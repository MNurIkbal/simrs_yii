<?php 

namespace Doco\bedah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\assets\CalenderAssets;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\bedah\models\JadwalOperasiForm;
use app\modules\bedah\models\RescheduleOperasiForm;
use app\modules\bedah\models\BatalJadwalForm;

class JadwalOperasiController extends DocoController
{
    protected $_title = "Informasi Jadwal Operasi";
    protected $_module = '/bedah/jadwal-operasi';
    protected $_restBedah;

    public function init()
    {
        CalenderAssets::register(Yii::$app->view);
        parent::init();
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;

    }

    public function actionIndex()
    {
        $title = $this->_title;
        $list_ruangan = [];
        try {
            $response = $this->_restBedah->get('allow/get-ruangan',[
                'query' => [
                    'id' => DocoConstants::INSTALASI_BEDAH
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $list_ruangan = $response['response']['ruangan'];
        } catch (RequestException $e) {

        } 
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->_restBedah->get('jadwal-operasi', [
                'query' => $request->post()
            ]);

            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            $ruangan = isset($response['data_ruangan']) ? $response['data_ruangan'] : [];
            $data_jadwal = isset($response['jadwal_data']) ? $response['jadwal_data'] : [];
            return DocoHelpers::response([
                'ruangan' => $ruangan,
                'data_jadwal' => $data_jadwal
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actionView($id)
    {
        $id_parent = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $result = [];
        $title = Yii::t('fe', 'Jadwal Operasi');
        $model = new JadwalOperasiForm;
        try {
            $response = $this->_restBedah->get('jadwal-operasi/view',[
                'query' => [
                    'id' => $id_parent
                ]

            ]);
            
            $body = json_decode($response->getBody(), true);
            
            $data = isset($body['response']['data']) ? $body['response']['data'] : [];
            $tmp_list_pendaftaran = isset($body['response']['list_pendaftaran']) ? $body['response']['list_pendaftaran'] : [];
            $list_pendaftaran = [];
            if(!empty($tmp_list_pendaftaran)){
                foreach($tmp_list_pendaftaran as $key => $row){
                    $tgl_pendaftaran = !empty($row['tgl_pendaftaran']) ? date("d M Y  H:i:s",strtotime($row['tgl_pendaftaran'])) : '';
                    $list_pendaftaran[$key] = $row;
                    $list_pendaftaran[$key]['tgl_pendaftaran'] = $tgl_pendaftaran;
                }
            }
            if ($data['created_date']) {
                $data['created_date'] = date('d-M-Y H:i:s', strtotime($data['created_date']));
            }
            if ($data['tgl_permintaan']) {
                $data['tgl_permintaan'] = date('d-M-Y', strtotime($data['tgl_permintaan']));
            }
            if (!empty($data)) {
                $statusPeriksa = isset($data['status_periksa']) ? $data['status_periksa'] : null;
                if ($data['status_penunjang'] == DocoConstants::DISETUJUI && $statusPeriksa == DocoConstants::LAB_ST_PEN_BATAL) {
                    $data['status'] = $data['nama_statusperiksa'];
                }
            }
            
            $pasienkirimkeunitlain_id = ArrayHelper::getValue($data, 'pasienkirimkeunitlain_id', 0);
            
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
        return $this->render('view', get_defined_vars());
    }

    public function actionGetDataPemeriksaan($id)
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
        $id_parent = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restBedah->get('jadwal-operasi/get-data-pemeriksaan',[
                'query' => [
                    'id' => $id_parent
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $daftartindakan = false;
            foreach ($body['response']['data'] as $key => $value) {
                if(!empty($value['permintaankepenunjang_id'])){
                    $daftartindakan = true;
                    $no++;
                    $value['rowNum'] = $no;
                    $value['is_cyto'] = $value['is_cyto'] ? 'Ya' : 'Tidak';
                    $value['jenis_pemeriksaan'] = $value['golonganoperasi_nama'] . ' - ' . $value['kegiatanoperasi_nama'];
                }else{
                    $value['rowNum'] = null;
                    $value['jenis_pemeriksaan'] = null;
                }
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['order_tindakan'] = $daftartindakan;
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetNoRequest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restBedah->get('allow/get-no-request',[
                'query' => $request->get()
            ]);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array();
            foreach ($body['response']['data'] as $value) {
                $result['results'][] = [
                    'id' => $value['no_orderkeunitlain'],
                    'text' => $value['no_orderkeunitlain']
                ];
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

    public function actionTerima($id)
    {
        $request = Yii::$app->request;
        $model = new JadwalOperasiForm;
        $model->load($request->post()); 
        $formName = substr(strrchr(get_class($model), "\\"), 1); 
        $model->scenario = 'approve'; 
        $is_konfirm = $request->get('is_konfirm', null);
        if ($model->validate()) {
            $id_parent = DocoHelpers::decrypt($id);
            $response = $this->_restBedah->post('jadwal-operasi/terima', [
                'query' => [
                    'id' => $id_parent,
                    'is_konfirm' => $is_konfirm,
                ],
                'form_params' => $model->attributes
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response, false, $formName);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionTolak($id)
    {
        $request = Yii::$app->request;
        $model = new JadwalOperasiForm;
        $model->load($request->post());
        // $model->rule = 'decline';
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->scenario = 'tolak';
        if ($model->validate()) {
            $id_parent = DocoHelpers::decrypt($id);
            $response = $this->_restBedah->post('jadwal-operasi/tolak',[
                'query' => [
                    'id' => $id_parent
                ],
                'form_params' => $model->attributes
            ]);
            $response = json_decode($response->getBody(),true);

            return DocoHelpers::response($response, false, $formName);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionGetDokterOperator()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restBedah->get('allow/get-dokter-operator',[
                'query' => $request->get()
            ]);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array();
            foreach ($body['response']['data'] as $value) {
                $result['results'][] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai']
                ];
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
     * Get data kamar ruangan bedah sentral
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionKamarRuangan()
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'jadwal-operasi/kamar-ruangan',
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
        ]);
    }

    public function actionReschedule($id)
    {
        $request = Yii::$app->request;
        try {
            $title = Yii::t('fe', 'Reschedule Jadwal Operasi');
            $id = !is_numeric($id) ? DocoHelpers::decrypt($id) : $id;
            $model = new RescheduleOperasiForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if($model->load($request->post())) {
                $model->rencanaoperasi_id = $id;
                if($model->validate()) {
                    $response = $this->_restBedah->post('jadwal-operasi/reschedule?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false, $formName);
                }
                else {
                    $response = $model->errors;
                    return DocoHelpers::response($response,422,$formName);
                }
            }
            else {
                $response = $this->_restBedah->get('jadwal-operasi/get-jadwal-operasi?id='.$id);
                $body = json_decode($response->getBody(), true);
                $res = isset($body['response']) ? $body['response'] : [];
                $model->attributes = $res;
                $model->dr_anestesi_id = $res['dr_anastesi_id'];
                $model->ruangan_id = $res['ruangan_id'];
            }
            return $this->renderAjax('partial/_modal_reschedule', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionBatal($id)
    {
        $request = Yii::$app->request;
        try {
            $title = Yii::t('fe', 'Batal Jadwal Operasi');
            $id = !is_numeric($id) ? DocoHelpers::decrypt($id) : $id;
            $model = new BatalJadwalForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if($model->load($request->post())) {
                if($model->validate()) {
                    $response = $this->_restBedah->post('jadwal-operasi/tolak?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false, $formName);
                }
                else {
                    $response = $model->errors;
                    return DocoHelpers::response($response,422,$formName);
                }
            }
            return $this->renderAjax('partial/_modal_batal', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataJadwalOperasi()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->_restBedah->get('jadwal-operasi/get-data-jadwal', [
                'query' => $request->post()
            ]);
            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            $results = ArrayHelper::getValue($response, 'results', []);
            $lepasValidasiJadwalOperasi = ArrayHelper::getValue($response, 'lepas_validasi_jadwal_operasi', false);
            return DocoHelpers::response([
                'lepas_validasi_jadwal_operasi' => $lepasValidasiJadwalOperasi,
                'data_jadwal' => $results
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actionRuangan()
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'jadwal-operasi/ruangan',
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
        ]);
    }
    public function actionRiwayatOperasi($pasienkirimkeunitlain_id)
    {
        $request = Yii::$app->request;
        try {
            $title = Yii::t('fe', 'Riwayat Jadwal Operasi');
           
            return $this->renderAjax('partial/_modal_riwayat_operasi', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionRiwayatOperasiGetData()
	{
		$id = isset($_GET['pasienkirimkeunitlain_id']) ? $_GET['pasienkirimkeunitlain_id'] : null;
		$response = $this->_restBedah->post('jadwal-operasi/riwayat-operasi-get-data',[
			'form_params' => [
				'pasienkirimkeunitlain_id' => !empty($id && !is_numeric($id)) ? DocoHelpers::decrypt($id) : $id,
			]
		]);

		$response = json_decode($response->getBody(), true);
		if (isset($response['response'])) {
			foreach ($response['response']['data'] as $key => $value) {
				$value['status_operasi'] = isset($value['status_operasi']) ? ucwords(strtolower($value['status_operasi'])) : ''; 

				$response['data'][$key] = $value; 
			}

            $response['data'] = isset($response['data']) ? $response['data'] : [];
			$response['recordsTotal'] = isset($response['response']['_meta']['totalCount']) ? $response['response']['_meta']['totalCount'] : 0;
			$response['recordsFiltered'] = isset($response['response']['_meta']['totalCount']) ? $response['response']['_meta']['totalCount'] : 0;

			return json_encode($response);
		} else {
			return json_encode([
				"data" => [],
				'recordsTotal' => 0,
				'recordsFiltered' => 0
			]);
		}
	}
}