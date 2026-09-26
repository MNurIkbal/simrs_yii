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
</style>

<div class="content-group">
    <div class="panel-body bg-indigo-300 border-radius-top text-center">
        <a href="#" class="display-inline-block content-group-sm" style="margin-top:10px;">
            <img src="/media/img/icon-app/default.jpg" class="img-circle img-responsive"
            alt="" style="width: 110px; height: 110px;">
        </a>
        <div class="content-group-sm" style="margin-top:-20px;color:white;">
            <h6 class="text-semibold no-margin-bottom namapasien">
                -
            </h6>
            <span class="display-block nomorrekammedik">-</span>
        </div>
    </div>
    <ul class="nav nav-tabs nav-justified no-margin no-border-radius border-top border-top-teal-300">
        <li class="active">
            <a href="#info" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="true"><b>SEP</b></a>
        </li>
        <li class="">
            <a href="#list-history" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="false"><b>PESERTA</b></a>
        </li>
    </ul>
    <div class="tab-content panel-body" style="border: 1px solid #dddddd;background:#f2fdf7;">
        <div class="tab-pane fade active in" id="info">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>No. SEP</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 nosep">-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>Tanggal SEP</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 tglsep">-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>Pelayanan</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 jenispelayanan">-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>Diagnosa</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 diagnosa">-</div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="list-history">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>No. BPJS</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 nokartubpjs">-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>Nama Peserta</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 namapeserta">-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>Tanggal Lahir</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 tanggallahir">-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>Jenis Kelamin</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 jeniskelamin">-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>Hak Kelas</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 hakkelas">-</div>
                </div>
                <div class="col-sm-12" style="margin-top:7px;">
                    <div class="col-md-4"><span style="color:#1ca189;"><b>PPK Asal</b></span></div>
                    <div class="col-md-1">&nbsp;:&nbsp;</div>
                    <div class="col-md-7 faskes">-</div>
                </div>
            </div>
        </div>
    </div>
</div>