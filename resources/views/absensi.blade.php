@extends('sb-admin.layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<h1 class="mt-4">Absensi Karyawan</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
    <li class="breadcrumb-item active">Absensi Karyawan</li>
</ol>
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-clock"></i> Absensi
    </div>
    <div class="card-body">
        <div class="row justify-content-center mb-4">
            <div class="col-md-6 text-center">
                <h5 id="current-time" class="text-primary font-weight-bold"></h5>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <button class="btn btn-success btn-lg w-100 mb-3" onclick="openCamera('DATANG')" @if ($disableDatangButton) disabled @endif>DATANG</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-danger btn-lg w-100" onclick="openCamera('PULANG')" @if ($disablePulangButton) disabled @endif>PULANG</button>
            </div>
        </div>        
        <div class="row mt-4">
            <div class="col-md-12 table-responsive">
                <hr>
                <h5 class="text-center">Absensi Hari Ini</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Foto Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($listData) && !$listData->isEmpty())
                            @foreach ($listData as $index => $item)
                                <tr>
                                    <td>{{ $item->nama_karyawan }}</td>
                                    <td>{{ $item->status_absen }}</td>
                                    <td>{{ $item->addtime }}</td>
                                    <td>
                                        @if (!empty($item->foto_kehadiran))
                                            <img src="{{ url('/webhrm/public/uploads').'/'. $item->foto_kehadiran }}" alt="Foto Kehadiran" class="img-fluid" style="max-width: 100px;">
                                        @else
                                            <span class="text-muted">Tidak ada foto</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk kamera -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalLabel">Ambil Foto Selfie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="camera-stream" autoplay playsinline style="width: 100%;"></video>
                <button class="btn btn-primary mt-3 w-50" onclick="takeSelfie()">Ambil Foto</button>
                <canvas id="selfie-canvas" style="display: none;"></canvas>
            </div>
        </div>
    </div>
</div>

@if (session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
@endif

<script>
    // Update waktu berjalan
    function updateCurrentTime() {
        const now = new Date();
        const formattedTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('current-time').textContent = formattedTime;
    }
    setInterval(updateCurrentTime, 1000);

    let cameraStream = null;

    function openCamera(status) {
        const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
        cameraModal.show();

        // Simpan status absensi (MASUK/PULANG) ke dalam atribut dataset
        document.getElementById('cameraModal').dataset.status = status;

        const video = document.getElementById('camera-stream');
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                cameraStream = stream;
                video.srcObject = stream;
            })
            .catch(error => {
                console.error("Kamera tidak dapat diakses:", error);
            });
    }

    function takeSelfie() {
        const canvas = document.getElementById('selfie-canvas');
        const video = document.getElementById('camera-stream');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Hentikan stream kamera
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
        }

        // Konversi gambar ke data URL
        const dataURL = canvas.toDataURL('image/png');

        // Upload gambar
        uploadPhoto(dataURL);
    }

    function uploadPhoto(dataURL) {

        // Ambil status absensi dari atribut dataset
        const status = document.getElementById('cameraModal').dataset.status;

        // Ubah dataURL ke Blob
        const blob = dataURLToBlob(dataURL);

        // Membuat nama file unik dengan menambahkan timestamp
        const fileName = 'selfie' + status + '_' + Date.now() + '.png';

        // Buat form data untuk diunggah
        const formData = new FormData();
        formData.append('photo', blob, fileName);
        formData.append('status', status);

        // Kirim request menggunakan Fetch API
        fetch('/upload-foto', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Foto berhasil diunggah');
                    // Tutup modal setelah alert
                    const cameraModal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
                    cameraModal.hide();
                    location.reload();
                } else {
                    alert('Gagal mengunggah foto.');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function dataURLToBlob(dataURL) {
        const parts = dataURL.split(',');
        const mime = parts[0].match(/:(.*?);/)[1];
        const byteString = atob(parts[1]);
        const arrayBuffer = new Uint8Array(byteString.length);

        for (let i = 0; i < byteString.length; i++) {
            arrayBuffer[i] = byteString.charCodeAt(i);
        }

        return new Blob([arrayBuffer], { type: mime });
    }
</script>

@endsection
