@extends('layouts.user')

@section('content')

<style>
/* ================== DASHBOARD WRAPPER ================== */
.dashboard-wrapper {
    max-width: 1320px;
    margin: 0 auto;
    padding: 24px 28px 40px;
    background: transparent;
    box-sizing: border-box;
}

/* ================== STAT CARDS ================== */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    width: 100%;
    margin-bottom: 24px;
}

.stat-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-sizing: border-box;
}

.stat-card h6 {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 6px;
}

.stat-card h2 {
    font-size: 1.6rem;
    margin: 0 0 6px 0;
}

/* ================== MIDDLE SECTION ================== */
.middle-row {
    display: grid;
    grid-template-columns: 420px 1fr;
    gap: 28px;
    align-items: stretch;
    width: 100%;
}

/* NETRALISIR BOOTSTRAP COLUMN DI DALAM GRID */
.middle-row > .col-lg-5,
.middle-row > .col-lg-7 {
    width: 100%;
    max-width: 100%;
    padding: 0;
}

/* ================== LEFT : KONTEN TEMATIK ================== */
.konten-tematik-box {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    height: 720px;
    box-sizing: border-box;

    /* PENTING */
    overflow: hidden;
}


/* ================== SCHEDULE SECTION ================== */
.konten-tematik-box > .mt-4 {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0; /* WAJIB untuk scroll */
}

.schedules-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding-right: 6px;
}

/* Scrollbar halus */
.schedules-container::-webkit-scrollbar {
    width: 6px;
}

.schedules-container::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

.schedules-container::-webkit-scrollbar-track {
    background: transparent;
}

/* ================== CALENDAR ================== */

/* Header weekday lebih kecil */
#calendarWeekdays {
    font-size: 0.7rem;
    margin-bottom: 6px;
}

#calendarDays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}

#calendarDays .day {
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    cursor: pointer;
}

#calendarDays .muted {
    color: #bfc9cf;
}

#calendarDays .day:hover {
    background: #f7fbff;
}

#calendarContainer {
    flex-shrink: 0;
}

#calendarContainer .d-flex.gap-2 {
    background: #f8fafc;
    padding: 8px;
    border-radius: 10px;
    margin-bottom: 12px;
    flex-shrink: 0;
    gap: 8px;
    flex-wrap: nowrap;
}

/* Perkecil jarak header bulan */
#calendarContainer h6 {
    font-size: 0.9rem;
    margin: 0;
}

#calendarContainer input.form-control {
    height: 50px;
    font-size: 0.85rem;
}

#applyRangeBtn {
    height: 40px !important;
    font-size: 0.85rem;
    padding: 0 14px !important;
    min-width: 110px;
    white-space: nowrap;
}



/* RANGE */
.range-start,
.range-end {
    background: #cfe9ff !important;
    border-radius: 999px !important;
}

.in-range {
    background: #eef6ff !important;
}

/* ================== SCHEDULE ================== */
.schedules-container {
    flex: 1;
    overflow-y: auto;
    padding-right: 6px;
}

/* ================== RIGHT : CHARTS ================== */
.portal-box-soft,
.kepuasan-box-soft {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px;
    height: 340px;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
}

.portal-box-soft canvas,
.kepuasan-box-soft canvas {
    flex: 1;
    width: 100% !important;
}

/* JARAK ANTAR CHART */
.col-lg-7 .kepuasan-box-soft {
    margin-top: 24px;
}

/* ================== TABLE ================== */
.custom-table {
    width: 100%;
    border-collapse: collapse;
}

.custom-table thead {
    background: #f8f9fa;
}

.custom-table th {
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #666;
    border-bottom: 2px solid #eee;
    font-size: 0.9rem;
}

.custom-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #eee;
    font-size: 0.9rem;
}

.custom-table tbody tr:hover {
    background: #f9f9f9;
}

.custom-table tbody tr:last-child td {
    border-bottom: none;
}

/* ================== PAGE BACKGROUND ================== */
body {
    background: #f6f2ee !important;
}

.container,
.container-fluid {
    padding-left: 0 !important;
    padding-right: 0 !important;
    background: transparent !important;
}

</style>
<div class="dashboard-wrapper">
    {{-- ================== STAT CARDS SECTION ================== --}}
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <h6>JUMLAH INFOGRAFIS</h6>
            <h2>{{ $jumlahInfografis1 }}</h2>
            <span>Sosial dan kepandulukan</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH VIEWER DATA</h6>
            <h2>{{ $penggunaanDataView }}</h2>
            <span>Jumlah Penggunaan Menurut Perangkat Daerah</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH DOWNLOAD DATA</h6>
            <h2>{{ $penggunaanDataDownload }}</h2>
            <span>Jumlah Penggunaan Menurut Perangkat Daerah</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH INFOGRAFIS</h6>
            <h2>{{ $jumlahInfografis2 }}</h2>
            <span>Pertanian dan pertambangan</span>
        </div>
    </div>

    {{-- ================== MIDDLE SECTION: CALENDAR + CHARTS ================== --}}
    <div class="mb-4 middle-row">
        
        {{-- LEFT: KONTEN TEMATIK CALENDAR & SCHEDULES --}}
        <div class="col-lg-5">
            <div class="p-4 konten-tematik-box">
                <h5 class="mb-3">Konten Tematik</h5>
                
                {{-- CALENDAR --}}
                <div id="calendarContainer" class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button class="btn btn-sm btn-light" onclick="previousMonth()">‹</button>
                        <h6 id="calendarMonth">{{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}</h6>
                        <button class="btn btn-sm btn-light" onclick="nextMonth()">›</button>
                    </div>
                    <div class="d-flex gap-2 mb-2 align-items-center">
                        <input type="text" id="rangeStart" class="form-control" placeholder="Start" />
                        <input type="text" id="rangeEnd" class="form-control" placeholder="End" />
                        <button id="applyRangeBtn" class="btn btn-primary" style="height:60px;padding:0 18px;border-radius:8px;">Apply Range</button>
                        
                    </div>

                    <div class="calendar">
                        <div id="calendarWeekdays" class="text-center small fw-bold mb-2" style="display:grid;grid-template-columns:repeat(7,1fr);gap:8px;">
                            <div>SUN</div>
                            <div>MON</div>
                            <div>TUE</div>
                            <div>WED</div>
                            <div>THU</div>
                            <div>FRI</div>
                            <div>SAT</div>
                        </div>
                        <div id="calendarDays" class="row g-1">
                            {{-- Calendar days will be generated by JavaScript --}}
                        </div>
                    </div>
                </div>

                {{-- SCHEDULES --}}
                <div class="mt-4" style="display:flex; flex-direction:column; flex:1;">
                    <h6 class="mb-3">Schedules</h6>
                    <div class="schedules-container" style="overflow-y:auto; padding-right:6px; flex:1;">
                        @forelse($kontenTematikAgendas as $agenda)
                            <div class="schedule-item mb-3 p-3" style="background: #f5f5f5; border-radius: 6px;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted d-block">{{ $agenda->tanggal_target->format('d M Y') }}</small>
                                        <strong class="d-block mt-1">{{ $agenda->agenda }}</strong>
                                        <small class="text-muted d-block mt-1">{{ $agenda->data_dukung }}</small>
                                    </div>
                                    <span class="badge bg-success">{{ $agenda->progress }}%</span>
                                </div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $agenda->progress }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">Tidak ada agenda untuk bulan ini</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: CHARTS --}}
        <div class="col-lg-7">
            {{-- PORTAL SATA CHART --}}
            <div class="p-4 portal-box-soft">
                <h5 class="mb-3">Portal SATA</h5>
                <small class="text-muted d-block mb-3">{{ now()->format('d M Y') }}</small>
                <div style="flex:1; display:flex; align-items:stretch;">
                    <canvas id="portalSataChart" style="width:100%;height:100%;object-fit:contain;"></canvas>
                </div>
            </div>

            {{-- KEPUASAN PENGUNJUNG CHART --}}
            <div class="p-4 kepuasan-box-soft">
                <h5 class="mb-3">Kepuasan Pengunjung</h5>
                <small class="text-muted d-block mb-3">{{ now()->format('d M Y') }}</small>
                <div style="flex:1; display:flex; align-items:stretch;">
                    <canvas id="kepuasanChart" style="width:100%;height:100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ================== TABLE SECTION: DAFTAR DATA & REKOMENDASI ================== --}}
    <div class="row mt-4">
        {{-- DAFTAR DATA TABLE --}}
        <div class="col-12 mb-4">
            <div class="p-4" style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h5 class="mb-3">Jumlah Daftar Data Menurut Perangkat Daerah</h5>
                <small class="text-muted d-block mb-3">{{ $tanggalDaftarData ? $tanggalDaftarData->format('d M Y') : 'N/A' }}</small>
                
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Perangkat Daerah</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarDataTerbaru as $data)
                            <tr>
                                <td>{{ $data->perangkat_daerah }}</td>
                                <td>{{ $data->jumlah }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- REKOMENDASI STATISTIK TABLE --}}
        <div class="col-12">
            <div class="p-4" style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h5 class="mb-3">Jumlah Rekomendasi Statistik Tahun {{ now()->year }}</h5>
                <small class="text-muted d-block mb-3">{{ $tanggalRekomendasi ? $tanggalRekomendasi->format('d M Y') : 'N/A' }}</small>
                
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Diajukan Oleh</th>
                            <th>Layak</th>
                            <th>Pemeriksaan</th>
                            <th>Pengajuan</th>
                            <th>Perbaikan</th>
                            <th>Total Keseluruhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekomendasiTerbaru as $rekomendasi)
                            <tr>
                                <td>{{ $rekomendasi->instansi_tujuan }}</td>
                                <td>{{ is_numeric($rekomendasi->Layak) ? $rekomendasi->Layak : intval(preg_replace('/[^0-9]/', '', $rekomendasi->Layak)) }}</td>
                                <td>{{ is_numeric($rekomendasi->Pemeriksaan) ? $rekomendasi->Pemeriksaan : intval(preg_replace('/[^0-9]/', '', $rekomendasi->Pemeriksaan)) }}</td>
                                <td>{{ is_numeric($rekomendasi->Pengajuan) ? $rekomendasi->Pengajuan : intval(preg_replace('/[^0-9]/', '', $rekomendasi->Pengajuan)) }}</td>
                                <td>{{ is_numeric($rekomendasi->Perbaikan) ? $rekomendasi->Perbaikan : intval(preg_replace('/[^0-9]/', '', $rekomendasi->Perbaikan)) }}</td>
                                <td>{{ is_numeric($rekomendasi->Total) ? $rekomendasi->Total : intval(preg_replace('/[^0-9]/', '', $rekomendasi->Total)) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Calendar Logic
let currentMonth = {{ $currentMonth }};
let currentYear = {{ $currentYear }};
let rangeStart = @json($rangeStart ?? null);
let rangeEnd = @json($rangeEnd ?? null);

function generateCalendar(month, year) {
    const firstDay = new Date(year, month - 1, 1).getDay();
    const daysInMonth = new Date(year, month, 0).getDate();
    const daysInPrevMonth = new Date(year, month - 1, 0).getDate();

    const calendarDays = document.getElementById('calendarDays');
    const monthName = new Date(year, month - 1).toLocaleString('default', { month: 'long', year: 'numeric' });

    document.getElementById('calendarMonth').textContent = monthName;
    calendarDays.innerHTML = '';

    // Previous month days (no selection) - include real dates so ranges crossing months work
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        const prevDate = new Date(year, month - 2, day); // month-2 because JS months 0-indexed
        const pad = (n) => String(n).padStart(2, '0');
        const dateStrPrev = `${prevDate.getFullYear()}-${pad(prevDate.getMonth()+1)}-${pad(prevDate.getDate())}`;
        const dayDiv = document.createElement('div');
        dayDiv.className = 'col text-center p-2 muted day';
        dayDiv.textContent = day;
        dayDiv.setAttribute('data-date', dateStrPrev);
        calendarDays.appendChild(dayDiv);
    }

    // Current month days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'col text-center p-2 day';
        dayDiv.style.cursor = 'pointer';
        dayDiv.textContent = day;

        const pad = (n) => String(n).padStart(2, '0');
        const dateStr = `${year}-${pad(month)}-${pad(day)}`;
        dayDiv.setAttribute('data-date', dateStr);

        const isToday = day === {{ $today->day }} && month === {{ $today->month }} && year === {{ $today->year }};
        if (isToday) {
            dayDiv.style.background = '#e3f2fd';
            dayDiv.style.fontWeight = 'bold';
            dayDiv.style.borderRadius = '4px';
        }

        dayDiv.addEventListener('click', function() {
            // Toggle/select logic:
            // - If no start -> set start
            // - If start exists and no end -> clicking same date clears selection; else set end (swap if needed)
            // - If both set -> clicking start/end clears that part; clicking elsewhere starts new selection
            if (!rangeStart) {
                rangeStart = dateStr;
                rangeEnd = null;
            } else if (rangeStart && !rangeEnd) {
                if (dateStr === rangeStart) {
                    // double-click same date -> clear
                    rangeStart = null;
                    rangeEnd = null;
                } else {
                    // set end, ensure start <= end
                    if (new Date(dateStr) < new Date(rangeStart)) {
                        rangeEnd = rangeStart;
                        rangeStart = dateStr;
                    } else {
                        rangeEnd = dateStr;
                    }
                }
            } else {
                // both set
                if (dateStr === rangeStart) {
                    rangeStart = null;
                    rangeEnd = null;
                } else if (dateStr === rangeEnd) {
                    rangeEnd = null;
                } else {
                    // start a new selection
                    rangeStart = dateStr;
                    rangeEnd = null;
                }
            }
            setRangeInputs();
            highlightRange();
        });

        calendarDays.appendChild(dayDiv);
    }

    // Next month days
    // Next month filler days
    for (let day = 1; day <= (42 - firstDay - daysInMonth); day++) {
        const nextDate = new Date(year, month, day);
        const pad = (n) => String(n).padStart(2, '0');
        const dateStrNext = `${nextDate.getFullYear()}-${pad(nextDate.getMonth()+1)}-${pad(nextDate.getDate())}`;
        const dayDiv = document.createElement('div');
        dayDiv.className = 'col text-center p-2 muted day';
        dayDiv.textContent = day;
        dayDiv.setAttribute('data-date', dateStrNext);
        calendarDays.appendChild(dayDiv);
    }
}

function previousMonth() {
    currentMonth--;
    if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
}

// Initialize calendar
generateCalendar(currentMonth, currentYear);
setRangeInputs();
highlightRange();
// Portal SATA Chart
const ctxPortalSata = document.getElementById('portalSataChart').getContext('2d');
new Chart(ctxPortalSata, {
    type: 'line',
    data: {
        labels: @json($portalSataLabels ?? []),
        datasets: [
            {
                label: 'Capaian',
                data: @json($portalSataCapaian ?? []),
                borderColor: '#5DADE2',
                backgroundColor: 'rgba(93, 173, 226, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Calendar range selection helpers
function setRangeInputs() {
    document.getElementById('rangeStart').value = rangeStart ? rangeStart : '';
    document.getElementById('rangeEnd').value = rangeEnd ? rangeEnd : '';
}

function clearRangeHighlight() {
    document.querySelectorAll('#calendarDays .col').forEach(el => el.classList.remove('range-start','range-end','in-range'));
}

function highlightRange() {
    clearRangeHighlight();
    if (!rangeStart) return;
    const start = new Date(rangeStart);
    const end = rangeEnd ? new Date(rangeEnd) : start;
    document.querySelectorAll('#calendarDays .col[data-date]').forEach(el => {
        const d = new Date(el.getAttribute('data-date'));
        if (d.getTime() === start.getTime()) el.classList.add('range-start');
        if (d.getTime() === end.getTime()) el.classList.add('range-end');
        if (d.getTime() > start.getTime() && d.getTime() < end.getTime()) el.classList.add('in-range');
    });
}

// Apply Range button behaviour: redirect to dashboard with query params
document.getElementById('applyRangeBtn').addEventListener('click', function(){
    if (!rangeStart) return alert('Pilih rentang tanggal terlebih dahulu');
    const end = rangeEnd ? rangeEnd : rangeStart;
    const base = window.location.pathname;
    const qs = '?tanggal_awal=' + encodeURIComponent(rangeStart) + '&tanggal_akhir=' + encodeURIComponent(end);
    window.location.href = base + qs;
});

// Kepuasan Pengunjung Line Chart
const ctxKepuasan = document.getElementById('kepuasanChart').getContext('2d');
new Chart(ctxKepuasan, {
    type: 'line',
    data: {
        labels: @json($kepuasanLabelsSeries ?? []),
        datasets: [
            {
                label: 'Sangat Puas',
                data: @json($kepuasanSangatPuas ?? []),
                borderColor: '#7B68EE',
                backgroundColor: 'rgba(123,104,238,0.08)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            },
            {
                label: 'Puas',
                data: @json($kepuasanPuas ?? []),
                borderColor: '#5DADE2',
                backgroundColor: 'rgba(93,173,226,0.08)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            },
            {
                label: 'Tidak Puas',
                data: @json($kepuasanTidakPuas ?? []),
                borderColor: '#E74C3C',
                backgroundColor: 'rgba(231,76,60,0.08)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

@endsection
