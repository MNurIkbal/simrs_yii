<?php
/**
 *
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of Sirs
 */

namespace app\modules\pendaftaran\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\components\traits\PendaftaranTrait;
use app\modules\api\models\BpjsForm;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\PasienAdmisiForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\TipePasienForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\PjpasienForm;
use app\modules\pendaftaran\models\MultiCarabayarForm;
use app\modules\pendaftaran\components\Lookup;
use app\components\Services\BpjsConfigService;

class DaftarRanapIndexProcess extends \app\components\DocoBaseProcessExtension
{
    protected $_title = 'Pendaftaran Rawat Inap';
    protected $_module = '/pendaftaran/daftar-ranap';
    protected $_restMaster;
    protected $_restPendaftaran;

    protected $_instalasi_id_ri = DocoConstants::INSTALASI_ID_RI;
    //use PendaftaranTrait;

    protected function getAttributes()
    {
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    protected function processFlow($controller)
    {
        $this->getAttributes();

        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $id_booking = $request->get('id_booking', null);
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (!empty($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }
        $active_workspace = $session->get('active_workspace');
        $loket_id = null;
        $loket_nama = '';
        $params = 'ranap';
        $isRanap = 1;
        $module = $this->_module;
        $title = $this->_title;
        $modelPasien = new PasienForm;
        $modelKunjungan = new KunjunganForm();
        $modelAdmisi = new PasienAdmisiForm();
        $modelBpjs = new BpjsNewForm();
        $modelBpjs->scenario = "skdpranap";
        // dump($modelBpjs->scenario);die;

        $tipePasien = new TipePasienForm();
        $modelRujukan = new RujukanForm();
        $modelPjPasien = new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $multiPayer = new MultiCarabayarForm;
        $modelAdmisi->scenario = 'with_mandatory_pjawab';
        $instalasi_id = $this->_instalasi_id_ri;
        $ruanganId = $active_workspace['ruangan_id'];
        $rujukRanap = [];
        $getDefaultAsalRujukan = ArrayHelper::getValue((new Lookup)->getValueFromLookupT(null, 'asal_rujukan'), 'kode_id', 1);
        $modelKunjungan->konfig_referral_required = $modelKunjungan->konfig_referral_required = ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE) == TRUE 
                                                    && strtoupper(ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE)) == 'TRUE';

        try {
            if (Yii::$app->request->post()) {
                return $controller->actionSimpanKunjungan($params);
            }
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $cache = Yii::$app->cache;
            $masterWarnaTempatTidur = $cache->getOrSet("warna-tempat-tidur", function() {
                $masterWarnaTempatTidur = $this->_restPendaftaran->get(
                    'allow-antrian/get-warna-tempat-tidur', [
                        'query' => [],
                    ]
                );
                return json_decode($masterWarnaTempatTidur->getBody(), true)['response']['warna_tempat_tidur'];
            });
            $dataForm = $this->getDataForm($instalasi_id, 2, null, null, null, $pendaftaran_id, $controller);
            $rujukan_dari = ArrayHelper::getValue($dataForm, 'rujukan_dari');
            $penjamin_umum = ArrayHelper::getValue($dataForm, 'default_penjamin');
            $rujukan_datang_sendiri = ArrayHelper::getValue($dataForm, 'default_asal_rujukan');
            $rujukRanap = ArrayHelper::getValue($dataForm, 'rujukRanap');
            $bpjs_config = (new BpjsConfigService)->getConfig();
            $modelBpjs->asal_rujukan = $bpjs_config->ppkPelayanan;
            $render_pasien_data = [
                'modelPasien' => $modelPasien,
                'tipePasien' => $tipePasien,
                'data_lookup' => $dataForm["lookup"],
                'data_master' => $dataForm["master"],
                'optionsProv' => $dataForm['optionsProv'],
                "carabayar" => $dataForm['cara_bayar'],
                'listResponses' => $dataForm['lookup'],
                'listRujukan' => $dataForm['asal_rujukan'],
                'carabayarOptions' => $dataForm['carabayarOptions'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                'modelBpjs' => $modelBpjs,
                'modelRujukan' => $modelRujukan,
                'isRanap' => $isRanap,
                'default_asal_rujukan' => $dataForm['default_asal_rujukan'],
                'rujukRanap' => $dataForm['rujukRanap'],
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer
            ];

            $render_kunjungan_data = [
                "modelKunjungan" => $modelKunjungan,
                'data_lookup' => $dataForm["lookup"],
                'data_master' => $dataForm["master"],
                // "pemilihanDokter" => $pemilihanDokter,
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'default_jenis_penyakit' => $dataForm['default_jenis_penyakit'],
                'rujukRanap' => $dataForm['rujukRanap'],
            ];

            $render_ranap_data = [
                'modelBpjs' => $modelBpjs,
                "modelKunjungan" => $modelKunjungan,
                'modelAdmisi' => $modelAdmisi,
                'jeniskasus' => $dataForm['jeniskasus'],
                'data_master' => $dataForm["master"],
                'kelaspelayanan' => $dataForm['kelas_pelayanan'],
                'carabayar' => $dataForm['cara_bayar'],
                'listResponses' => $dataForm['lookup'],
                'listRujukan' => $dataForm['asal_rujukan'],
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'instalasi_id' => $instalasi_id,
                'id_booking' => $id_booking,
                'param' => $params,
                'data_lookup' => $dataForm["lookup"],
                'modelPj' => $modelPjPasien,
                'modelAsuransi' => $modelAsuransi,
                'is_limit_tagihan' => $dataForm['konfigSystem']['is_limit_tagihan'],
                'rujukRanap' => $dataForm['rujukRanap'],
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer,
                'show_referal' => $dataForm['konfigSystem']['show_referal_pendaftaran'],
            ];
        } catch (\Exception $e) {
            $render_pasien_data = $render_kunjungan_data = [];
        }
        return $controller->render('index', get_defined_vars());
    }

    protected function getDataForm(
        $instalasi_id = null,
        $default = null,
        $pendaftaranol_id = null,
        $loket_id = null,
        $janji_id = null,
        $pendaftaran_id = null,
        $controller = null
    )
    {
        $result = $controller->getDataApi($instalasi_id, $default, $pendaftaranol_id, $loket_id, $janji_id, $pendaftaran_id, $controller);
        return $result;
    }
}
