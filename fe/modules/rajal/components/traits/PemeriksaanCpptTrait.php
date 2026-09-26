<?php
/*
@author: Ardi Pratama
*/

namespace app\modules\rajal\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;
use app\components\DHtml;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rajal\models\SoapRjForm;
use app\modules\rajal\models\VerbalOrderForm;
use GuzzleHttp\Client;

trait PemeriksaanCpptTrait
{
    protected $_state = "pulang";

    public function actionCppt()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        $konsulpoliId = $request->get('konsulpoli', null);
        $statepulang = $request->get('state', null);
        $data_pasien = $this->_data_pasien;
        $model = new SoapRjForm;
        $model->scenario = 'soap_cppt';

        $konfig_system = Yii::$app->cache->get(DocoConstants::VAR_K_S);
        $konfigCppt = self::getKonfigCppt($this->_restRajal, $this->helper->decrypt($pendaftaran_id), $this->_pegawai_id, empty($konfig_system));
        $soapExist = isset($konfigCppt['soap_exist']) ? $konfigCppt['soap_exist'] : [];
        $diagnose = [
            'primary' => isset($soapExist['data']['a_diag_utama']) ? $soapExist['data']['a_diag_utama'] : null,
            'secondary' => isset($soapExist['data']['a_diag_penyerta']) ? $soapExist['data']['a_diag_penyerta'] : null,
        ];

        $model->attributes = $soapExist['data'];
        $model->tgl_soaprj = isset($soapExist['data']['tgl_soaprj']) ? date('Y-m-d H:i:00', strtotime($soapExist['data']['tgl_soaprj'])) : date('Y-m-d H:i:00');
        $model->a_diag_utama = null;
        $model->a_diag_penyerta = null;
        $model->soaprj_id = isset($soapExist['data']['soaprj_id']) ? $soapExist['data']['soaprj_id'] : null;

        $pelayananConfigButton = isset($konfigCppt['pelayanan_config_button']) ? $konfigCppt['pelayanan_config_button'] : [];
        $pelayananConfigButton = isset($pelayananConfigButton['data']) ? $pelayananConfigButton['data'] : [];
        $isPerawat = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_KEPERAWATAN;

        if (!empty($pelayananConfigButton)) {
            foreach ($pelayananConfigButton as $key => $pelayanan) {
                $dataPelayanan = [];
                $dataPelayanan['name'] = $pelayanan['nama_fitur'];
                $dataPelayanan['title'] = $pelayanan['title'];
                $pelayanan['additional_data'] = json_decode($pelayanan['additional_data'], true);
                $additional = empty($pelayanan['additional_data']) ? [] : $pelayanan['additional_data'];
                $dataPelayanan = array_merge($dataPelayanan, $additional);
                if ($isPerawat && !$pelayanan['is_perawat']) {
                    $dataPelayanan['disabled'] = true;
                } else if (!$isPerawat && !$pelayanan['is_dokter']) {
                    $dataPelayanan['disabled'] = true;
                }

                if ( isset($pelayanan['additional_condition'])) {
                    $pelayanan['additional_condition'] = json_decode($pelayanan['additional_condition'], true);
                    foreach ($pelayanan['additional_condition']['data_pasien'] as $attribute => $value) {
                        if (is_array($value['values'])) {
                            foreach ($value['values'] as $val) {
                                if (isset($data_pasien[$attribute])) {
                                    if ($data_pasien[$attribute] == $val) {
                                        $dataPelayanan[$value['attr']] = $value['attr_value'];
                                    }
                                }
                            }
                        } else {
                            if (isset($data_pasien[$attribute])) {
                                if ($data_pasien[$attribute] == $value['values']) {
                                    $dataPelayanan[$value['attr']] = $value['attr_value'];
                                }
                            }
                        }
                    }
                }
                $pelayananConfigButton[$key] = $dataPelayanan;
            }
        }

        $rest_time_reset = isset($konfigCppt['time_reset_suggest_soap']) ? $konfigCppt['time_reset_suggest_soap'] : [];
        $time_reset = isset($rest_time_reset['data']) ? $rest_time_reset['data']: 1440;

        $config_system = isset($konfigCppt['get_konfig_system']) ? $konfigCppt['get_konfig_system'] : $konfig_system;
        $config_soap = isset($config_system['response']) ? $config_system['response'] : $config_system;
        $config_soap = isset($config_soap['hide_instruksi_soap']) ? $config_soap['hide_instruksi_soap']: true;

        $pasien_encrypt_id = $data_pasien['pasien_id'];
        // $ruanganCppt = $this->getRuanganCppt($pendaftaran_id);

        $total_belum_baca_rad = isset($soapExist['data']['total_hasil_radiologi']) ? $soapExist['data']['total_hasil_radiologi'] : [];

        return $this->renderAjax('cppt/__cppt', [
            'encrytedPendaftaranId' => $pendaftaran_id,
            'konsulpoliId' => $konsulpoliId,
            'model' => $model,
            'soapDiagnosa' => $diagnose,
            'tgl_pendaftaran' => $this->_data_pasien['tgl_pendaftaran'],
            'instalasi_id' => $this->_instalasi_id,
            'patientData' => $this->_data_pasien,
            'pelayananConfigButton' => $pelayananConfigButton,
            'id_pegawai' => $this->_pegawai_id,
            'time_reset' => $time_reset,
            'isPerawat' => $isPerawat,
            'pasien_encrypt_id' => $pasien_encrypt_id,
            'total_belum_baca_rad' => $total_belum_baca_rad,
            'id_ruangan' => $this->_id_ruangan,
            'config_soap' => $config_soap,
            'ruangan_cppt_id' => $this->_data_pasien['ruangan_id']
        ]);
    }

    protected static function getKonfigCppt(Client $client, $pendaftaran_id, $pegawai_id, $konfig_system = false)
    {
        $requests = [
            /* get existing data SOAP */
            'soap_exist' => [
                'url' => 'cppt/soap',
                'params' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'pegawai_id' => $pegawai_id,
                ]
            ],
            // 'konfig_pelayanan' => [
            //     'url' => 'allow/get-konfig-pelayanan',
            //     'params' => [
            //         'kode_transaksi' => DocoConstants::TIME_RESET_SUGGEST_SOAP
            //     ]
            // ],
            'pelayanan_config_button' => [
                'url' => 'allow/pelayanan-config-button'
            ],
            'time_reset_suggest_soap' => [
                'url' => 'allow/time-reset-suggest-soap',
                'params' => [
                    'kode_lookup' => DocoConstants::TIME_RESET_SUGGEST_SOAP
                ]
            ],
        ];
        if ($konfig_system) {
            $requests['get_konfig_system'] = [
                'url' => 'allow/get-konfig-system',
            ];
        }
        try {
            return DocoHelpers::poolRequest($client, DocoHelpers::makeGetRequests($requests));
        } catch(RequestException $re) {
            Yii::error(['RequestException' => $re]);
            return [];
        } catch(Exception $e) {
            Yii::error(['Exception' => $e]);
            return [];
        }
    }

    public function actionGetDataCppt()
    {
        $stime = microtime(true);
        // Try catch
        try {
            // Inisiasi
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());

            $draw = $params->get('draw', 1);
            $data = [];
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
            $source = $params->get('source', null);
            $kelompokpegawai_id = $params->get('kelompokpegawai_id', null);
            $ruangan_id = $params->get('ruangan_id', null);
            $pegawai_id = $params->get('pegawai_id', null);
            $tgl_cppt = $params->get('tanggal_cppt', null);

            // Inisiasi result
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            // Get request
            $request = $this->_restRajal->get('cppt/index?pendaftaran_id=' . $pendaftaran_id . '&ruangan_id=' . $ruangan_id . '&pegawai_id=' . $pegawai_id . '&' . http_build_query($yiiRestfulParams) . '&source=' . $source .'&tgl_cppt='.$tgl_cppt."&filter_kelompokpegawai_id={$kelompokpegawai_id}&filter_pasien=true", ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            // Inisiasi nomor
            $no = $params->get('start', 1);
            $numbering = 0;
            // Loop untuk membuat array dari response
            $instruksiDpjp = [];

            if (isset($response['response']["data"]['soap'])) {
                foreach ($response['response']["data"]['soap'] as $k_data => $v_data) {
                    $no++;
                    $is_icd_x = isset($v_data['is_icd_x']) ? $v_data['is_icd_x'] : true;
                    $tgl_edit = isset($v_data['created_date']) ? date('d/m/Y / H:i:s', strtotime($v_data['created_date'])) : '';
                    $hapusCpptButton = DHtml::button('<b><i class=\'fa fa-trash\'></i></b> Hapus', [
                        'akses' => 'delete-diagnosa',
                        'data-soaprj_id' => $v_data['soaprj_id'],
                        'class' => 'btn btn-danger btn-labeled btn-xs btn-delete-cppt'
                    ]);
                    $kelompokpegawai_id = $v_data['kelompokpegawai_soap_id'] != null ?  $v_data['kelompokpegawai_soap_id'] : $v_data['kelompokpegawai_id'];
                    $spesialis_nama = $kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS ? strtolower($v_data['spesialis_nama']) : '';

                    $cpptData = [
                        'no' => '<p >'.$no.'</p>',
                        'primary' => DocoHelpers::encrypt($pendaftaran_id),
                        'ruangan' => '<p >'. @$v_data['ruangan_nama'] . '</p><hr> <p >' . (isset($v_data['tgl_soaprj'])
                            ? date('d/m/Y / H:i:s', strtotime($v_data['tgl_soaprj'])) : '-') . '</p><hr><p >' . @$v_data['kelompokpegawai_nama'] . ($spesialis_nama != '' ? ' - ' . ucwords(strtolower($spesialis_nama)) : '') . '</p><br><p>' . @$v_data['nama_pegawai'].'</p>',
                        'tgl_soaprj' => '<p >'.strtotime($v_data['tgl_soaprj']).'</p>',
                        'penatalaksanaan' => $this->getPenatalaksanaan($v_data),
                        'ruangan_nama' => @$v_data['ruangan_nama'],
                        'instruksi' => '<div style="white-space: pre-line" >' . str_replace('<br />'," ", @$v_data['verbal_instruksi']) . '</div>',
                        'aksi' => $v_data['last_modified_by'] == ($this->_loginpemakai_id) ? (($v_data['is_pulang'] == false) ? ($v_data['is_deleted_soap']) ? '<p > Data sudah di ubah oleh <br>'.$v_data['pegawai_update_nama'].' - <br>'.$tgl_edit.'</p>' : "
                        <button class='btn btn-info btn-labeled btn-xs edit-cppt' data-soaprj_id='".$v_data['soaprj_id']."' data-is_icd_x='".$v_data['is_icd_x']."'><b><i class='fa fa-edit'></i></b> Edit</button>
                        <button class='btn btn-info btn-labeled btn-xs copy-cppt' data-soaprj_id='".$v_data['soaprj_id']."' data-is_icd_x='".$v_data['is_icd_x']."'><b><i class='fa fa-copy'></i></b> Copy</button>
                        <button class='btn btn-danger btn-labeled btn-xs batal-edit-cppt' data-soaprj_id='".$v_data['soaprj_id']."'><b><i class='fa fa-close'></i></b> Batal Edit</button>
                        ".$hapusCpptButton."
                        " : (($v_data['is_pulang'] == true) ? ($v_data['is_deleted_soap']) ? '<p > Data sudah di ubah oleh <br>'.$v_data['pegawai_update_nama'].' - <br>'.$tgl_edit.'</p>' : '' : '')) : '',
                        'rawData' => [
                            'soaprj_id' => [
                                'formId' => 'soaprjform-soaprj_id',
                                'value' => $v_data['soaprj_id']
                            ],
                            'pendaftaran_id' => [
                                'formId' => 'soaprjform-pendaftaran_id',
                                'value' => DocoHelpers::encrypt($pendaftaran_id),
                            ],
                            'diagnosa_utama' => [
                                'formId' => 'soaprjform-a_diag_utama',
                                'value' => !empty($v_data['a_diag_utama']) ? json_decode($v_data['a_diag_utama'], true) : []
                            ],
                            'diagnosa_penyerta' => [
                                'formId' => 'soaprjform-a_diag_penyerta',
                                'value' => !empty($v_data['a_diag_penyerta']) ? json_decode($v_data['a_diag_penyerta'], true) : []
                            ],
                            'subject' => [
                                'formId' => 'soaprjform-subject',
                                'value' => str_replace('<br />'," ", $v_data['subject'])
                            ],
                            'object' => [
                                'formId' => 'soaprjform-object',
                                'value' => str_replace('<br />'," ", $v_data['object'])
                            ],
                            'planning' => [
                                'formId' => 'soaprjform-planning',
                                'value' => str_replace('<br />'," ", $v_data['planning'])
                            ],
                            'catatan_dokter' => [
                                'formId' => 'soaprjform-catatan_dokter',
                                'value' => str_replace('<br />'," ", ArrayHelper::getValue($v_data, 'catatan_dokter', null))
                            ],
                            'tgl_soaprj' => [
                                'formId' => 'soaprjform-tgl_soaprj',
                                'value' => ArrayHelper::getValue($v_data, 'tgl_soaprj', null)
                            ],
                            'instruksi' => [
                                'formId' => 'soaprjform-instruksi',
                                'value' => str_replace('<br />'," ", ArrayHelper::getValue($v_data, 'verbal_instruksi', null))
                            ],
                            'is_deleted_soap' => [
                                'value' => ArrayHelper::getValue($v_data, 'is_deleted_soap', null)
                            ],
                            'is_dokter' => [
                                'value' => $kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS ? true : false
                            ],
                            'is_icd_x' => [
                                'value' => $is_icd_x
                            ],
                        ]
                    ];
                    $data[] = $cpptData;
                }
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['response']['totalCount'];
            $result['recordsFiltered'] = $response['response']['totalCount'];
            $result['time'] = microtime(true)-$stime;

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // Get penatalaksanaan
    private function getPenatalaksanaan($data)
    {
        $pegawai_id = $this->_pegawai_id;
        $userIdentity = Yii::$app->session->get('user_identity');
        // Deklarasi html
        $html = '<table border="0" cellpadding="0" cellspacing="0">';
        // Cek subjek
        if (isset($data['subject']) && $data['subject'] != '') {
            // normalize string
            $data['subject'] = str_replace('<br />'," ", $data['subject']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b >Subjektif :</b><br/><div style="white-space: pre-line" >' . $data['subject'] . '</div></td>';
            $html .= '</tr>';
        }

        // Cek objek
        if (isset($data['object']) && $data['object'] != '') {
            // normalize string
            $data['object'] = str_replace('<br />'," ", $data['object']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b >Objektif :</b><br/><div style="white-space: pre-line" >' . $data['object'] . '</div></td>';
            $html .= '</tr>';
        }

        // Cek subject
        if ((isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') || (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '')) {
            $html .= '<tr style="line-height:130%;">';
            if (isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') {
                $diag_utama = json_decode($data['a_diag_utama'], true);
                if (!empty($diag_utama['text'])) {
                    if($data['is_icd_x']){
                        $html .= '<td><b>Diagnosa Utama:</b><br/>'.wordwrap(@$diag_utama['text'],70,"\n") .'</td>';
                    }
                    else{
                        $html .= '<td><b>Diagnosa Utama:</b><br/><div style="white-space: pre-line">'.$diag_utama['text'].'</div></td>';
                    }
                    $html .= '</tr>';
                }
            }
            // Cek asesmen
            if (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '') {
                // Encode
                if (!is_array($data['a_diag_penyerta'])) {
                    $diagnosaPenyerta = json_decode($data['a_diag_penyerta'], true);
                    if (!is_array($diagnosaPenyerta)) {
                        $diagnosaPenyerta = json_decode($diagnosaPenyerta, true);
                    }
                } else {
                    $diagnosaPenyerta = $data['a_diag_penyerta'];
                }
                // Cek diagnosa
                if ($diagnosaPenyerta != '') {
                    $html .= '<tr style="line-height:130%;">';
                    $html .= '<td><b >' . Yii::t('fe', 'Diagnosa Penyerta') . ' :</b><br/>';
                    foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                        $html .= '&nbsp;&nbsp;&nbsp;- ' . @$valueDiagnosaPenyerta['text'] . '<br/>';
                    }
                    $html .= '</td></tr>';
                }
            }
        }

        // Cek planning
        if (isset($data['planning']) && $data['planning'] != '') {
            // normalize string
            $data['planning'] = str_replace('<br />'," ", $data['planning']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b >Planning :</b><br/><div style="white-space: pre-line" >' . $data['planning'] . '</div></td>';
            $html .= '</tr>';
        }

        // Cek catatan dokter
        if (isset($data['catatan_dokter']) && $data['catatan_dokter'] != '') {
            // normalize string
            $data['catatan_dokter'] = str_replace('<br />'," ", $data['catatan_dokter']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>' . Yii::t('fe', 'Catatan Dokter') . ' :</b><br/><div style="white-space: pre-line">' . $data['catatan_dokter'] . '</div></td>';
            $html .= '</tr>';
        }

        // Cek Verbal Order
        if (isset($data['pemberi_instruksi_nama']) && !empty($data['pemberi_instruksi_nama'])) {
            // Set html
            // if ($pegawai_id == $data['pemberi_instruksi_id'] && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
            if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                $html .= '<tr style="line-height:130%;">';
                $html .= '<td><b>Pemberi Instruksi :</b><br/>' . $this->helper->purifyHtml($data['pemberi_instruksi_nama']) . '</td>';
                $html .= '</tr>';
            }
            // }
        }

        // Set end tag html
        $html .= '</table>';

        // Return
        return $html;
    }

    private function getInstruksiDpjp($data_dpjp)
    {
        // try {
            $html = '';
            $html .= '<table>';
            if (isset($data_dpjp['jenis'])) {
                foreach ($data_dpjp['jenis'] as $k_dpjp => $v_dpjp) {
                    if ($k_dpjp == '' || empty($v_dpjp)) {
                        break;
                    }
                    $instruksiJudul = 'Tindakan';
                    if ($k_dpjp == 'Penunjang') {
                        $instruksiJudul = 'Penunjang';
                    } else if ($k_dpjp == 'Reseptur' || $k_dpjp == 'Non Racikan' || $k_dpjp == 'Racikan') {
                        $instruksiJudul = 'Obat';
                    } else if ($k_dpjp == 'Tindakan & BMHP') {
                        $instruksiJudul = 'Tindakan';
                    }

                    $instruksiJenis = 'Tindakan';
                    if ($k_dpjp == 'Non Racikan') {
                        $instruksiJenis = 'Non Racikan';
                    } else if ($k_dpjp == 'Racikan') {
                        $instruksiJenis = 'Racikan';
                    } else if ($k_dpjp == 'Reseptur') {
                        $instruksiJenis = 'Obat';
                    }
                    $headingTr = '';
                    foreach ($v_dpjp as $v_dpjpdetail) {
                        if ($v_dpjpdetail['tgl_tindakan'] !== null && $v_dpjpdetail['kelompoktindakan_id'] != 17) {
                            if (empty($headingTr)) {
                                $headingTr .= '<tr><td>' . $instruksiJudul . '</td><td></td></tr>';
                                $headingTr .= '<tr><td></td><td>' . $instruksiJenis . '</td></tr>';
                                $html .= $headingTr;
                            }
                            if ($v_dpjpdetail['is_hapus'] == TRUE) {
                                $html .= '<tr class="strikeout">';
                            } else {
                                $html .= '<tr>';
                            }
                            $html .= '<td>' . date('d-M-Y H:i:s', strtotime($v_dpjpdetail['tgl_tindakan'])) . '</td>';
                            $ket_cyto = '';
                            if (isset($v_dpjpdetail['cyto_tindakan']) && $v_dpjpdetail['grouping_tipe'] == 'Tindakan & BMHP') {
                                if ($v_dpjpdetail['cyto_tindakan'] == TRUE) {
                                    $ket_cyto = ' - Cyto';
                                } else {
                                    $ket_cyto = ' - Non Cyto';
                                }
                            }
                            if ($v_dpjpdetail['grouping_tipe'] == 'Verbal Order') {
                                $html .= '<td> ' . @$v_dpjpdetail['verbal_instruksi'] . $ket_cyto . ' - ' . @$v_dpjpdetail['pemberi_instruksi_nama'];
                            } else {
                                $html .= '<td> ' . @$v_dpjpdetail['instruksi'] . $ket_cyto . ' - ' . @$v_dpjpdetail['qty'];
                            }

                            if ($v_dpjpdetail['jenis'] == "PAKET") {
                                if (isset($v_dpjpdetail['daftar_paket'])) {
                                    $html .= '<br>';
                                    $list_paket = json_decode($v_dpjpdetail['daftar_paket'], TRUE);
                                    $html .= '<ul>';
                                    foreach ($list_paket as $v_list_paket) {
                                        $html .= '<li>';
                                        $html .= @$v_list_paket;
                                        $html .= '</li>';
                                    }
                                    $html .= '</ul>';
                                }
                            }
                            $html .= '</td>';
                            $html .= '</tr>';
                        }
                    }
                }
            } else if (isset($data_dpjp['list_orderpenunjang'])) {
                foreach ($data_dpjp['list_orderpenunjang'] as $k_order => $v_order) {
                    $html .= '';
                    $html .= '<tr><td></br>Penunjang</td><td></td></tr>';
                    $html .= '<tr><td>' . @$v_order['penunjang']['instalasi_penunjang_nama'] . ' - ' . @$v_order['penunjang']['ruangan_penunjang_nama'] . '</td><td></td></tr>';
                    $html .= '<tr><td>' . @$v_order['penunjang']['catatan_dokterpengirim'] . '</td><td></td></tr>';
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    if ($v_order['penunjang']['status'] == '472') {
                        $html .= '<td><span class="label label-danger">Dibatalkan</span></td>';
                    } elseif ($v_order['penunjang']['status'] == '541') {
                        $html .= '<td><span class="label label-danger">Ditolak</span></td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    $html .= '<td></td>';
                    $html .= '</tr>';

                    foreach ($v_order['jenis'] as $k_penunjangdpjp => $v_penunjangdpjp) {
                        $instruksiJudul = 'Tindakan';
                        if ($k_penunjangdpjp == 'Penunjang') {
                            $instruksiJudul = 'Penunjang';
                        } else if ($k_penunjangdpjp == 'Reseptur') {
                            $instruksiJudul = 'Obat';
                        } else if ($k_penunjangdpjp == 'Tindakan & BMHP') {
                            $instruksiJudul = 'Tindakan';
                        }
                        $html .= '<tr><td></td><td> Tindakan ' . @$v_order['penunjang']['instalasi_penunjang_nama'] . '</td></tr>';
                        foreach ($v_penunjangdpjp as $v_dpjppenunjangdetail) {
                            if ($v_dpjppenunjangdetail['is_hapus'] == TRUE) {
                                $html .= '<tr class="strikeout">';
                            } else {
                                $html .= '<tr>';
                            }
                            $html .= '<td>' . date('d-M-Y H:i:s', strtotime($v_dpjppenunjangdetail['tgl_tindakan'])) . '</td>';
                            $ket_cyto = '';
                            if (isset($v_dpjppenunjangdetail['cyto_tindakan'])) {
                                if ($v_dpjppenunjangdetail['cyto_tindakan'] == TRUE) {
                                    $ket_cyto = ' - Cyto';
                                } else {
                                    $ket_cyto = ' - Non Cyto';
                                }
                            }
                            $html .= '<td>';
                            $html .= @$v_dpjppenunjangdetail['instruksi'] . $ket_cyto . ' - ' . @$v_dpjppenunjangdetail['qty'];
                            if ($v_dpjppenunjangdetail['jenis'] == "PAKET") {
                                if (isset($v_dpjppenunjangdetail['daftar_paket'])) {
                                    $html .= '<br>';
                                    $list_paket = json_decode($v_dpjppenunjangdetail['daftar_paket'], TRUE);
                                    $html .= '<ul>';
                                    foreach ($list_paket as $v_list_paket) {
                                        $html .= '<li>';
                                        $html .= @$v_list_paket;
                                        $html .= '</li>';
                                    }
                                    $html .= '</ul>';
                                }
                            }
                            $html .= '</td>';
                            $html .= '</tr>';
                        }
                    }
                }
            }
            $html .= '</table>';
            return $html;
    //     } catch (Exception $e) {
    //         return '';
    //     }
    }

    public function actionCetakListCppt($id, $all = false)
    {
        // Path
        $path = Yii::getAlias("@download") . "/list-cppt-rajal.pdf";
        $request = Yii::$app->request;
        $kelompokpegawai_id = $request->get('kelompokpegawai_id', null);
        $filterruangan_id = $request->get('filterruangan_id', null);
        $pegawai_id = $request->get('pegawai_id', null);
        $tgl_cppt = $request->get('tanggal_cppt', null);
        $filter_pasien = $request->get('filter_pasien', null);
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $ruangan_id = $request->get('ruangan_id', Yii::$app->docoVars->workspace('ruangan_id'));
        $kelompokpegawai_id = $request->get('kelompokpegawai_id');
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        $all = ($all == true) ? 1 : 0;
        // Try catch
        try {
            // Request
            $request = $this->_restRajal->get('cppt/cetak-list-cppt-rajal', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $ruangan_id,
                    'filter_kelompokpegawai_id' => $kelompokpegawai_id,
                    'nama_usercetak' => $nama_usercetak,
                    'id_usercetak' => $id_usercetak,
                    'all' => $all,
                    'filterruangan_id' => $filterruangan_id,
                    'pegawai_id' => $pegawai_id,
                    'tgl_cppt' => $tgl_cppt,
                    'kelompokpegawai_id' => $kelompokpegawai_id,
                ],
                'save_to' => $path,
            ]);
            $body = json_decode($request->getBody(), true);
            $result = [
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_id' => $ruangan_id,
                'nama_usercetak' => $nama_usercetak,
                'id_usercetak' => $id_usercetak
            ];
            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionShowPopupPdf()
    {
        $title = 'Cetak PDF CPPT';
        $randString = DocoHelpers::generateRandomString();
        $params = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
        $ruangan_id = $params->get('ruangan_id', null);
        $pegawai_id = $params->get('pegawai_id', null);
        $kelompokpegawai_id = $params->get('kelompokpegawai_id', null);
        $tgl_cppt = $params->get('tanggal_cppt', null);
        $filter_pasien = $params->get('filter_pasien', null);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        $filterruangan_id = $params->get('filterruangan_id', null);
        $process_path = '/rajal/pemeriksaan/process-sync-pdf';
        $download_pdf_path = '/rajal/pemeriksaan/download-pdf';
        $filter = [
            'pendaftaran_id' => $pendaftaran_id,
            'ruangan_id' => $ruangan_id,
            'filter_kelompokpegawai_id' => $kelompokpegawai_id,
            'nama_usercetak' => $nama_usercetak,
            'id_usercetak' => $id_usercetak,
            'filterruangan_id' => $filterruangan_id,
            'pegawai_id' => $pegawai_id,
            'tgl_cppt' => $tgl_cppt,
            'kelompokpegawai_id' => $kelompokpegawai_id,
            'filter_pasien' => $filter_pasien,
            'only_attributes' => true,
        ];
        Yii::$app->session->setFlash($randString, $filter);
        return $this->renderAjax('//cppt/_modal_progress', compact('title', 'randString', 'process_path', 'download_pdf_path'));
    }

    public function actionProcessSyncPdf($randString)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restRajal, [
            'url' => "cppt/export-pdf-cppt-bgproses",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'CPPT Rajal.pdf';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $this->_restRajal->get('cppt/download-file-pdf', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path);
    }

    private function getRuanganCppt($pendaftaran_id)
    {
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $request = $this->_restRajal->get('cppt/index', [
            'query' => [
                'pendaftaran_id' => $pendaftaran_id,
            ]
        ]);

        $response = json_decode($request->getBody(), true);
        $data = isset($response['response']['ruanganCppt']) ? $response['response']['ruanganCppt'] : [];

        return $data;
    }

    /* Fungsi create VERBAL ORDER */
    public function actionCreateVerbalOrder($id)
    {
        $params = Yii::$app->request;
        $post = $params->post('VerbalOrderForm');
        $data_pasien = $this->_data_pasien;
        $model = new VerbalOrderForm;

        if (empty($post)) {
            $ruangan_id = $data_pasien['ruangan_id'];
            $pendaftaran_id = $id;
            $kelaspelayanan_id = $data_pasien['kelaspelayanan_id'];
            $penjamin_id = $data_pasien['penjamin_id'];
            $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
        }

        if (Yii::$app->request->post()) {
            $post['ruangan_id'] = $data_pasien['ruangan_id'];
            $model->attributes = $post;
            if ($model->validate()) {
                $res = $this->_restRajal->post('verbal-order/create', [
                    'form_params' => $post
                ]);
                $response = json_decode($res->getBody(), true);
                return json_encode($response);
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        }

        return $this->renderAjax('cppt/form_verbal_order', get_defined_vars());
    }
    /**
     * This function will save record SOAP
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveSoap($pendaftaran_id, $konsulpoli_id = null)
    {
        $payload = Yii::$app->request->post('SoapRjForm');
        $request = Yii::$app->request->get();
        $ruangan_asal_id = Yii::$app->request->get('ruangan_asal_id', null);
        $tglExplode = explode(' ', $payload['tgl_soaprj']);
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $payload['tgl_soaprj'] = isset($payload['tgl_soaprj']) ? date_format(date_create_from_format('d/m/Y', $tglExplode[0]), 'Y-m-d') . date(' H:i:00', strtotime($tglExplode[1])) : null;

        $payload['subject'] = DocoHelpers::purifyText(ArrayHelper::getValue($payload, 'subject'));
        $payload['object'] = DocoHelpers::purifyText(ArrayHelper::getValue($payload, 'object'));
        $payload['a_diag_utama'] = DocoHelpers::purifyText(ArrayHelper::getValue($payload, 'a_diag_utama'));
        $payload['planning'] = DocoHelpers::purifyText(ArrayHelper::getValue($payload, 'planning'));
        $payload['catatan_dokter'] = DocoHelpers::purifyText(ArrayHelper::getValue($payload, 'catatan_dokter'));
        $payload['instruksi'] = DocoHelpers::purifyText(ArrayHelper::getValue($payload, 'instruksi'));
        $payload['a_diag_utama_text'] = DocoHelpers::purifyText(ArrayHelper::getValue($payload, 'a_diag_utama_text'));
        $payload['a_diag_penyerta'] = DocoHelpers::purifyText(ArrayHelper::getValue($payload, 'a_diag_penyerta'));
        $model = new SoapRjForm;
        $model->scenario = 'soap_cppt';
        $model->attributes = $payload;
        $model->final = Yii::$app->request->get('final', 1);
        $model->tgl_soaprj = isset($payload['tgl_soaprj']) ? $payload['tgl_soaprj'] : null;
        $model->soaprj_id =  isset($payload['soaprj_id']) ? $payload['soaprj_id'] : null;
        $model->is_icd_x =  isset($payload['is_icd_x']) ? $payload['is_icd_x'] : null;
        $pendaftaran_id = $this->helper->decrypt($pendaftaran_id);
        $konsulpoli_id = $this->helper->decrypt($konsulpoli_id);
        
        if (empty($model->a_diag_penyerta)) {
            $model->a_diag_penyerta = [];
        }
        if (in_array($model->a_diag_utama, $model->a_diag_penyerta)) {
            return DocoHelpers::responseTemplate(
                422,
                'Error',
                [],
                [
                    'title' => Yii::t('fe', 'Peringatan!'),
                    'text' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                    'message' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                ]
            );
        } else if (!$model->validate()) {
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $formName);
        } else if (empty($pendaftaran_id)) {
            return $this->responseJson(400, 'ID Pendaftaran tidak boleh kosong');
        } else {
            $is_dokter = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_MEDIS ? true : false;

            $result = $this->guzzleExec($this->_restRajal, [
                'url' => 'soap/create-soap',
                'method' => 'post',
                'payload' => [
                    'query' => compact('pendaftaran_id', 'is_dokter','loginpemakai_id','is_replace', 'konsulpoli_id', 'ruangan_asal_id'),
                    'form_params' => [
                        'SoapRjForm' => $model->attributes
                    ]
                ],
            ]);

            $pendId = $this->helper->encrypt($pendaftaran_id);
            $this->helper->updateSessionDataPasien($pendId, [
                'cppt' => [
                    'a_diag_utama' => $result['data']['a_diag_utama']
                ]
            ]);
            if (isset($result['data']['update_session_data']) && $result['data']['update_session_data']) {
                $this->helper->updateSessionDataPasien($pendId, [
                    'status_periksa' => DocoConstants::STATUS_PERIKSA_DIPERIKSA,
                    'status_periksa_nama' => 'Diperiksa'
                ]);
            }

            return $this->helper->response($result, 200);
        }
    }

    public function actionCpptFilters($term, $type)
    {
        $response = $this->guzzleExec($this->restGeneral, [
            'url' => 'cppt/get-filter-cppt?',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pasien_id' => $this->_pasien_id, 'term' => $term, 'type' => $type
                ]
            ]
        ]);

        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

    public function actionDeleteCppt() {
        $req = Yii::$app->request;
        $cppt_id = $req->get('cppt_id');
        $pendaftaran_id = $req->get('pendaftaran_id');

        $response = $this->guzzleExec($this->restGeneral, [
            'url' => 'cppt/delete-cppt',
            'method' => 'post',
            'payload' => [
                'form_params' => [
                    'pendaftaran_id' => DocoHelpers::setDecryptIdFromString($pendaftaran_id),
                    'cppt_id' => $cppt_id,
                    'user_id' => $this->_loginpemakai_id
                ]
            ],
        ]);

        return json_encode($response);
    }
}
