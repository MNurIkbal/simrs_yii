<?php
// author : ardi pratama

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
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;
use app\modules\igd\components\traits\bedah\IntraOperasiTrait;
use app\modules\igd\components\traits\bedah\PostOperasiTrait;
use app\modules\igd\components\traits\bedah\ApiTrait;
use app\modules\igd\components\traits\bedah\ViewInpostTrait;

use app\components\Traits\HistoryPatientTrait;
use app\components\Traits\LaporanTindakanTrait;
use app\components\Traits\Pelayanan\TerraMedikTrait;

class RiwayatPasienController extends DocoController
{
    use IntraOperasiTrait;
    use PostOperasiTrait;
    use ApiTrait;
    use ViewInpostTrait;
    use HistoryPatientTrait;
    use LaporanTindakanTrait;
    use TerraMedikTrait;

    protected $_restIgd;
    protected $_restBedah;
    protected $_restRajal;
    protected $_restDefault;
    protected $_restRm;
    protected $allowAction = ['*'];
    protected $_title = "Informasi pasien operasi";
    protected $_module = 'igd/inf-pasien-operasi/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;
        $this->_restIgd = Yii::$app->docoRest->igd;
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->restGeneral = $this->_restRajal;
        $this->_restDefault = $this->_restIgd;
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->type = 'RD';
    }

    public function actionIndex($norm)
    {
        try {
            $title        = Yii::t('fe', 'Riwayat Pasien');
            $instalasi_id = DocoHelpers::decrypt(Yii::$app->request->get('instalasi', null));
            $modal        = Yii::$app->request->get('modal', null);
            $norm         = Yii::$app->request->get('norm', '');
            $aksesRiwayatPasien = DHtml::cekHakAkses('data-history-patient');
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
            if ($modal == 'is_modal') {
                return $this->renderAjax('_modal_riwayat_pasien', get_defined_vars());
            }
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionGetDataRiwayatPasien()
    {
        try {
            $request = Yii::$app->request;
            $norm = $request->get('norm');
            $is_modal = $request->get('is_modal', null);
            $draw = $request->get('draw', 1);
            $result = $data = [];
            $result['data'] = [];
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

            $getDataFilterRiwayatPasien = $this->getDataFilterRiwayatPasien($norm);

            $response = $this->_restIgd->request('get', 'riwayat-pasien/index', [
                'query' => [
                    'norm' => $norm,
                    'filter' => $filter
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $rowNum = 1;
            $arr_temp = [];
            foreach ($body['response']['data'] as $key => $value) {
                if (isset($getDataFilterRiwayatPasien['list_pendaftaran'])) {
                    if (in_array($value['pendaftaran_id'], $getDataFilterRiwayatPasien['list_pendaftaran'], true)) {
                        if (isset($getDataFilterRiwayatPasien['list_cara_keluar'])) {
                            if (!in_array($value['carakeluar_id'], $getDataFilterRiwayatPasien['list_cara_keluar'])) {
                                unset($body['response']['data'][$key]);
                            }
                        }
                    }
                }
            }

            foreach ($body['response']['data'] as $key => $v) {
                $cara_keluar = '';
                if (!empty($v['tglpasienpulang'])) {
                    $cara_keluar .= date('d M Y H:i:s', strtotime($v['tglpasienpulang'])) . "<br>";
                    $cara_keluar .= $v['carakeluar_nama'] . "<br>";
                    if (isset($v['kondisikeluar_nama'])) {
                        $cara_keluar .= ' - ' . $v['kondisikeluar_nama'] . "<br>";
                    }
                }
                $v['rowNum'] = $rowNum;
                $data[] = [
                    'pendaftaran_id' => $v['pendaftaran_id'],
                    'tgl_pendaftaran' => date('d M Y', strtotime($v['tgl_pendaftaran'])) . ' / ' . $v['no_pendaftaran'],
                    'ruangan_pend' => $v['ruangan_pend'],
                    'dok_rjrd' => $v['dok_rjrd'],
                    'cara_keluar' => $cara_keluar,
                    'aksi_pelayanan' => $this->riwayatPelayanan($norm, $v, $is_modal),
                    'aksi_penunjang' => $this->riwayatPenunjang($norm, $v),
                ];
                $rowNum++;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['recordsTotal'];
            $result['recordsFiltered'] = $body['response']['recordsFiltered'];

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    protected function getDataFilterRiwayatPasien($norm)
    {
        try {
            $resListCK = $this->_restIgd->get('allow/get-list-cara-keluar', ['form_params' => []]);
            $bodyresListCK = json_decode($resListCK->getBody(), true)['response'];
            $listCaraKeluar = [];

            foreach ($bodyresListCK as $key => $value) {
                $listCaraKeluar[] = $value['carakeluar_id'];
            }

            $resRPbyPendaftaran = $this->_restIgd->get('riwayat-pasien/index?norm=' . $norm . '&pendaftaran_id=pendaftaran_id', ['form_params' => []]);
            $bodyRPbyPendaftaran = json_decode($resRPbyPendaftaran->getBody(), true)['response']['data'];
            $arrPendaftaran = [];

            foreach ($bodyRPbyPendaftaran as $k => $v) {
                $arrPendaftaran[] = $v['pendaftaran_id'];
            }

            $result = [
                'list_cara_keluar' => $listCaraKeluar,
                'list_pendaftaran' => $arrPendaftaran
            ];
        } catch (Exception $e) {
            $result = [
                'list_cara_keluar' => [],
                'list_pendaftaran' => []
            ];
        }
        return $result;
    }

    protected function riwayatPelayanan($norm, $data, $is_modal)
    {
        $btn_pelayanan = '';


        if ($data['instalasi_pend_id'] == 1 && ($data['r_anamesa'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Asesmen Keperawatan',
                    [
                        '/rajal/pemeriksaan/cetak-anamnesa?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
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
        if ($data['instalasi_pend_id'] == 1 && ($data['r_pemeriksaanfisik'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Asesmen Medis',
                    [
                        '/igd/riwayat-pasien/cetak-pemeriksaan-fisik?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
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
                        'data-url'    => '/igd/riwayat-pasien/cetak-pemeriksaan-fisik?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && ($data['r_soaprj'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - CPPT',
                    [
                        // DocoHelpers::crossUrl('opento', [
                        //     'ruangan_id' => $data['ruangan_pend_id'],
                        //     'instalasi_id' => $data['instalasi_pend_id'],
                        //     'modul' => 'rajal',
                        //     'url' => '/rajal/pemeriksaan/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                        //     ])
                        '/rajal/pemeriksaan/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ruangan_id=' . $data['ruangan_pend_id']
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
                        'data-url'    => '/rajal/pemeriksaan/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 7 && ($data['r_soaprj'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - CPPT',
                    [
                        // DocoHelpers::crossUrl('opento', [
                        //     'ruangan_id' => $data['ruangan_pend_id'],
                        //     'instalasi_id' => $data['instalasi_pend_id'],
                        //     'modul' => 'rajal',
                        //     'url' => '/rajal/pemeriksaan/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                        //     ])
                        '/rajal/pemeriksaan/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_rj_id']) . '&ruangan_id=' . $data['ruangan_pend_id']
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
                        'data-url'    => '/rajal/pemeriksaan/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_rj_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && ($data['r_diagnosa'] == 1) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Diagnosa',
                    [
                        // DocoHelpers::crossUrl('opento', [
                        //     'ruangan_id' => $data['ruangan_pend_id'],
                        //     'instalasi_id' => $data['instalasi_pend_id'],
                        //     'modul' => 'rajal',
                        //     'url' => '/rajal/pemeriksaan/export-pdf-diagnosa?pendaftaran_id='.DocoHelpers::encrypt($data['pendaftaran_id'])
                        //     ])
                        '/rajal/pemeriksaan/export-pdf-diagnosa?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ruangan_id=' . $data['ruangan_pend_id']
                    ],
                    [
                        'id' => 'btn-cetak-rajal-diagnosa-' . @$data['rowNum'],
                        'class' => 'btn btn-info btn-sm btn-riwayat',
                        'target' => '_blank'
                    ]
                );
            } else {
                $btn_pelayanan .= Html::button(
                    'RJ - Diagnosa',
                    [
                        'id'          => 'btn-cetak-rajal-diagnosa-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/rajal/pemeriksaan/export-pdf-diagnosa?pendaftaran_id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
                    ]
                );
            }
        }
        if ($data['instalasi_pend_id'] == 1 && (($data['r_tindakan'] == 1) || ($data['r_bmhp'] == 1)) && ($data['pasienadmisi_id'] == '')) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RJ - Tindakan & BMHP',
                    [
                        '/rajal/pemeriksaan/cetak-tindakan?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ruangan_id=' . $data['ruangan_pend_id'],
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
                        'data-url'    => '/rajal/pemeriksaan/cetak-tindakan?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&ruangan_id=' . $data['ruangan_pend_id'],
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
        // if ($data['instalasi_pend_id'] == 1 && ($data['r_konsulpoli'] == 1) && ($data['pasienadmisi_id'] == '')) {
        //     $btn_pelayanan .= Html::a('RJ - Konsul Poli',
        //         [
        //             'index?norm='.$norm
        //         ], [
        //             'id' => 'btn-cetak-rajal-konsul-poli-'.@$data['rowNum'],
        //             'class'=>'btn btn-info btn-sm btn-riwayat',
        //             'target' => '_blank'
        //         ]
        //     );
        // }
        if ($data['instalasi_pend_id'] == 2 && $data['r_asesmendokter'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RD - Asesmen Medis',
                    [
                        '/igd/pemeriksaan-igd/cetak-pdf-asesmen-dokter?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
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
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-pdf-asesmen-dokter?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
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
                        'id'          => 'btn-cetak-igd-asesmen-dpjp-' . @$data['rowNum'],
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/igd/pemeriksaan-igd/cetak-list-dpjp?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
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
        if ($data['instalasi_pend_id'] == 2 && $data['r_reseptur'] == 1) {
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
        if (!empty($data['pasienadmisi_id']) && $data['r_asesmenawal'] == 1) {
            if ($is_modal == null) {
                $btn_pelayanan .= Html::a(
                    'RI - Asesmen Keperawatan',
                    [
                        '/ranap/pemeriksaan-rawat-inap/cetak-pdf-asesmen-awal?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm
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
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-pdf-asesmen-awal?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
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
                        'class'       => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                        'data-target' => '#modal-preview',
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-list-cppt?id=' . DocoHelpers::encrypt($data['pendaftaran_id']) . '&norm=' . $norm,
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
            // $btn_pelayanan .= Html::button('RI - Reseptur',
            //     [
            //         'id' => 'btn-cetak-ranap-reseptur-'.@$data['rowNum'],
            //         'class'=>'btn btn-info btn-sm btn-riwayat btn-reseptur',
            //         'data-toggle'=>'modal',
            //         'data-target' => '#modal_riwayat',
            //         'action' => '/igd/riwayat-pasien/list-reseptur?id='.DocoHelpers::encrypt($data['pendaftaran_id']).'&ins='.$data['instalasi_pend_id']
            //     ]
            // );
        }
        if (!empty($data['pasienadmisi_id']) && $data['r_instruktitindakanbmhp'] == 1) {
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
                        '/ranap/pemeriksaan-rawat-inap/cetak-pdf-resume-medis?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
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
                        'data-url'    => '/ranap/pemeriksaan-rawat-inap/cetak-pdf-resume-medis?id=' . DocoHelpers::encrypt($data['pendaftaran_id']),
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
        if (!empty($data['pasienadmisi_id']) && $data['r_asuhangizi'] == 1) {
            $btn_pelayanan .= Html::button(
                'RI - Asuhan Gizi',
                [
                    'id' => 'btn-cetak-ranap-asuhangizi-' . @$data['rowNum'],
                    'class' => 'btn btn-info btn-sm btn-riwayat btn-asuhan-gizi',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_riwayat',
                    'action' => '/igd/riwayat-pasien/list-asuhan-gizi?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
                ]
            );
        }

        return $btn_pelayanan;
    }

    protected function riwayatPenunjang($norm, $data)
    {
        $btn_penunjang = '';
        if ($data['p_laboratorium'] == 1) {
            $btn_penunjang .= Html::button(
                'Laboratorium',
                [
                    'id' => 'btn-cetak-penunjang-lab-' . @$data['rowNum'],
                    'class' => 'btn btn-info btn-sm btn-riwayat btn-penunjang',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_riwayat',
                    'action' => '/igd/riwayat-pasien/list-penunjang-laboratorium?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
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
                    'data-width' => '90%',
                    'action' => '/igd/riwayat-pasien/list-penunjang-radiologi?id=' . DocoHelpers::encrypt($data['pendaftaran_id'])
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
        return $btn_penunjang;
    }

    public function actionListReseptur($id, $ins = null)
    {
        $pendaftaran_id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restIgd->request('get', 'riwayat-pasien/list-reseptur', [
                'query' => ['pendaftaran_id' => $pendaftaran_id],
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $data_reseptur = $body['response']['data_reseptur'];
            $infoPasien = $body['response']['data_pasien'];

            $arr_map_reseptur = [];
            foreach ($data_reseptur as $val_data_reseptur) {
                $val_data_reseptur['pendaftaran_id'] = $id;
                $arr_map_reseptur[$val_data_reseptur['reseptur_id']][] = $val_data_reseptur;
            }
            foreach ($arr_map_reseptur as $key => $value) {
                if($key){
                    foreach ($value as $detailKey => $data) {
                        if((!empty($data['reseptur_id']) && is_null($data['penjualanresep_id'])) && $data['status_reseptur_id'] == 660){
                            unset($arr_map_reseptur[$key][$detailKey]);
                        }
                    }
                }
            }
            $listReseptur = $arr_map_reseptur;
        } catch (RequestException $e) {
            $infoPasien = null;
            $listReseptur = [];
        } catch (\Exception $e) {
            $infoPasien = null;
            $listReseptur = [];
        }

        return $this->renderAjax('_modal_reseptur', ['infoPasien' => $infoPasien, 'listReseptur' => $listReseptur, 'pendaftaran_id' => $pendaftaran_id, 'ins' => $ins]);
    }

    public function actionCetakResep($no_resep)
    {
        $path = Yii::getAlias("@download") . "/cetak-reseptur.pdf";

        try {
            $request = $this->_restIgd->get('riwayat-pasien/cetak-reseptur-satuan', [
                'query' => ['no_resep' => $no_resep],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionListPenunjangLaboratorium($id)
    {
        $pendaftaran_id = DocoHelpers::decrypt($id);
        try {
            $infoPasien = null;
            $listLab = [];
            $response = $this->_restIgd->request('get', 'riwayat-pasien/list-penunjang-laboratorium', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
                ],
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $data_laboratorium = $body['response']['data_laboratorium'];
            $infoPasien = $body['response']['data_pasien'];

            // if (!empty($listLab)) {
            //     foreach ($listLab as $key => $value) {
            //         $value['status_periksa'] = DocoConstants::$status_lab[$value['status_periksa']];
            //         $color = '#FFFfff';
            //         $font = 'black';

            //         if (strtolower($value['status_periksa']) == 'batal') {
            //             $color = '#D24D57';
            //             $font = 'white';
            //         } else if (strtolower($value['status_periksa']) == 'selesai') {
            //             $color = '#26A65B';
            //             $font = 'white';
            //         } else if(strtolower($value['status_periksa']) == 'periksa') {
            //             $color = '#2574A9';
            //             $font = 'white';
            //         } else if(strtolower($value['status_periksa']) == 'ambil sampel') {
            //             $color = '#F5D76E';
            //             $font = 'white';
            //         }
            //         $$listLab[$key]['status_periksa'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'">'.$value['status_periksa'].'</span>';

            //         $$listLab[$key]['tglmasukpenunjang'] = DocoHelpers::convDateTime($value['tglmasukpenunjang'],true,false);
            //         $$listLab[$key]['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])),false,false);
            //     }
            // }

            $arr_map_lab = [];
            foreach ($data_laboratorium as $val_data_lab) {
                $arr_map_lab[$val_data_lab['no_masukpenunjang']][] = $val_data_lab;
            }
            $listLab = $arr_map_lab;
        } catch (RequestException $e) {
            $infoPasien = null;
            $listLab = [];
        } catch (\Exception $e) {
            $infoPasien = null;
            $listLab = [];
        }

        return $this->renderAjax('_modal_laboratorium', ['infoPasien' => $infoPasien, 'listLab' => $listLab, 'pendaftaran_id' => $pendaftaran_id]);
    }

    public function actionCetakHasilLaboratorium($no_rujukan)
    {
        $path = Yii::getAlias("@download") . "/cetak-hasil-lab.pdf";

        try {
            $request = $this->_restIgd->get('riwayat-pasien/cetak-hasil-laboratorium', [
                'query' => ['no_rujukan' => $no_rujukan],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionListPenunjangRadiologi($id, $norm = null)
    {
        $pendaftaran_id = DocoHelpers::decrypt($id);
        try {
            $infoPasien = null;
            $user = Yii::$app->session->get('user_identity');
            $listRad = [];
            $response = $this->_restIgd->get('riwayat-pasien/list-penunjang-radiologi', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
                    'no_rekam_medik' => $norm,
                    'type' => Yii::$app->request->get('type', 'hasil'),
                    'instalasi_id' => Yii::$app->request->get('instalasi_id', null)
                ],
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $data_radiologi = $body['response']['data_radiologi'];
            $infoPasien = $body['response']['data_pasien'];
            $infoPasien['id_pegawai'] = $user['id_pegawai'];
            $arr_map_rad = [];
            foreach ($data_radiologi as $val_data_rad) {
                $arr_map_rad[$val_data_rad['no_rujukan']][] = $val_data_rad;
            }
            $res_map_rad = [];
            foreach ($arr_map_rad as $key => $value) {
                $strIds = '';
                foreach ($value as $k => $v) {
                    $strIds .= DocoHelpers::encrypt($v['hasilpemeriksaanrad_id']) . '-' . DocoHelpers::encrypt($v['pasienmasukpenunjang_id']) . '_';
                }
                $arr_v = [];
                foreach ($value as $x => $z) {
                    $pemeriksaan = "<li>";
                    $pemeriksaan .= isset($z['daftartindakan_nama']) ? str_replace(",", "</li><li>", $z['daftartindakan_nama']) : "-";
                    $pemeriksaan .= "</li>";
                    $z['daftartindakan_nama'] = $pemeriksaan;
                    if(isset($z['status_batal'])){
                        if ($z['status_batal']) {
                            $z['stat_periksa'] = 'Batal';
                        } else if (!empty($z['is_hasil'])) {
                            $z['hide'] = false;
                            if (!empty($z['tgl_verifikasi'])) {
                                $z['disabled'] = false;
                                $z['stat_periksa'] = 'Selesai';
                            } else {
                                $z['stat_periksa'] = 'Periksa';
                            }
                        } else {
                            $z['stat_periksa'] = 'Belum Diperiksa';
                        }
                    }else {
                        $z['stat_periksa'] = ' - ';
                    }

                    $z['penunjang_pemeriksaanrad'] = $strIds;
                    $arr_v[] = $z;
                }
                $res_map_rad[$key] = $arr_v;
            }

            $listRad = $res_map_rad;
            $konfig_disable_button = ArrayHelper::getValue($body, 'response.konfig_disable_button');
        } catch (RequestException $e) {
            $infoPasien = null;
            $this->logError($e);
            $listRad = [];
        } catch (\Exception $e) {
            $infoPasien = null;
            $this->logError($e);
            $listRad = [];
        }

        if ($norm != null) {
            return $this->renderAjax('_modal_radiologi_rm', ['infoPasien' => $infoPasien, 'listRad' => $listRad, 'pendaftaran_id' => $pendaftaran_id, 'konfig_disable_button' => $konfig_disable_button]);
        } else {
            return $this->renderAjax('_modal_radiologi', ['infoPasien' => $infoPasien, 'listRad' => $listRad, 'pendaftaran_id' => $pendaftaran_id, 'konfig_disable_button' => $konfig_disable_button]);
        }
    }

    public function actionReadCetakanRadiologi()
    {
        $post = Yii::$app->request->post();
        return $this->guzzleExec($this->_restIgd, [
            'method' => 'POST',
            'url' => 'riwayat-pasien/read-cetakan-radiologi',
            'payload' => [
                'query' => compact('post')
            ],
            'returnResponse' => true
        ]);
        // $asdasd = $asdasda;
    }

    public function actionListPenunjangBedah($id)
    {
        $pendaftaran_id = DocoHelpers::decrypt($id);
        try {
            $infoPasien = null;
            $listBedah = [];
            $response = $this->_restIgd->request('get', 'riwayat-pasien/list-penunjang-bedah', [
                'query' => ['pendaftaran_id' => $pendaftaran_id],
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $data_bedah = $body['response']['data_bedah'];
            $infoPasien = $body['response']['data_pasien'];

            $arr_map_bedah = [];
            foreach ($data_bedah as $val_data_bedah) {
                $arr_map_bedah[$val_data_bedah['no_rujukan']][] = $val_data_bedah;
            }
            $listBedah = $arr_map_bedah;

            // echo "<pre>";var_dump($listBedah);die();
        } catch (RequestException $e) {
            $infoPasien = null;
            $listBedah = [];
        } catch (\Exception $e) {
            $infoPasien = null;
            $listBedah = [];
        }

        return $this->renderAjax('_modal_bedah', ['infoPasien' => $infoPasien, 'listBedah' => $listBedah, 'pendaftaran_id' => $pendaftaran_id]);
    }

    public function actionCetakHasilRadiologi($no_rujukan)
    {
        $path = Yii::getAlias("@download") . "/cetak-hasil-rad.pdf";
        $aaa = $aaa;
        try {
            $request = $this->_restIgd->get('riwayat-pasien/cetak-hasil-radiologi', [
                'query' => ['no_rujukan' => $no_rujukan],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakHasilBedah($no_rujukan)
    {
        $path = Yii::getAlias("@download") . "/cetak-hasil-operasi.pdf";

        try {
            $request = $this->_restIgd->get('riwayat-pasien/cetak-hasil-bedah', [
                'query' => ['no_rujukan' => $no_rujukan],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @todo Method untuk mendapatkan data pindah kamar
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakRiwayatPindahKamar($id, $no_rekam_medik)
    {
        try {
            $user = Yii::$app->session->get('user_identity');
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/cetak-riwayat-pindah-kamar.pdf";

            $restIgd = $this->_restIgd->request('get', 'riwayat-pasien/cetak-riwayat-pindah-kamar', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'nama_pegawai' => $user['nama_pegawai'],
                    'no_rekam_medik' => $no_rekam_medik,
                ],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $data_pasien = null;
            $pindah_kamar = [];
        } catch (\Exception $e) {
            $data_pasien = null;
            $pindah_kamar = [];
        }
    }

    /**
     * @todo Method untuk mendapatkan data tindakan bmhp
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakRiwayatTindakanBmhp($id, $no_pendaftaran)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/cetak-riwayat-tindakan-bmhp.pdf";

            $restIgd = $this->_restIgd->request('get', 'riwayat-pasien/cetak-riwayat-tindakan-bmhp', [
                'query' => [
                    'id' => $pendaftaran_id,
                    'no_pendaftaran' => $no_pendaftaran,
                ],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $data_pasien = null;
            $data_tindakan_bmhp = [];
        } catch (\Exception $e) {
            $data_pasien = null;
            $data_tindakan_bmhp = [];
        }
    }

    /**
     * @todo Method untuk menampilkan halaman review reseptur
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionReviewReseptur($id, $ins = null)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            // echo "<pre>";var_dump($pendaftaran_id);die();
            $restIgd = $this->_restIgd->request('get', 'riwayat-pasien/list-reseptur', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ],
                'form_params' => []
            ]);
            $response = json_decode($restIgd->getBody(), true);
            $data_reseptur = $response['response']['data_reseptur'];
            $data_pasien = $response['response']['data_pasien'];
            $arr_map_reseptur = [];
            foreach ($data_reseptur as $value) {
                $value['pendaftaran_id'] = $id;
                $value['status_reseptur'] = '-';
                $value['instruksi_id'] = '-';
                $arr_map_reseptur[$value['noresep']] = $value;
                $arr_map_reseptur[$value['noresep']]['tglreseptur'] = DocoHelpers::convDateTime($value['tglreseptur'], false, false);
            }
            $data_reseptur = $arr_map_reseptur;

            return $this->render('form_review_pasien', [
                'data_pasien' => $data_pasien,
                'data_reseptur' => $data_reseptur,
                'pendaftaran_id' => $pendaftaran_id,
                'instalasi_id' => $ins,
            ]);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Method untuk cetak visite dokter
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakVisiteDokter($id, $norm)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/cetak-riwayat-visite-dokter.pdf";

            $restIgd = $this->_restIgd->request('get', 'riwayat-pasien/cetak-riwayat-visite-dokter', [
                'query' => [
                    'id' => $pendaftaran_id,
                    'norm' => $norm,
                    'ruangan_id' => Yii::$app->docoVars->workspace('ruangan_id'),
                ],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $data_pasien = null;
            $visite_dokter = [];
        } catch (\Exception $e) {
            $data_pasien = null;
            $visite_dokter = [];
        }
    }

    /**
     * @todo Action untuk menampilkan halaman riwayat permintaan konsul
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionRiwayatPermintaanKonsul($id, $norm)
    {
        try {
            return $this->render('list_riwayat_permintaan_konsul', [
                'id' => $id,
                'norm' => $norm,
            ]);
        } catch (RequestException $e) {
            $data_pasien = null;
            $permintaan_konsul = [];
        } catch (\Exception $e) {
            $data_pasien = null;
            $permintaan_konsul = [];
        }
    }

    /**
     * @todo Action untuk mendapatkan data riwayat permintaan konsul
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataRiwayatPermintaanKonsul($norm)
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $yiiRestfulParams['advanced-filter']['no_rekam_medik'] = $norm;

            $result = [];
            $result['draw'] = $draw;
            $result['data'] = $data;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            $restIgd = $this->_restIgd->get('riwayat-pasien/get-riwayat-permintaan-konsul?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restIgd->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['permintaankonsul_id']);
                    $value['check'] = null;
                    $value['primary'] = $primaryKey;
                    $value['no'] = $no;
                    $value['waktu_permintaan'] = DocoHelpers::convDateTime($value['waktu_permintaan'], false, true);
                    $value['no_rekam_medik'] = $value['no_rekam_medik'] . ' / ' . $value['no_pendaftaran'];
                    $value['cara_bayar'] = $value['carabayar_nama'] . ' / ' . $value['penjamin_nama'];
                    $value['hak_kelas'] = $value['kls_hak'] . ' / ' . $value['kls_rawat'];
                    $value['nama_ruangan'] = $value['ruangan_nama'] . ' / ' . $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];

                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            }

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Action untuk menampilkan popup detail riwayat permintaan konsul
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionDetail()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $id = DocoHelpers::decrypt($get['id']);

            $restIgd = $this->_restIgd->get('riwayat-pasien/get-riwayat-permintaan-konsul?id=' . $id, ['form_params' => []]);
            $body = json_decode($restIgd->getBody(), true);
            $getDataPermintaan = $body['response'];
            $permintaankonsul_id = $get['id'];

            return $this->renderPartial('_modal_detail_permintaan_konsul', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @todo Action untuk cetak riwayat permintaan konsul
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakRiwayatPermintaanKonsul($id, $norm)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/cetak-riwayat-permintaan-konsul.pdf";

            $restIgd = $this->_restIgd->request('get', 'riwayat-pasien/cetak-riwayat-permintaan-konsul', [
                'query' => [
                    'id' => $pendaftaran_id,
                    'norm' => $norm,
                    'ruangan_id' => Yii::$app->docoVars->workspace('ruangan_id'),
                ],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $data_pasien = null;
            $permintaan_konsul = [];
        } catch (\Exception $e) {
            $data_pasien = null;
            $permintaan_konsul = [];
        }
    }

    /**
     * @todo Action untuk menampilkan list asuhan gizi pada modal
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionListAsuhanGizi($id)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $data_pasien = null;

            $restIgd = $this->_restIgd->get('riwayat-pasien/get-riwayat-asuhan-gizi?id=' . $pendaftaran_id);
            $body = json_decode($restIgd->getBody(), true)['response'];
            $data_pasien = $body['data_pasien'];
            $data_gizi = $body['data_gizi'];

            if (isset($data_pasien['tanggal_lahir']) && $data_pasien['tanggal_lahir'] != '') {
                $data_pasien['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($data_pasien['tanggal_lahir'])), false, false);
            }

            if (!empty($data_gizi)) {
                foreach ($data_gizi as $key => $value) {
                    $data_gizi[$key]['created_date'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['created_date'])), false, true);
                    $data_gizi[$key]['aksi'] = Html::a(
                        'Detail',
                        [
                            '/gizi/asesmen-gizi/cetak-asuhan-gizi?id=' . DocoHelpers::encrypt($value['pendaftaran_id']) . '&asuhangizi_id=' . DocoHelpers::encrypt($value['asuhangizi_id'])
                        ],
                        [
                            'id' => 'btn-cetak-ranap-asuhangizi-' . $key,
                            'class' => 'btn btn-info btn-sm btn-riwayat btn-asuhan-gizi',
                            'target' => '_blank'
                        ]
                    );
                }
            }

            return $this->renderAjax('_modal_asuhan_gizi', [
                'pendaftaran_id' => $pendaftaran_id,
                'data_pasien' => $data_pasien,
                'data_gizi' => $data_gizi,
            ]);
        } catch (RequestException $e) {
            $data_pasien = null;
            $data_gizi = [];
        } catch (\Exception $e) {
            $data_pasien = null;
            $data_gizi = [];
        }
    }

    public function actionDetailPasienBedah($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $status = DocoHelpers::decrypt($request->get('periksa'));
        $title = Yii::t('fe', 'Detail Pasien Operasi');
        $data = [];
        $posisi = '';
        if ($status == 488) {
            $response = $this->_restBedah->get('inf-pasien-operasi/mulai-operasi', ['form_params' => ['pasienmasukpenunjang_id' => $id]]);
        } else {
            $response = $this->_restBedah->get('inf-pasien-operasi/view', ['query' => ['id' => $id]]);
        }
        try {
            $body = json_decode($response->getBody(), TRUE);
            $data = $body['response']['data'];
            $posisi = $body['response']['posisi'];
        } catch (Exception $e) {
            $data = [];
        }
        $display = 'style="display:block;"';
        if ($request->get('pasien') !== NULL) {
            $display = 'style="display:none";';
        }

        return $this->render('_pasien/detail', get_defined_vars());
    }

    /**
     * @todo Method untuk cetak pemeriksaan fisik
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakPemeriksaanFisik($id)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/cetak-riwayat-pemeriksaan-fisik.pdf";

            $restIgd = $this->_restIgd->request('get', 'riwayat-pasien/cetak-pemeriksaan-fisik', [
                'query' => [
                    'id' => $pendaftaran_id,
                ],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $data_pasien = null;
        } catch (\Exception $e) {
            $data_pasien = null;
        }
    }

    /**
     * @todo Action untuk menampilkan popup list reseptur
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionListResepturRajal()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $id = DocoHelpers::decrypt($get['id']);
            $ruangan_id = $get['ruangan_id'];
            $instalasi_id = $get['instalasi_id'];

            $restIgd = $this->_restIgd->get('riwayat-pasien/list-reseptur-rajal?id=' . $id);
            $body = json_decode($restIgd->getBody(), true);
            $data_pasien = $body['response']['data_pasien'];
            $data_reseptur = $body['response']['data_reseptur'];

            return $this->renderPartial('_modal_list_reseptur', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }


    /* 
        * @todo Action untuk menampilkan popup Laporan Terapi
    */
    // public function actionListLaporanTerapi()
    // {
    //     $title = 'Laporan Terapi';
    //     $type = Yii::$app->request->get('type', 'igd');

    //     switch ($type) {
    //         case 'igd':
    //                 $url = [
    //                     'datatable' => Url::to(['/igd/riwayat-pasien/get-list-tindakan', 'id' => Yii::$app->request->get('id'), 'type' => $type]
    //                     )
    //                 ];
    //             break;
    //         default:
    //                 $url = [
    //                     'datatable' => Url::to(['/igd/riwayat-pasien/get-list-tindakan', 'id' => Yii::$app->request->get('id'), 'type' => $type])
    //                 ];
    //             break;
    //     }
    //     return $this->renderAjax('//cppt/laporan_tindakan', compact('title', 'url'));
    // }

    /* 
        * @todo Action untuk get Data Laporan Terapi
    */
    // public function actionGetListTindakan()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;$type = Yii::$app->request->get('type', 'igd');

    //     switch ($type) {
    //         case 'igd':
    //                 $url = 'riwayat-pasien/fetch-list-tindakan';
    //             break;
    //         default:
    //                 $url = 'riwayat-pasien/fetch-list-tindakan';
    //             break;
    //     }

    //     $getData = $this->helper->guzzleExec($this->_restIgd, [
    //         'url' => $url,
    //         'payload' => [
    //             'query' => [
    //                 'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id')),
    //                 'start' => Yii::$app->request->get('start', 0),
    //                 'length' => Yii::$app->request->get('length', 10)
    //             ]
    //         ]
    //     ]);

    //     foreach ($getData['data'] as $key => $value) {
    //         $getData['data'][$key]['instruksi'] = str_replace('Cyto', 'CITO', $value['instruksi']);
    //     }

    //     return [
    //         'draw' => Yii::$app->request->get('draw'),
    //         'data' => $getData['data'],
    //         'recordsTotal' => $getData['totalCount'],
    //         'recordsFiltered' => $getData['totalCount'],
    //     ];
    // }
}
