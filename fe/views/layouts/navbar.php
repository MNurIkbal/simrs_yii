<?php

use app\components\DocoConstants;
use app\components\DocoHelpers;
use yii\helpers\Url;

$session = Yii::$app->session;
$controllerID = Yii::$app->controller->id;
$moduleName = Yii::$app->controller->moduleName;
$konfig = Yii::$app->cache->get(DocoConstants::VAR_K_S, []);
$url = Yii::$app->docoVars->workspace('url');
if (!empty($url) && $url !== '-') {
    $moduleName = preg_replace('/\W/m', '', $url);
}

$ambulanceNotification = Yii::$app->cache->get('ambulanceNotification', []);
$laboratoriumNotification = Yii::$app->cache->get('laboratoriumNotification', []);
$farmasiNotification = Yii::$app->cache->get('farmasiNotification', []);
$radiologiNotification = Yii::$app->cache->get('radiologiNotification', []);

$other_room = Yii::$app->docoVars->workspace("ruangan_lain");
$other_room = (is_array($other_room)) ? $other_room : [];
$idx = Yii::$app->docoVars->workspace("ruangan_index") == '-' ? 0 : Yii::$app->docoVars->workspace("ruangan_index");
unset($other_room[$idx]);
$color_navbar = Yii::$app->docoVars->identity("warna_header");
$color_font = Yii::$app->docoVars->identity("font_header");

$hasAccessESign = DocoHelpers::checkButtonAccess('/rm/esign', 'sign');
$urlEsign = '/rm/esign/list-sign';
if(!$hasAccessESign) {
    $workspaces = $session->get('workspace');
    $menu_modules = $session->get('menu_module');
    if (!empty($menu_modules) && is_array($menu_modules)) {
        foreach ($menu_modules as $key => $menu_module) {
            if(isset($menu_module['akses']['/rm/esign'])) {
                $hasAccessESign = in_array('sign', $menu_module['akses']['/rm/esign']);
                if($hasAccessESign) {
                    $workspace = $workspaces[$key];
                    $installation = reset($workspace['installation']);
                    $room = reset($installation['rooms']);
                    $urlEsign = DocoHelpers::crossUrl('jumpto', [
                        'ruangan_id' => $room['id'],
                        'instalasi_id' => $installation['id'],
                        'modul' => $workspace['slug'],
                        'url' => 'rm/esign/list-sign',
                    ]);
                    break;
                }
            }
        }
    }
}
?>

<!-- <style>
    .scrollable-menu {
        height: auto;
        max-height: 50vh;
        overflow-x: hidden;
    }

    <?php if (Yii::$app->controller->id == 'site') : ?>
    
    .content-wrapper {
        margin-left: 0 !important;
        width: 100% !important;
        background-color: #1e2142 !important;
    }
    
    .panel-toolbar.sticky {
        left: 0 !important;
        width: 100% !important;
    }

    <?php endif; ?>

</style> -->

<style>
    .scrollable-menu {
        height: auto;
        max-height: 50vh;
        overflow-x: hidden;
    }

    #sidebar {
        padding-right: 0 !important;
    }

    /* KITA TARGETKAN 'site' CONTROLLER */
    <?php if (Yii::$app->controller->id == 'site') : ?>
    
    /* 1. Paksa semua background jadi biru tua */
    body,
    .page-container {
        background-color: #1e2142 !important;
    }

    /* .page-content {
        background-color: #1e2142 !important;
        padding-top: 75px !important;
        background-image: url('/media/img/background/doctor-holding.jpg');
        
    } */

    .page-content {
        background-image: 
            linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
            url('/media/img/background/doctor-holding.jpg'); 
        }


    /* 3. Paksa .content-wrapper jadi transparan */
    .content-wrapper {
        margin-left: 0 !important;
        width: 100% !important;
        background-color: transparent !important; /* WAJIB TRANSPARAN */
        margin-top: 0 !important;
    }
    
    /* 4. Pastikan navbar Anda tetap putih (tidak ikut biru) */
    .navbar-fixed-top,
    #navbar-second {
        background-color: #ffffff !important;
    }

    /* 5. Style untuk toolbar (biarkan sama) */
    .panel-toolbar.sticky {
        left: 0 !important;
        width: 100% !important;
    }

    <?php endif; ?>

</style>
<div class="navbar-position">
    <div class="navbar-fixed-top" id="main">
        <!-- Second navbar -->
        <nav class="navbar navbar-sirs" id="navbar-second" >
            <div class="second-navbar">
                <?php if ($controllerID != 'site' && $session->has('moduleID')) : ?>
                    <button id="sidebarToggle" class="btn btn-sm btn-outline-light">
                        <i class="fa fa-bars"></i>
                    </button>
                <?php endif; ?>
                <div class="navbar-logo img-fluid">
                    <a href="<?php echo Url::home(); ?>">
                        <img class="logo-rs"
                            src="<?= Url::base(true) . Yii::$app->docoVars->identity("path_gambar_login") . Yii::$app->docoVars->identity("logo_rumahsakit"); ?>"
                            style="height:100px;"
                        >
                    </a>
                </div>
                <div class="navbar-title">
                    <h5 class="rs-title"><?= Yii::$app->docoVars->identity("nama_rumahsakit"); ?></h5>
                </div>
                <!-- navbar kiri -->
                <nav class="left-navbar navbar-expand-sm" >
                    <?php if ($controllerID != 'site' && $session->has('moduleID')) : ?>
                        <button onClick="sidebar_open()" class="navbar-toggler btn side-button" data-toggle="collapse" data-target="#collapse_target">
                            <span class="navbar-toggler-icon"> <i class="fa fa-navicon"></i></span>
                        </button>
                        <aside class="sidebar" id="sidebar">
                            <div class="sidebar-header" >
                            </div>
                            <nav class="sidebar-menu">
                                <ul id="mainmenu" class="nav nav-pills nav-stacked">
                                    <?= empty(Yii::$app->session->get('menu')) ? '' : Yii::$app->session->get('menu'); ?>
                                </ul>
                            </nav>
                        </aside>
                    <?php endif; ?>
                </nav>

                <!-- navbar kanan -->
                <div class="right-navbar">
                    <ul class="nav navbar-right">
                    <?php if ($controllerID != 'site' && $session->has('moduleID')) : ?>
                        <?php
                        if ($moduleName == 'ambulan') :
                        ?>
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ambulance"></i>
                                    <span class="label label-warning" id="ambulance-notification-total"><?= $ambulanceNotification['totalNotProcess'] ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="notification-list">
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu" id="ambulance-notification-list">
                                            <?php
                                            foreach ($ambulanceNotification['record'] as $notification) :
                                            ?>
                                                <li>
                                                    <p class="notification-title"><i class="fa fa-ambulance"></i>Pemesanan Ambulan untuk No Polisi <?= $notification['no_polisi'] ?> dengan No Pemesanan : <?= $notification['no_pesanambulan'] ?></p>
                                                    <p class="notification-date" data-date="<?= $notification['tgl_pesanambulan'] ?>"><i class="fa fa-clock-o"></i>Memuat ...</p>
                                                </li>
                                            <?php
                                            endforeach;
                                            ?>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="/ambulan/informasi-permintaan-ambulan">Lihat semua</a></li>
                                </ul>
                            </li>
                        <?php
                        endif;
                        ?>

                        <?php
                        if ($moduleName == 'laboratorium') :
                        ?>
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning" id="laboratorium-notification-total"><?= $laboratoriumNotification['totalUnread'] ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">
                                        <div>Notifikasi</div>
                                        <div><i class="fa fa-undo" id="refresh-notification-btn"></i></div>
                                    </li>
                                    <li class="notification-list">
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu" id="laboratorium-notification-list">
                                            <?php if (is_array($laboratoriumNotification)) { ?>
                                                <?php foreach ($laboratoriumNotification['record'] as $eachNotification) :
                                                    $notification = json_decode($eachNotification['additional_data'], true);

                                                ?>
                                                    <li class="<?= isset($eachNotification['is_read']) && !$eachNotification['is_read'] ? 'notification--unread' : '' ?> laboratorium-expertise-notification" data-id="<?= $eachNotification['notifikasi_id'] ?>" data-no="<?= $notification['his_reg_no'] ?>">
                                                        <p class="notification-title"><i class="fa fa-flask"></i><?= $notification['judulnotifikasi'] ?></p>
                                                        <p class="notification-message"><?= $notification['isi_notifikasi'] ?></p>
                                                        <p class="notification-date" data-date="<?= $notification['created_date'] ?>"><i class="fa fa-clock-o"></i></p>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="javascript:void(0)">Lihat semua</a></li>
                                </ul>
                            </li>
                        <?php
                        endif;
                        ?>

                        <?php
                        if ($moduleName == 'radiologi') :
                        ?>
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning" id="radiologi-notification-total"><?= $radiologiNotification['totalUnread'] ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">
                                        <div>Notifikasi</div>
                                        <div><i class="fa fa-undo" id="refresh-notification-btn"></i></div>
                                    </li>
                                    <li class="notification-list">
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu" id="radiologi-notification-list">
                                            <?php if (is_array($radiologiNotification)) { ?>
                                                <?php foreach ($radiologiNotification['record'] as $eachNotification) :
                                                    $notification = json_decode($eachNotification['additional_data'], true);

                                                ?>
                                                    <li class="<?= isset($eachNotification['is_read']) && !$eachNotification['is_read'] ? 'notification--unread' : '' ?> radiologi-expertise-notification" data-id="<?= $eachNotification['notifikasi_id'] ?>" data-no="<?= $notification['his_reg_no'] ?>">
                                                        <p class="notification-title"><i class="fa fa-bell"></i><?= $notification['judulnotifikasi'] ?></p>
                                                        <p class="notification-message"><?= $notification['isi_notifikasi'] ?></p>
                                                        <p class="notification-date" data-date="<?= $notification['created_date'] ?>"><i class="fa fa-clock-o"></i> <?= $notification['created_date'] ?></p>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="javascript:void(0)">Lihat semua</a></li>
                                </ul>
                            </li>
                        <?php
                        endif;
                        ?>

                        <?php
                        if ($moduleName == 'apotek') :
                        ?>
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-medkit"></i>
                                    <span class="label label-warning" id="farmasi-notification-total">
                                        <?= $farmasiNotification['totalUnread'] ?>
                                    </span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="notification-list farmasi_scroll">
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu" id="farmasi-notification-list">
                                            <?php
                                            if (isset($farmasiNotification['record'])) :
                                                foreach ($farmasiNotification['record'] as $notification) :
                                                    $detail_notif = json_decode($notification['additional_data'], true);
                                            ?>
                                                    <li class="<?= isset($notification['is_read']) && !$notification['is_read'] ? 'farmasi-notif-unread' : '' ?> farmasi-notif-item" data-id="<?= $notification['notifikasi_id'] ?>" data-rid="<?= $detail_notif['enc_reseptur_id'] ?>" data-noresep="<?= $detail_notif['noresep'] ?>">
                                                        <p class="notification-title"><i class="fa fa-medkit"></i>
                                                            <?= $notification['judulnotifikasi'] ?>
                                                        </p>
                                                        <p class="notification-message">
                                                            <?= $notification['isi_notifikasi'] ?>
                                                        </p>
                                                        <p class="notification-date" data-date="<?= $notification['tglnotifikasi'] ?>"><i class="fa fa-clock-o"></i>Memuat ...</p>
                                                    </li>
                                            <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="#" class="clear-notif-farmasi">Bersihkan Notifikasi</a></li>
                                </ul>
                            </li>
                        <?php
                        endif;
                        ?>
                    <?php endif; ?>
                    <?php if($moduleName != 'laboratorium' && $moduleName != 'radiologi') : ?>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning" id="notification-total"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">
                                <div>Notifikasi</div>
                                <div><i class="fa fa-undo" id="refresh-notification-btn"></i></div>
                            </li>
                            <li class="notification-list">
                                <ul class="menu" id="notification-list">
                                </ul>
                            </li>
                            <li class="footer" id="all-notification-btn"><a href="#">Lihat semua</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <li class="dropdown dropdown-user">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <!--<img src="<?= Url::base(true) . '/templete/limitless/assets/images/placeholder.jpg'; ?>" class="profile-icon">-->
                            <i class="fa fa-user"></i>
                            <span><?= Yii::$app->docoVars->user("nama"); ?></span>
                            <i class="caret"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-right">
                            <!--li><a href="<?= Url::home() . 'dcms/loginpemakai/view?id=' . Yii::$app->docoVars->user("uid"); ?>"></i> <?= Yii::t('fe', 'Profil saya') ?></a></li-->
                            <li><a href="<?= Url::home() . 'dcms/profile'; ?>"></i> <?= Yii::t('fe', 'Profil saya') ?></a></li>
                            <?php
                            $roles = Yii::$app->docoVars->user('roles');
                            if (!is_array($roles)) {
                                $roles = [$roles];
                            }
                            if (in_array('sysadmin', $roles)) :
                            ?>
                                <li id="flush-cache-btn"><a href="javascript:void(0)"></i> <?= Yii::t('fe', 'Flush cache') ?></a></li>
                            <?php
                            endif;
                            ?>
                            <li><a href="#" onclick="logoutapp()"><?= Yii::t('fe', 'Keluar') ?></a></li>
                        </ul>
                    </li>
                        <?php if ($controllerID != 'site' && $session->has('moduleID')) : ?>
                        <!-- Menu Utama-->
                        <!-- <?//php if($hasAccessESign) : ?> -->
                        <!-- <li>
                            <a href="<?//= $urlEsign ?>">
                                <i class="fa fa-list"></i>&nbsp;E - Sign
                            </a>
                        </li> -->
                        <!-- <?//php endif; ?> -->
                        <li>
                            <a href="javascript:void(0)" onclick="changeWorkspace(this)" data-home="true">
                                <i class="fa fa-home"></i> &nbsp;
                                <?= Yii::t('fe', 'Menu Utama'); ?>
                            </a>
                        </li>
                        <!-- sirs it-->
                        <li class="dropdown" style="<?= (isset($konfig['is_hide_ruangan']) && $konfig['is_hide_ruangan'] == true) ? "display:none" : "" ?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-navicon"></i> &nbsp;
                                <?= Yii::t('fe', Yii::$app->docoVars->workspace("ruangan_name")); ?>
                                <span class="caret"></span>
                            </a>
                            <!--dropdown -->
                            <ul class="dropdown-menu dropdown-menu-right scrollable-menu">
                                <?php foreach ($other_room as $key => $value) : ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="changeWorkspace(this)" data-id="<?= $value['id']; ?>" data-index="<?= $key; ?>" data-home="false" data-instalasi-index="0">
                                            <?= Yii::t('fe', $value['name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- navbar kanan -->
            </div>
        </nav>
        <!-- /second navbar -->
    </div>
</div>
<!--end of Navbar -->

<?php if ($controllerID != 'dashboard' && $controllerID != 'site') : ?>
    <div class="page-header" style="">
    </div>
<?php endif; ?>

<?php
$moduleID = ($session->has('moduleID')) ? $session->get('moduleID') : 0;
?>
<script>
    const moduleName = "<?= $moduleName ?>"
    var moduleID = "<?php echo $moduleID; ?>";
    var coID = "<?php echo $controllerID; ?>";
    var ruangan_index = <?php echo is_numeric(Yii::$app->docoVars->workspace("ruangan_id")) ? Yii::$app->docoVars->workspace("ruangan_id") : '-1'; ?>;
    var urlEsign = "<?= $urlEsign ?>";
    function logoutapp() {
        localStorage.removeItem('notifications')
        window.location.href = "/site/logout";
        sessionStorage.clear()
        localStorage.clear();
    }

    //toggle
    function sidebar_open() {
        document.getElementById("collapse_target").style.width = "90vw";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const body = document.body;

        if (sidebar) {
            if (sidebar.classList.contains('collapsed')) {
                body.classList.add('sidebar-collapsed');
            }

            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                body.classList.toggle('sidebar-collapsed');
                setTimeout(function() {
                    if ($.fn.dataTable) {
                        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                    }
                }, 310);
            });
        }
    });
</script>
