-- public.infotagihanobat_v source

CREATE OR REPLACE VIEW public.infotagihanobat_v
AS SELECT data.penjualanresep_id,
    data.tglpenjualan,
    data.jenis_penjualan,
    data.pendaftaran_id,
    data.no_pendaftaran,
    data.noresep,
    data.nama_pembeli,
    data.carabayar_id,
    data.carabayar_nama,
    data.penjamin_id,
    data.penjamin_nama,
    data.totalharga_netto,
    data.no_rekam_medik,
    data.jenispenjualan,
    data.status_bayar,
    data.status_bayar_nama,
    data.jasa,
    data.administrasi,
    data.obat,
    data.totalharga_jual,
    data.carabayar_kode_warna,
    data.is_close_bill,
        CASE
            WHEN COALESCE(data.countkronis, 0::bigint) >= 1 THEN true
            ELSE false
        END AS is_kronis
   FROM ( SELECT penjualanresep_t.penjualanresep_id,
            penjualanresep_t.tglpenjualan,
            fgetnamalookup(penjualanresep_t.jenispenjualan::integer) AS jenis_penjualan,
                CASE
                    WHEN gabung_billing.ref_pendaftaran_id IS NOT NULL THEN gabung_billing.ref_pendaftaran_id
                    WHEN gabung_billing.ref_pendaftaran_id IS NULL THEN penjualanresep_t.pendaftaran_id
                    ELSE penjualanresep_t.pendaftaran_id
                END AS pendaftaran_id,
                CASE
                    WHEN gabung_billing.ref_pendaftaran_id IS NOT NULL THEN gabung_billing.ref_no_pendaftaran
                    WHEN gabung_billing.ref_pendaftaran_id IS NULL THEN pendaftaran_t.no_pendaftaran
                    ELSE pendaftaran_t.no_pendaftaran
                END AS no_pendaftaran,
            penjualanresep_t.noresep,
                CASE
                    WHEN pasien_m.nama_pasien IS NOT NULL THEN pasien_m.nama_pasien::text
                    WHEN penjualanresep_t.nama_pembeli IS NOT NULL THEN penjualanresep_t.nama_pembeli::text
                    WHEN karyawan.nama_pegawai IS NOT NULL THEN karyawan.nama_pegawai::text
                    ELSE ''::text
                END AS nama_pembeli,
            penjualanresep_t.carabayar_id,
            carabayar_m.carabayar_nama,
            penjualanresep_t.penjamin_id,
            penjamin_m.penjamin_nama,
            penjualanresep_t.totharganetto AS totalharga_netto,
            pasien_m.no_rekam_medik,
            penjualanresep_t.jenispenjualan,
            penjualanresep_t.status_bayar,
            fgetnamalookup(penjualanresep_t.status_bayar::integer) AS status_bayar_nama,
            COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) AS jasa,
            COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS administrasi,
            COALESCE(penjualanresep_t.totalhargajual, 0::double precision) AS obat,
            COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totalharga_jual,
            carabayar_m.carabayar_kode_warna,
            pendaftaran_t.is_close_bill,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.is_kronis) FILTER (WHERE a.is_kronis IS TRUE) AS count_obat_racikan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countkronis
           FROM penjualanresep_t
             LEFT JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna
                   FROM carabayar_m a) carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.is_close_bill
                   FROM pendaftaran_t a) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.ref_pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran AS ref_no_pendaftaran
                   FROM gabungpelayanandetail_t a
                     LEFT JOIN ( SELECT b.pendaftaran_id,
                            b.no_pendaftaran
                           FROM pendaftaran_t b) pendaftaran_t_1 ON a.ref_pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE a.is_deleted = false) gabung_billing ON penjualanresep_t.pendaftaran_id = gabung_billing.pendaftaran_id
          WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false) data;