<?php 

/**
 * @author Randy Vianda Putra
 * @todo Informasi Formulir So
 * @copyright 17 January 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\InformasiForm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanStokController extends DocoController
{
    
    protected $_title = "Laporan Stok";
    protected $_module = '/apotek/laporan-stok';
    protected $_restApotek;
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restKasir = Yii::$app->docoRest->kasir;
    }

    
    public function actionOpname()
    {
        $title = $this->_title . ' Opname';
        $model = new InformasiForm;
        $instalasi = [];
        $ruangan = [];
        // $cache = Yii::$app->cache;
        // $data = $this->getResep();
        // $temp_cache = [];
        // $no_resep = $cache->get('no_resep');
        // if ($no_resep === false) {
        //     $no = 0;
        //     foreach ($data['data_resep'] as $key => $value) {
        //         $list_cache[$value['noresep']] = [
        //             'reseptur_id' => $value['reseptur_id'],
        //             'no_resep' => $value['noresep'],
        //             'tgl_resep' => $value['tglresep'],
        //             'nama_pasien' => $value['nama_pasien'],
        //             'no_pendaftaran' => $value['no_pendaftaran'],
        //             'nama_dokter' => $value['nama_pegawai'],
        //             'instalasi' => $value['instalasireseptur_nama'],
        //             'ruangan' => $value['ruangan_nama']
        //         ];
        //         $no++;
        //     }
        //     if ($no) {
        //         $listResep = json_encode($list_cache);
        //         Yii::$app->cache->set('no_resep', $listResep, 30);
        //     }
        // }
        // $no_resep = $cache->get('no_resep');

        return $this->render('opname', get_defined_vars());
    }
    public function actionGetDataStokopname()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];        
        try {
            $response = $this->_restApotek->get('lap-stok-opname/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];                    
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;                
                $value['selisih'] = $value['totalharga_fisik'] - $value['totalharga_sistem'];
                $value['totalharga_fisik'] = 'Rp. '.number_format($value['totalharga_fisik'],0,',','.');
                $value['totalharga_sistem'] = 'Rp. '.number_format($value['totalharga_sistem'],0,',','.');
                $value['selisih'] = 'Rp. '.number_format($value['selisih'],0,',','.');
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    public function actionGetDataNostokopname()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'lap-stok-opname/data-nostokopname',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                   
            foreach ($body['response'] as $key => $value) {
                
                $data[] = ['id'=>$value['nostokopname'],'text'=>$value['nostokopname']];
                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionPrintOpname()
    {
        $title = 'Print ' . $this->_title . ' Opname';

        return $this->render('print-opname', get_defined_vars());
    }
    // Beware Section Data Selection
    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $result = [];
        try {
            $response = $this->_restApotek->get('lap-stok-opname/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            foreach ($body['response']['data'] as $key => $value) {
                // Data Selection

                $value['selisih'] = $value['totalharga_fisik'] - $value['totalharga_sistem'];
                $value['totalharga_fisik'] = 'Rp. '.number_format($value['totalharga_fisik'],0,',','.');
                $value['totalharga_sistem'] = 'Rp. '.number_format($value['totalharga_sistem'],0,',','.');
                $value['selisih'] = 'Rp. '.number_format($value['selisih'],0,',','.');

                $newValue = [];
                $newValue[\Yii::t('fe', 'Tanggal Mutasi')] = $value['tglstokopname'];
                $newValue[\Yii::t('fe', 'No Stok Opname')] = $value['nostokopname'];
                $newValue[\Yii::t('fe', 'Harga Netto Sistem')] = $value['totalharga_sistem'];
                $newValue[\Yii::t('fe', 'No rekam medis')] = $value['totalharga_fisik'];
                $newValue[\Yii::t('fe', 'Selisih')] = $value['selisih'];
                $result[$key] = $newValue;
            }
        } catch (RequestException $e) {
        } catch (\Exception $e) {
        }

        // Directory Creation
        $header = array(
            Yii::t("fe", "Tanggal Mutasi") => (@$yiiRestfulParams['advanced-filter']['tglstokopname']),
            Yii::t("fe", "No Stok Opname") => (@$yiiRestfulParams['advanced-filter']['nostokopname']),            
        );

        $filePath = DocoHelpers::exportExcel($this->_title.' Opname', $result, $header, array(
            "uploadPath" => Yii::getAlias("@uploads"), // Optional, default folder "uploads" di root app & root advanced app
        ));

        if (file_exists($filePath))
            return \Yii::$app->response->sendFile($filePath);
        else
            throw new \yii\web\NotFoundHttpException();
    }

    public function actionObatAlkes()
    {
        $title = $this->_title . ' Obat Alkes';
        $model = new InformasiForm;
        $instalasi = [];
        $ruangan = [];
        // $cache = Yii::$app->cache;
        // $data = $this->getResep();
        // $temp_cache = [];
        // $no_resep = $cache->get('no_resep');
        // if ($no_resep === false) {
        //     $no = 0;
        //     foreach ($data['data_resep'] as $key => $value) {
        //         $list_cache[$value['noresep']] = [
        //             'reseptur_id' => $value['reseptur_id'],
        //             'no_resep' => $value['noresep'],
        //             'tgl_resep' => $value['tglresep'],
        //             'nama_pasien' => $value['nama_pasien'],
        //             'no_pendaftaran' => $value['no_pendaftaran'],
        //             'nama_dokter' => $value['nama_pegawai'],
        //             'instalasi' => $value['instalasireseptur_nama'],
        //             'ruangan' => $value['ruangan_nama']
        //         ];
        //         $no++;
        //     }
        //     if ($no) {
        //         $listResep = json_encode($list_cache);
        //         Yii::$app->cache->set('no_resep', $listResep, 30);
        //     }
        // }
        // $no_resep = $cache->get('no_resep');

        return $this->render('obat-alkes', get_defined_vars());
    }


}