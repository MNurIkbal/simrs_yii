<?php

namespace Doco\components;

use Yii;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;
// use Doco\models\Lookup;
// use app\modules\v1\models\GolonganUmur;
// use \DateTime;
// use Doco\components\DocoMessages;

class DocoSpout
{
    public static function exportExcel($title, $results, $excelHeader = array(), $options = array(), $footer = [], $footerInfo = [],$stram = false){
        return self::generateFile($title, $results, $excelHeader, $options, $footer, $footerInfo, $stram, Type::XLSX);
    }

    public static function exportCsv($title, $results, $excelHeader = array(), $options = array(), $footer = [], $footerInfo = [],$stram = false){
        return self::generateFile($title, $results, $excelHeader, $options, $footer, $footerInfo, $stram, Type::CSV);
    }

	protected static function generateFile($title, $results, $excelHeader = array(), $options = array(), $footer = [], $footerInfo = [],$stram = false, $type = Type::XLSX)
    {
        // Directory Creation
        $uploadPath = @$options['uploadPath'];
        $skipIncrement = @$options['skipIncrement'];
        $skipHeader = @$options['skipHeader'];
        $subHeader = @$options['subHeader'];
        $customHeader = @$options['customHeader'];
        $fileName = @$options['fileName'];
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
        if(isset($options['fileName'])){
            $fileName = @$options['fileName'];
        }else{
            $fileName= isset($realPath[0]) ? $realPath[0] : $title;
        }
        $extension = $type == Type::XLSX ? '.xlsx' : '.csv';
        $filePath = $uploadPath . '/' . $fileName . $extension ;
        $downloadPath = $uploadPath . '/' . $fileName . $extension;
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
                foreach($row as $key => $val) {
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
        $styleTitle = (new StyleBuilder())
        	->setFontBold()
        	->setFontSize(14)
        	->build();
        $styleText = (new StyleBuilder())
           ->setShouldWrapText(true)
           ->build();

        $writer = WriterFactory::create($type);
        $writer->openToFile($filePath);

        $writer->addRowsWithStyle([[$title],['']],$styleTitle);
        foreach ($excelHeader as $key => $val) {
        	$writer->addRow([$key.':','','',$val]);
        }
        $writer->addRow(['']);

        if(count($arrayData)>0 && isset($arrayData[0]) && reset($arrayData) == $arrayData[0]){
        	$styleBold = (new StyleBuilder())
        	->setFontBold()
        	->build();
        	$writer->addRowWithStyle($arrayData[0],$styleBold);
        	unset($arrayData[0]);
        }
        foreach ($arrayData as $rowValues) {
        	$writer->addRowWithStyle($rowValues,$styleText);
        }
        return $writer;
    }
}