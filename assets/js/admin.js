/**
 * Shiro Theme Admin - Tab Settings + Conditional Visibility
 * Reference: OneBlog admin.js tab implementation.
 */
document.addEventListener('DOMContentLoaded', function () {
    var tabs = [
        { id: 'shiro-basic', label: '基本设置', selector: '[name="sealEnabled"],[name="sealText"],[name="faviconMode"],[name="faviconUrl"],[name="since"],[name="rssEnabled"]' },
        { id: 'shiro-nav', label: '导航与外观', selector: '[name="menuItems"],[name="darkModeDefault"],[name="darkModeToggle"]' },
        { id: 'shiro-article', label: '文章阅读', selector: '[name="excerptLength"],[name="tocEnabled"],[name="tocDepth"],[name="tocMinHeadings"],[name="progressBar"],[name="backToTop"]' },
        { id: 'shiro-comment', label: '评论搜索', selector: '[name="searchEnabled"],[name="searchLimit"],[name="commentsProvider"],[name="disqusShortname"],[name="giscusConfig"]' },
        { id: 'shiro-footer', label: '页脚与友链', selector: '[name="footerText"],[name="icpBeian"],[name="policeBeian"],[name="googleAnalyticsId"],[name="themeColor"],[name="customCSS"],[name="customJS"],[name="links"]' }
    ];

    var form = document.querySelector('form');
    var tabContainer = document.getElementById('tab-container');
    var tabNav = document.getElementById('tab-nav');
    var tabContent = document.getElementById('tab-content');

    if (!form || !tabContainer || !tabNav || !tabContent) return;

    // Move tab container into the form as first child
    form.insertBefore(tabContainer, form.firstChild);

    // Generate tab nav and content panels
    tabs.forEach(function (tab) {
        var li = document.createElement('li');
        var a = document.createElement('a');
        a.href = '#' + tab.id;
        a.textContent = tab.label;
        a.addEventListener('click', function (e) {
            e.preventDefault();
            switchTab(tab.id);
        });
        li.appendChild(a);
        tabNav.appendChild(li);

        var pane = document.createElement('div');
        pane.id = tab.id;
        pane.className = 'tab-pane';
        tabContent.appendChild(pane);
    });

    // Move form fields into corresponding tab panels
    tabs.forEach(function (tab) {
        if (!tab.selector) return;
        var fields = form.querySelectorAll(tab.selector);
        var pane = document.getElementById(tab.id);
        if (!pane) return;

        fields.forEach(function (field) {
            var container = field.closest('.typecho-option') || field.closest('ul');
            if (container && pane) {
                pane.appendChild(container);
            }
        });
    });

    // Default: show first tab
    switchTab(tabs[0].id);

    function switchTab(tabId) {
        var panes = document.querySelectorAll('.tab-pane');
        for (var i = 0; i < panes.length; i++) {
            panes[i].classList.remove('active');
        }
        var target = document.getElementById(tabId);
        if (target) target.classList.add('active');

        var links = tabNav.querySelectorAll('a');
        for (var j = 0; j < links.length; j++) {
            links[j].classList.remove('active');
        }
        var activeLink = tabNav.querySelector('a[href="#' + tabId + '"]');
        if (activeLink) activeLink.classList.add('active');

        try { localStorage.setItem('shiro_admin_tab', tabId); } catch (e) {}
    }

    // Restore last tab
    var saved = '';
    try { saved = localStorage.getItem('shiro_admin_tab') || ''; } catch (e) {}
    if (saved && document.getElementById(saved)) {
        switchTab(saved);
    }

    // ── Conditional visibility: comment provider sub-fields ──
    var providerSelect = form.querySelector('[name="commentsProvider"]');
    var disqusField = form.querySelector('[name="disqusShortname"]');
    var giscusField = form.querySelector('[name="giscusConfig"]');

    if (providerSelect) {
        var disqusContainer = disqusField ? (disqusField.closest('.typecho-option') || disqusField.closest('ul')) : null;
        var giscusContainer = giscusField ? (giscusField.closest('.typecho-option') || giscusField.closest('ul')) : null;

        function updateCommentFields() {
            var val = providerSelect.value;
            if (disqusContainer) disqusContainer.style.display = val === 'disqus' ? '' : 'none';
            if (giscusContainer) giscusContainer.style.display = val === 'giscus' ? '' : 'none';
        }

        providerSelect.addEventListener('change', updateCommentFields);
        updateCommentFields();
    }
});
