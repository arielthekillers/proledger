<?php
$kotaOptions = [];
foreach ($tujuanList as $t) {
    if (!empty($t['kab_kota'])) {
        $kotaOptions[$t['kab_kota']] = true;
    }
}
$kotaList = array_keys($kotaOptions);
sort($kotaList);
?>

<div class="max-w-7xl mx-auto space-y-4">
    <!-- Top Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="<?= APP_URL ?>/kolaborasi/detail/<?= (int)$kolab['id'] ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 text-xs shadow-2xs">
                <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
                <span>Kembali ke Kolaborasi</span>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-800">Analisa Distribusi Per Tujuan</h1>
                <p class="text-[11px] text-slate-500"><?= htmlspecialchars($kolab['nama']) ?> &bull; Konsolidasi barang lintas perusahaan ke tujuan yang sama agar pengiriman bisa digabung</p>
            </div>
        </div>
    </div>

    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-2xs">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lokasi Tujuan</div>
            <div class="text-xl font-bold text-slate-800 mt-1"><?= (int)$totals['tujuan'] ?> Lokasi</div>
            <div class="text-[11px] text-slate-500"><?= (int)$totals['items'] ?> total item distribusi</div>
        </div>
        <div class="bg-emerald-50/50 rounded-2xl border border-emerald-200/80 p-3.5 shadow-2xs">
            <div class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Sudah Terkirim</div>
            <div class="text-xl font-bold text-emerald-800 mt-1"><?= (int)$totals['terkirim'] ?> Item</div>
            <div class="text-[11px] text-emerald-600">Tiba di Tujuan / Gudang</div>
        </div>
        <div class="bg-amber-50/50 rounded-2xl border border-amber-200/80 p-3.5 shadow-2xs">
            <div class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Sedang Dikirim / Transit</div>
            <div class="text-xl font-bold text-amber-900 mt-1"><?= (int)$totals['sedang_dikirim'] ?> Item</div>
            <div class="text-[11px] text-amber-700">Potensial digabung / titip kirim</div>
        </div>
        <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-3.5 shadow-2xs">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Belum Dipesan</div>
            <div class="text-xl font-bold text-slate-800 mt-1"><?= (int)$totals['belum_dipesan'] ?> Item</div>
            <div class="text-[11px] text-slate-500">Menunggu pemesanan toko</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-3 flex flex-wrap items-center justify-between gap-2.5 shadow-2xs">
        <div class="flex flex-wrap items-center gap-1.5" id="distFilterTabs">
            <button type="button" onclick="setDistBucketFilter('all', this)" class="dist-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-600 text-white cursor-pointer">
                Semua Tujuan
            </button>
            <button type="button" onclick="setDistBucketFilter('pending', this)" class="dist-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer">
                Perlu Dikirim / Gabung
            </button>
            <button type="button" onclick="setDistBucketFilter('sedang_dikirim', this)" class="dist-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer">
                Sedang Dikirim
            </button>
            <button type="button" onclick="setDistBucketFilter('belum_dipesan', this)" class="dist-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer">
                Belum Dipesan
            </button>
            <button type="button" onclick="setDistBucketFilter('terkirim', this)" class="dist-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer">
                Sudah Terkirim
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <input type="text" id="searchTujuanInput" oninput="filterTujuanCards()"
                   placeholder="Cari sekolah, barang, perusahaan..."
                   class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs w-56 focus:outline-none focus:border-blue-500">
            <select id="filterKotaSelect" onchange="filterTujuanCards()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 cursor-pointer">
                <option value="">Semua Wilayah</option>
                <?php foreach ($kotaList as $kt): ?>
                    <option value="<?= htmlspecialchars(strtolower($kt)) ?>"><?= htmlspecialchars($kt) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Cards Per Tujuan -->
    <div class="space-y-3" id="tujuanCardsContainer">
        <?php if (empty($tujuanList)): ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-10 text-center text-xs text-slate-400">
                Belum ada data alokasi distribusi pada ruang kolaborasi ini.
            </div>
        <?php else: ?>
            <?php foreach ($tujuanList as $idx => $t):
                $searchBlob = strtolower($t['nama_tujuan'] . ' ' . $t['kab_kota'] . ' ' . implode(' ', array_map(fn($x) => $x['komponen'] . ' ' . $x['perusahaan_nama'] . ' ' . $x['kode_paket'] . ' ' . $x['nama_paket'], $t['items'])));
            ?>
                <div class="tujuan-card bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs"
                     data-search="<?= htmlspecialchars($searchBlob) ?>"
                     data-kota="<?= htmlspecialchars(strtolower($t['kab_kota'])) ?>"
                     data-pending="<?= (int)$t['pending_count'] ?>"
                     data-sedang="<?= (int)$t['cnt_sedang'] ?>"
                     data-belum="<?= (int)$t['cnt_belum'] ?>"
                     data-terkirim="<?= (int)$t['cnt_terkirim'] ?>">

                    <div class="px-4 py-3 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 cursor-pointer"
                         onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($t['nama_tujuan']) ?></span>
                                <?php if (!empty($t['kab_kota'])): ?>
                                    <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600 text-[10px] font-semibold">
                                        <?= htmlspecialchars($t['kab_kota']) ?>
                                    </span>
                                <?php endif; ?>
                                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold">
                                    <?= (int)$t['total_paket'] ?> Paket &bull; <?= (int)$t['total_perusahaan'] ?> Perusahaan
                                </span>
                            </div>
                            <?php if (!empty($t['nama_pic']) || !empty($t['no_kontak'])): ?>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    PIC: <?= htmlspecialchars($t['nama_pic'] ?: '-') ?> <?= !empty($t['no_kontak']) ? '(' . htmlspecialchars($t['no_kontak']) . ')' : '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-1.5 flex-wrap text-[11px]">
                            <?php if ($t['cnt_terkirim'] > 0): ?>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">
                                    ✓ <?= $t['cnt_terkirim'] ?> Terkirim
                                </span>
                            <?php endif; ?>
                            <?php if ($t['cnt_sedang'] > 0): ?>
                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold">
                                    <?= $t['cnt_sedang'] ?> Sedang Dikirim
                                </span>
                            <?php endif; ?>
                            <?php if ($t['cnt_belum'] > 0): ?>
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-bold">
                                    <?= $t['cnt_belum'] ?> Belum Dipesan
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="overflow-x-auto <?= ($t['pending_count'] === 0 && $idx > 2) ? 'hidden' : '' ?>">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-white text-[10px] uppercase text-slate-400 font-bold border-b border-slate-100">
                                <tr>
                                    <th class="py-2 px-4">Kode</th>
                                    <th class="py-2 px-3">Perusahaan &amp; Paket</th>
                                    <th class="py-2 px-3">Item Barang</th>
                                    <th class="py-2 px-3 text-center">Qty</th>
                                    <th class="py-2 px-3 text-center">Status</th>
                                    <th class="py-2 px-4">Ekspedisi / Resi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($t['items'] as $it):
                                    $bCls = $it['bucket'] === 'terkirim'
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                        : ($it['bucket'] === 'sedang_dikirim' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200');
                                ?>
                                    <tr class="dist-item-row hover:bg-slate-50/70" data-bucket="<?= $it['bucket'] ?>">
                                        <td class="py-2 px-4 font-mono font-bold text-slate-700 whitespace-nowrap"><?= htmlspecialchars($it['kode_paket']) ?></td>
                                        <td class="py-2 px-3">
                                            <div class="font-semibold text-blue-700 text-[11px]"><?= htmlspecialchars($it['perusahaan_nama']) ?></div>
                                            <div class="text-[11px] text-slate-500 truncate max-w-xs" title="<?= htmlspecialchars($it['nama_paket']) ?>"><?= htmlspecialchars($it['nama_paket']) ?></div>
                                        </td>
                                        <td class="py-2 px-3 font-semibold text-slate-800"><?= htmlspecialchars($it['komponen']) ?></td>
                                        <td class="py-2 px-3 text-center font-bold whitespace-nowrap"><?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan']) ?></td>
                                        <td class="py-2 px-3 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border <?= $bCls ?>">
                                                <?= htmlspecialchars($it['status_label']) ?>
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 text-[11px] text-slate-500 whitespace-nowrap">
                                            <?= $it['ekspedisi'] ? htmlspecialchars($it['ekspedisi']) : '-' ?>
                                            <?= $it['no_resi'] ? ' &bull; <span class="font-mono">' . htmlspecialchars($it['no_resi']) . '</span>' : '' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
let currentDistBucket = 'all';

function setDistBucketFilter(bucket, btn) {
    currentDistBucket = bucket;
    document.querySelectorAll('.dist-tab-btn').forEach(b => {
        b.className = 'dist-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer';
    });
    if (btn) {
        btn.className = 'dist-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-600 text-white cursor-pointer';
    }
    filterTujuanCards();
}

function filterTujuanCards() {
    const q = (document.getElementById('searchTujuanInput')?.value || '').trim().toLowerCase();
    const kota = (document.getElementById('filterKotaSelect')?.value || '').trim().toLowerCase();

    document.querySelectorAll('.tujuan-card').forEach(card => {
        const matchQ = !q || (card.dataset.search || '').includes(q);
        const matchKota = !kota || (card.dataset.kota || '') === kota;
        let matchBucket = true;
        if (currentDistBucket === 'pending') {
            matchBucket = parseInt(card.dataset.pending || 0, 10) > 0;
        } else if (currentDistBucket === 'sedang_dikirim') {
            matchBucket = parseInt(card.dataset.sedang || 0, 10) > 0;
        } else if (currentDistBucket === 'belum_dipesan') {
            matchBucket = parseInt(card.dataset.belum || 0, 10) > 0;
        } else if (currentDistBucket === 'terkirim') {
            matchBucket = parseInt(card.dataset.terkirim || 0, 10) > 0;
        }

        const showCard = matchQ && matchKota && matchBucket;
        card.style.display = showCard ? '' : 'none';

        if (showCard) {
            card.querySelectorAll('.dist-item-row').forEach(row => {
                const b = row.dataset.bucket;
                let showRow = true;
                if (currentDistBucket === 'pending') {
                    showRow = (b === 'sedang_dikirim' || b === 'belum_dipesan');
                } else if (currentDistBucket !== 'all') {
                    showRow = (b === currentDistBucket);
                }
                row.style.display = showRow ? '' : 'none';
            });
        }
    });
}
</script>
