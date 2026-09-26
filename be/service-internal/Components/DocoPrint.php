<?php

/**
* @author yaya
* kebutuhan untuk  Dokumen tercetak
*/
namespace Integrasi\Components;


use Yii;
use Integrasi\Components\models\DocMapping;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class DocoPrint extends Mpdf
{

    public $attributes = [];
    protected $model;

    public function __construct($defaultKodeDoc = null)
    {
        $_SERVER['SCRIPT_NAME'] = dirname(__DIR__);
        if (!is_null($defaultKodeDoc)) {
            $whereClause = [
                'kode_doc' => $defaultKodeDoc
            ];
        } else {
            $nameService = strtolower(Yii::$app->params['service']);
            $actionMethod = Yii::$app->controller->action->actionMethod;
            $class = substr(strrchr(get_class(Yii::$app->controller), "\\"), 1);
            $whereClause = [
                'doc_key' => $nameService . '-' . $class .'-'.$actionMethod
            ];
        }
        $this->model = DocMapping::find()->with([
            'footer' => function ($query) {
                $query->select([
                    'docfooter_k.docfooter_id',
                    'docfooter_k.template_footer',
                    'docfooter_k.flag_berulang',
                    'docfooter_k.konten'
                ]);
            },
            'header' => function ($query) {
                $query->select([
                    'docheader_k.docheader_id',
                    'docheader_k.flag_berulang',
                    'docheader_k.template_header'
                ]);
            },
            'kertas' => function ($query) {
                $query->select([
                    'kertas_k.kertas_id',
                    'kertas_k.panjang',
                    'kertas_k.lebar',
                    'kertas_k.batas_kiri',
                    'kertas_k.batas_kanan',
                    'kertas_k.batas_atas',
                    'kertas_k.batas_bawah',
                ]);
            }
        ])->where($whereClause)->one();

        $format = 'A4';
        $orientation = 'P';
        $margin_left = 15;
        $margin_right = 15;
        $margin_top = 16;
        $margin_bottom = 16;

        if (!empty($this->model->kertas->panjang) && !empty($this->model->kertas->lebar)) {
            $panjang = $this->model->kertas->panjang * 10;
            $lebar = $this->model->kertas->lebar * 10;
            $margin_left = $this->model->kertas->batas_kiri * 10;
            $margin_right = $this->model->kertas->batas_kanan * 10;
            $margin_top = $this->model->kertas->batas_atas * 10;
            $margin_bottom = $this->model->kertas->batas_bawah * 10;
            $format = [
                $lebar,
                $panjang,
            ];
            if ($panjang > $lebar) {
                $orientation = 'L';
                $format = [
                    $panjang,
                    $lebar,
                ];
            }
        }
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        parent::__construct([
            'fontDir' => array_merge($fontDirs, [
                    __DIR__ . '/../custom/font',
            ]),
            'fontdata' => $fontData + [
                'dotmatrix' => [
                    'R' => 'dotmatrix.ttf',
                    'I' => 'dotmatrix.ttf',
                ]
            ],
            'tempDir' => dirname(__DIR__) . '/Config/temp/',
            'mode' => 'utf-8',
            'format' => $format,
            'margin_left' => $margin_left,
            'margin_right' => $margin_right,
            'margin_top' => $margin_top,
            'margin_bottom' => $margin_bottom,
            'orientation' =>  $orientation
        ]);
    }

    public function generateHtml($break = false, $side = "O", $write = false)
    {
        $this->useOddEven = 1;
        $this->setAutoTopMargin = true;
        $this->setAutoBottomMargin = true;
        
        // Set Body
        $html = '';
        $array_key = $array_value = [];

        foreach ($this->attributes as $key => $value) {
            $array_key[] = $key;
            if (is_array($value)) {
                $array_value[] = implode(', ', $value);
            } else {
                $array_value[] = $value;
            }
        }
        $html = str_replace($array_key, $array_value, $this->model->docbody_text);
        $this->model->header->template_header = str_replace($array_key, $array_value, $this->model->header->template_header);
        $this->model->footer->template_footer = str_replace($array_key, $array_value, $this->model->footer->template_footer);

        if ($break) {
            $html .= "<pagebreak />";
        }

        // Set Header
        if (empty($this->model->header->flag_berulang)) {
            // Header tidak berulang
            if (!empty($this->model->header->template_header)) {
                $first_page_templateHeader = str_replace($array_key, $array_value, $this->model->header->template_header);
                $this->SetHTMLHeader($first_page_templateHeader, $side, $write);
                $this->WriteHTML('');
                $this->SetHTMLHeader('', $side, $write);
            }
        } else {
            $new_templateHeader = str_replace($array_key, $array_value, $this->model->header->template_header);
            // Header Berulang 
            $this->SetHTMLHeader($new_templateHeader, $side, $write);
        }

        // Set footer
        $footerTemplate = '';
        if (!empty($this->model->footer->template_footer)) {
            $footerTemplate = $this->model->footer->template_footer;
            // $footerTemplate = str_replace('#username#', \Yii::$app->jwt->user->nama_pemakai, $template);
            if (!empty($this->model->footer->flag_berulang)) {
                $this->SetHTMLFooter($footerTemplate);
            } else {
                $this->SetHTMLFooter('');
            }
        }

        $this->WriteHTML($html);

        if (empty($this->model->footer->flag_berulang)) {
            $this->SetHTMLFooter($footerTemplate);
        }
    }

    public function Output($multiple = false,$name = '', $dest = 'F')
    { 
        if (!$multiple) {
            $this->generateHtml();
        }
        $nameFile = !empty($name) ? $name : $this->model->nama_doc;
        // $nameFile = !empty($this->model->nama_doc) ? $this->model->nama_doc : $name;
        parent::Output($nameFile.'.pdf', $dest);
    }

    /**
    * @return void
    **/

    private function setNoReapetHeader()
    {
        $setting = '<sethtmlpageheader name="firstpage" value="on" show-this-page="1" />';
        $setting .= '<sethtmlpageheader name="otherpages" value="on" />';
        $setting .= '<htmlpageheader name="firstpage" style="display:none">';
        $setting .= $this->model->header->template_header;
        $setting .= '</htmlpageheader>';
        $setting .= '<htmlpageheader name="otherpages" style="display:none">';
        $setting .= '<div style="text-align:center">{PAGENO}</div>';
        $setting .= '</htmlpageheader>';
        $setting .= $this->model->docbody_text;
        $this->model->docbody_text = $setting;
    }

    /**
     *
     * Kebutuhan return html dari docMapping_k (dokumen tercetak)
     * @author metafiliana
     * @return string $footer default '', string $body default '', string footer default ''
     *
     */
    public function OutputHtml($name = '', $dest = '')
    { 
        $header = '';
        $body = '';
        $footer = '';

        // Set Header
        if (empty($this->model->header->flag_berulang)) {
            // Header tidak berulang
            if (!empty($this->model->header->template_header))
                $this->setNoReapetHeader();
        } else {
            // Header Berulang
            $header = $this->model->header->template_header;
        }
        
        // Set Body
        if (!empty($this->model->docbody_text)) {
            $array_key =  $array_value = [];
            foreach ($this->attributes as $key => $value) {
                $array_key[] = $key;
                if (is_array($value)) {
                    $array_value[] = implode(', ', $value);
                } else {
                    $array_value[] = $value;
                }
            }
            $body = str_replace($array_key, $array_value, $this->model->docbody_text);
        }

        // Set footer
        if (!empty($this->model->footer->template_footer)) {
            $footer = $this->model->footer->template_footer;
        }

        $return = [
            'header' => $header,
            'body' => $body,
            'footer' => $footer,
        ];

        return $return;
    }

}