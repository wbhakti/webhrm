@extends('sb-admin.layouts.app')

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Form Pengajuan Cuti</h4>
                </div>
                <div class="card-body">
                    @if (isset($pending) && $pending == 'Y')
                        <div class="alert alert-danger">
                            <span><b>Penting!</b> Tidak bisa ajukan cuti karena masih ada cuti yang pending.</span>
                        </div>
                    @endif
                    <form action="/postaddcuti" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="employee_name" class="form-label"><strong>Nama</strong></label>
                            <input type="text" class="form-control" value="{{ $user->NAMA }}" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label for="sisa_cuti" class="form-label"><strong>Sisa Cuti</strong></label>
                            <input type="text" class="form-control" id="sisa_cuti" name="sisa_cuti" value="{{ $user->SALDO_CUTI }}" readonly style="background-color: #e9ecef; color: #6c757d;">
                        </div>
                        <!-- Input untuk Jumlah Hari Cuti -->
                        <div class="mb-3">
                            <label for="jumlah_hari" class="form-label"><strong>Jumlah Hari Cuti</strong></label>
                            <input type="number" class="form-control" id="jumlah_hari" name="jumlah_cuti" placeholder="Masukkan jumlah hari cuti" required min="1" max="12">
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label"><strong>Tanggal Mulai Cuti</strong></label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_selesai" class="form-label"><strong>Tanggal Selesai Cuti</strong></label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                        <div class="mb-3">
                            <label for="reason"><strong>Alasan Cuti</strong></label>
                            <textarea class="form-control" id="reason" name="alasan_cuti" rows="3" required></textarea>
                        </div>
                        @if (isset($pending) && $pending == 'Y')
                            <button type="submit" class="btn btn-primary" disabled>Ajukan Cuti</button>
                        @else
                            <button type="submit" class="btn btn-primary">Ajukan Cuti</button>
                        @endif
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    alert('{{ session('success') }}');
</script>
@endif

@if(session('error'))
<script>
    alert('{{ session('error') }}');
</script>
@endif

<!-- JavaScript untuk Logika Penghitungan Tanggal -->
<script>
    document.getElementById('jumlah_hari').addEventListener('input', function() {
        // Ambil tanggal mulai dan jumlah hari cuti
        const tanggalMulai = document.getElementById('tanggal_mulai').value;
        const jumlahHari = parseInt(this.value, 10);
        
        if (tanggalMulai && jumlahHari > 0) {
            const tanggalMulaiDate = new Date(tanggalMulai);
            const tanggalSelesaiDate = new Date(tanggalMulaiDate);
            tanggalSelesaiDate.setDate(tanggalMulaiDate.getDate() + jumlahHari - 1);

            // Format tanggal selesai menjadi yyyy-mm-dd
            const year = tanggalSelesaiDate.getFullYear();
            const month = String(tanggalSelesaiDate.getMonth() + 1).padStart(2, '0');
            const day = String(tanggalSelesaiDate.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;

            // Set tanggal selesai minimum dan maksimum
            document.getElementById('tanggal_selesai').min = tanggalMulai;
            document.getElementById('tanggal_selesai').max = formattedDate;
            document.getElementById('tanggal_selesai').value = formattedDate; // Atur nilai default tanggal selesai
        }
    });

    // Reset min & max tanggal selesai jika tanggal mulai berubah
    document.getElementById('tanggal_mulai').addEventListener('change', function() {
        document.getElementById('jumlah_hari').dispatchEvent(new Event('input'));
    });
</script>

@endsection