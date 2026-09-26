-- public.nilaipemeriksaanlabdetail_v source

CREATE OR REPLACE VIEW public.nilaipemeriksaanlabdetail_v
AS SELECT x.pemeriksaanlab_id,
    x.daftartindakan_id,
    x.daftartindakan_nama,
    x.tipepaket_id,
    x.tipepaket_nama,
    x.nilairujukan_id,
    x.nama_rujukan,
    x.jenis_kelamin,
    x.jenis_kelamin_nama,
    x.golonganumur_id,
    x.gol_umurlab_nama,
    x.gol_umurlab_minimal,
    x.gol_umurlab_maksimal,
    x.nilai_rujukan,
    x.nilai_min,
    x.nilai_max,
    x.satuanlab_nama,
    x.keterangan,
    x.hasil,
    x.petugaslab_id,
    x.hasilpemeriksaanlabdetail_id,
    x.petugaslab_nama,
    x.samplelab_id,
    x.pasienmasukpenunjang_id,
    x.hasilpemeriksaanlab_id,
    x.is_deleted,
    x.metode,
    x.jenispemeriksaanlab_id,
    x.jenispemeriksaanlab_nama,
    x.no_urut,
    x.is_verifikasi,
    x.tanggal_verifikasi,
    x.petugas_verifikasi,
    x.tipe
   FROM ( SELECT pemeriksaanlab_m.pemeriksaanlab_id,
            pemeriksaanlab_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            nilairujukan_m.nilairujukan_id,
            COALESCE(nilairujukan_m.nama_rujukan, daftartindakan_m.daftartindakan_nama) AS nama_rujukan,
            nilairujukan_m.jenis_kelamin,
            fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
            nilairujukan_m.golonganumur_id,
            golonganumurlab_m.gol_umurlab_nama,
            nilairujukan_m.umur_awal AS gol_umurlab_minimal,
            nilairujukan_m.umur_akhir AS gol_umurlab_maksimal,
            nilairujukan_m.nilai_rujukan,
            nilairujukan_m.nilai_min,
            nilairujukan_m.nilai_max,
            nilairujukan_m.satuan_hasillab AS satuanlab_nama,
            nilairujukan_m.keterangan,
            hasilpemeriksaanlabdetail_t.hasil,
            hasilpemeriksaanlabdetail_t.petugaslab_id,
            hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
            petugaslab.nama_pegawai AS petugaslab_nama,
            ambilsample_t.samplelab_id,
            ambilsample_t.pasienmasukpenunjang_id,
            hasilpemeriksaanlab_t.hasilpemeriksaanlab_id, 
            nilairujukan_m.is_deleted,
            hasilpemeriksaanlabdetail_t.metode,
            jenispemeriksaanlab_m.jenispemeriksaanlab_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
            COALESCE(hasilpemeriksaanlabdetail_t.no_urut, nilairujukan_m.no_urut)::integer AS no_urut,
            hasilpemeriksaanlabdetail_t.is_verifikasi,
            hasilpemeriksaanlabdetail_t.tanggal_verifikasi,
            hasilpemeriksaanlabdetail_t.petugas_verifikasi,
            'tindakan'::text AS tipe
           FROM ambilsample_t
             JOIN ( SELECT a.pemeriksaanlab_id,
                    a.jenispemeriksaanlab_id,
                    a.daftartindakan_id
                   FROM pemeriksaanlab_m a
                  WHERE a.is_deleted = false AND a.is_active = true) pemeriksaanlab_m ON ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.daftartindakan_id
             JOIN ( SELECT a.jenispemeriksaanlab_id,
                    a.jenispemeriksaanlab_nama
                   FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a
                  WHERE a.is_deleted = false AND a.is_active = true) daftartindakan_m ON pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT a.pemeriksaanlab_id,
                    a.golonganumur_id,
                    a.nilairujukan_id,
                    a.nama_rujukan,
                    a.jenis_kelamin,
                    a.nilai_rujukan,
                    a.nilai_min,
                    a.nilai_max,
                    a.satuan_hasillab,
                    a.keterangan,
                    a.is_deleted,
                    a.no_urut,
                    a.umur_awal,
                    a.umur_akhir
                   FROM nilairujukan_m a
                  WHERE a.is_active = true) nilairujukan_m ON pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id
             LEFT JOIN ( SELECT a.golonganumurlab_id,
                    a.gol_umurlab_nama,
                    a.gol_umurlab_minimal,
                    a.gol_umurlab_maksimal
                   FROM golonganumurlab_m a) golonganumurlab_m ON nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id
             LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.hasilpemeriksaanlab_id,
                    a.samplelab_id,
                    a.is_deleted
                   FROM hasilpemeriksaanlab_t a
                  WHERE a.is_deleted IS FALSE) hasilpemeriksaanlab_t ON ambilsample_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id
             LEFT JOIN ( SELECT a.pemeriksaanlab_id,
                    a.nilairujukan_id,
                    a.hasilpemeriksaanlab_id,
                    a.petugaslab_id,
                    a.samplelab_id,
                    a.hasil,
                    a.hasilpemeriksaanlabdetail_id,
                    a.metode,
                    a.no_urut,
                    a.is_verifikasi,
                    a.tanggal_verifikasi,
                    a.petugas_verifikasi
                   FROM hasilpemeriksaanlabdetail_t a
                  WHERE a.is_deleted IS FALSE AND a.is_active IS TRUE) hasilpemeriksaanlabdetail_t ON pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id AND nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id AND hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugaslab ON hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id
          WHERE ambilsample_t.is_deleted = false AND ambilsample_t.is_active = true
        UNION ALL
         SELECT pemeriksaanlab_m.pemeriksaanlab_id,
            paketpelayanan_mp.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanlab_m.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            nilairujukan_m.nilairujukan_id,
            COALESCE(nilairujukan_m.nama_rujukan, daftartindakan_m.daftartindakan_nama) AS nama_rujukan,
            nilairujukan_m.jenis_kelamin,
            fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
            nilairujukan_m.golonganumur_id,
            golonganumurlab_m.gol_umurlab_nama,
            nilairujukan_m.umur_awal AS gol_umurlab_minimal,
            nilairujukan_m.umur_akhir AS gol_umurlab_maksimal,
            nilairujukan_m.nilai_rujukan,
            nilairujukan_m.nilai_min,
            nilairujukan_m.nilai_max,
            nilairujukan_m.satuan_hasillab AS satuanlab_nama,
            nilairujukan_m.keterangan,
            hasilpemeriksaanlabdetail_t.hasil,
            hasilpemeriksaanlabdetail_t.petugaslab_id,
            hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
            petugaslab.nama_pegawai AS petugaslab_nama,
            ambilsample_t.samplelab_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
            nilairujukan_m.is_deleted,
            hasilpemeriksaanlabdetail_t.metode,
            jenispemeriksaanlab_m.jenispemeriksaanlab_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
            COALESCE(hasilpemeriksaanlabdetail_t.no_urut, nilairujukan_m.no_urut)::integer AS no_urut,
            hasilpemeriksaanlabdetail_t.is_verifikasi,
            hasilpemeriksaanlabdetail_t.tanggal_verifikasi,
            hasilpemeriksaanlabdetail_t.petugas_verifikasi,
            'paket'::text AS tipe
           FROM ambilsample_t
             JOIN pemeriksaanlab_m ON ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.tipepaket_id
             JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
             JOIN pasienmasukpenunjang_t ON ambilsample_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
             JOIN tipepaket_m ON pemeriksaanlab_m.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN paketpelayanan_mp ON pemeriksaanlab_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN nilairujukan_m ON pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id
             LEFT JOIN golonganumurlab_m ON nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id
             LEFT JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id AND hasilpemeriksaanlab_t.is_deleted IS FALSE
             LEFT JOIN hasilpemeriksaanlabdetail_t ON pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id AND nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id AND hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id AND hasilpemeriksaanlabdetail_t.is_deleted IS FALSE AND hasilpemeriksaanlabdetail_t.is_active IS TRUE
             LEFT JOIN pegawai_m petugaslab ON hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id
             LEFT JOIN samplelab_m ON hasilpemeriksaanlabdetail_t.samplelab_id = samplelab_m.samplelab_id
          WHERE ambilsample_t.is_deleted = false AND ambilsample_t.is_active = true AND pemeriksaanlab_m.is_deleted = false AND pemeriksaanlab_m.is_active = true AND daftartindakan_m.is_deleted = false AND daftartindakan_m.is_active = true AND nilairujukan_m.is_active = true) x
  ORDER BY x.no_urut;