<?php
    use yii\helpers\Html;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <?=
        Html::dropDownList('jumlah', null, $listOpt, [
            'class' => 'select2',
            'id' => 'jumlah',
        ]);
    ?>
</div>
<script type="text/javascript">
    var jenis = "<?= $jenis ?>";
    var route = "<?= $jenis ?>";
    var pendaftaran_id = "<?= $pendaftaran_id ?>";
    var pasien_id = "<?= $pasien_id ?>";
    var no_pendaftaran = "<?= $no_pendaftaran ?>";
    var template = "<?= $jenisTemplate ?>";
    var jumlah = 1;
    var html = "<?= isset($htmlMode) ? $htmlMode : 1 ?>";

    $(function () {
        var _modal = $('#modal_print_label');
        if (_modal.length) {
            _modal.css("z-index", 1066)
        }

        $("#jumlah").change(function() {
            jumlah = $(this).val();
            var _queryParams = $.param({
                pendaftaran_id : pendaftaran_id,
                pasien_id : pasien_id,
                no_pendaftaran : no_pendaftaran,
                jumlah : jumlah,
                jenis : jenis,
                template : template,
                html : html,
            });

            if(jenis == 'mcu' || jenis == 'reservasi') {
                route = 'rajal';
            }

            window.open(`/pendaftaran/daftar-${route}/print-label-pasien-baru?${_queryParams}`, "_blank");
            if (_modal.length) {
                _modal.modal("toggle");
            } else {
                $("#modal_backdrop").modal("toggle");
            }
        });
    });
</script>