CREATE VIEW "public"."antrian_v" AS  SELECT antrian_t.antrian_id,
    antrian_t.no_antrian,
    antrian_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.no_telepon_pasien,
    antrian_t.ruangan_id,
    ruangan_m.ruangan_nama,
    antrian_t.carabayar_id,
    carabayar_m.carabayar_nama,
    antrian_t.penjamin_id,
    penjamin_m.penjamin_nama,
    antrian_t.pendaftaran_id,
    antrian_t.layarantrian_id,
    layarantrian_m.layarantrian_nama,
    antrian_t.loket_id,
    loket_m.loket_nama,
    antrian_t.panggilan_ke,
    antrian_t.tgl_antrian,
    antrian_t.status_antrian,
        CASE
            WHEN antrian_t.status_antrian = 0 THEN 'Belum Panggil'::text
            WHEN antrian_t.status_antrian = 1 THEN 'Panggil'::text
            WHEN antrian_t.status_antrian = 2 THEN 'Lewati'::text
            ELSE 'Batal'::text
        END AS stat_antrian,
    antrian_t.status_pasien,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = antrian_t.status_pasien) AS stat_pasien,
    COALESCE(( SELECT DISTINCT ON (reseptur_t_1.antrian_id)
                CASE
                    WHEN resepturracikan_t.ct_racikan >= 1 THEN 1
                    WHEN resepturdetail_t.count_obat_racikan >= 1 THEN 1
                    ELSE 2
                END AS racikan_id
           FROM reseptur_t reseptur_t_1
             LEFT JOIN ( SELECT a.reseptur_id,
                    count(a.racikan_id) FILTER (WHERE a.racikan_id = 1) AS count_obat_racikan
                   FROM resepturdetail_t a
                  WHERE a.reseptur_id IS NOT NULL AND a.is_deleted = false
                  GROUP BY a.reseptur_id) resepturdetail_t ON reseptur_t_1.reseptur_id = resepturdetail_t.reseptur_id
             LEFT JOIN ( SELECT count(*) AS ct_racikan,
                    a.reseptur_id
                   FROM resepturracikan_t a
                  GROUP BY a.reseptur_id) resepturracikan_t ON reseptur_t_1.reseptur_id = resepturracikan_t.reseptur_id
          WHERE reseptur_t_1.antrian_id = antrian_t.antrian_id
        UNION ALL
         SELECT DISTINCT ON (penjualanresep_t_1.antrian_id)
                CASE
                    WHEN obatalkespasien_t.count_obat_racikan >= 1 THEN 1
                    ELSE 2
                END AS racikan_id
           FROM penjualanresep_t penjualanresep_t_1
             LEFT JOIN ( SELECT a.penjualanresep_id,
                    count(a.racikan_id) FILTER (WHERE a.racikan_id = 1) AS count_obat_racikan
                   FROM obatalkespasien_t a
                  WHERE a.penjualanresep_id IS NOT NULL AND a.is_deleted = false
                  GROUP BY a.penjualanresep_id) obatalkespasien_t ON penjualanresep_t_1.penjualanresep_id = obatalkespasien_t.penjualanresep_id
          WHERE penjualanresep_t_1.antrian_id = antrian_t.antrian_id), 0) AS racikan_id,
    racikan_m.racikan_nama,
    pegawai_m.nama_pegawai,
    concat(( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = pegawai_m.gelardepan::integer), ' ', pegawai_m.nama_pegawai, ' ', gelarbelakang.gelarbelakang_nama) AS nama_pegawai_lengkap,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = antrian_t.groupcarabayar_id) AS namagroupcarabayar,
    antrian_t.jenisantrian_id,
    pegawai_m.dokter_id,
    ruangan_m.poliklinik_id,
    jadwalbukapoli_m.shift_id,
    antrian_t.is_online,
    antrian_t.fungsiantrian_id,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = antrian_t.fungsiantrian_id) AS fungsi_nama,
    instalasi.instalasi_nama,
    antrian_t.antrian_farmasi,
        CASE
            WHEN (( SELECT lookup_m.lookup_name
               FROM lookup_m
              WHERE lookup_m.lookup_id = antrian_t.antrian_farmasi)) IS NULL THEN 'Belum Proses'::text::character varying
            ELSE ( SELECT lookup_m.lookup_name
               FROM lookup_m
              WHERE lookup_m.lookup_id = antrian_t.antrian_farmasi)
        END AS stat_antrian_farmasi,
        CASE
            WHEN antrian_t.antrian_farmasi = 584 THEN 'Siap Ambil'::text
            WHEN antrian_t.antrian_farmasi = 585 THEN 'Selesai'::text
            WHEN antrian_t.antrian_farmasi = 586 THEN 'Selesai'::text
            ELSE 'Proses'::text
        END AS stat_proses_antrian_farmasi,
    antrian_t.is_appointment,
    pendaftaran_t.no_pendaftaran,
    pendaftaranol_t.tgl_pendaftaranol,
    antrian_t.panggil_flag,
    pegawai_m.pegawai_id,
    ruangan_m.ruangan_urutan,
    jenisantriandetail_m.nama AS lantai,
    antrian_t.jenisantriandetail_id,
        CASE
            WHEN ((( SELECT lookup_m.lookup_name
               FROM lookup_m
              WHERE lookup_m.lookup_id = reseptur_t.status_reseptur
            UNION ALL
             SELECT lookup_m.lookup_name
               FROM lookup_m
              WHERE lookup_m.lookup_id = penjualanresep_t.status_reseptur
     LIMIT 1))::text) = 'Diserahkan'::text THEN 'Diserahkan'::text::character varying::text
            WHEN antrian_t.panggilan_ke > 1::double precision THEN 'Siap Diserahkan'::text::character varying::text
            ELSE (( SELECT lookup_m.lookup_name
               FROM lookup_m
              WHERE lookup_m.lookup_id = reseptur_t.status_reseptur
            UNION ALL
             SELECT lookup_m.lookup_name
               FROM lookup_m
              WHERE lookup_m.lookup_id = penjualanresep_t.status_reseptur
     LIMIT 1))::text
        END AS status_reseptur,
        CASE
            WHEN (( SELECT konfigsystem_k.konfig_display_antrian_farmasi_etiket
               FROM konfigsystem_k)) IS TRUE AND COALESCE(penjualanresep_t.is_cetak_etiket, reseptur_t.is_cetak_etiket, false) IS FALSE THEN false
            ELSE true
        END AS is_display,
    COALESCE(penjualanresep_t.tgl_cetak_etiket, reseptur_t.tgl_cetak_etiket, date(antrian_t.tgl_antrian)) AS tgl_cetak_etiket
   FROM antrian_t
     LEFT JOIN ( SELECT ruangan_m_1.ruangan_id,
            ruangan_m_1.ruangan_nama,
            ruangan_m_1.poliklinik_id,
            ruangan_m_1.ruangan_urutan
           FROM ruangan_m ruangan_m_1) ruangan_m ON antrian_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT carabayar_m_1.carabayar_id,
            carabayar_m_1.carabayar_nama
           FROM carabayar_m carabayar_m_1) carabayar_m ON antrian_t.antrian_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT layarantrian_m_1.layarantrian_id,
            layarantrian_m_1.layarantrian_nama
           FROM layarantrian_m layarantrian_m_1) layarantrian_m ON antrian_t.layarantrian_id = layarantrian_m.layarantrian_id
     LEFT JOIN ( SELECT loket_m_1.loket_id,
            loket_m_1.loket_nama
           FROM loket_m loket_m_1) loket_m ON antrian_t.loket_id = loket_m.loket_id
     LEFT JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.nama_pasien,
            pasien_m_1.no_telepon_pasien
           FROM pasien_m pasien_m_1) pasien_m ON antrian_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON antrian_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai,
            pegawai_m_1.gelardepan,
            pegawai_m_1.dokter_id,
            pegawai_m_1.gelarbelakang
           FROM pegawai_m pegawai_m_1) pegawai_m ON antrian_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN ( SELECT jadwaldokter_m_1.jadwaldokter_id,
            jadwaldokter_m_1.jadwalbukapoli_id
           FROM jadwaldokter_m jadwaldokter_m_1
          WHERE jadwaldokter_m_1.is_deleted = false AND jadwaldokter_m_1.is_active = true) jadwaldokter_m ON antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id
     LEFT JOIN ( SELECT jadwalbukapoli_m_1.jadwalbukapoli_id,
            jadwalbukapoli_m_1.shift_id
           FROM jadwalbukapoli_m jadwalbukapoli_m_1
          WHERE jadwalbukapoli_m_1.is_deleted = false) jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
     LEFT JOIN ( SELECT instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama
           FROM instalasi_m) instalasi ON antrian_t.instalasi_id = instalasi.instalasi_id
     LEFT JOIN ( SELECT pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.pendaftaran_id
           FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaranol_t_1.antrian_id,
            pendaftaranol_t_1.tgl_pendaftaranol
           FROM pendaftaranol_t pendaftaranol_t_1) pendaftaranol_t ON antrian_t.antrian_id = pendaftaranol_t.antrian_id
     LEFT JOIN jenisantriandetail_m ON antrian_t.jenisantriandetail_id = jenisantriandetail_m.jenisantriandetail_id
     LEFT JOIN ( SELECT a.penjualanresep_id,
            a.antrian_id,
            a.status_reseptur,
            a.is_cetak_etiket,
            a.tgl_cetak_etiket::date AS tgl_cetak_etiket
           FROM penjualanresep_t a) penjualanresep_t ON penjualanresep_t.antrian_id = antrian_t.antrian_id
     LEFT JOIN ( SELECT a.reseptur_id,
            a.antrian_id,
            a.status_reseptur,
            a.is_cetak_etiket,
            a.tgl_cetak_etiket::date AS tgl_cetak_etiket
           FROM reseptur_t a
          WHERE a.is_deleted = false) reseptur_t ON reseptur_t.reseptur_id = antrian_t.antrian_id
     LEFT JOIN racikan_m ON COALESCE(antrian_t.racikan_id) = racikan_m.racikan_id;