<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\classes\ExcelColumn;
use Integrasi\Components\DocoExcelActiveRecord;

class LaporanPemakaianObatRuanganView extends DocoExcelActiveRecord {
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
        $advanced_filter = ArrayHelper::getValue($request, 'advanced-filter');
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
            (array) new ExcelColumn('ruangan_nama', self::STRING_TYPE, 'Nama Ruangan'),
            (array) new ExcelColumn('tgl_transaksi', self::DATERANGE, 'Tanggal Transaksi'),
            (array) new ExcelColumn('no_transaksi', self::STRING_TYPE, 'Nomor Transaksi'),
            (array) new ExcelColumn('jenisobatalkes_nama', self::STRING_TYPE, 'Jenis Obat Alkes'),
            (array) new ExcelColumn('kode_obat', self::STRING_TYPE, 'Kode Obat'),
            (array) new ExcelColumn('nama_obat', self::STRING_TYPE, 'Nama Obat'),
            (array) new ExcelColumn('qty_input', self::NUMBER, 'Qty'),
            (array) new ExcelColumn('satuan_besar', self::STRING_TYPE, 'Satuan'),
            (array) new ExcelColumn('harga_netto_konversi', self::NUMBER, 'Harga Satuan (Rp)'),
            (array) new ExcelColumn('total_harga', self::NUMBER, 'Total Harga (Rp)'),
            (array) new ExcelColumn('user', self::STRING_TYPE, 'User'),
            (array) new ExcelColumn('catatan', self::STRING_TYPE, 'Catatan'),
        ];
    }
}
