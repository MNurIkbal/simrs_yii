<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use kartik\datetime\DateTimePicker;
    $model->tgl_pemakaiandari = date('d-M-Y H:i',strtotime($model->tgl_pemakaiandari));
?>
<style>
    .datepicker>div{
        display:block;
    }
    .datepicker>div{
        display:block;
    }
    .plat-nomor{
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #fff;
    }
    .background-plat{
        text-align: center;
        display: inline-block;
        position: relative;
        width: 165px;
        padding: 5px;
        border-color: #fff;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #54be8b;
        margin-left : 9px;
    }

    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }
    .my-legend .legend-scale ul li {
        display: contents;
        float: left;
        width: 50px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }
    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 50px;
        border: solid 0.2px;
    }
    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }
    .my-legend a {
        color: #777;
    }

    .square-batal {
        height: 30px;
        width: 70px;
        background-color: rgba(255, 188, 188, 0.58);
        color:#ffffff;
        padding: 5px 0 5px 10px;
    }
    .tab-content > .has-padding {
        padding: 0px !important;
    }
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b><?= $title ?></b></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'pemakaian-form', 
            'action' => '/ambulan/informasi-pemakaian-ambulan/simpan-pemakaian-ambulan?id='.$id, 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group">
        <label class="text-left control-label col-sm-4 text-bold">No. Polisi</label>
        <div class="col-md-8 background-plat">
            <span class="plat-nomor"><?= $model->no_polisi ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="text-left control-label col-sm-4 text-bold">Tanggal Pemakaian</label>
        <div class="col-lg-6">
            <?php
                echo DateTimePicker::widget([
                    'name' => 'FormPengembalian[tgl_pemakaiandari]',
                    'id' => 'formpengembalian-tgl_pemakaiandari',
                    'class' => 'tgl_pemakaiandari',
                    'language' => 'en',
                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                    'value' => $model->tgl_pemakaiandari,
                    'disabled' => true,
                    'pluginOptions' => [
                        'format' => 'dd-M-yyyy hh:ii',
                        'showMeridian' => true,
                        'autoclose' => true,
                        'todayBtn' => true,
                        'endDate' => date('Y-m-d H:i:s'),
                    ]
                ]);
            ?>
        </div>
    </div>
    <div class="form-group required">
        <label class="text-left control-label col-sm-4 text-bold ">Tanggal Kembali</label>
        <div class="col-lg-6">
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
                        'todayBtn' => true,
                        // 'endDate' => date('Y-m-d H:i:s', strtotime($model->tgl_pemakaiandari)),
                        'startDate' => date('Y-m-d H:i:s', strtotime($model->tgl_pemakaiandari))
                    ]
                ]);
            ?>

        </div>
    </div>
    <div class="form-group">
        <label class="text-left control-label col-sm-4 text-bold">Km Awal</label>
        <div class="col-lg-3">
            <?= $form->field($model, 'km_awal')->textInput([
                    'class' => 'form-control text-right doco-number',
                    'readonly' => true,
                ])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label class="text-left control-label col-sm-4 text-bold">Km Akhir</label>
        <div class="col-lg-3">
            <?= $form->field($model, 'km_akhir')->textInput([
                    'class' => 'form-control text-right doco-number',
                ])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="text-left control-label col-sm-4 text-bold">Biaya Pemakaian</label>
        <div class="col-lg-6">
            <?= $form->field($model, 'biaya_pemakaian', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ],
                'addon' => [
                    'prepend' => [
                        'content' => 'Rp.'
                    ]]
                ])->textInput([
                    'class' => 'form-control text-right doco-number',
                    'readonly' => true,
                ])->label(false); ?>
        </div>
    </div>
    <?php
        // if (empty($model->pendaftaran_id)) :
        if (false) :
    ?>
        <div class="form-group required">
            <label class="text-left control-label col-sm-4 text-bold">Biaya Tambahan</label>
            <div class="col-lg-6">
                <?= $form->field($model, 'biaya_tambahan', [
                    'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-4',
                            'wrapper' => 'col-md-5'
                        ],
                    'addon' => [
                        'prepend' => [
                            'content' => 'Rp.'
                        ]]
                    ])->textInput([
                        'class' => 'form-control text-right doco-number',
                        'id' => 'biaya_tambahan'
                    ])->label(false); ?>
            </div>
        </div>
    <?php
        endif;
    ?>
    <div class="form-group">
        <label class="text-left control-label col-sm-4 text-bold">Total Biaya</label>
        <div class="col-lg-6">
            <?php $model->nominal_tagihan = (int) $model->nominal_tagihan; ?>
            <?= $form->field($model, 'nominal_tagihan', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ],
                'addon' => [
                    'prepend' => [
                        'content' => 'Rp.'
                    ]]
                ])->textInput([
                    'class' => 'form-control text-right doco-number',
                    'readonly' => true,
                    'id' => 'nominal_tagihan'
                ])->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<hr>
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", [
            'class' => 'btn bg-teal',
            'id' => 'pemakaian-ambulan'
    ]) ?>
    <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
                        'class' => 'btn bg-slate',
                        'id' => 'btn-kembali'
                    ]); ?>
</div>

<script type="text/javascript">
    var _totalNominal = 0;
    var header = 'Perhatian !';
    var message = 'Apakah anda yakin untuk menutup halaman ini ?';
    var label = {
        buttons: {
            'Yes': 'button-yes',
            'No': 'button-no'
        }
    };
    $(function() {
        $('.doco-number').trigger('change');
        _totalNominal = parseFloat(docoHelper.convertToAngka($('#nominal_tagihan').val()));
    });
    $('#btn-kembali').on('click', function (event) {
        event.preventDefault();
        $.showQuestionDialog(header, message, label, function(reaction) {
            if (reaction == 'Yes') {
                $('#modal_backdrop').modal('toggle');
            }
        });
    });
    $('#biaya_tambahan').on('keyup', function (event) {
        event.preventDefault();
        var _value = parseFloat(docoHelper.convertToAngka($(this).val()));
        if (isNaN(_value)) _value = 0;
        $('#nominal_tagihan').val(_totalNominal + _value).trigger('change');
    })
    $('#pemakaian-ambulan').on('click', function (event) {
        event.preventDefault();
        $().docoForm('click', {
            data : $('#pemakaian-form').serializeArray(),
            url : $('#pemakaian-form').attr('action'),
            success : function (data) {
                $('#modal_backdrop').modal('toggle');
                table.draw();
            }
        });
    });
</script>