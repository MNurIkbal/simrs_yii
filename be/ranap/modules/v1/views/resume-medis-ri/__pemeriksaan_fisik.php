<div class="section-table">
    <table class="tbl tbl-no-bordered" width="100%">
        <tr>
            <td class="label">Berat Badan</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['berat_badan']) ? $data['berat_badan'] : '' ?> Kg</td>
            <td class="label">Tinggi Badan</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['tinggi_badan']) ? $data['tinggi_badan'] : '' ?> Cm</td>
            <td class="label">TD</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['td']) ? $data['td'] : '' ?> mmHg</td>
        </tr>
        <tr>
            <td class="label">Nadi</td>
            <td width="8px">:</td>
            <td class="value-label"><?=isset($data['nadi']) ? $data['nadi'] : ''?> x/menit</td>
            <td class="label">RR</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['rr']) ? $data['rr'] : '' ?> x/menit</td>
            <td class="label">Suhu</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['suhu']) ? $data['suhu'] : '' ?> °C</td>
        </tr>
    </table>
</div>
