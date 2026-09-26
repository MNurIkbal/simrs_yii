<?php
use yii\helpers\Html;
use app\components\DocoHelpers;
?>
<div class="row">
    <div class="col-md-12" id="konsultasi-row">
        <!-- <h6 class="text-label-size text-bold">Konsultasi</h6> -->

        <!-- <div class="form-group row" style="margin-top: 10px">
            <div class="col-md-12">
                <?= Html::activeTextArea($model, 'konsultasi', ['class' => 'form-control', 'rows' => 5]) ?>
            </div>
        </div> -->
        <!-- <table class="table table-bordered table-hover" id="resumemedis-table-konsultasi">
            <thead>
                <tr class="bg-inverse">
                    <th>Tanggal Konsul</th>
                    <th>Tanggal Selesai Konsul</th>
                    <th>Nama Dokter</th>
                    <th>Catatan Dokter</th>
                    <th>Hasil Konsul</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php /*
                if(!empty($consuleRecord)) {
                    foreach($consuleRecord as $index => $record) {
                        ?>
                        <tr>
                            <td><?=Html::textInput('konsultasirow['.$index.'][tgl_konsulpoli]', $record['tgl_konsulpoli'], ['class' => 'form-control input-select-date'])?></td>
                            <td><?=Html::textInput('konsultasirow['.$index.'][tgl_selesaikonsul]', $record['tgl_selesaikonsul'], ['class' => 'form-control input-select-date'])?></td>
                            <td><?=Html::textInput('konsultasirow['.$index.'][dok_mengkonsul]', $record['dok_mengkonsul'], ['class' => 'form-control'])?></td>
                            <td><?=Html::textInput('konsultasirow['.$index.'][catatan_dokter_konsul]', $record['catatan_dokter_konsul'], ['class' => 'form-control'])?></td>
                            <td><?=Html::textInput('konsultasirow['.$index.'][jawaban_konsul]', $record['jawaban_konsul'], ['class' => 'form-control'])?></td>
                            <td>
                                <button type="button" data-target="resumemedis-table-konsultasi" class="btn btn-danger btn-sm btn-delete-item"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr class="no-data-row">
                        <td colspan="6" class="text-center">Belum Ada Data</td>
                    </tr>
                    <?php
                }
                */?>
            </tbody>
            <tfoot>
                <tr>
                    <td><?=Html::textInput('tgl_konsulpoli', '', ['class' => 'form-control input-select-date', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('tgl_selesaikonsul', '', ['class' => 'form-control input-select-date', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('dok_mengkonsul', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('catatan_dokter_konsul', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('jawaban_konsul', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td>
                        <button type="button" style="margin-bottom: 10px" class="btn btn-sm btn-info btn-add-item" data-target="resumemedis-table-konsultasi" data-form="konsultasi-row"><i class="fa fa-plus"></i></button>
                    </td>
                </tr>
            </tfoot>
        </table> -->
    </div>
</div>
