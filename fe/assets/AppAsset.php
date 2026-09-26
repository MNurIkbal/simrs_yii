<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'fonts/font.css',
        'templete/limitless/assets/css/icons/fontawesome/styles.min.css',
        'templete/limitless/assets/css/icons/icomoon/styles.css',
        'templete/limitless/assets/css/bootstrap.css',
        'templete/limitless/assets/css/core.css',
        'templete/limitless/assets/css/components.min.css',
        'templete/limitless/assets/css/colors.css',
        // 'templete/limitless/assets/lib/autocomplete/jquery.typeahead.min.css',
        'css/nestable.css',
        'css/bootstrap-duallistbox.css',
        'css/bootstrap-timepicker.min.css',
        // inject from antrian assets
        'css/keyboard.min.css',
        'css/swiper.min.css',
        'build/bundle.css',
        'css/custommargin.min.css',
        'additional-packages/datepicker-bootstrap-1.9.1/css/bootstrap-datepicker3.standalone.min.css',
        'additional-packages/jquery.timeline-2.1.3/css/jquery.timeline.min.css',
        'additional-packages/jquery.timeline-2.1.3/css/jquery.timeline.min.css',
        'additional-packages/tui.calendar-1.15.3/css/tui-calendar.css',
        'css/custom-datatables.css',
        'css/custom-layout-navbar.css',
        'css/custom-modules.css',
    ];
    public $js = [
        // 'templete/limitless/assets/js/core/libraries/jquery.min.js',
        'js/socket-172.io.min.js',
        'templete/limitless/assets/js/core/libraries/bootstrap.min.js',
        'templete/limitless/assets/js/plugins/loaders/pace.min.js',
        'templete/limitless/assets/js/plugins/loaders/blockui.min.js',
        'templete/limitless/assets/js/plugins/ui/nicescroll.min.js',
        'templete/limitless/assets/js/plugins/ui/drilldown.js',
        'templete/limitless/assets/js/plugins/visualization/d3/d3.min.js',
        'templete/limitless/assets/js/plugins/visualization/d3/d3_tooltip.js',
        'templete/limitless/assets/js/plugins/forms/styling/switchery.min.js',
        'templete/limitless/assets/js/plugins/forms/styling/uniform.min.js',
        'templete/limitless/assets/js/plugins/forms/selects/bootstrap_multiselect.js',
        'templete/limitless/assets/js/plugins/ui/moment/moment.min.js',
        'templete/limitless/assets/js/plugins/pickers/pickadate/picker.js',
        'templete/limitless/assets/js/plugins/pickers/pickadate/picker.date.js',
        'templete/limitless/assets/js/plugins/pickers/pickadate/picker.time.js',
        'templete/limitless/assets/js/plugins/pickers/anytime.min.js',
        'templete/limitless/assets/js/plugins/pickers/daterangepicker.js',
        'templete/limitless/assets/js/plugins/notifications/pnotify.min.js',
        'templete/limitless/assets/js/plugins/tables/datatables/datatables.min.js',
        'templete/limitless/assets/js/plugins/tables/datatables/dataTables.buttons.min.js',
        'templete/limitless/assets/js/plugins/tables/datatables/extensions/select.min.js',
        'templete/limitless/assets/js/plugins/tables/datatables/dataTables.scroller.min.js',
        'templete/limitless/assets/js/plugins/forms/selects/select2.min.js',
        'templete/limitless/assets/js/plugins/forms/inputs/touchspin.min.js',
        'templete/limitless/assets/js/plugins/forms/styling/switchery.min.js',
        'templete/limitless/assets/js/plugins/forms/styling/switch.min.js',
        'templete/limitless/assets/js/plugins/velocity/velocity.min.js',
        'templete/limitless/assets/js/plugins/velocity/velocity.ui.min.js',
        'templete/limitless/assets/js/core/app.js',
        'templete/limitless/assets/js/pages/animations_velocity_ui.js',
        'templete/limitless/assets/js/pages/components_popups.js',
        'templete/limitless/assets/js/plugins/forms/wizards/steps.min.js',
        'templete/limitless/assets/js/plugins/forms/wizards/stepy.min.js',
        'templete/limitless/assets/js/plugins/forms/validation/validate.min.js',
        'js/fixedColumns.js',
        'js/jquery-block-ui.js',
        'js/dialog.js',
        'js/jquery.nestable.js',
        'js/swiper.min.js',
        'js/isotope-docs/js/isotope-docs.min.js',
        'js/font-icon-picker/jquery.fonticonpicker.min.js',
        'js/dataTables.rowsGroup.js',
        'js/webcam.min.js',
        'templete/limitless/assets/js/plugins/forms/tags/tagsinput.min.js',
        'templete/limitless/assets/js/plugins/forms/tags/tokenfield.min.js',
        'vendor/bower/crypto-js/crypto-js.js',
        'vendor/bower/i18next/i18next.min.js',
        'vendor/bower/i18next-xhr-backend/i18nextXHRBackend.min.js',
        'vendor/bower/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js',
        'vendor/bower/bootstrap-timepicker/js/bootstrap-timepicker.js',
        'js/jquery.bootstrap-duallistbox.js',
        'js/docoHotKeys.js',
        'js/docoHealth.js',
        'js/jquery.formautofill.min.js',
        'js/fullcalendar.min.js',
        'js/scheduler.min.js',
        'js/locale-all.js',
        'js/custom-datatable.js',

        'js/jquery.inputmask.js',
        'js/jquery.inputmask.min.js',
        'js/jquery.inputmask.numeric.extensions.js',
        'js/jquery.inputmask.numeric.extensions.min.js',

        // 'templete/limitless/assets/lib/autocomplete/jquery.typeahead.min.js',
        // inject from antrian assets
        'js/jqClock.min.js',
        'js/jquery.redirect.js',
        'js/jquery.keyboard.min.js',
        'js/converColor.js',
        'js/dependent-dropdown.js',
        'templete/limitless/assets/js/core/libraries/jasny_bootstrap.min.js',
        'templete/limitless/assets/js/plugins/uploaders/fileinput/fileinput.min.js',
        'templete/limitless/assets/js/plugins/notifications/sweet_alert.min.js',
        'templete/limitless/assets/js/plugins/uploaders/fileinput/fileinput.min.js',
        'additional-packages/collect-js/collect.js',
        'additional-packages/chart-js/chart.js',
        'additional-packages/datepicker-bootstrap-1.9.1/js/bootstrap-datepicker.min.js',
        'additional-packages/jquery.timeline-2.1.3/js/jquery.timeline.min.js',
        'additional-packages/tui-code-snippet/tui-code-snippet.min.js',
        'additional-packages/tui.calendar-1.15.3/js/tui-calendar.js',
        'js/responsiveslides.min.js',
        'js/dataTables.rowGroup.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
