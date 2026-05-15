/**
 * Shiro Post Enhancements
 * - Export/Copy Markdown with copyright notice
 * - Copy text with copyright appended
 * - Lazy Mermaid and KaTeX rendering
 */
;(function () {
    'use strict';

    var meta = window.__postMeta;
    var articleBody = document.getElementById('article-body')
        || document.querySelector('.prose-shiro[data-pagefind-body]');

    // ── Copyright notice ──
    function getCopyright() {
        return '\n\n---\n'
            + '> 作者：' + meta.author + '\n'
            + '> 原文链接：' + meta.url + '\n'
            + '> 来源：' + meta.siteName + '\n'
            + '> 转载请注明出处';
    }

    // ── HTML to Markdown (simple conversion) ──
    function htmlToMarkdown(el) {
        var html = el.innerHTML;
        // Headers
        html = html.replace(/<h1[^>]*>(.*?)<\/h1>/gi, '# $1\n\n');
        html = html.replace(/<h2[^>]*>(.*?)<\/h2>/gi, '## $1\n\n');
        html = html.replace(/<h3[^>]*>(.*?)<\/h3>/gi, '### $1\n\n');
        html = html.replace(/<h4[^>]*>(.*?)<\/h4>/gi, '#### $1\n\n');
        html = html.replace(/<h5[^>]*>(.*?)<\/h5>/gi, '##### $1\n\n');
        // Bold & italic
        html = html.replace(/<strong[^>]*>(.*?)<\/strong>/gi, '**$1**');
        html = html.replace(/<b[^>]*>(.*?)<\/b>/gi, '**$1**');
        html = html.replace(/<em[^>]*>(.*?)<\/em>/gi, '*$1*');
        html = html.replace(/<i[^>]*>(.*?)<\/i>/gi, '*$1*');
        // Links
        html = html.replace(/<a[^>]+href="([^"]*)"[^>]*>(.*?)<\/a>/gi, '[$2]($1)');
        // Images
        html = html.replace(/<img[^>]+src="([^"]*)"[^>]*alt="([^"]*)"[^>]*\/?>/gi, '![$2]($1)');
        html = html.replace(/<img[^>]+src="([^"]*)"[^>]*\/?>/gi, '![]($1)');
        // Code blocks
        html = html.replace(/<pre[^>]*><code[^>]*class="language-([^"]*)"[^>]*>([\s\S]*?)<\/code><\/pre>/gi, function (m, lang, code) {
            return '```' + lang + '\n' + decodeHtml(code) + '\n```\n\n';
        });
        html = html.replace(/<pre[^>]*><code[^>]*>([\s\S]*?)<\/code><\/pre>/gi, function (m, code) {
            return '```\n' + decodeHtml(code) + '\n```\n\n';
        });
        // Inline code
        html = html.replace(/<code[^>]*>(.*?)<\/code>/gi, '`$1`');
        // Lists
        html = html.replace(/<li[^>]*>(.*?)<\/li>/gi, '- $1\n');
        html = html.replace(/<\/?[ou]l[^>]*>/gi, '\n');
        // Blockquote
        html = html.replace(/<blockquote[^>]*>([\s\S]*?)<\/blockquote>/gi, function (m, content) {
            return content.trim().split('\n').map(function (l) { return '> ' + l; }).join('\n') + '\n\n';
        });
        // Paragraphs & breaks
        html = html.replace(/<br\s*\/?>/gi, '\n');
        html = html.replace(/<p[^>]*>(.*?)<\/p>/gi, '$1\n\n');
        // Horizontal rule
        html = html.replace(/<hr[^>]*\/?>/gi, '---\n\n');
        // Strip remaining tags
        html = html.replace(/<[^>]+>/g, '');
        // Decode entities
        html = decodeHtml(html);
        // Clean up whitespace
        html = html.replace(/\n{3,}/g, '\n\n').trim();
        return html;
    }

    function decodeHtml(html) {
        var txt = document.createElement('textarea');
        txt.innerHTML = html;
        return txt.value;
    }

    // ── Export Markdown (download) ──
    var exportBtn = document.getElementById('exportMd');
    if (meta && exportBtn) {
        exportBtn.addEventListener('click', function () {
            var body = articleBody;
            if (!body) return;
            var md = '# ' + meta.title + '\n\n' + htmlToMarkdown(body) + getCopyright();
            var blob = new Blob([md], { type: 'text/markdown;charset=utf-8' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = (meta.title || 'article') + '.md';
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    // ── Copy Markdown (clipboard) ──
    var copyBtn = document.getElementById('copyMd');
    if (meta && copyBtn) {
        copyBtn.addEventListener('click', function () {
            var body = articleBody;
            if (!body) return;
            var md = '# ' + meta.title + '\n\n' + htmlToMarkdown(body) + getCopyright();
            navigator.clipboard.writeText(md).then(function () {
                var orig = copyBtn.textContent;
                copyBtn.textContent = '已复制';
                setTimeout(function () { copyBtn.innerHTML = '<i class="iconfont icon-book"></i> 复制 Markdown'; }, 2000);
            });
        });
    }

    // ── Copy selection with copyright ──
    if (meta) {
        document.addEventListener('copy', function (e) {
            var sel = window.getSelection();
            if (!sel || sel.isCollapsed) return;
            var container = sel.anchorNode && sel.anchorNode.parentElement;
            if (!container) return;
            if (!articleBody || !articleBody.contains(container)) return;

            var text = sel.toString();
            if (text.length < 30) return; // Don't append for short copies

            var copyright = '\n\n——————————\n'
                + '作者：' + meta.author + '\n'
                + '原文链接：' + meta.url + '\n'
                + '来源：' + meta.siteName + '\n'
                + '转载请注明出处';

            e.preventDefault();
            e.clipboardData.setData('text/plain', text + copyright);
        });
    }

    // ── Lazy content renderers ──
    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', reject, { once: true });
                return;
            }

            var script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.crossOrigin = 'anonymous';
            script.addEventListener('load', resolve, { once: true });
            script.addEventListener('error', reject, { once: true });
            document.head.appendChild(script);
        });
    }

    function loadStyle(href) {
        if (document.querySelector('link[href="' + href + '"]')) return;
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.crossOrigin = 'anonymous';
        document.head.appendChild(link);
    }

    function renderMermaid() {
        if (!articleBody) return;
        var blocks = articleBody.querySelectorAll('code.language-mermaid, code.lang-mermaid');
        if (!blocks.length && !articleBody.querySelector('.mermaid')) return;

        blocks.forEach(function (code, index) {
            var pre = code.closest('pre');
            var div = document.createElement('div');
            div.className = 'mermaid';
            div.textContent = code.textContent;
            div.id = div.id || 'shiro-mermaid-' + index;
            if (pre && pre.parentNode) pre.parentNode.replaceChild(div, pre);
        });

        loadScript('https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.min.js')
            .then(function () {
                if (!window.mermaid) return;
                window.mermaid.initialize({ startOnLoad: false, securityLevel: 'strict' });
                window.mermaid.run({ nodes: articleBody.querySelectorAll('.mermaid') });
            })
            .catch(function () {});
    }

    function renderMath() {
        if (!articleBody) return;
        var text = articleBody.textContent || '';
        var hasMath = articleBody.querySelector('.math, .katex, code.language-math, code.language-tex')
            || /(^|[^\\])(\$\$[\s\S]+?\$\$|\$[^$\n]{1,160}\$|\\\(|\\\[)/.test(text);
        if (!hasMath) return;

        var version = '0.16.46';
        loadStyle('https://cdn.jsdelivr.net/npm/katex@' + version + '/dist/katex.min.css');
        loadScript('https://cdn.jsdelivr.net/npm/katex@' + version + '/dist/katex.min.js')
            .then(function () {
                return loadScript('https://cdn.jsdelivr.net/npm/katex@' + version + '/dist/contrib/auto-render.min.js');
            })
            .then(function () {
                if (typeof window.renderMathInElement !== 'function') return;
                window.renderMathInElement(articleBody, {
                    delimiters: [
                        { left: '$$', right: '$$', display: true },
                        { left: '\\[', right: '\\]', display: true },
                        { left: '\\(', right: '\\)', display: false },
                        { left: '$', right: '$', display: false }
                    ],
                    ignoredTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
                    throwOnError: false
                });
            })
            .catch(function () {});
    }

    renderMermaid();
    renderMath();
})();
