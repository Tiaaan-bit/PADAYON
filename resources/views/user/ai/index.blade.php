@extends('layouts.user')

@section('title', 'Padayon Massage Center - Aira AI Assistant')

@section('content')
    <div class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-3 py-4 sm:px-6 sm:py-6">
        <section class="w-full max-w-4xl overflow-hidden rounded-2xl border border-[#e8e0d5] bg-white shadow-sm">

            {{-- AIRA CHARACTER --}}
            <div class="bg-white px-4 py-2 text-center">
                <div class="mx-auto flex h-32 w-32 items-center justify-center sm:h-40 sm:w-40">
                    <img id="aira-character" src="{{ asset('images/aira/idel1.gif') }}"
                        data-idle="{{ asset('images/aira/idel1.gif') }}" data-typing="{{ asset('images/aira/idel1.gif') }}"
                        data-listening="{{ asset('images/aira/listening1.gif') }}"
                        data-talking="{{ asset('images/aira/talking1.gif') }}" alt="Aira, your Padayon AI assistant"
                        class="h-full w-full object-contain">
                </div>
            </div>

            <p id="aira-character-status" class="sr-only" role="status" aria-live="polite">Ready to help you</p>

            <p id="aira-voice-instructions" class="px-4 pb-2 text-center text-sm text-gray-600" role="status"
                aria-live="polite"></p>

            {{-- CONVERSATION --}}
            <div id="aira-messages" role="log" aria-live="polite" aria-label="Conversation with Aira"
                class="h-72 min-h-0 max-h-72 space-y-4 overflow-y-auto overscroll-contain bg-white p-4 sm:h-80 sm:max-h-80 sm:p-6">
            </div>

            {{-- CHAT INPUT --}}
            <div class="border-t border-[#eee5d9] bg-white p-4 sm:px-5 sm:pb-5">
                <form id="aira-form" class="flex items-end gap-2">
                    <label for="aira-input" class="sr-only">Message Aira</label>

                    <textarea id="aira-input" rows="1" maxlength="2000" placeholder="Type your message..."
                        class="min-h-12 max-h-32 flex-1 resize-none overflow-y-auto rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20"></textarea>

                    {{-- OPTIONAL MICROPHONE INPUT --}}
                    <button id="aira-mic" type="button" aria-label="Speak your message" title="Speak your message"
                        class="inline-flex min-h-12 min-w-12 shrink-0 items-center justify-center rounded-xl bg-[#849753] px-3 py-3 text-white transition hover:bg-[#718442] focus:outline-none focus:ring-2 focus:ring-[#849753] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <rect x="9" y="2" width="6" height="13" rx="3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10a7 7 0 0 0 14 0m-7 7v5m-4 0h8" />
                        </svg>
                    </button>

                    {{-- OPTIONAL VOICE REPLIES --}}
                    <button id="aira-voice-toggle" type="button" aria-label="Enable voice replies" aria-pressed="false"
                        title="Toggle voice replies"
                        class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#849753] p-3 text-white transition hover:bg-[#718442] focus:outline-none focus:ring-2 focus:ring-[#849753] focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5 6 9H3v6h3l5 4V5Zm4 4a5 5 0 0 1 0 6m3-9a9 9 0 0 1 0 12" />
                        </svg>
                    </button>

                    {{-- SEND MESSAGE --}}
                    <button id="aira-send" type="submit"
                        class="min-h-12 shrink-0 rounded-xl bg-[#6F4E37] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#59402e] focus:outline-none focus:ring-2 focus:ring-[#6F4E37] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        Send
                    </button>
                </form>
            </div>
        </section>
    </div>

    <script>
        (() => {
            function initializeAira() {
                const messages = document.getElementById('aira-messages');

                if (!messages || messages.dataset.initialized === 'true') {
                    return;
                }

                const form = document.getElementById('aira-form');
                const input = document.getElementById('aira-input');
                const sendButton = document.getElementById('aira-send');
                const micButton = document.getElementById('aira-mic');
                const voiceToggle = document.getElementById('aira-voice-toggle');
                const voiceToggleLabel = document.getElementById('aira-voice-toggle-label');
                const voiceInstructions = document.getElementById('aira-voice-instructions');
                const character = document.getElementById('aira-character');
                const characterStatus = document.getElementById('aira-character-status');

                if (
                    !form || !input || !sendButton || !micButton ||
                    !voiceToggle || !voiceInstructions || !character ||
                    !characterStatus
                ) {
                    console.error('Aira initialization failed: required HTML elements are missing.');
                    return;
                }

                // Mark initialized only after confirming required elements exist.
                messages.dataset.initialized = 'true';

                const Recognition =
                    window.SpeechRecognition || window.webkitSpeechRecognition;

                const conversation = [];
                let busy = false;
                let recognition = null;
                let recording = false;
                let pendingTranscript = '';
                let voiceRepliesEnabled = false;

                const animations = {
                    idle: character.dataset.idle,
                    typing: character.dataset.typing,
                    listening: character.dataset.listening,
                    talking: character.dataset.talking
                };

                function setState(next) {
                    if (!animations[next]) {
                        next = 'idle';
                    }

                    const labels = {
                        idle: 'Ready to help you',
                        typing: 'Aira is thinking...',
                        listening: 'Aira is listening...',
                        talking: 'Aira is speaking...'
                    };

                    characterStatus.textContent = labels[next];

                    if (character.getAttribute('src') !== animations[next]) {
                        character.src = animations[next];
                    }
                }

                function addMessage(role, text) {
                    const row = document.createElement('div');

                    row.className = role === 'user' ?
                        'flex justify-end' :
                        'flex justify-start';

                    const bubble = document.createElement('div');

                    bubble.className = role === 'user' ?
                        'max-w-[88%] whitespace-pre-wrap break-words rounded-2xl rounded-br-sm bg-[#849753] px-4 py-3 text-sm leading-6 text-white' :
                        'max-w-[88%] whitespace-pre-wrap break-words rounded-2xl rounded-bl-sm border border-[#e8e0d5] bg-[#F8F6F2] px-4 py-3 text-sm leading-6 text-gray-800';

                    bubble.textContent = text;
                    row.appendChild(bubble);
                    messages.appendChild(row);
                    messages.scrollTop = messages.scrollHeight;
                }

                // GREETING: display exactly once per chat initialization.
                const greeting = @json('Good day, ' . auth()->user()->name . '! How can I help you today?');

                addMessage('assistant', greeting);

                conversation.push({
                    role: 'assistant',
                    content: greeting
                });

                function stopListening() {
                    const currentRecognition = recognition;

                    recognition = null;
                    recording = false;
                    pendingTranscript = '';

                    if (currentRecognition) {
                        try {
                            currentRecognition.abort();
                        } catch (error) {
                            // Recognition may already have stopped.
                        }
                    }

                    micButton.setAttribute('aria-label', 'Speak your message');
                    micButton.title = 'Speak your message';
                }

                function stopAudio() {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                    }
                }

                function updateVoiceToggle() {
                    if (voiceToggleLabel) {
                        voiceToggleLabel.textContent = voiceRepliesEnabled ?
                            'Voice replies: On' :
                            'Voice replies: Off';
                    }

                    voiceToggle.setAttribute(
                        'aria-pressed',
                        voiceRepliesEnabled ? 'true' : 'false'
                    );

                    voiceToggle.setAttribute(
                        'aria-label',
                        voiceRepliesEnabled ?
                        'Disable voice replies' :
                        'Enable voice replies'
                    );

                    voiceToggle.classList.toggle(
                        'bg-[#6F4E37]',
                        voiceRepliesEnabled
                    );

                    voiceToggle.classList.toggle(
                        'bg-[#849753]',
                        !voiceRepliesEnabled
                    );
                }

                function speak(text) {
                    if (!voiceRepliesEnabled || !('speechSynthesis' in window)) {
                        setState('idle');
                        return;
                    }

                    stopAudio();

                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'en-PH';
                    utterance.rate = 0.95;

                    const voices = window.speechSynthesis.getVoices();

                    const preferred =
                        voices.find(voice =>
                            /^en-PH$/i.test(voice.lang) &&
                            /female|zira|samantha|aria|jenny/i.test(voice.name)
                        ) ||
                        voices.find(voice => /^en-PH$/i.test(voice.lang)) ||
                        voices.find(voice =>
                            /^en-/i.test(voice.lang) &&
                            /female|zira|samantha|aria|jenny/i.test(voice.name)
                        );

                    if (preferred) {
                        utterance.voice = preferred;
                    }

                    utterance.onstart = () => setState('talking');

                    utterance.onend = () => {
                        setState(recording ? 'listening' : 'idle');
                    };

                    utterance.onerror = () => {
                        setState(recording ? 'listening' : 'idle');
                    };

                    window.speechSynthesis.speak(utterance);
                }

                async function sendMessage(raw) {
                    const text = raw.trim();

                    if (!text || busy) {
                        return;
                    }

                    busy = true;
                    sendButton.disabled = true;
                    input.disabled = true;
                    micButton.disabled = true;

                    stopListening();
                    stopAudio();

                    addMessage('user', text);

                    conversation.push({
                        role: 'user',
                        content: text
                    });

                    input.value = '';
                    input.style.height = 'auto';

                    setState('typing');
                    voiceInstructions.textContent = 'Aira is preparing a response...';

                    try {
                        const response = await fetch(
                            @json(route('user.ai.chat')), {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': @json(csrf_token())
                                },
                                body: JSON.stringify({
                                    messages: conversation
                                        .filter(message =>
                                            message.role === 'user' ||
                                            message.role === 'assistant'
                                        )
                                        .slice(-10)
                                })
                            }
                        );

                        let data;

                        try {
                            data = await response.json();
                        } catch (error) {
                            throw new Error('The server returned an invalid response. Please try again.');
                        }

                        if (!response.ok) {
                            throw new Error(
                                data.message ||
                                'Aira is temporarily unavailable. Please try again.'
                            );
                        }

                        if (typeof data.reply !== 'string' || !data.reply.trim()) {
                            throw new Error('Aira returned an empty response. Please try again.');
                        }

                        conversation.push({
                            role: 'assistant',
                            content: data.reply
                        });

                        addMessage('assistant', data.reply);

                        if (voiceRepliesEnabled) {
                            speak(data.reply);
                            voiceInstructions.textContent =
                                'Aira has replied. You can continue typing or use the microphone.';
                        } else {
                            setState('idle');
                            voiceInstructions.textContent =
                                'You can type another message or press the microphone to speak.';
                        }
                    } catch (error) {
                        const lastMessage = conversation[conversation.length - 1];

                        if (
                            lastMessage &&
                            lastMessage.role === 'user' &&
                            lastMessage.content === text
                        ) {
                            conversation.pop();
                        }

                        addMessage(
                            'assistant',
                            error.message || 'Please try again in a moment.'
                        );

                        setState('idle');

                        voiceInstructions.textContent =
                            'There was a problem sending your message. Please try again.';
                    } finally {
                        busy = false;
                        sendButton.disabled = false;
                        input.disabled = false;
                        micButton.disabled = !Recognition;

                        if (!recording) {
                            setState('idle');
                        }

                        input.focus();
                    }
                }

                function startListening() {
                    if (!Recognition) {
                        voiceInstructions.textContent =
                            'Voice input is not supported in this browser. Please type your message.';
                        return;
                    }

                    if (busy || recording) {
                        return;
                    }

                    stopAudio();
                    pendingTranscript = '';

                    const currentRecognition = new Recognition();
                    recognition = currentRecognition;

                    currentRecognition.lang = 'en-PH';
                    currentRecognition.continuous = false;
                    currentRecognition.interimResults = false;
                    currentRecognition.maxAlternatives = 1;

                    currentRecognition.onstart = () => {
                        recording = true;
                        micButton.setAttribute('aria-label', 'Stop recording');
                        micButton.title = 'Stop recording';

                        voiceInstructions.textContent = 'Aira is listening. Speak now.';
                        setState('listening');
                    };

                    currentRecognition.onresult = event => {
                        const result = event.results[0]?.[0]?.transcript;

                        if (result) {
                            pendingTranscript = result.trim();
                            voiceInstructions.textContent = 'I heard: ' + pendingTranscript;
                        }
                    };

                    currentRecognition.onerror = event => {
                        const errors = {
                            'not-allowed': 'Please allow microphone access in your browser.',
                            'no-speech': 'No speech detected. Please try again.',
                            'network': 'Voice recognition needs a network connection.',
                            'audio-capture': 'No microphone was detected. Check your microphone.',
                            'service-not-allowed': 'Your browser does not allow voice recognition.'
                        };

                        voiceInstructions.textContent =
                            errors[event.error] || 'Voice input failed. Please try again.';
                    };

                    currentRecognition.onend = () => {
                        recording = false;

                        if (recognition === currentRecognition) {
                            recognition = null;
                        }

                        micButton.setAttribute('aria-label', 'Speak your message');
                        micButton.title = 'Speak your message';

                        const transcript = pendingTranscript;
                        pendingTranscript = '';

                        if (transcript && !busy) {
                            voiceInstructions.textContent =
                                'Sending your spoken message to Aira...';

                            sendMessage(transcript);
                        } else if (!busy) {
                            setState('idle');

                            voiceInstructions.textContent =
                                'You can type a message or press the microphone to speak.';
                        }
                    };

                    try {
                        currentRecognition.start();
                    } catch (error) {
                        recognition = null;
                        recording = false;

                        voiceInstructions.textContent =
                            'Unable to start the microphone. Please try again.';

                        setState('idle');
                    }
                }

                // Submit typed messages.
                form.addEventListener('submit', event => {
                    event.preventDefault();
                    sendMessage(input.value);
                });

                // Enter sends; Shift + Enter creates a new line.
                input.addEventListener('keydown', event => {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        form.requestSubmit();
                    }
                });

                // Auto-resize the text area.
                input.addEventListener('input', () => {
                    input.style.height = 'auto';
                    input.style.height = Math.min(input.scrollHeight, 128) + 'px';

                    if (!busy && input.value.trim()) {
                        setState('typing');
                    } else if (!busy && !recording) {
                        setState('idle');
                    }
                });

                // Microphone input is optional.
                micButton.addEventListener('click', () => {
                    if (busy) {
                        return;
                    }

                    if (!Recognition) {
                        voiceInstructions.textContent =
                            'Voice input is not supported in this browser. Please type your message.';
                        return;
                    }

                    if (recording && recognition) {
                        try {
                            recognition.stop();
                        } catch (error) {
                            // Recognition may already have stopped.
                        }

                        return;
                    }

                    startListening();
                });

                // Optional spoken replies.
                voiceToggle.addEventListener('click', () => {
                    voiceRepliesEnabled = !voiceRepliesEnabled;

                    if (voiceRepliesEnabled && !('speechSynthesis' in window)) {
                        voiceRepliesEnabled = false;
                        updateVoiceToggle();

                        voiceInstructions.textContent =
                            'Spoken replies are not supported in this browser.';
                        return;
                    }

                    updateVoiceToggle();

                    if (!voiceRepliesEnabled) {
                        stopAudio();

                        if (!recording) {
                            setState('idle');
                        }

                        voiceInstructions.textContent =
                            'Voice replies are off. Aira will respond in text.';
                        return;
                    }

                    voiceInstructions.textContent =
                        'Voice replies are on. Aira can now read replies aloud.';
                });

                // Initial state.
                micButton.disabled = !Recognition;
                updateVoiceToggle();
                setState('idle');

                voiceInstructions.textContent = Recognition ?
                    'Type a message or press the microphone to speak. Voice replies are optional.' :
                    'Type a message to chat with Aira. Voice input is not supported in this browser.';

                if ('speechSynthesis' in window) {
                    window.speechSynthesis.getVoices();
                }
            }

            // Support regular page loads and Livewire navigation.
            document.addEventListener('DOMContentLoaded', initializeAira);
            document.addEventListener('livewire:navigated', initializeAira);
        })();
    </script>
@endsection
