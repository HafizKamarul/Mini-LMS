<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1 class="h4 mb-0">{{ $exam->title }}</h1>
            <div class="badge text-bg-danger fs-6">Time Left: <span id="countdown">--:--</span></div>
        </div>
    </x-slot>

    <div class="row g-3" id="exam-area">
        <div class="col-lg-3">
            <div class="card"><div class="card-body"><h3 class="h6">Questions</h3><div id="question-nav" class="d-grid gap-2"></div><div class="small text-muted mt-3">Green = answered, Yellow = flagged</div></div></div>
        </div>
        <div class="col-lg-9">
            <div class="card"><div class="card-body">
                <p class="small text-muted mb-1" id="question-meta"></p>
                <h3 class="h5" id="question-text"></h3>
                <div id="question-body" class="mt-4"></div>
                <div class="mt-3 form-check"><input id="flag-question" type="checkbox" class="form-check-input"><label for="flag-question" class="form-check-label">Flag this question</label></div>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button id="prev-btn" type="button" class="btn btn-outline-secondary">Previous</button>
                    <button id="save-btn" type="button" class="btn btn-outline-primary">Save Answer</button>
                    <button id="next-btn" type="button" class="btn btn-outline-secondary">Next</button>
                    <form id="submit-form" method="POST" action="{{ route('student.exams.submit', $exam) }}" class="ms-auto">@csrf<button id="submit-btn" type="submit" class="btn btn-success" onclick="return confirm('Submit exam now?')">Submit Exam</button></form>
                </div>
                <p id="save-status" class="small text-muted mt-3 mb-0"></p>
            </div></div>
        </div>
    </div>

    <script>
        const questions = @json($questions);
        const deadlineTimestamp = new Date(@json($deadline->toIso8601String())).getTime();
        const saveEndpointBase = @json(url('/student/exams/'.$exam->id.'/questions'));
        let currentIndex = 0;
        const questionNav = document.getElementById('question-nav');
        const questionMeta = document.getElementById('question-meta');
        const questionText = document.getElementById('question-text');
        const questionBody = document.getElementById('question-body');
        const flagQuestion = document.getElementById('flag-question');
        const saveStatus = document.getElementById('save-status');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const saveBtn = document.getElementById('save-btn');
        const countdown = document.getElementById('countdown');
        const submitForm = document.getElementById('submit-form');
        const submitBtn = document.getElementById('submit-btn');
        const examArea = document.getElementById('exam-area');
        let isSubmitting = false;
        let timerInterval = null;
        let tabSwitchAlertShown = false;
        const getCurrentQuestion = () => questions[currentIndex];
        const hasAnyAnsweredQuestion = () => questions.some((question) => question.type === 'short_answer' ? !!(question.answer.answer_text && question.answer.answer_text.trim() !== '') : !!question.answer.question_option_id);
        const isAnswered = (question) => question.type === 'short_answer' ? !!(question.answer.answer_text && question.answer.answer_text.trim() !== '') : !!question.answer.question_option_id;
        function buildNav() { questionNav.innerHTML = ''; questions.forEach((question, idx) => { const btn = document.createElement('button'); btn.type = 'button'; btn.textContent = idx + 1; btn.className = 'btn btn-sm ' + (idx === currentIndex ? 'btn-primary' : question.answer.is_flagged ? 'btn-warning' : isAnswered(question) ? 'btn-success' : 'btn-outline-secondary'); btn.addEventListener('click', async () => { await saveCurrentQuestion(); currentIndex = idx; renderQuestion(); }); questionNav.appendChild(btn); }); }
        function renderQuestion() { const question = getCurrentQuestion(); questionMeta.textContent = `Question ${currentIndex + 1} of ${questions.length} | ${question.marks} marks`; questionText.textContent = question.question_text; flagQuestion.checked = !!question.answer.is_flagged; if (question.type === 'short_answer') { questionBody.innerHTML = `<textarea id="subjective-answer" class="form-control" rows="6" placeholder="Write your answer here...">${question.answer.answer_text ?? ''}</textarea>`; } else { let optionsHtml = ''; question.options.forEach((option) => { const checked = question.answer.question_option_id === option.id ? 'checked' : ''; optionsHtml += `<label class="list-group-item d-flex gap-2 align-items-start"><input type="radio" name="mcq_option" value="${option.id}" ${checked} class="form-check-input mt-1"><span>${option.option_text}</span></label>`; }); questionBody.innerHTML = `<div class="list-group">${optionsHtml}</div>`; } prevBtn.disabled = currentIndex === 0; nextBtn.disabled = currentIndex === questions.length - 1; buildNav(); }
        async function saveCurrentQuestion() { const question = getCurrentQuestion(); const payload = { question_option_id: null, answer_text: null, is_flagged: flagQuestion.checked }; if (question.type === 'short_answer') { const textarea = document.getElementById('subjective-answer'); payload.answer_text = textarea ? textarea.value : null; } else { const selected = document.querySelector('input[name="mcq_option"]:checked'); payload.question_option_id = selected ? Number(selected.value) : null; } const response = await fetch(`${saveEndpointBase}/${question.id}/save`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }, body: JSON.stringify(payload) }); if (!response.ok) { const data = await response.json(); saveStatus.textContent = data.message || 'Save failed.'; saveStatus.className = 'small text-danger'; return false; } question.answer.question_option_id = payload.question_option_id; question.answer.answer_text = payload.answer_text; question.answer.is_flagged = payload.is_flagged; const data = await response.json(); saveStatus.textContent = `Saved at ${new Date(data.saved_at).toLocaleTimeString()}`; saveStatus.className = 'small text-success'; buildNav(); return true; }
        prevBtn.addEventListener('click', async () => { if (currentIndex === 0) return; await saveCurrentQuestion(); currentIndex -= 1; renderQuestion(); }); nextBtn.addEventListener('click', async () => { if (currentIndex >= questions.length - 1) return; await saveCurrentQuestion(); currentIndex += 1; renderQuestion(); }); saveBtn.addEventListener('click', saveCurrentQuestion); function setSubmissionLockedState() { isSubmitting = true; prevBtn.disabled = true; nextBtn.disabled = true; saveBtn.disabled = true; flagQuestion.disabled = true; submitBtn.disabled = true; saveStatus.textContent = 'Submitting exam...'; saveStatus.className = 'small text-primary'; } async function submitOnce() { if (isSubmitting) return; setSubmissionLockedState(); clearInterval(timerInterval); await saveCurrentQuestion(); submitForm.submit(); } submitForm.addEventListener('submit', function (event) { if (!hasAnyAnsweredQuestion()) { event.preventDefault(); saveStatus.textContent = 'Cannot submit an empty exam. Please answer at least one question.'; saveStatus.className = 'small text-danger'; return; } if (isSubmitting) { event.preventDefault(); return; } setSubmissionLockedState(); }); function updateCountdown() { const diff = Math.max(0, deadlineTimestamp - Date.now()); const totalSeconds = Math.floor(diff / 1000); const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0'); const seconds = String(totalSeconds % 60).padStart(2, '0'); countdown.textContent = `${minutes}:${seconds}`; if (diff <= 0) { countdown.textContent = '00:00'; submitOnce(); } } function blockClipboardEvent(event) { event.preventDefault(); saveStatus.textContent = 'Copy, cut, and paste are disabled during the exam.'; saveStatus.className = 'small text-danger'; } function showTabSwitchWarning() { if (tabSwitchAlertShown || isSubmitting) return; tabSwitchAlertShown = true; alert('Warning: Tab switching is detected. Please stay on the exam page.'); } function resetTabSwitchWarning() { tabSwitchAlertShown = false; } examArea.addEventListener('copy', blockClipboardEvent); examArea.addEventListener('cut', blockClipboardEvent); examArea.addEventListener('paste', blockClipboardEvent); document.addEventListener('visibilitychange', function () { if (document.hidden) { showTabSwitchWarning(); return; } resetTabSwitchWarning(); }); window.addEventListener('blur', showTabSwitchWarning); window.addEventListener('focus', resetTabSwitchWarning); timerInterval = setInterval(updateCountdown, 1000); updateCountdown(); renderQuestion();
    </script>
</x-app-layout>
