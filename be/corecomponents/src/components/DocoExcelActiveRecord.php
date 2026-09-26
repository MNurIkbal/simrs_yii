<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\components;

use Yii;
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DocoExcelActiveRecord extends DocoActiveRecord {
    const DATERANGE_TYPE = 'daterange';
    const DATE_TYPE = 'date';
    const STRING_TYPE = 'string';
    const NUMBER_TYPE = 'number';
    const DATE_FORMAT = 'd M Y';
    const DATE_TIME = 'd M Y hh:mm:ss';
    const DATE_EXCEL = 'dateexcel';

    /**
     * @usage set array untuk header excel; menampilkan filter pada header excel
     * @param array $advanced_filter
     * example usage: InfRetur/ExportExcelAction.php
    */
    public function setHeaderExcel($advanced_filter) {
        $cols = $this->columnNames();
        $array_filter = [];
        foreach ($cols as $col) {
            $name = $col['name'];
            $label = $col['label'];
            $type = ArrayHelper::getValue($col, 'type', '-');
            if($type == self::DATERANGE_TYPE) {
                if(isset($advanced_filter[$name])) {
                    $exp = explode(' - ', $advanced_filter[$name]);
                    $tgl_awal = $exp[0];
                    $tgl_akhir = $exp[1];
                    $tgl_awal = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                    $tgl_akhir = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                    $array_filter[Yii::t('app', $label)] = date(self::DATE_FORMAT, strtotime($tgl_awal))." - ".date(self::DATE_FORMAT, strtotime($tgl_akhir));
                }
            } else {
                if(isset($advanced_filter[$name])) {
                    $array_filter[Yii::t('app', $label)] = $advanced_filter[$name];
                }
            }
        }

        return $array_filter;
    }

    /**
     * @usage mapping array data excel
     * @param query array $data
     * example usage: InfRetur/ExportExcelAction.php
    */
    public function mappingDataExcel($data) {
        $cols = $this->columnNames();
        $result = [];

        if(!is_array($data)) {
            $data = $data->all();
        }

        foreach ($data as $key => $value) {
            $newValue = [];
            foreach ($cols as $col) {
                $name = $col['name'];
                $type = ArrayHelper::getValue($col, 'type', '-');
                $rowData = ArrayHelper::getValue($value, $name, '-');
                if($type == self::DATE_TYPE) {
                    if(empty($rowData)) {
                        $rowData = '';
                    } else {
                        $rowData = date(self::DATE_FORMAT, strtotime($rowData));
                    }
                }

                if($type == self::DATE_EXCEL && $rowData != null) {
                    if(empty($rowData)) {
                        $rowData = '';
                    } else {
                        $rowData = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($rowData);
                    }
                }


                if($type == self::NUMBER_TYPE) {
                    $rowData = number_format($rowData, 2, '.', ',');
                }

                $newValue[\Yii::t('app', $col['label'])] = $rowData;
            }
            $result[$key] = $newValue;
        }

        return $result;
    }
}
