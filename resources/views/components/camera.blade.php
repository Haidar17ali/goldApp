<hr class="my-4">

<h5 class="fw-bold mb-3">Foto Barang Emas</h5>
<div class="mb-3 text-center">
    <video id="cameraPreview" autoplay playsinline class="rounded border"
        style="width: 320px; height: 240px; display:none;"></video>
    <canvas id="photoCanvas" style="display:none;"></canvas>
    <img id="photoResult" class="rounded border mt-3" style="max-width: 320px; display:none;">
</div>

<div class="text-center mb-4">
    <button type="button" id="openCamera" class="btn btn-success btn-lg">
        <i class="fas fa-camera"></i> Buka Kamera
    </button>
    <button type="button" id="takePhoto" class="btn btn-primary btn-lg" style="display:none;">
        <i class="fas fa-circle"></i> Ambil Foto
    </button>
    <button type="button" id="retakePhoto" class="btn btn-warning btn-lg" style="display:none;">
        <i class="fas fa-redo"></i> Ambil Ulang
    </button>
</div>

<input type="hidden" name="photo_base64" id="photoBase64">

<script>
    // ======================
    // 📸 Kamera & Foto Logic
    // ======================
    const openCameraBtn = document.getElementById('openCamera');
    const takePhotoBtn = document.getElementById('takePhoto');
    const retakePhotoBtn = document.getElementById('retakePhoto');
    const cameraPreview = document.getElementById('cameraPreview');
    const photoCanvas = document.getElementById('photoCanvas');
    const photoResult = document.getElementById('photoResult');
    const photoBase64Input = document.getElementById('photoBase64');

    let stream = null;

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: {
                        ideal: "environment"
                    }
                }, // kamera belakang
                audio: false
            });
            cameraPreview.srcObject = stream;
            cameraPreview.style.display = 'block';
            takePhotoBtn.style.display = 'inline-block';
            openCameraBtn.style.display = 'none';
            retakePhotoBtn.style.display = 'none';
            photoResult.style.display = 'none';
        } catch (err) {
            alert('Tidak dapat mengakses kamera. Pastikan izin sudah diberikan.');
            console.error(err);
        }
    }

    openCameraBtn.addEventListener('click', startCamera);

    takePhotoBtn.addEventListener('click', () => {
        const context = photoCanvas.getContext('2d');
        photoCanvas.width = cameraPreview.videoWidth;
        photoCanvas.height = cameraPreview.videoHeight;
        context.drawImage(cameraPreview, 0, 0);

        // Hentikan kamera
        stream.getTracks().forEach(track => track.stop());
        cameraPreview.style.display = 'none';
        takePhotoBtn.style.display = 'none';
        openCameraBtn.style.display = 'none';
        retakePhotoBtn.style.display = 'inline-block';

        // Ambil data base64
        const photoData = photoCanvas.toDataURL('image/png');
        photoResult.src = photoData;
        photoResult.style.display = 'block';
        photoBase64Input.value = photoData;
    });

    retakePhotoBtn.addEventListener('click', () => {
        // Sembunyikan hasil, buka kamera lagi
        photoResult.style.display = 'none';
        retakePhotoBtn.style.display = 'none';
        openCameraBtn.style.display = 'none';
        startCamera();
    });
</script>
