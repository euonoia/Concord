import { Html5Qrcode } from "html5-qrcode";

export function initQrScanner(stationId = 1) {
    const startBtn = document.getElementById('start-btn');
    const scanLine = document.getElementById('scan-line');
    const feedback = document.getElementById('feedback');

    if (!startBtn) return;

    let html5QrCode;
    let currentCameraId = null;

    startBtn.addEventListener('click', async () => {
        startBtn.style.display = 'none';
        scanLine?.classList.remove('hidden');

        html5QrCode = new Html5Qrcode("reader");

        try {
            // Get all cameras
            const devices = await Html5Qrcode.getCameras();
            if (!devices || devices.length === 0) throw new Error("No camera devices found.");

            // Prefer back camera
            const backCamera = devices.find(device =>
                device.label.toLowerCase().includes('back') ||
                device.label.toLowerCase().includes('rear')
            );

            currentCameraId = backCamera ? backCamera.id : devices[0].id;

            await html5QrCode.start(
                currentCameraId,
                {
                    fps: 15,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                async (decodedText) => {
                    await html5QrCode.stop();
                    scanLine?.classList.add('hidden');

                    // Send QR code to backend for verification
                    processScan(decodedText, stationId);
                }
            );

        } catch (err) {
            console.error(err);
            alert(
                "Camera access failed. Make sure:\n\n" +
                "• You are using HTTPS\n" +
                "• Camera permissions are allowed\n" +
                "• No other app is using the camera"
            );
            startBtn.style.display = 'block';
            scanLine?.classList.add('hidden');
        }
    });

    async function processScan(qrCode, station) {
        feedback?.classList.remove('hidden');
        feedback.className =
            "p-4 rounded-2xl mb-6 text-sm font-bold bg-blue-50 text-blue-700";
        feedback.innerText = "Verifying Attendance...";

        try {
            const response = await fetch(`/attendance/verify/${station}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':
                        document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_code: qrCode })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || "Failed to log attendance.");
            }

            feedback.className =
                "p-4 rounded-2xl mb-6 text-sm font-bold bg-emerald-50 text-emerald-700";
            feedback.innerText = "Success! Attendance Recorded.";

            setTimeout(() => {
                window.location.href = "/dashboard";
            }, 2000);

        } catch (err) {
            feedback.className =
                "p-4 rounded-2xl mb-6 text-sm font-bold bg-red-50 text-red-700";
            feedback.innerText = err.message;

            startBtn.style.display = 'block';
        }
    }
}