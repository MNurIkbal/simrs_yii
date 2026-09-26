<?php
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
?>
<style type="text/css">
    .main-page-inside {
        display:table;
        width:100%;
        table-layout: fixed;
    }
    .sidebar {
        display: table-cell;
        vertical-align: top;
    }
    .content-inside {
        display: table-cell;
        vertical-align: top;
        position:relative;
    }
    .datepicker>div {
        display: block;
    }
</style>
<div class="main-page-inside">
    <div class="sidebar sidebar-main sidebar-default">
        <div class="sidebar-fixed affix-top">
            <div class="sidebar-content">

                <!-- Main navigation -->
                <div class="sidebar-category sidebar-category-visible">
                    <div class="category-title">
                        <span>Tools BPJS</span>
                        <!-- <ul class="icons-list">
                            <li><a href="#" data-action="collapse" class=""></a></li>
                        </ul> -->
                    </div>

                    <div class="category-content no-padding" style="display: block;">
                        <ul class="navigation navigation-main navigation-accordion">

                            <!-- Main -->
                            <li class="navigation-header"><span>Main</span> <i class="icon-menu" title="" data-original-title="Main pages"></i></li>
                            <li><a href="#"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
                            <li class="active">
                                <a href="#" class="has-ul"><i class="icon-stack"></i> <span>SEP</span></a>
                                <ul>
                                    <li><a href="#" class="sidebar-menu" data-target="_cekfingerpeserta">Cek Finger Print Peserta</a></li>
                                    <li><a href="#" class="sidebar-menu" data-target="_listpesertafinger">List Peserta Finger Print</a></li>
                                    <li><a href="#" class="sidebar-menu" data-target="_septanpafinger">Pengajuan Finger Print</a></li>
                                    <!-- 
                                    <li>
                                        <a href="#" class="has-ul">3 columns</a>
                                        <ul class="hidden-ul">
                                            <li><a href="3_col_dual.html">Dual sidebars</a></li>
                                            <li><a href="3_col_double.html">Double sidebars</a></li>
                                        </ul>
                                    </li> 
                                    -->
                                </ul>
                            </li>
                            <!-- /main -->

                        </ul>
                    </div>
                </div>
                <!-- /main navigation -->

            </div>
        </div>
    </div>
    <div class="content-inside" id="main-content-inside">
        
    </div>
</div>
<?php
$this->registerJs($this->render("script.js"), View::POS_END, 'index');
?>