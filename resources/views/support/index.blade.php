@extends('layouts.app')

@section('title', 'Help & Support')
@section('page-heading', 'Help & Support')

@push('styles')
<style>
    .support-page { max-width: 1100px; margin: 0 auto; }
    .support-hero { padding: 1.75rem; margin-bottom: 1.25rem; color: #fff; background: linear-gradient(135deg, #0f766e, #16233a); border-radius: 16px; }
    .support-hero h2 { margin: 0 0 .35rem; font-size: 1.45rem; font-weight: 700; }
    .support-hero p { margin: 0; color: rgba(255, 255, 255, .8); }
    .support-card { background: #fff; border: 1px solid var(--pm-border); border-radius: 14px; box-shadow: var(--pm-shadow); }
    .support-card-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--pm-border); }
    .support-card-header h5 { margin: 0; color: var(--pm-navy); font-size: .95rem; font-weight: 700; }
    .support-card-body { padding: 1.25rem; }
    .support-tip { display: flex; gap: .75rem; padding: .85rem 1rem; background: rgba(13, 148, 136, .08); border: 1px solid rgba(13, 148, 136, .16); border-radius: 10px; color: var(--pm-text); font-size: .82rem; }
    .support-tip i { color: var(--pm-teal); font-size: 1rem; }
    .support-ticket { padding: 1rem 0; border-bottom: 1px solid var(--pm-border); }
    .support-ticket:last-child { padding-bottom: 0; border-bottom: 0; }
    .support-ticket-title { color: var(--pm-navy); font-weight: 700; }
    .support-ticket-meta { margin-top: .2rem; color: var(--pm-muted); font-size: .78rem; }
    .support-status { display: inline-flex; padding: .22rem .55rem; border-radius: 999px; background: rgba(13, 148, 136, .11); color: var(--pm-teal-dark); font-size: .68rem; font-weight: 700; text-transform: capitalize; }
    .ai-chat { display: flex; flex-direction: column; gap: .7rem; min-height: 190px; max-height: 330px; padding: 1rem; overflow-y: auto; background: #F8FAFC; border: 1px solid var(--pm-border); border-radius: 12px; }
    .ai-message { max-width: 88%; padding: .65rem .8rem; border-radius: 11px; font-size: .83rem; line-height: 1.5; white-space: pre-wrap; }
    .ai-message.assistant { align-self: flex-start; background: #fff; border: 1px solid var(--pm-border); color: var(--pm-text); }
    .ai-message.user { align-self: flex-end; background: var(--pm-teal); color: #fff; }
    .ai-note { margin-top: .7rem; color: var(--pm-muted); font-size: .72rem; }
    html[data-theme="dark"] .support-card { background: linear-gradient(145deg, #172338 0%, #141E2E 100%); border-color: #2C3D58; }
    html[data-theme="dark"] .support-card-header, html[data-theme="dark"] .support-ticket { border-color: #2C3D58; }
    html[data-theme="dark"] .support-tip { background: rgba(45, 212, 191, .09); border-color: rgba(45, 212, 191, .2); }
    html[data-theme="dark"] .support-status { background: rgba(45, 212, 191, .14); color: #5EEAD4; }
    html[data-theme="dark"] .ai-chat { background: #0F1A2B; border-color: #2C3D58; }
    html[data-theme="dark"] .ai-message.assistant { background: #1B2A42; border-color: #2C3D58; }
</style>
@endpush

@section('content')
<div class="support-page">
    <div class="support-hero">
        <h2>How can we help?</h2>
        <p>Send a request to the support team and keep track of your submitted requests.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4" role="alert">{{ session('success') }}</div>
    @endif

    <section class="support-card mb-4">
        <div class="support-card-header d-flex align-items-center justify-content-between gap-3">
            <h5><i class="bi bi-stars me-2"></i>AI Help</h5>
            @if($aiEnabled)<span class="support-status">Available</span>@endif
        </div>
        <div class="support-card-body">
            @if($aiEnabled)
                <div id="aiChat" class="ai-chat" aria-live="polite">
                    <div class="ai-message assistant">Hi! I can help you find your way around the system or troubleshoot common tasks. What do you need help with?</div>
                </div>
                <form id="aiHelpForm" class="mt-3">
                    <div class="input-group">
                        <input id="aiMessage" class="form-control" maxlength="2000" placeholder="Ask about properties, tenants, invoices, reports, or settings…" required>
                        <button id="aiSendButton" class="btn pm-btn-primary" type="submit"><i class="bi bi-send"></i> Ask AI</button>
                    </div>
                </form>
                <p class="ai-note">Do not share passwords, payment details, or other sensitive information with AI Help.</p>
            @else
                <div class="support-tip"><i class="bi bi-info-circle"></i><span>AI Help needs an OpenAI API key before it can answer questions. You can still submit a support request below.</span></div>
            @endif
        </div>
    </section>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="support-card">
                <div class="support-card-header"><h5>Submit a support request</h5></div>
                <div class="support-card-body">
                    <div class="support-tip mb-4"><i class="bi bi-lightbulb"></i><span>Include as much detail as possible so the team can help you quickly.</span></div>
                    <form method="POST" action="{{ route('support.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="general" @selected(old('category') === 'general')>General question</option>
                                <option value="account" @selected(old('category') === 'account')>Account access</option>
                                <option value="technical" @selected(old('category') === 'technical')>Technical issue</option>
                                <option value="billing" @selected(old('category') === 'billing')>Billing</option>
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input id="subject" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" maxlength="150" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label">How can we help?</label>
                            <textarea id="message" name="message" rows="6" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn pm-btn-primary" type="submit"><i class="bi bi-send me-1"></i> Send request</button>
                    </form>
                </div>
            </section>
        </div>
        <div class="col-lg-5">
            <section class="support-card">
                <div class="support-card-header"><h5>Your support requests</h5></div>
                <div class="support-card-body">
                    @forelse($requests as $supportRequest)
                        <div class="support-ticket">
                            <div class="d-flex justify-content-between gap-3"><span class="support-ticket-title">{{ $supportRequest->subject }}</span><span class="support-status">{{ $supportRequest->status }}</span></div>
                            <div class="support-ticket-meta">{{ ucfirst($supportRequest->category) }} · {{ $supportRequest->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">You have not submitted any support requests yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@if($aiEnabled)
@push('scripts')
<script>
    (() => {
        const form = document.getElementById('aiHelpForm');
        const input = document.getElementById('aiMessage');
        const chat = document.getElementById('aiChat');
        const button = document.getElementById('aiSendButton');
        const history = [];

        const appendMessage = (content, role) => {
            const message = document.createElement('div');
            message.className = `ai-message ${role}`;
            message.textContent = content;
            chat.appendChild(message);
            chat.scrollTop = chat.scrollHeight;
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            appendMessage(message, 'user');
            history.push({ role: 'user', content: message });
            input.value = '';
            input.disabled = true;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const response = await fetch('{{ route('support.ai') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ message, history: history.slice(-6, -1) }),
                });
                const payload = await response.json();
                const answer = payload.answer || payload.message || 'AI Help is temporarily unavailable. Please submit a support request instead.';
                appendMessage(answer, 'assistant');
                history.push({ role: 'assistant', content: answer });
            } catch (_) {
                appendMessage('AI Help is temporarily unavailable. Please submit a support request instead.', 'assistant');
            } finally {
                input.disabled = false;
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-send"></i> Ask AI';
                input.focus();
            }
        });
    })();
</script>
@endpush
@endif
