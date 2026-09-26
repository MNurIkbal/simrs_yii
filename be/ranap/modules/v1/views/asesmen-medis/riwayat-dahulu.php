<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-26 16:08:18
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-09-13 11:12:05
 */

?>

<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<?php 
if(count($dataPenyakit) > 0):
?>

<table class="tbl-bordered">
    <thead>
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th>Tahun</th>
            <th>Penyakit</th>
            <th>Terapi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 0;
        foreach ($dataPenyakit as $key => $value) :
            $no++;
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$value['tahun']?></td>
                <td><?=$value['penyakit']?></td>
                <td><?=$value['terapi']?></td>
            </tr>
            <?php 
        endforeach;
        ?>
    </tbody>
</table>

<?php
endif;
?>
