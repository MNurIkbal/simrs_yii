<?php

use yii\helpers\Html;
use yii\web\View;

?>
<style>
    .table-custom {
        border: none !important;
    }

    .table-custom>tbody>tr>td {
        border: none !important;
        border-color: white !important;
        padding: 5px !important;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title text-center"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="panel-toolbar clearfix">

    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 mb-">
                <form id="form-cek-pasien">
                    <div class="d-flex justify-content-between" style="display: flex; justify-content: space-between; align-items: center">
                        <div style="display: flex;">
                            <div style="margin-right: 10px;">
                                <b>No. Asuransi</b>
                                <?= Html::textInput('no_kartu', null, ['class' => 'form-control input-sm', 'placeholder' => 'Masukan no asuransi..', 'id' => 'no_kartu', 'required' => true]) ?>
                            </div>
                            <div>
                                <b>Penjamin</b>
                                <?= Html::dropDownList('penjamin_id', null, $penjamin, ['class' => 'form-control input-sm select2', 'id' => 'penjamin_id', 'prompt' => 'Pilih Penjamin']) ?>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex;">
                                <button type="button" class="btn btn-primary btn-xs btn-labeled" data-dismiss="modal"><b><i class="fa fa-arrow-left"></i></b> Kembali</button>
                                <button type="button" class="btn btn-primary btn-xs btn-labeled" id="btn-cari-pasien"><b><i class="fa fa-search"></i></b> Cari</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="mt-5">
                    <table class="table table-striped table-condensed table-hover bg-inverse">
                        <thead>
                            <tr>
                                <th>Identitas Pasien</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <table class="table-custom" style="border: none;">
                                <tr>
                                    <td>No Kartu</td>
                                    <td>: </td>
                                    <td><span id="no_kartu_peserta"></span></td>
                                </tr>
                                <tr>
                                    <td>Member ID</td>
                                    <td>: </td>
                                    <td><span id="member_id"></span></td>
                                </tr>
                                <tr>
                                    <td>No BPJS</td>
                                    <td>: </td>
                                    <td><span id="no_bpjs"></span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table-custom">
                                <tbody>
                                    <tr>
                                        <td>Nama Peserta</td>
                                        <td>: </td>
                                        <td><span id="nama_peserta"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Lahir</td>
                                        <td>: </td>
                                        <td><span id="tanggal_lahir"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Kelamin</td>
                                        <td>: </td>
                                        <td><span id="jenis_kelamin"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table-custom">
                                <tr>
                                    <td>Nama Perusahaan</td>
                                    <td>: </td>
                                    <td><span id="nama_perusahaan"></span></td>
                                </tr>
                                <tr>
                                    <td>Member VIP</td>
                                    <td>: </td>
                                    <td><span id="member_vip"></span></td>
                                </tr>
                                <tr>
                                    <td>No Polis</td>
                                    <td>: </td>
                                    <td><span id="no_polis"></span> </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="mt-5">
                    <table class="table table-striped table-condensed table-hover bg-inverse">
                        <thead>
                            <tr>
                                <th>Benefit Pasien</th>
                            </tr>
                        </thead>
                    </table>
                    <table class="table table-striped table-condensed table-hover">
                        <tbody class="data-benefit">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-left">

</div>

<?php
$this->registerJs($this->render('cek-pasien.js'), View::POS_END);
?>