<?php

namespace app\modules\v1\models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;

class LapDiskonPayerView extends DocoExcelActiveRecord
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
        return 'laporandiskonpayer_v';
    }

    public function columnNames()
    {
        return [
            (array) new ExcelColumn('tgl_pembayaran', self::DATE_TYPE, 'Tanggal Pembayaran'),
            (array) new ExcelColumn('tgl_masuk', self::DATE_TYPE, 'Tanggal Masuk / Keluar'),
            (array) new ExcelColumn('no_pembayaran', self::STRING_TYPE, 'No Pembayaran'),
            (array) new ExcelColumn('instalasi_nama', self::STRING_TYPE, 'Instalasi / Ruangan Akhir'),
            (array) new ExcelColumn('no_pendaftaran', self::STRING_TYPE, 'No Pendaftaran'),
            (array) new ExcelColumn('nama_pasien', self::STRING_TYPE, 'Nama Pasien'),
            (array) new ExcelColumn('carabayar_nama', self::STRING_TYPE, 'Cara Bayar / Penjamin'),
            (array) new ExcelColumn('billing', self::NUMBER_TYPE, 'Billing'),
            (array) new ExcelColumn('diskon_payer', self::STRING_TYPE, 'Diskon Payer'),
            (array) new ExcelColumn('billing_after_diskon', self::NUMBER_TYPE, 'Billing'),
            (array) new ExcelColumn('disc_billing', self::NUMBER_TYPE, 'Disc. Billing'),
            (array) new ExcelColumn('dibayar_penjamin', self::NUMBER_TYPE, 'Jumlah Dibayar Penjamin'),
            (array) new ExcelColumn('dibayar_pasien', self::NUMBER_TYPE, 'Jumlah Dibayar Pasien'),
        ];
    }

    public function setHeaderExcel($advanced_filter)
    {
        $cols = $this->columnNames();
        $array_filter = [];
        foreach ($cols as $col) {
            $name = $col['name'];
            $label = $col['label'];
            $type = ArrayHelper::getValue($col, 'type', '-');
            if ($type == self::DATERANGE_TYPE) {
                if (isset($advanced_filter[$name])) {
                    $exp = explode(' - ', $advanced_filter[$name]);
                    $tgl_awal = $exp[0];
                    $tgl_akhir = $exp[1];
                    $tgl_awal = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                    $tgl_akhir = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                    $array_filter[Yii::t('app', $label)] = date(self::DATE_FORMAT, strtotime($tgl_awal)) . " - " . date(self::DATE_FORMAT, strtotime($tgl_akhir));
                }
            } else {
                if (isset($advanced_filter[$name])) {
                    $array_filter[Yii::t('app', $label)] = $advanced_filter[$name];
                }
                else {
                    if(isset($advanced_filter['carabayar_id']) && !empty($advanced_filter['carabayar_id'])) {
                        $carabayar_id = $advanced_filter['carabayar_id'];
                        $dataCaraBayar = $this->getCaraBayar($carabayar_id);
                        $array_filter[Yii::t('app', 'Cara Bayar')] = $dataCaraBayar['carabayar_nama'];
                    }
                    if(isset($advanced_filter['penjamin_id']) && !empty($advanced_filter['penjamin_id'])) {
                        $penjamin_id = $advanced_filter['penjamin_id'];
                        $dataPenjamin = $this->getPenjamin($penjamin_id);
                        $array_filter[Yii::t('app', 'Nama Payer')] = $dataPenjamin['penjamin_nama'];
                    }
                }
            }
        }
        return $array_filter;
    }

    public function mappingDataExcel($data)
    {
        $cols = $this->columnNames();
        $result = [];
        foreach ($data->all() as $key => $value) {
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
                
                if($name == 'tgl_masuk') {
                    $tglMasuk = !empty($value['tgl_masuk']) ? date(self::DATE_FORMAT, strtotime($value['tgl_masuk'])) : '-';
                    $tglKeluar = !empty($value['tgl_keluar']) ? date(self::DATE_FORMAT, strtotime($value['tgl_keluar'])) : '-';
                    $newValue[\Yii::t('app', $col['label'])] = $tglMasuk .' / ' . $tglKeluar;
                }
                elseif($name == 'instalasi_nama') {
                    $instalasi = !empty($value['instalasi_nama']) ? $value['instalasi_nama'] : '-';
                    $ruangan = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '-';
                    $newValue[\Yii::t('app', $col['label'])] = $instalasi .' / '. $ruangan;
                }
                elseif($name == 'carabayar_nama') {
                    $carabayar = !empty($value['carabayar_nama']) ? $value['carabayar_nama'] : '-';
                    $penjamin = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : '-';
                    $newValue[\Yii::t('app', $col['label'])] = $carabayar .' / '. $penjamin;
                }
                elseif($name == 'diskon_payer') {
                    $newValue[\Yii::t('app', $col['label'])] = empty($rowData) || is_null($rowData) ? '0%' : $rowData . '%';
                }
                else {
                    $newValue[\Yii::t('app', $col['label'])] = $rowData;
                }
            }

            $result[$key] = $newValue;
        }
        return $result;
    }

    private function getCaraBayar($carabayar_id)
    {
        return CaraBayar::findOne($carabayar_id);
    }

    private function getPenjamin($penjamin_id)
    {
        return Penjamin::findOne($penjamin_id);
    }
}
