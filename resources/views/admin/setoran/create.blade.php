@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow border border-gray-200">
    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Input Setoran Sampah Baru</h1>
            <p class="text-xs text-gray-500">Pilih nasabah dan masukkan berat penimbangan (F-16, F-17)</p>
        </div>
        <a href="{{ route('admin.setoran.index') }}" class="text-xs text-gray-500 hover:underline">&larr; Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.setoran.store') }}" id="form-setoran" class="space-y-5 text-xs">
        @csrf

        <!-- Pilih Nasabah Aktif -->
        <div>
            <label class="block font-semibold text-gray-700 uppercase mb-1">Pilih Nasabah Aktif</label>
            <select name="nasabah_id" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:border-emerald-600">
                <option value="">-- Pilih Nasabah Penyetor --</option>
                @foreach($nasabahList as $nasabah)
                    <option value="{{ $nasabah->id }}" {{ old('nasabah_id') == $nasabah->id ? 'selected' : '' }}>
                        {{ $nasabah->nomor_nasabah }} - {{ $nasabah->user->name }} (Saldo saat ini: Rp {{ number_format($nasabah->saldo, 0, ',', '.') }})
                    </option>
                @endforeach
            </select>
            @error('nasabah_id') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Tabel Rincian Sampah (Dinamis) -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label class="font-semibold text-gray-700 uppercase">Item Setoran Sampah (Minimal 1 Jenis)</label>
                <button type="button" id="btn-tambah-baris" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1 rounded border border-gray-300 font-medium">
                    + Tambah Baris
                </button>
            </div>

            <table class="w-full text-left text-xs border border-gray-200 rounded" id="tabel-items">
                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="p-2">Jenis Sampah</th>
                        <th class="p-2 w-28">Harga/kg</th>
                        <th class="p-2 w-28">Berat (kg)</th>
                        <th class="p-2 w-32">Subtotal</th>
                        <th class="p-2 w-10 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="container-items" class="divide-y divide-gray-100">
                    <tr class="item-row">
                        <td class="p-2">
                            <select name="items[0][jenis_sampah_id]" class="w-full p-1.5 border border-gray-300 rounded select-jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisList as $j)
                                    <option value="{{ $j->id }}" data-harga="{{ $j->harga_saat_ini }}">
                                        {{ $j->nama }} (Rp {{ number_format($j->harga_saat_ini, 0, ',', '.') }}/kg)
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-2 font-mono text-gray-600 text-harga">Rp 0</td>
                        <td class="p-2">
                            <input type="number" step="0.01" min="0.01" name="items[0][berat_kg]" placeholder="0.00" required
                                   class="w-full p-1.5 border border-gray-300 rounded input-berat text-right font-mono">
                        </td>
                        <td class="p-2 font-bold font-mono text-emerald-700 text-subtotal">Rp 0</td>
                        <td class="p-2 text-center">
                            <button type="button" class="btn-hapus-baris text-red-500 hover:text-red-700 font-bold text-sm">&times;</button>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-50 font-bold border-t border-gray-200 text-sm">
                    <tr>
                        <td colspan="2" class="p-2.5 text-right uppercase text-xs text-gray-600">Total Berat:</td>
                        <td class="p-2.5 font-mono text-right" id="total-berat">0.00 kg</td>
                        <td colspan="2" class="p-2.5 font-mono text-emerald-800 text-base" id="total-nilai">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
            @error('items') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-3 border-t border-gray-100">
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 rounded text-sm shadow transition">
                Simpan Setoran & Tambah Saldo
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('container-items');
    const btnTambah = document.getElementById('btn-tambah-baris');
    let rowIndex = 1;

    const templateOptions = `@foreach($jenisList as $j)<option value="{{ $j->id }}" data-harga="{{ $j->harga_saat_ini }}">{{ $j->nama }} (Rp {{ number_format($j->harga_saat_ini, 0, ',', '.') }}/kg)</option>@endforeach`;

    btnTambah.addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="p-2">
                <select name="items[${rowIndex}][jenis_sampah_id]" class="w-full p-1.5 border border-gray-300 rounded select-jenis" required>
                    <option value="">-- Pilih Jenis --</option>
                    ${templateOptions}
                </select>
            </td>
            <td class="p-2 font-mono text-gray-600 text-harga">Rp 0</td>
            <td class="p-2">
                <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][berat_kg]" placeholder="0.00" required
                       class="w-full p-1.5 border border-gray-300 rounded input-berat text-right font-mono">
            </td>
            <td class="p-2 font-bold font-mono text-emerald-700 text-subtotal">Rp 0</td>
            <td class="p-2 text-center">
                <button type="button" class="btn-hapus-baris text-red-500 hover:text-red-700 font-bold text-sm">&times;</button>
            </td>
        `;
        container.appendChild(tr);
        rowIndex++;
        attachListeners(tr);
    });

    function attachListeners(row) {
        const selectJenis = row.querySelector('.select-jenis');
        const inputBerat = row.querySelector('.input-berat');
        const btnHapus = row.querySelector('.btn-hapus-baris');

        selectJenis.addEventListener('change', calculateAll);
        inputBerat.addEventListener('input', calculateAll);

        btnHapus.addEventListener('click', function () {
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateAll();
            } else {
                alert('Setoran minimal memuat 1 baris jenis sampah.');
            }
        });
    }

    function calculateAll() {
        let grandTotalBerat = 0;
        let grandTotalNilai = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.select-jenis');
            const input = row.querySelector('.input-berat');
            const tdHarga = row.querySelector('.text-harga');
            const tdSubtotal = row.querySelector('.text-subtotal');

            const selectedOption = select.options[select.selectedIndex];
            const harga = selectedOption ? parseFloat(selectedOption.dataset.harga || 0) : 0;
            const berat = parseFloat(input.value) || 0;
            const subtotal = harga * berat;

            tdHarga.textContent = 'Rp ' + harga.toLocaleString('id-ID');
            tdSubtotal.textContent = 'Rp ' + Math.round(subtotal).toLocaleString('id-ID');

            grandTotalBerat += berat;
            grandTotalNilai += subtotal;
        });

        document.getElementById('total-berat').textContent = grandTotalBerat.toFixed(2) + ' kg';
        document.getElementById('total-nilai').textContent = 'Rp ' + Math.round(grandTotalNilai).toLocaleString('id-ID');
    }

    // Attach listener to first row
    document.querySelectorAll('.item-row').forEach(attachListeners);
});
</script>
@endsection
