<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
    }
    .bg-inverse th, .td-inverse td {
        border: 1px solid #000000;
        padding: 10px;
        text-align: left;
    }
  /* tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }   */
</style>
<table id="lap-pasien-rujuk-ranap" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th align="center" >RAWAT JALAN</th>
                <th align="center" >RAWAT DARURAT</th>
                <th align="center" >JUMLAH</th>
            </tr>
        </thead>
            <tbody>
                <tr class="td-inverse">
                    <td align="center"><?= $data['pasienrjkeri'] ?></td>
                    <td align="center"><?= $data['pasienrdkeri'] ?></td>
                    <td align="center"><?= $data['jumlah'] ?></td>
                </tr>
        </tbody>
</table>