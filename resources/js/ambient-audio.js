// resources/js/ambient-audio.js
export default () => ({
    isAudioOn: false,
    scentAudio: null,
    init() {
        this.scentAudio = document.getElementById('scentAudio');
        if (!this.scentAudio) {
            console.warn('Ambient audio element not found.');
            return;
        }
        // Optional: Stop audio if user navigates away or closes tab
        window.addEventListener('beforeunload', () => {
            if (this.isAudioOn && this.scentAudio) {
                this.scentAudio.pause();
            }
        });
    },
    toggleAudio() {
        if (!this.scentAudio) return;
        if (this.scentAudio.paused) {
            this.scentAudio.volume = 0.10; // Even lower
            this.scentAudio.play().then(() => {
                this.isAudioOn = true;
            }).catch(error => {
                console.error("Audio play failed:", error);
                this.isAudioOn = false; // Ensure state is correct
            });
        } else {
            this.scentAudio.pause();
            this.isAudioOn = false;
        }
    }
});
