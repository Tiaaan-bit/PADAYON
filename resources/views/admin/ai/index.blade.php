@extends('layouts.admin')

@section('title', 'Admin AI Assistant | Padayon Massage Center')

@section('content')
<div class="mx-auto max-w-4xl px-3 py-4 sm:px-6 sm:py-6">
    <section class="overflow-hidden rounded-2xl border border-[#e8e0d5] bg-white shadow-sm">

        {{-- HEADER --}}
        <header class="flex items-center gap-3 bg-[#849753] px-4 py-4 text-white sm:px-6">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/20">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3a4 4 0 0 0-4 4v3a4 4 0 0 0 8 0V7a4 4 0 0 0-4-4Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 10v1a7 7 0 0 0 14 0v-1M12 18v3m-4 0h8" />
                </svg>
            </div>

            <div class="min-w-0">
                <h1 class="text-lg font-bold">Admin AI Assistant</h1>
                <p id="admin-ai-status" role="status" aria-live="polite"
                    class="text-sm text-white/90">
                    Your Padayon business assistant is ready to help.
                </p>
            </div>
        </header>

        {{-- AI CHARACTER --}}
        <div class="border-b border-[#eee5d9] bg-[#F8F6F2] px-4 py-5 text-center">
            <div class="mx-auto flex h-44 w-44 items-center justify-center sm:h-52 sm:w-52">
                <img id="admin-ai-character"
                    src="{{ asset('images/aira/aira-idle.gif') }}"
                    data-idle="{{ asset('images/aira/aira-idle.gif') }}"
                    data-typing="{{ asset('images/aira/aira-typing.gif') }}"
                    data-talking="{{ asset('images/aira/aira-talking.gif') }}"
                    alt="Padayon Admin AI Assistant"
                    class="h-full w-full object-contain">
            </div>
        </div>

        {{-- WELCOME --}}
        <div id="admin-ai-welcome" class="bg-white px-4 py-6 sm:px-6">
            <h2 class="text-center text-lg font-semibold text-gray-800">
                Hello, {{ auth()->user()->name }}!
            </h2>

            <p class="mt-2 text-center text-sm text-gray-500">
                What would you like to know about your business today?
            </p>

            <div class="mx-auto mt-5 grid max-w-xl grid-cols-1 gap-3 sm:grid-cols-2">
                <button type="button" data-question="Give me a summary of this month's business performance."
                    class="rounded-xl border border-[#849753]/50 px-4 py-3 text-left text-sm font-medium text-gray-700 transition hover:bg-[#f1f4e9]">
                    <span class="mb-1 block font-semibold text-[#52652b]">Business Summary</span>
                    Review this month's performance
                </button>

                <button type="button" data-question="Analyze our recorded revenue for this month."
                    class="rounded-xl border border-[#849753]/50 px-4 py-3 text-left text-sm font-medium text-gray-700 transition hover:bg-[#f1f4e9]">
                    <span class="mb-1 block font-semibold text-[#52652b]">Revenue Analysis</span>
                    Review recorded revenue
                </button>

                <button type="button" data-question="Which massage services are the most popular this month?"
                    class="rounded-xl border border-[#849753]/50 px-4 py-3 text-left text-sm font-medium text-gray-700 transition hover:bg-[#f1f4e9]">
                    <span class="mb-1 block font-semibold text-[#52652b]">Popular Services</span>
                    Discover popular services
                </button>

                <button type="button" data-question="Analyze the appointment workload of our therapists this month."
                    class="rounded-xl border border-[#849753]/50 px-4 py-3 text-left text-sm font-medium text-gray-700 transition hover:bg-[#f1f4e9]">
                    <span class="mb-1 block font-semibold text-[#52652b]">Therapist Workload</span>
                    Review appointment distribution
                </button>
            </div>

            <div class="mt-5 text-center">
                <button id="admin-ai-start-chat" type="button"
                    class="rounded-xl bg-[#6F4E37] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#59402e]">
                    Start a Conversation
                </button>
            </div>
        </div>

        {{-- MESSAGES --}}
        <div id="admin-ai-messages" role="log" aria-live="polite"
            aria-label="Conversation with Admin AI"
            class="hidden h-72 max-h-72 space-y-4 overflow-y-auto overscroll-contain bg-white p-4 sm:h-80 sm:max-h-80 sm:p-6">
        </div>

        {{-- MESSAGE INPUT --}}
        <div id="admin-ai-chat-panel" class="hidden border-t border-[#eee5d9] bg-white">
            <form id="admin-ai-form" class="flex items-end gap-2 p-4 sm:px-5 sm:pb-5">
                <label for="admin-ai-input" class="sr-only">Message Admin AI</label>

                <textarea id="admin-ai-input" rows="1" maxlength="2000"
                    placeholder="Ask about revenue, appointments, services..."
                    class="min-h-12 max-h-32 flex-1 resize-none overflow-y-auto rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20"></textarea>

                <button id="admin-ai-send" type="submit"
                    class="min-h-12 rounded-xl bg-[#6F4E37] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#59402e] disabled:cursor-not-allowed disabled:opacity-50">
                    Send
                </button>
            </form>

            <div class="flex flex-wrap items-center justify-between gap-3 px-4 pb-4 sm:px-5">
                <p class="text-xs text-gray-500">Ask questions about your business analytics.</p>

                <button id="admin-ai-clear" type="button"
                    class="text-sm font-medium text-[#6F4E37] underline hover:text-[#59402e]">
                    New conversation
                </button>
            </div>
        </div>

        <p class="border-t border-[#eee5d9] px-4 py-4 text-center text-xs text-gray-500">
            AI-generated insights may contain mistakes. Verify important business figures against your reports.
        </p>
    </section>
</div>

<script>
(() => {
    function initializeAdminAI() {
        const messages = document.getElementById('admin-ai-messages');

        if (!messages || messages.dataset.initialized === 'true') {
            return;
        }

        messages.dataset.initialized = 'true';

        const welcome = document.getElementById('admin-ai-welcome');
        const panel = document.getElementById('admin-ai-chat-panel');
        const form = document.getElementById('admin-ai-form');
        const input = document.getElementById('admin-ai-input');
        const sendButton = document.getElementById('admin-ai-send');
        const startButton = document.getElementById('admin-ai-start-chat');
        const clearButton = document.getElementById('admin-ai-clear');
        const character = document.getElementById('admin-ai-character');
        const status = document.getElementById('admin-ai-status');

        const conversation = [];
        let busy = false;

        const animations = {
            idle: character.dataset.idle,
            typing: character.dataset.typing,
            talking: character.dataset.talking
        };

        function setState(state, message) {
            character.src = animations[state] || animations.idle;

            const labels = {
                idle: 'Your Padayon business assistant is ready to help.',
                typing: 'Analyzing your question...',
                talking: 'Your answer is ready.'
            };

            status.textContent = message || labels[state] || labels.idle;
        }

        function addMessage(role, text) {
            const row = document.createElement('div');
            row.className = role === 'user'
                ? 'flex justify-end'
                : 'flex justify-start';

            const bubble = document.createElement('div');
            bubble.className = role === 'user'
                ? 'max-w-[88%] whitespace-pre-wrap break-words rounded-2xl rounded-br-sm bg-[#849753] px-4 py-3 text-sm leading-6 text-white'
                : 'max-w-[88%] whitespace-pre-wrap break-words rounded-2xl rounded-bl-sm border border-[#e8e0d5] bg-[#F8F6F2] px-4 py-3 text-sm leading-6 text-gray-800';

            // Render plain text only; never inject AI output as HTML.
            bubble.textContent = text;

            row.appendChild(bubble);
            messages.appendChild(row);
            messages.scrollTop = messages.scrollHeight;

            return row;
        }

        function beginConversation() {
            welcome.classList.add('hidden');
            messages.classList.remove('hidden');
            panel.classList.remove('hidden');

            if (conversation.length === 0) {
                const greeting = 'Hello! I can help you analyze Padayon’s revenue, appointments, popular services, therapist workload, and business performance. What would you like to know?';

                conversation.push({
                    role: 'assistant',
                    content: greeting
                });

                addMessage('assistant', greeting);
            }

            input.focus();
        }

        async function sendMessage(raw) {
            const text = raw.trim();

            if (!text || busy) {
                return;
            }

            beginConversation();

            busy = true;
            sendButton.disabled = true;
            input.disabled = true;

            input.value = '';
            conversation.push({ role: 'user', content: text });
            addMessage('user', text);

            setState('typing', 'Analyzing your question...');

            const loading = addMessage('assistant', 'Thinking...');

            try {
                const response = await fetch(
                    @json(route('admin.ai.chat')),
                    {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token())
                        },
                        body: JSON.stringify({
                            messages: conversation.slice(-10)
                        })
                    }
                );

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || 'The assistant is temporarily unavailable. Please try again.'
                    );
                }

                if (typeof data.reply !== 'string' || !data.reply.trim()) {
                    throw new Error('The assistant returned an empty response. Please try again.');
                }

                loading.querySelector('div').textContent = data.reply;

                conversation.push({
                    role: 'assistant',
                    content: data.reply
                });

                setState('talking', 'Your answer is ready.');
            } catch (error) {
                loading.querySelector('div').textContent =
                    error.message || 'Something went wrong. Please try again.';

                // Keep the conversation visible, but don't include a failed request
                // in the history sent to the AI on the next attempt.
                const last = conversation[conversation.length - 1];

                if (last && last.role === 'user' && last.content === text) {
                    conversation.pop();
                }

                setState('idle', 'Unable to complete your request.');
            } finally {
                busy = false;
                sendButton.disabled = false;
                input.disabled = false;
                input.focus();
                messages.scrollTop = messages.scrollHeight;
            }
        }

        startButton.addEventListener('click', beginConversation);

        document.querySelectorAll('[data-question]').forEach(button => {
            button.addEventListener('click', () => {
                sendMessage(button.dataset.question || '');
            });
        });

        form.addEventListener('submit', event => {
            event.preventDefault();
            sendMessage(input.value);
        });

        input.addEventListener('keydown', event => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        clearButton.addEventListener('click', () => {
            if (busy) return;

            conversation.length = 0;
            messages.replaceChildren();

            welcome.classList.remove('hidden');
            messages.classList.add('hidden');
            panel.classList.add('hidden');

            setState('idle');
        });

        setState('idle');
    }

    document.addEventListener('DOMContentLoaded', initializeAdminAI);
    document.addEventListener('livewire:navigated', initializeAdminAI);
})();
</script>
@endsection