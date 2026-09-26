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
    padding: 0px;
    text-align: left;
  }
  tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }
  .data-makanan {
    padding: 25px;
    margin: 10px;
  }
</style> 
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th align="left"><?=\Yii::t("app", "Nama Jenis Diet");?></th>
                        <th><?=\Yii::t("app", "Nama Makanan");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td align="center"><?= $value['rowNum'] ?></td>
                            <td align="left"><?= $value['jenisdiet_nama'] ?></td>
                            <td align="left"><?= $value['makanandiet_nama'] ?></td>
                        </tr>
                    <?php
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>