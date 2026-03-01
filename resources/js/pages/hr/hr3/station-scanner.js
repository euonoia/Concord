
import { Html5Qrcode } from "html5-qrcode";

export default function initAttendanceScanner() {
    const startBtn = document.getElementById('start-btn');
    const scanLine = document.getElementById('scan-line');
    const feedback = document.getElementById('feedback');

    if (!startBtn) return;

    let html5QrCode = null;
    let activeCameraId = null;

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';

    startBtn.addEventListener('click', async () => {
        startBtn.disabled = true;
        startBtn.style.display = 'none';
        scanLine?.classList.remove('hidden');

        html5QrCode = new Html5Qrcode("reader");

        try {
            const cameras = await Html5Qrcode.getCameras();
            if (!cameras || cameras.length === 0) throw new Error("No camera devices found.");

            const backCamera = cameras.find(cam =>
                cam.label.toLowerCase().includes('back') ||
                cam.label.toLowerCase().includes('rear')
            );
            activeCameraId = backCamera ? backCamera.id : cameras[0].id;

            await html5QrCode.start(
                activeCameraId,
                { fps: 15, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                async (decodedText) => {
                    await html5QrCode.stop();
                    scanLine?.classList.add('hidden');
                    handleScan(decodedText);
                }
            );
        } catch (err) {
            console.error(err);
            alert(
                "Camera failed to start.\n\n• HTTPS required\n• Allow camera permissions\n• Close other apps using the camera"
            );
            resetScanner();
        }
    });

    async function handleScan(url) {
        feedback?.classList.remove('hidden');
        feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-blue-50 text-blue-700 animate-pulse";
        feedback.innerText = "Verifying Station Security...";

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const data = await response.json();
                throw new Error(data.message || "Invalid QR Code");
            }

            feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-emerald-50 text-emerald-700";
            feedback.innerText = "Success! Attendance Recorded.";

            if (navigator.vibrate) navigator.vibrate(200);
            setTimeout(() => window.location.href = "/hr/dashboard", 2000);

        } catch (err) {
            feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-red-50 text-red-700";
            feedback.innerText = err.message;
            resetScanner();
        }
    }

    function resetScanner() {
        startBtn.disabled = false;
        startBtn.style.display = 'block';
        scanLine?.classList.add('hidden');
    }
}