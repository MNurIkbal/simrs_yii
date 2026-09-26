<?php

use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'kategori-obat-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'formConfig' => ['showErrors' => true, 'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
    'action' => '/master/kategori-obat/create'
]);
?>

<?php if ($id): ?>
    <?= Html::hiddenInput('restriction_obat_id', $id) ?>
<?php endif; ?>
<style type="text/css">
    .modal-open .modal {
        overflow-y: hidden !important;
    }

    .modal-body {
        height: 70%;
        max-height: 500px;
        overflow-y: auto;
        padding: 10px 15px;
    }

    .select2-container {
        z-index: 999999 !important;
    }

    .select2-results__group {
        font-weight: bold;
        padding: 4px 10px;
        color: #555;
        background: #f1f1f1;
    }

    .custom-dropdown-checkbox {
        position: relative;
        width: 100%;
        font-family: sans-serif;
        max-height: 320px;
        overflow-y: auto;
    }

    .custom-dropdown-checkbox .dropdown-toggle {
        width: 100%;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 8px 12px;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    .custom-dropdown-checkbox .child-list {
        list-style: none;
        padding: 5px 0 5px 30px;
        margin: 0;
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
        display: block;
        float: left;
        width: 100px;
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
        width: 100px;
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
        background-color: #F4C2C2;
        padding: 5px 0 5px 10px;
    }

    .section-flex {
        display: flex;
        padding: 5px;
    }

    .section-box {
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 8px;
        margin-bottom: 12px;
        background: #fff;
    }

    .section-box .control-label {
        padding-left: 0px !important;
        font-weight: bold;
        margin: auto auto 10px;
    }

    .custom-dropdown-checkbox .search-children {
        margin-left: 35px;
    }

    .custom-dropdown-checkbox .child-list {
        padding-left: 32px;
    }

    /* Additional compact styling */
    .form-group {
        margin-bottom: 8px;
    }

    .modal-header {
        padding: 8px 15px;
    }

    .modal-footer {
        padding: 8px 15px;
    }

    .panel-body {
        padding: 8px;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>

<div class="modal-body">
    <div class="col-md-12 section-flex">
        <div class="col-md-6">
            <?= $form->field($model, 'kategori', [
                'template' => '<div class="form-group row">{label}<div class="col-md-8">{input}{error}{hint}</div></div>',
                'labelOptions' => [
                    'class' => 'control-label col-md-3',
                    'style' => 'margin:6px auto;'
                ],
            ])->textInput(['class' => 'form-control input-sm']); ?>
        </div>
    </div>
    <div class="col-md-12 section-box">
        <div class="col-md-6">

            <?= $form->field($model, 'instalasi_id')->checkboxList($instalasi, [
                'itemOptions' => [
                    'class' => 'instalasi_id'
                ],
                'inline' => true,
            ])->label('Instalasi'); ?>

        </div>
    </div>
    <div class="col-md-12 section-box">
        <div class="col-md-12">
            <label class="control-label"><?= Yii::t('fe', 'Cara Bayar/Penjamin') ?> <span class="text-danger">*</span></label>
            <div class="row">
                <?php
                $totalItems = count($dataPenjaminSelect);
                $half = ceil($totalItems / 2);
                $chunks = array_chunk($dataPenjaminSelect, $half);
                ?>

                <?php foreach ($chunks as $chunk) : ?>
                    <div class="col-md-6">
                        <?php
                        echo $form->field($model, 'penjamin_id')->begin();
                        ?>
                        <div class="custom-dropdown-checkbox">
                            <?php foreach ($chunk as $group) : ?>
                                <div class="group-wrapper">
                                    <div class="parent-checkbox">
                                        <label>
                                            <input type="checkbox" class="parent-checkbox-input">
                                            <span class="parent-text"><?= "[ + ] " . Html::encode($group['text']) ?></span>
                                        </label>
                                    </div>
                                    <input type="text" class="search-children form-control input-sm" placeholder="Cari..." style="margin-bottom:6px; max-width: 90%; display: none;">
                                    <ul class="child-list" style="display: none;">
                                        <?php foreach ($group['children'] as $child) : ?>
                                            <li>
                                                <label>
                                                    <?php
                                                    $penjaminIds = [];
                                                    if (is_array($model->penjamin_id)) {
                                                        $penjaminIds = $model->penjamin_id;
                                                    } elseif (is_string($model->penjamin_id) && !empty($model->penjamin_id)) {
                                                        $decoded = json_decode($model->penjamin_id, true);
                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                            $penjaminIds = $decoded;
                                                        } else {
                                                            $penjaminIds = explode(',', $model->penjamin_id);
                                                        }
                                                    }
                                                    $isChecked = in_array($child['id'], $penjaminIds);
                                                    ?>
                                                    <?= Html::checkbox('KategoriObatForm[penjamin_id][]', $isChecked, ['value' => $child['id']]) ?>
                                                    <?= Html::encode($child['text']) ?>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php
                        echo $form->field($model, 'penjamin_id')->end();
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-12 section-box">
        <div class="col-sm-12">
            <div class="form-group">
                <label class="control-label text-left control-label col-sm-12">
                    <h4><strong>List Obat Alkes</strong></h4>
                </label>
            </div>
            <div class="form-group col-md-4 mt-1">
                <?= $form->field($model, 'obatalkes_id', [
                    'labelOptions' => ['class' => 'text-right']
                ])->dropDownList([], [
                    'class' => 'form-control select2 autoObat',
                    'id' => 'obatalkes_id',
                    'prompt' => 'Pilih Obat',
                    'tabindex' => '1'
                ])->label(false) ?>
            </div>
            <div class="form-group col-md-1">
                <button id="btn-add-obat" class="btn btn-sm btn-success" type="button">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
            <div class="col-md-6">
            </div>
        </div>
        <div class="col-md-12" style="margin-bottom: 8px;">
            <div class="col-md-6" style="float: right;">
                <div class='legend-header'>Keterangan</div>
            </div>
        </div>

        <div class="col-md-12" style="margin-bottom: 8px;">
            <div class="col-md-6" style="margin-top: 18px;">
                <div class="form-group" style="max-width:350px;">
                    <div class="input-group">
                        <input type="text" id="search-table-obat" class="form-control" placeholder="Search Obat">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class='legend-index'>
                    <div class="legend-wrapper">
                        <div class="legend-information">
                            <div class="legend-information__color" style="background-color: #ffc0cb"></div>
                            <div class="legend-information__text">Obat Tidak Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel-body">
                <table id="table-obat" class="table table-striped">
                    <thead>
                        <tr class="bg-inverse">
                            <th width=3%>No</th>
                            <th width=25%><?= \Yii::t("fe", "Kode"); ?></th>
                            <th width=25%><?= \Yii::t("fe", "Nama Obat"); ?></th>
                            <th width="12"><?= \Yii::t("fe", "Aksi"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="isi-table-obat">
                            <td class="text-center" colspan="4">
                                <?= \Yii::t("fe", "No data available in table."); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
    <?= Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'), ['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<?php
$existingObatData = [];
if (!empty($model->detail_obat)) {
    if (is_array($model->detail_obat)) {
        $existingObatData = $model->detail_obat;
    } elseif (is_string($model->detail_obat)) {
        $decoded = json_decode($model->detail_obat, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $existingObatData = $decoded;
        } else {
            $existingObatData = explode(',', trim($model->detail_obat));
            $existingObatData = array_filter($existingObatData);
        }
    }
}
?>
<script type="text/javascript">
    var existingObatIds = <?= json_encode($existingObatData) ?>;
    var isEditMode = <?= $id ? 'true' : 'false' ?>;
    var editId = <?= $id ? json_encode($id) : 'null' ?>;
</script>
<?php
$this->registerJs($this->render('form.js'), View::POS_END);
?>