<?php

use yii\db\Migration;

/**
 * Class m210512_081928_oddo_20210512_penyesuianvieworderan
 */
class m210512_081928_oddo_20210512_penyesuianvieworderan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."rencanaoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rencanaoperasi_v\" AS  SELECT rencanaoperasi_t.rencanaoperasi_id,
    rencanaoperasi_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.pendaftaran_id,
    rencanaoperasi_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.jeniskelamin,
    pasien_m.tanggal_lahir,
    pasien_m.photopasien,
    pendaftaran_t.umur,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_rj.kelaspelayanan_nama
            ELSE kelas_ri.kelaspelayanan_nama
        END AS kelaspelayanan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_rj.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama,
    rencanaoperasi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    rencanaoperasi_t.tgl_permintaan,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS status,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS status_operasi,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dr_pemeriksa.nama_pegawai
            ELSE dr_pemeriksa_admisi.nama_pegawai
        END AS dok_pemeriksa,
    inpostoperasi_t.mulai_operasi,
    inpostoperasi_t.selesai_operasi,
    app.nama_pegawai AS pegawai_approve,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_approve,
    jenis_operasi.kegiatanoperasi_nama,
    pasienmasukpenunjang_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar
   FROM rencanaoperasi_t
     JOIN pasienkirimkeunitlain_t ON rencanaoperasi_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON rencanaoperasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON rencanaoperasi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN kelaspelayanan_m kelas_rj ON pendaftaran_t.kelaspelayanan_id = kelas_rj.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kelas_ri ON pasienadmisi_t.kelaspelayanan_id = kelas_ri.kelaspelayanan_id
     LEFT JOIN penjamin_m penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
     LEFT JOIN carabayar_m carabayar_rj ON pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id
     LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
     LEFT JOIN carabayar_m carabayar_ri ON penjamin_ri.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     LEFT JOIN pegawai_m dr_pemeriksa ON pendaftaran_t.pegawai_id = dr_pemeriksa.pegawai_id
     LEFT JOIN pegawai_m dr_pemeriksa_admisi ON pasienadmisi_t.pegawai_id = dr_pemeriksa_admisi.pegawai_id
     LEFT JOIN inpostoperasi_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m app ON loginpemakai_k.pegawai_id = app.pegawai_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            string_agg(kegiatanoperasi_m.kegiatanoperasi_nama::text, ','::text) AS kegiatanoperasi_nama
           FROM permintaankepenunjang_t
             JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN operasi_m ON permintaankepenunjang_t.operasi_id = operasi_m.operasi_id
             JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
             JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) jenis_operasi ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = jenis_operasi.pasienkirimkeunitlain_id;");

        $this->execute('ALTER TABLE "public"."rencanaoperasi_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infoorderanbedah_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoorderanbedah_v\" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_periksa
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_periksa
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12;");

        $this->execute('ALTER TABLE "public"."infoorderanbedah_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienlab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienlab_v\" AS  SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision)
            WHEN 0 THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pasienadmisi_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision)
            WHEN 0 THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    rujukan_t.no_rujukan,
    rujukan_t.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    rujukan_t.rujukandari_id AS ruanganasal_id,
    perujuk_m.namaperujuk AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision)
            WHEN 0 THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    'APS'::character varying AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision)
            WHEN 0 THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
  WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND
        CASE
            WHEN pendaftaran_t.carabayar_id <> 2 THEN pasienmasukpenunjang_t.is_bayar = true
            ELSE pasienmasukpenunjang_t.is_deleted IS FALSE
        END;");

        $this->execute('ALTER TABLE "public"."infopasienlab_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienrad_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienrad_v\" AS  SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pasien_m.no_telepon_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
    concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m dokter_perujuk ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
UNION ALL
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pasien_m.no_telepon_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
    concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    rujukan_t.no_rujukan,
    rujukan_t.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    rujukan_t.rujukandari_id AS ruanganasal_id,
    perujuk_m.namaperujuk AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pasien_m.no_telepon_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
    concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
  WHERE pendaftaran_t.instalasi_id = 5
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    'APS'::character varying AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pasien_m.no_telepon_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
    concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
  WHERE ruang_penunjang.instalasi_id = 5 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.is_bayar = true;");

        $this->execute('ALTER TABLE "public"."infopasienrad_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    pasienmasukpenunjang_t.no_antrian,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.carabayar_id
            ELSE penjamin_ri.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_rj.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_rj.kelaspelayanan_nama
            ELSE kelas_ri.kelaspelayanan_nama
        END AS kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienmasukpenunjang_t.catatan AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    cppt_t.a_diag_utama ->> 'text'::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN penjamin_m penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
     LEFT JOIN carabayar_m carabayar_rj ON penjamin_rj.carabayar_id = carabayar_rj.carabayar_id
     LEFT JOIN kelaspelayanan_m kelas_rj ON pendaftaran_t.kelaspelayanan_id = kelas_rj.kelaspelayanan_id
     LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
     LEFT JOIN carabayar_m carabayar_ri ON penjamin_ri.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN kelaspelayanan_m kelas_ri ON pasienadmisi_t.kelaspelayanan_id = kelas_ri.kelaspelayanan_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     LEFT JOIN ( SELECT DISTINCT ON (cppt_t_1.pendaftaran_id, cppt_t_1.pegawai_id) cppt_t_1.pendaftaran_id,
            cppt_t_1.pegawai_id,
            cppt_t_1.a_diag_utama
           FROM cppt_t cppt_t_1
          WHERE cppt_t_1.is_deleted = false AND cppt_t_1.is_active = true AND cppt_t_1.is_instruksi_pulang = false) cppt_t ON pasienmasukpenunjang_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m_1.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m pegawai_m_1 ON loginpemakai_k.pegawai_id = pegawai_m_1.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id AS penjamin_id,
    carabayar_m.carabayar_nama AS penjamin_nama,
    pendaftaran_t.penjamin_id AS carabayar_id,
    penjamin_m.penjamin_nama AS carabayar_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_perujuk ON pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id
     JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN instalasi_m instalasi_asal ON pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN rencanaoperasi_t ON pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_penunjang ON pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.instalasi_id = 12 AND pasienmasukpenunjang_t.is_bayar = true
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id AS penjamin_id,
    carabayar_m.carabayar_nama AS penjamin_nama,
    pendaftaran_t.penjamin_id AS carabayar_id,
    penjamin_m.penjamin_nama AS carabayar_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_perujuk ON pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id
     JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN instalasi_m instalasi_asal ON pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ruangan_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id
     LEFT JOIN rencanaoperasi_t ON pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_penunjang ON pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienmasukpenunjang_t.status_periksa IS NOT NULL AND ruangan_penunjang.instalasi_id = 12 AND pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL AND pendaftaran_t.instalasi_id <> 12;");

        $this->execute('ALTER TABLE "public"."infopasienoperasi_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infoorderanrad_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoorderanrad_v\" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasien_m.alamat_pasien,
    NULL::character varying AS kode_pos,
    pasien_m.no_telepon_pasien,
    ruangan_m.ruangan_singkatan AS kode_ruangan,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa,
    pemeriksaan.nama_pemeriksaan,
    penjamin_m.penjamin_kode AS carabayar_kode,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pembayaran.no_pembayaran,
    pegawai_m.nomorindukpegawai,
    pasien_m.jeniskelamin AS jenis_kelamin_id,
        CASE
            WHEN pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NULL THEN 'Belum Bayar'::text
            ELSE COALESCE(tindakanpelayanan.status_bayar, 'Batal Bayar'::text)
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(total_pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT x.pasienmasukpenunjang_id,
            x.status_bayar
           FROM ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        CASE
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL THEN 'Sudah Bayar'::text
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                            ELSE 'Batal Bayar'::text
                        END AS status_bayar
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.pasienmasukpenunjang_id) x
          GROUP BY x.pasienmasukpenunjang_id, x.status_bayar) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            string_agg(DISTINCT daftartindakan_m.daftartindakan_nama::text, ', '::text) AS nama_pemeriksaan
           FROM permintaankepenunjang_t
             JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            pembayaranpelayanan_t.no_pembayaran
           FROM tindakansudahbayar_t
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN tindakanpelayanan_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, pembayaranpelayanan_t.no_pembayaran) pembayaran ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pembayaran.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) total_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = total_pemeriksaan.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasien_m.alamat_pasien,
    NULL::character varying AS kode_pos,
    pasien_m.no_telepon_pasien,
    ruangan_m.ruangan_singkatan AS kode_ruangan,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa,
    pemeriksaan.nama_pemeriksaan,
    penjamin_m.penjamin_kode AS carabayar_kode,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pembayaran.no_pembayaran,
    pegawai_m.nomorindukpegawai,
    pasien_m.jeniskelamin AS jenis_kelamin_id,
        CASE
            WHEN pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NULL THEN 'Belum Bayar'::text
            ELSE COALESCE(tindakanpelayanan.status_bayar, 'Batal Bayar'::text)
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(total_pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT x.pasienmasukpenunjang_id,
            x.status_bayar
           FROM ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        CASE
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL THEN 'Sudah Bayar'::text
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                            ELSE 'Batal Bayar'::text
                        END AS status_bayar
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.pasienmasukpenunjang_id) x
          GROUP BY x.pasienmasukpenunjang_id, x.status_bayar) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            string_agg(DISTINCT daftartindakan_m.daftartindakan_nama::text, ', '::text) AS nama_pemeriksaan
           FROM permintaankepenunjang_t
             JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            pembayaranpelayanan_t.no_pembayaran
           FROM tindakansudahbayar_t
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN tindakanpelayanan_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, pembayaranpelayanan_t.no_pembayaran) pembayaran ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pembayaran.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) total_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = total_pemeriksaan.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5;");

        $this->execute('ALTER TABLE "public"."infoorderanrad_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infoorderanlab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoorderanlab_v\" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    COALESCE(pasienmasukpenunjang_t.is_bayar, false) AS is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
            WHEN true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
    COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, 0::bigint) AS jml_pemeriksaan_approve,
    COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision) AS jumlah_tagihan,
    COALESCE(tindakan_bayar.jumlah_bayar, 0::double precision) AS jumlah_bayar
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan_approve
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE AND permintaankepenunjang_t.is_approve IS TRUE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    COALESCE(pasienmasukpenunjang_t.is_bayar, false) AS is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
            WHEN true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
    COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, 0::bigint) AS jml_pemeriksaan_approve,
    COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision) AS jumlah_tagihan,
    COALESCE(tindakan_bayar.jumlah_bayar, 0::double precision) AS jumlah_bayar
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan_approve
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE AND permintaankepenunjang_t.is_approve IS TRUE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4;");

        $this->execute('ALTER TABLE "public"."infoorderanlab_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienradiologi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienradiologi_v\" AS  SELECT header.tipe_pasien,
    header.pendaftaran_id,
    header.pasienmasukpenunjang_id,
    header.pasienkirimkeunitlain_id,
    header.tglmasukpenunjang,
    header.no_pendaftaran,
    header.no_masukpenunjang,
    header.no_rekam_medik,
    header.nama_pasien,
        CASE header.is_mcu
            WHEN true THEN header.pegawai_id
            ELSE detail.dokter_id_detail
        END AS pegawai_id,
        CASE header.is_mcu
            WHEN true THEN header.dokter_penunjang
            ELSE detail.dokter_nama_detail
        END AS dokter_penunjang,
    header.no_rujukan,
    header.asalrujukan_id,
    header.asalrujukan_nama,
    header.ruanganasal_id,
    header.ruangan_nama,
    header.status_periksa,
    header.no_antrian,
    header.carabayar_id,
    header.carabayar_nama,
    header.penjamin_id,
    header.penjamin_nama,
    header.kelaspelayanan_id,
    header.kelaspelayanan_nama,
    header.umur,
    header.jeniskelamin,
    header.j_kelamin,
    header.tanggal_lahir,
    header.kuning,
    header.merah,
    header.ungu,
    header.coklat,
    header.tgl_rujukan,
    header.pasien_id,
    header.pasienadmisi_id,
    header.ruangan_id,
    header.is_bayar,
    header.status_penunjang,
    header.catatan_dokterpengirim,
    header.no_telepon_pasien,
    header.dokter_perujuk_id,
    header.dokter_perujuk_nama,
        CASE header.is_mcu
            WHEN true THEN header.nama_dokter_penunjang::character varying
            ELSE detail.dokter_nama_detail
        END AS nama_dokter_penunjang,
    header.unit_asal,
    header.nama_diagnosa,
    header.is_mcu,
    detail.jenispemeriksaanrad_nama,
    detail.tipepaket_nama,
    detail.detail_2,
    detail.daftartindakan_id,
    detail.daftartindakan_nama,
        CASE
            WHEN hasil.is_hasil >= 1 THEN true
            ELSE false
        END AS is_hasil,
    detail.tindakanpelayanan_id,
    hasil.tgl_verifikasi,
    header.created_by,
    header.penjamin_kode,
    detail.cyto_tindakan,
    detail.qty_tindakan,
    header.no_identitas_pasien,
    detail.hasilpemeriksaanrad_id,
    detail.status_bayar,
    detail.status_periksa_penunjang,
    detail.status_batal,
    header.groupcarabayar_id,
    header.sepesial_pemeriksaan
   FROM ( SELECT 'ORDER'::text AS tipe_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasienmasukpenunjang_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
            instalasi_m.instalasi_nama AS asalrujukan_nama,
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            penjamin_m.carabayar_id,
            carabayar_m.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
            pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            pasienadmisi_t.pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasien_m.no_telepon_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
            concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            diagnosa.diagnosa_utama AS nama_diagnosa,
            false AS is_mcu,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien,
            carabayar_m.groupcarabayar_id,
            true AS sepesial_pemeriksaan
           FROM pasienmasukpenunjang_t
             JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pegawai_m dokter_perujuk ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
             LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
                  WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN ( SELECT cppt_t_1.cppt_id,
                            cppt_t_1.pendaftaran_id,
                            cppt_t_1.a_diag_utama,
                            cppt_t_1.a_diag_penyerta
                           FROM cppt_t cppt_t_1
                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                    cppt_last.pendaftaran_id
                                   FROM cppt_t cppt_last
                                  WHERE cppt_last.is_deleted = false
                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
                  WHERE cppt_t.a_diag_utama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                     JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
                  WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5
        UNION ALL
         SELECT 'ORDER'::text AS tipe_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasienmasukpenunjang_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
            instalasi_m.instalasi_nama AS asalrujukan_nama,
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
            pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            pasienadmisi_t.pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasien_m.no_telepon_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
            concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            diagnosa.diagnosa_utama AS nama_diagnosa,
            false AS is_mcu,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien,
            carabayar_m.groupcarabayar_id,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 2 THEN true
                    ELSE false
                END AS sepesial_pemeriksaan
           FROM pasienmasukpenunjang_t
             JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
                  WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN ( SELECT cppt_t_1.cppt_id,
                            cppt_t_1.pendaftaran_id,
                            cppt_t_1.a_diag_utama,
                            cppt_t_1.a_diag_penyerta
                           FROM cppt_t cppt_t_1
                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                    cppt_last.pendaftaran_id
                                   FROM cppt_t cppt_last
                                  WHERE cppt_last.is_deleted = false
                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
                  WHERE cppt_t.a_diag_utama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                     JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
                  WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
        UNION ALL
         SELECT 'RUJUKAN RS'::text AS tipe_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasienmasukpenunjang_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            rujukan_t.no_rujukan,
            rujukan_t.asalrujukan_id,
            asalrujukan_m.asalrujukan_nama,
            rujukan_t.rujukandari_id AS ruanganasal_id,
            perujuk_m.namaperujuk AS ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
            pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            NULL::integer AS pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasien_m.no_telepon_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
            concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            diagnosa.diagnosa_utama AS nama_diagnosa,
            false AS is_mcu,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien,
            carabayar_m.groupcarabayar_id,
            false AS sepesial_pemeriksaan
           FROM pasienmasukpenunjang_t
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
                  WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN ( SELECT cppt_t_1.cppt_id,
                            cppt_t_1.pendaftaran_id,
                            cppt_t_1.a_diag_utama,
                            cppt_t_1.a_diag_penyerta
                           FROM cppt_t cppt_t_1
                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                    cppt_last.pendaftaran_id
                                   FROM cppt_t cppt_last
                                  WHERE cppt_last.is_deleted = false
                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
                  WHERE cppt_t.a_diag_utama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                     JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
                  WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
          WHERE pendaftaran_t.instalasi_id = 5
        UNION ALL
         SELECT 'APS'::text AS tipe_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            NULL::character varying AS no_rujukan,
            pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
            'APS'::character varying AS asalrujukan_nama,
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
            pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            NULL::integer AS pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasien_m.no_telepon_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
            concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            diagnosa.diagnosa_utama AS nama_diagnosa,
                CASE ruangan_pendaftaran.instalasi_id
                    WHEN 21 THEN true
                    ELSE false
                END AS is_mcu,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien,
            carabayar_m.groupcarabayar_id,
            false AS sepesial_pemeriksaan
           FROM pasienmasukpenunjang_t
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
             LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
                  WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN ( SELECT cppt_t_1.cppt_id,
                            cppt_t_1.pendaftaran_id,
                            cppt_t_1.a_diag_utama,
                            cppt_t_1.a_diag_penyerta
                           FROM cppt_t cppt_t_1
                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                    cppt_last.pendaftaran_id
                                   FROM cppt_t cppt_last
                                  WHERE cppt_last.is_deleted = false
                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
                  WHERE cppt_t.a_diag_utama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                     JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
                  WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
             LEFT JOIN ruangan_m ruangan_pendaftaran ON pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id
          WHERE ruang_penunjang.instalasi_id = 5 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.is_bayar = true) header
     LEFT JOIN ( SELECT 'NON_PAKET'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
            tindakanpelayanan_t.tipepaket_id,
            ''::character varying AS tipepaket_nama,
            NULL::text AS detail_2,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS dokter_id_detail,
            pegawai_m.nama_pegawai AS dokter_nama_detail,
            pasienmasukpenunjang_t.created_by,
            hasilpemeriksaanrad.hasilpemeriksaanrad_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN false
                    ELSE true
                END AS status_bayar,
                CASE
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NULL THEN 'BELUM PERIKSA'::text
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL THEN 'SELESAI'::text
                    WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'BATAL'::text
                    ELSE NULL::text
                END AS status_periksa_penunjang,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN true
                    ELSE false
                END AS status_batal
           FROM pasienmasukpenunjang_t
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
             LEFT JOIN pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
             LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             LEFT JOIN pegawai_m ON COALESCE(permintaankepenunjang_t.dokter_id::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                    hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
                    hasilpemeriksaanrad_t.tindakanpelayanan_id
                   FROM hasilpemeriksaanrad_t
                  WHERE hasilpemeriksaanrad_t.is_deleted IS FALSE) hasilpemeriksaanrad ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id
          WHERE daftartindakan_m.kelompoktindakan_id = 10
        UNION ALL
         SELECT 'PAKET'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            NULL::text AS detail_2,
            paketpelayanan_mp.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS dokter_id_detail,
            pegawai_m.nama_pegawai AS dokter_nama_detail,
            pasienmasukpenunjang_t.created_by,
            hasilpemeriksaanrad.hasilpemeriksaanrad_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN false
                    ELSE true
                END AS status_bayar,
                CASE
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NULL THEN 'BELUM PERIKSA'::text
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL THEN 'SELESAI'::text
                    WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'BATAL'::text
                    ELSE NULL::text
                END AS status_periksa_penunjang,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN true
                    ELSE false
                END AS status_batal
           FROM pasienmasukpenunjang_t
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
             LEFT JOIN pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
             LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             LEFT JOIN pegawai_m ON COALESCE(permintaankepenunjang_t.dokter_id::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                    hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
                    hasilpemeriksaanrad_t.tindakanpelayanan_id
                   FROM hasilpemeriksaanrad_t
                  WHERE hasilpemeriksaanrad_t.is_deleted IS FALSE) hasilpemeriksaanrad ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id
          WHERE daftartindakan_m.kelompoktindakan_id = 10
        UNION ALL
         SELECT 'PAKET_MCU'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            detail_1.j_rad AS jenispemeriksaanrad_nama,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            detail_1.detail_2,
            detail_1.detail_3id AS daftartindakan_id,
            detail_1.detail_3 AS daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS dokter_id_detail,
            pegawai_m.nama_pegawai AS dokter_nama_detail,
            pasienmasukpenunjang_t.created_by,
            hasilpemeriksaanrad.hasilpemeriksaanrad_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN false
                    ELSE true
                END AS status_bayar,
                CASE
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NULL THEN 'BELUM PERIKSA'::text
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL THEN 'SELESAI'::text
                    WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'BATAL'::text
                    ELSE NULL::text
                END AS status_periksa_penunjang,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN true
                    ELSE false
                END AS status_batal
           FROM pasienmasukpenunjang_t
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    paketpelayanan_mp.paketdetail_id AS detail_2id,
                    paket_detail.tipepaket_nama AS detail_2,
                    paket_detail.daftartindakan_id AS detail_3id,
                    paket_detail.daftartindakan_nama AS detail_3,
                    paket_detail.p_rad,
                    paket_detail.j_rad
                   FROM tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            daftartindakan_m.daftartindakan_id,
                            daftartindakan_m.daftartindakan_nama,
                            pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                           FROM tipepaket_m a
                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id
                             JOIN ruangan_m ruangan_m_1 ON paketpelayanan_mp_1.ruangan_id = ruangan_m_1.ruangan_id AND ruangan_m_1.instalasi_id = 5
                             JOIN daftartindakan_m ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
                             LEFT JOIN pemeriksaanrad_m ON daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                             LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id) paket_detail ON paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id
                  WHERE tipepaket_m_1.is_deleted = false
                UNION ALL
                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    NULL::integer AS detail_2id,
                    NULL::character varying AS detail_2,
                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
                    tindakan_detail.daftartindakan_nama AS detail_3,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                   FROM tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN ruangan_m ruangan_m_1 ON paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id AND ruangan_m_1.instalasi_id = 5
                     JOIN daftartindakan_m tindakan_detail ON paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id
                     LEFT JOIN pemeriksaanrad_m ON tindakan_detail.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                  WHERE tipepaket_m_1.is_deleted = false) detail_1 ON tindakanpelayanan_t.tipepaket_id = detail_1.detail_1id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                    hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
                    hasilpemeriksaanrad_t.tindakanpelayanan_id
                   FROM hasilpemeriksaanrad_t
                  WHERE hasilpemeriksaanrad_t.is_deleted IS FALSE) hasilpemeriksaanrad ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id) detail ON header.pasienmasukpenunjang_id = detail.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS is_hasil,
            hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
            hasilpemeriksaanrad_t.tindakanpelayanan_id,
            pemeriksaanrad_m.daftartindakan_id,
            hasilpemeriksaanrad_t.tgl_verifikasi
           FROM hasilpemeriksaanrad_t
             JOIN pemeriksaanrad_m ON pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id
          WHERE hasilpemeriksaanrad_t.is_deleted = false
          GROUP BY hasilpemeriksaanrad_t.pasienmasukpenunjang_id, hasilpemeriksaanrad_t.tindakanpelayanan_id, pemeriksaanrad_m.daftartindakan_id, hasilpemeriksaanrad_t.tgl_verifikasi) hasil ON header.pasienmasukpenunjang_id = hasil.pasienmasukpenunjang_id AND detail.tindakanpelayanan_id = hasil.tindakanpelayanan_id AND detail.daftartindakan_id = hasil.daftartindakan_id;");

        $this->execute('ALTER TABLE "public"."infopasienradiologi_v" OWNER TO "postgres";');
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210512_081928_oddo_20210512_penyesuianvieworderan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210512_081928_oddo_20210512_penyesuianvieworderan cannot be reverted.\n";

        return false;
    }
    */
}
