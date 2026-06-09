// ============================================================
// CUSTOM AUTOCOMPLETE ENGINE (tanpa Select2 untuk nama pembeli)
// ============================================================
const acAjaxUrl = window.NazframTransaksiOffline.routes.users;
let acTimers = {};

/**
 * Inisialisasi autocomplete pada input nama.
 * @param {string} inputId   - id elemen <input type="text">
 * @param {string} listId    - id elemen <div.autocomplete-list>
 * @param {string} hiddenId  - id elemen <input type="hidden"> (untuk user_id atau nama)
 * @param {string} type      - 'online' | 'offline'
 * @param {string|null} nohpId - id input no HP/WA untuk autofill (null jika tidak ada)
 */
function initAC(inputId, listId, hiddenId, type, nohpId) {
    const $input  = $('#' + inputId);
    const $list   = $('#' + listId);
    const $hidden = $('#' + hiddenId);

    if ($input.length === 0) return;

    // Hapus event lama agar tidak double-bind
    $input.off('.ac').on('input.ac', function() {
        const term = $(this).val().trim();
        // Reset hidden value saat user mengetik ulang
        $hidden.val('');
        clearTimeout(acTimers[inputId]);
        if (term.length < 1) {
            $list.removeClass('show').empty();
            return;
        }
        acTimers[inputId] = setTimeout(function() {
            $.ajax({
                url: acAjaxUrl,
                data: { search: term, type: type },
                dataType: 'json',
                success: function(data) {
                    $list.empty();
                    if (!data || data.length === 0) {
                        if (type === 'offline') {
                            // Tipe offline: boleh nama baru
                            $list.append(`<div class="autocomplete-item" data-id="${$('#'+inputId).val()}" data-name="${$('#'+inputId).val()}" data-nohp="">
                                <i class="fas fa-plus-circle mr-1 text-success"></i> Tambah sebagai nama baru: <strong>${$('#'+inputId).val()}</strong>
                            </div>`);
                        } else {
                            $list.append('<div class="autocomplete-no-result"><i class="fas fa-search mr-1"></i> Tidak ada akun ditemukan</div>');
                        }
                    } else {
                        data.forEach(function(u) {
                            let label, subLabel, dataId, dataName;
                            if (type === 'online') {
                                label    = u.name;
                                subLabel = '@' + u.username;
                                dataId   = u.id;
                                dataName = u.name;
                            } else {
                                label    = u.name;
                                subLabel = u.nohp && u.nohp !== '-' ? '<i class="fab fa-whatsapp mr-1"></i>' + u.nohp : 'Pelanggan lama';
                                dataId   = u.name; // untuk offline, value = nama
                                dataName = u.name;
                            }
                            $list.append(`<div class="autocomplete-item"
                                data-id="${dataId}"
                                data-name="${dataName}"
                                data-nohp="${u.nohp || ''}"
                            >
                                <strong>${label}</strong>
                                <small>${subLabel}</small>
                            </div>`);
                        });
                        if (type === 'offline') {
                            // Tambahkan opsi nama baru
                            const currentVal = $('#'+inputId).val();
                            $list.append(`<div class="autocomplete-item" data-id="${currentVal}" data-name="${currentVal}" data-nohp="">
                                <i class="fas fa-plus-circle mr-1 text-success"></i> Gunakan nama baru: <strong>${currentVal}</strong>
                            </div>`);
                        }
                    }
                    $list.addClass('show');
                }
            });
        }, 280);
    });

    // Saat item diklik
    $(document).off('click.ac_' + inputId).on('click.ac_' + inputId, '#' + listId + ' .autocomplete-item', function() {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        const nohp = $(this).data('nohp') || '';
        $input.val(name);
        $hidden.val(id);
        $list.removeClass('show').empty();
        if (nohpId && nohp && nohp !== '-') {
            $('#' + nohpId).val(nohp);
        }
    });

    // Tutup saat klik di luar
    $(document).off('click.acclose_' + inputId).on('click.acclose_' + inputId, function(e) {
        if (!$(e.target).closest('#' + inputId + ', #' + listId).length) {
            // Jika tipe offline & hidden masih kosong, pakai teks yang ada
            if (type === 'offline' && $hidden.val() === '' && $input.val().trim() !== '') {
                $hidden.val($input.val().trim());
            }
            $list.removeClass('show').empty();
        }
    });
}

function initOfflineForm(prefix) {
    const isKunOrMag = (prefix === 'kun' || prefix === 'mag');
    const nohpSuffix = isKunOrMag ? 'no_wa' : 'no_hp_prod';
    const nohpInputId = 'nohp_' + prefix;

    function applyToggle() {
        const isOnline = $('#tipe_online_' + prefix).is(':checked');
        if (isOnline) {
            $('#wrapper_online_' + prefix).show();
            $('#wrapper_offline_' + prefix).hide();
            if (prefix === 'prod') $('#wrapper_nohp_prod').hide();
            if (prefix !== 'prod') $('#wrapper_nohp_' + prefix).hide();
        } else {
            $('#wrapper_online_' + prefix).hide();
            $('#wrapper_offline_' + prefix).show();
            $('#wrapper_nohp_' + prefix).show();
        }
    }

    $("input[name='tipe_pembeli_" + prefix + "']").off('change.toggle').on('change.toggle', applyToggle);
    applyToggle();

    // Init autocomplete untuk akun online
    initAC('input_online_' + prefix, 'aclist_online_' + prefix, 'user_id_online_' + prefix + '_val', 'online', null);
    // Init autocomplete untuk nama offline
    const nohpTarget = (prefix === 'prod') ? 'nohp_prod' : 'nohp_' + prefix;
    initAC('input_offline_' + prefix, 'aclist_offline_' + prefix, 'nama_pembeli_offline_' + prefix + '_val', 'offline', nohpTarget);
}

function initProductSelect() {
    const $select = $('#prod_select');
    if ($select.length === 0) return;
    if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
    $select.select2({ width: '100%', placeholder: '-- Pilih Produk --', allowClear: true });
}
function initKunjunganSelect() {
    const $select = $('#kun_select');
    if ($select.length === 0) return;
    if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
    $select.select2({ width: '100%', placeholder: '-- Pilih Paket Kunjungan --', allowClear: true });
}
function initMagangSelect() {
    const $select = $('#mag_select');
    if ($select.length === 0) return;
    if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
    $select.select2({ width: '100%', placeholder: '-- Pilih Paket Magang --', allowClear: true });
}

$(document).ready(function() {
    // Inisialisasi form untuk setiap tab
    initOfflineForm('prod');
    initOfflineForm('kun');
    initOfflineForm('mag');

    // Inisialisasi Select2 untuk dropdown pilih produk/paket
    initProductSelect();

    // Auto-focus kolom pencarian Select2 setelah dropdown dibuka
    $(document).on('select2:open', function() {
        setTimeout(function() {
            const searchField = document.querySelector('.select2-container--open .select2-search__field');
            if (searchField) searchField.focus();
        }, 50);
    });

    // Saat tab berpindah, refresh Select2 paket
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        var targetId = $(e.target).attr('id');
        if (targetId === 'produk-tab')   { initProductSelect(); }
        if (targetId === 'kunjungan-tab') { initKunjunganSelect(); }
        if (targetId === 'magang-tab')    { initMagangSelect(); }
    });

    // --- LOGIKA TAB PRODUK (KERANJANG BELANJA MULTI-PRODUK) ---
    let cart = [];

            $('#prod_select').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const stock = parseInt(selectedOption.data('stock')) || 0;
                $('#prod_qty').val('').attr('max', stock).attr('min', 1);
            });

            $('#btn_add_product').on('click', function() {
                const selectedOption = $('#prod_select').find(':selected');
                const productId = $('#prod_select').val();

                if (!productId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih produk terlebih dahulu.',
                        confirmButtonColor: '#007bff'
                    });
                    return;
                }

                const productName = selectedOption.data('name');
                const price = parseFloat(selectedOption.data('price')) || 0;
                const stock = parseInt(selectedOption.data('stock')) || 0;
                const qty = parseInt($('#prod_qty').val()) || 0;

                if (qty <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Jumlah produk yang ditambahkan harus minimal 1.',
                        confirmButtonColor: '#007bff'
                    });
                    return;
                }

                if (stock <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok Habis',
                        text: `Stok produk "${productName}" sudah habis!`,
                        confirmButtonColor: '#007bff'
                    });
                    return;
                }

                const existingIndex = cart.findIndex(item => item.product_id === productId);

                if (existingIndex > -1) {
                    const newQty = cart[existingIndex].quantity + qty;
                    if (newQty > stock) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Batas Stok Terlampaui',
                            text: `Jumlah produk "${productName}" di keranjang (${cart[existingIndex].quantity} kg) ditambah input baru (${qty} kg) melebihi stok yang tersedia (${stock} kg).`,
                            confirmButtonColor: '#007bff'
                        });
                        return;
                    }
                    cart[existingIndex].quantity = newQty;
                } else {
                    if (qty > stock) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Batas Stok Terlampaui',
                            text: `Jumlah produk yang dimasukkan (${qty} kg) melebihi stok yang tersedia (${stock} kg).`,
                            confirmButtonColor: '#007bff'
                        });
                        return;
                    }
                    cart.push({
                        product_id: productId,
                        name: productName,
                        price: price,
                        quantity: qty,
                        stock: stock
                    });
                }

                $('#prod_select').val('').trigger('change');
                $('#prod_qty').val('');
                renderCart();
            });

            function renderCart() {
                const tbody = $('#cart_tbody');
                tbody.empty();
                $('.cart-hidden-inputs').remove();

                if (cart.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted" id="cart_empty_msg">
                                <i class="fas fa-shopping-basket fa-2x mb-2 d-block text-secondary" style="opacity: 0.5;"></i>
                                Belum ada produk yang ditambahkan.
                            </td>
                        </tr>
                    `);
                    $('#prod_total_items_txt').text('0 Barang');
                    $('#prod_subtotal_txt').text('Rp 0');
                    $('#prod_total_txt').text('Rp 0');
                    $('#btn_submit_order').attr('disabled', true);
                    return;
                }

                let totalQty = 0;
                let subtotal = 0;

                cart.forEach((item, index) => {
                    const itemSubtotal = item.price * item.quantity;
                    totalQty += item.quantity;
                    subtotal += itemSubtotal;

                    const tr = $(`
                        <tr>
                            <td class="py-2 pl-3 align-middle" style="font-size: 0.9rem;"><strong>${item.name}</strong></td>
                            <td class="py-2 text-right align-middle" style="font-size: 0.9rem;">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</td>
                            <td class="py-2 text-center align-middle" style="font-size: 0.9rem;"><span class="badge badge-info" style="font-size: 0.85rem; padding: 4px 8px; border-radius: 8px;">${item.quantity} kg</span></td>
                            <td class="py-2 text-right align-middle font-weight-bold text-success" style="font-size: 0.9rem;">Rp ${new Intl.NumberFormat('id-ID').format(itemSubtotal)}</td>
                            <td class="py-2 text-center align-middle">
                                <button type="button" class="btn btn-sm btn-danger btn-remove-item" data-index="${index}" style="border-radius: 8px; padding: 4px 8px;"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `);
                    tbody.append(tr);

                    $('#formProdukOffline').append(`
                        <input type="hidden" class="cart-hidden-inputs" name="items[${index}][product_id]" value="${item.product_id}">
                        <input type="hidden" class="cart-hidden-inputs" name="items[${index}][quantity]" value="${item.quantity}">
                    `);
                });

                $('#prod_total_items_txt').text(`${totalQty} kg (${cart.length} Produk)`);
                $('#prod_subtotal_txt').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
                $('#prod_total_txt').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
                $('#btn_submit_order').removeAttr('disabled');
            }

            $(document).on('click', '.btn-remove-item', function() {
                const index = $(this).data('index');
                cart.splice(index, 1);
                renderCart();
            });

            $('#formProdukOffline').on('submit', function(e) {
                e.preventDefault();
                if (cart.length === 0) return;

                // Pastikan hidden field nama terisi dari text input jika user ketik langsung
                const isProdOnline = $('#tipe_online_prod').is(':checked');
                if (isProdOnline) {
                    const inputVal = $('#input_online_prod').val().trim();
                    const hiddenVal = $('#user_id_online_prod_val').val();
                    if (!hiddenVal) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih akun online dari daftar saran yang muncul saat mengetik.', confirmButtonColor: '#007bff' });
                        return;
                    }
                } else {
                    const inputVal = $('#input_offline_prod').val().trim();
                    if (!inputVal) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Nama pembeli offline tidak boleh kosong.', confirmButtonColor: '#007bff' });
                        return;
                    }
                    // Jika hidden belum diisi (user ketik langsung tanpa pilih saran), isi dari text
                    if (!$('#nama_pembeli_offline_prod_val').val()) {
                        $('#nama_pembeli_offline_prod_val').val(inputVal);
                    }
                }

                Swal.fire({
                    title: 'Konfirmasi Transaksi',
                    text: "Apakah Anda yakin data pesanan produk offline ini sudah benar?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Selesai!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                        $.ajax({
                            url: window.NazframTransaksiOffline.routes.produk,
                            method: "POST",
                            data: $('#formProdukOffline').serialize(),
                            success: function(response) {
                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false })
                                .then(() => { window.location.href = window.NazframTransaksiOffline.routes.redirectTransaksi; });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.' });
                            }
                        });
                    }
                });
            });

            // --- LOGIKA TAB KUNJUNGAN ---
            function calculateKunjungan() {
                const selectedOption = $('#kun_select').find(':selected');
                const price = parseFloat(selectedOption.data('price')) || 0;
                const qty = parseInt($('#kun_qty').val()) || 1;
                const total = price * qty;

                $('#kun_price').val(new Intl.NumberFormat('id-ID').format(price));
                $('#kun_item_price_txt').text('Rp ' + new Intl.NumberFormat('id-ID').format(price));
                $('#kun_qty_txt').text(qty + ' Orang');
                $('#kun_total_txt').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
            }

            $('#kun_select').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const min = selectedOption.data('min') || 1;
                const max = selectedOption.data('max') || '';

                $('#kun_qty').attr('min', min);
                if (max) {
                    $('#kun_qty').attr('max', max);
                    $('#kun_limit_txt').text(`* Batas peserta: Min ${min} orang, Max ${max} orang.`);
                } else {
                    $('#kun_qty').removeAttr('max');
                    $('#kun_limit_txt').text(`* Batas peserta: Min ${min} orang.`);
                }
                if (parseInt($('#kun_qty').val()) < min) $('#kun_qty').val(min);
                calculateKunjungan();
            });

            $('#kun_qty').on('input change', function() { calculateKunjungan(); });

            $('#formKunjunganOffline').on('submit', function(e) {
                e.preventDefault();

                // Validasi nama pembeli kunjungan
                const isKunOnline = $('#tipe_online_kun').is(':checked');
                if (isKunOnline) {
                    if (!$('#user_id_online_kun_val').val()) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih akun online dari daftar saran yang muncul saat mengetik.', confirmButtonColor: '#007bff' });
                        return;
                    }
                } else {
                    const inputVal = $('#input_offline_kun').val().trim();
                    if (!inputVal) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Nama penanggung jawab tidak boleh kosong.', confirmButtonColor: '#007bff' });
                        return;
                    }
                    if (!$('#nama_pembeli_offline_kun_val').val()) {
                        $('#nama_pembeli_offline_kun_val').val(inputVal);
                    }
                }

                Swal.fire({
                    title: 'Konfirmasi Reservasi',
                    text: "Apakah Anda yakin mencatat reservasi kunjungan offline ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Selesai!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                        $.ajax({
                            url: window.NazframTransaksiOffline.routes.kunjungan,
                            method: "POST",
                            data: $('#formKunjunganOffline').serialize(),
                            success: function(response) {
                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false })
                                .then(() => { window.location.href = window.NazframTransaksiOffline.routes.redirectKunjungan; });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.' });
                            }
                        });
                    }
                });
            });

            // Inisialisasi FullCalendar Mini (Square Aspect Ratio)
            const calendarEl = document.getElementById('calendar-kunjungan-offline');
            const inputTanggal = document.getElementById('tanggal_reservasi_input');
            let calendar;

            if (calendarEl && typeof FullCalendar !== 'undefined') {
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    firstDay: 0,
                    selectable: true,
                    unselectAuto: false,
                    height: 'auto',
                    contentHeight: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    events: function(fetchInfo, successCallback, failureCallback) {
                        $.ajax({
                            url: window.NazframTransaksiOffline.routes.jadwalEvents,
                            data: { start: fetchInfo.startStr, end: fetchInfo.endStr },
                            success: function(response) {
                                let transformedEvents = [];
                                response.forEach(function(ev) {
                                    const kat = ev.extendedProps?.kategori;
                                    if (kat === 'libur') {
                                        transformedEvents.push({ id: ev.id, title: '', start: ev.start, end: ev.end, display: 'background', color: '#ffcccc' });
                                    } else if (kat === 'kunjungan') {
                                        transformedEvents.push({ id: ev.id, title: '', start: ev.start, end: ev.end, display: 'background', color: '#28a745' });
                                    }
                                });
                                successCallback(transformedEvents);
                            },
                            error: function(err) { failureCallback(err); }
                        });
                    },
                    selectAllow: function(selectInfo) {
                        const start = moment(selectInfo.start);
                        const day = start.day();
                        if (day === 0 || day === 6) return false;

                        const minDate = moment().add(3, 'days').startOf('day');
                        if (start.isBefore(minDate)) return false;

                        const dateStr = start.format('YYYY-MM-DD');
                        const overlapping = calendar.getEvents().some(event => {
                            if (event.display === 'background') {
                                const evStart = moment(event.start).format('YYYY-MM-DD');
                                const evEnd = event.end ? moment(event.end).format('YYYY-MM-DD') : moment(event.start).add(1, 'days').format('YYYY-MM-DD');
                                return moment(dateStr).isSameOrAfter(evStart) && moment(dateStr).isBefore(evEnd);
                            }
                            return false;
                        });
                        return !overlapping;
                    },
                    select: function(info) { inputTanggal.value = info.startStr; },
                    dateClick: function(info) {
                        const start = moment(info.date);
                        const day = start.day();
                        if (day === 0 || day === 6) return;

                        const minDate = moment().add(3, 'days').startOf('day');
                        if (start.isBefore(minDate)) return;

                        const dateStr = start.format('YYYY-MM-DD');
                        const overlapping = this.getEvents().some(event => {
                            if (event.display === 'background') {
                                const evStart = moment(event.start).format('YYYY-MM-DD');
                                const evEnd = event.end ? moment(event.end).format('YYYY-MM-DD') : moment(event.start).add(1, 'days').format('YYYY-MM-DD');
                                return moment(dateStr).isSameOrAfter(evStart) && moment(dateStr).isBefore(evEnd);
                            }
                            return false;
                        });

                        if (!overlapping) {
                            this.select(info.date);
                        }
                    },
                    longPressDelay: 50,
                    selectLongPressDelay: 50
                });
                calendar.render();

                $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                    if (e.target.id === 'kunjungan-tab') {
                        setTimeout(() => { calendar.render(); }, 100);
                    }
                });
            }

            // --- KALENDER MAGANG ---
            const magCalendarEl = document.getElementById('calendar-magang-offline');
            const inputTanggalMagang = document.getElementById('tanggal_magang_input');
            let calendarMagang;

            if (magCalendarEl && typeof FullCalendar !== 'undefined') {
                calendarMagang = new FullCalendar.Calendar(magCalendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    firstDay: 0,
                    selectable: true,
                    unselectAuto: false,
                    height: 'auto',
                    contentHeight: 'auto',
                    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                    events: function(fetchInfo, successCallback, failureCallback) {
                        $.ajax({
                            url: window.NazframTransaksiOffline.routes.jadwalEvents,
                            data: { start: fetchInfo.startStr, end: fetchInfo.endStr },
                            success: function(response) {
                                let events = [];
                                response.forEach(function(ev) {
                                    if (ev.extendedProps && ev.extendedProps.kategori === 'libur') {
                                        events.push({ id: ev.id, title: '', start: ev.start, end: ev.end, display: 'background', color: '#ffcccc' });
                                    }
                                });
                                successCallback(events);
                            },
                            error: function(err) { failureCallback(err); }
                        });
                    },
                    selectAllow: function(selectInfo) {
                        const start = moment(selectInfo.start);
                        const day = start.day();
                        if (day === 0 || day === 6) return false;

                        // Tambahan validasi H+3 untuk magang
                        const minDate = moment().add(3, 'days').startOf('day');
                        if (start.isBefore(minDate)) return false;

                        const dateStr = start.format('YYYY-MM-DD');
                        const overlapping = calendarMagang.getEvents().some(function(event) {
                            if (event.display === 'background') {
                                const evStart = moment(event.start).format('YYYY-MM-DD');
                                const evEnd = event.end ? moment(event.end).format('YYYY-MM-DD') : moment(event.start).add(1, 'days').format('YYYY-MM-DD');
                                return moment(dateStr).isSameOrAfter(evStart) && moment(dateStr).isBefore(evEnd);
                            }
                            return false;
                        });
                        return !overlapping;
                    },
                    select: function(info) { inputTanggalMagang.value = info.startStr; },
                    dateClick: function(info) {
                        const start = moment(info.date);
                        const day = start.day();
                        if (day === 0 || day === 6) return;

                        const minDate = moment().add(3, 'days').startOf('day');
                        if (start.isBefore(minDate)) return;

                        const dateStr = start.format('YYYY-MM-DD');
                        const overlapping = this.getEvents().some(event => {
                            if (event.display === 'background') {
                                const evStart = moment(event.start).format('YYYY-MM-DD');
                                const evEnd = event.end ? moment(event.end).format('YYYY-MM-DD') : moment(event.start).add(1, 'days').format('YYYY-MM-DD');
                                return moment(dateStr).isSameOrAfter(evStart) && moment(dateStr).isBefore(evEnd);
                            }
                            return false;
                        });

                        if (!overlapping) {
                            this.select(info.date);
                        }
                    },
                    longPressDelay: 50,
                    selectLongPressDelay: 50
                });
                calendarMagang.render();

                $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                    if (e.target.id === 'magang-tab') {
                        setTimeout(function() { calendarMagang.render(); }, 100);
                    }
                });
            }

            // --- LOGIKA TAB MAGANG ---
            function calculateMagang() {
                const selectedOption = $('#mag_select').find(':selected');
                const price = parseFloat(selectedOption.data('price')) || 0;
                const duration = parseInt($('#mag_duration').val()) || 1;
                const total = price * duration;

                $('#mag_price').val(new Intl.NumberFormat('id-ID').format(price));
                $('#mag_item_price_txt').text('Rp ' + new Intl.NumberFormat('id-ID').format(price));
                $('#mag_dur_txt').text(duration + ' Bulan');
                $('#mag_total_txt').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
            }

            $('#mag_select').on('change', function() { calculateMagang(); });
            $('#mag_duration').on('input change', function() { calculateMagang(); });

            $('#formMagangOffline').on('submit', function(e) {
                e.preventDefault();

                // Validasi nama pembeli magang
                const isMagOnline = $('#tipe_online_mag').is(':checked');
                if (isMagOnline) {
                    if (!$('#user_id_online_mag_val').val()) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih akun online dari daftar saran yang muncul saat mengetik.', confirmButtonColor: '#007bff' });
                        return;
                    }
                } else {
                    const inputVal = $('#input_offline_mag').val().trim();
                    if (!inputVal) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Nama peserta magang tidak boleh kosong.', confirmButtonColor: '#007bff' });
                        return;
                    }
                    if (!$('#nama_pembeli_offline_mag_val').val()) {
                        $('#nama_pembeli_offline_mag_val').val(inputVal);
                    }
                }

                Swal.fire({
                    title: 'Konfirmasi Pendaftaran',
                    text: "Apakah Anda yakin mencatat pendaftaran magang offline ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Selesai!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                        $.ajax({
                            url: window.NazframTransaksiOffline.routes.magang,
                            method: "POST",
                            data: $('#formMagangOffline').serialize(),
                            success: function(response) {
                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false })
                                .then(() => { window.location.href = window.NazframTransaksiOffline.routes.redirectMagang; });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.' });
                            }
                        });
                    }
                });
            });

});
