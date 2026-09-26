<?php 
/**
 * @author : Ardi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;

class LapDaftarRawatController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-daftar-rawat/';
    protected $_controllerService = 'lap-daftar-rawat/';
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Pasien Di Rawat');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $module = $this->_module;
        return $this->render('index', get_defined_vars());
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
        $result['recordsFiltered'] = 0;
        $counter=0;

        try {
            $response = $this->_restRm->get('lap-daftar-rawat/index?ruangan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            // print_r($body); die;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $data[$key] = $value;
                $data[$counter]['carabayar_penjamin'] = $value['Cara Bayar'] . ' / ' . $value['Penjamin'];
                $data[$counter]['pendaftaran'] = $value['No. Pendaftaran'];
                $data[$counter]['r_medik'] = $value['No. Rekam Medik'];
                $data[$counter]['pasien'] = $value['Nama Pasien'];
                $data[$counter]['jk'] = $value['Jenis Kelamin'];
                $data[$counter]['kp'] = $value['Kelas Pelayanan'];
                $data[$counter]['jkp'] = $value['Jenis Kasus Penyakit'];
                $data[$counter]['lr'] = ($value['Lama Rawat']) ? $value['Lama Rawat']." Hari" : "" ; 
                $data[$counter]['Tanggal Masuk'] = date('d F Y H:i:s', strtotime($value['Tanggal Masuk']));
                $data[$counter]['Tanggal Keluar'] = ($value['Tanggal Keluar']) ? date('d F Y H:i:s', strtotime($value['Tanggal Keluar'])) : "" ;
                $data[$counter]['rowNum'] = $no;
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['Tanggal Keluar'])) {
            $tgl_keluar_range = explode(' - ', $yiiRestfulParams['advanced-filter']['Tanggal Keluar']);
            $tgl_awal = $tgl_keluar_range[0];
            $tgl_akhir = $tgl_keluar_range[1];

            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));

            $yiiRestfulParams['advanced-filter']['tgl_keluar_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_keluar_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['Tanggal Keluar']);
        }
        try {
            $path = Yii::getAlias("@download") . "/laporan-daftar-rawat.xlsx";
            // $query = [
            //     'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id"),
            // ];
            // $query = array_merge($query,$yiiRestfulParams);

            $response = $this->_restRm->get('lap-daftar-rawat/export-excel',[
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
}