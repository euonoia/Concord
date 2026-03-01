import { Html5Qrcode } from "html5-qrcode";

export default function initAttendanceScanner() {
    console.log("Attendance Scanner Module Loaded");

    const startBtn = document.getElementById("start-btn");
    const scanLine = document.getElementById("scan-line");
    const feedback = document.getElementById("feedback");
    const readerId = "reader";

    if (!startBtn) return;

    let html5QrCode;

    startBtn.addEventListener("click", async () => {
        startBtn.disabled = true;
        startBtn.style.display = "none";
        scanLine?.classList.remove("hidden");

        html5QrCode = new Html5Qrcode(readerId);

        try {
            const devices = await Html5Qrcode.getCameras();
            if (!devices || devices.length === 0) throw new Error("No cameras found");

            const backCamera = devices.find(d => d.label.toLowerCase().includes("back") || d.label.toLowerCase().includes("rear"));
            const cameraId = backCamera ? backCamera.id : devices[0].id;

            await html5QrCode.start(
                cameraId,
                { fps: 15, qrbox: 250 },
                async (decodedText) => {
                    await html5QrCode.stop();
                    scanLine?.classList.add("hidden");
                    handleScan(decodedText);
                }
            );
        } catch (err) {
            console.error(err);
            alert("Camera failed to start. Make sure you are on HTTPS and allowed camera permissions.");
            reset();
        }
    });

    async function handleScan(decodedText) {
        feedback?.classList.remove("hidden");
        feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-blue-50 text-blue-700";
        feedback.innerText = "Verifying Attendance...";

        let payload;
        try {
            payload = JSON.parse(decodedText);
            if (!payload.token) throw new Error("QR code missing token");
        } catch {
            feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-red-50 text-red-700";
            feedback.innerText = "Invalid QR code";
            reset();
            return;
        }

        try {
            const response = await fetch("/hr/hr3/attendance/verify", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                },
                body: JSON.stringify({ token: payload.token }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) throw new Error(data.message || "Failed to record attendance");

            feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-emerald-50 text-emerald-700";
            feedback.innerText = "Attendance Recorded Successfully!";

            setTimeout(() => { window.location.href = "/hr/dashboard"; }, 2000);

        } catch (err) {
            feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-red-50 text-red-700";
            feedback.innerText = err.message;
            reset();
        }
    }

    function reset() {
        startBtn.disabled = false;
        startBtn.style.display = "block";
        scanLine?.classList.add("hidden");
    }
}