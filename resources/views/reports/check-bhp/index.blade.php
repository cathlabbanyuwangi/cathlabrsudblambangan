<x-app-layout>
    
    {{-- CSS ditaruh langsung di sini agar 100% ter-load oleh browser --}}
    <style>
        .bg-luxury-body { 
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%) !important; 
        }

        .clay-panel-luxury {
            background-color: #f1f5f9 !important;
            border-radius: 32px !important;
            box-shadow:
                14px 14px 28px rgba(148, 163, 184, 0.3),
                -14px -14px 28px rgba(255, 255, 255, 0.9),
                inset 2px 2px 4px rgba(255, 255, 255, 0.8),
                inset -2px -2px 6px rgba(148, 163, 184, 0.15) !important;
            border: none !important;
        }

        .clay-input-luxury {
            background-color: #e2e8f0 !important;
            border-radius: 20px !important;
            box-shadow:
                inset 4px 4px 8px rgba(148, 163, 184, 0.4),
                inset -4px -4px 8px rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(203, 213, 225, 0.8) !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            width: 100% !important;
            outline: none !important;
        }
        .clay-input-luxury:focus {
            background-color: #f1f5f9 !important;
            border-color: #6366f1 !important;
            box-shadow:
                inset 2px 2px 4px rgba(148, 163, 184, 0.2),
                inset -2px -2px 4px rgba(255, 255, 255, 0.9),
                0 0 0 3px rgba(99, 102, 241, 0.25) !important;
        }

        .select-wrap { position: relative; }
        .select-wrap::after {
            content: "▼";
            font-size: 9px;
            color: #4f46e5;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .clay-btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            border-radius: 20px !important;
            box-shadow: 6px 6px 14px rgba(99, 102, 241, 0.35), -6px -6px 14px rgba(255, 255, 255, 0.8) !important;
            color: white !important;
            font-weight: 900 !important;
            border: none !important;
            cursor: pointer;
            transition: all 0.2s;
        }
        .clay-btn-primary:active { transform: scale(0.97); }

        .clay-btn-secondary {
            background-color: #e2e8f0 !important;
            border-radius: 20px !important;
            box-shadow: 5px 5px 10px rgba(148, 163, 184, 0.25), -5px -5px 10px rgba(255, 255, 255, 0.9) !important;
            color: #475569 !important;
            font-weight: 900 !important;
            border: none !important;
            cursor: pointer;
            transition: all 0.2s;
        }
        .clay-btn-secondary:active { transform: scale(0.97); }

        .clay-pill-emerald {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 12px;
            color: white;
            box-shadow: 3px 3px 8px rgba(16, 185, 129, 0.25);
        }
        .clay-pill-indigo {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border-radius: 12px;
            color: white;
            box-shadow: 3px 3px 8px rgba(99, 102, 241, 0.25);
        }
        .clay-row { transition: all 0.25s ease; }
        .clay-row:hover { background-color: rgba(255, 255, 255, 0.6); }
    </style>

    <div class="bg-luxury-body min-h-screen py-10 font-sans">
        
        {{-- HEADER --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl clay-panel-luxury flex items-center justify-center text-2xl shadow-sm">💊</div>
                        <span class="text-xs font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-4 py-1.5 rounded-full border border-indigo-100">Manajemen Logistik & Medis</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        Historis <span class="text-indigo-600">BHP High-Value</span>
                    </h2>
                    <p class="text-sm text-slate-500 font-medium">Monitoring alat medis bernilai tinggi (≥ Rp 200.000) terintegrasi statistik.</p>
                </div>
                
                <div class="clay-panel-luxury px-7 py-4 flex items-center space-x-4">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-md">📊</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Record</p>
                        <p class="text-xl font-black text-slate-900 leading-none mt-1">{{ $bhps->total() }} Item</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            {{-- PANEL FILTER --}}
            <div class="clay-panel-luxury p-8 sm:p-10">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200/60">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 font-black">🔍</div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Filter Data & Periode</h3>
                </div>

                <form action="{{ route('check-bhp.index') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider ml-1">Identitas Pasien</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base z-10">🧑‍⚕️</span>
                                <input type="text" name="patient" value="{{ request('patient') }}" placeholder="Nama / No RM..." class="clay-input-luxury pl-12 pr-4 py-3.5 text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider ml-1">Nama Barang / BHP</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base z-10">💉</span>
                                <input type="text" name="item" value="{{ request('item') }}" placeholder="Contoh: Stent..." class="clay-input-luxury pl-12 pr-4 py-3.5 text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider ml-1">Bulan</label>
                            <div class="select-wrap">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base z-10">📅</span>
                                <select name="month" class="clay-input-luxury pl-12 pr-10 py-3.5 text-sm cursor-pointer">
                                    <option value="all" {{ $month == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                    @php
                                        $namaBulan = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                                    @endphp
                                    @foreach($namaBulan as $num => $nama)
                                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider ml-1">Tahun</label>
                            <div class="select-wrap">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base z-10">🕰️</span>
                                <select name="year" class="clay-input-luxury pl-12 pr-10 py-3.5 text-sm cursor-pointer">
                                    <option value="all" {{ $year == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                                    @foreach($yearsList ?? [date('Y')] as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 flex items-center justify-end gap-4 pt-6 border-t border-slate-200/60">
                        <a href="{{ route('check-bhp.index') }}" class="clay-btn-secondary px-8 py-3.5 text-xs uppercase tracking-wider flex items-center gap-2">
                            ✕ Reset Filter
                        </a>
                        <button type="submit" class="clay-btn-primary px-10 py-3.5 text-xs uppercase tracking-wider flex items-center gap-2 shadow-lg">
                            🔍 Proses Pencarian
                        </button>
                    </div>
                </form>
            </div>

            {{-- PANEL GRAFIK --}}
            <div class="clay-panel-luxury p-8 sm:p-10 overflow-hidden">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200/60">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold">📈</div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                            Top 7 Akumulasi Biaya & Frekuensi Pemakaian
                            @if($month != 'all' || $year != 'all') 
                                <span class="text-indigo-600 font-bold">({{ $month != 'all' ? ($namaBulan[$month] ?? '') : '' }} {{ $year != 'all' ? $year : '' }})</span>
                            @endif
                        </h3>
                    </div>
                </div>
                
                <div class="relative w-full rounded-2xl p-4 bg-slate-200/50" style="height: 380px; box-shadow: inset 4px 4px 10px rgba(148, 163, 184, 0.3), inset -4px -4px 10px rgba(255, 255, 255, 0.9);">
                    <canvas id="bhpChart"></canvas>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <div class="clay-panel-luxury overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-200/50 text-[11px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-200/60">
                                <th class="px-8 py-5 whitespace-nowrap">Tanggal & Waktu</th>
                                <th class="px-8 py-5">Informasi Pasien</th>
                                <th class="px-8 py-5">Barang Habis Pakai (BHP)</th>
                                <th class="px-8 py-5 whitespace-nowrap text-center">Qty / Pemakaian</th>
                                <th class="px-8 py-5 text-right">Harga Satuan</th>
                                <th class="px-8 py-5 text-right">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/50">
                            @forelse($bhps as $bhp)
                            <tr class="clay-row">
                                <td class="px-8 py-5">
                                    <div class="text-sm font-black text-slate-800">{{ \Carbon\Carbon::parse($bhp->created_at)->format('d M Y') }}</div>
                                    <div class="text-[10px] font-black text-indigo-600 mt-0.5 uppercase tracking-wider">{{ \Carbon\Carbon::parse($bhp->created_at)->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-indigo-600 font-black text-sm shadow-sm border border-slate-200 shrink-0">
                                            {{ substr($bhp->patient->name ?? 'A', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-slate-900">{{ $bhp->patient->name ?? 'Pasien Dihapus' }}</div>
                                            <div class="text-[11px] font-bold text-slate-500 mt-0.5">No. RM: {{ $bhp->patient->medical_record_number ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center px-4 py-2 clay-pill-indigo text-xs font-black">
                                        💊 {{ $bhp->item_name }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-base font-black text-slate-800">{{ $bhp->quantity }}</span> 
                                    <span class="text-xs text-slate-500 font-bold uppercase ml-0.5">{{ $bhp->unit ?? 'pcs' }}</span>
                                </td>
                                <td class="px-8 py-5 text-sm font-bold text-slate-600 text-right whitespace-nowrap">
                                    Rp {{ number_format($bhp->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="px-8 py-5 text-right whitespace-nowrap">
                                    <span class="inline-block px-4 py-2 clay-pill-emerald text-sm font-black">
                                        Rp {{ number_format($bhp->subtotal ?? ($bhp->quantity * $bhp->unit_price), 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-8 py-24 text-center">
                                    <div class="w-20 h-20 mx-auto clay-panel-luxury flex items-center justify-center text-3xl mb-4">📭</div>
                                    <h3 class="text-base font-black text-slate-800">Data Tidak Ditemukan</h3>
                                    <p class="text-xs text-slate-500 mt-1">Tidak ada rekam medis BHP bernilai tinggi yang cocok dengan filter Anda.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($bhps->hasPages())
                <div class="px-8 py-5 border-t border-slate-200/60 bg-slate-200/30">
                    {{ $bhps->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Script ditaruh langsung agar chart langsung dirender --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('bhpChart');
            if(ctx) {
                const chartGradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                chartGradient.addColorStop(0, '#4f46e5');
                chartGradient.addColorStop(1, '#7c3aed');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels ?? []) !!},
                        datasets: [{
                            label: 'Akumulasi Biaya',
                            data: {!! json_encode($chartData ?? []) !!},
                            qtyData: {!! json_encode($chartQty ?? []) !!},
                            backgroundColor: chartGradient,
                            borderRadius: 14,
                            barPercentage: 0.55,
                            hoverBackgroundColor: '#4338ca'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13, weight: 'bold' },
                                padding: 16,
                                cornerRadius: 16,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        let biaya = context.raw.toLocaleString('id-ID');
                                        let freq = context.dataset.qtyData[context.dataIndex];
                                        return [
                                            `💰 Total Biaya: Rp ${biaya}`,
                                            `📦 Jumlah Pemakaian: ${freq} Unit`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(148, 163, 184, 0.2)', borderDash: [4, 4] },
                                ticks: {
                                    font: { weight: 'bold', size: 11 },
                                    color: '#475569',
                                    callback: function(value) {
                                        if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                        return 'Rp ' + (value / 1000) + ' Rb';
                                    }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { weight: 'bold', size: 11 }, color: '#1e293b' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>