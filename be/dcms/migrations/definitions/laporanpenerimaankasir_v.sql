-- public.laporanpenerimaankasir_v source

CREATE OR REPLACE VIEW public.laporanpenerimaankasir_v
AS SELECT pegawai_kasir.nama_pegawai AS kasir,
    rekap_kasir.tanggal,
    rekap_kasir.no_kwitansi,
    rekap_kasir.no_registrasi,
    rekap_kasir.info_pasien,
    rekap_kasir.rupiah,
    rekap_kasir.transaksi,
    rekap_kasir.keterangan,
    rekap_kasir.cara_bayar,
    rekap_kasir.penjamin,
    rekap_kasir.bank_id,
    bank_m.nama_bank,
    rekap_kasir.no_kartu,
    rekap_kasir.ruangan_id,
    rekap_kasir.deskripsi,
    ruangan_akhir.ruangan_nama,
    instalasi_akhir.instalasi_id,
    instalasi_akhir.instalasi_nama,
    rekap_kasir.kelaspelayanan_id,
    rekap_kasir.kelaspelayanan_nama
   FROM ( SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            pembayaran_t.total_tunai - pembayaran_t.total_kembalian AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBAYARAN TAGIHAN NON MULTY'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            NULL::text AS no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM pembayaranpelayanan_t
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.closingkasir_id,
                    a.is_deleted
                   FROM tandabuktibayar_t a) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             JOIN ( SELECT a.closingkasir_id,
                    a.ruangan_id,
                    a.tgl_closingkasir,
                    a.created_by
                   FROM closingkasir_t a
                  WHERE a.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT a.pembayaran_id,
                    a.total_tunai,
                    a.total_kembalian
                   FROM pembayaran_t a) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.pasien_id,
                    a.no_pendaftaran,
                    a.ruangan_id
                   FROM pendaftaran_t a) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.ruangan_id,
                    a.kelaspelayanan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE pembayaranpelayanan_t.penjualanresep_id IS NULL AND pembayaranpelayanan_t.pendaftaran_id IS NOT NULL AND tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL AND (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) <> 0::double precision
        UNION ALL
         SELECT COALESCE(closingkasir_t.ruangan_id, tandabuktibayar_t.ruangan_id) AS kasir,
            to_char(COALESCE(closingkasir_t.tgl_closingkasir, tandabuktibayar_t.tglbuktibayar), 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            pembayaran_t.total_nontunai - pembayaran_t.total_kembalian AS rupiah,
            'NON TUNAI'::text AS transaksi,
            (((('PEMBAYARAN TAGIHAN NON MULTY'::text || ' - '::text) || COALESCE(pembayaranmetode_t.metode_bayar, ''::character varying)::text) || ' - '::text) || (COALESCE(pembayaranmetode_t.nama_edc, ''::character varying)::text || ' - '::text)) || COALESCE(pembayaranmetode_t.no_kartu, ''::character varying)::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            COALESCE(closingkasir_t.created_by, pembayaran_t.created_by) AS created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM pembayaranpelayanan_t
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.closingkasir_id,
                    a.is_deleted,
                    a.created_by,
                    a.tglbuktibayar,
                    a.ruangan_id
                   FROM tandabuktibayar_t a) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             JOIN ( SELECT a.closingkasir_id,
                    a.ruangan_id,
                    a.tgl_closingkasir,
                    a.created_by
                   FROM closingkasir_t a
                  WHERE a.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT a.pembayaran_id,
                    a.total_tunai,
                    a.total_kembalian,
                    a.total_nontunai,
                    a.created_by
                   FROM pembayaran_t a) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.pasien_id,
                    a.no_pendaftaran,
                    a.ruangan_id
                   FROM pendaftaran_t a) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.ruangan_id,
                    a.kelaspelayanan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT b.pembayaran_id,
                    b.jenisnontunai_id,
                    b.total_dibayar,
                    b.metode_bayar,
                    b.no_kartu,
                    b.nama_edc
                   FROM pembayaranmetode_t b) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE pembayaranpelayanan_t.penjualanresep_id IS NULL AND pembayaranpelayanan_t.pendaftaran_id IS NOT NULL AND tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL AND (pembayaran_t.total_nontunai - pembayaran_t.total_kembalian) <> 0::double precision
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS rupiah,
            'PENJAMIN'::text AS transaksi,
            'PEMBAYARAN TAGIHAN NON MULTY'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM pembayaranpelayanan_t
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.closingkasir_id,
                    a.is_deleted,
                    a.created_by,
                    a.tglbuktibayar,
                    a.ruangan_id
                   FROM tandabuktibayar_t a) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             JOIN ( SELECT a.closingkasir_id,
                    a.ruangan_id,
                    a.tgl_closingkasir,
                    a.created_by
                   FROM closingkasir_t a
                  WHERE a.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT a.pembayaran_id,
                    a.total_tunai,
                    a.total_kembalian,
                    a.total_nontunai,
                    a.created_by,
                    a.total_dijamin,
                    a.pemberianpiutang_id,
                    a.is_deleted
                   FROM pembayaran_t a) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.pasien_id,
                    a.no_pendaftaran,
                    a.ruangan_id
                   FROM pendaftaran_t a) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.ruangan_id,
                    a.kelaspelayanan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT b.pembayaran_id,
                    b.jenisnontunai_id,
                    b.total_dibayar,
                    b.metode_bayar,
                    b.no_kartu
                   FROM pembayaranmetode_t b) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT c.pemberianpiutang_id,
                    c.total_piutang
                   FROM pemberianpiutang_t c) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE pembayaran_t.total_dijamin <> 0::double precision OR pembayaran_t.pemberianpiutang_id IS NOT NULL AND pembayaran_t.is_deleted IS FALSE
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            pembayaran_t.total_tunai - pembayaran_t.total_kembalian AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBAYARAN TAGIHAN'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            NULL::text AS no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM pembayaran_t
             JOIN ( SELECT a.pembayaran_id,
                    a.closingkasir_id,
                    a.tandabuktibayar_id
                   FROM tandabuktibayar_t a
                  WHERE a.pembayaran_id IS NOT NULL) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT a.closingkasir_id,
                    a.ruangan_id,
                    a.tgl_closingkasir,
                    a.created_by
                   FROM closingkasir_t a
                  WHERE a.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id,
                    a.pasienadmisi_id,
                    a.no_pendaftaran,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.ruangan_id,
                    a.kelaspelayanan_id
                   FROM pendaftaran_t a) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.ruangan_id,
                    a.kelaspelayanan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) > 0::double precision
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            pembayaranmetode_t.total_dibayar AS rupiah,
            'NON TUNAI'::text AS transaksi,
            (((('PEMBAYARAN TAGIHAN'::text || ' - '::text) || pembayaranmetode_t.metode_bayar::text) || ' - '::text) || (COALESCE(pembayaranmetode_t.nama_edc, ''::character varying)::text || ' - '::text)) || COALESCE(pembayaranmetode_t.no_kartu, ''::character varying)::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            jenisnontunai_m.bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM pembayaran_t
             JOIN ( SELECT b.pembayaran_id,
                    b.closingkasir_id,
                    b.tandabuktibayar_id
                   FROM tandabuktibayar_t b
                  WHERE b.pembayaran_id IS NOT NULL) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT b.closingkasir_id,
                    b.ruangan_id,
                    b.tgl_closingkasir,
                    b.created_by
                   FROM closingkasir_t b
                  WHERE b.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT b.pendaftaran_id,
                    b.pasien_id,
                    b.pasienadmisi_id,
                    b.no_pendaftaran,
                    b.carabayar_id,
                    b.penjamin_id,
                    b.ruangan_id,
                    b.kelaspelayanan_id
                   FROM pendaftaran_t b) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT b.pasien_id,
                    b.nama_pasien
                   FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT b.pembayaran_id,
                    b.jenisnontunai_id,
                    b.total_dibayar,
                    b.metode_bayar,
                    b.no_kartu,
                    b.nama_edc
                   FROM pembayaranmetode_t b) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT b.jenisnontunai_id,
                    b.bank_id
                   FROM jenisnontunai_m b) jenisnontunai_m ON pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN ( SELECT b.carabayar_id,
                    b.carabayar_nama
                   FROM carabayar_m b) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT b.penjamin_id,
                    b.penjamin_nama
                   FROM penjamin_m b) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT b.pasienadmisi_id,
                    b.ruangan_id,
                    b.kelaspelayanan_id
                   FROM pasienadmisi_t b) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE pembayaran_t.total_nontunai <> 0::double precision AND pembayaran_t.is_deleted IS FALSE
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS rupiah,
            'PENJAMIN'::text AS transaksi,
            'PEMBAYARAN TAGIHAN'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM pembayaran_t
             JOIN ( SELECT c.tandabuktibayar_id,
                    c.pembayaran_id,
                    c.closingkasir_id
                   FROM tandabuktibayar_t c
                  WHERE c.pembayaran_id IS NOT NULL) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT c.closingkasir_id,
                    c.ruangan_id,
                    c.tgl_closingkasir,
                    c.created_by
                   FROM closingkasir_t c
                  WHERE c.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT c.pendaftaran_id,
                    c.pasien_id,
                    c.no_pendaftaran,
                    c.penjamin_id,
                    c.carabayar_id,
                    c.pasienadmisi_id,
                    c.ruangan_id,
                    c.kelaspelayanan_id
                   FROM pendaftaran_t c) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT c.pasien_id,
                    c.nama_pasien
                   FROM pasien_m c) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT c.pemberianpiutang_id,
                    c.total_piutang
                   FROM pemberianpiutang_t c) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
             LEFT JOIN ( SELECT c.carabayar_id,
                    c.carabayar_nama
                   FROM carabayar_m c) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT c.penjamin_id,
                    c.penjamin_nama
                   FROM penjamin_m c) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT c.pembayaran_id,
                    c.no_kartu
                   FROM pembayaranmetode_t c) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT c.pasienadmisi_id,
                    c.ruangan_id,
                    c.kelaspelayanan_id
                   FROM pasienadmisi_t c) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE pembayaran_t.total_dijamin <> 0::double precision OR pembayaran_t.pemberianpiutang_id IS NOT NULL
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
            pembayaran_t.penggunaan_uangmuka - pembayaran_t.total_kembalian AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBAYARAN TAGIHAN'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            NULL::text AS no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM pembayaran_t
             JOIN ( SELECT a.pembayaran_id,
                    a.closingkasir_id,
                    a.tandabuktibayar_id
                   FROM tandabuktibayar_t a
                  WHERE a.pembayaran_id IS NOT NULL) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT a.closingkasir_id,
                    a.ruangan_id,
                    a.tgl_closingkasir,
                    a.created_by
                   FROM closingkasir_t a
                  WHERE a.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id,
                    a.pasienadmisi_id,
                    a.no_pendaftaran,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.ruangan_id,
                    a.kelaspelayanan_id
                   FROM pendaftaran_t a) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.ruangan_id,
                    a.kelaspelayanan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE (pembayaran_t.penggunaan_uangmuka - pembayaran_t.total_kembalian) > 0::double precision
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_registrasi,
            COALESCE(pasien_m.nama_pasien, penjualanresep_t.nama_pembeli) AS info_pasien,
            - (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBATALAN PEMBAYARAN TAGIHAN'::text AS keterangan,
            COALESCE(carabayar_m.carabayar_nama, carabayar_resep.carabayar_nama) AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            NULL::text AS no_kartu,
            COALESCE(closingkasir_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.kelaspelayanan_nama
           FROM closingkasir_t
             JOIN ( SELECT a.pembatalanpembayaran_id,
                    a.tgl_buktikeluar,
                    a.uang_diterima,
                    a.closingkasir_id,
                    a.created_by,
                    a.is_deleted
                   FROM tandabuktikeluar_t a) tandabuktikeluar_t ON closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id
             JOIN ( SELECT a.pembatalanpembayaran_id,
                    a.no_pembayaran,
                    a.pendaftaran_id,
                    a.pembayaran_id,
                    a.total_nontunai,
                    a.total_ditagihkan,
                    COALESCE(a.total_dijamin, 0::double precision) + a.total_pembulatan + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    a.total_tunai,
                    a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - (a.total_discount + a.total_discountpembayaran) AS total_tagihan,
                    pembayaranpelayanan_t.deleted_date AS tgl_batal,
                    pembayaranpelayanan_t.alasan_batal,
                    pembayaranpelayanan_t.ruangan_id,
                    pembayaranpelayanan_t.deleted_date,
                    a.total_discount,
                    a.total_discountpembayaran,
                    a.total_dijamin,
                    a.total_pembulatan,
                    pembayaranpelayanan_t.penjualanresep_id,
                    a.total_administrasi,
                    a.total_kembalian,
                    a.pembulatan,
                    pemberianpiutang_t.total_piutang,
                    a.is_deleted
                   FROM pembatalanpembayaran_t a
                     LEFT JOIN ( SELECT a1.pembayaran_id,
                            a1.ruangan_id,
                            pembayaran_t_1.deleted_date,
                            pembayaran_t_1.alasan_batal,
                            a1.penjualanresep_id,
                            pembayaran_t_1.no_pembayaran
                           FROM pembayaranpelayanan_t a1
                             LEFT JOIN ( SELECT a_1.pembayaran_id,
                                    a_1.deleted_date,
                                    a_1.alasan_batal,
                                    a_1.no_pembayaran
                                   FROM pembayaran_t a_1) pembayaran_t_1 ON a1.pembayaran_id = pembayaran_t_1.pembayaran_id
                          GROUP BY a1.pembayaran_id, a1.ruangan_id, pembayaran_t_1.deleted_date, pembayaran_t_1.alasan_batal, a1.penjualanresep_id, pembayaran_t_1.no_pembayaran) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                     LEFT JOIN ( SELECT a1.pemberianpiutang_id,
                            a1.total_piutang
                           FROM pemberianpiutang_t a1) pemberianpiutang_t ON a.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                  WHERE a.is_deleted = false) pembayaran_t ON tandabuktikeluar_t.pembatalanpembayaran_id = pembayaran_t.pembatalanpembayaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.penjamin_id,
                    a.carabayar_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama
                   FROM pendaftaran_t a
                     LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                            a_1.kelaspelayanan_id
                           FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ( SELECT a_1.kelaspelayanan_id,
                            a_1.kelaspelayanan_nama
                           FROM kelaspelayanan_m a_1) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k_1 ON tandabuktikeluar_t.created_by = loginpemakai_k_1.loginpemakai_id
             LEFT JOIN ( SELECT a.penjualanresep_id,
                    a.noresep,
                    a.karyawan_id,
                    a.nama_pembeli,
                    a.penjamin_id,
                    a.carabayar_id
                   FROM penjualanresep_t a) penjualanresep_t ON pembayaran_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_pendaftaran ON pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_pendaftaran ON pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_resep ON penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_resep ON penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id
             LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY pembayaranpelayanan_t.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) > 0::double precision AND tandabuktikeluar_t.is_deleted = false
        UNION ALL
         SELECT COALESCE(closingkasir_t.ruangan_id) AS kasir,
            to_char(COALESCE(closingkasir_t.tgl_closingkasir), 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_registrasi,
            COALESCE(pasien_m.nama_pasien, penjualanresep_t.nama_pembeli) AS info_pasien,
            - (pembayaran_t.total_nontunai - pembayaran_t.total_kembalian) AS rupiah,
            'NON TUNAI'::text AS transaksi,
            (((('PEMBATALAN PEMBAYARAN TAGIHAN'::text || ' - '::text) || COALESCE(pembayaranmetode_t.metode_bayar, ''::character varying)::text) || ' - '::text) || (COALESCE(pembayaranmetode_t.nama_edc, ''::character varying)::text || ' - '::text)) || COALESCE(pembayaranmetode_t.no_kartu, ''::character varying)::text AS keterangan,
            COALESCE(carabayar_m.carabayar_nama, carabayar_resep.carabayar_nama) AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            COALESCE(closingkasir_t.created_by) AS created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.kelaspelayanan_nama
           FROM closingkasir_t
             JOIN ( SELECT a.pembatalanpembayaran_id,
                    a.tgl_buktikeluar,
                    a.uang_diterima,
                    a.closingkasir_id,
                    a.created_by,
                    a.is_deleted
                   FROM tandabuktikeluar_t a) tandabuktikeluar_t ON closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id
             JOIN ( SELECT a.pembatalanpembayaran_id,
                    a.no_pembayaran,
                    a.pendaftaran_id,
                    a.pembayaran_id,
                    a.total_nontunai,
                    a.total_ditagihkan,
                    COALESCE(a.total_dijamin, 0::double precision) + a.total_pembulatan + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    a.total_tunai - a.total_kembalian AS total_tunai,
                    a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - (a.total_discount + a.total_discountpembayaran) AS total_tagihan,
                    pembayaranpelayanan_t.deleted_date AS tgl_batal,
                    pembayaranpelayanan_t.alasan_batal,
                    pembayaranpelayanan_t.ruangan_id,
                    pembayaranpelayanan_t.deleted_date,
                    a.total_discount,
                    a.total_discountpembayaran,
                    a.total_dijamin,
                    a.total_pembulatan,
                    pembayaranpelayanan_t.penjualanresep_id,
                    a.total_administrasi,
                    a.total_kembalian,
                    a.pembulatan,
                    pemberianpiutang_t.total_piutang,
                    a.is_deleted
                   FROM pembatalanpembayaran_t a
                     LEFT JOIN ( SELECT a1.pembayaran_id,
                            a1.ruangan_id,
                            pembayaran_t_1.deleted_date,
                            pembayaran_t_1.alasan_batal,
                            a1.penjualanresep_id,
                            pembayaran_t_1.no_pembayaran
                           FROM pembayaranpelayanan_t a1
                             LEFT JOIN ( SELECT a_1.pembayaran_id,
                                    a_1.deleted_date,
                                    a_1.alasan_batal,
                                    a_1.no_pembayaran
                                   FROM pembayaran_t a_1) pembayaran_t_1 ON a1.pembayaran_id = pembayaran_t_1.pembayaran_id
                          GROUP BY a1.pembayaran_id, a1.ruangan_id, pembayaran_t_1.deleted_date, pembayaran_t_1.alasan_batal, a1.penjualanresep_id, pembayaran_t_1.no_pembayaran) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                     LEFT JOIN ( SELECT a1.pemberianpiutang_id,
                            a1.total_piutang
                           FROM pemberianpiutang_t a1) pemberianpiutang_t ON a.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                  WHERE a.is_deleted = false) pembayaran_t ON tandabuktikeluar_t.pembatalanpembayaran_id = pembayaran_t.pembatalanpembayaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.penjamin_id,
                    a.carabayar_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    COALESCE(pasienadmisi_t.ruangan_id, a.ruangan_id) AS ruangan_id
                   FROM pendaftaran_t a
                     LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                            a_1.kelaspelayanan_id,
                            a_1.ruangan_id
                           FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ( SELECT a_1.kelaspelayanan_id,
                            a_1.kelaspelayanan_nama
                           FROM kelaspelayanan_m a_1) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k_1 ON tandabuktikeluar_t.created_by = loginpemakai_k_1.loginpemakai_id
             LEFT JOIN ( SELECT a.shift_id,
                    a.shift_nama
                   FROM shift_m a) shift_m ON closingkasir_t.shift_id = shift_m.shift_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_nama,
                    a.instalasi_id
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.penjualanresep_id,
                    a.noresep,
                    a.karyawan_id,
                    a.nama_pembeli,
                    a.penjamin_id,
                    a.carabayar_id
                   FROM penjualanresep_t a) penjualanresep_t ON pembayaran_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_pendaftaran ON pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_pendaftaran ON pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_resep ON penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_resep ON penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id
             LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY pembayaranpelayanan_t.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
             LEFT JOIN ( SELECT c.pembayaran_id,
                    c.no_kartu,
                    c.metode_bayar,
                    c.jenisnontunai_id,
                    c.nama_edc
                   FROM pembayaranmetode_t c) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
          WHERE pembayaran_t.total_nontunai <> 0::double precision AND tandabuktikeluar_t.is_deleted = false
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_registrasi,
            COALESCE(pasien_m.nama_pasien, penjualanresep_t.nama_pembeli) AS info_pasien,
            - (COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision)) AS rupiah,
            'PENJAMIN'::text AS transaksi,
            'PEMBATALAN PEMBAYARAN TAGIHAN'::text AS keterangan,
            COALESCE(carabayar_m.carabayar_nama, carabayar_resep.carabayar_nama) AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.kelaspelayanan_nama
           FROM closingkasir_t
             JOIN ( SELECT a.pembatalanpembayaran_id,
                    a.tgl_buktikeluar,
                    a.uang_diterima,
                    a.closingkasir_id,
                    a.created_by,
                    a.is_deleted
                   FROM tandabuktikeluar_t a) tandabuktikeluar_t ON closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id
             JOIN ( SELECT a.pembatalanpembayaran_id,
                    a.no_pembayaran,
                    a.pendaftaran_id,
                    a.pembayaran_id,
                    a.total_nontunai,
                    a.total_ditagihkan,
                    COALESCE(a.total_dijamin, 0::double precision) + a.total_pembulatan + COALESCE(pemberianpiutang_t_1.total_piutang, 0::double precision) AS total_penjamin,
                    a.total_tunai - a.total_kembalian AS total_tunai,
                    a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - (a.total_discount + a.total_discountpembayaran) AS total_tagihan,
                    pembayaranpelayanan_t.deleted_date AS tgl_batal,
                    pembayaranpelayanan_t.alasan_batal,
                    pembayaranpelayanan_t.ruangan_id,
                    pembayaranpelayanan_t.deleted_date,
                    a.total_discount,
                    a.total_discountpembayaran,
                    a.total_dijamin,
                    a.total_pembulatan,
                    pembayaranpelayanan_t.penjualanresep_id,
                    a.total_administrasi,
                    a.total_kembalian,
                    a.pembulatan,
                    pemberianpiutang_t_1.total_piutang,
                    a.is_deleted,
                    pembayaranpelayanan_t.pemberianpiutang_id
                   FROM pembatalanpembayaran_t a
                     LEFT JOIN ( SELECT a1.pembayaran_id,
                            a1.ruangan_id,
                            pembayaran_t_1.deleted_date,
                            pembayaran_t_1.alasan_batal,
                            a1.penjualanresep_id,
                            pembayaran_t_1.no_pembayaran,
                            pembayaran_t_1.pemberianpiutang_id
                           FROM pembayaranpelayanan_t a1
                             LEFT JOIN ( SELECT a_1.pembayaran_id,
                                    a_1.deleted_date,
                                    a_1.alasan_batal,
                                    a_1.no_pembayaran,
                                    a_1.pemberianpiutang_id
                                   FROM pembayaran_t a_1) pembayaran_t_1 ON a1.pembayaran_id = pembayaran_t_1.pembayaran_id
                          GROUP BY a1.pembayaran_id, a1.ruangan_id, pembayaran_t_1.deleted_date, pembayaran_t_1.alasan_batal, a1.penjualanresep_id, pembayaran_t_1.no_pembayaran, pembayaran_t_1.pemberianpiutang_id) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                     LEFT JOIN ( SELECT a1.pemberianpiutang_id,
                            a1.total_piutang
                           FROM pemberianpiutang_t a1) pemberianpiutang_t_1 ON a.pemberianpiutang_id = pemberianpiutang_t_1.pemberianpiutang_id
                  WHERE a.is_deleted = false) pembayaran_t ON tandabuktikeluar_t.pembatalanpembayaran_id = pembayaran_t.pembatalanpembayaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.penjamin_id,
                    a.carabayar_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    COALESCE(pasienadmisi_t.ruangan_id, a.ruangan_id) AS ruangan_id
                   FROM pendaftaran_t a
                     LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                            a_1.kelaspelayanan_id,
                            a_1.ruangan_id
                           FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ( SELECT a_1.kelaspelayanan_id,
                            a_1.kelaspelayanan_nama
                           FROM kelaspelayanan_m a_1) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k_1 ON tandabuktikeluar_t.created_by = loginpemakai_k_1.loginpemakai_id
             LEFT JOIN ( SELECT a.shift_id,
                    a.shift_nama
                   FROM shift_m a) shift_m ON closingkasir_t.shift_id = shift_m.shift_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_nama,
                    a.instalasi_id
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.penjualanresep_id,
                    a.noresep,
                    a.karyawan_id,
                    a.nama_pembeli,
                    a.penjamin_id,
                    a.carabayar_id
                   FROM penjualanresep_t a) penjualanresep_t ON pembayaran_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_pendaftaran ON pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_pendaftaran ON pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_resep ON penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_resep ON penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id
             LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY pembayaranpelayanan_t.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
             LEFT JOIN ( SELECT c.pembayaran_id,
                    c.no_kartu,
                    c.metode_bayar,
                    c.jenisnontunai_id
                   FROM pembayaranmetode_t c) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT c.pemberianpiutang_id,
                    c.total_piutang
                   FROM pemberianpiutang_t c) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
          WHERE pembayaran_t.total_dijamin <> 0::double precision OR pembayaran_t.pemberianpiutang_id IS NOT NULL AND tandabuktikeluar_t.is_deleted IS FALSE
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_registrasi,
            COALESCE(pasien_m.nama_pasien, penjualanresep_t.nama_pembeli) AS info_pasien,
            - (pembayaran_t.penggunaan_uangmuka - pembayaran_t.total_kembalian) AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBATALAN PEMBAYARAN TAGIHAN'::text AS keterangan,
            COALESCE(carabayar_m.carabayar_nama, carabayar_resep.carabayar_nama) AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            NULL::text AS no_kartu,
            COALESCE(closingkasir_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.kelaspelayanan_nama
           FROM closingkasir_t
             JOIN ( SELECT a.pembatalanpembayaran_id,
                    a.tgl_buktikeluar,
                    a.uang_diterima,
                    a.closingkasir_id,
                    a.created_by,
                    a.is_deleted
                   FROM tandabuktikeluar_t a) tandabuktikeluar_t ON closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id
             JOIN ( SELECT a.pembatalanpembayaran_id,
                    a.no_pembayaran,
                    a.pendaftaran_id,
                    a.pembayaran_id,
                    a.total_nontunai,
                    a.total_ditagihkan,
                    a.penggunaan_uangmuka,
                    COALESCE(a.total_dijamin, 0::double precision) + a.total_pembulatan + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    a.total_tunai - a.total_kembalian AS total_tunai,
                    a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - (a.total_discount + a.total_discountpembayaran) AS total_tagihan,
                    pembayaranpelayanan_t.deleted_date AS tgl_batal,
                    pembayaranpelayanan_t.alasan_batal,
                    pembayaranpelayanan_t.ruangan_id,
                    pembayaranpelayanan_t.deleted_date,
                    a.total_discount,
                    a.total_discountpembayaran,
                    a.total_dijamin,
                    a.total_pembulatan,
                    pembayaranpelayanan_t.penjualanresep_id,
                    a.total_administrasi,
                    a.total_kembalian,
                    a.pembulatan,
                    pemberianpiutang_t.total_piutang,
                    a.is_deleted
                   FROM pembatalanpembayaran_t a
                     LEFT JOIN ( SELECT a1.pembayaran_id,
                            a1.ruangan_id,
                            pembayaran_t_1.deleted_date,
                            pembayaran_t_1.alasan_batal,
                            a1.penjualanresep_id,
                            pembayaran_t_1.no_pembayaran
                           FROM pembayaranpelayanan_t a1
                             LEFT JOIN ( SELECT a_1.pembayaran_id,
                                    a_1.deleted_date,
                                    a_1.alasan_batal,
                                    a_1.no_pembayaran
                                   FROM pembayaran_t a_1) pembayaran_t_1 ON a1.pembayaran_id = pembayaran_t_1.pembayaran_id
                          GROUP BY a1.pembayaran_id, a1.ruangan_id, pembayaran_t_1.deleted_date, pembayaran_t_1.alasan_batal, a1.penjualanresep_id, pembayaran_t_1.no_pembayaran) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                     LEFT JOIN ( SELECT a1.pemberianpiutang_id,
                            a1.total_piutang
                           FROM pemberianpiutang_t a1) pemberianpiutang_t ON a.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                  WHERE a.is_deleted = false) pembayaran_t ON tandabuktikeluar_t.pembatalanpembayaran_id = pembayaran_t.pembatalanpembayaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.penjamin_id,
                    a.carabayar_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama
                   FROM pendaftaran_t a
                     LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                            a_1.kelaspelayanan_id
                           FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ( SELECT a_1.kelaspelayanan_id,
                            a_1.kelaspelayanan_nama
                           FROM kelaspelayanan_m a_1) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k_1 ON tandabuktikeluar_t.created_by = loginpemakai_k_1.loginpemakai_id
             LEFT JOIN ( SELECT a.penjualanresep_id,
                    a.noresep,
                    a.karyawan_id,
                    a.nama_pembeli,
                    a.penjamin_id,
                    a.carabayar_id
                   FROM penjualanresep_t a) penjualanresep_t ON pembayaran_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_pendaftaran ON pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_pendaftaran ON pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_resep ON penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_resep ON penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id
             LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY pembayaranpelayanan_t.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE (pembayaran_t.penggunaan_uangmuka - pembayaran_t.total_kembalian) > 0::double precision AND tandabuktikeluar_t.is_deleted = false
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            pembayaran_t.total_tunai - pembayaran_t.total_kembalian AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBAYARAN RESEP BEBAS'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            NULL::text AS no_kartu,
            penjualanresep_t.ruangan_id,
            NULL::character varying AS deskripsi,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama
           FROM penjualanresep_t
             JOIN ( SELECT d.penjualanresep_id,
                    d.pembayaran_id,
                    d.no_pembayaran,
                    d.pembayaranpelayanan_id
                   FROM pembayaranpelayanan_t d) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
             JOIN ( SELECT d.pembayaran_id,
                    d.total_tunai,
                    d.total_kembalian,
                    d.no_pembayaran
                   FROM pembayaran_t d
                  WHERE d.total_tunai <> 0::double precision) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT d.pembayaran_id,
                    d.closingkasir_id,
                    d.pembayaranpelayanan_id
                   FROM tandabuktibayar_t d) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT d.closingkasir_id,
                    d.ruangan_id,
                    d.tgl_closingkasir,
                    d.created_by
                   FROM closingkasir_t d
                  WHERE d.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT d.penjamin_id,
                    d.penjamin_nama,
                    d.carabayar_id
                   FROM penjamin_m d) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT d.carabayar_id,
                    d.carabayar_nama
                   FROM carabayar_m d) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            pembayaranmetode_t.total_dibayar AS rupiah,
            'NON TUNAI'::text AS transaksi,
            ((('PEMBAYARAN RESEP BEBAS 1'::text || ' - '::text) || pembayaranmetode_t.metode_bayar::text) || ' - '::text) || COALESCE(pembayaranmetode_t.no_kartu, ''::character varying)::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            jenisnontunai_m.bank_id,
            pembayaranmetode_t.no_kartu,
            penjualanresep_t.ruangan_id,
            NULL::character varying AS deskripsi,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama
           FROM penjualanresep_t
             JOIN ( SELECT e.penjualanresep_id,
                    e.pembayaran_id,
                    e.no_pembayaran,
                    e.pembayaranpelayanan_id
                   FROM pembayaranpelayanan_t e) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
             JOIN ( SELECT e.pembayaran_id,
                    e.total_nontunai,
                    e.no_pembayaran
                   FROM pembayaran_t e
                  WHERE e.total_nontunai <> 0::double precision) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT e.pembayaran_id,
                    e.closingkasir_id,
                    e.pembayaranpelayanan_id
                   FROM tandabuktibayar_t e) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT e.closingkasir_id,
                    e.ruangan_id,
                    e.tgl_closingkasir,
                    e.created_by
                   FROM closingkasir_t e
                  WHERE e.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT e.penjamin_id,
                    e.penjamin_nama,
                    e.carabayar_id
                   FROM penjamin_m e) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT e.carabayar_id,
                    e.carabayar_nama
                   FROM carabayar_m e) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT e.pembayaran_id,
                    e.jenisnontunai_id,
                    e.total_dibayar,
                    e.metode_bayar,
                    e.no_kartu,
                    e.nama_edc
                   FROM pembayaranmetode_t e) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT e.jenisnontunai_id,
                    e.bank_id
                   FROM jenisnontunai_m e) jenisnontunai_m ON pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS rupiah,
            'PENJAMIN'::text AS transaksi,
            'PEMBAYARAN RESEP BEBAS'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            penjualanresep_t.ruangan_id,
            NULL::character varying AS deskripsi,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama
           FROM penjualanresep_t
             JOIN ( SELECT f.penjualanresep_id,
                    f.pembayaran_id,
                    f.no_pembayaran,
                    f.pembayaranpelayanan_id
                   FROM pembayaranpelayanan_t f) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
             JOIN ( SELECT f.pembayaran_id,
                    f.pemberianpiutang_id,
                    f.total_dijamin,
                    f.no_pembayaran
                   FROM pembayaran_t f
                  WHERE f.total_dijamin <> 0::double precision OR f.pemberianpiutang_id IS NOT NULL) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT f.pembayaran_id,
                    f.closingkasir_id,
                    f.pembayaranpelayanan_id
                   FROM tandabuktibayar_t f) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT f.closingkasir_id,
                    f.ruangan_id,
                    f.tgl_closingkasir,
                    f.created_by
                   FROM closingkasir_t f
                  WHERE f.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT f.penjamin_id,
                    f.penjamin_nama,
                    f.carabayar_id
                   FROM penjamin_m f) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT f.carabayar_id,
                    f.carabayar_nama
                   FROM carabayar_m f) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT f.pemberianpiutang_id,
                    f.total_piutang
                   FROM pemberianpiutang_t f) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
             LEFT JOIN ( SELECT f.pembayaran_id,
                    f.no_kartu
                   FROM pembayaranmetode_t f) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            pembayaran_t.total_tunai - pembayaran_t.total_kembalian AS rupiah,
            'TUNAI'::text AS transaksi,
            'PEMBAYARAN RESEP BEBAS'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            NULL::text AS no_kartu,
            penjualanresep_t.ruangan_id,
            NULL::character varying AS deskripsi,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama
           FROM penjualanresep_t
             JOIN ( SELECT d.penjualanresep_id,
                    d.pembayaran_id,
                    d.no_pembayaran
                   FROM pembayaranpelayanan_t d) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
             JOIN ( SELECT d.pembayaran_id,
                    d.total_tunai,
                    d.total_kembalian,
                    d.no_pembayaran
                   FROM pembayaran_t d
                  WHERE d.total_tunai <> 0::double precision AND d.is_deleted IS FALSE) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT d.pembayaran_id,
                    d.closingkasir_id
                   FROM tandabuktibayar_t d) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT d.closingkasir_id,
                    d.ruangan_id,
                    d.tgl_closingkasir,
                    d.created_by
                   FROM closingkasir_t d
                  WHERE d.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT d.penjamin_id,
                    d.penjamin_nama,
                    d.carabayar_id
                   FROM penjamin_m d) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT d.carabayar_id,
                    d.carabayar_nama
                   FROM carabayar_m d) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
          WHERE penjualanresep_t.pendaftaran_id IS NULL
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            pembayaranmetode_t.total_dibayar AS rupiah,
            'NON TUNAI'::text AS transaksi,
            ('PEMBAYARAN RESEP BEBAS'::text || ' - '::text) || pembayaranmetode_t.metode_bayar::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            jenisnontunai_m.bank_id,
            pembayaranmetode_t.no_kartu,
            penjualanresep_t.ruangan_id,
            NULL::character varying AS deskripsi,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama
           FROM penjualanresep_t
             JOIN ( SELECT e.penjualanresep_id,
                    e.pembayaran_id,
                    e.no_pembayaran
                   FROM pembayaranpelayanan_t e) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
             JOIN ( SELECT e.pembayaran_id,
                    e.total_nontunai,
                    e.no_pembayaran
                   FROM pembayaran_t e
                  WHERE e.total_nontunai <> 0::double precision AND e.is_deleted IS FALSE) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT e.pembayaran_id,
                    e.closingkasir_id
                   FROM tandabuktibayar_t e) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT e.closingkasir_id,
                    e.ruangan_id,
                    e.tgl_closingkasir,
                    e.created_by
                   FROM closingkasir_t e
                  WHERE e.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT e.penjamin_id,
                    e.penjamin_nama,
                    e.carabayar_id
                   FROM penjamin_m e) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT e.carabayar_id,
                    e.carabayar_nama
                   FROM carabayar_m e) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT e.pembayaran_id,
                    e.jenisnontunai_id,
                    e.total_dibayar,
                    e.metode_bayar,
                    e.no_kartu
                   FROM pembayaranmetode_t e) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT e.jenisnontunai_id,
                    e.bank_id
                   FROM jenisnontunai_m e) jenisnontunai_m ON pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE penjualanresep_t.pendaftaran_id IS NULL
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaran_t.no_pembayaran AS no_kwitansi,
            penjualanresep_t.noresep AS no_registrasi,
            penjualanresep_t.nama_pembeli AS info_pasien,
            COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS rupiah,
            'PENJAMIN'::text AS transaksi,
            'PEMBAYARAN RESEP BEBAS'::text AS keterangan,
            carabayar_m.carabayar_nama AS cara_bayar,
            pembayaran_penjamin.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            penjualanresep_t.ruangan_id,
            NULL::character varying AS deskripsi,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama
           FROM penjualanresep_t
             JOIN ( SELECT f.penjualanresep_id,
                    f.pembayaran_id,
                    f.no_pembayaran
                   FROM pembayaranpelayanan_t f) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
             JOIN ( SELECT f.pembayaran_id,
                    f.pemberianpiutang_id,
                    f.total_dijamin,
                    f.no_pembayaran
                   FROM pembayaran_t f
                  WHERE f.is_deleted IS FALSE AND f.total_dijamin <> 0::double precision OR f.pemberianpiutang_id IS NOT NULL) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT f.pembayaran_id,
                    f.closingkasir_id
                   FROM tandabuktibayar_t f) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
             JOIN ( SELECT f.closingkasir_id,
                    f.ruangan_id,
                    f.tgl_closingkasir,
                    f.created_by
                   FROM closingkasir_t f
                  WHERE f.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT f.penjamin_id,
                    f.penjamin_nama,
                    f.carabayar_id
                   FROM penjamin_m f) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT f.carabayar_id,
                    f.carabayar_nama
                   FROM carabayar_m f) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT f.pemberianpiutang_id,
                    f.total_piutang
                   FROM pemberianpiutang_t f) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
             LEFT JOIN ( SELECT f.pembayaran_id,
                    f.no_kartu
                   FROM pembayaranmetode_t f) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    string_agg(penjamin_m_1.penjamin_nama::text, ', '::text) AS penjamin_nama
                   FROM pembayaranpelayanan_t a
                     JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m_1 ON a.penjamin_id = penjamin_m_1.penjamin_id
                  GROUP BY a.pembayaran_id) pembayaran_penjamin ON pembayaran_t.pembayaran_id = pembayaran_penjamin.pembayaran_id
          WHERE penjualanresep_t.pendaftaran_id IS NULL
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            bayaruangmuka_t.no_uangmuka AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
                CASE
                    WHEN bayaruangmuka_t.metode_pembayaran = 27 THEN tandabuktibayar_t.uangditerima
                    WHEN bayaruangmuka_t.metode_pembayaran = 28 THEN tandabuktibayar_t.uangditerima
                    ELSE 0::double precision
                END AS rupiah,
                CASE
                    WHEN bayaruangmuka_t.metode_pembayaran = 27 THEN 'TUNAI'::text
                    WHEN bayaruangmuka_t.metode_pembayaran = 28 THEN 'NON TUNAI'::text
                    ELSE 'PENJAMIN'::text
                END AS transaksi,
                CASE
                    WHEN bayaruangmuka_t.metode_pembayaran = 27 THEN 'UANG MASUK'::text
                    WHEN bayaruangmuka_t.metode_pembayaran = 28 THEN ('UANG MASUK'::text || ' - '::text) || jenisnontunai_m.nama::text
                    ELSE NULL::text
                END AS keterangan,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_pendaftaran.carabayar_nama
                    ELSE carabayar_admisi.carabayar_nama
                END AS cara_bayar,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_pendaftaran.penjamin_nama
                    ELSE penjamin_admisi.penjamin_nama
                END AS penjamin,
            closingkasir_t.created_by,
            jenisnontunai_m.bank_id,
            NULL::character varying AS no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM bayaruangmuka_t
             JOIN ( SELECT g.bayaruangmuka_id,
                    g.closingkasir_id,
                    g.uangditerima
                   FROM tandabuktibayar_t g
                  WHERE g.is_deleted = false AND g.bayaruangmuka_id IS NOT NULL) tandabuktibayar_t ON bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
             JOIN ( SELECT g.closingkasir_id,
                    g.ruangan_id,
                    g.tgl_closingkasir,
                    g.created_by
                   FROM closingkasir_t g
                  WHERE g.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT g.pendaftaran_id,
                    g.pasien_id,
                    g.pasienadmisi_id,
                    g.carabayar_id,
                    g.penjamin_id,
                    g.no_pendaftaran,
                    g.ruangan_id
                   FROM pendaftaran_t g) pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT g.pasienadmisi_id,
                    g.carabayar_id,
                    g.penjamin_id,
                    g.ruangan_id,
                    g.kelaspelayanan_id
                   FROM pasienadmisi_t g) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT g.carabayar_id,
                    g.carabayar_nama
                   FROM carabayar_m g) carabayar_pendaftaran ON pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id
             LEFT JOIN ( SELECT g.carabayar_id,
                    g.carabayar_nama
                   FROM carabayar_m g) carabayar_admisi ON pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id
             LEFT JOIN ( SELECT g.penjamin_id,
                    g.penjamin_nama
                   FROM penjamin_m g) penjamin_pendaftaran ON pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id
             LEFT JOIN ( SELECT g.penjamin_id,
                    g.penjamin_nama
                   FROM penjamin_m g) penjamin_admisi ON pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id
             LEFT JOIN ( SELECT g.jenisnontunai_id,
                    g.nama,
                    g.bank_id
                   FROM jenisnontunai_m g) jenisnontunai_m ON bayaruangmuka_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembatalanuangmuka_t.no_uangmuka AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien AS info_pasien,
                CASE
                    WHEN pembatalanuangmuka_t.metode_pembayaran = 27 THEN - pembatalanuangmuka_t.jmlkaskeluarbatal
                    WHEN pembatalanuangmuka_t.metode_pembayaran = 28 THEN - pembatalanuangmuka_t.jmlkaskeluarbatal
                    ELSE 0::double precision
                END AS rupiah,
                CASE
                    WHEN pembatalanuangmuka_t.metode_pembayaran = 27 THEN 'TUNAI'::text
                    WHEN pembatalanuangmuka_t.metode_pembayaran = 28 THEN 'NON TUNAI'::text
                    ELSE 'PENJAMIN'::text
                END AS transaksi,
                CASE
                    WHEN pembatalanuangmuka_t.metode_pembayaran = 27 THEN 'BATAL UANG MUKA'::text
                    WHEN pembatalanuangmuka_t.metode_pembayaran = 28 THEN ('BATAL UANG MUKA'::text || ' - '::text) || jenisnontunai_m.nama::text
                    ELSE NULL::text
                END AS keterangan,
            pendaftaran_t.carabayar_nama AS cara_bayar,
            pendaftaran_t.penjamin_nama AS penjamin,
            closingkasir_t.created_by,
            jenisnontunai_m.bank_id,
            NULL::character varying AS no_kartu,
            pendaftaran_t.ruangan_id,
            NULL::character varying AS deskripsi,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.kelaspelayanan_nama
           FROM closingkasir_t
             JOIN ( SELECT a.tgl_buktikeluar,
                    a.closingkasir_id,
                    a.uang_diterima,
                    a.pembatalanuangmuka_id
                   FROM tandabuktikeluar_t a) tandabuktikeluar_t ON closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id
             JOIN ( SELECT b.pembatalanuangmuka_id,
                    b.bayaruangmuka_id,
                    bayaruangmuka_t.pendaftaran_id,
                    b.jmlkaskeluarbatal,
                    b.tglpembatalan,
                    b.keterangan_batal,
                    bayaruangmuka_t.metode_pembayaran,
                    bayaruangmuka_t.ruangan_id,
                    bayaruangmuka_t.no_uangmuka,
                    bayaruangmuka_t.jenisnontunai_id
                   FROM pembatalanuangmuka_t b
                     JOIN ( SELECT a.bayaruangmuka_id,
                            a.pendaftaran_id,
                            a.metode_pembayaran,
                            a.no_uangmuka,
                            a.ruangan_id,
                            a.jenisnontunai_id
                           FROM bayaruangmuka_t a) bayaruangmuka_t ON b.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id) pembatalanuangmuka_t ON tandabuktikeluar_t.pembatalanuangmuka_id = pembatalanuangmuka_t.pembatalanuangmuka_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.penjamin_id,
                    a.carabayar_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    carabayar_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    COALESCE(pasienadmisi_t.ruangan_id, a.ruangan_id) AS ruangan_id
                   FROM pendaftaran_t a
                     LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                            a_1.kelaspelayanan_id,
                            a_1.carabayar_id,
                            a_1.penjamin_id,
                            a_1.ruangan_id
                           FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ( SELECT a_1.kelaspelayanan_id,
                            a_1.kelaspelayanan_nama
                           FROM kelaspelayanan_m a_1) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
                     LEFT JOIN ( SELECT a_1.carabayar_id,
                            a_1.carabayar_nama
                           FROM carabayar_m a_1) carabayar_m ON COALESCE(pasienadmisi_t.carabayar_id, a.carabayar_id) = carabayar_m.carabayar_id
                     LEFT JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, a.penjamin_id) = penjamin_m.penjamin_id) pendaftaran_t(pendaftaran_id, penjamin_id, carabayar_id, no_pendaftaran, pasien_id, tgl_pendaftaran, kelaspelayanan_id, kelaspelayanan_nama, carabayar_id_1, carabayar_nama, penjamin_id_1, penjamin_nama, ruangan_id) ON pembatalanuangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_nama,
                    a.instalasi_id
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.shift_id,
                    a.shift_nama
                   FROM shift_m a) shift_m ON closingkasir_t.shift_id = shift_m.shift_id
             LEFT JOIN ( SELECT string_agg(concat(jenisnontunai_m_1.tipe_pembayaran::text, '=-', bayaruangmuka_t.jumlah_uangmuka::text), ','::text) AS metode_pembayaran,
                    ''::text AS metode_pembayaran_nama,
                    bayaruangmuka_t.bayaruangmuka_id
                   FROM bayaruangmuka_t
                     JOIN ( SELECT a.jenisnontunai_id,
                            a.tipe_pembayaran
                           FROM jenisnontunai_m a) jenisnontunai_m_1 ON bayaruangmuka_t.jenisnontunai_id = jenisnontunai_m_1.jenisnontunai_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lkp_tipe ON jenisnontunai_m_1.tipe_pembayaran = lkp_tipe.lookup_id
                  GROUP BY bayaruangmuka_t.bayaruangmuka_id) pembayaranmetode ON pembatalanuangmuka_t.bayaruangmuka_id = pembayaranmetode.bayaruangmuka_id
             LEFT JOIN ( SELECT g.jenisnontunai_id,
                    g.nama,
                    g.bank_id
                   FROM jenisnontunai_m g) jenisnontunai_m ON pembatalanuangmuka_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir::timestamp with time zone, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaranpiutang_t.no_pembayaranpiutang AS no_kwitansi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
                CASE
                    WHEN pemberianpiutang_t.penjualanresep_id IS NULL THEN pasien_m.nama_pasien
                    WHEN pemberianpiutang_t.pendaftaran_id IS NULL THEN penjualanresep_t.nama_pembeli
                    ELSE NULL::character varying
                END AS info_pasien,
                CASE
                    WHEN pembayaranpiutang_t.metode_pembayaran = 27 THEN pembayaranpiutang_t.total_bayarpiutang
                    WHEN pembayaranpiutang_t.metode_pembayaran = 28 THEN pembayaranpiutang_t.total_bayarpiutang
                    ELSE 0::double precision
                END AS rupiah,
                CASE
                    WHEN pembayaranpiutang_t.metode_pembayaran = 27 THEN 'TUNAI'::text
                    WHEN pembayaranpiutang_t.metode_pembayaran = 28 THEN 'NON TUNAI'::text
                    ELSE 'PENJAMIN'::text
                END AS transaksi,
                CASE
                    WHEN pembayaranpiutang_t.metode_pembayaran = 27 THEN 'PEMBAYARAN PIUTANG'::text
                    WHEN pembayaranpiutang_t.metode_pembayaran = 28 THEN ('PEMBAYARAN PIUTANG'::text || ' - '::text) || jenisnontunai_m.nama::text
                    ELSE NULL::text
                END AS keterangan,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_m.carabayar_nama
                    ELSE carabayar_resep.carabayar_nama
                END AS cara_bayar,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_m.penjamin_nama
                    ELSE penjamin_resep.penjamin_nama
                END AS penjamin,
            closingkasir_t.created_by,
            jenisnontunai_m.bank_id,
            NULL::character varying AS no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM pembayaranpiutang_t
             JOIN ( SELECT h.pembayaranpiutang_id,
                    h.closingkasir_id
                   FROM tandabuktibayar_t h
                  WHERE h.is_deleted = false) tandabuktibayar_t ON pembayaranpiutang_t.pembayaranpiutang_id = tandabuktibayar_t.pembayaranpiutang_id
             JOIN ( SELECT h.closingkasir_id,
                    h.ruangan_id,
                    h.tgl_closingkasir,
                    h.created_by
                   FROM closingkasir_t h
                  WHERE h.is_deleted IS FALSE) closingkasir_t ON tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT h.pemberianpiutang_id,
                    h.pendaftaran_id,
                    h.penjualanresep_id
                   FROM pemberianpiutang_t h) pemberianpiutang_t ON pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
             LEFT JOIN ( SELECT h.pendaftaran_id,
                    h.no_pendaftaran,
                    h.pasien_id,
                    h.carabayar_id,
                    h.penjamin_id,
                    h.pasienadmisi_id,
                    h.ruangan_id
                   FROM pendaftaran_t h) pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT h.pasienadmisi_id,
                    h.ruangan_id,
                    h.kelaspelayanan_id
                   FROM pasienadmisi_t h) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT h.pasien_id,
                    h.nama_pasien
                   FROM pasien_m h) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT h.penjualanresep_id,
                    h.carabayar_id,
                    h.penjamin_id,
                    h.nama_pembeli
                   FROM penjualanresep_t h) penjualanresep_t ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT h.carabayar_id,
                    h.carabayar_nama
                   FROM carabayar_m h) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT h.carabayar_id,
                    h.carabayar_nama
                   FROM carabayar_m h) carabayar_resep ON penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id
             LEFT JOIN ( SELECT h.penjamin_id,
                    h.penjamin_nama
                   FROM penjamin_m h) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT h.penjamin_id,
                    h.penjamin_nama
                   FROM penjamin_m h) penjamin_resep ON penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id
             LEFT JOIN ( SELECT h.jenisnontunai_id,
                    h.nama,
                    h.bank_id
                   FROM jenisnontunai_m h) jenisnontunai_m ON pembayaranpiutang_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        UNION ALL
         SELECT
                CASE
                    WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN closing_bayar.ruangan_id
                    WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN closing_keluar.ruangan_id
                    ELSE NULL::integer
                END AS kasir,
                CASE
                    WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN to_char(closing_bayar.tgl_closingkasir, 'YYYY-MM-DD'::text)::date
                    WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN to_char(closing_keluar.tgl_closingkasir, 'YYYY-MM-DD'::text)::date
                    ELSE NULL::date
                END AS tanggal,
            pembayarantransaksi_t.no_transaksi AS no_kwitansi,
            NULL::character varying AS no_registrasi,
                CASE
                    WHEN pembayarantransaksi_t.tipe_transaksi = 700 THEN supplier_m.supplier_nama
                    WHEN pembayarantransaksi_t.tipe_transaksi = 701 THEN pegawai_m.nama_pegawai
                    WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien_m.nama_pasien
                    ELSE NULL::character varying
                END AS info_pasien,
                CASE
                    WHEN pembayarantransaksi_t.metode_pembayaran = 27 AND pembayarantransaksi_t.jenis_transaksi = 668 THEN pembayarantransaksi_t.jumlah
                    WHEN pembayarantransaksi_t.metode_pembayaran = 27 AND pembayarantransaksi_t.jenis_transaksi = 669 THEN - pembayarantransaksi_t.jumlah
                    WHEN pembayarantransaksi_t.metode_pembayaran = 28 AND pembayarantransaksi_t.jenis_transaksi = 668 THEN pembayarantransaksi_t.jumlah
                    WHEN pembayarantransaksi_t.metode_pembayaran = 28 AND pembayarantransaksi_t.jenis_transaksi = 669 THEN - pembayarantransaksi_t.jumlah
                    ELSE 0::double precision
                END AS rupiah,
                CASE
                    WHEN pembayarantransaksi_t.metode_pembayaran = 27 THEN 'TUNAI'::text
                    WHEN pembayarantransaksi_t.metode_pembayaran = 28 THEN 'NON TUNAI'::text
                    ELSE NULL::text
                END AS transaksi,
                CASE
                    WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN 'PENERIMAAN'::text
                    WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN 'PENGELUARAN'::text
                    ELSE NULL::text
                END AS keterangan,
            NULL::character varying AS cara_bayar,
            NULL::character varying AS penjamin,
                CASE
                    WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN closing_bayar.created_by
                    WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN closing_keluar.created_by
                    ELSE NULL::integer
                END AS created_by,
            NULL::integer AS bank_id,
            NULL::character varying AS no_kartu,
            NULL::integer AS ruangan_id,
            pembayarantransaksi_t.deskripsi,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama
           FROM pembayarantransaksi_t
             LEFT JOIN ( SELECT i.penerimaanumum_id,
                    i.closingkasir_id
                   FROM tandabuktibayar_t i
                  WHERE i.closingkasir_id IS NOT NULL) penerimaan ON pembayarantransaksi_t.pembayarantransaksi_id = penerimaan.penerimaanumum_id
             LEFT JOIN ( SELECT i.pembayarantransaksi_id,
                    i.closingkasir_id
                   FROM tandabuktikeluar_t i
                  WHERE i.closingkasir_id IS NOT NULL) pengeluaran ON pembayarantransaksi_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id
             LEFT JOIN ( SELECT i.closingkasir_id,
                    i.ruangan_id,
                    i.tgl_closingkasir,
                    i.created_by
                   FROM closingkasir_t i) closing_bayar ON penerimaan.closingkasir_id = closing_bayar.closingkasir_id
             LEFT JOIN ( SELECT i.closingkasir_id,
                    i.ruangan_id,
                    i.tgl_closingkasir,
                    i.created_by
                   FROM closingkasir_t i) closing_keluar ON pengeluaran.closingkasir_id = closing_keluar.closingkasir_id
             LEFT JOIN ( SELECT i.pasien_id,
                    i.nama_pasien
                   FROM pasien_m i) pasien_m ON pembayarantransaksi_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT i.pegawai_id,
                    i.nama_pegawai
                   FROM pegawai_m i) pegawai_m ON pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT i.supplier_id,
                    i.supplier_nama
                   FROM supplier_m i) supplier_m ON pembayarantransaksi_t.supplier_id = supplier_m.supplier_id
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            returbayarpelayanan_t.no_returbayar AS no_kwitansi,
                CASE
                    WHEN pembayaranpelayanan_t.pendaftaran_id IS NOT NULL THEN pendaftaran_t.no_pendaftaran
                    WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN penjualanresep_t.noresep
                    ELSE NULL::character varying
                END AS no_registrasi,
                CASE
                    WHEN pembayaranpelayanan_t.pendaftaran_id IS NOT NULL THEN pasien_m.nama_pasien
                    WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN penjualanresep_t.nama_pembeli
                    ELSE NULL::character varying
                END AS info_pasien,
            - returbayarpelayanan_t.total_biayaretur AS rupiah,
            'TUNAI'::text AS transaksi,
            'RETUR PEMBAYARAN NON MULTY'::text AS keterangan,
            NULL::character varying AS cara_bayar,
            NULL::character varying AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM returbayarpelayanan_t
             JOIN ( SELECT j.returbayarpelayanan_id,
                    j.closingkasir_id
                   FROM tandabuktikeluar_t j
                  WHERE j.is_deleted = false) tandabuktikeluar_t ON returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id
             JOIN ( SELECT j.closingkasir_id,
                    j.ruangan_id,
                    j.tgl_closingkasir,
                    j.created_by
                   FROM closingkasir_t j
                  WHERE j.is_deleted IS FALSE) closingkasir_t ON tandabuktikeluar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT j.tandabuktibayar_id
                   FROM tandabuktibayar_t j) tandabuktibayar_t ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
             JOIN ( SELECT j.tandabuktibayar_id,
                    j.pendaftaran_id,
                    j.penjualanresep_id,
                    j.pembayaran_id
                   FROM pembayaranpelayanan_t j) pembayaranpelayanan_t ON tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id
             LEFT JOIN ( SELECT j.pendaftaran_id,
                    j.pasien_id,
                    j.no_pendaftaran,
                    j.pasienadmisi_id,
                    j.ruangan_id
                   FROM pendaftaran_t j) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT j.pasienadmisi_id,
                    j.ruangan_id,
                    j.kelaspelayanan_id
                   FROM pasienadmisi_t j) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT j.pasien_id,
                    j.nama_pasien
                   FROM pasien_m j) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT j.penjualanresep_id,
                    j.noresep,
                    j.nama_pembeli
                   FROM penjualanresep_t j) penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT j.pembayaran_id,
                    j.no_kartu
                   FROM pembayaranmetode_t j) pembayaranmetode_t ON pembayaranpelayanan_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
          WHERE returbayarpelayanan_t.total_biayaretur <> 0::double precision
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            returbayarpelayanan_t.no_returbayar AS no_kwitansi,
                CASE
                    WHEN pembayaranpelayanan_t.pendaftaran_id IS NOT NULL THEN pendaftaran_t.no_pendaftaran
                    WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN penjualanresep_t.noresep
                    ELSE NULL::character varying
                END AS no_registrasi,
                CASE
                    WHEN pembayaranpelayanan_t.pendaftaran_id IS NOT NULL THEN pasien_m.nama_pasien
                    WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN penjualanresep_t.nama_pembeli
                    ELSE NULL::character varying
                END AS info_pasien,
            - returbayarpelayanan_t.total_nontunai AS rupiah,
            'NON TUNAI'::text AS transaksi,
            'RETUR PEMBAYARAN NON MULTY'::text AS keterangan,
            NULL::character varying AS cara_bayar,
            NULL::character varying AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM returbayarpelayanan_t
             JOIN ( SELECT k.returbayarpelayanan_id,
                    k.closingkasir_id
                   FROM tandabuktikeluar_t k
                  WHERE k.is_deleted = false) tandabuktikeluar_t ON returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id
             JOIN ( SELECT k.closingkasir_id,
                    k.ruangan_id,
                    k.tgl_closingkasir,
                    k.created_by
                   FROM closingkasir_t k
                  WHERE k.is_deleted IS FALSE) closingkasir_t ON tandabuktikeluar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT k.tandabuktibayar_id
                   FROM tandabuktibayar_t k) tandabuktibayar_t ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
             JOIN ( SELECT k.tandabuktibayar_id,
                    k.pendaftaran_id,
                    k.penjualanresep_id,
                    k.pembayaran_id
                   FROM pembayaranpelayanan_t k) pembayaranpelayanan_t ON tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id
             LEFT JOIN ( SELECT k.pendaftaran_id,
                    k.no_pendaftaran,
                    k.pasien_id,
                    k.pasienadmisi_id,
                    k.ruangan_id
                   FROM pendaftaran_t k) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT k.pasienadmisi_id,
                    k.ruangan_id,
                    k.kelaspelayanan_id
                   FROM pasienadmisi_t k) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT k.pasien_id,
                    k.nama_pasien
                   FROM pasien_m k) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT k.penjualanresep_id,
                    k.noresep,
                    k.nama_pembeli
                   FROM penjualanresep_t k) penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT k.pembayaran_id,
                    k.no_kartu
                   FROM pembayaranmetode_t k) pembayaranmetode_t ON pembayaranpelayanan_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
          WHERE returbayarpelayanan_t.total_nontunai <> 0::double precision
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            returbayarpelayanan_t.no_returbayar AS no_kwitansi,
                CASE
                    WHEN pembayaranpelayanan_t.pendaftaran_id IS NOT NULL THEN pendaftaran_t.no_pendaftaran
                    WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN penjualanresep_t.noresep
                    ELSE NULL::character varying
                END AS no_registrasi,
                CASE
                    WHEN pembayaranpelayanan_t.pendaftaran_id IS NOT NULL THEN pasien_m.nama_pasien
                    WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN penjualanresep_t.nama_pembeli
                    ELSE NULL::character varying
                END AS info_pasien,
            - returbayarpelayanan_t.total_biayaretur AS rupiah,
            'TUNAI'::text AS transaksi,
            'RETUR PEMBAYARAN'::text AS keterangan,
            NULL::character varying AS cara_bayar,
            NULL::character varying AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM returbayarpelayanan_t
             JOIN ( SELECT j.returbayarpelayanan_id,
                    j.closingkasir_id
                   FROM tandabuktikeluar_t j
                  WHERE j.is_deleted = false) tandabuktikeluar_t ON returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id
             JOIN ( SELECT j.closingkasir_id,
                    j.ruangan_id,
                    j.tgl_closingkasir,
                    j.created_by
                   FROM closingkasir_t j
                  WHERE j.is_deleted IS FALSE) closingkasir_t ON tandabuktikeluar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT j.tandabuktibayar_id,
                    j.pembayaran_id
                   FROM tandabuktibayar_t j) tandabuktibayar_t ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
             JOIN ( SELECT a.pembayaran_id
                   FROM pembayaran_t a) pembayaran_t ON tandabuktibayar_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT j.tandabuktibayar_id,
                    j.pendaftaran_id,
                    j.penjualanresep_id,
                    j.pembayaran_id
                   FROM pembayaranpelayanan_t j) pembayaranpelayanan_t ON pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
             LEFT JOIN ( SELECT j.pendaftaran_id,
                    j.pasien_id,
                    j.no_pendaftaran,
                    j.pasienadmisi_id,
                    j.ruangan_id
                   FROM pendaftaran_t j) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT j.pasienadmisi_id,
                    j.ruangan_id,
                    j.kelaspelayanan_id
                   FROM pasienadmisi_t j) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT j.pasien_id,
                    j.nama_pasien
                   FROM pasien_m j) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT j.penjualanresep_id,
                    j.noresep,
                    j.nama_pembeli
                   FROM penjualanresep_t j) penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT j.pembayaran_id,
                    j.no_kartu
                   FROM pembayaranmetode_t j) pembayaranmetode_t ON pembayaranpelayanan_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
          WHERE returbayarpelayanan_t.total_biayaretur <> 0::double precision
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            returbayarpelayanan_t.no_returbayar AS no_kwitansi,
                CASE
                    WHEN pembayaranpelayanan_t.pendaftaran_id IS NOT NULL THEN pendaftaran_t.no_pendaftaran
                    WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN penjualanresep_t.noresep
                    ELSE NULL::character varying
                END AS no_registrasi,
                CASE
                    WHEN pembayaranpelayanan_t.pendaftaran_id IS NOT NULL THEN pasien_m.nama_pasien
                    WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN penjualanresep_t.nama_pembeli
                    ELSE NULL::character varying
                END AS info_pasien,
            - returbayarpelayanan_t.total_nontunai AS rupiah,
            'NON TUNAI'::text AS transaksi,
            'RETUR PEMBAYARAN'::text AS keterangan,
            NULL::character varying AS cara_bayar,
            NULL::character varying AS penjamin,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            pembayaranmetode_t.no_kartu,
            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) AS ruangan_id,
            NULL::character varying AS deskripsi,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM returbayarpelayanan_t
             JOIN ( SELECT k.returbayarpelayanan_id,
                    k.closingkasir_id
                   FROM tandabuktikeluar_t k
                  WHERE k.is_deleted = false) tandabuktikeluar_t ON returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id
             JOIN ( SELECT k.closingkasir_id,
                    k.ruangan_id,
                    k.tgl_closingkasir,
                    k.created_by
                   FROM closingkasir_t k
                  WHERE k.is_deleted IS FALSE) closingkasir_t ON tandabuktikeluar_t.closingkasir_id = closingkasir_t.closingkasir_id
             JOIN ( SELECT j.tandabuktibayar_id,
                    j.pembayaran_id
                   FROM tandabuktibayar_t j) tandabuktibayar_t ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
             JOIN ( SELECT a.pembayaran_id
                   FROM pembayaran_t a) pembayaran_t ON tandabuktibayar_t.pembayaran_id = pembayaran_t.pembayaran_id
             JOIN ( SELECT j.tandabuktibayar_id,
                    j.pendaftaran_id,
                    j.penjualanresep_id,
                    j.pembayaran_id
                   FROM pembayaranpelayanan_t j) pembayaranpelayanan_t ON pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
             LEFT JOIN ( SELECT k.pendaftaran_id,
                    k.no_pendaftaran,
                    k.pasien_id,
                    k.pasienadmisi_id,
                    k.ruangan_id
                   FROM pendaftaran_t k) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT k.pasienadmisi_id,
                    k.ruangan_id,
                    k.kelaspelayanan_id
                   FROM pasienadmisi_t k) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT k.pasien_id,
                    k.nama_pasien
                   FROM pasien_m k) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT k.penjualanresep_id,
                    k.noresep,
                    k.nama_pembeli
                   FROM penjualanresep_t k) penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT k.pembayaran_id,
                    k.no_kartu
                   FROM pembayaranmetode_t k) pembayaranmetode_t ON pembayaranpelayanan_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
          WHERE returbayarpelayanan_t.total_nontunai <> 0::double precision
        UNION ALL
         SELECT closingkasir_t.ruangan_id AS kasir,
            to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text)::date AS tanggal,
            pembayaranpiutang_t.no_pembayaranpiutang AS no_kwitansi,
            COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_registrasi,
                CASE
                    WHEN pemberianpiutang_t.pendaftaran_id IS NULL THEN penjualanresep_t.nama_pembeli
                    ELSE pasien_m.nama_pasien
                END AS info_pasien,
            tandabuktibayar_t.jmlpembayaran AS rupiah,
                CASE
                    WHEN pembayaranpiutang_t.metode_pembayaran = 27 THEN 'TUNAI'::text
                    ELSE 'NON TUNAI'::text
                END AS transaksi,
            'PEMBAYARAN PIUTANG'::text AS keterangan,
            COALESCE(pendaftaran_t.carabayar_nama, carabayar_resep.carabayar_nama) AS carabayar_nama,
            COALESCE(pendaftaran_t.penjamin_nama, penjamin_resep.penjamin_nama) AS penjamin_nama,
            closingkasir_t.created_by,
            NULL::integer AS bank_id,
            NULL::text AS no_kartu,
            pendaftaran_t.ruangan_id,
            NULL::character varying AS deskripsi,
            COALESCE(pendaftaran_t.kelaspelayanan_id, kelaspelayanan_resep.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(pendaftaran_t.kelaspelayanan_nama, kelaspelayanan_resep.kelaspelayanan_nama) AS kelaspelayanan_nama
           FROM closingkasir_t
             JOIN ( SELECT a.jmlpembayaran,
                    a.uangditerima,
                    a.closingkasir_id,
                    a.pembayaranpiutang_id,
                    a.tglbuktibayar
                   FROM tandabuktibayar_t a) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
             JOIN ( SELECT a.metode_pembayaran,
                    a.total_bayarpiutang,
                    a.no_pembayaranpiutang,
                    a.pembayaranpiutang_id,
                    a.pemberianpiutang_id
                   FROM pembayaranpiutang_t a) pembayaranpiutang_t ON tandabuktibayar_t.pembayaranpiutang_id = pembayaranpiutang_t.pembayaranpiutang_id
             JOIN ( SELECT a.pemberianpiutang_id,
                    a.pendaftaran_id,
                    a.penjualanresep_id,
                    a.no_pemberianpiutang
                   FROM pemberianpiutang_t a) pemberianpiutang_t ON pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.penjamin_id,
                    a.carabayar_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    carabayar_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    COALESCE(pasienadmisi_t.ruangan_id, a.ruangan_id) AS ruangan_id
                   FROM pendaftaran_t a
                     LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                            a_1.kelaspelayanan_id,
                            a_1.carabayar_id,
                            a_1.penjamin_id,
                            a_1.ruangan_id
                           FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ( SELECT a_1.kelaspelayanan_id,
                            a_1.kelaspelayanan_nama
                           FROM kelaspelayanan_m a_1) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
                     LEFT JOIN ( SELECT a_1.carabayar_id,
                            a_1.carabayar_nama
                           FROM carabayar_m a_1) carabayar_m ON COALESCE(pasienadmisi_t.carabayar_id, a.carabayar_id) = carabayar_m.carabayar_id
                     LEFT JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, a.penjamin_id) = penjamin_m.penjamin_id) pendaftaran_t(pendaftaran_id, penjamin_id, carabayar_id, no_pendaftaran, pasien_id, tgl_pendaftaran, kelaspelayanan_id, kelaspelayanan_nama, carabayar_id_1, carabayar_nama, penjamin_id_1, penjamin_nama, ruangan_id) ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.penjualanresep_id,
                    a.nama_pembeli,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.noresep
                   FROM penjualanresep_t a) penjualanresep_t ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_resep ON penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_resep ON penjualanresep_t.kelaspelayanan_id = kelaspelayanan_resep.kelaspelayanan_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_resep ON penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_nama,
                    a.instalasi_id
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.shift_id,
                    a.shift_nama
                   FROM shift_m a) shift_m ON closingkasir_t.shift_id = shift_m.shift_id) rekap_kasir
     JOIN ( SELECT loginpemakai.loginpemakai_id,
            loginpemakai.pegawai_id
           FROM loginpemakai_k loginpemakai) loginpemakai_k ON rekap_kasir.created_by = loginpemakai_k.loginpemakai_id
     JOIN ( SELECT pegawai.pegawai_id,
            pegawai.nama_pegawai
           FROM pegawai_m pegawai) pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id
     LEFT JOIN ( SELECT bank.bank_id,
            bank.nama_bank
           FROM bank_m bank) bank_m ON rekap_kasir.bank_id = bank_m.bank_id
     LEFT JOIN ( SELECT ruangan_m.ruangan_id,
            ruangan_m.instalasi_id,
            ruangan_m.ruangan_nama
           FROM ruangan_m) ruangan_akhir ON rekap_kasir.ruangan_id = ruangan_akhir.ruangan_id
     LEFT JOIN ( SELECT instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama
           FROM instalasi_m) instalasi_akhir ON ruangan_akhir.instalasi_id = instalasi_akhir.instalasi_id;