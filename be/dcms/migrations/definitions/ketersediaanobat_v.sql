CREATE OR REPLACE VIEW public.ketersediaanobat_v AS
SELECT
    hit.instalasi_id,
    hit.instalasi_nama,
    hit.ruangan_id,
    hit.ruangan_nama,
    hit.obatalkes_id,
    hit.obatalkes_nama,
    hit.obatalkes_nama AS obatalkes_namalain,
    hit.obatalkes_kode,
    hit.qty_masuk,
    hit.qty_keluar,
    hit.nilai_ro,
    hit.satuanbesar_id,
    hit.satuanbesar_nama,
    hit.satuankecil_id,
    hit.satuankecil_nama,
    hit.satuansedang_id,
    hit.satuansedang_nama,
    hit.jenisobatalkes_id,
    hit.jenisobatalkes_nama,
    hit.group_jenisobat,
    CASE
        WHEN hit.min_stok IS NULL THEN 0::double precision
        ELSE hit.min_stok
    END AS min_stok,
    CASE
        WHEN hit.max_stok IS NULL THEN 0::double precision
        ELSE hit.max_stok
    END AS max_stok,
    hit.total_stok AS qty_stok,
    COALESCE(
        hit.jml_mutasi,
        0::double precision
    ) AS jml_mutasi,
    COALESCE(
        hit.jml_resep_farmasi::double precision,
        0::double precision
    ) AS jml_resep_farmasi,
    COALESCE(
        hit.jml_resep_dokter::double precision,
        0::double precision
    ) AS jml_resep_dokter,
    COALESCE(
        hit.jml_mutasi,
        0::double precision
    ) + COALESCE(
        hit.jml_resep_farmasi::double precision,
        0::double precision
    ) + COALESCE(
        hit.jml_resep_dokter::double precision,
        0::double precision
    ) + COALESCE(
        hit.qty_cssd::bigint,
        0::bigint
    )::double precision + COALESCE(
        hit.qty_udd::double precision,
        0::bigint::real::double precision
    ) AS qty_dipesan,
    hit.total_stok::double precision - (
        COALESCE(
            hit.jml_mutasi,
            0::double precision
        ) + COALESCE(
            hit.jml_resep_farmasi::double precision,
            0::double precision
        ) + COALESCE(
            hit.jml_resep_dokter::double precision,
            0::double precision
        ) + COALESCE(
            hit.qty_cssd::bigint,
            0::bigint
        )::double precision + COALESCE(
            hit.qty_udd::double precision,
            0::bigint::real::double precision
        )
    ) AS qty_tersedia,
    hit.reference_mutasi,
    hit.reference_resep_farmasi,
    hit.reference_udd_detail_t AS reference_udd,
    hit.reference_cssd,
    hit.reference_resep_dokter,
    hit.hargaygdigunakan,
    hit.hargamaksimum,
    hit.hargaminimum,
    hit.hargaratarata,
    hit.hargaterakhir,
    hit.harganetto,
    hit.ven_id,
    hit.ven_name
FROM (
        SELECT
            instalasi_m.instalasi_id, instalasi_m.instalasi_nama, stokobatalkes_r.ruangan_id, ruangan_m.ruangan_nama, stokobatalkes_r.obatalkes_id, obatalkes_m.obatalkes_namalain, obatalkes_m.obatalkes_kode, obatalkes_m.hargajual, obatalkes_m.satuankecil_id, satuan_besar.satuanunit_nama AS satuanbesar_nama, obatalkes_m.satuansedang_id, satuan_sedang.satuanunit_nama AS satuansedang_nama, satuan_kecil.satuanunit_nama AS satuankecil_nama, obatalkes_m.satuanbesar_id, obatalkes_m.jenisobatalkes_id, jenisobatalkes_m.jenisobatalkes_nama, jenisobatalkes_m.group_jenisobat, konfigfarmasi_k.persenppn AS ppn, konfigfarmasi_k.persenmargin AS margin, konfigfarmasi_k.persen_diskon AS disc, konfigfarmasi_k.hargaygdigunakan, obatalkes_m.harganetto, obatalkes_m.hargamaksimum, obatalkes_m.hargaminimum, obatalkes_m.hargaratarata, obatalkes_m.hargaterakhir, stokobatalkes_r.qty_masuk, stokobatalkes_r.qty_keluar, stokobatalkes_r.qty_dipesan, stokobatalkes_r.qty_tersedia, stokobatalkes_r.qty_sisa AS qty_stok, obatalkes_m.obatalkes_nama, obatalkes_m.nilai_ro, konfigrak_m.min_stok, konfigrak_m.max_stok, kartustok.total AS total_stok, (
                SELECT sum(mt.jumlah_mutasi) AS jml
                FROM mutasiobatdetail_t mt
                    LEFT JOIN (
                        SELECT a.ruanganasal_id, a.nomutasioa, a.mutasiobatruangan_id, a.is_deleted, a.status_mutasi
                        FROM mutasiobatruangan_t a
                        WHERE
                            a.status_mutasi = 401
                            AND a.is_deleted = false
                    ) mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id
                WHERE
                    mt.is_deleted IS FALSE
                    AND mt2.ruanganasal_id = stokobatalkes_r.ruangan_id
                    AND mt.obatalkes_id = stokobatalkes_r.obatalkes_id
            ) AS jml_mutasi, round(resepfarmasi.jml::numeric, 3) AS jml_resep_farmasi, round(resepdokter.jml::numeric, 3) AS jml_resep_dokter, (
                SELECT string_agg(
                        mt2.nomutasioa::text, ','::text
                    ) AS reference
                FROM mutasiobatdetail_t mt
                    LEFT JOIN (
                        SELECT a.ruanganasal_id, a.nomutasioa, a.mutasiobatruangan_id, a.is_deleted, a.status_mutasi
                        FROM mutasiobatruangan_t a
                        WHERE
                            a.status_mutasi = 401
                            AND a.is_deleted = false
                    ) mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id
                WHERE
                    mt.is_deleted IS FALSE
                    AND mt2.ruanganasal_id = stokobatalkes_r.ruangan_id
                    AND mt.obatalkes_id = stokobatalkes_r.obatalkes_id
            ) AS reference_mutasi, resepfarmasi.reference AS reference_resep_farmasi, resepdokter.reference AS reference_resep_dokter, obatalkes_m.ven AS ven_id, look_ven.lookup_name AS ven_name, 0 AS qty_cssd, 0 AS qty_udd, NULL::text AS reference_cssd, NULL::text AS reference_udd_detail_t
        FROM
            stokobatalkes_r
            JOIN (
                SELECT a.ruangan_id, a.ruangan_nama, a.instalasi_id
                FROM ruangan_m a
            ) ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
            JOIN (
                SELECT a.instalasi_id, a.instalasi_nama
                FROM instalasi_m a
            ) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
            JOIN (
                SELECT
                    a.obatalkes_id, a.obatalkes_namalain, a.obatalkes_kode, a.hargajual, a.satuankecil_id, a.satuansedang_id, a.satuanbesar_id, a.jenisobatalkes_id, a.harganetto, a.hargamaksimum, a.hargaminimum, a.hargaratarata, a.hargaterakhir, a.obatalkes_nama, a.nilai_ro, a.ven, a.is_active, a.is_deleted
                FROM obatalkes_m a
            ) obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
            LEFT JOIN (
                SELECT a.satuanunit_id, a.satuanunit_nama
                FROM satuanunit_m a
            ) satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
            LEFT JOIN (
                SELECT a.satuanunit_id, a.satuanunit_nama
                FROM satuanunit_m a
            ) satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
            LEFT JOIN (
                SELECT a.satuanunit_id, a.satuanunit_nama
                FROM satuanunit_m a
            ) satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id
            JOIN (
                SELECT a.jenisobatalkes_id, a.jenisobatalkes_nama, a.group_jenisobat
                FROM jenisobatalkes_m a
            ) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
            LEFT JOIN (
                SELECT a.konfigfarmasi_id, a.is_deleted, a.persenppn, a.persenmargin, a.persen_diskon, a.hargaygdigunakan
                FROM konfigfarmasi_k a
            ) konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
            LEFT JOIN (
                SELECT a.stokobatr_id, a.obatalkes_id, a.min_stok, a.max_stok, a.is_deleted
                FROM konfigrak_m a
            ) konfigrak_m ON stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id
            AND stokobatalkes_r.obatalkes_id = konfigrak_m.obatalkes_id
            AND konfigrak_m.is_deleted = false
            LEFT JOIN (
                SELECT st.ruangan_id, st.obatalkes_id, sum(
                        round(st.qtystok_in::numeric, 3) - round(st.qtystok_out::numeric, 3)
                    ) AS total
                FROM stokobatalkes_t st
                WHERE
                    st.is_deleted = false
                GROUP BY
                    st.ruangan_id, st.obatalkes_id
            ) kartustok ON kartustok.ruangan_id = stokobatalkes_r.ruangan_id
            AND kartustok.obatalkes_id = stokobatalkes_r.obatalkes_id
            LEFT JOIN (
                SELECT a.ruangan_id, a.obatalkes_id, sum(a.jml) AS jml, string_agg(a.reference::text, ','::text) AS reference
                FROM (
                        SELECT pt.ruangan_id, ot.obatalkes_id, sum(
                                COALESCE(
                                    ot.det_konversi, ot.qty_konversi
                                )
                            ) AS jml, pt.noresep AS reference
                        FROM
                            obatalkespasien_calc ot
                            LEFT JOIN (
                                SELECT a_1.obatalkes_id
                                FROM obatalkes_m a_1
                            ) om ON om.obatalkes_id = ot.obatalkes_id
                            LEFT JOIN (
                                SELECT a_1.ruangan_id, a_1.noresep, a_1.penjualanresep_id, a_1.is_deleted, a_1.status_reseptur, a_1.reseptur_id
                                FROM penjualanresep_calc a_1
                            ) pt ON pt.penjualanresep_id = ot.penjualanresep_id
                        WHERE
                            pt.is_deleted IS FALSE
                            AND pt.status_reseptur <> 660
                            AND pt.status_reseptur <> 432
                        GROUP BY
                            pt.ruangan_id, ot.obatalkes_id, pt.noresep
                    ) a
                WHERE
                    a.jml <> 0::double precision
                GROUP BY
                    a.ruangan_id, a.obatalkes_id
            ) resepfarmasi ON resepfarmasi.ruangan_id = stokobatalkes_r.ruangan_id
            AND resepfarmasi.obatalkes_id = stokobatalkes_r.obatalkes_id
            LEFT JOIN (
                SELECT a.ruangan_id, a.obatalkes_id, sum(a.jml) AS jml, string_agg(a.reference, ','::text) AS reference
                FROM (
                        SELECT
                            rt.ruangan_id, dt.obatalkes_id, sum(
                                COALESCE(
                                    dt.det_konversi, dt.qty_konversi
                                )
                            ) AS jml, CASE
                                WHEN penjualanresep_t.noresep IS NULL THEN rt.noresep::text
                                ELSE NULL::text
                            END AS reference
                        FROM
                            reseptur_t rt
                            JOIN (
                                SELECT a_1.resepturdetail_id, a_1.obatalkes_id, a_1.det_konversi, a_1.qty_konversi, a_1.reseptur_id, a_1.is_deleted
                                FROM resepturdetail_calc a_1
                            ) dt ON dt.reseptur_id = rt.reseptur_id
                            LEFT JOIN (
                                SELECT a_1.penjualanresep_id, a_1.noresep, a_1.reseptur_id
                                FROM penjualanresep_calc a_1
                            ) penjualanresep_t ON rt.reseptur_id = penjualanresep_t.reseptur_id
                        WHERE
                            rt.status_reseptur <> 660
                            AND rt.status_reseptur <> 432
                            AND rt.is_deleted IS FALSE
                            AND dt.is_deleted IS FALSE
                            AND penjualanresep_t.noresep IS NULL
                        GROUP BY
                            rt.ruangan_id, dt.obatalkes_id, rt.noresep, penjualanresep_t.noresep
                    ) a
                WHERE
                    a.jml <> 0::double precision
                GROUP BY
                    a.ruangan_id, a.obatalkes_id
            ) resepdokter ON resepdokter.ruangan_id = stokobatalkes_r.ruangan_id
            AND resepdokter.obatalkes_id = stokobatalkes_r.obatalkes_id
            LEFT JOIN (
                SELECT a.lookup_id, a.lookup_name
                FROM lookup_m a
            ) look_ven ON obatalkes_m.ven = look_ven.lookup_id
        WHERE
            obatalkes_m.is_active = true
            AND obatalkes_m.is_deleted = false
    ) hit;