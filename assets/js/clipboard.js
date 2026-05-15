;(() => {
    // Typecho outputs: <pre><code class="language-xxx">...</code></pre>
    // Also support Hexo-style: <figure class="highlight xxx">...</figure>
    const codeBlocks = document.querySelectorAll('.prose-shiro pre');
    if (!codeBlocks.length) return;

    const i18nCopy = () => (window.__i18n && window.__i18n.clipboard_copy) || 'Copy code';
    const i18nCopied = () => (window.__i18n && window.__i18n.clipboard_copied) || 'Copied';

    function copyText(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }
        return new Promise((resolve, reject) => {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.cssText = 'position:fixed;left:-9999px;opacity:0';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy') ? resolve() : reject();
            } catch (_) {
                reject();
            } finally {
                document.body.removeChild(ta);
            }
        });
    }

    const iconCopy = '<svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="14" height="14" rx="1"/><path d="M16 8V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2"/></svg>';
    const iconDone = '<svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>';

    codeBlocks.forEach((pre) => {
        const codeEl = pre.querySelector('code') || pre;
        if (!codeEl.textContent.trim()) return;

        // Detect language from class (e.g., "language-javascript" or "lang-js")
        const langMatch = codeEl.className.match(/(?:language|lang)-(\S+)/);
        const lang = langMatch ? langMatch[1] : '';

        const btn = document.createElement('button');
        btn.className = 'copy-btn';
        btn.setAttribute('aria-label', i18nCopy());
        btn.innerHTML = iconCopy;

        btn.addEventListener('click', () => {
            const text = codeEl.textContent;
            copyText(text).then(() => {
                btn.innerHTML = iconDone;
                btn.classList.add('copied');
                btn.setAttribute('aria-label', i18nCopied());
                setTimeout(() => {
                    btn.innerHTML = iconCopy;
                    btn.classList.remove('copied');
                    btn.setAttribute('aria-label', i18nCopy());
                }, 2000);
            }).catch(() => {});
        });

        const wrapper = document.createElement('div');
        wrapper.className = 'highlight-wrapper';
        pre.parentNode.insertBefore(wrapper, pre);
        wrapper.appendChild(pre);
        wrapper.appendChild(btn);

        if (lang) {
            const label = document.createElement('span');
            label.className = 'code-lang';
            label.textContent = lang;
            wrapper.appendChild(label);
        }
    });
})();
