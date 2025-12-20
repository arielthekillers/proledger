                    <?php foreach ($modal_detail as $md): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800"><?= htmlspecialchars($md['nama_paket'] ?? 'Paket #' . $md['paket_id']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">Joined recently</div>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600 font-medium">
                                <?= formatRupiah($md['modal_pasal']) ?>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600 font-medium">
                                <?= formatRupiah($md['modal_belanja']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">
                                <?= formatRupiah($md['total_modal']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-blue-600">
                                <?php 
                                    // Formula: (Pagu - Total Modal Packet) / Jumlah Pemodal
                                    // Note: We need Total Modal of the PACKET, not just this user.
                                    // However, in current context we might not have the full packet total modal readily available in this row 
                                    // unless we sum it up or fetch it.
                                    // BUT, for simple estimation as requested: "Pagu - Modal / Pemodal".
                                    // If "Modal" means "Total Modal Packet", we need that value.
                                    // Let's assume (Pagu - (Modal Pasal + Modal Belanja * Pemodal)) / Pemodal?
                                    // Wait, the correct logic is Project Profit = Pagu - Cost.
                                    // Cost usually includes the RAB (Modal Belanja) + Pasal (Fees)? 
                                    // Let's deduce from standard logic: 
                                    // Total Modal Packet = (Modal Pasal Per Orang + Modal Belanja Per Orang) * Jumlah Pemodal.
                                    // Since it's uniform per person usually:
                                    // Total Estimation Profit = Pagu - (Total Modal per Orang * Jumlah Pemodal).
                                    // Estimation Profit Per Person = Total Estimation Profit / Jumlah Pemodal.
                                    
                                    // Simpler: (Pagu / Jumlah Pemodal) - Total Modal Per Person.
                                    
                                    $pagu = $md['pagu'] ?? 0;
                                    $jml_pemodal = $md['jumlah_pemodal'] > 0 ? $md['jumlah_pemodal'] : 1;
                                    $total_modal_person = $md['total_modal'];
                                    
                                    // Calculate Total Modal for the whole packet (This is an approximation if all pay same)
                                    // Or simply use the fields we calculate.
                                    // Let's use: (Pagu - (Total Modal Person * Jml Pemodal)) / Jml Pemodal
                                    // = (Pagu / Jml Pemodal) - Total Modal Person
                                    
                                    $est_profit = ($pagu / $jml_pemodal) - $total_modal_person;
                                    echo formatRupiah($est_profit);
                                ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?= APP_URL ?>/paket/detail/<?= $md['paket_id'] ?>?from=pemodal&pemodal_id=<?= $user['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-colors">
                                    <ion-icon name="arrow-forward"></ion-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
