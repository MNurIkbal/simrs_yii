<?php

namespace app\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\classes\ExcelColumn;

class LaporanPemakaianObatRuanganView extends \Doco\components\DocoActiveRecord {
    const START_DATETIME = 'Y-m-d 00:00:00';
    const END_DATETIME = 'Y-m-d 23:59:59';
    const DATERANGE = 'daterange';
    const NUMBER = 'number';
    const STRING_TYPE = 'string';
    const DATE_FORMAT = 'd M Y';


    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'laporanpemakaianobatruangan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [];
    }

    public function daterangeFilter($query, $request, $field_name) {
        $advanced_filter = $request->get('advanced-filter');
        $start = date(self::START_DATETIME);
        $end   = date(self::END_DATETIME);
        if(isset($advanced_filter) && isset($advanced_filter[$field_name])) {
            $explode = explode(" - ", $advanced_filter[$field_name]);
            if(count($explode) == 2) {
                $start = date(self::START_DATETIME, strtotime($explode[0]));
                $end = date(self::END_DATETIME, strtotime($explode[1]));
            }
        }
        $query->andWhere(['between', $field_name, $start, $end]);
    }

    public function columnNames()
    {
        return [
            (array) new ExcelColumn('tgl_transaksi', self::DATERANGE, 'Tanggal Transaksi'),
            (array) new ExcelColumn('no_transaksi', self::STRING_TYPE, 'Nomor Transaksi'),
            (array) new ExcelColumn('kode_obat', self::STRING_TYPE, 'Kode Obat'),
            (array) new ExcelColumn('nama_obat', self::STRING_TYPE, 'Nama Obat'),
            (array) new ExcelColumn('qty_input', self::STRING_TYPE, 'Qty'),
            (array) new ExcelColumn('satuan_besar', self::STRING_TYPE, 'Satuan'),
            (array) new ExcelColumn('harga_netto_konversi', self::NUMBER, 'Harga Satuan (Rp)'),
            (array) new ExcelColumn('total_harga', self::NUMBER, 'Total Harga (Rp)'),
            (array) new ExcelColumn('user', self::STRING_TYPE, 'User'),
            (array) new ExcelColumn('catatan', self::STRING_TYPE, 'Catatan'),
        ];
    }

    public function setHeaderExcel($advanced_filter) {
        $cols = $this->columnNames();
        $array_filter = [];
        foreach ($cols as $col) {
            $name = $col['name'];
            $label = $col['label'];
            $type = ArrayHelper::getValue($col, 'type', '-');
            if($type == self::DATERANGE) {
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

    public function mappingDataExcel($data) {
        $cols = $this->columnNames();
        $result = [];
        foreach ($data->all() as $key => $value) {
            $newValue = [];
            foreach ($cols as $col) {
                $name = $col['name'];
                $type = ArrayHelper::getValue($col, 'type', '-');
                $rowData = ArrayHelper::getValue($value, $name, '-');
                if($type == self::DATERANGE && $rowData != '-') {
                    $rowData = date(self::DATE_FORMAT, strtotime($rowData));
                }

                if($type == self::NUMBER) {
                    $rowData = number_format($rowData, 2, ',', '.');
                }

                $newValue[\Yii::t('app', $col['label'])] = $rowData;
            }

            $result[$key] = $newValue;
        }

        return $result;
    }
}
