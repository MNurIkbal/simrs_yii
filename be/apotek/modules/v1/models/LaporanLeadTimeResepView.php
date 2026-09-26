<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * This is the model class for table "laporanleadtimeresep_v".
 */
class LaporanLeadTimeResepView extends DocoExcelActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanleadtimeresep_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('ruangan', self::STRING_TYPE, 'Ruangan'),
            (array) new ExcelColumn('tgl_resep', self::DATE_EXCEL, 'Tanggal Resep'),
            (array) new ExcelColumn('no_resep', self::STRING_TYPE, 'No Resep'),
            (array) new ExcelColumn('jenis_resep', self::STRING_TYPE, 'Jenis Resep'),
            (array) new ExcelColumn('jumlah_r', self::NUMBER_TYPE, 'Jumlah R/'),
            (array) new ExcelColumn('dokter', self::STRING_TYPE, 'Dokter'),
            (array) new ExcelColumn('jumlah_item', self::NUMBER_TYPE, 'Jumlah Item'),
            (array) new ExcelColumn('jam_resep_masuk', self::DATE_TIME, 'Jam Resep Masuk'),
            (array) new ExcelColumn('jam_resep_dibayar', self::DATE_TIME, 'Jam Resep Dibayarkan'),
            (array) new ExcelColumn('jam_production', self::DATE_TIME, 'Jam Resep Production'),
            (array) new ExcelColumn('jam_diserahkan', self::DATE_TIME, 'Jam Resep Siap Diserahkan'),
            (array) new ExcelColumn('waktu_tunggu', self::DATE_TIME, 'Waktu Tunggu Obat')
        ];
    }
}
