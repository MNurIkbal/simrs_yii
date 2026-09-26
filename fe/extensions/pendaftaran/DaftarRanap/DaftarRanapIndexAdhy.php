<?php
/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of Sirs
 */

namespace app\extensions\pendaftaran\DaftarRanap;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
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
use app\modules\pendaftaran\processes\DaftarRanapIndexProcess;

class DaftarRanapIndexAdhy extends DaftarRanapIndexProcess
{    
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
        $tipePasien = new TipePasienForm();
        $modelRujukan = new RujukanForm();
        $modelPjPasien = new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $multiPayer = new MultiCarabayarForm;
        $modelAdmisi->scenario = 'with_mandatory_pjawab';
        $instalasi_id = $this->_instalasi_id_ri;
        $ruanganId = $active_workspace['ruangan_id'];
        $rujukRanap = [];

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
                'klasifikasiKamar' => $dataForm['klasifikasiKamar'],
            ];
        } catch (\Exception $e) {
            $render_pasien_data = $render_kunjungan_data = [];
        }

        return $controller->render('@app/extensions/pendaftaran/views/daftar-ranap/indexAdhy', get_defined_vars());
    }
}