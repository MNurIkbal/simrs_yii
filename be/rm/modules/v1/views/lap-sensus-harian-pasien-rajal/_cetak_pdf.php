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
  tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }  
</style>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table id="lap-sensus-harian-pasien-rajal" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th rowspan="4" width="1"  align="center">NO</th>
                            <th rowspan="4"  align="center">DEPARTMENT</th>
                            <th colspan="<?= $coljumlahpasien ?>" align="center">
                                JUMLAH PASIEN
                            </th>
                           <th colspan="2"  align="center">
                                JUMLAH
                           </th>
                            <th rowspan="4"  align="center">ADOA</th>
                            <th rowspan="4"  align="center">ADOAD</th>
                            <th rowspan="4"  align="center">ADOAPP</th>
                        </tr>
                        <tr class="bg-inverse">
                            <th colspan="<?= $colspanCounter ?>"  align="center">BARU</th>
                            <th rowspan="3">JUMLAH BARU</th>
                            <th colspan="<?= $colspanCounter ?>"  align="center">LAMA</th>
                            <th rowspan="3">JUMLAH LAMA</th>
                            <th rowspan="3"  align="center">Kunjungan</th>
                            <th rowspan="3"  align="center">Hp</th>
                        </tr>
                        <tr class="bg-inverse">
                        <?php 
                            $counter = 2;
                            for ($i=0; $i < $counter ; $i++) { 
                                foreach($carabayar as $k => $v) { ?>
                                    <th id="<? $k ?>" colspan="2" align="center"><?=$v;?></th>
                        <?php }
                            }
                         ?>
                        </tr>
                        <tr class="bg-inverse">
                            <?php 
                                for ($i=0; $i < $colspanCounter ; $i++) { ?>
                                    <th align="center"><?= $jenis_kelamin[0]['lookup_kode'] ?></th>
                                    <th align="center"><?= $jenis_kelamin[1]['lookup_kode'] ?></th>   
                            <?php   
                                }
                            ?>
                        </tr>
                    </thead>
                        <tbody>
                        <?php
                        foreach($data as $k => $v) { ?>
                        <tr class="td-inverse">
                        <?php for ($i=0; $i <  count($columns); $i++) { ?>
                                <td align="center"><?= $v[$columns[$i]];?></td>
                        <?php
                            } ?>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
        </div>
    </div>
</div>