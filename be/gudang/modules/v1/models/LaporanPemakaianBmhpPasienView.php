<?php

namespace app\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\classes\ExcelColumn;

class LaporanPemakaianBmhpPasienView extends \Doco\components\DocoActiveRecord {
    const START_DATETIME = 'Y-m-d 00:00:00';
    const END_DATETIME = 'Y-m-d 23:59:59';
    const DATERANGE = 'daterange';
    const NUMBER = 'number';
    const STRING_TYPE = 'string';
    const BOOLEAN_TYPE = 'boolean';
    const DATE_FORMAT = 'd M Y';


    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'laporanpemakaianbmhp_v';
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
            (array) new ExcelColumn('no_rm', self::STRING_TYPE, 'Nomor Rekam Medik'),
            (array) new ExcelColumn('no_pendaftaran', self::STRING_TYPE, 'Nomor Pendaftaran'),
            (array) new ExcelColumn('nama_pasien', self::STRING_TYPE, 'Nama Pasien'),
            (array) new ExcelColumn('tindakan', self::STRING_TYPE, 'Nama Tindakan'),
            (array) new ExcelColumn('obatalkes_kode', self::STRING_TYPE, 'Kode Obat'),
            (array) new ExcelColumn('obatalkes_nama', self::STRING_TYPE, 'Nama Obat'),
            (array) new ExcelColumn('qty', self::NUMBER, 'Qty'),
            (array) new ExcelColumn('satuan_kecil_nama', self::STRING_TYPE, 'Satuan'),
            (array) new ExcelColumn('harga_netto', '-', 'Harga Satuan (Rp)'),
            (array) new ExcelColumn('total', '-', 'Total Harga (Rp)'),
            (array) new ExcelColumn('is_ditagihkan', self::BOOLEAN_TYPE, 'Ditagihkan'),
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

                if($rowData != '-') {
                    $rowData = self::typeFormatter($type, $rowData);
                }

                $newValue[\Yii::t('app', $col['label'])] = $rowData;
            }

            $result[$key] = $newValue;
        }

        return $result;
    }

    public function typeFormatter($type, $rowData)
    {
        $result = '';

        switch ($type) {
            case self::DATERANGE:
                $result = date(self::DATE_FORMAT, strtotime($rowData));
                break;

            case self::NUMBER:
                $result = number_format($rowData, 2, ',', '.');
                break;

            case self::BOOLEAN_TYPE:
                $result = $rowData ? "Ya" : "Tidak";
                break;
                
            default:
                $result = $rowData;
                break;
        }

        return $result;
    }
}
