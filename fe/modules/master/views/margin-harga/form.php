<?php
// Author : Naufal Ziyad L
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\widgets\DatePicker;

?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    // 'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Nama')?></label>
        <div class="col-lg-7">
            <?= $form->field($model, 'perda_margin')
                ->textInput(['class' => 'form-control'])
                ->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Berlaku')?></label>
        <div class="col-lg-7">
           <?= $form->field($model, 'tgl_berlaku')->widget(DatePicker::classname(), [
                'name' => 'date_12',
                'readonly' => true,
                'language' => 'en',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                    'startDate' => "0d",
                ]
            ])->label(false); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3">
            <label class=""><?= Yii::t('fe', 'Harga Min') ?> <span class="text-danger">*</span></label>
            <div class="col-lg-12">
                <?= Html::textInput('KonfigMarginDetailForm[harga_min]',null,[
                    'class' => 'form-control harga_min doco-number',
                ]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <label class=""><?= Yii::t('fe', 'Harga Max') ?> <span class="text-danger">*</span></label>
            <div class="col-lg-12">
                <?= Html::textInput('KonfigMarginDetailForm[harga_max]',null,[
                    'class' => 'form-control harga_max doco-number',
                ]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <label class=""><?= Yii::t('fe', 'Margin') ?> <span class="text-danger">*</span></label>
            <div class="col-lg-12">
                <?= Html::textInput('KonfigMarginDetailForm[margin]',null,[
                    'class' => 'form-control margin',
                ]) ?>
            </div>
        </div>
        <div class="col-md-3 button-list">
            <label for=""></label>
            <?= Html::submitButton('<i class="fa fa-plus"></i> ' . Yii::t('fe', "Tambah"), [
                'class' => 'btn btn-success addrow',
                'style' => 'margin-top:17px;'
                ]); ?>
            <input type="hidden" name="form" value="true">
        </div>
    </div>

    <table id="temp" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?=\Yii::t("fe", "Harga Min (Rp.)");?></th>
                <th><?=\Yii::t("fe", "Harga Max (Rp.)");?></th>
                <th><?=\Yii::t("fe", "Margin (%)");?></th>
                <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="5">
                    <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                </td>
            </tr>
        </tbody>
    </table>
    <br><br><br><hr>
    <div class="modal-footer">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
            'class' => 'btn btn-info btn-labeled btn-xs',
            'id' => 'btn-submit'
        ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
            'class' => 'btn btn-info btn-labeled btn-xs',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    var tableTemp;
    var id = "<?= $id ?>";

    $('#btn-submit').on('click', function (event) {
        var _form = $('#form');
        $(this).docoForm("click", {
            url : _form.attr('action'),
            data : _form.serializeArray(),
                success : function(data) {
                $("#modal_backdrop").modal("toggle");
                tableTemp.draw();
                table.draw();
            }
        });
    });

    function ajaxLoading(element) {
        $(element).attr("disabled", true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }

    function ajaxAfterLoading(element, text) {
        $(element).attr('disabled', false);
        $(element).html(text)
    }

    $(document).on("click", ".addrow", function(event) {
        event.preventDefault();
        var data = $("#form").serialize();
        var form = $("#form");
        var submit_btn = form.find(".addrow");
        var _hargaMin = form.find('.harga_min').val();
        var _hargaMax = form.find('.harga_max').val();
        var _margin = form.find('.margin').val();

        if (_hargaMin == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Harga Minimum tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        if (_hargaMax == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Harga Maximum tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        if (_margin == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Margin tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        if (parseInt(_hargaMin) == 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Harga Minimum tidak boleh kurang dari 1"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        if (parseInt(_hargaMax) == 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Harga Maximum tidak boleh kurang dari 1"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        if (parseInt(_margin) == 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Margin tidak boleh kurang dari 1"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        $.ajax({
            url: "/master/margin-harga/set-list-item",
            type: "post",
            data: form.serialize(),
            beforeSend: function () {
                ajaxLoading(submit_btn);
            },
            success: function (data) {
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di tambah"));
                form = 'false';
                var value = data.data;
                var response = {};
                
                tableTemp.draw();

                $(".harga_min").val(null).trigger("change");
                $(".harga_max").val(null).trigger("change");
                $(".margin").val(null).trigger("change");

                return false;
            },
            error: function (res) {
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                var resMessage = res.responseJSON.message;
                docoNotification("error", i18next.t("Perhatian"), i18next.t(resMessage));
                return false;
            },
            complete: function() {
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            }
        });
    });

    tableTemp = $("#temp").docoTabel({
        filter: false,
        displayLength: 20,
        lengthChange : false,
        processing: true,
        serverSide: true,
        paging: false,
        info: false,
        scrollY: "100px",
        ajax: baseUrl+"master/margin-harga/get-list-item",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false,
                width: "1"
            },
            {
                title: "Harga Min", 
                data: "harga_min",
                searchable: false,
                orderable: false,
                class: "text-right"
            },
            {
                title: "Harga Max", 
                data: "harga_max",
                searchable: false,
                orderable: false,
                class: "text-right"
            },
            {
                title: "Margin",
                data: "margin",
                searchable: false,
                orderable: false,
                class: "text-right"
            },
            {
                title: "Aksi",
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
    });

    $(document).on('click','.delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            skipConfirm: true,
            success : function (data) {
                table.draw();
            }
        });
    });

    $(document).on('click','.delete-cache', function(event) {
        event.preventDefault();
        var id = $(this).data("id");
        var action = $(this).data("action");
        var button = this;
        var valButton = $(button).html();
        var ResData = {
                id : id
            };

        $(this).docoForm("click",{
            url: action,
            confirmTitle: i18next.t("Konfirmasi"),
            confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
            data: ResData,
            method: "GET",
            before: function () {
                $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
                $(button).prop("disabled", true);
            },
            success: function () {
                tableTemp.draw();
                $(button).parent().parent().remove();
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di hapus"));
            }
        });
    });
</script>
