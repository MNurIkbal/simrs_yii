-- public.sie_invoicesudahbayar source

CREATE OR REPLACE VIEW public.sie_invoicesudahbayar
AS SELECT invoicesudahbayar_v.tgl_pembayaran,
    pasienadmisi_t.tgl_admisi,
    invoicesudahbayar_v.tgl_pasienpulang,
    invoicesudahbayardetail_v.no_pendaftaran,
        CASE
            WHEN pasienadmisi_t.tgl_admisi IS NOT NULL AND invoicesudahbayar_v.tgl_pasienpulang::time without time zone <= '07:00:00'::time without time zone THEN invoicesudahbayar_v.tgl_pasienpulang::date - 1
            WHEN pasienadmisi_t.tgl_admisi IS NOT NULL AND invoicesudahbayar_v.tgl_pasienpulang::time without time zone > '07:00:00'::time without time zone THEN invoicesudahbayar_v.tgl_pasienpulang::date
            WHEN pasienadmisi_t.tgl_admisi IS NULL AND invoicesudahbayardetail_v.tgl_pendaftaran::time without time zone <= '07:00:00'::time without time zone THEN invoicesudahbayardetail_v.tgl_pendaftaran::date - 1
            ELSE invoicesudahbayardetail_v.tgl_pendaftaran::date
        END AS tgl_omset,
    invoicesudahbayardetail_v.tgl_pendaftaran,
    invoicesudahbayardetail_v.ruangan_pelayanan,
    invoicesudahbayardetail_v.instalasi_pelayanan,
    invoicesudahbayardetail_v.carabayar_pelayanan,
    invoicesudahbayardetail_v.penjamin_pelayanan,
    invoicesudahbayardetail_v.dokter_tindakan,
    invoicesudahbayardetail_v.groupinacbg_nama,
    invoicesudahbayardetail_v.sub_total,
    invoicesudahbayardetail_v.tarif_diskon,
    invoicesudahbayardetail_v.sub_total - invoicesudahbayardetail_v.tarif_diskon AS net_tagihan,
        CASE
            WHEN pasienadmisi_t.tgl_admisi IS NOT NULL THEN 'RAWAT INAP'::text
            WHEN pasienadmisi_t.tgl_admisi IS NULL AND invoicesudahbayar_v.instalasi_id = 2 THEN 'RAWAT DARURAT'::text
            ELSE 'RAWAT JALAN'::text
        END AS klp
   FROM invoicesudahbayardetail_v
     LEFT JOIN pasienadmisi_t ON invoicesudahbayardetail_v.pendaftaran_id = pasienadmisi_t.pendaftaran_id
     JOIN invoicesudahbayar_v ON invoicesudahbayardetail_v.pembayaran_id = invoicesudahbayar_v.pembayaran_id
  WHERE invoicesudahbayardetail_v.is_diskon = false;