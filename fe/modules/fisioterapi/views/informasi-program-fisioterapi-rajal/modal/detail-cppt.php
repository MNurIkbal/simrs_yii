<?php
use yii\web\View;
use yii\helpers\ArrayHelper;
use app\widgets\fisioterapi\DHHeaderProgramTerapi;
?>
<style>
    .table-left td{
        border-left: none !important;
        border-right: none !important;
    }
    .table-left th{
        border: none !important;
    }
    .table-right td{
        border-left: none !important;
        border-right: none !important;
    }
    .table-right th{
        border: none !important;
    }
    .modal-body{
        margin-top: -10px;
    }
    .content-right{
        margin-bottom : 20px;
    }
    .content-table .dataTables_wrapper .dataTables_scroll{
        overflow-x: hidden;
    }
    
    .content-table .dataTables_wrapper .dataTables_scroll{
        border: 0.1px solid #bbb;
    }
    .kv-datetime-remove{
        display: none !important;
    }
    .strike-text {
        text-decoration: line-through;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">CPPT</h5>
</div>
<div class="modal-body">
    <?php 
        echo DHHeaderProgramTerapi::widget([
            'id' => $id,
            'data' => $data,
            'scheduledetailDoctor' => $scheduledetailDoctor,
        ]);
    ?>
    <div class="row content-table" style="margin-top: 20px !important">
        <div class="col-sm-12">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h5 class="panel-title">CPPT</h5>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered" id="tb-cppt-detail" style="width:100%;">
                        <thead>
                            <tr class="bg-inverse">
                                <th colspan="4" class="text-center">SOAP / Verbal Order</th>
                            </tr>
                            <tr class="bg-inverse">
                                <th width="8px">No</th>
                                <th>Ruang / Tanggal dan Jam / Profesi</th>
                                <th>Hasil Asesmen Penatalaksanaan Pasien</th>
                                <th>Instruksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($cpptPatient)): ?>
                                <?php $no = 1; ?>
                                <?php 
                                    foreach ($cpptPatient as $key => $value):
                                    $isEdit = ArrayHelper::getValue($value, 'is_edit');
                                    $isCoret = $isEdit ? 'strike-text' : '';
                                ?>
                                    <tr>
                                        <td class="<?= $isCoret ?>"><?= $no ?></td>
                                        <td class="<?= $isCoret ?>">
                                            <?= htmlspecialchars(ArrayHelper::getValue($value, 'ruangan_nama')) ?>
                                            <br>
                                            <?= htmlspecialchars(ArrayHelper::getValue($value, 'tgl_soapfisioterapi')) ?>
                                            <br>
                                            <?= htmlspecialchars(ArrayHelper::getValue($value, 'terapis_nama')) ?>
                                        </td>
                                        <td class="<?= $isCoret ?>">
                                            <strong>Subjektif:</strong>
                                            <br>
                                            <?= htmlspecialchars(ArrayHelper::getValue($value, 'subject')) ?>
                                            <br>
                                            <br>
                                            <strong>Objektif :</strong>
                                            <br>
                                            <?= htmlspecialchars(ArrayHelper::getValue($value, 'object')) ?>
                                            <br>
                                            <br>
                                            <strong>Assesment:</strong>
                                            <br>
                                            <b>Diagnosa Utama :</b> 
                                            <br/>
                                            <?php
                                                $aDiagUtama = json_decode(ArrayHelper::getValue($value, 'a_diag_utama'), true);
                                                echo htmlspecialchars(ArrayHelper::getValue($aDiagUtama, 'text'));
                                            ?>
                                            <br/>
                                            <b>Diagnosa Penyerta :</b>
                                            <br/>
                                            <?php
                                                $aDiagPenyerta = json_decode(ArrayHelper::getValue($value, 'a_diag_penyerta'), true);
                                                for ($i = 0; $i < count($aDiagPenyerta); $i++) {
                                                    echo htmlspecialchars(ArrayHelper::getValue($aDiagPenyerta, "$i.text"));
                                                    echo '<br/>';
                                                }
                                            ?>
                                            <br/>
                                            <strong>Planning:</strong>
                                            <br>
                                            <?= htmlspecialchars(ArrayHelper::getValue($value, 'planning')) ?>
                                            <br>
                                            <br>
                                            <strong>Catatan Terapis :</strong>
                                            <br>
                                            <?= htmlspecialchars(ArrayHelper::getValue($value, 'catatan_dokter')) ?>
                                            <br>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars(ArrayHelper::getValue($value, 'instruksi')) ?>
                                        </td>
                                    </tr>
                                <?php $no++; ?>
                                <?php endforeach ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="3"><center>Data Kosong</center></td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs($this->render('js/detail-cppt.js'), View::POS_END);
?>