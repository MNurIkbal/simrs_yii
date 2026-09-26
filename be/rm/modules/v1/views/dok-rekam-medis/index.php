<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-29 10:43:12
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-03-29 10:44:58
 */
?>
<table border="0" style="width: 50%">
	<?php 
	foreach ($filter as $key => $value) :
	?>
	<tr>
		<td>
			<?=$key?>
		</td>
		<td>
			<?=$value?>
		</td>
	</tr>
	<?php
	endforeach;
	?>
</table>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>No Rak</td>
            <td>No Sub Rak</td>
            <td>Nomor Rekam Medik</td>
            <td>Warna Dokumen Rekam Medik</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($detail as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= !empty($value['no_rak']) ? $value['no_rak'] : '' ?></td>
                <td><?= !empty($value['sub_rak']) ? $value['sub_rak'] : '' ?></td>
                <td><?= !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '' ?></td>
                <td><?= !empty($value['warnadokrm']) ? $value['warnadokrm'] : '' ?></td>
            </tr>
        <?php
            $no++;
            endforeach;

        ?>
    </tbody>
</table>
