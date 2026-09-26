<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
?>

<style type="text/css">
    .modal-dialog {
        width: 75%;
    }
</style>

<div class="modal-header bg-inverse" id="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
        'id' => 'batal-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
    ]);
    ?>
    
    <table class="table table-condensed table-borderless">
        <tr>
            <td style="width: 15%;">No. Rekam Medik</td>
            <td style="width: 1%;">:</td>
            <th><?= $nomerRm ?></th>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td>:</td>
            <th><?= $namaPasien ?></th>
        </tr>
        <tr>
            <td>Tempat / Tgl Lahir</td>
            <td>:</td>
            <th><?= isset($tempatLahir) ? $tempatLahir : '-'; ?> / <?= $tanggalLahir ?></th>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <th><?= $jenisKelamin ?></th>
        </tr>
    </table>


    <div class="tabbable">
        <div class="nav-sticky-wrapper nav-sticky" id="nav-sticky">
        <div class="nav nav-tabs nav-tab-cppt" id="nav-tab" role="tablist">
            <a id="tab-skrining-rajal" class="nav-item nav-tab-type nav-link" data-toggle="tab" is-riwayat="<?= $is_riwayat ?>" href="#skrining-rajal" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">SKRINING PASIEN RAWAT JALAN</a>
            <a id="tab-skrining-covid" class="nav-item nav-tab-type nav-link" data-toggle="tab" is-riwayat="<?= $is_riwayat ?>" href="#skrining-covid" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">SKRINING PASIEN COVID</a>
            <a id="tab-asesment-informasi" class="nav-item nav-tab-type nav-link" data-toggle="tab" is-riwayat="<?= $is_riwayat ?>" href="#asesment-informasi" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">ASSESMENT INFORMASI PASIEN RAWAT JALAN</a>
        </div>

        <div class="tab-content">
            <div class="tab-pane" id="skrining-rajal">
                <div id="content-skrining-rajal"></div>
            </div>

            <div class="tab-pane" id="skrining-covid">
                <div id="content-skrining-covid"></div>
            </div>

            <div class="tab-pane" id="asesment-informasi">
                <div id="content-asesment-informasi"></div>
            </div>
        </div>
    </div>

    <div class="modal-footer" id="modal-footer">
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>

<?php 
$this->registerJs("
    var pendaftaran_id = '".$id."';
");
?>

<?php 
    $this->registerJs($this->render('js/_skriningpasien.js'));
?>

<script type="text/javascript">
    $("#batal-form").docoForm("submit",{
        success : function(data) {
            table.draw();
            $("#modal_backdrop").modal("toggle");
        },
    });
</script>
