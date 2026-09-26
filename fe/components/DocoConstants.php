<?php

/**
 * @author: rizfardi@docotel.com
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components;

use Yii;

class DocoConstants
{
    const INSTALASI_ID_RJ = 1;
    const INSTALASI_ID_RD = 2;
    const INSTALASI_ID_RI = 3;
    const INSTALASI_ID_PENUNJANG = [4, 5, 7];
    const INSTALASI_ID_LAB = 4;
    const INSTALASI_ID_RAD = 5;
    const INSTALASI_ID_REHAB = 7;
    const INSTALASI_ID_RM = 8;
    const INSTALASI_FISIOTERAPI = 7;
    const INSTALASI_ID_BEDAH = 12;
    const INSTALASI_GUDANG_UMUM = 15;
    const INSTALASI_GUDANG_FARMASI = 66;
    const INSTALASI_MCU = 21;
    const INSTALASI_FARMASI = 6;
    const INSTALASI_KASIR = 9;
    const INSTALASI_ID_APPOINTMENT = '';
    const SINGKATAN_RJ = 'RJ';
    const TITLE_RJ = 'Rawat Jalan';
    const SINGKATAN_RI = 'RI';
    const TITLE_RI = 'Rawat Inap';
    const SINGKATAN_RD = 'RD';
    const SINGKATAN_RD_NEW = 'IGDD';
    const TITLE_RD = 'Rawat Darurat';
    const SINGKATAN_MCU = 'MCU';

    /**
     *
     * @author : Dede Herdiana
     * Digunakan untuk mendefinisikan format text
     *
     */
    const RUPIAH = 'RUPIAH';
    const TEXT = 'TEXT';

    /**
     *
     * @author : metafiliana
     * LOOKUP by KODE, get lookup_type = jenis_stokopname
     * LU_KD_JSO_SA = stok awal
     * LU_KD_JSO_P = penyesuaian
     *
     */
    const LU_KD_JSO_SA = 'SA';
    const LU_KD_JSO_P = 'P';

    /**
     * @todo ini untuk kelompok tindakan karcis
     **/
    const VAR_KEL_KRCS = 17;

    /**
     * @todo ini untuk kelompok tindakan karcis penunjang
     **/
    const VAR_KEL_KRCS_PNJ = 19;

    /**
     * @todo ini untuk kelompok tindakan pelayanan pembedahan
     **/
    const KEL_PEL_BEDAH = 3;

    /**
     * @todo untuk keperuluan di transaksi pembayaran tagihan pasoen
     **/
    const PASIEN_PULANG = 'pasien_pulang';
    const PASIEN_KARCIS = 'pasien_karcis';
    const PASIEN_PENUNJANG = 'pasien_penunjang';
    const PASIEN_ALKES = 'pasien_alkes';

    /**
     * @todo ini untuk Pasien batal di pendaftaran
     **/
    const BTL_PERIKSA = 411;

    /**
     * @todo ini untuk kebutuhan di master pengambilan-antrian
     **/
    public static $show_cara_bayar = [326, 315, 314];

    /**
     * @todo ini untuk kebutuhan di master pengambilan-antrian
     **/
    public static $show_instalasi = [330, 329, 319];

    /**
     * @todo ini untuk kebutuhan di master fungsi Ruangan Berdasarkan
     * @example [
     *      'lookup_kode' => [
     *                'lookup_id' => 'lookup_name'
     *        ]
     *   ]
     **/
    const CACHE_FUNGSI_ANTRIAN = 'fungsi_antrian';

    /**
     * @todo ini untuk kebutuhan di master fungsi Ruangan Berdasarkan
     * @example [
     *      'instalasi_id' => [
     *            'ruangan_id' => 'ruangan_nama'
     *        ]
     *   ]
     **/
    const CACHE_RUANGAN = 'ruangan';
    const CACHE_PEGAWAI_DOKTER = 'pegawai_dokter';

    const WS_RAJAL = 5; // workspace ruangan pendaftaran rawat jalan
    const WS_IGD = 10; // workspace ruangan pendaftaran rawat darurat
    const WS_RANAP = 11; // workspace ruangan pendaftaran rawat inap
    const WS_PENUNJANG = 28; // workspace ruangan pendaftaran penunjang
    const WS_RAJAL_CBBT = 176;
    const WS_MCU = 74;
    const PARAM_DFTR = [
        self::WS_RAJAL => 'rajal',
        self::WS_RANAP => 'ranap',
        self::WS_IGD => 'igd',
        self::WS_PENUNJANG => 'penunjang',
        self::WS_RAJAL_CBBT => 'rajal',
        self::WS_MCU => 'mcu',
    ];


    // const untuk status booking pemesanan kamar
    const BOOK_EXPIRED = 367;
    const BOOK_BATAL = 368;
    const BOOK_TOLAK = 369;
    const BOOK_SETUJU = 370;
    const BOOK_BLM_KNFRM = 371;

    const ANTRIAN_POLI = 312;
    const ANTRIAN_PENUNJANG = 179;

    const GUDANG_FARMASI = 25;
    const GUDANG_PEMUSNAHAN = 61;

    /**
     * @todo untuk keterangan terisi kamar di transaksi booking kamar
     **/
    const ISI_PRMPN = 3; // isi perempuan
    const ISI_LAKI = 4; // isi laki2
    const DIPESAN = 6; // dipesan

    /**
     * author: Rizqi Febian
     * @todo untuk kondisi hasil tekanan darah
     **/
    const TD_HASIL = [];

    static $optJenisResep = [
        343 => 'Penjualan Resep Bebas',
        344 => 'Penjualan Resep Pasien Rumah Sakit',
        // 345 => 'Penjualan Resep Karyawan'
    ];

    /**
     * author: sunarko
     * @todo status rawat inap
     **/
    const STAT_PERIKSA = 441;

    const STATUS_TERIMA = 400;
    const STATUS_BELUM_DITERIMA = 401;
    const VAR_CACHE_GOLONGANUMUR = 'golongan_umur';
    const KELOMPOK_PENUNJANG = 19;

    /**
     * author: iqbal qurahman
     * @todo id kelompok pegawai
     **/
    const KELOMPOK_MEDIS = 1; // Dokter
    const KELOMPOK_KEPERAWATAN = 2; //Perawat
    const KELOMPOK_KEBIDANAN = 3;
    const KELOMPOK_KEFARMASIAN = 4;
    const KELOMPOK_KESEHATAN = 5;
    const KELOMPOK_KESLING = 6;
    const KELOMPOK_NONKESEHATAN = 7;
    const KELOMPOK_KETERAPIAN = 8;
    const KELOMPOK_TEKMEDIK = 9;
    const KELOMPOK_TEKBIOMEDIK = 10;
    const KELOMPOK_KESTRADISIONAL = 11;
    const KELOMPOK_GIZI = 12;

    static $status_lab = [
        473 => 'Periksa',
        474 => 'Ambil Sampel',
        475 => 'Selesai',
        476 => 'Batal',
        477 => 'Belum Periksa',
    ];

    static $status_radiologi = [
        473 => 'Periksa',
        475 => 'Selesai',
        476 => 'Batal',
        477 => 'Belum Periksa',
    ];

    static $asal_rujukan = [
        'order' => 'Order',
        'rujukanRS' => 'Rujukan Rs',
        // 'aps'=>'APS'
    ];

    const SATUAN_BUAH = 58;

    /**
     * @author: Randy Vianda Putra
     * @todo max upload pemeriksaan laboratorium
     * 100 mb to bytes
     **/
    const MAX_UPLOAD_LAB = 104857600;

    const MAX_UPLOAD_RAD = 262144000;

    const MAX_UPLOAD_ENDOSKOPI = 11534336;
    /**
     * @todo const status permintaan konsul
     * @usage Transaksi permintaan and informasi
     * @author Iqbal@docotel.com
     **/
    const STATUS_PERMINTAAN_KONSUL_BATAL = '439';
    const STATUS_PERMINTAAN_KONSUL_TIDAK_SETUJU = '438';
    const STATUS_PERMINTAAN_KONSUL_SETUJU = '437';
    const STATUS_PERMINTAAN_KONSUL_DEFAULT = '9999';

    /**
     * @todo const jenis permintaan konsul
     * @usage
     * @author rizal@docotel.com
     **/
    const JNS_KNSL_1X = 434;
    const JNS_KNSL_RB = 435;
    const JNS_KNSL_AR = 436;
    const JNS_KNSL_AR_RB = 1437;

    const ST_SELESAI_PNNJG = 475;
    const INSTALASI_BEDAH = 12;

    /**
     * @author: Rizal
     * @todo const status periksa lookup
     **/
    const STATUS_PERIKSA_ANTR_POLI = 1;
    const STATUS_PERIKSA_ANTR_PENDFTR = 339;
    const STATUS_PERIKSA_ANTR_KASIR = 430;
    const STATUS_PERIKSA_SDH_PERIKSA = 3;
    const STATUS_PERIKSA_DIPERIKSA = 2;
    const STATUS_PERIKSA_PULANG = 4;
    const STATUS_PERIKSA_BTL_PERIKSA = 402;
    const STATUS_PERIKSA_BTL_KUNJUNGAN = 628;
    const STATUS_PERIKSA_RUJUK_RANAP = 433;
    const STATUS_PERIKSA_BTL_KONSUL = 411;
    const STATUS_PERIKSA_SET_DOKTER = 540;
    const STATUS_PERIKSA_BLM_PERIKSA = 486;
    const STATUS_PERIKSA_MCU_SELESAI = 1148;

    const STATUS_ANTRIAN_POLI = 1;  //Poliklinik
    const STATUS_PERIKSA = 2; //Diperiksa
    const STATUS_PULANG = 4; //Pulang
    const STATUS_BATAL_PERIKSA = 402; //Batal Periksa
    const STATUS_RUJUK_RAWAT_INAP = 433; //Rujuk Rawat Inap

    /**
     * @author ali.padilah@docotel.com
     * @desc const untuk menampung jenis antrian
     **/
    const JA_FAR = 176; // jenis antrian farmasi
    const JA_PDN = 177; // jenis antrian pendaftaran
    const JA_KSR = 178; // jenis antrian kasir
    const JA_PNG = 179; // jenis antrian penunjang
    const JA_POL = 312; // jenis antrian poliklinik

    public static $jenis_operasi = [
        0 => 'Elective',
        1 => 'Emergency',
    ];
    const DIAGNOSA_UTAMA = 2; //kelompok diagnosa, diagnosa utama
    const DIAGNOSA_MASUK = 1; //kelompok diagnosa, diagnosa masuk
    const DIAGNOSA_PENYERTA = 3; //kelompok diagnosa, diagnosa penyerta
    const DIAGNOSA_TERAPI = 6; //kelompok diagnosa, terapi

    const BPJS_KEY = "3fd0e9c73e27d200188f7f3d57e4a60ff656c85c796610366aea2a765c06c701";
    const INACBG_TARIF = "1";
    const INACBG_NIK = "123123123123";
    const INACBG_TARIF_KODE = "AP";
    const CARAMASUK_RJ = "outp";
    const CARAMASUK_RD = "emd";
    const CARAMASUK_RI = "inp";

    /**
     * @todo Konstan kelompok diagnosa
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    const VAR_KELOMPOK_DIAGNOSA_MASUK = 1; // kelompok diagnosa terapi
    const VAR_KELOMPOK_DIAGNOSA_UTAMA = 2; // kelompok diagnosa utama
    const VAR_KELOMPOK_DIAGNOSA_PENYERTA = 3; // kelompok diagnosa penyerta
    const VAR_KELOMPOK_DIAGNOSA_OPERASI = 4; // kelompok diagnosa operasi
    const VAR_KELOMPOK_DIAGNOSA_KELUARGA = 5; // kelompok diagnosa keluarga
    const VAR_KELOMPOK_DIAGNOSA_TERAPI = 6; // kelompok diagnosa terapi
    const VAR_KELOMPOK_DIAGNOSA_AWALAN = 7; // kelompok diagnosa awalan
    const VAR_KELOMPOK_DIAGNOSA_KERJA = 9; // kelompok diagnosa kerja
    const VAR_KELOMPOK_DIAGNOSA_FUNGSI = 10; // kelompok diagnosa kerja

    /**
     * Penjamin Asuransi
     **/

    const BELUM_PENGAJUAN = 552;
    const PROSES_PEMBAYARAN = 553;
    const SUDAH_PEMBAYARAN = 554;
    const BATAL_PEMBAYARAN = 555;
    const SUDAH_LUNAS = 576;

    const KELASPELAYANAN_1 = 1; //kelaspelayanan_id kelas 1
    const KELASPELAYANAN_2 = 2; //kelaspelayanan_id kelas 2
    const KELASPELAYANAN_VIP = 5; //kelaspelayanan_id kelas 5

    public static $statusAlokasi = [
        1 => 'Sudah Alokasi',
        0 => 'Belum Alokasi'
    ];

    public static $statusAsal = [
        'REKOMENDASI' => 'Rekomendasi Order',
        'PO_MANUAL' => 'PO Manual',
        'permintaan_pembelian_barang'  => 'Permintaan Pembelian Barang',
        'PURCHASE REQUEST' => 'Purchase Request'
    ];

    const JENIS_KAMAR_LAKI_LAKI = 340;
    const JENIS_KAMAR_PEREMPUAN = 341;
    const JENIS_KAMAR_FLEKSIBEL = 342;
    const JENIS_KAMAR_CAMPUR = 431;

    const STATUS_BATAL_PO = 575;
    const STATUS_PO_EXPIRED = 686;
    const STATUS_BELUM_TERIMA_PO = 572;
    const STATUS_PO_BELUM_SEMUA_DITERIMA = 573;
    const STATUS_PO_SUDAH_SEMUA_DITERIMA = 574;

    // constants asal rujukan
    const ASAL_RUJUKAN_DS = 1; // datang sendiri
    const ASAL_RUJUKAN_PS = 2; // puskesmas
    const ASAL_RUJUKAN_RS = 3; // rumah sakit
    const ASAL_RUJUKAN_KL = 4; // klinik
    const ASAL_RUJUKAN_DP = 5; // dokter praktek
    const ASAL_RUJUKAN_RI = 6; // rujukan instalasi

    // constants cara bayar
    const CARA_BAYAR_ASU = 2;
    const CARA_BAYAR_BPJS = 6;
    const CARA_BAYAR_PRIVATE = 5;
    const CARA_BAYAR_BPJS_KETENAGAKERJAAN = 19;

    const JENIS_OBAT = 'obat';
    const JENIS_BARANG = 'barang';

    const TIDAK_ASESMEN = 'Tidak Asesmen';
    const BELUM_ASESMEN = 'Belum Asesmen';
    const SUDAH_ASESMEN = 'Sudah Asesmen';

    const PENURUNAN_BB = [
        0 => 'Tidak Terjadi',
        1 => 'Tidak Yakin',
        2 => 'Ya, Turun',
    ];

    // constants jenis identitas
    const JENIS_KTP = 94;
    const JENIS_BPJS = 601;

    //constants default ADL SCORE
    const ADL_SUBACUTE = 12;
    const ADL_CRONIC = 12;

    /**
     * @todo Konstan status pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    const VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES = 564; // status pendaftaran online belum diproses
    const VAR_STATUS_DAFTAR_OL_DISETUJUI = 565; // status pendaftaran online disetujui
    const VAR_STATUS_DAFTAR_OL_DITOLAK = 566; // status pendaftaran online ditolak


    const MODUL = [
        9 => "farmasi",
        4 => "rajal",
        10 => "laboratorium",
        11 => "radiologi"
    ];

    const VAR_SF_1 = 583; // Belum Proses
    const VAR_SF_2 = 584; // Sedang Proses
    const VAR_SF_3 = 585; // Siap Ambil
    const VAR_SF_4 = 586; // Selesai


    public static $statusPenerimaan = [
        0 => 'Belum Verifikasi',
        1 => 'Sudah Verifikasi',
        2 => 'Batal'
    ];

    const EMERGENCY = 'EMERGENCY';
    const NON_EMERGENCY = 'NON EMERGENCY';

    const STATUS_RANAP_BELUM_PERIKSA = 440;
    const STATUS_RANAP_PERIKSA = 441;
    const STATUS_RANAP_BATAL_RAWAT = 453;
    const STATUS_RANAP_PULANG = 487;

    const JUAL_BEBAS = 343;
    const JUAL_RS = 344;
    const JUAL_KARYAWAN = 345;

    const RUANGAN_FISIO = 935; //ruangan fisioterapi

    /**
     * @todo Konstan id kuota antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    const VAR_ID_KUOTA_ANTRIAN_POLIKLINIK = 597; // id kuota antrian poliklinik
    const VAR_ID_KUOTA_ANTRIAN_DOKTER = 598; // id kuota antrian dokter
    const VAR_ID_TANPA_KUOTA = 599; // id tanpa antrian kuota

    //const return identity user (main allow controller)
    const STS_NOTLOGIN = 1;
    const STS_LOGIN = 2;
    const STS_LOGINANDMETHOD = 3;

    const BELUM_DIKIRIM = 398;
    const SUDAH_DIKIRIM = 399;
    const BATAL_PESAN = 602;

    public static $statusPesan = [
        self::BELUM_DIKIRIM => 'Belum Dikirim',
        self::SUDAH_DIKIRIM => 'Sudah Dikirim',
        self::BATAL_PESAN => 'Batal'
    ];

    const GOL_DARAH_TIDAKTAHU = 600;
    const R_GuDANG_BARANG = 55;

    /**
     * @todo Konstan kondisi bayi
     * @author Ardi
     **/
    const KONDISI_BAYI_NORMAL = 85; // Kondisi Bayi: Normal
    const KONDISI_BAYI_CACAT = 86; // Kondisi Bayi: Cacat
    const KONDISI_BAYI_HIPOTERMI = 87; // Kondisi Bayi: Hipotermi

    const VAR_PENYAKIT_PERSALINAN = 5; //variable jenis kasus penyakit persalinan
    const VAR_RUANGAN_JNZ = 38; //variable id ruangan jenazah

    const JABATAN_SUPERVISER_KA = 3;
    const RUANGAN_GUDANG = 25;
    const PERUBAHAN_MANUAL = 626;
    const RUANGAN_PENGADAAN = 57;

    // status lunas
    const STAT_BAYAR_LUNAS = 348;
    const STAT_BAYAR_BLM_LUNAS = 349;

    /**
     * @todo Konstan jenis pelayanan
     * @author Budi
     **/
    // jenis pelayanan untuk kebutuhan bpjs
    const JNS_PLY_RAJAL = 2;
    const JNS_PLY_RANAP = 1;

    // group carabayar
    const GROUP_UMUM = 417;
    const GROUP_BPJS = 418;
    const GROUP_JAMINAN = 419;
    const GROUP_ASURANSI_UCUP = 420;
    const JENIS_TARIF_INACBG = ['AP' => 'TARIF RS KELAS A PEMERINTAH'];
    const DEFAULT_KELAS_INACBG = 'KELAS A';

    const GOUP_ALKES = 620;
    const GOUP_ALKES_PERAWAT_BMHP = 'groupjenisobat_perawat_bmhp';
    const GOUP_ALKES_PERAWAT_RESEP = 'groupjenisobat_perawat_resep';

    const KLS_PLYN_RANAP_BPJS = 1;

    const STATUS_DISTRIBUSI = [
        'Belum Dikirim' => 'Belum Dikirim',
        'Sudah Dikirim' => 'Sudah Dikirim',
        'Diterima' => 'Diterima'
    ];

    const CARA_KELUAR_PULANG = 1;
    const CARA_KELUAR_MENINGGAL = 4;

    const ADJUSTMENT_MASUK = 0;
    const ADJUSTMENT_KELUAR = 1;
    const NOTIFICATION_RM = '/media/sounds/NotificationRM.mp3';

    const STATUS_VERIFIKASIOBAT_BELUM = 666;
    const STATUS_VERIFIKASIOBAT_SUDAH = 667;

    // metode bayar
    const METODE_NON_TUNAI = 28;

    // status verifikasi pasien bpjs
    const STATUS_VERIFIKASI_BPJS_BLM = 549;
    const STATUS_VERIFIKASI_BPJS_SDH = 550;
    const STATUS_VERIFIKASI_BPJS_PRS = 556;
    const STATUS_VERIFIKASI_BPJS_FNL = 551;

    // List Tarif Inacbg
    const LIST_TARIF_INACBG = [
        'AP' => 'TARIF RS KELAS A PEMERINTAH',
        'AS' => 'TARIF RS KELAS A SWASTA',
        'BP' => 'TARIF RS KELAS B PEMERINTAH',
        'BS' => 'TARIF RS KELAS B SWASTA',
        'CP' => 'TARIF RS KELAS C PEMERINTAH',
        'CS' => 'TARIF RS KELAS C SWASTA',
        'DP' => 'TARIF RS KELAS D PEMERINTAH',
        'DS' => 'TARIF RS KELAS D SWASTA',
        'RSCM' => 'TARIF RSUPN CIPTO MANGUNKUSUMO',
        'RSJP' => 'TARIF RSJPD HARAPAN KITA',
        'RSD' => 'TARIF RS KANKER DHARMAIS',
        'RSAB' => 'TARIF RSAB HARAPAN KITA'
    ];

    const DEFAULT_KODE_INACBG = 'AP';

    // status konsul poli
    const STATUS_KONSUL_DIJAWAB = 671;
    const STATUS_KONSUL_BLM_DIJAWAB = 670;

    // Depo Tujuan
    const DEPO_APOTEK_RJ = 6;
    const DEPO_APOTEK_RI = 12;
    const DEPO_APOTEK_RD = 13;
    const DEPO_UMUM = 48;
    const DEPO_APOTEK_RD_BARU = 49;
    const DEPO_APOTEK_MELATI = 50;

    const TIM_DOKTER_BEDAH = 508;
    const TIM_DOKTER_ANASTESI = 509;
    const TIM_ASS_ANASTESI1 = 511;
    const TIM_ASS_ANASTESI2 = 512;
    const TIM_ASS_BEDAH1 = 513;
    const TIM_ASS_BEDAH2 = 514;


    /* Jenis rencana kontrol*/
    const KONSUL_POLI = 672;
    const S_KONSUL_POLI = 'Konsul';
    const RENCANA_KONTROL = 673;
    const S_RENCANA_KONTROL = 'Kontrol';

    // Lookup ID Jenis Kelamin
    const LOOKUP_LAKI = 15;
    const LOOKUP_PEREMPUAN = 16;
    const STATUS_RESEPTUR_BELUM_DIPROSES = 346;
    const STATUS_RESEPTUR_SUDAH_DIPROSES = 347;
    const STATUS_RESEPTUR_BATAL = 432;
    const STATUS_RESEPTUR_DISERAHKAN = 660;
    const AMBULANCE_ORDER_NOT_PROCESS = 606;
    const AMBULANCE_ORDER_PROCESS = 607;

    //const penjamin
    const VAR_P_Perseorangan = 1; //perseorangan

    //const status penunjang
    const VAR_S_PEN_D = 471; //disetujui
    const VAR_S_PEN_B = 472; //batal
    const VAR_S_PEN_AS = 474; //ambil sample
    const VAR_S_PEN_BP = 477; //belum periksa

    //const status PR
    const VAR_SUDAH_PO = 713;
    const VAR_BELUM_PO = 712;
    const VAR_PO_SEBAGIAN = 719;
    const VAR_CANCEL_PR = 720;
    const VAR_APPROVED = 1056;
    const VAR_BELUM_APPROVED = 1057;

    // ** Jenis pendaftaran */
    const J_P_L = 726;
    const J_P_O = 727;

    const RUANGAN_THT = 21;
    const RUANGAN_MATA = 4;

    // Anestesi Regional
    const ANES_REG_OTHER = 134;

    // status pendaftaran pasien
    const VAR_PAS_B = "310"; //pasien baru
    const VAR_PAS_L = "311"; //pasien lama

    /**
     * @todo const penunjang sudah operasi
     * @author Budi
     **/
    const VAR_P_SdhO = "483";

    //** Tipe Laporan Rm*/
    const L_T_SOAP_DOKTER = 729;
    const L_T_RESUME_DOKTER = 730;

    //untuk memunculkan status kamar selain isi dan kosong
    const STATUS_KAMAR_NON_ISI = [5, 6, 9, 10, 12, 13]; //booking, ready, vacant, ready, cek kelengkapan, service
    const STATUS_SELESAI_RAD = 475;

    // Status Lab Penunjang
    const LAB_ST_PEN_BELUMPERIKSA = '477';
    const LAB_ST_PEN_AMBILSAMPLE = '474';
    const LAB_ST_PEN_PERIKSA = '473';
    const LAB_ST_PEN_SELESAI = '475';
    const LAB_ST_PEN_BATAL = '476';

    // Jenis Kegiatan Keperawatan
    const LIST_TINDAKAN_KEPERAWATAN = '1038';
    const LIST_TINDAKAN_KEBIDAHANAN = '1039';

    // status worklist
    const WORKLIST_BELUM_DISIAPKAN  = 674;
    const WORKLIST_DISIAPKAN        = 675;
    const WORKLIST_DITELAAH         = 676;
    const WORKLIST_QC               = 677;
    const WORKLIST_SIAP_DISERAHKAN  = 678;
    // kelompok tindakan cathlab
    const KEL_TIND_CATHLAB = 34;
    // kelompok tindakan visite
    const KEL_TIND_VISITE = 32;

    const VAR_K_S = 'var_cache_konfig_system';
    const PENJADWALAN_DAN_PENDAFTARAN = 10;

    const FORM_ASESMEN_MEDIS = 'asesmen_medis';
    const FORM_ASESMEN_AWAL_KEP = 'asesmen_keperawatan';
    const FORM_RESUME_MEDIS = 'resume_medis';
    const TYPE_RI = 'ranap';
    const TYPE_RJ = 'rj';
    const TYPE_IGD = 'igd';
    const RI_LAP_TERAPI = 'ranaplaporanterapi';
    const RJ_LAP_TERAPI = 'rajallaporanterapi';
    const RD_LAP_TERAPI = 'igdlaporanterapi';

    const MAX_UPLOAD_DOKUMEN = 20971520;
    const TIM_OPERASI_DOKTER_BEDAH = 508;

    const CACHE_LOOKUP_TRANSAKSI = 'var_cache_lookup_transaksi';

    /**
     *
     * @author : Dede Herdiana
     * LOOKUP by KODE, get lookup_type = jenis_layanan
     * MAS_KM_KELOMPOK = Jenis layanan kelompok tindakan
     * MAS_KM_TINDAKAN = Jenis layanan tindakan
     * MAS_KM_PAKET = Jenis layanan paket
     * MAS_KM_OBAT = Jenis layanan obat
     *
     **/
    const TD_KELOMPOK = '1025';
    const TD_TINDAKAN = '1026';
    const TD_PAKET = '1027';
    const TD_OBAT = '1028';
    const TD_KELAS = '1033';

    const BMHP_BELUM_VERIFIKASI = 679;
    const BMHP_SUDAH_VERIFIKASI = 680;

    const MONITORING_RM_REQUEST = 1020;
    const MONITORING_RM_DELIVERY = 1021;
    const MONITORING_RM_ISSUE = 1022;
    const MONITORING_RM_REMIND = 1025;

    //status antrian jkn
    const STATUS_TUNGGU_ADMISI = '2';
    const TIME_RESET_SUGGEST_SOAP = 'reset_suggestion_soap';

    const BATAL_PESAN_PEMESAN = 'pemesan';
    const BATAL_PESAN_TUJUAN = 'tujuan';

    const CACHE_KELAS_PELAYANAN = 'get-kelas-pelayanan';
    const OPEN = 'OPEN';
    const CLOSE = 'CLOSE';
    const CANCEL = 'BATAL';

    // Lookup Status Program Fisio
    const STATUS_PROGRAM_FISIO_CLOSE = 1170;
    const STATUS_PROGRAM_FISIO_OPEN = 1171;
    const STATUS_PROGRAM_FISIO_CANCEL = 1172;

    //Jenis Recana Kontrol/Inap
    const STS_RECANA_KONTROL = 1125;
    const STS_RENCAN_INAP = 1126;
    const FILTER_BY_TGL_RENCANA = 2;
    const FILTER_NUMBER = 'number';
    const FILTER_CHAR = 'char';

    const TUJUAN_KUNJ_BPJS_TRUE = 1;
    const TUJUAN_KUNJ_BPJS_FALSE = 0;

    // Lookup Status Kunjungan Fisioterapi
    const STATUS_KUNJUNGAN_FISIO_BELUM_REALISASI = 1206;
    const STATUS_KUNJUNGAN_FISIO_REALISASI = 1207;
    const STATUS_KUNJUNGAN_FISIO_KETIDAKHADIRAN = 1208;
    const STATUS_KUNJUNGAN_FISIO_DROP_OUT = 1209;

    const STATUS_LAB_BELUM_DIPERIKSA = 'BELUM DIPERIKSA';
    const STATUS_LAB_PERIKSA = 'PERIKSA';
    const STATUS_LAB_SELESAI = 'SELESAI';

    const PENJUALAN_BMHP = [
        'BMHP' => 'Penjualan BMHP',
    ];

    const STATUS_BMHP = [
        111 => 'Batal',
    ];

    const ASSESMEN_PELAYANAN_BPJS_TUJUAN_KONTROL = 5;

    // filter worklist
    const WORKLIST_KONFIG_FILTER_RUANGAN = 'konfig_worklist_filter_ruangan';
    const WORKLIST_KONFIG_URUTAN_PERIKSA = 'konfig_worklist_filter_urutan_periksa';

    // konfig stok obat alkes
    const KONFIG_VALIDASI_STOK_OBAT_ALKES = 'konfig_validasi_stok_obat_alkes';

    // buta warna parsial
    const BUTA_WARNA_PARSIAL = 'buta_warna_parsial';

    /* Mapping kelompokdiagnosa */
    const MAP_DIAGNOSA_UTAMA = 1; // kelompok diagnosa utama
    const MAP_DIAGNOSA_TAMBAHAN = 2; // kelompok diagnosa tambahan
    const MAP_DIAGNOSA_OPERTINDAKAN = 3; // kelompok diagnosa tindakan / operasi
    const MAP_DIAGNOSA_LUAR = 4; // kelompok diagnosa sebab luar
    const MAP_DIAGNOSA_MORFOLOGI = 5; // kelompok diagnosa morfologi

    const LIST_JAMINAN = [
        '703' => 'JKN',
        '716' => 'JAMINAN COVID-19',
    ];

    const LIST_STATUS_COVID = [
        'ODP' => 'ODP',
        'PDP' => 'PDP',
        'Terkonfirmasi' => 'Terkonfirmasi',
    ];

    const LIST_IDENTITAS_PASIEN = [
        '1' => 'NIK',
        '2' => 'KITAS',
        '3' => 'Paspor',
        '4' => 'Kartu JKN',
        '5' => 'Lainnya',
    ];

    const PAYOR_ID_COVID = 71;
    const PAYOR_ID_KIPI = 72;
    const PAYOR_ID_BAYI_BARU_LAHIR = 73;
    const PAYOR_ID_PERPANJANGAN_MASA_RAWAT = 74;
    const PAYOR_ID_COINSIDENSE = 75;
    const PAYOR_ID_JAMPERSAL = 76;
    const PAYOR_ID_JKN = 3;

    static $icd_utama = ['ICD X', 10];
    static $icd_sekunder = ['ICD IX', 9];

    //Approval SEP BPJS
    const INS_APPR_BPJS = [
        'RI' => 1,
        'RJ' => 2,
    ];
    const JNS_APPR_BPJS = [
        'Tgl.SEP_Backdate' => 1,
        'Fingerprint' => 2,
    ];

    //pencarian rujukan BPJS
    const PENCARIAN_NIK = 2;
    const ASAL_RUJUKAN_FAKSES_1 = 1;

    //status penunjang
    const BELUM_SETUJU = 470;
    const DISETUJUI = 471;
    const BATAL_APPROVE = 472;
    const BLM_PERIKSA = 477;

    //cara bayar
    const CARA_BAYAR_UMUM = 5;
    // Jenis Nutrional Risk Score
    const KATEGORI_NRS = [
            0 => 'dewasa',
            1 => 'anak'
        ];

    const JNS_RSVRVS_OL = 621; // jenis reservasi - pendaftaran online
    const JNS_RSVRVS_R = 622; // jenis reservasi - reservasi
    const JNS_RESERVASI_ESIANTRI = 1310; // jenis reservasi - esiantri
    const JNS_RESERVASI_MQARE = 1198; // jenis reservasi - mqare

    public static $mapp_kel_diagnosa_penjaminasuransi = [
        'Utama' => 2,
        'Tambahan' => 3,
        'Tindakan/Operasi' => 6,
    ];

    public static $labelDiagnosaINACBSEklaim = [
        'Utama' => 'Diagnosa Utama (ICD 10)',
        'Tambahan' => 'Diagnosa Penyerta (ICD 10)',
        'Tindakan/Operasi' => 'Tindakan (ICD 9)',
    ];

    const SKALA_NYERI = [
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
    ];

    const LAPORAN_PEMBELIAN = 1;
    const LAPORAN_PENGELUARAN_KASIR = 2;
    const LAPORAN_KAS_HARIAN = 3;
    const LAPORAN_PENERIMAAN_HARIAN = 4;
    const REKAP_LAPORAN_KASIR = 5;

    const LIST_LAPORAN_KASIR = [
        self::LAPORAN_PEMBELIAN => 'Laporan Pembelian',
        self::LAPORAN_PENGELUARAN_KASIR => 'Laporan penerimaan dan pengeluaran kasir',
        self::LAPORAN_KAS_HARIAN => 'Laporan kas Harian',
        self::LAPORAN_PENERIMAAN_HARIAN => 'Laporan Penerimaan Harian',
        self::REKAP_LAPORAN_KASIR => 'Rekap Laporan Kasir',
    ];

    const EXPIRED_CACHE = 60 * 60 * 24; // expired in 1 day

    /* KATEGORI RESEP RAWAT INAP */
    const KATEGORI_RESEP_UDD = 2115;
    const KATEGORI_RESEP_TERAPI_BARU = 2116;
    const KATEGORI_RESEP_OBAT_PULANG = 2117;
    /* RETUR*/
    const RETUR_BELUM_VERIFIKASI = 2118;
    const RETUR_VERIFIKASI = 2119;
    const BATAL_RETUR = 2120;

    /**
     * Status persetujuan otoritas penjamin
     */
    const STATUS_APPROVED = 1324;
    const STATUS_REJECT = 1325;
    const STATUS_PENDING = 1323;
    
    /** Antrian BPJS */
    const ANTRIAN_BPJS = 2121;
    const IDENTITAS_NIK = 0;
    const IDENTITAS_KARTU_BPJS = 1;
    const JENIS_KUNJUNGAN = [
        1 => 'Rujukan FKTP',
        2 => 'Rujukan Internal',
        3 => 'Kontrol',
        4 => 'Rujukan Antar RS',
    ];
    const PASIEN_JKN = 1100;
    const RUANGAN_PENDAFTARAN_RAJAL = 5;
    const BPJS_MANDIRI = 21;
    const RUJUKAN_FKT = 1096;
    const JENIS_KUNJUNGAN_KONTROL = 1098;

    /* Unduh Dokumen */
    const ON_PROGRES_UNDUH_DOKUMEN = 2122;
    const SELESAI_UNDUH_DOKUMN = 2123;
    const BELUM_UNDUH_DOKUMEN = 2172;
    // id keterangan tempat tidur yg ditampilkan
    const KET_VACANT = 9;
    const KET_NEED_TO_MAINTENANCE = 13;
    const KET_NEED_TO_BE_CLEAN = 16;

    // satu sehat type 
    const PRACTITIONER = 'Practitioner';
    const ORGANIZATION = 'Organization';
    const LOCATION = 'Location';
    const LOCATIONUPDATE = 'LocationUpdate';
    const ENCOUNTER = 'Encounter';
    const ENCOUNTERINPROGRESS = 'EncounterInProgress';
    const ENCOUNTERFINISH = 'EncounterFinish';

    // list satu sehat type
    const LIST_SATU_SEHAT_TYPE = [
        self::PRACTITIONER => 'Practitioner',
        self::ORGANIZATION => 'Organization',
        self::LOCATION => 'Location',
        self::LOCATIONUPDATE => 'LocationUpdate',
        self::ENCOUNTER => 'Encounter',
        self::ENCOUNTERINPROGRESS => 'EncounterInProgress',
        self::ENCOUNTERFINISH => 'EncounterFinish',
    ];

    // pemesanan produksi Obat
    const RUANGAN_GUDANG_FARMASI = 25;
    const BELUM_VERIFIKASI_PESANAN = 2183;
    const SUDAH_VERIFIKASI_PESANAN = 2184;
    const BATAL_PEMESANAN = 2185;

    // produksi Obat
    const PRODUKSI = 2186;
    const DEFINE_MATERIAL = 2187;
    const BATAL_PRODUKSI = 2188;

    // satu sehat status integrasi
    const SATU_SEHAT_STATUS_DIPROSES = 'Diproses';
    const SATU_SEHAT_STATUS_SELESAI = 'Selesai';
    const SATU_SEHAT_STATUS_GAGAL = 'Gagal';

    // list satu sehat status integrasi
    const LIST_SATU_SEHAT_STATUS_INTEGRASI = [
        self::SATU_SEHAT_STATUS_DIPROSES => 'Diproses',
        self::SATU_SEHAT_STATUS_SELESAI => 'Selesai',
        self::SATU_SEHAT_STATUS_GAGAL => 'Gagal',
    ];

    
    const RESERVASI_ESIANTRI = 1310;

    // lookup transaksi ruangan tujuan mutasi obat alkes expired
    const LT_GUDANG_FARMASI = 'gudang_farmasi';
    const LT_GUDANG_PEMUSNAHAN = 'gudang_pemusnahan';

    const POLI_THT = 'ruangan_spesialis_tht';
    const POLI_MATA = 'ruangan_spesialis_mata';
    const POLI_OBGYN = 'ruangan_spesialis_obgyn';
    const POLI_GIGI = 'ruangan_spesialis_gigi';

    const KODE_FORM_INSPEKSIUS = 'inspeksius';
    const KODE_FORM_IMUNOLOOGI = 'imunologi';
    const KODE_FORM_GERIATRI = 'geriatri';
    const KODE_FORM_KEKERASAN = 'kekerasan';
    const KODE_FORM_TERMINAL = 'terminal';
    const KODE_FORM_KRONIK = 'kronik';
    const KODE_FORM_NEONATUS = 'neonatus';

    const REASON_REVOKE_TILAKA_RESIGN = "Resign";
    const REASON_REVOKE_TILAKA_PHK = "PHK";
    const REASON_REVOKE_TILAKA_HABIS_KONTRAK = "Habis Kontrak";
    const REASON_REVOKE_TILAKA_MUTASI = "Mutasi";
    const REASON_REVOKE_TILAKA_PEMINDAHAN_DEPARTEMEN = "Pemindahan Departemen";
    const REASON_REVOKE_TILAKA_PINDAH_DIVISI = "Pindah Divisi";
    const REASON_REVOKE_TILAKA_INTERNAL_FRAUD = "Internal Fraud";
    const REASON_REVOKE_TILAKA_PENUTUPAN_HAK_AKSES = "Penutupan Hak Akses";
    const REASON_REVOKE_TILAKA_PELANGGARAN_HUKUM_DARI_USER = "Pelanggaran Hukum dari User";
    const REASON_REVOKE_TILAKA_PERANGKAT_HILANG_PERANGKAT_DICURI = "Perangkat Hilang, Perangkat Dicuri";
        
    const KODE_FORM_KECANDUAN = 'kecanduan';
    const KODE_FORM_PSIKIATRIS = 'psikiatris';
    const KODE_FORM_ANAK = 'anak';
    const KODE_FORM_BEDAH = 'bedah';
    const KODE_FORM_LUKA_BAKAR = 'luka-bakar';
    const KODE_FORM_GINEKOLOGI = 'ginekologi';
    const KODE_FORM_KEBIDANAN = 'kebidanan';
    const KODE_FORM_MATA = 'mata';
    const KODE_FORM_KULIT = 'kulit-kelamin';
    const KODE_FORM_SYARAF = 'syaraf';
    const PJ_DIRI_SENDIRI = 245;
    const KODE_FORM_MEDIS_BEDAH = 'medis-bedah';
    const KODE_FORM_KESEHATAN_ANAK = 'kesehatan-anak';
    const KODE_FORM_PENYAKIT_DALAM = 'penyakit-dalam';

    const JENIS_OBAT_BPJS_KRONIS = 4031;
    const JENIS_OBAT_BPJS_KEMO = 4033;
    const JENIS_OBAT_BPJS_PRB = 4032;
    const SUMBER_TTV_MONITORING = 'Monitoring TTV';
    const SUMBER_TTV_MONITORING_ID = '2253';
    const SUMBER_ASKEP_MONITORING_ID = '2255';
    const SUMBER_ASMED_MONITORING_ID = '2254';

    const JENIS_EWS_KEBIDANAN = 2256;
    const JENIS_EWS_ANAK = 2258;
    const JENIS_EWS_DEWASA = 2257;
    const JENIS_EWS_IBU_HAMIL = 2259;
    const SUMBER_EWS_MONITORING_ID = '2264';
    const SUMBER_SBAR_MONITORING_ID = '2266';

    // LOOKUP VERIFIKASI BANTARAN
    const BELUM_VERIFIKASI_BANTARAN = 4035;
    const SUDAH_VERIFIKASI_BANTARAN = 4036;
    const TOLAK_VERFIKASI_BANTARAN = 4037;

    // LOOKUP STATUS PELAYANAN BANTARAN
    const DALAM_PELAYANAN_BANTARAN = 4038;
    const SELESAI_PELAYANAN_BANTARAN = 4039;
    const MENUNGGU_DIDAFTARKAN_BANTARAN = 4040;
}
