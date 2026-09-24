// Small progressive enhancements - the site works without JavaScript.

// Mobile navigation toggle
document.querySelectorAll('[data-toggle]').forEach((button) => {
    const target = document.getElementById(button.dataset.toggle);
    if (!target) return;
    button.addEventListener('click', () => {
        const open = target.hidden;
        target.hidden = !open;
        button.setAttribute('aria-expanded', String(open));
    });
});

// Confirm destructive actions: <form data-confirm="Are you sure?">
document.addEventListener('submit', (event) => {
    const message = event.target.dataset?.confirm;
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

// Markdown editor: Write / Preview tabs
document.querySelectorAll('[data-markdown-editor]').forEach((editor) => {
    const textarea = editor.querySelector('textarea');
    const preview = editor.querySelector('[data-preview]');
    const tabs = editor.querySelectorAll('[data-tab]');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    tabs.forEach((tab) => {
        tab.addEventListener('click', async () => {
            tabs.forEach((t) => t.setAttribute('aria-selected', String(t === tab)));
            const showPreview = tab.dataset.tab === 'preview';
            textarea.hidden = showPreview;
            preview.hidden = !showPreview;

            if (showPreview) {
                preview.innerHTML = '<p class="text-slate-400">Loading preview…</p>';
                const response = await fetch(editor.dataset.previewUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, Accept: 'application/json' },
                    body: JSON.stringify({ body: textarea.value }),
                });
                const data = response.ok ? await response.json() : { html: '<p>Preview unavailable.</p>' };
                preview.innerHTML = data.html || '<p class="text-slate-400">Nothing to preview yet.</p>';
            }
        });
    });
});

// Character counters: <textarea data-maxlength-counter="excerpt-count">
document.querySelectorAll('[data-counter]').forEach((field) => {
    const output = document.getElementById(field.dataset.counter);
    const update = () => (output.textContent = `${field.value.length}/${field.maxLength}`);
    field.addEventListener('input', update);
    update();
});
