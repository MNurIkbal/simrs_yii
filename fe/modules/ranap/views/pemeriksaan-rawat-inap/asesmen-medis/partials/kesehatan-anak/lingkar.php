<?php
use kartik\date\DatePicker;
$classCenter = 'text-center';
$dateFormat = "yyyy-mm-dd";

?>

<div class="row">
    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Lingkar Kepala </p>
</div>
<div class="row">
    <div class="col-sm-12">
        <table style="width:100%" class="table-lingkar-kepala">
            <thead>
                <tr>
                    <th class="<?= $classCenter ?>" id="lingkarKepalaTanggalHeader">Tanggal</th>
                    <th class="<?= $classCenter ?>" id="lingkarKepalaUrutanHeader">Ukuran (cm)</th>
                    <th class="<?= $classCenter ?>" id="aksiLingkarKepala">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="margin-bottom:5px;margin-top:30px;">
                    <td class="<?= $classCenter ?>">
                        <?= DatePicker::widget([
                            'name' => 'KesehatanAnakForm[lingkarKepalaTanggal][]',
                            'type' => DatePicker::TYPE_INPUT,
                            'readonly' => true,
                            'options' => ['class' => 'lingkarKepalaTanggal  input-margin', 'id' => 'lingkarKepalaTanggal_0'],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'format' => $dateFormat,
                                'orientation' => 'bottom'
                            ]
                        ]); ?>
                    </td>
                    <td class="<?= $classCenter ?>">
                        <input type="text" name="KesehatanAnakForm[lingkarKepalaUkuran][]"
                            class="form-control input-sm lingkarKepalaUkuran input-margin">
                    </td>
                    <td style="text-align: center;width:15%;">
                        <button type="button" class="btn btn-success addRowLingkarKepala" name="addRowLingkarKepala"
                            id="addRowLingkarKepala">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<br>
<div class="row">
    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Lingkar Dada </p>
</div>
<div class="row">
    <div class="col-sm-12">
        <table style="width:100%" class="table-lingkar-dada">
            <thead>
                <tr>
                    <th class="<?= $classCenter ?>" id="lingkarDadaTanggalHeader">Tanggal</th>
                    <th class="<?= $classCenter ?>" id="lingkarDadaUrutanHeader">Ukuran (cm)</th>
                    <th class="<?= $classCenter ?>" id="aksiLingkarDada">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="margin-bottom:5px;margin-top:30px;">
                    <td class="<?= $classCenter ?>">
                        <?= DatePicker::widget([
                            'name' => 'KesehatanAnakForm[lingkarDadaTanggal][]',
                            'type' => DatePicker::TYPE_INPUT,
                            'readonly' => true,
                            'options' => ['class' => 'lingkarDadaTanggal  input-margin', 'id' => 'lingkarDadaTanggal_0'],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'format' => $dateFormat,
                                'orientation' => 'bottom'
                            ]
                        ]); ?>
                    </td>
                    <td class="<?= $classCenter ?>">
                        <input type="text" name="KesehatanAnakForm[lingkarDadaUkuran][]"
                            class="form-control input-sm lingkarDadaUkuran input-margin">
                    </td>
                    <td style="text-align: center;width:15%;">
                        <button type="button" class="btn btn-success addRowLingkarDada" name="addRowLingkarDada"
                            id="addRowLingkarDada">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<br>
<div class="row">
    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Lingkar Perut </p>
</div>
<div class="row">
    <div class="col-sm-12">
        <table style="width:100%" class="table-lingkar-perut">
            <thead>
                <tr>
                    <th class="<?= $classCenter ?>" id="lingkarPerutTanggalHeader">Tanggal</th>
                    <th class="<?= $classCenter ?>" id="lingkarPerutUrutanHeader">Ukuran (cm)</th>
                    <th class="<?= $classCenter ?>" id="aksiLingkarPerut">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="margin-bottom:5px;margin-top:30px;">
                    <td class="<?= $classCenter ?>">
                        <?= DatePicker::widget([
                            'name' => 'KesehatanAnakForm[lingkarPerutTanggal][]',
                            'type' => DatePicker::TYPE_INPUT,
                            'readonly' => true,
                            'options' => ['class' => 'lingkarPerutTanggal  input-margin', 'id' => 'lingkarPerutTanggal_0'],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'format' => $dateFormat,
                                'orientation' => 'bottom'
                            ]
                        ]); ?>
                    </td>
                    <td class="<?= $classCenter ?>">
                        <input type="text" name="KesehatanAnakForm[lingkarPerutUkuran][]"
                            class="form-control input-sm lingkarPerutUkuran input-margin">
                    </td>
                    <td style="text-align: center;width:15%;">
                        <button type="button" class="btn btn-success addRowLingkarPerut" name="addRowLingkarPerut"
                            id="addRowLingkarPerut">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
