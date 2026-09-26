<?php


namespace Doco\ranap\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\ranap\models\LaporanVisiteDokterView;
use app\modules\ranap\models\VisiteDokterView;
use app\modules\ranap\models\InfoTarifrs;
use app\modules\ranap\models\TindakanPelayanan;

class TraVisiteDokterController extends DocoController
{
    protected $_page;
    protected $_restRanap;
	public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;

        $model = new InfoTarifrs;
        $modelV = new VisiteDokterView;
        $modelTra = new TindakanPelayanan;
        
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi_nama = Yii::$app->docoVars->workspace("instalasi_name");
        $id_cppt = "";
        $params = [
            'controller'=>'traVisitDokter',
            'ruangan_id'=>$ruangan_id,
            'instalasi_nama'=>$instalasi_nama,
        ];
        if ($request->post()) {
            $modelTra->load($request->post());
            if ($modelTra->validate()) {
                $dokterVisite = $modelTra['dokterpenanggungjawab_id'];
                $jenisVisite = $modelTra['daftartindakan_id'];
                $cppt = $request->post('VisiteDokterView');
                $cppt_id = $cppt['cppt_id'];
                
                $response = $this->_restRanap->request('POST', 'tra-visite-dokter/onvisite-dokter',[
                    'query' => ['id' => $cppt_id],
                    'form_params' =>['pegawai_id'=>$dokterVisite, 'daftartindakan_id'=>$jenisVisite]
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,false,true);
            } else {
                    $errors = DocoHelpers::parseError($modelTra->errors,'TindakanPelayanan');
                return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
            }
        } else {

            $datajenis_visite = [];
            $datadokter_visite = [];
            $resMaster['kamarruangan'] = [];
            $resMaster['dokter'] = [];
            if( ($params["ruangan_id"] != '-') && ($params["instalasi_nama"] != '-') && (!empty($resMaster)) ){
                $response = $this->_restRanap->get('allow/get-api?' . http_build_query($params));
                $body = json_decode($response->getBody(), TRUE);
                $resMaster = $body['response']['master'];
                $datadokter_visite = $body['response']['master']['doktervisite'];
            }
            return $this->render('index', get_defined_vars());
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $counter=0;

        try {
            $response = $this->_restRanap->get('tra-visite-dokter/index?ruangan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = json_encode($value['cppt_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['idnya'] = $primary;
                $value['tgl_cppt'] = date('d M Y H:i:s', strtotime($value['tgl_cppt']));
                $value['tgl_admisi'] = date('d M Y H:i:s', strtotime($value['tgl_admisi']));
                $value['carabayar_penjamin'] = $value['carabayar_nama'] . ' / ' . $value['penjamin_nama'];
                $value['ruangan_kamar'] = $value['ruangan_nama'] . ' - ' . $value['kamarruangan_nokamar'] .' - '. $value['no_tempattidur'];
                $value['rowNum'] = $no;
                $data[$key] = $value;
                $counter++;
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

    public function actionGetDataJenisVisite($id='')
    {   
        try{
            $data = [];
            if(isset($id)){
                $response = $this->_restRanap->get('tra-visite-dokter/get-jenis-visite?id='.$id);
                $body = json_decode($response->getBody(), True);
                foreach ($body['response']['data'] as $key => $value) {
                    $data[] = ['id' => $value['daftartindakan_id'], 'text' => $value['daftartindakan_nama']];
                }
                $total = count($body['response']['data']);
                $return = ['result'=>$data];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetNamaRuangan()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-pindah/data-nama-ruangan',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['ruangan_nama'], 'text' => $value['ruangan_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

}