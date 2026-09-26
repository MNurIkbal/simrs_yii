<div align="center" class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
    <div class="panel-heading">
        <?php
            $periode_awal = !empty($data_header['periode_awal']) ? date('d-m-Y', strtotime($data_header['periode_awal'])) : '-';
            $periode_akhir = !empty($data_header['periode_akhir']) ? date('d-m-Y', strtotime($data_header['periode_akhir'])) : '-';
        ?>
        <h4 class="panel-title text-center"><center><?= Yii::t('app', 'DETAIL STOK OPNAME') ?></center></h4>
        <h4 class="panel-title text-center"><center><?= Yii::t('app', $data_header['ruangan_nama']) ?></center></h4>
        <h4 class="panel-title text-center"><center><?= Yii::t('app', 'Periode') ?> <?= $periode_awal ?> s/d <?= $periode_akhir ?></center></h4>
    </div>
    <div class="panel-body">
        <table width="100%" class="tabel">
            <tbody>
                <tr>
                    <td class="bold"><?= Yii::t('app', 'Tanggal stok opname') ?></td>
                    <td class="header_noResep"><?=@$data_header['tglstokopname']?></td>
                    <td class="bold"><?= Yii::t('app', 'Periode stok') ?></td>
                    <td class="header_namaPasien"><?= $periode_awal ?> s/d <?= $periode_akhir ?></td>
                </tr>
                <tr>
                    <td class="bold"><?= Yii::t('app', 'No stok opname') ?></td>
                    <td class="header_noPendaftaran"><?=@$data_header['nostokopname']?></td>
                    <td class="bold"><?= Yii::t('app', 'No formulir stok opname') ?></td>
                    <td class="header_dokter"><?=@$data_header['noformulir']?></td>
                </tr>
                <tr>
                    <td class="bold"><?= Yii::t('app', 'Jenis stok opname') ?></td>
                    <td class="header_noPendaftaran"><?=@$data_header['jenis_stokopname']?></td>
                </tr>
            </tbody>
        </table>
        <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat" border="1" width="100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="1">No</th>
                    <th><?= Yii::t('app', 'Nama barang') ?></th>
                    <th><?= Yii::t('app', 'Kondisi') ?></th>
                    <th><?= Yii::t('app', 'Stok sistem') ?></th>
                    <th><?= Yii::t('app', 'Stok fisik') ?></th>
                    <th><?= Yii::t('app', 'Selisih') ?></th>
                </tr>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $no = 1;
                    $total_stok_sistem = $total_stok_fisik = $total_selisih_stok = $total_harga_sistem = $total_harga_fisik = $total_harga_netto = 0;
                    foreach ($data_barang as $key => $value) {
                        $total_stok_sistem += $value['volume_sistem'];
                        $total_stok_fisik += $value['volume_fisik'];
                        $selisih = $value['volume_sistem'] - $value['volume_fisik'];
                        $total_selisih_stok += $selisih;
                        $total_harga_sistem += $value['volume_sistem'] * $value['harganetto'];
                        $total_harga_fisik += $value['volume_fisik'] * $value['harganetto'];
                        $total_harga_netto = $total_harga_sistem - $total_harga_fisik;
                ?>  
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['barang_nama'] ?></td>
                            <td><?= $value['kondisibarang'] ?></td>
                            <td><?= $value['volume_sistem'] ?></td>
                            <td><?= $value['volume_fisik'] ?></td>
                            <td>( <?= $selisih ?> )</td>
                        </tr>
                <?php
                    $no++;
                    }
                ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= Yii::t('app', 'Total Stok Sistem') ?></td>
                        <td><?= $total_stok_sistem ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= Yii::t('app', 'Total Stok Fisik') ?></td>
                        <td><?= $total_stok_fisik ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= Yii::t('app', 'Total Selisih Stok') ?></td>
                        <td>( <?= $total_selisih_stok ?> )</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= Yii::t('app', 'Total Harga Netto Stok Sistem') ?></td>
                        <td><?= "Rp. ".number_format($total_harga_sistem, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= Yii::t('app', 'Total Harga Netto Stok Fisik') ?></td>
                        <td><?= "Rp. ".number_format($total_harga_fisik, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= Yii::t('app', 'Total Harga Netto Stok') ?></td>
                        <td>( <?= "Rp. ".number_format($total_harga_netto, 0, ',', '.'); ?> )</td>
                    </tr>
            </tbody>
        </table>
        <div class="clear"><br></div>
    </div>
</div>