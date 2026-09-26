<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-28 15:31:40
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-28 16:53:10
 * @Description: 
 */

?>

<div>
    <center>
        <h3><?=$title?></h3>
    </center>
    <br />
    <table width="100%" border="1">
        <thead>
            <th><?=Yii::t('app', 'No');?></th>
            <th><?=Yii::t('app', 'Nama tindakan');?></th>
            <th><?=Yii::t('app', 'Nama obat alkes');?></th>
        </thead>
        <tbody>
        <?php
            $no = 1;
            $string = '';
            foreach ($data as $key => $value) {
                $string .= 
                    "<tr>"
                        ."<td>". $no ."</td>"
                        ."<td>". $value['daftartindakan_nama'] ."</td>"
                        ."<td>". $value['obatalkes_namalain'] ."</td>"
                    ."</tr>"
                ;
                $no++;
            }

            echo $string;
        ?>
        </tbody>
    </table>
</div>