<style>
    td, span {
        font-family:Arial,Helvetica,sans-serif;
        font-size: 11pt;
    }
    
    .w-full {
        width: 100%;
    }

    .border-top {
        border-top: 1px solid black;
        border-bottom: 2px solid black;
        font-weight: bold;
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .line-height {
        line-height: 1.5;
    }
</style>

<table class="w-full line-height" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td width="5%" class="border-top"><span>No</span></td>
            <td width="70%" class="border-top"><span>Nama Barang</span></td>
            <td width="5px" class="border-top"><span>Qty Pemesanan</span></td>
            <td width="5px" class="border-top"><span>Qty Dikirim</span></td>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            if (count($detail_mutasi) > 0) :
                foreach ($detail_mutasi as $value) :
        ?>
                    <tr>
                        <td><span><?= $no ?></span></td>
                        <td><span><?= isset($value['barang_nama']) ? $value['barang_nama'] : '' ?></span></td>
                        <td><span><?= isset($value['qty_dipesan']) && isset($value['satuanbesar_nama'])  ? $value['qty_dipesan']." ".$value['satuanbesar_nama'] : '' ?></span></td>
                        <td><span><?= isset($value['qty_input']) && isset($value['satuanbesar_nama'])  ? $value['qty_input']." ".$value['satuanbesar_nama'] : '' ?></span></td>
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