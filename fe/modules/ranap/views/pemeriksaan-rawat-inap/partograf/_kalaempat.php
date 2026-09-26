<?php
    use app\components\DocoHelpers;
    use kartik\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use kartik\datetime\DateTimePicker;
?>
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe', 'Kondisi Ibu'); ?></b></h6>
        </div>

        <div class="panel-body">
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'form-kala-empat',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'formConfig' => [
                        'labelSpan' => 4,
                        'deviceSize' => ActiveForm::SIZE_SMALL,
                    ]
                ]);
            ?>
            <?= $form->field($model, 'k4_keadaanumum',[
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2 text-bold',
                            'wrapper' => 'col-md-8'
                        ]
                    ])->textInput([
                            'class' => 'form-control',
                            'id' => 'k4_keadaanumum'
                    ]); 
            ?>
                <div class="form-group highlight-addon has-size-sm field-k4_td_systolic required">
                    <label class="control-label has-star text-left control-label col-sm-2 text-bold" for="k4_td_systolic">Tekanan Darah</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" id="k4_td_systolic" 
                                class="form-control input-sm doco-number text-right" 
                                name="KalaEmpatForm[k4_td_systolic]" 
                                placeholder="Mm" 
                                value="<?= $model->k4_td_systolic ?>" 
                                autocomplete="off" aria-required="true">
                                <span class="input-group-addon">Mm</span>
                            <input type="text" id="k4_td_diastolic" 
                                class="form-control input-sm doco-number text-right" 
                                name="KalaEmpatForm[k4_td_diastolic]" 
                                placeholder="Hg" 
                                value="<?= $model->k4_td_diastolic ?>" 
                                autocomplete="off" aria-required="true">
                                <span class="input-group-addon">Hg</span>
                        </div>
                    </div>
                </div>
            <?= $form->field($model, 'k4_detaknadi', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2 text-bold',
                                'wrapper' => 'col-md-2'
                            ],
                        'addon' => ['append' => [
                                'content' => '/Menit']]
                        ])->textInput([
                                'placeholder' => $model->getAttributeLabel('k4_detaknadi'),
                                'class' => 'form-control input-sm doco-number text-right',
                                'id' => 'k4_detaknadi',
                                'autocomplete' => "off"
                        ]); ?>
            <?= $form->field($model, 'k4_pernapasan', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2 text-bold',
                                'wrapper' => 'col-md-2'
                            ],
                        'addon' => ['append' => [
                                'content' => '/Menit']]
                        ])->textInput([
                                'placeholder' => $model->getAttributeLabel('k4_pernapasan'),
                                'class' => 'form-control input-sm doco-number text-right',
                                'id' => 'tanggal-pemakaian',
                                'autocomplete' => "off"
                        ]); ?>
            <div style="display: none;">
                <?php
                    echo DateTimePicker::widget([
                        'name' => 'FormPengembalian[tgl_kembali]',
                        'id' => 'formpengembalian-tgl_kembali',
                        'class' => 'tgl_kembali',
                        'language' => 'en',
                        'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                        'value' => null,
                        'readonly' => true,
                        'pluginOptions' => [
                            'format' => 'dd-M-yyyy hh:ii',
                            'showMeridian' => true,
                            'autoclose' => true,
                            'todayBtn' => true
                        ]
                    ]);
                ?>
            </div>
            <hr>
            <?= $form->field($model, 'k4_masalah', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2 text-bold',
                                'wrapper' => 'col-md-8'
                            ]
                        ])->textArea([
                                'placeholder' => $model->getAttributeLabel('k4_masalah'),
                                'class' => 'form-control input-sm',
                                'id' => 'k4_masalah',
                                'rows' => 4
                        ]); ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe', 'Pemantauan Kala IV'); ?></b></h6>
        </div>
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'form-pemantauan-empat',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'action' => '/ranap/pemeriksaan-rawat-inap/simpan-pemantauan?id=' . $pendaftaranId,
                    'formConfig' => [
                        'labelSpan' => 4,
                        'deviceSize' => ActiveForm::SIZE_SMALL,
                    ]
                ]);
            ?>
        <div class="panel-body">
            <br>
            <div class="form-group">
                <table id="partologi-pemantauan-kala" 
                class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">Jam<br>Ke<sub>*</sub></th>
                            <th>Waktu <sub>*</sub></th>
                            <th>Tekanan Darah <sub>*</sub><br>MmHg</th>
                            <th>Nadi<sub>*</sub><br>(Menit)</th>
                            <th>Suhu<sub>*</sub><br>(°C)</th>
                            <th>Tinggi Fundus<br>Uteri</th>
                            <th>Kontraksi Uterus</th>
                            <th>Kantung Kemih</th>
                            <th>Darah yang <br>Keluar</th>
                            <th width="12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9">
                                <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-right">
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b> ".Yii::t('fe', 'Sebelumnya'), [
            'class' => 'btn btn-xs btn-labeled btn-info btn-sebelumnya',
            'id' => 'btn-sebelumnya',
            'data-index' => 4
        ]) ?>
        <?= Html::button('<b><i class="fa fa-floppy-o"></i></b> Simpan', [
            'class' => 'btn btn-xs btn-labeled btn-info',
            'id' => 'btn-simpan-kala-empat',
        ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-right'></i></b> ".Yii::t('fe', 'Selanjutnya'), [
            'class' => 'btn btn-xs btn-labeled btn-info btn-selanjutnya',
            'id' => 'btn-selanjutnya',
            'data-index' => 4
        ]) ?>
    </div>
</div>

<script type="text/javascript">

    var tablePemantauan;

    $(function () {
        tablePemantauan = $("#partologi-pemantauan-kala").docoTabel({
            filter: true,
            sorting: [[2, "desc"]], 
            displayLength: 50,
            lengthChange: false,
            ordering: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: true,
            ajax: "/ranap/pemeriksaan-rawat-inap/get-data-pemantauan?id=" + "<?= $pendaftaranId ?>",
            columns: [
                {
                    title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jam Ke&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", 
                    data: "jam_ke", 
                    className:'text-center'
                },
                {
                    title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Waktu &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", 
                    data: "waktu",
                    width : "30%",
                    className:'text-center'
                },
                {
                    title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tekanan Darah (HmHg)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", 
                    data: "td_hmhg",
                    className:'text-center'
                },
                {
                    title: "Nadi (Menit)", 
                    data: "detak_nadi",
                    className:'text-center'
                },
                {
                    title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Suhu (°C)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", 
                    data: "suhu",
                    className:'text-center'
                },
                {
                    title: "Tinggi Fundus Uteri", 
                    data: "tinggi_fundus",
                    className:'text-center'
                },
                {
                    title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kontraksi Uterus&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", 
                    data: "kontraksi_uterus",
                    className:'text-center'
                },
                {
                    title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kantung Kemih&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", 
                    data: "kandung_kemih",
                    className:'text-center'
                },
                {
                    title: "Darah yang Keluar", 
                    data: "darah_keluar",
                    className:'text-center'
                },
                {
                    title: "Aksi", 
                    data: "aksi",
                    className:'text-center'
                },
            ],
            drawCallback : function () {
                $('.select2').select2();
                $('#date-time-picker').datetimepicker({
                    autoclose: true,
                    fontAwesome: false,
                    format: "dd-M-yyyy hh:ii",
                    icons : {
                        leftArrow: "glyphicon-arrow-left",
                        rightArrow: "glyphicon-arrow-right"
                    },
                    icontype: "glyphicon",
                    showMeridian: true,
                    todayBtn: true
                });
                $('#tambah-pemantauan').on('click', function (event) {
                    event.preventDefault();
                    $().docoForm('click', {
                        data : $('#form-pemantauan-empat').serializeArray(),
                        url : $('#form-pemantauan-empat').attr('action'),
                        success : function (data) {
                            tablePemantauan.draw();
                        },
                        error : function (event, data) {
                            $('table span.help-block').remove();
                        }
                    });
                });
                $('#hapus-pemantauan').on('click', function (event) {
                    event.preventDefault();
                    var _idParent = $(this).data('id');
                    $().docoForm('delete', {
                        url : '/ranap/pemeriksaan-rawat-inap/hapus-pemantauan-kala?id=' + _idParent,
                        success : function (data) {
                            tablePemantauan.draw();
                        }
                    });
                });
            }
        });
        $(".dataTables_filter").hide();
    });


    $('#btn-simpan-kala-empat').on('click', function(event) {
        event.preventDefault();
        $().docoForm('click', {
            url : $('#form-kala-empat').attr('action'),
            data : $('#form-kala-empat').serializeArray(),
            success :   function (data) {

            }
        });
    });
</script>