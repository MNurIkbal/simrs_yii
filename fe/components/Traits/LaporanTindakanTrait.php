<?php

namespace app\components\Traits;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\BatalInstruksiForm;
use app\models\BatalTindakanBmhpForm;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

trait LaporanTindakanTrait
{

    public function actionListDataTindakan()
    {
        $title = 'Laporan Terapi';
        $type = Yii::$app->request->get('type', DocoConstants::TYPE_RJ);
        $pendaftaran_id = Yii::$app->request->get('id');
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
        $pasien_id = Yii::$app->request->get('pasien_id', null); // kegunaannya untuk ri dan rd
        $is_jenis = Yii::$app->request->get('is_jenis', 'rj');
        switch ($type) {
            case DocoConstants::TYPE_RJ:
            case 'rajalLaporanTerapi':
                $url = [
                    'datatable' => Url::to(
                        ['/rajal/pemeriksaan/get-list-tindakan', 'id' => $pendaftaran_id, 'pasien_id' => $pasien_id, 'type' => $type, 'is_jenis' => $is_jenis]
                    )
                ];
                $title = $title . ' - '. ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-') . ' / ' . ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-') . ' / ' . ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
                break;
            case DocoConstants::TYPE_RI:
            case 'ranapLaporanTerapi':
                $url = [
                    'datatable' => Url::to(
                        ['/ranap/pemeriksaan-rawat-inap/get-list-tindakan', 'id' => $pendaftaran_id, 'pasien_id'=> $pasien_id, 'pasienadmisi_id' => $pasienadmisi_id,  'type' => $type,]
                    )
                ];
                $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
                $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
                if ($isTitipan) {
                    $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
                }
                $title = $title . ' - '. ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-') . ' / ' . ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-') . ' / ' . $kelasTagihan;
                break;
            case DocoConstants::TYPE_IGD:
            case 'igdLaporanTerapi':
                $url = [
                    'datatable' => Url::to(['/igd/riwayat-pasien/get-list-tindakan', 'id' => Yii::$app->request->get('id'), 'pasien_id'=> $pasien_id,'type' => $type])
                ];

                $dataPasien = $this->guzzleExec($this->restGeneral, [
                    'url' => 'riwayat-pasien/patient-global',
                    'method' => 'GET',
                    'payload' => [
                        'query' => [
                            'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                        ]
                    ]
                ]);

                $title = $title . ' - '. ArrayHelper::getValue($dataPasien, 'data.nama_pasien', '-') . ' / ' . ArrayHelper::getValue($dataPasien, 'data.penjamin_nama', '-') . ' / ' . ArrayHelper::getValue($dataPasien, 'data.kelaspelayanan_nama', '-');
                break;
            case DocoConstants::TYPE_IGD:
                    $url = [
                        'datatable' => Url::to(['/igd/riwayat-pasien/get-list-tindakan', 'id' => Yii::$app->request->get('id'), 'type' => $type])
                    ];

                    $dataPasien = $this->guzzleExec($this->restGeneral, [
                        'url' => 'riwayat-pasien/patient-global',
                        'method' => 'GET',
                        'payload' => [
                            'query' => [
                                'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                            ]
                        ]
                    ]);
    
                    $title = $title . ' - '. ArrayHelper::getValue($dataPasien, 'data.nama_pasien', '-') . ' / ' . ArrayHelper::getValue($dataPasien, 'data.penjamin_nama', '-') . ' / ' . ArrayHelper::getValue($dataPasien, 'data.kelaspelayanan_nama', '-');
                break;
            default:
                $url = [
                    'datatable' => Url::to(
                        ['/rajal/pemeriksaan/get-list-tindakan', 'id' => Yii::$app->request->get('id'), 'type' => $type]
                    )
                ];
                break;
        }
        return $this->renderAjax('//cppt/laporan_tindakan', compact('title', 'url'));
    }

    public function actionGetListTindakan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $type = Yii::$app->request->get('type', DocoConstants::TYPE_RJ);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $pasien_id = '';
        $urlDelete = '';
        $instalasiPenunjangArr = [DocoConstants::INSTALASI_ID_RAD, DocoConstants::INSTALASI_ID_LAB, DocoConstants::INSTALASI_ID_BEDAH];
        switch (strtolower($type)) {
            case DocoConstants::TYPE_RJ:
            case 'rajallaporanterapi':
                $url = 'cppt/get-list-tindakan';
                $pasien_id = $this->_data_pasien['pasien_id'];
                $urlDelete = '/rajal/pemeriksaan/batal-instruksi-form';
                break;
            case DocoConstants::TYPE_RI:
            case 'ranaplaporanterapi':
                $url = 'pemeriksaan-rawat-inap/fetch-list-tindakan';
                $urlDelete = '/ranap/pemeriksaan-rawat-inap/batal-instruksi-form';
                $pasien_id = Yii::$app->request->get('pasien_id', null) ? $this->helper->decrypt(Yii::$app->request->get('pasien_id', null)) : null;
                break;
            case DocoConstants::TYPE_IGD:
            case 'igdlaporanterapi':
                $url = 'riwayat-pasien/fetch-list-tindakan';
                $urlDelete = '/igd/riwayat-pasien/batal-instruksi-form';
                $pasien_id = Yii::$app->request->get('pasien_id', null) ? $this->helper->decrypt(Yii::$app->request->get('pasien_id', null)) : null;
                break;
            case DocoConstants::TYPE_RI:
                    $url = 'pemeriksaan-rawat-inap/fetch-list-tindakan';
                    $urlDelete = '/ranap/pemeriksaan-rawat-inap/batal-instruksi-form';
                break;
            case DocoConstants::TYPE_IGD:
                    $url = 'riwayat-pasien/fetch-list-tindakan';
                    $urlDelete = '/igd/riwayat-pasien/batal-instruksi-form';
                break;
            default:
                $url = 'cppt/get-list-tindakan';
                $pasien_id = $this->_data_pasien['pasien_id'];
                break;
        }
        $queryParams = array_merge($payload, [
            'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id')),
            'pasien_id' => $pasien_id,
            'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
            'start' => Yii::$app->request->get('start', 0),
            'length' => Yii::$app->request->get('length', 10),
            'type' => $type,
            'instruksi' => Yii::$app->request->get('instruksi'),
            'jenis' => Yii::$app->request->get('jenis'),
            'startDate' => Yii::$app->request->get('startDate'),
            'endDate' => Yii::$app->request->get('endDate'),
        ]);
        $getData = $this->helper->guzzleExec($this->_restDefault, [
            'url' => $url,
            'payload' => [
                'query' => $queryParams
            ]
        ]);
        $statusImplementasiFarmasi = isset($getData['status_implementasi_farmasi']) ? $getData['status_implementasi_farmasi'] : DocoConstants::STATUS_RESEPTUR_BELUM_DIPROSES;
        foreach ($getData['data'] as $key => $value) {
            $getData['data'][$key]['instruksi'] = str_replace('Cyto', 'CITO', $value['instruksi']);
            $buttonAction = [];
            $is_resep_lunas = strtolower($type) == DocoConstants::RJ_LAP_TERAPI ? $value['status_bayar'] != DocoConstants::STAT_BAYAR_LUNAS ? false : true : $value['is_bayar'];

            /**
             * Penyesuaian show alasan, tanggal & pegawai ketika order bedah ditolak
             * 541 = Ditolak
             */

            if (!$value['deleted'] && (int) $value['status_implementasi'] != 541) {
                if (!$value['is_bayar']) {
                    if (strtolower($value['grouping_tipe']) == 'tindakanbmhp' && !$value['is_pulang']
                         && $value['status_bmhp_id'] == DocoConstants::BMHP_BELUM_VERIFIKASI) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
                                    'instruksitindakan_id' => $value['instruksitindakan_id'],
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'group' => strtolower($value['grouping_tipe'])
                                ]),
                                'data-instruksitindakan_id' => $value['instruksitindakan_id']
                            ]
                        ];
                    } else if (Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RJ
                              && strtolower($value['grouping_tipe_key']) == 'tindakanbmhp' && !$value['is_pulang']
                              && $value['status_bmhp_id'] == DocoConstants::BMHP_BELUM_VERIFIKASI) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'obatalkespasien_id' => $value['obatalkespasien_id'],
                                    'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                                    'group' => strtolower($value['grouping_tipe'])
                                ])
                            ]
                        ];
                    } else if (Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RJ && strtolower($value['grouping_tipe_key']) == 'tindakanbmhp' && !$value['is_pulang'] && empty($value['status_bmhp_id'])) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'obatalkespasien_id' => $value['obatalkespasien_id'],
                                    'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                                    'group' => strtolower($value['grouping_tipe'])
                                ])
                            ]
                        ];
                    
                    } else if (Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RI && strtolower($value['grouping_tipe']) == 'tindakanbmhp' && !$value['is_pulang'] && empty($value['status_bmhp_id'])) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'instruksitindakan_id' => $value['instruksitindakan_id'],
                                    'instruksi_id' => $value['instruksi_id'],
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'group' => strtolower($value['grouping_tipe']),
                                    'instalasi_id' => $value['instalasi_penunjang_id']
                                ])
                            ]
                        ];
                    } else if (Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RD && strtolower($value['grouping_tipe']) == 'tindakanbmhp' && !$value['is_pulang'] && empty($value['status_bmhp_id'])) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'instruksitindakan_id' => $value['instruksitindakan_id'],
                                    'instruksi_id' => $value['instruksi_id'],
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'group' => strtolower($value['grouping_tipe']),
                                    'instalasi_id' => $value['instalasi_penunjang_id']
                                ])
                            ]
                        ];
                    }

                    // Batal Penunjang
                    // aksi hanya dimunculkan jika tindakan terkait belum dibayar dan status tindakan tersebut tidak termasuk dalam list status yg dijadikan kondisi
                    /**
                     * Reseptur
                     * 347 = Sudah Diproses
                     *
                     * Implementasi
                     * 455 = Sudah Implementasi
                     *
                     * Penunjang
                     * 477 = Sudah disetujui/belum periksa penunjang
                     * 471 = Sudah disetujui Bedah
                     * 472 = Batal
                     * 473 = Periksa
                     * 475 = Selesai
                     * 476 = Batal
                     * 482 = Sedang operasi
                     * 483 = Sudah operasi
                     * 488 = Belum operasi
                     * 692 = Reschedule
                     */

                    if (in_array($value['instalasi_penunjang_id'], $instalasiPenunjangArr) && !in_array((int) $value['status_implementasi'], [347, 455, 477, 471, 472, 473, 475, 476, 482, 483, 488, 692]) ||  in_array($value['instalasi_penunjang_id'], $instalasiPenunjangArr) && $value['is_telah_implementasi']) {
                        // pengecekan kondisi untuk menyesuaikan parameter yg dikirim disesuaikan dengan instalasi di pendaftaran

                        if ((Yii::$app->docoVars->workspace('instalasi_id') ==  DocoConstants::INSTALASI_ID_RJ && !$value['is_pulang'])) {
                            // Baru untuk bedah
                            if ((ArrayHelper::getValue($value, 'grouping_tipe_key') == 'penunjang' && !ArrayHelper::getValue($value, 'is_telah_implementasi', false) && ArrayHelper::getValue($value, 'status_implementasi') != DocoConstants::LAB_ST_PEN_BELUMPERIKSA) || ArrayHelper::getValue($value, 'grouping_tipe_key') != 'penunjang') {
                                $buttonAction = [
                                    'title' => '<i class="fa fa-trash"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'href' => Url::to([
                                            $urlDelete,
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'permintaankepenunjang_id' => $value['permintaankepenunjang_id'],
                                            'jenis' => strtolower($value['jenis']),
                                            'type' => $type,
                                            'group' => strtolower($value['grouping_tipe']),
                                            'instalasi_id' => $value['instalasi_penunjang_id']
                                        ]),
                                    ]
                                ];
                            }
                        } else if ((Yii::$app->docoVars->workspace('instalasi_id') ==  DocoConstants::INSTALASI_ID_RI && !$value['is_pulang'])) {
                            if (count(array_intersect([ArrayHelper::getValue($value, 'instalasi_penunjang_id')], DocoConstants::INSTALASI_ID_PENUNJANG)) && !ArrayHelper::getValue($value, 'is_telah_implementasi', false) && ArrayHelper::getValue($value, 'status_implementasi') != DocoConstants::LAB_ST_PEN_BELUMPERIKSA) {
                                // Baru untuk bedah
                                $buttonAction = [
                                    'title' => '<i class="fa fa-trash"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'href' => Url::to([
                                            $urlDelete,
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'instruksitindakan_id' => $value['instruksitindakan_id'],
                                            'jenis' => strtolower($value['jenis']),
                                            'type' => $type,
                                            'group' => strtolower($value['grouping_tipe']),
                                            'instalasi_id' => $value['instalasi_penunjang_id']
                                        ]),
                                    ]
                                ];
                            }
                        } else if (Yii::$app->docoVars->workspace('instalasi_id') ==  DocoConstants::INSTALASI_ID_RD && !$value['is_pulang']) {
                            if (count(array_intersect([ArrayHelper::getValue($value, 'instalasi_penunjang_id')], DocoConstants::INSTALASI_ID_PENUNJANG)) && !ArrayHelper::getValue($value, 'is_telah_implementasi', false) && ArrayHelper::getValue($value, 'status_implementasi') != DocoConstants::LAB_ST_PEN_BELUMPERIKSA) {
                                $buttonAction = [
                                    'title' => '<i class="fa fa-trash"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'href' => Url::to([
                                            $urlDelete,
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'instruksitindakan_id' => $value['instruksitindakan_id'],
                                            'jenis' => strtolower($value['jenis']),
                                            'type' => $type,
                                            'group' => strtolower($value['grouping_tipe']),
                                            'instalasi_id' => $value['instalasi_penunjang_id']
                                        ]),
                                    ]
                                ];
                            }
                        }
                    }

                    if (strtolower($value['grouping_tipe'])  == 'reseptur') {
                        if($value['status_implementasi'] != DocoConstants::STATUS_RESEPTUR_DISERAHKAN && $value['status_implementasi'] != DocoConstants::STATUS_RESEPTUR_BATAL &&   !$value['is_pulang'] && !$is_resep_lunas) {
                            $buttonAction = [
                                'title' => '<i class="fa fa-trash"></i>',
                                'attr' => [
                                    'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-batal-instruksi',
                                    'href' => Url::to([
                                        $urlDelete,
                                        'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                        'noresep' => $value['noresep'],
                                        'type' => $type,
                                        'group' => strtolower($value['grouping_tipe']),
                                        'instalasi_id' => $value['instalasi_penunjang_id']
                                    ]),
                                ]
                            ];
                        } else if ($value['status_implementasi'] == DocoConstants::STATUS_RESEPTUR_BATAL) {
                            if (strtolower($type) == DocoConstants::TYPE_RI || strtolower($type) == DocoConstants::RI_LAP_TERAPI) {
                                $buttonAction = [
                                    'title' => '<i class="fa fa-eye"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-info btn-sm btn-view-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'data-width' => '50%',
                                        'href' => Url::to([
                                            '/ranap/pemeriksaan-rawat-inap/view-pembatalan-instruksi',
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'noresep' => $value['noresep'],
                                            'group' => $value['grouping_tipe'],
                                        ]),
                                    ]
                                ];
                            } else if (strtolower($type) == DocoConstants::TYPE_IGD | strtolower($type) == DocoConstants::RD_LAP_TERAPI) {
                                $buttonAction = [
                                    'title' => '<i class="fa fa-eye"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-info btn-sm btn-view-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'data-width' => '50%',
                                        'href' => Url::to([
                                            '/igd/riwayat-pasien/view-pembatalan-instruksi',
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'noresep' => $value['noresep'],
                                            'group' => $value['grouping_tipe'],
                                        ]),
                                    ]
                                ];
                            } else if (Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RJ  || strtolower($type) == DocoConstants::RJ_LAP_TERAPI) {
                                $buttonAction = [
                                    'title' => '<i class="fa fa-eye"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-info btn-sm btn-view-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'data-width' => '50%',
                                        'href' => Url::to([
                                            '/rajal/pemeriksaan/view-pembatalan-instruksi',
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'group' => $value['grouping_tipe'],
                                            'noresep' => $value['noresep'],
                                        ]),
                                    ]
                                ];
                            }
                        }
                    }
                }
            } else {
                if ((strtolower($type) == DocoConstants::TYPE_RI || strtolower($type) == DocoConstants::RI_LAP_TERAPI) && in_array(strtolower($value['grouping_tipe']), ['tindakanbmhp', 'penunjang', 'reseptur'])) {
                    $buttonAction = [
                        'title' => '<i class="fa fa-eye"></i>',
                        'attr' => [
                            'class' => 'btn btn-info btn-sm btn-view-instruksi',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-batal-instruksi',
                            'data-width' => '50%',
                            'href' => Url::to([
                                '/ranap/pemeriksaan-rawat-inap/view-pembatalan-instruksi',
                                'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                'instruksitindakan_id' => $value['instruksitindakan_id'],
                                'group' => $value['grouping_tipe'],
                                'noresep' => $value['noresep']
                            ]),
                        ]
                    ];
                } else if ((strtolower($type) == DocoConstants::TYPE_IGD || strtolower($type) == DocoConstants::RD_LAP_TERAPI) && in_array(strtolower($value['grouping_tipe']), ['tindakanbmhp', 'penunjang', 'reseptur'])) {
                    $buttonAction = [
                        'title' => '<i class="fa fa-eye"></i>',
                        'attr' => [
                            'class' => 'btn btn-info btn-sm btn-view-instruksi',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-batal-instruksi',
                            'data-width' => '50%',
                            'href' => Url::to([
                                '/igd/riwayat-pasien/view-pembatalan-instruksi',
                                'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                'instruksitindakan_id' => $value['instruksitindakan_id'],
                                'group' => $value['grouping_tipe'],
                                'noresep' => $value['noresep']
                            ]),
                        ]
                    ];
                } else if ((Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RJ || strtolower($type) == DocoConstants::RJ_LAP_TERAPI) && in_array(strtolower($value['grouping_tipe_key']), ['tindakanbmhp', 'penunjang','reseptur'])) {
                    $buttonAction = [
                        'title' => '<i class="fa fa-eye"></i>',
                        'attr' => [
                            'class' => 'btn btn-info btn-sm btn-view-instruksi',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-batal-instruksi',
                            'data-width' => '50%',
                            'href' => Url::to([
                                '/rajal/pemeriksaan/view-pembatalan-instruksi',
                                'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                                'obatalkespasien_id' => $value['obatalkespasien_id'],
                                'permintaankepenunjang_id' => $value['permintaankepenunjang_id'],
                                'group' => $value['grouping_tipe'],
                                'noresep' => $value['noresep'],
                            ]),
                        ]
                    ];
                }
            }
            $getData['data'][$key]['aksi'] = !empty($buttonAction) ? Html::button($buttonAction['title'], $buttonAction['attr']) : '';
        }
        return [
            'draw' => Yii::$app->request->get('draw'),
            'data' => $getData['data'],
            'load_more' => $getData['load_more']
        ];
    }

    public function actionBatalInstruksiForm()
    {
        $model = new BatalInstruksiForm;
        $title = 'Formulir Batal Tindakan Bmhp';
        $request = Yii::$app->request;
        $submitUrl = '';
        $apiBatal = '';

        if ($request->isPost) {
            $type = ArrayHelper::getValue($request->post(), 'BatalInstruksiForm.type');
            $jenis = ArrayHelper::getValue($request->post(), 'BatalInstruksiForm.jenis');
            $group = ArrayHelper::getValue($request->post(), 'BatalInstruksiForm.tipe_instruksi');
        } else {
            $type = $request->get('type', null);
            $jenis = $request->get('jenis', null);
            $group = $request->get('group', null);
        }

        switch (strtolower($type)) {
            case DocoConstants::TYPE_RI:
            case 'ranaplaporanterapi':
                if ($group == 'penunjang') {
                    $model->scenario = BatalInstruksiForm::SCENARIO_PENUNJANG;
                    $apiBatal = 'pemeriksaan-rawat-inap/batal-penunjang';
                } else if($group == 'reseptur'){
                    $model->scenario = BatalInstruksiForm::SCENARIO_RESEPTUR;
                    $apiBatal = 'pemeriksaan-rawat-inap/batal-instruksi';
                } else {
                    $model->scenario = BatalInstruksiForm::SCENARIO_INSTRUKSI;
                    $apiBatal = 'pemeriksaan-rawat-inap/batal-instruksi';
                }
                $submitUrl = '/ranap/pemeriksaan-rawat-inap/batal-instruksi-form';
                break;
            case DocoConstants::TYPE_IGD:
            case 'igdlaporanterapi':
                if ($group == 'penunjang') {
                    $model->scenario = BatalInstruksiForm::SCENARIO_PENUNJANG;
                    $apiBatal = 'riwayat-pasien/batal-penunjang';
                } else if($group == 'reseptur'){
                    $model->scenario = BatalInstruksiForm::SCENARIO_RESEPTUR;
                    $apiBatal = 'riwayat-pasien/batal-instruksi';
                } else {
                    $model->scenario = BatalInstruksiForm::SCENARIO_INSTRUKSI;
                    $apiBatal = 'riwayat-pasien/batal-instruksi';
                }
                $submitUrl = '/igd/riwayat-pasien/batal-instruksi-form';
                break;
            case DocoConstants::TYPE_RJ:
            case 'rajallaporanterapi':
                $submitUrl = '/rajal/pemeriksaan/batal-instruksi-form';
                $apiBatal = 'tindakan-bmhp/hapus-tindakan-bmhp';
                if ($group == 'penunjang') {
                    $model->scenario = BatalInstruksiForm::SCENARIO_PENUNJANGRJ;
                    $apiBatal = 'tindakan-bmhp/batal-penunjang';
                } else if ($jenis == 'bmhp') {
                    $model->scenario = BatalInstruksiForm::SCENARIO_BMHPRJ;
                } else if($group == 'reseptur'){
                    $model->scenario = BatalInstruksiForm::SCENARIO_RESEPTUR;
                    $apiBatal = 'tindakan-bmhp/batal-instruksi';
                } else {
                    $model->scenario = BatalInstruksiForm::SCENARIO_TINDAKANRJ;
                }
                break;
            default:
                $submitUrl = 'pemeriksaan/batal-instruksi-form';
                break;
        }


        if ($model->load(Yii::$app->request->post(),'BatalInstruksiForm')) {
            if (!$model->validate()) {
                $response = $model->errors;
                $errors = $this->helper->parseError($response, 'BatalInstruksiForm');
                return $this->helper->responseTemplate(422, 'Error', $errors);
            }

            try {
                
                
                $response = $this->_restDefault->post($apiBatal, [
                    'query' => [
                        'id' => Yii::$app->request->get('id', null),
                        'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
                        'pendaftaran_id' => $model->pendaftaran_id
                    ],
                    'form_params' => $model->attributes,
                ]);
                $response = json_decode($response->getBody(), true);
                return $this->helper->response($response);
            } catch (RequestException $e) {
                $error = json_decode($e->getResponse()->getBody(), true);
                return DocoHelpers::response($error, 422);
            } catch (\Exception $e) {
                return DocoHelpers::response(['message' => $e->getMessage()], 500);
            }
        } else {
            if ($group == 'penunjang') $title = 'Formulir Batal Penunjang';
            if ($group == 'reseptur') $title = 'Formulir Batal Resep';
            $model->pendaftaran_id = $this->helper->decrypt($request->get('id', null));
            $model->pasienadmisi_id = $request->get('pasienadmisi_id', null);
            $model->instruksitindakan_id = $request->get('instruksitindakan_id', null);
            $model->instruksi_id = $request->get('instruksi_id', null);
            $model->obatalkespasien_id = $request->get('obatalkespasien_id', null);
            $model->permintaankepenunjang_id = $request->get('permintaankepenunjang_id', null);
            $model->tindakanpelayanan_id = $request->get('tindakanpelayanan_id', null);
            $model->instalasi_id = $request->get('instalasi_id', null);
            $model->noresep = $request->get('noresep', null);
            $model->type = $type;
            $model->jenis = $jenis;
            $model->tipe_instruksi = $group;
            return $this->renderAjax('//cppt/batal_tindakan_form', [
                'model' => $model,
                'title' => $title,
                'submitUrl' => $submitUrl
            ]);
        }
    }

    public function actionViewPembatalanInstruksi()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pendaftaran_id = $this->helper->decrypt($pendaftaran_id);
        switch (Yii::$app->docoVars->workspace('instalasi_id')) {
            case DocoConstants::INSTALASI_ID_RI:
                $url = 'pemeriksaan-rawat-inap/history-pembatalan';
                $query = [
                    'instruksitindakan_id' => $request->get('instruksitindakan_id', null),
                    'noresep' => $request->get('noresep', null),
                    'pendaftaran_id' => $pendaftaran_id,
                ];
                break;
            case DocoConstants::INSTALASI_ID_RD:
                $url = 'riwayat-pasien/history-pembatalan';
                $query = [
                    'instruksitindakan_id' => $request->get('instruksitindakan_id', null),
                    'noresep' => $request->get('noresep', null),
                    'pendaftaran_id' => $pendaftaran_id
                ];
                break;
            case DocoConstants::INSTALASI_ID_RJ:
                $url = 'tindakan-bmhp/history-pembatalan';
                $query = [
                    'pendaftaran_id' => $pendaftaran_id,
                    'tindakanpelayanan_id' => $request->get('tindakanpelayanan_id', null),
                    'obatalkespasien_id' => $request->get('obatalkespasien_id', null),
                    'noresep' => $request->get('noresep', null),
                    'permintaankepenunjang_id' => $request->get('permintaankepenunjang_id', null)
                ];
                break;
            default:
                $url = 'pemeriksaan/history-pembatalan';
                break;
        }
        $data = $this->helper->guzzleExec($this->_restDefault, [
            'url' => $url,
            'payload' => [
                'query' => $query
            ],
        ]);
        $title =  isset($data['tipe_instruksi']) && $data['tipe_instruksi'] == 'BMHP' ? 'Detail Pembatalan Tindakan & Bmhp' : 'Detail Pembatalan ' . $request->get('group', null);
        $qty_total = isset($data['qty']) ? $data['qty'] : 0;
        $qty_sisa = isset($data['qty_sisa']) ? $data['qty_sisa'] : 0;
        $qty = $qty_total - $qty_sisa;
        $is_bmhp = isset($data['tipe_instruksi']) && $data['tipe_instruksi'] == 'BMHP' ? true : false;
        $size = $is_bmhp ? 3 : 4;


        return $this->renderAjax('//cppt/view_tindakan_form', [
            'data' => $data,
            'title' => $title,
            'qty' => $qty,
            'is_bmhp' => $is_bmhp,
            'size' => $size,
        ]);
    }
}
