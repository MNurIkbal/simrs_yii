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
</style>

<div class="content-group">
    <div class="panel-body bg-indigo-300 border-radius-top text-center" style="color:#000000!important;font-size:14px;">
        <a href="#" class="display-inline-block content-group-sm" style="margin-top:10px;">
            <img src="/media/img/icon-app/default.jpg" class="img-circle img-responsive"
            alt="" style="width: 110px; height: 110px;">
        </a>
        <div class="content-group-sm" style="margin-top:-20px;color:white;">
            <h6 class="text-semibold no-margin-bottom nama-pasien">
                -
            </h6>
            <span class="display-block rm-pasien">-</span>
        </div>
    </div>
    <ul class="nav nav-tabs nav-justified no-margin no-border-radius border-top border-top-teal-300">
        <li class="active">
            <a href="#info" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="true"><b><i class="fa fa-info-circle fa-lg"></i></b></a>
        </li>
        <li class="">
            <a href="#list-history" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="false"><b><i class="fa fa-book fa-lg"></i></b></a>
        </li>
        <li class="" id="ket-bpjs">
            <a href="#bpjs" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="false"><b><i class="fa fa-medkit fa-lg"></i></b></a>
        </li>
    </ul>
    <div class="tab-content panel-body" style="border: 1px solid #dddddd;background:#f2fdf7;">
        <div class="tab-pane fade active in" id="info">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-2"><span class="icon-jk" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 kelamin-pasien">&nbsp;-</div>
                </div>
                <div class="col-sm-12">
                    <div class="col-md-6">Tempat lahir</div>
                    <div class="col-md-6 tempat-lahir-pasien">:&nbsp;-</div>
                </div>
                <div class="col-sm-12">
                    <div class="col-md-6">Tanggal lahir</div>
                    <div class="col-md-6 tanggal-lahir-pasien">:&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-gd" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 darah-pasien">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-mom" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 ibu-pasien">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-tlp" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 tlp-pasien">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-address" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 alamat-pasien">&nbsp;-</div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="list-history">
            <div class="row">
                <div class="col-sm-12">
                    <h8 class="list-group-item-heading">
                        <center><b><u class="history-pendaftaran-id">-</u></b></center>
                    </h8>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-ins" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 instalasi">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-rg" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 ruangan">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-dr" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 dokter">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-in" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 masuk">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-out" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 keluar">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-ck" style="color:#1ca189;"></span></div>
                    <div class="col-md-10 cara-keluar">&nbsp;-</div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="bpjs">
            <div class="row">
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-no-kartu" style="color:#1ca189;"></span></div>
                    <div class="col-md-10" id="bpjsnew_detail_nokartu">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-nik" style="color:#1ca189;"></span></div>
                    <div class="col-md-10" id="bpjsnew_detail_nik">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-tgl-lahir" style="color:#1ca189;"></span></div>
                    <div class="col-md-10" id="bpjsnew_detail_tgl_lahir">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-jns-ps" style="color:#1ca189;"></span></div>
                    <div class="col-md-10" id="bpjsnew_detail_jenis_peserta">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-rg" style="color:#1ca189;"></span></div>
                    <div class="col-md-10" id="bpjsnew_detail_hak_kelas">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-tmt" style="color:#1ca189;"></span></div>
                    <div class="col-md-10" id="bpjsnew_detail_tmt_tat">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-kd" style="color:#1ca189;"></span></div>
                    <div class="col-md-10" id="bpjsnew_detail_ppk_rujukan">&nbsp;-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-2"><span class="icon-st" style="color:#1ca189;"></span></div>
                    <div class="col-md-6" id="bpjsnew_detail_status_peserta">&nbsp;-</div>
                </div>
            </div><hr>
            <div align="center">
            <button type="button" class="btn btn-detail-bpjs btn-info btn-labeled btn-xs" action="<?= Url::home() ?>pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=" data-toggle="modal" data-target="#modal_backdrop" data-width="90%" style="display:none"><b><i class="fa fa-eye"></i></b>History BPJS</button></div>
        </div>
    </div>
</div>
<div class="content-group" id="info-piutang" style="display:none">
    <div class="panel panel-danger">
        <div class="panel-heading text-uppercase text-semibold" style="text-align: center"><strong>Piutang Pasien</strong></div>
        <div class="panel-body" style="text-align: center">Rp. <b id="val-piutang"></b></div>
    </div>
</div>
<div class="content-group" id="info-catatan" style="display:none">
    <div class="panel panel-danger">
        <div class="panel-heading text-uppercase text-semibold" style="text-align:center;"><strong>Catatan Penting Pasien</strong></div>
        <div class="panel-body" style="text-align:left;"><b id="val-catatan"></b></div>
    </div>
</div>

<?php
    $this->registerJs(''.$this->render('js/pasien.js'));
?>