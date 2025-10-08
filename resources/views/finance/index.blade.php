@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div x-data="financeDashboard(@js($incMonth ?? now()->month), @js($incYear ?? now()->year), @js($revenueNetByMonth ?? []), @js($revenueByCustomerByMonth ?? []), @js($monthlySubtotal ?? 0), @js($monthlyPpn ?? 0), @js($monthlyRevenue ?? 0))" x-init="init()" class="min-h-[70vh]">
    <!-- Top navbar (page-level) with filters -->
    <div class="sticky top-16 z-20 -mx-6 md:-mx-10 px-6 md:px-10 py-3 bg-white/90 backdrop-blur border-b border-gray-100 dark:bg-gray-900/80 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Laporan Pendapatan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan performa keuangan perusahaan</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <select x-model="filters.month" @change="applyFilters()" class="no-arrow appearance-none bg-white border border-gray-200 text-gray-700 text-sm rounded-lg pl-9 pr-9 py-2 shadow-sm hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:border-blue-400 dark:focus:ring-blue-400">
                        <template x-for="(m, idx) in months" :key="idx">
                            <option :value="String(idx+1)" :selected="String(idx+1)===filters.month" x-text="m"></option>
                        </template>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 left-2 flex items-center text-gray-400 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                    <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-gray-400 dark:text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
                <!-- Filter Tahun (hidden untuk filtering, tapi tetap ada) -->
                <select x-model="filters.year" @change="applyFilters()" class="hidden">
                    <template x-for="y in allYears" :key="y">
                        <option :value="String(y)" :selected="String(y)===filters.year" x-text="y"></option>
                    </template>
                </select>
                <!-- Link Pilih Tahun -->
                <button type="button" @click="openYearModal()" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700 dark:hover:bg-indigo-900/50">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span x-text="'Pilih Tahun (' + filters.year + ')'"></span>
                </button>
                <a :href="filterUrl()" @click.prevent="applyFilters()" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 shadow-sm hover:shadow transition dark:bg-blue-600/80 dark:hover:bg-blue-500">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Terapkan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        <div class="group bg-white rounded-2xl p-5 shadow-sm hover:shadow-md border border-gray-100 transition dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pendapatan Bruto</p>
                <span class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition dark:bg-gray-700 dark:text-blue-300 dark:group-hover:bg-gray-600"><i class="fa-solid fa-chart-column"></i></span>
            </div>
            <div class="mt-3 text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="formatCurrency(summary.bruto)"></div>
            <p class="mt-1 text-xs text-blue-600 dark:text-blue-300" x-text="trendText(summary.trendBruto)"></p>
        </div>
        <div class="group bg-white rounded-2xl p-5 shadow-sm hover:shadow-md border border-gray-100 transition dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">PPN (11%)</p>
                <span class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition dark:bg-gray-700 dark:text-blue-300 dark:group-hover:bg-gray-600"><i class="fa-solid fa-percent"></i></span>
            </div>
            <div class="mt-3 text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="formatCurrency(summary.ppn)"></div>
            <p class="mt-1 text-xs text-blue-600 dark:text-blue-300" x-text="trendText(summary.trendPPN)"></p>
        </div>
        <div class="group bg-white rounded-2xl p-5 shadow-sm hover:shadow-md border border-gray-100 transition dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pendapatan Net</p>
                <span class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition dark:bg-gray-700 dark:text-blue-300 dark:group-hover:bg-gray-600"><i class="fa-solid fa-sack-dollar"></i></span>
            </div>
            <div class="mt-3 text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="formatCurrency(summary.net)"></div>
            <p class="mt-1 text-xs text-blue-600 dark:text-blue-300" x-text="trendText(summary.trendNet)"></p>
        </div>
    </div>

    <!-- Monthly table -->
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
        <div class="px-5 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Pendapatan per Bulan (<span x-text="filters.year"></span>)</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Klik baris untuk melihat rincian</p>
            </div>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tahun: <span class="text-gray-800 dark:text-gray-100" x-text="formatCurrency(totalYear)"></span></div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs dark:bg-gray-900/40 dark:text-gray-300">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">Bulan</th>
                        <th class="text-right px-5 py-3 font-semibold">Bruto</th>
                        <th class="text-right px-5 py-3 font-semibold">PPN</th>
                        <th class="text-right px-5 py-3 font-semibold">Net</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="(row, idx) in monthly" :key="row.month">
                        <tr class="hover:bg-blue-50/40 transition cursor-pointer dark:hover:bg-gray-700/50" @click="toggle(idx)">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <button class="h-7 w-7 inline-grid place-items-center rounded-full bg-blue-50 text-blue-600 shadow-sm transition group/btn dark:bg-gray-700 dark:text-gray-300"
                                            :class="{ 'rotate-90': row.open }">
                                        <i class="fa-solid fa-chevron-right text-xs transition-transform duration-300" :class="{ 'rotate-90': row.open }"></i>
                                    </button>
                                    <div class="font-medium text-gray-800 dark:text-gray-100" x-text="row.label"></div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right font-medium text-gray-700 dark:text-gray-300" x-text="formatCurrency(row.bruto)"></td>
                            <td class="px-5 py-4 text-right font-medium text-gray-700 dark:text-gray-300" x-text="formatCurrency(row.ppn)"></td>
                            <td class="px-5 py-4 text-right font-semibold text-gray-900 dark:text-gray-100" x-text="formatCurrency(row.net)"></td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" class="text-xs text-blue-600 hover:underline dark:text-blue-400"
                                        @click.stop="openIncomeDetail(row.month, Number(filters.year), null)">
                                    Lihat detail
                                </button>
                            </td>
                        </tr>
                        <tr x-show="row.open" x-transition.opacity x-transition.scale.origin.top class="bg-white dark:bg-gray-900/30">
                            <td colspan="5" class="px-5 pb-5">
                                <div class="rounded-xl border border-blue-100 p-4 shadow-sm dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Ringkasan Pendapatan</p>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Bruto = Net + PPN</span>
                                    </div>
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="bg-blue-50 rounded-lg p-3 dark:bg-gray-700">
                                            <p class="text-xs text-blue-600 dark:text-blue-300">Bruto</p>
                                            <p class="text-base font-semibold text-blue-800 dark:text-blue-200" x-text="formatCurrency(row.bruto)"></p>
                                        </div>
                                        <div class="bg-blue-50 rounded-lg p-3 dark:bg-gray-700">
                                            <p class="text-xs text-blue-600 dark:text-blue-300">PPN 11%</p>
                                            <p class="text-base font-semibold text-blue-800 dark:text-blue-200" x-text="formatCurrency(row.ppn)"></p>
                                        </div>
                                        <div class="bg-blue-50 rounded-lg p-3 dark:bg-gray-700">
                                            <p class="text-xs text-blue-600 dark:text-blue-300">Net</p>
                                            <p class="text-base font-semibold text-blue-800 dark:text-blue-200" x-text="formatCurrency(row.net)"></p>
                                        </div>
                                    </div>
                                    <!-- Detail per Customer (NET) -->
                                    <div class="mt-4 overflow-x-auto">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Detail Transaksi</h4>
                                            <button @click="openIncomeDetail(row.month, Number(filters.year), null)" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600/80 dark:hover:bg-blue-500">
                                                <i class="fa-solid fa-eye"></i>
                                                Lihat detail bulan ini
                                            </button>
                                        </div>
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-slate-50 text-slate-600 uppercase text-xs dark:bg-gray-900/40 dark:text-gray-300">
                                                <tr>
                                                    <th class="text-left px-3 py-2">Customer</th>
                                                    <th class="text-right px-3 py-2">Net</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                                <template x-if="!row.details.length">
                                                    <tr>
                                                        <td colspan="2" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada data rinci.</td>
                                                    </tr>
                                                </template>
                                                <template x-for="(d, i) in row.details" :key="i">
                                                    <tr class="hover:bg-blue-50/40 transition dark:hover:bg-gray-700/50">
                                                        <td class="px-3 py-2">
                                                            <button class="text-blue-600 hover:underline dark:text-blue-400" @click="openIncomeDetail(row.month, Number(filters.year), d.name)" x-text="d.name"></button>
                                                        </td>
                                                        <td class="px-3 py-2 text-right dark:text-gray-300" x-text="formatCurrency(d.amount)"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal Pilih Tahun -->
    <div x-show="yearModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="closeYearModal()"></div>
        <div class="relative bg-white w-[92vw] max-w-lg rounded-2xl shadow-lg overflow-hidden dark:bg-gray-800">
            <div class="px-5 py-4 border-b flex items-center justify-between dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Tahun</h3>
                <button type="button" @click="closeYearModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-4 gap-2 max-h-60 overflow-y-auto">
                    <template x-for="year in allYears" :key="year">
                        <button type="button" @click="selectYear(year)" 
                                class="px-3 py-2 text-sm font-medium rounded-md border transition-colors"
                                :class="String(year) === filters.year ? 
                                        'bg-indigo-600 text-white border-indigo-600' : 
                                        'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600'"
                                x-text="year">
                        </button>
                    </template>
                </div>
            </div>
            <div class="px-5 py-3 border-t bg-gray-50 text-right dark:border-gray-700 dark:bg-gray-900/40">
                <button type="button" @click="closeYearModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function financeDashboard(incMonth, incYear, revenueNetByMonth, revenueByCustomerByMonth, monthlySubtotal, monthlyPpn, monthlyRevenue) {
    return {
        months: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        allYears: @js($allYears ?? []),
        yearModalOpen: false,
        filters: { month: String(incMonth), year: String(incYear) },
        summary: { bruto: 0, ppn: 0, net: 0, trendBruto: 0.0, trendPPN: 0.0, trendNet: 0.0 },
        monthly: [],
        totalYear: 0,
        source: { netByMonth: revenueNetByMonth || {}, byCustomerByMonth: revenueByCustomerByMonth || {} },
        // Modal state
        detailOpen: false,
        detailLoading: false,
        detailTitle: '',
        detailRows: [],
        detailTab: 'customer', // default to customer view
        detailForm: { month: String(incMonth), year: String(incYear), customer: '' },
        init(){ this.refresh(); try { if (window.Alpine) Alpine.store('fin', this); } catch(e) { console.warn(e); } },
        refresh(){
            // Susun data 12 bulan dari server (NET), hitung PPN 11% dan Bruto
            this.monthly = this.months.map((label, idx)=>{
                const monthIdx = idx + 1;
                const net  = Number(this.source.netByMonth[monthIdx] || 0);
                const ppn  = Math.round(net * 0.11);
                const bruto = net + ppn;
                const details = Array.isArray(this.source.byCustomerByMonth[monthIdx])
                    ? this.source.byCustomerByMonth[monthIdx].map(r => ({ name: r.customer, amount: Number(r.subtotal||0) }))
                    : [];
                return { month: monthIdx, label, bruto, ppn, net, open: false, details };
            });
            const openIdx = Math.max(0, Math.min(11, (Number(this.filters.month)||1) - 1));
            if (this.monthly[openIdx]) this.monthly[openIdx].open = true;
            this.totalYear = this.monthly.reduce((a,b)=> a + b.net, 0);
            const sel = this.monthly[openIdx];
            if (sel) {
                // Gunakan ringkasan bulan terpilih
                this.summary = {
                    bruto: sel.bruto,
                    ppn: sel.ppn,
                    net: sel.net,
                    trendBruto: 0.0,
                    trendPPN: 0.0,
                    trendNet: 0.0
                };
            }
        },
        toggle(i){ this.monthly[i].open = !this.monthly[i].open; },
        formatCurrency(v){
            try { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v||0); } catch(e){ return `Rp ${Number(v||0).toLocaleString('id-ID')}` }
        },
        trendText(t){ return (t>=0?'+':'') + t + '% dari bulan lalu'; }
        ,
        formatDate(d){
            try{
                const dt = new Date(d);
                if (isNaN(dt.getTime())) return d || '-';
                return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            }catch(e){ return d || '-'; }
        }
        ,
        modalTitle(){
            try{
                const m = Number((this.detailForm && this.detailForm.month) ? this.detailForm.month : this.filters.month);
                const y = (this.detailForm && this.detailForm.year) ? this.detailForm.year : this.filters.year;
                const cust = (this.detailForm && this.detailForm.customer ? String(this.detailForm.customer).trim() : '');
                const base = `Transaksi Pendapatan - ${this.months[Math.max(0, m-1)]} ${y}`;
                return cust ? base + ` · ${cust}` : base;
            }catch(e){ return 'Transaksi Pendapatan'; }
        }
        ,
        filterUrl(){
            const p = new URLSearchParams();
            p.set('inc_month', Number(this.filters.month));
            p.set('inc_year', Number(this.filters.year));
            return `{{ route('finance.income') }}?` + p.toString();
        },
        applyFilters(){ window.location = this.filterUrl(); }
        ,
        async openIncomeDetail(month, year, customer=null){
            try{
                this.detailOpen = true; this.detailLoading = true; this.detailRows = [];
                const p = new URLSearchParams();
                p.set('inc_month', month); p.set('inc_year', year);
                if (customer) p.set('customer', customer);
                const url = `{{ route('finance.income.detail') }}` + '?' + p.toString();
                const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
                const json = await res.json();
                this.detailTitle = `Transaksi Pendapatan - ${this.months[month-1]} ${year}` + (customer? ` · ${customer}` : '');
                const rows = Array.isArray(json.data)? json.data : [];
                // Tambahkan flag _open untuk expand/collapse detail item per PO
                this.detailRows = rows.map(r => ({ ...r, _open: false, items: Array.isArray(r.items)? r.items : [] }));
                this.detailTab = 'customer';
                // set form state
                this.detailForm = { month: String(month), year: String(year), customer: customer ? String(customer) : '' };
                
            }catch(e){ console.error(e); }
            finally{ this.detailLoading = false; }
        },
        toggleDetailRow(i){ if (this.detailRows[i]) this.detailRows[i]._open = !this.detailRows[i]._open; },
        closeDetail(){ this.detailOpen = false; this.detailRows = []; },
        // Ringkasan seperti kartu di dashboard
        detailSummary(){
            const net = this.detailRows.reduce((a,b)=> a + Number(b.net||0), 0);
            const ppn = this.detailRows.reduce((a,b)=> a + Number(b.ppn||0), 0);
            const bruto = this.detailRows.reduce((a,b)=> a + Number(b.bruto||0), 0);
            return { net, ppn, bruto };
        },
        // Kelompokkan per customer untuk tampilan mirip tabel dashboard
        detailByCustomer(){
            const map = new Map();
            for (const r of this.detailRows){
                const key = r.customer || '-';
                const cur = map.get(key) || { customer: key, net: 0, ppn: 0, bruto: 0 };
                cur.net += Number(r.net||0);
                cur.ppn += Number(r.ppn||0);
                cur.bruto += Number(r.bruto||0);
                map.set(key, cur);
            }
            return Array.from(map.values()).sort((a,b)=> b.net - a.net);
        },
        openYearModal(){ this.yearModalOpen = true; },
        closeYearModal(){ this.yearModalOpen = false; },
        selectYear(year){
            this.filters.year = String(year);
            this.closeYearModal();
        }
    }
}
</script>
@endpush
@endsection

@push('modals')
<div x-show="$store.fin && $store.fin.detailOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="$store.fin.closeDetail()"></div>
    <div class="relative bg-white w-[92vw] max-w-5xl rounded-2xl shadow-lg overflow-hidden dark:bg-gray-800 dark:text-gray-100">
        <div class="px-5 py-3 border-b flex items-center justify-between dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100" x-text="$store.fin.modalTitle()"></h3>
            <button class="h-8 w-8 inline-flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" @click="$store.fin.closeDetail()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-5">
            <template x-if="$store.fin.detailLoading">
                <div class="py-10 text-center text-gray-500 dark:text-gray-400">Memuat data...</div>
            </template>
            <template x-if="!$store.fin.detailLoading">
                <div>
                    <!-- Filter/Form ringkas untuk Detail (tanpa Bulan/Tahun) -->
                    <form class="mb-4 grid grid-cols-1 gap-3 items-end" @submit.prevent="$store.fin.openIncomeDetail(Number($store.fin.filters.month), Number($store.fin.filters.year), $store.fin.detailForm.customer || null)">
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1 dark:text-gray-400">Customer (opsional)</label>
                                <input type="text" class="w-full border rounded-md px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 placeholder-gray-400" placeholder="Ketik nama customer untuk filter" x-model="$store.fin.detailForm.customer" />
                            </div>
                            <button type="submit" class="h-9 px-3 rounded-md bg-blue-600 text-white text-sm dark:bg-blue-600/80 dark:hover:bg-blue-500">Terapkan</button>
                        </div>
                    </form>
                    <!-- Summary like dashboard cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                        <div class="bg-blue-50 rounded-lg p-3 dark:bg-gray-700">
                            <p class="text-xs text-blue-600 dark:text-blue-300">Bruto</p>
                            <p class="text-base font-semibold text-blue-800 dark:text-blue-200" x-text="$store.fin.formatCurrency($store.fin.detailSummary().bruto)"></p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3 dark:bg-gray-700">
                            <p class="text-xs text-blue-600 dark:text-blue-300">PPN 11%</p>
                            <p class="text-base font-semibold text-blue-800 dark:text-blue-200" x-text="'- ' + $store.fin.formatCurrency($store.fin.detailSummary().ppn)"></p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3 dark:bg-gray-700">
                            <p class="text-xs text-blue-600 dark:text-blue-300">Net</p>
                            <p class="text-base font-semibold text-blue-800 dark:text-blue-200" x-text="$store.fin.formatCurrency($store.fin.detailSummary().net)"></p>
                        </div>
                    </div>

                    <!-- Table: Per Customer (aggregated) -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-xs dark:bg-gray-900/40 dark:text-gray-300">
                                <tr>
                                    <th class="text-left px-3 py-2">Tanggal PO</th>
                                    <th class="text-left px-3 py-2">Customer</th>
                                    <th class="text-right px-3 py-2">Bruto</th>
                                    <th class="text-right px-3 py-2">PPN</th>
                                    <th class="text-right px-3 py-2">Net</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-if="!$store.fin.detailRows.length">
                                    <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td></tr>
                                </template>
                                <template x-for="(r, i) in $store.fin.detailRows" :key="r.po_id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-3 py-2 dark:text-gray-300" x-text="$store.fin.formatDate(r.tanggal_po)"></td>
                                        <td class="px-3 py-2 dark:text-gray-300" x-text="r.customer"></td>
                                        <td class="px-3 py-2 text-right dark:text-gray-300" x-text="$store.fin.formatCurrency(r.bruto)"></td>
                                        <td class="px-3 py-2 text-right dark:text-gray-300" x-text="'- ' + $store.fin.formatCurrency(r.ppn)"></td>
                                        <td class="px-3 py-2 text-right dark:text-gray-200" x-text="$store.fin.formatCurrency(r.net)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50 dark:bg-gray-900/40">
                                    <td class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-300" colspan="2">Grand Total</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-800 dark:text-gray-100" x-text="$store.fin.formatCurrency($store.fin.detailSummary().bruto)"></td>
                                    <td class="px-3 py-2 text-right font-semibold text-red-600" x-text="'- ' + $store.fin.formatCurrency($store.fin.detailSummary().ppn)"></td>
                                    <td class="px-3 py-2 text-right font-bold text-gray-900 dark:text-gray-100" x-text="$store.fin.formatCurrency($store.fin.detailSummary().net)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </div>
    </div>
</div>
@endpush
