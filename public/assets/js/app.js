/* =========================================================
   Monitoring Service Kendaraan - UI helpers
   ========================================================= */

// Konfirmasi sebelum hapus data
document.addEventListener('submit', function (e) {
    var form = e.target;
    if (form.matches('[data-confirm]') && !window.confirm(form.getAttribute('data-confirm'))) {
        e.preventDefault();
    }
});

// Auto-dismiss alert
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (el) {
            try {
                bootstrap.Alert.getOrCreateInstance(el).close();
            } catch (err) { /* abaikan */ }
        });
    }, 5000);
});

// Format angka menjadi rupiah pada input ber-class .input-rupiah (saat blur)
document.addEventListener('blur', function (e) {
    var el = e.target;
    if (el && el.matches && el.matches('.input-rupiah') && el.value !== '') {
        var num = parseFloat(String(el.value).replace(/[^\d.-]/g, ''));
        if (!isNaN(num)) {
            el.value = num.toLocaleString('id-ID');
        }
    }
}, true);

// Saat form disubmit, bersihkan format ribuan pada input .input-rupiah
document.addEventListener('submit', function (e) {
    e.target.querySelectorAll('.input-rupiah').forEach(function (el) {
        el.value = String(el.value).replace(/[^\d.-]/g, '');
    });
});
