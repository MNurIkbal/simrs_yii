<?php

namespace app\components\Traits;

use Yii;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DHtml;

trait HistoryPatientTrait
{
    /**
     * @var String $restGeneral
     * @author Aris Munandar (aris.m@gmail.com)
     */
    public $restGeneral;
    public $fileAction = [
        'preview' => [
            'pdf'
        ],
        'download' => [
            'doc',
            'docx',
            'xls',
            'xlsx',
            'txt'
        ],
        'detail' => [
            'jpg',
            'png'
        ]
    ];

    public function actionHistoryPatient($norm)
    {
        try {
            $title        = Yii::t('fe', 'Riwayat Pasien');
            $pendaftaranId = Yii::$app->request->get('id', null);
            $pasienadmisiId = Yii::$app->request->get('pasienadmisi_id', null);
            $restMaster   = Yii::$app->docoRest->master;
            $instalasi_id = Yii::$app->request->get('instalasi', null);
            $is_jenis     = Yii::$app->request->get('is_jenis', null);
            $is_penunjang = Yii::$app->request->get('is_penunjang', null);
            // $konfigNas = ArrayHelper::getValue($this->getKonfigNas(), 'konfig');
            // $disableBerkasPasien = !empty($konfigNas) ? false : true;
            $aksesRiwayatPasien = DHtml::cekHakAkses('data-history-patient');

            if ($is_jenis == 'lab') {
                $title = Yii::t('fe', 'Riwayat Hasil Laboratorium');
            } elseif ($is_jenis == 'rad') {
                $title = Yii::t('fe', 'Riwayat Hasil Radiologi');
            }

            if ($pendaftaranId !== null) {
                $dataPasienQuery = [
                    'pendaftaran_id' => $this->helper->decrypt($pendaftaranId),
                ];

                if ($pasienadmisiId !== null) {
                    $dataPasienQuery['pasienadmisi_id'] = $pasienadmisiId;
                }

                $dataPasien = $this->guzzleExec($this->restGeneral, [
                    'url' => 'riwayat-pasien/patient-global',
                    'method' => 'GET',
                    'payload' => [
                        'query' => $dataPasienQuery,
                    ]
                ]);

                $title = $title . ' - '. ArrayHelper::getValue($dataPasien, 'data.nama_pasien', '-') . ' / ' . ArrayHelper::getValue($dataPasien, 'data.penjamin_nama', '-') . ' / ' . ArrayHelper::getValue($dataPasien, 'data.kelaspelayanan_nama', '-');
            }

            switch ($instalasi_id) {
                case DocoConstants::INSTALASI_ID_RJ:
                    $instalasi = Yii::t('fe', 'Rawat Jalan');
                    $url = '/rajal';
                    break;
                case DocoConstants::INSTALASI_ID_RI:
                    $instalasi = Yii::t('fe', 'Rawat Inap');
                    $url = '/ranap';
                    break;
                default:
                    $instalasi = Yii::t('fe', 'Rawat Darurat');
                    $url = '/igd';
                    break;
            }

            $responseRuangan = $this->guzzleExec($restMaster, [
                'url' => 'ruangan/get-by-riwayat',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'norm' => $norm,
                    ]
                ]
            ]);

            $responseDokter = $this->guzzleExec($restMaster, [
                'url' => 'dokter/get-by-riwayat',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'norm' => $norm,
                    ]
                ]
            ]);

            $listRuangan = [];
            foreach ($responseRuangan as $ruangan) {
                $listRuangan[$ruangan['ruangan_pend_id']] = [
                    'id' => $ruangan['ruangan_pend_id'],
                    'text' => $ruangan['ruangan_pend']
                ];
                if (isset($ruangan['ruangan_adm_id']) && !empty($ruangan['ruangan_adm_id'])) {
                    $listRuangan[$ruangan['ruangan_adm_id']] = [
                        'id' => $ruangan['ruangan_adm_id'],
                        'text' => $ruangan['ruangan_adm']
                    ];
                }
            }
            $listRuangan = array_values($listRuangan);

            $listDokter = [];
            foreach ($responseDokter as $dokter) {
                if (isset($dokter['dok_rjrd_id']) && !empty($dokter['dok_rjrd_id'])) {
                    $listDokter[$dokter['dok_rjrd_id']] = [
                        'id' => $dokter['dok_rjrd_id'],
                        'text' => $dokter['dok_rjrd']
                    ];
                }
                if (isset($dokter['dok_ri_id']) && !empty($dokter['dok_ri_id'])) {
                    $listDokter[$dokter['dok_ri_id']] = [
                        'id' => $dokter['dok_ri_id'],
                        'text' => $dokter['dok_ri']
                    ];
                }
            }
            $listDokter = array_values($listDokter);

            return $this->renderAjax('//cppt/penunjang/_modal_riwayat_pasien_penunjang', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionGetDataHistoryPatient()
    {
        try {
            $request = Yii::$app->request;
            $norm = $request->get('norm');
            $is_modal = $request->get('is_modal', null);
            $is_jenis = $request->get('is_jenis', null);
            $draw = $request->get('draw', 1);
            $result = $data = [];
            $result['data'] = [];
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
            $filter['advancedFilter'] = $request->get('advancedFilter', []);
            $response = $this->restGeneral->request('get', 'riwayat-pasien/data-history-patient', [
                'query' => [
                    'norm' => $norm,
                    'filter' => $filter,
                    'is_jenis' => $is_jenis,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $rowNum = 1;
            $arr_temp = [];
            $load_more = isset($body['response']['load_more']) ? $body['response']['load_more'] : false;

            foreach ($body['response']['data'] as $key => $v) {
                $cara_keluar = '';
                $ruangan_pend_id   = $v['ruangan_pend_id'];
                $instalasi_pend_id = $v['instalasi_pend_id'];
                if (!empty($v['tglpasienpulang'])) {
                    $cara_keluar .= date('d M Y H:i:s', strtotime($v['tglpasienpulang'])) . "<br>";
                    $cara_keluar .= $v['carakeluar_nama'] . "<br>";
                    if (isset($v['kondisikeluar_nama'])) {
                        $cara_keluar .= ' - ' . $v['kondisikeluar_nama'] . "<br>";
                    }
                }
                $v['rowNum'] = $rowNum;
                $v['is_rujukan'] = in_array($v['pendaftaran_id'], $body['response']['dataRujukan']);

                if (!isset($v['is_uji_fungsi_fisio'])) {
                    $v['is_uji_fungsi_fisio'] = $this->checkUjiFungsiFisio($v['pendaftaran_id']);
                }

                if (!isset($v['is_formulir_rajal'])) {
                    $v['is_formulir_rajal'] = $this->checkFormulirRajal($v['pendaftaran_id']);
                }

                $aksi_pelayanan = null;
                $aksi_penunjang = null;
                if ($v['p_laboratorium'] == 1 || $v['p_radiologi'] == 1 || $v['p_operasi'] == 1 || $v['r_asesmengizinrs'] == 1) {
                    $aksi_penunjang = $this->riwayatPenunjangGlobal($norm, $v, $is_jenis);
                }
                if ($is_jenis != 'lab') {
                    $aksi_pelayanan = $this->riwayatPelayananGlobal($norm, $v, $is_modal, $ruangan_pend_id, $instalasi_pend_id);
                }
                $data[] = [
                    'pendaftaran_id' => $v['pendaftaran_id'],
                    'tgl_pendaftaran' => date('d M Y', strtotime($v['tgl_pendaftaran'])) . ' / ' . $v['no_pendaftaran'],
                    'ruangan_pend' => !empty($v['pasienadmisi_id']) ? $v['ruangan_adm'] : $v['ruangan_pend'],
                    'dok_rjrd' => !empty($v['pasienadmisi_id']) ? $v['dok_ri'] : $v['dok_rjrd'],
                    'cara_keluar' => $cara_keluar,
                    'aksi_pelayanan' => $aksi_pelayanan,
                    'aksi_penunjang' => $aksi_penunjang,
                ];
                $rowNum++;
            }

            $result['data'] = $data;
            $result['load_more'] = $load_more;

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    protected function riwayatPelayananGlobal($norm, $data, $is_modal, $ruangan_pend_id, $instalasi_pend_id)
    {
        $btn_pelayanan = '';
        $is_nursingnotes = ArrayHelper::getValue($data, 'is_nursingnotes');
        $is_partograf = ArrayHelper::getValue($data, 'is_partograf');

        if ($data['instalasi_pend_id'] == 1 && ($data['pasienadmisi_id'] == '')) {
            $btn_pelayanan .= Html::button(
                'RJ - Skrining',
                [
                    'id'          => 'btn-cetak-rajal-skrining-' . @$data['rowNum'],
                    'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                    'data-target' => '#modal-preview',
                    'data-url'    => '/rajal/informasi/skrining-pasien?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&is_riwayat=true',
                ]
            );
        }

        if ($data['instalasi_pend_id'] == 1 && ($data['r_anamesa'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Asesmen Keperawatan',
                    [
                        '/rajal/pemeriksaan/cetak-anamnesa?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ],
                    [
                        'id' => 'btn-cetak-rajal-asesmen-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - Asesmen Keperawatan',
                    [
                        'id'          => 'btn-cetak-rajal-asesmen-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/rajal/pemeriksaan/cetak-anamnesa?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && ($is_nursingnotes == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Nursing Note',
                    [
                        '/rajal/pemeriksaan/cetak-nursing-note?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-igd-askep-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - Nursing Note',
                    [
                        'id'          => 'btn-cetak-igd-askep-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-url'    => '/rajal/pemeriksaan/cetak-nursing-note?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && ($data['r_pemeriksaanfisik'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Asesmen Medis',
                    [
                        '/rajal/pemeriksaan/export-pdf-periksa-fisik?pemeriksaanfisik_id=' . $data['pemeriksaanfisik_id']
                    ],
                    [
                        'id' => 'btn-cetak-rajal-pemeriksaan-fisik-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - Asesmen Medis',
                    [
                        'id'          => 'btn-cetak-rajal-pemeriksaan-fisik-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/rajal/pemeriksaan/export-pdf-periksa-fisik?pemeriksaanfisik_id=' . $data['pemeriksaanfisik_id'],
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && ($data['r_soaprj'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - CPPT',
                    [
                        '/rajal/pemeriksaan/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ruangan_id='
                    ],
                    [
                        'id' => 'btn-cetak-rajal-cppt-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - CPPT',
                    [
                        'id'          => 'btn-cetak-rajal-cppt-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-toggle' => 'modal',
                        'action'    => '/rajal/pemeriksaan/show-popup-pdf?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ruangan_id=',
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 7 && ($data['r_soaprj'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - CPPT',
                    [
                        '/rajal/pemeriksaan/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_rj_id']) . '&ruangan_id='
                    ],
                    [
                        'id' => 'btn-cetak-rajal-cppt-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - CPPT',
                    [
                        'id'          => 'btn-cetak-rajal-cppt-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-toggle' => 'modal',
                        'action'    => '/rajal/pemeriksaan/show-popup-pdf?id=' . DocoHelpers::encrypt($data['pendaftaran_rj_id']) . '&ruangan_id=',
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && ($data['r_diagnosa'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Diagnosa',
                    [
                        '/rajal/pemeriksaan/export-pdf-diagnosa?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-rajal-diagnosa-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $urlSubmit = $this->helper->crossUrl('opento', [
                    'ruangan_id' => $ruangan_pend_id,
                    'instalasi_id' => $instalasi_pend_id,
                    'modul' => 'rajal',
                    'url' => '/rajal/pemeriksaan/export-pdf-diagnosa?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                ]);

                $btn_pelayanan .= Html::button(
                    'RJ - Diagnosa',
                    [
                        'id'          => 'btn-cetak-rajal-diagnosa-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => $urlSubmit,
                    ]
                );
            }
        }

        if ($data['instalasi_pend_id'] == 1 && (($data['r_tindakan'] == 1) || ($data['r_bmhp'] == 1)) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Tindakan & BMHP',
                    [
                        '/rajal/pemeriksaan/cetak-tindakan?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-rajal-tindakan-bmhp-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'

                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - Tindakan & BMHP',
                    [
                        'id'          => 'btn-cetak-rajal-tindakan-bmhp-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/rajal/pemeriksaan/cetak-tindakan?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && ($data['r_reseptur'] == 1) && ($data['pasienadmisi_id'] == '')) {
            $btn_pelayanan .= Html::button(
                'RJ - Reseptur',
                [
                    'id' => 'btn-cetak-rajal-reseptur-' . @$data['rowNum'],
                    'class' => 'btn btn-info btn-sm btn-riwayat',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_riwayat',
                    'action' => '/igd/riwayat-pasien/list-reseptur-rajal?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ruangan_id=' . $data['ruangan_pend_id'] . '&instalasi_id=' . $data['instalasi_pend_id']
                ]
            );
        }
        if ($data['instalasi_pend_id'] == 1 && ($data['r_resumemedis_rj_rd'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Resume Medis',
                    [
                        '/rajal/pemeriksaan/cetak-resume?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . DocoHelpers::encrypt($data['pasien_id'])
                    ],
                    [
                        'id' => 'btn-cetak-rajal-resume-medis-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - Resume Medis',
                    [
                        'id'          => 'btn-cetak-rajal-resume-medis-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/rajal/pemeriksaan/cetak-resume?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . DocoHelpers::encrypt($data['pasien_id']),
                    ]
                );
            }
        }
        /* KONSUL POLI RAJAL */
        if ($data['instalasi_pend_id'] == 1 && ($data['r_konsulpoli'] == 1) && ($data['pasienadmisi_id'] == '')) {
            $btn_pelayanan .= Html::button(
                'RJ - Konsultasi',
                [
                    'id'          => 'btn-cetak-rajal-konsul-poli-' . @$data['rowNum'],
                    'class'       => 'btn btn-info btn-sm btn-riwayat btn-show-konsul',
                    'data-target' => '#modal-lab',
                    'data-url'    => '/rajal/pemeriksaan/konsulpoli?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . DocoHelpers::encrypt($data['pasien_id']) . '&is_modal=false&preview_only=true',
                ]
            );
        }

        /* KONSUL POLI RANAP */
        if (!empty($data['pasienadmisi_id']) && $data['r_permintaankonsul'] == 1) {
            $btn_pelayanan .= Html::button(
                'RI - Konsultasi',
                [
                    'id'          => 'btn-cetak-ranap-konsul-poli-' . @$data['rowNum'],
                    'class'       => 'btn btn-info btn-sm btn-riwayat btn-show-konsul',
                    'data-target' => '#modal-lab',
                    'data-url'    => '/ranap/pemeriksaan-rawat-inap/permintaan-konsul?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . DocoHelpers::encrypt($data['pasien_id']) . '&is_modal=false&preview_only=true',
                ]
            );
        }

        /*Hasil USG */
        if (!empty($data['r_hasilusg']) && $data['r_hasilusg'] == 1) {
            $instalasi_pend_id_usg = !empty($data['pasienadmisi_id']) ? DocoConstants::INSTALASI_ID_RI : $data['instalasi_pend_id'];
            $btn_pelayanan .= Html::button(
                'Hasil Pemeriksaan',
                [
                    'id'          => 'btn-cetak-hasil-usg-' . @$data['rowNum'],
                    'class' => 'btn btn-info btn-sm btn-riwayat btn-penunjang',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_riwayat',
                    'action'    => '/api/usg/list-riwayat-usg?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . DocoHelpers::encrypt($data['pasien_id']) . '&instalasi_id=' . $instalasi_pend_id_usg
                ]
            );
        }

        if ($data['instalasi_pend_id'] == 2 && $data['r_asesmenperawat_rd'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - Asesmen Keperawatan',
                    [
                        '/igd/pemeriksaan-igd/cetak-askep-rd?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-igd-askep-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - Asesmen Keperawatan',
                    [
                        'id'          => 'btn-cetak-igd-askep-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-askep-rd?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 2 && $is_partograf == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - Partograf',
                    [
                        '/igd/pemeriksaan-igd/cetak-keadaan-umum?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-keadaan-umum',
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(

                    'RD - Partograf',
                    [
                        'id'          => 'btn-cetak-keadaan-umum',
                        'data-target' => '/igd/pemeriksaan-igd/cetak-keadaan-umum?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 2 && $is_nursingnotes == 1 && empty($data['pasienadmisi_id'])) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - Nursing Note',
                    [
                        '/igd/pemeriksaan-igd/cetak-nursing-note?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-igd-askep-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - Nursing Note',
                    [
                        'id'          => 'btn-cetak-igd-askep-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-nursing-note?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 2 && $data['r_asesmendokter'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - Asesmen Medis',
                    [
                        '/igd/pemeriksaan-igd/cetak-asmed-rd?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-igd-asesmen-dokter-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - Asesmen Medis',
                    [
                        'id'          => 'btn-cetak-igd-asesmen-dokter-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-asmed-rd?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 2 && $data['r_cppt'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - CPPT',
                    [
                        '/igd/pemeriksaan-igd/cetak-list-dpjp?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-igd-asesmen-dpjp-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - CPPT',
                    [
                        'id' => 'btn-cetak-ranap-asesmen-dpjp-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat',
                        'data-target' => '#modal-preview',
                        'data-toggle' => 'modal',
                        'action'    => '/igd/pemeriksaan-igd/show-popup-pdf?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
                    ]
                );
            }

            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - Instruksi & Implementasi',
                    [
                        '/igd/pemeriksaan-igd/cetak-implementasi-pdf?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-igd-implementasi-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - Instruksi & Implementasi',
                    [
                        'id'          => 'btn-cetak-igd-implementasi-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-implementasi-pdf?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
                    ]
                );
            }
        }
        // if ($data['instalasi_pend_id'] == 2 &&($data['r_instruktitindakan'] == 1 || $data['r_instruktitindakanbmhp'] == 1)){
        //     $btn_pelayanan .= Html::a('RD - Tindakan & BMHP',
        //         [
        //             '/igd/pemeriksaan-igd/cetak-tindakan-bmhp?id='.DocoHelpers::encrypt($data['pendaftaran_id']).'&norm='.$norm
        //         ], [
        //          'id' => 'btn-cetak-igd-tindakan-bmhp-'.@$data['rowNum'],
        //             'class'=>'btn btn-info btn-sm btn-riwayat',
        //             'target' => '_blank'
        //         ]
        //     );
        // }
        if ($data['instalasi_pend_id'] == 2 && $data['r_reseptur'] == 1 && empty($data['pasienadmisi_id'])) {
            $btn_pelayanan .= Html::button(
                'RD - Reseptur',
                [
                    'id' => 'btn-cetak-igd-reseptur-' . @$data['rowNum'],
                    'class' => 'btn btn-info btn-sm btn-riwayat btn-reseptur',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_riwayat',
                    'action' => '/igd/riwayat-pasien/list-reseptur?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                ]
            );
        }

        if ($data['instalasi_pend_id'] == 2 && $data['r_kesimpulan_rd'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - Kesimpulan',
                    [
                        '/igd/pemeriksaan-igd/cetak-pdf-kesimpulan?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-igd-kesimpulan-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - Kesimpulan',
                    [
                        'id'          => 'btn-cetak-igd-kesimpulan-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-pdf-kesimpulan?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }

        if ($data['instalasi_pend_id'] == 2 && $data['r_resumemedis_rj_rd'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - Resume Medis',
                    [
                        '/igd/pemeriksaan-igd/cetak-resume?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . $data['pasien_id'] . '&margin_top=0'
                    ],
                    [
                        'id' => 'btn-cetak-igd-resume-medis-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - Resume Medis',
                    [
                        'id'          => 'btn-cetak-igd-resume-medis-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-resume?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . $data['pasien_id'] . '&margin_top=0'
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 2 && $data['carakeluar_id'] == 5) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::button(
                    'RD - SPRI',
                    [
                        'id'          => 'btn-cetak-igd-spri-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-spri?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&type=rd',
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - SPRI',
                    [
                        'id'          => 'btn-cetak-igd-spri-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-spri?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&type=rd',
                    ]
                );
            }
        }

        if ($data['instalasi_pend_id'] == 2 && $data['is_meninggal'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::button(
                    'RD - Surat Kematian',
                    [
                        'id'          => 'btn-cetak-igd-surat-kematian' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-pdf-surat-kematian?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - Surat Kematian',
                    [
                        'id'          => 'btn-cetak-igd-surat-kematian' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-pdf-surat-kematian?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }

        if ($data['instalasi_pend_id'] == 2) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::button(
                    'RD - Formulir Triase',
                    [
                        'id'          => 'btn-cetak-igd-formulir-triase' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-formulir-triase?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&is_cetak=1',
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RD - Formulir Triase',
                    [
                        'id'          => 'btn-cetak-igd-formulir-triase' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-formulir-triase?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&is_cetak=1',
                    ]
                );
            }
        }

        if ($data['instalasi_pend_id'] == 1 && $data['carakeluar_id'] == 5) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::button(
                    'RJ - SPRI',
                    [
                        'id'          => 'btn-cetak-rajal-spri-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-spri?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&type=rj',
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - SPRI',
                    [
                        'id'          => 'btn-cetak-rajal-spri-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-spri?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&type=rj',
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && $data['is_meninggal'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::button(
                    'RJ - Surat Kematian',
                    [
                        'id'          => 'btn-cetak-rajal-meninggal-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/rajal/pemeriksaan/export-pdf-surat-kematian?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - Surat Kematian',
                    [
                        'id'          => 'btn-cetak-rajal-meninggal-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/rajal/pemeriksaan/export-pdf-surat-kematian?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if (!empty($data['pasienadmisi_id']) && $data['r_asesmenawal'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Asesmen Keperawatan',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-askep-rd?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-ranap-asesmen-awal-keperawatan-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Asesmen Keperawatan',
                    [
                        'id'          => 'btn-cetak-ranap-asesmen-awal-keperawatan-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-askep-rd?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }


        if (!empty($data['pasienadmisi_id']) && $data['r_asesmenmedis'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Asesmen Medis',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-asesmen?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-ranap-asesmen-awal-medis-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Asesmen Medis',
                    [
                        'id'          => 'btn-cetak-ranap-asesmen-awal-medis-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-asesmen?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
                    ]
                );
            }
        }

        if (!empty($data['pasienadmisi_id']) && $is_nursingnotes == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Nursing Note',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-nursing-note?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-igd-askep-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Nursing Note',
                    [
                        'id'          => 'btn-cetak-igd-askep-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-nursing-note?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }

        if (!empty($data['pasienadmisi_id']) && $data['r_rekonsiliasiobat'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Rekonsiliasi Obat',
                    [
                        '/ranap/pemeriksaan-rawat-inap/export-pdf-rekon?pendaftaran_id=' . $data['pendaftaran_id'] . '&admisi_id=' . $data['pasienadmisi_id']
                    ],
                    [
                        'id' => 'btn-cetak-ranap-rekon-obat-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Rekonsiliasi Obat',
                    [
                        'id'          => 'btn-cetak-ranap-rekon-obat-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/export-pdf-rekon?pendaftaran_id=' . $data['pendaftaran_id'] . '&admisi_id=' . $data['pasienadmisi_id'],
                    ]
                );
            }
        }

        if (!empty($data['pasienadmisi_id']) && $data['r_dischargeplan'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Discharge Planning',
                    [
                        '/ranap/pemeriksaan-rawat-inap/print-pdf?pasienadmisi_id=' . DocoHelpers::encrypt($data['pasienadmisi_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-ranap-discharge-plan-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Discharge Planning',
                    [
                        'id'          => 'btn-cetak-ranap-discharge-plan-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/print-pdf?pasienadmisi_id=' . DocoHelpers::encrypt($data['pasienadmisi_id']) . '&norm=' . $norm,
                    ]
                );
            }
        }

        if (!empty($data['pasienadmisi_id']) && $is_partograf == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Partograf',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-keadaan-umum?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ],
                    [
                        'id' => 'btn-cetak-keadaan-umum',
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Partograf',
                    [
                        'id'          => 'btn-cetak-keadaan-umum',
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-url' => '/ranap/pemeriksaan-rawat-inap/cetak-keadaan-umum?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }

        if (!empty($data['pasienadmisi_id']) && $data['r_cppt'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - CPPT',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-ranap-asesmen-dpjp-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - CPPT',
                    [
                        'id' => 'btn-cetak-ranap-asesmen-dpjp-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat',
                        'data-target' => '#modal-preview',
                        'data-toggle' => 'modal',
                        'action'    => '/ranap/pemeriksaan-rawat-inap/show-popup-pdf?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
                    ]
                );
            }
        }
        // if ($data['instalasi_pend_id'] == 3 || $data['instalasi_pend_id'] == 2 && ($data['r_tindakan'] == 1 || $data['r_bmhp'] == 1)){
        //     $btn_pelayanan .= Html::a('RI - Tindakan dan BMHP',
        //         [
        //             '/igd/riwayat-pasien/cetak-riwayat-tindakan-bmhp?id='.DocoHelpers::encrypt($data['pendaftaran_id']).'&no_pendaftaran='.$data['no_pendaftaran']
        //         ], [
        //             'id' => 'btn-cetak-ranap-tindakanbmhp-'.@$data['rowNum'],
        //             'class'=>'btn btn-info btn-sm btn-riwayat btn-tindakanbmhp',
        //             'target' => '_blank'
        //         ]
        //     );
        // }
        if (!empty($data['pasienadmisi_id']) && $data['r_reseptur'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Reseptur',
                    [
                        '/igd/riwayat-pasien/review-reseptur?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ins=' . $data['instalasi_pend_id']
                    ],
                    [
                        'id' => 'btn-cetak-ranap-implementasi-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Reseptur',
                    [
                        'id' => 'btn-cetak-ranap-reseptur-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-reseptur',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_riwayat',
                        'action' => '/igd/riwayat-pasien/list-reseptur?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ins=' . $data['instalasi_pend_id']
                    ]
                );
            }
        }
        if (!empty($data['pasienadmisi_id']) && ($data['r_instruktitindakan'] == 1 || $data['r_instruktitindakanbmhp'] == 1)) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Instruksi & Implementasi',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-implementasi-pdf?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&skip_ruangan=1'
                    ],
                    [
                        'id' => 'btn-cetak-ranap-implementasi-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Instruksi & Implementasi',
                    [
                        'id'          => 'btn-cetak-ranap-implementasi-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-implementasi-pdf?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&skip_ruangan=1',
                    ]
                );
            }
        }
        if (!empty($data['pasienadmisi_id']) && $data['r_pemberianobat'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Catatan Pemberian Obat',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-pemberian-obat?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-ranap-pemberianobat-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-pemberian-obat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Catatan Pemberian Obat',
                    [
                        'id'          => 'btn-cetak-ranap-pemberianobat-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-pemberian-obat?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
                    ]
                );
            }
        }
        if (!empty($data['pasienadmisi_id']) && $data['r_pindahkamar'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Pindah Kamar',
                    [
                        '/igd/riwayat-pasien/cetak-riwayat-pindah-kamar?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&no_rekam_medik=' . $data['no_rekam_medik']
                    ],
                    [
                        'id' => 'btn-pindah-kamar-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-pindah-kamar',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Pindah Kamar',
                    [
                        'id'          => 'btn-pindah-kamar-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/riwayat-pasien/cetak-riwayat-pindah-kamar?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&no_rekam_medik=' . $data['no_rekam_medik'],
                    ]
                );
            }
        }
        if (!empty($data['pasienadmisi_id']) && $data['r_resumemedis_ri'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Resume Medis',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-resume?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . DocoHelpers::encrypt($data['pasien_id']) . '&margin_top=0'
                    ],
                    [
                        'id' => 'btn-cetak-resume-medis-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-resume-medis',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Resume Medis',
                    [
                        'id'          => 'btn-cetak-resume-medis-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-resume?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasien_id=' . DocoHelpers::encrypt($data['pasien_id']) . '&margin_top=0',
                    ]
                );
            }
        }
        if (!empty($data['pasienadmisi_id']) && $data['r_visitedokter'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Visite Dokter',
                    [
                        '/igd/riwayat-pasien/cetak-visite-dokter?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-ranap-visitedokter-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-visite-dokter',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Visite Dokter',
                    [
                        'id'          => 'btn-cetak-ranap-visitedokter-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/riwayat-pasien/cetak-visite-dokter?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
                    ]
                );
            }
        }
        if (!empty($data['pasienadmisi_id']) && $data['is_meninggal'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::button(
                    'RI - Surat Kematian',
                    [
                        'id'          => 'btn-cetak-igd-surat-kematian' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/inf-pasien-pulang/export-pdf-surat-kematian?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Surat Kematian',
                    [
                        'id'          => 'btn-cetak-igd-surat-kematian' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/inf-pasien-pulang/export-pdf-surat-kematian?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }

        if (!empty($data['pasienadmisi_id']) && $data['r_permintaankonsul'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Permintaan Konsul',
                    [
                        '/igd/riwayat-pasien/riwayat-permintaan-konsul?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ],
                    [
                        'id' => 'btn-cetak-ranap-permintaankonsul-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-permintaan-konsul',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RI - Permintaan Konsul',
                    [
                        'id' => 'btn-cetak-ranap-permintaankonsul-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_riwayat',
                        'action' => '/igd/riwayat-pasien/riwayat-permintaan-konsul?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
                    ]
                );
            }
        }
        // cetak dirujuk ke
        // if ($dataRujukan = $this->getDataRujukan($data['pendaftaran_id']) > 0 && !empty($data['carakeluar_id']) && $data['carakeluar_id'] == '2') {
        if ($data['is_rujukan'] && !empty($data['carakeluar_id']) && $data['carakeluar_id'] == '2') {
            $btn_pelayanan .= Html::a(
                'Cetak Rujukan',
                [
                    '/igd/pemeriksaan-igd/cetak-rujukan?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                ],
                [
                    'id' => 'btn-cetak-rajal-asesmen-rujukan' . @$data['rowNum'],
                    'class' => 'btn btn-info btn-sm btn-riwayat-rujukan',
                    'target' => '_blank'
                ]
            );
        }

        if (isset($data['is_dokumen']) && $data['is_dokumen']) {
            $btn_pelayanan .= Html::button('Dokumen Upload', [
                'id' => 'btn-view-dokumen',
                'class' => 'btn btn-info btn-sm btn-riwayat',
                'action' => '/api/upload-dokumen/tab-upload-dokumen?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&pasienadmisi_id=' . $data['pasienadmisi_id'] . '&is_modal=true&parent_id=dokumen-riwayat-pasien&isHide=true',
                'data-toggle' => 'modal',
                'data-target' => '#modal_riwayat',
            ]);
        }

        if (!empty($data['pasienadmisi_id']) && $data['r_asesmenmedis_spesialis'] == 1) {
            $pendaftaranId = DocoHelpers::encrypt($data['pendaftaran_id']);
            $pasienAdmisiId = DocoHelpers::encrypt($data['pasienadmisi_id']);

            $btn_pelayanan .= Html::button(
                'RI - Asesmen Medis Rawat Inap',
                [
                    'id' => 'btn-cetak-asmed-spesialis-' . @$data['rowNum'],
                    'class' => 'btn btn-info btn-sm btn-riwayat',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_riwayat',
                    'data-width' => '75%',
                    'action' => '/ranap/pemeriksaan-rawat-inap/modal-asesmen-spesialis?id=' . $pendaftaranId . '&pasienadmisi_id=' . $pasienAdmisiId,
                ]
            );
        }

        if(isset($data['is_monitoringttv']) && $data['is_monitoringttv']) {
            $btn_pelayanan .= Html::button('Monitoring TTV', [
                'id' => 'btn-cetak-monitoring-ttv',
                'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                'data-target' => '#modal-preview',
                'data-url' => '/rajal/pemeriksaan/cetak-monitoring-ttv?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
            ]);
        }
        
        // resume medis fisio
        if($data['is_resume_fisio']) {
            $btn_pelayanan .= Html::button('Fisio - Resume Medis', [
                'id' => 'btn-resume-medis-fisio',
                'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                'data-target' => '#modal-preview',
                'data-url' => '/fisioterapi/resume-medis/cetak-resume?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
            ]);
        }
        
        if(isset($data['is_uji_fungsi_fisio']) && $data['is_uji_fungsi_fisio']) {
            $btn_pelayanan .= Html::button('Uji Fungsi', [
                'id' => 'btn-cetak-uji-fungsi-fisio',
                'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                'data-target' => '#modal-preview',
                'data-url' => '/fisioterapi/uji-fungsi-fisioterapi/cetak-uji-fungsi?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
            ]);
        }

        if(isset($data['is_sbar']) && $data['is_sbar']) {
            $btn_pelayanan .= Html::button('SBAR', [
                'id' => 'btn-cetak-sbar',
                'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                'data-target' => '#modal-preview',
                'data-url' => '/rajal/pemeriksaan/cetak-sbar?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
            ]);
        }

        // Formulir Rajal
        if(isset($data['is_formulir_rajal']) && $data['is_formulir_rajal']) {
            $btn_pelayanan .= Html::button('Formulir Rawat Jalan', [
                'id' => 'btn-cetak-formulir-rawat-jalan',
                'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                'data-target' => '#modal-preview',
                'data-url' => '/fisioterapi/formulir-rajal/cetak-formulir-rajal?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
            ]);
        }

        if($data['is_rehabilitasimedis']) {
            $btn_pelayanan .= Html::button('Fisio - Program Rehabilitasi', [
                'id' => 'btn-rehabilitasi-medis',
                'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                'data-target' => '#modal-preview',
                'data-url' => '/fisioterapi/rehabilitasi/cetak-program-rehabilitasi?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
            ]);
        }

        return $btn_pelayanan;
    }

    protected function riwayatPenunjangGlobal($norm, $data, $is_jenis)
    {
        $btn_penunjang = '';
        if ($is_jenis == null) {
            if ($data['p_laboratorium'] == 1) {
                $btn_penunjang .= Html::button(
                    'Laboratorium',
                    [
                        'id' => 'btn-cetak-penunjang-lab-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-penunjang',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_riwayat',
                        'action' => '/igd/riwayat-pasien/list-penunjang-laboratorium?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . (!is_null($data['pasienadmisi_id']) ? '&pasienadmisi_id=' . $data['pasienadmisi_id'] : '')
                    ]
                );
            }
            if ($data['p_radiologi'] == 1) {
                $btn_penunjang .= Html::button(
                    'Radiologi',
                    [
                        'id' => 'btn-cetak-penunjang-rad-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-penunjang',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_riwayat',
                        'data-width' => '88%',
                        'data-style' => 'margin-right: 15px !important',
                        'action' => '/igd/riwayat-pasien/list-penunjang-radiologi?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $data['no_rekam_medik'] . '&type=riwayat' . '&instalasi_id=' . $data['instalasi_pend_id'] . (!is_null($data['pasienadmisi_id']) ? '&pasienadmisi_id=' . $data['pasienadmisi_id'] : '')
                    ]
                );
            }
            if ($data['p_operasi'] == 1) {
                $btn_penunjang .= Html::button(
                    'Bedah Sentral',
                    [
                        'id' => 'btn-cetak-penunjang-bedah-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-penunjang',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_riwayat',
                        'action' => '/igd/riwayat-pasien/list-penunjang-bedah?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ]
                );
            }
            if ($data['r_asesmengizinrs'] == 1) {
                $btn_penunjang .= Html::a(
                    'NRS',
                    '/igd/riwayat-pasien/cetak-nrs?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    [
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-nrs',
                        'target' => '_blank'
                    ]
                );
            }
        } elseif ($is_jenis == 'lab') {
            if ($data['p_laboratorium'] == 1) {
                $btn_penunjang .= Html::button(
                    'Laboratorium',
                    [
                        'id' => 'btn-cetak-penunjang-lab-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-penunjang',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_riwayat',
                        'action' => '/igd/riwayat-pasien/list-penunjang-laboratorium?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . (!is_null($data['pasienadmisi_id']) ? '&pasienadmisi_id=' . $data['pasienadmisi_id'] : '')
                    ]
                );
            }
        } elseif ($is_jenis == 'rad') {
            if ($data['p_radiologi'] == 1) {
                $btn_penunjang .= Html::button(
                    'Radiologi',
                    [
                        'id' => 'btn-cetak-penunjang-rad-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-penunjang',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_riwayat',
                        'action' => '/igd/riwayat-pasien/list-penunjang-radiologi?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ]
                );
            }
        } else {
            if ($data['p_operasi'] == 1) {
                $btn_penunjang .= Html::button(
                    'Bedah Sentral',
                    [
                        'id' => 'btn-cetak-penunjang-bedah-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-penunjang',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_riwayat',
                        'action' => '/igd/riwayat-pasien/list-penunjang-bedah?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                    ]
                );
            }
        }
        return $btn_penunjang;
    }

    public function actionHistorySurgeryOrders($norm)
    {
        $pasienAdmisiId = Yii::$app->request->get('pasienadmisi_id', null);
        try {
            $title = Yii::t('fe', 'Riwayat Bedah');
            $url   = '/igd';
            $pendaftaran_id = Yii::$app->request->get('id', null);
            $query = [
                'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
            ];

            if ($pasienAdmisiId !== null) {
                $query['pasienadmisi_id'] = $pasienAdmisiId;
            }

            $dataPasien = $this->guzzleExec($this->restGeneral, [
                'url' => 'riwayat-pasien/patient-global',
                'method' => 'GET',
                'payload' => [
                    'query' => $query
                ]
            ]);

            $nama_pasien       = isset($dataPasien['data']['nama_pasien']) ? $dataPasien['data']['nama_pasien'] : '-';
            $no_rekam_medik    = isset($dataPasien['data']['no_rekam_medik']) ? $dataPasien['data']['no_rekam_medik'] : '-';
            $no_telepon_pasien = isset($dataPasien['data']['no_telepon_pasien']) ? $dataPasien['data']['no_telepon_pasien'] : '-';
            $umur              = isset($dataPasien['data']['umur']) ? $dataPasien['data']['umur'] : '-';
            $jenis_kelamin     = isset($dataPasien['data']['jenis_kelamin']) ? $dataPasien['data']['jenis_kelamin'] : '-';

            $title = $title . ' - ' . $nama_pasien . ' / ' . ArrayHelper::getValue($dataPasien, 'data.penjamin_nama', '-') . ' / ' . ArrayHelper::getValue($dataPasien, 'data.kelaspelayanan_nama', '-');
            return $this->renderAjax('//cppt/penunjang/_modal_riwayat_bedah', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionGetDataHistorySurgery()
    {
        try {
            $request = Yii::$app->request;
            $norm = $request->get('norm');
            $draw = $request->get('draw', 1);
            $result = $data = [];
            $result['data'] = [];
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

            $response = $this->restGeneral->request('get', 'riwayat-pasien/data-history-surgery', [
                'query' => [
                    'norm' => $norm,
                    'filter' => $filter,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $rowNum = 1;
            $arr_temp = [];

            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $jadwalMulai = ArrayHelper::getValue($value, 'jam_rencana_mulai');
                $jadwalSelesai = ArrayHelper::getValue($value, 'jam_rencana_selesai');
                $jadwalTanggal = ArrayHelper::getValue($value, 'tanggal_permintaan');
                $tanggalOrder = ArrayHelper::getValue($value, 'created_date');
                $value['tanggal_permintaan'] = '';
                $jadwalDay = '';
                if ($jadwalTanggal) {
                    $jadwalDay = date('D', strtotime($jadwalTanggal));
                    $jadwalDay = DocoHelpers::$_hari_indo[$jadwalDay];
                    $value['tanggal_permintaan'] = date('d-m-Y H:i:s', strtotime($tanggalOrder));
                    $jadwalTanggal = date('d-m-Y', strtotime($jadwalTanggal));
                }
                if ($tanggalOrder) {
                    $tanggalOrder = date('d-m-Y', strtotime($tanggalOrder));
                }
                $value['rowNum'] = $no;
                $value['tanggal_disetujui'] = isset($value['tanggal_disetujui']) ? date('d-m-Y H:i:s', strtotime($value['tanggal_disetujui'])) : '-';
                $value['jadwal_bedah'] = "($jadwalDay) <br/> $jadwalTanggal <br/> $jadwalMulai - $jadwalSelesai";
                $value['disetujui_oleh'] = isset($value['disetujui_oleh']) ? $value['disetujui_oleh'] : '-';
                $row[$key] = $value;
            }

            $result['data'] = $row;
            $result['recordsTotal'] = $body['response']['recordsTotal'];
            $result['recordsFiltered'] = $body['response']['recordsFiltered'];

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    protected function getDataRujukan($pendaftaran_id)
    {
        $reqData = $this->restGeneral->get('riwayat-pasien/get-data-rujukan', [
            'query' => [
                'pendaftaran_id' => $pendaftaran_id
            ]
        ]);
        $bodyData = json_decode($reqData->getBody(), true);
        return $bodyData['response']['totalCount'];
    }

    public function actionPopupViewDokumen()
    {
        return $this->renderAjax('//riwayat-pasien/_view_dokumen', [
            'url' => '/igd/riwayat-pasien/get-dokumen-data?id=' . Yii::$app->request->get('id', null) . '&pasienadmisi_id=' . Yii::$app->request->get('pasienadmisi_id', null),
            'title' => 'List Dokumen Pasien'
        ]);
    }

    public function actionGetDokumenData()
    {
        $data = [];
        $getDataDokumen = $this->helper->guzzleExec($this->restGeneral, [
            'url' => 'riwayat-pasien/get-dokumen-data',
            'payload' => [
                'query' => array_merge(
                    [
                        'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id', null)),
                        'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null)
                    ],
                    Yii::$app->request->get()
                )
            ],
        ]);
        if (!empty($getDataDokumen['data'])) {
            foreach ($getDataDokumen['data'] as $key => $item) {
                $data[$key] = $item;
                $fileType = end(explode('.', $item['filename']));
                $data[$key]['aksi'] = '';
                if (in_array($fileType, $this->fileAction['detail'])) {
                    $data[$key]['aksi'] = Html::button('Lihat', [
                        'class' => 'btn btn-info btn-sm btn-view-img',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-preview-img',
                        'action' => '/igd/riwayat-pasien/preview-dokumen?dokumenupload_id=' . $item['dokumenupload_id']
                    ]);
                } else if (in_array($fileType, $this->fileAction['preview'])) {
                    $data[$key]['aksi'] = Html::button(
                        'Lihat',
                        [
                            'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                            'data-target' => '#modal-preview',
                            'data-url' => '/igd/riwayat-pasien/preview-dokumen?dokumenupload_id=' . $item['dokumenupload_id'] . '&filetype=pdf',
                        ]
                    );
                } else if (in_array($fileType, $this->fileAction['download'])) {
                    $data[$key]['aksi'] = Html::a(
                        'Unduh',
                        '/igd/riwayat-pasien/preview-dokumen?dokumenupload_id=' . $item['dokumenupload_id'] . '&filetype=' . $fileType,
                        [
                            'class' => 'btn btn-info btn-sm',
                            'target' => '_blank'
                        ]
                    );
                }
            }
        }
        $result['data'] = $data;
        $result['recordsTotal'] = $getDataDokumen['recordsTotal'];
        $result['recordsFiltered'] = $getDataDokumen['recordsFiltered'];
        return $this->helper->response($result);
    }

    public function actionPreviewDokumen()
    {
        $getDataDokumen = $this->helper->guzzleExec($this->restGeneral, [
            'url' => 'riwayat-pasien/get-detail-dokumen',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id', null)),
                    'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
                    'dokumenupload_id' => Yii::$app->request->get('dokumenupload_id')
                ],
            ],
        ]);
        $filePath = Yii::getAlias('@baseFileUrl') . $getDataDokumen['path'] . $getDataDokumen['filename'];

        if (in_array(Yii::$app->request->get('filetype', null), $this->fileAction['preview'])) {
            return DocoHelpers::previewPdf($filePath);
        } else if (in_array(Yii::$app->request->get('filetype', null), $this->fileAction['download'])) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            return $this->renderAjax('//riwayat-pasien/_detail_dokumen', [
                'data' => $getDataDokumen,
                'type' => end(explode('.', $getDataDokumen['filename'])),
                'title' => 'Dokumen - ' . ucwords(strtolower($getDataDokumen['nama_dokumen'])),
                'path' => $filePath
            ]);
        }
    }

    public function actionCetakNrs($pendaftaran_id)
    {
        $path = Yii::getAlias("@download") . "/cetak-nrs.pdf";
        $_restGizi = Yii::$app->docoRest->gizi;
        try {
            $request = $_restGizi->get('nrs/cetak-nrs', [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('pendaftaran_id', null)),
                ],
                'save_to' => $path,
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionViewDetailPatient()
    {
        try {
            $restMaster   = Yii::$app->docoRest->master;
            $instalasi_id = DocoHelpers::decrypt(Yii::$app->request->get('instalasi_id', null));
            $pasien_id = Yii::$app->request->get('pasien_id', null);

            switch ($instalasi_id) {
                case DocoConstants::INSTALASI_ID_RJ:
                    $api_url = 'tra-pemeriksaan/get-data-pasien-detail';
                    $restApi = Yii::$app->docoRest->rajal;
                    break;
                case DocoConstants::INSTALASI_ID_RI:
                    $restApi = Yii::$app->docoRest->ranap;
                    $api_url = 'pemeriksaan-rawat-inap/get-data-pasien-detail';
                    break;
                case DocoConstants::INSTALASI_ID_RD:
                    $api_url = 'pemeriksaan-igd/get-data-pasien-detail';
                    $restApi = Yii::$app->docoRest->igd;
                    break;
                default:
                    throw new \Exception();
                    break;
            }

            $response = $this->guzzleExec($restApi, [
                'url' => $api_url,
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'id' => $pasien_id,
                    ]
                ]
            ]);

            $config_golongan_darah = $this->guzzleExec($restMaster, [
                'url' => 'lookup/list-lookup-by-type',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'param' => 'golongan_darah'
                    ]
                ]
            ]);

            $data_pasien = isset($response['data_pasien']) ? $response['data_pasien'] : null;
            $data_keluarga = isset($response['data_keluarga']) ? $response['data_keluarga'] : null;
            return $this->renderAjax('//cppt/_modal_informasi_patient_detail', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionModalBerkasPasien()
    {
        try {
            $this->_restRm = Yii::$app->docoRest->rm;
            $request = Yii::$app->request;
            $no_rekam_medik = $request->get('no_rekam_medik', '');
            $info_pasien = $this->_restRm->get('allow/get-info-pasien', [
                'query' => [
                    'no_rekam_medik' => $no_rekam_medik
                ]
            ]);

            $response = json_decode($info_pasien->getBody(), true);
            $info_pasien = ArrayHelper::getValue($response, 'response');
            if (ArrayHelper::getValue($response, 'metadata.status', 500) != 200) {
                return DocoHelpers::responseTemplate(ArrayHelper::getValue($info_pasien, 'metadata.status'), ArrayHelper::getValue($info_pasien, 'response.message', 'Terjadi Kesalah pada server'));
            }

            return $this->renderAjax('//riwayat-pasien/berkas-pasien/_index', compact('info_pasien', 'no_rekam_medik'));
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionDatatableBerkasPasien()
    {
        try {
            @ini_set('max_execution_time', '300');
            $request = Yii::$app->request;
            $params = @parse_ini_file('../config/env/.env', true);
            $connectionFtp = $this->actionFtpConnectNAS();
            $folder_path = isset($params['konfigftp_nas']['path']) ? $params['konfigftp_nas']['path'] . '/' . $request->get('no_rekam_medik') : null;
            $results = [];
            $total_data = 0;
            $filtered_data = 0;
            if (is_resource($connectionFtp)) {
                try {
                    $list = ftp_rawlist($connectionFtp, $folder_path);
                } catch (\Exception $e) {
                    $list = [];
                }

                if (is_array($list) && !empty($list)) {
                    // Mengolah data mentah dari FTP
                    foreach ($list as $key => $line) {
                        $regex = preg_split('/\s+/', $line, 9);
                        list($perms, $links, $user, $group, $size, $d1, $d2, $d3, $filename) = $regex;

                        $timestamp = strtotime(implode(' ', array($d1, $d2, $d3)));
                        $date = new \DateTime(implode(' ', array($d1, $d2, $d3)), new \DateTimeZone(Yii::$app->formatter->defaultTimeZone)); // set format utc terlebihd dahulu, karena ftp php gak ngasih format bahwa ini UTC
                        $timestamp = $date->setTimezone(new \DateTimeZone(Yii::$app->formatter->timeZone))->format('U'); // mirip strtotime, tapi ngerubah format dari utc ke timezone (dari sirs indo/jakarta)
                        $type = $regex[0]{
                        0} === 'd' ? 'directory' : 'file';
                        if (pathinfo($filename, PATHINFO_EXTENSION) == 'pdf') {
                            $results[] = compact('filename', 'timestamp', 'type', 'links', 'user', 'group', 'perms');
                        }
                    }
                }

                /*
                  Apply Filter datatable
                */
                $order = $request->get('order', []);
                $columns = $request->get('columns', []);
                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $total_data = count($results);
                /*  Search Filter Fitur (searching udah bisa multiple tapi hanya string search aja)*/
                $filter_search = [];
                foreach ($columns as $key => $column) {
                    if (!empty($column['search']['value'])) {
                        $filter_search[$column['data']] = $column['search']['value'];
                    }
                }
                $results = array_filter($results, function ($val) use ($filter_search) {
                    foreach ($filter_search as $key => $filter) {
                        if (stripos($val[$key], $filter) === FALSE) return false;
                    }
                    return true;
                });

                $filtered_data = count($results);

                /* End Of Filter Search */

                /*  Ordering Fitur (Belum bisa multiple)*/
                if (!empty($order)) {
                    foreach ($order as $key => $value) {
                        $key_filtered = ArrayHelper::getValue($columns, $value['column'] . '.data');
                        if (ArrayHelper::getValue($value, 'dir', 'asc') == 'desc') {
                            usort($results, function ($a, $b) use ($key_filtered) {
                                return strcasecmp($b[$key_filtered], $a[$key_filtered]);
                            });
                        } else {
                            usort($results, function ($a, $b) use ($key_filtered) {
                                return strcasecmp($a[$key_filtered], $b[$key_filtered]);
                            });
                        }
                    }
                }
                /* End Of Filter Search */

                /*  Pagination Fitur */
                $results = array_slice($results, $start, $length);
                /* End Of Filter Search */
            }
            $result['data'] = $results;
            $result['recordsTotal'] = $total_data;
            $result['recordsFiltered'] = $filtered_data;

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }
    
    public function actionPreviewBerkasPasien()
    {
        $request = Yii::$app->request;
        $params = @parse_ini_file('../config/env/.env', true);
        $base_path = ArrayHelper::getValue($params, 'konfigftp_nas.path', null);

        $ftp_nas_config = [
            'username' => ArrayHelper::getValue($params, 'konfigftp_nas.username', null),
            'password' => ArrayHelper::getValue($params, 'konfigftp_nas.password', null),
            'ip_server' => ArrayHelper::getValue($params, 'konfigftp_nas.host', null)
        ];

        DocoHelpers::previewPdfFromFTP($ftp_nas_config, $base_path . '/' . $request->get('no_rekam_medik', '') . '/' . $request->get('name', ''));
    }

    public function actionFtpConnectNAS()
    {
        $params = @parse_ini_file('../config/env/.env', true);
        $host = isset($params['konfigftp_nas']) ? $params['konfigftp_nas']['host'] : null;
        $user = isset($params['konfigftp_nas']) ? $params['konfigftp_nas']['username'] : null;
        $password = isset($params['konfigftp_nas']) ? $params['konfigftp_nas']['password'] : null;
        $ftpConn = ftp_connect($host);
        $login = ftp_login($ftpConn, $user, $password);
        ftp_pasv($ftpConn, true);

        return $ftpConn;
    }

    /**
     * @param int $pendaftaran_id
     * @return bool
     */
    protected function checkUjiFungsiFisio($pendaftaran_id)
    {
        try {
            $restFisioterapi = Yii::$app->docoRest->fisioterapi;
            $response = $restFisioterapi->request('get', 'uji-fungsi-fisioterapi/get-data-uji-fungsi-fisioterapi', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($body, 'response', []);
            $data = ArrayHelper::getValue($response, 'data', []);
            
            $hasUjiFungsiData = !empty($data);
            
            return $hasUjiFungsiData;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * @param int $pendaftaran_id
     * @return bool
     */
    protected function checkFormulirRajal($pendaftaran_id)
    {
        try {
            $restFisioterapi = Yii::$app->docoRest->fisioterapi;
            $response = $restFisioterapi->request('get', 'formulir-rajal/get-data-formulir-rajal', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($body, 'response', []);
            $data = ArrayHelper::getValue($response, 'data', []);
            
            return !empty($data);
        } catch (\Exception $e) {
            return false;
        }
    }
}
