<?php
/**
 * 
 * @author : iqbal.rukmana
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\rajal\Pemeriksaan;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Doco\master\models\KamarForm;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\Services\AksesFormService;
use app\components\Pelayanan\PelayananHelpers;

use app\modules\rajal\models\AnamnesaForm;
use app\modules\rajal\models\AsesmenKeperawatan;
use app\extensions\rajal\models\ModelAsesmenKeperawatanAdhy;

class AsesmenKeperawatanAdhy extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "Rajal :: Pemeriksaan pasien rawat jalan";
    protected $_module = '/rajal/pemeriksaan';

    public function getDataPasien($pendaftaran_id)
    {
       try {
            $cachePas = Yii::$app->cache->get('pasien-pendaftaran-id-'.PelayananHelpers::encryptId($pendaftaran_id));
            if (!empty($pendaftaran_id) && empty($cachePas)) {
                $resApi = Yii::$app->docoRest->rajal->get('tra-pemeriksaan/get-pasien', [
                    'query' => [
                        'id' => $pendaftaran_id,
                        'cppt' => true,
                        'konsulpoli_id' => Yii::$app->request->get('konsulpoliId', null)
                    ]
                ]);
                $resApi = json_decode($resApi->getBody(), true)['response'];
                $data_pasien = $resApi['registration'];
                $data_pasien['askep'] = $resApi['askep'];
                if (!empty($data_pasien['askep'])) {
                    $addictionArray = explode(",", $data_pasien['askep']['ketergantungan_jenis']);
                    $data_pasien['askep']['status_merokok'] = in_array('rokok', $addictionArray);
                    $data_pasien['askep']['riwayat_penyakit_keluarga'] = '';
                    $arrayHistory = explode(",", $data_pasien['askep']['riwayat_penyakit_keluarga_list']);
                    $totalDisease = count($arrayHistory);
                    foreach ($arrayHistory as $indexDisease => $disease) {
                        $data_pasien['askep']['riwayat_penyakit_keluarga'] .= ucwords($disease) . (($indexDisease + 1) >= $totalDisease ? '' : ', ');
                    }
                } else {
                    $data['askep'] = [
                        'status_merokok' => false,
                        'riwayat_penyakit_keluarga' => '-',
                        'status_ekonomi' => '-'
                    ];
                }
                $data_pasien['cppt'] = $resApi['cppt'];
                $data_pasien['alergi'] = isset($resApi['askep']['alergi']) ? $resApi['askep']['alergi'] : null;
            } else {
                $data_pasien = $cachePas;
            }
            $data_pasien['ruangan_id'] = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
            $data_pasien['ruangan_name'] = Yii::$app->docoVars->workspace('ruangan_name');
            return $data_pasien;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function processFlow($controller)
    {
        $request        = Yii::$app->request;
        $isRajal        = $request->get('type', 'rj') == 'rj';
        $id             = $request->get('id', null);
        $pasien_id      = $request->get('pasien_id', null);
        $pegawai_id     = $request->get('pegawai_id', null);
        $status         = $request->get('status', 0);
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $pasien_id      = DocoHelpers::decrypt($pasien_id);
        $pegawai_id     = DocoHelpers::decrypt($pegawai_id);
        $userIdentity = Yii::$app->session->get('user_identity');
        // $restRajal  = Yii::$app->docoRest->rajal;
        
        $getDataPasien = $this->getDataPasien($pendaftaran_id);
        $list_data = $this->getListData($getDataPasien);

        if (!$isRajal) {
            $list_data = $this->getListData($getDataPasien);

            $data_diagnosa = $list_data['data_diagnosa'];
            $data_diagnosaimunisasi = $list_data['data_diagnosaimunisasi'];
            $listDiagnosa = ArrayHelper::map($data_diagnosaimunisasi, 'diagnosa_id', 'diagnosa_nama');
            $data_pegawai = $list_data['data_pegawai'];
            $data_dokter = $list_data['data_dokter']; //dokter
            $data_perawat = $list_data['data_perawat'];
            $data_obatalkes = $list_data['data_obatalkes'];
            $modelAnamnesa = new ModelAsesmenKeperawatanAdhy;
            $formName = substr(strrchr(get_class($modelAnamnesa), "\\"), 1);
            $is_update = false;
            $id_anamnesa = null;
            $status_update = 'false';
            $status_periksa_pasien = $this->getStatusPeriksa($pendaftaran_id);
            $modelAnamnesa->pegawaidokter_id = $pegawai_id;
            $modelAnamnesa->tgl_anamnesis = date('d F Y');
            $namaDokter = isset($getDataPasien['nama_pegawai']) ? $getDataPasien['nama_pegawai'] : '';

            // $namaDokter = '';
            // foreach ($data_dokter as $key => $value) {
            //     if ($value['pegawai_id'] == $modelAnamnesa->pegawaidokter_id) {
            //         $namaDokter = $value['nama_pegawai'];
            //     }
            // }

            try {
                $response = Yii::$app->docoRest->rajal->get('tra-pemeriksaan/get-anamnesa?id=' . $pendaftaran_id);
                $body = json_decode($response->getBody(), true);
                $data = $body['response']['data_one'];
                $data_riwayatdiagnosapasien = $this->getRiwayatDiagnosaPasien($pasien_id);
                $data_riwayatdiagnosapasien = $data_riwayatdiagnosapasien['data_diagnosa'];
                $strImunisasi = [];
                if (!empty($data)) {
                    $data['riwayat_penyakitterdahulu'] = !empty($data['riwayat_penyakitterdahulu']) ? json_decode($data['riwayat_penyakitterdahulu'], true) : null;
                    $data['riwayat_penyakitkeluarga'] = !empty($data['riwayat_penyakitkeluarga']) ? json_decode($data['riwayat_penyakitkeluarga'], true) : null;
                    // untuk kebutuhan change field alergi menjadi free text - issue 1699
                    // $data['riwayat_alergiobat'] = !empty($data['riwayat_alergiobat']) ? json_decode($data['riwayat_alergiobat'], true) : null;
                    $data['riwayat_imunisasi'] = !empty($data['riwayat_imunisasi']) ? json_decode($data['riwayat_imunisasi'], true) : null;
                    if (is_array($data['riwayat_penyakitterdahulu'])) {
                        // $data_riwayatdiagnosapasien = array_merge($data_riwayatdiagnosapasien, $data['riwayat_penyakitterdahulu']);
                        $data_riwayatdiagnosapasien =  $data['riwayat_penyakitterdahulu'];
                    }
                    if (isset($data['riwayat_imunisasi'])) {
                        foreach ($data['riwayat_imunisasi'] as $key => $value) {
                            if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                                $strImunisasi[$value] = $value;
                            }
                        }
                        foreach ($listDiagnosa as $key => $value) $strImunisasi[$key] = $value;
                    }

                    $modelAnamnesa->attributes = $data;
                    if (isset($data['is_nyeri'])) {
                        if ($data['is_nyeri'] == TRUE) {
                            $modelAnamnesa->is_nyeri = 1;
                        } else {
                            $modelAnamnesa->is_nyeri = 0;
                        }
                    }
                    if (isset($data['is_resikojatuh'])) {
                        if ($data['is_resikojatuh'] == TRUE) {
                            $modelAnamnesa->is_resikojatuh = 1;
                        } else {
                            $modelAnamnesa->is_resikojatuh = 0;
                        }
                    }

                    $modelAnamnesa->riwayat_penyakitterdahulu = $data_riwayatdiagnosapasien;
                    $modelAnamnesa->riwayat_penyakitkeluarga = $data['riwayat_penyakitkeluarga'];
                    $modelAnamnesa->riwayat_alergiobat = $data['riwayat_alergiobat'];
                    $modelAnamnesa->riwayat_imunisasi = $data['riwayat_imunisasi'];
                    if (!empty($data['tgl_anamnesis'])) {
                        $date = '2012-09-09 03:09:00';
                        $dt = new DateTime($data['tgl_anamnesis']);
                        $modelAnamnesa->tgl_anamnesis = $dt->format('Y-m-d');
                    }

                    $is_update = true;
                    $id_anamnesa = $data['anamesa_id'];

                    $status_periksa = $data['status_periksa'];
                    if ($status_periksa == DocoConstants::STATUS_PULANG || $status_periksa == DocoConstants::STATUS_RUJUK_RAWAT_INAP) {
                        $status_update = 'true';
                    }
                }
                $listDataDiagnosaImunisasi = (count($strImunisasi) > 0) ? $strImunisasi : $listDiagnosa;
                $responseListPackAsesmen = Yii::$app->docoRest->rajal->get('allow/list-pack-asesmen?pendaftaran_id=' . $pendaftaran_id, ['form_params' => []]);
                $dataRiwayat = json_decode($responseListPackAsesmen->getBody(), true)['response']['data_riwayat'];
                if ($post = $request->post()) {
                    $post['AnamnesaForm']['riwayat_penyakitterdahulu'] = !empty($post['AnamnesaForm']['riwayat_penyakitterdahulu']) ? json_encode($post['AnamnesaForm']['riwayat_penyakitterdahulu']) : null;
                    $post['AnamnesaForm']['riwayat_penyakitkeluarga'] = !empty($post['AnamnesaForm']['riwayat_penyakitkeluarga']) ? json_encode($post['AnamnesaForm']['riwayat_penyakitkeluarga']) : null;
                    // untuk kebutuhan change field alergi menjadi free text - issue 1699
                    // $post['AnamnesaForm']['riwayat_alergiobat'] = !empty($post['AnamnesaForm']['riwayat_alergiobat']) ? json_encode($post['AnamnesaForm']['riwayat_alergiobat']) : null;
                    $post['AnamnesaForm']['riwayat_imunisasi'] = !empty($post['AnamnesaForm']['riwayat_imunisasi']) ? json_encode($post['AnamnesaForm']['riwayat_imunisasi']) : null;

                    $modelAnamnesa->load($post);
                    $modelAnamnesa->pendaftaran_id = $pendaftaran_id;
                    $modelAnamnesa->pasien_id = $pasien_id;
                    $modelAnamnesa->riwayat_penyakitterdahulu = $post['AnamnesaForm']['riwayat_penyakitterdahulu'];
                    $modelAnamnesa->riwayat_penyakitkeluarga = $post['AnamnesaForm']['riwayat_penyakitkeluarga'];
                    $modelAnamnesa->riwayat_alergiobat = $post['AnamnesaForm']['riwayat_alergiobat'];
                    $modelAnamnesa->riwayat_imunisasi = $post['AnamnesaForm']['riwayat_imunisasi'];
                    if ($modelAnamnesa->validate()) {
                        if ($is_update) {
                            $response = $this->_restRajal->post('tra-pemeriksaan/update-anamnesa?id=' . $id_anamnesa, [
                                'form_params' => $modelAnamnesa->attributes
                            ]);
                            $response = json_decode($response->getBody(), true);
                        } else {
                            $response = $this->_restRajal->post('tra-pemeriksaan/create-anamnesa', [
                                'form_params' => $modelAnamnesa->attributes
                            ]);
                            $response = json_decode($response->getBody(), true);
                        }
                        return DocoHelpers::response($response, false, true);
                    } else {
                        return DocoHelpers::response($modelAnamnesa->errors, 422, $formName);
                    }
                }
            } catch (Exception $e) {
                return DocoHelpers::response(['message' => $e->getMessage()], 500);
            } catch (RequestException $e) {
                return DocoHelpers::response(['message' => $e->getMessage()], 500);
            }

            $asesmenData = $asesmenData['data'];
            $pasienData = $pasienData['data'];
            $model = new ModelAsesmenKeperawatanAdhy;
            $pendaftaran_id = $pendaftaran_id;
            $configVal = $controller->getConfig('asesmen_keperawatan');
            return $controller->render('@app/modules/rajal/views/pemeriksaan/__anamnesa', get_defined_vars());
        } else {
            $pasienData = Yii::$app->docoRest->rajal->get('asesmen-keperawatan/get-pasien', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ]
            ]);
            $pasienData = json_decode($pasienData->getBody(), true)['response'];
            $pasienData['data']['agama'] = !empty($pasienData['data']['agama']) ? $pasienData['data']['agama'] : '-';
            $pasienData['data']['golongan_darah'] = !empty($pasienData['data']['golongan_darah']) ? $pasienData['data']['golongan_darah'] : '-';
            $pasienData['data']['pendidikan_nama'] = !empty($pasienData['data']['pendidikan_nama']) ? $pasienData['data']['pendidikan_nama'] : '-';

            $dokterNama = Yii::$app->docoRest->rajal->get('asesmen-keperawatan/get-nama-dokter', [
                'query' => [
                    'pegawai_id' => $pegawai_id
                ]
            ]);
            $dokterNama = json_decode($dokterNama->getBody(), true)['response'];

            $status_update = $this->getStatusPeriksa($pendaftaran_id);
            $cekAkses = (new AksesFormService)->execute($getDataPasien['pasien_id'], DocoConstants::FORM_ASESMEN_AWAL_KEP);

            if ($cekAkses == true) {
                $status_update = false;
            }

            // Nurse validation
            $is_perawat = 0;
            if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                $is_perawat = 1;
            }
            //asesmen awal
            if ($status == 0) {
                $asesmenData = Yii::$app->docoRest->rajal->get('asesmen-keperawatan/get-asesmen', [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ]
                ]);
                $asesmenData = json_decode($asesmenData->getBody(), true)['response'];

                $askep_id = isset($asesmenData['data']['anamesa_id']) ? $asesmenData['data']['anamesa_id'] : null;
                // dump($askep_id);
                // die();
                $asesmenData['data']['tgl_anamnesis'] = date('d/m/Y', strtotime("now"));
                $asesmenData['data']['dokter_nama'] = !empty($asesmenData['data']['dokter_nama']) ? $asesmenData['data']['dokter_nama'] : $dokterNama['data']['nama_pegawai'];
                $asesmenData['data']['pegawaidokter_id'] = !empty($asesmenData['data']['pegawaidokter_id']) ? $asesmenData['data']['pegawaidokter_id'] : $pegawai_id;

                $asesmenData['data']['perawat_nama'] = !empty($asesmenData['data']['perawat_nama']) ? $asesmenData['data']['perawat_nama'] : $userIdentity['nama_pegawai'];
                $asesmenData['data']['pegawaiperawat_id'] = !empty($asesmenData['data']['pegawaiperawat_id']) ? $asesmenData['data']['pegawaiperawat_id'] : $userIdentity['id_pegawai'];
                if (!isset($asesmenData['data']['anamesa_id'])) {
                    if (isset($asesmenData['data']['kode_transaksi'])) {
                        if ($asesmenData['data']['kode_transaksi'] == 'perusahaan') {
                            $asesmenData['data']['status_ekonomi'] = '00,' . $asesmenData['data']['penjamin_nama'];
                        } else {
                            $asesmenData['data']['status_ekonomi'] = $asesmenData['data']['kode_transaksi'];
                        }
                    }
                }

                if(!empty($asesmenData['data']['obat_dikonsumsi_nama'])) {
                    $obatKonsumsiRaw = trim(preg_replace('/\s\s+/', '', $asesmenData['data']['obat_dikonsumsi_nama']));
                    $obatKonsumsi = trim(preg_replace('/\t+/', '', $obatKonsumsiRaw));
                    $asesmenData['data']['obat_dikonsumsi_nama'] = $obatKonsumsi;
                } else {
                    $asesmenData['data']['obat_dikonsumsi_nama'] = null;
                }

                // Status Btn Verifikasi Gizi
                $disableGiziBtn = 1;
                if (Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_GIZI && !$asesmenData['data']['is_verifikasigizi'] && (!empty($asesmenData['data']['nutrisi_1a']) || !empty($asesmenData['data']['nutrisi_1b']) || !empty($asesmenData['data']['nutrisi_2']) || !empty($asesmenData['data']['strongkids_kurus']) || !empty($asesmenData['data']['strongkids_turunbb']) || !empty($asesmenData['data']['strongkids_kondisikhusus']) || !empty($asesmenData['data']['strongkids_keadaan_beresiko']))) {
                    $disableGiziBtn = 0;
                }
                $asesmenData = $asesmenData['data'];
                $pasienData = $pasienData['data'];
                $model = new ModelAsesmenKeperawatanAdhy;
                $pendaftaranId = $pendaftaran_id;
                $configVal = $controller->getConfig('asesmen_keperawatan');
                return $controller->renderAjax('@app/extensions/rajal/views/Pemeriksaan/AsesmenKeperawatanAdhy', get_defined_vars());
            }
            //asesmen ulang
            else {
                $asesmenData = Yii::$app->docoRest->rajal->get('asesmen-keperawatan/get-asesmen', [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ]
                ]);
                $asesmenData = json_decode($asesmenData->getBody(), true)['response'];

                $asesmenData['data']['tgl_anamnesis'] = date('d/m/Y', strtotime("now"));
                $asesmenData['data']['dokter_nama'] = !empty($asesmenData['data']['dokter_nama']) ? $asesmenData['data']['dokter_nama'] : $dokterNama['data']['nama_pegawai'];
                $asesmenData['data']['pegawaidokter_id'] = !empty($asesmenData['data']['pegawaidokter_id']) ? $asesmenData['data']['pegawaidokter_id'] : $pegawai_id;

                if(!empty($asesmenData['data']['obat_dikonsumsi_nama'])) {
                    $obatKonsumsiRaw = trim(preg_replace('/\s\s+/', '', $asesmenData['data']['obat_dikonsumsi_nama']));
                    $obatKonsumsi = trim(preg_replace('/\t+/', '', $obatKonsumsiRaw));
                    $asesmenData['data']['obat_dikonsumsi_nama'] = $obatKonsumsi;
                } else {
                    $asesmenData['data']['obat_dikonsumsi_nama'] = null;
                }

                // Status Btn Verifikasi Gizi
                $disableGiziBtn = 1;
                if (Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_GIZI && !$asesmenData['data']['is_verifikasigizi'] && (!empty($asesmenData['data']['nutrisi_1a']) || !empty($asesmenData['data']['nutrisi_1b']) || !empty($asesmenData['data']['nutrisi_2']) || !empty($asesmenData['data']['strongkids_kurus']) || !empty($asesmenData['data']['strongkids_turunbb']) || !empty($asesmenData['data']['strongkids_kondisikhusus']) || !empty($asesmenData['data']['strongkids_keadaan_beresiko']))) {
                    $disableGiziBtn = 0;
                }
                $asesmenData = $asesmenData['data'];
                $pasienData = $pasienData['data'];
                $model = new ModelAsesmenKeperawatanAdhy;
                $pendaftaranId = $pendaftaran_id;
                $configVal = $controller->getConfig('asesmen_keperawatan');
                return $controller->render('@app/extensions/rajal/views/Pemeriksaan/AsesmenKeperawatanAdhy', get_defined_vars());
            }
        }
    }

    /**
     *
     * private function
     *
     */
    private function getListData($dataPasien)
    {
        $result = [
            'data_statusperiksa' => [],
            'data_pegawai' => [],
            'data_penjamin' => [],
            'data_jabatan' => [],
            'data_diagnosa' => [],
            'data_kelompokdiagnosa' => [],
            'data_diagnosaruangan' => [],
            'data_rujukankeluar' => [],
            'data_jadwalpoli' => [],
            'data_tindakanruangan' => [],
            'data_paket' => [],
            'data_dokter' => [],
            'data_perawat' => [],
            'data_diagnosaimunisasi' => [],
            'data_obatalkes' => [],
            'data_satuantindakan' => [],
            'data_ruanganapotek' => [],
            'data_signa' => [],
            'konfig_farmasi' => [],
            'count_riwayat' => [],
            'data_template' => [],
            'thirdapp' => Yii::$app->params->thirdApp,
            'tabs' => Yii::$app->params->rajalTabs
        ];
        try {
            $response = Yii::$app->docoRest->rajal->get('allow/allow-get-list-data?id_ruangan=' . $dataPasien['ruangan_id'] . '&kelaspelayanan_id=' . $dataPasien['kelaspelayanan_id'] . '&penjamin_id=' . $dataPasien['penjamin_id'] . '&pendaftaran_id=' . $dataPasien['pendaftaran_id'] . '&pegawai_id=' . $dataPasien['pegawai_id']);
            $body = json_decode($response->getBody(), true);
            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
            $data_jabatan = empty($body['response']['data-jabatan']) ? [] : $body['response']['data-jabatan'];
            $data_diagnosa = empty($body['response']['data-diagnosa']) ? [] : $body['response']['data-diagnosa'];
            $data_kelompokdiagnosa = empty($body['response']['data-kelompokdiagnosa']) ? [] : $body['response']['data-kelompokdiagnosa'];
            $data_jadwalpoli = empty($body['response']['data-jadwalpoli']) ? [] : $body['response']['data-jadwalpoli'];
            $data_tindakanruangan = empty($body['response']['data-tindakanruangan']) ? [] : $body['response']['data-tindakanruangan'];
            $data_paket = empty($body['response']['data-paket']) ? [] : $body['response']['data-paket'];
            $data_diagnosaruangan = empty($body['response']['data-diagnosaruangan']) ? [] : $body['response']['data-diagnosaruangan'];
            $data_rujukankeluar = empty($body['response']['data-rujukankeluar']) ? [] : $body['response']['data-rujukankeluar'];
            $data_dokter = empty($body['response']['data-dokter']) ? [] : $body['response']['data-dokter'];
            $data_perawat = empty($body['response']['data-perawat']) ? [] : $body['response']['data-perawat'];
            $data_diagnosaimunisasi = empty($body['response']['data-diagnosaimunisasi']) ? [] : $body['response']['data-diagnosaimunisasi'];
            $data_obatalkes = empty($body['response']['data-obatalkes']) ? [] : $body['response']['data-obatalkes'];
            $data_satuantindakan = empty($body['response']['data-satuantindakan']) ? [] : $body['response']['data-satuantindakan'];
            $data_ruanganapotek = empty($body['response']['data-ruanganapotek']) ? [] : $body['response']['data-ruanganapotek'];
            $data_signa = empty($body['response']['data-signa']) ? [] : $body['response']['data-signa'];
            $data_konfig = empty($body['response']['data-konfigfarmasi']) ? [] : $body['response']['data-konfigfarmasi'];
            $count = $body['response']['count-riwayat'];
            $data_permintaan_konsul = empty($body['response']['data_permintaan_konsul']) ? [] : $body['response']['data_permintaan_konsul'];

            $default_status_approve = empty($body['response']['default_status_approve']) ? null : $body['response']['default_status_approve'];

            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
                'data_penjamin' => $data_penjamin,
                'data_jabatan' => $data_jabatan,
                'data_diagnosa' => $data_diagnosa,
                'data_kelompokdiagnosa' => $data_kelompokdiagnosa,
                'data_diagnosaruangan' => $data_diagnosaruangan,
                'data_rujukankeluar' => $data_rujukankeluar,
                'data_jadwalpoli' => $data_jadwalpoli,
                'data_tindakanruangan' => $data_tindakanruangan,
                'data_paket' => $data_paket,
                'data_dokter' => $data_dokter,
                'data_perawat' => $data_perawat,
                'data_diagnosaimunisasi' => $data_diagnosaimunisasi,
                'data_obatalkes' => $data_obatalkes,
                'data_satuantindakan' => $data_satuantindakan,
                'data_ruanganapotek' => $data_ruanganapotek,
                'data_signa' => $data_signa,
                'konfig_farmasi' => $data_konfig,
                'count_riwayat' => $count,
                'data_permintaan_konsul' => $data_permintaan_konsul,
                'default_status_approve' => $default_status_approve,
                'thirdapp' => Yii::$app->params->thirdApp,
                'tabs' => Yii::$app->params->rajalTabs
            ];

            return $result;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function getRiwayatDiagnosaPasien($pasien_id = null)
    {
        try {
            if (!$pasien_id) {
                return ['data_diagnosa' => []];
            }

            $data_request = Yii::$app->docoRest->rajal->get('allow/allow-get-diagnosa-pasien?pasien_id=' . $pasien_id);
            $body = json_decode($data_request->getBody(), TRUE);
            $data_diagnosa = $body['response']['data-diagnosa'];

            return [
                'data_diagnosa' => $data_diagnosa
            ];
        } catch (Exception $e) {
            return [
                'data_diagnosa' => [],
                'message' => $e->getMessage()
            ];
        }
    }

    public function getStatusPeriksa($pendaftaran_id, $dataKunjungan = [])
    {
        if (!is_numeric($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }

        if (empty($dataKunjungan)) {
            $getInfoKunjungan = Yii::$app->docoRest->rajal->get('tra-pemeriksaan/get-info-kunjungan-rajal', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ]
            ]);
            $DataBody = json_decode($getInfoKunjungan->getBody(), True);
            $dataKunjungan = $DataBody['response'];
        }

        $result = false;
        if (isset($dataKunjungan['status_periksa'])) {
            if (
                $dataKunjungan['status_periksa'] == DocoConstants::STATUS_PULANG ||
                $dataKunjungan['status_periksa'] == DocoConstants::STATUS_RUJUK_RAWAT_INAP
            ) {
                $result = true;
            }
        }

        return $result;
    }
}