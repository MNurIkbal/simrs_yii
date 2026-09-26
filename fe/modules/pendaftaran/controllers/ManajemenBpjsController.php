<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\pendaftaran\models\ManajemenBpjsForm;
use app\modules\pendaftaran\models\HapusSepInternalForm;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\web\Response;
use yii\helpers\Html;
use app\components\Services\Contracts\BpjsInterface;
use app\components\Services\BpjsService;

class ManajemenBpjsController extends DocoController
{
    protected $_title = 'Manajemen BPJS';
    protected $_restPendaftaran;
    protected $_module = 'manajemen-bpjs/';
    protected $allowAction = ['*'];

    protected $bpjsService;

    public function __construct($id, $module, $config = [], BpjsInterface $bpjsService)
    {
        $this->bpjsService = $bpjsService;
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();
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
        try {
            $title = $this->_title;
            
            $request = Yii::$app->request;
            $post = Yii::$app->request->post();
            $model = new ManajemenBpjsForm;
            
            if ($request->post()) {
                $post['ManajemenBpjsForm']['user'] = Yii::$app->docoVars->user("nama");
                if ($post['ManajemenBpjsForm']['kasus_kecelakaan'] == 0){
                    $post['ManajemenBpjsForm']['kode_provinsi'] = null;
                    $post['ManajemenBpjsForm']['kode_kabupaten'] = null;
                    $post['ManajemenBpjsForm']['kode_kecamatan'] = null;
                }
                $requests = $this->_restPendaftaran->post('allow-bpjs/update-sep',
                    ['form_params' => $post['ManajemenBpjsForm']]
                );
                $response = json_decode($requests->getBody(), true);
                if ($response['response']['metaData']['code'] == 201 || $response['response']['metaData']['code'] == '201')
                {
                    return DocoHelpers::responseTemplate(
                        500,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => $response['response']['metaData']['message'],
                            'message' => $response['response']['metaData']['message'],
                        ]
                    );
                }
                return DocoHelpers::response($response);
            }

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionGetPasien()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $data = $request->get();
            $nosep = isset($data['no_sep']) ? $data['no_sep'] : null;

            $params = [
                'nosep' => $nosep
            ];

            $sep = $this->bpjsService->cariSep($nosep);
            $peserta =  $this->bpjsService->cariPeserta($sep['response']['peserta']['noKartu']);
            $rujukan =  $this->bpjsService->cariRujukan($sep['response']['peserta']['noKartu']);
            $rujukan_internal = $this->bpjsService->cariSepInternal($nosep);

            $requests = $this->_restPendaftaran->get('allow-bpjs/cek-sep-pasien',[
                'query' => [
                    'nosep' => $nosep
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            if (!empty($response['response'])){
                $sep['response']['dpjp']['kdDPJP'] = $response['response']['kode_dpjp_melayani'];
                $sep['response']['dpjp']['nmDPJP'] = $response['response']['nama_dpjp_melayani'];
                
            }
            
            if (!empty($sep['response']['tglSep'])){
                $sep['response']['tglSep'] = date('d M Y', strtotime($sep['response']['tglSep']));
            }

            if ($rujukan_internal['metaData']['code'] == 200) {
                $rujukan_internal_data = $rujukan_internal['response']['list'];

                $no = 0;
                foreach ($rujukan_internal_data as $key => $value) {
                    $no++;
                    $value['rowNum'] = $no;
                    $value['aksi'] = Html::button("Cetak SEP", [
                        'style' => "margin-bottom:8px; padding-left:8px !important;",
                        'class' => 'btn btn-info btn-xs btn-cetak-sep-internal', 
                        'data-nosep' => DocoHelpers::encrypt($value['nosep']),
                        'data-kddokter' => DocoHelpers::encrypt($value['kddokter']),
                        'data-kdpolituj' => DocoHelpers::encrypt($value['kdpolituj']),
                        'onClick' => 'cetakRujukanInternal(this)',
                    ]) .' '. Html::button("Hapus", [
                        'style' => "margin-bottom:8px; padding-left:8px !important;",
                        'class' => 'btn btn-danger btn-xs btn-hapus-rujukan', 
                        'data-nosep' => DocoHelpers::encrypt($value['nosep']),
                        'data-nosurat' => DocoHelpers::encrypt($value['nosurat']),
                        'data-tglrujukinternal' => $value['tglrujukinternal'],
                        'data-kdpolituj' => DocoHelpers::encrypt($value['kdpolituj']),
                    ]);
                    
                    $arrData[$key] = $value;
                }
                $rujukan_internal['response']['list'] = $arrData;
            }

            $data = [
                'sep' => $sep,
                'peserta' => $peserta,
                'rujukan' => $rujukan,
                'rujukan_internal' => $rujukan_internal,
            ];
            return $data;
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionHapusSep($no_sep)
    {
        try {
            if (!empty($no_sep)) {
                $data = [
                    'no_sep' => $no_sep
                ];
                $requests = $this->_restPendaftaran->post('allow-bpjs/manajemen-delete-sep',
                    ['form_params' => $data]
                );
                $response = json_decode($requests->getBody(), true);
                if ($response['response']['metaData']['code'] >= 201 || $response['response']['metaData']['code'] == '201')
                {
                    return DocoHelpers::responseTemplate(
                        400,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => $response['response']['metaData']['message'],
                            'message' => $response['response']['metaData']['message'],
                        ]
                    );
                } else {
                    return DocoHelpers::responseTemplate(
                        200,
                        'Sukses',
                        [],
                        [
                            'title' => Yii::t('fe', 'Proses Berhasil!'),
                            'text' => 'Data SEP berhasil dihapus',
                            'message' => 'Data SEP berhasil dihapus',
                        ]
                    );
                }
            }

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionReferensiPoliNew()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('search');
        $bridgeRes = $this->bpjsService->referensiPoli($term);
        $results = [];
        if ($bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['poli'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionReferensiProvinsi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $term = $request->get('search');

        $bridgeRes = $this->bpjsService->referensiProvinsi();
        if ($bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['list'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        $data = ['results' => $results];
        return $data;
    }

    public function actionModalRujukanInternal()
    {
        $title = 'Histori Rujukan Internal';
        return $this->renderAjax('_modal-rujukan-internal', get_defined_vars());
    }

    public function actionPrintSepInternal()
    {
        $session = Yii::$app->session;
        $idRuanganWorkspace = $session->get('active_workspace')['ruangan_id'];
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-sep-internal.pdf";

        $nosep = $request->get('nosep');
        $kode_dpjp_melayani = $request->get('kddokter');
        $politujuan = $request->get('kdpolituj');

        if ($nosep) {
            $nosep = DocoHelpers::decrypt($nosep);
        }
        if ($kode_dpjp_melayani) {
            $kode_dpjp_melayani = DocoHelpers::decrypt($kode_dpjp_melayani);
        }
        if ($politujuan) {
            $politujuan = DocoHelpers::decrypt($politujuan);
        }

        $result = $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'allow-bpjs/print-sep-internal',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'nosep' => $nosep,
                    'kode_dpjp_melayani' => $kode_dpjp_melayani,
                    'politujuan' => $politujuan,
                    'ruangan_id' => $idRuanganWorkspace,
                ],
                'save_to' => $path
            ],
        ]);
            
        return DocoHelpers::previewPdf($path);
    }

    public function actionHapusSepInternal()
    {
        try {
            $request = Yii::$app->request;
            $nosep = $request->get('nosep');
            $nosurat = $request->get('nosurat');
            $kdpolituj = $request->get('kdpolituj');
            $tglrujukinternal = $request->get('tglrujukinternal');

            if ($nosep) {
                $nosep = DocoHelpers::decrypt($nosep);
            }
            if ($nosurat) {
                $nosurat = DocoHelpers::decrypt($nosurat);
            }
            if ($kdpolituj) {
                $kdpolituj = DocoHelpers::decrypt($kdpolituj);
            }

            $model = new  HapusSepInternalForm;
            $model->noSep = $nosep;
            $model->noSurat = $nosurat;
            $model->tglRujukanInternal = $tglrujukinternal;
            $model->kdPoliTuj = $kdpolituj;
            $model->username = Yii::$app->docoVars->user('nama');

            $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'allow-bpjs/delete-sep-internal',
                'method' => 'post',
                'payload' => [
                    'form_params' => $model->attributes
                ],
            ]);

            if ($result['metaData']['code'] >= 201 || $result['metaData']['code'] == '201')
            {
                Yii::error($result);
                return DocoHelpers::responseTemplate(
                    400,
                    'Error',
                    [],
                    [
                        'title' => Yii::t('fe', 'Peringatan!'),
                        'text' => $result['metaData']['message'],
                        'message' => $result['metaData']['message'],
                    ]
                );
            } else if ($result['metaData']['code'] == '200'){
                return DocoHelpers::responseTemplate(
                    200,
                    'Sukses',
                    [],
                    [
                        'title' => Yii::t('fe', 'Proses Berhasil!'),
                        'text' => 'Data SEP Internal berhasil dihapus',
                        'message' => 'Data SEP Internal berhasil dihapus',
                    ]
                );
            } else {
                Yii::error($result);
                return DocoHelpers::responseTemplate(
                    400,
                    'Error',
                    [],
                    [
                        'title' => Yii::t('fe', 'Peringatan!'),
                        'text' => 'Gagal menghapus SEP internal',
                        'message' => $result,
                    ]
                );
            }
        } catch (RequestException $e) {
            Yii::error($e->getMessage());
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }
}