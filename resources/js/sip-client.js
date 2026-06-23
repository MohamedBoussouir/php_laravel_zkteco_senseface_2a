import { Web } from "sip.js";

document.addEventListener('DOMContentLoaded', () => {
    // Check if SIP is configured/enabled via global window variable
    if (!window.SIP_CONFIG) return;

    const uri = window.SIP_CONFIG.uri;       // e.g. "sip:100@localhost"
    const password = window.SIP_CONFIG.password; // "dashboardpass"
    const wsServer = window.SIP_CONFIG.wsServer; // "wss://localhost:8089/ws"

    const userAgent = new Web.SimpleUser(wsServer, {
        aor: uri,
        userAgentOptions: {
            authorizationPassword: password,
            authorizationUsername: "100",
            sessionDescriptionHandlerFactoryOptions: {
                peerConnectionConfiguration: {
                    iceServers: []
                },
                iceGatheringTimeout: 500
            }
        },
        media: {
            constraints: { audio: true, video: false },
            remote: {
                audio: document.getElementById('sip-remote-audio')
            }
        }
    });

    const connectSIP = async () => {
        try {
            await userAgent.connect();
            await userAgent.register({
                requestDelegate: {
                    onReject: (response) => {
                        console.error("SIP Registration rejected", response);
                    }
                }
            });
            console.log("SIP Registered successfully!");
        } catch (error) {
            console.error("Failed to connect SIP:", error);
        }
    };

    // UI Elements
    const callModal = document.getElementById('sip-call-modal');
    const acceptBtn = document.getElementById('sip-accept-btn');
    const rejectBtn = document.getElementById('sip-reject-btn');
    const unlockBtn = document.getElementById('sip-unlock-btn');
    const endBtn = document.getElementById('sip-end-btn');
    const ringtone = document.getElementById('sip-ringtone');
    const timerEl = document.getElementById('sip-call-timer');
    const remoteIframe = document.getElementById('sip-remote-iframe');

    let currentSession = null;
    let callTimerInterval = null;
    let callSeconds = 0;

    function formatTime(seconds) {
        const m = Math.floor(seconds / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function startTimer() {
        if (!timerEl) return;
        timerEl.style.display = 'block';
        callSeconds = 0;
        timerEl.innerText = "00:00";
        if (callTimerInterval) clearInterval(callTimerInterval);
        callTimerInterval = setInterval(() => {
            callSeconds++;
            timerEl.innerText = formatTime(callSeconds);
        }, 1000);
    }

    function stopTimer() {
        if (timerEl) timerEl.style.display = 'none';
        if (callTimerInterval) {
            clearInterval(callTimerInterval);
            callTimerInterval = null;
        }
    }

    userAgent.delegate = {
        onCallReceived: async () => {
            console.log("Incoming call from Intercom!");
            callModal.style.display = 'flex';
            acceptBtn.style.display = 'inline-block';
            rejectBtn.style.display = 'inline-block';
            unlockBtn.style.display = 'none';
            endBtn.style.display = 'none';
            acceptBtn.disabled = false;
            if (remoteIframe) remoteIframe.style.display = 'block';
            stopTimer();
            
            if (ringtone) {
                ringtone.currentTime = 0;
                ringtone.play().catch(e => console.log("Ringtone autoplay blocked by browser:", e));
            }
        },
        onCallAnswered: () => {
            console.log("Call Answered");
            unlockBtn.style.display = 'inline-block';
            endBtn.style.display = 'inline-block';
            acceptBtn.style.display = 'none';
            rejectBtn.style.display = 'none';
            startTimer();
            
            if (ringtone) {
                ringtone.pause();
                ringtone.currentTime = 0;
            }
        },
        onCallHungUp: () => {
            console.log("Call Ended");
            callModal.style.display = 'none';
            unlockBtn.style.display = 'none';
            endBtn.style.display = 'none';
            currentSession = null;
            if (remoteIframe) remoteIframe.style.display = 'none';
            stopTimer();
            
            if (ringtone) {
                ringtone.pause();
                ringtone.currentTime = 0;
            }
        }
    };

    acceptBtn.addEventListener('click', async () => {
        acceptBtn.disabled = true;
        try {
            await userAgent.answer();
        } catch (error) {
            console.error("Failed to answer call:", error);
            acceptBtn.disabled = false;
        }
    });

    rejectBtn.addEventListener('click', async () => {
        callModal.style.display = 'none';
        if (remoteIframe) remoteIframe.style.display = 'none';
        stopTimer();
        if (ringtone) {
            ringtone.pause();
            ringtone.currentTime = 0;
        }
        try {
            // Workaround: ZKTeco devices often ignore SIP 'Decline' during ringing.
            // We answer and immediately hang up to force the device to stop ringing.
            await userAgent.answer();
            await userAgent.hangup();
        } catch (error) {
            console.error("Failed to reject call via answer/hangup:", error);
            try { await userAgent.decline(); } catch (e) {}
        }
    });

    endBtn.addEventListener('click', async () => {
        callModal.style.display = 'none';
        if (remoteIframe) remoteIframe.style.display = 'none';
        stopTimer();
        try {
            await userAgent.hangup();
        } catch (error) {
            console.error("Failed to hangup:", error);
        }
    });

    unlockBtn.addEventListener('click', async () => {
        // Send DTMF '*' to unlock the door
        try {
            await userAgent.sendDTMF('*');
            alert("Unlock command sent via DTMF!");
        } catch (error) {
            console.error("Failed to send DTMF:", error);
        }
    });

    // Start connection
    connectSIP();
});
