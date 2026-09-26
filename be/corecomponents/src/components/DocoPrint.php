<?php

/**
* @author yaya
* kebutuhan untuk  Dokumen tercetak
*/
namespace Doco\components;


use Yii;
use Doco\models\DocMapping;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Doco\Services\Cache;
use yii\helpers\ArrayHelper;

class DocoPrint extends Mpdf
{

    public $attributes = [];
    protected $model;
    public $docName;
    protected $_format;
    protected $_margin_left;
    protected $_margin_right;
    protected $_margin_top;
    protected $_margin_bottom;
    protected $_orientation;

    public function __construct($defaultKodeDoc = null, $pageOrientation = null)
    {
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
                    'docfooter_k.konten',
                    'docfooter_k.gambar_kiri',
                    'docfooter_k.gambar_kanan'
                ]);
            },
            'header' => function ($query) {
                $query->select([
                    'docheader_k.docheader_id',
                    'docheader_k.flag_berulang',
                    'docheader_k.template_header',
                    'docheader_k.logo_kiri',
                    'docheader_k.logo_kanan'
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
            if ($panjang > $lebar && is_null($pageOrientation)) {
                $orientation = 'L';
                $format = [
                    $panjang,
                    $lebar,
                ];
            } else if(!is_null($pageOrientation)) {
                switch (strtolower($pageOrientation)) {
                    case 'p':
                        $orientation = 'P';
                        $format = [
                            $panjang,
                            $lebar,
                        ];
                        break;
                    case 'l':
                        $orientation = 'L';
                        $format = [
                            $lebar,
                            $panjang,
                        ];
                        break;
                    default:
                        $orientation = 'P';
                        break;
                }
            }
        }
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $this->_format = $format;
        $this->_margin_left = $margin_left;
        $this->_margin_right = $margin_right;
        $this->_margin_top = $margin_top;
        $this->_margin_bottom = $margin_bottom;
        $this->_orientation = $orientation;

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
            'tempDir' => dirname(__DIR__) . '/temp/',
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
        $html = $string_value = '';
        $array_key = $array_value = [];
        $additional_style = json_decode($this->model->additional_style, true);
        
        foreach ($this->attributes as $key => $value) {
            $array_key[] = $key;
            if (is_array($value)) {
                $string_value = implode(', ', $value);
            } else {
                $string_value = $value;
            }

            if(isset($additional_style[$key])) {
                foreach($additional_style[$key] as $selector => $attr) {
                    $patterns[] = '/'.$selector.'( *)?{( *).*?}/s';
                }
                $string_value = preg_replace($patterns, $additional_style[$key], $string_value);
            }

            $array_value[] = $string_value;
        }

        $array_key[] = '@tgl_print@';
        $array_key[] = '@nama_rs@';
        $array_value[] = date('d-m-Y H:i');
        $array_value[] = ArrayHelper::getValue(Cache::getProfileRs(true), 'nama_rumahsakit');

        $html = str_replace($array_key, $array_value, $this->model->docbody_text);
        $this->model->footer->template_footer = str_replace($array_key, $array_value, $this->model->footer->template_footer);

        if ($break) {
            $html .= "<pagebreak />";
        }

        // Set footer
        $footerTemplate = '';
        if (!empty($this->model->footer->template_footer)) {
            $footerTemplate = $this->model->footer->template_footer;
            $footerTemplate = str_replace('#username#', \Yii::$app->jwt->user->nama_pemakai, $footerTemplate);
            
            if(isset($this->model->footer->gambar_kiri)) {
                $footerTemplate = str_replace('#gambar_logo_kiri#', $this->model->footer->gambar_kiri , $footerTemplate);
            }

            if(isset($this->model->footer->gambar_kanan)) {
                $footerTemplate = str_replace('#gambar_logo_kanan#', $this->model->footer->gambar_kanan , $footerTemplate);
            }

            if (!empty($this->model->footer->flag_berulang)) {
                $this->SetHTMLFooter($footerTemplate);
            } else {
                $this->SetHTMLFooter('');
            }
        }

        // Set Header
        if (empty($this->model->header->flag_berulang)) {
            // Header tidak berulang
            if (!empty($this->model->header->template_header)) {
                $first_page_templateHeader = str_replace($array_key, $array_value, $this->model->header->template_header);
                if(isset($this->model->header->logo_kiri)) {
                    $first_page_templateHeader = str_replace('#url_logo_kiri#', $this->model->header->logo_kiri , $first_page_templateHeader);
                }
    
                if(isset($this->model->header->logo_kanan)) {
                    $first_page_templateHeader = str_replace('#url_logo_kanan#', $this->model->header->logo_kanan , $first_page_templateHeader);
                }
                $this->SetHTMLHeader($first_page_templateHeader, $side, $write);
                $this->WriteHTML('');
                $this->SetHTMLHeader('', $side, $write);
            }
        } else {
            $new_templateHeader = str_replace($array_key, $array_value, $this->model->header->template_header);
            if(isset($this->model->header->logo_kiri)) {
                $new_templateHeader = str_replace('#url_logo_kiri#', $this->model->header->logo_kiri , $new_templateHeader);
            }

            if(isset($this->model->header->logo_kanan)) {
                $new_templateHeader = str_replace('#url_logo_kanan#', $this->model->header->logo_kanan , $new_templateHeader);
            }
            // Header Berulang 
            $this->SetHTMLHeader($new_templateHeader, $side, $write);
        }

        $this->WriteHTML($html);

        if (empty($this->model->footer->flag_berulang)) {
            $this->SetHTMLFooter($footerTemplate);
        }
    }

    public function Output($multiple = false,$name = '', $dest = '')
    { 
        if (!$multiple) {
            $this->generateHtml();
        }
        $nameFile = !empty($this->model->nama_doc) ? $this->model->nama_doc : 'dokumen-tanpa-nama';
        $docName = !empty($this->docName) ? $this->docName : $nameFile;
        header("file-name:{$docName}.pdf");
        parent::Output($name, $dest);
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
        $setting .= '<div style="text-align:center"></div>'; /* page number on footer only */
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

    public function toHtml($break = false, $setHeader = false, $setFooter = false ){ 
        $constract = [
            // 'tempDir' => dirname(__DIR__) . '/temp/',
            'mode' => 'utf-8',
            'format' => $this->_format,
            'margin_left' => $this->_margin_left,
            'margin_right' => $this->_margin_right,
            'margin_top' => $this->_margin_top,
            'margin_bottom' => $this->_margin_bottom,
            'orientation' =>  $this->_orientation,
        ];
        return [
            '_constract' => $constract,
            '_html' => $this->createHtml($break , $setHeader, $setFooter)
        ];
    }

    public function createHtml($break = false, $setHeader = false, $setFooter = false )
    {
        $template_header = null;
        $html = null;
        $template_footer = null;
        if ($setHeader) {
            $template_header = $this->_set_header;
            $flagLoop = $setHeader; 
        }else{
            $flagLoop = $this->model->header->flag_berulang;
            if (empty($this->model->header->flag_berulang)) {
                if (!empty($this->model->header->template_header))
                   $template_header = $this->setNoReapetHeader();
            } else {
               $template_header = $this->model->header->template_header;
            }
        }
        if ($setFooter) {
            $template_footer = $this->_setFooter;
        }else{
            if (!empty($this->model->footer->template_footer)) {
                $template_footer = $this->model->footer->template_footer;
            }
        }
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
            $html = str_replace($array_key, $array_value, $this->model->docbody_text);
            if ($break) {
                $html .= "<pagebreak />";
            }
        }
        return [
            '_flag_berulang' => $flagLoop, 
            '_template_header' => $template_header, 
            '_header' => $template_header, 
            '_body' => $html, 
            '_footer' => $template_footer, 
        ];
    }
    
    public function getAdditionalData() {
        return $this->model->additional_data;
    }

    // jika ada kebutuhan cetakan beda RS beda layout, bisa set view yang perlu di render di kolom additional_data di docmapping_k
    // fungsi ini digunakan untuk get nama file view HTML yang di set di additional_data
    public function getRenderView($defaultView = null) {
        $additionalData = json_decode($this->model->additional_data, true);
        return ArrayHelper::getValue($additionalData, 'renderView', $defaultView);
    }
}