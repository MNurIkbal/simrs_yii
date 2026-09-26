<div class="modal fade" id="modal_update_harga" style="height: 500px">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center">Alert!!! <br> Terdapat Perbedaan Harga !!!!</h4>
            </div>
            <div class="modal-body" style="max-height: 650px; overflow-y: scroll;">
                <div class="table-responsive">
                    <form id="form-alert-harga">
                        <table id="tableAlertHarga" class="table table-bordered" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1"></th>
                                    <th>No.</th>
                                    <th>Nama Obat</th>
                                    <th>Harga Netto Transaksi</th>
                                    <th>Harga Dasar Sekarang</th>
                                    <th>Harga Dasar yang Disarankan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Data Kosong</td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="text-center" style="font-size: 16px">
                    <p>
                        Apakah anda ingin merubah harga dasar yang digunakan saat ini dengan harga yang disarankan oleh sistem ?
                    </p>
                    <p>
                        Setelah menekan tombol simpan, harga netto obat yang terpilih akan berubah.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-success" id="btn-simpan-alert">Simpan</button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-default" data-dismiss="modal">Tidak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>