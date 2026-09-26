<table border="1" style="width: 100%">
	<tr>
		<th>Radiologi</th>
		<th>Laboratorium</th>
		<th>Diagnostik Lain</th>
	</tr>

    <tr>
            <td valign='top'>
	        <?php foreach ($data_hasilpemeriksaan['radiologi'] as $value) : ?>
                <?=$value?>
                <br>
    	    <?php endforeach; ?>
            </td>
            <td valign='top'>
	        <?php foreach ($data_hasilpemeriksaan['laboratorium'] as $value) : ?>
                <?=$value?>
                <br>
    	    <?php endforeach; ?>
            </td>
            <td valign='top'>
	        <?php foreach ($data_hasilpemeriksaan['lainnya'] as $value) : ?>
                <?=$value?>
                <br>
    	    <?php endforeach; ?>
            </td>
    </tr>
</table>