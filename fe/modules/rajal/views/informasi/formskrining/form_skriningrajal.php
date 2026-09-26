<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

?>
<style>
    .custom-diff {
        display: flex !important;
        justify-content: space-around !important;
    }
    .panel-toolbar-skrining {
        padding: 6px 10px 6px 14px;
        border-bottom: 1px solid #cccccc;
        background-color: #f5f5f5;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Form Skrining Rajal') ?></h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapsed" href="#skrining-rajal"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar-skrining">
                <?= DocoHelpers::generateToolbar([
                    'custom-save' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'id' => 'submit-skrining-rajal',
                            'data-options' => 'click'
                        ]
                    ],
                    'custom-print' => [
                        'title' => Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-print-sklrining-rajal',
                            'disabled' => false 
                        ],
                    ],
                ], ''); ?>
            </div>
        <div class="card">
            <div class="card-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'form-skrining-rajal',
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'enableAjaxValidation'=>false, 
                        'enableClientValidation'=>false,
                    ]);
                ?>
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>
                            <span>KESADARAN</span>
                        </th>
                        <?= $form->field($model, 'kesadaran')->label(false)->radioList($kesadaran, ['inline' => true, 'item' => function($index, $label, $name, $checked, $value) {
                                $disabled = '';
                                $check = '';    
                                if($checked == true) {
                                    $check = 'checked';
                                }
                                $return = '<td><label class="radio-' . $value . '">';
                                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . 'class="attribute-'.$index.'">';
                                $return .= ' <i></i>';
                                $return .= '<span>' . ucwords($label) . '</span>';
                                $return .= '</label></td>';
                                return $return;
                            }
                        ]);?>
                    </tr>
                    <tr>
                        <th>
                            <span>PERNAPASAN</span>
                        </th>
                        <?= $form->field($model, 'pernapasan')->label(false)->radioList($pernapasan, ['inline' => true, 'item' => function($index, $label, $name, $checked, $value) {
                                $disabled = '';
                                $check = '';    
                                if($checked == true) {
                                    $check = 'checked';
                                }
                                $return = '<td><label class="radio-' . $value . '">';
                                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . 'class="attribute-'.$index.'">';
                                $return .= ' <i></i>';
                                $return .= '<span>' . ucwords($label) . '</span>';
                                $return .= '</label></td>';
                                return $return;
                            }
                        ]);?>
                    </tr>
                    <tr>
                        <th>
                            <span>RESIKO JATUH</span>
                        </th>
                        <?= $form->field($model, 'resiko_jatuh')->label(false)->radioList($resiko_jatuh, ['inline' => true, 'item' => function($index, $label, $name, $checked, $value) {
                                $disabled = '';
                                $check = '';    
                                if($checked == true) {
                                    $check = 'checked';
                                }
                                $return = '<td><label class="radio-' . $value . '">';
                                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . 'class="attribute-'.$index.'">';
                                $return .= ' <i></i>';
                                $return .= '<span>' . ucwords($label) . '</span>';
                                $return .= '</label></td>';
                                return $return;
                            }
                        ]);?>
                    </tr>
                    <tr>
                        <th>
                            <span>NYERI DADA</span>
                        </th>
                        <?= $form->field($model, 'nyeri_dada')->label(false)->radioList($nyeri_dada, ['inline' => true, 'item' => function($index, $label, $name, $checked, $value) {
                                $disabled = '';
                                $check = '';    
                                if($checked == true) {
                                    $check = 'checked';
                                }
                                $return = '<td><label class="radio-' . $value . '">';
                                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . 'class="attribute-'.$index.'">';
                                $return .= ' <i></i>';
                                $return .= '<span>' . ucwords($label) . '</span>';
                                $return .= '</label></td>';
                                return $return;
                            }
                        ]);?>
                    </tr>
                    <tr>
                        <th>
                            <span>NYERI (NRS/VAS) Skala 0 - 10</span>
                        </th>
                        <td>
                            <input type="text" name="number-text-1" class="form-control doco-number skala-nyeri" min="0" max=10 maxlength="2" id="nyeri_1">
                        </td>
                        <td>
                            <input type="text" name="number-text-2" class="form-control doco-number skala-nyeri" min="0" max=10 maxlength="2" id="nyeri_2">
                        </td>
                        <td>
                            <input type="text" name="number-text-3" class="form-control doco-number skala-nyeri" min="0" max=10 maxlength="2" id="nyeri_3">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <span>BATUK</span>
                        </th>
                        <?= $form->field($model, 'batuk')->label(false)->radioList($batuk, ['inline' => true, 'item' => function($index, $label, $name, $checked, $value) {
                                $disabled = '';
                                $check = '';    
                                if($checked == true) {
                                    $check = 'checked';
                                }
                                $return = '<td><label class="radio-' . $value . '">';
                                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . 'class="attribute-'.$index.'">';
                                $return .= ' <i></i>';
                                $return .= '<span>' . ucwords($label) . '</span>';
                                $return .= '</label></td>';
                                return $return;
                            }
                        ]);?>
                    </tr>
                    <tr>
                        <th>
                            <span>KEPUTUSAN</span>
                        </th>
                        <?= $form->field($model, 'keputusan')->label(false)->radioList($keputusan, ['inline' => true, 'item' => function($index, $label, $name, $checked, $value) {
                                $disabled = '';
                                $check = '';    
                                if($checked == true) {
                                    $check = 'checked';
                                }
                                $return = '<td><label class="radio-' . $value . '">';
                                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . 'class="attribute-'.$index.'">';
                                $return .= ' <i></i>';
                                $return .= '<span>' . ucwords($label) . '</span>';
                                $return .= '</label></td>';
                                return $return;
                            }
                        ]);?>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <div style="display: flex; align-items: center;">
                                <div>
                                    <span style="margin-right: 50px;">BAHASA SEHARI - HARI</span>
                                </div>
                                <div style="display: flex; justify-content: space-around;">
                                    <span style="min-width: 150px;">
                                        <?= $form->field($model, 'bahasa')->checkbox(['label' => 'Indonesia'])?>
                                    </span>
                                    <span style="min-width: 150px; display: flex;">
                                        <?= $form->field($model, 'bahasa_daerah')->checkbox(['label' => 'Daerah'])?>
                                        <?= $form->field($model, 'bahasa_daerah_text')->label(false)->textInput(['class' => 'mt-2'])?>
                                    </span>
                                    <span style="min-width: 150px; display: flex;">
                                        <?= $form->field($model, 'bahasa_asing')->checkbox(['label' => 'Asing'])?>
                                        <?= $form->field($model, 'bahasa_asing_text')->label(false)->textInput(['class' => 'mt-2'])?>
                                    </span>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4"><div style="display: flex; align-items: center;">
                            <div style="display: flex; align-items: center;">
                                <div>
                                    <table>
                                        <tr>
                                            <th style="min-width: 200px;">POLI KLINIK TUJUAN</th>
                                            <th style="min-width: 10px;">:</th>
                                            <th><?= $datapasien['ruangan_nama'] ?></th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4"><div style="display: flex; align-items: center;">
                            <div style="display: flex; align-items: center;">
                                <div style="margin-right: 100px;">
                                    <table>
                                        <tr>
                                            <th style="min-width: 200px;">ASAL RUJUKAN</th>
                                            <th style="min-width: 10px;">:</th>
                                            <th><?= $datapasien['asalrujukan_nama'] != null ? $datapasien['asalrujukan_nama'] : '-'; ?></th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div style="display: flex; justify-content: space-around;">
                                <table>
                                    <tr>
                                        <th class="text-center">
                                            <span>Petugas Loket Pendaftaran</span>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <span><?= $petugas_loket ?></span>
                                        </th>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <th class="text-center">
                                            <span>Pasien / Keluarga Pasien</span>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <span><?= $datapasien['nama_pasien'] ?></span>
                                        </th>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
                <?=  Html::activeHiddenInput($model, 'pendaftaran_id', ['value' => $pendaftaran_id]) ?>
                <?=  Html::activeHiddenInput($model, 'nama_pasien', ['value' => $datapasien['nama_pasien']]) ?>
                <?=  Html::activeHiddenInput($model, 'poli_tujuan', ['value' => $datapasien['ruangan_nama']]) ?>
                <?=  Html::activeHiddenInput($model, 'petugas_loket_pendaftaran', ['value' => $userlogin['nama_pegawai']]) ?>
                <?=  Html::activeHiddenInput($model, 'pasien_keluarga_pasien', ['value' => $datapasien['nama_pasien']]) ?>
                <?=  Html::activeHiddenInput($model, 'asal_rujukan', ['value' => $datapasien['asalrujukan_nama'] != null ? $datapasien['asalrujukan_nama'] : '-']) ?>
                <?=  Html::activeHiddenInput($model, 'is_riwayat', ['value' => $is_riwayat, 'id' => 'is_riwayat']) ?>
                <?php ActiveForm::end(); ?>
                <?=  Html::activeHiddenInput($model, 'nyeri') ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        const bahasaDaerah = $('#skriningrajalform-bahasa_daerah') 
        const bahasaAsing = $('#skriningrajalform-bahasa_asing') 
        const cetakPdf = $("#btn-print-sklrining-rajal")
        const pendaftaran_id = $('#skriningrajalform-pendaftaran_id').val()
        let nyeri = '<?= $model->nyeri ?>';
        if(nyeri != "") {
            nyeri = JSON.parse(nyeri);
        }
        
        var is_riwayat = $('#is_riwayat').val()
        if(is_riwayat == 'true'){
            $(':input[type="radio"]').attr("disabled", 'disabled');
            $(':input[type="text"]').attr("disabled", 'disabled');
            $(':input[type="number"]').attr("disabled", 'disabled');
            $(':input[type="checkbox"]').attr("disabled", 'disabled');
            $('#submit-skrining-rajal').attr("disabled", 'disabled');
        }

        $("#submit-skrining-rajal").click(function (e) { 
            e.preventDefault();
            var _data = $('#form-skrining-rajal').serializeArray();
            let nyeri_1 = $("#nyeri_1").val();
            let nyeri_2 = $("#nyeri_2").val()
            let nyeri_3 = $("#nyeri_3").val()
            let nyeri = [];
            
            nyeri = [
                {
                    name: "SkriningRajalForm[nyeri_1]",
                    value : $('#nyeri_1').val()
                },
                {
                    name: "SkriningRajalForm[nyeri_2]",
                    value : $('#nyeri_2').val()
                },
                {
                    name: "SkriningRajalForm[nyeri_3]",
                    value : $('#nyeri_3').val()
                }
            ];

            $.merge(_data, nyeri);
            $().docoForm('click', {
                method: "POST",
                url: 'rajal/informasi/save-skrining-rajal',
                data: _data,
                success: function (data) {
                    $('#tab-skrining-rajal').trigger('click')
                }
            });
        });

        disabledCheck();

        /**
         * Function untuk melakukan disabled inputan untuk bahasa
         */
        function disabledCheck() {
            if(bahasaDaerah.is(':checked') == false) {
                $('#skriningrajalform-bahasa_daerah_text').prop("disabled", true)
                $('#skriningrajalform-bahasa_daerah_text').val(null)
            } else {
                $('#skriningrajalform-bahasa_daerah_text').prop("disabled", false)
            }

            if(bahasaAsing.is(':checked') == false) {
                $('#skriningrajalform-bahasa_asing_text').prop("disabled", true)
                $('#skriningrajalform-bahasa_asing_text').val(null)
            } else {
                $('#skriningrajalform-bahasa_asing_text').prop("disabled", false)
            }
        }

        bahasaDaerah.change(function (e) { 
            e.preventDefault();
            disabledCheck()
        });

        bahasaAsing.change(function (e) { 
            e.preventDefault();
            disabledCheck()
        });
        
        cetakPdf.click(function (e) {
            e.preventDefault()
            window.open(`/rajal/informasi/cetak-skrining-rajal?pendaftaran_id=${pendaftaran_id}`)
        })

        defaultNyeri()
        function defaultNyeri() {
            if(nyeri != "") {
                $('#nyeri_1').val(nyeri['nyeri_1'])
                $('#nyeri_2').val(nyeri['nyeri_2']) 
                $('#nyeri_3').val(nyeri['nyeri_3']) 
            }
        }

        $(".skala-nyeri").on("input", function() {
            if($(this).val() > 10) {
                $(this).val(10);
            }

            if($(this).val() < 0) {
                $(this).val(0);
            }
        });
    });
</script>
