<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace app\modules\pendaftaran\components\traits;

use Yii;
use yii\web\Response;
use yii\web\UploadedFile;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Mpdf\Mpdf;

use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\PasienAdmisiForm;
use app\modules\pendaftaran\models\PjpasienForm;
use app\modules\pendaftaran\models\KeluargaPasienForm;
use app\modules\pendaftaran\models\PasienBpjsForm;
use app\modules\pendaftaran\models\MultiCarabayarForm;
use app\modules\pendaftaran\models\PenanggungBiayaForm;
use app\modules\pendaftaran\models\EditPendaftaranForm;
use app\modules\pendaftaran\components\Lookup;
use GuzzleHttp\Exception\RequestException;

use app\components\Services\Contracts\BpjsInterface;

use app\modules\pendaftaran\models\TipePasienForm;

trait PendaftaranTrait
{
    protected $_restPendaftaran;
    protected $pendaftaranTipe = '';
    protected $default_kp = '';
    public $bpjsService;

    public function __construct($id, $module, $config = [], BpjsInterface $bpjsService)
    {
        $this->bpjsService = $bpjsService;
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $default_kp = 69;
    }

    public function getDataApi(
        $instalasi_id = null,
        $default = null,
        $pendaftaranol_id = null,
        $loket_id = null,
        $janji_id = null,
        $pendaftaran_id = null
    )
    {
        try {
            $instalasi_id = is_array($instalasi_id) ? json_encode($instalasi_id) : $instalasi_id;
            $config_app = Yii::$app->cache->get('app');
            $propinsi_id = $config_app['propinsi_id'];
            $kabupaten_id = $config_app['kabupaten_id'];
            $response = $this->_restPendaftaran->get('allow/get-api',[
                'query' => [
                    'instalasi_id' => $instalasi_id,
                    'default' => $default,
                    'pendaftaranol_id' => $pendaftaranol_id,
                    'loket_id' => $loket_id,
                    'janji_id' => $janji_id,
                    'pendaftaran_id' => $pendaftaran_id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $lookup = $master = $optionsProv = $ruangan = $carabayar = $asalrujukan = $jeniskasus = $instalasi = $kode_bpjs= $optionsRuangan = [];
            $rujukanDari = [];
            $loket_instalasi = null;

            if (isset($body['response']['loket_instalasi'])) {
                $loket_instalasi = $body['response']['loket_instalasi'];
            }

            if (isset($body['response']['rujukan_dari'])) {
                $rujukanDari = $body['response']['rujukan_dari'];
            }

            if(!empty($body['response']['master'])) {
                $master = $body['response']['master'];
            }

            if(!empty($body['response']['lookup'])) {
                $lookup = $body['response']['lookup'];
            }

            if(isset($body['response']['default_alamat'])) {
                $depdrop = $body['response']['default_alamat'];

                if(isset($depdrop['provinsi_id']) && !empty($depdrop['provinsi_id'])){
                    $propinsi_id = $depdrop['provinsi_id'];
                }

                if(isset($depdrop['kabupaten_id']) && !empty($depdrop['kabupaten_id'])){
                    $kabupaten_id = $depdrop['kabupaten_id'];
                }
            }

            if(!empty($body['response']['master']['propinsi'])) {
                foreach ($body['response']['master']['propinsi'] as $key => $propinsi) {
                    $optionsProv[$propinsi['propinsi_id']] = ['data-kode'=>$propinsi['kode_propinsi']];
                }
            }
            if (!empty($body['response']['ruangan'])) {
                $ruangan = ArrayHelper::map($body['response']['ruangan'], 'ruangan_id','ruangan_nama');
                $kode_bpjs = ArrayHelper::map($body['response']['ruangan'], 'ruangan_id','kode_ruangan_bpjs');
                foreach ($body['response']['ruangan'] as $key => $ruangan_bpjs) {
                    $kode_ruangan_bpjs = isset($ruangan_bpjs['kode_ruangan_bpjs']) ? $ruangan_bpjs['kode_ruangan_bpjs'] : 0;
                    $optionsRuangan[$ruangan_bpjs['ruangan_id']] = ['data-kode-bpjs'=> $kode_ruangan_bpjs];
                }
            }

            $carabayarOptions = $carabayarWtihoutBpjs = [];
            if (!empty($body['response']['cara_bayar'])) {
                $carabayar = $body['response']['cara_bayar'];
                $payment = [];
                foreach ($carabayar as $key => $value) {
                    $carabayarOptions[$value['carabayar_id']] = ['data-id' => $value['groupcarabayar_id']];
                    $payment[$value['carabayar_id']] = $value['carabayar_nama'];
                    if (DocoConstants::INSTALASI_MCU === $instalasi_id && $value['groupcarabayar_id'] === DocoConstants::GROUP_BPJS) {
                        unset($payment[$value['carabayar_id']]);
                    }
                    if(!$value['groupcarabayar_id'] === DocoConstants::GROUP_BPJS) {
                        $carabayarWtihoutBpjs[$value['carabayar_id']] = ['data-id' => $value['groupcarabayar_id']];
                    }
                }
                $carabayar = $payment;
            }

            if (!empty($body['response']['asal_rujukan'])) {
                $asalrujukan = $body['response']['asal_rujukan'];
            }

            if (!empty($body['response']['kelas_pelayanan'])) {
                $kelaspelayanan = ArrayHelper::map($body['response']['kelas_pelayanan'], 'kelaspelayanan_id','kelaspelayanan_nama');
            }

            if (!empty($body['response']['jeniskasus'])) {
                $jeniskasus = ArrayHelper::map($body['response']['jeniskasus'], 'jeniskasuspenyakit_id','jeniskasuspenyakit_nama');
            }

            $klasifikasiKamar = [];
            if (!empty($body['response']['klasifikasiKamar'])) {
                $klasifikasiKamar = ArrayHelper::map($body['response']['klasifikasiKamar'], 'klasifikasikamar_id','klasifikasikamar_nama');
            }
            $instalasi = (!empty($body['response']['instalasi'])
                                    ? ArrayHelper::map($body['response']['instalasi'], 'instalasi_id', 'instalasi_nama') : []);

            $default_asal_rujukan = $body['response']['default_asal_rujukan'];
            $default_jenis_penyakit = $body['response']['default_jenis_penyakit'];
            $default_penjamin = $body['response']['default_penjamin'];
            $result = [
                'response' => $body['response']['lookup'],
                'lookup' => $lookup,
                'master' => $master,
                'ruangan' => $ruangan,
                'ruangan_bpjs' => $kode_bpjs,
                'cara_bayar' => $carabayar,
                'carabayarOptions' => $carabayarOptions,
                'asal_rujukan' => $asalrujukan,
                'kelas_pelayanan' => $kelaspelayanan,
                'jeniskasus' => $jeniskasus,
                'penjamin' => $body['response']['penjamin'],
                'instalasi' => $instalasi,
                'optionsProv' => $optionsProv,
                'pendaftaranol' => $body['response']['pendaftaranol'],
                'rujukan_dari' => $rujukanDari,
                'loket_instalasi' => $loket_instalasi,
                'defaultPropinsi' => $propinsi_id,
                'defaultKota' => $kabupaten_id,
                'default_asal_rujukan' => $default_asal_rujukan,
                'default_jenis_penyakit' => $default_jenis_penyakit,
                'default_penjamin' => $default_penjamin,
                'janjiPoli' => !empty($body['response']['janjiPoli']) ? $body['response']['janjiPoli'] : [],
                'konfigSystem' => $body['response']['konfigSystem'],
                'rujukRanap' => !empty($body['response']['rujukRanap']) ? $body['response']['rujukRanap'] : [],
                'klasifikasiKamar' => $klasifikasiKamar,
                'optionsRuangan' => $optionsRuangan,
                'penjaminIntegrasi' => !empty($body['response']['penjaminIntegrasi']) ? $body['response']['penjaminIntegrasi'] : [],
            ];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }

    private function SetPasienFormModel(){
        return Yii::$app->docoPlugin->execute($this,'model_pasien_form');
    }

    public function actionPrintKarcis()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-karcis.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId, 'tipe' => $this->pendaftaranTipe];
            $response = $this->_restPendaftaran->post(
                'inf-daftar-sepuluh-terakhir/print-karcis',
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

    public function actionPrintStatusPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-status-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id', null);
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $pegawai_id = Yii::$app->docoVars->user('uid');
            $source = $request->get('source', 'pendaftaran');
            $param = ArrayHelper::getValue((new Lookup)->getValueFromLookupT($ruangan_id, 'workspace_pendaftaran'), 'additional_value');
            $konsulpoli_id = $request->get('konsulpoli_id',null);
            if($konsulpoli_id == null){
                $konsulpoli_id = 0;
            }
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param, 'source'=>$source, 'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id, 'pegawai_id' => $pegawai_id, 'konsulpoli_id' => $konsulpoli_id];
            if(Yii::$app->report->enabled){
                if ($param != 'ranap' && $param != 'penunjang' && $param != 'igd') {
                    $urlReport = 'pendaftaran/inf-daftar-sepuluh-terakhir/print-status-pasien';
                } else if ($param == 'penunjang') {
                    $urlReport = 'print-tracer-penunjang';
                } else if ($param == 'ranap'){
                    $urlReport = 'print-tracer-ranap';
                } else if ($param == 'igd'){
                    $urlReport = 'print-tracer-igd';
                }

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
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintAsesmen()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-asesmen.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $param = DocoConstants::PARAM_DFTR[$ruangan_id];
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param];
            $response = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/print-asesmen',
                [
                    'query' => $post,
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

    public function actionPrintKartuPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-kartu-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $templateResponse = $this->_restPendaftaran->request('GET', 'inf-daftar-sepuluh-terakhir/get-template-kartu', [
                'form_params'=>[],
                'query'=>[]
            ]);
            $templateBody = json_decode($templateResponse->getBody(),TRUE);
            $templateResult = $templateBody['response'];

            if($templateResult) {
                $jenisTemplate = $templateResult['jenis_template'];
            } else {
                $jenisTemplate = 'default';
            }
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'template'=>$jenisTemplate];
            $response = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/print-kartu-pasien',
                [
                    'query' => $post,
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

    public function actionPrintIdentitasPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-identitas-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $param = DocoConstants::PARAM_DFTR[$ruangan_id];
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param];
            $response = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/print-identitas-pasien',
                [
                    'query' => $post,
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

    public function actionPrintSuratKematian()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-surat-kematian.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $param = DocoConstants::PARAM_DFTR[$ruangan_id];
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param];
            $response = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/print-surat-kematian',
                [
                    'query' => $post,
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

    public function actionPrintGelangPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-gelang-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-gelang-pasien',
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

    public function actionPrintGelangPasienAnak()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-gelang-pasien-anak.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-gelang-pasien-anak',
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

    public function actionPrintLabelPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-label-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-label-pasien',
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

    public function actionPrintLabelPasienBaru()
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id', null);
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $no_pendaftaran = $request->get('no_pendaftaran', null);
        $jumlah = $request->get('jumlah', null);
        $jenis = $request->get('jenis', null);
        $html = $request->get('html', true);
        $is_farmasi = $request->get('is_farmasi', false);
        $template = $request->get('template', 'default');
        $decryptPasienId = DocoHelpers::decrypt($pasien_id);
        $decryptPendaftaranId = DocoHelpers::setDecryptIdFromString($pendaftaran_id);
        $decryptNoPendaftaran = DocoHelpers::decrypt($no_pendaftaran);
        $post = [
            'pasien_id' => $decryptPasienId,
            'pendaftaran_id' => $decryptPendaftaranId,
            'no_pendaftaran' => $decryptNoPendaftaran,
            'jumlah' => $jumlah,
            'jenis' => $jenis,
            'template' => $template,
            'html' => $html,
            'is_farmasi' => $is_farmasi,
        ];

        $optGuzzle = [
            'form_params' => $post
        ];

        if(Yii::$app->report->enabled){
            if ($template == 'print_label_report') {
                $urlReport = 'print-label-report';
            }else{
                $urlReport = $template;
            }
            $path = !empty($optGuzzle['save_to']) ? $optGuzzle['save_to'] : null;

            Yii::$app->report->exec($urlReport,[
                'queryParameter' => $post,
                'manualRender'=>function() use($post,$path){
                    $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-label-pasien-baru',[
                        'form_params' => $post,
                        'save_to' => $path
                    ]);

                    return DocoHelpers::previewPdf($path);
                }
            ]);
        }

        if (!$html || $template == 'sb' || $template == 'bin' || $template == 'bd') {
            $optGuzzle['save_to'] = Yii::getAlias("@download") . "/print-label-pasien.pdf";
        }

        if($template == 'ad' || $template == 'new_bg') {
            $orientation = 'P';
        } else {
            $orientation = 'L';
        }

        if($template == 'zebra'){
            $format = [30, 80];
        } else {
            $format = null;
        }

        if($is_farmasi) {
            $rest = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-label-farmasi', $optGuzzle);
        } else {
            $rest = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-label-pasien-baru', $optGuzzle);
        }

        $result = json_decode($rest->getBody(), true);

        if (!empty($optGuzzle['save_to'])) {
            return DocoHelpers::previewPdf($optGuzzle['save_to']);
        } else {
            $body = $result['response']['body'];
            $mpdf = new Mpdf([
                'orientation' => $orientation,
                'tempDir' => Yii::getAlias("@download"),
                'setAutoTopMargin' => 'pad',
                'format' => $format
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
    }

    public function actionPrintSep()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-sep.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $jenis_pendaftaran = $request->get('jenis', null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $params = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];

            if ($jenis_pendaftaran != null) {
                $params['jenis_pendaftaran'] = $jenis_pendaftaran;
            }

            $response = $this->_restPendaftaran
                ->get('allow-bpjs/print-sep',
                    [
                        'query' => $params,
                        'save_to' => $path
                    ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetDataSepuluhTerakhir()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $draw = $request->get('draw', 1);
        $param = $request->get('param');
        $pendaftaran_id = $request->get('pendaftaran_id',null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $response = $this->_restPendaftaran->request('GET', 'inf-daftar-sepuluh-terakhir/index', [
                'form_params'=>[],
                'query'=>['user_id'=>$loginpemakai_id, 'jenis'=>$param, 'pendaftaran_id'=>$pendaftaran_id]
            ]);

            $body = json_decode($response->getBody(),TRUE);
            $response = $body['response']['data'];
            $no = $request->get('start',1);

            foreach ($response as $key => $value) {
                $no++;
                $primaryPendaftaran = DocoHelpers::encrypt($value['pendaftaran_id']);
                $primaryPasien = DocoHelpers::encrypt($value['pasien_id']);
                $value['ruangan_nama'] = $param == 'ranap'
                    ? $value['ruangan_nama'] . ' - ' . $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur']
                    : $value['ruangan_nama'];
                $value['rowNum'] = $no;
                $value['primary'] = $primaryPendaftaran;
                $value['pasien_id'] = $primaryPasien;
                $value['pendaftaran_id'] = $primaryPendaftaran;
                $value['primaryPendaftaran'] = $primaryPendaftaran;
                $value['primaryPasien'] = $primaryPasien;
                $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_pendaftaran']));
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($response);
            $result['recordsFiltered'] = count($response);
            return $result;
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (ErrorException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetFormBpjs($params)
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            if ($params == 'bayi' || $params == 'ranap') {
                $jenis_pelayanan = DocoConstants::JNS_PLY_RANAP;
            } else {
                $jenis_pelayanan = DocoConstants::JNS_PLY_RAJAL;
            }
            $validate = preg_match('/-/', $params);
            // WIP
            if($validate) {
                $exp = explode("-", $params);
                $params = $exp[0];
                $pendaftaran_id = DocoHelpers::decrypt($exp[1]);
                $no_rekam_medik = $exp[2];
            } else {
                $pendaftaran_id = DocoHelpers::decrypt($get['pendaftaran_id']);
                $no_rekam_medik = isset($get['no_rekam_medik']) ? $get['no_rekam_medik'] : '';
            }

            $nama_pasien = isset($get['nama_pasien']) ? $get['nama_pasien'] : '';
            $pasienadmisi_id = isset($get['pasienadmisi_id']) ? $get['pasienadmisi_id'] : '';

            $modelBpjs = new BpjsNewForm();
            $modelBpjs->pendaftaran_id = $pendaftaran_id;
            $modelBpjs->no_rekam_medik = $no_rekam_medik;
            $modelBpjs->pasienadmisi_id = $pasienadmisi_id;
            $modelBpjs->nama_pasien = $nama_pasien;
            $modelBpjs->jenis_pelayanan = $jenis_pelayanan;

            $packFormBpjs = ['modelBpjs' => $modelBpjs];

        } catch (RequestException $e) {
            return $dataPendaftaran = '';
        } catch (Exception $e) {
            return $dataPendaftaran = '';
        }

        return $this->renderAjax('../daftar/partial/_formbpjs_global', get_defined_vars());
    }

    public function actionGetFormBpjsManual($params)
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $tipe = $request->get('tipe', null);

            $pendaftaran_id = $get['pendaftaran_id'];
            $no_rekam_medik = isset($get['no_rekam_medik']) ? $get['no_rekam_medik'] : '';
            $nama_pasien = isset($get['nama_pasien']) ? $get['nama_pasien'] : '';
            $pasienadmisi_id = isset($get['pasienadmisi_id']) ? $get['pasienadmisi_id'] : '';

            if($exp = explode("-", $nama_pasien)) {
                $nama_pasien = $exp[0];
            }

            if($params == 'ranap') {
                $jenis_pelayanan = DocoConstants::JNS_PLY_RANAP;
                $tableID = 'table-daftar-terakhir-ranap';
            }
            elseif($params == 'bayi') {
                $jenis_pelayanan = DocoConstants::JNS_PLY_RAJAL;
            }
            elseif($params == 'rajal') {
                $jenis_pelayanan = DocoConstants::JNS_PLY_RAJAL;
                $tableID = 'table-daftar-terakhir';
            }
            else {
                $jenis_pelayanan = DocoConstants::JNS_PLY_RAJAL;
                $tableID = 'table-daftar-terakhir-igd';
            }

            // if ($params == 'bayi' || $params == 'ranap') {
            //     $jenis_pelayanan = DocoConstants::JNS_PLY_RANAP;

            // } else {
            //     $jenis_pelayanan = DocoConstants::JNS_PLY_RAJAL;
            // }

            $modelBpjs = new BpjsNewForm();
            $modelBpjs->pendaftaran_id = $pendaftaran_id;
            $modelBpjs->no_rekam_medik = $no_rekam_medik;
            $modelBpjs->pasienadmisi_id = $pasienadmisi_id;
            $modelBpjs->nama_pasien = $nama_pasien;
            $modelBpjs->jenis_pelayanan = $jenis_pelayanan;

            $packFormBpjs = ['modelBpjs' => $modelBpjs];

        } catch (RequestException $e) {
            return $dataPendaftaran = '';
        } catch (Exception $e) {
            return $dataPendaftaran = '';
        }

        if(!$tipe) {
            $partial = '../daftar/partial/_formbpjs_manual_global';
        }
        else {
            $partial = '../informasi-pasien/_formbpjs_manual';
        }

        return $this->renderAjax($partial, get_defined_vars());
    }

    private function getPendaftaran($pendaftaran_id)
    {
        try {
            $requests = $this->_restPendaftaran->get('allow/get-pendaftaran?pendaftaran_id='.$pendaftaran_id);
            $response = json_decode($requests->getBody(), true);
            $result = ['response'=>$response['response']];

            return $result['response'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionGetFormAsuransi($params)
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $pendaftaran_id = isset($get['pendaftaran_id']) ? $get['pendaftaran_id'] : null;
        $asalrujukan_id = isset($get['asalrujukan_id']) ? $get['asalrujukan_id'] : null;
        $penjamin_id = isset($get['penjamin_id']) ? $get['penjamin_id'] : null;
        $pasien_id = isset($get['pasien_id']) ? $get['pasien_id'] : null;
        $carabayar_id = isset($get['carabayar_id']) ? $get['carabayar_id'] : null;

        if($params == 'bayi' || $params == 'ranap') {
            $instalasi_id = DocoConstants::INSTALASI_ID_RI;
        } elseif($params == 'igd') {
            $instalasi_id = DocoConstants::INSTALASI_ID_RD;
        } else {
            $instalasi_id = DocoConstants::INSTALASI_ID_RJ;
        }

        $modelAsuransi = new AsuransiForm();
        $modelRujukan = new RujukanForm();

        $modelAsuransi->pendaftaran_id = $pendaftaran_id;
        $formName = substr(strrchr(get_class($modelAsuransi), "\\"), 1);
        $packFormAsuransi = ['modelAsuransi' => $modelAsuransi];
        $packFormRujukan = ['modelRujukan' => $modelRujukan];
        $rujukandari = $this->getRujukanDari($asalrujukan_id);
        $data = $this->getDataApi($instalasi_id, 2);
        $kelaspelayanan = $data['kelas_pelayanan'];

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $post['pendaftaran_id'] = $pendaftaran_id;
            $post['asalrujukan_id'] = $asalrujukan_id;
            $post['penjamin_id'] = $penjamin_id;
            $post['pasien_id'] = $pasien_id;
            $post['carabayar_id'] = $carabayar_id;
            $post['pasien_id'] = $pasien_id;

            $is_rujukan = isset($post['RujukanForm']) ? true : false;
            $modelAsuransi->attributes = $post['AsuransiForm'];
            $modelRujukan->attributes = $post['RujukanForm'];
            $modelRujukan->asalrujukan_id = $post['asalrujukan_id'];
            $modelRujukan->is_rujukan = $is_rujukan;

            $modelAsuransi->asalrujukan_id = $post['asalrujukan_id'];
            $modelAsuransi->carabayar_id = $post['carabayar_id'];
            $modelAsuransi->penjamin_id = $post['penjamin_id'];
            $modelAsuransi->pasien_id = $post['pasien_id'];

            if (!in_array($modelRujukan->asalrujukan_id, [DocoConstants::ASAL_RUJUKAN_DS, DocoConstants::ASAL_RUJUKAN_RI])
                && !$modelRujukan->validate()) {
                $formName = substr(strrchr(get_class($modelRujukan), "\\"), 1);
                $response = $modelRujukan->getErrors();
                return DocoHelpers::response($response, 422, $formName);
            }

            $data = array_merge($modelAsuransi->attributes, $modelRujukan->attributes);
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
            if($modelAsuransi->validate()) {
                try {
                    $response = $this->_restPendaftaran->post('pendaftaran-igd/save-data-asuransi', [
                        'query' => [
                            'is_rujukan' => $is_rujukan,
                            'pendaftaran_id' => $pendaftaran_id
                        ],
                        'form_params' => $data
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            }
            else {
                $errors = DocoHelpers::parseError($modelAsuransi->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        return $this->renderAjax('partial/_formAsuransi', get_defined_vars());
    }

    private function getRujukanDari($asalrujukan_id)
    {
        try {
            $requests = $this->_restPendaftaran->get('allow/get-rujukan-dari?id='.$asalrujukan_id);
            $response = json_decode($requests->getBody(), true);
            $result = ['response'=>$response['response']];

            return $result['response'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionSimpanKunjungan($params)
    {
        $request = Yii::$app->request;
        $isMultiPayer = false;
        if($request->post('instalasi_id')) {
            if($request->post('instalasi_id') == DocoConstants::INSTALASI_MCU) {
                $params = DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU];
            }
        }

        $userLogin = Yii::$app->docoVars->user('id_pegawai');
        $payLoadRequest = [
            'tipe_pasien' => [],
            'kunjungan' => [],
            'pasien' => [],
            'rujukan' => [],
            'penanggung_jawab' => [],
            'pj_pasien' => [],
            'asuransi' => [],
            'bpjs' => [],
            'multi_payer' => [],
        ];

        switch ($params) {
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RJ;
                $default_scenario = "pendaftaran-rajal";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_IGD]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RD;
                $default_scenario = "pendaftaran-igd";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]:
                $instalasi_id = DocoConstants::INSTALASI_MCU;
                $default_scenario = "pendaftaran-mcu";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_RANAP]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RI;
                $default_scenario = "default";
                break;
            default:
                $instalasi_id = null;
                $default_scenario = "default";
                break;
        }

        $modelPasien = new PasienForm;
        $modelRujukan = new RujukanForm;
        $modelKunjungan = new KunjunganForm;
        $modelTipePasien = new TipePasienForm;
        $modelPjPasien= new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelBpjs = new BpjsNewForm;
        $modelMultiPayer = new MultiCarabayarForm;
        
        if (!empty($request->post('TipePasienForm')) && $instalasi_id !=  DocoConstants::INSTALASI_ID_RI) {
            $modelKunjungan->scenario = 'form_kunjugan';
        } else if (!empty($request->post('TipePasienForm')) && $instalasi_id ==  DocoConstants::INSTALASI_ID_RI) {
            $modelKunjungan->scenario = 'form_kunjugan_ranap';
        } else {
            $modelKunjungan->scenario = 'with_mandatory_pjawab';
        }

        if (!empty($request->post('PasienForm'))) {
            $modelPasien->attributes = $request->post('PasienForm');

            // Improvement multiple jenis identitas
            if (!empty($modelPasien->no_identitas_pasien)) {
                $data = array();
                $jenisIdentitas = $modelPasien->jenisidentitas ? $modelPasien->jenisidentitas : [];
                $noIdentitas = $modelPasien->no_identitas_pasien;

                foreach ($noIdentitas as $key => $value) {
                    if ($value) {
                        $data[] = [
                            'jenisidentitas' => array_key_exists($key, $jenisIdentitas) ? $jenisIdentitas[$key] : null,
                            'no_identitas_pasien' => $value
                        ];
                    }
                }

                $modelPasien->jenisidentitas = null;
                $modelPasien->no_identitas_pasien = null;
                $modelPasien->additional_identitas = !empty($data) ? json_encode($data) : null;
            }

            $modelPasien->scenario = $default_scenario;
            if(!empty($modelTipePasien->no_rekam_medik)) {
                $modelPasien->scenario = "pendaftaran-pasien-lama";
            }
            $payLoadRequest['pasien'] = $modelPasien->attributes;
        }

        if (!empty($request->post('TipePasienForm'))) {
            $modelTipePasien->attributes = $request->post('TipePasienForm');
            $modelTipePasien->antrian_id = $request->post('antrian_id');
            $modelTipePasien->pendaftaranol_id = $request->post('pendaftaranol_id');
            /** Kondisi ketika Bpjs Error */
            if (!empty($modelPasien->no_rekam_medik)){
                $modelTipePasien->no_rekam_medik = $modelPasien->no_rekam_medik;
                $modelTipePasien->asalrujukan_id = 2; // WIP
            };

            /** Kondisi asal rujukan di MCU */
            if($params == DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]) {
                $modelTipePasien->asalrujukan_id = $request->post('asalrujukan_id_hidden');
            }
            $payLoadRequest['tipe_pasien'] = $modelTipePasien->attributes;
            if($modelTipePasien->is_multi_payer == true) {
                $isMultiPayer = true;
            }
        }

        if (!empty($request->post('PjpasienForm'))) {
            $modelPjPasien->attributes = $request->post('PjpasienForm');
            $payLoadRequest['pj_pasien'] = $modelPjPasien->attributes;
        }

        if (!empty($request->post('BpjsNewForm'))
                && $request->post('is_bpjs')
                && empty($modelPasien->no_rekam_medik) && !$request->post('allow_notif_bpjs')) {
                $modelBpjs->attributes = $request->post('BpjsNewForm');
                $payLoadRequest['bpjs'] = $modelBpjs->attributes;

                if ($payLoadRequest['bpjs']['ppk_rujukan'] == '') {
                    $payLoadRequest['bpjs']['ppk_rujukan'] = $request->post('ppk_rujukan_hidden');
                    $payLoadRequest['bpjs']['asal_rujukan'] = $request->post('asal_rujukan_hidden');
                }

                //validasi igd tidak perlu kirim no rujukan
                if ($params == DocoConstants::PARAM_DFTR[DocoConstants::WS_IGD]){
                    $payLoadRequest['bpjs']['no_rujukan'] = '';
                }
        } else if (!empty($request->post('BpjsNewForm')) && $request->post('is_bpjs') && $request->post('allow_bpjs')) { //handle condition unauth bpjs
            $modelBpjs->attributes = $request->post('BpjsNewForm');
                $payLoadRequest['bpjs'] = $modelBpjs->attributes;

                if ($payLoadRequest['bpjs']['ppk_rujukan'] == '') {
                    $payLoadRequest['bpjs']['ppk_rujukan'] = $request->post('ppk_rujukan_hidden');
                    $payLoadRequest['bpjs']['asal_rujukan'] = $request->post('asal_rujukan_hidden');
                }
        }

        if (!empty($request->post('RujukanForm'))) {
            $modelRujukan->attributes = $request->post('RujukanForm');
            $payLoadRequest['rujukan'] = $modelRujukan->attributes;
        }
    
        if (!empty($request->post('KunjunganForm'))) {
            $kunjungan = $request->post('KunjunganForm');
            $dokter = null;

            if (($instalasi_id == DocoConstants::INSTALASI_ID_RJ || 
                 $instalasi_id ==  DocoConstants::INSTALASI_ID_RD || 
                 $instalasi_id ==  DocoConstants::INSTALASI_MCU) && !isset($kunjungan['dokter_id'])) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal!',
                        'text' => 'Dokter belum dipilih.'
                    ]
                ],422);
            }

            switch ($instalasi_id) {
                case DocoConstants::INSTALASI_ID_RJ:
                    $dokter = $kunjungan['dokter_id'];
                    break;
                case DocoConstants::INSTALASI_ID_RI:
                    $dokter = null;
                    break;
                case DocoConstants::INSTALASI_ID_RD:
                    $dokter = $kunjungan['dokter_id'];
                    break;
                case DocoConstants::INSTALASI_MCU:
                    $dokter = $kunjungan['dokter_id'];
                    break;
                default:
                    $dokter = (isset($kunjungan['pegawai_id']) && !is_null($kunjungan['pegawai_id'])) ? $kunjungan['pegawai_id'] : null;
                    break;
            }
            $modelKunjungan->attributes = $kunjungan;
            $modelKunjungan->pegawai_id = $dokter;
            $modelKunjungan->tindakan_karcis = $request->post('list_tindakan');
            $modelKunjungan->konfig_referral_required = ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE) == TRUE 
                                                        && strtoupper(ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE)) == 'TRUE';  //assign ulang konfignya, karena takut ada perubahan by inspect di FE
  
            if($params == 'mcu') {
                $list_paket = json_decode($request->post('list_penunjang','{}'), true);
                if(empty($list_paket)) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal!',
                            'text' => 'Paket MCU tidak boleh kosong.'
                        ]
                    ],422);
                }
                else {
                    $list_paket = $list_paket['paket'];
                    $tipepaket_id = $arrPaket = [];
                    if(!empty($list_paket)) {
                        foreach ($list_paket as $key => $value) {
                            $tipepaket_id[] = $value['id'];
                        }
                    }

                    $arrPaket = "[" . implode(",", $tipepaket_id) . "]";
                    $modelKunjungan->list_paket = $arrPaket;
                }

                if($modelTipePasien->is_kolektif == true){
                    $listPasienMcu = $request->post('listPasienMcu');
                    if(empty($listPasienMcu)) {
                        return DocoHelpers::response([
                            'response' => [
                                'title' => 'Proses Gagal!',
                                'text' => 'List Pasien MCU tidak boleh kosong.'
                            ]
                        ],422);
                    }
                    $modelKunjungan->list_pasien_mcu = $listPasienMcu;
                    $no_exportexcel = date('YmdHis');
                    $modelKunjungan->no_exportexcel = $no_exportexcel;
                }
            }
            else {
                $modelKunjungan->list_penunjang = $request->post('list_penunjang','{}');
                // if ($modelKunjungan->instalasi_id == DocoConstants::INSTALASI_FISIOTERAPI) {
                //     $list_fisio = json_decode($request->post('list_fisioterapi','{}'), true);
                //     if(empty($list_fisio)) {
                //         return DocoHelpers::response([
                //             'response' => [
                //                 'title' => 'Proses Gagal!',
                //                 'text' => 'Jadwal terapi tidak boleh kosong.'
                //             ]
                //         ], 422);
                //     } else {
                //         $programterapi_id = $arrTerapi = [];
                //         foreach ($list_fisio as $key => $value) {
                //             $programterapi_id[] = $key;
                //         }

                //         $arrTerapi = "[" . implode(",", $programterapi_id) . "]";
                //         $modelKunjungan->list_fisio = $arrTerapi;
                //     }
                // }
            }

            $payLoadRequest['kunjungan'] = $modelKunjungan->attributes;
            if ($params == 'penunjang') {
                $listPenunjang = json_decode($modelKunjungan->list_penunjang,true);
                // if (empty($listPenunjang ) && $modelKunjungan->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH) {
                //     return DocoHelpers::response([
                //         'response' => [
                //             'title' => 'Proses Gagal!',
                //             'text' => 'Tindakan tidak boleh kosong.'
                //         ]
                //     ], 422);
                // }
            }

            if ($request->post('is_ranap')) {
                $ranap = $request->post('PasienAdmisiForm');
                $modelKunjungan->tgl_pendaftaran = $ranap['tgl_admisi'];
                $modelKunjungan->pegawai_id = isset($ranap['pegawai_id']) && !is_null($ranap['pegawai_id']) ? $ranap['pegawai_id'] : null;
            }

            if(!$modelKunjungan->validate()) {
                $errors = DocoHelpers::parseError($modelKunjungan->errors, 'KunjunganForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        // if (!empty($request->post('AsuransiForm'))) {
        //     $asuransi = $request->post('AsuransiForm');
        //     $modelAsuransi->attributes = $asuransi;
        //     $modelAsuransi->nokartuasuransi = $request->post('no_asuransi');
        //     $payLoadRequest['asuransi'] = $modelAsuransi->attributes;
        // }

        if (!empty($request->post('no_asuransi'))) {
            $asuransi = $request->post('AsuransiForm');
            $modelAsuransi->attributes = $asuransi;
            $modelAsuransi->nokartuasuransi = $request->post('no_asuransi');
            $modelAsuransi->penjamingrade_id = ($modelAsuransi->penjamingrade_id != '') ? $modelAsuransi->penjamingrade_id : null;
            $payLoadRequest['asuransi'] = $modelAsuransi->attributes;
        }

        if(!empty($request->post('buatjanjipoli_id'))){
            $payLoadRequest['buatjanjipoli_id'] = $request->post('buatjanjipoli_id');
            $payLoadRequest['status_janji'] = !empty($request->post('status_janji')) ? $request->post('status_janji') : '';
        }

        if ($request->post('is_bbl')) {
            $payLoadRequest['is_bbl'] = $request->post('is_bbl');
            if (!$request->post('kelahiran_id')) {
                return (new DocoHelpers)->macroResponseJson(400, 'Mohon pilih data bayi', []);
            }
            $payLoadRequest['kelahiran_id'] = $request->post('kelahiran_id');
            $payLoadRequest['pendaftaran_ibu_id'] = $request->post('pendaftaran_ibu_id');
        }
        if ($request->post('is_ranap')) {
            $payLoadRequest['tipe_pasien']['no_rekam_medik'] = $request->post('TipePasienForm')['no_rekam_medik'];
            $payLoadRequest['is_ranap'] = $request->post('is_ranap');
            $payLoadRequest['kelaspelayanan_selected'] = $request->post('kelaspelayanan_selected');
            $payLoadRequest['is_pasientitipan'] = $request->post('pasientitipan', null);
            $payLoadRequest['is_aps'] = $request->post('pasienaps');
            $payLoadRequest['pasien_admisi'] = $request->post('PasienAdmisiForm');
            $payLoadRequest['pendaftaranasal_id'] = $request->post('pendaftaranasal_id');
            // $payLoadRequest['pasien_admisi']['is_pasientitipan'] = ((int) $payLoadRequest['is_pasientitipan'] == 1) ? true : false;
            $payLoadRequest['pasien_admisi']['is_aps'] = ((int) $payLoadRequest['is_aps'] == 1) ? true : false;
            if($payLoadRequest['pasien_admisi']['is_pasientitipan'] == '1' && empty($payLoadRequest['pasien_admisi']['kamar_titipan_id']) && empty($payLoadRequest['pasien_admisi']['tempattidur_titipan_id'])) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal!',
                        'text' => 'Kamar tagihan belum di pilih.'
                    ]
                ], 422);
            }
        }
        $payLoadRequest['allow_bpjs'] = $request->post('allow_bpjs');

        if (isset($request->post('BpjsNewForm')['no_kartu']) && $request->post('BpjsNewForm')['no_kartu'] == '') {
            unset($payLoadRequest['bpjs']);
            $payLoadRequest['allow_bpjs'] = false;
        }

        if($isMultiPayer && !empty($request->post('MultiCarabayarForm'))) {
            $postMultipayer = $request->post('MultiCarabayarForm');
            $modelMultiPayer->attributes = $postMultipayer;
            $modelMultiPayer->add_no_asuransi_1 = null;
            $modelMultiPayer->add_no_asuransi_2 = null;
            $modelMultiPayer->add_nokartuasuransi_1 = ($postMultipayer['add_no_asuransi_1']) ? $postMultipayer['add_no_asuransi_1'] : null;
            $modelMultiPayer->add_nokartuasuransi_2 = ($postMultipayer['add_no_asuransi_2']) ? $postMultipayer['add_no_asuransi_2'] : null;
            $modelMultiPayer->add_namapemilikasuransi_1 = isset($postMultipayer['add_namapemilikasuransi_1']) ? $postMultipayer['add_namapemilikasuransi_1'] : null;
            $modelMultiPayer->add_namaperusahaan_1 = isset($postMultipayer['add_namaperusahaan_1']) ? $postMultipayer['add_namaperusahaan_1'] : null;
            $modelMultiPayer->add_nomorpokokperusahaan_1 = isset($postMultipayer['add_nomorpokokperusahaan_1']) ? $postMultipayer['add_nomorpokokperusahaan_1'] : null;
            $modelMultiPayer->add_asuransipasien_id_1 = ($request->post('multicarabayarform-add_asuransipasien_id_1')) ? $request->post('multicarabayarform-add_asuransipasien_id_1') : null;
            $modelMultiPayer->add_penjamingrade_id_1 = isset($postMultipayer['add_penjamingrade_id_1']) ? $postMultipayer['add_penjamingrade_id_1'] : null;
            if(isset($modelMultiPayer->is_add_payer) && $modelMultiPayer->is_add_payer == true) {
                $modelMultiPayer->add_namapemilikasuransi_2 = isset($postMultipayer['add_namapemilikasuransi_2']) ? $postMultipayer['add_namapemilikasuransi_2'] : null;
                $modelMultiPayer->add_namaperusahaan_2 = isset($postMultipayer['add_namaperusahaan_2']) ? $postMultipayer['add_namaperusahaan_2'] : null;
                $modelMultiPayer->add_nomorpokokperusahaan_2 = isset($postMultipayer['add_nomorpokokperusahaan_2']) ? $postMultipayer['add_nomorpokokperusahaan_2'] : null;
                $modelMultiPayer->add_asuransipasien_id_2 = ($request->post('multicarabayarform-add_asuransipasien_id_2')) ? $request->post('multicarabayarform-add_asuransipasien_id_2') : null;
                $modelMultiPayer->add_penjamingrade_id_2 = isset($postMultipayer['add_penjamingrade_id_2']) ? $postMultipayer['add_penjamingrade_id_2'] : null;
            }
            $payLoadRequest['multi_payer'] = $modelMultiPayer->attributes;
        }
        if(!$modelRujukan->validate()) {
            $errors = DocoHelpers::parseError($modelRujukan->errors, 'RujukanForm');
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }

        $payLoadRequest['eligible_pasien'] = $request->post('eligible_pasien');
        $payLoadRequest['refresh_asuransi'] = $request->post('refresh_asuransi');
        
        try {
            $payLoadRequest['allow_antrol'] = $request->post('allow_antrol');
            $sent = $this->_restPendaftaran->post('pendaftaran-'.$params.'/save-pendaftaran', [
                    'form_params'=> $payLoadRequest
                ]
            );

            $response = json_decode($sent->getBody(), true);
            
            // return DocoHelpers::response($response,422);
            if (isset($response['metadata']['status'])) {
                $status = $response['metadata']['status'];
                if ($status == 422) {
                    $error = [];
                    $text = [];
                    if (isset($response['response']['data'])) {
                        $data = $response['response']['data'];
                        if (isset($data['kunjungan'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['kunjungan'],'KunjunganForm'));
                        }

                        if (isset($data['KunjunganForm[data]'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['KunjunganForm[data]'],'KunjunganForm'));
                        }

                        if (isset($data['tipe_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['tipe_pasien'],'TipePasienForm'));
                        }

                        if (isset($data['pasien_admisi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pasien_admisi'],'PasienAdmisiForm'));
                        }

                        if (isset($data['pj_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pj_pasien'],'PjpasienForm'));
                        }

                        if (isset($data['asuransi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['asuransi'],'AsuransiForm'));
                        }

                        if (isset($data['pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pasien'],'PasienForm'));
                            if(is_array($data['pasien'])){
                                foreach($data['pasien'] as $value){
                                    $text = array_merge($text, $value);
                                }
                            }                           
                            
                        }

                        if (isset($data['bpjs'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['bpjs'],'BpjsNewForm'));
                        }

                        $response = [
                            'metadata' => [
                                'status' => 422
                            ],
                            'response' => [
                                'data' => $error,
                                'text' => implode(',',$text)
                            ]
                        ];
                    }
                }

                if ($status == 200) {
                    $is_ranap = isset($payLoadRequest['is_ranap']) ? true : false;
                    if($is_ranap) {
                        $data_dashboard['reload'] = 1;
                        $mode = Yii::$app->params->mode;
                        Yii::$app->redis->executeCommand('PUBLISH', [
                            'channel' => 'display-dashboard-kamar-'.$mode,
                            'message' => json_encode(['data' => $data_dashboard])
                        ]);
                    }

                    if(isset($payLoadRequest['tipe_pasien']['pendaftaranol_id'])) {
                        Yii::$app->redis->executeCommand('PUBLISH', [
                            'channel' => 'informasi-reservasi:'.$userLogin,
                            'message' => json_encode([
                                'status' => 'Successfully'
                             ]),
                        ]);
                    }
                }

                //hapus session
                $session = Yii::$app->session;
                if( !empty($session->get('ruangan-bpjs')) ) {
                    $session->set('ruangan-bpjs', null);
                }
            }
            return DocoHelpers::response($response);
        } catch(RequestException $e) {
            $contentGuzzle = json_decode($e->getResponse()->getBody(true));
            if (isset($contentGuzzle->metadata) && $contentGuzzle->metadata->status < 500) {
                return (new DocoHelpers)->macroResponseJson($contentGuzzle->metadata->status, $contentGuzzle->response->message, []);
            } else {
                return (new DocoHelpers)->macroResponseJson(500, 'Terjadi kesalahan pada server', []);
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getMessage(),null);
        }
    }

    /*
    * author: Budi
    * date: 11-07-2019
    * desc: get data diagnosa
    * params needed: diagnosa_name
    */
    public function actionGetDiagnosa()
    {
        if(isset($_GET['search']) && !empty($_GET['search'])){
            $type = isset($_GET['type']) ? $_GET['type'] : null;
            $response = $this->_restPendaftaran->request('POST', 'allow/get-diagnosa',[
                            'form_params'=>['term'=>$_GET['search'], 'type'=>$type],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $text = $value['diagnosa_kode'] . ' - ' . $value['diagnosa_namalainnya'];
                $data[] = ['id'=>$value['diagnosa_id'],'text'=>$text];
            }
            $total = count($body['response']);
            $return = ['results'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    /*
    * author: Rizqi Febian
    * date: 13-04-2018
    * get data list tarif karcis by ruangan id and kelaspelayanan id
    * params needed: ruangan_id, kelaspelayanan_id
    */
    public function actionGetKarcis($ruangan_id,$kp_id,$status,$penjamin_id,$dokter_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $ruanganId = Yii::$app->docoVars->workspace('ruangan_id');
        /*
            *  This method will replace get data ruangan from DocoConstants::PARAM_DFTR to table lookup transaksi,
            *  required mapping ruangan on table lookup transaksi with kode_transaksi = workspace_pendaftaran
        */
        $param = ArrayHelper::getValue((new Lookup)->getValueFromLookupT($ruanganId, 'workspace_pendaftaran'), 'additional_value');
        // $param = !empty(DocoConstants::PARAM_DFTR[$ruanganId]) ? DocoConstants::PARAM_DFTR[$ruanganId] : '';
        $tarifgroup =($param == 'penunjang') ? DocoConstants::VAR_KEL_KRCS_PNJ : DocoConstants::VAR_KEL_KRCS;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            if(empty($kp_id) || empty($ruangan_id) || empty($penjamin_id) || empty($tarifgroup) || empty($dokter_id)){
                return $result;
            }
            $query = [
                'ruangan_id' => $ruangan_id,
                'kelaspelayanan_id' => $kp_id,
                'penjamin_id' => $penjamin_id,
                'kelompoktindakan_id' => $tarifgroup,
                'dokter_id' => (int) $dokter_id,
                'param' => $param
            ];

            $response = $this->_restPendaftaran->get('pendaftaran/get-tarif-karcis-pendaftaran', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            if ($status == 1) {
                $checked = false;
            } else {
                $checked = true;
            }


            $i = 0;
            $y = 0;
            $komponenTarif = [];
            foreach ($body['response']['data'] as $key => $value) {
                $tmp_total = 0;
                if($status == 0){
                    if($value['daftartindakan_id'] != 1){
                        $checked = false;
                    }
                }
                if($checked || $value['is_default']==true){
                    $tmp_total = $value['harga_tariftindakan'];
                }

                if($value['komponentarif_id'] == '6'){
                    $value['checked'] = $checked;
                    $value['tmp_view'] = 'Rp. ' . number_format($value['harga_tariftindakan'], 0, ',', '.');
                    $value['tmp_total'] = $tmp_total;
                    $value['number'] = $i + 1;
                    $value['aksi'] = Html::checkbox('status[]', ($value['is_default']) ? true : $checked, [
                        'label' => '',
                        'data-key' => $i,
                        // 'disabled' => ($value['is_default']) ? true : false,
                        'disabled' => false,
                        'class' => 'check-aksi styled',
                        'value' => $value['daftartindakan_id'],
                    ]);
                    $value['cek-default'] = ($value['is_default']) ? 1 : 0;
                    $value['konsultasi'] = ($value['is_konsultasi']) ? Yii::t('fe', 'Ya') : Yii::t('fe', 'Tidak');
                    $data[$i] = $value;
                    $i++;
                }else{
                    $komponenTarif[$value['daftartindakan_id']][] = $value;
                    $y++;
                }
            }

            foreach ($data as $key => $value) {
                if(isset($komponenTarif[$value['daftartindakan_id']])){
                    $data[$key]['komponen'] = json_encode($komponenTarif[$value['daftartindakan_id']]);
                }
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return $result;
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }

    public function actionValidationTipePasien()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new TipePasienForm;
        $isMultiPayer = isset($post['is_multi_payer']) ? $post['is_multi_payer'] : false;
        $is_pasienrs = (isset($post['jenis_pendaftaran']) && $post['jenis_pendaftaran'] == 'pasien-rs') ? TRUE : FALSE;
        if (!empty($post)) {
            $model->attributes = $post;
            $model->chk_status_pasien = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : 0;

            if($isMultiPayer == 'true') {
                $dataPasien = null;
                $mainBpjs = false;
                $dataMultiPayer = isset($post['list_tmp']) ? $post['list_tmp'] : null;
                $modelMultiPayer = new MultiCarabayarForm;
                if($dataMultiPayer) {
                    // custom validation
                    if(!$request->post('is_bpjs') && $is_pasienrs == FALSE) {
                        if($model->groupcarabayar_id == DocoConstants::GROUP_UMUM) {
                            if ((!empty($model->tipe_pasien) && $model->jenis_pendaftaran != 'pasien-rs') && empty($model->no_rekam_medik)) {
                                $model->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
                                return DocoHelpers::response($model->errors,422,'TipePasienForm');
                            }
                        }
                        if ($model->groupcarabayar_id == DocoConstants::GROUP_JAMINAN) {
                            if($model->chk_status_pasien == 1) {
                                if(empty($model->no_rekam_medik)) {
                                    $model->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
                                    return DocoHelpers::response($model->errors,422,'TipePasienForm');
                                }
                            }
                        }
                    }

                    if($model->validate()) {
                        $modelMultiPayer->attributes = $dataMultiPayer;
                        $modelMultiPayer->scenario = 'default';
                        $modelMultiPayer->instalasi_workspace = ArrayHelper::getValue($post, 'instalasi_workspace');
                        if($modelMultiPayer->validate()) {
                            if ($request->post('is_bpjs') && $is_pasienrs == FALSE) {
                                $bpjsForm = new PasienBpjsForm;
                                $bpjsForm->jenis_pencarian = $request->post('jenis_pencarian');
                                $bpjsForm->asal_rujukan = $request->post('asal_rujukan');
                                $bpjsForm->jenis_kartu = $request->post('jenis_kartu');
                                $bpjsForm->tanggal_sep = $request->post('tanggal_sep');
                                $bpjsForm->jenis_pelayanan = $request->post('jenis_pelayanan');
                                $bpjsForm->no_rujukan_f = $request->post('no_rujukan_f');
                                $bpjsForm->scenario = 'rujukan';
                                $url = 'rujukan';
                                $postAttr = [
                                    'nomor' => trim($bpjsForm->no_rujukan_f),
                                    'asal_rujukan' => $bpjsForm->asal_rujukan,
                                    'tglSEP' => $bpjsForm->tanggal_sep,
                                ];

                                if ($bpjsForm->jenis_pencarian == 2) {
                                    $url = 'peserta';
                                    $bpjsForm->no_kartu = $request->post('no_kartu');
                                    $postAttr = [
                                        'nokartu' => trim($bpjsForm->no_kartu),
                                        'tglSEP' => $bpjsForm->tanggal_sep,
                                        'isktp' => $bpjsForm->jenis_kartu == 1 ? 0 : 1,
                                        'asal_rujukan' => $bpjsForm->asal_rujukan,
                                    ];

                                    $bpjsForm->scenario = 'rujukan-manual';
                                }
                                if ($bpjsForm->validate()) {
                                     try {
                                        $response = $this->_restPendaftaran->post("allow-bpjs/{$url}", [
                                            'form_params' => $postAttr,
                                            'timeout' => 25, // Connection timeout 7 Detik
                                        ]);
                                        $body = json_decode($response->getBody(), true);
                                        if (isset($body['response']['metaData']['code'])) {
                                            if ($body['response']['metaData']['code'] == 201) {
                                                return DocoHelpers::response([
                                                    'response' => [
                                                        'title' => 'Proses Gagal !',
                                                        'text' => $body['response']['metaData']['message']
                                                    ]
                                                ],422);
                                            } else if ($body['response']['metaData']['code'] > 400) {
                                                $dataPasien = [
                                                    'messages' => $body['response']['metaData']['message']
                                                ];
                                            } else {
                                                $dataPasien = isset($body['response']['response']) ? $body['response']['response'] : null;
                                            }
                                        } else {
                                            $dataPasien = [
                                                'messages' => 'Terjadi Kesalahan pada server bpjs'
                                            ];
                                        }
                                    } catch (RequestException $e) {
                                        $dataPasien = [
                                            'messages' => 'Terjadi Kesalahan pada server bpjs',
                                            'error' => $e->getMessage()
                                        ];
                                    } catch (\GuzzleHttp\Exception\ConnectException $e) {
                                        $dataPasien = [
                                            'messages' => 'Terjadi Kesalahan pada server bpjs'
                                        ];
                                    }
                                }  else {
                                    return DocoHelpers::response($bpjsForm->errors,422,'BpjsNewForm');
                                }
                            }

                            return DocoHelpers::response(['response' => [
                                'messages' => 'Proses Berhasil',
                                'title' => 'Proses Berhasil',
                                'text' => 'Langkah Tipe pasien berhasil',
                                'pasien_bpjs' => $dataPasien
                            ]]);
                        } else {
                            return DocoHelpers::response($modelMultiPayer->errors,422,'MultiCarabayarForm');
                        }
                    } else {
                        return DocoHelpers::response($model->errors,422,'TipePasienForm');
                    }
                } else {
                    return DocoHelpers::response($modelMultiPayer->errors,422,'MultiCarabayarForm');
                }
            } else {
                if ($model->validate()) {
                    $dataPasien = null;
                    if ($request->post('is_bpjs') && $is_pasienrs == FALSE) {
                        $bpjsForm = new PasienBpjsForm;
                        $bpjsForm->jenis_pencarian = $request->post('jenis_pencarian');
                        $bpjsForm->asal_rujukan = $request->post('asal_rujukan');
                        $bpjsForm->jenis_kartu = $request->post('jenis_kartu');
                        $bpjsForm->tanggal_sep = $request->post('tanggal_sep');
                        $bpjsForm->jenis_pelayanan = $request->post('jenis_pelayanan');
                        $bpjsForm->no_rujukan_f = $request->post('no_rujukan_f');
                        $bpjsForm->scenario = 'rujukan';
                        $url = 'rujukan';
                        $postAttr = [
                            'nomor' => trim($bpjsForm->no_rujukan_f),
                            'asal_rujukan' => $bpjsForm->asal_rujukan,
                            'tglSEP' => $bpjsForm->tanggal_sep,
                        ];

                        if ($bpjsForm->jenis_pencarian == 2) {
                            $url = 'peserta';
                            $bpjsForm->no_kartu = $request->post('no_kartu');
                            $postAttr = [
                                'nokartu' => trim($bpjsForm->no_kartu),
                                'tglSEP' => $bpjsForm->tanggal_sep,
                                'isktp' => $bpjsForm->jenis_kartu == 1 ? 0 : 1,
                                'asal_rujukan' => $bpjsForm->asal_rujukan,
                            ];

                            $bpjsForm->scenario = 'rujukan-manual';
                        }
                        if ($bpjsForm->validate()) {
                             try {
                                $response = $this->_restPendaftaran->post("allow-bpjs/{$url}", [
                                    'form_params' => $postAttr,
                                    'timeout' => 25, // Connection timeout 7 Detik
                                ]);
                                $body = json_decode($response->getBody(), true);
                                if (isset($body['response']['metaData']['code'])) {
                                    if ($body['response']['metaData']['code'] > 200) {
                                        return DocoHelpers::response([
                                            'response' => [
                                                'title' => 'Proses Gagal !',
                                                'text' => $body['response']['metaData']['message']
                                            ]
                                        ],422);
                                    } else if ($body['response']['metaData']['code'] > 400) {
                                        $dataPasien = [
                                            'messages' => $body['response']['metaData']['message']
                                        ];
                                    } else {
                                        $dataPasien = isset($body['response']['response']) ? $body['response']['response'] : null;
                                    }
                                } else {
                                    $dataPasien = [
                                        'messages' => 'Terjadi Kesalahan pada server bpjs'
                                    ];
                                }
                            } catch (RequestException $e) {
                                $dataPasien = [
                                    'messages' => 'Terjadi Kesalahan pada server bpjs',
                                    'error' => $e->getMessage()
                                ];
                            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                                $dataPasien = [
                                    'messages' => 'Terjadi Kesalahan pada server bpjs'
                                ];
                            }
                        }  else {
                            return DocoHelpers::response($bpjsForm->errors,422,'BpjsNewForm');
                        }
                    }
                    return DocoHelpers::response(['response' => [
                        'messages' => 'Proses Berhasil',
                        'title' => 'Proses Berhasil',
                        'text' => 'Langkah Tipe pasien berhasil',
                        'pasien_bpjs' => $dataPasien
                    ]]);
                } else {
                    return DocoHelpers::response($model->errors,422,'TipePasienForm');
                }
            }
        }
        return DocoHelpers::response([
            'messages' => 'Tidak ada yang diproses'
        ],422);
    }

    public function actionValidationRujukan()
    {
        $post = Yii::$app->request->post();
        if (!empty($post)) {
            $model = new RujukanForm;
            $model->load($post);
            $model->asalrujukan_id = isset($post['asalrujukan_id']) ? $post['asalrujukan_id'] : null;
            if ($model->validate()) {
                return DocoHelpers::response(['response' => [
                    'messages' => 'Proses Berhasil',
                    'title' => 'Proses Berhasil',
                    'text' => 'Langkah Rujukan berhasil'
                ]]);
            } else {
                return DocoHelpers::response($model->errors,422,'RujukanForm');
            }
        }
        return DocoHelpers::response([
            'messages' => 'Tidak ada yang diproses'
        ],422);
    }

    public function actionValidationKunjungan()
    {
        $post = Yii::$app->request->post();

        if (!empty($post)) {
            $errors = [];
            $isRanap = false;
            $model = new KunjunganForm;
            if (isset($post['is_ranap']) && $post['is_ranap']) {
                $model->scenario = 'form_kunjugan_ranap';
                $isRanap = true;
            } else {
                $model->scenario = 'form_kunjugan';
            }
            $model->load($post);
            if(!$isRanap) {
                $pegId = (isset($post['KunjunganForm']['dokter_id']) && !is_null($post['KunjunganForm']['dokter_id'])) ? $post['KunjunganForm']['dokter_id'] : null;
                if(is_null($pegId)) {
                    $pegId =  (isset($post['KunjunganForm']['pegawai_id']) && !is_null($post['KunjunganForm']['pegawai_id'])) ? $post['KunjunganForm']['pegawai_id'] : null;
                }
                $model->pegawai_id = $pegId;
            }
            $errorKunjungan = !$model->validate() ? $model->errors : [];
            $errorAdmisi = [];
            if (isset($post['is_ranap']) && $post['is_ranap']) {
                $modelPasienAdmisi = new PasienAdmisiForm;
                $modelPasienAdmisi->load($post);
                $errorAdmisi = !$modelPasienAdmisi->validate() ? $modelPasienAdmisi->errors : [];
            }

            if (in_array($model->instalasi_id, DocoConstants::INSTALASI_ID_PENUNJANG) ) {
                $request = Yii::$app->request;
                $listPenunjang = json_decode($request->post('list_penunjang','{}'),true);
                // if (empty($listPenunjang)) {
                //     return DocoHelpers::response([
                //         'response' => [
                //             'title' => 'Proses Gagal!',
                //             'text' => 'Tindakan tidak boleh kosong.'
                //         ]
                //     ],422);
                // }
            }

            if(isset($post['instalasi_id']) && $post['instalasi_id'] == DocoConstants::INSTALASI_MCU) {

                $request = Yii::$app->request;
                $listPenunjang = json_decode($request->post('list_penunjang','{}'),true);
                if (empty($listPenunjang)) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal!',
                            'text' => 'Paket MCU tidak boleh kosong.'
                        ]
                    ],422);
                }
            }

            if (empty($errorKunjungan) && empty($errorAdmisi)) {
                return DocoHelpers::response(['response' => [
                    'messages' => 'Proses Berhasil',
                    'title' => 'Proses Berhasil',
                    'text' => 'Langkah Kunjungan berhasil'
                ]]);
            } else {
                return DocoHelpers::response([
                    'response' => [
                        'data' => DocoHelpers::mapErrorForm([
                            'KunjunganForm' => $errorKunjungan,
                            'PasienAdmisiForm' => $errorAdmisi
                        ]),
                        'payload' => $post
                    ]
                ],422);
            }
        }
        return DocoHelpers::response([
            'messages' => 'Tidak ada yang diproses'
        ],422);
    }

    public function actionValidationAsuransi()
    {
        $post = Yii::$app->request->post();
        $get = Yii::$app->request->get();
        if (!empty($post)) {
            if(isset($get['isMultiPayer']) && $get['isMultiPayer'] == 'true') {
                $model = new MultiCarabayarForm;
                $scenario = (isset($post['multicarabayarform-add_asuransipasien_id_2'])) ? 'second-validate-asuransi' : 'first-validate-asuransi';
                $model->scenario = ArrayHelper::getValue($post, 'instalasi_workspace') == DocoConstants::WS_IGD ? 'asuransi-igd' : $scenario;
                $model->load($post);
                if ($model->validate()) {
                    return DocoHelpers::response(['response' => [
                        'messages' => 'Proses Berhasil',
                        'title' => 'Proses Berhasil',
                        'text' => 'Langkah Asuransi berhasil'
                    ]]);
                } else {
                    return DocoHelpers::response($model->errors,422,'MultiCarabayarForm');
                }
            } else {
                $model = new AsuransiForm;
                $model->load($post);
                $isIntegrasi = ArrayHelper::getValue($post, 'isIntegrasi');
                $model->isIntegrasi = $isIntegrasi == "true" ? 1 : 0;
                $model->scenario = ArrayHelper::getValue($post, 'instalasi_workspace') == DocoConstants::WS_IGD ? AsuransiForm::SCENARIO_IGD : 'default';
                if ($model->validate()) {
                    return DocoHelpers::response(['response' => [
                        'messages' => 'Proses Berhasil',
                        'title' => 'Proses Berhasil',
                        'text' => 'Langkah Asuransi berhasil'
                    ]]);
                } else {
                    return DocoHelpers::response($model->errors,422,'AsuransiForm');
                }
            }
        }
        return DocoHelpers::response([
            'messages' => 'Tidak ada yang diproses'
        ],422);
    }

    public function actionValidationKeluargaPasien()
    {
        $post = Yii::$app->request->post();
        if (!empty($post)) {
            $model = new KeluargaPasienForm;
            $model->load($post);
            if ($model->validate()) {
                return DocoHelpers::response(['response' => [
                    'messages' => 'Proses Berhasil',
                    'title' => 'Proses Berhasil',
                    'text' => 'Langkah Pasien berhasil'
                ]]);
            } else {
                return DocoHelpers::response($model->errors,422,'KeluargaPasienForm');
            }
        }
        return DocoHelpers::response([
            'messages' => 'Tidak ada yang diproses'
        ],422);
    }

    public function actionValidationBpjs()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $post_ranap = !empty($post['post_ranap']) ? $post['post_ranap'] : false;
        $is_beda_poli_rujukan = ArrayHelper::getValue($post, 'is_beda_poli_rujukan');
        $dataRujukan = $dataKunjungan = [];
        $scenarioExt = ArrayHelper::getValue($post, 'ext', '');
        $ruangan_bpjs = 0;

        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $ruangan_pendaftaran = ArrayHelper::getValue($active_workspace,'ruangan_id');

        $bpjs = new BpjsNewForm;
        $postBpjs = isset($post['BpjsNewForm']) ? $post['BpjsNewForm'] : [];
        $bpjs->attributes = $postBpjs;

        if($post_ranap) {
            $bpjs->asal_rujukan = !empty($request->post('asal_rujukan_hidden')) ?
                $request->post('asal_rujukan_hidden') : null;

            $bpjs->ppk_rujukan = !empty($request->post('ppk_rujukan_hidden')) ?
                $request->post('ppk_rujukan_hidden') : null;

            $bpjs->scenario = 'skdp' . $scenarioExt;
            if(ArrayHelper::getValue($postBpjs,'poli_tujuan') == 'IGD'){
                $bpjs->scenario = 'default';
            }
            $bpjs->is_tujuan_kunj = 1;
        }
        if ($is_beda_poli_rujukan) {
            $bpjs->scenario = 'skdpbedapoli' . $scenarioExt;
        }

        $bpjs->jenis_pelayanan = isset($postBpjs['jenis_pelayanan']) ? $postBpjs['jenis_pelayanan'] : 1;
        $bpjs->poli_tujuan = !empty($postBpjs['poli_tujuan']) ? $postBpjs['poli_tujuan'] : 'IGD';
        $bpjs->poli_eksekutif = isset($postBpjs['poli_eksekutif']) ? $postBpjs['poli_eksekutif'] : 0;
        $bpjs->no_kartu = $request->post('no_kartu');
        $bpjs->user = Yii::$app->docoVars->user("nama");
        if (isset($post['is_skdp'])) {
            if ($bpjs->kasus_kecelakaan) {
                if ($bpjs->status_suplesi == '1') {
                    $bpjs->scenario = 'skdpsuplesi' . $scenarioExt;
                } else {
                    $bpjs->scenario = 'skdpkll' . $scenarioExt;
                }
            } else {
                $bpjs->scenario = 'skdp' . $scenarioExt;
            }
        } else {
            if ($bpjs->kasus_kecelakaan) {
                if ($bpjs->status_suplesi == '1') {
                    $bpjs->scenario = 'suplesi';
                } else {
                    $bpjs->scenario = 'kll';
                }
            }
        }

        if(empty(ArrayHelper::getValue($post, 'scenario'))) {
            $bpjs->scenario = $bpjs->scenario == "default" ? "default" . $scenarioExt: $bpjs->scenario;
        } else {
            $bpjs->scenario = ArrayHelper::getValue($post, 'scenario', 'default');
        }
        // $merge_scenario = $bpjs->scenario == "default" ? "" : $bpjs->scenario;

        // if ($postBpjs['poli_tujuan'] == 'IGD') {
        //     $bpjs->scenario = 'igd'.$merge_scenario;
        // }else{
        //     $bpjs->scenario = 'rajal'.$merge_scenario;
        //     $bpjs->scenario = 'ranap'.$merge_scenario;
        // }
        if ($bpjs->validate()) {
            try {
                if ($bpjs->is_tujuan_kunj != DocoConstants::TUJUAN_KUNJ_BPJS_TRUE) {
                    // Cek rujukan dan histori sep untuk kebutuhan tujuan kunjungan
                    $attrRujukan = [
                        'nomor' => $bpjs->no_rujukan,
                        'asal_rujukan' => $bpjs->asal_rujukan,
                        'tglSEP' => $bpjs->tanggal_sep,
                    ];

                    $response = $this->_restPendaftaran->post("allow-bpjs/rujukan", [
                        'form_params' => $attrRujukan,
                        'timeout' => 25,
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if (isset($body['response']['metaData']['code'])) {
                        if ($body['response']['metaData']['code'] == 200) {
                            $dataRujukan = ArrayHelper::getValue($body['response'], 'response');
                            $lastSep = ArrayHelper::getValue($dataRujukan['lastSep']['response'], 'histori');

                            if (!empty($lastSep)) {
                                $ppkPelayananRs = $dataRujukan['ppkPelayananRs_nama'];
                                foreach ($lastSep as $key => $value) {
                                    if (strtolower($value['ppkPelayanan']) == strtolower($ppkPelayananRs) && $value['noRujukan'] == $bpjs->no_rujukan) {
                                        $dataKunjungan[] = $value;
                                        break;
                                    }
                                }
                            }
                        } else {
                            return DocoHelpers::response([
                                'response' => [
                                    'title' => 'Proses Gagal!',
                                    'text' => $body['response']['metaData']['message']
                                ]
                            ],422);
                        }
                    } else {
                        return DocoHelpers::response([
                            'response' => [
                                'title' => 'Proses Gagal!',
                                'text' => 'Terjadi Kesalahan pada server BPJS'
                            ]
                        ],422);
                    }
                }

                /*Check no rujukan*/
                if (!empty($bpjs->no_rujukan) && $ruangan_pendaftaran == DocoConstants::WS_RANAP) {
                    $response = $this->_restPendaftaran->get("allow-bpjs/cek-no-rujukan-is-used", [
                        'query' => ['no_rujukan' => $bpjs->no_rujukan],
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if (ArrayHelper::getValue($body, 'metadata.status', 500) != 200) {
                        return DocoHelpers::response([
                            'response' => [
                                'title' => 'Proses Gagal!',
                                'text' => ArrayHelper::getValue($body, 'response.message')
                            ]
                        ],422);
                    }
                }

                if(!empty($bpjs->poli_tujuan)) {
                    $session = Yii::$app->session;
                    if( !empty($session->get('ruangan-bpjs')) ) {
                        foreach($session->get('ruangan-bpjs') as $k => $v) {
                            if($v == $bpjs->poli_tujuan) {
                                $ruangan_bpjs = $k;
                                break;
                            }
                        }
                    }
                }


                return DocoHelpers::response(['response' => [
                    'messages' => 'Proses Berhasil',
                    'title' => 'Proses Berhasil',
                    'text' => 'Langkah Form BPJS berhasil',
                    'data_rujukan' => $dataRujukan,
                    'data_kunjungan' => $dataKunjungan,
                    'tgl_sep' => date('Y-m-d', strtotime($bpjs->tanggal_sep)),
                    'is_tujuan_kunj' => $bpjs->is_tujuan_kunj, //Param jika tujuan kunjungan sudah diset,
                    'ruangan_bpjs' => $ruangan_bpjs, //default ruangan
                ]]);
            } catch (RequestException $e) {
                return DocoHelpers::response([
                    'messages' => $e->getMessage()
                ],500);
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                return DocoHelpers::response([
                    'messages' => $e->getMessage()
                ],500);
            }
        } else {
            return DocoHelpers::response($bpjs->errors, 422, 'BpjsNewForm');
        }
    }

    public function actionValidationPasien()
    {
        $post = Yii::$app->request->post();
        if (!empty($post)) {
            $queryUrl = Yii::$app->request->get();
            if (isset($queryUrl['is_bbl']) && (!isset($post['kelahiran_id']) || (isset($post['kelahiran_id']) && empty($post['kelahiran_id'])))) {
                return (new DocoHelpers)->macroResponseJson(400, 'Mohon pilih bayi');
            }
            $instalasi_id = (isset($post["PasienForm"]["additional_data"])) ? $post["PasienForm"]["additional_data"] : "";
            switch ($instalasi_id) {
                case DocoConstants::INSTALASI_ID_RJ:
                    $default_scenario = "pendaftaran-rajal";
                    break;
                case DocoConstants::INSTALASI_ID_RD:
                    $default_scenario = "pendaftaran-igd";
                    break;
                case DocoConstants::INSTALASI_MCU:
                    $default_scenario = "default";
                    break;
                default:
                    $default_scenario = "default";
                    break;
            }
            $model = $this->SetPasienFormModel();
            $model->scenario = isset($queryUrl['is_bbl']) ? 'pendaftaran-pasien-lama' : $default_scenario;
            $post["PasienForm"]["additional_data"] = "";
            $model->load($post);

            if (!empty($model->jenisidentitas)) {
                $makeNull = false;

                foreach ($model->jenisidentitas as $value) {
                    if (!$value) {
                        $makeNull = true;
                    }
                }

                if ($makeNull) {
                    $model->jenisidentitas = null;
                }
            }

            if (!empty($model->no_identitas_pasien)) {
                $makeNull = false;

                foreach ($model->no_identitas_pasien as $value) {
                    if (!$value) {
                        $makeNull = true;
                    }
                }

                if ($makeNull) {
                    $model->no_identitas_pasien = null;
                }
            }

            if ($model->validate()) {
                return DocoHelpers::response(['response' => [
                    'messages' => 'Proses Berhasil',
                    'title' => 'Proses Berhasil',
                    'text' => 'Langkah Pasien baru berhasil'
                ]]);
            } else {
                return DocoHelpers::response($model->errors,422,'PasienForm');
            }
        }
        return DocoHelpers::response([
            'messages' => 'Tidak ada yang diproses'
        ],422);
    }

    public function actionGetInfoPasien($id, $no_rm = null, $param=null)
    {
        try {
            if($id == "null") {
                $id = "";
            }
            if($param == 'sty') {
                $endpoint = 'allow/get-data-pasien-sy';
            } else {
                $endpoint = 'allow/get-info-pasien-pendaftaran';
            }
            $requests = $this->_restPendaftaran->get($endpoint,[
                'query' => [
                    'id' => $id,
                    'no_rm' => $no_rm,
                    'is_ranap' => Yii::$app->request->get('is_ranap', false)
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            $pasien_id = ArrayHelper::getValue($response,'response.info_pasien.pasien_id');
            if(isset($response['response']['info_pasien'])) {
                $response['response']['info_pasien']['encrypted_pasien_id'] = DocoHelpers::encrypt($pasien_id);
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        }
    }

    public function actionGetAsuransi($q, $penjamin_id)
    {
        try {
            $payloadRequest = Yii::$app->request->get();
            $requests = $this->_restPendaftaran->get('allow/get-pasien-asuransi',[
                'query' => [
                    'q' => $q,
                    'penjamin_id' => $penjamin_id,
                    'is_ranap' => isset($payloadRequest['is_ranap'])
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            $data = isset($response['response']['data']) ? $response['response']['data'] : [];
            $listData = [];
            foreach ($data as $key => $value) {
                $row = $value;
                $row['id'] = $value['pasien_id'];
                $value['tanggal_lahir'] = date('d-M-Y', strtotime($value['tanggal_lahir']));
                $row['value'] = $value['nokartuasuransi'] . ' - ' . $value['nama_pasien'] . ' - (' . $value['tanggal_lahir'] . ')' ;
                if(!isset($listData[$value['pasien_id']])){
                    $listData[$value['pasien_id']] = $row;
                }
            }

            return DocoHelpers::response($listData);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        }
    }

    public function actionDetailKunjungan($no_rekam_medik)
    {
        $title = Yii::t('fe', 'Detail Kunjungan Pasien');

        return $this->renderAjax('partial/_formdetailkunjungan', get_defined_vars());
    }

    public function actionGetDetailKunjungan($no_rekam_medik)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $param = $request->get('param');
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->request('GET', 'allow/get-detail-kunjungan', [
                'form_params'=>[],
                'query'=>['no_rekam_medik'=>$no_rekam_medik]
            ]);

            $body = json_decode($response->getBody(),TRUE);
            $response = $body['response']['data'];
            $no = $request->get('start',0);

            if(!empty($response)) {
                foreach ($response as $key => $value) {
                    $no++;
                    $value['rowNum'] = $no;
                    $value['tglpasienpulang'] = isset($value['tglpasienpulang']) ? date('d-m-Y H:i:s', strtotime($value['tglpasienpulang'])) : '';
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = count($response);
                $result['recordsFiltered'] = $request->get('length', 10);
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
            }

            return $result;
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (ErrorException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetailHistoryBpjs($no_kartu)
    {
        $title = Yii::t('fe', 'Detail History BPJS');

        return $this->renderAjax('/daftar/partial/component/_formdetailbpjs', get_defined_vars());
    }

    public function actionGetDetailKunjunganBpjs()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $param = $request->get('param');
        $noKartu = $request->get('no_kartu');

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $data = [];
        $count = 0;

        $tglMulai = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : date("Y-m-d", strtotime("-89 days"));
        $tglAkhir = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : date('Y-m-d');

        $dataParam = [
            'noKartu' => $noKartu,
            'tglMulai' => $tglMulai,
            'tglAkhir' => $tglAkhir,
        ];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $body = $this->bpjsService->detailHistoryBpjs($dataParam);
            if($body['metaData']['code'] == 200) {
                $count = count($body['response']['histori']);
                $response = $body['response']['histori'];
                $no = $request->get('start',0);
                if(!empty($response)) {
                    foreach ($response as $key => $value) {
                        $no++;
                        $value['rowNum'] = $no;
                        list($kode,$nama) = explode('-', $value['diagnosa']);
                        $value['diagnosa'] = $kode;
                        $value['jnsPelayanan'] = ($value['jnsPelayanan'] == 1) ? 'RI' : 'RJ';
                        $value['tglSep'] = ($value['tglSep'] != null) ? date('d-m-Y', strtotime($value['tglSep'])) : null;
                        $value['tglPlgSep'] = ($value['tglPlgSep'] != null) ? date('d-m-Y', strtotime($value['tglPlgSep'])) : null;
                        $data[$key] = $value;
                    }
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $count;
                $result['recordsFiltered'] = $request->get('length', 10);
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
            }
            return $result;
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (ErrorException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }

    public function actionPilihJumlahCetakan()
    {
        $title = 'Pilih jumlah label';
        $request = Yii::$app->request;
        $jenis = $request->get('jenis');
        $pendaftaran_id = DocoHelpers::encrypt($request->get('pendaftaran_id'));
        $pasien_id = DocoHelpers::encrypt($request->get('pasien_id'));
        $no_pendaftaran = DocoHelpers::encrypt($request->get('no_pendaftaran'));

        $response = $this->_restPendaftaran->request('GET', 'inf-daftar-sepuluh-terakhir/get-template-label', [
            'form_params'=>[],
            'query'=>[]
        ]);
        $body = json_decode($response->getBody(),TRUE);
        $result = $body['response'];

        if($result) {
            $jenisTemplate = $result['jenis_template'];
            $jumlahCetakan = $result['jumlah_template'];
        } else {
            $jenisTemplate = 'kn';
            $jumlahCetakan = 11;
        }

        $pendaftaran_id = split('-', $pendaftaran_id);
        if (array_key_exists(1, $pendaftaran_id)) {
            $pendaftaran_id = $pendaftaran_id[0];
        } else {
            $pendaftaran_id = $pendaftaran_id[0];
        }

        return $this->renderPartial('/daftar/_modal_jumlah_cetakan', get_defined_vars());
    }

    public function actionPrintLabelBed()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-label-bed.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-label-bed',
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

    public function actionPrintR2k()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-r2k.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-r2k',
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

    public function actionPrintR2mk()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-r2mk.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-r2mk',
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

    public function actionValidationTipePasienV2()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new TipePasienForm;

        $is_pasienrs = (isset($post['jenis_pendaftaran']) && $post['jenis_pendaftaran'] == 'pasien-rs') ? TRUE : FALSE;
        if (!empty($post)) {
            $model->attributes = $post;
            $model->chk_status_pasien = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : 0;
            if($model->chk_status_pasien != 0 && !$model->no_rekam_medik && $model->carabayar_id != DocoConstants::CARA_BAYAR_BPJS) {
                $model->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
                return DocoHelpers::response($model->errors,422,'TipePasienForm');
            }
            if ($model->validate()) {
                $is_retensi = isset($post['is_retensi']) ? $post['is_retensi'] : 0;
                if($is_retensi == true) {
                    $response = [
                        'metadata' => [
                            'status' => 422
                        ],
                        'response' => [
                            'title' => 'Pasien sudah diretensi dan tidak bisa melakukan pendaftaran',
                            'text' => ' ',
                            'data' => $is_retensi
                        ]
                    ];
                    return DocoHelpers::response($response);
                }

                $dataPasien = null;
                $form_pendaftaran = ($request->post('form_pendaftaran')) ? $request->post('form_pendaftaran') : null;
                // if (isset()) {
                //     # code...
                // }
                if ($form_pendaftaran == 'penunjang') {
                    # code...
                    if ($request->post('is_bpjs') && $is_pasienrs == FALSE) {
                        $bpjsForm = new PasienBpjsForm;
                        $bpjsForm->jenis_pencarian = $request->post('jenis_pencarian'); -
                        $bpjsForm->asal_rujukan = $request->post('asal_rujukan'); -
                        $bpjsForm->jenis_kartu = $request->post('jenis_kartu'); -
                        $bpjsForm->tanggal_sep = $request->post('tanggal_sep'); -
                        $bpjsForm->jenis_pelayanan = $request->post('jenis_pelayanan'); -
                        $bpjsForm->no_rujukan_f = $request->post('no_rujukan_f');
                        $bpjsForm->scenario = 'rujukan';
                        $url = 'rujukan';
                        $postAttr = [
                            'nomor' => $bpjsForm->no_rujukan_f,
                            'asal_rujukan' => $bpjsForm->asal_rujukan,
                            'tglSEP' => $bpjsForm->tanggal_sep,
                        ];


                        if ($bpjsForm->jenis_pencarian == 2) {
                            $url = 'peserta';

                            if($request->post('no_kartu') == '' || $request->post('namapemilikasuransi') == '') {
                                if($request->post('no_kartu') == '') {
                                    $bpjsForm->addError('no_kartu', 'Nomor Kartu tidak boleh kosong');
                                }

                                if($request->post('namapemilikasuransi') == '') {
                                    $bpjsForm->addError('namapemilikasuransi', 'Nama Peserta tidak boleh kosong');
                                }
                                return DocoHelpers::response($bpjsForm->errors,422,'PasienBpjsForm');
                            }else {
                                $bpjsForm->nama_peserta_bpjs = $request->post('namapemilikasuransi');
                                $bpjsForm->no_kartu = $request->post('no_kartu');
                            }
                            $postAttr = [
                                'nokartu' => $bpjsForm->no_kartu,
                                'tglSEP' => $bpjsForm->tanggal_sep,
                                'isktp' => $bpjsForm->jenis_kartu == 1 ? 0 : 1,
                            ];

                            $bpjsForm->scenario = 'rujukan-manual-penunjang';
                        }
                        if($post['skipBpjs'] == true) {
                            if(!empty($model->tipe_pasien) && empty($model->no_rekam_medik)) {
                                $model->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
                                return DocoHelpers::response($model->errors,422,'TipePasienForm');
                            }
                            $bpjsForm->scenario = 'skip-bpjs';

                            if(!$bpjsForm->validate()) {
                                return DocoHelpers::response($bpjsForm->errors,422,'BpjsNewForm');
                            }
                        } else {
                            if ($bpjsForm->validate()) {
                                try {
                                    $response = $this->_restPendaftaran->post("allow-bpjs/{$url}", [
                                        'form_params' => $postAttr,
                                        'timeout' => 25, // Connection timeout 7 Detik
                                    ]);
                                    $body = json_decode($response->getBody(), true);
                                    if (isset($body['response']['metaData']['code'])) {
                                        if ($body['response']['metaData']['code'] != 200 || $body['response']['metaData']['code'] != '200') {
                                            return DocoHelpers::responseTemplate(
                                                400,
                                                'Error',
                                                [],
                                                [
                                                    'title' => Yii::t('fe', 'Peringatan!'),
                                                    'text' => $body['response']['metaData']['message'],
                                                    'message' => $body['response']['metaData']['message'],
                                                ]
                                            );
                                        } else {
                                                $dataPasien = isset($body['response']['response']) ? $body['response']['response'] : null;
                                        }
                                        // if ($body['response']['metaData']['code'] == 201) {
                                        //     return DocoHelpers::response([
                                        //         'response' => [
                                        //             'title' => 'Proses Gagal !',
                                        //             'text' => $body['response']['metaData']['message']
                                        //         ]
                                        //     ],422);
                                        // } else if ($body['response']['metaData']['code'] > 400) {
                                        //     $dataPasien = [
                                        //         'messages' => $body['response']['metaData']['message']
                                        //     ];
                                        // } else {
                                        //     $dataPasien = isset($body['response']['response']) ? $body['response']['response'] : null;
                                        // }
                                    } else {
                                        $dataPasien = [
                                            'messages' => 'Terjadi Kesalahan pada server bpjs'
                                        ];
                                    }
                                } catch (RequestException $e) {
                                    $dataPasien = [
                                        'messages' => 'Terjadi Kesalahan pada server bpjs',
                                        'error' => $e->getMessage()
                                    ];
                                } catch (\GuzzleHttp\Exception\ConnectException $e) {
                                    $dataPasien = [
                                        'messages' => 'Terjadi Kesalahan pada server bpjs'
                                    ];
                                }
                            }  else {
                                return DocoHelpers::response($bpjsForm->errors,422,'BpjsNewForm');
                            }
                        }
                    }
                } else {
                    # code...
                    if ($request->post('is_bpjs') && $is_pasienrs == FALSE) {
                        $bpjsForm = new PasienBpjsForm;
                        $bpjsForm->jenis_pencarian = $request->post('jenis_pencarian');
                        $bpjsForm->asal_rujukan = $request->post('asal_rujukan');
                        $bpjsForm->jenis_kartu = $request->post('jenis_kartu');
                        $bpjsForm->tanggal_sep = $request->post('tanggal_sep');
                        $bpjsForm->jenis_pelayanan = $request->post('jenis_pelayanan');
                        $bpjsForm->no_rujukan_f = $request->post('no_rujukan_f');
                        $bpjsForm->scenario = 'rujukan';
                        $url = 'rujukan';
                        $postAttr = [
                            'nomor' => $bpjsForm->no_rujukan_f,
                            'asal_rujukan' => $bpjsForm->asal_rujukan,
                            'tglSEP' => $bpjsForm->tanggal_sep,
                        ];

                        if ($bpjsForm->jenis_pencarian == 2) {
                            $url = 'peserta';
                            $bpjsForm->no_kartu = $request->post('no_kartu');
                            $postAttr = [
                                'nokartu' => $bpjsForm->no_kartu,
                                'tglSEP' => $bpjsForm->tanggal_sep,
                                'isktp' => $bpjsForm->jenis_kartu == 1 ? 0 : 1,
                            ];

                            $bpjsForm->scenario = 'rujukan-manual';
                        }

                        if($post['skipBpjs'] == true) {
                            if(!empty($model->tipe_pasien) && empty($model->no_rekam_medik)) {
                                $model->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
                                return DocoHelpers::response($model->errors,422,'TipePasienForm');
                            }
                            $bpjsForm->scenario = 'skip-bpjs';

                            if(!$bpjsForm->validate()) {
                                return DocoHelpers::response($bpjsForm->errors,422,'BpjsNewForm');
                            }
                        } else {
                            if ($bpjsForm->validate()) {
                                try {
                                    $response = $this->_restPendaftaran->post("allow-bpjs/{$url}", [
                                        'form_params' => $postAttr,
                                        'timeout' => 25, // Connection timeout 7 Detik
                                    ]);
                                    $body = json_decode($response->getBody(), true);
                                    if (isset($body['response']['metaData']['code'])) {
                                        if ($body['response']['metaData']['code'] != 200 || $body['response']['metaData']['code'] != '200') {
                                            return DocoHelpers::responseTemplate(
                                                400,
                                                'Error',
                                                [],
                                                [
                                                    'title' => Yii::t('fe', 'Peringatan!'),
                                                    'text' => $body['response']['metaData']['message'],
                                                    'message' => $body['response']['metaData']['message'],
                                                ]
                                            );
                                        } else {
                                                $dataPasien = isset($body['response']['response']) ? $body['response']['response'] : null;
                                        }
                                    } else {
                                        $dataPasien = [
                                            'messages' => 'Terjadi Kesalahan pada server bpjs'
                                        ];
                                    }
                                } catch (RequestException $e) {
                                    $dataPasien = [
                                        'messages' => 'Terjadi Kesalahan pada server bpjs',
                                        'error' => $e->getMessage()
                                    ];
                                } catch (\GuzzleHttp\Exception\ConnectException $e) {
                                    $dataPasien = [
                                        'messages' => 'Terjadi Kesalahan pada server bpjs'
                                    ];
                                }
                            }  else {
                                return DocoHelpers::response($bpjsForm->errors,422,'BpjsNewForm');
                            }
                        }
                    }
                }



                if (!empty($request->post('no_asuransi'))) {
                    $asuransiForm = new AsuransiForm;
                    $asuransiForm->nokartuasuransi = $request->post('no_asuransi');
                    $asuransiForm->namapemilikasuransi = $request->post('namapemilikasuransi');
                    $asuransiForm->nomorpokokperusahaan = $request->post('nomorpokokperusahaan');
                    $asuransiForm->kelastanggungan_id = $request->post('kelastanggungan_id');
                    $asuransiForm->namaperusahaan = $request->post('namaperusahaan');
                    $asuransiForm->masaberlakukartu = $request->post('masaberlakukartu');
                    $asuransiForm->status_konfirmasi = $request->post('status_konfirmasi');
                    $asuransiForm->nama_asuransi = $request->post('nama_asuransi');
                    if ($asuransiForm->validate()) {

                    } else {
                        return DocoHelpers::response($asuransiForm->errors,422,'AsuransiForm');
                    }
                }

                if ($model->groupcarabayar_id == DocoConstants::GROUP_ASURANSI_UCUP){
                    $modelPenanggungBiaya = new PenanggungBiayaForm;
                    $modelPenanggungBiaya->penanggungbiaya_nama = $request->post('penanggungbiaya_nama');
                    $modelPenanggungBiaya->namabagian = $request->post('namabagian');
                    $modelPenanggungBiaya->noindukkaryawan = $request->post('noindukkaryawan');
                    $modelPenanggungBiaya->instansi = $request->post('instansi');
                    $modelPenanggungBiaya->ruangcarabayar_id = $request->post('ruangcarabayar_id');

                    switch ($model->carabayar_id) {
                        case 44:
                            $scenario_penanggungbiaya = 'instansi';
                            break;
                        default:
                            $scenario_penanggungbiaya = 'rk_karyawan-personil';
                            break;
                    }

                    $modelPenanggungBiaya->scenario = $scenario_penanggungbiaya;
                    if ($modelPenanggungBiaya->validate()) {
                        if (!empty($request->post('noindukkaryawan'))){
                            if (!preg_match("/^[0-9][0-9]*$/", $modelPenanggungBiaya->noindukkaryawan)){
                                $modelPenanggungBiaya->addError('noindukkaryawan', "NIP hanya boleh angka");
                                return DocoHelpers::response($modelPenanggungBiaya->errors,422,'PenanggungBiayaForm');
                            }
                        }
                    } else {
                        return DocoHelpers::response($modelPenanggungBiaya->errors,422,'PenanggungBiayaForm');
                    }
                }

                return DocoHelpers::response(['response' => [
                    'messages' => 'Proses Berhasil',
                    'title' => 'Proses Berhasil',
                    'text' => 'Langkah Tipe pasien berhasil',
                    'pasien_bpjs' => $dataPasien
                ]]);
            } else {
                $error = DocoHelpers::parseError($model->errors,'TipePasienForm');
                if ($model->groupcarabayar_id == 419 && empty($model->no_asuransi)) {
                    $asuransiForm = new AsuransiForm;
                    $asuransiForm->nokartuasuransi = $request->post('no_asuransi');
                    $asuransiForm->namapemilikasuransi = $request->post('namapemilikasuransi');
                    if (empty($asuransiForm->namapemilikasuransi)) {
                        $asuransiForm->addError('namapemilikasuransi', 'Nama Peserta tidak boleh kosong');
                        $error = array_merge($error, DocoHelpers::parseError($asuransiForm->errors,'AsuransiForm'));
                    }
                }

                $response = [
                    'metadata' => [
                        'status' => 422
                    ],
                    'response' => [
                        'data' => $error
                    ]
                ];
                return DocoHelpers::response($response);
            }
        }
        return DocoHelpers::response([
            'messages' => 'Tidak ada yang diproses'
        ],422);
    }

    public function actionGetPendaftaranUmum()
    {
        try {
            $payloadRequest = Yii::$app->request;
            $q = $payloadRequest->get('q',null);
            $pendaftaran_id = $payloadRequest->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $tgl_pendaftaran = $payloadRequest->get('tgl_pendaftaran',null);
            $tgl_filter = [];
            if ($tgl_pendaftaran != null) {
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_pendaftaran . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_pendaftaran . ' 23:59:59'));
                $tgl_filter['tgl_pendaftaran_awal'] = $tgl_awal_format;
                $tgl_filter['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            }
            $requests = $this->_restPendaftaran->get('pendaftaran-rajal/get-data-pendaftaran-umum',[
                'query' => [
                    'q' => $q,
                    'pendaftaran_id' => $decryptPendaftaranId,
                    'advanced-filter' => $tgl_filter
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            $data = isset($response['response']['data']) ? $response['response']['data'] : [];
            if (isset($data['pendaftaran_id'])) {
                $data['primaryPendaftaran'] = DocoHelpers::encrypt($data['pendaftaran_id']);
                $data['primaryPasien'] = DocoHelpers::encrypt($data['pasien_id']);
            }
            $results = $data;

            return DocoHelpers::response(['results' => $results]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        }
    }

    public function actionGetPenanggungBiaya($q, $carabayar_id)
    {
        try {
            $payloadRequest = Yii::$app->request->get();
            $requests = $this->_restPendaftaran->get('pendaftaran-rajal/get-penanggung-biaya',[
                'query' => [
                    'q' => $q,
                    'carabayar_id' => $carabayar_id,
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            $data = isset($response['response']['data']) ? $response['response']['data'] : [];
            $results = $data;

            return DocoHelpers::response(['results' => $results]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        }
    }

    public function actionSimpanKunjunganV2($params)
    {
        $request = Yii::$app->request;
        if($request->post('instalasi_id')) {
            if($request->post('instalasi_id') == DocoConstants::INSTALASI_MCU) {
                $params = DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU];
            }
        }

        $payLoadRequest = [
            'tipe_pasien' => [],
            'kunjungan' => [],
            'pasien' => [],
            'rujukan' => [],
            'penanggung_jawab' => [],
            'pj_pasien' => [],
            'asuransi' => [],
            'bpjs' => [],
            'penanggungbiaya' => [],
            'keluarga_pasien' => [],
        ];

        switch ($params) {
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RJ;
                $default_scenario = "pendaftaran-rajal";
                $kunjungan_scenario = "kunjungan_rj_styp";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_IGD]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RD;
                $default_scenario = "pendaftaran-igd";
                $kunjungan_scenario = "kunjungan_rj_styp";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]:
                $instalasi_id = DocoConstants::INSTALASI_MCU;
                $default_scenario = "default";
                $kunjungan_scenario = "kunjungan_rj_styp";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_PENUNJANG]:
                $default_scenario = "default";
                $kunjungan_scenario = "kunjungan_penunjang_styp";
                break;
            default:
                $instalasi_id = DocoConstants::INSTALASI_ID_RI;
                $default_scenario = "default";
                $kunjungan_scenario = "kunjungan_ri_styp";
                break;
        }

        $modelPasien = new PasienForm;
        $modelRujukan = new RujukanForm;
        $modelKunjungan = new KunjunganForm;
        $modelTipePasien = new TipePasienForm;
        $modelPjPasien= new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelBpjs = new BpjsNewForm;
        $modelPenanggungBiaya = new PenanggungBiayaForm;
        $modelKp = new KeluargaPasienForm;

        if (!empty($request->post('KunjunganForm'))) {
            $modelKunjungan->scenario = $kunjungan_scenario;
        }

        if (!empty($request->post('PasienForm'))) {
            $modelPasien->attributes = $request->post('PasienForm');

            // Improvement multiple jenis identitas
            if (!empty($modelPasien->no_identitas_pasien)) {
                $data = array();
                $jenisIdentitas = $modelPasien->jenisidentitas ? $modelPasien->jenisidentitas : [];
                $noIdentitas = $modelPasien->no_identitas_pasien;

                foreach ($noIdentitas as $key => $value) {
                    if ($value) {
                        $data[] = [
                            'jenisidentitas' => array_key_exists($key, $jenisIdentitas) ? $jenisIdentitas[$key] : null,
                            'no_identitas_pasien' => $value
                        ];
                    }
                }

                $modelPasien->jenisidentitas = null;
                $modelPasien->no_identitas_pasien = null;
                $modelPasien->additional_identitas = !empty($data) ? json_encode($data) : null;
            }
            $modelPasien->namadepan = ($modelPasien->namadepan != '') ? $modelPasien->namadepan : null;
            $modelPasien->bahasa_sehari = ($modelPasien->bahasa_sehari != '') ? $modelPasien->bahasa_sehari : null;
            $modelPasien->alamatdepan = ($modelPasien->alamatdepan != '') ? $modelPasien->alamatdepan : null;

            $modelPasien->scenario = $default_scenario;
            $payLoadRequest['pasien'] = $modelPasien->attributes;
        }

        if (!empty($request->post('TipePasienForm'))) {
            $modelTipePasien->attributes = $request->post('TipePasienForm');
            $modelTipePasien->antrian_id = $request->post('antrian_id');
            $modelTipePasien->pendaftaranol_id = $request->post('pendaftaranol_id');
            /** Kondisi ketika Bpjs Error */
            if (!empty($modelPasien->no_rekam_medik)){
                $modelTipePasien->no_rekam_medik = $modelPasien->no_rekam_medik;
                $modelTipePasien->asalrujukan_id = 2; // WIP
            };

            /** Kondisi asal rujukan di MCU */
            if($params == DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]) {
                $modelTipePasien->asalrujukan_id = $request->post('asalrujukan_id_hidden');
            }
            $payLoadRequest['tipe_pasien'] = $modelTipePasien->attributes;
        }

        if (!empty($request->post('KeluargaPasienForm'))) {
            $keluargaPasien = $request->post('KeluargaPasienForm');
            if(!empty($keluargaPasien['keluarga_nama'])){
                $modelKp->attributes = $keluargaPasien;
                if ($modelKp->keluarga_pekerjaan_id == '') {
                    $modelKp->keluarga_pekerjaan_id = null;
                }
                $modelKp->keluarga_namadepan = ($modelKp->keluarga_namadepan != '') ? $modelKp->keluarga_namadepan : null;
                $modelKp->keluarga_pekerjaan_id = ($modelKp->keluarga_pekerjaan_id != '') ? $modelKp->keluarga_pekerjaan_id : null;
                $modelKp->keluarga_propinsi_id = ($modelKp->keluarga_propinsi_id != '') ? $modelKp->keluarga_propinsi_id : null;
                $modelKp->keluarga_kabupaten_id = ($modelKp->keluarga_kabupaten_id != '') ? $modelKp->keluarga_kabupaten_id : null;
                $modelKp->keluarga_kecamatan_id = ($modelKp->keluarga_kecamatan_id != '') ? $modelKp->keluarga_kecamatan_id : null;
                $modelKp->keluarga_kelurahan_id = ($modelKp->keluarga_kelurahan_id != '') ? $modelKp->keluarga_kelurahan_id : null;
                $modelKp->keluarga_hubungan = ($modelKp->keluarga_hubungan != '') ? $modelKp->keluarga_hubungan : null;
                $modelKp->alamatdepan = ($modelKp->alamatdepan != '') ? $modelKp->alamatdepan : null;
                $payLoadRequest['keluarga_pasien'] = $modelKp->attributes;
            }
        }

        if (!empty($request->post('PjpasienForm'))) {
            $modelPjPasien->attributes = $request->post('PjpasienForm');
            $modelPjPasien->pj_namadepan = ($modelPjPasien->pj_namadepan != '') ? $modelPjPasien->pj_namadepan : null;
            $modelPjPasien->pj_pekerjaan_id = ($modelPjPasien->pj_pekerjaan_id != '') ? $modelPjPasien->pj_pekerjaan_id : null;
            $modelPjPasien->pj_propinsi_id = ($modelPjPasien->pj_propinsi_id != '') ? $modelPjPasien->pj_propinsi_id : null;
            $modelPjPasien->pj_kabupaten_id = ($modelPjPasien->pj_kabupaten_id != '') ? $modelPjPasien->pj_kabupaten_id : null;
            $modelPjPasien->pj_kecamatan_id = ($modelPjPasien->pj_kecamatan_id != '') ? $modelPjPasien->pj_kecamatan_id : null;
            $modelPjPasien->pj_kelurahan_id = ($modelPjPasien->pj_kelurahan_id != '') ? $modelPjPasien->pj_kelurahan_id : null;
            $modelPjPasien->pj_hubungan = ($modelPjPasien->pj_hubungan != '') ? $modelPjPasien->pj_hubungan : null;
            $payLoadRequest['pj_pasien'] = $modelPjPasien->attributes;
        }
        if (!empty($request->post('BpjsNewForm'))
                && $request->post('is_bpjs')
                && empty($modelPasien->no_rekam_medik) && !$request->post('allow_notif_bpjs')) {
                $modelBpjs->attributes = $request->post('BpjsNewForm');
                $payLoadRequest['bpjs'] = $modelBpjs->attributes;
                $asuransi = $request->post('AsuransiForm');
                $payLoadRequest['bpjs']['nama_peserta'] = ($params == 'penunjang') ? $asuransi['namapemilikasuransi']: null;
                $payLoadRequest['bpjs']['nomorpokokperusahaan'] = ($params == 'penunjang') ?  $asuransi['nomorpokokperusahaan'] : null;
                $payLoadRequest['bpjs']['namaperusahaan'] = ($params == 'penunjang') ?  $asuransi['namaperusahaan'] : null;
                $payLoadRequest['bpjs']['skip_bpjs'] = ($params == 'penunjang') ? true : false;

                $dataBpjs = $request->post('BpjsNewForm');
                $payLoadRequest['bpjs']['jenis_peserta'] = isset($dataBpjs['jenis_peserta']) ? $dataBpjs['jenis_peserta'] : null;

                if ($payLoadRequest['bpjs']['ppk_rujukan'] == '') {
                    $payLoadRequest['bpjs']['ppk_rujukan'] = $request->post('ppk_rujukan_hidden');
                    $payLoadRequest['bpjs']['asal_rujukan'] = $request->post('asal_rujukan_hidden');
                }

                // Change penjamin to jenis peserta when bpjs registration
                $payLoadRequest['tipe_pasien']['penjamin_id'] = $modelBpjs->jenis_peserta;
        }

        if (!empty($request->post('RujukanForm'))) {
            $modelRujukan->attributes = $request->post('RujukanForm');
            $payLoadRequest['rujukan'] = $modelRujukan->attributes;
        }

        if (!empty($request->post('KunjunganForm'))) {
            $kunjungan = $request->post('KunjunganForm');
            $modelKunjungan->attributes = $kunjungan;
            $modelKunjungan->kelaspelayanan_id = $this->default_kp;
            $modelKunjungan->tindakan_karcis = $request->post('list_tindakan');
            if($params == 'mcu') {
                $list_paket = json_decode($request->post('list_penunjang','{}'), true);
                if(empty($list_paket)) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal!',
                            'text' => 'Paket MCU tidak boleh kosong.'
                        ]
                    ],422);
                }
                else {
                    $list_paket = $list_paket['paket'];
                    $tipepaket_id = $arrPaket = [];
                    if(!empty($list_paket)) {
                        foreach ($list_paket as $key => $value) {
                            $tipepaket_id[] = $value['id'];
                        }
                    }

                    $arrPaket = "[" . implode(",", $tipepaket_id) . "]";
                    $modelKunjungan->list_paket = $arrPaket;
                }

                if($modelTipePasien->is_kolektif == true){
                    $listPasienMcu = $request->post('listPasienMcu');
                    if(empty($listPasienMcu)) {
                        return DocoHelpers::response([
                            'response' => [
                                'title' => 'Proses Gagal!',
                                'text' => 'List Pasien MCU tidak boleh kosong.'
                            ]
                        ],422);
                    }
                    $modelKunjungan->list_pasien_mcu = $listPasienMcu;
                }
            }
            else {
                $modelKunjungan->list_penunjang = $request->post('list_penunjang','{}');
            }

            $modelKunjungan->dokterpengirim_id = ($modelKunjungan->dokterpengirim_id != '') ? $modelKunjungan->dokterpengirim_id : null;
            $modelKunjungan->styrujukaninstalasi_id = ($modelKunjungan->styrujukaninstalasi_id != '') ? $modelKunjungan->styrujukaninstalasi_id : null;

            $payLoadRequest['kunjungan'] = $modelKunjungan->attributes;
            if ($params == 'penunjang') {
                $listPenunjang = json_decode($modelKunjungan->list_penunjang,true);
                /*if (empty($listPenunjang ) && $modelKunjungan->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal!',
                            'text' => 'Tindakan tidak boleh kosong.'
                        ]
                    ], 422);
                }*/
            }
        }

        if (!empty($request->post('no_asuransi'))) {
            $asuransi = $request->post('AsuransiForm');
            $modelAsuransi->attributes = $asuransi;
            $modelAsuransi->nokartuasuransi = $request->post('no_asuransi');
            $payLoadRequest['asuransi'] = $modelAsuransi->attributes;
        }

        if (!empty($request->post('PenanggungBiayaForm'))) {
            $penanggungbiaya = $request->post('PenanggungBiayaForm');
            if(!empty($penanggungbiaya['penanggungbiaya_nama'])){
                $modelPenanggungBiaya->carabayar_id = $modelTipePasien->carabayar_id;
                $modelPenanggungBiaya->attributes = $penanggungbiaya;
                $modelPenanggungBiaya->ruangcarabayar_id = ($modelPenanggungBiaya->ruangcarabayar_id != '') ? $modelPenanggungBiaya->ruangcarabayar_id : null;
                $payLoadRequest['penanggungbiaya'] = $modelPenanggungBiaya->attributes;
            }
        }

        if(!empty($request->post('buatjanjipoli_id'))){
            $payLoadRequest['buatjanjipoli_id'] = $request->post('buatjanjipoli_id');
            $payLoadRequest['status_janji'] = !empty($request->post('status_janji')) ? $request->post('status_janji') : '';
        }

        if ($request->post('is_bbl')) {
            $payLoadRequest['is_bbl'] = $request->post('is_bbl');
            if (!$request->post('kelahiran_id')) {
                return (new DocoHelpers)->macroResponseJson(400, 'Mohon pilih data bayi', []);
            }
            $payLoadRequest['kelahiran_id'] = $request->post('kelahiran_id');
            $payLoadRequest['pendaftaran_ibu_id'] = $request->post('pendaftaran_ibu_id');
        }

        if ($request->post('is_ranap')) {
            $payLoadRequest['tipe_pasien']['no_rekam_medik'] = isset($request->post('TipePasienForm')['no_rekam_medik']) ? $request->post('TipePasienForm')['no_rekam_medik'] : '' ;
            $payLoadRequest['is_ranap'] = $request->post('is_ranap');
            $payLoadRequest['kelaspelayanan_selected'] = $request->post('kelaspelayanan_selected');
            $payLoadRequest['is_pasientitipan'] = $request->post('pasientitipan', null);
            $payLoadRequest['is_aps'] = $request->post('pasienaps');
            $payLoadRequest['pasien_admisi'] = $request->post('PasienAdmisiForm');
            $payLoadRequest['pendaftaranasal_id'] = $request->post('pendaftaranasal_id');
            // $payLoadRequest['pasien_admisi']['is_pasientitipan'] = ((int) $payLoadRequest['is_pasientitipan'] == 1) ? true : false;
            $payLoadRequest['pasien_admisi']['is_aps'] = ((int) $payLoadRequest['is_aps'] == 1) ? true : false;
            if($payLoadRequest['pasien_admisi']['is_pasientitipan'] == '1' && empty($payLoadRequest['pasien_admisi']['kamar_titipan_id']) && empty($payLoadRequest['pasien_admisi']['tempattidur_titipan_id'])) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal!',
                        'text' => 'Kamar tagihan belum di pilih.'
                    ]
                ], 422);
            }
            $modelKunjungan->kelaspelayanan_id = $request->post('kelaspelayanan_selected');
            $modelKunjungan->tgl_pendaftaran = $request->post('PasienAdmisiForm')['tgl_admisi'];
            $payLoadRequest['kunjungan']['ruangan_id'] = $request->post('KunjunganForm')['ruangan_id'];
            $payLoadRequest['kunjungan']['kelaspelayanan_id'] = $request->post('kelaspelayanan_selected');
        }
        $payLoadRequest['allow_bpjs'] = $request->post('allow_bpjs');

        if(!$modelKunjungan->validate()) {
            $errors = DocoHelpers::parseError($modelKunjungan->errors, 'KunjunganForm');
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }

        try {
            $sent = $this->_restPendaftaran->post('pendaftaran-'.$params.'/save-pendaftaran-'.$params, [
                    'form_params'=> $payLoadRequest
                ]
            );

            $response = json_decode($sent->getBody(), true);//simpanv2
            if (isset($response['metadata']['status'])) {
                $status = $response['metadata']['status'];
                if ($status == 422) {
                    $error = [];
                    if (isset($response['response']['data'])) {
                        $data = $response['response']['data'];
                        if (isset($data['kunjungan'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['kunjungan'],'KunjunganForm'));
                        }

                        if (isset($data['KunjunganForm[data]'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['KunjunganForm[data]'],'KunjunganForm'));
                        }

                        if (isset($data['tipe_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['tipe_pasien'],'TipePasienForm'));
                        }

                        if (isset($data['pasien_admisi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pasien_admisi'],'PasienAdmisiForm'));
                        }

                        if (isset($data['pj_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pj_pasien'],'PjpasienForm'));
                        }

                        if (isset($data['asuransi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['asuransi'],'AsuransiForm'));
                        }

                        if (isset($data['pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pasien'],'PasienForm'));
                        }

                        if (isset($data['bpjs'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['bpjs'],'BpjsNewForm'));
                        }

                        if (isset($data['keluarga_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['keluarga_pasien'],'KeluargaPasienForm'));
                        }

                        $response = [
                            'metadata' => [
                                'status' => 422
                            ],
                            'response' => [
                                'data' => $error
                            ]
                        ];
                    }
                }
            }
            return DocoHelpers::response($response);
        } catch(RequestException $e) {
            $contentGuzzle = json_decode($e->getResponse()->getBody(true));
            if (isset($contentGuzzle->metadata) && $contentGuzzle->metadata->status < 500) {
                return (new DocoHelpers)->macroResponseJson($contentGuzzle->metadata->status, $contentGuzzle->response->message, []);
            } else {
                return (new DocoHelpers)->macroResponseJson(500, 'Terjadi kesalahan pada server', []);
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionUpdateKunjunganV2($params, $id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        if($request->post('instalasi_id')) {
            if($request->post('instalasi_id') == DocoConstants::INSTALASI_MCU) {
                $params = DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU];
            }
        }
        $param_rs = $request->get('param_rs', null);

        $payLoadRequest = [
            'kunjungan' => [],
            'asuransi' => [],
            'penanggungbiaya' => []
        ];

        if(!empty($param_rs)) {
            $payLoadRequest['param_rs'] = $param_rs;
        }

        switch ($params) {
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RJ;
                $default_scenario = "pendaftaran-rajal";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_IGD]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RD;
                $default_scenario = "pendaftaran-igd";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]:
                $instalasi_id = DocoConstants::INSTALASI_MCU;
                $default_scenario = "default";
                break;
            default:
                $instalasi_id = DocoConstants::INSTALASI_ID_RI;
                $default_scenario = "default";
                break;
        }

        $modelPasien = new PasienForm;
        $modelRujukan = new RujukanForm;
        $modelKunjungan = new KunjunganForm;
        $modelTipePasien = new TipePasienForm;
        $modelPjPasien= new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelBpjs = new BpjsNewForm;
        $modelPenanggungBiaya = new PenanggungBiayaForm;
        $modelEditPendaftaran = new EditPendaftaranForm;

        $modelEditPendaftaran->jenis = $params;
        if (!empty($param_rs)) {
            $modelEditPendaftaran->scenario = ($param_rs != 'st-yusup') ? 'default' : 'st-yusup';
        } else {
            $modelEditPendaftaran->scenario = ($params != 'ranap') ? 'default' : 'ranap';
        }

        $is_bpjs = false;

        if (!empty($request->post('TipePasienForm'))) {
            $modelKunjungan->scenario = 'kunjungan_penunjang';
        } else {
            $modelKunjungan->scenario = 'with_mandatory_pjawab';
        }

        $dataPendaftaran = $this->_restPendaftaran->get('tra-pasien-rawat-jalan/view', [
            'query' => [
                'id' => $id,
                'jenis' => $params,
            ]
        ]);
        $body = json_decode($dataPendaftaran->getBody(), true);
        $attributes = $body['response'];
        //$modelEditPendaftaran->attributes = $attributes['data_update'];

        if (!empty($request->post('TipePasienForm'))) {
            $modelTipePasien->attributes = $request->post('TipePasienForm');
            $modelTipePasien->antrian_id = $request->post('antrian_id');
            $modelTipePasien->pendaftaranol_id = $request->post('pendaftaranol_id');
            /** Kondisi ketika Bpjs Error */
            if (!empty($modelPasien->no_rekam_medik)){
                $modelTipePasien->no_rekam_medik = $modelPasien->no_rekam_medik;
                $modelTipePasien->asalrujukan_id = 2; // WIP
            };

            /** Kondisi asal rujukan di MCU */
            if($params == DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]) {
                $modelTipePasien->asalrujukan_id = $request->post('asalrujukan_id_hidden');
            }
            //$payLoadRequest['tipe_pasien'] = $modelTipePasien->attributes;
        }

        if (!empty($request->post('PjpasienForm'))) {
            $modelPjPasien->attributes = $request->post('PjpasienForm');
            //$payLoadRequest['pj_pasien'] = $modelPjPasien->attributes;
        }

        if (!empty($request->post('BpjsNewForm'))
                && $request->post('is_bpjs')
                && empty($modelPasien->no_rekam_medik) && !$request->post('allow_notif_bpjs')) {
                $modelBpjs->attributes = $request->post('BpjsNewForm');
                //$payLoadRequest['bpjs'] = $modelBpjs->attributes;
                $modelEditPendaftaran->scenario = 'edit-bpjs';
                $is_bpjs = true;
                $modelEditPendaftaran->allow_bpjs = $request->post('allow_bpjs');
                $modelEditPendaftaran->is_bpjs = $is_bpjs;

        }

        if (!empty($request->post('RujukanForm'))) {
            $modelRujukan->attributes = $request->post('RujukanForm');
            //$payLoadRequest['rujukan'] = $modelRujukan->attributes;
        }

        if (!empty($request->post('KunjunganForm'))) {
            $kunjungan = $request->post('KunjunganForm');
            $modelKunjungan->attributes = $kunjungan;
            /* Default Jenis Kasus Penyakit Umum */
            $modelKunjungan->jeniskasuspenyakit_id = 23;
            $modelKunjungan->tindakan_karcis = $request->post('list_tindakan');
            if($params == 'mcu') {
                $list_paket = json_decode($request->post('list_penunjang','{}'), true);
                if(empty($list_paket)) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal!',
                            'text' => 'Paket MCU tidak boleh kosong.'
                        ]
                    ],422);
                }
                else {
                    $list_paket = $list_paket['paket'];
                    $tipepaket_id = $arrPaket = [];
                    if(!empty($list_paket)) {
                        foreach ($list_paket as $key => $value) {
                            $tipepaket_id[] = $value['id'];
                        }
                    }

                    $arrPaket = "[" . implode(",", $tipepaket_id) . "]";
                    $modelKunjungan->list_paket = $arrPaket;
                }

                if($modelTipePasien->is_kolektif == true){
                    $listPasienMcu = $request->post('listPasienMcu');
                    if(empty($listPasienMcu)) {
                        return DocoHelpers::response([
                            'response' => [
                                'title' => 'Proses Gagal!',
                                'text' => 'List Pasien MCU tidak boleh kosong.'
                            ]
                        ],422);
                    }
                    $modelKunjungan->list_pasien_mcu = $listPasienMcu;
                }
            }
            else {
                $modelKunjungan->list_penunjang = $request->post('list_penunjang','{}');
            }

            //$payLoadRequest['kunjungan'] = $modelKunjungan->attributes;
            if ($params == 'penunjang') {
                $listPenunjang = json_decode($modelKunjungan->list_penunjang,true);
                /*if (empty($listPenunjang ) && $modelKunjungan->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal!',
                            'text' => 'Tindakan tidak boleh kosong.'
                        ]
                    ], 422);
                }*/
            }
        }

        if (!empty($request->post('no_asuransi'))) {
            $asuransi = $request->post('AsuransiForm');
            $modelAsuransi->scenario = 'edit-pendaftaran';
            $modelAsuransi->attributes = $asuransi;
            $modelAsuransi->nokartuasuransi = $request->post('no_asuransi');
            $modelAsuransi->asuransipasien_id = $attributes['data_update']['asuransipasien_id'];
            $modelAsuransi->masaberlakukartu = !empty($modelAsuransi->masaberlakukartu)
                        ? date('Y-m-d', strtotime($modelAsuransi->masaberlakukartu)) : null;
            $payLoadRequest['asuransi'] = $modelAsuransi->attributes;
        }

        if (!empty($request->post('PenanggungBiayaForm'))) {
            $penanggungbiaya = $request->post('PenanggungBiayaForm');
            if(!empty($penanggungbiaya['penanggungbiaya_nama'])){
                $modelPenanggungBiaya->penanggungbiaya_id = $attributes['data_update']['penanggungbiaya_id'];
                //$modelPenanggungBiaya->pasien_id = $attributes['data_update']['pasien_id'];
                $modelPenanggungBiaya->carabayar_id = $modelTipePasien->carabayar_id;
                $modelPenanggungBiaya->attributes = $penanggungbiaya;
                $payLoadRequest['penanggungbiaya'] = $modelPenanggungBiaya->attributes;
            }
        }

        if(!empty($request->post('buatjanjipoli_id'))){
            //$payLoadRequest['buatjanjipoli_id'] = $request->post('buatjanjipoli_id');
            //$payLoadRequest['status_janji'] = !empty($request->post('status_janji')) ? $request->post('status_janji') : '';
        }

        if ($request->post('is_bbl')) {
            //$payLoadRequest['is_bbl'] = $request->post('is_bbl');
            if (!$request->post('kelahiran_id')) {
                return (new DocoHelpers)->macroResponseJson(400, 'Mohon pilih data bayi', []);
            }
            //$payLoadRequest['kelahiran_id'] = $request->post('kelahiran_id');
            //$payLoadRequest['pendaftaran_ibu_id'] = $request->post('pendaftaran_ibu_id');
        }
        if ($request->post('is_ranap')) {
            //$payLoadRequest['tipe_pasien']['no_rekam_medik'] = $request->post('TipePasienForm')['no_rekam_medik'];
            //$payLoadRequest['is_ranap'] = $request->post('is_ranap');
            //$payLoadRequest['kelaspelayanan_selected'] = $request->post('kelaspelayanan_selected');
            //$payLoadRequest['is_pasientitipan'] = $request->post('pasientitipan');
            //$payLoadRequest['is_aps'] = $request->post('pasienaps');
            //$payLoadRequest['pasien_admisi'] = $request->post('PasienAdmisiForm');
            //$payLoadRequest['pendaftaranasal_id'] = $request->post('pendaftaranasal_id');
            // $payLoadRequest['pasien_admisi']['is_pasientitipan'] = ((int) $payLoadRequest['is_pasientitipan'] == 1) ? true : false;
            //$payLoadRequest['pasien_admisi']['is_aps'] = ((int) $payLoadRequest['is_aps'] == 1) ? true : false;

            $modelEditPendaftaran->is_pasientitipan = $request->post('pasientitipan');
            $modelEditPendaftaran->is_aps = $request->post('pasienaps');
        }
        //$payLoadRequest['allow_bpjs'] = $request->post('allow_bpjs');

        /*$modelEditPendaftaran->tgl_admisi = date('d-m-Y H:i', strtotime($modelEditPendaftaran->tgl_admisi));
        $modelEditPendaftaran->keterangan = $attributes['data_update']['keterangan_pendaftaran'];
        $modelEditPendaftaran->nomor_urut = $attributes['selectedNomorUrut'];
        // case bpjs
        $jenisPelayanan = '';
        $data_bpjs = $attributes['data_bpjs'];
        if(!empty($data_bpjs)) {
            $peserta = isset($data_bpjs['peserta']) ? $data_bpjs['peserta'] : '';
            $jenisPelayanan = ($data_bpjs['jnsPelayanan'] == DocoConstants::KLS_PLYN_RANAP_BPJS)
                    ? 'Rawat Inap' : 'Rawat Jalan';
        }

        $modelEditPendaftaran->nosep = isset($data_bpjs['noSep']) ? $data_bpjs['noSep'] : '';
        $status_periksa = isset($attributes['data_update']['status_periksa_id'])
                ? $attributes['data_update']['status_periksa_id'] : null;

        $disabled = ($status_periksa == 1) ? false : true;
        //case ranap
        if($jenis_pendaftaran == 'ranap') {
            $modelEditPendaftaran->kamarruangan_nokamar = isset($attributes['data_update']['kamarruangan_nokamar'])
                    ? $attributes['data_update']['kamarruangan_nokamar']. ' - ' . $attributes['data_update']['no_tempattidur'] : null;
            $modelEditPendaftaran->pegawai_id = isset($attributes['data_update']['dokter_admisi_id'])
                    ? $attributes['data_update']['dokter_admisi_id'] : null;
            $status_periksa = isset($attributes['data_update']['status_ranap'])
                    ? $attributes['data_update']['status_ranap'] : null;

            $statusPeriksa = DocoConstants::STATUS_RANAP_BELUM_PERIKSA;

            $class = ($status_periksa === $statusPeriksa) ? 'tgl_admisi' : '';
            $disabled = ($status_periksa === $statusPeriksa) ? false : true;
        }*/

        $modelEditPendaftaran->pendaftaran_id = $attributes['data_update']['pendaftaran_id'];
        $modelEditPendaftaran->carabayar_id = $modelTipePasien->carabayar_id;
        $modelEditPendaftaran->group_carabayar = $modelKunjungan->group_carabayar;
        $modelEditPendaftaran->penjamin_id = $modelTipePasien->penjamin_id;
        $modelEditPendaftaran->ruangan_id = $modelKunjungan->ruangan_id;
        $modelEditPendaftaran->jeniskasuspenyakit_id = $modelKunjungan->jeniskasuspenyakit_id;
        $modelEditPendaftaran->kelaspelayanan_id = $modelKunjungan->kelaspelayanan_id;
        $modelEditPendaftaran->pegawai_id = $modelKunjungan->dokter_id;
        $modelEditPendaftaran->keterangan = $modelKunjungan->keterangan;
        $payLoadRequest['kunjungan'] = $modelEditPendaftaran->attributes;

        try {
            $response = [];
            $sent = $this->_restPendaftaran->put('inf-pasien/update-pendaftaran?id='.$id, [
                'form_params' => $payLoadRequest
            ]);

            $response = json_decode($sent->getBody(), true);

            if (isset($response['metadata']['status'])) {
                $status = $response['metadata']['status'];
                if ($status == 422) {
                    $error = [];
                    if (isset($response['response']['data'])) {
                        $data = $response['response']['data'];
                        if (isset($data['kunjungan'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['kunjungan'],'KunjunganForm'));
                        }

                        if (isset($data['asuransi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['asuransi'],'AsuransiForm'));
                        }

                        if (isset($data['penanggungbiaya'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['penanggungbiaya'],'PenanggungBiayaForm'));
                        }

                        $response = [
                            'metadata' => [
                                'status' => 422
                            ],
                            'response' => [
                                'data' => $error
                            ]
                        ];
                    }
                }
            }
            $response['response']['id'] = DocoHelpers::encrypt($id);
            return DocoHelpers::response($response);
        } catch(RequestException $e) {
            $contentGuzzle = json_decode($e->getResponse()->getBody(true));
            if (isset($contentGuzzle->metadata) && $contentGuzzle->metadata->status < 500) {
                return (new DocoHelpers)->macroResponseJson($contentGuzzle->metadata->status, $contentGuzzle->response->message, []);
            } else {
                return (new DocoHelpers)->macroResponseJson(500, 'Terjadi kesalahan pada server', []);
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionGetUpdateData($id)
    {
        $id = DocoHelpers::decrypt($id);
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $instalasi_id = $active_workspace['instalasi_id'];
        $ruanganId = $active_workspace['ruangan_id'];
        $jenis_pendaftaran = DocoConstants::PARAM_DFTR[$ruanganId];

        $response = $this->_restPendaftaran->get('tra-pasien-rawat-jalan/view', [
            'query' => [
                'id' => $id,
                'jenis' => $jenis_pendaftaran,
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        $attributes = $body['response'];

        return DocoHelpers::response($attributes);
    }

    public function actionModalCetakan($param)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $is_bpjs = $request->get('is_bpjs');
        $module = $this->_module;
        $title = Yii::t('fe', 'Data Berhasil Disimpan');

        return $this->renderAjax('partial/component/_modal_cetakan', get_defined_vars());
    }

    public function actionConfirmPendaftaran($param)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $instalasi_id = $request->get('instalasi_id', null);
        switch ($instalasi_id)  {
            case DocoConstants::INSTALASI_ID_RJ:
                $jenis = 'rajal';
                break;

            case DocoConstants::INSTALASI_ID_RD:
                $jenis = 'igd';
                break;

            case DocoConstants::INSTALASI_ID_PENUNJANG:
                $jenis = 'penunjang';
                break;

            case DocoConstants::INSTALASI_ID_RI:
                $jenis = 'ranap';
                break;

            case DocoConstants::INSTALASI_MCU:
                $jenis = 'mcu';
                break;

            default:
                $jenis = null;
                break;
        }
        $module = $this->_module;
        $title = Yii::t('fe', 'Konfirmasi');

        return $this->renderAjax('partial/component/_confirm_pendaftaran', get_defined_vars());
    }

    public function actionPrintSuratReferal()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-surat-referal.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-surat-referal',
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

    public function actionPrintSuratKeterangan()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-surat-keterangan.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-surat-keterangan',
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

    public function actionPrintTriase()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-triase.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $param = DocoConstants::PARAM_DFTR[$ruangan_id];
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param];
            $response = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/print-triase',
                [
                    'query' => $post,
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

    public function actionPrintLabelPenunjang()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-label-penunjang.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $param = DocoConstants::PARAM_DFTR[$ruangan_id];
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param];
            $response = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/print-label-penunjang',
                [
                    'query' => $post,
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

    public function actionPrintLabelPenunjangKecil()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-label-penunjang-kecil.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $param = DocoConstants::PARAM_DFTR[$ruangan_id];
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param];
            $response = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/print-label-penunjang-kecil',
                [
                    'query' => $post,
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

    public function actionCountSyncData()
    {
        return $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'inf-pasien/get-data-sync',
            'payload' => []
        ]);
    }

    /**
     * @method : get data last regist styp
     */
    public function actionGetKunjunganTerkahir()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $draw = $request->get('draw', 1);
        $param = $request->get('param', null);
        $pendaftaran_id = $request->get('pendaftaran_id',null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $data = [];

        $result = $response = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->guzzleExec($this->_restPendaftaran, [
                'url' => 'allow/get-kunjungan-sebelumnya',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ]
                ],
            ]);
            $no = $request->get('start',1);
            $response['rowNum'] = $no;
            if(!empty($response)) {
                $response['primaryPendaftaran'] = DocoHelpers::encrypt($response['pendaftaran_id']);
                $response['primary'] = DocoHelpers::encrypt($response['pendaftaran_id']);
                $response['primaryPasien'] = DocoHelpers::encrypt($response['pasien_id']);
                $response['pasien_id'] = DocoHelpers::encrypt($response['pasien_id']);
            }
            $data[] = $response;
            $result['data'] = $data;
            $result['recordsTotal'] = count($response);
            $result['recordsFiltered'] = count($response);
            return $result;
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (ErrorException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPasienBpjs($q, $penjamin_id)
    {

        try {
            $payloadRequest = Yii::$app->request->get();
            $requests = $this->_restPendaftaran->get('allow/get-pasien-bpjs',[
                'query' => [
                    'q' => $q,
                    'penjamin_id' => $penjamin_id,
                    'is_ranap' => isset($payloadRequest['is_ranap'])
                ]
            ]);
            $response = json_decode($requests->getBody(), true);

            $data = isset($response['response']['data']) ? $response['response']['data'] : [];
            // dump($data);die;
            $listData = [];
            foreach ($data as $key => $value) {
                $row = $value;
                // $row['id'] = $value['pasien_id'];
                // $value['tanggal_lahir'] = date('d-M-Y', strtotime($value['tanggal_lahir']));
                $row['value'] = $value['nokartuasuransi'];
                if(!isset($listData[$value['no_rekam_medik']])){
                    $listData[$value['no_rekam_medik']] = $row;
                }
            }

            return DocoHelpers::response($listData);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        }
    }

    public function getKonfigSystem() {
        try {
            $restPendaftaran =  $this->_restPendaftaran->get('allow/get-konfig-system');
            $response = json_decode($restPendaftaran->getBody(), true);
            $konfig = $response['response'];

            return $konfig;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionTujuanKunjunganBpjs()
    {
        $title = Yii::t('fe', 'Tujuan Kunjungan');
        $model = new BpjsNewForm();

        return $this->renderAjax('/daftar/partial/component/_modal_tujuan_kunjungan', get_defined_vars());
    }

    public function actionTujuanProsedurBpjs()
    {
        $title = Yii::t('fe', 'Prosedur Kunjungan');
        $model = new BpjsNewForm();

        return $this->renderAjax('/daftar/partial/component/_modal_tujuan_prosedur', get_defined_vars());
    }

    public function actionProsedurBpjs($param)
    {
        $model = new BpjsNewForm();
        $prosedurList = [];
        $cache = Yii::$app->cache;

        if ($param == DocoConstants::TUJUAN_KUNJ_BPJS_TRUE) { // Case prosedur berkelanjutan
            $title = Yii::t('fe', 'Prosedur Berkelanjutan');
            $subtitle = Yii::t('fe', 'Pilih prosedur berkelanjutan');
            $lookup_type = 'prosedur_lanjut_bpjs';
        } else { // Case prosedur tidak berkelanjutan
            $title = Yii::t('fe', 'Prosedur Tidak Berkelanjutan');
            $subtitle = Yii::t('fe', 'Pilih prosedur tidak berkelanjutan');
            $lookup_type = 'prosedur_tidak_lanjut_bpjs';
        }

        $dataCache = $cache->get($lookup_type);
        if(!$dataCache) {
            $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'allow-bpjs/pack-tujuan-kunjungan',
                'payload' => [
                    'query' => [
                        'lookup_type' => $lookup_type
                    ]
                ]
            ]);
            $dataCache = $result['list_data'];
            $cache->set($lookup_type,$dataCache);
        }

        $prosedurList = ArrayHelper::map($dataCache, 'lookup_kode','lookup_name');

        return $this->renderAjax('/daftar/partial/component/_modal_prosedur_bpjs', get_defined_vars());
    }

    public function actionAssesmentPelayananBpjs()
    {
        $payload = Yii::$app->request->get();
        $cache = Yii::$app->cache;
        $model = new BpjsNewForm();
        $assesmentList = [];

        $title = Yii::t('fe', 'Assesment Pelayanan');
        $subtitle = Yii::t('fe', 'Mengapa pelayanan ini tidak diselesaikan pada hari yang sama sebelumnya?');
        $lookup_type = 'assesmen_pelayanan_bpjs';
        $dataCache = $cache->get($lookup_type);
        if(!$dataCache) {
            $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'allow-bpjs/pack-tujuan-kunjungan',
                'payload' => [
                    'query' => [
                        'lookup_type' => $lookup_type
                    ]
                ]
            ]);
            $dataCache = $result['list_data'];
            $cache->set($lookup_type,$dataCache);
        }

        $assesmentList = ArrayHelper::map($dataCache, 'lookup_kode','lookup_name');
        if(ArrayHelper::getValue($payload, 'bedaPoli')) {
            ArrayHelper::remove($assesmentList, DocoConstants::ASSESMEN_PELAYANAN_BPJS_TUJUAN_KONTROL);
        }

        return $this->renderAjax('/daftar/partial/component/_modal_assesment_bpjs', get_defined_vars());
    }

    public function actionPrintAntrianPoli()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-antrian-poli.pdf";
        try {
            $pendaftaran_id = $request->get('pendaftaran_id',null); // jika antrian_id kosong bisa pake pendaftaran_id
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $antrian_id = $request->get('antrian_id', 0); // jika pendaftaran id kosong, biasanya pake antrian_id
            // $waktuestimasi = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/get-estimasi-time-antrian', [
            //         'query' => ['pendaftaran_id' => $decryptPendaftaranId,
            //                     'antrian_id' => $antrian_id]
            //     ]);
            // $waktuestimasi = json_decode($waktuestimasi->getBody(), true);
            $post = [
                    'pendaftaran_id'=> $decryptPendaftaranId,
                    'antrian_id' => empty($antrian_id) || $antrian_id == 0 ? 0 : $antrian_id,
                    'jumlah' => $request->get('jumlah', 1),
                    // 'waktuestimasi_mulai' => ArrayHelper::getValue($waktuestimasi, 'response.waktuestimasi_mulai'),
                    // 'waktuestimasi_berakhir' => ArrayHelper::getValue($waktuestimasi, 'response.waktuestimasi_berakhir'),
                ];

            if(Yii::$app->report->enabled){
              $urlReport = 'print-antrian-poliklinik';

        			return Yii::$app->report->exec($urlReport.'?'.http_build_query($post),[
        				'queryParameter' => $post,
        				'manualRender'=>function() use($post,$path){
        					$response = $this->serviceRest->post('inf-daftar-sepuluh-terakhir/print-antrian-poli', [
        						'form_params' => $post,
        						'save_to' => $path
        					]);

        					return DocoHelpers::previewPdf($path);
        				}
        			]);
        		}

            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-antrian-poli',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );
            // $return = DocoHelpers::responseJsonString($response->getBody(), null);
            // dump($return);exit;
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetKunjunganByNoRm()
    {
        try {
            $no_rekam_medik = Yii::$app->request->get('no_rekam_medik');
            $requests = $this->_restPendaftaran->get('allow/get-info-kunjungan-pasien',[
                'query' => [
                    'no_rekam_medik' => $no_rekam_medik
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        }
    }
}
