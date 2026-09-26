<?php
/**
 * @author Budi
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
?>

<div class="modal-header bg-inverse">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h5 class="modal-title"><?= $title ?></h5>
</div>

<?php
    $form = ActiveForm::begin([
        'id' => 'topup-plafon-form',
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>false,
        'type' => ActiveForm::TYPE_VERTICAL,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
?>
<?= $form->field($model, 'pendaftaran_id')->hiddenInput()->label(false); ?>
<?= $form->field($model, 'instalasi_id')->hiddenInput()->label(false); ?>
<?= $form->field($model, 'kelaspelayanan_id')->hiddenInput()->label(false); ?>

<div class="modal-body">
	<div class="col-md-12">
        <div class="panel-toolbar clearfix">
            <div class="btn-group pull-left">
                <?= DocoHelpers::generateToolbar([
                    'history-plafon' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'History'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'id' => 'history-plafon',
                            'data-width' => "60%",
                            'data-options' => 'click'
                        ]
                    ],
                    'update-plafon' => [
                        'title' => \Yii::t('fe', 'Update'),
                        'icon' => 'fa fa-floppy-o',
                        'attributes' => [
                            'id' => 'update-plafon',
                            'data-options' => 'click'
                        ]
                    ],
                ]) ?>
            </div>
        </div>
        <br>
		<div class="row">
            <div class="col-md-12">
                <table class="table datatable-basic table-hover dataTable no-footer" id="table-detail" style="width: 100%;margin-bottom:20px;">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'Instalasi'); ?></th>
                            <th><?=Yii::t('fe', 'Kelas Pelayanan'); ?></th>
                            <th style="width:30%"><?=Yii::t('fe', 'Nominal Plafon'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= ArrayHelper::getValue($detailPlafon, 'instalasi_nama') ?></td>
                            <td><?= ArrayHelper::getValue($detailPlafon, 'kelaspelayanan_nama') ?></td>
                            <td><?= $form->field($model, 'plafon')->textInput(['class' => 'form-control input-sm doco-number'])->label(false); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
	</div>
</div>

<?php ActiveForm::end(); ?>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
<div id="modal_history_plafon" style="z-index: 2041 !important; overflow-y: auto !important;" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>

<script type="text/javascript">
    var pendaftaranId = "<?= $pendaftaranId ?>";
    $('#plafonbpjsform-plafon').trigger('change')
    $(document).ready(function(){
        $('#update-plafon').unbind();
        $('#update-plafon').bind('click', function(e){
            e.preventDefault();
            var _form = $("#topup-plafon-form").serializeArray();
            $().docoForm("click",{
                url : "/kasir/inf-pasien-pulang/simpan-plafon?pendaftaran_id=" + pendaftaranId,
                method : "POST",
                type : "json",
                data: _form,
                success : function (data) {
                    $('#modal_backdrop').modal('toggle');
                    $('#table-informasi').DataTable().draw();
                    $('#example').DataTable().draw();
                    $("#plafon-bpjs").prop("disabled", true);
                }
            });
        })
        $('#history-plafon').unbind();
        $('#history-plafon').bind('click', (e) => {
            e.preventDefault();
            const { width } = $('#history-plafon').data()
            showLoader('Memuat Halaman...')
            $('#modal_riwayat').find('.modal-dialog').css('width', width)
            $('#modal_riwayat .modal-content').docoLoad({
                url: "/kasir/inf-pasien-pulang/modal-history-plafon?pendaftaran_id=" + pendaftaranId,
                dataType: 'html',
                success: function (data) {
                    hideLoader()
                    $('#modal_riwayat .modal-content').parents('.modal').modal('show')
                },
                error: function () {
                    hideLoader()
                }
            })
        });
    })
</script>
