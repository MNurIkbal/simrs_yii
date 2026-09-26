-- public.baranghistory_v source

CREATE OR REPLACE VIEW public.baranghistory_v
AS SELECT baranghistory_r.barang_id,
    baranghistory_r.barang_nama,
    baranghistory_r.tgl_baranghistory,
    baranghistory_r.harga_dasar,
    fgetnamalookup(baranghistory_r.keterangan::integer) AS keterangan,
    pegawai_m.nama_pegawai,
    baranghistory_r.catatan
   FROM baranghistory_r
     JOIN loginpemakai_k ON baranghistory_r.last_modified_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id;