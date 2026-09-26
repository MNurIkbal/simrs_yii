<?php
    use Doco\components\DocoHelpers;
    $no_polisi =  !empty($noPolisi) ? $noPolisi : '-'; 

?>
<style type="text/css">
    .plat-nomor{
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #fff;
    }
    .background-plat{
        /*border: 1px solid transparent;*/
        text-align: center;
        display: inline-block;
        position: relative;
        width: 165px;
        padding: 5px;
        border-color: #fff;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #54be8b; /*#001;*/
        /*border: 1px solid #291ce8;*/
    }
    .f-right{
        float: right;
        text-align: -webkit-right;
    }
    .p-17{
        padding:17px 0px !important;
    }
</style>
<div>
    <div class="col-md-12 f-right">
        <div class="background-plat">
            <span class="plat-nomor">
                <?= $no_polisi ?>
            </span>
        </div>
    </div>
</div>