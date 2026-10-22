<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Padayon AI Assistant</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #F8F6F2;
            color: #2F2420;
        }

        main {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .panel {
            width: 100%;
            max-width: 520px;
            text-align: center;
            background: white;
            padding: 32px 24px;
            border-radius: 24px;
            box-shadow: 0 4px 18px #00000010;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 32px;
        }

        .subtitle {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .mic {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            width: min(76vw, 300px);
            height: min(76vw, 300px);
            min-width: 220px;
            min-height: 220px;
            border: 0;
            border-radius: 50%;
            margin: 24px auto;
            background: #849753;
            color: white;
            font-size: 26px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 8px 0 #61713A;
            transition: transform .15s, background .15s;
            touch-action: manipulation;
        }

        .mic:active {
            transform: translateY(5px);
            box-shadow: 0 3px 0 #61713A;
        }

        .mic:focus-visible,
        .logout-button:focus-visible {
            outline: 5px solid #6F4E37;
            outline-offset: 6px;
        }

        .mic:disabled {
            opacity: .8;
            cursor: wait;
        }

        .mic-icon {
            font-size: 64px;
            line-height: 1;
        }

        #status {
            min-height: 54px;
            margin: 28px 0 12px;
            padding: 16px;
            border-radius: 14px;
            background: #F4EDDB;
            font-size: 18px;
            line-height: 1.5;
        }

        .logout-form {
            margin-top: 24px;
        }

        .logout-button {
            padding: 14px 24px;
            border: 0;
            border-radius: 12px;
            background: #6F4E37;
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Keyboard-only helper; screen readers can still announce it. */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                transition: none !important;
            }
        }
    </style>
</head>

<body>
    <main>
        <section class="panel" aria-labelledby="assistant-heading">


            <button type="button" id="micButton" class="mic" aria-label="Talk to Aira, your Padayon AI assistant"
                aria-describedby="status">
                <span class="mic-icon" aria-hidden="true">🎙</span>
                <span id="buttonLabel">TAP TO TALK</span>
            </button>

            <p id="status" role="status" aria-live="polite" aria-atomic="true">
                Ready to assist. Tap the big button to begin.
            </p>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-button">
                    Log Out
                </button>
            </form>

            <div id="conversation" class="sr-only" aria-live="off"></div>
        </section>
    </main>

    
<script>
    (() => {
        const chatUrl = @json(route('therapist.ai.chat'));
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
        const therapistName = @json(auth('therapist')->user()->name ?? 'there');

        const micButton = document.getElementById('micButton');
        const buttonLabel = document.getElementById('buttonLabel');
        const status = document.getElementById('status');

        const SpeechRecognition =
            window.SpeechRecognition ||
            window.webkitSpeechRecognition;

        let recognition = null;
        let messages = [];
        let busy = false;
        let listening = false;
        let greeted = false;
        let silenceTimer = null;
        let silenceReminderActive = false;
        let preferredVoice = null;
        let speechRequestId = 0;

        // --------------------------------------------------
        // STATUS AND BUTTON
        // --------------------------------------------------

        function setStatus(message) {
            status.textContent = message;
        }

        function setButton(label, icon = '🎙') {
            buttonLabel.textContent = label;
            micButton.querySelector('.mic-icon').textContent = icon;
        }

        // --------------------------------------------------
        // FEMALE VOICE SELECTION
        // --------------------------------------------------

        function selectFemaleVoice() {
            if (!('speechSynthesis' in window)) {
                return null;
            }

            const voices = window.speechSynthesis.getVoices();

            if (!voices.length) {
                return null;
            }

            const englishVoices = voices.filter(voice =>
                /^en(?:-|$)/i.test(voice.lang)
            );

            // Prefer known feminine-sounding English voices.
            // Voice names differ between Windows, Android, and iOS.
            const femaleVoicePattern =
                /female|samantha|karen|moira|tessa|victoria|zira|aria|jenny|ava|emma|sara|susan|hazel|serena|fiona|google uk english female|microsoft.*(zira|aria|jenny|ava|emma)/i;

            preferredVoice =
                englishVoices.find(voice =>
                    femaleVoicePattern.test(voice.name)
                ) ||
                null;

            // If a matching female voice is unavailable, prefer
            // an English Philippines voice before other English voices.
            if (!preferredVoice) {
                preferredVoice =
                    englishVoices.find(voice =>
                        /^en-PH$/i.test(voice.lang)
                    ) ||
                    englishVoices.find(voice =>
                        /^en-US$/i.test(voice.lang)
                    ) ||
                    englishVoices.find(voice =>
                        /^en-GB$/i.test(voice.lang)
                    ) ||
                    englishVoices[0] ||
                    null;
            }

            return preferredVoice;
        }

        if ('speechSynthesis' in window) {
            selectFemaleVoice();

            // Some browsers load voices after the page loads.
            window.speechSynthesis.addEventListener(
                'voiceschanged',
                () => {
                    selectFemaleVoice();
                }
            );
        }

        // --------------------------------------------------
        // SPEECH SYNTHESIS
        // --------------------------------------------------

        function stopSpeaking() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
        }

        function speak(text) {
            return new Promise(resolve => {
                if (!('speechSynthesis' in window)) {
                    setStatus(text);
                    resolve();
                    return;
                }

                const currentRequest = ++speechRequestId;

                stopSpeaking();

                // Refresh the voice selection if voices are available now.
                if (!preferredVoice) {
                    selectFemaleVoice();
                }

                const utterance = new SpeechSynthesisUtterance(text);

                if (preferredVoice) {
                    utterance.voice = preferredVoice;
                    utterance.lang = preferredVoice.lang;
                } else {
                    utterance.lang = 'en-PH';
                }

                utterance.rate = 1.0;
                utterance.pitch = 1.1;
                utterance.volume = 1;

                let finished = false;

                function finish() {
                    if (finished) return;
                    finished = true;

                    // Do not let an older utterance reset the
                    // interface while a newer utterance is speaking.
                    if (currentRequest === speechRequestId) {
                        setButton('TAP TO TALK');
                    }

                    resolve();
                }

                utterance.onstart = () => {
                    if (currentRequest !== speechRequestId) return;

                    setStatus(text);
                    setButton('AIRA IS SPEAKING', '🔊');
                };

                utterance.onend = finish;
                utterance.onerror = finish;

                window.speechSynthesis.speak(utterance);
            });
        }

        // --------------------------------------------------
        // SPEECH RECOGNITION
        // --------------------------------------------------

        function createRecognition() {
            if (!SpeechRecognition) {
                return null;
            }

            const instance = new SpeechRecognition();

            instance.lang = 'en-PH';
            instance.continuous = false;
            instance.interimResults = false;
            instance.maxAlternatives = 1;

            instance.onstart = () => {
                listening = true;
                silenceReminderActive = false;

                setButton('LISTENING...', '👂');
                setStatus('Listening. Please speak now.');

                clearTimeout(silenceTimer);

                silenceTimer = setTimeout(async () => {
                    if (!listening || busy) return;

                    silenceReminderActive = true;
                    listening = false;

                    clearTimeout(silenceTimer);

                    try {
                        recognition?.stop();
                    } catch (error) {
                        // Recognition may already have stopped.
                    }

                    await speak(
                        'I cannot hear you speaking. Tap the button when you are ready to try again.'
                    );

                    setButton('TAP TO TALK');
                    setStatus(
                        'Ready for your next question. Tap the big button to talk.'
                    );
                }, 7000);
            };

            instance.onresult = event => {
                clearTimeout(silenceTimer);

                const transcript =
                    event.results?.[0]?.[0]?.transcript?.trim();

                listening = false;

                if (!transcript) {
                    setStatus(
                        'I did not catch that. Tap the button and try again.'
                    );
                    setButton('TAP TO TALK');
                    return;
                }

                sendMessage(transcript);
            };

            instance.onerror = async event => {
                clearTimeout(silenceTimer);
                listening = false;

                setButton('TAP TO TALK');

                // Prevent a duplicate reminder after the silence timer.
                if (silenceReminderActive) {
                    return;
                }

                const errors = {
                    'no-speech':
                        'I did not hear you. Please tap the button and try again.',
                    'not-allowed':
                        'Microphone permission is blocked. Please enable microphone access in your browser.',
                    'service-not-allowed':
                        'Speech recognition is blocked by the browser.',
                    'audio-capture':
                        'I cannot find a working microphone.',
                    'network':
                        'Voice recognition needs a working network connection.'
                };

                const message = errors[event.error] ||
                    'I could not hear you clearly. Please try again.';

                setStatus(message);
                await speak(message);
            };

            instance.onend = () => {
                listening = false;
                clearTimeout(silenceTimer);

                if (!busy && !window.speechSynthesis?.speaking) {
                    setButton('TAP TO TALK');
                }
            };

            return instance;
        }

        async function startListening() {
            if (busy || listening) return;

            if (!SpeechRecognition) {
                await speak(
                    'Voice recognition is not available on this browser. Please try a supported browser with microphone access enabled.'
                );
                return;
            }

            stopSpeaking();

            try {
                recognition = createRecognition();

                if (!recognition) {
                    throw new Error('Speech recognition is unavailable.');
                }

                recognition.start();
            } catch (error) {
                setStatus(
                    'I could not start the microphone. Please wait a moment and tap again.'
                );
                setButton('TAP TO TALK');
            }
        }

        // --------------------------------------------------
        // SEND QUESTION TO LARAVEL / GROQ
        // --------------------------------------------------

        async function sendMessage(text) {
            if (busy || !text.trim()) return;

            busy = true;
            micButton.disabled = true;

            setButton('PLEASE WAIT', '⏳');
            setStatus('Finding your answer...');

            messages.push({
                role: 'user',
                content: text.trim()
            });

            messages = messages.slice(-10);

            try {
                const response = await fetch(chatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        messages: messages
                    })
                });

                let data;

                try {
                    data = await response.json();
                } catch (error) {
                    throw new Error('The server returned an invalid response.');
                }

                if (response.status === 401) {
                    await speak(
                        'Your session may have expired. Please log in again.'
                    );
                    return;
                }

                if (!response.ok) {
                    throw new Error(
                        data.message ||
                        'The assistant is temporarily unavailable.'
                    );
                }

                if (
                    typeof data.reply !== 'string' ||
                    !data.reply.trim()
                ) {
                    throw new Error(
                        'I received an empty answer. Please try again.'
                    );
                }

                messages.push({
                    role: 'assistant',
                    content: data.reply.trim()
                });

                messages = messages.slice(-10);

                await speak(data.reply.trim());

                setStatus(
                    'Ready for your next question. Tap the big button to talk again.'
                );
            } catch (error) {
                console.error('Aira assistant error:', error);

                await speak(
                    'Sorry, I could not get an answer just now. Please tap the button to try again.'
                );

                setStatus(
                    'Ready to try again. Tap the big button when you are ready.'
                );
            } finally {
                busy = false;
                micButton.disabled = false;
                setButton('TAP TO TALK');
            }
        }

        // --------------------------------------------------
        // MAIN BUTTON
        // --------------------------------------------------

        micButton.addEventListener('click', async () => {
            if (busy || listening) return;

            // Warm up voice selection on the first user interaction.
            if (!preferredVoice) {
                selectFemaleVoice();
            }

            // Greet the therapist once per page session.
            if (!greeted) {
                greeted = true;

                await speak(
                    `Hello, ${therapistName}. I am Aira, your AI assistant.`
                );
            }

            // Keep this short to reduce the time before listening.
            await speak('I am listening.');

            await startListening();
        });

        // --------------------------------------------------
        // INITIAL PAGE STATE
        // --------------------------------------------------

        if (!SpeechRecognition) {
            setStatus(
                'Tap the button to begin. Your browser may need help enabling voice recognition.'
            );
        } else {
            setStatus(
                'Ready to assist. Tap the big button to begin.'
            );
        }
    })();
</script>

</body>

</html>
