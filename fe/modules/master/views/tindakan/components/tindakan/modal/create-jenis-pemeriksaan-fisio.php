<?php
/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */
use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;

?>
<style>
    .row{
        padding: 5px;
    }
    .wrapper-catatan p {
        display: inline;
        word-break: break-word;
    }.required-cust::after{
        content: "*";
        margin-left: 3px;
        font-weight: normal;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        color: tomato;
    }.content-right{
        float: right;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Tambah Jenis Pemeriksaan Fisioterapi</h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-3">
            <label class="control-label required-cust">Kode Jenis Pemeriksaan</label>
        </div>
        <div class="col-md-6">
            <?=
                Html::textInput(
                    'pemeriksaan-kode', 
                    '', 
                    [
                        'class' => 'form-control pemeriksaan-kode',
                    ]); 
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label class="control-label required-cust">Nama Jenis Pemeriksaan</label>
        </div>
        <div class="col-md-6">
            <?=
                Html::textInput(
                    'pemeriksaan-nama', 
                    '', 
                    [
                        'class' => 'form-control pemeriksaan-nama',
                    ]); 
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label class="control-label required-cust">Nama Lain Jenis Pemeriksaan</label>
        </div>
        <div class="col-md-6">
            <?=
                Html::textInput(
                    'pemeriksaan-namaLain', 
                    '', 
                    [
                        'class' => 'form-control pemeriksaan-namaLain',
                    ]); 
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label class="control-label required-cust">Kelompok Pemeriksaan</label>
        </div>
        <div class="col-md-6">
            <?=
                Html::dropDownList(
                    'pemeriksaan-kelompok', 
                    '', 
                    ArrayHelper::map($fisioAttributes['kelompokPemeriksaan'], 'kelompokpemeriksaanfisio_id', 'nama_kelompok'),
                    [
                        'class' => 'form-control select2 pemeriksaan-kelompokPemeriksaan',
                        'prompt' => 'Pilih'
                    ]); 
            ?>
            <p class="text-error" id="pelor-kel" style="color: red; display:none">Kelompok pemeriksaan tidak boleh kosong.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label class="control-label">Catatan</label>
        </div>
        <div class="col-md-6">
            <?=
                Html::textarea(
                    'pemeriksaan-catatan', 
                    '', 
                    [
                        'class' => 'form-control pemeriksaan-catatan',
                    ]); 
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label class="control-label">Status</label>
        </div>
        <div class="col-md-6">
            <?=
                Html::checkbox(
                    'pemeriksaan-status', 
                    true, 
                    [
                        'id' => 'pemeriksaan-status',
                        'label' => 'Aktif'
                    ]); 
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 content-right" style="margin-bottom: 0px;">
            <div class="simpan-jenis">
                <button type="button" style="margin-bottom: 0px;" id="btn-simpan" class="content-right btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-save"></i></b>Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php
// $this->registerJsVar('details', $details);
$this->registerJs($this->render('js/create-jenis-pemeriksaan-fisio.js'), View::POS_END);
?>