-- public.infopembayarantransaksi_v source

CREATE OR REPLACE VIEW public.infopembayarantransaksi_v
AS SELECT pembayarantransaksi_t.pembayarantransaksi_id,
    pembayarantransaksi_t.jenis_transaksi,
    fgetnamalookup(pembayarantransaksi_t.jenis_transaksi::integer) AS jenis,
    pembayarantransaksi_t.tgl_transaksi,
    pembayarantransaksi_t.no_transaksi,
    pembayarantransaksi_t.tipe_transaksi,
    fgetnamalookup(pembayarantransaksi_t.tipe_transaksi::integer) AS tipe,
    pembayarantransaksi_t.supplier_id,
    supplier_m.supplier_nama,
    pembayarantransaksi_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pembayarantransaksi_t.pasien_id,
    pasien_m.nama_pasien,
    pembayarantransaksi_t.metode_pembayaran,
    fgetnamalookup(pembayarantransaksi_t.metode_pembayaran::integer) AS metode_pembayaran_nama,
    pembayarantransaksi_t.jumlah,
    pembayarantransaksi_t.kategoritransaksi_id,
    kategoritransaksi_m.kategoritransaksi_nama,
    pembayarantransaksi_t.deskripsi,
    pembayarantransaksi_t.referensi,
        CASE
            WHEN pembayarantransaksi_t.tipe_transaksi = 700 THEN supplier_m.supplier_nama
            WHEN pembayarantransaksi_t.tipe_transaksi = 701 THEN pegawai_m.nama_pegawai
            WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien_m.nama_pasien::character varying
            ELSE NULL::character varying
        END AS dari_kepada,
    pembayarantransaksi_t.created_by,
    pembuat.nama_pegawai AS created_by_nama
   FROM pembayarantransaksi_t
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pasien_id,
            concat(lkp_namadepan.lookup_name, a.nama_pasien) AS nama_pasien
           FROM pasien_m a
             LEFT JOIN lookup_m lkp_namadepan ON a.namadepan::integer = lkp_namadepan.lookup_id) pasien_m ON pembayarantransaksi_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON pembayarantransaksi_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.kategoritransaksi_id,
            a.kategoritransaksi_nama
           FROM kategoritransaksi_m a) kategoritransaksi_m ON pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            pegawai_pembuat.nama_pegawai
           FROM loginpemakai_k a
             LEFT JOIN ( SELECT b.pegawai_id,
                    b.nama_pegawai
                   FROM pegawai_m b) pegawai_pembuat ON a.pegawai_id = pegawai_pembuat.pegawai_id) pembuat ON pembayarantransaksi_t.created_by = pembuat.loginpemakai_id
  WHERE pembayarantransaksi_t.is_deleted = false;
