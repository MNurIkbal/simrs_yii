<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\components\traits\PendaftaranTrait;

use app\modules\pendaftaran\models\KetersediaanKamarForm;

class InformasiPasienRujukRanapController extends DocoController
{

    protected $_titleInfo = "Informasi Pasien Rujuk Rawat Inap";
    protected $_module = 'pendaftaran/informasi-pasien-rujuk-ranap/';
    protected $_restMaster;
    protected $_restPendaftaran;
    protected $_restIgd;

    protected $_id_carabayar_bpjs;
    protected $_is_hide_alias;
    protected $_is_set_igdkeri;
    use PendaftaranTrait;

    const STATUS_BATAL = [402, 628, 453, 1058];
    const STATUS_PULANG = [4, 487];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restIgd = Yii::$app->docoRest->igd;
        $this->_id_carabayar_bpjs = null;

        $cache = Yii::$app->cache;
        if (!empty($cache->get('is_hide_alias'))) {
            $this->_is_hide_alias = $cache->get('is_hide_alias');
        } else {
            $requests = $this->_restPendaftaran->post('allow/get-konfig-system');
            $response = json_decode($requests->getBody(), true);
            $response = $response['response'];
            $cache->set('is_hide_alias', $response['is_hide_alias']);
            $this->_is_hide_alias = $response['is_hide_alias'];

            $cache->set('_is_set_igdkeri_rujuk_ranap', $response['is_set_igdkeri']);
            $this->_is_set_igdkeri = $response['is_set_igdkeri'];
        }

        if (!empty($cache->get('_is_set_igdkeri_rujuk_ranap'))) {
            $this->_is_set_igdkeri = $cache->get('_is_set_igdkeri_rujuk_ranap');
        } else {
            $requests = $this->_restPendaftaran->post('allow/get-konfig-system');
            $response = json_decode($requests->getBody(), true);
            $response = $response['response'];
            $cache->set('_is_set_igdkeri_rujuk_ranap', $response['is_set_igdkeri']);
            $this->_is_set_igdkeri = $response['is_set_igdkeri'];
        }
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
        $session = Yii::$app->session;
        $title = Yii::t('fe', "Informasi Pasien Rujuk Rawat Inap");
        $active_workspace = $session->get('active_workspace');

        $rest = $this->_restPendaftaran->post('inf-pasien-rujuk-ranap/pack-informasi-pasien', [
            'form_params' => []
        ]);
        $result = json_decode($rest->getBody(), true);
        $result = $result['response'];
        $carabayarList = isset($result['carabayar']) ? ArrayHelper::map($result['carabayar'], 'carabayar_id', 'carabayar_nama') : [];
        $ruanganList = isset($result['ruangan']) ? ArrayHelper::map($result['ruangan'], 'ruangan_id', 'ruangan_nama') : [];
        $statusList = isset($result['status_periksa']) ? ArrayHelper::map($result['status_periksa'], 'lookup_id', 'lookup_name') : [];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tglrujukranap'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tglrujukranap']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];

        } else {
            $tgl_awal = $tgl_akhir = date('Y-m-d');
        }
        if (isset($yiiRestfulParams['advanced-filter']['status_periksa_id'])) {
            $status_periksa = $yiiRestfulParams['advanced-filter']['status_periksa_id'];
            $yiiRestfulParams['advanced-filter']['status_periksa'] = $status_periksa;
            unset($yiiRestfulParams['advanced-filter']['status_periksa_id']);
        }
        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tglrujukranap']);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $response = $this->_restPendaftaran->get('inf-pasien-rujuk-ranap?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        // dump($body['response']['data']);die;
        $no = $request->get('start',1);
        if(!empty($body['response']['data'])) {
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $value['tglrujukranap'] = date('d-m-Y H:i:s', strtotime($value['tglrujukranap']));
                
                if (isset($this->_is_hide_alias) && $this->_is_hide_alias != false) {
                    $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . $value['nama_pasien'];
                } else {
                    $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . (isset($value['namadepan_pasien']) ? $value['namadepan_pasien'] : '') . ' ' . $value['nama_pasien'];
                }
                
                $value['rowNum'] = $no;
                $value['carabayar_penjamin'] = $value['carabayar_nama'] . ' / ' . $value['penjamin_nama'];
                $value['ruangan'] = $value['ruangan_nama'] . ' - ' . $value['kamarruangan_nokamar'];
                $value['can_cancel'] = false;
                $value['status_rujuk'] = null;
                $value['status_ketersediaan_kamar'] = null;
                if ($value['ket'] == 'RJ' || ($value['ket'] != 'RJ' && isset($this->_is_set_igdkeri) && $this->_is_set_igdkeri != false)) {
                    if(!empty($value['prev_status_periksa_id'])) {
                        $value['status_periksa_id'] = $value['prev_status_periksa_id'];
                        $value['status_periksa_nama'] = $value['prev_status_periksa_nama'];
                    }
                }

                if (!empty($value['pasienadmisi_id']) && !in_array($value['status_periksa_id'], self::STATUS_PULANG)) {
                    $value['status_rujuk'] = 'sedang_ranap';
                } else if (empty($value['pasienadmisi_id']) && $value['status_periksa_id'] == 433) {
                    if( strtotime($value['tglrujukranap']) > strtotime('-7 day') ) {
                        $value['status_rujuk'] = 'rujuk_ranap';
                        if(!empty($value['keter_kamartempattidur_id']) && $value['tempattidurtujuan_id'] != $value['keter_kamartempattidur_id']) {
                            $value['status_ketersediaan_kamar'] = 'user_suggestion';
                            $value['ruangan'] = $value['keter_ruangan_nama'] . ' - ' . $value['keter_kamarruangan_nokamar'] . ' - ' . $value['keter_kamartempattidur_no'];
                        }
                    } else {
                        $value['status_rujuk'] = 'expired';
                    }
                }
                
                if (in_array($value['status_periksa_id'], self::STATUS_BATAL)){
                    $value['status_rujuk'] = 'batal_ranap';
                }
                $value['primary'] = $primaryKey;
                $value['type'] = strtolower($value['ket']);
                $data[$key] = $value;
            }
        }
            
        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }

    public function actionKetersediaanKamar() 
    {
        $request = Yii::$app->request;
        $title     = Yii::t('fe', 'Ketersediaan Kamar');
        $modelKamar = new KetersediaanKamarForm;
        $payLoadRequest = [];
        $instalasi_id = DocoConstants::INSTALASI_ID_RI;

        if ($request->post()) {
            $kamar = $request->post('KetersediaanKamarForm');
            $modelKamar->attributes = $kamar;
            $modelKamar->pasien_id = DocoHelpers::decrypt($modelKamar->pasien_id);
            $payLoadRequest['kamar'] = $modelKamar->attributes;

            if ($modelKamar->validate()) {
                $response = $this->_restPendaftaran->post('inf-pasien-rujuk-ranap/save-ketersediaan-kamar', [
                    'form_params' => $payLoadRequest
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            }
            return DocoHelpers::response($modelKunjungan->errors,422,'KetersediaanKamarForm');
        } else {
            $dataForm = $this->getDataApi($instalasi_id, 2);
            $jeniskasus = $dataForm['jeniskasus'];
            $kelaspelayanan = $dataForm['kelas_pelayanan'];
            return $this->renderAjax('_modal_kamar', get_defined_vars());
        }
    }

    public function actionBatalRanap() 
    {
        $request = Yii::$app->request;
        $payLoadRequest = [];
        $instalasi_id = DocoConstants::INSTALASI_ID_RI;

        try {
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            $response = $this->_restPendaftaran->get('inf-pasien-rujuk-ranap/batal-ranap', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ],
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCetakSpri()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-dokumen-spri.pdf";
        try {
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);

            $prev_pendaftaran_id = $request->get('prev_no_pendaftaran',null);
            $decryptPrevPendaftaranId = DocoHelpers::decrypt($prev_pendaftaran_id);

            if(Yii::$app->report->enabled){
                if($request->get('type') == 'ri'){
                    $url = 'cetak-spri?pendaftaran_id='.$decryptPrevPendaftaranId;
                } else {
                    $url = 'cetak-spri?pendaftaran_id='.$decryptPendaftaranId.'&type='.$request->get('type');
                }
                return Yii::$app->report->exec($url);
            }

            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $response = $this->_restIgd->get('kesimpulan/cetak-spri',
                [
                    'query' => [
                        'pendaftaran_id' => $decryptPendaftaranId,
                        'prev_no_pendaftaran' => $decryptPrevPendaftaranId,
                        'type' => $request->get('type')
                    ],
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
}
