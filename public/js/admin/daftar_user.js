// JavaScript untuk Daftar User

document.addEventListener('DOMContentLoaded', function () {
    // Fitur pencarian user
    const searchInput = document.getElementById('searchUser');
    const userTable = document.getElementById('userTable');
    const tableBody = userTable.querySelector('tbody');
    const rows = tableBody.querySelectorAll('tr');

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let matchFound = false;

                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchTerm)) {
                        matchFound = true;
                    }
                });

                row.style.display = matchFound ? '' : 'none';
            });
        });
    }

    // Fitur Detail User Modal
    $('.btn-detail-user').click(function() {
        const name = $(this).data('name');
        const username = $(this).data('username');
        const email = $(this).data('email');
        const nohp = $(this).data('nohp') || '-';
        const alamat = $(this).data('alamat') || 'Alamat belum diatur';
        const lat = $(this).data('lat');
        const lng = $(this).data('lng');
        const status = $(this).data('status');

        $('#user-det-name').text(name);
        $('#user-det-username').text('@' + username);
        $('#user-det-email').text(email);
        $('#user-det-status').text(status).removeClass().addClass('badge ' + (status === 'Active' ? 'badge-success' : 'badge-danger'));
        $('#user-det-nohp').text(nohp);
        $('#user-det-alamat').text(alamat);

        // WhatsApp Link
        if (nohp !== '-') {
            let waNumber = nohp.replace(/[^0-9]/g, '');
            if (waNumber.startsWith('0')) waNumber = '62' + waNumber.slice(1);
            $('#user-det-wa-link').attr('href', `https://wa.me/${waNumber}`).show();
        } else {
            $('#user-det-wa-link').hide();
        }

        // Maps Integration
        if (lat && lng) {
            const mapUrl = `https://www.google.com/maps?q=${lat},${lng}&output=embed`;
            const gmapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
            $('#user-map-iframe').attr('src', mapUrl).show();
            $('#user-map-placeholder').hide();
            $('#user-det-map-btn').attr('href', gmapsUrl).show();
        } else {
            $('#user-map-iframe').hide();
            $('#user-map-placeholder').show();
            const addressUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(alamat)}`;
            $('#user-det-map-btn').attr('href', addressUrl).show();
        }

        $('#modalDetailUser').modal('show');
    });

    // Tooltip untuk informasi tambahan
    $('[data-toggle="tooltip"]').tooltip();
});
