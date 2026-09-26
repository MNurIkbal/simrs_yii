<?php

namespace app\components\Traits;

use app\components\DHtml;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\Pelayanan\PelayananHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is trait worklist patient
 */
trait WorklistPatientTrait
{
    /**
     * @var String $restGeneral
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $restGeneral;
    public $_restKasir;

    /**
     * @var String $modulEndpoint
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $modulEndpoint;

    /**
     * This function will return index
     *
     * @return View
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionIndex()
    {
        $isDokter = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_MEDIS ? true : false;
        $config_tab_worklist = $this->guzzleExec($this->restGeneral, [
                    'url' => 'worklist/get-worklist-config',
                    'method' => 'get',
          ]);

        $filters = $this->guzzleExec($this->restGeneral, [
            'url' => 'worklist/filters',
            'payload' => [
                'query' => [
                    'types' => [
                        'carabayar',
                        'ruangan',
                        'jeniskasuspenyakit',
                        'kelaspelayanan',
                        'status_periksa',
                        'status_periksa_rajal',
                        'status_periksa_ranap',
                        'status_periksa_igd',
                        'status_periksa_ot',
                        'status_periksa_mcu',
                        'status_periksa_ol'
                    ],
                    'isDokter' => $isDokter,
                ]
            ]
        ]);

        // jika tidak ada, set array kosong aja = []
        $defaultSorting = $this->modulEndpoint == DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL] 
                      ?  [[10, "desc"], [2, "asc"]] // untuk rajal, default sorting nomor antrian
                      : $this->modulEndpoint == DocoConstants::PARAM_DFTR[DocoConstants::WS_RANAP] 
                        ? [] : [[10, 'desc']]; // untuk ranap, default sorting tidak ada, selain itu hanya tgl pendaftaran
                      
        $konfig = $this->guzzleExec($this->restGeneral, [
            'url' => 'worklist/get-konfig-worklist-filter',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'kode' => DocoConstants::WORKLIST_KONFIG_URUTAN_PERIKSA
                ]
            ]
        ]);

        return $this->render('//worklist-patient/index', [
            'type' => $this->modulEndpoint,
            'dropdown' => $filters,
            'config_tab_worklist' => $config_tab_worklist,
            'is_dokter' => $isDokter,
            'defaultSorting' => $defaultSorting
        ]);
    }

    /**
     * This function will return data table
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDatatable()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $tabType = Yii::$app->request->get('tabType', null);
        $isLunas = Yii::$app->request->get('is_lunas', null);
        $isStopAkomodasi = Yii::$app->request->get('is_stopakomodasi', null);
        $isKonsul = Yii::$app->request->get('is_konsul', null);
        $isPasienTitipan = Yii::$app->request->get('is_pasientitipan', null);
        $isSoap = Yii::$app->request->get('is_isisoap', null);
        $isNosep = Yii::$app->request->get('is_nosep', null);
        if ($isLunas && $isStopAkomodasi) {
            $payload['advanced-filter']['is_stopakomodasi'] = filter_var($isStopAkomodasi, FILTER_VALIDATE_BOOLEAN);
            $payload['advanced-filter']['is_lunas'] = filter_var($isLunas, FILTER_VALIDATE_BOOLEAN);
        }
        if ($isKonsul){
            $payload['advanced-filter']['is_konsul'] = filter_var($isKonsul, FILTER_VALIDATE_BOOLEAN);
        }
        if($isPasienTitipan){
            $payload['advanced-filter']['is_pasientitipan'] = filter_var($isPasienTitipan, FILTER_VALIDATE_BOOLEAN);
        }
        if($isSoap){
            $payload['advanced-filter']['is_isisoap'] = filter_var($isSoap, FILTER_VALIDATE_BOOLEAN);
        }
        if($isStopAkomodasi){
            $payload['advanced-filter']['is_stopakomodasi'] = filter_var($isStopAkomodasi, FILTER_VALIDATE_BOOLEAN);
        }
        $modulAccessed = $this->modulEndpoint;
        $userIdentity = Yii::$app->session->get('user_identity');
        $isDokter = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_MEDIS ? true : false;
        $response = $this->guzzleExec($this->restGeneral, [
            'url' => 'worklist/datatable',
            'method' => 'get',
            'payload' => [
                'query' => array_merge($payload, compact('tabType', 'modulAccessed','userIdentity','isDokter','isNosep'))
            ]
        ]);

        foreach ($response['data'] as $key => $record) {
            $today = strtotime(date('Y-m-d'));
            $tgl_pendaftaran = strtotime(date('Y-m-d', strtotime($record['tgl_pendaftaran'])));
            $response['data'][$key]['is_today'] = $today == $tgl_pendaftaran ? '' : 'disabled';
            $no_telepon = trim($record['no_telepon_pasien']);
            $response['data'][$key]['primary'] = $this->helper->encrypt($record['pendaftaran_id']);
            $response['data'][$key]['origin_jenis'] = $record['jenis'];
            $response['data'][$key]['is_stopakomodasi'] = isset($record['is_stopakomodasi']) ? $record['is_stopakomodasi'] : false;
            $response['data'][$key]['jenis'] = strtolower($record['jenis']) == 'mcu' ? 'RJ' : $record['jenis'];
            $response['data'][$key]['action'] = DHtml::worklistPatientBtn($response['data'][$key],$response['batalStopAkomodasi']);
            $response['data'][$key]['no_telepon'] = !empty($no_telepon) ? $no_telepon : (!empty($record['no_mobile_pasien']) ? $record['no_mobile_pasien'] : ' - ');
            $response['data'][$key]['nama_pegawai'] = $this->formatKonsulpoliDokter($record['nama_pegawai']);
            $response['data'][$key]['konsulpoli_dokter_nama'] = $this->formatKonsulpoliDokter($record['konsulpoli_dokter_nama']);
            $response['data'][$key]['alergi_catatan'] = $this->formatAlergiCatatanPenting($record['riwayat_alergi'], $record['catatanpenting_pasien']);

        }

        return $response;
    }

    public function actionGetRuanganWorklist()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $workspace = Yii::$app->session->get('active_workspace');
            $ruangan_id = $workspace['ruangan_id'];
            $ruangan_name = $workspace['ruangan_name'];
            $result['results'][] = [
                'value' => $ruangan_id,
                'text' => $ruangan_name
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

    public function actionGetKonfigWorklist($kode)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];

        try {
            $response = $this->guzzleExec($this->restGeneral, [
                'url' => 'worklist/get-konfig-worklist-filter',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'kode' => $kode
                    ]
                ]
            ]);
            return $response;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function formatKonsulpoliDokter($nama_dokter = NULL) {
        $formatDokter = '-';
        if(!empty($nama_dokter)) {
            $raw = explode('##', $nama_dokter);
            $twoFirst = [];
            $lastData = [];

            for ($i = 0; $i < count($raw); $i++) {
                if($i < 2) {
                    $twoFirst[] = $raw[$i];
                } else {
                    $lastData[] = $raw[$i];
                }
            }

            $lastDataParse = implode('<br>', $lastData);
            $lastDataLainnya = '<br><font color=\'#1160f2\'><b>+ ' . count($lastData) . ' Dokter Lainnya</b></font>';

            $dataHtmlStatus = (count($raw) > 2) ? true : false;
            $dataTitle = (count($raw) > 2) ? $lastDataParse : '';

            $dataLainnya = (count($raw) > 2) ? $lastDataLainnya : '';

            $formatDokter = '
            <span
                rel="tooltip"
                data-toggle="tooltip"
                data-trigger="hover"
                data-placement="bottom"
                data-html="'.$dataHtmlStatus.'"
                title="'.$dataTitle.'">
                    '.implode('<br>', $twoFirst).'
                    '.$dataLainnya.'
            </span>';
        }

        return $formatDokter;
    }

    public function formatAlergiCatatanPenting($riwayat_alergi = '', $catatan_penting = '')
    {
        $text_alergi = 'Alergi : - <br>';
        $text_catatan_penting = '<br>Catatan Penting : - ';
        if(!empty($riwayat_alergi)) {
            $text_alergi = 'Alergi : <br>'. $riwayat_alergi;
        }

        if(!empty($catatan_penting)){
            $explode = explode("\n", $catatan_penting);
            $array = [];
            for ($i = 0; $i < count($explode); $i++) {
                $array[] = $explode[$i];
            }
            $catatan_penting = implode('<br>', $array);
            $text_catatan_penting = '<br>Catatan Penting : <br>'.htmlspecialchars($catatan_penting);
        }
        $text_merge = $text_alergi.''.$text_catatan_penting;
        $formatAlergi = '<a
        rel="tooltip"
        data-toggle="tooltip"
        data-placement="right"
        data-html="true"
        style="text-align: left;"
         title="'.$text_merge.'">
        <i class="fa fa-info-circle top" style="color: #FC8338;"></i>
        </a>';

        return $formatAlergi;
    }

    /**
     * This function will return list of all source data filters
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionFilters($type)
    {
        $response = $this->guzzleExec($this->restGeneral, [
            'url' => 'worklist/filters',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'types' => $type,
                    'term' => Yii::$app->request->get('term'),
                    'additionalPayload' => Yii::$app->request->get('additionalPayload', []),
                    'page' => Yii::$app->request->get('page', 1),
                ]
            ]
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response[$type]);
    }

    public function actionCetakRincian($id, $instalasi_id){
        $path = Yii::getAlias("@download") . "/cetak-rincian-{$id}.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');

        $request = Yii::$app->request;
            // $id = $request->get('id');
            $url = "tagihan-pasien/cetak-rincian";

            if(!is_numeric($id)) {
                $id = DocoHelpers::decrypt($id);
            }

        try {

            $response = $this->_restKasir->get($url, [
                'query' => [
                    'id' => $id,
                    'nama_pegawai' => $userIdentity['nama_pegawai'],
                    'instalasi_id' => $instalasi_id,
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakDetailRincian($id, $instalasi_id){
        $path = Yii::getAlias("@download") . "/cetak-rincian-{$id}.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');

        $request = Yii::$app->request;
           // $id = $request->get('id');
            $url = "tagihan-pasien/cetak-detail-rincian";

            if(!is_numeric($id)) {
                $id = DocoHelpers::decrypt($id);
            }

        try {

            $response = $this->_restKasir->get($url, [
                'query' => [
                    'id' => $id,
                    'nama_pegawai' => $userIdentity['nama_pegawai'],
                    'instalasi_id' => $instalasi_id,
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionShowPopup($id, $instalasi_id, $modul)
    {
        $title = 'Cetak Detail Rincian Tagihan';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $userIdentity = Yii::$app->session->get('user_identity');
        $nama_pegawai = isset($userIdentity['nama_pegawai']) ? $userIdentity['nama_pegawai'] : null;
        $_GET['nama_pegawai'] = $nama_pegawai;
        $_GET['randString'] = $randString;
        $get = $request->get();
        Yii::$app->session->setFlash($randString, $get);
        return $this->renderAjax('//worklist-patient/_modal', get_defined_vars());
    }

    public function actionProcessSync($randString,$id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::error($randString);
        Yii::error($id);
        $payload = Yii::$app->session->getFlash($randString);
        $payload['id'] = DocoHelpers::encrypt($id);
        return $this->guzzleExec($this->_restKasir, [
            'url' => "tagihan-pasien/cetak-detail-rincian",
            'payload' => [
                'query' => $payload,
            ],
        ]);
    }

    public function actionDownloadInvoice()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $path = Yii::getAlias("@download").'/'.$fileName;
        $response = $this->_restKasir->get('tagihan-pasien/download-invoice',
        [
            'query' => [
                'fileName' => $fileName,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);

        return DocoHelpers::previewPdf($path);
    }

}
