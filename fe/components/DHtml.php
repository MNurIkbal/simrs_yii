<?php

namespace app\components;

use Yii;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoConstants;

class DHtml extends Html
{

    public static function getTitleMenu($default = null)
    {
        if (!empty($default)) return $default;
        $controller = Yii::$app->controller;
        $modul = '/' . $controller->module->id . '/' . $controller->id;
        $mappMenu = Yii::$app->session->get('url-to-menu');
        return isset($mappMenu[$modul]) ? $mappMenu[$modul] : $default;
    }

    protected static function setHakAkses()
    {
        $controller = Yii::$app->controller;
        $modul = '/' . $controller->module->id . '/' . $controller->id;
        $akses = Yii::$app->session->get('akses_menu');
        return isset($akses[$modul]) ? $akses[$modul] : [];
    }

    /**
     * @inheritdoc
     */
    public static function a($text, $url = null, $options = [])
    {
        $hakAkses = self::setHakAkses();
        $akses = @$options['akses'];
        if (in_array($akses, $hakAkses)) {
            return parent::a($text, $url, $options);
        }
    }

    /**
     * @inheritdoc
     */
    public static function submitButton($content = 'Submit', $options = [])
    {
        $hakAkses = self::setHakAkses();
        $akses = @$options['akses'];
        if (in_array($akses, $hakAkses)) {
            return parent::submitButton($content, $options);
        }
    }

    /**
     * @inheritdoc
     */
    public static function button($content = 'Button', $options = [])
    {

        $hakAkses = self::setHakAkses();
        $akses = @$options['akses'];
        if (in_array($akses, $hakAkses)) {
            return parent::button($content, $options);
        }
    }

    public static function cekHakAkses($akses)
    {
        $hakAkses = self::setHakAkses();
        if (in_array($akses, $hakAkses)) {
            return true;
        }

        return false;
    }

    public static function hasAkses($module, $akses)
    {
        $allAkses = Yii::$app->session->get('akses_menu');
        return isset($allAkses[$module]) ? in_array($akses, $allAkses[$module]) : false;
    }

    public static function trueFalseRadio($model, $fieldName, $option = [])
    {
        $responseHtml = '';
        $colSize = isset($option['colSize']) ? $option['colSize'] : 1;
        foreach (['0' => 'Tidak', '1' => 'Ya'] as $keyOption => $optionValue) :
            $optionHtml = [
                'value' => $keyOption,
                'id' => $fieldName . '-' . $keyOption,
                'data-fieldname' => $fieldName,
                'class' => isset($option['class']) ? $option['class'] : '',
                'label' => '<span class="">' . $optionValue . '</span>',
                'inline' => true
            ];
            if (isset($option['childDependent'])) {
                $optionHtml = array_merge($optionHtml, [
                    'data-dependent' => json_encode($option['childDependent']),
                    // 'class' => $optionHtml['class'] . ' custom-dependent-input'
                ]);
            }
            $responseHtml .= '<div class="col-sm-' . $colSize . '">' .
                Html::activeRadio($model, $fieldName, $optionHtml) .
                '</div>';
        endforeach;
        return $responseHtml;
    }

    private static function choiceForm($arrayConfigHtml, $type = 'checkbox')
    {
        $otherFieldName = '';
        $colSize = '1';
        $data = $model = $fieldName = [];
        if (isset($arrayConfigHtml['model'])) {
            $model = $arrayConfigHtml['model'];
        }
        if (isset($arrayConfigHtml['fieldName'])) {
            $fieldName = $arrayConfigHtml['fieldName'];
        }
        if (isset($arrayConfigHtml['data'])) {
            $data = $arrayConfigHtml['data'];
        }
        if (isset($arrayConfigHtml['otherFieldName'])) {
            $otherFieldName = $arrayConfigHtml['otherFieldName'];
        } else {
            $otherFieldName = $fieldName;
        }
        if (isset($arrayConfigHtml['colSize'])) {
            $colSize = $arrayConfigHtml['colSize'];
        }
        if (isset($arrayConfigHtml['class'])) {
            $className = $arrayConfigHtml['class'];
        } else {
            $className = [];
        }
        if (isset($arrayConfigHtml['childDependent'])) {
            $dataDependent = json_encode($arrayConfigHtml['childDependent']);
        }
        if (isset($arrayConfigHtml['otherColSize'])) {
            $otherColSize = $arrayConfigHtml['otherColSize'];
        } else {
            $otherColSize = ($colSize != '1') ? '6' : '4';
        }
        if (isset($arrayConfigHtml['withoutOtherField'])) {
            $withoutOtherField = $arrayConfigHtml['withoutOtherField'];
        } else {
            $withoutOtherField = false;
        }
        $responseHtml = '';
        foreach ($data as $keyOption => $optionValue) :
            $optionInput = [
                'value' => $keyOption,
                'id' => $fieldName . '-' . $keyOption,
                'data-fieldname' => str_replace("[]", "", $fieldName),
                'label' => '<span class="">' . $optionValue . '</span>',
                'inline' => true,
            ];
            if($type == 'checkbox') {
                $optionInput['checked'] = !empty($arrayConfigHtml['selected']) ? in_array($keyOption, $arrayConfigHtml['selected']) : false;
            }
            if (!empty($className)) {
                $optionInput['class'] = $className;
            }
            if (isset($dataDependent)) {
                $optionInput['data-dependent'] = $dataDependent;
            }
            if ($keyOption == '00' && !$withoutOtherField) {
                $responseHtml .= '<div class="col-sm-' . $otherColSize . '">' .
                    '<div class="row">' .
                    '<div class="col-sm-4">' .
                    (($type == 'checkbox') ? Html::activeCheckbox($model, $fieldName, $optionInput) : Html::activeRadio($model, $fieldName, $optionInput)) .
                    '</div>' .
                    '<div class="col-sm-8">' .
                    Html::activeTextInput($model, $otherFieldName, [
                        'class' => 'form-control input-tag',
                        'id' => 'other-' . $optionInput['data-fieldname'],
                        'disabled' => true
                    ]) .
                    '</div>' .
                    '</div>' .
                    '</div>';
            } else {
                $responseHtml .= '<div class="col-sm-' . $colSize . '">' .
                    (($type == 'checkbox') ? Html::activeCheckbox($model, $fieldName, $optionInput) : Html::activeRadio($model, $fieldName, $optionInput)) .
                    '</div>';
            }
        endforeach;
        return $responseHtml;
    }

    public static function multipleCheckbox($arrayConfigHtml = [])
    {
        return self::choiceForm($arrayConfigHtml);
    }

    public static function multipleRadio($arrayConfigHtml = [])
    {
        return self::choiceForm($arrayConfigHtml, 'radio');
    }

    public static function dontKnowRadio($model, $fieldName, $option = [])
    {
        $responseHtml = '';
        $colSize = isset($option['childDependent']['colSize']) ? $option['childDependent']['colSize'] : 1;
        foreach (['2' => 'Tidak Tahu', '0' => 'Tidak', '1' => 'Ya'] as $keyOption => $optionValue) :
            $optionHtml = [
                'value' => $keyOption,
                'id' => $fieldName . '-' . $keyOption,
                'data-fieldname' => $fieldName,
                'class' => isset($option['class']) ? $option['class'] : '',
                'label' => '<span class="">' . $optionValue . '</span>',
                'inline' => true
            ];
            if (isset($option['childDependent'])) {
                $optionHtml = array_merge($optionHtml, [
                    'data-dependent' => json_encode($option['childDependent']),
                    // 'class' => $optionHtml['class'] . ' custom-dependent-input'
                ]);
            }
            $responseHtml .= '<div class="col-sm-' . $colSize . '">' .
                Html::activeRadio($model, $fieldName, $optionHtml) .
                '</div>';
        endforeach;
        return $responseHtml;
    }

    /**
     * Return button worklist
     * 
     * @param String type
     * @return String/Html
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function worklistPatientBtn($record,$batalStopAkomodasi = true)
    {

        if (Yii::$app->docoVars->workspace('instalasi_id') == 10) {
            return '';
        }
        $printSection = self::btnGroupByArray([], 'print');
        $checkSection = '<button disabled type="button" data-toggle="tooltip" title="Periksa" class="btn btn-xs btn-only btn-transparent"><i class="fa fa-heartbeat"></i></button>';
        $otherSection = self::btnGroupByArray([]);
        switch (strtolower($record['jenis'])) {
            case 'rj':
                $antrian = '<button data-toggle="tooltip" data-original-title="'.$record['no_antrian']  .'" type="button" class="btn btn-only btn-transparent btn-xs data-filter antrian" '. $record['is_today'] .' data-antrianId="'. $record['antrian_id'] .'" data-antrian="'. $record['no_antrian'] .'" data-id="'. $record['pendaftaran_id'] .'" data-pegawaiid="'. $record['pegawai_id'] .'" data-namapasien="'. $record['nama_pasien'] .'" data-ruanganid="'. $record['ruangan_id'] .'"><i class="fa fa-volume-up"></i></button>';
                $printSection = self::btnGroupByArray([
                    // [
                    //     'url' => DocoHelpers::crossUrl('opento', [
                    //         'ruangan_id' => $record['ruangan_id'],
                    //         'instalasi_id' => $record['ruangan_id'],
                    //         'modul' => 'rajal',
                    //         'url' => 'rajal/worklist/show-popup?id=' . $record['pendaftaran_id'] . '&instalasi_id=' . $record['instalasi_id'] . '&modul=rajal'
                    //     ]),
                    //     'title' => 'Cetak Rincian',
                    //     'type' => 'modal',
                    //     'disabled' => (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS)?'disabled':'',
                    // ]
                ], 'file-pdf-o');
                if ($record['status_periksa'] == 1 || $record['status_periksa'] == 430) {
                    $periksaUrl = DocoHelpers::crossUrl('opento', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'rajal',
                        'url' => 'rajal/informasi/confirm-periksa?pendaftaran_id=' . $record['primary'] . ($record['origin_jenis'] == 'MCU' ? '&ruangan_id=' . $record['ruangan_id'] : '') . '&konsulpoli_id=' . DocoHelpers::encrypt($record['konsulpoli_id']). '&is_jenis=' . $record['origin_jenis']
                    ]);
                    $checkSection = '<div><button type="button" action="' . $periksaUrl . '" data-toggle="tooltip" data-target="#modal_backdrop" data-btntrigger="modal" title="Periksa" class="btn btn-modal-trigger btn-xs btn-only btn-transparent"><i class="fa fa-heartbeat"></i></button></div>';
                } else if (in_array($record['status_periksa'], [628, DocoConstants::STATUS_PERIKSA_BTL_PERIKSA])) {
                    $periksaUrl = DocoHelpers::crossUrl('opento', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'rajal',
                        'url' => 'rajal/informasi/confirm-periksa?pendaftaran_id=' . $record['primary'] . ($record['origin_jenis'] == 'MCU' ? '&ruangan_id=' . $record['ruangan_id'] : '') . '&konsulpoli_id=' . DocoHelpers::encrypt($record['konsulpoli_id']). '&is_jenis=' . $record['origin_jenis']
                    ]);
                    $checkSection = '<div><button type="button" action="' . $periksaUrl . '" data-toggle="tooltip" data-target="#modal_backdrop" data-btntrigger="modal" title="Periksa" class="btn btn-modal-trigger btn-xs btn-only btn-transparent" disabled><i class="fa fa-heartbeat"></i></button></div>';
                } else {
                    $periksaUrl = DocoHelpers::crossUrl('jumpto', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'rajal',
                        'url' => 'rajal/pemeriksaan/periksa?id=' . $record['primary'] . '&ruanganId=' . DocoHelpers::encrypt($record['ruangan_id']) . '&konsulpoliId=' . DocoHelpers::encrypt($record['konsulpoli_id']). '&is_jenis=' . $record['origin_jenis']
                    ]);
                    $checkSection = '<div><a type="button" href="' . $periksaUrl . '" data-toggle="tooltip" title="Periksa" class="btn btn-xs btn-only btn-transparent"><i class="fa fa-heartbeat"></i></a></div>';
                }
                $otherSection = self::btnGroupByArray([
                    [
                        'url' => DocoHelpers::crossUrl('opento', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'rajal',
                            'url' => '/rajal/informasi/batal-periksa?pendaftaran_id=' . $record['primary']
                        ]),
                        'title' => 'Batal Periksa',
                        'type' => 'modal',
                        'disabled' => in_array($record['status_periksa'], [DocoConstants::STATUS_PERIKSA_ANTR_POLI, DocoConstants::STATUS_PERIKSA_DIPERIKSA]) ? false : true,
                    ],
                    [
                        'url' => !empty($record['keterangan_pendaftaran']) ? $record['keterangan_pendaftaran'] : '#',
                        'title' => 'Telekonsul',
                        'disabled' => empty($record['keterangan_pendaftaran']) || $record['status_periksa'] == DocoConstants::STATUS_BATAL_PERIKSA,
                        'type' => 'newWindow'
                    ],
                    [
                        'url' => DocoHelpers::crossUrl('opento', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'rajal',
                            'url' => '/rajal/informasi/skrining-pasien?pendaftaran_id=' . $record['primary'] . '&is_riwayat=false'
                        ]),
                        'title' => 'Skrining Pasien',
                        'type' => 'modal'
                    ],
                    [
                        'url' => '/pendaftaran/rencana-kontrol-inap/print-rencana?rencanakontrol_id=' . (!isset($record['rencanakontrol_id']) ? '' : DocoHelpers::encrypt($record['rencanakontrol_id'])),
                        'title' => 'Surat Rencana Kontrol',
                        'disabled' => !isset($record['rencanakontrol_id']) && empty($record['rencanakontrol_id']),
                        'type' => 'newPage'
                    ],
                ], 'bars');
                break;
            case 'ri':
                $antrian = '<button data-toggle="tooltip" data-original-title="'.$record['no_antrian']  .'" type="button" class="btn btn-only btn-transparent btn-xs data-filter antrian" '. $record['is_today'] .' data-antrianId="'. $record['antrian_id'] .'" data-antrian="'. $record['no_antrian'] .'" data-id="'. $record['pendaftaran_id'] .'" data-pegawaiid="'. $record['pegawai_id'] .'" data-namapasien="'. $record['nama_pasien'] .'" data-ruanganid="'. $record['ruangan_id'] .'"><i class="fa fa-volume-up"></i></button>';
                $printSection = self::btnGroupByArray([
                    // [
                    //     'url' => DocoHelpers::crossUrl('opento', [
                    //         'ruangan_id' => $record['ruangan_id'],
                    //         'instalasi_id' => $record['ruangan_id'],
                    //         'modul' => 'ranap',
                    //         'url' => 'ranap/worklist/cetak-rincian?id=' . $record['pendaftaran_id'] . '&instalasi_id=' . $record['instalasi_id']
                    //     ]),
                    //     'title' => 'Cetak Rincian',
                    //     'type' => 'newPage',
                    //     'disabled' => (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS)?'disabled':'',
                    // ],
                    // [
                    //     'url' => DocoHelpers::crossUrl('opento', [
                    //         'ruangan_id' => $record['ruangan_id'],
                    //         'instalasi_id' => $record['ruangan_id'],
                    //         'modul' => 'ranap',
                    //         'url' => 'ranap/worklist/show-popup?id=' . $record['pendaftaran_id'] . '&instalasi_id=' . $record['instalasi_id'] . '&modul=ranap'
                    //     ]),
                    //     'title' => 'Cetak Detail Rincian',
                    //     'type' => 'modal',
                    //     'disabled' => (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS)?'disabled':'',
                    // ],
                    [
                        'url' => DocoHelpers::crossUrl('opento', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'ranap',
                            'url' => 'ranap/inf-pasien-ranap/export-surat-keterangan-kelahiran?pendaftaran_id=' . $record['pendaftaran_id'],
                        ]),
                        'title' => 'Cetak Surat Kelahiran',
                        'type' => 'newPage',
                        'disabled' => !$record['is_bayi']
                    ],
                    [
                        'url' => DocoHelpers::crossUrl('opento', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'ranap',
                            'url' => 'ranap/inf-pasien-ranap/export-surat-r2bbl?pendaftaran_id=' . $record['pendaftaran_id'],
                        ]),
                        'title' => 'Cetak Surat R2BBL',
                        'type' => 'newPage',
                        'disabled' => !$record['is_bayi']
                    ],
                ], 'file-pdf-o');
                $periksaUrl = DocoHelpers::crossUrl('jumpto', [
                    'ruangan_id' => $record['ruangan_id'],
                    'instalasi_id' => $record['instalasi_id'],
                    'modul' => 'ranap',
                    'url' => 'ranap/pemeriksaan-rawat-inap/periksa?id=' . $record['primary']
                ]);
                if ($record['status_periksa'] == 453) {
                    $checkSection = '<div><a type="button" href="' . $periksaUrl . '" data-toggle="tooltip" title="Periksa" class="btn btn-xs btn-only btn-transparent" disabled><i class="fa fa-heartbeat"></i></a></div>';
                } else {
                    $checkSection = '<div><a type="button" href="' . $periksaUrl . '" data-toggle="tooltip" title="Periksa" class="btn btn-xs btn-only btn-transparent"><i class="fa fa-heartbeat"></i></a></div>';
                }
                $akomodasiArr = !$record['is_stopakomodasi'] ? [
                    'url' => DocoHelpers::crossUrl('opento', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'ranap',
                        'url' => 'ranap/inf-pasien-ranap/stop-akomodasi?pendaftaran_id=' . $record['pendaftaran_id'],
                    ]),
                    'title' => 'Stop Akomodasi',
                    'tableId' => 'table-patient',
                    'msg' => 'Apakah Anda yakin akan menghentikan akomodasi ini ?',
                    'type' => 'api',
                    'disabled' => $record['is_pasientitipan'] && ($record['is_stopakomodasi'] || !$record['is_stoppasientitipan'])
                ] : [
                    'url' => DocoHelpers::crossUrl('opento', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'ranap',
                        'url' => 'ranap/inf-pasien-ranap/batal-stop-akomodasi?pendaftaran_id=' . $record['pendaftaran_id'],
                    ]),
                    'title' => 'Batal Stop Akomodasi',
                    'tableId' => 'table-patient',
                    // 'msg' => 'Apakah Anda yakin akan batal stop akomodasi ?',
                    // 'type' => 'api',
                    'type' => 'modal',
                //  'disabled' => $record['is_pulang'],
                    'hidden' => $batalStopAkomodasi,
                ];
                $otherSection = self::btnGroupByArray([
                    [
                        'url' => DocoHelpers::crossUrl('jumpto', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'ranap',
                            'url' => 'ranap/inf-pasien-ranap/pasien-pulang?id=' . $record['primary']
                        ]),
                        'title' => 'Pulang',
                        'type' => 'page',
                        'disabled' => !($record['is_stopakomodasi'] && !$record['is_pulang'] && ($record['penjamin_id'] != DocoConstants::VAR_P_Perseorangan || ($record['penjamin_id'] == DocoConstants::VAR_P_Perseorangan && $record['is_lunas'])))
                    ],
                    [
                        'url' => DocoHelpers::crossUrl('opento', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'ranap',
                            'url' => 'ranap/inf-pasien-ranap/stop-pasien-titipan?pendaftaran_id=' . $record['pendaftaran_id'],
                        ]),
                        'title' => 'Stop Pasien Titipan',
                        'tableId' => 'table-patient',
                        'msg' => 'Apakah Anda yakin akan menghentikan kelas titipan pasien ini?',
                        'type' => 'api',
                        'disabled' => !$record['is_pasientitipan'] || $record['is_stoppasientitipan']
                    ],
                    [
                        'url' => DocoHelpers::crossUrl('jumpto', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'ranap',
                            'url' => 'pendaftaran/pindah-kamar?no_pendaftaran='. DocoHelpers::encrypt($record['no_pendaftaran']).'&no_rm=' . DocoHelpers::encrypt($record['no_rekam_medik']),
                        ]),
                        'title' => 'Pindah Kamar',
                        'type' => 'page',
                        'disabled' => ($record['is_stopakomodasi'] || $record['is_pulang'])
                    ],
                    [
                        'url' => DocoHelpers::crossUrl('opento', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'ranap',
                            'url' => '/ranap/inf-pasien-ranap/riwayat-visit-dokter?pendaftaran_id=' . $record['primary'],
                        ]),
                        'title' => 'Riwayat Visit',
                        'modalWidth' => '50%',
                        'type' => 'modal'
                    ],
                    [
                        'url' => '/pendaftaran/rencana-kontrol-inap/print-rencana?rencanakontrol_id=' . (!isset($record['rencanakontrol_id']) ? '' : DocoHelpers::encrypt($record['rencanakontrol_id'])),
                        'title' => 'Surat Rencana Kontrol',
                        'disabled' => !isset($record['rencanakontrol_id']) && empty($record['rencanakontrol_id']),
                        'type' => 'newPage'
                    ],
                    $akomodasiArr
                ], 'bars');
                break;
            case 'rd':
                $antrian = '<button data-toggle="tooltip" data-original-title="'.$record['no_antrian']  .'" type="button" class="btn btn-only btn-transparent btn-xs data-filter antrian" '. $record['is_today'] .' data-antrianId="'. $record['antrian_id'] .'" data-antrian="'. $record['no_antrian'] .'" data-id="'. $record['pendaftaran_id'] .'" data-pegawaiid="'. $record['pegawai_id'] .'" data-namapasien="'. $record['nama_pasien'] .'" data-ruanganid="'. $record['ruangan_id'] .'"><i class="fa fa-volume-up"></i></button>';
                if ($record['status_periksa'] == 1) {
                    $periksaUrl = DocoHelpers::crossUrl('opento', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'igd',
                        'url' => 'igd/inf-pasien-igd/assign-dokter?id=' . $record['primary'] . '&ruangan_id=' . $record['ruangan_id'] . '&instalasi_id=' . $record['instalasi_id'] . '&pegawai_id=' . $record['pegawai_id']. '&crossModule=1'
                    ]);
                    $checkSection = '<div><button type="button" action="' . $periksaUrl . '" data-toggle="tooltip" data-target="#modal_backdrop" data-btntrigger="modal" title="Periksa" class="btn btn-update-dpjp btn-xs btn-only btn-transparent"><i class="fa fa-heartbeat"></i></button></div>';
                } else if (in_array($record['status_periksa'], [628, DocoConstants::STATUS_PERIKSA_BTL_PERIKSA])) {
                    $periksaUrl = DocoHelpers::crossUrl('opento', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'igd',
                        'url' => 'igd/inf-pasien-igd/assign-dokter?id=' . $record['primary'] . '&ruangan_id=' . $record['ruangan_id'] . '&instalasi_id=' . $record['instalasi_id'] . '&crossModule=1'
                    ]);
                    $checkSection = '<div><button type="button" action="' . $periksaUrl . '" data-toggle="tooltip" data-target="#modal_backdrop" data-btntrigger="modal" title="Periksa" class="btn btn-update-dpjp btn-xs btn-only btn-transparent" disabled><i class="fa fa-heartbeat"></i></button></div>';
                } else {
                    $periksaUrl = DocoHelpers::crossUrl('jumpto', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'igd',
                        'url' => 'igd/pemeriksaan-igd/periksa?id=' . $record['primary']
                    ]);
                    $checkSection = '<div><a type="button" href="' . $periksaUrl . '" data-toggle="tooltip" title="Periksa" class="btn btn-xs btn-only btn-transparent"><i class="fa fa-heartbeat"></i></a></div>';
                }
                $printSection = self::btnGroupByArray([
                    // [
                    //     'url' => DocoHelpers::crossUrl('opento', [
                    //         'ruangan_id' => $record['ruangan_id'],
                    //         'instalasi_id' => $record['ruangan_id'],
                    //         'modul' => 'igd',
                    //         'url' => 'igd/worklist/show-popup?id=' . $record['pendaftaran_id'] . '&instalasi_id=' . $record['instalasi_id'] . '&modul=igd'
                    //     ]),
                    //     'title' => 'Cetak Rincian',
                    //     'type' => 'modal',
                    //     'disabled' => (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS)?'disabled':'',
                    // ]
                ], 'file-pdf-o');
                $otherSection = self::btnGroupByArray([
                    [
                        'url' => DocoHelpers::crossUrl('opento', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'igd',
                            'url' => '/igd/inf-pasien-igd/aksi-batal?id=' . $record['primary']
                        ]),
                        'title' => 'Batal Periksa',
                        'type' => 'modal',
                        'disabled' => in_array($record['status_periksa'], [DocoConstants::STATUS_PERIKSA_ANTR_POLI, DocoConstants::STATUS_PERIKSA_DIPERIKSA]) ? false : true,
                    ]
                ], 'bars');
                break;
            case 'ot':
                if ($record['status_periksa'] == 488) {
                    $periksaUrl = DocoHelpers::crossUrl('jumpto', [
                        'ruangan_id' => $record['ruangan_id'],
                        'instalasi_id' => $record['instalasi_id'],
                        'modul' => 'bedah',
                        'url' => '/bedah/informasi-pasien-operasi/detail?id=' . DocoHelpers::encrypt($record['pasienmasukpenunjang_id']) . '&periksa=' . DocoHelpers::encrypt($record['status_periksa']),
                    ]);
                    $checkSection = '<div><a type="button" href="' . $periksaUrl . '" data-toggle="tooltip" title="Periksa" class="btn btn-xs btn-only btn-transparent"><i class="fa fa-heartbeat"></i></a></div>';
                }
                $otherSection = self::btnGroupByArray([
                    [
                        'url' => DocoHelpers::crossUrl('jumpto', [
                            'ruangan_id' => $record['ruangan_id'],
                            'instalasi_id' => $record['instalasi_id'],
                            'modul' => 'bedah',
                            'url' => '/bedah/informasi-pasien-operasi/detail?id=' . DocoHelpers::encrypt($record['pasienmasukpenunjang_id']) . '&periksa=' . DocoHelpers::encrypt($record['status_periksa']),
                        ]),
                        'title' => 'Lihat',
                        'type' => 'page',
                        'disabled' => $record['status_periksa'] != 483
                    ],
                ], 'bars');
                break;
        }
        return $antrian . $checkSection . $otherSection;
    }

    /**
     * This function will return btn group by array
     * 
     * @param Array $arrayOfBtn
     * @param String $iconParent
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function btnGroupByArray($arrayOfBtn, $iconParent = 'ellipsis-v')
    {
        $html = '<div class="btn-group">
            <button ' . (empty($arrayOfBtn) ? 'disabled' : '') . ' type="button" class="btn btn-transparent btn-xs btn-only dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-' . $iconParent . '"></i></button>
            <ul class="dropdown-menu" role="menu">';
        foreach ($arrayOfBtn as $btnOption) {
            $disabled = isset($btnOption['disabled']) ? $btnOption['disabled'] : false;
            $hidden = isset($btnOption['hidden']) ?  $btnOption['hidden'] == false ? '' : 'class="hidden"' : '';
            $html .= '<li ' . ($disabled ? 'class="disabled"' : $hidden) . '><a ';
            if ($disabled) {
                $html .= 'href="javascript:void(0)"';
            } else {
                switch ($btnOption['type']) {
                    case 'modal':
                        $modalWidth = '';
                        if (isset($btnOption['modalWidth'])) {
                            $modalWidth = 'data-width="' . $btnOption['modalWidth'] . '"';
                        }

                        $html .= 'action="' . $btnOption['url'] . '" '. $modalWidth .' data-toggle="tooltip" data-target="#modal_backdrop" data-btntrigger="modal"';
                        break;
                    case 'api':
                        $html .= 'data-type="api-trigger" data-tableid="' . (isset($btnOption['tableId']) ? $btnOption['tableId'] : '') . '" data-msg="' . (isset($btnOption['msg']) ? $btnOption['msg'] : '') . '" data-url="' . $btnOption['url'] . '"';
                        break;
                    case 'page':
                        $html .= 'href="' . $btnOption['url'] . '"';
                        break;
                    case 'newPage':
                        $html .= 'href="' . $btnOption['url'] . '" target="__blank"';
                        break;
                    case 'newWindow':
                        $html .= 'href="javascript:void(0)" data-url="' . $btnOption['url'] . '" class="new-window-btn"';
                        break;
                }
            }
            $html .= '>' . $btnOption['title'] . '</a></li>';
        }
        return $html . '<ul></div>';
    }

    protected static function getProtectedValue($obj, $name = null) {
        $array = (array) $obj;
        $prefix = chr(0).'*'.chr(0);
        $name = $name ? $name : '_title';
        return isset($array[$prefix.$name]) ? $array[$prefix.$name] : null;
    }

    public static function titleMenu($default = null)
    {
        $controller = Yii::$app->controller;
        $_title = self::getProtectedValue($controller, '_title');
        $modul = '/' . $controller->module->id . '/' . $controller->id;
        $mappMenu = Yii::$app->session->get('url-to-menu');
        $titleMenu = isset($mappMenu[$modul]) ? $mappMenu[$modul] : null;
        $_title = $default ? $default : $_title;
        return $titleMenu ? $titleMenu : $_title;
    }
}
