<?php

use yii\db\Migration;

/**
 * Class m210728_044651_feature_US667_sy_pendaftaran_v
 */
class m210728_044651_feature_US667_sy_pendaftaran_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.sy_pendaftaran_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_pendaftaran_v\" AS
            SELECT 'RDRJPENUNJANG'::text AS tipe,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.pasienbatalperiksa_id,
            pendaftaran_t.penanggungjawab_id,
            penanggungjawab_m.penanggungjawab_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pegawai_m.nama_pegawai AS nama_dokterrj,
            pegawai_m.additional_data AS kode_dokterrj,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pegawai_rd.nama_pegawai AS nama_dokterri,
            pegawai_rd.additional_data AS kode_dokterri,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.caramasuk_id,
            asalrujukan_m.asalrujukan_nama AS caramasuk_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.no_pembayaran,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_nama,
            pendaftaran_t.antrian_id,
            antrian_t.no_antrian,
            pendaftaran_t.karcis_id,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.no_urutantri,
            fgetnamalookup((pendaftaran_t.transportasi)::integer) AS transportasi,
            fgetnamalookup((pendaftaran_t.keadaan_masuk)::integer) AS keadaan_masuk,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
            fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan_nama,
            kunj.lookup_kode AS kunjungan,
            fgetnamalookup((pendaftaran_t.status_masuk)::integer) AS status_masuk,
            pendaftaran_t.umur,
            pendaftaran_t.tgl_selesaiperiksa,
            pendaftaran_t.keterangan_pendaftaran,
            fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasi,
            pendaftaran_t.asuransipasien_id,
            asuransipasien_m.nama_asuransi,
            pendaftaran_t.tgl_akandilayani,
            fgetnamalookup((pendaftaran_t.statusdok_rekammedik)::integer) AS statusdok_rekammedik,
            pendaftaran_t.bpjs_id,
            bpjs_t.nokartuasuransi,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verifikasi,
            pendaftaran_t.limit_tagihan,
            pendaftaran_t.additional_data,
            ruangan_m.additional_data AS instalasi_singkatan,
            kelaspelayanan_m.additional_data AS kelaspelayanan_singkatan,
            kamarruangan_m.additional_data AS kdruangri,
            kamartempattidur_m.no_tempattidur AS nobed,
            asalrujukan_m.asalrujukan_kode AS cara_masuk,
            carabayar_m.additional_data AS klppng,
            carabayar_m.carabayar_singkatan AS vkkelreg,
            rmed.ruangan_nama AS kdbagianrm,
            bpjs_t.nokartuasuransi AS nobpjs,
            look_status.lookup_kode AS status_kode,
            NULL::character varying AS prosedurmasuk,
            NULL::text AS hakkelas,
            NULL::text AS kelaspermintaan,
            NULL::text AS dokterkonsul,
            NULL::character varying AS diagnosa_awal,
            NULL::text AS dokterpengirim,
            pasienbatalperiksa_t.alasan_batal
            FROM (((((((((((((((((((((((pendaftaran_t
            JOIN ruangan_m rmed ON ((rmed.ruangan_id = 16)))
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pegawai_m pegawai_rd ON ((pasienadmisi_t.pegawai_id = pegawai_rd.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN asalrujukan_m ON ((pendaftaran_t.caramasuk_id = asalrujukan_m.asalrujukan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
            LEFT JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN antrian_t ON ((pendaftaran_t.antrian_id = antrian_t.antrian_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN lookup_m kunj ON (((pendaftaran_t.kunjungan)::integer = kunj.lookup_id)))
            LEFT JOIN kamarruangan_m ON ((pendaftaran_t.ruangan_id = kamarruangan_m.ruangan_id)))
            LEFT JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
            LEFT JOIN lookup_m look_status ON (((pendaftaran_t.status_periksa)::text = ((look_status.lookup_id)::character varying)::text)))
            LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
            WHERE (pendaftaran_t.instalasi_id <> 3)
            UNION ALL
            SELECT 'RI'::text AS tipe,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_pendaftaran,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.pasienbatalperiksa_id,
            pendaftaran_t.penanggungjawab_id,
            penanggungjawab_m.penanggungjawab_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pegawai_m.nama_pegawai AS nama_dokterrj,
            pegawai_m.additional_data AS kode_dokterrj,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pegawai_rd.nama_pegawai AS nama_dokterri,
            pegawai_rd.additional_data AS kode_dokterri,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.caramasuk_id,
            asalrujukan_m.asalrujukan_nama AS caramasuk_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.no_pembayaran,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_nama,
            pendaftaran_t.antrian_id,
            antrian_t.no_antrian,
            pendaftaran_t.karcis_id,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.no_urutantri,
            fgetnamalookup((pendaftaran_t.transportasi)::integer) AS transportasi,
            fgetnamalookup((pendaftaran_t.keadaan_masuk)::integer) AS keadaan_masuk,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
            fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan_nama,
            kunj.lookup_kode AS kunjungan,
            fgetnamalookup((pendaftaran_t.status_masuk)::integer) AS status_masuk,
            pendaftaran_t.umur,
            pendaftaran_t.tgl_selesaiperiksa,
            pendaftaran_t.keterangan_pendaftaran,
            fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasi,
            pendaftaran_t.asuransipasien_id,
            asuransipasien_m.nama_asuransi,
            pendaftaran_t.tgl_akandilayani,
            fgetnamalookup((pendaftaran_t.statusdok_rekammedik)::integer) AS statusdok_rekammedik,
            pendaftaran_t.bpjs_id,
            bpjs_t.nokartuasuransi,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verifikasi,
            pendaftaran_t.limit_tagihan,
            pendaftaran_t.additional_data,
            ruangan_m.additional_data AS instalasi_singkatan,
            kelaspelayanan_m.additional_data AS kelaspelayanan_singkatan,
            kamarruangan_m.additional_data AS kdruangri,
            kamartempattidur_m.no_tempattidur AS nobed,
            asalrujukan_m.asalrujukan_kode AS cara_masuk,
            carabayar_m.additional_data AS klppng,
            carabayar_m.carabayar_singkatan AS vkkelreg,
            rmed.ruangan_nama AS kdbagianrm,
            bpjs_t.nokartuasuransi AS nobpjs,
            look_status.lookup_kode AS status_kode,
            pasienadmisi_t.prosedurmasuk_id AS prosedurmasuk,
            hakkelas.additional_data AS hakkelas,
            kelaspermintaan.additional_data AS kelaspermintaan,
            pasienadmisi_t.dokterkonsul_id AS dokterkonsul,
            pasienadmisi_t.diagnosa_awal,
            dokterpengirim.additional_data AS dokterpengirim,
            pasienbatalperiksa_t.alasan_batal
            FROM (((((((((((((((((((((((((((pendaftaran_t
            JOIN ruangan_m rmed ON ((rmed.ruangan_id = 16)))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            LEFT JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pegawai_m pegawai_rd ON ((pasienadmisi_t.pegawai_id = pegawai_rd.pegawai_id)))
            LEFT JOIN pegawai_m dokterpengirim ON ((pasienadmisi_t.dokterpengirim_id = dokterpengirim.pegawai_id)))
            LEFT JOIN asalrujukan_m ON ((pendaftaran_t.caramasuk_id = asalrujukan_m.asalrujukan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
            LEFT JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN kelaspelayanan_m hakkelas ON ((pasienadmisi_t.hakkelas_id = hakkelas.kelaspelayanan_id)))
            LEFT JOIN kelaspelayanan_m kelaspermintaan ON ((pasienadmisi_t.kelaspermintaan_id = kelaspermintaan.kelaspelayanan_id)))
            LEFT JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN antrian_t ON ((pendaftaran_t.antrian_id = antrian_t.antrian_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN lookup_m kunj ON (((pendaftaran_t.kunjungan)::integer = kunj.lookup_id)))
            LEFT JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            LEFT JOIN kamartempattidur_m ON ((masukkamar_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
            LEFT JOIN kamarruangan_m ON ((masukkamar_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
            LEFT JOIN lookup_m look_status ON (((pendaftaran_t.status_periksa)::text = ((look_status.lookup_id)::character varying)::text)))
            LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
            WHERE (pendaftaran_t.instalasi_id = 3)
            ;");
            $this->execute('
                ALTER TABLE public.sy_pendaftaran_v OWNER TO postgres;
            ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210728_044651_feature_US667_sy_pendaftaran_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210728_044651_feature_US667_sy_pendaftaran_v cannot be reverted.\n";

        return false;
    }
    */
}
