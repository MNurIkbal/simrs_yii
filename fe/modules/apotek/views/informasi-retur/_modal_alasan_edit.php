<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
?>

<style type="text/css">
    .required {
        color: red;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2" style="padding-top: 4px;">
                Alasan Edit <span class="required">*</span>
            </div>
            <div class="col-md-10">
                <textarea
                    id="alasan_edit"
                    name="alasan_edit"
                    placeholder="Masukkan alasan edit Retur"
                    class="form-control"></textarea>
            </div>
        </div><br>
        <div class="row">
            <div class="col-md-2" style="padding-top: 4px;">
                Username <span class="required">*</span>
            </div>
            <div class="col-md-10">
              <input type="text" class="form-control input-pemakai" readonly="" value="<?= !empty(Yii::$app->user->identity->nama)
              ?  Yii::$app->user->identity->nama : null?>">
            </div>
        </div><br>
        <div class="row">
            <div class="col-md-2" style="padding-top: 4px;">
                Password <span class="required">*</span>
            </div>
            <div class="col-md-10">
              <input type="password" class="form-control input-sandi" placeholder="Password">
            </div>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
            'class' => 'btn btn-info btn-labeled btn-xs',
            'data-dismiss' => 'modal'
        ]); ?>
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
            'class' => 'btn btn-info btn-labeled btn-xs',
            'id' => 'btn-submit-edit'
        ]) ?>
    </div>
</div>

<?php
    $this->registerJs("
        $(document).ready(function(){
            $('#btn-submit-edit').on('click', function(){
                alasan_edit = $('#alasan_edit').val();
                pass = $('.input-sandi').val();
                if(alasan_edit == null || alasan_edit == '') {
                    docoNotification('error', 'Proses Gagal !', 'Alasan edit tidak boleh kosong');
                    $('#alasan_edit').focus();
                    return false;
                }

                if(pass == null || pass == '') {
                    docoNotification('error', 'Proses Gagal !', 'Password tidak boleh kosong');
                    $('.input-sandi').focus();
                    return false;
                }

                $('#btn-submit-edit').docoForm('click', {
                    url: '/apotek/informasi-retur/validasi-edit',
                    type: 'post',
                    skipConfirm: true,
                    skipErrorNotif: true,
                    skipSuccessNotif: true,
                    data: {
                        alasan : $('#alasan_edit').val(),
                        username: $('.input-pemakai').val(),
                        pass: $('.input-sandi').val(),
                        pendaftaran_id: '".$pendaftaran_id."',
                        retur_id: '".$retur_id."',
                        type: 'Edit'
                    },
                    success: function(response) {
                        window.location.href = `/apotek/informasi-retur/edit?id=${pendaftaran_id}&returresep_id=${retur_id}`
                    },
                });
            });
        });

    ", VIEW::POS_END, 'js-kuning');
?>
