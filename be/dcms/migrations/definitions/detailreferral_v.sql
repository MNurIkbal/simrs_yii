-- public.detailreferral_v source

CREATE OR REPLACE VIEW public.detailreferral_v
AS SELECT data.tanggal AS "Tanggal",
    data.nama_referal AS "Nama Referral",
    data.no_pendaftaran AS "No Pendaftaran",
    data.nama_pasien AS "Nama Pasien",
    data.cara_bayar AS "Cara Bayar",
    data.penjamin AS "Penjamin"
   FROM ( SELECT pendaftaran_t.tgl_pendaftaran::date AS tanggal,
            COALESCE(btrim(pegawai_m.nama_pegawai::text), btrim(upper(pendaftaran_t.referal_luar::text))::character varying::text) AS nama_referal,
            pendaftaran_t.no_pendaftaran,
            pasien_m.nama_pasien,
            carabayar_m.carabayar_nama::text AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.referal_pegawai_id = pegawai_m.pegawai_id
          WHERE pendaftaran_t.status_periksa::text <> ALL (ARRAY['628'::character varying::text, '453'::character varying::text, '402'::character varying::text])) data
  WHERE data.nama_referal IS NOT NULL
  ORDER BY data.tanggal;