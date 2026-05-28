@extends('master')

@section('konten')
<link rel="stylesheet" href="{{ asset('css/pengguna/reservasi_kunjungan.css') }}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/stylekunjungan.css') }}?v={{ time() }}">

<div class="reservasi-page-wrapper kunjungan-page">
    <header class="kunjungan-header-form page-header-sub">
        <h1>RESERVASI</h1>
    </header>

    <div class="action-bar-reservasi">
        <a href="{{ route('nazfram.kunjungan') }}" class="btn-back-modern">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="reservasi-form-frame">
        <div class="reservasi-card-modern">
            <h2 class="form-title-modern">{{ $kunjungan->name }}</h2>

            <form action="{{ route('nazfram.reservasi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_kunjungan" value="{{ $id_kunjungan }}">

                <!-- Kalender -->
                <div class="form-group-modern">
                    <label>Pilih Rencana Tanggal Kunjungan</label>
                    <p style="color:#555;font-size:0.85rem;background:#fff8e1;padding:10px 15px;border-left:4px solid #ffc107;border-radius:4px;margin-bottom:15px;line-height:1.6;">
                        Jadwal hanya dapat dipilih minimal H+3 dari tanggal pemesanan.
                    </p>

                    {{-- Tombol trigger khusus mobile --}}
                    <button type="button" class="cal-trigger-btn" id="btn-open-cal">
                        <i class="fas fa-calendar-alt"></i>
                        <span id="cal-trigger-label">Ketuk untuk pilih tanggal</span>
                    </button>

                    {{-- Kalender desktop --}}
                    <div class="cal-desktop">
                        <div id="calendar-reservasi"></div>
                    </div>

                    <input type="hidden" name="tanggal_reservasi" id="tanggal_reservasi_input" value="{{ old('tanggal_reservasi') }}" required>
                    @error('tanggal_reservasi')
                        <div class="invalid-feedback" style="display:block;color:#dc3545;font-size:0.85rem;margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Peserta -->
                <div class="form-group-modern">
                    <label>Jumlah Peserta (Orang)</label>
                    <input type="number" name="jumlah_peserta" id="jumlah_peserta" class="@error('jumlah_peserta') is-invalid @enderror" placeholder="Masukkan jumlah peserta" min="{{ $kunjungan->min_people }}" max="{{ $kunjungan->max_people }}" value="{{ old('jumlah_peserta') }}" required>
                    <div id="peserta-error-msg" style="display:none;color:#dc3545;font-size:0.85rem;margin-top:5px;font-weight:500;">Minimal peserta adalah {{ $kunjungan->min_people }} orang dan maksimal {{ $kunjungan->max_people }} orang.</div>
                    <small style="color:#888;">* Batas peserta: minimal {{ $kunjungan->min_people }} orang dan maksimal {{ $kunjungan->max_people }} orang.</small>
                    @error('jumlah_peserta')
                        <div class="invalid-feedback" style="display:block;color:#dc3545;font-size:0.85rem;margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Instansi -->
                <div class="form-group-modern">
                    <label>Nama Instansi / Kelompok</label>
                    <input type="text" id="instansi" name="instansi" class="@error('instansi') is-invalid @enderror" value="{{ old('instansi') }}" placeholder="Contoh: SMK Negeri 1 Subang" required>
                    @error('instansi')
                        <div class="invalid-feedback" style="display:block;color:#dc3545;font-size:0.85rem;margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="total-container" style="background:#fcfdfc;border:1px dashed #1b3a1a;border-radius:15px;padding:15px 20px;margin:20px 0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-weight:700;color:#1b3a1a;font-size:1rem;">Total Pembayaran:</span>
                        <span id="total_bayar_text" style="font-size:1.5rem;font-weight:800;color:#1b3a1a;">Rp 0</span>
                    </div>
                    <input type="hidden" name="total_harga" id="total_harga_input">
                </div>

                <!-- Metode Pembayaran -->
                <div class="pesanan-section">
                    <h3 class="section-title"><i class="fas fa-wallet"></i> Metode Pembayaran</h3>
                    <label class="radio-card active" id="card-qris">
                        <input type="radio" name="metode_pembayaran" id="radio-qris" value="qris" checked onchange="updateUI()">
                        <div>
                            <strong>QRIS</strong>
                            <div style="font-size:0.85rem;color:#666;">Bayar instan via e-Wallet/M-Banking.</div>
                        </div>
                    </label>
                    <div id="qris-info" style="display:none;">
                        <p><strong>Info:</strong> Anda akan diarahkan ke halaman pembayaran QRIS setelah menekan tombol daftar.</p>
                    </div>
                </div>

                <div class="form-actions-modern" style="margin-top:30px;">
                    <div style="margin-top:40px;">
                        <button type="submit" id="btn-submit-reservasi" class="btn-Daftar btn-daftar-form-full">
                            Daftar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Kalender Mobile --}}
<div class="cal-modal-overlay" id="cal-modal-overlay">
    <div class="cal-modal-box">
        <div class="cal-modal-header">
            <span><i class="fas fa-calendar-alt" style="margin-right:6px;color:#1b3a1a;"></i> Pilih Tanggal Kunjungan</span>
            <button type="button" class="cal-modal-close" id="btn-close-cal">&times;</button>
        </div>
        <div id="calendar-reservasi-modal"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hargaDasar    = {{ $kunjungan->price }};
    const inputPeserta  = document.getElementById('jumlah_peserta');
    const inputInstansi = document.getElementById('instansi');
    const textTotal     = document.getElementById('total_bayar_text');
    const qrisInfo      = document.getElementById('qris-info');
    const inputTanggal  = document.getElementById('tanggal_reservasi_input');
    const errorMsg      = document.getElementById('peserta-error-msg');
    const btnSubmit     = document.getElementById('btn-submit-reservasi');
    const methodRadios  = document.querySelectorAll('input[name="metode_pembayaran"]');
    const triggerBtn    = document.getElementById('btn-open-cal');
    const triggerLabel  = document.getElementById('cal-trigger-label');
    const overlay       = document.getElementById('cal-modal-overlay');
    const btnClose      = document.getElementById('btn-close-cal');

    // ── Validasi tanggal ─────────────────────────────────────
    function isDateAllowed(calInstance, start) {
        const day = start.day();
        if (day === 0 || day === 6) return false;
        const minDate = moment().add(3, 'days').startOf('day');
        if (start.isBefore(minDate)) return false;
        const dateStr = start.format('YYYY-MM-DD');
        return !calInstance.getEvents().some(event => {
            if (event.display !== 'background') return false;
            const evStart = moment(event.start).format('YYYY-MM-DD');
            const evEnd   = event.end
                ? moment(event.end).format('YYYY-MM-DD')
                : moment(event.start).add(1, 'days').format('YYYY-MM-DD');
            return moment(dateStr).isSameOrAfter(evStart) && moment(dateStr).isBefore(evEnd);
        });
    }

    // ── Setelah tanggal dipilih ──────────────────────────────
    function onDatePicked(dateStr) {
        inputTanggal.value = dateStr;
        const formatted = moment(dateStr).locale('id').format('dddd, D MMMM YYYY');
        triggerLabel.textContent = formatted;
        hitungTotal();
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // ── Konfigurasi kalender ─────────────────────────────────
    function buatKalender(elId, ref) {
        return new FullCalendar.Calendar(document.getElementById(elId), {
            initialView: 'dayGridMonth',
            locale: 'id',
            firstDay: 0,
            selectable: true,
            unselectAuto: false,
            selectLongPressDelay: 0,
            longPressDelay: 0,
            headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
            buttonText: { today: 'Hari ini' },
            events: "{{ route('nazfram.pelatihan.events') }}?type=kunjungan",
            selectAllow: info => isDateAllowed(ref.instance, moment(info.start)),
            select:    info => onDatePicked(info.startStr),
            dateClick: info => {
                if (isDateAllowed(ref.instance, moment(info.date))) onDatePicked(info.dateStr);
            }
        });
    }

    // ── Kalender Desktop ─────────────────────────────────────
    const desktopRef = { instance: null };
    if (document.getElementById('calendar-reservasi')) {
        desktopRef.instance = buatKalender('calendar-reservasi', desktopRef);
        desktopRef.instance.render();
    }

    // ── Kalender Modal (mobile, lazy init) ──────────────────
    const modalRef = { instance: null };
    function bukaModal() {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        if (!modalRef.instance) {
            modalRef.instance = buatKalender('calendar-reservasi-modal', modalRef);
            modalRef.instance.render();
        }
    }
    function tutupModal() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    triggerBtn.addEventListener('click', bukaModal);
    btnClose.addEventListener('click', tutupModal);
    overlay.addEventListener('click', e => { if (e.target === overlay) tutupModal(); });

    // Restore label jika ada old value
    if (inputTanggal.value) {
        triggerLabel.textContent = moment(inputTanggal.value).locale('id').format('dddd, D MMMM YYYY');
    }

    // ── Metode Pembayaran ────────────────────────────────────
    function updateUI() {
        const sel = document.querySelector('input[name="metode_pembayaran"]:checked');
        if (qrisInfo) qrisInfo.style.display = (sel && sel.value === 'qris') ? 'block' : 'none';
    }
    methodRadios.forEach(r => r.addEventListener('change', updateUI));

    // ── Hitung Total ─────────────────────────────────────────
    function hitungTotal() {
        const jumlah = parseInt(inputPeserta.value) || 0;
        const minP   = {{ $kunjungan->min_people }};
        const maxP   = {{ $kunjungan->max_people }};
        const invalid = inputPeserta.value !== '' && (jumlah < minP || jumlah > maxP);

        errorMsg.style.display        = invalid ? 'block' : 'none';
        inputPeserta.style.borderColor= invalid ? '#dc3545' : '#e0e6e0';
        btnSubmit.style.opacity       = invalid ? '0.5' : '1';
        btnSubmit.style.pointerEvents = invalid ? 'none' : 'auto';

        if (textTotal) {
            textTotal.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0
            }).format(jumlah * hargaDasar);
        }
    }

    inputPeserta.addEventListener('input', hitungTotal);
    inputInstansi.addEventListener('input', hitungTotal);
    updateUI();
    hitungTotal();
});
</script>
@endsection
