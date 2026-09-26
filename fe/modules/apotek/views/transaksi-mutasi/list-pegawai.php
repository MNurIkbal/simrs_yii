<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', $title) ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="row">
        <?php
            echo Html::beginForm(null,'POST',[
                    'class' => 'form-filter form-horizontal',
                ]);
        ?>
        <div class="form-group">
            <div class="col-md-12">
                <div class="col-md-4">
                    <?php
                        echo Html::textInput('nip',null,[
                            'class' => 'form-control pickadate',
                            'id' => 'anytime-month-numeric',
                            'placeholder' => 'NIP'
                        ]);
                    ?>
                </div>
                <div class="col-md-4">                
                    <?php
                        echo Html::textInput('pegawai_id',null,[
                            'class' => 'form-control',
                            'placeholder' => 'Nama Pegawai'
                        ]);
                    ?>
                </div>
                <div class="col-md-4">            
                    <?php
                        echo Html::textInput('jabatan',null,[
                            'class' => 'form-control',
                            'placeholder' => 'Jabatan'
                        ]);
                    ?>
                </div>
            </div>
        </div>

        <?php
            echo Html::endForm();
        ?>
    </div>
    <br>
    <table id="exampleFilter" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?=\Yii::t("fe", "NIP");?></th>
                <th><?=\Yii::t("fe", "Nama pegawai");?></th>
                <th><?=\Yii::t("fe", "Jabatan");?></th>
                <th width="1"><?=\Yii::t("fe", "Aksi");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    var tableSearch;

    // Event Reload
    $(document).on("click", ".data-check", function() {
        var id = $(this).attr("data-id");
        var value = $(this).attr("data-value");

        $("#mutasiobatalkes-form")
            .find("#mutasiobatruanganform-pegawaimengetahui_id")
            .html("<option value=\""+id+"\" selected>"+value+"</option>");
        // $("#mutasiobatalkes-form")
        //     .find("input[name=pegawai_nama]")
        //     .val(value);

        $("#modal_backdrop").modal("hide");
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableSearch = $("#exampleFilter").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"apotek/transaksi-mutasi/get-data-pegawai",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "NIP"));?>", data: "nomorindukpegawai"},
                {title: "<?=(\Yii::t("fe", "Nama pegawai"));?>", data: "nama_pegawai"},
                {title: "<?=(\Yii::t("fe", "Jabatan"));?>", data: "jabatan.jabatan_nama"},
                {
                    title: "<?=(\Yii::t("fe", ""));?>",
                    data: "check",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-modal").datatableBootstrapFilter(tableSearch);
    });
</script>