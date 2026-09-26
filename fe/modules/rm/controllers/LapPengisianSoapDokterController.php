<?php 
namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class LapPengisianSoapDokterController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-pengisian-soap-dokter/';
    protected $_controllerService = 'lap-pengisian-soap-dokter/';
    const TITLE_SOAP = 'SOAP';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Pengisian SOAP Dokter');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->guzzleExec($this->_restRm,[
            'url' => 'lap-pengisian-soap-dokter/generate-api',
            'method' => 'get',
            'payload' =>[]
        ]);
        $module = $this->_module;
        $title_soap = self::TITLE_SOAP;

        return $this->render('index', compact('api','title','title_soap'));
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {

            $getData = $this->guzzleExec($this->_restRm,[
                'url' => 'lap-pengisian-soap-dokter/index',
                'method' => 'get',
                'payload' => [
                    'query' => http_build_query($yiiRestfulParams)
                ]
            ]);
            foreach ($getData['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['primary'] = DocoHelpers::encrypt($value['pegawai_id']);
                $value['jumlah_soap'] = $value['jumlah_soap'] ? $value['jumlah_soap'] : 0;
                $value['jumlah_pasien'] = $value['jumlah_pasien'] ? $value['jumlah_pasien'] : 0;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $getData['_meta']['totalCount'];
            $result['recordsFiltered'] = $getData['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();

            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            
            return $result;
        }
    }

    public function actionGetDataPasien()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);
        $instalasi_id = $request->get('instalasi_id', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $pegawai_id = $request->get('pegawai_id', null);
        $jenis_laporan = $request->get('jenis_laporan', null);
        $tgl_pendaftaran = $request->get('tgl_pendaftaran');
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {

            $getData = $this->guzzleExec($this->_restRm,[
                'url' => 'lap-pengisian-soap-dokter/get-data-pasien',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'pegawai_id' => $pegawai_id,
                        'instalasi_id' => $instalasi_id,
                        'ruangan_id' => $ruangan_id,
                        'tgl_pendaftaran' => $tgl_pendaftaran,
                    ]
                ]
            ]);
            foreach ($getData['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('Y-m-d 00:00:01', strtotime($value['tgl_pendaftaran']));
                $value['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran'], false, false);
                $value['jenis_laporan'] = '';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $getData['_meta']['totalCount'];
            $result['recordsFiltered'] = $getData['_meta']['totalCount'];

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
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/laporan-pengisian-soap-dokter.xlsx";
            $data = $this->guzzleExec($this->_restRm,[
                'url' => 'lap-pengisian-soap-dokter/export-excel',
                'method' => 'get',
                'payload' => [
                    'query' => http_build_query($yiiRestfulParams)
                ],
                'save_to' => $path
            ]);

            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDetail()
    {
        try {
            $request = Yii::$app->request;
            $response = [];
            $title = Yii::t('fe', 'Detail');
            $pegawai_id = json_decode(DocoHelpers::decrypt($request->get('id')));
            $instalasi_id = $request->get('instalasi_id');
            $ruangan_id = $request->get('ruangan_id');
            $jenis_laporan = $request->get('jenis_laporan');
            $tipe = self::TITLE_SOAP;
            $tgl_pendaftaran = $request->get('tgl_pendaftaran');

            $getData = $this->guzzleExec($this->_restRm,[
                'url' => 'lap-pengisian-soap-dokter/get-nama',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'pegawai' => $pegawai_id,
                        'instalasi_id' => $instalasi_id,
                        'ruangan_id' => $ruangan_id,
                    ]
                ]
            ]);

            $instalasi_nama = $getData['instalasi_nama'];
            $ruangan_nama = $getData['ruangan_nama'];
            $dokter = $getData['dokter'];
            
            return $this->renderAjax('detail', compact('tgl_pendaftaran','title','dokter','instalasi_nama','ruangan_nama','tipe','pegawai_id','instalasi_id','ruangan_id','jenis_laporan'));
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionExportExcelDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);
        $instalasi_id = $request->get('instalasi_id', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $pegawai_id = $request->get('pegawai_id', null);
        $jenis_laporan = $request->get('jenis_laporan', null);
        $tgl_pendaftaran = $request->get('tgl_pendaftaran');
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $path = Yii::getAlias("@download") . "/laporan-detail-pengisian-soap-dokter.xlsx";
            $data = $this->guzzleExec($this->_restRm,[
                'url' => 'lap-pengisian-soap-dokter/export-excel-detail',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'pegawai_id' => $pegawai_id,
                        'instalasi_id' => $instalasi_id,
                        'ruangan_id' => $ruangan_id,
                        'tgl_pendaftaran' => $tgl_pendaftaran
                    ]
                ],
                'save_to' => $path
            ]);

            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();

            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            
            return $result;
        }
    }
}