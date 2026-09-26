<?php 
use Doco\components\DocoHelpers;
?>
<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-size: 8px;
        font-family: 'Times New Roman';
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 4px;
    }
    
    .tbl-bordered td {
        border: 1px solid black;
        padding: 4px;
        font-family: 'Times New Roman';
    }

    thead td {
        text-align: center;
        font-weight: bold;
    }

    .rak-name {
        font-weight: bold;
        font-size: 12px;
        font-family: 'Times New Roman';
    }

    tr.border-head th { }
    tr.border-bottom td { }
</style>

<?php 
    $page = 1;
    if (count($detail)) :
        foreach ($detail as $key => $data) :
?>
    <?php
        if($page > 1) :
    ?>
        <div style="page-break-before:always" />
    <?php
        endif;
    ?>

    <p class="rak-name"><?php
        if($key == "") {
            echo "Tanpa Rak";
        } else {
            echo $key;
        }
    ?></p>
    <table class="tbl-bordered" cellpadding="1" cellspacing="1" style="width:100%; margin-bottom: 20px;">
        <thead>
            <tr class="border-head">
                <th>No</th>
                <th>Laci</th>
                <th>Kode Obat</th>
                <th>Nama obat alkes</th>
                <th>UoM</th>
                <th>In</th>
                <th>Out</th>
                <th>Stok Fisik 1</th>
                <th>Stok Fisik 2</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $no = 1;
                if (count($data)) :
                    foreach ($data as $value) :
            ?>
                        <tr class="<?= $no == count($data) ? "border-bottom" : "" ?>">
                            <td style="max-width: 4%; text-align:center"><?= $no ?></td>
                            <td style="max-width: 7%"><?= !is_null($value['laci']) ? $value['laci'] : 'Tanpa Rak' ?></td>
                            <td width="14%"><?= $value['obatalkes_kode'] ?></td>
                            <td width="26%"><?= $value['obatalkes_nama'] ?></td>
                            <td width="11%"><?= $value['uom'] ?></td>
                            <td width="7%"></td>
                            <td width="7%"></td>
                            <td width="8%"></td>
                            <td width="8%"></td>
                            <td style="max-width: 8%"><?= $value['satuanunit_nama'] ?></td>
                        </tr>
            <?php
                    $no++;
                    endforeach;
                else :
            ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Data kosong</td>
                </tr>
            <?php
                endif;
            ?>
        </tbody>
    </table>
    <br>
<?php
        $page++;
        endforeach;
    else :
?>
    <tr>
        <td colspan="4" style="text-align: center;">Data kosong</td>
    </tr>
<?php
    endif;
?>
