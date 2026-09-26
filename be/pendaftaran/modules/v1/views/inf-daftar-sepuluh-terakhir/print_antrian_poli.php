<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    body{
        font-family: tahoma;
    }
    .fz-9 {
        font-size: 9px;
    }
    .fz-10 {
        font-size: 10px;
    }
    .fz-11 {
        font-size: 11px;
    }
    .fz-12 {
        font-size: 12px;
    }
    .fz-13 {
        font-size: 13px;
    }
    .fz-24 {
        font-size: 24px;
    }
    .fz-32 {
        font-size: 32px;
    }
    .fw-b {
        font-weight: bold;
    }

    .header-label1 {
        font-size:13px; font-weight: bold; border-right: 0
    }
    .header-label2 {
        font-size:10px; font-weight: bold; border-right: 0
    }
    .bg-inverse th {
        padding: 0px;
        text-align: center;
    }
    .td-inverse td {
        padding: 0px;
        text-align: left;
    }
</style>
<div>
  <?php foreach($datas as $key => $data){ ?>

    <table class="table table-striped table-condensed table-hover" style="width:100%;margin: 1px;" border="0">
        <thead>
            <tr class="bg-inverse">
                <th width="100%"  align="center" class="header-label2 fz-10">
                    &nbsp;
                    <br>
                    <br>
                    <br>
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="100%"  align="center" class="header-label2 fz-24">
                    <?php echo @$data['nama_ruangan'] ?>
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="100%"  align="center" class="header-label2 fz-10">
                    &nbsp;
                    <br>
                    <br>
                    <br>
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="100%"  align="center" class="header-label2 fz-10">
                    &nbsp;
                </th>
            </tr>
            <tr>
                <th width="100%"  align="center" class="header-label2 fz-24">
                    Antrian : <br>
                </th>
            </tr>
            <tr>
                <th width="100%"  align="center" class="header-label2 fz-32">
                    <?php echo @$data['no_antrian'] ?>
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="100%"  align="center" class="header-label2 fz-10">
                    &nbsp;
                    <br>
                    <br>
                    <br>
                </th>
            </tr>
            <tr>
                <th width="100%"  align="center" class="header-label2 fz-13">
                    <?php echo @$data['no_rekam_medik'] ?>
                </th>
            </tr>
            <tr>
                <td width="100%"  align="center" class="fz-9">
                    <?php echo @$data['tgl_pendaftaran'].' '.@$data['jam_kunjungan']# ?>
                </td>
            </tr>
        </thead>
    </table>
  <?php }?>
</div>
