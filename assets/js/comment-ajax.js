/**
 * Shiro Comment AJAX Submit
 * Submit comments without page reload, show loading/success/error feedback.
 */
;(function () {
    'use strict';

    var form = document.querySelector('.comment-form form');
    if (!form) return;

    var submitBtn = form.querySelector('button[type="submit"]');
    if (!submitBtn) return;

    var originalText = submitBtn.textContent;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Validate
        var textarea = form.querySelector('textarea[name="text"]');
        if (!textarea || !textarea.value.trim()) {
            showFeedback('请输入评论内容', 'error');
            return;
        }

        // Loading state
        submitBtn.disabled = true;
        submitBtn.textContent = '提交中...';

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (res) {
            if (res.redirected || res.ok) {
                showFeedback('评论提交成功', 'success');
                textarea.value = '';
                // Reload comments after short delay
                setTimeout(function () { location.reload(); }, 1500);
            } else {
                throw new Error('HTTP ' + res.status);
            }
        })
        .catch(function (err) {
            showFeedback('提交失败，请重试', 'error');
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });

    function showFeedback(msg, type) {
        // Remove existing feedback
        var existing = form.querySelector('.comment-feedback');
        if (existing) existing.remove();

        var el = document.createElement('div');
        el.className = 'comment-feedback';
        el.style.cssText = 'margin-top:8px;padding:8px 12px;border-radius:6px;font-size:13px;'
            + (type === 'success'
                ? 'background:#f0fdf4;color:#166534;border:1px solid #bbf7d0'
                : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca');
        el.textContent = msg;
        submitBtn.parentNode.appendChild(el);

        setTimeout(function () { el.remove(); }, 4000);
    }
})();
