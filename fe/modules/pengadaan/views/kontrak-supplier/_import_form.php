<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
    use kartik\widgets\ActiveForm;
?>
<style type="text/css">
    .modal-dialog {
        /*w*/idth: 65% !important;
        margin: 30px auto;
    }
    button#button-back {
        height: 30px;
        padding-top: 5px;
    }

    #file-upload {
        display:none;
        margin: 10px;
    }
    .inputfile + label {
        max-width: 100%;
        font-size: 1.25rem;
        /* 20px */
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        display: inline-block;
        overflow: hidden;
        /* padding: 0.625rem 1.25rem; */
        padding: 7px 25px;
        width: auto;
        /* 10px 20px */
    }
    .no-js .inputfile + label {
        display: none;
    }

    .inputfile:focus + label,
    .inputfile.has-focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }

    .inputfile + label * {
        /* pointer-events: none; */
        /* in case of FastClick lib use */
    }

    .inputfile + label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        /* 4px */
        margin-right: 0.25em;
        /* 4px */
    }


    .inputfile-1 + label {
        color: #f1e5e6;
        background-color: #54be8b;
        
    }

    .inputfile-1:focus + label,
    .inputfile-1.has-focus + label,
    .inputfile-1 + label:hover {
        background-color: #722040;
    }

    .lurus {
        float: left;
        margin-left: 20px;
    }
    .highlight {
      background-color: #FF9;
    }
    /*td:last-child {  
      background-color: #FF9;    
    }*/
    .dataTables_wrapper {
        position: relative;
        clear: both;
        padding-top: 20px;
    }
    .format-upload{
        font-size: 12px;
        text-align: left;
        width: 14%;
        font-style: italic;
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
    width: 100%;
    }
  .my-legend .legend-scale ul li {
    display: inline-grid;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: left;
    font-size: 98%;
    list-style: none;
    width: 40%;
    font-style: italic;
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
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title;?></h5>
</div>
<div class="modal-body">
    <div class="panel">
        <div class="panel-toolbar clearfix">
             <?=
             DocoHelpers::generateToolbar([
                "back"=> [
                        'attributes'=>[
                            'id' => 'btn-back',
                        ]
                    ],
                'save' => [
                    'title' => \Yii::t('fe', 'Upload'),
                    'attributes' => [
                        'id' => 'upload-kontrak-supplier',
                    ]
                ],
            ], "#tmp-kontrak");
            ?>
        </div>
    </div>
    <?php $form = ActiveForm::begin([
            'id' => 'upload-kontrak-supplier-form',
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false, 
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 4, 
                'deviceSize' => ActiveForm::SIZE_MEDIUM
            ],
            'options' => [
                'skip-confirm' => "true"
            ]
        ]);
    ?>
    <div class="clear"><br></div>
    <div class="form-group">
        <div class="col-md-2">
            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Pilih File'); ?></label>
        </div>
        <div class="col-md-10">
            <input type="file" name="UploadForm[upload_file]" id="file-upload" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected" >
            <label for="file-upload">
                <i class="fa fa-upload"></i>
                <span id="label-file">Pilih Berkas</span>
            </label>
            <p class="format-upload">format :xls, xlsx</p>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
               <div class="progress">
                    <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                </div>
                <div class="msg"></div>
        </div>
    </div>
    <div class="row">
        <div class="row">
            <div class="col-md-12 filter-form"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 filter-form"></div>
    </div>
    <table id="tmp-kontrak" class="table table-striped table-condensed table-hover" style="width:100%;padding-top: 10px;">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th ><?=\Yii::t("fe", "Nomor Kontrak Supplier");?></th>
                <th ><?=\Yii::t("fe", "Tgl Berlaku (DD/MM/YYYY)");?></th>
                <th ><?=\Yii::t("fe", "Kode Supplier");?></th>
                <th ><?=\Yii::t("fe", "Nama Supplier");?></th>
                <th ><?=\Yii::t("fe", "Payterm");?></th>
                <th ><?=\Yii::t("fe", "PPN (%)");?></th>
                <th ><?=\Yii::t("fe", "Contact Person");?></th>
                <th ><?=\Yii::t("fe", "Kode Obat");?></th>
                <th ><?=\Yii::t("fe", "Nama Obat");?></th>
                <th ><?=\Yii::t("fe", "Harga Order");?></th>
                <th ><?=\Yii::t("fe", "Pengurang");?></th>
                <th ><?=\Yii::t("fe", "Qty Min");?></th>
                <th ><?=\Yii::t("fe", "Total Harga");?></th>
                <th ><?=\Yii::t("fe", "Keterangan");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="16"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12">
            <div class='my-legend'>
                <div class='legend-title'>Peringatan :</div>
                <div class='legend-scale'>
                    <ul class='legend-labels'>
                        <li><span style='background:#F6C1C1;'></span>
                            <p>Data tidak valid dan tidak akan tersimpan</p>
                            <p>Terdapat salah satu kolom mandatory yang tidak diisi/Kode obat tidak sesuai/Kode supplier tidak sesuai</p>
                        </li>
                    </ul>
                </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php 
$this->registerJs('
    var _tableObat;
    var no_urut = 0;
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/_import_form.js'));
?>