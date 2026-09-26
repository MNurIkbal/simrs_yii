<?php
use yii\helpers\Html;
?>
<div class="row">
    <div class="col-md-12" id="obatpulang-row">
        <h6 class="text-label-size text-bold">Obat - obatan yang dibawa pulang</h6>

        <div class="form-group row" style="margin-top: 10px">
            <div class="col-md-12">
                <?= Html::activeTextArea($model, 'obat_dibawa_pulang', ['class' => 'form-control', 'rows' => 5]) ?>
            </div>
        </div>
        <!-- <table class="table table-bordered table-hover" id="resumemedis-table-obatpulang">
            <thead>
                <tr class="bg-inverse">
                    <th>Racikan / Non Racikan</th>
                    <th>R Ke -</th>
                    <th>Nama Obat</th>
                    <th>Satuan Kecil</th>
                    <th>Signa</th>
                    <th>Qty</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php /* 
                if(!empty($takehomeMedicineRecord)){
                    foreach($takehomeMedicineRecord as $index => $record) {
                        ?>
                        <tr>
                            <td><?=Html::textInput('obatrsrow['.$index.'][racikan_nama]',$record['racikan_nama'], ['class' => 'form-control'])?></td>
                            <td><?=Html::textInput('obatrsrow['.$index.'][rke]', isset($record['rke']) ? $record['rke'] : '' , ['class' => 'form-control'])?></td>
                            <td><?=Html::textInput('obatrsrow['.$index.'][obatalkes_nama]',$record['obatalkes_nama'], ['class' => 'form-control'])?></td>
                            <td><?=Html::textInput('obatrsrow['.$index.'][satuan_kecil]',$record['satuan_kecil'], ['class' => 'form-control'])?></td>
                            <td><?php
                            $signa_text = '';
                            if(!empty($record['signa_nama'])){
                                $signa_text = $record['signa_nama'];
                            }else {
                                $signaDecode = !empty($record['signa']) ? json_decode($record['signa'], true) : '';
                                $signa_text = isset($signaDecode['text']) ? $signaDecode['text'] : '';
                            }
                            echo Html::textInput('obatrsrow['.$index.'][signa]', $signa_text , ['class' => 'form-control'])
                            ?></td>
                            <td><?=Html::textInput('obatrsrow['.$index.'][qty_reseptur]',$record['qty_reseptur'], ['class' => 'form-control doco-decimal'])?></td>
                            <td>
                                <button type="button" data-target="resumemedis-table-obatpulang" class="btn btn-danger btn-sm btn-delete-item"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr class="no-data-row">
                        <td colspan="7" class="text-center">Belum Ada Data</td>
                    </tr>
                    <?php
                }
                */?>
            </tbody>
            <tfoot>
                <tr>
                    <td><?=Html::textInput('racikan_nama', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('rke', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('obatalkes_nama', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('satuan_kecil', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('signa', '', ['class' => 'form-control', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('qty_reseptur', '', ['class' => 'form-control doco-decimal', 'style' => 'margin-bottom: 10px'])?></td>
                    <td>
                        <button type="button" style="margin-bottom: 10px" class="btn btn-sm btn-info btn-add-item" data-target="resumemedis-table-obatpulang" data-form="obatpulang-row"><i class="fa fa-plus"></i></button>
                    </td>
                </tr>
            </tfoot>
        </table> -->
    </div>
</div>