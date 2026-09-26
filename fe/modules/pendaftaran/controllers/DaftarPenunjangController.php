<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\pendaftaran\components\Lookup;

use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\TipePasienForm;
use app\modules\pendaftaran\models\PjpasienForm;
use app\modules\pendaftaran\models\MultiCarabayarForm;

use GuzzleHttp\Exception\RequestException;

use app\modules\pendaftaran\components\traits\PendaftaranTrait;

class DaftarPenunjangController extends DocoController
{
    use PendaftaranTrait;

    protected $_title = 'Pendaftaran Penunjang';
    protected $_module = '/pendaftaran/daftar-penunjang';
    protected $allowAction = ['*'];
    protected $_restPendaftaran;
    protected $_instalasi_id = DocoConstants::INSTALASI_ID_PENUNJANG;

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function actionIndex($loket_id = null)
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $loket_nama = '';
        $pasien_id = $carabayar_id = $penjamin_id = $ruangan_id = '';
        $params = 'penunjang';
        $module = $this->_module;
        $title = $this->_title;
        $modelPasien = new PasienForm;
        $modelKunjungan = new KunjunganForm;
        $tipePasien = new TipePasienForm;
        $modelRujukan = new RujukanForm;
        $modelPj = new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelBpjs = new BpjsNewForm;
        $multiPayer = new MultiCarabayarForm;
        if($active_workspace['ruangan_id'] == '28') {
            $modelKunjungan->scenario = 'kunjungan_penunjang';
        } else {
            $modelKunjungan->scenario = 'with_mandatory_pjawab';
        }
        $modelKunjungan->konfig_referral_required = $modelKunjungan->konfig_referral_required = ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE) == TRUE 
                                                    && strtoupper(ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE)) == 'TRUE';

        try {
            if (Yii::$app->request->post()) {
                return $this->actionSimpanKunjungan($params);
            }
            /** Menampilkan Form Pendaftaran */
            $instalasi_id = $this->_instalasi_id;
            $ruanganId = $active_workspace['ruangan_id'];
            $instalasi_workspace = $active_workspace['instalasi_id'];
            $dataForm = $this->getDataApi($instalasi_id, 2,null, $loket_id);
            $modelPasien->propinsi_id = isset($dataForm['defaultPropinsi']) ? $dataForm['defaultPropinsi'] : null;
            $modelPasien->kabupaten_id = isset($dataForm['defaultKota']) ? $dataForm['defaultKota'] : null;
            $rujukan_dari = $dataForm['rujukan_dari'];
            $modelKunjungan->instalasi_id = !empty($dataForm['loket_instalasi']) ? $dataForm['loket_instalasi'] : null;
            $render_pasien_data = [
                "tipePasien" => $tipePasien,
                "modelKunjungan" => $modelKunjungan,
                "modelRujukan" => $modelRujukan,
                'modelPasien' => $modelPasien,
                'modelBpjs' => $modelBpjs,
                'data_lookup' => $dataForm["lookup"],
                'data_master' => $dataForm["master"],
                'optionsProv' => $dataForm['optionsProv'],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => $penjamin_id,
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'is_penunjang' => true,
                'default_asal_rujukan' => $dataForm['default_asal_rujukan'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_hide_alias' => isset($dataForm['konfigSystem']['is_hide_alias']) ? $dataForm['konfigSystem']['is_hide_alias'] : null,
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer
            ];

            $render_kunjungan_data = [
                "modelKunjungan" => $modelKunjungan,
                "modelPj" => $modelPj,
                'data_lookup' => $dataForm["lookup"],
                'data_master' => $dataForm["master"],
                'kelaspelayanan' => $dataForm["kelas_pelayanan"],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => $penjamin_id,
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'modelAsuransi' => $modelAsuransi,
                'modelBpjs' => $modelBpjs,
                'default_jenis_penyakit' => $dataForm['default_jenis_penyakit'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_limit_tagihan' => $dataForm['konfigSystem']['is_limit_tagihan'],
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer,
                'optionsRuangan' => $dataForm['optionsRuangan'],
                'show_referal' => $dataForm['konfigSystem']['show_referal_pendaftaran'],
            ];
            
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        } catch (Exception $e) {
            $render_pasien_data =
            $render_kunjungan_data = [];
        }
        $jenisantrian_id = DocoConstants::JA_PNG;
        return $this->render('index', get_defined_vars());
    }

    public function actionSearchPasienSelect2() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $q = $request->get('q');

        try {
            $response = $this->_restPendaftaran->get('allow/search-pendaftaran?term='.$q, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];
            $list = $tmp = [];

            /** Prevent More Data */
            foreach ($data as $value) {
                if(!isset($tmp[$value['pendaftaran_id']])){
                    $tmp[$value['pendaftaran_id']] = $value;
                }
            }

            foreach($tmp as $v){
                $item = [
                    "id" => $v['pendaftaran_id'],
                    "text" => $v['no_rekam_medik']. " / " .$v['no_pendaftaran']. " / " .$v['nama_pasien'],
                    "data" => $v
                ];
                $list[] = $item;
            }

            return [
                'list' => $list,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionListDokter() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $query = http_build_query($request->get());

        try {
            $response = $this->_restPendaftaran->get('allow/search-dokter?'.$query, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];
            $list = [];

            foreach ($data as $value) {
                $item = [
                    "id" => $value['pegawai_id'],
                    "text" => $value['nama_pegawai'],
                ];
                $list[] = $item;
            }

            return $list;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionGetPendaftaranPasien($no_pendaftaran) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            $response = $this->_restPendaftaran->get('allow/search-pendaftaran?no_pendaftaran='.$no_pendaftaran, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'][0];

            return $data;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}