<table id="observasiEws" class="table table-striped table-hover table-bordered no-footer" style="width: 100%;">
    <thead>
        <tr class="bg-inverse header-observasi">
            <th style="width: 250px;"><?= Yii::t('fe', 'Parameter') ?></th>
            <?php foreach ($header as $key => $value) : ?>
                <th class="text-center">
                    <u style="cursor: pointer;" class="update-ews-date" data-date="<?= $value ?>" data-ews-id="<?= isset($headerIds[$key]) ? $headerIds[$key] : '' ?>"><?= date('d/m H:i', strtotime($value)) ?></u>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody class="tbody-observasi">
        <?php if (!empty($header)) : ?>
            <?php foreach ($body as $key => $value) : ?>
                <tr>
                    <td>
                        <b style="cursor: pointer;"><?= $key; ?></b>
                    </td>
                    <?php foreach ($value as $keyScore => $val) : ?>
                        <?php
                            $kategori = isset($scoreFooter[$keyScore]) ? $scoreFooter[$keyScore]['kategori'] : 0;
                            $coloring = '';
                            if ($kategori == 1) {
                                $coloring = 'resiko-rendah';
                            } elseif ($kategori == 2) {
                                $coloring = 'resiko-sedang';
                            } elseif ($kategori == 3) {
                                $coloring = 'resiko-tinggi';
                            } elseif ($kategori == 4) {
                                $coloring = 'resiko-sangat-tinggi';
                            }
                        ?>
                        <td class="text-center <?= $coloring; ?>">
                            <b><?= $val['score']; ?></b>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td>
                    <b>Total Score</b>
                </td>
                <?php foreach ($scoreFooter as $val) : ?>
                    <?php
                        $kategori = $val['kategori'];
                        $coloring = '';
                        if ($kategori == 1) {
                            $coloring = 'resiko-rendah';
                        } elseif ($kategori == 2) {
                            $coloring = 'resiko-sedang';
                        } elseif ($kategori == 3) {
                            $coloring = 'resiko-tinggi';
                        } elseif ($kategori == 4) {
                            $coloring = 'resiko-sangat-tinggi';
                        }
                    ?>
                    <td class="text-center <?= $coloring; ?>">
                        <b><?= $val['score']; ?></b>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php

use yii\web\View;

$this->registerJs($this->render('table-ews.js'), View::POS_END);
?>