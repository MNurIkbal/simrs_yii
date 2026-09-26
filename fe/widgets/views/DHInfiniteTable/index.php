<?php

use yii\web\View;
?>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered datatable-basic dataTable" id="<?= $id ?>" name="<?= $name ?>" style="width:100%; height:50px">
            <br>
            <thead>
                <?= $thead ?>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr id="menu-action-infinite-table">
                    <th colspan="<?= $columnsLength ?>">
                        <div class="flex-menu-infinite-table">
                            <div class="list-action-button-infinite-table" style="display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-xs btn-only btn-primary-color btn-load-infinite-datatable-widget" disabled>Load More Data</button>
                                <button type="button" class="btn btn-xs btn-only btn-primary-color btn-hide-infinite-datatable-widget" disabled>Hide Data</button>
                            </div>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
$this->registerJs('
    var idInfiniteDataTableWidget = "'.$id.'";
    var nameInfiniteDataTableWidget = "'.$name.'";
    var ajaxInfiniteDataTableWidget = '.json_encode($ajax).';
    var limitInfiniteDataTableWidget = '.json_encode($limit).';
    var columnsInfiniteDataTableWidget = '.json_encode($columns).';
    var columnsLengthInfiniteDataTableWidget = "'.$columnsLength.'";
    var sortingInfiniteDataTableWidget = '.json_encode($sorting).';
    var filtersInfiniteDataTableWidget = '.json_encode($formFilters).';
    var functionsInfiniteDataTableWidget  = '.json_encode($functions).';
', View::POS_READY);
$this->registerJs($this->render("js/index.js"), View::POS_READY, 'js');
?>