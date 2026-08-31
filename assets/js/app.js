let isPlaying = false;
let currentAudio = null;
let sequenceTimeout = null;

function playAudioFile(url) {
    return new Promise((resolve) => {
        if (!isPlaying) return resolve('stopped');
        currentAudio = new Audio(url);
        currentAudio.onended = resolve;
        currentAudio.onerror = resolve; // Continue to next even if file is missing
        currentAudio.play().catch(resolve);
    });
}

async function playAlarmSequence(roomName, floor, area) {
    try {

        
        // 2. Play Room Name wav file
        if (!isPlaying) return;
        // Gunakan nama ruangan sebagai nama file (misal: "IGD.wav", "Ramin.wav")
        await playAudioFile('assets/audio/' + encodeURIComponent(roomName) + '.wav');
        

        
        // Loop the sequence after 2 seconds
        if (isPlaying) {
            sequenceTimeout = setTimeout(() => {
                if (isPlaying) playAlarmSequence(roomName, floor, area);
            }, 2000);
        }
    } catch (e) {
        // Stopped or error
    }
}

function playAlarmSound(roomName, floor, area) {
    if (isPlaying) return;
    isPlaying = true;
    playAlarmSequence(roomName, floor, area);
}

function stopAlarmSound() {
    isPlaying = false;
    if (currentAudio) {
        currentAudio.pause();
        currentAudio.currentTime = 0;
        currentAudio = null;
    }
    if (sequenceTimeout) clearTimeout(sequenceTimeout);
}

// Function to call Code Blue
async function callCodeBlue(ip) {
    try {
        const formData = new FormData();
        if (ip) formData.append('simulate_ip', ip);
        
        const response = await fetch('api/call_code_blue.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        alert(result.message);
    } catch (error) {
        console.error("Error calling code blue:", error);
        alert("Terjadi kesalahan sistem.");
    }
}

// Team Dashboard Polling
let activeCallId = null;

async function checkPendingCalls() {
    try {
        const response = await fetch('api/check_calls.php');
        const result = await response.json();
        
        const standbyContainer = document.getElementById('standby-container');
        const alertContainer = document.getElementById('alert-container');
        
        if (result.status === 'success' && result.data.length > 0) {
            const call = result.data[0];
            activeCallId = call.call_id;
            
            document.getElementById('alert-room').innerText = call.room_name;
            document.getElementById('alert-floor').innerText = call.floor;
            
            standbyContainer.style.display = 'none';
            alertContainer.style.display = 'block';
            document.body.style.backgroundColor = '#900'; // Flash red
            playAlarmSound(call.room_name, call.floor, call.area);
        } else {
            activeCallId = null;
            standbyContainer.style.display = 'block';
            alertContainer.style.display = 'none';
            document.body.style.backgroundColor = 'var(--bg-dark)';
            stopAlarmSound();
        }
    } catch (error) {
        console.error("Error checking calls:", error);
    }
}

async function respondToCall() {
    if (!activeCallId) return;
    
    try {
        const formData = new FormData();
        formData.append('call_id', activeCallId);
        
        const response = await fetch('api/respond_call.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            stopAlarmSound();
            document.body.style.backgroundColor = 'var(--bg-dark)';
            // Polling will handle UI reset on next tick
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Error responding to call:", error);
    }
}

// Start polling for Team Dashboard if element exists
if (document.getElementById('team-dashboard')) {
    // We need user interaction to unlock audio API.
    document.addEventListener('click', () => {
        // Just trigger a silent speech to unlock the engine on mobile/strict browsers
        const silent = new SpeechSynthesisUtterance('');
        window.speechSynthesis.speak(silent);
    }, { once: true });
    
    setInterval(checkPendingCalls, 2000);
    checkPendingCalls();
}
