@extends('layouts.app')

@section('title', 'DASHBOARD')

@section('content')
<!-- Konten Dashboard -->
<div id="dashboard-pdf-content" class="px-4 py-6">
    <!-- Statistik - Responsive Grid -->
    <!-- Made statistics grid fully responsive -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-6">
        <!-- Pendapatan (Net) -->
        <div class="bg-green-100 border border-green-300 rounded-xl p-4 md:p-5 flex items-center space-x-3 md:space-x-4 shadow dark:bg-green-900/30 dark:border-green-700">
            <div class="bg-green-500 text-white p-2 md:p-3 rounded-full flex-shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.333 0-4 1-4 4s2.667 4 4 4 4-1 4-4-2.667-4-4-4z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-gray-600 dark:text-gray-300 text-sm md:text-base">Pendapatan (Net) - Bulan Ini</div>
                <div class="text-lg md:text-xl font-bold truncate">Rp {{ number_format(($monthlySubtotal ?? 0), 0, ',', '.') }}</div>
                <div class="mt-0.5 text-[11px] md:text-xs inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200 dark:bg-green-800/40 dark:text-green-200 dark:border-green-700">Tanpa PPN</div>
            </div>
        </div>

        <!-- Pendapatan (Kotor/Bruto) -->
        <div class="bg-emerald-100 border border-emerald-300 rounded-xl p-4 md:p-5 flex items-center space-x-3 md:space-x-4 shadow dark:bg-emerald-900/30 dark:border-emerald-700">
            <div class="bg-emerald-500 text-white p-2 md:p-3 rounded-full flex-shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M6 6h12m-9 12h6"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-gray-600 dark:text-gray-300 text-sm md:text-base">Pendapatan (Kotor) - Bulan Ini</div>
                <div class="text-lg md:text-xl font-bold truncate">Rp {{ number_format(($monthlyRevenue ?? 0), 0, ',', '.') }}</div>
                <div class="mt-0.5 text-[11px] md:text-xs inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-800/40 dark:text-emerald-200 dark:border-emerald-700">Net + PPN 11%</div>
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="bg-red-100 border border-red-300 rounded-xl p-4 md:p-5 flex items-center space-x-3 md:space-x-4 shadow dark:bg-red-900/30 dark:border-red-700">
            <div class="bg-red-500 text-white p-2 md:p-3 rounded-full flex-shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16c1.333 0 4-1 4-4s-2.667-4-4-4-4 1-4 4 2.667 4 4 4z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-gray-600 dark:text-gray-300 text-sm md:text-base">Pengeluaran</div>
                <div class="text-lg md:text-xl font-bold truncate">Rp {{ number_format(($combinedMonthlyExpense ?? ($monthlySalaryTotal ?? 0)), 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Total Karyawan -->
        <div class="bg-yellow-100 border border-yellow-300 rounded-xl p-4 md:p-5 flex items-center space-x-3 md:space-x-4 shadow dark:bg-yellow-900/30 dark:border-yellow-700">
            <div class="bg-yellow-500 text-white p-2 md:p-3 rounded-full flex-shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m14-10a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-gray-600 dark:text-gray-300 text-sm md:text-base">Total Karyawan</div>
                <div class="text-lg md:text-xl font-bold truncate">{{ $totalKaryawan ?? 0 }} <span class="font-normal text-sm md:text-base">Orang</span></div>
            </div>
        </div>

        <!-- Total Invoice -->
        <div class="bg-blue-100 border border-blue-300 rounded-xl p-4 md:p-5 flex items-center space-x-3 md:space-x-4 shadow dark:bg-blue-900/30 dark:border-blue-700">
            <div class="bg-blue-500 text-white p-2 md:p-3 rounded-full flex-shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6m-9 7h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-gray-600 dark:text-gray-300 text-sm md:text-base">Total Invoice</div>
                <div class="text-lg md:text-xl font-bold truncate">{{ $invoiceCount ?? 0 }} <span class="font-normal text-sm md:text-base">Invoice</span></div>
            </div>
        </div>

        <!-- Tombol Print dan PDF - Responsive -->
        <!-- Made buttons responsive and properly positioned -->
        
    </div>

    <!-- Filter Bulan & Tahun -->
    <div class="mb-3 flex items-center justify-end gap-2">
        <!-- Selector Bulan -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            @php($bulanTerpilih = (int) ($bulanNow ?? now()->format('n')))
            @php($tahunTerpilih = (int) (request('year') ?? request('inc_year') ?? ($tahunNow ?? now()->format('Y'))))
            
            <!-- Hidden fields untuk menjaga parameter lain -->
            <input type="hidden" name="year" value="{{ $tahunTerpilih }}">
            <input type="hidden" name="inc_year" value="{{ $tahunTerpilih }}">
            
            <!-- Bulan dikirim ke keduanya: month & inc_month -->
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
                <select name="month" aria-label="Pilih bulan"
                        class="appearance-none pl-9 pr-8 py-1.5 text-sm rounded-full border border-gray-300 shadow-sm bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:hover:bg-gray-700"
                        onchange="this.form.inc_month.value=this.value; this.form.submit();">
                    @php($namaBulan=['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'])
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ $bulanTerpilih===$m?'selected':'' }}>{{ $namaBulan[$m] }}</option>
                    @endfor
                </select>
                <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" /></svg>
            </div>
            <input type="hidden" name="inc_month" value="{{ $bulanTerpilih }}">
        </form>
        
        <!-- Link Pilih Tahun -->
        <button type="button" onclick="openYearModal()" 
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-full hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700 dark:hover:bg-indigo-900/50">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Pilih Tahun ({{ (int) (request('year') ?? request('inc_year') ?? ($tahunNow ?? now()->format('Y'))) }})
        </button>
    </div>

    <!-- Grafik Keuangan: Pendapatan (Net) vs Pengeluaran -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 md:p-6 shadow dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-base md:text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Statistik Keuangan</h2>
        <div class="relative h-64 md:h-80 lg:h-96">
            <canvas id="barChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Ringkasan Data (Tabel) -->
    <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow dark:bg-gray-800 dark:border-gray-700">
        <!-- Header + Controls -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h3 class="text-base md:text-lg font-semibold text-gray-800 dark:text-gray-100">Ringkasan Bulanan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lihat data sesuai bulan & tahun yang dipilih</p>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold">Metrix</th>
                        <th class="text-left px-4 py-3 font-semibold">Nilai</th>
                        <th class="text-left px-4 py-3 font-semibold">Periode</th>
                        <th class="text-left px-4 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">Pendapatan (Net)</td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format(($monthlySubtotal ?? 0), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $namaBulan[$bulanNow ?? now()->format('n')] ?? '' }} {{ $tahunNow ?? now()->format('Y') }}</td>
                        <td class="px-4 py-3"><a href="{{ route('finance.income') }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900/40 dark:text-indigo-300">Detail</a></td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">Pendapatan (Kotor)</td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format(($monthlyRevenue ?? 0), 0, ',', '.') }} <span class="ml-2 text-xs text-emerald-700 dark:text-emerald-300">(Net + PPN)</span></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $namaBulan[$incMonth ?? ($bulanNow ?? now()->format('n'))] ?? '' }} {{ $incYear ?? ($tahunNow ?? now()->format('Y')) }}</td>
                        <td class="px-4 py-3"><a href="{{ route('finance.income') }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300">Detail</a></td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">Pengeluaran</td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format(($combinedMonthlyExpense ?? 0), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $namaBulan[$bulanNow ?? now()->format('n')] ?? '' }} {{ $tahunNow ?? now()->format('Y') }}</td>
                        <td class="px-4 py-3"><a href="{{ route('finance.expense') }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-rose-100 text-rose-700 hover:bg-rose-200 dark:bg-rose-900/40 dark:text-rose-300">Detail</a></td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">Total Karyawan</td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $totalKaryawan ?? 0 }} Orang</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $tahunNow ?? now()->format('Y') }}</td>
                        <td class="px-4 py-3"><a href="{{ route('employee.index') }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-900/40 dark:text-amber-300">Kelola</a></td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">Total Invoice</td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $invoiceCount ?? 0 }} Invoice</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $tahunNow ?? now()->format('Y') }}</td>
                        <td class="px-4 py-3"><a href="{{ route('suratjalan.index') }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/40 dark:text-blue-300">Lihat</a></td>
                    </tr>
                    <!-- Baris Barang Masuk/Keluar dihapus -->
                </tbody>
            </table>
        </div>
    </div>

    
    <!-- Total Pendapatan (paling bawah) -->
    
 </div>

<!-- Modal Pilih Tahun -->
<div id="yearModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Tahun</h3>
                <button type="button" onclick="closeYearModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-4 gap-2 max-h-60 overflow-y-auto">
                @php($selectedYear = (int) (request('year') ?? request('inc_year') ?? ($tahunNow ?? now()->format('Y'))))
                @foreach(($allYears ?? []) as $year)
                    <button type="button" onclick="selectYear({{ $year }})" 
                            class="year-btn px-3 py-2 text-sm font-medium rounded-md border transition-colors
                                   {{ $selectedYear === (int) $year ? 
                                      'bg-indigo-600 text-white border-indigo-600' : 
                                      'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600' }}">
                        {{ $year }}
                    </button>
                @endforeach
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="closeYearModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
 @endsection


@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Modal functions
function openYearModal() {
    document.getElementById('yearModal').classList.remove('hidden');
}

function closeYearModal() {
    document.getElementById('yearModal').classList.add('hidden');
}

function selectYear(year) {
    const currentMonth = {{ (int)($bulanNow ?? now()->format('n')) }};
    const currentIncMonth = {{ (int)($incMonth ?? now()->format('n')) }};
    
    // Redirect dengan parameter tahun yang dipilih
    const url = new URL(window.location.href);
    url.searchParams.set('year', year);
    url.searchParams.set('inc_year', year);
    url.searchParams.set('month', currentMonth);
    url.searchParams.set('inc_month', currentIncMonth);
    
    window.location.href = url.toString();
}

// Close modal when clicking outside
document.getElementById('yearModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeYearModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeYearModal();
    }
});

// Chart.js code
(function(){
  const canvasEl = document.getElementById('barChart');
  if (!canvasEl) return;
  const ctx = canvasEl.getContext('2d');

  const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const salaryMap = @json($salaryByMonth ?? []);
  const otherMap = @json($expensesByMonth ?? []);
  const revenueMap = @json($revenueNetByMonth ?? []);
  const dataPendapatan = Array.from({length:12}, (_,i)=> Number(revenueMap[i+1] ?? 0));
  const dataPengeluaran = Array.from({length:12}, (_,i)=> (Number(salaryMap[i+1] ?? 0) + Number(otherMap[i+1] ?? 0)));

  function fmtIDR(v){ try { return new Intl.NumberFormat('id-ID').format(v||0); } catch { return (v||0).toLocaleString('id-ID'); } }
  function getChartColors(){ const dark=document.documentElement.classList.contains('dark'); return { text: dark?'#e5e7eb':'#374151', grid: dark?'rgba(255,255,255,0.08)':'rgba(0,0,0,0.05)' }; }
  let colors = getChartColors();

  const gradientBlue = ctx.createLinearGradient(0,0,0,canvasEl.height);
  gradientBlue.addColorStop(0,'rgba(59,130,246,0.45)');
  gradientBlue.addColorStop(1,'rgba(59,130,246,0.05)');
  const gradientRed = ctx.createLinearGradient(0,0,0,canvasEl.height);
  gradientRed.addColorStop(0,'rgba(239,68,68,0.45)');
  gradientRed.addColorStop(1,'rgba(239,68,68,0.05)');

  const chart = new Chart(ctx, {
    type: 'line',
    data: { labels: monthLabels, datasets: [
      { label: 'Pendapatan (Net)', data: dataPendapatan, backgroundColor: gradientBlue, borderColor: 'rgba(59,130,246,1)', fill: true, borderWidth: 2, pointRadius: 2, tension: 0.35 },
      { label: 'Pengeluaran', data: dataPengeluaran, backgroundColor: gradientRed, borderColor: 'rgba(239,68,68,1)', fill: true, borderWidth: 2, pointRadius: 2, tension: 0.35 }
    ]},
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { labels: { color: colors.text } }, tooltip: { callbacks: { label: (ctx)=> `${ctx.dataset.label}: Rp ${fmtIDR(ctx.parsed.y||0)}` } } },
      scales: {
        x: { ticks: { color: colors.text }, grid: { color: colors.grid } },
        y: { beginAtZero: true, ticks: { color: colors.text, callback: (v)=> `Rp ${fmtIDR(v)}` }, grid: { color: colors.grid } }
      }
    }
  });

  function resize(){
    canvasEl.style.width = '100%';
    canvasEl.style.height = '100%';
    canvasEl.removeAttribute('width');
    canvasEl.removeAttribute('height');
    chart.resize();
    chart.update('none');
  }
  window.addEventListener('load', resize);
  setTimeout(resize, 0);
  window.addEventListener('resize', resize);
  window.addEventListener('themechange', function(){
    colors = getChartColors();
    chart.options.plugins.legend.labels.color = colors.text;
    chart.options.scales.x.ticks.color = colors.text;
    chart.options.scales.y.ticks.color = colors.text;
    chart.options.scales.x.grid.color = colors.grid;
    chart.options.scales.y.grid.color = colors.grid;
    chart.update();
  });
})();
</script>
@endpush
<!-- static dashboard: no interactive scripts below -->
