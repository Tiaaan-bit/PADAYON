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
            font-family: Arial, sans-serif;
            background: #F8F6F2;
            color: #2F2420;
        }

        header {
            background: #849753;
            color: white;
            padding: 20px;
        }

        main {
            max-width: 720px;
            margin: auto;
            padding: 24px 16px;
        }

        .panel {
            background: white;
            padding: 24px;
            border-radius: 18px;
            box-shadow: 0 4px 18px #00000010;
        }

        .mic {
            display: block;
            width: 140px;
            height: 140px;
            border: 0;
            border-radius: 50%;
            margin: 24px auto;
            background: #849753;
            color: white;
            font-size: 48px;
            cursor: pointer;
        }

        button:focus-visible,
        input:focus-visible {
            outline: 4px solid #6F4E37;
            outline-offset: 4px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        input {
            flex: 1;
            min-width: 180px;
            padding: 14px;
            border: 2px solid #849753;
            border-radius: 10px;
            font-size: 16px;
        }

        .action {
            padding: 14px 18px;
            border: 0;
            border-radius: 10px;
            background: #6F4E37;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        #conversation {
            margin-top: 24px;
        }

        .message {
            padding: 14px;
            margin: 12px 0;
            border-radius: 12px;
            overflow-wrap: anywhere;
        }

        .user {
            background: #E8EFDF;
        }

        .assistant {
            background: #F4EDDB;
        }

        #status {
            text-align: center;
            min-height: 24px;
        }

        .logout-button {
            padding: 12px 20px;
            border: 2px solid white;
            border-radius: 10px;
            background: #6F4E37;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .logout-button:hover {
            background: #513825;
        }
    </style>
</head>

<body>
    <header>
        <h1>Padayon AI Assistant</h1>
        <p>
            Hello, {{ $therapist->name }}.
            You can speak or type your question.
        </p>
        <form action="{{ route('logout') }}" method="POST" style="margin-top: 16px;">
            @csrf
            <button type="submit" class="action" style="background: #6F4E37;">
                Log Out
            </button>
        </form>
    </header>

    <main>
        <section class="panel" aria-labelledby="assistant-heading">
            <h2 id="assistant-heading">How can I help you today?</h2>

            <p>
                Press the microphone and speak, or type your question below.
            </p>

            <button type="button" id="micButton" class="mic" aria-label="Start speaking">
                🎙
            </button>

            <p id="status" role="status" aria-live="polite">
                Ready to assist.
            </p>

            <form id="chatForm">
                <div class="actions">
                    <input type="text" id="messageInput" maxlength="2000" placeholder="Type your question..."
                        aria-label="Your question" required>

                    <button class="action" type="submit" id="sendButton">
                        Send
                    </button>
                </div>
            </form>

            <div class="actions" style="margin-top: 16px;">
                <button type="button" class="action" id="stopButton">
                    Stop speaking
                </button>

            </div>

            <div id="conversation" aria-live="polite"></div>
        </section>
    </main>

    <script>
        (() => {
            const chatUrl = @json(route('therapist.ai.chat'));
            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            ).content;

            const form = document.getElementById('chatForm');
            const input = document.getElementById('messageInput');
            const sendButton = document.getElementById('sendButton');
            const micButton = document.getElementById('micButton');
            const status = document.getElementById('status');
            const conversation = document.getElementById('conversation');

            let messages = [];
            let busy = false;

            const SpeechRecognition =
                window.SpeechRecognition ||
                window.webkitSpeechRecognition;

            let recognition = null;

            function setStatus(message) {
                status.textContent = message;
            }

            function addMessage(role, content) {
                const item = document.createElement('div');
                item.className = `message ${role}`;

                const label = document.createElement('strong');
                label.textContent = role === 'user' ?
                    'You: ' :
                    'Padayon AI: ';

                const text = document.createElement('span');
                text.textContent = content;

                item.append(label, text);
                conversation.appendChild(item);

                item.scrollIntoView({
                    behavior: 'smooth',
                    block: 'end'
                });
            }

            function speak(text) {
                if (!('speechSynthesis' in window)) {
                    setStatus('Your browser cannot read responses aloud.');
                    return;
                }

                window.speechSynthesis.cancel();

                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'en-PH';
                utterance.rate = 0.95;

                utterance.onstart = () => setStatus('Padayon AI is speaking.');
                utterance.onend = () => setStatus('Ready to assist.');
                utterance.onerror = () => setStatus('Response displayed.');

                window.speechSynthesis.speak(utterance);
            }

            async function sendMessage(text) {
                if (busy || !text.trim()) return;

                busy = true;
                sendButton.disabled = true;

                messages.push({
                    role: 'user',
                    content: text.trim()
                });

                addMessage('user', text.trim());
                input.value = '';
                setStatus('Thinking. Please wait.');

                try {
                    const response = await fetch(chatUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            messages: messages.slice(-10)
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message || 'Unable to contact the assistant.'
                        );
                    }

                    messages.push({
                        role: 'assistant',
                        content: data.reply
                    });

                    // Keep only the most recent ten messages.
                    messages = messages.slice(-10);

                    addMessage('assistant', data.reply);
                    speak(data.reply);

                } catch (error) {
                    setStatus(error.message);
                } finally {
                    busy = false;
                    sendButton.disabled = false;
                    input.focus();
                }
            }

            form.addEventListener('submit', event => {
                event.preventDefault();
                sendMessage(input.value);
            });

            micButton.addEventListener('click', () => {
                if (!SpeechRecognition) {
                    setStatus(
                        'Voice recognition is unavailable. Please type your question.'
                    );
                    input.focus();
                    return;
                }

                if (recognition) {
                    recognition.stop();
                }

                recognition = new SpeechRecognition();
                recognition.lang = 'en-PH';
                recognition.interimResults = false;
                recognition.maxAlternatives = 1;

                recognition.onstart = () => {
                    setStatus('Listening. Please speak now.');
                    micButton.setAttribute('aria-label', 'Listening');
                };

                recognition.onresult = event => {
                    const transcript =
                        event.results[0][0].transcript;

                    input.value = transcript;
                    sendMessage(transcript);
                };

                recognition.onerror = event => {
                    setStatus(
                        event.error === 'not-allowed' ?
                        'Microphone permission was denied.' :
                        'Voice recognition failed. Please type instead.'
                    );
                };

                recognition.onend = () => {
                    micButton.setAttribute('aria-label', 'Start speaking');
                };

                recognition.start();
            });

            document.getElementById('stopButton').addEventListener('click', () => {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                }

                if (recognition) {
                    recognition.stop();
                }

                setStatus('Speech stopped.');
            });
        })();
    </script>
</body>

</html>
