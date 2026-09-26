
<style media="print">
    .btn {
        display: none;
    }
    .panel {
        border: none;
    }
</style>

<div class="col-md-12 panel panel-default" style="margin-top:10px;">
    <div class="panel-heading">
        <div class="panel-title">
            <h1 class="text-center"><?= $title ?></h1>
        </div>
    </div>
    <table id="example" class="table table-bordered table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th>Obat Alkes</th>
                <th>Jenis Kasus Penyakit</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                if ($count > 0) {
                    foreach ($data as $value) : 
            ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['obatalkes_namalain'] ?></td>
                            <td><?= $value['jeniskasuspenyakit_nama'] ?></td>
                        </tr>
            <?php
                    $no++;
                    endforeach;
                } else {
            ?>
            <tr>
                <td class="text-center" colspan="3">Data tidak ditemukan.</td>
            </tr>
                <?php } ?>
        </tbody>
    </table>
    <a href="#" onclick="window.print();" class="btn btn-dodger-blue"><i class="fa fa-print"></i> Print</a>
</div>
