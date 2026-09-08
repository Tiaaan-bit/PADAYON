<div x-data="dessaWidget()" x-init="init()" x-cloak class="fixed right-4 bottom-4 z-50 mb-15">
    <button x-show="!open" @click="toggle()" type="button"
        class="relative w-13 h-13 rounded-full bg-white shadow-2xl border border-gray-200 overflow-hidden transition transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-[#849753]/30"
        aria-label="Open Dessa assistant">
        <img :src="currentGif" alt="Dessa assistant" class="w-full h-full object-cover">
    </button>

    <div x-show="open" x-transition.opacity @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div @click.away="open = false" x-transition.scale.95
            class="w-[92vw] max-w-[92vw] sm:w-full sm:max-w-180 max-h-[88vh] rounded-2xl bg-white border border-gray-200 shadow-2xl overflow-hidden">
            <div class="bg-[#849753] text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="min-w-0">
                        <p class="font-semibold leading-none">Dessa</p>
                        <p class="text-xs opacity-90">Voice receptionist</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span x-show="listening" class="text-xs bg-white/20 px-2 py-1 rounded-full">Listening</span>
                    <span x-show="isSpeaking" class="text-xs bg-white/20 px-2 py-1 rounded-full">Speaking</span>
                    <button @click="open = false" class="text-white/90 hover:text-white text-lg leading-none"
                        type="button" aria-label="Close assistant">
                        ✕
                    </button>
                </div>
            </div>

            <div class="p-3 sm:p-5 space-y-3 sm:space-y-5">
                <div class="rounded-2xl bg-gray-50 border border-gray-100 p-3 h-65 sm:h-105 overflow-y-auto relative"
                    x-ref="messagesBox">
                    <div class="sticky top-0 z-20 bg-gray-50 flex justify-center pt-2 pb-4">
                        <img :src="currentGif" alt="Dessa avatar" class="w-20 h-20 sm:w-24 sm:h-24 object-cover">
                    </div>

                    <div class="space-y-3">
                        <template x-for="message in messages" :key="message.id">
                            <div :class="message.role === 'user' ? 'text-right' : 'text-left'">
                                <div class="inline-block max-w-[85%] rounded-2xl px-3 py-2 text-sm leading-relaxed shadow-sm"
                                    :class="message.role === 'user' ?
                                        'bg-[#849753] text-white' :
                                        'bg-white border border-gray-200 text-gray-700'"
                                    x-text="message.text"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="toggleListening()" type="button"
                        class="px-3 py-2 rounded-xl border text-sm font-medium transition"
                        :class="listening ? 'border-red-300 text-red-600 bg-red-50' :
                            'border-gray-300 text-gray-700 bg-white hover:bg-gray-50'">
                        <span x-text="listening ? 'Stop Mic' : 'Mic'"></span>
                    </button>

                    <input x-model="input" @input="handleTyping()" @keydown.enter.prevent="sendText()" type="text"
                        placeholder="Ask Dessa or type a message..."
                        class="flex-1 rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753] text-sm">

                    <button @click="sendText()" type="button"
                        class="px-4 py-2 rounded-xl bg-[#849753] text-white text-sm font-medium hover:bg-[#6F4E37] transition">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function dessaWidget() {
        return {
            open: false,
            input: '',
            listening: false,
            isSpeaking: false,
            recognition: null,
            voices: [],
            selectedVoiceName: '',

            idleGif: '{{ asset('build/assets/images/desa.gif') }}',
            listeningGif: '{{ asset('build/assets/images/desa4.gif') }}',
            talkingGif: '{{ asset('build/assets/images/desa2.gif') }}',
            lookingDownGif: '{{ asset('build/assets/images/desa-looking-down.gif') }}',
            currentGif: '{{ asset('build/assets/images/desa.gif') }}',

            messages: [{
                id: 1,
                role: 'assistant',
                text: 'Hi, I’m Dessa!'
            }],

            async init() {
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

                if (SpeechRecognition) {
                    this.recognition = new SpeechRecognition();
                    this.recognition.lang = 'en-US';
                    this.recognition.interimResults = false;
                    this.recognition.continuous = false;

                    this.recognition.onresult = (event) => {
                        const transcript = event.results[0][0].transcript;
                        this.input = transcript;
                        this.listening = false;
                        this.currentGif = this.idleGif;
                        this.sendText();
                    };

                    this.recognition.onend = () => {
                        this.listening = false;
                        if (!this.isSpeaking && !this.input.trim()) {
                            this.currentGif = this.idleGif;
                        }
                    };

                    this.recognition.onerror = () => {
                        this.listening = false;
                        if (!this.isSpeaking && !this.input.trim()) {
                            this.currentGif = this.idleGif;
                        }
                        this.messages.push({
                            id: Date.now(),
                            role: 'assistant',
                            text: 'I could not hear that. Please try again.'
                        });
                        this.scrollToBottom();
                    };
                }

                await this.loadVoices();
            },

            async loadVoices() {
                const readVoices = () => window.speechSynthesis.getVoices();

                let voices = readVoices();
                if (!voices.length) {
                    await new Promise((resolve) => {
                        const done = () => resolve();
                        window.speechSynthesis.addEventListener('voiceschanged', done, {
                            once: true
                        });
                        setTimeout(done, 1500);
                    });
                    voices = readVoices();
                }

                this.voices = voices;

                const preferred =
                    voices.find(v => /female|woman|girl|samantha|victoria|zira/i.test(v.name)) ||
                    voices.find(v => /en/i.test(v.lang)) ||
                    voices[0] ||
                    null;

                this.selectedVoiceName = preferred ? preferred.name : '';
            },

            getSelectedVoice() {
                return this.voices.find(v => v.name === this.selectedVoiceName) || this.voices[0] || null;
            },

            toggle() {
                this.open = true;
                this.$nextTick(() => this.scrollToBottom());
            },

            handleTyping() {
                if (this.listening || this.isSpeaking) return;

                this.currentGif = this.input.trim().length > 0 ?
                    this.lookingDownGif :
                    this.idleGif;
            },

            toggleListening() {
                if (!this.recognition) {
                    this.messages.push({
                        id: Date.now(),
                        role: 'assistant',
                        text: 'Voice input is not supported in this browser.'
                    });
                    this.scrollToBottom();
                    return;
                }

                if (this.listening) {
                    this.recognition.stop();
                    this.listening = false;
                    if (!this.isSpeaking && !this.input.trim()) {
                        this.currentGif = this.idleGif;
                    }
                    return;
                }

                this.open = true;
                this.listening = true;
                this.isSpeaking = false;
                this.currentGif = this.listeningGif;
                this.recognition.start();

                this.messages.push({
                    id: Date.now(),
                    role: 'assistant',
                    text: 'I’m listening. Tell me what you need.'
                });
                this.scrollToBottom();
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const box = this.$refs.messagesBox;
                    if (box) box.scrollTop = box.scrollHeight;
                });
            },

            speak(text) {
                if (!window.speechSynthesis) return;

                const utterance = new SpeechSynthesisUtterance(text);
                const voice = this.getSelectedVoice();

                if (voice) {
                    utterance.voice = voice;
                }

                utterance.lang = 'en-US';
                utterance.rate = 0.95;
                utterance.pitch = 1.25;

                utterance.onstart = () => {
                    this.isSpeaking = true;
                    this.listening = false;
                    this.currentGif = this.talkingGif;
                };

                utterance.onend = () => {
                    this.isSpeaking = false;
                    this.currentGif = this.input.trim() ? this.lookingDownGif : this.idleGif;
                };

                utterance.onerror = () => {
                    this.isSpeaking = false;
                    this.currentGif = this.input.trim() ? this.lookingDownGif : this.idleGif;
                };

                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(utterance);
            },

            async sendText() {
                const text = this.input.trim();
                if (!text) return;

                this.messages.push({
                    id: Date.now(),
                    role: 'user',
                    text
                });
                this.input = '';

                if (!this.isSpeaking && !this.listening) {
                    this.currentGif = this.idleGif;
                }

                this.scrollToBottom();

                try {
                    const response = await fetch('/dessa/message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            message: text
                        })
                    });

                    const data = await response.json();
                    const reply = data.reply || 'I could not complete that request.';

                    this.messages.push({
                        id: Date.now() + 1,
                        role: 'assistant',
                        text: reply
                    });

                    this.speak(reply);
                    this.scrollToBottom();
                } catch (error) {
                    const reply = 'Sorry, something went wrong while contacting the appointment system.';

                    this.messages.push({
                        id: Date.now() + 1,
                        role: 'assistant',
                        text: reply
                    });

                    this.speak(reply);
                    this.scrollToBottom();
                }
            }
        }
    }
</script>
