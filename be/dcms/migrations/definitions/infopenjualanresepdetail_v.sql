-- public.infopenjualanresepdetail_v source

CREATE OR REPLACE VIEW public.infopenjualanresepdetail_v
AS SELECT pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.jenispenjualan,
    lookup_jenispenjualan.lookup_name AS jenis_penjualan,
    penjualanresep_t.tglresep,
    penjualanresep_t.noresep,
    penjualanresep_t.totharganetto,
    penjualanresep_t.totalhargajual,
    penjualanresep_t.totaltarifservice,
    penjualanresep_t.biayaadministrasi,
    penjualanresep_t.biayakonseling,
    penjualanresep_t.pembulatanharga,
    penjualanresep_t.jasadokterresep,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai,
    pegawai_m.gelardepan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    penjualanresep_t.tglpenjualan,
    penjualanresep_t.discount,
    penjualanresep_t.subsidiasuransi,
    penjualanresep_t.subsidipemerintah,
    penjualanresep_t.subsidirs,
    penjualanresep_t.iurbiaya,
    penjualanresep_t.lamapelayanan,
    penjualanresep_t.pasienadmisi_id,
    penjualanresep_t.reseptur_id,
    pendaftaran_t.pendaftaran_id,
    obatalkes_m.obatalkes_id,
    jenisobatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nobatch AS obatalkes_golongan,
    obatalkes_m.obatalkes_kategori,
    obatalkes_m.obatalkes_kadarobat,
    obatalkes_m.kekuatan_obat AS kekuatan,
    obatalkes_m.ppn_persen,
    obatalkespasien_t.racikan_id,
    obatalkespasien_t.shift_id,
    obatalkespasien_t.tglpelayanan,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargasatuan_oa,
    signaobat_m.signa_nama AS signa_oa,
    obatalkespasien_t.harganetto_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.etiket,
    obatalkespasien_t.biayaservice,
    obatalkespasien_t.biayakemasan,
    obatalkespasien_t.oa,
    sumberdana_m.sumberdana_id,
    sumberdana_m.sumberdana_nama,
    satuanunit_m.satuanunit_id AS satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuankecil_nama,
    obatalkespasien_t.tipepaket_id,
    obatsudahbayar_t.obatsudahbayar_id,
    pasien_m.statusperkawinan,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    antrianfarmasi_t.antrianfarmasi_id,
    antrianfarmasi_t.no_antrian,
    antrianfarmasi_t.panggil_antrian,
    antrianfarmasi_t.antrian_lewat,
    antrianfarmasi_t.tglambil_antrian,
    racikan_m.racikan_id AS racikanantrian_id,
    racikan_m.racikan_nama AS racikanantrian_nama,
    racikan_m.racikan_singkatan AS racikanantrian_singkatan,
    racikan_m.tarif_service AS racikanantrian_tarifservice,
    racikan_m.persen_service AS racikanantrian_persenservice,
    racikan_m.biaya_kemasan AS racikanantrian_biayakemasan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    pembayaranpelayanan_t.tgl_pembayaran,
    pembayaranpelayanan_t.no_pembayaran,
    tandabuktibayar_t.tandabuktibayar_id,
    tandabuktibayar_t.tglbuktibayar,
    tandabuktibayar_t.nobuktibayar,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pegawaipasien.nomorindukpegawai AS nomorindukpasien,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.iter,
    penjualanresep_t.nama_pembeli,
    karyawan.nama_pegawai AS nama_karyawan,
    obatalkes_m.obatalkes_nama,
    peg_reseptur.nama_pegawai AS pegawai_reseptur,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.resepturdetail_id,
    resepturdetail_t.additional_data::json ->> 'satuaninput_id'::text AS satuaninput_id,
    resepturdetail_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    resepturdetail_t.additional_data::json ->> 'satuankonversi_id'::text AS satuankonversi_id,
    resepturdetail_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    resepturdetail_t.hargasatuan_reseptur,
    resepturdetail_t.harganetto_reseptur,
    resepturdetail_t.hargajual_reseptur,
    resepturdetail_t.qty_reseptur,
    resepturdetail_t.etiket AS etiket_reseptur,
    obatalkespasien_t.det,
    obatalkespasien_t.det_konversi,
    obatalkespasien_t.det_medis,
    obatalkespasien_t.qty_medis
   FROM obatalkespasien_t
     LEFT JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.namadepan,
            pasien_m_1.nama_pasien,
            pasien_m_1.nama_panggilan AS nama_bin,
            pasien_m_1.jeniskelamin,
            pasien_m_1.tempat_lahir,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.alamat_pasien,
            pasien_m_1.rt,
            pasien_m_1.rw,
            pasien_m_1.statusperkawinan,
            pasien_m_1.agama,
            pasien_m_1.golongandarah,
            pasien_m_1.rhesus,
            pasien_m_1.anakke,
            pasien_m_1.jumlah_bersaudara,
            pasien_m_1.no_telepon_pasien,
            pasien_m_1.no_mobile_pasien,
            pasien_m_1.warga_negara,
            pasien_m_1.pegawai_id,
            pasien_m_1.photopasien,
            pasien_m_1.alamatemail
           FROM pasien_m pasien_m_1) pasien_m ON obatalkespasien_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT penjualanresep_t_1.penjualanresep_id,
            penjualanresep_t_1.jenispenjualan,
            penjualanresep_t_1.tglresep,
            penjualanresep_t_1.noresep,
            penjualanresep_t_1.totharganetto,
            penjualanresep_t_1.totalhargajual,
            penjualanresep_t_1.totaltarifservice,
            penjualanresep_t_1.biayaadministrasi,
            penjualanresep_t_1.biayakonseling,
            penjualanresep_t_1.pembulatanharga,
            penjualanresep_t_1.jasadokterresep,
            penjualanresep_t_1.tglpenjualan,
            penjualanresep_t_1.discount,
            penjualanresep_t_1.subsidiasuransi,
            penjualanresep_t_1.subsidipemerintah,
            penjualanresep_t_1.subsidirs,
            penjualanresep_t_1.iurbiaya,
            penjualanresep_t_1.lamapelayanan,
            penjualanresep_t_1.pasienadmisi_id,
            penjualanresep_t_1.reseptur_id,
            penjualanresep_t_1.iter,
            penjualanresep_t_1.pendaftaran_id,
            penjualanresep_t_1.pegawai_id,
            penjualanresep_t_1.carabayar_id,
            penjualanresep_t_1.penjamin_id,
            penjualanresep_t_1.antrianfarmasi_id,
            penjualanresep_t_1.ruangan_id,
            penjualanresep_t_1.karyawan_id,
            penjualanresep_t_1.is_active,
            penjualanresep_t_1.is_deleted,
            penjualanresep_t_1.nama_pembeli
           FROM penjualanresep_t penjualanresep_t_1) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT reseptur_t_1.reseptur_id,
            reseptur_t_1.pegawai_id
           FROM reseptur_t reseptur_t_1) reseptur_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai
           FROM pegawai_m pegawai_m_1) peg_reseptur ON reseptur_t.pegawai_id = peg_reseptur.pegawai_id
     LEFT JOIN ( SELECT pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.pendaftaran_id
           FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai,
            pegawai_m_1.gelardepan
           FROM pegawai_m pegawai_m_1) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT carabayar_m_1.carabayar_id,
            carabayar_m_1.carabayar_nama
           FROM carabayar_m carabayar_m_1) carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT penjamin_m_1.penjamin_id,
            penjamin_m_1.penjamin_nama
           FROM penjamin_m penjamin_m_1) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT obatalkes_m_1.obatalkes_id,
            obatalkes_m_1.obatalkes_kode,
            obatalkes_m_1.obatalkes_namalain,
            obatalkes_m_1.obatalkes_nobatch,
            obatalkes_m_1.obatalkes_kategori,
            obatalkes_m_1.obatalkes_kadarobat,
            obatalkes_m_1.kekuatan_obat,
            obatalkes_m_1.ppn_persen,
            obatalkes_m_1.jenisobatalkes_id,
            obatalkes_m_1.obatalkes_nama
           FROM obatalkes_m obatalkes_m_1) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT jenisobatalkes_m_1.jenisobatalkes_id,
            jenisobatalkes_m_1.jenisobatalkes_nama
           FROM jenisobatalkes_m jenisobatalkes_m_1) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT sumberdana_m_1.sumberdana_id,
            sumberdana_m_1.sumberdana_nama
           FROM sumberdana_m sumberdana_m_1) sumberdana_m ON obatalkespasien_t.sumberdana_id = sumberdana_m.sumberdana_id
     LEFT JOIN ( SELECT satuanunit_m_1.satuanunit_id,
            satuanunit_m_1.satuanunit_nama
           FROM satuanunit_m satuanunit_m_1) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN ( SELECT antrianfarmasi_t_1.antrianfarmasi_id,
            antrianfarmasi_t_1.no_antrian,
            antrianfarmasi_t_1.racikan_id,
            antrianfarmasi_t_1.panggil_antrian,
            antrianfarmasi_t_1.antrian_lewat,
            antrianfarmasi_t_1.tglambil_antrian
           FROM antrianfarmasi_t antrianfarmasi_t_1) antrianfarmasi_t ON penjualanresep_t.antrianfarmasi_id = antrianfarmasi_t.antrianfarmasi_id
     LEFT JOIN ( SELECT racikan_m_1.racikan_id,
            racikan_m_1.racikan_nama,
            racikan_m_1.racikan_singkatan,
            racikan_m_1.tarif_service,
            racikan_m_1.persen_service,
            racikan_m_1.biaya_kemasan
           FROM racikan_m racikan_m_1) racikan_m ON antrianfarmasi_t.racikan_id = racikan_m.racikan_id
     JOIN ( SELECT ruangan_m_1.ruangan_id,
            ruangan_m_1.ruangan_nama,
            ruangan_m_1.instalasi_id
           FROM ruangan_m ruangan_m_1) ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT instalasi_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama
           FROM instalasi_m instalasi_m_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT obatsudahbayar_t_1.obatsudahbayar_id,
            obatsudahbayar_t_1.pembayaranpelayanan_id
           FROM obatsudahbayar_t obatsudahbayar_t_1) obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaranpelayanan_id,
            pembayaranpelayanan_t_1.tgl_pembayaran,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     LEFT JOIN ( SELECT tandabuktibayar_t_1.tandabuktibayar_id,
            tandabuktibayar_t_1.tglbuktibayar,
            tandabuktibayar_t_1.nobuktibayar,
            tandabuktibayar_t_1.pembayaranpelayanan_id
           FROM tandabuktibayar_t tandabuktibayar_t_1) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nomorindukpegawai
           FROM pegawai_m pegawai_m_1) pegawaipasien ON pasien_m.pegawai_id = pegawaipasien.pegawai_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai
           FROM pegawai_m pegawai_m_1) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN ( SELECT signaobat_m_1.signa_id,
            signaobat_m_1.signa_nama
           FROM signaobat_m signaobat_m_1) signaobat_m ON
        CASE
            WHEN obatalkespasien_t.signa_oa IS NULL OR obatalkespasien_t.signa_oa::text = ''::text THEN '999'::character varying
            ELSE obatalkespasien_t.signa_oa
        END::integer = signaobat_m.signa_id
     LEFT JOIN ( SELECT resepturdetail_t_1.additional_data,
            resepturdetail_t_1.resepturdetail_id,
            resepturdetail_t_1.hargasatuan_reseptur,
            resepturdetail_t_1.harganetto_reseptur,
            resepturdetail_t_1.hargajual_reseptur,
            resepturdetail_t_1.qty_reseptur,
            resepturdetail_t_1.etiket,
            resepturdetail_t_1.satuankecil_id
           FROM resepturdetail_t resepturdetail_t_1) resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
     LEFT JOIN ( SELECT satuanunit_m_1.satuanunit_id,
            satuanunit_m_1.satuanunit_nama
           FROM satuanunit_m satuanunit_m_1) satuan_input ON resepturdetail_t.satuankecil_id = satuan_input.satuanunit_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) lookup_jenispenjualan ON penjualanresep_t.jenispenjualan::integer = lookup_jenispenjualan.lookup_id
  WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false AND obatalkespasien_t.is_deleted = false;