<?php

use yii\web\View;
use app\components\DocoHelpers;
?>

<!-- Modal Header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" id="btn-close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Detail History Terapi</h5>
</div>

<!-- Modal Body -->
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table id="table_history_fisioterapi" class="table table-striped table-condensed table-hover" style="width: 100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th>Tanggal Rujukan</th>
                        <th>Dokter Perujuk</th>
                        <th>Jumlah Terapi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultData as $key => $value) : ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $value['tgl_permintaan'] ?></td>
                            <td><?= $value['nama_pegawai'] ?></td>
                            <td><?= $value['frekuensi'] ?></td>
                            <td>
                                <div>
                                    <button type="button" class="btn btn-info btn-labeled btn-xs btn-cetak-form"
                                        data-pendaftaran_id="<?= $value['pendaftaran_id'] ?>"
                                        data-tgl_permintaan="<?= date('Y-m-d', strtotime($value['tgl_permintaan'])) ?>"
                                        data-jam_permintaan="<?= date('H:i:s', strtotime($value['tgl_permintaan'])) ?>"
                                    >
                                        <b><i class="fa fa-print"></i></b>
                                        Cetak Klaim
                                    </button>
                                    <button type="button" class="btn btn-info btn-labeled btn-xs btn-cetak-history"
                                        data-pendaftaran_id="<?= $value['pendaftaran_id'] ?>"
                                        data-tgl_permintaan="<?= date('Y-m-d', strtotime($value['tgl_permintaan'])) ?>"
                                        data-jam_permintaan="<?= date('H:i:s', strtotime($value['tgl_permintaan'])) ?>"
                                    >
                                        <b><i class="fa fa-print"></i></b>
                                        Cetak History Terapi
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Javascript -->
<?php
$this->registerJs($this->render("js/modal_history_fisioterapi.js"), View::POS_END, "js");
?>