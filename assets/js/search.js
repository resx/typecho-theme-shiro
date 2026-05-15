/**
 * Shiro AJAX Search for Typecho
 * Replaces Pagefind with Typecho-native AJAX search.
 */
;(function () {
  'use strict';

  var DEBOUNCE_MS = 300;
  var MIN_QUERY_LENGTH = 2;

  var searchModal = document.getElementById('searchModal');
  var searchInput = document.getElementById('searchInput');
  var searchResults = document.getElementById('searchResults');
  var searchToggle = document.getElementById('searchToggle');
  var searchClose = document.getElementById('searchClose');

  if (!searchModal || !searchInput) return;

  var i18n = (window.__i18n && window.__i18n.search) || {};
  var debounceTimer = null;
  var currentQuery = '';
  var resultCache = {};
  var activeController = null;
  var searchIndex = null;
  var searchIndexPromise = null;
  var searchIndexFailed = false;
  var lastFocus = null;

  function openModal() {
    if (searchModal.getAttribute('data-open') === 'true') return;
    lastFocus = document.activeElement;
    searchModal.setAttribute('data-open', 'true');
    document.documentElement.setAttribute('data-modal-open', 'true');
    loadSearchIndex().catch(function () {
      searchIndexFailed = true;
    });
    setTimeout(function () { searchInput.focus(); }, 100);
  }

  function closeModal() {
    if (searchModal.getAttribute('data-open') !== 'true') return;
    searchModal.setAttribute('data-open', 'false');
    document.documentElement.removeAttribute('data-modal-open');
    searchInput.value = '';
    searchResults.innerHTML = '';
    currentQuery = '';
    if (activeController) {
      activeController.abort();
      activeController = null;
    }
    if (lastFocus && typeof lastFocus.focus === 'function') {
      try { lastFocus.focus(); } catch (_) {}
    }
  }

  function onInput() {
    var query = searchInput.value.trim();
    if (debounceTimer) clearTimeout(debounceTimer);

    if (query.length < MIN_QUERY_LENGTH) {
      searchResults.innerHTML = '';
      currentQuery = '';
      return;
    }

    debounceTimer = setTimeout(function () {
      performSearch(query);
    }, DEBOUNCE_MS);
  }

  function performSearch(query) {
    if (query === currentQuery) return;
    currentQuery = query;

    if (resultCache[query]) {
      renderResults(resultCache[query]);
      return;
    }

    if (searchIndex) {
      renderLocalResults(query);
      return;
    }

    if (!searchIndexFailed) {
      searchResults.innerHTML = '<div class="text-center py-8 text-slate-500">' + (i18n.loading || 'Loading...') + '</div>';
      loadSearchIndex()
        .then(function () {
          if (query !== currentQuery) return;
          renderLocalResults(query);
        })
        .catch(function () {
          searchIndexFailed = true;
          performRemoteSearch(query);
        });
      return;
    }

    performRemoteSearch(query);
  }

  function loadSearchIndex() {
    if (searchIndex) return Promise.resolve(searchIndex);
    if (searchIndexPromise) return searchIndexPromise;

    var url = window.__searchIndexUrl || ((window.__searchUrl || '/') + '?do=shiro_search_index');
    searchIndexPromise = fetch(url, { headers: { 'Accept': 'application/json' } })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(function (data) {
        searchIndex = Array.isArray(data.items) ? data.items : [];
        return searchIndex;
      })
      .catch(function (err) {
        searchIndexPromise = null;
        throw err;
      });

    return searchIndexPromise;
  }

  function renderLocalResults(query) {
    var normalized = normalize(query);
    var terms = normalized.split(/\s+/).filter(Boolean);
    var limit = Math.max(1, Math.min(30, window.__searchLimit || 10));
    var scored = [];

    for (var i = 0; i < searchIndex.length; i++) {
      var item = searchIndex[i];
      var title = normalize(item.title);
      var excerpt = normalize(item.excerpt);
      var text = normalize(item.searchText || '');
      var matched = true;
      var score = 0;

      for (var t = 0; t < terms.length; t++) {
        var term = terms[t];
        var inTitle = title.indexOf(term) !== -1;
        var inExcerpt = excerpt.indexOf(term) !== -1;
        var inText = text.indexOf(term) !== -1;

        if (!inTitle && !inExcerpt && !inText) {
          matched = false;
          break;
        }
        if (inTitle) score += 10;
        if (inExcerpt) score += 4;
        if (inText) score += 1;
      }

      if (matched) scored.push({ score: score, item: item });
    }

    scored.sort(function (a, b) {
      if (b.score !== a.score) return b.score - a.score;
      return String(b.item.date || '').localeCompare(String(a.item.date || ''));
    });

    var results = scored.slice(0, limit).map(function (entry) {
      return entry.item;
    });
    var data = { keyword: query, total: results.length, results: results };
    resultCache[query] = data;
    renderResults(data);
  }

  function performRemoteSearch(query) {
    if (activeController) {
      activeController.abort();
    }
    activeController = window.AbortController ? new AbortController() : null;
    var controller = activeController;

    searchResults.innerHTML = '<div class="text-center py-8 text-slate-500">' + (i18n.loading || 'Loading...') + '</div>';

    var url = (window.__searchUrl || '/') + '?do=shiro_search&q=' + encodeURIComponent(query);

    fetch(url, {
      signal: controller ? controller.signal : undefined,
      headers: { 'Accept': 'application/json' }
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.keyword !== currentQuery) return;
        resultCache[query] = data;
        renderResults(data);
      })
      .catch(function (err) {
        if (err && err.name === 'AbortError') return;
        searchResults.innerHTML = '<div class="text-center py-8 text-slate-500">Search error</div>';
      })
      .finally(function () {
        if (activeController === controller) activeController = null;
      });
  }

  function normalize(str) {
    return String(str || '').toLowerCase();
  }

  function renderResults(data) {
    var total = data.total || 0;
    var keyword = data.keyword || '';

    var summaryText = '';
    if (total === 0) {
      summaryText = (i18n.zero_results || 'No results for "[SEARCH_TERM]"')
        .replace('[SEARCH_TERM]', escapeHtml(keyword));
    } else if (total === 1) {
      summaryText = (i18n.one_result || '[COUNT] result for "[SEARCH_TERM]"')
        .replace('[COUNT]', total)
        .replace('[SEARCH_TERM]', escapeHtml(keyword));
    } else {
      summaryText = (i18n.many_results || '[COUNT] results for "[SEARCH_TERM]"')
        .replace('[COUNT]', total)
        .replace('[SEARCH_TERM]', escapeHtml(keyword));
    }

    var html = '<div class="text-sm text-slate-500 px-4 py-2">' + summaryText + '</div>';

    if (data.results && data.results.length > 0) {
      html += '<ul class="divide-y divide-slate-100">';
      for (var idx = 0; idx < data.results.length; idx++) {
        var item = data.results[idx];
        html += '<li>'
          + '<a href="' + escapeHtml(item.url) + '" class="block px-4 py-3 hover:bg-slate-50 transition-colors">'
          + '<div class="font-medium text-slate-700">' + escapeHtml(item.title) + '</div>'
          + '<div class="text-sm text-slate-500 mt-1 line-clamp-2">' + escapeHtml(item.excerpt) + '</div>'
          + '<div class="text-xs text-slate-400 mt-1">' + escapeHtml(item.date) + '</div>'
          + '</a></li>';
      }
      html += '</ul>';
    }

    searchResults.innerHTML = html;
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str || ''));
    return div.innerHTML;
  }

  // Event listeners
  searchToggle && searchToggle.addEventListener('click', function (e) { e.preventDefault(); openModal(); });
  searchClose && searchClose.addEventListener('click', closeModal);
  searchInput.addEventListener('input', onInput);

  // Backdrop click + close buttons via data-search-close
  searchModal.addEventListener('click', function (e) {
    var t = e.target;
    if (t && t.closest && t.closest('[data-search-close]')) {
      e.preventDefault();
      closeModal();
    }
  });

  // Keyboard shortcuts
  document.addEventListener('keydown', function (e) {
    var isOpen = searchModal.getAttribute('data-open') === 'true';
    if (e.key === 'Escape' && isOpen) { e.preventDefault(); closeModal(); return; }
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') { e.preventDefault(); isOpen ? closeModal() : openModal(); return; }
    if (e.key === '/' && !isOpen && !e.metaKey && !e.ctrlKey && !e.altKey) {
      var ae = document.activeElement;
      var tag = ae && ae.tagName;
      if (ae && (ae.isContentEditable || tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT')) return;
      e.preventDefault();
      openModal();
    }
  });

  // Focus trap
  searchModal.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || searchModal.getAttribute('data-open') !== 'true') return;
    var focusables = searchModal.querySelectorAll('button, [href], input, textarea, [tabindex]:not([tabindex="-1"])');
    if (!focusables.length) return;
    var first = focusables[0];
    var last = focusables[focusables.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  });
})();
