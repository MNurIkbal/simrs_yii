<?php

namespace app\components\Traits;

use Yii;

use app\components\DocoConstants;
use yii\web\Response;
use yii\helpers\ArrayHelper;

trait TindakanBmhpTrait
{
    /**
     * @var String $type
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $type;

    /**
     * @var String $serviceRest
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $serviceRest;

    /**
     * @var String $mainUrl
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $mainUrl;

    /**
     * @var String $frontendUrl
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $frontendUrl;

    /**
     * @var String $backendUrl
     * @author Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
     */
    public $backendUrl;

    /**
     * @var String $saveEndpoint
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $saveEndpoint;

    /**
     * @var Func $saveEndpoint
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $mappingPayloadFunc;

    /**
     * override init section
     *
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function setServiceByType()
    {
        $serviceRest = null;
        switch ($this->type) {
            case 'RJ':
                $serviceRest = Yii::$app->docoRest->rajal;
                $this->mainUrl = 'pemeriksaan';
                $this->frontendUrl = '/rajal/' . $this->mainUrl;
                $this->backendUrl = 'tindakan-bmhp';
                $this->saveEndpoint = $this->backendUrl . '/save-tindakan-bmhp';
                break;
            case 'RI':
                $serviceRest = Yii::$app->docoRest->ranap;
                $this->mainUrl = 'pemeriksaan-rawat-inap';
                $this->frontendUrl = '/ranap/' . $this->mainUrl;
                $this->backendUrl = $this->mainUrl;
                $this->saveEndpoint = $this->mainUrl . '/cppt-create-tindakan';
                break;
            case 'RD':
                $serviceRest = Yii::$app->docoRest->igd;
                $this->mainUrl = 'pemeriksaan-igd';
                $this->frontendUrl = '/igd/' . $this->mainUrl;
                $this->backendUrl = 'asesmen-dpjp';
                $this->saveEndpoint = $this->backendUrl . '/dpjp-create-terapi-tindakan';
                break;
        }
        if (empty($serviceRest)) {
            return $this->responseJson(400, 'Service REST Tindakan BMHP not set');
        }
        $this->serviceRest = $serviceRest;
    }

    /**
     * Render modal tindakan bmhp
     *
     * @return Html
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionFormTindakanBmhp($id)
    {
        $this->setServiceByType();
        // get bundle data
        $user = Yii::$app->session->get('user_identity');
        $query = [
            'pendaftaran_id' => $this->helper->decrypt($id),
            'kelompokpegawai_id' => $user['kelompokpegawai_id']
        ];
        if ($this->type == 'RJ') {
            $query['pegawai_id'] = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_MEDIS && empty(Yii::$app->docoVars->user('spesialis_id')) ?  Yii::$app->docoVars->user('id_pegawai') : $this->_data_pasien['pegawai_id'];
        }

        $url = [
            'urlModalHistoryTindakan' => '/rajal/pemeriksaan/modal-history-tindakan?pasien_id=' . $this->_data_pasien['pasien_id']
        ];

        $query['ruangan_id'] = isset($this->_data_pasien['ruangan_id']) ? $this->_data_pasien['ruangan_id'] : 1;
        $query['spesialis_id'] = $user['spesialis_id'];
        $bundleData = $this->guzzleExec($this->serviceRest, [
            'url' => $this->backendUrl . '/bundle-data-tindakan-bmhp',
            'payload' => [
                'query' => $query,
            ],
        ]);
        // Cek jika login sebagai dokter umum
        if ($user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS && empty($user['spesialis_id']) && isset($bundleData['use_default_dpjp']) && $bundleData['use_default_dpjp']) {
            switch ($this->type) {
                case 'RJ':
                    $user['nama_pegawai'] = $this->_data_pasien['nama_pegawai'];
                    $user['id_pegawai'] = $this->_data_pasien['pegawai_id'];
                    break;
                case 'RI':
                    $user['nama_pegawai'] = $this->_data_pasien['dokter_admisi'];
                    $user['id_pegawai'] = $this->_data_pasien['dokter_admisi_id'];
                    break;
                case 'RD':
                    if ($this->_data_pasien['dokter_jaga_id'] != $user['id_pegawai']) {
                        $user['nama_pegawai'] = $this->_data_pasien['dokter_jaga'];
                        $user['id_pegawai'] =  $this->_data_pasien['dokter_jaga_id'];
                    }
                    break;
            }
        }
        $penjaminId = isset($this->_data_pasien['penjamin_id']) ? $this->_data_pasien['penjamin_id'] : null;
        $kelasPelayananId = isset($this->_data_pasien['kelaspelayanan_id']) ? $this->_data_pasien['kelaspelayanan_id'] : null;
        $cpptId = Yii::$app->request->get('cppt_id', null);

        $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
        $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
        if ($isTitipan) {
            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
        }
        return $this->renderAjax('//cppt/tindakan/__tindakan_bmhp', [
            'user' => $user,
            'dokter' => $bundleData['data_dokter'],
            'perawat' => $bundleData['data_perawat'],
            'tindakan' => $bundleData['data_tindakanruangan'],
            'paket' => $bundleData['data_paketruangan'],
            'typeMeds' => $bundleData['data_group_obat'],
            'penjaminId' => $penjaminId,
            'kelasPelayananId' => $kelasPelayananId,
            'pendaftaranId' => $id,
            'frontendUrl' => $this->frontendUrl,
            'typeData' => $this->type,
            'cpptId' => $cpptId,
            'url' => $url,
            'no_pendaftaran' => $this->_data_pasien['no_pendaftaran'],
            'isNurse' => $user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS,
            'dokterDpjp' => $user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS ? $bundleData['data_kunjungan']['pegawai_id'] : $user['id_pegawai'],
            'defaultDepo' => [
                'id' => $bundleData['default_depo'],
                'name' => $bundleData['ruangan_depo_nama'],
            ],
            'constCathlab' => $bundleData['const_cathlab'],
            'konfigFormulir' => $bundleData['konfigFormulir'],
            'useDefaultDpjp' => $bundleData['use_default_dpjp'],
            'data_spesialis' => $bundleData['data_spesialis'],
            'konfig_spesialis' => $bundleData['konfig_spesialis'],
            'konfig_tindakan_harga' => $bundleData['konfig_tindakan_harga'],
            'infoPasien' => [
                'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                'kelaspelayanan_nama' => $kelasTagihan,
            ],
            'instalasi_id' => ArrayHelper::getValue($this->_data_pasien, 'instalasi_id')
        ]);
    }
    /**
     * List of tindakan paket id by pendaftaran id and depo id
     *
     * @param String $pendaftaran_id
     * @param String $depo_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionListTindakanPaket($pendaftaran_id, $depo_id)
    {
        $this->setServiceByType();
        $query = [
            'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
            'depo_id' => $depo_id
        ];
        if ($this->type == 'RJ') {
            $query['pegawai_id'] = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_MEDIS ?  Yii::$app->docoVars->user('id_pegawai') : $this->_data_pasien['pegawai_id'];
        }
        $data = $this->guzzleExec($this->serviceRest, [
            'url' => $this->backendUrl . '/list-tindakan-bmhp',
            'payload' => [
                'query' => $query
            ]
        ]);
        return $this->responseJson(200, 'Data berhasil diambil', $data);
    }

    /**
     * Save all of tindakan and BMHP
     *
     * @param String $pendaftaran_id
     * @param String $depo_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveTindakanBmhp()
    {
        $this->setServiceByType();
        $pendaftaran_id = $this->helper->decrypt(Yii::$app->request->post('pendaftaran_id'));
        if (empty($pendaftaran_id)) {
            return $this->responseJson(400, 'Pendaftaran ID tidak valid');
        }
        $user = Yii::$app->session->get('user_identity');
        $request = Yii::$app->request;
        $payloadPost = $request->post();
        $header = isset($payloadPost['header']) ? $payloadPost['header'] : [];
        $tindakan = isset($payloadPost['tindakan']) ? $payloadPost['tindakan'] : [];
        $obat = isset($payloadPost['obat']) ? $payloadPost['obat'] : [];
        $use_default_dpjp = isset($payloadPost['use_default_dpjp']) ? $payloadPost['use_default_dpjp'] : false;
        $hasCathlab = false;

        // Cek config default dpjp
        if (isset($use_default_dpjp) && $use_default_dpjp) {
            if ($user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS && empty($user['spesialis_id'])) {
                switch ($this->type) {
                    case 'RJ':
                        $user['nama_pegawai'] = $this->_data_pasien['nama_pegawai'];
                        $user['id_pegawai'] = $this->_data_pasien['pegawai_id'];
                        break;
                    case 'RI':
                        $user['nama_pegawai'] = $this->_data_pasien['dokter_admisi'];
                        $user['id_pegawai'] = $this->_data_pasien['dokter_admisi_id'];
                        break;
                    case 'RD':
                        if ($this->_data_pasien['dokter_jaga_id'] != $user['id_pegawai']) {
                            $user['nama_pegawai'] = $this->_data_pasien['dokter_jaga'];
                            $user['id_pegawai'] =  $this->_data_pasien['dokter_jaga_id'];
                        }
                        break;
                }
            }

            if ($user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                $header['dokterpenanggungjawab_id'] = $user['id_pegawai'];
            }
        }

        $payload = [];
        switch ($this->type) {
            case 'RJ':
                foreach ($tindakan as $tindakanItem) {
                    if (!$hasCathlab && $tindakanItem['kelompoktindakan_id'] == DocoConstants::KEL_TIND_CATHLAB) {
                        $hasCathlab = true;
                    }
                }
                $payload = [
                    'TindakanBmhpForm' => $header,
                    'TindakanPelayananForm' => $tindakan,
                    'ObatAlkesPasienForm' => $obat,
                    'InstruksiForm' => [
                        'catatan_instruksi' => $payloadPost['catatan'],
                    ],
                ];
                break;
            case 'RI':
                // mapping ward var
                $loggedInUser = Yii::$app->session->get('user_identity');
                $cpptId = isset($payloadPost['cppt_id']) ? (int) $this->helper->decrypt($payloadPost['cppt_id']) : 0;
                $payloadTindakan = [];
                $payloadBmhp = [];
                $payload = [
                    'ruangan' => $this->_data_pasien['ruangan_id'] . '@#' . $this->_data_pasien['kamarruangan_id'] . '@#' . $this->_data_pasien['kamartempattidur_id'] . '@#' . @$this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'],
                    'pegawai_ruangan' => $loggedInUser['id_pegawai'],
                    'pendaftaran_id' => $this->helper->decrypt($payloadPost['pendaftaran_id']),
                    'is_dokter' => $loggedInUser['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS,
                    'kelaspelayanan_id' => $this->_data_pasien['kelaspelayanan_id'],
                    'penjamin_id' => $this->_data_pasien['penjamin_id'],
                    'ruangan_id' => $this->_data_pasien['ruangan_id'],
                    'jeniskasuspenyakit_id' => $this->_data_pasien['jeniskasuspenyakit_id'],
                    'instalasi_id' => Yii::$app->docoVars->workspace('instalasi_id'),
                    'pasien_id' => $this->_data_pasien['pasien_id'],
                    'pasienadmisi_id' => $this->_data_pasien['pasienadmisi_id'],
                    'InstruksiForm' => [
                        'catatan_instruksi' => $payloadPost['catatan'],
                        'is_puasa' => $payloadPost['is_puasa'],
                        'cppt_id' => is_int($cpptId) ? $cpptId : 0,
                    ]
                ];

                // mapping tindakan
                foreach ($tindakan as $eachTindakan) {
                    if (!$hasCathlab && $eachTindakan['kelompoktindakan_id'] == DocoConstants::KEL_TIND_CATHLAB) {
                        $hasCathlab = true;
                    }
                    if (isset($this->_data_pasien['previous_kelas_pelayanan']) && ($eachTindakan['kelompoktindakan_id'] != DocoConstants::KEL_TIND_VISITE && !$eachTindakan['is_konsultasi'])) {
                        $payload['kelaspelayanan_id'] = $this->_data_pasien['previous_kelas_pelayanan'];
                    }
                    $payloadTindakan[] = [
                        'tgl_tindakan' => date("Y-m-d H:i:s"),
                        'daftartindakan_id' => $eachTindakan['daftartindakan_id'],
                        'dokterdpjp_id' => $header['dokterpenanggungjawab_id'],
                        'perawat1_id' => $eachTindakan['perawat1_id'],
                        'perawat2_id' => $eachTindakan['perawat2_id'],
                        'qty' => $eachTindakan['qty_tindakan'],
                        'pasien_id' => $payload['pasien_id'],
                        'carabayar_id' => $this->_data_pasien['carabayar_id'],
                        'pendaftaran_id' => $payload['pendaftaran_id'],
                        'jeniskasuspenyakit_id' => $payload['jeniskasuspenyakit_id'],
                        'penjamin_id' => $payload['penjamin_id'],
                        'ruangan_id' => $payload['ruangan_id'],
                        'instalasi_id' => $payload['instalasi_id'],
                        'kelaspelayanan_id' => $payload['kelaspelayanan_id'],
                        'tarif_satuan' => $eachTindakan['fee'],
                        'tarif_cyto' => $eachTindakan['cyto_fee'],
                        'is_cyto' => $eachTindakan['cyto_tindakan'],
                        'is_concern' => $eachTindakan['consent_tindakan'],
                        'jumlah_tarif' => $eachTindakan['cyto_tindakan'] ? $eachTindakan['fee'] + $eachTindakan['cyto_fee'] : $eachTindakan['fee'],
                        'tipepaket_id' => $eachTindakan['tipepaket_id'],
                        // set prefix
                        'id_instruksi_tindakan' => (!empty($eachTindakan['tipepaket_id']) ? 'PAKET-' . $eachTindakan['tipepaket_id'] : 'TINDAKAN-' . $eachTindakan['daftartindakan_id']),
                    ];
                }
                $payload['InstruksiTindakanForm'] = $payloadTindakan;

                // mapping bmhp
                foreach ($obat as $eachObat) {
                    $payloadBmhp[] = [
                        'tgl_pelayanan' => date("Y-m-d H:i:s"),
                        'daftartindakan_id' => @$eachObat['daftartindakan_id'],
                        'dokter_id' => $header['dokterpenanggungjawab_id'],
                        'tipepaket_id' => @$eachObat['tipepaket_id'],
                        'obatalkes_id' => $eachObat['obatalkes_id'],
                        'perawat1_id' => $eachObat['perawat1_id'],
                        'perawat2_id' => $eachObat['perawat2_id'],
                        'dokter_id' => $header['dokterpenanggungjawab_id'],
                        'qty' => $eachObat['qty_oa'],
                        'pasien_id' => $payload['pasien_id'],
                        'pendaftaran_id' => $payload['pendaftaran_id'],
                        'penjamin_id' => $payload['penjamin_id'],
                        'carabayar_id' => $this->_data_pasien['carabayar_id'],
                        'ruangan_id' => $header['depo_id'],
                        'is_ditagihkan' => $eachObat['is_ditagihkan'],
                        'id_instruksi_tindakan' => !empty($eachObat['tipepaket_id']) || !empty($eachObat['daftartindakan_id']) ? (!empty($eachObat['tipepaket_id']) ? 'PAKET-' . $eachObat['tipepaket_id'] : 'TINDAKAN-' . $eachObat['daftartindakan_id']) : '0',
                    ];
                }
                $payload['InstruksiTindakanBmhpForm'] = $payloadBmhp;
                break;
            case 'RD':
                $post = $request->post();
                $payloadTindakan = [];
                $payloadBmhp = [];

                $userIdentity = $this->_user_identity;
                $cpptId = isset($payloadPost['cppt_id']) ? (int) $this->helper->decrypt($payloadPost['cppt_id']) : 0;
                $instruksi = [
                    'catatan_instruksi' => $request->post('catatan'),
                    'cppt_id' => $cpptId,
                ];
                $perawat_cppt = null;
                if (isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                    $perawat_cppt = $userIdentity['id_pegawai'];
                }

                $payload = [
                    'pendaftaran_id' => $this->helper->decrypt($payloadPost['pendaftaran_id']),
                    'kelaspelayanan_id' => $this->_data_pasien['kelaspelayanan_id'],
                    'penjamin_id' => $this->_data_pasien['penjamin_id'],
                    'ruangan_id' => $this->_data_pasien['ruangan_id'],
                    'jeniskasuspenyakit_id' => $this->_data_pasien['jeniskasuspenyakit_id'],
                    'instalasi_id' => Yii::$app->docoVars->workspace('instalasi_id'),
                    'pasien_id' => $this->_data_pasien['pasien_id'],
                    'depo_id' => isset($header['depo_id']) ? $header['depo_id'] : null,
                    'instruksi' => $instruksi,
                    'perawat_cppt' => $perawat_cppt,
                    'dokterdpjp_id' => $header['dokterpenanggungjawab_id']
                ];

                // mapping tindakan
                foreach ($tindakan as $eachTindakan) {
                    if (!$hasCathlab && $eachTindakan['kelompoktindakan_id'] == DocoConstants::KEL_TIND_CATHLAB) {
                        $hasCathlab = true;
                    }
                    $payloadTindakan[] = [
                        'tgl_tindakan' => date("Y-m-d H:i:s"),
                        'daftartindakan_id' => $eachTindakan['daftartindakan_id'],
                        'dokterdpjp_id' => $header['dokterpenanggungjawab_id'],
                        'perawat1_id' => $eachTindakan['perawat1_id'],
                        'perawat2_id' => $eachTindakan['perawat2_id'],
                        'qty' => $eachTindakan['qty_tindakan'],
                        'pasien_id' => $payload['pasien_id'],
                        'carabayar_id' => $this->_data_pasien['carabayar_id'],
                        'pendaftaran_id' => $payload['pendaftaran_id'],
                        'jeniskasuspenyakit_id' => $payload['jeniskasuspenyakit_id'],
                        'penjamin_id' => $payload['penjamin_id'],
                        'ruangan_id' => $payload['ruangan_id'],
                        'instalasi_id' => $payload['instalasi_id'],
                        'kelaspelayanan_id' => $payload['kelaspelayanan_id'],
                        'tarif_satuan' => $eachTindakan['fee'],
                        'tarif_cyto' => $eachTindakan['cyto_fee'],
                        'is_cyto' => $eachTindakan['cyto_tindakan'],
                        'is_concern' => $eachTindakan['consent_tindakan'],
                        'jumlah_tarif' => $eachTindakan['cyto_tindakan'] ? $eachTindakan['fee'] + $eachTindakan['cyto_fee'] : $eachTindakan['fee'],
                        'tipepaket_id' => $eachTindakan['tipepaket_id'],
                        // set prefix
                        'id_instruksi_tindakan' => (!empty($eachTindakan['tipepaket_id']) ? 'PAKET-' . $eachTindakan['tipepaket_id'] : 'TINDAKAN-' . $eachTindakan['daftartindakan_id']),
                    ];
                }
                $payload['tindakan'] = $payloadTindakan;
                // mapping bmhp
                foreach ($obat as $eachObat) {
                    $payloadBmhp[] = [
                        'tgl_pelayanan' => date("Y-m-d H:i:s"),
                        'daftartindakan_id' => @$eachObat['daftartindakan_id'],
                        'dokter_id' => $header['dokterpenanggungjawab_id'],
                        'tipepaket_id' => @$eachObat['tipepaket_id'],
                        'obatalkes_id' => $eachObat['obatalkes_id'],
                        'dokter_id' => $header['dokterpenanggungjawab_id'],
                        'perawat1_id' => $eachObat['perawat1_id'],
                        'perawat2_id' => $eachObat['perawat2_id'],
                        'qty' => $eachObat['qty_oa'],
                        'pasien_id' => $payload['pasien_id'],
                        'pendaftaran_id' => $payload['pendaftaran_id'],
                        'penjamin_id' => $payload['penjamin_id'],
                        'carabayar_id' => $this->_data_pasien['carabayar_id'],
                        'ruangan_id' => $header['depo_id'],
                        'is_ditagihkan' => $eachObat['is_ditagihkan'],
                        'id_instruksi_tindakan' => !empty($eachObat['tipepaket_id']) || !empty($eachObat['daftartindakan_id']) ? (!empty($eachObat['tipepaket_id']) ? 'PAKET-' . $eachObat['tipepaket_id'] : 'TINDAKAN-' . $eachObat['daftartindakan_id']) : '0',
                    ];
                }
                $payload['bmhp'] = $payloadBmhp;
                break;
            default:
                break;
        }

        return $this->guzzleExec($this->serviceRest, [
            'url' => $this->saveEndpoint,
            'method' => 'POST',
            'payload' => [
                'query' => compact('pendaftaran_id'),
                'form_params' => $payload
            ],
            'success' => function ($response) use ($pendaftaran_id, &$hasCathlab) {
                if ($this->type == 'RD') {
                    $getCache = Yii::$app->cache->get('data-pasien-igd-' . $pendaftaran_id);
                    if ($getCache && $hasCathlab) {
                        $getCache['hasCathlab'] = $hasCathlab;
                        Yii::$app->cache->set('data-pasien-igd-' . $pendaftaran_id, $getCache, 3600);
                    }
                } else {
                    $pendaftaranId = $this->helper->encrypt($pendaftaran_id);
                    $getCache = Yii::$app->cache->get('pasien-pendaftaran-id-' . $pendaftaranId);
                    if ($getCache && $hasCathlab) {
                        $getCache['hasCathlab'] = $hasCathlab;
                        Yii::$app->cache->set('pasien-pendaftaran-id-' . $pendaftaranId, $getCache, 3600);
                    }
                }
                return $this->helper->macroResponseJson(200, isset($response['message']) ? $response['message'] : 'Proses API Berhasil', $response);
            },
            'returnResponse' => true
        ]);
    }

    /**
     * This function will return list of meds
     *
     * @param String penjaminId
     * @param String jenis
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionListBmhp()
    {
        $this->setServiceByType();
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $jenis = $request->get('jenis', null);
        $keyword = $request->get('term', null);
        $page = $request->get('page', 1);
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');

        $userIdentity = Yii::$app->session->get('user_identity');
        // remove condition nurse can only order alkes
        // if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
        //     $jenis = DocoConstants::GOUP_ALKES;
        // }

        $params = [
            // 'instalasi_id' => $instalasi_id,
            'instalasi_id' => $request->get('instalasi_id', null),
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $penjamin_id,
            'kelaspelayanan_id' => $kelaspelayanan_id,
            'group_jenisobat' => $jenis,
            'page' => $page,
            'keyword' => $keyword,
            'get_konfig_stok' => false,
        ];

        $response = $this->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'allow/get-list-stok-apotek',
            'payload' => [
                'query' => $params
            ]
        ]);
        return $this->responseJson(200, 'Data berhasil diambil', $response['data']);
    }

    public function actionTindakanSpesialis()
    {
        $this->setServiceByType();
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $spesialis_id = $request->get('spesialis_id', null);

        $params = [
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $penjamin_id,
            'kelaspelayanan_id' => $kelaspelayanan_id,
            'spesialis_id' => $spesialis_id,
        ];

        $listTindakan = $this->guzzleExec($this->serviceRest, [
            'url' => $this->backendUrl . '/list-tindakan-spesialis',
            'payload' => [
                'query' => $params,
            ],
        ]);

        return $this->responseJson(200, 'Data berhasil diambil', $listTindakan);
    }

    /**
     * This function will return list of depo
     *
     * @param String $term
     * @param String $page
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionListDepo()
    {
        $this->setServiceByType();
        return $this->guzzleExec($this->serviceRest, [
            'url' => $this->backendUrl . '/list-depo',
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
        ]);
    }

    public function actionModalHistoryTindakan()
    {
        $pasien_id = Yii::$app->request->get('pasien_id', null);
        return $this->renderAjax('//cppt/tindakan/_history_tindakan', get_defined_vars());
    }

    public function actionGetListHistoryTindakan($no_pendaftaran){
        $this->setServiceByType();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $getData = $this->guzzleExec($this->serviceRest, [
                'url' => $this->backendUrl . '/tindakan-bmhp',
                'payload' => [
                    'query' => [
                        'no_pendaftaran' => $no_pendaftaran,
                        'ruangan_id' => $ruangan_id,
                        'start' => $request->get('start', 0),
                        'length' => $request->get('length', 10)
                    ]
                ]
            ]);

            $no = $request->get('start',1);
            foreach ($getData['data'] as $key => $value) {
                $no++;
                if(isset($value['tindakanbmhp_is_deleted']) && $value['tindakanbmhp_is_deleted'] == TRUE){
                    $keterangan = 'Dibatalkan';
                }else if(isset($value['tindakansudahbayar_id']) || isset($value['pasienpulang_id'])){
                    $keterangan = 'Sudah Dibayar/Pulang';
                }else{
                    $keterangan = '-';
                }
                $value['rowNum'] = $no;
                $value['keterangan'] = $keterangan;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $getData['_meta']['totalCount'];
            $result['recordsFiltered'] = $getData['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
