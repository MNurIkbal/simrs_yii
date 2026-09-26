<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;

class InfoPasienRanapView extends DocoExcelActiveRecord {

    public static function tableName() {
        return 'infopasienri_v';
    }

    // public function columnNames() {
    //     return [
    //         (array) new ExcelColumn('tgl_admisi', self::DATE_EXCEL, 'Kode Obat'),
    //         (array) new ExcelColumn('no_rekam_medik', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('no_pendaftaran', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('nama_pasien', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('jenis_kelamin', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('dokter_admisi', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('carabayar_nama', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('penjamin_nama', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('hak_kelas', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('kelas_pelayanan', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('status_titipan', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('jeniskasuspenyakit_nama', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('stat_ranap', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('ruangan_nama', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('kamarruangan_nokamar', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('no_tempattidur', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('hari_rawat', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('tgl_pindahkamar', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('rencana_pulang', self::STRING_TYPE, 'Kode Obat'),
    //         (array) new ExcelColumn('status_pasien', self::STRING_TYPE, 'Kode Obat'),
    //     ];
    // }

}