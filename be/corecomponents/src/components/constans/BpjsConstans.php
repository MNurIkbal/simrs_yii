<?php


namespace Doco\components\constans;

class BpjsConstans
{
    const TUJUAN_PROSEDUR = 1;
    const TUJUAN_KONSUL = 2;
    const TUJUAN_NORMAL = 0;
    const TUJUAN_KONTROL = 5;

    const PROSEDUR_TIDAK_LANJUT = 0;
    const PROSEDUR_LANJUT = 1;

    const PELAYANAN_RAWAT_INAP = 1;

    const LAKALANTAS_BKLL = 0;
    const LAKALANTAS_KLL_BKK = 1;
    const LAKALANTAS_KLL_KK = 2;
    const LAKALANTAS_KK = 3;

    const JENIS_PESERTA_PNS = 1;

    const STRING_IGD = 'IGD';

    const STRING_PROSEDUR_TIDAK_LANJUT = 'Prosedur tidak berkelanjutan';
    const STRING_PROSEDUR_LANJUT = 'Prosedur berkelanjutan';
    const STRING_KONSUL_DOKTER = 'Konsul dokter';
    const STRING_TUJUAN_NORMAL = 'Normal';
    const STRING_TUJUAN_KONTROL = 'Konsultasi dokter (pertama)';
    const STRING_KUNJUNGAN_INTERNAL = 'Kunjungan rujukan internal';

    //* kode hak kelas BPJS
    const KELAS_1 = 1;
    const KELAS_2 = 2;
    const KELAS_3 = 3;

    // *Lookup value
    const V_NAIK_KELAS_ICU = 7;
    const V_NAIK_KELAS_ICCU = 6;
    const V_NAIK_KELAS_3 = 5;
    const V_NAIK_KELAS_2 = 4;
    const V_NAIK_KELAS_1 = 3;
    const V_NAIK_KELAS_VIP = 2;
    const V_NAIK_KELAS_VVIP = 1;

    const JENIS_KELAMIN_PESERTA_LAKI = 'L';
    const JENIS_KELAMIN_PESERTA_PEREMPUAN = 'P';

    /* String Laka Lantas */
    const STRING_LAKALANTAS = [
        '0' => 'Bukan kecelakaan lalu lintas',
        '1' => 'Kecelakaan lalu lintas',
        '2' => 'Kecelakaan lalu lintas dan kerja',
        '3' => 'Kecelakaan kerja',
    ];
}
