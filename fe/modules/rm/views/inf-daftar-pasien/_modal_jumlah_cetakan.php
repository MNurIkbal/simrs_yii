<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <select name="jumlah" id="jumlah" class="select2">
        <?php for ($i=0; $i < $jumlahCetakan; $i++) { ?>
        <?php if ($i % 2 == 0): ?> 
            <?php if ($i == 0): ?>
                    <option value="<?= $i ?>">Pilih</option>
                <?php else : ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
            <?php endif ?>
        <?php endif ?>
        <?php } ?>
    </select>
</div>
<script type="text/javascript">
    var jenis = "<?= $jenis ?>";
    var pendaftaran_id = "<?= $pendaftaran_id ?>";
    var pasien_id = "<?= $pasien_id ?>";
    var no_pendaftaran = "<?= $no_pendaftaran ?>";
    var template = "<?= $jenisTemplate ?>";
    var jumlah = 1;

    $(function () {
        $("#jumlah").change(function() {
            jumlah = $(this).val();

            window.open("/rm/inf-daftar-pasien/print-label-pasien-baru?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id+"&no_pendaftaran="+no_pendaftaran+"&jumlah="+jumlah+"&template="+template+"", "_blank");
            $("#modal_backdrop").modal("toggle");
        });
    });
</script>