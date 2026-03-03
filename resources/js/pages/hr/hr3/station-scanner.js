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
                    // Stop scanning immediately after a successful read
                    await html5QrCode.stop();
                    scanLine?.classList.add("hidden");

                    // Send token directly for verification
                    handleScan(decodedText.trim());
                }
            );
        } catch (err) {
            console.error("Camera start failed:", err);
            alert("Camera failed to start. Make sure you are on HTTPS and allowed camera permissions.");
            reset();
        }
    });

async function handleScan(decodedText) {
    feedback?.classList.remove("hidden");
    feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-blue-50 text-blue-700 text-center";
    feedback.innerText = "Verifying Attendance...";

    let tokenToSend = decodedText.trim();

    if (tokenToSend.includes('/')) {
        const parts = tokenToSend.split('/');
        tokenToSend = parts[parts.length - 1]; 
    }

    try {
        const response = await fetch("/attendance/verify", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({ 
                token: tokenToSend,
                station: 1 
            }),
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to record attendance");
        }

        feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-emerald-50 text-emerald-700 text-center";
        feedback.innerText = "Attendance Recorded Successfully!";

        setTimeout(() => window.location.href = "/hr/dashboard", 1500);

    } catch (err) {
        console.error("Verification Error:", err);
        feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-red-50 text-red-700 text-center";
        feedback.innerText = err.message;
        
        setTimeout(() => reset(), 3000);
    }
}


    function reset() {
        startBtn.disabled = false;
        startBtn.style.display = "block";
        scanLine?.classList.add("hidden");
    }
}