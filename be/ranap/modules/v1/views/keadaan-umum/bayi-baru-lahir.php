<?php

use Doco\components\DocoHelpers;

?>
<style type="text/css">
    .tbl-no-border {
        border-collapse: collapse;
    }

    .tbl-no-border th {
        border: 0px;
    }
    .tbl-no-border td {
        border: 0px;
    }
</style>

<?php 
    foreach ($detail as $value) :
        $penilaian = '-';
        if (isset($cache['penilaian'][$value['penilaian']])) {
            $penilaian = $cache['penilaian'][$value['penilaian']];
        }

        $kondisi_bayi = isset($cache['kondisi_bayi'][$value['kondisi_bayi']]) ? '<i>' . $cache['kondisi_bayi'][$value['kondisi_bayi']] . '</i>' : null;
        switch ($value['kondisi_bayi']) {
            case 85:
                $normal_tindakan = [];
                if (!empty($value['normal_tindakan'])) {
                    $nt = json_decode($value['normal_tindakan'],true);
                    foreach ($nt as $val) {
                        $normal_tindakan[] = isset($cache['bayinormal_tindakan'][$val]) 
                                                    ? $cache['bayinormal_tindakan'][$val] : null;
                    }
                }
                $normal_tindakan = implode(",", $normal_tindakan);

                $asfiksia = '';
                if (isset($cache['asfiksia'][$value['asfiksia']])) {
                    $asfiksia = $cache['asfiksia'][$value['asfiksia']];
                }

                $asfiksia_tindakan = [];
                if (!empty($value['asfiksia_tindakan'])) {
                    $nt = json_decode($value['asfiksia_tindakan'],true);
                    foreach ($nt as $val) {
                        $asfiksia_tindakan[] = isset($cache['asfiksia_tindakan'][$val]) 
                                                    ? $cache['asfiksia_tindakan'][$val] : null;
                    }
                }

                $asfiksia_tindakan = implode(",", $asfiksia_tindakan);

                $kondisi_bayi .= '<ul>';
                     $kondisi_bayi .= '<li>Tindakan : '. $normal_tindakan .'</li>';
                     $kondisi_bayi .= '<li>Asfiksia : '. $asfiksia .'</li>';
                     $kondisi_bayi .= '<li>Tindakan : '. $asfiksia_tindakan .'</li>';
                $kondisi_bayi .= '</ul>';
                break;
            case 86:
                $kondisi_bayi .= ', ' . $value['keterangan_cacat'];
                break;
            case 87:
                $ketHipoter = '<ul>';
                $hipoter = [];
                if (!empty($value['keterangan_hipotermi'])) {
                    $hipoter = json_decode($value['keterangan_hipotermi'],true);
                }

                foreach ($hipoter as $val) {
                    $ketHipoter .= '<li>'. $val .'</li>';
                }
                $ketHipoter .= '</ul>';
                $kondisi_bayi .= $ketHipoter;
                break;
            default:
                $kondisi_bayi = '-';
                break;
        }
?>
    <br>
    <table width="100%" class="tbl-no-border" style="vertical-align: top;">
        <tbody >
            <tr>
                <td style="width:50%"><strong>Berat Badan</strong></td>
                <td style="width:1%">:</td>
                <td  style="width:49%"><?= $value['berat_badan'] ?> Gram</td>
            </tr>
            <tr>
                <td><strong>Panjang Badan</strong></td>
                <td>:</td>
                <td><?= $value['tinggi_badan'] ?> Cm</td>
            </tr>
            <tr>
                <td><strong>Tanggal Lahir</strong></td>
                <td>:</td>
                <td><?= $value['tgl_lahir'] ?></td>
            </tr>
            <tr>
                <td><strong>Jenis Kelamin</strong></td>
                <td>:</td>
                <td><?= $value['jenis_kelamin'] == 15 ? 'Laki-laki' : 'Perempuan' ?></td>
            </tr>
            <tr>
                <td><strong>Penilaian</strong></td>
                <td>:</td>
                <td><?= $penilaian ?></td>
            </tr>
            <tr>
                <td><strong>Kondisi Bayi Lahir</strong></td>
                <td>:</td>
                <td><?= $kondisi_bayi ?></td>
            </tr>
            <tr>
                <td><strong>Pemberian ASI setelah</strong></td>
                <td>:</td>
                <td><?php
                    $is_asi = '';
                    if (!is_null($value['is_asi'])) {
                        if ($value['is_asi']) {
                            $is_asi = 'Ya, ' . $value['keterangan_asi'] . ' jam setelah bayi lahir';
                        } else {
                            $is_asi = 'Tidak, ' . $value['keterangan_asi'];
                        }
                    }
                    echo $is_asi;
                ?></td>
            </tr>
            <tr>
                <td><strong>jam pertama bayi lahir</strong></td>
                <td>:</td>
                <td><?= $value['masalah_lain'] ?></td>
            </tr>
            <tr>
                <td><strong>Masalah lain</strong></td>
                <td>:</td>
                <td><?= $value['hasil'] ?></td>
            </tr>
        </tbody>
    </table>
<?php
    endforeach;
?>