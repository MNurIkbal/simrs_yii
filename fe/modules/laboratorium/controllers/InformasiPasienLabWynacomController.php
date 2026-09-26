<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Pasien Laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

namespace Doco\laboratorium\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\master\models\KamarForm;
use app\modules\laboratorium\models\BatalPeriksaPenunjangForm;
use app\modules\laboratorium\models\InfoPasienLabView;

use yii\helpers\ArrayHelper;

class InformasiPasienLabWynacomController extends DocoController
{
    protected $_title = "Informasi Pasien Laboratorium Intregasi LIS";
    protected $_module = '/laboratorium/informasi-pasien-lab-wynacom';
    protected $_restLab;
    protected $_restKasir;
    protected $_restMaster;
    protected $_id_ruangan;

    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->laboratorium;
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $cara_bayar = $penjamin = $instalasi = $asalRujukan = [];
        $status = DocoConstants::$status_lab;
        $statusFilter = [
            0 => 'BATAL',
            1 => 'BELUM PROSES',
            2 => 'DIPROSES',
            3 => 'SEBAGIAN',
            4 => 'SELESAI'
        ];
        // $asalRujukan = DocoConstants::$asal_rujukan;
        try {
            $loginpemakai_id = Yii::$app->user->identity->loginpemakai_id;
            $payload['loginpemakai_id'] = $loginpemakai_id;
            $response = $this->_restLab->get('inf-pasien-lab-wynacom/get-options', [
                'form_params' => [],
                'query' => $payload
            ]);
            $body = json_decode($response->getBody(), true);
            $cara_bayar = $body['response']['cara_bayar'];
            $penjamin = $body['response']['penjamin'];
            $instalasi = $body['response']['instalasi'];
            $asalRujukan = $body['response']['asal_rujukan'];
            $isException = $body['response']['is_exception'];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
            $isException = false;
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restLab->request('get', 'inf-pasien-lab-wynacom/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $value['primary'] = $primaryKey;
                unset($value['kamarruangan_id']);
                $value['rowNum'] = $no;
                $value['id_periksa'] = $value['status_periksa'];
                if(isset($value['status_periksa']) && !is_null($value['status_periksa'])){
                    $value['status_periksa'] = DocoConstants::$status_lab[$value['status_periksa']];
                }
                $value['history'] = '<button type="button" data-id="' . $value['pasienmasukpenunjang_id'] . '" data-no="' . $value['no_masukpenunjang'] . '" class="btn btn-xs btn-labeled btn-info btn-history"><b><i class="fa fa-sm fa-search"></i></b> History</button>';
                $value['noRegis'] = DocoHelpers::encrypt($value['no_pendaftaran']);;
                $color = '#FFFfff';
                $font = 'black';
                $value['nama_stat'] = '';
                $received_flag = !empty($value['received_flag']) ? $value['received_flag'] : false;
                $is_hasil = !empty($value['is_hasil']) ? $value['is_hasil'] : false;
                $statusResult = !empty($value['status_result']) ? $value['status_result'] : 0;
                $isComplete = !empty($value['is_complete']) ? $value['is_complete'] : false;
                $value['is_complete'] = !empty($value['is_complete']) ? 1 : 0;
                if (strtolower($value['status_periksa']) == 'batal') {
                    $value['stat'] = 0;
                    $value['nama_stat'] = 'BATAL';
                    $color = '#D24D57';
                    $font = 'white';
                } else if ($received_flag == false) {
                    $value['stat'] = 1;
                    $value['nama_stat'] = 'BELUM DIPROSES';
                    $color = 'white';
                    $font = 'black';
                } else if ($received_flag && $statusResult == 0) {
                    $value['stat'] = 2;
                    $value['nama_stat'] = 'DIPROSES';
                    $color = '#26A65B';
                    $font = 'white';
                } else if ($received_flag && $is_hasil && $statusResult == 1) {
                    $value['stat'] = 3;
                    $value['nama_stat'] = 'SEBAGIAN';
                    $color = '#ff985a';
                    $font = 'white';
                } else if ($received_flag && $is_hasil && $statusResult == 2) {
                    $value['stat'] = 4;
                    $value['nama_stat'] = 'SELESAI';
                    $color = '#85C1E9';
                    $font = 'white';
                }

                // if (strtolower($value['status_periksa']) == 'batal') {
                //     $value['stat'] = 0;
                //     $value['nama_stat'] = 'Batal';
                //     $color = '#D24D57';
                //     $font = 'white';
                // } else if (($value['is_hasil']) == false) {
                //     //belum diperiksa
                //     $value['stat'] = 1;
                //     $value['nama_stat'] = 'Belum Diperiksa';
                //     $color = 'white';
                //     $font = 'black';
                // } else if (($value['is_hasil']) == true) {
                //     //selesai
                //     $value['stat'] = 2;
                //     $value['nama_stat'] = 'Selesai';
                //     $color = '#26A65B';
                //     $font = 'white';
                // }
                // } else if (strtolower($value['status_periksa']) == 'selesai') {
                //     $color = '#26A65B';
                //     $font = 'white';
                // } else if(strtolower($value['status_periksa']) == 'periksa') {
                //     $color = '#2574A9';
                //     $font = 'white';
                // } else if(strtolower($value['status_periksa']) == 'ambil sampel') {
                //     $color = '#F5D76E';
                //     $font = 'white';
                // }
                // $value['status_periksa_btn'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'">'.$value['nama_stat'].'</span>';

                $value['no_rekam_medik'] = $value['no_rekam_medik'] . ' - ' . $value['nama_pasien'];
                $value['tglmasukpenunjang'] = DocoHelpers::convDateTime($value['tglmasukpenunjang'],true,false);
                $value['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])),false,false);
                $value['no_antrian'] = $this->getAksiListPasien($value);
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

    public function actionFormHistory($id,$no)
    {
        $pasienmasukpenunjang_id = $id;
        $no_masukpenunjang = $no;
        return $this->renderAjax('form-history', get_defined_vars());
    }

    public function actionGetDataHistory()
    {
        $id = Yii::$app->request->get('id', null);
        $no = Yii::$app->request->get('no', null);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $yiiRestfulParams['no'] = $no;
        $draw = $request->get('draw', 1);
        $data = [];

        $response = $this->_restLab->get('inf-pasien-lab-wynacom/get-history-view', [
            'form_params' => [],
            'query' => $yiiRestfulParams
        ]);
      

        $body     = json_decode($response->getBody(), true);
        $no       = $request->get('start', 1);
        $cekArray = [];
        foreach ($body['response']['data'] as $key => $value) {
            $no++;
            $primaryKey          = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
            $prevWynacomId       = DocoHelpers::encrypt($value['prev_hasilpemeriksaanlab_wynacom_id']);
            $wynacomId           = DocoHelpers::encrypt($value['hasilpemeriksaanlab_wynacom_id']);
            $value['rowNum']     = $no;
            $value['tgl_pemeriksaan'] = DocoHelpers::convDateTime(date('d-M-Y H:i', strtotime($value['tgl_pemeriksaan'])),false,false);
            $value['prev_hasil'] = '<a href="/laboratorium/informasi-pasien-lab-wynacom/form-history-preview?id='.$prevWynacomId.'" target=_blank>'.$value['test_nama_lis'] . ' : ' . $value['prev_hasil'].'</a>';

            if(in_array($value['test_nama_lis'], $cekArray)){
                $value['cur_hasil'] = '<a href="/laboratorium/informasi-pasien-lab-wynacom/form-history-preview?id='.$wynacomId.'" target=_blank>'.$value['test_nama_lis'] . ' : ' . $value['cur_hasil'].'</a>';
            }
            else{
                $value['cur_hasil'] = '<a href="/laboratorium/hasil-lab-wynacom/cetak?id='.$primaryKey.'" target=_blank>'.$value['test_nama_lis'] . ' : ' . $value['cur_hasil'].'</a>';
                array_unshift ($cekArray, $value['test_nama_lis']);
            }
            
            $data[$key] = $value;
        }
        $return = [
            'data' => $data,
            'recordsTotal' => $no,
            'recordsFiltered' => $no
        ];

        return DocoHelpers::response($return);
    }

    public function actionFormHistoryPreview()
    {
        $id = Yii::$app->request->get('id', null);
        $cache = Yii::$app->cache;
        $title = "History";
        $data = $this->getDataApi($id);
        $data_pasien = !empty($data['data_pasien']) ? $data['data_pasien'] : [];
        $hasilpemeriksaanlab_wynacom_id = DocoHelpers::decrypt($id);
        $data_wynacom = $this->_restLab->request('GET', 'inf-pasien-lab-wynacom/data-wynacom-preview?id=' . $hasilpemeriksaanlab_wynacom_id);
        $wynacom = json_decode($data_wynacom->getBody(),TRUE);
        $row = [];
        $rowNum = 0;
        foreach ($wynacom['response'] as $key => $value) {
            $rowNum++;
            $value['test_nama_lis']       = empty($value['test_nama_lis']) ? '-' : $value['test_nama_lis']; 
            $value['hasil']               = empty($value['hasil']) ? '-' : $value['hasil']; 
            $value['satuan']              = empty($value['satuan']) ? '-' : $value['satuan']; 
            $value['nilai_rujukan']       = empty($value['nilai_rujukan']) ? '-' : $value['nilai_rujukan']; 
            $value['test_method']         = empty($value['test_method']) ? '-' : $value['test_method']; 
            $value['rowNum']              = $rowNum;
            $row[$key]                    = $value;
            }
        $count_expertise = $data['count_data_expertise'];
        $catatan = !empty($data['data_hasil_lab']['catatan_instruksi'])
            ? $data['data_hasil_lab']['catatan_instruksi']
            : '';

        return $this->render('form_history_preview', get_defined_vars());
    }

    private function getDataApi($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restLab->request('GET', 'hasil-lab-wynacom/generate-api?id=' . $id);
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_pasien' => $body['response']['data-pasien'],
                'data_hasil_lab' => $body['response']['data-hasil-lab'],
                'count_data_expertise' => $body['response']['count-data-expertise'],
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function getAksiListPasien($data)
    {
        $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);

        $return_data = '';
        if(!empty($data['no_antrian'])){
            $return_data .= Html::button(
                '<i class="fa fa-volume-up"></i> '.$data['no_antrian'] ,
                [
                    'class' => 'btn btn-turquoise btn-md antrian',
                    'data-tooltip' => "tooltip",
                    'data-original-title' => Yii::t('fe', 'Panggil antrian'),
                    'data-id' => $data['pendaftaran_id'],
                    'data-antrian' => $data['no_antrian'],
                ]
            );
        }
        $return_data .= '&nbsp;&nbsp;';

        return $return_data;
    }

    /**
     * @author Randy Vianda Putra
     * @todo Panggil Antrian
     * @copyright 7 May 2018 aweutist
     */
    public function actionPanggilAntrian()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $post = Yii::$app->request->post();

        try {
            $no_antrian = $post['no_antrian'];
            $teks_panggil = DocoHelpers::convertAntrian($no_antrian);
            $response['teks_panggil'] = $teks_panggil;
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }


    public function actionGetAsalRujukan2($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if($get['asal_1'] == "order"){
                $response = $this->_restLab->get('inf-pasien-lab-wynacom/list-instalasi');
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value){
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if($get['asal_1'] == "rujukanRS"){
                $response = $this->_restLab->get('inf-pasien-lab-wynacom/list-asal-rujukan');
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
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


    public function actionGetAsalRujukan3($q = "",$q2="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if ($get['asal_1'] == "order") {
                $response = $this->_restLab->get('inf-pasien-lab-wynacom/list-ruangan?instalasi_id='.$get['asal_2']);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if ($get['asal_1'] == "rujukanRS") {
                $response = $this->_restLab->get('inf-pasien-lab-wynacom/list-asal-rujukan-dari?asalrujukan_id=' . $get['asal_2']);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
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

    public function actionFormBatal($id){
        $id = DocoHelpers::decrypt($id);
        $model = new BatalPeriksaPenunjangForm;
        $detail = $pemeriksaan = $data = [];
        try {
            $response = $this->_restLab->get('inf-pasien-lab-wynacom/detail-periksa?pasienmasukpenunjang_id=' . $id);
            $body = json_decode($response->getBody(), true);
            $detail = $body['response']['labDetail'];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }
        return $this->render('form_batal', get_defined_vars());
    }

    public function actionBatalOrder()
    {
        $request = Yii::$app->request;
        $model = new BatalPeriksaPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {

                $formData = $request->post(); // tampung formdata

                $form_params['pasienmasukpenunjang_id'] = DocoHelpers::decrypt($formData['pasienmasukpenunjang_id']);
                $form_params['peg_menyetujui_id'] = $formData['BatalPeriksaPenunjangForm']['peg_menyetujui_id'];
                $form_params['tgl_batalperiksa'] = $formData['BatalPeriksaPenunjangForm']['tgl_batalperiksa'];
                $form_params['alasan'] = $formData['BatalPeriksaPenunjangForm']['alasan'];
                try {
                    $response = $this->_restLab->post('inf-pasien-lab-wynacom/proses-batal', [
                        'form_params' => $form_params
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
            return $this->renderPartial('form', get_defined_vars());
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
            $response = $this->_restLab->get('inf-pasien-lab-wynacom/get-pemeriksaan-view', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
                $value['primary'] = $primaryKey;
                unset($value['tindakanpelayanan_id']);
                $value['rowNum'] = $no;
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


    public function actionPrintRincian($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/_rincian_tagihan_lab.pdf";
        try {
            $response = $this->_restLab->get('inf-pasien-lab-wynacom/print-rincian?id='.$id, ['save_to' => $path]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getListDataApi()
    {
            $request = Yii::$app->request;
            $response = $this->_restLab->get('inf-pasien-lab-wynacom/get-list-data?id_ruangan='.$this->_id_ruangan);
            $body = json_decode($response->getBody(),TRUE);

            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
            ];

            return $result;
    }

    public function actionSetDokter($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $title = Yii::t('fe', 'Mulai Pemeriksaan');
        $list_data = $this->getListDataApi();
        $data_pegawai = $list_data["data_pegawai"];

        $modelPasien = new InfoPasienLabView;
        $form_name = substr(strrchr(get_class($modelPasien), "\\"), 1);
        $is_disabled = 'true';
        
        $response = $this->_restLab->get('inf-pasien-lab-wynacom/get-pasien?id='.$id);
        $response = json_decode($response->getBody(), true);
        $data_pasien = $response["response"]["data"];
        // Cek data
        if ($modelPasien->load($data_pasien, '')) {
            // Assign manually
            $modelPasien->pegawai_id = $data_pasien['pegawai_id'];
            // Cek post
            if ($request->post()){
                $data = $request->post('InfoPasienLabView');
                $modelPasien->pegawai_id = $data["pegawai_id"];
                $modelPasien->tglmasukpenunjang = $data["tglmasukpenunjang"];
                $modelPasien->pasienmasukpenunjang_id = $id;
                if($modelPasien->validate()) {
                    $response = $this->_restLab->post('inf-pasien-lab-wynacom/ubah-dokter', [
                            'form_params' => $modelPasien->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    $redirect = Url::to(['/laboratorium/speciment/index', 'id' => DocoHelpers::encrypt($id)]);
                    return json_encode(['url'=>$redirect]);
                }else{
                    return DocoHelpers::response($modelPasien->errors, 422, $form_name);
                }
            }else{
                return $this->renderAjax('_set_dokter', get_defined_vars());
            }
        }
        else {
            return DocoHelpers::responseTemplate(
                500,
                Yii::t('fe', 'Kegagalan Pada Sistem'),
                [],
                ['title' => Yii::t('fe', 'Gagal Mengubah Dokter'), 'text' => Yii::t('fe', 'Data tidak ditemukan.')]
            );
        }
    }


}