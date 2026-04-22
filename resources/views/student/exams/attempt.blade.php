<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $exam->title }}
            </h2>
            <div class="rounded-md bg-red-100 px-3 py-2 text-sm font-semibold text-red-800">
                Time Left: <span id="countdown">--:--</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="exam-area" class="grid gap-6 lg:grid-cols-4">
                <div class="lg:col-span-1 bg-white shadow-sm sm:rounded-lg p-4">
                    <h3 class="mb-3 text-sm font-semibold text-gray-700">Questions</h3>
                    <div id="question-nav" class="grid grid-cols-5 gap-2"></div>
                    <p class="mt-4 text-xs text-gray-500">Green = answered, Yellow = flagged</p>
                </div>

                <div class="lg:col-span-3 bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500" id="question-meta"></p>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900" id="question-text"></h3>

                    <div id="question-body" class="mt-6"></div>

                    <div class="mt-6 flex items-center gap-2">
                        <input id="flag-question" type="checkbox" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                        <label for="flag-question" class="text-sm text-gray-700">Flag this question</label>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <button id="prev-btn" type="button" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Previous</button>
                        <button id="save-btn" type="button" class="px-4 py-2 rounded-md bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-500">Save Answer</button>
                        <button id="next-btn" type="button" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Next</button>
                        <form id="submit-form" method="POST" action="{{ route('student.exams.submit', $exam) }}" class="ml-auto">
                            @csrf
                            <button id="submit-btn" type="submit" class="px-4 py-2 rounded-md bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-500" onclick="return confirm('Submit exam now?')">Submit Exam</button>
                        </form>
                    </div>

                    <p id="save-status" class="mt-3 text-xs text-gray-500"></p>
                </div>
            </div>
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

        function getCurrentQuestion() {
            return questions[currentIndex];
        }

        function isAnswered(question) {
            if (question.type === 'short_answer') {
                return !!(question.answer.answer_text && question.answer.answer_text.trim() !== '');
            }
            return !!question.answer.question_option_id;
        }

        function buildNav() {
            questionNav.innerHTML = '';
            questions.forEach((question, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = idx + 1;
                btn.className = 'h-9 rounded text-xs font-semibold border';

                if (idx === currentIndex) {
                    btn.classList.add('border-indigo-600', 'bg-indigo-100', 'text-indigo-700');
                } else if (question.answer.is_flagged) {
                    btn.classList.add('border-yellow-500', 'bg-yellow-100', 'text-yellow-700');
                } else if (isAnswered(question)) {
                    btn.classList.add('border-green-500', 'bg-green-100', 'text-green-700');
                } else {
                    btn.classList.add('border-gray-300', 'bg-white', 'text-gray-600');
                }

                btn.addEventListener('click', async () => {
                    await saveCurrentQuestion();
                    currentIndex = idx;
                    renderQuestion();
                });

                questionNav.appendChild(btn);
            });
        }

        function renderQuestion() {
            const question = getCurrentQuestion();
            questionMeta.textContent = `Question ${currentIndex + 1} of ${questions.length} | ${question.marks} marks`;
            questionText.textContent = question.question_text;
            flagQuestion.checked = !!question.answer.is_flagged;

            if (question.type === 'short_answer') {
                questionBody.innerHTML = `
                    <textarea id="subjective-answer" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="6" placeholder="Write your answer here...">${question.answer.answer_text ?? ''}</textarea>
                `;
            } else {
                let optionsHtml = '';
                question.options.forEach((option) => {
                    const checked = question.answer.question_option_id === option.id ? 'checked' : '';
                    optionsHtml += `
                        <label class="flex items-start gap-3 rounded-md border border-gray-200 p-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="mcq_option" value="${option.id}" ${checked} class="mt-1 text-indigo-600 focus:ring-indigo-500">
                            <span>${option.option_text}</span>
                        </label>
                    `;
                });
                questionBody.innerHTML = `<div class="space-y-3">${optionsHtml}</div>`;
            }

            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === questions.length - 1;
            buildNav();
        }

        async function saveCurrentQuestion() {
            const question = getCurrentQuestion();
            const payload = {
                question_option_id: null,
                answer_text: null,
                is_flagged: flagQuestion.checked,
            };

            if (question.type === 'short_answer') {
                const textarea = document.getElementById('subjective-answer');
                payload.answer_text = textarea ? textarea.value : null;
            } else {
                const selected = document.querySelector('input[name="mcq_option"]:checked');
                payload.question_option_id = selected ? Number(selected.value) : null;
            }

            const response = await fetch(`${saveEndpointBase}/${question.id}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const data = await response.json();
                saveStatus.textContent = data.message || 'Save failed.';
                saveStatus.className = 'mt-3 text-xs text-red-600';
                return false;
            }

            question.answer.question_option_id = payload.question_option_id;
            question.answer.answer_text = payload.answer_text;
            question.answer.is_flagged = payload.is_flagged;

            const data = await response.json();
            saveStatus.textContent = `Saved at ${new Date(data.saved_at).toLocaleTimeString()}`;
            saveStatus.className = 'mt-3 text-xs text-emerald-600';
            buildNav();
            return true;
        }

        prevBtn.addEventListener('click', async () => {
            if (currentIndex === 0) return;
            await saveCurrentQuestion();
            currentIndex -= 1;
            renderQuestion();
        });

        nextBtn.addEventListener('click', async () => {
            if (currentIndex >= questions.length - 1) return;
            await saveCurrentQuestion();
            currentIndex += 1;
            renderQuestion();
        });

        saveBtn.addEventListener('click', saveCurrentQuestion);

        function setSubmissionLockedState() {
            isSubmitting = true;
            prevBtn.disabled = true;
            nextBtn.disabled = true;
            saveBtn.disabled = true;
            flagQuestion.disabled = true;
            submitBtn.disabled = true;
            saveStatus.textContent = 'Submitting exam...';
            saveStatus.className = 'mt-3 text-xs text-indigo-600';
        }

        async function submitOnce() {
            if (isSubmitting) {
                return;
            }

            setSubmissionLockedState();
            clearInterval(timerInterval);
            await saveCurrentQuestion();
            submitForm.submit();
        }

        submitForm.addEventListener('submit', function (event) {
            if (isSubmitting) {
                event.preventDefault();
                return;
            }

            setSubmissionLockedState();
        });

        function updateCountdown() {
            const now = Date.now();
            const diff = Math.max(0, deadlineTimestamp - now);
            const totalSeconds = Math.floor(diff / 1000);
            const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const seconds = String(totalSeconds % 60).padStart(2, '0');
            countdown.textContent = `${minutes}:${seconds}`;

            if (diff <= 0) {
                countdown.textContent = '00:00';
                submitOnce();
            }
        }

        function blockClipboardEvent(event) {
            event.preventDefault();
            saveStatus.textContent = 'Copy, cut, and paste are disabled during the exam.';
            saveStatus.className = 'mt-3 text-xs text-red-600';
        }

        function showTabSwitchWarning() {
            if (tabSwitchAlertShown || isSubmitting) {
                return;
            }

            tabSwitchAlertShown = true;
            alert('Warning: Tab switching is detected. Please stay on the exam page.');
        }

        function resetTabSwitchWarning() {
            tabSwitchAlertShown = false;
        }

        examArea.addEventListener('copy', blockClipboardEvent);
        examArea.addEventListener('cut', blockClipboardEvent);
        examArea.addEventListener('paste', blockClipboardEvent);

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                showTabSwitchWarning();
                return;
            }

            resetTabSwitchWarning();
        });

        window.addEventListener('blur', showTabSwitchWarning);
        window.addEventListener('focus', resetTabSwitchWarning);

        timerInterval = setInterval(updateCountdown, 1000);
        updateCountdown();
        renderQuestion();
    </script>
</x-app-layout>
