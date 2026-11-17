/* =========================================================
   Form Input Service: item dinamis + kalkulasi total
   ========================================================= */
(function () {
    'use strict';

    var data      = window.SparepartData || [];
    var itemLama  = window.ItemLama || [];
    var daftar    = document.getElementById('daftarItem');
    var tpl       = document.getElementById('tplItem');
    var form      = document.getElementById('formService');

    if (!daftar || !tpl || !form) {
        return;
    }

    var rupiah = function (n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    };

    var angka = function (v) {
        var n = parseFloat(String(v == null ? '' : v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n;
    };

    // ---- Bangun baris item baru ----
    function buatBaris(sparepartId, qty) {
        var node = tpl.content.firstElementChild.cloneNode(true);
        var sel  = node.querySelector('[data-sp]');

        data.forEach(function (sp) {
            var opt   = document.createElement('option');
            opt.value = sp.id;
            opt.textContent = sp.kode + ' - ' + sp.nama + ' (' + rupiah(sp.harga) + ')';
            opt.setAttribute('data-harga', sp.harga);
            sel.appendChild(opt);
        });

        sel.value = sparepartId || '';
        node.querySelector('[data-qty]').value = qty || 1;

        sel.addEventListener('change', function () { updateBaris(node); hitungTotal(); });
        node.querySelector('[data-qty]').addEventListener('input', function () { updateBaris(node); hitungTotal(); });
        node.querySelector('[data-hapus]').addEventListener('click', function () {
            node.remove();
            hitungTotal();
        });

        daftar.appendChild(node);
        updateBaris(node);

        return node;
    }

    function updateBaris(node) {
        var opt  = node.querySelector('[data-sp] option:checked');
        var qty  = angka(node.querySelector('[data-qty]').value);
        var hrg  = angka(opt ? opt.getAttribute('data-harga') : 0);

        node.querySelector('[data-harga]').textContent = '@ ' + rupiah(hrg);
        node.querySelector('[data-subtotal]').textContent = rupiah(hrg * qty);
    }

    // ---- Total keseluruhan ----
    function hitungTotal() {
        var total = 0;

        daftar.querySelectorAll('[data-row]').forEach(function (row) {
            var opt = row.querySelector('[data-sp] option:checked');
            var qty = angka(row.querySelector('[data-qty]').value);
            var hrg = angka(opt ? opt.getAttribute('data-harga') : 0);
            total  += hrg * qty;
        });

        total += angka(document.getElementById('biaya_jasa').value);

        document.getElementById('totalBiaya').textContent = rupiah(total);
        document.getElementById('totalBiayaInput').value = total;
    }

    // ---- Tombol tambah item ----
    document.getElementById('btnTambahItem').addEventListener('click', function () {
        buatBaris('', 1);
        var rows = daftar.querySelectorAll('[data-row]');
        var last = rows[rows.length - 1];
        if (last) {
            last.querySelector('[data-sp]').focus();
        }
    });

    // Biaya jasa -> hitung total
    document.getElementById('biaya_jasa').addEventListener('input', hitungTotal);

    // ---- Info kendaraan terpilih ----
    var selKendaraan = document.getElementById('kendaraan_id');
    var infoK        = document.getElementById('infoKendaraan');

    selKendaraan.addEventListener('change', function () {
        var opt = selKendaraan.options[selKendaraan.selectedIndex];
        if (!opt || !opt.value) {
            infoK.textContent = '';
            return;
        }
        var jenis = opt.getAttribute('data-jenis') === 'roda4' ? 'Roda 4' : 'Roda 2';
        infoK.innerHTML = '<i class="bi bi-info-circle"></i> ' + esc(opt.getAttribute('data-label') || '') +
            ' &middot; ' + jenis +
            (opt.getAttribute('data-no') ? ' &middot; No. Pol ' + esc(opt.getAttribute('data-no')) : '');
    });

    // ---- Info bengkel terpilih ----
    var selBengkel = document.getElementById('bengkel_id');
    var infoB      = document.getElementById('infoBengkel');

    selBengkel.addEventListener('change', function () {
        var opt = selBengkel.options[selBengkel.selectedIndex];
        if (!opt || !opt.value) {
            infoB.textContent = '';
            return;
        }
        var telp = opt.getAttribute('data-telp');
        var jam  = opt.getAttribute('data-jam');
        if (telp || jam) {
            infoB.innerHTML = '<i class="bi bi-telephone"></i> ' + esc(telp || '-') +
                ' &middot; <i class="bi bi-clock"></i> ' + esc(jam || '-');
        } else {
            infoB.textContent = '';
        }
    });

    // ---- Saran jadwal service berikutnya (+180 hari) ----
    var tanggal = document.getElementById('tanggal');
    var next    = document.getElementById('next_service_date');
    var odo     = document.getElementById('odometer');
    var nextOdo = document.getElementById('next_service_odometer');

    function saranJadwal() {
        if (tanggal.value && !next.value) {
            var d = new Date(tanggal.value);
            d.setDate(d.getDate() + 180);
            next.value = d.toISOString().slice(0, 10);
        }
        if (odo.value && !nextOdo.value) {
            nextOdo.value = Number(odo.value) + 5000;
        }
    }

    tanggal.addEventListener('change', saranJadwal);
    odo.addEventListener('change', saranJadwal);

    // ---- Simpan item yang sudah ada (saat edit / old()) ----
    (itemLama.length ? itemLama : [{ sparepart_id: '', qty: 1 }]).forEach(function (it) {
        buatBaris(it.sparepart_id, it.qty);
    });

    // Info awal jika sudah terpilih dari input user sebelumnya
    selKendaraan.dispatchEvent(new Event('change'));
    selBengkel.dispatchEvent(new Event('change'));

    form.addEventListener('submit', function (e) {
        // Validasi sederhana: tiap baris wajib memilih spare part & qty > 0
        var valid = true;
        daftar.querySelectorAll('[data-row]').forEach(function (row) {
            var sel = row.querySelector('[data-sp]');
            var qty = angka(row.querySelector('[data-qty]').value);
            if (sel.value === '' || qty < 1) {
                valid = false;
                row.style.borderColor = '#dc3545';
            } else {
                row.style.borderColor = '';
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Setiap item harus memilih spare part dan qty minimal 1.');
        }
    });

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
})();
