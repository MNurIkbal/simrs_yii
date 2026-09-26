<?php

namespace app\modules\v1\models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Pegawai;

class LapDiskonView extends DocoExcelActiveRecord
{
    const DATERANGE_TYPE = 'daterange';
    const DATE_TYPE = 'date';
    const STRING_TYPE = 'string';
    const NUMBER_TYPE = 'number';
    const DATE_FORMAT = 'd M Y';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporandiskon_v';
    }

    public function columnNames()
    {
        return [
            (array) new ExcelColumn('no_pembayaran', self::STRING_TYPE, 'Bill No'),
            (array) new ExcelColumn('tgl_diskon', self::DATE_TYPE, 'Discount Date'),
            (array) new ExcelColumn('no_pendaftaran', self::STRING_TYPE, 'IP No.'),
            (array) new ExcelColumn('nama_pasien', self::STRING_TYPE, 'Patient Name'),
            (array) new ExcelColumn('authorized_by', self::STRING_TYPE, 'Authorized By'),
            (array) new ExcelColumn('billing_total', self::NUMBER_TYPE, 'Bill Amt.'),
            (array) new ExcelColumn('discount_total', self::NUMBER_TYPE, 'Discount'),
            (array) new ExcelColumn('username', self::STRING_TYPE, 'Username'),
            (array) new ExcelColumn('remarks', self::STRING_TYPE, 'Remarks'),
            (array) new ExcelColumn('tgl_keluar', self::DATE_TYPE, 'Discharge Date'),
            (array) new ExcelColumn('discount_type', self::STRING_TYPE, 'Discount Type'),
        ];
    }

    public function setHeaderExcel($advanced_filter)
    {
        $cols = $this->columnNames();
        $array_filter = [];
        $pegawai_id = Yii::$app->user->identity->pegawai_id;
        $nama_pegawai = Pegawai::find()->select([
            'nama_pegawai'
        ])->where([
            'pegawai_id' => $pegawai_id
        ])->asArray()->one();
        
        foreach ($cols as $col) {
            $name = $col['name'];
            $label = $col['label'];
            $type = ArrayHelper::getValue($col, 'type', '-');
            if ($type == self::DATERANGE_TYPE) {
                if (isset($advanced_filter)) {
                    $exp = explode(' - ', $advanced_filter);
                    $tgl_awal = $exp[0];
                    $tgl_akhir = $exp[1];
                    $tgl_awal = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                    $tgl_akhir = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                    $array_filter[Yii::t('app', $label)] = date(self::DATE_FORMAT, strtotime($tgl_awal)) . " - " . date(self::DATE_FORMAT, strtotime($tgl_akhir));
                    
                }
            } else {
                if (isset($advanced_filter[$name])) {
                    $array_filter[Yii::t('app', $label)] = $advanced_filter[$name];
                    $array_filter[Yii::t('app', 'Nama pegawai')] = $nama_pegawai['nama_pegawai'];
                }
            }
        }
        return $array_filter;
    }

    public function mappingDataExcel($data)
    {
        $cols = $this->columnNames();
        $result = [];
        
        foreach ($data->asArray()->all() as $key => $value) {
            $newValue = [];
            foreach ($cols as $col) {
                $name = $col['name'];
                $type = ArrayHelper::getValue($col, 'type', '-');
                $rowData = ArrayHelper::getValue($value, $name, '-');
                if ($type == self::DATERANGE_TYPE && $rowData != '-') {
                    $rowData = date(self::DATE_FORMAT, strtotime($rowData));
                }

                if ($type == self::DATE_TYPE && $rowData != null) {
                    $rowData = date(self::DATE_FORMAT, strtotime($rowData));
                }

                if ($type == self::NUMBER_TYPE) {
                    $rowData = $rowData;
                    $rowData = number_format($rowData, 0, ',', '.') . ' ';
                }
                
                if($name == 'remarks') {
                    $remarks = !empty($value['remarks']) ? $value['remarks'] : '-';
                    $newValue[\Yii::t('app', $col['label'])] = $remarks; }
                elseif($name == 'tgl_keluar') {
                        $tgl_keluar = !empty($value['tgl_keluar']) ?  date(self::DATE_FORMAT, strtotime($value['tgl_keluar'])) : '-';
                        $newValue[\Yii::t('app', $col['label'])] = $tgl_keluar; }
                else {
                    $newValue[\Yii::t('app', $col['label'])] = $rowData;
                }
            }
            $result[$key] = $newValue;
            
        }
        
        return $result;
    }

  
}
