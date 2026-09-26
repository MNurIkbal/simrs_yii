<?php 
// Author : Budi

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
use kartik\mpdf\Pdf;

class LapKunjunganController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-kunjungan/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Kunjungan Pasien Rumah Sakit');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get('lap-kunjungan/generate-api');
        $api = json_decode($api->getBody(), True);

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $jenisIdentitas = $this->populateJenisIdentitas(false);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-kunjungan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // dump($body);die;
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['tgl_pendaftaran'] = DocoHelpers::display_label($value['tgl_pendaftaran'], true, 
                    date('d M Y', strtotime($value['tgl_pendaftaran'])));

                $value['rowNum'] = $no;
                $value['primary'] = $value['no_rekam_medik'];
                $value['data_pasien'] = $value['no_pendaftaran'] . '<br>' . $value['no_rekam_medik'] . ' ('. $value['jenis_kelamin'].')'. '<br>'. $value['nama_pasien'];
                $value['tgl_lahir'] = $value['tanggal_lahir'] . '<br>' . $value['umur'];
                $value['carabayar_penjamin'] = $value['carabayar_nama'] . '<br>' . wordwrap($value['penjamin_nama'],40,"<br>\n");
                $value['instalasi_ruangan'] = $value['instalasi_nama'] . '<br>' . $value['ruangan_nama'];
                $value['enc_pendaftaran_id'] = $primaryKey;
                $value['admisi'] = (is_null($value['pasienadmisi_id'])) ? DocoHelpers::encrypt(0) : DocoHelpers::encrypt($value['pasienadmisi_id']);
                $value['alamat_pasien'] = wordwrap($value['alamat_pasien'],40,"<br>\n");
                $value['diagnosa_utama'] = wordwrap($value['diagnosa_utama'],40,"<br>\n");
                $value['diagnosa_penyerta'] = wordwrap($value['diagnosa_penyerta'],40,"<br>\n");
                $value['no_telepon_pasien'] = isset($value['no_telepon_pasien'])? $value['no_telepon_pasien'] : $value['no_mobile_pasien'];
                $value['asalrujukan_nama'] = isset($value['asalrujukan_nama'])? $value['asalrujukan_nama'] : null;
                $value['nama_perujuk'] = isset($value['nama_perujuk'])? $value['nama_perujuk'] : null;
                $value['rujukandari_nama'] = isset($value['rujukandari_nama'])? $value['rujukandari_nama'] : null;
                $noIdentitas = '';
                if (!empty($value['additional_pasien'])) {
                    $pasienAdds = json_decode($value['additional_pasien'], JSON_UNESCAPED_SLASHES);
                    if (!empty($pasienAdds) && isset($pasienAdds[0]) && is_array($pasienAdds[0])) {
                        foreach($pasienAdds as $adds) {
                            if (isset($jenisIdentitas[$adds['jenisidentitas']])) {
                                if (!empty($noIdentitas)) {$noIdentitas .= '<br/>';}
                                $noIdentitas .= $jenisIdentitas[$adds['jenisidentitas']] . ' - ' . $adds['no_identitas_pasien'];
                            }
                        }
                    }
                }
                $value['no_identitas'] = $noIdentitas;

                if (!empty($value['diagnosa_penyerta'])) {
                    $diagnosa_penyerta = '';
                    $temp = explode('$', $value['diagnosa_penyerta']);

                    if (count($temp) > 1) {
                        foreach ($temp as $k => $val) {
                            $diagnosa_penyerta = ($k == 0) ? $diagnosa_penyerta.$val : $diagnosa_penyerta.'<br><br>'.$val;
                        }

                        $value['diagnosa_penyerta'] = $diagnosa_penyerta;
                    } else {
                        $value['diagnosa_penyerta'] = $temp[0];
                    }
                }

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('lap-kunjungan/get-ruangan-by?id='.$parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_id'], 
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('lap-kunjungan/get-penjamin-by?id='.$parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['penjamin_id'], 
                    'name' => $value['penjamin_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPegawai()
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

        try {
            $response = $this->_restRm->get('lap-kunjungan/list-pegawai?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['nama_pegawai'],
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);
                
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionSearch($tipe = NULL)
    {        
        $jabatan = $this->_restRm->get('lap-kunjungan/list-jabatan');
        $jabatan = json_decode($jabatan->getBody(), True);

        return $this->renderPartial('search', get_defined_vars());
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            //$path = Yii::getAlias("@download") . "/lap-kunjungan.xlsx";
            $response = $this->_restRm->get('lap-kunjungan/export-excel',[
                'query' => $yiiRestfulParams,
                //'save_to' => $path,
            ]);
            $response = json_decode($response->getBody(), True);
            $results = $response['response']['result'];
            $uid = DocoHelpers::encrypt($results['uid']);

            return $this->redirect([Yii::$app->homeUrl.'/rm/lap-kunjungan', 'uid' => $uid]);
            //return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcelSerconn()
    {
        $request = Yii::$app->request;
        $title = 'Excel Laporan Kunjungan Pasien Rumah Sakit';
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "lap-kunjungan/sync-export-excel",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $uid = $request->get('uid');
        $uid = DocoHelpers::decrypt($uid);
        try {
            $response = $this->_restRm->get('lap-kunjungan/get-excel-url?uid='.$uid);
            $response = json_decode($response->getBody(), True);
            $results = $response['response']['result'];

            return $results;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $type_icd = $request->get('type_icd');
        $term = $request->get('term');
        try {
            $response = $this->_restRm->get('allow/get-icd',[
                'query' => [
                    'type' => $type_icd,
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['diagnosa_id'],
                    'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_namalainnya'],
                ];
            }
        } catch (RequestException $e) {
            $data = [];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('filename', null);

        $fileDownloads = 'Laporan Kunjungan Pasien Rumah Sakit.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('lap-kunjungan/download-file',
        [
            'query' => [
                'no_request' => $no_request,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    protected function populateJenisIdentitas($options = false)
    {
        $res = [];
        $tempResp = $this->_restRm->get('allow/get-look-up', ['query' => ['params' => 'jenis_identitas']]);
        if ($tempResp->getStatusCode() == 200) {
            $respBody = json_decode($tempResp->getBody(), true);
            if ($options) {
                $res = $respBody['response'];
            } else {
                foreach ($respBody['response'] as $arrs) {
                    $res[$arrs['lookup_id']] = $arrs['lookup_name'];
                }
            }
        }
        return $res;
    }
}