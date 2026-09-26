<?php

namespace Doco\igd\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\Pelayanan\PelayananHelpers;
use GuzzleHttp\Exception\RequestException;

use app\modules\igd\components\traits\AsesmenDpjpTrait;
use app\modules\igd\components\traits\FormulirTriaseTrait;
use app\modules\igd\components\traits\AsesmenKeperawatanTrait;
use app\modules\igd\components\traits\AsesmenMedisTrait;
use app\modules\igd\components\traits\AsesmenDokterTrait;
use app\modules\igd\components\traits\ImplementasiTrait;
use app\modules\igd\components\traits\ReturObatTrait;
use app\modules\igd\components\traits\KesimpulanTrait;
use app\modules\igd\components\traits\RiwayatPasienTrait;
use app\modules\igd\components\traits\PermintaanMakanTrait;
use app\components\Traits\CathlabTrait;
use app\components\Traits\HistoryPatientTrait;
use app\components\Traits\TindakanBmhpTrait;
use app\components\Traits\GiziTrait;
use app\components\Traits\ResumeMedisTrait;
use app\components\Traits\HistoryFisioTrait;
use app\modules\igd\components\traits\PemeriksaanResepturTrait;
use app\components\Traits\Pelayanan\NursingNoteTrait;
use app\components\Traits\Pelayanan\TerraMedikTrait;
use app\components\Traits\SuratKeteranganTrait;
use app\components\Traits\ICareTrait;

use app\components\Traits\RujukanPasienTrait;
use app\modules\igd\components\traits\PemeriksaanPartografTrait;
use app\components\Traits\MonitoringTtvTrait;
use app\components\Traits\ObservasiEwsTrait;
use app\components\Traits\SbarTrait;

class PemeriksaanIgdController extends DocoController
{
    use AsesmenDpjpTrait;
    use FormulirTriaseTrait;
    use AsesmenKeperawatanTrait;
    use AsesmenMedisTrait;
    use AsesmenDokterTrait;
    use ImplementasiTrait;
    use ReturObatTrait;
    use KesimpulanTrait;
    use RiwayatPasienTrait;
    use PermintaanMakanTrait;
    use CathlabTrait;
    use HistoryPatientTrait;
    use TindakanBmhpTrait;
    use GiziTrait;
    use ResumeMedisTrait;
    use PemeriksaanResepturTrait;
    use RujukanPasienTrait;
    use NursingNoteTrait;
    use HistoryFisioTrait;
    use TerraMedikTrait;
    use SuratKeteranganTrait;
    use ICareTrait;
    use PemeriksaanPartografTrait;
    use MonitoringTtvTrait;
    use ObservasiEwsTrait;
    use SbarTrait;

    protected $allowAction = ['*'];

    protected $_restIgd;
    protected $_restApotek;
    protected $_user_identity;
    protected $_pendaftaran_id;
    protected $_ruangan_id;
    protected $_instalasi_id;
    protected $_pegawai_id;
    protected $_data_pasien;
    protected $_data_pegawai;
    protected $_pasien_id;
    protected $_pasienadmisi_id;
    protected $_jeniskelamin;
    protected $_kelaspelayanan_id;
    protected $_restRanap;
    protected $_data_riwayat_pasien;

    public function init()
    {
        parent::init();
        try {
            $docoVars = Yii::$app->docoVars;
            $this->_restIgd = Yii::$app->docoRest->igd;
            $this->_restApotek = Yii::$app->docoRest->apotek;
            $this->_restRanap = Yii::$app->docoRest->ranap;
            $this->_user_identity = Yii::$app->session->get('user_identity');
            if (empty($this->_user_identity)) return false;
            $this->_pendaftaran_id = PelayananHelpers::decryptId(Yii::$app->request->get('id', null));
            $this->_ruangan_id = $docoVars->workspace('ruangan_id') ? $docoVars->workspace('ruangan_id') : 1;
            $this->_instalasi_id = Yii::$app->docoVars->workspace('instalasi_id') ? Yii::$app->docoVars->workspace('instalasi_id') : null;
            $this->_pegawai_id = $docoVars->user('id_pegawai') ? $docoVars->user('id_pegawai') : 1;

            $data_pasien_igd = Yii::$app->cache->get('data-pasien-igd-' . $this->_pendaftaran_id);
            $data_pegawai = Yii::$app->cache->get('data-pegawai-' . $this->_pegawai_id);

            $data_riwayat_pasien = [];
            if(isset($data_pasien_igd['pasien_id'])){
                $data_riwayat_pasien = Yii::$app->cache->get('data-riwayat-pasien-' . isset($data_pasien_igd['pasien_id']) ? $data_pasien_igd['pasien_id'] : '');
            }
            if ((empty($data_pasien_igd) || empty($data_pegawai) || (!empty($data_pasien_igd) && !isset($data_pasien_igd['pendaftaran_id']))) && !empty($this->_pendaftaran_id)) {
                $resultApi = $this->guzzleExec($this->_restIgd, [
                    'url' => 'pemeriksaan-igd/get-api',
                    'payload' => [
                        'query' => [
                            'id' => $this->_pendaftaran_id,
                            'pegawai_id' => $this->_pegawai_id,
                            'cppt' => true
                        ]
                    ]
                ]);

                // CPPT
                $resultApi['data_pasien']['cppt'] = !empty($resultApi['cppt']) ? $resultApi['cppt'] : [];

                $cacheDataPasien = isset($resultApi['data_pasien']) ? $resultApi['data_pasien'] : [];
                Yii::$app->cache->set('data-pasien-igd-' . $this->_pendaftaran_id, $cacheDataPasien, 3600);
                $data_pasien_igd = $cacheDataPasien;

                $cacheDataPegawai = isset($resultApi['data_pegawai']) ? $resultApi['data_pegawai'] : [];
                Yii::$app->cache->set('data-pegawai-' . $this->_pegawai_id, $cacheDataPegawai, 3600);
                $data_pegawai = $cacheDataPegawai;

                $cacheDataRiwayat = isset($resultApi['data_riwayat_pasien']) ? $resultApi['data_riwayat_pasien'] : [];
                $cacheDataRiwayat = isset($resultApi['data_riwayat_pasien']) ? $resultApi['data_riwayat_pasien'] : [];
                if(isset($data_pasien_igd['pasien_id'])){
                    Yii::$app->cache->set('data-riwayat-pasien-' . $data_pasien_igd['pasien_id'], $cacheDataRiwayat, 30);
                }
                $data_riwayat_pasien = $cacheDataRiwayat;
            }

            $this->_data_pasien = $data_pasien_igd;
            $this->_pasien_id = isset($this->_data_pasien['pasien_id']) ? $this->_data_pasien['pasien_id'] : null;
            $this->_pasienadmisi_id = isset($data_pasien['pasienadmisi_id']) ? $data_pasien['pasienadmisi_id'] : null;
            $this->_jeniskelamin = !empty($data_pasien_igd['jeniskelamin']) ? $data_pasien_igd['jeniskelamin'] : null;
            $this->_kelaspelayanan_id = !empty($data_pasien_igd['kelaspelayanan_id']) ? $data_pasien_igd['kelaspelayanan_id'] : null;
            $this->_data_pegawai = $data_pegawai;
            $this->_data_riwayat_pasien = $data_riwayat_pasien;
            // cathlab requirement
            $this->instalasi = 'igd';
            $this->baseUrl = 'pemeriksaan-igd';
            $this->restGeneral = $this->_restIgd;
            $this->_restGeneralRanap = $this->_restRanap;
            $this->type = 'RD';
        } catch(\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terjadi kesalahan"));
        }
    }

    public function actionPeriksa($id)
    {
        $request = Yii::$app->request;
        $pulang = $request->get('state', null);
        $urlBack = !is_null($pulang) ? Url::to(['/igd/inf-pasien-pulang']) : Url::to(['/igd']);
        $encrytedPendaftaranId = $id;
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $this->_pendaftaran_id = $pendaftaran_id;
        $cpptId = $request->get('cpptId', null);
        $thirdApp = Yii::$app->params->thirdApp;
        $tabs = Yii::$app->params->igdTabs;
        $iCare = Yii::$app->params->iCare;
        $userIdentity = Yii::$app->session->get('user_identity');
        $cathlabTabs = isset($this->_data_pasien['hasCathlab']) && $this->_data_pasien['hasCathlab'] ? true : false;
        $showTtvTab = isset($this->_data_pasien['showTtvTab']) && $this->_data_pasien['showTtvTab'] ? true : false;
        $showEwsTab = isset($this->_data_pasien['showEwsTab']) && $this->_data_pasien['showEwsTab'] ? true : false;
        $showSbarTab = isset($this->_data_pasien['showSbarTab']) && $this->_data_pasien['showSbarTab'] ? true : false;
        $id_ruangan = $this->_ruangan_id;
        $hasAccessIcare = DocoHelpers::checkButtonAccess('/igd/pemeriksaan-igd', 'icare');
        $ruanganperiksa_id = DocoHelpers::encrypt(ArrayHelper::getValue($this->_data_pasien, 'ruangan_id', null));
        try {
            $title = Yii::t('fe', 'Pemeriksaan pasien');
            $data_pasien = $this->_data_pasien;
            $data_pasien = array_merge($data_pasien, ['riwayat_pasien' => $this->_data_riwayat_pasien]);
            if ($data_pasien['status_periksa_id'] == DocoConstants::STATUS_PERIKSA_SET_DOKTER) {
                $response = $this->helper->guzzleExec($this->_restIgd, [
                    'url' => 'inf-pasien-igd/update-status-periksa',
                    'payload' => [
                        'query' => [
                            'id' => $encrytedPendaftaranId
                        ]
                    ]
                ]);
                // Update cache
                $cache = Yii::$app->cache;
                $cacheData = $cache->get('data-pasien-igd-' . $data_pasien['pendaftaran_id']);
                $cacheData['status_periksa_id'] = $response['data']['status_periksa_id'];
                $cacheData['status_periksa_nama'] = $response['data']['status_periksa_nama'];
                $cache->set('data-pasien-igd-' . $data_pasien['pendaftaran_id'], $cacheData, 3600);

                // Update data_pasien
                $data_pasien['status_periksa_id'] = $response['data']['status_periksa_id'];
                $data_pasien['status_periksa_nama'] = $response['data']['status_periksa_nama'];
            }

            $query_params_icare = [
                'icare_identifier' => ArrayHelper::getValue($data_pasien, 'icare_identifier'),
                'no_pendaftaran' => ArrayHelper::getValue($data_pasien, 'no_pendaftaran'),
                'no_rekam_medik' => ArrayHelper::getValue($data_pasien, 'no_rekam_medik'),
                'nama_pasien' => ArrayHelper::getValue($data_pasien, 'nama_pasien'),
                'id_pegawai' => $userIdentity['id_pegawai'],
            ];

            return $this->render('periksa', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function dataInstruksiTindakan($pendaftaran_id = "")
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restIgd->get(
                'inf-pasien-igd/data-instruksi-tindakan',
                [
                    'query' => ['pendaftaran_id' => $pendaftaran_id]
                ]
            );
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionCetakRincianTagihan($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $path = Yii::getAlias("@download") . "/rincian-tagihan-pasien-igd.pdf";
        try {
            $response = $this->_restIgd->get('pemeriksaan-igd/cetak-rincian-tagihan?id=' . $id, [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * List dropdown of doctor
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionAllDokterList()
    {
        $dokterList = $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'pemeriksaan-igd/all-dokter-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);

        $payload = Yii::$app->request->get('payload', []);
        if ($payload['page'] == 1 && !empty($dokterList['data'])) {
            $data = $dokterList['data'];
            $allData = [
                'id' => '%',
                'text' => \Yii::t('fe', 'Semua Dokter')
            ];
            array_unshift($data, $allData);
            $dokterList['data'] = $data;
        }

        return $dokterList;
    }

    public function getStatusPeriksa()
    {
        $result = false;
        if (isset($this->_data_pasien['status_periksa_id'])) {
            if (
                $this->_data_pasien['status_periksa_id'] == DocoConstants::STATUS_PULANG ||
                $this->_data_pasien['status_periksa_id'] == DocoConstants::STATUS_RUJUK_RAWAT_INAP
            ) {
                $result = true;
            }
        }

        return [
            'result' => $result
        ];
    }

}
