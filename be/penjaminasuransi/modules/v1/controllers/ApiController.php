<?php
/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\cache\Cache;
use Doco\components\DocoHelpers;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoConstants;
use Doco\Services\Vendors\PendaftaranService;

use app\modules\v1\models\Lookup;
use app\modules\v1\payload\GroupTarifEklaimPayload;

class ApiController extends DocoActiveController 
{
    public $modelClass = '';
    const NEW_CLAIM = 'new_claim';
    const SET_CLAIM_DATA = 'set_claim_data';
    const DELETE_CLAIM = 'delete_claim';
    const KEY_SUC_SYSTEM = 'sucess';
    const KEY_FAILED_SYSTEM = 'failed';
    protected $coder = '';
    protected $type = '';
    protected $tarif = '';
    protected $env = '';
    protected $payorId = '';
    protected $payorCd = '';

    public function init() {
        parent::init();
        $this->env = $this->getEnv();
        $this->coder = $this->getCoderNik();
        $this->type = DocoConstants::CLAIM_RAJAL;
        $this->tarif = $this->getTarif();
        $this->payorId = $this->getPayorId();
        $this->payorCd = $this->getPayorCd();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionSetKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new GroupTarifEklaimPayload;
        $model->attributes = $post['group_tarif'];
        $noPendaftaran = isset($post['no_pendaftaran']) ? $post['no_pendaftaran'] : null;

        if(empty($noPendaftaran)) {
            $errorMessage = 'No Pendaftaran tidak boleh kosong';
            return $this->responseJson(422, $errorMessage, $post);
        }

        $dataPendaftaran = $this->getDataPendaftaran($noPendaftaran);

        if(empty($dataPendaftaran)) {
            $errorMessage = 'No Pendaftaran tidak ditemukan';
            return $this->responseJson(422, $errorMessage, $dataPendaftaran);
        } else if ($dataPendaftaran && isset($dataPendaftaran['metadata']) && $dataPendaftaran['metadata']['status'] != 200) {
            $errorMessage = isset($dataPendaftaran['metadata']['message']) ? $dataPendaftaran['metadata']['message'] : 'Terjadi Kesalahan API';
            $errorCode = isset($dataPendaftaran['metadata']) ? $dataPendaftaran['metadata']['status'] : 500;
            return $this->responseJson($errorCode, $errorMessage, $dataPendaftaran);
        }

        $data = $this->generateDataKlaim($model, $dataPendaftaran);
        return $this->prosesKlaim($data, $this->type);
    }

    private function prosesKlaim($data, $type)
    {
        $newClaim['metadata']['method'] = self::NEW_CLAIM;
        $newClaim['data']['nomor_kartu'] = $data['no_kartu'];
        $newClaim['data']['nomor_sep'] = $data['no_sep'];
        $newClaim['data']['nomor_rm'] = $data['no_rekam_medik'];
        $newClaim['data']['nama_pasien'] = $data['nama_pasien'];
        $newClaim['data']['tgl_lahir'] = $data['tgl_lahir'];
        $newClaim['data']['gender'] = ($data['jeniskelamin'] == DocoConstants::LAKI) ? DocoConstants::JENIS_LAKI : DocoConstants::JENIS_PEREMPUAN;
        $response = json_decode(DocoHelpers::restInacbgs($newClaim), true);
        if ($response['metadata']['code'] != 200) {
            if ($response['metadata']['code'] != 400 && $response['metadata']['error_no'] != 'E2007') {
                return [
                    'status' => $response['metadata']['code'],
                    'title' => $response['metadata']['error_no'],
                    'text' => isset($response['metadata']['message']) ? $response['metadata']['message'] : 'Terjadi Kesalahan pada method '. self::NEW_CLAIM,
                    'data' => $data
                ];
            } else if ($response['metadata']['code'] == 400 && $response['metadata']['error_no'] == 'E2007') {
                return [
                    'status' => $response['metadata']['code'],
                    'title' => $response['metadata']['error_no'],
                    'text' => isset($response['metadata']['message']) ? $response['metadata']['message'] : 'Terjadi Kesalahan pada method '. self::NEW_CLAIM,
                    'data' => isset($response['duplicate']) ? $response['duplicate'] : $data
                ];
            } else {
                return [
                    'status' => $response['metadata']['code'],
                    'title' => $response['metadata']['error_no'],
                    'text' => isset($response['metadata']['message']) ? $response['metadata']['message'] : 'Terjadi Kesalahan pada method '. self::NEW_CLAIM,
                    'data' => $data
                ];
            }
        }

        $prosesKlaim['metadata']['method'] = self::SET_CLAIM_DATA;
        $prosesKlaim['metadata']['nomor_sep'] = $data['no_sep'];
        $prosesKlaim['data'] = [
            'nomor_sep' => $data['no_sep'],
            'nomor_kartu' => $data['no_kartu'],
            'tgl_masuk' => $data['tgl_masuk'],
            'tgl_pulang' => $data['tgl_keluar'],
            'kelas_rawat' => $data['jenis_kelasrawat'],
            'birth_weight' => $data['berat_lahir'],
            'discharge_status' => $data['carapulang_id'],
            'diagnosa' => $data['diagnosa_primer'],
            'procedure' => $data['diagnosa_sekunder'],
            'nama_dokter' => $data['nama_dokter'],
            'tarif_rs' => [
                'prosedur_non_bedah' => $data['tarif_rs']['prosedur_non_bedah'],
                'prosedur_bedah' => $data['tarif_rs']['prosedur_bedah'],
                'konsultasi' => $data['tarif_rs']['konsultasi'],
                'tenaga_ahli' => $data['tarif_rs']['tenaga_ahli'],
                'keperawatan' => $data['tarif_rs']['keperawatan'],
                'penunjang' => $data['tarif_rs']['penunjang'],
                'radiologi' => $data['tarif_rs']['radiologi'],
                'laboratorium' => $data['tarif_rs']['laboratorium'],
                'pelayanan_darah' => $data['tarif_rs']['pelayanan_darah'],
                'rehabilitasi' => $data['tarif_rs']['rehabilitasi'],
                'kamar' => $data['tarif_rs']['kamar_akomodasi'],
                'rawat_intensif' => $data['tarif_rs']['rawat_intensif'],
                'obat' => $data['tarif_rs']['obat'],
                'obat_kronis' => $data['tarif_rs']['obat_kronis'],
                'obat_kemoterapi' => $data['tarif_rs']['obat_kemoterapi'],
                'alkes' => $data['tarif_rs']['alkes'],
                'bmhp' => $data['tarif_rs']['bmhp'],
                'sewa_alat' => $data['tarif_rs']['sewa_alat'],
            ],
            'payor_id' => $this->payorId,
            'payor_cd' => $this->payorCd,
            'kode_tarif' => $this->tarif,
            'coder_nik' => $this->coder,
        ];

        if($type == DocoConstants::CLAIM_RANAP) {
            // if ($data['naik_kelas'] == 'kelas_4' || $data['naik_kelas'] == 4) {
            //     $naikKelas = 'vip';
            // } elseif ($data['naik_kelas'] == 'kelas_5' || $data['naik_kelas'] == 5) {
            //     $naikKelas = 'vvip';
            // } else {
            //     $naikKelas = $data['naik_kelas'];
            // }

            $prosesKlaim['data']['jenis_rawat'] = DocoConstants::CLAIM_RANAP;
            // $prosesKlaim['data']['adl_sub_acute'] = $data['adl_subacute'];
            // $prosesKlaim['data']['adl_chronic'] = $data['adl_cronic'];
            // $prosesKlaim['data']['upgrade_class_ind'] = $data['is_naikkelas'];
            // $prosesKlaim['data']['upgrade_class_class'] = $naikKelas;
            // $prosesKlaim['data']['upgrade_class_los'] = $data['lama_rawatkelas'];
            // $prosesKlaim['data']['icu_indikator'] = $data['is_rawatintensif'];
            // $prosesKlaim['data']['icu_los'] = $data['lama_rawatintensif'];
            // $prosesKlaim['data']['ventilator_hour'] = $data['ventilator'];
        } else {
            $prosesKlaim['data']['jenis_rawat'] = DocoConstants::CLAIM_RAJAL;
            $prosesKlaim['data']['tgl_pulang'] = $data['tgl_masuk'];
            // $prosesKlaim['data']['tarif_poli_eks'] = $data['tarif_poli_eks'];
        }

        $klaim = json_decode(DocoHelpers::restInacbgs($prosesKlaim), true);
        if ($klaim['metadata']['code'] != 200) {
            $hapus = $this->actionDeleteKlaim($data['no_sep']);
            return [
                'status' => $klaim['metadata']['code'],
                'title' => $klaim['metadata']['error_no'],
                'text' => $klaim['metadata']['message'],
                'data' => $data
            ];
        }

        return [
            'status' => $klaim['metadata']['code'],
            'title' =>  $klaim['metadata']['message'],
            'text' => self::KEY_SUC_SYSTEM,
            'data' => $data
        ];
    }

    private function getCoderNik()
    {
        $getData = $this->getLookupByType($this->env)
        ->andWhere(['lookup_name' => 'coder_nik'])
        ->one();

        return $getData->lookup_value;
    }

    private function getPayorId()
    {
        $getDataPayorId = $this->getLookupByType($this->env)
        ->andWhere(['lookup_name' => 'payor_id'])
        ->one();

        return $getDataPayorId->lookup_value;
    }

    private function getPayorCd()
    {
        $getDataPayorCd = $this->getLookupByType($this->env)
        ->andWhere(['lookup_name' => 'payor_cd'])
        ->one();

        return $getDataPayorCd->lookup_value;
    }

    private function getEnv()
    {
        $conf = @parse_ini_file(''.realpath(Yii::$app->basePath).'/config/env/.env', true);
        $vclaim = isset($conf['inacbg']['env_vclaim']) ? $conf['inacbg']['env_vclaim'] :'';
        $env = ($vclaim == DocoConstants::LOOKUP_BPJS_LIVE) ? DocoConstants::LOOKUP_BPJS_LIVE : DocoConstants::LOOKUP_BPJS;

        return $env;
    }

    private function getTarif()
    {
        $getData = $this->getLookupByType($this->env)
        ->andWhere(['lookup_name' => 'default_tarif'])
        ->one();

        return $getData->lookup_value;
    }

    private function getDataPendaftaran($noPendaftaran)
    {
        $data = [];
        $params['no_pendaftaran'] = $noPendaftaran;
        $getData = (new PendaftaranService)->getInfoPatient($params);

        $data = (isset($getData['response']) && $getData['metadata']['status'] == 200) ? $getData['response'] : $getData;

        return $data;
    }

    private function generateDataKlaim($payload, $dataPendaftaran)
    {  
        $isRanap = empty($dataPendaftaran['pasienadmisi_id']) ? false : true;
        $data = [
            'no_sep' => isset($dataPendaftaran['nosep']) ? $dataPendaftaran['nosep'] : null,
            'no_kartu' => isset($dataPendaftaran['nokartuasuransi']) ? $dataPendaftaran['nokartuasuransi'] : null,
            'no_rekam_medik' => isset($dataPendaftaran['no_rekam_medik']) ? $dataPendaftaran['no_rekam_medik'] : null,
            'nama_pasien' => isset($dataPendaftaran['nama_pasien']) ? $dataPendaftaran['nama_pasien'] : null,
            'tgl_lahir' => isset($dataPendaftaran['tanggal_lahir']) ? $dataPendaftaran['tanggal_lahir'] : null,
            'jeniskelamin' => isset($dataPendaftaran['jeniskelamin_id']) ? $dataPendaftaran['jeniskelamin_id'] : null,
            'tgl_masuk' => isset($dataPendaftaran['tgl_masuk']) ? $dataPendaftaran['tgl_masuk'] : null,
            'tgl_keluar' => isset($dataPendaftaran['tgl_keluar']) ? $dataPendaftaran['tgl_keluar'] : null,
            'jenis_kelasrawat' => isset($dataPendaftaran['jnspelayanan']) ? $dataPendaftaran['jnspelayanan'] : null,
            'berat_lahir' => '',
            'carapulang_id' => 1,
            'diagnosa_primer' => '#',
            'diagnosa_sekunder' =>'#',
            'nama_dokter' => isset($dataPendaftaran['dokter_nama']) ? $dataPendaftaran['dokter_nama'] : null,
            'tarif_rs' => $payload,
        ];

        if($isRanap) {
            $this->type = DocoConstants::CLAIM_RANAP;
        }

        return $data;
    }

    public function actionDeleteKlaim($nosep)
    {
        $hapusklaim = [
            'metadata' => [
                'method' => self::DELETE_CLAIM,
            ],
            'data' => [
                'nomor_sep' => $nosep,
                'coder_nik' => $this->coder,
            ],
        ];

        return json_decode(DocoHelpers::restInacbgs($hapusklaim), true);
    }
}