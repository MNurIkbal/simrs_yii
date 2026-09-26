<?php

use yii\db\Migration;

/**
 * Class m220921_094205_view_gateway_penjualanobat
 */
class m220921_094205_view_gateway_penjualanobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_penjualanobat_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_penjualanobat_v\"
        AS SELECT penjualanresep_t.tglpenjualan,
            'penjualan reseptur'::text AS jenis_penjualan,
            penjualanresep_t.penjualanresep_id,
            reseptur_t.pendaftaran_id,
            pasien_m.pasien_id,
            pasien_m.nama_pasien AS nama_pembeli,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir,
            pegawairesep.nama_pegawai AS petugasresep,
            lookup_statusbayar.lookup_name AS status_penjualan,
            resepturdetail_t.data_resep
           FROM reseptur_t
             JOIN ( SELECT a.penjualanresep_id,
                    a.status_reseptur,
                    a.status_bayar,
                    a.tglpenjualan
                   FROM penjualanresep_t a) penjualanresep_t ON penjualanresep_t.penjualanresep_id = reseptur_t.penjualanresep_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id
                   FROM pendaftaran_t a) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.no_identitas_pasien,
                    a.nama_pasien,
                    a.tanggal_lahir
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawairesep ON reseptur_t.pegawai_id = pegawairesep.pegawai_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lookup_statusreseptur ON penjualanresep_t.status_reseptur::integer = lookup_statusreseptur.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lookup_statusbayar ON penjualanresep_t.status_bayar::integer = lookup_statusbayar.lookup_id
             JOIN ( SELECT reseptur.reseptur_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT racikan_m.racikan_nama AS jenis_racikan,
                                    obatalkes_m.obatalkes_id,
                                    obatalkes_m.obatalkes_kode,
                                    obatalkes_m.obatalkes_nama,
                                    resepturdetail_t_1.hargasatuan_reseptur AS hargasatuan,
                                    resepturdetail_t_1.harganetto_reseptur AS harganetto,
                                    resepturdetail_t_1.qty_reseptur AS qty,
                                    resepturdetail_t_1.hargajual_reseptur AS sub_total
                                   FROM resepturdetail_t resepturdetail_t_1
                                     LEFT JOIN ( SELECT a.racikan_id,
                                            a.racikan_nama
                                           FROM racikan_m a) racikan_m ON resepturdetail_t_1.racikan_id = racikan_m.racikan_id
                                     LEFT JOIN ( SELECT a.obatalkes_id,
                                            a.obatalkes_kode,
                                            a.obatalkes_nama
                                           FROM obatalkes_m a) obatalkes_m ON resepturdetail_t_1.obatalkes_id = obatalkes_m.obatalkes_id
                                  WHERE obatalkes_m.obatalkes_id IS NOT NULL AND reseptur.reseptur_id = resepturdetail_t_1.reseptur_id) d) AS data_resep
                   FROM reseptur_t reseptur
                  GROUP BY reseptur.reseptur_id) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
        UNION ALL
         SELECT obatalkespasien_t.tglpelayanan AS tglpenjualan,
            'penjualan obatalkespasien'::text AS jenis_penjualan,
            obatalkespasien_t.penjualanresep_id,
            obatalkespasien_t.pendaftaran_id,
            pasien_m.pasien_id,
            pasien_m.nama_pasien AS nama_pembeli,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir,
            pegawairesep.nama_pegawai AS petugasresep,
            '-'::text AS status_penjualan,
            detail_obat.data_resep
           FROM ( SELECT a.tglpelayanan,
                    a.penjualanresep_id,
                    a.pendaftaran_id,
                    a.pegawai_id
                   FROM obatalkespasien_t a
                  GROUP BY a.tglpelayanan, a.penjualanresep_id, a.pendaftaran_id, a.pegawai_id) obatalkespasien_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id
                   FROM pendaftaran_t a) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.no_identitas_pasien,
                    a.nama_pasien,
                    a.tanggal_lahir
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawairesep ON obatalkespasien_t.pegawai_id = pegawairesep.pegawai_id
             JOIN ( SELECT pendaftaran.pendaftaran_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT '-'::text AS jenis_racikan,
                                    obatalkes_m.obatalkes_kode,
                                    obatalkes_m.obatalkes_nama,
                                    obatalkespasien_t_1.hargasatuan_oa AS hargasatuan,
                                    obatalkespasien_t_1.harganetto_oa AS harganetto,
                                    obatalkespasien_t_1.qty_oa AS qty,
                                    obatalkespasien_t_1.hargajual_oa AS sub_total
                                   FROM obatalkespasien_t obatalkespasien_t_1
                                     LEFT JOIN ( SELECT a.racikan_id,
                                            a.racikan_nama
                                           FROM racikan_m a) racikan_m ON obatalkespasien_t_1.racikan_id = racikan_m.racikan_id
                                     LEFT JOIN ( SELECT a.obatalkes_id,
                                            a.obatalkes_kode,
                                            a.obatalkes_nama
                                           FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t_1.obatalkes_id = obatalkes_m.obatalkes_id
                                  WHERE obatalkespasien_t_1.penjualanresep_id IS NULL AND pendaftaran.pendaftaran_id = obatalkespasien_t_1.pendaftaran_id) d) AS data_resep
                   FROM pendaftaran_t pendaftaran
                  GROUP BY pendaftaran.pendaftaran_id) detail_obat ON pendaftaran_t.pendaftaran_id = detail_obat.pendaftaran_id
          WHERE obatalkespasien_t.penjualanresep_id IS NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_094205_view_gateway_penjualanobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_094205_view_gateway_penjualanobat cannot be reverted.\n";

        return false;
    }
    */
}
