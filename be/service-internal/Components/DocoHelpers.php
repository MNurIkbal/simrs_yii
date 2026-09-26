<?php

namespace Integrasi\Components;

use Yii;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \DateTime;
use Integrasi\Service\Mhg\Cache\Cache;
use yii\helpers\ArrayHelper;
use Integrasi\Components\PhpOffice;

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
        "Nopember",
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
        "Nop",
        "Des"
    ];

    /* From Brimob */
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

    
    public static $_hari_indo = [
        'Sun' => 'Minggu',
        'Mon' => 'Senin',
        'Tue' => 'Selasa',
        'Wed' => 'Rabu',
        'Thu' => 'Kamis',
        'Fri' => 'Jumat',
        'Sat' => 'Sabtu'
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
    * @param string $errorMessage
    *
    * @return json
    */

    public static function dataTabelsException($errorMessage = '')
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->statusCode = 200;
        return json_encode([
            'data' => [],
            'draw' => 1,
            'recordsFiltered' => 0,
            'recordsTotal' => 0,
            'errorMessage' => $errorMessage
        ]);
    }

    /**
    * @param boolean $status
    *
    * @return string
    */
    public function isActive($status = 0, $buttonType = false)
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
            return '<span class="label '. $classBtn .' position-right">'. $label .'</span>';
        else 
            return '<span class="'. $classTxt .'" style="font-weight:bold">'. $label .'</span>';
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

    /**
    * @param array $data
    * @param string $model
    *
    * @return array
    */

    public static function parseError(array $data,$model)
    {
        $result = [];
        if ($model) {
            foreach ($data as $key => $value) {
                $result[$model .'['. $key .']'] = $value;
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

    public function response($data, $statusCode = 200, $parse = false)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($parse && isset($data['metadata']['status'])) {
            \Yii::$app->response->statusCode = $data['metadata']['status'];
            $response = [
                'response' => isset($data['response']) ? $data['response'] : []
                ];
            if (isset($data['return'])) {
                $response['response']['return'] = $data['return'];
            }
            return $response;
        } else {
            \Yii::$app->response->statusCode = $statusCode;
            return $data;
        }
    }

    /**
    * @param array $data
    * @param integer $statusCode
    * 
    * @return json
    */

    public function responseTemplate($status, $message, $data = [])
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->statusCode = $status;
        return [
            "metadata" => [
                "status" => $status,
                "message" => $message
            ],
            "response" => [
                "data" => $data
            ]
        ];
    }

    /**
    * @param array $data
    * @param integer $statusCode
    * 
    * @return json
    */

    public function responseJsonString($str, $formName)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = json_decode($str, TRUE);
        $status = $result['metadata']['status'];
        if ($status == 422) {
            foreach($result['response'] as $row)
                $response['data'][$formName."[".$row["field"]."]"] = [$row["message"]];
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

    public static function __callstatic($name, $args) {

        if (!empty($this) && property_exists($this, $name)) {
            return call_user_func_array(
                array(get_called_class(), $name),
                $args
            );
        }

        throw new \BadMethodCallException("Static method " . __CLASS__ ."::$name() doesn't exist");
    }

    public static function exportExcel($title, $results, $excelHeader = array(), $options = array(), $footer = [], $footerInfo = [], $stram = false)
    {
        // Directory Creation
        $uploadPath = @$options['uploadPath'];
        $skipIncrement = @$options['skipIncrement'];
        $skipHeader = @$options['skipHeader'];
        $subHeader = @$options['subHeader'];
        $customHeader = @$options['customHeader'];
        $nameHeaderSkipped = @$options['nameHeaderSkipped'];
        //edited by Rizqi Fitrianto
        //add variables modules for handling app name in excel file
        //07-March-2018
        $modules = explode('\\', \Yii::getAlias('@app'));
        $modules = end($modules);
        $fileprefix = @$options['filePrefix'] ? @$options['filePrefix'] . "-" : ucfirst($modules) . "-";

        if (!$uploadPath)
            $uploadPath = "./uploads";
        if (!is_dir($uploadPath))
            mkdir($uploadPath, 0777);


        $fileName = str_replace(" ", "-", $fileprefix . (Yii::t("app", $title)));
        $match = preg_match('/([^\/\r\n]*)([\w]+)$/', $fileName, $realPath);
        $fileName = isset($realPath[0]) ? $realPath[0] : $title;
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
            foreach ((array)$firstRow as $key => $b) {
                $firstRow = $b;
                break;
            }
        }

        $countColumn = [];
        if ($firstRow != null) {
            $data = array();
            // header
            if (!$skipHeader) {
                if (!$skipIncrement) {
                    $data[] = "No";
                }

                foreach ($firstRow as $key => $val) {
                    // ignore field with suffix '_id' in list
                    if (substr($key, -3) == '_id') continue;

                    if ($key == 'is_active') {
                        $data[] = Yii::t("app", "Status");
                    } else {
                        $data[] = Yii::t("app", ucfirst(str_ireplace("_", " ", $key)));
                    }
                }
            }
            // row
            $countColumn = $data;
            $arrayData[] = $data;
            $no = 1;
            foreach ($results as $row) {
                $data = array();
                if (!$skipIncrement) {
                    $data[] = $no;
                }
                foreach ($row as $key => $val) {
                    // ignore field with suffix '_id' in list
                    if (substr($key, -3) == '_id') continue;

                    if ($key == 'is_active') {
                        if ($val == true) {
                            $data[] = Yii::t("app", "Aktif");
                        } else {
                            $data[] = Yii::t("app", "Tidak Aktif");
                        }
                    } else if ($key == 'is_online') { // penambahan untuk kebutuhan cara bayar brimob
                        if ($val == true) {
                            $data[] = Yii::t("app", "Ya");
                        } else {
                            $data[] = Yii::t("app", "Tidak");
                        }
                    } else {
                        $data[] = (string)$val;
                    }
                }
                $arrayData[] = $data;
                $no++;
            }
            $countColumn = $results ? $results[0] : [];
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
        $firstAlphabet = 'A';
        $lastAlphabet = '';
        for ($x = 0; $x < count($countColumn); $x++) {
            $firstAlphabet++;
        }
        $lastAlphabet = $firstAlphabet;

        $row = 1;
        $firstColumn = 'A';
        $lastColumn = $lastAlphabet;


        // Header Creation
        $sheet->mergeCells("{$firstColumn}{$row}:{$lastColumn}{$row}");
        $sheet->setCellValue("{$firstColumn}{$row}", Yii::t("app", $title));
        $row++;

        // kebutuhan subtitle
        if (isset($options['subTitle'])) {
            $sheet->mergeCells("{$firstColumn}{$row}:{$lastColumn}{$row}");
            $sheet->setCellValue("{$firstColumn}{$row}", Yii::t("app", $options['subTitle']));
            $row++;
        }

        foreach ($excelHeader as $key => $val) {
            $sheet->mergeCells("{$firstColumn}{$row}:{$lastColumn}{$row}");
            $sheet->setCellValue("{$firstColumn}{$row}", Yii::t("app", $key) . " : {$val}");
            $row++;
        }

        if (isset($options['subHeader'])) {
            $row++;
            if (is_array($options['subHeader'])) {
                $styleHeader = array(
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

                foreach ($options['subHeader'] as $key => $val) {
                    $sheet->mergeCells("{$firstColumn}{$row}:{$lastColumn}{$row}");
                    $sheet->setCellValue("{$firstColumn}{$row}", "{$val}");
                    $sheet->getStyle("{$firstColumn}{$row}:{$firstColumn}{$row}")->applyFromArray($styleHeader);
                    $row++;
                }
            } else {
                $sheet->mergeCells("{$firstColumn}{$row}:{$lastColumn}{$row}");
                $sheet->setCellValue("{$firstColumn}{$row}", Yii::t("app", $options['subHeader']));
            }
        }


        $row++; // Row = 6

        if (isset($options['customHeader'])) {
            $styleHeader = array(
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

            $startX = $endX = 'A';
            $startY = $row;
            $selisih = 0;
            $no = 0;
            $x = [];
            foreach ($options['customHeader'] as $headers) {
                $firstX = $lastX = 'A';
                $firstY = $lastY = $row;
                foreach ($headers as $headerRow) {
                    // startfrom
                    if (isset($headerRow['startfrom'])) {
                        for ($i = 1; $i < $headerRow['startfrom']; $i++) {
                            $firstX++;
                            $lastX++;
                        }
                    }
                    // colspan
                    if (isset($headerRow['colspan'])) {
                        for ($i = 1; $i < $headerRow['colspan']; $i++) {
                            $lastX++;
                        }
                    }
                    // rowspan
                    if (isset($headerRow['rowspan'])) {
                        for ($i = 1; $i < $headerRow['rowspan']; $i++) {
                            $lastY++;
                        }
                    }

                    $sheet->mergeCells("{$firstX}{$firstY}:{$lastX}{$lastY}");
                    $sheet->setCellValue("{$firstX}{$firstY}", $headerRow['label']);

                    $endX = $endX > $lastX ? $endX : $lastX;
                    $firstX = $lastX;
                    $firstX++;
                    $lastX++;
                    $lastY = $row;
                }
                // $endX = $lastX;
                $endY = $row;
                $row++;
            }
            $sheet->getStyle("{$startX}{$startY}:{$endX}{$endY}")->applyFromArray($styleHeader);
        }

        // Define array style
        $styleArray = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
        );
        // Body Creation
        $startTabel = "{$firstColumn}{$row}";
        $thStart = $row;
        if (count($footer) > 0 && isset($footer['data'])) {
            $newData = [];
            $x = 0;
            $footerColumn = $row + count($arrayData);
            $firstFooter = $firstColumn . $footerColumn;
            $y = 'A';
            for ($z = 0; $z < $footer['title'][1]; $z++) {
                $y++;
            }
            $lastFooter = $y . $footerColumn;
            $headerTitle = $arrayData[0];
            if ($skipHeader) {
                $headerTitle = empty($nameHeaderSkipped) ? [] : $nameHeaderSkipped;
            }
            foreach ($headerTitle as $key => $value) {
                if (count($footer['title']) > 0) {
                    if ($x == 0) {
                        $newData[] = $footer['title'][0];
                    } else if ($x <= $footer['title'][1]) {
                        $newData[] = '';
                    } else {
                        $newData[] = isset($footer['data'][$value]) ? $footer['data'][$value] : '';
                    }
                    $x++;
                }
            }
            // Merge cells
            $spreadsheet->setActiveSheetIndex(0)->mergeCells($firstFooter . ':' . $lastFooter);

            // Assign merged cells key
            $sheet->getStyle($firstFooter . ':' . $lastFooter)->applyFromArray($styleArray);
            $arrayData[count($arrayData)] = $newData;
        }

        // rizal
        $startTabel = "{$firstColumn}{$row}";
        $thStart = $row;
        if ($footerInfo) {
            for ($z = 0; $z < $footerInfo[0] - 1; $z++) {
                $firstColumn++;
            }
            $footerColumn = $row + count($arrayData) + 2;

            $newData = [];
            foreach ($footerInfo[1] as $key => $value) {
                $firstFooter = $firstColumn . $footerColumn;
                $sheet->setCellValue($firstFooter, $value);
                $footerColumn++;
            }
            $arrayData[count($arrayData)] = $newData;
        }

        $spreadsheet->getActiveSheet()->fromArray(
            $arrayData,
            null,
            $startTabel
        );

        // Define merged cells key
        $mergedKey = array();

        // Check merge cells
        if (isset($options['mergeCells']) && $options['mergeCells'] == true) {
            // Get what to merged
            if (isset($options['cellsToMerged']) && !empty($options['cellsToMerged'])) {
                // Loop
                foreach ($options['cellsToMerged'] as $value) {
                    // Check
                    if (isset($arrayData[0]) && in_array(Yii::t("app", ucfirst(str_ireplace("_", " ", $value))), $arrayData[0])) {
                        // Get key
                        $key = array_search(Yii::t("app", ucfirst(str_ireplace("_", " ", $value))), $arrayData[0]);

                        // Assign key
                        $firstKey = isset($alphabet[$key]) ? $alphabet[$key] . '4' : '';
                        $lastKey = isset($alphabet[$key]) ? $alphabet[$key] : '';
                        $lastKey = $lastKey . '' . strval(count($arrayData) + 2);

                        // Merge cells
                        $spreadsheet->setActiveSheetIndex(0)->mergeCells($firstKey . ':' . $lastKey);

                        // Assign merged cells key
                        $sheet->getStyle($firstKey . ':' . $lastKey)->applyFromArray($styleArray);
                    }
                }
            }

            if (isset($options['mergeHeader']) && is_array($options['mergeHeader'])) {
                $debug = [];
                $no = 0;
                $selisih = 0;
                foreach ($options['mergeHeader'] as $key => $value) {
                    $no = ($no ? $no : $thStart) + ($key - $selisih) + 1;
                    $sheet->mergeCells("{$firstColumn}{$no}:{$lastColumn}{$no}");
                    $sheet->setCellValue("{$firstColumn}{$no}", $value);
                    $selisih = $key;
                }
            }
        }

        $row += count($arrayData);
        // Sheet Header
        $fontSize = ArrayHelper::getValue($options, 'titleStyle.fontSize', 16);
        $alignment = ArrayHelper::getValue($options, 'titleStyle.alignment', 'center');

        switch ($alignment) {
            case 'left':
                $direction = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                break;

            case 'right':
                $direction = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
                break;

            default:
                $direction = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                break;
        }

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => $fontSize,
            ),
            'alignment' => array(
                'horizontal' => $direction,
            ),
        );

        $sheet->getStyle('A1')->applyFromArray($styleArray);

        // style sheet sub title
        if (isset($options['subTitle'])) {
            $sheet->getStyle('A2')->applyFromArray($styleArray);
        }

        // Table border
        $styleArray = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
            'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER
        );
        $sheet->getStyle("{$firstColumn}{$thStart}:{$lastColumn}" . ($row - 1))->getNumberFormat()->applyFromArray($styleArray);

        // format code row
        if (isset($options['customFormatCode'])) {
            foreach ($options['customFormatCode'] as $z) {
                $colDirection = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                $formatCode = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                if (isset($z['formatCode'])) {
                    switch ($z['formatCode']) {
                        case 'date':
                            $formatCode = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH;
                            break;

                        case 'datetime':
                            $formatCode = PhpOffice::FORMAT_SHORTDATE_TIMESTAMP;
                            break;

                        case 'shortdate':
                            $formatCode = PhpOffice::FORMAT_SHORTDATE;
                            break;

                        case 'number':
                            $formatCode = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                            $colDirection = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
                            break;

                        case 'general':
                            $formatCode = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL;
                            break;

                        /* non comma sparated number */
                        case 'numberncs':
                            $formatCode = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER;
                            break;

                        case 'datetime':
                            $formatCode = \Integrasi\Components\Custom\PhpOfficeNumberFormat::FORMAT_DATE_DATETIME2;
                            break;

                        case 'number2':
                            $formatCode = \Integrasi\Components\Custom\PhpOfficeNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3;
                            break;

                        case 'textwrap':
                            $lastRow = $sheet->getHighestRow();
                            $column = $z['selectColumn'];
                            $sheet->getStyle($column . "1:" . $column . $lastRow)
                                ->getAlignment()->setWrapText(true);

                        case 'number3':
                            $formatCode = \Integrasi\Components\Custom\PhpOfficeNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3_NEW;
                            break;

                        default:
                            $formatCode = $z['formatCode'];
                            break;
                    }
                }

                if (isset($z['startNumCoordinate']) && isset($z['strCoordinate'])) {
                    $startColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($z['strCoordinate']);
                    $startNumberCoordinate = $z['startNumCoordinate'];

                    $start = $startColumnLetter . $startNumberCoordinate;
                }

                if (isset($z['endNumCoordinate']) && isset($z['strCoordinate'])) {
                    $endColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($z['strCoordinate']);
                    $endNumberCoordinate = $z['endNumCoordinate'];

                    $end = $endColumnLetter . $endNumberCoordinate;
                }

                // startRow
                if (isset($z['startRow'])) {
                    $start = $z['startRow'];
                }

                // endRow
                if (isset($z['endRow'])) {
                    $end = $z['endRow'];
                }

                if (isset($start) && isset($end)) {
                    $sheet->getStyle("{$start}:{$end}")
                        ->getNumberFormat()
                        ->setFormatCode($formatCode);
                }

                // apply formating for whole column
                if (isset($z['selectColumn'])) {
                    $lastRow = $sheet->getHighestRow();
                    $column = $z['selectColumn'];

                    $sheet->getStyle($column . "1:" . $column . $lastRow)
                        ->getNumberFormat()
                        ->setFormatCode($formatCode);
                    
                    $colStyleArray = array(
                        'alignment' => array(
                            'horizontal' => $colDirection,
                        ),
                    );

                    $newStyleAlignment['alignment'] = ArrayHelper::getValue($z, 'alignment', $colStyleArray);
                    unset($z['selectColumn']);
                    $z = array_merge($z, $newStyleAlignment);

                    $sheet->getStyle($column . "1:" . $column . $lastRow)->applyFromArray($z);
                }
            }
        }

        //totalCount
        if (isset($options['totalCount'])) {
            $titleTotalRow = $options['totalCount']['title'];
            $valueTotalRow = $options['totalCount']['value'];
            $columnTotalRow = $options['totalCount']['columnValue'];
            $columnLabelTotalRow = $options['totalCount']['columnLabel'];
            $totalRow = $row + 2;

            $startTotalRow = "{$columnTotalRow}{$totalRow}";
            $startLabelTotalRow = "{$columnLabelTotalRow}{$totalRow}";

            $styleArrayTotalCount = [
                'font' => [
                    'bold' => true,
                ],
            ];
            $spreadsheet->getActiveSheet()->setCellValue($startLabelTotalRow, $titleTotalRow);
            $spreadsheet->getActiveSheet()->getStyle($startLabelTotalRow)->applyFromArray($styleArrayTotalCount);

            $spreadsheet->getActiveSheet()->setCellValue($startTotalRow, $valueTotalRow);
            $spreadsheet->getActiveSheet()->getStyle($startTotalRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
        }

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
        if (!$skipHeader) {
            $sheet->setAutoFilter("{$firstColumn}{$thStart}:{$lastColumn}{$thStart}");
        }

        // Auto size columns for each worksheet
        foreach ($alphabet as $char)
            $sheet->getColumnDimension($char)->setAutoSize(true);

        // File Writing
        $writer = new Xlsx($spreadsheet);
        if ($stram) return $writer;
        $writer->save($filePath);
        return $filePath;
        // Write file to the browser
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
        if (!$date){
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
            'hari' => $day,
            'bulan' => $month,
            'urutan_hari' => $id_hari,
            'tahun' => date('Y', $timestamp),
        ];

        return $return;
    }
    
    /**
     * @todo format number
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    static public function formatNumber($var, $null = true, $fractional = false)
    {
        $var = round($var, 2);
        $var = str_replace('.', ',', $var);
        if ($null === true && $var == 0)
            // return "N/A";
        // return "0";
        return $var;

        if ($null === false && ($var == 0 || $var == "")) {
            // return "0";
            return $var;
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

    /**
     * @todo Convert total hari ke tahun,bulan,hari helper 
     * @author Budi Sikasep
     */
    public static function convertToDay($totalHari)
    {
        $tahunMaksimal = ($totalHari % 365);
        $tahun = floor ($totalHari / 365);
        $bulan = floor ((($totalHari - ($tahun*365))/30));
        $hari = $tahunMaksimal % 30;

        return [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'hari' => $hari,
        ];
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

    public function getAverage($arr) {
        $average = array_sum($arr) / count($arr);

        return round($average);
    }

    /**
     * @todo Helper untuk konversi second ke hari jam menit dan detik
     * @param int detik
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function secondsToTime($seconds) {
        $start = new DateTime();
        $start->setTimestamp(0);
        $end = new DateTime();
        $end->setTimestamp($seconds);

        return $start->diff($end)->format('%a hari %h jam %i menit %s detik');
    }

    /**
     * @todo Helper untuk konversi hari jam menit dan detik ke seconds
     * @param string hari jam menit dan detik 
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function timeToSeconds($time)
    {
        $replace = preg_match_all('/(\d+)/', $time, $getTime);
        $day = isset($getTime[0][0]) ? (($getTime[0][0] != 0) ? $getTime[0][0] * 86400 : 0) : 0;
        $hour = isset($getTime[0][0]) ? $getTime[0][1] * 3600 : 0;
        $minute = isset($getTime[0][1]) ? $getTime[0][2] * 60 : 0;
        $second = isset($getTime[0][2]) ? $getTime[0][3] : 1;
        $total_seconds = $day + $hour + $minute + $second;

        return $total_seconds;
    }

    public function convertAntrian($value='PBU1048') {
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
        $hurufConverted[] = DocoConstants::OPENING_ANTRIAN;
        foreach ($res as $key => $each) {
            if (is_numeric($each)) {
                //edited ketika return blank, supaya tidak merubah function terbilang : ali.padilah@docotel.com
                $hurufConverted[] = trim(self::terbilang($each,true) == " " ? "Kosong" : self::terbilang($each,true));
            } else {
                $hurufConverted[] = $each;
            }
        }

        return implode(' ', $hurufConverted);
    }
     
    public static function Terbilang($e, $is_antrian = false) 
    {

        $abil = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $ratus = [200,300,400,500,600,700,800,900];

        if ($e < 12)
            return " " . $abil[$e];
        elseif ($e < 20)
            return self::Terbilang($e - 10) . " Belas";
        elseif ($e < 100)
            return self::Terbilang($e / 10) . " Puluh" . self::Terbilang($e % 10);
        elseif ($e < 200)
            return " Seratus" . (($e - 100) > 0 ? self::Terbilang($e - 100) : '') . (($is_antrian && $e != 100) ? " " : "") ;
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
    
    public function encryptInacbg($data, $key)
    {
        $key = hex2bin($key);
        if(mb_strlen($key, "8bit") !== 32){
            throw new \Exception("Need 256bit key");
        }

        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $iv = openssl_random_pseudo_bytes($iv_size);
        // $iv = random_bytes($iv_size);

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        $signature = mb_substr(hash_hmac("sha256", $encrypted, $key, true), 0 ,10, "8bit");

        $encoded = chunk_split(base64_encode($signature.$iv.$encrypted));
        return $encoded;
    }

    public function decryptInacbg($str, $strkey, $cons = false)
    {
        if($cons){
            $first = strpos($str, "\n")+1;
            $last = strpos($str, "\n")-1;
            $str = substr($str, $first, strlen($str) - $first - $last);
        }
        $key = hex2bin($strkey);
        if(mb_strlen($key, "8bit") !== 32){
            throw new \Exception("Need 256bit key");
        }
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");

        $decoded = base64_decode($str);
        $signature = mb_substr($decoded, 0, 10, "8bit");
        $iv = mb_substr($decoded, 10, $iv_size, "8bit");
        $encrypted = mb_substr($decoded, $iv_size+10, NULL, "8bit");

        $calc_signature = mb_substr( hash_hmac("sha256", $encrypted, $key, true), 0, 10, "8bit");
        if(!DocoHelpers::inacbgCompare($signature, $calc_signature))
        {
            return "Signature not match";
        }
        $decrypted = openssl_decrypt($encrypted, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

    public function inacbgCompare($a, $b)
    {
        if(strlen($a) !== strlen($b)) return false;

        $result = 0;
        for($i = 0; $i < strlen($a); $i++){
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

    /**
    * @todo Fungsi untuk enkripsi AES 128
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    function aes128Encrypt($key, $data) {
        if(16 !== strlen($key)) $key = hash('MD5', $key, true);
        $padding = 16 - (strlen($data) % 16);
        $data .= str_repeat(chr($padding), $padding);
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, str_repeat("\0", 16)));
    }

    /**
    * @todo Fungsi untuk deskripsi AES 128
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    function aes128Decrypt($key, $data) {
        $data = base64_decode($data);
        if(16 !== strlen($key)) $key = hash('MD5', $key, true);
        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, str_repeat("\0", 16));
        $padding = ord($data[strlen($data) - 1]); 
        return substr($data, 0, -$padding); 
    }

    /**
    * @todo Fungsi untuk convert tanggal lahir ke umur
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public static function convertDateToAge($bornday, $type = 'tahun', $boundary = null)
    {
        $bornday = new DateTime($bornday);
        $boundary = !empty($boundary) ? $boundary : date('Y-m-d', strtotime('NOW'));
        $now = new DateTime($boundary);

        $diff = $now->diff($bornday, true);

        if ($type == 'tahun') {
            return $diff->format('%y');
        } else if ($type == 'bulan') {
            return $diff->m + (12 * $diff->y);
        } else if ($type == 'hari') {
            return $diff->format('%a');
        } else {
            // return $diff->format('%y')." tahun ".$diff->format('%m')." bulan ".$diff->format('%a')." hari";

            // modify convert hari : ali
            return $diff->format('%y')." tahun ".$diff->format('%m')." bulan ".$diff->format('%d')." hari";
        }
    }


     /**
    * @todo Fungsi curl
    * @author ali.padilah@docotel.com
    **/
    public static function curl($url = null, $data = [], $header = [] , $action = 'GET')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);


        if(curl_exec($ch) === false)
        {
            $content = [
                'status' => false,
                'error' => 'Curl error: ' . curl_error($ch)
            ];

            return $content;
        } else {
            $content = [
                'status' => true
            ];
        }

        curl_close($ch);

        return $content;
    }

    public function pembulatan($sum, $isNaik = false, $satuan = 500)
    {
        $tmp = $satuan ? $sum % $satuan : 0;
        $add = 0;
        $result = 0;
        try {
            if ($tmp > 0) {
                if ($isNaik == true) {
                    $add = $satuan - $tmp;
                    $result = (int)$sum + (int)$add;

                } else {
                    $add = $tmp;
                    $result = $sum - $tmp;
                }
            } else {
                $result = $sum;
            }

            return [
                'total' => $result,
                'pembulatan' => $add
            ];

        } catch (Exception $e) {
            return $result;
        }
    }

    public static function getUmur($param, $umur_only = false, $parse = false)
    {
        if (!$parse){
            $diff = date_diff(date_create(date('Y-m-d', strtotime($param))), date_create(date('Y-m-d')));
            if (!$umur_only){
                return $diff->y." tahun ".$diff->m." bulan ".$diff->d." hari";
            }else{
                return $diff->y;
            }
        }else{
            $arr_umur_temp = explode(' ', $param);

            $arr_umur = [];
            $arr_umur['tahun'] = $arr_umur_temp[0];
            $arr_umur['bulan'] = $arr_umur_temp[2];
            $arr_umur['hari'] = $arr_umur_temp[4];

            return $arr_umur;
        }
    }

    protected function convertTerbilang($num)
    {
        $output = "";
        $num = str_pad($num, 36, "0", STR_PAD_LEFT);
        $group = rtrim(chunk_split($num, 3, " "), " ");
        $groups = explode(" ", $group);

        $groups2 = array();
        foreach ($groups as $g) {
            $groups2[] = $this->convertThreeDigit($g{0}, $g{1}, $g{2});
        }

        for ($z = 0; $z < count($groups2); $z++) {
            if ($groups2[$z] != "") {
                $output .= $groups2[$z] . $this->convertGroup(11 - $z) . ($z < 11 && !array_search('',
                        array_slice($groups2, $z + 1, -1))
                    && $groups2[11] != '' && $groups[11]{0} == '0' ? " and " : " ");
            }
        }

        $output = rtrim($output, " ");

        return $output;
    }

    public static function terbilangKwitansi($e,$format = "Rupiah")
    {
        return self::Terbilang($e,false).' '.$format;
    }

    public static function convertToHari($tanggal, $tanggal2=null)
    {
        $tanggal2 = $tanggal2 ? : date('Y-m-d');
        $ts1 = date_create($tanggal);
        $ts2 = date_create($tanggal2);

        $diff = date_diff($ts1,$ts2);
        return $diff->format("%a");
    }

    /**
     * @todo Rupiah to integer helper 
     * @author iqbal@docotel.com
     */
    public static function convertToNumber($val)
    {
        return str_replace('.', '', $val);
    }

    public static function convertCommaToPoint($val)
    {
        return str_replace(',', '.', $val);
    }

    public static function checkSchemaTabel($table, $columns)
    {
        $rows = (new \yii\db\Query())
            ->select('column_name')
            ->from('information_schema.columns')
            ->where([
                'column_name' => $columns,
                'table_name' => $table
            ])
            ->all();
        return $rows;
    }

    /**
     * Mapping array error validation message to one string
     *
     * @param Array $arrayMessage
     * @return String
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function mapMessageErrorValidation($arrayMessage, $firstRow=true)
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
     * Helper to convert date using custom format
     *
     * @param String $date
     * @param String $format default 'd-m-Y'
     * @return String
     * @author Tsani Nashrullah
     **/
    public function convertDate($date, $format='d-m-Y')
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
     * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     * 
     * Fungsi get Data - request per 10 data
     * mencegah load data keseluruhan pada dropdown
     * Request data diambil dari parameter
     * 
     * $model : untuk query ke database
     * $option : kondisi dalam query
     * 
     * Infinity Scroll Select2
     * InfinityScrollSelect2
     * 
     * Default Function Query Get Data 
     * 
     */
    public function getDataPaginationSelect2($model, $option)
    {
        $params = Yii::$app->request;
        $term = null;
        if ($params->get('term')) {
            $term = $params->get('term');
        }
        $page = $params->get('page',0);
        $limit = $params->get('limit',10);
        $offset = $params->get('offset',($page-1)*10);

        $query = $model::find();
        if (isset($option['_SELECT']) && !empty($option['_SELECT'])) {
                $query->select($option['_SELECT']);
        }
        if($term){
            foreach ($option as $key => $value) {
                foreach ($option[$key] as $key1 => $value1) {
                    if (($key === 'ILIKE') && !empty($option['ILIKE'])) {
                        $query->orWhere([$key,'LOWER('.$value1.')',$term]);
                    }
                    if (($key === 'WHERE') && !empty($option['WHERE'])) {
                        $query->andWhere(['LOWER('.$value1.')' => strtolower($term)]);
                    }
                }
            }
        }

        foreach ($option as $key => $value) {
            foreach ($option[$key] as $key1 => $value1) {
                if (($key === 'DEFAULT') && !empty($option['DEFAULT'])) {
                    $query->andWhere([$value1[0] => $value1[1]]);
                }
                if (($key === 'OTHER') && !empty($option['OTHER'])) {
                    $query->andWhere([$value1[0], $value1[1], $value1[2]]);
                }
                if (($key === 'ORDERBY') && !empty($option['ORDERBY'])) {
                    $query->orderby([$value1[0] => $value1[1]]);
                }
                if (($key === 'FILTERWHERE') && !empty($option['FILTERWHERE'])) {
                    $query->andFilterWhere(['AND',
                        [$value1[0], $value1[1], $value1[2]]
                    ]);
                }
            }
        }

        if (isset($option['GROUPBY']) && !empty($option['GROUPBY'])) {
            $query->groupBy($option['GROUPBY']);
        }

        return $query->offset($offset)->limit($limit)->asArray()->all();
    }

    public function getDiffDateTime($startDate, $endDate)
    {
        
        $firstDate = date_create(date('Y-m-d', strtotime($startDate)));
        $lastDate = date_create(date('Y-m-d', strtotime($endDate)));
        $diff = date_diff($firstDate, $lastDate);
        $year = $diff->y;
        $mounth = $diff->m;
        $hour = $diff->h; 

        $days = $diff->days; 

        $firstHour = (int) date('H', strtotime($startDate));
        $resHoursfirst =  ((int)$firstHour == 0 ) ? 0 : 24 - (int)$firstHour;
        $secondHour = (int) date('H', strtotime($endDate));
        $resHourssecond = 24 - (int)$secondHour;

        if ($days == 0) {
            $resHours = $secondHour - $firstHour;
        } else if($days == 1) {
            $resHours = $resHoursfirst + $secondHour;
        } else {
            $hoursDays = 24 * ($days - 1); 
            $resHours = $hoursDays + $resHoursfirst + $secondHour;
        }
        $results = [
            'tahun' => $year > 1 ? $year + 1 : $year,
            'bulan' => $mounth > 1 ? $mounth + 1 : $mounth,
            'hari' => $days > 1 ? $days + 1 : $days,
            'jam' => $resHours
            ];
        return $results;
    }

    /**
     * convert array to array keys by id
     * 
     * @param Array $arrays
     * @param String $idName
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function setKeyById($arrays, $idName) {
        $result = [];
        foreach ($arrays as $key => $array) {
            $keyName = isset($array[$idName]) ? $array[$idName] : $key;
            $result[$keyName] = $array;
        }
        return $result;
    }

    /**
     * Mapping error message for form frontend response
     *
     * @param Array $errors
     * @return Array
     **/
    public static function mapErrorForm($errors, $modelName='')
    {
        $result = [];
        foreach ($errors as $keyError => $value) {
            $result[$modelName . '[' . $keyError . ']'] = $value;
        }
        return $result;
    }

    /**
     * Get value of key array using string
     *
     * @param String $string
     * @param String $string
     * @return String/Array
     * @author Tsani Nashrullah
     **/
    public function getKeyByString($string, $arrayList)
    {
        $result = $arrayList;
        $finalResult = null;
        $arrayKey = explode(".", $string);
        for ($i = 0; $i < count($arrayKey); $i++) {
            if (!isset($result[$arrayKey[$i]])) {
                return null;
            } else {
                $finalResult = $result[$arrayKey[$i]];
                $result = $result[$arrayKey[$i]];
            }
        }
        return $finalResult;
    }
    
    public function namaPasien($nama, $maxLength = 20)
    {
        if (strlen($nama) > $maxLength) {
            $parts = preg_split('/\s+/', $nama);
            $nama = isset($parts[0]) ? $parts[0] . ' ' : '';
            unset($parts[0]);
            $length = count($parts);
            if (count($length)) {
                for ($x = 1; $x <= $length; $x++) {
                    $sparator = $x == $length ? '' : '.';
                    $firstChar = isset($parts[$x][0]) ? strtoupper($parts[$x][0]) : '';
                    $nama .= $firstChar ? $firstChar.$sparator : null;
                }
            }
        }
        return $nama;
    }
    
    static function terbilangToEnglish($number)
    {
        $hyphen      = ' ';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . self::terbilangToEnglish(abs($number));
        }

        $string = $fraction = null;
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string    = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::terbilangToEnglish($remainder);
                }
                break;
            default:
                $baseUnit     = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder    = $number % $baseUnit;
                $string       = self::terbilangToEnglish($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::terbilangToEnglish($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return ucwords($string);
    }

    public static function cutSentence($text, $length = 20)
    {
        $displayText = '';
        if (strlen($text) > $length) {
            $tempText = substr($text, 0, $length);
            $tempText = explode(" ", $tempText);

            if (is_array($tempText)) {
                for ($j = 0; $j < count($tempText); $j++) {
                    if (!array_key_exists($j+1, $tempText)) {
                        $tempText[$j] = substr($tempText[$j], 0, 1).'.';
                    }

                    $displayText = $displayText.' '.$tempText[$j];
                }
            } else {
                $displayText = $text;
            }
        } else {
            $displayText = $text;
        }

        return $displayText;
    }

    public static function mappDays($dayId)
    {
        $listMapp = [
            75 => 2, //"MONDAY",
            76 => 3, // "TUESDAY"
            77 => 4, //"WEDNESDAY"
            78 => 5, // "THURSDAY"
            79 => 6, // "FRIDAY"
            80 => 7, //"SATURDAY"
            81 => 1, //"SUNDAY"
        ];

        return isset($listMapp[$dayId]) ? $listMapp[$dayId] : null;
    }

    public static function getShift($dateTime = null)
    {
        $time = strtotime(date($dateTime ? : "H:i:s"));
        $listShift = Cache::getShift();
        $shift_name = "";
        foreach ($listShift as $value) {
            $timeStart = strtotime($value['shift_jamawal']);
            $timeEnd = strtotime($value['shift_jamakhir']);
            if ($time >= $timeStart && $time <= $timeEnd) {
                $shift_name = $value['shift_nama'];
                break;
            }
        }
        return $shift_name;
    }
    
    public static function generateTimeStamp($date, $timezone = null)
    {
        $dateConvert = strtotime($date);

        if ($timezone == 'UTC') {
            date_default_timezone_set('UTC');
            $dateConvert = strtotime($date . "-7hours");
        }
        // $timestamp = DateTime::createFromFormat(
        //     'Y-m-d h:i:s',
        //     $date,
        //     new \DateTimeZone('UTC')
        // );

        return self::formatToMilisecond($dateConvert, 'milidetik');
    }

    public static function formatToMilisecond($angka, $satuan = null)
    {
        $milisecond = $angka * 1000;
        
        if ($satuan == 'detik') {
            return $angka;
        } else if ($satuan == 'milidetik') {
            return $milisecond;
        }

        return [
            'detik' => $angka,
            'milidetik' => $milisecond
        ];
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

    public static function getUploadFileExcel($file)
    {
        try {
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::Identify($file);
            $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::CreateReader($inputFileType);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $e) {
            return [
                'sheet' => '',
                'highestRow' => 0,
                'highestColumn' => 'AMK',
                'message' => $e->getMessage()
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

    /**
     * @method genderCode (Get Inisial Jenis Kelamin)
     * @param Integer $lookupId
     * @return String
     */
    public static function genderCode($lookupId)
    {
        if (empty($lookupId)) {
            $genderCode =  '-';
        }
        if ($lookupId == DocoConstants::VAR_LK) {
            $genderCode = 'L';
        } elseif ($lookupId == DocoConstants::VAR_PR) {
            $genderCode = 'P';
        } else {
            $genderCode = 'U';
        }
        return $genderCode;
    }

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

    public static function filterArrayLike($data = array(), $comparisonKey, $comparisonValue, $caseInsensitive = true)
    {
        if (empty($data)) {
            return array();
        }

        if ($comparisonValue === '' || $comparisonValue === null) {
            return $data;
        }

        $filtered = array_filter($data, function($item) use ($comparisonKey, $comparisonValue, $caseInsensitive) {
            if (!isset($item[$comparisonKey])) {
                return false;
            }

            $value = (string)$item[$comparisonKey];
            $comparisonValue = (string)$comparisonValue;

            if ($caseInsensitive) {
                return stripos($value, $comparisonValue) !== false;
            } else {
                return strpos($value, $comparisonValue) !== false;
            }
        });

        return array_values($filtered);
    }
}
