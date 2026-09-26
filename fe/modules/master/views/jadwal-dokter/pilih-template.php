<?php

/**
 * @author Randy Vianda Putra
 * @todo Modal pilih template
 * @copyright 05 November 2018 aweutist
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\components\DocoHelpers;

?>

<style lang="css">
    .label-template {
        font-size: 14px;
        overflow: hidden;
    }
    .radio-list {
        padding: 1px;
        width: 360px;
        display: inline-block;
        float: left;
    }
    .button-list {
        margin: 5px;
        float: left;
    }
    [type="radio"]:checked,
    [type="radio"]:not(:checked) {
        position: absolute;
        left: -999px;
    }
    [type="radio"]:checked + label,
    [type="radio"]:not(:checked) + label
    {
        position: relative;
        padding-left: 40px;
        width: 350px;
        cursor: pointer;
        line-height: 20px;
        display: inline-block;
        color: #666;
    }
    [type="radio"]:checked + label:before,
    [type="radio"]:not(:checked) + label:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 20px;
        height: 20px;
        border: 1px solid #ddd;
        border-radius: 100%;
        background: #fff;
    }
    [type="radio"]:checked + label:after,
    [type="radio"]:not(:checked) + label:after {
        content: '';
        width: 12px;
        height: 12px;
        background: #54be8b;
        position: absolute;
        top: 4px;
        left: 4px;
        border-radius: 100%;
        -webkit-transition: all 0.2s ease;
        transition: all 0.2s ease;
    }
    [type="radio"]:not(:checked) + label:after {
        opacity: 0;
        -webkit-transform: scale(0);
        transform: scale(0);
    }
    [type="radio"]:checked + label:after {
        opacity: 1;
        -webkit-transform: scale(1);
        transform: scale(1);
    }
</style>

<!-- Modal header -->
<div id="content">
    <div class="modal-header bg-inverse">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title"><?= Yii::t('fe', 'Daftar Template') ?></h5>
    </div>

    <!-- Modal body -->
    <div class="modal-body">
        <div class="row">
            <?php
                foreach ($data_template as $key => $value) {
            ?>
            <div class="col-md-offset-2">
                <div>
                    <div class="radio-list">
                        <input 
                            name="template"
                            type="radio" 
                            id="template-<?= $key ?>" 
                            data-id="<?= $value['notifikasi_id']?>"
                            data-notif="<?= $value['notifikasi']?>"
                            data-header-notif="<?= $value['judul_temp'] ?>"
                        >
                        <label for="template-<?= $key ?>" class="label-template control-label col-md-4"><?= $value['judul_temp'] ?></label>
                    </div>
                    <div class="button-list">
                        <button type="button" class="btn btn-sm btn-danger" id="delete-notif" data-id="<?= $value['notifikasi_id'] ?>">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>

    <!-- Modal footer -->
    <div class="modal-footer">
        <div class="pull-left">
            <?= Html::button("<b><i class='fa fa-check'></i></b>". Yii::t('fe', 'Pakai'), ['class' => 'btn btn-info btn-xs btn-labeled', 'id' => 'btn-set-notif']) ?>
        </div>
        <div class="pull-right">
            <?= Html::button("<b><i class='fa fa-plus'></i></b>". Yii::t('fe', 'Tambah baru'), [
                'class' => 'btn btn-info btn-xs btn-labeled',
                'id' => 'btn-add-template',
                'data-id' => $id
            ]) ?>
        </div>
    </div>
</div>
<!-- Javascript -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#btn-add-template').on("click", function(e){
            const id = $(this).attr('data-id');
            $('#content').docoLoad({
                url: `/master/jadwal-dokter/add-template?id=${id}`,
                dataType: 'html',
                success : function(data) {
                }
            });
        });

        $(document).on('click', '#btn-set-notif', function() {
            const notif_id = $('input[name="template"]:checked').attr('data-id');
            const header_notif = $('input[name="template"]:checked').attr('data-header-notif');
            const notif = $('input[name="template"]:checked').attr('data-notif');
            if (notif) {
                $('textarea.notif-set').val(notif);
                $('.header-notif').val(header_notif);
                $('.notif-id').val(notif_id);
                $('.close').trigger('click');
                $('.list-notif').show();
            }
        });

        $(document).on('click', '#delete-notif', function() {
            const id = $(this).attr('data-id');
            const button = this;
            if (id) {
                $(button).parent().parent().remove();
                $(this).docoForm('delete', {
                    url: `/master/jadwal-dokter/delete-template?id=${id}`,
                    success: function (data) {
                        $('#content').docoLoad({
                            url: `/master/jadwal-dokter/pilih-template?id=${id}`,
                            dataType: 'html',
                        });
                    }
                });
            }
        });
    });

</script>
