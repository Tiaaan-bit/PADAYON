@extends('layouts.user')

@section('title', 'Aira AI Assistant | Padayon Massage Center')

@section('content')
    <div class="mx-auto max-w-4xl px-3 py-4 sm:px-6 sm:py-6">
        <section class="overflow-hidden rounded-2xl border border-[#e8e0d5] bg-white shadow-sm">

            {{-- HEADER --}}
            <header class="flex items-center gap-3 bg-[#849753] px-4 py-4 text-white sm:px-6">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3a4 4 0 0 0-4 4v3a4 4 0 0 0 8 0V7a4 4 0 0 0-4-4Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v1a7 7 0 0 0 14 0v-1M12 18v3m-4 0h8" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h1 class="text-lg font-bold">Aira AI Assistant</h1>
                    <p id="aira-character-status" class="text-sm text-white/90" role="status" aria-live="polite">
                        Your Padayon assistant is ready to help.
                    </p>
                </div>
            </header>

            {{-- AIRA CHARACTER --}}
            <div class="border-[#eee5d9] bg-white px-4 py-5 text-center">
                <div class="mx-auto flex h-44 w-44 items-center justify-center sm:h-52 sm:w-52">
                    <img id="aira-character" src="{{ asset('images/aira/aira-idle.gif') }}"
                        data-idle="{{ asset('images/aira/aira-idle.gif') }}"
                        data-typing="{{ asset('images/aira/aira-typing.gif') }}"
                        data-listening="{{ asset('images/aira/aira-listening.gif') }}"
                        data-talking="{{ asset('images/aira/aira-talking.gif') }}" alt="Aira, your Padayon AI assistant"
                        class="h-full w-full object-contain">
                </div>
            </div>

            {{-- INITIAL MODE SELECTION --}}
            <div id="aira-mode-selection" class="bg-white px-4 py-6 sm:px-6">
                <h2 class="text-center text-lg font-semibold text-gray-800">
                    Hello, {{ auth()->user()->name }}!
                </h2>

                <div class="mx-auto mt-1 grid max-w-md grid-cols-1 gap-3 sm:grid-cols-2">
                    <button id="aira-type-mode" type="button"
                        class="flex min-h-14 items-center justify-center gap-3 rounded-xl border-2 border-[#849753] bg-white px-5 py-3 font-semibold text-[#52652b] transition hover:bg-[#718442] focus:outline-none focus:ring-2 focus:ring-[#849753] focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7h8M8 11h8M8 15h5M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                        </svg>
                        Type
                    </button>

                    <button id="aira-speak-mode" type="button"
                        class="flex min-h-14 items-center justify-center gap-3 rounded-xl border-2 border-[#849753] bg-white px-5 py-3 font-semibold text-[#52652b] transition hover:bg-[#f1f4e9] focus:outline-none focus:ring-2 focus:ring-[#849753] focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <rect x="9" y="2" width="6" height="13" rx="3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10a7 7 0 0 0 14 0m-7 7v5m-4 0h8" />
                        </svg>
                        Speak
                    </button>
                </div>
            </div>

            {{-- CONVERSATION: MESSAGES SCROLL INSIDE A FIXED-HEIGHT AREA --}}
            <div id="aira-messages" role="log" aria-live="polite" aria-label="Conversation with Aira"
                class="hidden h-72 min-h-0 max-h-72 space-y-4 overflow-y-auto overscroll-contain bg-white p-4 sm:h-80 sm:max-h-80 sm:p-6">
            </div>

            {{-- TYPE MODE --}}
            <div id="aira-type-panel" class="hidden border-t border-[#eee5d9] bg-white">
                <form id="aira-form" class="flex items-end gap-2 p-4 sm:px-5 sm:pb-5">
                    <label for="aira-input" class="sr-only">Message Aira</label>

                    <textarea id="aira-input" rows="1" maxlength="2000" placeholder="Type your message..."
                        class="min-h-12 max-h-32 flex-1 resize-none overflow-y-auto rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20"></textarea>

                    <button id="aira-send" type="submit"
                        class="min-h-12 rounded-xl bg-[#6F4E37] px-5 py-3 text-sm font-semibold text-white hover:bg-[#59402e] disabled:cursor-not-allowed disabled:opacity-50">
                        Send
                    </button>
                </form>

                <div class="px-4 pb-4 text-center">
                    <button id="aira-change-mode-type" type="button"
                        class="text-sm font-medium text-[#6F4E37] underline hover:text-[#59402e]">
                        Change interaction mode
                    </button>
                </div>
            </div>

            {{-- SPEAK MODE --}}
            <div id="aira-speak-panel" class="hidden border-t border-[#eee5d9] bg-white px-4 py-5">
                <p id="aira-voice-instructions" class="mb-4 text-center text-sm text-gray-600">
                    Press the microphone and speak when you're ready.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3">
                    <button id="aira-mic" type="button"
                        class="inline-flex min-h-12 items-center gap-2 rounded-full bg-[#849753] px-6 py-3 text-sm font-semibold text-white hover:bg-[#718442] focus:outline-none focus:ring-2 focus:ring-[#849753] focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <rect x="9" y="2" width="6" height="13" rx="3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10a7 7 0 0 0 14 0m-7 7v5m-4 0h8" />
                        </svg>
                        <span id="aira-mic-label">Start speaking</span>
                    </button>

                    <button id="aira-stop" type="button"
                        class="min-h-12 rounded-full border border-gray-300 px-4 py-3 text-sm text-gray-600 hover:bg-gray-50">
                        Stop speaking
                    </button>

                    <button id="aira-change-mode-speak" type="button"
                        class="min-h-12 rounded-full border border-[#849753] px-4 py-3 text-sm font-semibold text-[#52652b] hover:bg-[#f1f4e9]">
                        Change mode
                    </button>
                </div>
            </div>

            <p class="border-t border-[#eee5d9] px-4 py-4 text-center text-xs text-gray-500">
                Aira may make mistakes. Check important appointment and payment details in your account.
            </p>
        </section>
    </div>

    <script>
        (() => {
            function initializeAira() {
                const messages = document.getElementById('aira-messages');

                // Avoid duplicate listeners during Livewire navigation.
                if (!messages || messages.dataset.initialized === 'true') {
                    return;
                }

                messages.dataset.initialized = 'true';

                const modeSelection = document.getElementById('aira-mode-selection');
                const typeModeButton = document.getElementById('aira-type-mode');
                const speakModeButton = document.getElementById('aira-speak-mode');
                const typePanel = document.getElementById('aira-type-panel');
                const speakPanel = document.getElementById('aira-speak-panel');
                const changeTypeButton = document.getElementById('aira-change-mode-type');
                const changeSpeakButton = document.getElementById('aira-change-mode-speak');

                const form = document.getElementById('aira-form');
                const input = document.getElementById('aira-input');
                const sendButton = document.getElementById('aira-send');
                const micButton = document.getElementById('aira-mic');
                const micLabel = document.getElementById('aira-mic-label');
                const stopButton = document.getElementById('aira-stop');
                const voiceInstructions = document.getElementById('aira-voice-instructions');

                const character = document.getElementById('aira-character');
                const characterStatus = document.getElementById('aira-character-status');

                const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;

                const conversation = [];

                let busy = false;
                let recognition = null;
                let recording = false;
                let mode = null;
                let pendingTranscript = '';
                let requestGeneration = 0;

                const animations = {
                    idle: character.dataset.idle,
                    typing: character.dataset.typing,
                    listening: character.dataset.listening,
                    talking: character.dataset.talking,
                };

                function setState(next) {
                    if (!animations[next]) {
                        next = 'idle';
                    }

                    const labels = {
                        idle: 'Ready to help you',
                        typing: 'Aira is waiting for your message',
                        listening: 'Aira is listening...',
                        talking: 'Aira is speaking...',
                    };

                    characterStatus.textContent = labels[next];

                    // Avoid restarting the GIF if the same animation is already selected.
                    if (character.getAttribute('src') !== animations[next]) {
                        character.src = animations[next];
                    }
                }

                function stopListening() {
                    if (recognition) {
                        const currentRecognition = recognition;
                        recognition = null;

                        try {
                            currentRecognition.stop();
                        } catch (error) {
                            // Recognition may already have stopped.
                        }
                    }

                    recording = false;
                    pendingTranscript = '';
                    micLabel.textContent = 'Start speaking';
                }

                function stopAudio() {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                    }
                }

                function addMessage(role, text) {
                    const row = document.createElement('div');
                    row.className = role === 'user' ? 'flex justify-end' : 'flex justify-start';

                    const bubble = document.createElement('div');

                    bubble.className = role === 'user' ?
                        'max-w-[88%] whitespace-pre-wrap break-words rounded-2xl rounded-br-sm bg-[#849753] px-4 py-3 text-sm leading-6 text-white' :
                        'max-w-[88%] whitespace-pre-wrap break-words rounded-2xl rounded-bl-sm border border-[#e8e0d5] bg-[#F8F6F2] px-4 py-3 text-sm leading-6 text-gray-800';

                    // textContent prevents messages from being interpreted as HTML.
                    bubble.textContent = text;

                    row.appendChild(bubble);
                    messages.appendChild(row);
                    messages.scrollTop = messages.scrollHeight;
                }

                function showModeSelection() {
                    stopListening();
                    stopAudio();

                    mode = null;

                    modeSelection.classList.remove('hidden');
                    messages.classList.add('hidden');
                    typePanel.classList.add('hidden');
                    speakPanel.classList.add('hidden');

                    if (!busy) {
                        sendButton.disabled = false;
                        micButton.disabled = false;
                        input.disabled = false;
                        setState('idle');
                    }
                }

                function showMode(selectedMode) {
                    if (busy) {
                        return;
                    }

                    stopListening();
                    stopAudio();

                    mode = selectedMode;

                    modeSelection.classList.add('hidden');

                    // Show chat messages only in Type mode.
                    messages.classList.toggle('hidden', mode === 'speak');

                    typePanel.classList.toggle('hidden', mode !== 'type');
                    speakPanel.classList.toggle('hidden', mode !== 'speak');

                    if (conversation.length === 0) {
                        addMessage(
                            'assistant',
                            @json('Hello, ' . auth()->user()->name . '! I\'m Aira. How can I help you today?')
                        );

                        conversation.push({
                            role: 'assistant',
                            content: @json('Hello, ' . auth()->user()->name . '! I\'m Aira. How can I help you today?'),
                        });
                    }

                    if (mode === 'type') {
                        setState('typing');
                        input.focus();
                    } else {
                        setState('idle');

                        if (!Recognition) {
                            voiceInstructions.textContent =
                                'Voice input is not supported in this browser. You can change to Type mode.';
                            micButton.disabled = true;
                        } else {
                            voiceInstructions.textContent =
                                'Press the microphone and speak when you are ready.';
                            micButton.disabled = false;
                        }
                    }

                    messages.scrollTop = messages.scrollHeight;
                }

                function speak(text) {
                    if (!('speechSynthesis' in window) || mode !== 'speak') {
                        setState(mode === 'type' ? 'typing' : 'idle');
                        return;
                    }

                    window.speechSynthesis.cancel();

                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'en-PH';
                    utterance.rate = 0.95;

                    const voices = window.speechSynthesis.getVoices();

                    const preferred = voices.find(voice =>
                        /^en-PH$/i.test(voice.lang) &&
                        /female|zira|samantha|aria|jenny/i.test(voice.name)
                    ) || voices.find(voice =>
                        /^en-PH$/i.test(voice.lang)
                    ) || voices.find(voice =>
                        /^en-/i.test(voice.lang) &&
                        /female|zira|samantha|aria|jenny/i.test(voice.name)
                    );

                    if (preferred) {
                        utterance.voice = preferred;
                    }

                    utterance.onstart = () => {
                        if (mode === 'speak') {
                            setState('talking');
                        }
                    };

                    utterance.onend = () => {
                        if (mode === 'speak' && !recording) {
                            setState('idle');
                        }
                    };

                    utterance.onerror = () => {
                        if (mode === 'speak' && !recording) {
                            setState('idle');
                        }
                    };

                    window.speechSynthesis.speak(utterance);
                }

                async function sendMessage(raw) {
                    const text = raw.trim();

                    if (!text || busy) {
                        return;
                    }

                    const generation = requestGeneration;

                    busy = true;
                    sendButton.disabled = true;
                    micButton.disabled = true;
                    input.disabled = true;

                    stopListening();
                    stopAudio();

                    if (mode === 'type') {
                        addMessage('user', text);
                    }

                    conversation.push({
                        role: 'user',
                        content: text
                    });

                    input.value = '';
                    setState('typing');
                    characterStatus.textContent = 'Aira is thinking...';

                    try {
                        const response = await fetch(
                            @json(route('user.ai.chat')), {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': @json(csrf_token()),
                                },
                                body: JSON.stringify({
                                    messages: conversation
                                        .filter(message => message.role === 'user' || message.role ===
                                            'assistant')
                                        .slice(-10),
                                }),
                            }
                        );

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Aira is temporarily unavailable. Please try again.');
                        }

                        if (typeof data.reply !== 'string' || !data.reply.trim()) {
                            throw new Error('Aira returned an empty response. Please try again.');
                        }

                        conversation.push({
                            role: 'assistant',
                            content: data.reply,
                        });

                        if (mode === 'type') {
                            addMessage('assistant', data.reply);
                        }
                        if (generation === requestGeneration && mode === 'speak') {
                            speak(data.reply);
                        } else {
                            setState(mode === 'type' ? 'typing' : 'idle');
                        }
                    } catch (error) {
                        // Remove the failed user message from local conversation history.
                        const lastMessage = conversation[conversation.length - 1];

                        if (lastMessage && lastMessage.role === 'user' && lastMessage.content === text) {
                            conversation.pop();
                        }
                        if (mode === 'type') {
                            addMessage(
                                'assistant',
                                error.message || 'Please try again in a moment.'
                            );
                        }

                        setState(mode === 'type' ? 'typing' : 'idle');
                    } finally {
                        busy = false;
                        sendButton.disabled = false;
                        micButton.disabled = false;
                        input.disabled = false;

                        if (mode === 'type') {
                            input.focus();
                        }
                    }
                }

                function startListening() {
                    if (!Recognition) {
                        voiceInstructions.textContent =
                            'Voice input is not supported in this browser. Please choose Type mode.';
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
                        micLabel.textContent = 'Listening...';
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
                        recording = false;

                        const errors = {
                            'not-allowed': 'Please allow microphone access in your browser.',
                            'no-speech': 'No speech detected. Please try again.',
                            'network': 'Voice recognition needs a network connection.',
                            'audio-capture': 'No microphone was detected. Check your microphone.',
                            'service-not-allowed': 'Your browser does not allow voice recognition.',
                        };

                        voiceInstructions.textContent =
                            errors[event.error] || 'Voice input failed. Please try again.';
                    };

                    currentRecognition.onend = () => {
                        recording = false;

                        if (recognition === currentRecognition) {
                            recognition = null;
                        }

                        micLabel.textContent = 'Start speaking';

                        const transcript = pendingTranscript;
                        pendingTranscript = '';

                        if (transcript && mode === 'speak') {
                            voiceInstructions.textContent = 'Sending your message to Aira...';
                            sendMessage(transcript);
                        } else if (mode === 'speak' && !busy) {
                            voiceInstructions.textContent =
                                'Press the microphone and speak when you are ready.';
                            setState('idle');
                        }
                    };

                    try {
                        currentRecognition.start();
                    } catch (error) {
                        recognition = null;
                        recording = false;
                        micLabel.textContent = 'Start speaking';
                        voiceInstructions.textContent =
                            'Unable to start the microphone. Please try again.';
                        setState('idle');
                    }
                }

                typeModeButton.addEventListener('click', () => showMode('type'));
                speakModeButton.addEventListener('click', () => showMode('speak'));

                changeTypeButton.addEventListener('click', showModeSelection);
                changeSpeakButton.addEventListener('click', showModeSelection);

                form.addEventListener('submit', event => {
                    event.preventDefault();

                    if (mode !== 'type') {
                        return;
                    }

                    sendMessage(input.value);
                });

                input.addEventListener('input', () => {
                    if (!busy && mode === 'type') {
                        setState(input.value.trim() ? 'typing' : 'idle');
                    }
                });

                input.addEventListener('keydown', event => {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        form.requestSubmit();
                    }
                });

                micButton.addEventListener('click', () => {
                    if (mode !== 'speak' || busy) {
                        return;
                    }

                    if (recording && recognition) {
                        recognition.stop();
                        return;
                    }

                    startListening();
                });

                stopButton.addEventListener('click', () => {
                    stopAudio();

                    if (recording && recognition) {
                        stopListening();
                    }

                    if (mode === 'speak') {
                        voiceInstructions.textContent =
                            'Speech stopped. Press the microphone whenever you are ready.';
                    }

                    setState(mode === 'type' ? 'typing' : 'idle');
                });

                // Initial screen: greet the customer and wait for a mode selection.
                setState('idle');
            }

            document.addEventListener('DOMContentLoaded', initializeAira);
            document.addEventListener('livewire:navigated', initializeAira);
        })();
    </script>
@endsection
