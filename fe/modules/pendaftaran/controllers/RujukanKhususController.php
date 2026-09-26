<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\web\Response;
use app\components\Services\Contracts\BpjsInterface;
use app\modules\pendaftaran\models\RujukanKhususForm;
use Doco\models\bpjs\Bpjs;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\pendaftaran\models\RujukanKhususHapusForm;

class RujukanKhususController extends DocoController
{
    protected $_title = 'Rujukan Khusus';
    protected $title_hapus = 'HAPUS RUJUKAN KHUSUS';
    protected $_restPendaftaran;
    protected $_module = 'rujukan-khusus/';
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
        $title = $this->_title;
        $bulan = DocoHelpers::daftarBulan();
        $dataBulan = $dataTahun = [];
        foreach ($bulan as $key => $value) {
            $dataBulan[] = [
                'id' => $key,
                'text' => $value
            ];
        }

        for ($x = date('Y'); $x >= (date('Y') - 9) ; $x--) {
            $dataTahun[] = [
                'id' => $x,
                'text' => $x
            ];
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $filterParam = $request->get('advancedFilter');
            $data = $vclaimData = $result = [];
            $bulan = $tahun = null;
            $filterList = [
                'norujukan' => 'norujukan',
                'nokapst' => 'nokapst',
                'nmpst' => 'nmpst',
                'diagppk' => 'diagppk'
            ];

            if (isset($filterParam['blnRujukan'])) {
                $bulan = $filterParam['blnRujukan'];
            }

            if (isset($filterParam['thnRujukan'])) {
                $tahun = $filterParam['thnRujukan'];
            }
            
            $vclaimRes = $this->bpjsService->listRujukanKhusus($bulan, $tahun);
            if ($vclaimRes && $vclaimRes['metaData']['code'] == 200) {
                $vclaimData = $vclaimRes['response']['rujukan'];
            } 
            
            $no = 0;
            foreach ($vclaimData as $key => $value) {
                $isFiltered = $this->filterDataVclaim($filterList, $value, $filterParam);
                if ($isFiltered) {
                    $no++;
                    $value['no'] = $no;
                    $value['primary'] = $value['idrujukan'];

                    $data[] = $value;
                }
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($vclaimData);
            $result['recordsFiltered'] = count($data);

            return DocoHelpers::response($result);

        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    private function filterDataVclaim($filterList, $data, $filterParam) {
        $result = true;

        foreach ($filterParam as $key => $value) {
            if (isset($filterList[$key]) && $value) {
                if (strpos(strtolower($data[$key]), strtolower($value)) === false) {
                    $result = false;
                    break;
                }
            }
        }
        return $result;
    }


    /**
     * @todo Action Create Rujukan Khusus
     * @author Ikhwanu arriyadh <ikhwanu.arriyadh@sirs.co.id>
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $response = [];
        try {
            $title = 'Create '.$this->_title;
            $data = new RujukanKhususForm;
            if ($request->post()){
                $data->load($post);
                $data_diagnosa = (array) json_decode($post['data_diagnosa'],true);
                $data_procedure = (array) json_decode($post['data_procedure'],true);
                $data->data_diagnosa = json_encode($data_diagnosa);
                $data->data_procedure = json_encode($data_procedure);
                $data->scenario = 'create_form';

                $post['noRujukan'] = $post['RujukanKhususForm']['no_rujukan'];
                $post['temp_diagnosa'] = $data_diagnosa;
                $post['temp_procedure'] = $data_procedure;
                $post['user'] = Yii::$app->docoVars->user("nama");
                if ($data->validate()) {
                    $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                        'url' => 'rujukan-khusus/create-rujukan-khusus',
                        'method' => 'post',
                        'payload' => [
                            'form_params' => $post
                        ],
                    ]);
                    if ($response['data']['metaData']['code'] != 200 || $response['data']['metaData']['code'] != '200')
                    {
                        return DocoHelpers::responseTemplate(
                            400,
                            'Error',
                            $response['data']['response'],
                            [
                                'title' => Yii::t('fe', 'Peringatan!'),
                                'text' => $response['title'],
                                'message' => $response['text'],
                            ]
                        );
                    }

                    return DocoHelpers::response($response['data']);
                } else{
                    $response = $data->errors;
                    return DocoHelpers::response($response, 422, 'RencanaKontrolForm');
                }
            }
            return $this->render('form', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionGetPasienByRujukan()
    {
        try {
        $request = Yii::$app->request;
        $req_post_rujukan = $request->get('no_rujukan');
        // $response_rujukan =  $this->bpjsService->cariBerdasarkanNoRujukan($req_post_rujukan,1);
        // yii::error($response_rujukan);
        // $metadata = $response_rujukan['metaData'];
        // $code = ArrayHelper::getValue($response_rujukan['metaData'], 'code', null);
        // if ($code != 200 || $code != '200') {
        //     return DocoHelpers::responseTemplate(
        //         400,
        //         'Error',
        //         [],
        //         [
        //             'title' => Yii::t('fe', 'Peringatan!'),
        //             'text' => $response_rujukan['metaData']['message'],
        //             'message' => $response_rujukan['metaData']['message'],
        //         ]
        //     );
        // }
        // $data = [
        //     'rujukan' => $response_rujukan['response']
        // ];
        // return DocoHelpers::response($data);
        return DocoHelpers::responseTemplate(
            200,
            'Success',
            [],
            [
                'title' => Yii::t('fe', 'Success!'),
                'text' => '',
                'message' => '',
            ]
        );
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionConfirmHapus($idrujukan, $norujukan)
    {
        $request = Yii::$app->request;
        $idrujukan = $request->get('idrujukan',null);
        $norujukan = $request->get('norujukan',null);
        $username = Yii::$app->docoVars->user('nama');
        $hapusform = new RujukanKhususHapusForm;
        $title = $this->title_hapus;
        try{

            if ($request->post()) {
                $hapusform->load($request->post());
                $hapusform->idrujukan = $idrujukan;
                $hapusform->norujukan = $norujukan;
                $hapusform->password = DocoHelpers::encrypt($hapusform->password);
                if ($hapusform->validate()) {
                    $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                        'url' => 'rujukan-khusus/hapus-rujukan',
                        'method' => 'DELETE',
                        'payload' => [
                            'form_params' => $hapusform->attributes
                        ]
                    ]);
                    if (isset($response)){
                        if ($response['code'] > 200 || $response['code'] != '200'){
                            return DocoHelpers::responseTemplate(
                                500,
                                'Error',
                                [],
                                [
                                    'title' => Yii::t('fe', 'Peringatan!'),
                                    'text' => $response['message'],
                                    'message' => $response['message'],
                                ]
                            );
                        }
                    }
                    return DocoHelpers::response($response);
                }
                return DocoHelpers::response($hapusform->errors,422,'RujukanKhususHapusForm');
            } else {
                return $this->renderAjax('partial/_modal_batal', get_defined_vars());
            }
        } catch (\Exception $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}