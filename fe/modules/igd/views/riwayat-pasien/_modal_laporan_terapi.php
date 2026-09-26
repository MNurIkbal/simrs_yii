<?php

use yii\web\View;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered" width="100%" id="tbl-laporan-tindakan" data-href="<?=$url['datatable']?>">
                <thead>
                    <tr class="bg-inverse">
                        <th>Jenis</th>
                        <th>Tanggal Instruksi</th>
                        <th>Instruksi</th>
                        <th>Pegawai Pemberi Instruksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center">Data Belum Tersedia</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
    var tbLaporanTindakan;
    $(document).ready(function() {
        tbLaporanTindakan = $('#tbl-laporan-tindakan').DataTable({
            filter: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: $('#tbl-laporan-tindakan').data('href'),
            columns: [
                {
                    title: 'Jenis',
                    data: 'jenis',
                    render: (data, rowElement, rowData, rowAdditionalData) => {
                        let _jenis = rowData.jenis
                        switch(rowData.grouping_tipe){
                            case 'Penunjang':
                                _jenis = rowData.instalasi_nama + ' - ' + rowData.ruangan_pertindakan;
                                break;
                            case  'Tindakanbmhp':
                                rowData.grouping_tipe = 'Tindakan & BMHP'
                                break;
                        }

                        return rowData.grouping_tipe + '<br>' +  _jenis
                    },
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Tanggal Instruksi',
                    data: 'tgl_tindakan',
                    searchable: false,
                    orderable: false,
                    render: function(data) {
                        return moment(data).format('DD-MM-YYYY HH:mm')
                    }
                },
                {
                    title: 'Instruksi',
                    data: 'instruksi',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Pegawai Pemberi Instruksi',
                    data: 'nama_pegawai',
                    searchable: false,
                    orderable: false,
                },
            ],
        })
    })
", View::POS_END, 'jskuning');

?>
