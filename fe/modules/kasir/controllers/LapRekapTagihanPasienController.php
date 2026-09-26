<?php
/** Laporan Rekap Tagihan Pasien
 * Author : zn
 */
namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;

class LapRekapTagihanPasienController extends DocoController
{
    protected $_title = "Laporan Rekapitulasi Tagihan Pasien";
    protected $_module = 'kasir/lap-rekap-tagihan-pasien/';
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
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
        $restKasir = Yii::$app->docoRest->kasir;
        $listPenjamin = $this->getPenjamin();
        $listRuangan = $this->getRuangan();
        $listUnit = $this->getUnitPelayanan();

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
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
        $yiiRestfulParams['tanggal_mulai'] = $request->get('tanggalMulai', "date_range");
        $yiiRestfulParams['tanggal_akhir'] = $request->get('tanggalAkhir', null);
        $yiiRestfulParams['penjamin'] = $request->get('penjamin', null);
        $yiiRestfulParams['ruangan'] = $request->get('ruangan', null);
        $yiiRestfulParams['unit'] = $request->get('unit', null);

        try {
            $response = $this->_restKasir->get('lap-rekap-tagihan-pasien/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            //Set Nama Bulan
            for ($m=1; $m<=12; $m++) {
                $month[] = date('F', mktime(0,0,0,$m, 1, date('Y')));
            }
            $tmpPenjamin = ''; $valKunjungan = []; $valTotal = 0;
            $tmpPelayanan = ''; $valPelayanan = []; $valPelayananTotal = 0;
            $valGrandMonths = $valuePelayanan = $valuePenjamin = []; $valGrandTotal = 0;

            foreach ($body['response'] as $key => $value) {

                $no++;
                $value['rowNum'] = $no;
                $value['unit_pelayanan'] = isset($value['unit_pelayanan']) ? $value['unit_pelayanan'] : '-';
                $value['code_pelayanan'] = isset($value['unit_pelayanan']) ? $value['unit_pelayanan'] : '-';
                $value['penjamin'] = isset($value['penjamin']) ? $value['penjamin'] : '-';
                $value['ruangan'] = isset($value['ruangan']) ? $value['ruangan'] : '-';
                $value['bulan'] = isset($value['bulan']) ? $value['bulan'] : '-';
                $value['tahun'] = isset($value['tahun']) ? $value['tahun'] : '-';
                $value ['is_penjamin'] = FALSE;
                $value ['is_unit'] = FALSE;
                $value ['is_total'] = FALSE;
                $total = isset($value['total']) ? (float)$value['total'] : 0;
                $value['total'] = DocoHelpers::formatNumber($total);
                $valTotal += (float)$total;
                $valPelayananTotal += (float)$total;

                //Set Kolom Bulan
                foreach ($month as $val_month) {
                    
                    $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                    $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                    $value[$kunjungan_lama] = isset($value[$kunjungan_lama]) ? $value[$kunjungan_lama]:'';
                    $value[$kunjungan_baru] = isset($value[$kunjungan_baru]) ? $value[$kunjungan_baru]:'';

                    //Set Perhitungan Per Bulan untuk Penjamin
                    $valKunjungan[$kunjungan_lama] =isset($valKunjungan[$kunjungan_lama]) ? $valKunjungan[$kunjungan_lama] : 0;
                    $valKunjungan[$kunjungan_baru] =isset($valKunjungan[$kunjungan_baru]) ? $valKunjungan[$kunjungan_baru] : 0;
                    $valKunjungan[$kunjungan_lama] += ($value[$kunjungan_lama] == '') ? 0:$value[$kunjungan_lama];
                    $valKunjungan[$kunjungan_baru] += ($value[$kunjungan_baru] == '') ? 0:$value[$kunjungan_baru];

                     //Set Perhitungan Per Bulan untuk Unit Pelayanan
                     $valPelayanan[$kunjungan_lama] =isset($valPelayanan[$kunjungan_lama]) ? $valPelayanan[$kunjungan_lama] : 0;
                     $valPelayanan[$kunjungan_baru] =isset($valPelayanan[$kunjungan_baru]) ? $valPelayanan[$kunjungan_baru] : 0;
                     $valPelayanan[$kunjungan_lama] += ($value[$kunjungan_lama] == '') ? 0:$value[$kunjungan_lama];
                     $valPelayanan[$kunjungan_baru] += ($value[$kunjungan_baru] == '') ? 0:$value[$kunjungan_baru];
                }
                //Grouping By Penjamin
                if ($tmpPenjamin == ''){
                    $tmpPenjamin =  isset($value['penjamin']) ? $value['penjamin'] : '';
                }
                //Grouping By Unit Pelayanan
                if ($tmpPelayanan == ''){
                    $tmpPelayanan =   isset($value['unit_pelayanan']) ? $value['unit_pelayanan'] : '';
                }
                $nextPenjamin = isset($body['response'][$key+1]['penjamin']) ? $body['response'][$key+1]['penjamin'] : null;
                $next_unit = isset($body['response'][$key+1]['unit_pelayanan']) ? $body['response'][$key+1]['unit_pelayanan'] : null;
                if ( $no == count($body['response']) ||  $nextPenjamin != $tmpPenjamin ){
                    $data[]= $value;
                    //Create New Row
                    $valuePenjamin ['rowNum'] = '';
                    $valuePenjamin ['code_pelayanan'] = $value['unit_pelayanan'];
                    $valuePenjamin ['unit_pelayanan'] = 'SUB TOTAL '.$tmpPenjamin;
                    $valuePenjamin ['penjamin'] = '';
                    $valuePenjamin ['ruangan'] = '';
                    $valuePenjamin ['bulan'] = '';
                    $valuePenjamin ['tahun'] = '';
                    $valuePenjamin ['is_penjamin'] = TRUE;
                    $valuePenjamin ['is_unit'] = FALSE;
                    $valuePenjamin ['is_total'] = FALSE;
                    $valuePenjamin ['total'] = DocoHelpers::formatNumber($valTotal);
                    foreach ($month as $val_month) {
                        $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                        $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                        $valuePenjamin[$kunjungan_lama] = isset($valKunjungan[$kunjungan_lama]) ? $valKunjungan[$kunjungan_lama] : null;
                        $valuePenjamin[$kunjungan_baru] = isset($valKunjungan[$kunjungan_baru]) ? $valKunjungan[$kunjungan_baru] : null;
                    }

                    //Kosongkan row akumulasi kunjungan
                    $tmpPenjamin = '';
                    $valKunjungan = [];
                    $valTotal = 0;

                    $data[]= $valuePenjamin;
                    
                    if($no != count($body['response']) && $next_unit == $tmpPelayanan ){
                        continue;
                    }
                }

                
                if ($no == count($body['response']) ||  $next_unit != $tmpPelayanan ){

                        //Create New Row
                        $valuePelayanan ['rowNum'] = '';
                        $valuePelayanan ['unit_pelayanan'] = 'TOTAL '.$tmpPelayanan;
                        $valuePelayanan ['penjamin'] = '';
                        $valuePelayanan ['ruangan'] = '';
                        $valuePelayanan ['bulan'] = '';
                        $valuePelayanan ['tahun'] = '';
                        $valuePelayanan ['total'] = DocoHelpers::formatNumber($valPelayananTotal);
                        $valuePelayanan ['is_penjamin'] = FALSE;
                        $valuePelayanan ['is_unit'] = TRUE;
                        $valuePelayanan ['is_total'] = FALSE;
                        $valGrandTotal += $valPelayananTotal;
                        foreach ($month as $val_month) {
                            $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                            $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                            $valuePelayanan[$kunjungan_lama] = isset($valPelayanan[$kunjungan_lama]) ? $valPelayanan[$kunjungan_lama] : '';
                            $valuePelayanan[$kunjungan_baru] = isset($valPelayanan[$kunjungan_baru]) ? $valPelayanan[$kunjungan_baru] : '';

                            //Set Perhitungan Per Bulan untuk Grand Total
                            $valGrandMonths[$kunjungan_lama] =isset($valGrandMonths[$kunjungan_lama]) ? $valGrandMonths[$kunjungan_lama] : 0;
                            $valGrandMonths[$kunjungan_baru] =isset($valGrandMonths[$kunjungan_baru]) ? $valGrandMonths[$kunjungan_baru] : 0;
                            $valGrandMonths[$kunjungan_lama] += isset($valPelayanan[$kunjungan_lama]) ? $valPelayanan[$kunjungan_lama] :0;
                            $valGrandMonths[$kunjungan_baru] += isset($valPelayanan[$kunjungan_baru]) ? $valPelayanan[$kunjungan_baru] :0;
                        }

                        //Kosongkan row akumulasi kunjungan
                        $tmpPelayanan = '';
                        $valPelayanan = [];
                        $valPelayananTotal = 0;

                        $data[]= $valuePelayanan;
                        if($no != count($body['response']) && $next_unit == $tmpPelayanan){
                            $data[]= $value;
                        }
                        
                        continue;
                }

                $data[]= $value;
            }
            //Final Grand Total
            $value ['rowNum'] = '';
            $value ['unit_pelayanan'] = 'GRAND TOTAL';
            $value ['penjamin'] = '';
            $value ['ruangan'] = '';
            $value ['bulan'] = '';
            $value ['tahun'] = '';
            $value ['is_penjamin'] = FALSE;
            $value ['is_unit'] = FALSE;
            $value ['is_total'] = TRUE;
            $value ['total'] = DocoHelpers::formatNumber($valGrandTotal);
            foreach ($month as $val_month) {
                $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                $value[$kunjungan_lama] = isset($valGrandMonths[$kunjungan_lama]) ? $valGrandMonths[$kunjungan_lama] : 0;
                $value[$kunjungan_baru] = isset($valGrandMonths[$kunjungan_baru]) ? $valGrandMonths[$kunjungan_baru] : 0;
            }
            $data[]= $value;

            $result['data'] = $data;
            $result['recordsTotal'] = count($body['response']);
            $result['recordsFiltered'] = count($body['response']);
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionShowPopup()
    {
        $title = 'Laporan Rekapitulasi Tagihan Pasien';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['tanggal_mulai'] = $request->get('tanggalMulai', "date_range");
        $yiiRestfulParams['tanggal_akhir'] = $request->get('tanggalAkhir', null);
        $yiiRestfulParams['penjamin'] = $request->get('penjamin', null);
        $yiiRestfulParams['ruangan'] = $request->get('ruangan', null);
        $yiiRestfulParams['unit'] = $request->get('unit', null);
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "lap-rekap-tagihan-pasien/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Laporan Rekapitulasi Tagihan Pasien '.date('dmY').'.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('lap-rekap-tagihan-pasien/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    private function getPenjamin()
    {
        $data =[]; 
        $response = $this->_restKasir->get('lap-rekap-tagihan-pasien/get-penjamin');
        $result = json_decode($response->getBody(), true);

        if(!empty($result['response'])) {
            foreach ($result['response'] as $key => $value) {
                $data[$value['text']] = $value['text'];
            }
        }
        return $data;
    }

    private function getRuangan()
    {
        $data =[]; 
        $response = $this->_restKasir->get('lap-rekap-tagihan-pasien/get-ruangan');
        $result = json_decode($response->getBody(), true);

        if(!empty($result['response'])) {
            foreach ($result['response'] as $key => $value) {
                $data[$value['text']] = $value['text'];
            }
        }
        return $data;
    }

    private function getUnitPelayanan() 
    {
        return [
             DocoConstants::TITLE_RJ => DocoConstants::TITLE_RJ,
             DocoConstants::TITLE_RI => DocoConstants::TITLE_RI,
             DocoConstants::TITLE_RD => DocoConstants::TITLE_RD,
        ];
    }


}
