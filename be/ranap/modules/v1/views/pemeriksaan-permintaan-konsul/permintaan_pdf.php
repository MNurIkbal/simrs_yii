<?php

/**
 * @Author: iqbal
 * @Date:   2018-07-23 
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

<table border="0" style="width: 90%">
    <tr>
            <td valign='top'>
	        <?php foreach ($header1 as $key => $value) : ?>
                <?php echo '<b>'.$key.'</b>'; ?>
                <br>
    	    <?php endforeach; ?>
            </td>
            <td valign='top'>
	        <?php foreach ($header1 as $key => $valhead1) : ?>
                <?php echo $valhead1; ?>
                <br>
    	    <?php endforeach; ?>
            </td>

            <td valign='top'>
	        <?php foreach ($header2 as $key => $value) : ?>
               <?php echo '<b>'.$key.'</b>'; ?>
                <br>
    	    <?php endforeach; ?>
            </td>
            <td valign='top'>
	        <?php foreach ($header2 as $key => $value) : ?>
                <?php echo $value; ?>
                <br>
    	    <?php endforeach; ?>
            </td>
    </tr>
</table>

<br>
<br>
<hr />
<table border="0" style="width: 70%">
    <tr>
        <td valign='top'>
        <?php foreach ($formPermintaan as $key => $value) : ?>
            <?php echo '<b>'.$key.'</b>'; ?>
            <br>
        <?php endforeach; ?>
        </td>
        <td valign='top'>
        <?php foreach ($formPermintaan as $key => $value) : ?>
            <?=$value?>
            <br>
        <?php endforeach; ?>
    </tr>
</table>