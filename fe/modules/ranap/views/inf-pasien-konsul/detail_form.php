<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use app\components\DocoConstants;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="clear"></div>
    <div class="row">
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'jawaban-form', 
                    'options' => [
                            'class' => 'form-horizontal', 
                            'enableAjaxValidation' => true,
                            'role' => 'form',
                            'style' => 'padding: 0 10px 0 10px;'
                        ],
                ]); 
            ?>
           
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'No pendaftaran'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['no_pendaftaran'] ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'No rekam medik'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['no_rekam_medik'] ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Nama pasien'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['nama_pasien'] ?></label>
                </div>
            </div>
              <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Jenis konsul'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['jenis_konsul_nama'] ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu permintaan'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo date('d F Y H:i:s', strtotime($getDataPermintaan['waktu_permintaan'])); ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Dokter yang meminta konsul'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['dokterdpjpasal_nama'] ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Dokter yang dikonsul'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['dok_konsul'] ?></label>
                </div>
            </div>
             <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Persetujuan'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['status_konsul_nama'] ?></label>
                </div>
            </div>
             <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu persetujuan'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo date('d F Y H:i:s', strtotime($getDataPermintaan['waktu_permintaan'])); ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Permintaan konsultasi'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['ket_konsul'] ?></label>
                </div>
            </div>
             <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Penemuan dan rekomendasi dokter konsultan'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['jawaban_konsul'] ?></label>
                </div>
            </div>
            <?= Html::hiddenInput('PermintaanKonsulForm[permintaankonsul_id]', $getDataPermintaan['permintaankonsul_id'] );?>
    </div>
    <hr>
    <div class="modal-footer">
    <?php
        if(($getDataPermintaan['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU && $getDataPermintaan['jawaban_konsul'] != NULL)){
            echo Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.\Yii::t('fe', 'Jawaban konsultasi'),
                                        Url::to(['cetak-jawaban-pdf', 'permintaankonsul_id'=>$permintaankonsul_id]),
                                        ['class'=>'btn btn-info btn-labeled btn-xs','target'=>'_blank']
                                    );
        }

    ?>
        <?= Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.\Yii::t('fe', 'Permintaan konsultasi'),
                                        Url::to(['cetak-permintaan-pdf', 'permintaankonsul_id'=>$permintaankonsul_id]),
                                        ['class'=>'btn btn-info btn-labeled btn-xs','target'=>'_blank']
                                    ); ?>
    </div>
    <?php
        ActiveForm::end();
    ?>
    <br>
</div>
<script>
    $(document).ready(function () {
        $('label[for="permintaankonsulform-jawaban_konsul"]').hide();
        // $("#persetujuan").select2 ('container').find ('.select2-search').addClass ('hidden') ; 
    });
    $("#btn-setuju").on("click", function(event) {
        event.preventDefault();
        var data = $("#jawaban-form").serializeArray();
        $(this).docoForm('click',{
            url: '/ranap/inf-pasien-konsul/jawaban',
            data: data,
            success : function(res) {
                var form = $("#jawaban-form");
                form[0].reset();
                table.draw();
                $("#modal_backdrop").modal('toggle');
                //_afterSave()           
            }
        });
    });

</script>
