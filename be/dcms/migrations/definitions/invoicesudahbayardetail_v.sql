CREATE VIEW "public"."invoicesudahbayardetail_v" AS  SELECT tagihan.pendaftaran_id, 
    tagihan.pelayanan_id,
    tagihan.pasien_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN tagihan.nama_pembeli::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    pasien_m.tanggal_lahir,
    tagihan.umur,
    jk.lookup_name AS jeniskelamin,
    tagihan.tgl_pendaftaran,
    tagihan.no_pendaftaran,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan,
    tagihan.qty,
    tagihan.sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.tgl_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_tinpelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
    tagihan.penjamin_tinpelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.jeniskasuspenyakit_id,
    tagihan.pembayaranpelayanan_id,
    tagihan.biaya_administrasi,
    tagihan.e_collection,
    tagihan.nama_pemrekening,
    tagihan.no_rekening,
    tagihan.carabayar_pelayanan_id,
    tagihan.carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    tagihan.penjamin_pelayanan,
    tagihan.tarif_cyto,
    tagihan.tandabuktibayar_id,
    tagihan.jeniskasuspenyakit_nama,
    tagihan.penjualanresep_id,
    tagihan.is_konsultasi,
    dok_tindakan.nama_pegawai AS dokter_tindakan,
    tagihan.pembayaran_id,
    tagihan.satuan_kecil AS uom,
        CASE
            WHEN tagihan.sub_total < 0::double precision THEN
            CASE
                WHEN tagihan.tarif_dijamin_origin > 0::double precision AND tagihan.groupcarabayar_id <> 417 THEN tagihan.tarif_diskon * '-1'::integer::double precision
                WHEN tagihan.tarif_dijamin_origin = 0::double precision AND tagihan.tarif_dibayarkan_origin = 0::double precision AND tagihan.groupcarabayar_id <> 417 THEN tagihan.sub_total
                ELSE 0::double precision
            END
            ELSE
            CASE
                WHEN tagihan.tarif_dijamin = 0::double precision AND tagihan.tarif_dibayarkan = 0::double precision AND tagihan.groupcarabayar_id <> 417 THEN tagihan.sub_total
                ELSE tagihan.tarif_dijamin
            END
        END AS tarif_dijamin,
        CASE
            WHEN tagihan.sub_total < 0::double precision THEN
            CASE
                WHEN tagihan.tarif_dibayarkan_origin > 0::double precision AND tagihan.groupcarabayar_id = 417 THEN tagihan.tarif_diskon * '-1'::integer::double precision
                WHEN tagihan.tarif_dijamin_origin = 0::double precision AND tagihan.tarif_dibayarkan_origin > 0::double precision AND tagihan.groupcarabayar_id <> 417 THEN tagihan.tarif_diskon * '-1'::integer::double precision
                WHEN tagihan.tarif_dijamin_origin = 0::double precision AND tagihan.tarif_dibayarkan_origin = 0::double precision AND tagihan.groupcarabayar_id = 417 THEN tagihan.sub_total
                ELSE 0::double precision
            END
            ELSE
            CASE
                WHEN tagihan.tarif_dijamin = 0::double precision AND tagihan.tarif_dibayarkan = 0::double precision AND tagihan.groupcarabayar_id = 417 THEN tagihan.sub_total
                ELSE tagihan.tarif_dibayarkan
            END
        END AS tarif_dibayarkan,
    tagihan.groupinacbg_id,
    tagihan.groupinacbg_nama,
    tagihan.tarif_diskon,
    tagihan.is_visite,
    tagihan.tarifpenyulit_tindakan,
    tagihan.jenis_racikan,
    tagihan.tindakan_obat_kode,
    tagihan.is_akomodasi,
    tagihan.is_diskon,
    tagihan.kamarruangan_nokamar,
    tagihan.no_tempattidur,
    pembayaran_t.total_pembulatan,
    pembayaran_t.pembulatan,
        CASE
            WHEN tagihan.tarif_diskon = tagihan.sub_total THEN tagihan.sub_total
            ELSE 0::double precision
        END AS tarif_invoice,
    tagihan.dijamin_payer,
    tagihan.dijamin_subpayer,
    pendaftaran_t.penjamin_utama_id,
    tagihan.additional_data,
    tagihan.remarks,
    tagihan.qty_konversi,
    tagihan.is_cyto,
    tagihan.is_penyulit
   FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.umur,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
                CASE
                    WHEN tindakanpelayanan_t.qty_tindakan = 0 THEN tindakanpelayanan_t.tarif_tindakan / tindakanpelayanan_t.tarif_satuan
                    ELSE tindakanpelayanan_t.qty_tindakan::double precision
                END AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN kelompoktindakan_m.kelompoktindakan_nama
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompoktindakan_nama,
            pendaftaran_t_1.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
                CASE
                    WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                    ELSE false
                END AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            NULL::text AS jenis_racikan,
            daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
            daftartindakan_m.is_akomodasi,
            false AS is_diskon,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            carabayar_m_1.groupcarabayar_id,
            tindakanpelayanan_t.dijamin_payer,
            tindakanpelayanan_t.dijamin_subpayer,
            tindakanpelayanan_t.additional_data,
            tindakanpelayanan_t.tarif_dijamin AS tarif_dijamin_origin,
            tindakanpelayanan_t.tarif_dibayarkan AS tarif_dibayarkan_origin,
            (tindakanpelayanan_t.additional_data::json -> 'remarks'::text)::text AS remarks,
            tindakanpelayanan_t.qty_tindakan AS qty_konversi,
            tindakanpelayanan_t.is_cyto,
            tindakanpelayanan_t.is_penyulit
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t_1.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pendaftaran_id,
                    a.daftartindakan_id,
                    a.tarif_satuan,
                    a.qty_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.ruangan_id,
                    a.tgl_tindakan,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.dokterpenanggungjawab_id,
                    a.tarif_dijamin,
                    a.tarif_dibayarkan,
                    a.tarif_diskon,
                    a.tarifpenyulit_tindakan,
                    a.tindakansudahbayar_id,
                    a.is_deleted,
                    a.parent_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.dijamin_payer,
                    a.dijamin_subpayer,
                    a.additional_data,
                    a.cyto_tindakan AS is_cyto,
                    a.penyulit_tindakan AS is_penyulit
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id,
                    a.is_konsultasi,
                    a.daftartindakan_kode,
                    a.is_akomodasi,
                    a.groupinacbg_id
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a.tindakansudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN ( SELECT a.kelompoktindakan_id,
                    a.kelompoktindakan_nama
                   FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.biaya_administrasi,
                    a.e_collection,
                    a.nama_pemrekening,
                    a.no_rekening,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tandabuktibayar_id,
                    a.pembayaran_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT a.groupinacbg_id,
                    a.groupinacbg_nama
                   FROM groupinacbg_m a) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.umur,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            -
                CASE
                    WHEN tindakanpelayanan_t.qty_tindakan > 0 THEN tindakanpelayanan_t.tarif_diskon / tindakanpelayanan_t.qty_tindakan::double precision
                    ELSE tindakanpelayanan_t.tarif_satuan
                END AS tarif_satuan,
                CASE
                    WHEN tindakanpelayanan_t.qty_tindakan = 0 THEN tindakanpelayanan_t.tarif_tindakan / tindakanpelayanan_t.tarif_satuan
                    ELSE tindakanpelayanan_t.qty_tindakan::double precision
                END AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            - tindakanpelayanan_t.tarif_diskon AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN kelompoktindakan_m.kelompoktindakan_nama
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompoktindakan_nama,
            pendaftaran_t_1.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            0 AS tarif_dijamin,
            0 AS tarif_dibayarkan,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
                CASE
                    WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                    ELSE false
                END AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            NULL::text AS jenis_racikan,
            daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
            daftartindakan_m.is_akomodasi,
            true AS is_diskon,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            carabayar_m_1.groupcarabayar_id,
            0 AS dijamin_payer,
            0 AS dijamin_subpayer,
            tindakanpelayanan_t.additional_data,
            tindakanpelayanan_t.tarif_dijamin AS tarif_dijamin_origin,
            tindakanpelayanan_t.tarif_dibayarkan AS tarif_dibayarkan_origin,
            (tindakanpelayanan_t.additional_data::json -> 'remarks'::text)::text AS remarks,
            tindakanpelayanan_t.qty_tindakan AS qty_konversi,
            tindakanpelayanan_t.is_cyto,
            tindakanpelayanan_t.is_penyulit
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t_1.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pendaftaran_id,
                    a.daftartindakan_id,
                    a.tarif_satuan,
                    a.qty_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.ruangan_id,
                    a.tgl_tindakan,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.dokterpenanggungjawab_id,
                    a.tarif_dijamin,
                    a.tarif_dibayarkan,
                    a.tarif_diskon,
                    a.tarifpenyulit_tindakan,
                    a.tindakansudahbayar_id,
                    a.is_deleted,
                    a.parent_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.dijamin_payer,
                    a.dijamin_subpayer,
                    a.additional_data,
                    a.cyto_tindakan AS is_cyto,
                    a.penyulit_tindakan AS is_penyulit
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id,
                    a.is_konsultasi,
                    a.daftartindakan_kode,
                    a.is_akomodasi,
                    a.groupinacbg_id
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a.tindakansudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN ( SELECT a.kelompoktindakan_id,
                    a.kelompoktindakan_nama
                   FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.biaya_administrasi,
                    a.e_collection,
                    a.nama_pemrekening,
                    a.no_rekening,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tandabuktibayar_id,
                    a.pembayaran_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT a.groupinacbg_id,
                    a.groupinacbg_nama
                   FROM groupinacbg_m a) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
             JOIN ( SELECT a.is_invoice_diskon
                   FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
          WHERE tindakanpelayanan_t.tarif_diskon <> 0::double precision AND tindakanpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.umur,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
                CASE
                    WHEN tindakanpelayanan_t.qty_tindakan = 0 THEN tindakanpelayanan_t.tarif_tindakan / tindakanpelayanan_t.tarif_satuan
                    ELSE tindakanpelayanan_t.qty_tindakan::double precision
                END AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
                CASE
                    WHEN tipepaket_m.is_mcu IS TRUE THEN 'Paket Medical Check Up'::text
                    ELSE 'Paket Pemeriksaan'::text
                END AS kelompoktindakan_nama,
            pendaftaran_t_1.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            NULL::integer AS groupinacbg_id,
            NULL::character varying AS groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
            false AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            NULL::text AS jenis_racikan,
            tipepaket_m.tipepaket_kode AS tindakan_obat_kode,
            false AS is_akomodasi,
            false AS is_diskon,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            carabayar_m_1.groupcarabayar_id,
            tindakanpelayanan_t.dijamin_payer,
            tindakanpelayanan_t.dijamin_subpayer,
            tindakanpelayanan_t.additional_data,
            tindakanpelayanan_t.tarif_dijamin AS tarif_dijamin_origin,
            tindakanpelayanan_t.tarif_dibayarkan AS tarif_dibayarkan_origin,
            (tindakanpelayanan_t.additional_data::json -> 'remarks'::text)::text AS remarks,
            tindakanpelayanan_t.qty_tindakan AS qty_konversi,
            tindakanpelayanan_t.is_cyto,
            tindakanpelayanan_t.is_penyulit
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t_1.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pendaftaran_id,
                    a.daftartindakan_id,
                    a.tarif_satuan,
                    a.qty_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.ruangan_id,
                    a.tgl_tindakan,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.dokterpenanggungjawab_id,
                    a.tarif_dijamin,
                    a.tarif_dibayarkan,
                    a.tarif_diskon,
                    a.tarifpenyulit_tindakan,
                    a.tindakansudahbayar_id,
                    a.is_deleted,
                    a.parent_id,
                    a.tipepaket_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.dijamin_payer,
                    a.dijamin_subpayer,
                    a.additional_data,
                    a.cyto_tindakan AS is_cyto,
                    a.penyulit_tindakan AS is_penyulit
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama,
                    a.is_mcu,
                    a.tipepaket_kode
                   FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN ( SELECT a.tindakansudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.biaya_administrasi,
                    a.e_collection,
                    a.nama_pemrekening,
                    a.no_rekening,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tandabuktibayar_id,
                    a.pembayaran_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.umur,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            - tindakanpelayanan_t.tarif_diskon AS tarif_satuan,
                CASE
                    WHEN tindakanpelayanan_t.qty_tindakan = 0 THEN tindakanpelayanan_t.tarif_tindakan / tindakanpelayanan_t.tarif_satuan
                    ELSE tindakanpelayanan_t.qty_tindakan::double precision
                END AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            - tindakanpelayanan_t.tarif_diskon AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
                CASE
                    WHEN tipepaket_m.is_mcu IS TRUE THEN 'Paket Medical Check Up'::text
                    ELSE 'Paket Pemeriksaan'::text
                END AS kelompoktindakan_nama,
            pendaftaran_t_1.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            0 AS tarif_dijamin,
            0 AS tarif_dibayarkan,
            NULL::integer AS groupinacbg_id,
            NULL::character varying AS groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
            false AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            NULL::text AS jenis_racikan,
            tipepaket_m.tipepaket_kode AS tindakan_obat_kode,
            false AS is_akomodasi,
            true AS is_diskon,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            carabayar_m_1.groupcarabayar_id,
            0 AS dijamin_payer,
            0 AS dijamin_subpayer,
            tindakanpelayanan_t.additional_data,
            tindakanpelayanan_t.tarif_dijamin AS tarif_dijamin_origin,
            tindakanpelayanan_t.tarif_dibayarkan AS tarif_dibayarkan_origin,
            (tindakanpelayanan_t.additional_data::json -> 'remarks'::text)::text AS remarks,
            tindakanpelayanan_t.qty_tindakan AS qty_konversi,
            tindakanpelayanan_t.is_cyto,
            tindakanpelayanan_t.is_penyulit
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t_1.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pendaftaran_id,
                    a.daftartindakan_id,
                    a.tarif_satuan,
                    a.qty_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.ruangan_id,
                    a.tgl_tindakan,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.dokterpenanggungjawab_id,
                    a.tarif_dijamin,
                    a.tarif_dibayarkan,
                    a.tarif_diskon,
                    a.tarifpenyulit_tindakan,
                    a.tindakansudahbayar_id,
                    a.is_deleted,
                    a.parent_id,
                    a.tipepaket_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.dijamin_payer,
                    a.dijamin_subpayer,
                    a.additional_data,
                    a.cyto_tindakan AS is_cyto,
                    a.penyulit_tindakan AS is_penyulit
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama,
                    a.is_mcu,
                    a.tipepaket_kode
                   FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN ( SELECT a.tindakansudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.biaya_administrasi,
                    a.e_collection,
                    a.nama_pemrekening,
                    a.no_rekening,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tandabuktibayar_id,
                    a.pembayaran_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN ( SELECT a.is_invoice_diskon
                   FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
          WHERE tindakanpelayanan_t.is_deleted = false AND COALESCE(tindakanpelayanan_t.tarif_diskon, 0::double precision) <> 0::double precision
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                CASE
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'Drugs & Consumables'::character varying AS kelompoktindakan_nama,
            pendaftaran_t_1.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            obatalkespasien_t.pegawai_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan,
                CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                    WHEN 0 THEN 'Non Racikan'::text
                    ELSE 'Racikan'::text
                END AS jenis_racikan,
            obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
            false AS is_akomodasi,
            false AS is_diskon,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            carabayar_m_1.groupcarabayar_id,
            obatalkespasien_t.dijamin_payer,
            obatalkespasien_t.dijamin_subpayer,
            obatalkespasien_t.additional_data,
            obatalkespasien_t.tarif_dijamin AS tarif_dijamin_origin,
            obatalkespasien_t.tarif_dibayarkan AS tarif_dibayarkan_origin,
            NULL::text AS remarks,
                CASE
                    WHEN obatalkespasien_t.det_konversi IS NULL THEN obatalkespasien_t.qty_konversi
                    ELSE obatalkespasien_t.det_konversi
                END AS qty_konversi,
            false AS is_cyto,
            false AS is_penyulit
           FROM pendaftaran_t pendaftaran_t_1
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.kamartempattidur_id,
                    a.kamarruangan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t_1.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.obatalkespasien_id,
                    a.pendaftaran_id,
                    a.satuankecil_id,
                    a.obatalkes_id,
                    a.hargasatuan_oa,
                    a.det,
                    a.qty_oa,
                    a.hargajual_oa,
                    a.ruangan_id,
                    a.tglpelayanan,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tarifcyto,
                    a.tarif_dijamin,
                    a.tarif_dibayarkan,
                    a.tarif_diskon,
                    a.racikan_id,
                    a.obatsudahbayar_id,
                    a.is_deleted,
                    a.dijamin_payer,
                    a.dijamin_subpayer,
                    a.additional_data,
                    a.pegawai_id,
                    a.qty_konversi,
                    a.det_konversi
                   FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_kode,
                    a.obatalkes_nama,
                    a.groupinacbg_id
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a.obatsudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM obatsudahbayar_t a) obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.biaya_administrasi,
                    a.e_collection,
                    a.nama_pemrekening,
                    a.no_rekening,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tandabuktibayar_id,
                    a.pembayaran_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                    satuanunit_m.satuanunit_nama
                   FROM satuanunit_m) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN ( SELECT a.groupinacbg_id,
                    a.groupinacbg_nama
                   FROM groupinacbg_m a) groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
          WHERE obatalkespasien_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            - (obatalkespasien_t.tarif_diskon /
                CASE
                    WHEN
                    CASE
                        WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                        WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                        ELSE obatalkespasien_t.det
                    END = 0::double precision THEN 1::double precision
                    ELSE
                    CASE
                        WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                        WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                        ELSE obatalkespasien_t.det
                    END
                END) AS tarif_satuan,
                CASE
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            - obatalkespasien_t.tarif_diskon AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'Drugs & Consumables'::character varying AS kelompoktindakan_nama,
            pendaftaran_t_1.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            obatalkespasien_t.pegawai_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            0 AS tarif_dijamin,
            0 AS tarif_dibayarkan,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan,
                CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                    WHEN 0 THEN 'Non Racikan'::text
                    ELSE 'Racikan'::text
                END AS jenis_racikan,
            obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
            false AS is_akomodasi,
            true AS is_diskon,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            carabayar_m_1.groupcarabayar_id,
            0 AS dijamin_payer,
            0 AS dijamin_subpayer,
            obatalkespasien_t.additional_data,
            obatalkespasien_t.tarif_dijamin AS tarif_dijamin_origin,
            obatalkespasien_t.tarif_dibayarkan AS tarif_dibayarkan_origin,
            NULL::text AS remarks,
                CASE
                    WHEN obatalkespasien_t.det_konversi IS NULL THEN obatalkespasien_t.qty_konversi
                    ELSE obatalkespasien_t.det_konversi
                END AS qty_konversi,
            false AS is_cyto,
            false AS is_penyulit
           FROM pendaftaran_t pendaftaran_t_1
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.kamartempattidur_id,
                    a.kamarruangan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t_1.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.obatalkespasien_id,
                    a.pendaftaran_id,
                    a.satuankecil_id,
                    a.obatalkes_id,
                    a.hargasatuan_oa,
                    a.det,
                    a.qty_oa,
                    a.hargajual_oa,
                    a.ruangan_id,
                    a.tglpelayanan,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tarifcyto,
                    a.tarif_dijamin,
                    a.tarif_dibayarkan,
                    a.tarif_diskon,
                    a.racikan_id,
                    a.obatsudahbayar_id,
                    a.is_deleted,
                    a.dijamin_payer,
                    a.dijamin_subpayer,
                    a.additional_data,
                    a.pegawai_id,
                    a.det_konversi,
                    a.qty_konversi
                   FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_kode,
                    a.obatalkes_nama,
                    a.groupinacbg_id
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a.obatsudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM obatsudahbayar_t a) obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.biaya_administrasi,
                    a.e_collection,
                    a.nama_pemrekening,
                    a.no_rekening,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tandabuktibayar_id,
                    a.pembayaran_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                    satuanunit_m.satuanunit_nama
                   FROM satuanunit_m) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN ( SELECT a.groupinacbg_id,
                    a.groupinacbg_nama
                   FROM groupinacbg_m a) groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
             JOIN ( SELECT a.is_invoice_diskon
                   FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
          WHERE obatalkespasien_t.is_deleted = false AND COALESCE(obatalkespasien_t.tarif_diskon, 0::double precision) <> 0::double precision
        UNION ALL
         SELECT penjualanresep_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            penjualanresep_t.noresep AS no_pendaftaran,
            NULL::character varying AS umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                CASE
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            NULL::integer AS jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            NULL::character varying AS jeniskasuspenyakit_nama,
            obatalkespasien_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            NULL::boolean AS is_konsultasi,
            obatalkespasien_t.pegawai_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan,
                CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                    WHEN 0 THEN 'Non Racikan'::text
                    ELSE 'Racikan'::text
                END AS jenis_racikan,
            obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
            false AS is_akomodasi,
            false AS is_diskon,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            carabayar_m_1.groupcarabayar_id,
            obatalkespasien_t.dijamin_payer,
            obatalkespasien_t.dijamin_subpayer,
            obatalkespasien_t.additional_data,
            obatalkespasien_t.tarif_dijamin AS tarif_dijamin_origin,
            obatalkespasien_t.tarif_dibayarkan AS tarif_dibayarkan_origin,
            NULL::text AS remarks,
                CASE
                    WHEN obatalkespasien_t.det_konversi IS NULL THEN obatalkespasien_t.qty_konversi
                    ELSE obatalkespasien_t.det_konversi
                END AS qty_konversi,
            false AS is_cyto,
            false AS is_penyulit
           FROM obatalkespasien_t
             JOIN ( SELECT a.penjualanresep_id,
                    a.pendaftaran_id,
                    a.pasien_id,
                    a.tglresep,
                    a.noresep,
                    a.nama_pembeli,
                    a.jenispenjualan
                   FROM penjualanresep_t a) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_kode,
                    a.obatalkes_nama,
                    a.groupinacbg_id
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a.obatsudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM obatsudahbayar_t a) obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.biaya_administrasi,
                    a.e_collection,
                    a.nama_pemrekening,
                    a.no_rekening,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tandabuktibayar_id,
                    a.pembayaran_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                    satuanunit_m.satuanunit_nama
                   FROM satuanunit_m) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN ( SELECT a.groupinacbg_id,
                    a.groupinacbg_nama
                   FROM groupinacbg_m a) groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE penjualanresep_t.jenispenjualan::integer <> 344
        UNION ALL
         SELECT penjualanresep_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            penjualanresep_t.noresep AS no_pendaftaran,
            NULL::character varying AS umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            - (obatalkespasien_t.tarif_diskon /
                CASE
                    WHEN
                    CASE
                        WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                        WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                        ELSE obatalkespasien_t.det
                    END = 0::double precision THEN 1::double precision
                    ELSE
                    CASE
                        WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                        WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                        ELSE obatalkespasien_t.det
                    END
                END) AS tarif_satuan,
                CASE
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            0 AS tarifcyto_tindakan,
            - obatalkespasien_t.tarif_diskon AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            NULL::integer AS jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            NULL::character varying AS jeniskasuspenyakit_nama,
            obatalkespasien_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            NULL::boolean AS is_konsultasi,
            obatalkespasien_t.pegawai_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            0 AS tarif_dijamin,
            0 AS tarif_dibayarkan,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan,
                CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                    WHEN 0 THEN 'Non Racikan'::text
                    ELSE 'Racikan'::text
                END AS jenis_racikan,
            obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
            false AS is_akomodasi,
            true AS is_diskon,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            carabayar_m_1.groupcarabayar_id,
            0 AS dijamin_payer,
            0 AS dijamin_subpayer,
            obatalkespasien_t.additional_data,
            obatalkespasien_t.tarif_dijamin AS tarif_dijamin_origin,
            obatalkespasien_t.tarif_dibayarkan AS tarif_dibayarkan_origin,
            NULL::text AS remarks,
                CASE
                    WHEN obatalkespasien_t.det_konversi IS NULL THEN obatalkespasien_t.qty_konversi
                    ELSE obatalkespasien_t.det_konversi
                END AS qty_konversi,
            false AS is_cyto,
            false AS is_penyulit
           FROM obatalkespasien_t
             JOIN ( SELECT a.penjualanresep_id,
                    a.pendaftaran_id,
                    a.pasien_id,
                    a.tglresep,
                    a.noresep,
                    a.nama_pembeli,
                    a.jenispenjualan
                   FROM penjualanresep_t a) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_kode,
                    a.obatalkes_nama,
                    a.groupinacbg_id
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a.obatsudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM obatsudahbayar_t a) obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.biaya_administrasi,
                    a.e_collection,
                    a.nama_pemrekening,
                    a.no_rekening,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tandabuktibayar_id,
                    a.pembayaran_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN ( SELECT a.is_invoice_diskon
                   FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
             LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                    satuanunit_m.satuanunit_nama
                   FROM satuanunit_m) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN ( SELECT a.groupinacbg_id,
                    a.groupinacbg_nama
                   FROM groupinacbg_m a) groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE penjualanresep_t.jenispenjualan::integer <> 344 AND obatalkespasien_t.tarif_diskon > 0::double precision) tagihan
     LEFT JOIN ( SELECT a.pendaftaran_id,
            COALESCE(pasienadmisi_t.penjamin_id, a.penjamin_id) AS penjamin_utama_id
           FROM pendaftaran_t a
             LEFT JOIN ( SELECT b.pasienadmisi_id,
                    b.penjamin_id
                   FROM pasienadmisi_t b) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id) pendaftaran_t ON tagihan.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_nama,
            a.instalasi_id
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.tanggal_lahir,
            a.jeniskelamin
           FROM pasien_m a) pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dok_tindakan ON tagihan.doktertindakan_id = dok_tindakan.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.pembulatan,
            a.total_pembulatan
           FROM pembayaran_t a) pembayaran_t ON tagihan.pembayaran_id = pembayaran_t.pembayaran_id;