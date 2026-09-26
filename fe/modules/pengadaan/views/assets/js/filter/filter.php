<?php
use yii\web\View;
?>
<style>
    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
    .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
    .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
    .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
    .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
    .my-legend a {
    color: #777;
    }
    
    .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #26A65B;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
    .square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    
    }
    
    .legend-information__color {
        min-width: 16px;
        min-height: 16px;
        height: 16px;
        width: 16px;
        border-radius: 16px;
        margin-right: 8px;
        border: 1px solid #dddddd;
    }
    
    .legend-wrapper {
            display: flex;
            flex-flow: wrap;
    }
    .legend-information {
        padding: 8px;
        margin-right: 8px;
        margin-top: 8px;
        border: 1px solid #dddddd;
        border-radius: 4px;
        display: flex;
        background-color: #ffffff;
        color: #606060;
    }
    
    .semua {
        background-color: #FFFfff !important;
    }
    
    .ya {
        background-color: #2bcc6e !important;
    }
    
    .yes {
        background-color: #ffd2d2 !important;
    }
    
    .tidak {
        background-color: #2bcc6e !important;
    }
    
    .filter-selected {
        border-color : #2ca38b;
    }

    .circle-white {
        background-color: #ffffff;
    }

    .selected-btn-group {
        background-color: #2CA38B;
        color: #ffffff;
    }
</style>
<div class="legend-index row">
    <div class="col-md-3" id="is_prcyto">
        <div class="legend-header">Cito</div>
        <div class="legend-wrapper">
            <div class="legend-information selected-btn-group" data-type-cito="" data-params-cito="is_prcyto" id="cito-all">
                <div class="legend-information__color circle-white"></div>
                <div class="legend-information__text" >Semua</div>
            </div>
            <div class="legend-information" data-type-cito="Cito" data-params-cito="is_prcyto" id="cito-ya">
                <div class="legend-information__color circle-white"></div>
                <div class="legend-information__text">Cito</div>
            </div>
            <div class="legend-information" data-type-cito="Reguler" data-params-cito="is_prcyto" id="cito-no">
                <div class="legend-information__color circle-white"></div>
                <div class="legend-information__text" >Reguler</div>
            </div>
        </div>
    </div>
    <div class="col-md-3" id="is_admin">
        <div class="legend-header">Admin</div>
            <div class="legend-wrapper">
            <div class="legend-information selected-btn-group" data-type-admin="" data-params-admin="is_admin" id="admin-all">
                <div class="legend-information__color circle-white"></div>
                <div class="legend-information__text" >Semua</div>
            </div>
            <div class="legend-information" data-type-admin="Ya" data-params-admin="is_admin" id="admin-ya">
                <div class="legend-information__color circle-white"></div>
                <div class="legend-information__text">Ya</div>
            </div>
            <div class="legend-information" data-type-admin="Tidak" data-params-admin="is_admin" id="admin-no">
                <div class="legend-information__color circle-white"></div>
                <div class="legend-information__text" >Tidak</div>
            </div>
        </div>
    </div>

    <?php if($type != 'barang'): ?>
    <div class="col-md-3" id="is_consignment">
        <div class="legend-header">Consignment</div>
        <div class="legend-wrapper">
        <div class="legend-information selected-btn-group" data-type-consignment="" data-params-consignment="is_consignment" id="consignment-all">
            <div class="legend-information__color circle-white"></div>
            <div class="legend-information__text" >Semua</div>
        </div>
        <div class="legend-information" data-type-consignment="Ya" data-params-consignment="is_consignment" id="consignment-ya">
            <div class="legend-information__color circle-white"></div>
            <div class="legend-information__text">Ya</div>
        </div>
        <div class="legend-information" data-type-consignment="Tidak" data-params-consignment="is_consignment" id="consignment-no">
            <div class="legend-information__color circle-white"></div>
            <div class="legend-information__text" >Tidak</div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php
$isBarang = $type == 'barang' ? true : false;
$this->registerJs('
// Global Var
var tableName = "'.$tableName.'"
var url = "'.$url.'"
var isBarang = "'.$isBarang.'"
var get = "'.$get.'"
var excel = "'.$excel.'"
',View::POS_END);
$this->registerJs($this->render('filter.js'), View::POS_END);
?>
