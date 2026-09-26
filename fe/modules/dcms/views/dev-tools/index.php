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
                        <span>Dev Tools</span>
                    </div>

                    <div class="category-content no-padding" style="display: block;">
                        <ul class="navigation navigation-main navigation-accordion">

                            <!-- Main -->
                            <li class="navigation-header"><span>Main</span> <i class="icon-menu" title="" data-original-title="Main pages"></i></li>
                            <li><a href="#"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
                            <li class="active">
                                <a href="#" class="has-ul"><i class="icon-stack"></i> <span>Billing</span></a>
                                <ul>
                                    <li><a href="#" class="sidebar-menu" data-target="_pindahbilling">Pindah Billing</a></li>
                              
                                </ul>
                            </li>
                            <li class="active">
                                <a href="#" class="has-ul"><i class="icon-stack"></i> <span>LIS</span></a>
                                <ul>
                                    <li><a href="#" class="sidebar-menu" data-target="_resendorderlis">Resend Order</a></li>
                              
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