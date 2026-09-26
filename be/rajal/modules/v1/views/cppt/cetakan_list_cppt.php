<style type="text/css">
    ul {
        list-style: none;
    }
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
<table class="tbl-bordered">
    <thead>
        <tr>
            <th colspan="4" class="text-center"><?= Yii::t('app', 'SOAP') ?></th>
            <th colspan="3" class="text-center"><?= Yii::t('app', 'Terapi') ?></th>
            
        </tr>
        <tr>
            <th>No</th>
            <th><?= Yii::t('app', 'Ruangan/Profesi') ?></th>
            <th><?= Yii::t('app', 'Tanggal/Jam') ?></th>
            <th><?= Yii::t('app', 'Pengkajian Pasien S.O.A.P(Subjective,Objective,Analysis,Plan)') ?></th>
            <th><?= Yii::t('app', 'Tipe Instruksi') ?></th>
            <th><?= Yii::t('app', 'Catatan') ?></th>
            <th><?= Yii::t('app', 'Instruksi DPJP Termasuk Pasca Bedah') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $split_num = 10;
            $count_cppt = 0;
            foreach ($data as $v_data) {
                foreach ($v_data as $k_group => $v_group) {
                    foreach ($v_group as $val_cppt) {
                        $count_cppt++;
                    }
                }
            }
            $count_group =0;
            foreach ($data as $v_data) {
                foreach ($v_data as $k_group => $v_group) {
                    foreach ($v_group as $val_cppt) {?>
                        <tr>
                            <?php 
                            if($count_group % $split_num == 0){
                                $colp = $split_num;
                                if(($count_cppt - $count_group) < $split_num){
                                    $colp = ($count_cppt - $count_group);
                                }
                            ?>

                                <td rowspan="<?=$colp?>">1</td>
                                <td rowspan="<?=$colp?>"><?=$val_cppt['ruangan_nama'].'<br>'.$val_cppt['nama_profesi'].'<br>'.$val_cppt['nama_pegawai']?></td>
                                <td rowspan="<?=$colp?>"><?=date('d-m-Y / H:i:s',strtotime($val_cppt['tgl_soaprj']))?></td>
                                <?php
                                $html = '';
                                $html = '<ul>';

                                // Cek subjek
                                if (isset($val_cppt['subject']) && $val_cppt['subject'] != '') {
                                    // Set html
                                    $html .= '<li>';
                                    $html .= 'S';
                                    $html .= ':';
                                    $html .= ''.$val_cppt['subject'].'';
                                    $html .= '</li>';
                                }

                                // Cek objek
                                if (isset($val_cppt['object']) && $val_cppt['object'] != '') {
                                    // Set html
                                    $html .= '<li>';
                                    $html .= 'O';
                                    $html .= ':';
                                    $html .= ''.$val_cppt['object'].'';
                                    $html .= '</li>';
                                }

                                // Cek subject
                                if (($val_cppt['subject'] != '') && ($val_cppt['object'] != '') && ($val_cppt['planning'] != '')) {
                                    // Cek asesmen
                                    $html .= '<li>';
                                    $html .= 'A';
                                    $html .= ':';
                                    $html .= '';
                                    $html .= '</li>';
                                    if (isset($val_cppt['a_diag_utama']) && $val_cppt['a_diag_utama'] != '') {
                                        $diag_utama = json_decode($val_cppt['a_diag_utama'],TRUE);
                                        // Set html
                                        $html .= '<li>';
                                        $html .= ''.Yii::t('app', 'Diagnosa Utama').'';
                                        $html .= ':';
                                        $html .= ''.@$diag_utama['text'].'';
                                        $html .= '</li>';
                                    }

                                    // Cek asesmen
                                    if (isset($val_cppt['a_diag_penyerta']) && $val_cppt['a_diag_penyerta'] != '') {
                                        // Encode
                                        $diagnosaPenyerta = json_decode($val_cppt['a_diag_penyerta'],TRUE);

                                        // Cek diagnosa
                                        if ($diagnosaPenyerta != '') {
                                            // Inisialisasi counter
                                            $counter = 0;

                                            // Loop
                                            foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                                                // Cek counter
                                                if ($counter == 0) {
                                                    // Set html
                                                    $html .= '<li>';
                                                    $html .= ''.Yii::t('app', 'Diagnosa Penyerta').'';
                                                    $html .= ':';
                                                    $html .= '- '.@$valueDiagnosaPenyerta['text'].'';
                                                    $html .= '</li>';
                                                }
                                                else {
                                                    // Set html
                                                    $html .= '<li>';
                                                    $html .= '';
                                                    $html .= '';
                                                    $html .= '- '.@$valueDiagnosaPenyerta['text'].'';
                                                    $html .= '</li>';
                                                }

                                                // Plus the counter
                                                $counter++;
                                            }
                                        }
                                        else {
                                        }

                                    }
                                }

                                // Cek planning
                                if (isset($val_cppt['planning']) && $val_cppt['planning'] != '') {
                                    // Set html
                                    $html .= '<li>';
                                    $html .= 'P';
                                    $html .= ':';
                                    $html .= ''.$val_cppt['planning'].'';
                                    $html .= '</li>';
                                }

                                // Cek catatan dokter
                                if (isset($val_cppt['catatan_dokter']) && $val_cppt['catatan_dokter'] != '') {
                                    // Set html
                                    $html .= '<li>';
                                    $html .= Yii::t('app', 'Catatan Dokter');
                                    $html .= ':';
                                    $html .= ''.$val_cppt['catatan_dokter'].'';
                                    $html .= '</li>';
                                }

                                // Set end tag html
                                $html .= '</ul>';
                                ?>
                                <td rowspan="<?=$colp?>">
                                    <?=$html?>
                                </td>
                            <?php }
                            $count_group++;

                             ?>
                            <?php
                            $isFirst = FALSE; 
                                $v_tipe = '';
                                $v_tipe = $val_cppt['is_hapus'] == TRUE ? "<strike>".$val_cppt['grouping_tipe']."<br>".$val_cppt['jenis']."</strike>" : $val_cppt['grouping_tipe']."<br>".$val_cppt['jenis'];
                            ?>
                            <td><?=$v_tipe?></td>
                            <?php 
                                $v_catatan = '';
                                $v_catatan = $val_cppt['is_hapus'] == TRUE ? "<strike>".$val_cppt['catatan_dokterpengirim']."</strike>" : $val_cppt['catatan_dokterpengirim'];
                            ?>
                            <td><?=$v_catatan?></td>
                            <?php 
                                $v_instruksi = '';
                                $v_instruksi = $val_cppt['is_hapus'] == TRUE ? "<strike>".$val_cppt['instruksi']."</strike>" : $val_cppt['instruksi'];
                            ?>
                            <td><?=$v_instruksi?></td>
                        </tr>
                <?php }?>
            <?php }?>
        <?php }?>
    </tbody>
</table>