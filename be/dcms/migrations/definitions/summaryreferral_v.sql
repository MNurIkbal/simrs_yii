-- public.summaryreferral_v source

CREATE OR REPLACE VIEW public.summaryreferral_v
AS SELECT data.tgl_transaksi AS "Tanggal Transaksi",
    data.nama_referal AS "Nama Referal",
    count(data.*) AS "Jumlah Pendaftaran"
   FROM ( SELECT pendaftaran_t.tgl_pendaftaran::date AS tgl_transaksi,
            COALESCE(btrim(pegawai_m.nama_pegawai::text), btrim(upper(pendaftaran_t.referal_luar::text))::character varying::text) AS nama_referal
           FROM pendaftaran_t
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.referal_pegawai_id = pegawai_m.pegawai_id
          WHERE pendaftaran_t.status_periksa::text <> ALL (ARRAY['628'::character varying, '453'::character varying, '402'::character varying]::text[])) data
  WHERE data.nama_referal IS NOT NULL
  GROUP BY data.tgl_transaksi, data.nama_referal;