<?php
    use yii\helpers\Html;
    use yii\web\View;
    use app\components\DocoHelpers;
?>

<div class="modal-header bg-inverse" id="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <input type="hidden" name="pendaftaran-id" value="<?=DocoHelpers::encrypt($pendaftaranId)?>" id="pendaftaran-id">
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    Pasien
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <b><?= $data['pasien']['no_rekam_medik'] . ' - ' . $data['pasien']['nama_pasien'] . ' - ' . ($data['pasien']['jenis_kelamin'] === 'L' ? ' Laki-laki' : 'Perempuan')?></b>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    Nomor Pendaftaran
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <b>
                        <?= $data['pasien']['no_pendaftaran'] ?> <br>
                        <?= $data['pasien']['tgl_pendaftaran'] ?>
                    </b>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    Dokter DPJP
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <b><?= str_replace('##', '</br>', $data['pasien']['dokter_dpjp']) ?></b>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <table class="table datatable-basic table-striped table-hover dataTable" id="tbl-riwayat-visit">
                <thead>
                    <tr class="bg-inverse">
                        <th width=20%>Tanggal Visit</th>
                        <th>Dokter Visit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2" class="text-center">Belum ada data CPPT yang diinputkan</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php 
    $this->registerJs($this->render('js/riwayat_visit_dokter.js'), View::POS_END);
?>