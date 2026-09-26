<?php

use app\components\DHtml;
use kartik\widgets\ActiveForm;

$temp = [];
$multiple = ["jalan_nafas", "sirkulasi", "pernafasan", "disability"];
if(!empty($dataForm)) {
    foreach($dataForm as $k => $v) {
        if( in_array($k, $multiple) ) {
            $temp[$k] = explode(",", $v);
        }
    }
}
?>
<style>

    tr td {
        padding: 5px;
    }

    table {
        border-collapse: collapse;
        width:100%;
        border:1px solid #dee2e6;
    }
 
    table, td, th {
        border: 1px solid black;
    }

</style>
<div style="padding: 10px;">
    <?php
    $form = ActiveForm::begin([
        'id' => 'form-triase',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'labelSpan' => 4,
            'deviceSize' => ActiveForm::SIZE_SMALL,
        ],
    ]);
    ?>
    <table style="border:1px solid #FFF;">
        <tr>
            <td width="20%"><p>Dokter Triase</p></td>
            <td><?php echo !empty($dataForm['dokter_nama']) ? $dataForm['dokter_nama'] : "-"; ?></td>
            <td width="20%"><p>Perawat Triase</p></td>
            <td width="35%"><?php echo !empty($dataForm['perawat_nama']) ? $dataForm['perawat_nama'] : "-"; ?></td>
        </tr>
        <tr>
            <td width="20%"><p>Tanggal/Jam Pasien Datang</p></td>
            <td colspan="3"><?php echo !empty($dataForm['tgl_triase']) ? date("d F Y H:i:s", strtotime($dataForm['tgl_triase'])) : "-"; ?></td>
        </tr>
        <tr>
            <td width="20%"><p>Kategori Triase</p></td>
            <td colspan="3"><strong><?php echo !empty($dataForm['hasil_triase']) ? strtoupper($dataForm['hasil_triase']) : "-"; ?></strong></td>
        </tr>
    </table>
    <p></p>
    <table>
        <tr>
            <td width="20%">Pemeriksaan</td>
            <td width="20%">Gangguan</td>
            <td width="18%" class="asdasd">Resusitasi</td>
            <td width="23%">Emergent</td>
            <td width="20%">Urgent</td>
            <td width="19%">Less Urgent</td>
            <td width="15%">Non Urgent</td>
        </tr>
        <tr>
            <td rowspan="<?= count($configVal['jalan_napas_extra']) ?>">Jalan Napas (Airway)</td>
            <?php foreach ($configVal['jalan_napas_extra'] as $gangguan => $pilihan) : ?>
                <td><?= str_replace('_', ' ', ucwords($gangguan, '_')) ?></td>
                <?php foreach ($pilihan as $kategori => $list) : ?>
                    <td data-triage_group="jalan_napas_extra">
                        <?=
                        DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'jalan_nafas',
                            'data' => $list,
                            'colSize' => 12,
                            'triage_group' => 'jalan_napas_extra',
                            'class' => 'btn-triage-option jalan_nafas_' . $kategori . ' ' . $kategori . '-group',
                            'selected' => !empty($temp["jalan_nafas"]) ? $temp["jalan_nafas"] : [],
                        ])
                        ?>
                    </td>
                <?php endforeach; ?>
                <?php for ($i = count($pilihan); $i < 5; $i++) : ?>
                    <td></td>
                <?php endfor; ?>
        </tr>
        <?php if (array_keys($configVal['jalan_napas_extra'])[count($configVal['jalan_napas_extra']) - 1] !== $gangguan) : ?>
            <tr>
            <?php endif; ?>
        <?php endforeach; ?>
            </tr>
            <tr>
                <td rowspan="<?= count($configVal['pernapasan_extra']) ?>">Pernapasan (Breathing)</td>
                <?php foreach ($configVal['pernapasan_extra'] as $gangguan => $pilihan) : ?>
                    <td><?= str_replace('_', ' ', ucwords($gangguan, '_')) ?></td>
                    <?php foreach ($pilihan as $kategori => $list) : ?>
                        <td data-triage_group="pernapasan_extra">
                            <?=
                            DHtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'pernafasan',
                                'data' => $list,
                                'colSize' => 12,
                                'triage_group' => 'pernapasan_extra',
                                'class' => 'btn-triage-option pernafasan_' . $kategori . ' ' . $kategori . '-group',
                                'selected' => !empty($temp["pernafasan"]) ? $temp["pernafasan"] : [],
                            ])
                            ?>
                        </td>
                    <?php endforeach; ?>
                    <?php for ($i = count($pilihan); $i < 5; $i++) : ?>
                        <td></td>
                    <?php endfor; ?>
            </tr>
            <?php if (array_keys($configVal['pernapasan_extra'])[count($configVal['pernapasan_extra']) - 1] !== $gangguan) : ?>
                <tr>
                <?php endif; ?>
            <?php endforeach; ?>
                </tr>
                    <tr>
                        <td rowspan="<?= count($configVal['sirkulasi_extra']) ?>">Sirkulasi (Circulation)</td>
                        <?php foreach ($configVal['sirkulasi_extra'] as $gangguan => $pilihan) : ?>
                            <td><?= str_replace('_', ' ', ucwords($gangguan, '_')) ?></td>
                            <?php foreach ($pilihan as $kategori => $list) : ?>
                                <td data-triage_group="sirkulasi_extra">
                                    <?=
                                    DHtml::multipleCheckbox([
                                        'model' => $model,
                                        'fieldName' => 'sirkulasi',
                                        'data' => $list,
                                        'colSize' => 12,
                                        'triage_group' => 'sirkulasi_extra',
                                        'class' => 'btn-triage-option sirkulasi_' . $kategori . ' ' . $kategori . '-group',
                                        'selected' => !empty($temp["sirkulasi"]) ? $temp["sirkulasi"] : [],
                                    ])
                                    ?>
                                </td>
                            <?php endforeach; ?>
                            <?php for ($i = count($pilihan); $i < 5; $i++) : ?>
                                <td></td>
                            <?php endfor; ?>
                    </tr>
                    <?php if (array_keys($configVal['sirkulasi_extra'])[count($configVal['sirkulasi_extra']) - 1] !== $gangguan) : ?>
                        <tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td colspan="2">Kesadaran (Disability)</td>
                            <td colspan="5">
                                <div>
                                    <div>Hasil GCS&nbsp;<strong><?= $model->hasil_gcs; ?></strong></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="<?= count($configVal['disability']) ?>">Disability (Gangguan Lain)</td>
                            <?php foreach ($configVal['disability'] as $gangguan => $pilihan) : ?>
                                <td><?= str_replace('_', ' ', ucwords($gangguan, '_')) ?></td>
                                <?php foreach ($pilihan as $kategori => $list) : ?>
                                    <td data-triage_group="disability">
                                        <?=
                                        DHtml::multipleCheckbox([
                                            'model' => $model,
                                            'fieldName' => 'disability',
                                            'data' => $list,
                                            'colSize' => 12,
                                            'triage_group' => 'disability',
                                            'class' => 'btn-triage-option disability_' . $kategori . ' ' . $kategori . '-group',
                                            'selected' => !empty($temp["disability"]) ? $temp["disability"] : [],
                                        ])
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <?php for ($i = count($pilihan); $i < 5; $i++) : ?>
                                    <td></td>
                                <?php endfor; ?>
                        </tr>
                        <?php if (array_keys($configVal['disability'])[count($configVal['disability']) - 1] !== $gangguan) : ?>
                            <tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                            </tr>
                            <tr>
                                <td colspan="2">Response Time</td>
                                <td>
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'waktu_respon',
                                        'data' => $configVal['waktu_respon']['resusitasi'],
                                        'colSize' => 12
                                    ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'waktu_respon',
                                        'data' => $configVal['waktu_respon']['emergent'],
                                        'colSize' => 12
                                    ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'waktu_respon',
                                        'data' => $configVal['waktu_respon']['urgent'],
                                        'colSize' => 12
                                    ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'waktu_respon',
                                        'data' => $configVal['waktu_respon']['less_urgent'],
                                        'colSize' => 12
                                    ])
                                    ?>
                                </td>
                                <td>
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'waktu_respon',
                                        'data' => $configVal['waktu_respon']['non_urgent'],
                                        'colSize' => 12
                                    ])
                                    ?>
                                </td>
                            </tr>
    </table>
    <p></p>
    <table>
        <tr class="noborder">
            <td colspan="2" class="noborder"><h5><strong>Keluhan Utama</strong></h5></td>
        </tr>
        <tr>
            <td colspan="2"><?php echo !empty($model->keluhan_utama) ? $model->keluhan_utama : "-"; ?></td>
        </tr>
    </table>
    <p></p>
    <table>
        <tr class="noborder">
            <td colspan="2" class="noborder"><h5><strong>Alergi</strong></h5></td>
        </tr>
        <tr>
            <td colspan="2">
                <?=
                DHtml::dontKnowRadio($model, 'alergi', [
                    'childDependent' => [
                        'id' => 'is_alergi-form',
                        'class' => 'is_alergi-check',
                        'colSize' => 12
                    ]
                ]);
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <p>Obat</p>
                <?php echo !empty($model->alergi_obat) ? $model->alergi_obat : "-"; ?>
            </td>
            <td>
                <p>Lainnya</p>
                <?php echo !empty($model->alergi_lainnya) ? $model->alergi_lainnya : "-"; ?>
            </td>
        </tr>
    </table>
    <p></p>
    <table>
        <tr class="noborder">
            <td colspan="2" class="noborder"><h5><strong>Trauma</strong></h5></td>
        </tr>
        <tr>
            <td colspan="2">
                <?=
                DHtml::multipleRadio([
                    'model' => $model,
                    'fieldName' => 'trauma',
                    'data' => $configVal['trauma'],
                    'colSize' => 12
                ])
                ?>
            </td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <table>
        <tr class="noborder">
            <td colspan="2" class="noborder"><strong>Tanda Vital (Vital Sign)</strong></td>
        </tr>
        <tr>
            <td>
                <div style="padding-bottom: 20px;">Tekanan Darah</div>
                <div style="padding-top: 10px;"><strong><?php echo !empty($model->tekanan_darah) ? $model->tekanan_darah . " mmHg" : "-"; ?></strong></div>
            </td>
            <td>
                <div style="padding-bottom: 20px;">Frekuensi Nadi</div>
                <div style="padding-top: 10px;"><strong><?php echo !empty($model->nadi) ? $model->nadi . " x/menit" : "-"; ?></strong></div>
            </td>
        </tr>
        <tr>
            <td>
                <div style="padding-bottom: 20px;">Frekuensi Nafas</div>
                <div style="padding-top: 10px;"><strong><?php echo !empty($model->nafas) ? $model->nafas . " x/menit" : "-"; ?></strong></div>
            </td>
            <td>
                <div style="padding-bottom: 20px;">Suhu/Temp</div>
                <div style="padding-top: 10px;"><strong><?php echo !empty($model->suhu) ? $model->suhu . "°C" : "-"; ?></strong></div>
            </td>
        </tr>
        <tr>
            <td>
                <div style="padding-bottom: 20px;">Saturasi Oksigen (SpO2)</div>
                <div style="padding-top: 10px;"><strong><?php echo !empty($model->saturasi_oksigen) ? $model->saturasi_oksigen . "%" : "-"; ?></strong></div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <?php ActiveForm::end(); ?>
</div>
