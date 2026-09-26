<?php

namespace app\components;

use Yii;
use \yii\base\Model;
use yii\helpers\Url;
use yii\helpers\Html;
use Mike42\Escpos\Printer;
use GuzzleHttp\Psr7\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use GuzzleHttp\Exception\RequestException;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use yii\helpers\ArrayHelper;
use \DateTime;
use app\components\DocoConstants;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class DocoHelpers
{
    public static $namaBulan = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"
    ];

    public static $namaBulanAbbr = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "Mei",
        "Jun",
        "Jul",
        "Agu",
        "Sep",
        "Okt",
        "Nov",
        "Des"
    ];

    public static $_hari_indo = [
        'Sun' => 'Minggu',
        'Mon' => 'Senin',
        'Tue' => 'Selasa',
        'Wed' => 'Rabu',
        'Thu' => 'Kamis',
        'Fri' => 'Jumat',
        'Sat' => 'Sabtu'
    ];

    /* Hari */
    protected static $_hari = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    public static $_bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * @param string $tanggal
     * @return string
     */

    public static function convertTo422($tanggal)
    {
        return preg_replace('/(\d{2})-(\d{2})-(\d{4})/', '$3-$2-$1', $tanggal);
    }

    /**
     * @param string $tanggal
     * @return string
     */

    public static function convertTo224($tanggal)
    {
        return preg_replace_callback('/(\d{4})-(\d{2})-(\d{2})/', function ($mtc) {
            if ($mtc[0] === '0000-00-00')
                return '';
            return $mtc[3] . '-' . $mtc[2] . '-' . $mtc[1];
        }, $tanggal);
    }

    /**
     * @param string $errorMessage
     *
     * @return json
     */

    public static function dataTabelsException($errorMessage = '')
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = \Yii::$app->request;
        $draw = $request->get('draw', 1);
        \Yii::$app->response->statusCode = 200;
        return [
            'data' => [],
            'draw' => $draw,
            'recordsFiltered' => 0,
            'recordsTotal' => 0,
            'errorMessage' => $errorMessage
        ];
    }

    /**
     * @param boolean $status
     *
     * @return string
     */
    public static function isActive($status = 0, $buttonType = false)
    {
        $classTxt = "text-danger";
        $classBtn = "label-danger";
        $label = \Yii::t('fe', "Tidak aktif");
        if ($status) {
            $classTxt = "text-success";
            $classBtn = "label-success";
            $label = \Yii::t('fe', "Aktif");
        }

        if ($buttonType)
            return '<span class="label ' . $classBtn . ' position-right">' . $label . '</span>';
        else
            return '<span class="' . $classTxt . '" style="font-weight:bold">' . $label . '</span>';
    }

    public function isActive2($status = 0, $additionaldata = [])
    {
        $checked = '';
        if ($status) {
            $checked = 'checked="checked"';
        }

        return '<input type="checkbox"
                    id="chk-' . $additionaldata[0] . '"
                    data-id="' . $additionaldata[0] . '"
                    data-module="' . $additionaldata[1] . 'change-status"
                    data-size="mini"
                    data-on-color="success"
                    data-off-color="danger"
                    data-on-text="Active"
                    data-off-text="Inactive" class="switch" ' . $checked . ' onclick="changeStatus(this);" >
                    <script>
                        $("#chk-' . $additionaldata[0] . '").bootstrapSwitch();
                        $("#chk-' . $additionaldata[0] . '").on("switchChange.bootstrapSwitch", function (e, data) {
                            c = confirm("Apakah anda yakin untuk mengganti status tersebut?");
                            if(c == true){
                                changeStatus(this);
                            }
                            else {
                                $("#chk-' . $additionaldata[0] . '").bootstrapSwitch("state", !data, true);
                            }
                        });
                    </script>';
    }

    /**
     * @param mix $data
     *
     * @return string
     */

    public static function encrypt($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @param mix $data
     *
     * @return string
     */

    public static function decrypt($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public static function multipleParseError(Model $model, $error, $attributes, $key)
    {
        $model->addError($attributes . "[{$key}]", $error);
    }

    /**
     * @param array $data
     * @param string $model
     *
     * @return array
     */

    public static function parseError(array $data, $model)
    {
        $result = [];
        if ($model) {
            foreach ($data as $key => $value) {
                if (preg_match('/(\[\d+\])/', $key, $out)) {
                    $newKey = preg_replace('/(\[\d+\])/', '', $key);
                    $result[$model . '[' . $newKey . ']' . $out[0]] = $value;
                } else {
                    $result[$model . '[' . $key . ']'] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * @param array $data
     * @param integer $statusCode
     *
     * @return json
     */

    public static function response($data, $statusCode = 200, $parse = '')
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $statusCode = isset($data['metadata']['status']) ? $data['metadata']['status'] : $statusCode;
        if ($parse && $statusCode == 422) {
            \Yii::$app->response->statusCode = $statusCode;
            $body = isset($data['response']) ? $data['response'] : [];

            if (!isset($body['data'])) {
                $body['data'] = $data;
            }

            $response = [
                'response' => $body
            ];
            $dataArray = is_array($body['data']) ? $body['data'] : [];
            $response['response']['data'] = self::parseError($dataArray, $parse);

            if (isset($data['return'])) {
                $response['response']['return'] = $data['return'];
            }
            return $response;
        } else {
            \Yii::$app->response->statusCode = $statusCode;
            if (isset($data['metadata'])) {
                unset($data['metadata']);
            }
            return $data;
        }
    }

    /**
     * @param array $data
     * @param integer $statusCode
     * @param string $message
     * @param array $data
     * @param array $additional_response
     *
     * @return json
     */

    public static function responseTemplate($status, $message, $data = [], $additional_response = [])
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->statusCode = $status;

        $return['metadata']['status'] = $status;
        $return['metadata']['message'] = $message;
        $return['response']['data'] = $data;

        if ($additional_response) {
            foreach ($additional_response as $key => $value) {
                $return['response'][$key] = $value;
            }
        }

        return $return;
    }

    /**
     * @param array $data
     * @param integer $statusCode
     *
     * @return json
     */

    public static function responseJsonString($str, $formName)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = json_decode($str, TRUE);
        $status = isset($result['metadata']['status']) ? $result['metadata']['status'] : 500;
        if ($status == 422) {
            foreach ($result['response'] as $row)
                $response['data'][$formName . "[" . $row["field"] . "]"] = [$row["message"]];
            $result['response'] = $response;
        }
        \Yii::$app->response->statusCode = $status;
        return $result;
    }

    /**
     * @param string $name
     * @param array $args
     *
     * @throws BadMethodCallException
     */

    public static function __callstatic($name, $args)
    {

        if (!empty($this) && property_exists($this, $name)) {
            return call_user_func_array(
                array(get_called_class(), $name),
                $args
            );
        }

        throw new \BadMethodCallException("Static method " . __CLASS__ . "::$name() doesn't exist");
    }

    /**
     * @param boolean $status
     *
     * @return string
     */
    public static function labelYesNo($confirm = 0, $buttonType = false)
    {
        $classTxt = "text-danger";
        $classBtn = "label-danger";
        $label = \Yii::t('fe', "Tidak");
        if ($confirm) {
            $classTxt = "text-success";
            $classBtn = "label-success";
            $label = \Yii::t('fe', "Ya");
        }

        if ($buttonType)
            return '<span class="label ' . $classBtn . ' position-right">' . $label . '</span>';
        else
            return '<span class="' . $classTxt . '" style="font-weight:bold">' . $label . '</span>';
    }

    /**
     * @param boolean $status
     *
     * @return string
     */
    public static function switchStatus($status, $id, $classname = 'change-status', $paramOn = 'Aktif', $paramOff = 'Tidak&nbsp;aktif')
    {
        $str = Html::checkbox('noname', "$status", [
            'class' => 'switch ' . $classname,
            'label' => false,
            'checked' => $status == 1,
            'data-id' => $id,
            'data-on-color' => 'success',
            'data-off-color' => 'danger',
            'data-size' => 'mini',
            'data-on-text' => \Yii::t('fe', $paramOn),
            'data-off-text' => \Yii::t('fe', $paramOff),
        ]);
        $str .= "<script>\$(\".switch\").bootstrapSwitch();</script>";
        return $str;
    }

    // Author : Budi

    public static function display_label($str, $custom = false, $customValue = null)
    {
        if ($custom) {
            return isset($str) && $str ? $customValue : '-';
        } else {
            return isset($str) && $str ? $str : '-';
        }
    }

    public static function getUmur($param, $umur_only = false, $parse = false)
    {
        if (!$parse) {
            $diff = date_diff(date_create(date('Y-m-d', strtotime($param))), date_create(date('Y-m-d')));
            if (!$umur_only) {
                return "umur " . $diff->y . " tahun " . $diff->m . " bulan " . $diff->d . " hari";
            } else {
                return $diff->y;
            }
        } else {
            $arr_umur_temp = explode(' ', $param);

            $arr_umur = [];
            $arr_umur['tahun'] = $arr_umur_temp[0];
            $arr_umur['bulan'] = $arr_umur_temp[2];
            $arr_umur['hari'] = $arr_umur_temp[4];

            return $arr_umur;
        }
    }

    /*
    * DocoHelpers::generateToolbar([
    *       'seacrh', ->penggunaan tanpa kondisi apapun
    *       'reset',
    *       'payment' => [
    *           'type'=>'link' -> untuk membuat link atau 'type'=>'button' -> untuk membuat button
    *           'title' => \Yii::t('fe', 'Pembayaran'), -> title button atau link yg ingin dibuat
    *           'icon' => 'fa fa-shopping-cart', -> icon button atau link yang ingin dibuat
    *           'method'=>'method yang dituju'
    *           'attributes' => [ ->tempat memasukkan atribut baru yang diperlukan
    *               'class'=>'data-payment', ->kelas tambahan yang dibutuhkan
    *               'data-target'=>'url-target', -> url tujuan yg diinginkan
    *               'data-options'=>'link/delete/pdf/excel/print/aksi/import/export/download' -> (gunakan link jika akan digunakan sebagai link), (delete jika berfungsi untuk mengahapus data), (pdf,excel atau print untuk mencetak dokumen), (aksi jika tombol merupakan trigger untuk aksi tanpa form)
    *               ''
    *            ]
    *        ]
    * ],'#newTable'); -> digunakan pas id datatable yg digunakan bukan example
    *
    * toolbar default class: add,search,reset,edit,detail,cancel,delete,print,pdf,excel,back,import,export,download
    *
    */

    //last edited by Rizqi Fitrianto
    //add class .btn-toolbar to handle click event
    //add attribute data-table to handle which table used

    public static function generateToolbar($options = array('search', 'reset'), $table = '#example')
    {
        $defaultUrl = Url::home() . (Yii::$app->controller->module->id . "/" . Yii::$app->controller->id);
        $tmpHtmls = [];

        $attributes = isset($options['search']['attributes']) ? $options['search']['attributes'] : [];
        $title = isset($options['search']['title']) ? $options['search']['title'] : Yii::t('fe', 'Cari');
        $icon = isset($options['search']['icon']) ? $options['search']['icon'] : 'fa fa-search';
        $htmls['search'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-filter',
                'data-parent' => '',
                'data-title' => "Pencarian (Enter)"
            ], $attributes)
        );

        $attributes = isset($options['reset']['attributes']) ? $options['reset']['attributes'] : [];
        $title = isset($options['reset']['title']) ? $options['reset']['title'] : Yii::t('fe', 'Muat ulang');
        $icon = isset($options['reset']['icon']) ? $options['reset']['icon'] : 'fa fa-refresh';
        $htmls['reset'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-reset',
                'data-parent' => '',
                'data-title' => "Memuat Ulang (F7)"
            ], $attributes)
        );

        $attributes = isset($options['add']['attributes']) ? $options['add']['attributes'] : [];
        $title = isset($options['add']['title']) ? $options['add']['title'] : Yii::t('fe', 'Tambah');
        $icon = isset($options['add']['icon']) ? $options['add']['icon'] : 'fa fa-plus';
        $htmls['add'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-add btn-toolbar',
                'data-target' => @$options['add']['attributes']['url'] ? @$options['add']['attributes']['url'] : $defaultUrl . "/create",
                'data-options' => 'link',
            ], $attributes)
        );

        $attributes = isset($options['edit']['attributes']) ? $options['edit']['attributes'] : [];
        $title = isset($options['edit']['title']) ? $options['edit']['title'] : Yii::t('fe', 'Ubah');
        $icon = isset($options['edit']['icon']) ? $options['edit']['icon'] : 'fa fa-pencil';
        $htmls['edit'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar',
                'data-target' => @$options['edit']['attributes']['url'] ? @$options['edit']['attributes']['url'] : $defaultUrl . "/update?id=",
                'data-table' => $table
            ], $attributes)
        );

        $attributes = isset($options['detail']['attributes']) ? $options['detail']['attributes'] : [];
        $title = isset($options['detail']['title']) ? $options['detail']['title'] : Yii::t('fe', 'Detail');
        $icon = isset($options['detail']['icon']) ? $options['detail']['icon'] : 'fa fa-list-ul';
        $htmls['detail'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-detail btn-toolbar',
                'data-target' => @$options['detail']['attributes']['url'] ? @$options['detail']['attributes']['url'] : $defaultUrl . "/detail?id=",
                'data-table' => $table
            ], $attributes)
        );

        $attributes = isset($options['cancel']['attributes']) ? $options['cancel']['attributes'] : [];
        $title = isset($options['cancel']['title']) ? $options['cancel']['title'] : Yii::t('fe', 'Batal');
        $icon = isset($options['cancel']['icon']) ? $options['cancel']['icon'] : 'fa fa-close';
        $htmls['cancel'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-cancel btn-toolbar',
                'data-target' => @$options['cancel']['attributes']['url'] ? @$options['cancel']['attributes']['url'] : $defaultUrl . "/cancel?id=",
                'data-table' => $table
            ], $attributes)
        );

        $attributes = isset($options['delete']['attributes']) ? $options['delete']['attributes'] : [];
        $title = isset($options['delete']['title']) ? $options['delete']['title'] : Yii::t('fe', 'Hapus');
        $icon = isset($options['delete']['icon']) ? $options['delete']['icon'] : 'fa fa-trash';
        $htmls['delete'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-delete ' . ((isset($options['delete']['withoutClassToolbar']) && $options['delete']['withoutClassToolbar']) ? '' : 'btn-toolbar'),
                'data-target' => @$options['delete']['attributes']['url'] ? @$options['delete']['attributes']['url'] : $defaultUrl . "/delete?id=",
                'data-options' => 'delete',
                'data-table' => $table,
            ], $attributes)
        );

        $attributes = isset($options['print']['attributes']) ? $options['print']['attributes'] : [];
        $title = isset($options['print']['title']) ? $options['print']['title'] : Yii::t('fe', 'print');
        $icon = isset($options['print']['icon']) ? $options['print']['icon'] : 'fa fa-print';
        $htmls['print'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-print btn-toolbar',
                'data-target' => @$options['print']['attributes']['url'] ? @$options['print']['attributes']['url'] : $defaultUrl . "/export-print?",
                'data-options' => 'print',
                'data-table' => $table
            ], $attributes)
        );

        $attributes = isset($options['pdf']['attributes']) ? $options['pdf']['attributes'] : [];
        $title = isset($options['pdf']['title']) ? $options['pdf']['title'] : Yii::t('fe', 'pdf');
        $icon = isset($options['pdf']['icon']) ? $options['pdf']['icon'] : 'fa fa-file-pdf-o';
        $htmls['pdf'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-pdf btn-toolbar',
                'data-target' => @$options['pdf']['attributes']['url'] ? @$options['pdf']['attributes']['url'] : $defaultUrl . "/export-pdf?",
                'data-options' => 'pdf',
                'data-table' => $table
            ], $attributes)
        );

        $attributes = isset($options['excel']['attributes']) ? $options['excel']['attributes'] : [];
        $title = isset($options['excel']['title']) ? $options['excel']['title'] : Yii::t('fe', 'excel');
        $icon = isset($options['excel']['icon']) ? $options['excel']['icon'] : 'fa fa-file-excel-o';
        $htmls['excel'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-excel btn-toolbar',
                'data-target' => @$options['excel']['attributes']['url'] ? @$options['excel']['attributes']['url'] : $defaultUrl . "/export-excel?",
                'data-options' => 'excel',
                'data-table' => $table
            ], $attributes)
        );
        if (isset($attributes['data-visible'])) {
            if ($attributes['data-visible'] == false) {
                unset($htmls['excel']);
            }
        }

        $attributes = isset($options['save']['attributes']) ? $options['save']['attributes'] : [];
        $title = isset($options['save']['title']) ? $options['save']['title'] : Yii::t('fe', 'Simpan');
        $icon = isset($options['save']['icon']) ? $options['save']['icon'] : 'fa fa-floppy-o';
        $htmls['save'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-save',
                'id' => 'btn-submit',
                'data-target' => @$options['save']['attributes']['form_id'] ? @$options['save']['attributes']['form_id'] : 'ajax-form',
                'onClick' => 'triggerSubmit(this);',
                'data-title' => "Simpan Data (alt + s)"
            ], $attributes)
        );

        /*
         *author : Budi
         * addtioneal : added save (submit button)
         */
        $attributes = isset($options['save-submit']['attributes']) ? $options['save-submit']['attributes'] : [];
        $title = isset($options['save-submit']['title']) ? $options['save-submit']['title'] : Yii::t('fe', 'Simpan');
        $icon = isset($options['save-submit']['icon']) ? $options['save-submit']['icon'] : 'fa fa-floppy-o';
        $htmls['save-submit'] = Html::submitButton(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-save-submit',
                'data-title' => "Simpan Data (alt + s)"
            ], $attributes)
        );

        $attributes = isset($options['back']['attributes']) ? $options['back']['attributes'] : [];
        $title = isset($options['back']['title']) ? $options['back']['title'] : Yii::t('fe', 'Kembali');
        $icon = isset($options['back']['icon']) ? $options['back']['icon'] : 'fa fa-arrow-left';
        $htmls['back'] = Html::a(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            @$options['back']['attributes']['href'] ? @$options['back']['attributes']['href'] : $defaultUrl . "/#",
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-back'
            ], $attributes)
        );
        /*
         *author : iqbal
         * addtioneal : added download and upload
         */
        $attributes = isset($options['import']['attributes']) ? $options['import']['attributes'] : [];
        $title = isset($options['import']['title']) ? $options['import']['title'] : Yii::t('fe', 'import');
        $icon = isset($options['import']['icon']) ? $options['import']['icon'] : 'fa fa-list-ul';
        $htmls['import'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-import',
                'data-target' => @$options['import']['attributes']['url'] ? @$options['import']['attributes']['url'] : $defaultUrl . "/import",
                'data-options' => 'import',
                // 'data-table'=> $table
            ], $attributes)
        );
        $attributes = isset($options['download']['attributes']) ? $options['download']['attributes'] : [];
        $title = isset($options['download']['title']) ? $options['download']['title'] : Yii::t('fe', 'Download Template');
        $icon = isset($options['download']['icon']) ? $options['download']['icon'] : 'fa fa-file-excel-o';
        $htmls['download'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-excel-download btn-toolbar',
                'data-target' => @$options['download']['attributes']['url'] ? @$options['download']['attributes']['url'] : $defaultUrl . "/download-excel",
                'data-options' => 'download',
                'target' => '_blank',
                'onClick' => 'downloadSubmit(this);'
            ], $attributes)
        );

        $attributes = isset($options['export']['attributes']) ? $options['export']['attributes'] : [];
        $title = isset($options['export']['title']) ? $options['export']['title'] : Yii::t('fe', 'Export Excel');
        $icon = isset($options['export']['icon']) ? $options['export']['icon'] : 'fa fa-file-excel-o';
        $htmls['export'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-export-download btn-toolbar',
                'data-target' => @$options['export']['attributes']['url'] ? @$options['export']['attributes']['url'] : $defaultUrl . "/export-download-excel?",
                'data-options' => 'export',
                'data-table' => $table
            ], $attributes)
        );

        $attributes = isset($options['upload']['attributes']) ? $options['upload']['attributes'] : [];
        $title = isset($options['upload']['title']) ? $options['upload']['title'] : Yii::t('fe', 'Download Template');
        $icon = isset($options['upload']['icon']) ? $options['upload']['icon'] : 'fa fa-file-excel-o';
        $htmls['upload'] = Html::button(
            '<b><i class="' . $icon . '"></i></b>' . $title,
            array_merge([
                'class' => 'btn btn-info btn-labeled btn-xs data-excel-upload btn-toolbar',
                // 'data-target' => @$options['upload']['attributes']['url']? @$options['upload']['attributes']['url'] : $defaultUrl."/upload-file",
                'data-options' => 'upload',
                'target' => '_blank',
            ], $attributes)
        );

        if (is_array($options)) {
            if (count($options) == 0) {
                $tmpHtmls = $htmls;
            } else {
                foreach ($options as $key => $val) {
                    $index = is_int($key) ? $val : $key;
                    if (isset($htmls[$index])) {
                        $tmpHtmls[$index] = $htmls[$index];
                    } else {
                        if (is_array($val)) {
                            $option = $options[$index];
                            if (isset($option['attributes']['class'])) {
                                $option['attributes']['class'] .= ' btn btn-info btn-labeled btn-xs btn-toolbar';
                            } else {
                                $option['attributes']['class'] = 'btn btn-info btn-labeled btn-xs btn-toolbar btn-' . $index;
                            }
                            if (isset($option['method'])) {
                                $url = $defaultUrl . "/" . $option['method'];
                            }
                            $option['attributes']['data-table'] = $table;

                            if (@$option['attributes']['href'])
                                $url = $option['attributes']['href'];
                            if (@$option['attributes']['url'])
                                $url = $option['attributes']['url'];

                            if (@$option['type'] == 'link') {
                                $tmpHtmls[$index] = Html::a(
                                    '<b><i class="' . (@$option['icon']) . '"></i></b>' . Yii::t('fe', '' . (@$option['title']) . ''),
                                    $url,
                                    @$option['attributes']
                                );
                            } else {
                                $tmpHtmls[$index] = Html::button(
                                    '<b><i class="' . (@$option['icon']) . '"></i></b>' . Yii::t('fe', '' . (@$option['title']) . ''),
                                    @$option['attributes']
                                );
                            }

                            if (isset($option['attributes']['data-visible'])) {
                                if ($option['attributes']['data-visible'] == false) {
                                    unset($tmpHtmls[$index]);
                                }
                            }

                        } else {
                            $tmpHtmls[$index] = $val;
                        }
                    }
                }
            }
        }

        return implode("\n", $tmpHtmls);
    }

    public static function exportExcel($title, $results, $excelHeader = array(), $options = array())
    {
        // Directory Creation
        $uploadPath = @$options['uploadPath'];
        $fileprefix = @$options['filePrefix'] ? @$options['filePrefix'] . "-" : ucfirst(\Yii::$app->controller->module->id) . "-";

        if (!$uploadPath)
            $uploadPath = "../../uploads";
        if (!is_dir($uploadPath))
            mkdir($uploadPath, 0777);

        $fileName = str_replace(" ", "-", $fileprefix . (Yii::t("app", $title)));
        $filePath = $uploadPath . '/' . $fileName . '.xlsx';
        $downloadPath = $uploadPath . '/' . $fileName . '.xlsx';

        // Pivot Creation
        $alphabet = range('A', 'Z');

        $alphabet2 = function () {
            $data = array();
            foreach (range('A', 'Z') as $char) {
                $data[] = 'A' . $char;
            }
            return $data;
        };

        $alphabet = array_merge($alphabet, $alphabet2());

        $arrayData = array();
        $firstRow = isset($results[0]) ? $results[0] : null;

        // If Object
        if (is_object($firstRow)) {
            foreach ((array)$firstRow as $b) {
                $firstRow = $b;
                break;
            }
        }

        if ($firstRow != null) {
            $data = array();
            $data[] = "No";
            foreach ($firstRow as $key => $val)
                $data[] = Yii::t("app", ucfirst(str_ireplace("_", " ", $key)));
            $arrayData[] = $data;

            $no = 1;
            foreach ($results as $row) {
                $data = array();
                $data[] = $no;
                foreach ($row as $key => $val)
                    $data[] = $val;
                $arrayData[] = $data;
                $no++;
            }
        }

        // File Creation
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("Anonymous")
            ->setLastModifiedBy("Anonymous")
            ->setTitle("Office 2007 XLSX Anonymous Document")
            ->setSubject("Office 2007 XLSX Anonymous Document")
            ->setDescription("Anonymous document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Anonymous result file");

        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        $firstColumn = $alphabet[0];
        $lastColumn = $alphabet[($firstRow ? count($firstRow) : 0)];

        // Header Creation
        $sheet->mergeCells("{$firstColumn}{$row}:{$lastColumn}{$row}");
        $sheet->setCellValue("{$firstColumn}{$row}", Yii::t("app", $title));
        $row++;

        foreach ($excelHeader as $key => $val) {
            $sheet->mergeCells("{$firstColumn}{$row}:{$lastColumn}{$row}");
            $sheet->setCellValue("{$firstColumn}{$row}", Yii::t("app", $key) . " : {$val}");
            $row++;
        }
        $row++; // Row = 5

        // Body Creation
        $startTabel = "{$firstColumn}{$row}";
        $thStart = $row;

        $spreadsheet->getActiveSheet()->fromArray(
            $arrayData,
            NULL,
            $startTabel
        );

        $row += count($arrayData);

        // Sheet Header
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
        );
        $sheet->getStyle('A1')->applyFromArray($styleArray);

        // Table border
        $styleArray = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
        );
        $sheet->getStyle("{$firstColumn}{$thStart}:{$lastColumn}" . ($row - 1))->applyFromArray($styleArray);

        // Table header
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
            'fill' => array(
                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
                'endcolor' => array(
                    'argb' => 'FFFFFFFF',
                ),
            ),
        );
        $sheet->getStyle("{$firstColumn}{$thStart}:{$lastColumn}{$thStart}")->applyFromArray($styleArray);
        $sheet->setAutoFilter("{$firstColumn}{$thStart}:{$lastColumn}{$thStart}");

        // Auto size columns for each worksheet
        foreach ($alphabet as $char)
            $sheet->getColumnDimension($char)->setAutoSize(true);

        // File Writing
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }

    //edited by Rizqi Fitrianto
    //add downloadFile as Global function to download excel file
    //07-March-2018
    public static function downloadFile($filename, $stream = false)
    {

        if (file_exists($filename) && $stream) {
            $file = basename($filename);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($filename);
            die();
        }
        $path = \Yii::getAlias('@download');
        $filenames = basename($filename);
        $explodeNames = explode('-', $filenames);
        $moduleFile = $path . '/' . $explodeNames[0];
        $downloadPath = $moduleFile . '/' . basename($filenames);
        if (!file_exists($moduleFile)) {
            mkdir($moduleFile, 0777, true);
        }

        $file = basename($filename);
        $fp = fopen($downloadPath, 'wb');
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $file);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($downloadPath);
        exit;
    }

    /**
     * Mengubah Format Tanggal Menjadi Regular Format / SQL Format
     * Contoh:
     * SQL -> Regular
        $contoh = "2017-02-01 07:10:15"
        => DocoHelpers::convDateTime($contoh)
        => "01 Februari 2017 07:10:15"
     * Regular -> SQL
        $contoh = "01 Februari 2017 07:10:15"
        => DocoHelpers::convDateTime($contoh)
        => "2017-02-01 07:10:15"
     * With isAbbr
        $contoh = "2017-02-01 07:10:15"
        => DocoHelpers::convDateTime($contoh,true)
        => "01 Feb 2017 07:10:15"
     * With isjam
        $contoh = "2017-02-01 07:10:15"
        => DocoHelpers::convDateTime($contoh,true,true)
        => "01 Feb 2017 07:10:15"
     * Without isjam
        $contoh = "2017-02-01 07:10:15"
        => DocoHelpers::convDateTime($contoh,true,false)
        => "01 Feb 2017"
     * @param string $tanggal
     * @param boolean $isAbbr
     * @return success string format changed
     * @return failed string format unchanged
     */
    public static function convDateTime($tanggal, $isAbbr = false, $isJam = true)
    {
        if (empty($tanggal)) {
            return '-';
        }

        $month = DocoHelpers::$namaBulan;
        if ($isAbbr) {
            $month = DocoHelpers::$namaBulanAbbr;
        }



        $matchSql = [];
        $matchNormalDate = [];
        $returnDate = '';
        /*
        * format: "YYYY-MM-DD 00:00:00"
        */
        $regSql = '/(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})/';
        /*
        * format: "DD MM YYYY 00:00:00"
        */
        $regNormalDate = '/(\d{1,2}) ([a-zA-Z]{1,}) (\d{4}) (\d{1,2}):(\d{1,2}):(\d{1,2})/';

        preg_match_all($regSql, $tanggal, $matchSql, PREG_SET_ORDER, 0);
        preg_match_all($regNormalDate, $tanggal, $matchNormalDate, PREG_SET_ORDER, 0);
        if (count($matchSql) > 0) {
            $edate = $matchSql[0];
            $edate[2]--;
            $returnDate = $edate[3] . ' ' . $month[$edate[2]] . ' ' . $edate[1] . ' ' . $edate[4] . ':' . $edate[5] . ':' . $edate[6];
            if ($isJam === false) {
                $returnDate = $edate[3] . ' ' . $month[$edate[2]] . ' ' . $edate[1];
            }
        } elseif (count($matchNormalDate) > 0) {
            $edate = $matchNormalDate[0];
            $convMonth = array_search($edate[2], $month);
            /*
            * return original param if false
            */
            if ($convMonth === false) {
                if ($isAbbr === false) {
                    $convMonth = array_search($edate[2], DocoHelpers::$namaBulanAbbr);
                }
                if ($convMonth === false) {
                    return $tanggal;
                }
            }
            $convMonth++;
            $returnDate = $edate[3] . '-' . $convMonth . '-' . $edate[1] . ' ' . $edate[4] . ':' . $edate[5] . ':' . $edate[6];
            $returnDate = date('Y-m-d h:i:s', strtotime($returnDate));
        } else {
            $returnDate = $tanggal;
        }

        return $returnDate;
    }

    /**
     * @todo format number
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    static public function formatNumber($var, $null = true, $fractional = false, $roundTo = 2)
    {
        $var = round($var, $roundTo);
        $var = str_replace('.', ',', $var);
        if ($null === true && $var == 0)
            return $var;

        if ($null === false && ($var == 0 || $var == "")) {
            return "0";
        }

        if ($fractional) {
            $var = sprintf('%.2f', $var);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1.$2', $var);
            if ($replaced != $var) {
                $var = $replaced;
            } else {
                break;
            }
        }
        return $var;
    }

    /**
     * @todo Rupiah helper
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    static public function rupiahDisplay($var, $null = true, $fractional = false)
    {
        $rupiah = self::formatNumber($var, $null, $fractional);
        return $rupiah != "" && $rupiah != "N/A" ? "Rp. " . $rupiah : $rupiah;
    }

    public static function downloadPdf(Response $response, $path, $nameFile = '')
    {
        $body = $response->getHeaders();
        $default = isset($body['file-name'][0]) ? $body['file-name'][0] : '';
        if (file_exists($path)) {
            $nameFile = $nameFile ? $nameFile : $default;
            $nameFile = preg_replace('/\.\w+/', '', $nameFile);
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $nameFile . '.pdf"');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            ob_clean();
            flush(); // Flush system output buffer
            readfile($path);
            exit;
            // ob_clean();
            // flush();
            // return Yii::$app->response->sendFile($path,$nameFile . '.pdf');
        }
    }
    public static function breadcrumbs($breadcrumbs)
    {
        $home_titel = empty($breadcrumbs[0]['label']) ? '' : $breadcrumbs[0]['label'];
        $home_url = empty($breadcrumbs[0]['url'][0]) ? '' : $breadcrumbs[0]['url'][0];

        unset($breadcrumbs[0]);

        return [
            'homeLink' => [
                'label' => \Yii::t('yii', $home_titel),
                'url' => $home_url,
            ],
            'links' => isset($breadcrumbs) ? $breadcrumbs : [],
            'options' => ['class' => 'breadcrumb breadcrumb-arrows']
        ];
    }
    public static function previewPdf($path, Response $response = null, $isDeleteFile = false)
    {
        $filename = $path;
        if (file_exists($path)) {
            $body = !empty($response) ? $response->getHeaders() : [];
            $default = isset($body['file-name'][0]) ? $body['file-name'][0] : '';
            $nameFile = $default ? $default : $filename;
            $nameFile = preg_replace('/\.\w+/', '', $nameFile);
            if(strpos($nameFile, "/var") !== false){
                $convertNameFile = explode('/', $nameFile);
                $nameFileNew = end($convertNameFile);
            } else{
                $nameFileNew = $nameFile;
            }
            header('Content-Description: File Transfer');
            header("Content-Type: application/pdf");
            header("Content-Disposition: inline; filename=\"" . $nameFileNew . ".pdf\"");
            header("Content-Transfer-Encoding: binary");
            header("Expires: 0");
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Pragma: public");
            header('Content-Length: ' . filesize($filename));
            ob_clean();
            flush();
            readfile($filename);
            if($isDeleteFile) {
                unlink($path);
            }
            die();
        }
    }

    public static function daftarBulan()
    {
        return array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    }

    /**
     * @todo Helper convert date indo to english for kartik datetimepicker / datepicker
     * @param date date
     * @param boolean is_datetime
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function convertIndoToEnglish($date, $is_datetime = false, $is_abbr = false)
    {
        $headers = Yii::$app->request->headers;
        $lang = $headers->get('lang');
        $months = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $monthsAbbr = [
            'Jan' => 1,
            'Feb' => 2,
            'Mar' => 3,
            'Apr' => 4,
            'Mei' => 5,
            'Jun' => 6,
            'Jul' => 7,
            'Agu' => 8,
            'Ags' => 8,
            'Sep' => 9,
            'Okt' => 10,
            'Nov' => 11,
            'Des' => 12
        ];
        if ($lang == "ID") {
            $replace = explode(' ', $date);
            // get month from list months
            if (isset($replace[1])) {
                $tanggal = $replace[0];
                $bulan = $replace[1];
                $tahun = $replace[2];
            } else {
                $replace = explode('-', $date);
                $tanggal = $replace[0];
                $bulan = $replace[1];
                $tahun = $replace[2];
            }

            $usedMonths = $months;
            if ($is_abbr) {
                $usedMonths = $monthsAbbr;
            }
            if (isset($usedMonths[$bulan])) {
                $convert_month = $usedMonths[$bulan];
                if ($is_datetime) {
                    $jam = $replace[3];
                    $full_date = $tahun . '-' . $convert_month . '-' . $tanggal . ' ' . $jam;
                } else {
                    $full_date = $tahun . '-' . $convert_month . '-' . $tanggal;
                }
            } else {
                if ($is_datetime) {
                    $full_date = date('Y-m-d H:i:s', strtotime($date));
                } else {
                    $full_date = date('Y-m-d', strtotime($date));
                }
            }
        } else {
            if ($is_datetime) {
                $full_date = date('Y-m-d H:i:s', strtotime($date));
            } else {
                $full_date = date('Y-m-d', strtotime($date));
            }
        }

        return $full_date;
    }


    /**
     * Copies contents from $source to $dest, optionally ignoring SVN meta-data
     * folders (default).
     * @param string $source
     * @param string $dest
     * @param boolean $ignoreSvnFolders
     * @return boolean true on success false otherwise
     */
    public function copyDirectory($source, $dest, $excludeSvnFolders = true)
    {
        $sourceHandle = opendir($source);
        if (!$sourceHandle) {
            return false;
        }

        while ($file = readdir($sourceHandle)) {
            if ($file == '.' || $file == '..')
                continue;
            if ($excludeSvnFolders && $file == '.svn')
                continue;
            if (is_dir($source . '/' . $file)) {
                if (!file_exists($dest)) {
                    mkdir($dest, 0755, true);
                }
                self::copyDirectory($source . '/' . $file, $dest . '/' . $file, $excludeSvnFolders);
            } else {
                if (!file_exists($dest)) {
                    mkdir($dest, 0755, true);
                }
                copy($source . '/' . $file, $dest . '/' . $file);
            }
        }
        return true;
    }
    /*
    * @author: iqbal@docotel
    * @desc: multiplecopy file in folder to new file and create new zip
    * @created: 20 Desember 2018
    */
    public function multipleCopyToFolders($prefixModule, $data = [], $targetPath = '', $title)
    {
        if (count($data) == 0) {
            return false;
        }

        foreach ($data as $key => $value) {
            $alias = Yii::getAlias("@media");
            $filename = $value . '.zip';
            $pathToFile = Yii::getAlias("@download") . $filename;
            $pathInfo = pathInfo($alias . $value);
            $parentPath = $pathInfo['dirname'];
            $dirName = $pathInfo['basename'];
            if (file_exists($pathToFile)) {
                unlink($pathToFile);
            }
            $copyDirectory = self::copyDirectory($alias . $value, $alias . $targetPath);
        }
        if ($copyDirectory) {
            $targetInfo = pathInfo($alias . $targetPath);
            $targetPath = $targetInfo['dirname'];
            $targetDirName = $targetInfo['basename'];
            self::zipMaker($prefixModule . $targetDirName, $title . ' ' . $targetDirName);
        }
        return true;
    }

    /*
    * @author: Rizqi Febian
    * @desc: fungsi untuk download folder sebagai zip, sementara hanya bisa folder,
    * pengembangan selanjutnya baru multiple file
    * @created: 31 Juli 2018
    */
    public function zipMaker($files, $pathToFile = 'file', $dir = true)
    {
        $alias = Yii::getAlias("@media");
        $filename = $pathToFile . '.zip';
        $pathToFile = Yii::getAlias("@download") . '/' . $filename;

        $pathInfo = pathinfo($alias . $files);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        if (file_exists($pathToFile)) {
            unlink($pathToFile);
        }

        $z = new \ZipArchive();
        if ($z->open($pathToFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $z->addEmptyDir($dirName);
            self::folderToZip($alias . $files, $z, strlen("$parentPath/"));
            $z->close();
        } else {
            throw new \Exception("Cannot create ZIP file.");
        }

        // Pastikan tidak ada output sebelum header
        if (ob_get_length()) ob_clean();
        flush();

        header("Content-Type: application/zip");
        header('Content-Disposition: attachment; filename="' . basename($pathToFile) . '"');
        header("Content-Length: " . filesize($pathToFile));
        header("Content-Transfer-Encoding: binary");
        header("Pragma: no-cache");
        header("Expires: 0");

        readfile($pathToFile);
        exit;
    }
    /*
    * @author: Rizqi Febian
    * @desc: fungsi untuk generate folder menjadi zip, sementara hanya bisa folder,
    * pengembangan selanjutnya baru multiple file
    * @created: 31 Juli 2018
    */
    public static function folderToZip($folder, &$zipFile, $exclusiveLength)
    {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    /**
     * @todo Helper untuk ngambil berapa lama waktu tunggu
     * @param date date1
     * @param date date 2
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function getLamaTunggu($date1, $date2)
    {
        $date_1 = date_create($date1);
        $date_2 = date_create($date2);
        $diff = date_diff($date_1, $date_2);

        $waktu_tunggu = $diff->format('%d hari %h jam %i menit %s detik');

        return $waktu_tunggu;
    }

    public function encryptInacbg($data, $key)
    {
        $key = hex2bin($key);
        if (mb_strlen($key, "8bit") !== 32) {
            throw new \Exception("Need 256bit key");
        }

        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $iv = openssl_random_pseudo_bytes($iv_size);
        // $iv = random_bytes($iv_size);

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        $signature = mb_substr(hash_hmac("sha256", $encrypted, $key, true), 0, 10, "8bit");

        $encoded = chunk_split(base64_encode($signature . $iv . $encrypted));
        return $encoded;
    }

    public function decryptInacbg($str, $strkey, $cons = false)
    {
        if ($cons) {
            $first = strpos($str, "\n") + 1;
            $last = strpos($str, "\n") - 1;
            $str = substr($str, $first, strlen($str) - $first - $last);
        }
        $key = hex2bin($strkey);
        if (mb_strlen($key, "8bit") !== 32) {
            throw new \Exception("Need 256bit key");
        }
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");

        $decoded = base64_decode($str);
        $signature = mb_substr($decoded, 0, 10, "8bit");
        $iv = mb_substr($decoded, 10, $iv_size, "8bit");
        $encrypted = mb_substr($decoded, $iv_size + 10, NULL, "8bit");

        $calc_signature = mb_substr(hash_hmac("sha256", $encrypted, $key, true), 0, 10, "8bit");
        if (!DocoHelpers::inacbgCompare($signature, $calc_signature)) {
            return "Signature not match";
        }
        $decrypted = openssl_decrypt($encrypted, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

    public function inacbgCompare($a, $b)
    {
        if (strlen($a) !== strlen($b)) return false;

        $result = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }

        return $result == 0;
    }

    public function restInacbgs($postdata)
    {
        $ini = @parse_ini_file('../config/env/.env', true);
        $key = isset($ini['inacbg']['bpjs_key']) ? $ini['inacbg']['bpjs_key'] : DocoConstants::BPJS_KEY;
        $json_request = json_encode($postdata);
        $inacbgsent = DocoHelpers::encryptInacbg($json_request, $key);

        $header = isset($ini['inacbg']['header']) ? [$ini['inacbg']['header']] : ["Content-Type:application/x-www-form-urlencoded"];
        $url = isset($ini['inacbg']['url']) ? $ini['inacbg']['url'] : "http://192.168.200.28/e-klaim/ws.php";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $inacbgsent);

        $response = curl_exec($ch);
        return DocoHelpers::decryptInacbg($response, $key, true);
    }

    public function printStrukAntrian($data = [])
    {
        try {
            $connector = new WindowsPrintConnector("smb://DOCONB-143/epson-dell-merah");
            // $connector = new FilePrintConnector("LPT1");

            $printer = new Printer($connector);

            $printer->text($data['time'] . "\n");
            $printer->feed();
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(1, 1);
            $printer->text($data['first_header'] . "\n");
            $printer->feed();
            $printer->setTextSize(1, 1);
            $printer->text($data['second_header'] . "\n");
            $printer->setEmphasis(false);
            $printer->feed(1);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(5, 5);
            $printer->text($data['body'] . "\n");
            $printer->setEmphasis(false);
            $printer->feed(2);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(1, 1);
            $printer->text($data['first_footer'] . "\n");
            $printer->text($data['second_footer'] . "\n");
            $printer->setEmphasis(false);
            $printer->feed(3);


            $printer->cut();
            $printer->pulse();

            $printer->close();

            return "true";
        } catch (\Exception $e) {
            $printer->close();
            return "false";
        }
    }

    /**
     * @author Rizal
     * @since 2018-04-16 11:25:56
     * @param int number what will converted
     * @param boolean number what will converted
     * @return string terbilang
     * @desc untuk convert angka ke tulisan
     */
    public static function Terbilang($e, $is_antrian = false)
    {

        $abil = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        if($is_antrian) {
            $abil = array("Kosong", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        }
        $ratus = [200, 300, 400, 500, 600, 700, 800, 900];

        if ($e < 12)
            return " " . $abil[$e];
        elseif ($e < 20)
            return self::Terbilang($e - 10) . " Belas";
        elseif ($e < 100)
            return self::Terbilang($e / 10) . " Puluh" . self::Terbilang($e % 10);
        elseif ($e < 200)
            return " Seratus" . (($e - 100) > 0 ? self::Terbilang($e - 100) : '') . (($is_antrian && $e != 100) ? " " : "");
        elseif ($e < 1000)
            return self::Terbilang($e / 100) . " Ratus" . self::Terbilang($e % 100) .
                (($is_antrian && $e != in_array($e, $ratus)) ? " " : "");
        elseif ($e < 2000)
            return " Seribu" . (($e - 1000) > 0 ? self::Terbilang($e - 1000) : '');
        elseif ($e < 1000000)
            return self::Terbilang($e / 1000) . " Ribu" . self::Terbilang($e % 1000);
        elseif ($e < 1000000000)
            return self::Terbilang($e / 1000000) . " Juta" . self::Terbilang($e % 1000000);
    }

    public static function convertAntrian($value = 'PBU1048')
    {
        $huruf = [];
        $arr = str_split($value);
        $i = strlen($value);
        foreach ($arr as $str) {
            if (!is_numeric($str) || $str == 0) {
                $huruf[] = $str;
            } else {
                break;
            }
            $i--;
        }

        $res = array_merge($huruf, [substr($value, ($i * -1))]);

        $hurufConverted = [];
        foreach ($res as $key => $each) {
            if (is_numeric($each)) {
                $hurufConverted[] = trim(self::terbilang($each, true));
            } else {
                $hurufConverted[] = $each;
            }
        }

        return implode(' ', $hurufConverted);
    }

    protected function convertTerbilang($num)
    {
        $output = "";
        $num = str_pad($num, 36, "0", STR_PAD_LEFT);
        $group = rtrim(chunk_split($num, 3, " "), " ");
        $groups = explode(" ", $group);

        $groups2 = array();
        foreach ($groups as $g) {
            $groups2[] = $this->convertThreeDigit($g{
            0}, $g{
            1}, $g{
            2});
        }

        for ($z = 0; $z < count($groups2); $z++) {
            if ($groups2[$z] != "") {
                $output .= $groups2[$z] . $this->convertGroup(11 - $z) . ($z < 11 && !array_search(
                    '',
                    array_slice($groups2, $z + 1, -1)
                )
                    && $groups2[11] != '' && $groups[11]{
                    0} == '0' ? " and " : " ");
            }
        }

        $output = rtrim($output, " ");

        return $output;
    }

    public function generateBatFile($data = null, $name = null)
    {
        if ($data) {
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=" . $name . ".bat");

            if (is_array($data)) {
                foreach ($data as $i => $v) {
                    print $v . PHP_EOL;
                }
            } else {
                print $data . PHP_EOL;
            }
        } else {
            return false;
        }
    }

    public function generatePath($path = null)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return str_replace('/', '\\', $path);
        } else {
            return str_replace('\\', '/', $path);
        }
    }

    public function getNamaBulan($isAbbr = false)
    {
        $month = DocoHelpers::$namaBulanAbbr;
        if ($isAbbr) {
            $month = DocoHelpers::$namaBulan;
        }
        return json_encode($month);
    }

    public function getNamaHari()
    {
        $hari =  array_values(DocoHelpers::$_hari_indo);
        return json_encode($hari);
    }
    /**
     * @todo Helper untuk jam berdasarkan hari
     * @param date startDate
     * @param date endDate
     * @author iqbal@docotel.com
     */
    public function getDiffDateTime($startDate, $endDate)
    {

        // $firstDate = date_create($startDate);
        // $lastDate = date_create($endDate);
        // $diff = date_diff($firstDate, $lastDate);
        // $year = $diff->y;
        // $mounth = $diff->m;
        // $hour = $diff->h;
        // $days = $diff->days;

        // $firstHour = date('H', strtotime($startDate));
        // $resHoursfirst =  ((int)$firstHour == 0) ? 0 : 24 - (int)$firstHour;
        // $secondHour = date('H', strtotime($endDate));
        // $resHourssecond = 24 - (int)$secondHour;

        // if ($days == 0) {
        //     $resHours = (int)$secondHour - (int)$firstHour;
        // } else if ($days == 1) {
        //     $resHours = $resHoursfirst + $secondHour;
        // } else if ($days == 2) {
        //     $days = $days - 1;
        //     $hoursDays = 24 * $days;
        //     $resHours = $hoursDays + $resHoursfirst + $secondHour;
        // } else {
        //     $hoursDays = 24 * $days;
        //     $resHours = $hoursDays + $resHoursfirst + $secondHour;
        // }

        $firstDate = new DateTime($startDate);
        $lastDate = new DateTime($endDate);

        $diff = $lastDate->diff($firstDate);

        $year = $diff->y;
        $mounth = $diff->m;
        $days = $diff->days;
        $hours = $diff->h;
        $resHours = $hours + ($diff->days*24);

        $results = [
            'tahun' => ($year > 0) ? $year : $year + 1,
            'bulan' => ($mounth > 0) ? $mounth : $mounth + 1,
            'hari' => ($days > 0) ? $days : $days + 1,
            'jam' => $resHours
        ];
        return $results;
    }

    /**
     * @todo Helper Upload File Excel
     * @param
     * @author iqbal@docotel.com
     */
    public static function getUploadFileExcel($file)
    {
        try {
            $inputFileType = \PHPExcel_IOFactory::Identify($file);
            $objReader = \PHPExcel_IOFactory::CreateReader($inputFileType);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $e) {
            return [
                'sheet' => '',
                'highestRow' => 0,
                'highestColumn' => 'AMK',
            ];
        }
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        return [
            'sheet' => $sheet,
            'highestRow' => $highestRow,
            'highestColumn' => $highestColumn,
        ];
    }


    public function convertToAngka($string)
    {
        $explodedString = explode(',', $string);
        $result = str_replace(".", "", $explodedString[0]);

        if (isset($explodedString[1])) {
            $result = $result . '.' . $explodedString[1];
        }

        return $result;
    }

    public static function convertPointToComma($val)
    {
        return str_replace('.', ',', $val);
    }

    public function formatDecimal($number)
    {
        $result = number_format($number, 2, ".", ".");

        return $result;
    }

    /**
     * Macro of response
     *
     * @param Integer $httpCode Http Server code
     * @param String $message Message of response
     * @param Array $payloadResponse Payload of response will be assign to object data
     * @param Integer $customStatusCodeMeta
     * @return Array/Object
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public static function macroResponseJson($httpCode, $message, $payloadResponse = [], $customCodeMeta = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->statusCode = $httpCode;
        return [
            'meta' => [
                'result' => $httpCode < 300 && $httpCode >= 200 ? 'success' : 'failed',
                'message' => $message,
                'code' => !empty($customCodeMeta)  && is_int($customCodeMeta) ? $customCodeMeta : $httpCode,
                'title' => is_array($customCodeMeta) ? ArrayHelper::getValue($customCodeMeta, 'title') : null
            ],
            'data' => $payloadResponse
        ];
    }

    /**
     * Mapping array error validation message to one string
     *
     * @param Array $arrayMessage
     * @return String
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function mapMessageErrorValidation($arrayMessage, $firstRow = true)
    {
        $message = '';
        if ($firstRow) {
            $keysOfArray = array_keys($arrayMessage);
            $message = $arrayMessage[$keysOfArray[0]][0];
        } else {
            foreach ($arrayMessage as $value) {
                $message .= $value[0] . "\n";
            }
        }
        return $message;
    }

    /**
     * Mapping error message for form frontend response
     *
     * @param Array $errors
     * @param String $className
     * @return Array
     **/
    public static function mapErrorForm($errors, $className = null)
    {
        $result = [];
        foreach ($errors as $keyError => $value) {
            foreach ($value as $keyChild => $valueChild) {
                if (!empty($className)) {
                    $result[$className . "[$keyError]"][] = $valueChild;
                } else {
                    $result[$keyError . "[$keyChild]"] = $valueChild;
                }
            }
        }
        return $result;
    }

    /**
     * Guzzle Helper
     *
     * @param Class/Guzzle $guzzleClass
     * @param Array $optionGuzzle => two keys => url, method
     * @param Array $payloadData payload of request, two keys => form_params, query
     * @return Array
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function guzzleExec($guzzleClass, $optionGuzzle, $payloadData = [], $withStatusCode = false)
    {
        $result = [];
        $method = !isset($optionGuzzle['method']) ? 'get' : strtolower($optionGuzzle['method']);
        $withMetaData = ArrayHelper::getValue($optionGuzzle, 'with_metadata', false);
        if (isset($optionGuzzle['payload'])) {
            $payload = $optionGuzzle['payload'];
        } else {
            if ($method == 'get' || $method == 'delete') {
                $payload = [
                    'query' => isset($payloadData['query']) ? $payloadData['query'] : []
                ];
            } else {
                $payload = [
                    'query' => isset($payloadData['query']) ? $payloadData['query'] : [],
                    'form_params' => isset($payloadData['form_params']) ? $payloadData['form_params'] : [],
                ];
            }
        }
        if (isset($optionGuzzle['save_to']) && !empty($optionGuzzle['save_to'])) {
            $payload['save_to'] = $optionGuzzle['save_to'];
        }
        $isReturnResponse = isset($optionGuzzle['returnResponse']) && $optionGuzzle['returnResponse'];
        if ($isReturnResponse) {
            $result = [];
        }
        $successCallback = isset($optionGuzzle['success']) ? $optionGuzzle['success'] : null;
        try {
            $guzzleRequest = $guzzleClass->{$method}($optionGuzzle['url'], $payload);
            $response = json_decode($guzzleRequest->getBody(), true);
            if ($isReturnResponse) {
                if ( $successCallback ) {
                    return call_user_func($successCallback, $response['response'] );
                } else {
                    $result = $this->macroResponseJson(200, isset($response['response']['message']) ? $response['response']['message'] : 'Proses API Berhasil', $response['response']);
                }
            } else if ($withStatusCode) {
                return array_merge($response['response'], [
                    'httpStatusCode' => $guzzleRequest->getStatusCode()
                ]);
            } else if($withMetaData) {
                return $response;
            } else {
                return $response['response'];
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $httpStatusCode = $e->getResponse()->getStatusCode();
            if ($httpStatusCode >= 400 && $httpStatusCode <= 499) {
                // Client error
                $response = json_decode($e->getResponse()->getBody(), true);
                if ($isReturnResponse) {
                    $result = $this->macroResponseJson($httpStatusCode, isset($response['response']['message']) ? $response['response']['message'] : 'Proses API Gagal', $response['response'], ['title' => ArrayHelper::getValue($response, function($response, $default) {
                        return isset($response['response']['title']) ? $response['response']['title'] : (isset($response['response']['meta']['title']) ? $response['response']['meta']['title'] : 'Terjadi Kesalahan!');
                    })]);
                } else {
                    return array_merge(isset($response['response']) ? $response['response'] : $response, [
                        'httpStatusCode' => $httpStatusCode
                    ]);
                }
            } else {
                $this->logError($e);
                if ($isReturnResponse) {
                    $result = $this->macroResponseJson(500, 'Terjadi kesalahan pada server, silakan coba beberapa saat lagi.');
                } else {
                    throw new \Exception("Something went wrong on API");
                }
            }
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $this->logError($e);
            if ($isReturnResponse) {
                $result = $this->macroResponseJson(500, 'Terjadi kesalahan pada server, silakan coba beberapa saat lagi.');
            } else {
                throw new \Exception("Something went wrong on API");
            }
        } catch (RequestException $e) {
            $this->logError($e);
            if ($isReturnResponse) {
                $result = $this->macroResponseJson(500, 'Terjadi kesalahan pada server, silakan coba beberapa saat lagi.');
            } else {
                throw new \Exception("Something went wrong on API");
            }
        } catch (\Exception $e) {
            $this->logError($e);
            if ($isReturnResponse) {
                $result = $this->macroResponseJson(500, 'Terjadi kesalahan pada server, silakan coba beberapa saat lagi.');
            } else {
                throw new \Exception("Something went wrong on API");
            }
        }
        return $result;
    }

    /**
     * Logging exception
     *
     * @param Class/Exception $e
     * @author Tsani Nashrullah
     **/
    public function logError($e)
    {
        Yii::error([
            'Message' => $e->getMessage(),
            'File' => $e->getFile(),
            'Line' => $e->getLine(),
        ]);
    }

    /**
     * Call API datatable
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getDatatable($service, $option, $payload)
    {
        return $this->macroResponseJson(200, 'Berhasil mendapatkan data untuk tabel', $this->guzzleExec($service, $option, $payload));
    }

    /**
     * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     *
     * Fungsi get Data - request per 10 data
     * mencegah load data keseluruhan pada dropdown
     * Request data diambil dari parameter
     *
     * $url : api get data
     * $data_id : get id data - label id / parent id
     * $data_name : get value name untuk dropdown - output label nama (MAX 4 DATA)
     * $getRest : init master api
     * $get : multiple data parsing
     *
     * Infinity Scroll Select2
     * InfinityScrollSelect2
     *
     * Default Fungsi Query get Data
     *
     */
    public function paginationSelec2($url, $data_id, $data_name, $getRest, $get = null)
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page - 1) * 10;
        $parsing_data = [];
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!empty($get)) {
            $parsing_data = $get;
        } else {
            $parsing_data = [
                'query' => $get,
                'term' => $request->get('q'),
                'page' => $page,
                'offset' => $offset,
                'limit' => $limit
            ];
        }

        try {
            $result = $getRest->get($url, [
                'query' => $parsing_data
            ]);
            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $listString = [];
                foreach ($data_name as $name => $additional) {
                    if(!empty($value[$name]) && strtolower($additional) == strtolower(DocoConstants::RUPIAH)){
                        $value[$name] = self::rupiahDisplay($value[$name]);
                    }
                    $realName = !empty($value[$name]) ? $value[$name] : (!empty($value[$additional]) ? $value[$additional] : " ");
                    $listString[] = $realName;
                }
                $text = implode(" - ", $listString);
                $response[] = [
                    'id' => $value[$data_id],
                    'text' => $text,
                    'datavalue' => $value
                ];
            }
        } catch (RequestException $e) {
            $response['message'] = $e->getMessage();
        }
        return DocoHelpers::response([
            'result' => $response,
            'total_count' => count($response),
            'incomplete_results' => false,
            'pagination' => ['more' => count($response) === $limit ? true : false]
        ]);
    }

    /**
     *
     * fungsi return tanggal dan bulan bahasa indonesia
     * @param string $date format Y-m-d
     * @return array hari, bulan, tahun
     *
     */
    public static function getTanggalIndonesia($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $timestamp = strtotime($date);

        // get hari
        $day = date('N', $timestamp);
        // Get id hari
        $id_hari = $day;
        $day = self::$_hari[$day];

        // get bulan
        $month = date('n', $timestamp);
        $month = self::$_bulan[$month];

        $return = [
            'urutan_hari' => $id_hari,
            'hari' => $day,
            'tanggal' => date('d', $timestamp),
            'bulan' => $month,
            'tahun' => date('Y', $timestamp),
        ];

        return $return;
    }


    /**
     * Mapping array error validation message to one string
     *
     * @param Array $arrayMessage
     * @param Boolean $firstRow
     * @return String
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function mapErrorFormToString($arrayMessage, $firstRow = true)
    {
        $message = '';
        if (!$firstRow || isset($firstRow['arrayReturn'])) {
            if (isset($firstRow['arrayReturn'])) {
                $message = [];
            }
            foreach ($arrayMessage as $value) {
                if (isset($firstRow['arrayReturn'])) {
                    $message = array_merge($message, $value);
                } else {
                    $message .= $value[0] . "\n";
                }
            }
        } else {
            $keysOfArray = array_keys($arrayMessage);
            $message = $arrayMessage[$keysOfArray[0]][0];
        }
        return $message;
    }


    /**
     * Helper to convert date using custom format
     *
     * @param String $date
     * @param String $format default 'd-m-Y'
     * @return String
     * @author Tsani Nashrullah
     **/
    public static function convertDate($date, $format = 'd-m-Y')
    {
        $monthName = [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];
        $sortMonthName = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "Mei",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Des"
        ];
        $dayName = [
            "Minggu",
            "Senin",
            "Selasa",
            "Rabu",
            "Kamis",
            "Jum'at",
            "Sabtu"
        ];
        $result = '';
        $array_format = str_split($format);
        for ($i = 0; $i < count($array_format); $i++) {
            switch ($array_format[$i]) {
                case 'd':
                    $result .= date('d', strtotime($date));
                    break;
                case 'm':
                    $result .= $monthName[date('m', strtotime($date)) - 1];
                    break;
                case 'M':
                    $result .= $sortMonthName[date('m', strtotime($date)) - 1];
                    break;
                case 'Y':
                    $result .= date('Y', strtotime($date));
                    break;
                case 'H':
                    $result .= date('H', strtotime($date));
                    break;
                case 'i':
                    $result .= date('i', strtotime($date));
                    break;
                case 's':
                    $result .= date('s', strtotime($date));
                    break;
                case 'w':
                    $result .= $dayName[date('w', strtotime($date))];
                    break;
                case 'y':
                    $result .= date('y', strtotime($date));
                    break;
                default:
                    $result .= $array_format[$i];
                    break;
            }
        }
        return $result;
    }

    /**
     * This function will return string or array result from extract json from string
     *
     * @param String $string
     * @param String $key
     * @return Array/String
     * @author Tsani Nashrullah
     **/
    public static function jsonToArray($string, $key = null)
    {
        if (is_string($string)) {
            try {
                $newPayload = json_decode($string, true);
            } catch (\Exception $e) {
                $newPayload = $string;
            }
            return isset($newPayload[$key]) ? $newPayload[$key] : $newPayload;
        } else {
            return isset($string[$key]) ? $string[$key] : $string;
        }
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function crossUrl($type, $payload)
    {
        if (!isset($payload['ruangan_id']) || !isset($payload['modul']) || !isset($payload['instalasi_id']) || !isset($payload['url']) || !in_array($type, ['opento', 'jumpto'])) {
            return null;
        }
        // return '/'.$type.'/rajal'.'/'.$payload['instalasi_id'].'/'.$payload['ruangan_id'].'/'.urlencode(base64_encode($payload['url']));
        return '/' . $type . '/' . $payload['modul'] . '/' . $payload['instalasi_id'] . '/' . $payload['ruangan_id'] . '/' . bin2hex($payload['url']);
    }

    /**
     * @function : Check value is json
     *
     * @param str $string
     *
     * @return bool
     */
    public static function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function purifyHtml($html)
    {
        $br = ["<br />","<br>","<br/>"];
        return nl2br(htmlentities(wordwrap(str_ireplace($br, "\r\n", trim(preg_replace('~[\r\n]+~', '', $html))),50,"\n")));
    }

    public static function previewImg($path)
    {
        if ( file_exists($path) ) {
            $contentImg = base64_encode(file_get_contents($path));
            $src = 'data: '.mime_content_type($path).';base64,'.$contentImg;
            return "<img src='".$src."' class='img-responsive'>";
        } else {
            return "<p class='text-center'>File Tidak Ditemukan</p>";
        }
    }

    /**
     * @author Chacha Nurholis (chacha@sirs.co.id)
     *
     * @method Parsing Range Date [untuk kebutuhan filter rentang tanggal]
     * @param array $dates
     * @return array
     */
    public static function parsingRangeDate($dates)
    {
        if (!$dates) {
            $dates = date('j-M-Y') . ' - ' . date('j-M-Y');
        }

        $dates = explode(' - ', $dates);

        $startDate = !empty($dates[0]) ? $dates[0] : date('j-M-Y');
        $endDate   = !empty($dates[1]) ? $dates[1] : date('j-M-Y');

        return [
            'startDate' => date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00')),
            'endDate'   => date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'))
        ];
    }

    /**
     * @author Chacha Nurholis (chacha@sirs.co.id)
     *
     * @method Coalesce [memeriksa data dan jika kosong replace dengan custom]
     * @param string | number $data
     * @param strinb | number $replace
     * @return string | number
     */
    public static function coalesce($data, $replace)
    {
        if (empty($data)) {
            return $replace;
        }

        return $data;
    }

    public static function searchArray($value, $key, $array) {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $k;
            }
        }
        return null;
    }

    /**
     * @method genderCode (Get Kode Jenis Kelamin)
     * @param Integer $lookupId
     * @return String
     */
    public static function genderCode($lookupId)
    {
        if (empty($lookupId)) {
            $genderCode =  '-';
        }
        if ($lookupId == DocoConstants::LOOKUP_LAKI) {
            $genderCode = 'L';
        } elseif ($lookupId == DocoConstants::LOOKUP_PEREMPUAN) {
            $genderCode = 'P';
        } else {
            $genderCode = 'U';
        }
        return $genderCode;
    }

    public static function updateSessionDataPasien($pendId, $data = [])
    {
        $instalasiId = Yii::$app->docoVars->workspace('instalasi_id');
        if ($instalasiId == DocoConstants::INSTALASI_ID_RD) {
            $cache = Yii::$app->cache;
            $cacheData = $cache->get('data-pasien-igd-' . $pendId);
            if (!empty($data)) {
                foreach ($data as $k => $v) {
                    $cacheData[$k] = $v;
                }
                $cache->set('data-pasien-igd-' . $pendId, $cacheData, 3600);
            }
        } else {
            $cache = Yii::$app->cache;
            $cacheData = $cache->get('pasien-pendaftaran-id-' . $pendId);
            if (!empty($data)) {
                foreach ($data as $k => $v) {
                    $cacheData[$k] = $v;
                }
                $cache->set('pasien-pendaftaran-id-' . $pendId, $cacheData, 3600);
            }
        }

        return true;
    }

    public function listJam($awal, $akhir)
    {
        $jam = [];

        for ($i = $awal; $i < $akhir; $i++) {
            if ($i < 10){
                $jam['0'.$i.'.00'] = '0'.$i.'.00';
                $jam['0'.$i.'.30'] = '0'.$i.'.30';
            } else {
                $jam[$i.'.00'] =  $i.'.00';
                $jam[$i.'.30'] = $i.'.30';
            }

        }

        return $jam;
    }

    public static function getRequestId()
    {
        static $key = null;
        if ($key === null) {
            $request = Yii::$app->request;
            // called via FE (need header X-Request-Id)
            if (!$request->isConsoleRequest && $request->headers->has('x-request-id')) {
                $key = $request->headers->get('x-request-id');
            } else {
                $key = substr(uniqid() . static::generateRandomString(30), 0, 30);
            }
        }
        return strtoupper($key);
    }

    /**
     * @author iqbal Qurahman (iqbal.rukmana@sirs.co.id)
     *
     * @method get username and password ftp get file contents
     * @param preview files
     */
    public function previewPdfFromFTP($options, $path)
    {
        $username = $options['username'];
        $password = urlencode($options['password']);
        $ip_server = $options['ip_server'];
        $filename = "ftp://$username:$password@$ip_server/$path";
        $contents = file_get_contents($filename);
        $nameFileNew = 'hasilPemeriksaanFTP';

        // Header content type
        // header("Content-type: application/pdf");
        // header("Content-Length: " . filesize($filename));
        // readfile($filename); // Send the file to the browser.

        // header('Content-Description: File Transfer');
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"" . $nameFileNew . ".pdf\"");
        // header("Content-Transfer-Encoding: binary");
        header('Connection: Keep-Alive');
        // header("Expires: 0");
        // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        // header("Pragma: public");
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        // flush();
        readfile($filename);
        exit;

        /*end*/
    }

    public static function convertNamaHari($day)
    {
        switch($day){
            case 'Sun':
                $hari = "Minggu";
            break;

            case 'Mon':
                $hari = "Senin";
            break;

            case 'Tue':
                $hari = "Selasa";
            break;

            case 'Wed':
                $hari = "Rabu";
            break;

            case 'Thu':
                $hari = "Kamis";
            break;

            case 'Fri':
                $hari = "Jumat";
            break;

            case 'Sat':
                $hari = "Sabtu";
            break;

            default:
                $hari = "Tidak di ketahui";
            break;
        }
        return $hari;
    }


    //Decrypt ID dari array yang berbentuk string, contoh: id=asdq213wad,2eiqwjaids,2easdasdq 
    // diexplode dulu jadi ['asdq213wad','2eiqwjaids','2easdasdq'] 
    // lalu didecrypt dan dibalikin ke string semula jadi : id=1231,12312,123123 
    public static function setDecryptIdFromString($string) 
    { 
        $ids = explode(",", $string); 
        if(is_array($ids) && count($ids) > 0){ 
            $ids = array_map(function($val){ 
                    return DocoHelpers::decrypt($val); 
                }, $ids); 
        } 
 
        return implode(",", $ids); 
    } 

    public static function checkButtonAccess($path, $action) {
        $akses = Yii::$app->session->get('akses_menu');
        $hasAccess = false;
        if(isset($akses[$path]) && in_array($action, $akses[$path])){
            $hasAccess = true;
        }

        return $hasAccess;
    }
    
    public static function switchStatusV2($status, $id, $classname = 'change-status', $disabled = false, $paramOn = 'Aktif', $paramOff = 'Tidak&nbsp;aktif', $additionalAttribute = [])
    {
        $currentAttribute = [
            'class' => 'switch ' . $classname,
            'label' => false,
            'checked' => $status == 1,
            'data-id' => $id,
            'data-on-color' => 'success',
            'data-off-color' => 'danger',
            'data-size' => 'mini',
            'data-on-text' => \Yii::t('fe', $paramOn),
            'data-off-text' => \Yii::t('fe', $paramOff),
            'disabled' => $disabled
        ];
        $attributes = array_merge($currentAttribute, $additionalAttribute);
        $str = Html::checkbox('noname', "$status", $attributes);
        $str .= "<script>\$(\".switch\").bootstrapSwitch();</script>";
        return $str;
    }

    /* pooling request, experimental feel free to use or improve */
    public static function makeGetRequests($requests = [])
    {
        if (!empty($requests)) {
            return function () use ($requests) {
                foreach($requests as $keyword => $request) {
                    yield $keyword => new Request('GET', ArrayHelper::getValue($request, 'url') . (isset($request['params']) ? '?' . http_build_query($request['params']) : ''));
                }
            };
        }
    }


    public static function makeRequests($requests = [])
    {
        $reqs = [];
        foreach ($requests as $keyword => $request) {
            $params = ArrayHelper::getValue($request, 'params', '');
            $query_params = !empty($params) ? http_build_query($params) : '';
            $reqs[$keyword] = [
                'uri' => ArrayHelper::getValue($request, 'url', '') . (!empty($query_params) ? '?' : ''),
                'method' => ArrayHelper::getValue($request, 'method', 'GET'),
                'params' => $query_params,
            ];
        }
        return function () use ($reqs) {
            foreach($reqs as $keyword => $req) {
                yield $keyword => new Request('GET', $req['uri'] . $req['params']);
            }
        };
    }

    public static function poolRequest(Client $client, $requests, $concurrency = 10)
    {
        $result = [];
        $pool = new Pool($client, $requests(), [
            'concurrency' => $concurrency,
            'fulfilled' => function($response, $idx) use (&$result){
                $res = json_decode($response->getBody(), TRUE);
                $result[$idx] = $res['response'];
            },
            'rejected' => function($e, $idx) {
                $result[$idx] = [];
                $res = json_decode($e->getResponse()->getBody(), true);
                Yii::error($res);
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
        return $result;
    }

    public static function purifyText ($text)
    {
        $ini = @parse_ini_file('../config/env/.env', true);
        $key = isset($ini['regex_emoticon']['expression']) ? $ini['regex_emoticon']['expression'] : '';
        if (!empty($key)) {
            $emojiRegex = $key;
            $text = preg_replace($emojiRegex, '', $text);
        }
        return $text;
    }

    public static function uploadFileFtp($konfigFtp, $path, $file, $fileName)
    {
        $host = ArrayHelper::getValue($konfigFtp, 'host');
        $user = ArrayHelper::getValue($konfigFtp, 'user');
        $password = ArrayHelper::getValue($konfigFtp, 'password');
        $remotePath = ArrayHelper::getValue($konfigFtp, 'remotePath');

        if (!$host || !$user || !$password || !$remotePath) {
            return false;
        }
    
        $ftpConn = ftp_connect($host);
        if (!$ftpConn || !ftp_login($ftpConn, $user, $password)) {
            return false;
        }
    
        ftp_pasv($ftpConn, true);
        $remoteDir = $remotePath . $path;
    
        foreach (explode('/', $remoteDir) as $dir) {
            if (!empty($dir) && !@ftp_chdir($ftpConn, $dir)) {
                ftp_mkdir($ftpConn, $dir);
                ftp_chdir($ftpConn, $dir);
            }
        }
    
        $uploadStatus = ftp_put($ftpConn, "$remoteDir/$fileName", $file->tempName, FTP_BINARY);
        ftp_close($ftpConn);
    
        return $uploadStatus;
    }

    public static function deleteFileFtp($konfigFtp, $path, $file)
    {
        $host = ArrayHelper::getValue($konfigFtp, 'host');
        $user = ArrayHelper::getValue($konfigFtp, 'user');
        $password = ArrayHelper::getValue($konfigFtp, 'password');
        $remotePath = ArrayHelper::getValue($konfigFtp, 'remotePath');

        if (!$host || !$user || !$password || !$remotePath || !$file) {
            return false;
        }
    
        $ftpConn = ftp_connect($host);
        if (!$ftpConn || !ftp_login($ftpConn, $user, $password)) {
            return false;
        }
    
        ftp_pasv($ftpConn, true);
        $remoteFile = "$remotePath$path$file";
        $fileList = ftp_nlist($ftpConn, $remotePath . $path);
        $deleteStatus = false;
        if ($fileList && in_array($remoteFile, $fileList)) {
            $deleteStatus = ftp_delete($ftpConn, $remoteFile);
        }
        ftp_close($ftpConn);

        return $deleteStatus;
    }

    public static function previewImgFtp($konfigFtp, $path)
    {
        try{
            $host = ArrayHelper::getValue($konfigFtp, 'host');
            $user = ArrayHelper::getValue($konfigFtp, 'user');
            $password = ArrayHelper::getValue($konfigFtp, 'password');
            $remotePath = ArrayHelper::getValue($konfigFtp, 'remotePath');

            if ($host || $user || $password || $remotePath) {
                $ftpConn = ftp_connect($host);
                if ($ftpConn) {
                    $login = ftp_login($ftpConn, $user, $password);
                    if (!$login) {
                        ftp_close($ftpConn);
                    } else {
                        ftp_pasv($ftpConn, true);
                        $fullPath = $remotePath . '/' . $path;
                        $tempFile = tempnam(sys_get_temp_dir(), 'ftp_img_');
                        if (!ftp_get($ftpConn, $tempFile, $fullPath, FTP_BINARY)) {
                            ftp_close($ftpConn);
                            unlink($tempFile);
                        } else {
                            $contentImg = base64_encode(file_get_contents($tempFile));
                            $src = 'data: ' . mime_content_type($tempFile) . ';base64,' . $contentImg;
                            unlink($tempFile);
                            ftp_close($ftpConn);
                            return "<img src='" . $src . "' class='img-responsive'>";
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function previewPdfFtp($konfigFtp, $path)
    {
        try{
            $host = ArrayHelper::getValue($konfigFtp, 'host');
            $user = ArrayHelper::getValue($konfigFtp, 'user');
            $password = ArrayHelper::getValue($konfigFtp, 'password');
            $remotePath = ArrayHelper::getValue($konfigFtp, 'remotePath');

            if ($host || $user || $password || $remotePath) {
                $ftpConn = ftp_connect($host);
                if ($ftpConn) {
                    $login = ftp_login($ftpConn, $user, $password);
                    if (!$login) {
                        ftp_close($ftpConn);
                    } else {
                        ftp_pasv($ftpConn, true);
                        $fullPath = $remotePath . '/' . $path;
                        $tempFile = tempnam(sys_get_temp_dir(), 'ftp_pdf_');
                        if (!ftp_get($ftpConn, $tempFile, $fullPath, FTP_BINARY)) {
                            ftp_close($ftpConn);
                            unlink($tempFile);
                        } else {
                            header("Content-Type: application/pdf");
                            header("Content-Disposition: inline; filename=\"preview.pdf\"");
                            header('Content-Length: ' . filesize($tempFile));
                            readfile($tempFile);
                            unlink($tempFile);
                            ftp_close($ftpConn);
                            exit;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function downloadFileFtp($konfigFtp, $filePath, $namefile, $ext)
    {
        try{
            $host = ArrayHelper::getValue($konfigFtp, 'host');
            $user = ArrayHelper::getValue($konfigFtp, 'user');
            $password = ArrayHelper::getValue($konfigFtp, 'password');
            $remotePath = ArrayHelper::getValue($konfigFtp, 'remotePath');

            if ($host || $user || $password || $remotePath) {
                $ftpConn = ftp_connect($host);
                if ($ftpConn) {
                    $login = ftp_login($ftpConn, $user, $password);
                    if (!$login) {
                        ftp_close($ftpConn);
                    } else {
                        ftp_pasv($ftpConn, true);
                        $fileName = $namefile;
                        $remoteFile = $remotePath . $filePath;
                        $localTempFile = sys_get_temp_dir() . '/' . $fileName;
                        
                        if (!ftp_get($ftpConn, $localTempFile, $remoteFile, FTP_BINARY)) {
                            ftp_close($ftpConn);
                        } else {
                            ftp_close($ftpConn);
                            header("Pragma: public");
                            header("Expires: 0");
                            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                            header("Cache-Control: private", false);
                            header("Content-Description: File Transfer");
                            header("Content-Disposition: attachment; filename=\"$fileName\"");
                            if ($ext == 'xlsx') {
                                header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                            }
                            header("Content-Transfer-Encoding: binary");
                            header("Content-Length: " . filesize($localTempFile));
                            readfile($localTempFile);
                            unlink($localTempFile);
                            exit;
                            return Yii::$app->response->sendFile($localTempFile, $fileName);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
