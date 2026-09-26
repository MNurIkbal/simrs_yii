-- public.invoicepasienbelumbayar_v source

CREATE OR REPLACE VIEW public.invoicepasienbelumbayar_v
AS SELECT tagihan.ref_pendaftaran_id,
    tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
        CASE
            WHEN tagihan.kelompoktindakan_id = 17 THEN tagihan.tindakan_obat_nama
            WHEN tagihan.is_obat THEN tagihan.tindakan_obat_nama
            WHEN tagihan.kelompoktindakan_id = 28 OR tagihan.kelompoktindakan_id = 18 OR tagihan.kelompoktindakan_id = 59 OR tagihan.kelompoktindakan_id = 64 OR tagihan.kelompoktindakan_id = 6 THEN concat(tagihan.tindakan_obat_nama, '/ ', dokter_dpjp.nama_pegawai)::character varying
            WHEN tagihan.is_akomodasi IS TRUE THEN concat(tagihan.tindakan_obat_nama, '/  ', COALESCE(ruangan_m.ruangan_nama, ''::character varying), ' - ', COALESCE(kelaspelayanan_m.kelaspelayanan_nama, ''::character varying))::character varying
            ELSE tagihan.tindakan_obat_nama
        END AS tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan,
    tagihan.qty,
    tagihan.tarif_cyto,
    COALESCE(infotagihanpasien_r.subtotal, tagihan.sub_total) AS sub_total,
    tagihan.sub_total AS sub_total_origin,
        CASE
            WHEN infotagihanpasien_r.dijamin IS NULL THEN
            CASE
                WHEN carabayar_m.is_penjamin IS TRUE THEN tagihan.sub_total
                ELSE 0::double precision
            END
            ELSE infotagihanpasien_r.dijamin
        END AS sub_total_penjamin,
        CASE
            WHEN infotagihanpasien_r."totalDibayar" IS NULL THEN
            CASE
                WHEN carabayar_m.is_penjamin IS FALSE THEN tagihan.sub_total
                ELSE 0::double precision
            END
            ELSE infotagihanpasien_r."totalDibayar"
        END AS sub_total_pasien,
    infotagihanpasien_r.dijamin_subpayer,
    infotagihanpasien_r.persen_diskon,
        CASE
            WHEN infotagihanpasien_r.nominal_diskon IS NOT NULL THEN
            CASE
                WHEN infotagihanpasien_r.dijamin > 0::double precision THEN infotagihanpasien_r.nominal_diskon
                WHEN infotagihanpasien_r."totalDibayar" = 0::double precision AND infotagihanpasien_r.dijamin = 0::double precision AND carabayar_m.is_penjamin IS TRUE THEN infotagihanpasien_r.nominal_diskon
                WHEN infotagihanpasien_r."totalDibayar" = 0::double precision AND infotagihanpasien_r.dijamin = 0::double precision AND carabayar_m.is_penjamin IS FALSE THEN 0::double precision
                WHEN infotagihanpasien_r."totalDibayar" = 0::double precision AND infotagihanpasien_r.dijamin > 0::double precision THEN infotagihanpasien_r.nominal_diskon
                ELSE 0::double precision
            END
            ELSE 0::double precision
        END AS diskon_penjamin,
        CASE
            WHEN infotagihanpasien_r.nominal_diskon IS NOT NULL THEN
            CASE
                WHEN infotagihanpasien_r."totalDibayar" = 0::double precision AND infotagihanpasien_r.dijamin = 0::double precision AND carabayar_m.is_penjamin IS FALSE THEN infotagihanpasien_r.nominal_diskon
                WHEN infotagihanpasien_r."totalDibayar" = 0::double precision AND infotagihanpasien_r.dijamin = 0::double precision AND carabayar_m.is_penjamin IS TRUE THEN 0::double precision
                WHEN infotagihanpasien_r."totalDibayar" > 0::double precision AND infotagihanpasien_r.dijamin = 0::double precision THEN infotagihanpasien_r.nominal_diskon
                ELSE NULL::double precision
            END
            ELSE 0::double precision
        END AS diskon_pasien,
    COALESCE(infotagihanpasien_r.plafon_payer, 0::double precision) + COALESCE(infotagihanpasien_r.plafon_subpayer, 0::double precision) AS total_plafon,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_pelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.dokterpenanggungjawab_id,
    dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
    pasien_m.pasien_id,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_deleted,
    carabayar_m.groupcarabayar_id,
    tagihan.penjualanresep_id,
    tagihan.is_valid,
    tagihan.is_cyto,
    tagihan.pasienadmisi_id,
    tagihan.implementasi_id,
    tagihan.jeniskasuspenyakit_id,
    tagihan.discount,
    tagihan.tipepaket_id,
    tagihan.kamarruangan_id,
    tagihan.is_akomodasi,
    tagihan.kamartempattidur_id,
    tagihan.additional_data,
    tagihan.is_konsultasi,
    tagihan.tarifpenyulit_tindakan,
    tagihan.is_overwrite,
    tagihan.harga_origin,
    tagihan.cyto_origin,
    tagihan.penyulit_origin,
    lob.lob_id,
    asuransipasien_m.penjamingrade_id,
    NULL::text AS is_ditagihkan,
        CASE
            WHEN tagihan.pasienadmisi_id IS NULL AND tagihan.instalasi_id <> 2 THEN 'RAJAL'::text
            WHEN tagihan.pasienadmisi_id IS NULL AND tagihan.instalasi_id = 2 THEN 'IGD'::text
            ELSE 'RANAP'::text
        END AS pelayanan,
    tagihan.daftartindakan_kode,
    tagihan.kamarruangan_nokamar,
    tagihan.jenisobatalkes_id,
    tagihan.tgl_pelayanan_akhir
   FROM ( SELECT 1 AS tipe,
            pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan_akhir,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            daftartindakan_m.is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            daftartindakan_m.daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            NULL::double precision AS sub_total_penjamin,
            NULL::double precision AS sub_total_pasien,
            NULL::double precision AS dijamin_subpayer,
            NULL::double precision AS persen_diskon,
            NULL::double precision AS diskon_penjamin,
            NULL::double precision AS diskon_pasien,
            NULL::double precision AS total_plafon
           FROM tindakanpelayanan_t
             JOIN ( SELECT b.pendaftaran_id,
                    b.no_pendaftaran,
                    b.tgl_pendaftaran,
                    b.pasien_id,
                    b.penjamin_id,
                    b.jeniskasuspenyakit_id,
                    b.asuransipasien_id,
                    b.instalasi_id,
                    b.pasienadmisi_id
                   FROM pendaftaran_t b) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT b.daftartindakan_id,
                    b.kelompoktindakan_id,
                    b.daftartindakan_nama,
                    b.is_akomodasi,
                    b.is_konsultasi,
                    b.daftartindakan_kode
                   FROM daftartindakan_m b) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT b.kelompoktindakan_id,
                    b.kelompoktindakan_nama
                   FROM kelompoktindakan_m b) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL AND daftartindakan_m.is_akomodasi IS FALSE
        UNION ALL
         SELECT 2 AS tipe,
            gabungtagihan.ref_pendaftaran_id,
            tindakanpelayanan_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan_akhir,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            daftartindakan_m.is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            daftartindakan_m.daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            NULL::double precision AS sub_total_penjamin,
            NULL::double precision AS sub_total_pasien,
            NULL::double precision AS dijamin_subpayer,
            NULL::double precision AS persen_diskon,
            NULL::double precision AS diskon_penjamin,
            NULL::double precision AS diskon_pasien,
            NULL::double precision AS total_plafon
           FROM tindakanpelayanan_t
             JOIN ( SELECT b.pendaftaran_id,
                    b.no_pendaftaran,
                    b.tgl_pendaftaran,
                    b.pasien_id,
                    b.penjamin_id,
                    b.jeniskasuspenyakit_id,
                    b.asuransipasien_id,
                    b.instalasi_id,
                    b.pasienadmisi_id
                   FROM pendaftaran_t b) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT b.daftartindakan_id,
                    b.kelompoktindakan_id,
                    b.daftartindakan_nama,
                    b.is_akomodasi,
                    b.is_konsultasi,
                    b.daftartindakan_kode
                   FROM daftartindakan_m b) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT b.kelompoktindakan_id,
                    b.kelompoktindakan_nama
                   FROM kelompoktindakan_m b) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false) gabungtagihan ON tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL AND daftartindakan_m.is_akomodasi IS FALSE
        UNION ALL
         SELECT 7 AS tipe,
            pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            min(tindakanpelayanan_t.tgl_tindakan) AS tgl_pelayanan,
            max(tindakanpelayanan_t.tgl_tindakan) AS tgl_pelayanan_akhir,
            NULL::integer AS pelayanan_id,
            NULL::integer AS tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            sum(tindakanpelayanan_t.qty_tindakan) AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            sum(COALESCE(infotagihanpasien_r_1.subtotal, tindakanpelayanan_t.tarif_tindakan)) AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            sum(tindakanpelayanan_t.tarif_diskon) AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            daftartindakan_m.is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            NULL::text AS additional_data,
            daftartindakan_m.is_konsultasi,
            sum(tindakanpelayanan_t.tarifpenyulit_tindakan) AS tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            sum(tindakanpelayanan_t.cyto_origin) AS cyto_origin,
            sum(tindakanpelayanan_t.penyulit_origin) AS penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            daftartindakan_m.daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            sum(
                CASE
                    WHEN infotagihanpasien_r_1.dijamin IS NULL THEN
                    CASE
                        WHEN carabayar_m_1.is_penjamin IS TRUE THEN tindakanpelayanan_t.tarif_tindakan
                        ELSE 0::double precision
                    END
                    ELSE infotagihanpasien_r_1.dijamin
                END) AS sub_total_penjamin,
            sum(
                CASE
                    WHEN infotagihanpasien_r_1."totalDibayar" IS NULL THEN
                    CASE
                        WHEN carabayar_m_1.is_penjamin IS FALSE THEN tindakanpelayanan_t.tarif_tindakan
                        ELSE 0::double precision
                    END
                    ELSE infotagihanpasien_r_1."totalDibayar"
                END) AS sub_total_pasien,
            sum(infotagihanpasien_r_1.dijamin_subpayer) AS dijamin_subpayer,
            COALESCE(infotagihanpasien_r_1.persen_diskon, 0::double precision) AS persen_diskon,
            sum(
                CASE
                    WHEN infotagihanpasien_r_1.nominal_diskon IS NOT NULL THEN
                    CASE
                        WHEN infotagihanpasien_r_1.dijamin > 0::double precision THEN infotagihanpasien_r_1.nominal_diskon
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision AND carabayar_m_1.is_penjamin IS TRUE THEN infotagihanpasien_r_1.nominal_diskon
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision AND carabayar_m_1.is_penjamin IS FALSE THEN 0::double precision
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin > 0::double precision THEN infotagihanpasien_r_1.nominal_diskon
                        ELSE 0::double precision
                    END
                    ELSE 0::double precision
                END) AS diskon_penjamin,
            sum(
                CASE
                    WHEN infotagihanpasien_r_1.nominal_diskon IS NOT NULL THEN
                    CASE
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision AND carabayar_m_1.is_penjamin IS FALSE THEN infotagihanpasien_r_1.nominal_diskon
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision AND carabayar_m_1.is_penjamin IS TRUE THEN 0::double precision
                        WHEN infotagihanpasien_r_1."totalDibayar" > 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision THEN infotagihanpasien_r_1.nominal_diskon
                        ELSE NULL::double precision
                    END
                    ELSE 0::double precision
                END) AS diskon_pasien,
            max(COALESCE(infotagihanpasien_r_1.plafon_payer, 0::double precision) + COALESCE(infotagihanpasien_r_1.plafon_subpayer, 0::double precision)) AS total_plafon
           FROM tindakanpelayanan_t
             JOIN ( SELECT b.pendaftaran_id,
                    b.no_pendaftaran,
                    b.tgl_pendaftaran,
                    b.pasien_id,
                    b.penjamin_id,
                    b.jeniskasuspenyakit_id,
                    b.asuransipasien_id,
                    b.instalasi_id,
                    b.pasienadmisi_id
                   FROM pendaftaran_t b) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT b.daftartindakan_id,
                    b.kelompoktindakan_id,
                    b.daftartindakan_nama,
                    b.is_akomodasi,
                    b.is_konsultasi,
                    b.daftartindakan_kode
                   FROM daftartindakan_m b) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT b.kelompoktindakan_id,
                    b.kelompoktindakan_nama
                   FROM kelompoktindakan_m b) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m_1 ON tindakanpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id,
                    a.is_penjamin
                   FROM carabayar_m a) carabayar_m_1 ON penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
             LEFT JOIN ( SELECT a.infotagihanpasien_id,
                    a.pendaftaran_id,
                    a.pasienmasukpenunjang_id,
                    a.kelompok,
                    a.status,
                    a.tipe_pasien,
                    a."checkPenjamin",
                    a.cyto,
                    a."defaultPenjamin",
                    a.dijamin,
                    a.harga,
                    a.instalasi,
                    a."isPenjamin",
                    a.is_obat,
                    a.kelompoktindakan_nama,
                    a.keterangan,
                    a.nominal_diskon,
                    a.penjamin,
                    a.persen_diskon,
                    a.qty,
                    a.subtotal,
                    a.subtotal_origin,
                    a.tanggal,
                    a.tindakan,
                    a.tindakan_obat_id,
                    a."totalDibayar",
                    a.value,
                    a.penyulit,
                    a.pelayanan_id,
                    a.harga_origin,
                    a.cyto_origin,
                    a.penyulit_origin,
                    a.id,
                    a.is_ditagihkan,
                    a."subPenjamin",
                    a.dijamin_subpayer,
                    a.plafon_payer,
                    a.plafon_subpayer
                   FROM infotagihanpasien_r a) infotagihanpasien_r_1 ON tindakanpelayanan_t.pendaftaran_id = infotagihanpasien_r_1.pendaftaran_id AND infotagihanpasien_r_1.is_obat = false AND tindakanpelayanan_t.tindakanpelayanan_id = infotagihanpasien_r_1.pelayanan_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL AND daftartindakan_m.is_akomodasi IS TRUE
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.daftartindakan_id, daftartindakan_m.daftartindakan_nama, tindakanpelayanan_t.tarif_satuan, tindakanpelayanan_t.tarifcyto_tindakan, tindakanpelayanan_t.ruangan_id, tindakanpelayanan_t.kelaspelayanan_id, tindakanpelayanan_t.carabayar_id, tindakanpelayanan_t.penjamin_id, daftartindakan_m.kelompoktindakan_id, kelompoktindakan_m.kelompoktindakan_nama, pendaftaran_t.pasien_id, tindakanpelayanan_t.dokterpenanggungjawab_id, pendaftaran_t.penjamin_id, tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.is_valid, tindakanpelayanan_t.cyto_tindakan, tindakanpelayanan_t.pasienadmisi_id, tindakanpelayanan_t.implementasi_id, pendaftaran_t.jeniskasuspenyakit_id, tindakanpelayanan_t.tipepaket_id, tindakanpelayanan_t.kamarruangan_id, daftartindakan_m.is_akomodasi, tindakanpelayanan_t.kamartempattidur_id, daftartindakan_m.is_konsultasi, tindakanpelayanan_t.is_overwrite, tindakanpelayanan_t.harga_origin, pendaftaran_t.asuransipasien_id, pendaftaran_t.instalasi_id, (COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar)), daftartindakan_m.daftartindakan_kode, (COALESCE(infotagihanpasien_r_1.persen_diskon, 0::double precision))
        UNION ALL
         SELECT 8 AS tipe,
            gabungtagihan.ref_pendaftaran_id,
            tindakanpelayanan_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            min(tindakanpelayanan_t.tgl_tindakan) AS tgl_pelayanan,
            max(tindakanpelayanan_t.tgl_tindakan) AS tgl_pelayanan_akhir,
            NULL::integer AS pelayanan_id,
            NULL::integer AS tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            sum(tindakanpelayanan_t.qty_tindakan) AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            sum(COALESCE(infotagihanpasien_r_1.subtotal, tindakanpelayanan_t.tarif_tindakan)) AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            sum(tindakanpelayanan_t.tarif_diskon) AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            daftartindakan_m.is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            NULL::text AS additional_data,
            daftartindakan_m.is_konsultasi,
            sum(tindakanpelayanan_t.tarifpenyulit_tindakan) AS tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            sum(tindakanpelayanan_t.cyto_origin) AS cyto_origin,
            sum(tindakanpelayanan_t.penyulit_origin) AS penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            daftartindakan_m.daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            sum(
                CASE
                    WHEN infotagihanpasien_r_1.dijamin IS NULL THEN
                    CASE
                        WHEN carabayar_m_1.is_penjamin IS TRUE THEN tindakanpelayanan_t.tarif_tindakan
                        ELSE 0::double precision
                    END
                    ELSE infotagihanpasien_r_1.dijamin
                END) AS sub_total_penjamin,
            sum(
                CASE
                    WHEN infotagihanpasien_r_1."totalDibayar" IS NULL THEN
                    CASE
                        WHEN carabayar_m_1.is_penjamin IS FALSE THEN tindakanpelayanan_t.tarif_tindakan
                        ELSE 0::double precision
                    END
                    ELSE infotagihanpasien_r_1."totalDibayar"
                END) AS sub_total_pasien,
            sum(infotagihanpasien_r_1.dijamin_subpayer) AS dijamin_subpayer,
            COALESCE(infotagihanpasien_r_1.persen_diskon, 0::double precision) AS persen_diskon,
            sum(
                CASE
                    WHEN infotagihanpasien_r_1.nominal_diskon IS NOT NULL THEN
                    CASE
                        WHEN infotagihanpasien_r_1.dijamin > 0::double precision THEN infotagihanpasien_r_1.nominal_diskon
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision AND carabayar_m_1.is_penjamin IS TRUE THEN infotagihanpasien_r_1.nominal_diskon
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision AND carabayar_m_1.is_penjamin IS FALSE THEN 0::double precision
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin > 0::double precision THEN infotagihanpasien_r_1.nominal_diskon
                        ELSE 0::double precision
                    END
                    ELSE 0::double precision
                END) AS diskon_penjamin,
            sum(
                CASE
                    WHEN infotagihanpasien_r_1.nominal_diskon IS NOT NULL THEN
                    CASE
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision AND carabayar_m_1.is_penjamin IS FALSE THEN infotagihanpasien_r_1.nominal_diskon
                        WHEN infotagihanpasien_r_1."totalDibayar" = 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision AND carabayar_m_1.is_penjamin IS TRUE THEN 0::double precision
                        WHEN infotagihanpasien_r_1."totalDibayar" > 0::double precision AND infotagihanpasien_r_1.dijamin = 0::double precision THEN infotagihanpasien_r_1.nominal_diskon
                        ELSE NULL::double precision
                    END
                    ELSE 0::double precision
                END) AS diskon_pasien,
            max(COALESCE(infotagihanpasien_r_1.plafon_payer, 0::double precision) + COALESCE(infotagihanpasien_r_1.plafon_subpayer, 0::double precision)) AS total_plafon
           FROM tindakanpelayanan_t
             JOIN ( SELECT b.pendaftaran_id,
                    b.no_pendaftaran,
                    b.tgl_pendaftaran,
                    b.pasien_id,
                    b.penjamin_id,
                    b.jeniskasuspenyakit_id,
                    b.asuransipasien_id,
                    b.instalasi_id,
                    b.pasienadmisi_id
                   FROM pendaftaran_t b) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT b.daftartindakan_id,
                    b.kelompoktindakan_id,
                    b.daftartindakan_nama,
                    b.is_akomodasi,
                    b.is_konsultasi,
                    b.daftartindakan_kode
                   FROM daftartindakan_m b) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT b.kelompoktindakan_id,
                    b.kelompoktindakan_nama
                   FROM kelompoktindakan_m b) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m_1 ON tindakanpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id,
                    a.is_penjamin
                   FROM carabayar_m a) carabayar_m_1 ON penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false) gabungtagihan ON tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
             LEFT JOIN ( SELECT a.infotagihanpasien_id,
                    a.pendaftaran_id,
                    a.pasienmasukpenunjang_id,
                    a.kelompok,
                    a.status,
                    a.tipe_pasien,
                    a."checkPenjamin",
                    a.cyto,
                    a."defaultPenjamin",
                    a.dijamin,
                    a.harga,
                    a.instalasi,
                    a."isPenjamin",
                    a.is_obat,
                    a.kelompoktindakan_nama,
                    a.keterangan,
                    a.nominal_diskon,
                    a.penjamin,
                    a.persen_diskon,
                    a.qty,
                    a.subtotal,
                    a.subtotal_origin,
                    a.tanggal,
                    a.tindakan,
                    a.tindakan_obat_id,
                    a."totalDibayar",
                    a.value,
                    a.penyulit,
                    a.pelayanan_id,
                    a.harga_origin,
                    a.cyto_origin,
                    a.penyulit_origin,
                    a.id,
                    a.is_ditagihkan,
                    a."subPenjamin",
                    a.dijamin_subpayer,
                    a.plafon_payer,
                    a.plafon_subpayer
                   FROM infotagihanpasien_r a) infotagihanpasien_r_1 ON gabungtagihan.pendaftaran_id = infotagihanpasien_r_1.pendaftaran_id AND infotagihanpasien_r_1.is_obat = false AND tindakanpelayanan_t.tindakanpelayanan_id = infotagihanpasien_r_1.pelayanan_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL AND daftartindakan_m.is_akomodasi IS TRUE
          GROUP BY gabungtagihan.ref_pendaftaran_id, tindakanpelayanan_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.daftartindakan_id, daftartindakan_m.daftartindakan_nama, tindakanpelayanan_t.tarif_satuan, tindakanpelayanan_t.tarifcyto_tindakan, tindakanpelayanan_t.ruangan_id, tindakanpelayanan_t.kelaspelayanan_id, tindakanpelayanan_t.carabayar_id, tindakanpelayanan_t.penjamin_id, daftartindakan_m.kelompoktindakan_id, kelompoktindakan_m.kelompoktindakan_nama, pendaftaran_t.pasien_id, tindakanpelayanan_t.dokterpenanggungjawab_id, pendaftaran_t.penjamin_id, tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.is_valid, tindakanpelayanan_t.cyto_tindakan, tindakanpelayanan_t.pasienadmisi_id, tindakanpelayanan_t.implementasi_id, pendaftaran_t.jeniskasuspenyakit_id, tindakanpelayanan_t.tipepaket_id, tindakanpelayanan_t.kamarruangan_id, daftartindakan_m.is_akomodasi, tindakanpelayanan_t.kamartempattidur_id, daftartindakan_m.is_konsultasi, tindakanpelayanan_t.is_overwrite, tindakanpelayanan_t.harga_origin, pendaftaran_t.asuransipasien_id, pendaftaran_t.instalasi_id, (COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar)), daftartindakan_m.daftartindakan_kode, (COALESCE(infotagihanpasien_r_1.persen_diskon, 0::double precision))
        UNION ALL
         SELECT 3 AS tipe,
            pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan_akhir,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 21 THEN 17
                    ELSE NULL::integer
                END AS kelompoktindakan_id,
            'Others'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            tipepaket_m.tipepaket_kode AS daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            NULL::double precision AS sub_total_penjamin,
            NULL::double precision AS sub_total_pasien,
            NULL::double precision AS dijamin_subpayer,
            NULL::double precision AS persen_diskon,
            NULL::double precision AS diskon_penjamin,
            NULL::double precision AS diskon_pasien,
            NULL::double precision AS total_plafon
           FROM tindakanpelayanan_t
             JOIN ( SELECT c.pendaftaran_id,
                    c.no_pendaftaran,
                    c.tgl_pendaftaran,
                    c.pasien_id,
                    c.penjamin_id,
                    c.jeniskasuspenyakit_id,
                    c.asuransipasien_id,
                    c.instalasi_id,
                    c.pasienadmisi_id
                   FROM pendaftaran_t c) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT c.tipepaket_id,
                    c.tipepaket_nama,
                    c.tipepaket_kode
                   FROM tipepaket_m c) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
        UNION ALL
         SELECT 4 AS tipe,
            gabungtagihan.ref_pendaftaran_id,
            tindakanpelayanan_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan_akhir,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 21 THEN 17
                    ELSE NULL::integer
                END AS kelompoktindakan_id,
            'Others'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            tipepaket_m.tipepaket_kode AS daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            NULL::double precision AS sub_total_penjamin,
            NULL::double precision AS sub_total_pasien,
            NULL::double precision AS dijamin_subpayer,
            NULL::double precision AS persen_diskon,
            NULL::double precision AS diskon_penjamin,
            NULL::double precision AS diskon_pasien,
            NULL::double precision AS total_plafon
           FROM tindakanpelayanan_t
             JOIN ( SELECT c.pendaftaran_id,
                    c.no_pendaftaran,
                    c.tgl_pendaftaran,
                    c.pasien_id,
                    c.penjamin_id,
                    c.jeniskasuspenyakit_id,
                    c.asuransipasien_id,
                    c.instalasi_id,
                    c.pasienadmisi_id
                   FROM pendaftaran_t c) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT c.tipepaket_id,
                    c.tipepaket_nama,
                    c.tipepaket_kode
                   FROM tipepaket_m c) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false) gabungtagihan ON tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
        UNION ALL
         SELECT 5 AS tipe,
            pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan_akhir,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            COALESCE(racikan.namakelompokinvoice, 'Drugs and Consumable - Racikan'::character varying) AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            COALESCE(penjualanresep.pegawai_id, obatalkespasien_t.pegawai_id) AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.penjualanresep_id,
            NULL::boolean AS is_valid,
            NULL::boolean AS is_cyto,
            obatalkespasien_t.pasienadmisi_id,
            NULL::integer AS implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            obatalkespasien_t.tarif_diskon AS discount,
            NULL::integer AS tipepaket_id,
            NULL::integer AS kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            NULL::integer AS kamartempattidur_id,
            NULL::text AS additional_data,
            NULL::boolean AS is_konsultasi,
            0 AS tarifpenyulit_tindakan,
            obatalkespasien_t.is_overwrite,
            obatalkespasien_t.harga_origin,
            0 AS cyto_origin,
            0 AS penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            obatalkes_m.obatalkes_kode AS daftartindakan_kode,
            obatalkes_m.jenisobatalkes_id,
            NULL::double precision AS sub_total_penjamin,
            NULL::double precision AS sub_total_pasien,
            NULL::double precision AS dijamin_subpayer,
            NULL::double precision AS persen_diskon,
            NULL::double precision AS diskon_penjamin,
            NULL::double precision AS diskon_pasien,
            NULL::double precision AS total_plafon
           FROM obatalkespasien_t
             JOIN ( SELECT d.pendaftaran_id,
                    d.no_pendaftaran,
                    d.tgl_pendaftaran,
                    d.pasien_id,
                    d.penjamin_id,
                    d.jeniskasuspenyakit_id,
                    d.asuransipasien_id,
                    d.instalasi_id,
                    d.pasienadmisi_id
                   FROM pendaftaran_t d) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT d.obatalkes_id,
                    d.obatalkes_nama,
                    d.obatalkes_kode,
                    d.jenisobatalkes_id
                   FROM obatalkes_m d) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
             LEFT JOIN ( SELECT racikan_m.racikan_id,
                    racikan_m.racikan_nama,
                    racikan_m.namakelompokinvoice
                   FROM racikan_m) racikan ON racikan.racikan_id = obatalkespasien_t.racikan_id
           	 LEFT JOIN ( SELECT penjualanresep_id,
         		    penjualanresep_t.pegawai_id
         		   FROM penjualanresep_t) penjualanresep ON penjualanresep.penjualanresep_id = obatalkespasien_t.penjualanresep_id
          WHERE obatalkespasien_t.is_deleted = false
        UNION ALL
         SELECT 6 AS tipe,
            gabungtagihan.ref_pendaftaran_id,
            obatalkespasien_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan_akhir,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            COALESCE(racikan.namakelompokinvoice, 'Drugs and Consumable - Racikan'::character varying) AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.penjualanresep_id,
            NULL::boolean AS is_valid,
            NULL::boolean AS is_cyto,
            obatalkespasien_t.pasienadmisi_id,
            NULL::integer AS implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            obatalkespasien_t.tarif_diskon AS discount,
            NULL::integer AS tipepaket_id,
            NULL::integer AS kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            NULL::integer AS kamartempattidur_id,
            NULL::text AS additional_data,
            NULL::boolean AS is_konsultasi,
            0 AS tarifpenyulit_tindakan,
            obatalkespasien_t.is_overwrite,
            obatalkespasien_t.harga_origin,
            0 AS cyto_origin,
            0 AS penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            obatalkes_m.obatalkes_kode AS daftartindakan_kode,
            obatalkes_m.jenisobatalkes_id,
            NULL::double precision AS sub_total_penjamin,
            NULL::double precision AS sub_total_pasien,
            NULL::double precision AS dijamin_subpayer,
            NULL::double precision AS persen_diskon,
            NULL::double precision AS diskon_penjamin,
            NULL::double precision AS diskon_pasien,
            NULL::double precision AS total_plafon
           FROM obatalkespasien_t
             JOIN ( SELECT d.pendaftaran_id,
                    d.no_pendaftaran,
                    d.tgl_pendaftaran,
                    d.pasien_id,
                    d.penjamin_id,
                    d.jeniskasuspenyakit_id,
                    d.asuransipasien_id,
                    d.instalasi_id,
                    d.pasienadmisi_id
                   FROM pendaftaran_t d) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT d.obatalkes_id,
                    d.obatalkes_nama,
                    d.obatalkes_kode,
                    d.jenisobatalkes_id
                   FROM obatalkes_m d) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false) gabungtagihan ON obatalkespasien_t.pendaftaran_id = gabungtagihan.pendaftaran_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
             LEFT JOIN ( SELECT racikan_m.racikan_id,
                    racikan_m.racikan_nama,
                    racikan_m.namakelompokinvoice
                   FROM racikan_m) racikan ON racikan.racikan_id = obatalkespasien_t.racikan_id
          WHERE obatalkespasien_t.is_deleted = false) tagihan
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama,
            a.lob_id
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.lob_id
           FROM instalasi_m a) lob ON tagihan.instalasi_id = lob.instalasi_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            a.carabayar_id
           FROM penjamin_m a) penjamin_m ON tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.groupcarabayar_id,
            a.is_penjamin
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.no_mobile_pasien,
            a.alamatemail
           FROM pasien_m a) pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter_dpjp ON tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id
     LEFT JOIN ( SELECT a.asuransipasien_id,
            a.penjamingrade_id
           FROM asuransipasien_m a) asuransipasien_m ON tagihan.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN ( SELECT a.infotagihanpasien_id,
            a.pendaftaran_id,
            a.pasienmasukpenunjang_id,
            a.kelompok,
            a.status,
            a.tipe_pasien,
            a."checkPenjamin",
            a.cyto,
            a."defaultPenjamin",
            a.dijamin,
            a.harga,
            a.instalasi,
            a."isPenjamin",
            a.is_obat,
            a.kelompoktindakan_nama,
            a.keterangan,
            a.nominal_diskon,
            a.penjamin,
            a.persen_diskon,
            a.qty,
            a.subtotal,
            a.subtotal_origin,
            a.tanggal,
            a.tindakan,
            a.tindakan_obat_id,
            a."totalDibayar",
            a.value,
            a.penyulit,
            a.pelayanan_id,
            a.harga_origin,
            a.cyto_origin,
            a.penyulit_origin,
            a.id,
            a.is_ditagihkan,
            a."subPenjamin",
            a.dijamin_subpayer,
            a.plafon_payer,
            a.plafon_subpayer
           FROM infotagihanpasien_r a) infotagihanpasien_r ON tagihan.ref_pendaftaran_id = infotagihanpasien_r.pendaftaran_id AND tagihan.is_obat = infotagihanpasien_r.is_obat AND tagihan.pelayanan_id = infotagihanpasien_r.pelayanan_id
  WHERE tagihan.tindakansudahbayar_id IS NULL
  ORDER BY tagihan.tgl_pelayanan DESC;