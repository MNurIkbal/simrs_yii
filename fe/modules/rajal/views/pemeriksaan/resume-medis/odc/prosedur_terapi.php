<?php
use yii\helpers\Html;
use dosamigos\ckeditor\CKEditor;
?>
<div class="row">
    <div class="col-md-12" id="terapi-row">
        <h6 class="text-label-size text-bold">Lain Lain:</h6>
        <div class="form-group row" style="margin-top: 10px">
            <div class="col-md-12">
                <?= Html::activeTextArea($model, 'lain_lainnya', ['class' => 'form-control', 'rows' => 5]) ?>
            </div>
        </div>

        <h6 class="text-label-size text-bold">Tindakan Medis & Obat-Obatan/Terapi Selama di Rumah Sakit :</h6>
        <button type="button" data-type="tindakan_prosedur" id="search-prosedur-btn" class="btn btn-info btn-labeled btn-xs search-penunjang" style="margin-top:10px;margin-bottom:10px;"><b><i class="fa fa-search"></i></b> Tindakan/Prosedur</button>
        <div class="form-group row" style="margin-top: 10px">
            <div class="col-md-12">

        <?= $form->field($model, 'prosedur',[
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->widget(CKEditor::className(), [
                                'options' => ['rows' => 5],
                                'preset' => 'custom',
                                'clientOptions'=>[
                                    'toolbarGroups'=>[
                                        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                                        ['name' => 'colors'],
                                    ]
                                ]
                            ])->label(false) ?>
            </div>
        </div>
        <h6 style="margin-left: 3px;">Prosedur Terapi</h5>
        <div class="form-group row">
            <div class="col-md-12" id="terapi-row">
                <?= $form->field($model, 'instruksi_tindakanbmhp')->textArea(['rows' => 5])->label(false); ?>
            </div>
        </div>
        <!-- <table class="table table-bordered table-hover" id="resumemedis-table-terapi">
            <thead>
                <tr class="bg-inverse">
                    <th>Nama Tindakan / Paket</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php /*
                if( !empty($actRecord) ) {
                    foreach($actRecord as $item => $record) {
                    ?>
                    <tr>
                        <td><?=Html::textInput('terapirow['.$item.'][tindakan_paket_obat]',$record['tindakan_paket_obat'], ['class' => 'form-control'])?></td>
                        <td><?=Html::textInput('terapirow['.$item.'][qty]',$record['qty'], ['class' => 'form-control doco-decimal'])?></td>
                        <td>
                            <button type="button" data-target="resumemedis-table-terapi" class="btn btn-danger btn-sm btn-delete-item"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr class="no-data-row">
                        <td colspan="3" class="text-center">Belum Ada Data</td>
                    </tr>
                    <?php
                }
                */?>
            </tbody>
            <tfoot>
                <tr>
                    <td><?=Html::textInput('tindakan_paket_obat', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('qty', '', ['class' => 'form-control doco-decimal', 'style' => 'margin-bottom: 10px'])?></td>
                    <td>
                        <button type="button" style="margin-bottom: 10px" class="btn btn-sm btn-info btn-add-item" data-target="resumemedis-table-terapi" data-form="terapi-row"><i class="fa fa-plus"></i></button>
                    </td>
                </tr>
            </tfoot>
        </table> -->
    </div>
</div>
