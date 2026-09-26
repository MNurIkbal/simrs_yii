<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use kartik\select2\Select2;
    use kartik\typeahead\Typeahead;
    use yii\web\JsExpression;
    use yii\web\View;
    use app\components\DocoConstants;
?>
<style>
    .bg-indigo-300 {
        background-color: #1ca189 !important;
    }

    .icon-jk {
        background: url(../media/img/icon-pendaftaran/jk.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-gd {
        background: url(../media/img/icon-pendaftaran/gd.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-mom {
        background: url(../media/img/icon-pendaftaran/mom.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-tlp {
        background: url(../media/img/icon-pendaftaran/phone.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-address {
        background: url(../media/img/icon-pendaftaran/address.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-ins {
        background: url(../media/img/icon-pendaftaran/ins.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-rg {
        background: url(../media/img/icon-pendaftaran/rg.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-dr {
        background: url(../media/img/icon-pendaftaran/dr.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-in {
        background: url(../media/img/icon-pendaftaran/in.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-out {
        background: url(../media/img/icon-pendaftaran/out.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-ck {
        background: url(../media/img/icon-pendaftaran/ck.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-no-kartu {
        background: url(../media/img/icon-pendaftaran/no_kartu.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-nik {
        background: url(../media/img/icon-pendaftaran/nik.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-tgl-lahir {
        background: url(../media/img/icon-pendaftaran/tgl_lahir.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-jns-ps {
        background: url(../media/img/icon-pendaftaran/jns_ps.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-tmt {
        background: url(../media/img/icon-pendaftaran/tmt.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-kd {
        background: url(../media/img/icon-pendaftaran/kd.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .icon-st {
        background: url(../media/img/icon-pendaftaran/st.png);
        height: 20px;
        width: 20px;
        display: block;
    }

    .info-detail {
        margin-top: 7px;
    }
</style>

<div class="content-group">
    <div class="panel-body bg-indigo-300 border-radius-top text-center" style="color: #000000!important; background-color: #49ce8e6b!important">
        <!-- <a href="#" class="display-inline-block content-group-sm" style="margin-top:10px;">
            <img src="/media/img/icon-app/default.jpg" class="img-circle img-responsive"
            alt="" style="width: 110px; height: 110px;">
        </a> -->
        <div class="content-group-sm" style="margin-top:20px">
            <h6 class="text-semibold no-margin-bottom nama-pasien">
                -
            </h6>
            <span class="display-block rm-pasien">-</span>
        </div>
    </div>
    <ul class="nav nav-tabs nav-justified no-margin no-border-radius bg-teal-400 border-top border-top-teal-300">
        <li class="active" id="ket-bpjs" title="Info BPJS">
            <a href="#info-pasien" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="true"><b>Info BPJS</b></a>
        </li>
    </ul>
    <div class="tab-content panel-body" style="border: 1px solid #dddddd;background:#f2fdf7;">
        <div class="tab-pane fade active in" id="info-pasien">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-5">No. Kartu</div>
                    <div class="col-md-7 info-no_kartu" id="bpjsnew_detail_nokartu">:&nbsp;-</div>
                </div>
                <div class="col-sm-12 info-detail">
                    <div class="col-md-5">NIK</div>
                    <div class="col-md-7 info-nik">:&nbsp;-</div>
                </div>
                <div class="col-sm-12 info-detail">
                    <div class="col-md-5">Nama Peserta</div>
                    <div class="col-md-7 info-nama_peserta">:&nbsp;-</div>
                </div>
                <div class="col-sm-12 info-detail">
                    <div class="col-md-5">Tanggal Lahir</div>
                    <div class="col-md-7 info-tgl_lahir">:&nbsp;-</div>
                </div>
                <div class="col-sm-12 info-detail">
                    <div class="col-md-5">Jenis Peserta</div>
                    <div class="col-md-7 info-jenis_peserta">:&nbsp;-</div>
                </div>
                <div class="col-sm-12 info-detail">
                    <div class="col-md-5">Hak Kelas</div>
                    <div class="col-md-7 info-hak_kelas">:&nbsp;-</div>
                </div>
                <div class="col-sm-12 info-detail">
                    <div class="col-md-5">TMT/TAT</div>
                    <div class="col-md-7 info-tmt_tat">:&nbsp;-</div>
                </div>
                <div class="col-sm-12 info-detail">
                    <div class="col-md-5">Kode/Provinsi</div>
                    <div class="col-md-7 info-ppk_peserta">:&nbsp;-</div>
                </div>
                <div class="col-sm-12 info-detail">
                    <div class="col-md-5">Status Peserta</div>
                    <div class="col-md-7 info-status">:&nbsp;-</div>
                </div>
                <div class="col-sm-12">
                    <hr style="margin-top:20px;margin-bottom:10px;">
                    <div align="center">
                        <button type="button" class="btn btn-detail-bpjs btn-info btn-labeled btn-xs" action="<?= Url::home() ?>pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=" data-toggle="modal" data-target="#modal_backdrop" data-width="90%" ><b><i class="fa fa-eye"></i></b>History BPJS</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>