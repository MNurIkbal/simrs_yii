<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\rm\controllers;

use Yii;
use Mpdf\Mpdf;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

class InfDaftarPasienController extends DocoController
{
    protected $allowAction = ['*'];
    protected $title = 'Informasi Daftar Pasien';
    protected $_restRm;
    protected $_restPendaftaran;

    public function init()
    {
        parent::init();
        
    	$this->_restRm = Yii::$app->docoRest->rm;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function actions()
    {
        return [
            'get-data' => 'Doco\rm\actions\GetDataDaftarPasienAction',
            'update-proses' => 'Doco\rm\actions\UpdateProsesAction',
            'stream-pendaftaran' => 'Doco\rm\actions\StreamPendaftaranAction',
            'get-sound-file' => 'Doco\rm\actions\GetSoundFileAction'
        ];
    }

	public function actionIndex()
	{
		return Yii::$app->docoPlugin->execute($this,'inf_daftar_pasien_index');
	}   

    public function actionPrintGelangPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-gelang-pasien.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id',null);
        $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);

        $post = ['pendaftaran_id'=>$decryptPendaftaranId];
        $response = $this->_restRm->post('inf-daftar-pasien/print-gelang-pasien',
            [
                'form_params' => $post,
                'save_to' => $path
            ]
        );

        return DocoHelpers::previewPdf($path);
    }

    public function actionPrintLabelPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-label-pasien.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id',null);
        $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
        $post = ['pendaftaran_id'=>$decryptPendaftaranId];
        $response = $this->_restRm->post('inf-daftar-pasien/print-label-pasien',
            [
                'form_params' => $post,
                'save_to' => $path
            ]
        );
        $body = json_decode($response->getBody(), True);
        return DocoHelpers::previewPdf($path);
    }

    public function actionPrintStatusPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-status-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::setDecryptIdFromString($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::setDecryptIdFromString($pendaftaran_id);
            $no_pendaftaran = $request->get('no_pendaftaran',null);
            $instalasi_id = $request->get('instalasi_id', null);
            $isFrom = Yii::$app->request->get('is_from', 'rekam_medik');
            switch ($instalasi_id) {
                case DocoConstants::INSTALASI_ID_RJ:
                    $param = "rajal";
                    break;
                case DocoConstants::INSTALASI_ID_RI:
                    $param = "ranap";
                    break;
                case DocoConstants::INSTALASI_ID_RD:
                    $param = "igd";
                    break;
                case DocoConstants::INSTALASI_MCU:
                    $param = "mcu";
                    break;
                default:
                    $param = "rajal";
                    break;
            }
            $is_rsvp = !empty($pendaftaran_id) ? 0 : 1;
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param, 'is_from' => 'rekam_medik', 'is_rsvp' => $is_rsvp, 'no_pendaftaran' => $no_pendaftaran];
            if(Yii::$app->report->enabled){
                $urlReport = 'print-tracer-rm';

                Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $post,
                    'manualRender'=>function() use($post,$path){
                        $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-status-pasien',[
                            'form_params' => $post,
                            'save_to' => $path
                        ]);

                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-status-pasien',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintVoucherBerkas()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-status-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $instalasi_id = $request->get('instalasi_id', null);
            switch ($instalasi_id) {
                case DocoConstants::INSTALASI_ID_RJ:
                    $param = "rajal";
                    break;
                case DocoConstants::INSTALASI_ID_RI:
                    $param = "ranap";
                    break;
                case DocoConstants::INSTALASI_ID_RD:
                    $param = "igd";
                    break;
                case DocoConstants::INSTALASI_MCU:
                    $param = "mcu";
                    break;
                default:
                    $param = "rajal";
                    break;
            }
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-voucher-berkas',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPilihJumlahCetakan()
    {
        $title = 'Pilih jumlah label';
        $request = Yii::$app->request;
        $jenis = $request->get('jenis');
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $no_pendaftaran = DocoHelpers::encrypt($request->get('no_pendaftaran'));

        $response = Yii::$app->docoRest->pendaftaran->request('GET', 'inf-daftar-sepuluh-terakhir/get-template-label', [
            'form_params'=>[],
            'query'=>[]
        ]);
        $body = json_decode($response->getBody(),TRUE);
        $result = $body['response'];
        $jenisTemplate = 'kn';
        $jumlahCetakan = 11;
        
        if ($result) {
            $jenisTemplate = $result['jenis_template'];
            $jumlahCetakan = $result['jumlah_template'];
        }

        return $this->renderPartial('_modal_jumlah_cetakan', get_defined_vars());
    }

    public function actionPrintLabelPasienBaru()
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id', null);
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $no_pendaftaran = $request->get('no_pendaftaran', null);
        $jumlah = $request->get('jumlah', null);
        $template = $request->get('template', 'default');
        $decryptPasienId = DocoHelpers::setDecryptIdFromString($pasien_id);
        $decryptPendaftaranId = DocoHelpers::setDecryptIdFromString($pendaftaran_id);
        $decryptNoPendaftaran = DocoHelpers::setDecryptIdFromString($no_pendaftaran);
        $post = [
            'pasien_id' => $decryptPasienId,
            'pendaftaran_id' => $decryptPendaftaranId,
            'no_pendaftaran' => $decryptNoPendaftaran,
            'jumlah' => $jumlah,
            'template' => $template
        ];

        if($template == 'ad' || $template = 'new_bg') {
            $orientation = 'P';
        } else {
            $orientation = 'L';
        }

        $rest = $this->_restRm->post('inf-daftar-pasien/print-label-pasien-baru',
        // $rest = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-label-pasien-baru',
            [
                'form_params' => $post
            ]
        );
        $result = json_decode($rest->getBody(), true);
        // dump($result);die;
        $body = $result['response']['body'];
        $mpdf = new Mpdf([
            'orientation' => $orientation,
            'tempDir' => Yii::getAlias("@download"),
            'setAutoTopMargin' => 'pad'
        ]);

        $mpdf->AddPageByArray([
            'margin-left' => 12,
            'margin-right' => 17,
            'margin-top' => 1,
            'margin-bottom' => 0,
        ]);

        $mpdf->WriteHTML($body);
        $mpdf->Output();
    }

    public function actionPrintBelumProses(){
        //Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tglmasukpenunjang_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tglmasukpenunjang_range[0];
            $tgl_akhir = $tglmasukpenunjang_range[1];

        } else {
            $tgl_awal = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }
        
        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);


        $path = Yii::getAlias("@download") . "/print-pasien-belum-proses.pdf";
        $response = $this->_restRm->post('inf-daftar-pasien/print-belum-proses?'.http_build_query($yiiRestfulParams),
            [
        
                'save_to' => $path
            ]
        );
        $body = json_decode($response->getBody(), True);
        // dump($body);
        // die();
        return DocoHelpers::previewPdf($path);
    }

    public function actionFilters() 
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $additionalPayload = $request->get('additionalPayload', []);
        $response = $this->guzzleExec($this->_restRm, [
            'url' => 'inf-daftar-pasien/filters',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'additionalPayload' => $additionalPayload
                ]
            ],
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }
}