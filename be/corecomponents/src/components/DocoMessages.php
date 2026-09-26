<?php 

/**
 * @Author: budi@docotel.com
 * @Date:   2019-12-04 10:43:21
 * @Description: setting response message
 */

namespace Doco\components;

use Yii;

class DocoMessages 
{

    // response error title 
    const ERR_TITLE = 'Proses Gagal !';

    // response error message 
    const ERR_MESSAGE = 'Terjadi kesalahan pada sistem';

    // response error message pasien sudah dipulangkan
    const ERR_MESSAGE_DIPULANGKAN = 'Pasien sudah dipulangkan/dibatalkan.';

    // response error message pasien tidak sesuai
    const ERR_MESSAGE_TIDAK_SESUAI = 'Data pasien tidak sesuai';

    // response error message no SEP required
    const ERR_MESSAGE_SEP_REQUIRED = 'No. SEP tidak boleh kosong.';

    // response error message no SEP sudah digunakan
    const ERR_MESSAGE_SEP_EXIST = 'No. SEP sudah digunakan pasien lain.';

    // response error message peserta BPJS tidak ditemukan
    const ERR_MESSAGE_BPJS_EMPTY = 'Peserta BPJS tidak ditemukan';

    // response error message server bpjs
    const ERR_MESSAGE_BPJS_SERVER = 'Terjadi Kesalahan pada server BPJS';

    const ERR_MESSAGE_NO_DIBAYARKAN = 'Tidak ada obat/tindakan yang dibayarkan.';

    const ERR_MESSAGE_TINDAKAN_NOT_EXIST = 'Ada detail transaksi dengan tindakan yang tidak ada di cara bayar baru (baris berwarna merah)';

    const ERR_MESSAGE_PENDAFTARAN_NOT_EXIST = 'Data Pendaftaran tidak ditemukan.';
    
    const ERR_MESSAGE_TRANSAKSI_EXIST = 'Data Sudah di transaksikan';

    const ERR_MESSAGE_VALIDASI_0 = 'tidak boleh 0';

    const ERR_MESSAGE_DATA_NOT_FOUND = 'Data tidak ditemukan';

    // response success
    const SUC_TITLE = 'Proses Berhasil !';
    const SUC_MESSAGE = 'Data berhasil di simpan.';
    const SUC_MESSAGE_UPDATED = 'Data berhasil di ubah.';
    const SUC_MESSAGE_DELETED = 'Data berhasil di hapus.';
    
    // key response message
    const KEY_ERR_VALIDATION = 'error_validation';
    const KEY_ERR_SYSTEM = 'error';
    const KEY_ERR_CUSTOM = 'error_custom';
    const KEY_SUC_SYSTEM = 'sucess';
    const KEY_SUC_SYSTEM_DATA = 'sucess';
    const KEY_UPDATED = 'updated';
    const KEY_DELETED = 'deleted';

    const KEY_DYNAMIC_STATUS = 'dynamic_code';
    const MESSAGE_SBAR_DELETED   = 'Data SBAR telah di Hapus.';
    const MESSAGE_SBAR_VERIFIKASI     = 'Data SBAR telah di Verifikasi';
}
    