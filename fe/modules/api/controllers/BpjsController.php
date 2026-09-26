<?php
/**
 * @var created by ijal
 */

namespace Doco\api\controllers;

use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\Services\Contracts\BpjsInterface;
use app\components\Services\BpjsService;
use app\components\DocoConstants;
use yii\web\Response;
use Yii;
use yii\helpers\Html;

class BpjsController extends DocoController
{

    protected $_restPendaftaran;

    protected $bpjsService;

    const TIME_OUT = 7;

    protected $allowAction = ['*'];

    public function __construct($id, $module, $config = [], BpjsInterface $bpjsService)
    {
        $this->bpjsService = $bpjsService;
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    /**
     * Check no_asuransi BPJS
     * @param param input no_asuransi
     * @return mixed response json
     */
    public function actionPeserta()
    {
        $request = Yii::$app->request;
        // $no_asuransi = $request->post('no_asuransi'); // Nomor Peserta BPJS / Nomor KTP
        $post = $request->post();
        $post['tglSEP'] = date('Y-m-d', strtotime($post['tglSEP']));

        try {
            $response = $this->_restPendaftaran->post('allow-bpjs/peserta', [
                'form_params' => $post,
                'connect_timeout' => self::TIME_OUT, // Connection timeout
            ]);
            $body = json_decode($response->getBody(), true);
            return $response->getBody();

        } catch (RequestException $e) {
            return DocoHelpers::response('401', $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::response('401', $e->getMessage());
        }
        // "cURL error 28: Resolving timed out after 16 milliseconds (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)"
    }

    /**
     */
    public function actionRujukan()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        try {
            $response = $this->_restPendaftaran->post('allow-bpjs/rujukan', [
                'form_params' => $post,
                'connect_timeout' => self::TIME_OUT, // Connection timeout
            ]);
            $body = json_decode($response->getBody(), true);
            return $response->getBody();
        } catch (RequestException $e) {
            return DocoHelpers::response('401', $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::response('401', $e->getMessage());
        }
    }

    /**
     */
    public function actionListPoli()
    {
        $request = Yii::$app->request;

        $response = $this->_restPendaftaran->post('allow-bpjs/list-poli');
        $body = json_decode($response->getBody(), true);
        return $response->getBody();
    }

    public function actionReferensiPoli($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $cache = \Yii::$app->cache;
        if ($cache->get("bpjs-referensi-poli")) {
            return $cache->get("bpjs-referensi-poli");
        }
        $response = $this->_restPendaftaran->post('allow-bpjs/referensi-poli', [
            'form_params' => ['q' => $q],
        ]);
        $response = json_decode($response->getBody(), true);
        $bridgeRes = $response['response'];
        $results = [];
        if ($bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['poli'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        $cache->set("bpjs-referensi-poli",['results' => $results]);
        return ['results' => $results];
    }

    public function actionReferensiDiagnosa($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $response = $this->_restPendaftaran->post('allow-bpjs/referensi-diagnosa', [
            'form_params' => ['q' => $q],
        ]);
        $response = json_decode($response->getBody(), true);
        $bridgeRes = $response['response'];
        $results = [];
        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['diagnosa'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionReferensiFaskes($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $response = $this->_restPendaftaran->post('allow-bpjs/referensi-faskes', [
            'form_params' => ['q' => $q],
        ]);
        $response = json_decode($response->getBody(), true);
        $bridgeRes = $response['response'];
        $results = [];
        if ($bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['faskes'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionCreateSep()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $post['user'] = Yii::$app->docoVars->user("nama");

        $response = $this->_restPendaftaran->post('allow-bpjs/create-sep', [
            'form_params' => $post,
        ]);
        $body = json_decode($response->getBody(), true);
        return $response->getBody();
    }

    public function actionDeleteSampleSep()
    {
        $request = Yii::$app->request;
        $no_sep = $request->get('sep');

        $post = [
            'noSep' => $no_sep,
            'user' => Yii::$app->docoVars->user("nama"),
        ];

        $response = $this->_restPendaftaran->post('allow-bpjs/delete-sep', [
            'form_params' => $post,
        ]);

        $body = json_decode($response->getBody(), true);
        return $response->getBody();
    }

    public function actionReferensiFaskesNew()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $asal_rujukan = $request->get('asal_rujukan', null);
        $term = $request->get('search');
        // $response = $this->_restPendaftaran->post('allow-bpjs/referensi-faskes', [
        //     'form_params' => ['q' => $term, 'asal_rujukan' => $asal_rujukan],
        // ]);
        // $response = json_decode($response->getBody(), true);
        // $bridgeRes = $response['response'];
        $bridgeRes = $this->bpjsService->referensiFaskes($term, $asal_rujukan);
        $results = [];
        if ($bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['faskes'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionReferensiDiagnosaNew()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('search');

        // $response = $this->_restPendaftaran->post('allow-bpjs/referensi-diagnosa', [
        //     'form_params' => ['q' => $term],
        // ]);
        // $response = json_decode($response->getBody(), true);
        // $bridgeRes = $response['response'];
        $bridgeRes = $this->bpjsService->referensiDiagnosa($term);
        $results = [];
        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['diagnosa'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionCreateSepNew()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $post['user'] = Yii::$app->docoVars->user("nama");
        $post['tujuan'] = "IGD";
        $post['cob'] = 0;
        $post['eksekutif'] = 0;
        $post['no_rujukan_f'] = '';
        $post['tanggal_sep'] = $post['tglSep'];
        $post['jenis_pelayanan'] = $post['jnsPelayanan'];
        $post['kelas_rawat'] = $post['klsRawat'];
        $post['no_rekam_medik'] = $post['noMR'];
        $post['asal_rujukan'] = $post['asalRujukan'];
        $post['tanggal_rujukan'] = $post['tglRujukan'];
        $post['no_rujukan'] = $post['noRujukan'];
        $response = $this->_restPendaftaran->post('allow-bpjs/create-sep-new', [
            'form_params' => $post,
        ]);

        $body = json_decode($response->getBody(), true);
        return $response->getBody();
    }

    public function actionReferensiDokter($jnsPelayanan, $tglSep, $poliTujuan)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // $response = $this->_restPendaftaran->get('allow-bpjs/referensi-dokter', [
        //     'query' => [
        //         'pelayanan' => $jnsPelayanan,
        //         'tglSep' => date('Y-m-d', strtotime($tglSep)),
        //         'spesialis' => $poliTujuan,
        //     ],
        // ]);
        // $bridgeRes = json_decode($response->getBody(), true);
        // $bridgeRes = $response['response'];
        $bridgeRes = $this->bpjsService->referensiDokter($jnsPelayanan, $tglSep, $poliTujuan);
        $results = [];
        if (!empty($bridgeRes) && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['list'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionReferensiDpjp()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('search');
        $plyn = $request->get('pelayanan', null);
        $tglSep = $request->get('tgl', null);
        $poli = $request->get('poli', null);
        $first_search = $request->get('first_search', null);
        $cacheId = $request->get('cacheId', null); // case dpjp melayani  with key dpjp-melayani form JS
        if(empty($cacheId)) { // case dpjp skdp 
            $cacheId = $plyn.$tglSep.$poli;
        }
        $dataCache = Yii::$app->cache->get($cacheId);
        $results = [];

        if (!empty($cacheId) && !empty($dataCache)) {
            foreach ($dataCache as $key => $each) {
                if(!empty($term)) {
                    if (strpos(strtolower($each['nama']),strtolower($term)) !== FALSE) {
                        $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
                    }
                } else {
                    $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
                }
            }
            return ['results' => $results];
        }

        if ($poli == strtoupper(DocoConstants::PARAM_DFTR[DocoConstants::WS_IGD])){
            $plyn = 1;
        }
        
        // Yii::$app->cache->delete($cacheId);
        // $response = $this->_restPendaftaran->post('allow-bpjs/referensi-dpjp', [
        //     'form_params' => [
        //         'q' => $term,
        //         'pelayanan' => $plyn,
        //         'tglSep' => $tglSep,
        //         'poli' => $poli
        //     ],
        // ]);
        // $response = json_decode($response->getBody(), true);
        // $bridgeRes = $response['response'];
        // dump($bridgeRes);die;
        $bridgeRes = $this->bpjsService->referensiDpjp($plyn, $tglSep, $poli);
        // $debug = $this->bpjsService->debug();
        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200 ) {
            // var_dump($bridgeRes['response']['list']);die;
            $list = !empty($bridgeRes['response']['list']) ? $bridgeRes['response']['list'] : [];
            foreach ($list as $key => $each) {
                if(!empty($term)) {
                    if (strpos(strtolower($each['nama']),strtolower($term)) !== FALSE) {
                        $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
                    }
                } else {
                    $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
                }
            }
            if(!empty($cacheId) && !empty($list) && empty($term)) { 
                Yii::$app->cache->set($cacheId, $list, 43200);
            }
        }
        return ['results' => $results];
    }

    public function actionReferensiKelasRawat()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('search');
        $response = $this->_restPendaftaran->post('allow-bpjs/referensi-kelas-rawat', [
            'form_params' => ['q' => $term],
        ]);
        $response = json_decode($response->getBody(), true);
        $bridgeRes = $response['response'];
        $results = [];
        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['list'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionReferensiPoliNew()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('search');
        // $response = $this->_restPendaftaran->post('allow-bpjs/referensi-poli', [
        //     'form_params' => ['q' => $term],
        // ]);
        // $response = json_decode($response->getBody(), true);
        // $bridgeRes = $response['response'];
        $bridgeRes = $this->bpjsService->referensiPoli($term);
        $results = [];
        if (isset($bridgeRes['metaData']['code']) && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['poli'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionReferensiProvinsi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $cache = \Yii::$app->cache;
        if ($cache->get("bpjs-referensi-provinsi")) {
            return $cache->get("bpjs-referensi-provinsi");
        }
        $request = Yii::$app->request;
        $term = $request->get('search');
        // $response = $this->_restPendaftaran->post('allow-bpjs/referensi-provinsi', [
        //     'form_params' => ['q' => $term],
        // ]);
        // $response = json_decode($response->getBody(), true);
        // $results = ['data' => $response['response']['response']['list']];
        $bridgeRes = $this->bpjsService->referensiProvinsi();
        $results = ['data' => $bridgeRes['response']['list']];
        
        $cache->set("bpjs-referensi-provinsi", $results);
        return $results;
    }

    public function actionReferensiKabupaten()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $payload = Yii::$app->request->get();
        $result = [];
        $statusCode = 400;
        $message = 'Mohon isi kode propinsi';
        if (isset($payload['kode_propinsi']) && !empty($payload['kode_propinsi'])) {
            // $out = $this->_restPendaftaran->post('allow-bpjs/referensi-kabupaten', [
            //     'form_params' => ['kode_propinsi' => $payload['kode_propinsi']],
            // ]);
            // $response = json_decode($out->getBody(), true);
            $response = $this->bpjsService->referensiKabupaten($payload['kode_propinsi']);
            if ($response['metaData']['code'] == 200) {
                $result = $response['response']['list'];
                $statusCode = 200;
                $message = 'Berhasil mendapatkan data kabupaten';
            } else {
                $message = 'Terjadi kesalahan pada integrasi BPJS, silakan coba lagi secara berkala';
            }
        }
        \Yii::$app->response->statusCode = $statusCode;
        return [
            'data' => $result,
            'message' => $message,
        ];
    }

    public function actionReferensiKecamatan()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $payload = Yii::$app->request->get();
        $result = [];
        $statusCode = 400;
        $message = 'Mohon isi kode kabupaten';
        if (isset($payload['kode_kabupaten']) && !empty($payload['kode_kabupaten'])) {
            // $out = $this->_restPendaftaran->post('allow-bpjs/referensi-kecamatan', [
            //     'form_params' => ['kode_kabupaten' => $payload['kode_kabupaten']],
            // ]);
            // $response = json_decode($out->getBody(), true);
            $response = $this->bpjsService->referensiKecamatan($payload['kode_kabupaten']);
            if ($response['metaData']['code'] == 200) {
                $result = $response['response']['list'];
                $statusCode = 200;
                $message = 'Berhasil mendapatkan data kabupaten';
            } else {
                $message = 'Terjadi kesalahan pada integrasi BPJS, silakan coba lagi secara berkala';
            }
        }
        \Yii::$app->response->statusCode = $statusCode;
        return [
            'data' => $result,
            'message' => $message,
        ];
    }

    public function actionCariSep()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $param = trim($request->post('nosep'));
        $pendaftaran_id = trim($request->post('id', null));
        if(!empty($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }
        $additional = $request->get('additional', null);
        $response = $this->_restPendaftaran->post('allow/cek-sep', [
            'form_params' => ['nosep' => $param, 'pendaftaran_id' => $pendaftaran_id],
        ]);
        $body = json_decode($response->getBody(), true);
        $bodyAdditional = [];
        
        if($body['response'] == true) {
            $resBpjs = $this->_restPendaftaran->post('allow-bpjs/referensi-cari-sep', [
                'form_params' => ['nosep' => $param],
            ]);

            $bodyBpjs = json_decode($resBpjs->getBody(), true);

            if($additional) {
                if($additional == 'hak_kelas') {
                    $isKtp = false;
                    $noKartu = $bodyBpjs['response']['response']['peserta']['noKartu'];
                    $tglSep = $bodyBpjs['response']['response']['tglSep'];
                    
                    $res = $this->_restPendaftaran->get('allow-bpjs/referensi-peserta?nokartu='.$noKartu.'&tglSEP='.$tglSep.'&isKtp='.$isKtp);

                    $bodyAdditional = json_decode($res->getBody(), true);
                }
            }

            $result = [
                'data' => $bodyBpjs,
                'additional' => $bodyAdditional
            ];

            return $result;
        }
        else {
            $result = [
                'message' => 'No. SEP sudah digunakan oleh Pasien lain.',
            ];

            return $result;
        }
    }

    public function actionReferensiKepesertaan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $results = [];
        $term = $request->get('search');
        $response = $this->_restPendaftaran->post('allow-bpjs/referensi-kepesertaan', [
            'form_params' => ['q' => $term],
        ]);
        $response = json_decode($response->getBody(), true);
        if ($response['metadata']['status'] == 200) {
            $data = $response['response'];
            foreach($data as $key => $value) {
                $results[] = ['id' => $value['penjamin_id'], 'text' => $value['penjamin_nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionCariSuratKontrol()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $param = trim($request->get('no_surat_kontrol'));
        $result = [];
        if (empty($param)) {
           return false;
        }
        // Yii::error($request->get());
        $response = $this->bpjsService->cariSuratKontrol($param);
        // Yii::error($response);
        if ($response['metaData']['code'] == 200) {
            return $response;
        } else {
            return DocoHelpers::responseTemplate(
                400,
                'Error',
                [],
                [
                    'title' => Yii::t('fe', 'Peringatan!'),
                    'text' => $response['metaData']['message'],
                    'message' => $response['metaData']['message'],
                ]
            );
        }
    }
    
    public function actionReferensiProcedure()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('search');

        // $response = $this->_restPendaftaran->post('allow-bpjs/referensi-diagnosa', [
        //     'form_params' => ['q' => $term],
        // ]);
        // $response = json_decode($response->getBody(), true);
        // $bridgeRes = $response['response'];
        $bridgeRes = $this->bpjsService->referensiProcedure($term);
        $results = [];
        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['procedure'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionReferensiObatPrb()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('search');

        $bridgeRes = $this->bpjsService->refObatPRB($term);
        $results = [];
        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['list'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }
}
