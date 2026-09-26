<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-30 15:03:21
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-03 09:50:58
 */

namespace Doco\radiologi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\radiologi\models\ExpertiseForm;
use Doco\radiologi\models\InputExpertiseForm;
use yii\helpers\ArrayHelper;

class ExpertiseController extends DocoController
{
    protected $_title = "Hasil Pemeriksaan Radiologi";
    protected $_module = '/radiologi/hasil-rad';
    protected $_restRad;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex($id)
    {
        //add scenario for skip expertise, create configuration for this function next time please
        $scenario = InputExpertiseForm::Scenario_SkipExpertise;
        $model = new InputExpertiseForm;
        $model->scenario = $scenario;
        $alias = Yii::getAlias("@media");
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        $get = $request->get();
        $readonly = 'false';
        $disabled = '';
        $btnDisabled = false;
        $id = DocoHelpers::decrypt($id);
        $hasilpemeriksaanrad_id = DocoHelpers::decrypt($get['hasilpemeriksaanrad_id']);
        $penunjang_id = DocoHelpers::decrypt($get['penunjang_id']);
        $status = DocoHelpers::decrypt($get['status']);
        $folderName = $get['hasilpemeriksaanrad_id'].'-'.$get['penunjang_id'];
        $prefix = '/input-hasil-rad/';
        $disabledDownload = '';
        $konfigSystem = [];
        $is_expertise_desc = true;
        $is_expertise_kesan = true;
        if(!file_exists($alias.$prefix.$folderName.'/')){
            $disabledDownload = 'disabled';
        }
        $hasilpemeriksaanrad_id = isset($get['hasilpemeriksaanrad_id']) ? DocoHelpers::decrypt($get['hasilpemeriksaanrad_id']) : '';
        if($request->post()){
            $post = $request->post();
            $isVerifikasi = ArrayHelper::getValue($post, 'is_verifikasi');
            $model->attributes = $post['InputExpertiseForm'];
            $kesimpulan = nl2br($model->attributes['kesimpulan']);
            $kesan = nl2br($model->attributes['kesan']);
            $model->kesan= $kesan;
            $model->kesimpulan= $kesimpulan;
            if($model->validate()){
                $response = $this->_restRad->post('expertise/save-expertise', [
                    'query' => [
                        'is_verifikasi' => $isVerifikasi,
                        'penunjang_id' => $penunjang_id,
                        'daftartindakan_id' => $id,
                    ],
                    'form_params'=> $model->attributes,
                ]);
                $body = json_decode($response->getBody(), true);
                $this->redirect([
                    'expertise/index?id='.$get['id'].'&hasilpemeriksaanrad_id='.DocoHelpers::encrypt($body['response']['id_hasil']).'&penunjang_id='.$get['penunjang_id'].'&status='.$get['status']]);
                return DocoHelpers::response($body['response']);
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $result = [];
        $daftartindakan_nama = '';
        $no_hasilrad = '';
        $model->hasilpemeriksaanrad_id = $hasilpemeriksaanrad_id;
        $tgl_ambilfoto = date('d-M-Y');
        $pemeriksaanrad_id = '';
        try{
            $response = $this->_restRad->get('expertise/get-hasil', ['query'=>['id'=>$hasilpemeriksaanrad_id]]);
            $body = json_decode($response->getBody(), true);
            $image_link = isset($body['response']['queryBridging']['image_link']) ? $body['response']['queryBridging']['image_link'] : '';
            $expertiseText = isset($body['response']['queryBridging']['obv_value_html']) ? $body['response']['queryBridging']['obv_value_html'] : '';
            $imageDisabled = isset($body['response']['queryBridging']['image_link']) ? false : true;
            $no_hasilrad = isset($body['response']['query']['no_hasilrad']) ? $body['response']['query']['no_hasilrad'] : '';
            $daftartindakan_nama = isset($body['response']['query']['daftartindakan_nama']) ? $body['response']['query']['daftartindakan_nama'] : '';
            $tindakanpelayanan = isset($body['response']['query']['tindakanpelayanan_id']) ? $body['response']['query']['tindakanpelayanan_id'] : '';
            $pasienmasukpenunjang_id = isset($body['response']['query']['pasienmasukpenunjang_id']) ? $body['response']['query']['pasienmasukpenunjang_id'] : '';
            $pemeriksaanrad_id = isset($body['response']['query']['pemeriksaanradiologi_id']) ? $body['response']['query']['pemeriksaanradiologi_id'] : '';
            $tgl_ambilfoto = isset($body['response']['query']['tgl_ambilfoto']) ? $body['response']['query']['tgl_ambilfoto'] : '';
            $tindakanPelayananId = isset($body['response']['query']['tindakanpelayanan_id']) ? DocoHelpers::encrypt($body['response']['query']['tindakanpelayanan_id']) : '';
            $daftarTindakanId = isset($body['response']['query']['daftartindakan_id']) ? DocoHelpers::encrypt($body['response']['query']['daftartindakan_id']) : '';
            $pasienMasukPenunjang = isset($body['response']['query']['pasienmasukpenunjang_id']) ? DocoHelpers::encrypt($body['response']['query']['pasienmasukpenunjang_id']) : '';
            $model->attributes = $body['response']['query'];
            $model->kesan = !empty($model->kesan) ? nl2br(strip_tags($body['response']['query']['kesan']))  : nl2br(strip_tags($expertiseText));
            $model->kesimpulan = !empty($model->kesimpulan) ? nl2br(strip_tags($body['response']['query']['kesimpulan']))  : nl2br(strip_tags($expertiseText));
            $konfigSystem = !empty($body['response']['konfigSystem']) ? $body['response']['konfigSystem'] : [];
            $is_expertise_desc = isset($konfigSystem['is_expertise_desc']) ? $konfigSystem['is_expertise_desc'] : true;
            $is_expertise_kesan = isset($konfigSystem['is_expertise_kesan']) ? $konfigSystem['is_expertise_kesan'] : true;
            $pasienPenunjang = !empty($body['response']['pasienPenunjang']) ? $body['response']['pasienPenunjang'] : [];
            $tglPendaftaran = ArrayHelper::getValue($pasienPenunjang, 'tgl_pendaftaran');
            $tgl_hasilrad = isset($body['response']['query']['tgl_hasilrad']) ? $body['response']['query']['tgl_hasilrad'] : date('Y-m-d H:i:s');
            $btnDisabled = $body['response']['query']['tgl_verifikasi'] ? true : false;
        }catch(\Exception $e){
            $result = [];
        }
        $model->no_hasilrad = $tindakanpelayanan;
        Yii::$app->session->set('input-pemeriksaan', [
            'daftartindakan_nama' => $daftartindakan_nama
        ]);
        $penunjang_id = isset($get['penunjang_id']) ? $get['penunjang_id'] : '';
        // $backUrl = Url::to(['/radiologi/hasil-rad', 'id'=>$penunjang_id]);
        $backUrl = Url::to(['/radiologi/hasil-rad/index', 'id'=>DocoHelpers::encrypt($pasienmasukpenunjang_id), 'pelayananId' => $tindakanPelayananId, 'tindakanId' => $daftarTindakanId]);
        $path = Yii::$app->docoPlugin->execute($this,'form_expertise');
        return $this->render($path, get_defined_vars());
    }
    public function actionListExpertise($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'List expertise');
        $result = [];
        try {
            $response = $this->_restRad->get('expertise/get-expertise?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $options = ArrayHelper::map($body['response'], 'expertise_id', 'nama_expertise');
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[$value['expertise_id']] = $value;
            }
            $result = ['options'=>$options, 'data'=>$data];
        } catch (Exception $e) {
            $result = [];
        }

        return $this->renderAjax('list_expertise', get_defined_vars());
    }
    
    public function actionUnduhHasil($folder)
    {
        $foldername = '/input-hasil-rad/'.$folder;
        try {
            return DocoHelpers::zipMaker($foldername, 'Hasil Scan Radiologi '.$folder);
        } catch (\Exception $e) {
            return DocoHelpers::response(['return'=>'docoNotification("error", "Terjadi kesalahan", "-")']);
        }
        
    }

    public function actionAllUnduhHasil($folder,$target)
    {
        $title        = 'Riwayat Hasil Scan Radiologi';
        $prefixModule = '/input-hasil-rad/';
        $targetPath = $prefixModule.$title.' '.$target;
        $decodeFol = json_decode($folder);
        $foldername = [];
        foreach ($decodeFol as  $v) {
            $foldername[] = $prefixModule.$v;
        }
         try {
            return DocoHelpers::multipleCopyToFolders($prefixModule, $foldername, $targetPath, $title);
        } catch (\Exception $e) {
            return DocoHelpers::response(['return'=>'docoNotification("error", "Terjadi kesalahan", "-")']);
        }
    }

    public function actionPreviewHasil($id, $tindakan_id, $penunjang_id, $preview = null)
    {
        $daftartindakan_id = DocoHelpers::decrypt($id);
        $tindakanpelayanan_id = DocoHelpers::decrypt($tindakan_id);
        $pasienmasukpenunjang_id = DocoHelpers::decrypt($penunjang_id);
        $request = Yii::$app->request;
        $hasilpemeriksaanrad_id = $request->get('hasilpemeriksaanrad_id');
        $request = Yii::$app->request;
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $path = Yii::getAlias("@download") . "/preview-pemeriksaan.pdf";
        try {
            $sessionInput = Yii::$app->session->get('input-pemeriksaan', []);
            

            // $response = $this->_restRad->post('input-hasil/cetak-hasil-pdf', [
            //     'save_to' => $path,
            //     'query' => [
            //         'daftartindakan_id' => $daftartindakan_id,
            //         'tindakanpelayanan_id' => $tindakanpelayanan_id,
            //         'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
            //         'preview' => $preview
            //     ],
            //     'form_params' => [
            //         'daftartindakan_nama' => isset($sessionInput['daftartindakan_nama']) ? $sessionInput['daftartindakan_nama'] : '',
            //         'kesan' => isset($sessionInput['kesan']) ? $sessionInput['kesan'] : '',
            //         'kesimpulan' => isset($sessionInput['kesimpulan']) ? $sessionInput['kesimpulan'] : '',
            //         'tgl_hasil' => isset($sessionInput['tgl_hasil']) ? $sessionInput['tgl_hasil'] : '',
            //     ]
            // ]);
            // $body = json_decode($response->getBody(), true);
            // return DocoHelpers::previewPdf($path);

            $modulAlias = $activeWorkspace['modul_alias'];
            $urlReport = 'hasil-radiologi';
            if(Yii::$app->report->isAvailable($urlReport)){
                return Yii::$app->report->exec($urlReport, [
                       'queryParameter' => [
                            'modul' => $modulAlias,
                            'tindakan_id' => $daftartindakan_id,
                            'id' => $tindakanpelayanan_id,
                            'penunjang_id' => $pasienmasukpenunjang_id,
                            'hasilpemeriksaanrad_id' => !empty($hasilpemeriksaanrad_id) ? $hasilpemeriksaanrad_id : null,
                            'halaman' => "2",
                            'jenis_cetakan' => 2
                       ],
                ]);
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * Set session input
     * 
     * @param String $kesan
     * @param String $kesimpulan
     * @param String $daftartindakan_nama
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSetInput()
    {
        $kesan = Yii::$app->request->post('kesan', '');
        $kesimpulan = Yii::$app->request->post('kesimpulan', '');
        $tgl_hasil = Yii::$app->request->post('tgl_hasil', date('Y-m-d H:i:s'));
        // $daftartindakan_nama = Yii::$app->request->post('daftartindakan_nama', '');
        $existingData = Yii::$app->session->get('input-pemeriksaan', []);
        Yii::$app->session->set('input-pemeriksaan', array_merge($existingData, compact('kesan', 'kesimpulan', 'tgl_hasil')));
        return $this->responseJson(200, 'Berhasil memperbarui input preview.');
    }

    public function actionGetRiwayatExpertise()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $tindakanpelayanan_id = $request->get('tindakanpelayanan_id');
        $pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id');
        $yiiRestfulParams['tindakanpelayanan_id'] = $tindakanpelayanan_id;
        $yiiRestfulParams['pasienmasukpenunjang_id'] = $pasienmasukpenunjang_id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $response = $this->_restRad->get('expertise/get-riwayat-expertise', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_hasilrad'] = !empty($value['tgl_hasilrad']) ? date('d M Y H:i:s', strtotime($value['tgl_hasilrad'])) : null;
                $value['kesan'] = strlen($value['kesan']) > 500 ? substr(nl2br(strip_tags($value['kesan'])), 0, 500).'...' : nl2br(strip_tags($value['kesan']));
                $value['kesimpulan'] = strlen($value['kesimpulan']) > 500 ? substr(nl2br(strip_tags($value['kesimpulan'])), 0, 500).'...' : nl2br(strip_tags($value['kesimpulan']));
                $value['aksi'] = '<button type="button" class="btn btn-info btn-labeled btn-xs btn-toolbar" data-options="link" target="_blank" data-target="/radiologi/hasil-rad/cetak-hasil?id='.DocoHelpers::encrypt($value['daftartindakan_id']).'&tindakan_id='.DocoHelpers::encrypt($value['tindakanpelayanan_id']).'&penunjang_id='.DocoHelpers::encrypt($value['pasienmasukpenunjang_id']).'&hasilpemeriksaanrad_id='.DocoHelpers::encrypt($value['hasilpemeriksaanrad_id']).'"><b><i class="fa fa-print"></i></b>Lihat History</button>';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreateTemplateBaru()
    {
        // Init
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new ExpertiseForm();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $options = $optionsDokter = [];
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('expertise/create', [
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
        }
        return $this->renderAjax('form_template_expertise', compact('title','model','options','optionsDokter'));
    }
    
    public function actionGetHasilRadiologi()
    {
        $request = Yii::$app->request;
        $hasilPemeriksaaRadId = $request->get('hasilpemeriksaanrad_id');
        $hasilPemeriksaaRadId = DocoHelpers::decrypt($hasilPemeriksaaRadId);
        $response = $this->_restRad->get('expertise/get-hasil-radiologi', ['query'=>['id'=> $hasilPemeriksaaRadId]]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
    }
}
