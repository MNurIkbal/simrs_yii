<?php
use yii\web\View;
?>
<style>
    .legend-index {
        margin: 0px 0px !important;
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
    <div class="col-md-4" style="width: 31.5%" id="is_prcyto">
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
    <div class="col-md-4" style="width: 31.5%" id="is_admin">
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
    <div class="col-md-4" style="width: 31.5%" id="is_consignment">
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
</div>
<?php
$this->registerJs('
// Global Var
var tableName = "'.$tableName.'"
var _url = "'.$url.'"
',View::POS_END);
$this->registerJs($this->render('assets/js/_filter_selected.js'), View::POS_END);
?>