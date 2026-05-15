<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$options = Helper::options();
$provider = isset($options->commentsProvider) ? $options->commentsProvider : 'native';
$provider = in_array($provider, ['native', 'disqus', 'giscus'], true) ? $provider : 'native';
?>

<?php $disqusShortname = shiro_disqus_shortname(isset($options->disqusShortname) ? $options->disqusShortname : ''); ?>
<?php if ($provider === 'disqus' && $disqusShortname): ?>
<!-- Disqus Comments -->
<div class="section-divider" id="comments">
    <div id="disqus_thread"></div>
    <script>
    (function() {
        var target = document.getElementById('disqus_thread');
        if (!target) return;

        var loadDisqus = function() {
            if (target.getAttribute('data-loaded') === 'true') return;
            target.setAttribute('data-loaded', 'true');

            var d = document, s = d.createElement('script');
            var shortname = <?php echo shiro_json($disqusShortname); ?>;
            s.src = 'https://' + shortname + '.disqus.com/embed.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
        };

        if (!('IntersectionObserver' in window)) {
            loadDisqus();
            return;
        }

        var observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                observer.disconnect();
                loadDisqus();
            }
        }, { rootMargin: '200px' });
        observer.observe(target);
    })();
    </script>
</div>

<?php
elseif ($provider === 'giscus'):
    $gc = shiro_giscus_config();
    if (!empty($gc['repo'])):
?>
<!-- giscus Comments -->
<div class="section-divider" id="comments">
    <div id="giscus_thread"></div>
    <script>
    (function() {
        var target = document.getElementById('giscus_thread');
        if (!target) return;

        var loadGiscus = function() {
            if (target.getAttribute('data-loaded') === 'true') return;
            target.setAttribute('data-loaded', 'true');

            var script = document.createElement('script');
            script.src = 'https://giscus.app/client.js';
            script.setAttribute('data-repo', <?php echo shiro_json($gc['repo']); ?>);
            script.setAttribute('data-repo-id', <?php echo shiro_json(isset($gc['repo_id']) ? $gc['repo_id'] : ''); ?>);
            script.setAttribute('data-category', <?php echo shiro_json(isset($gc['category']) ? $gc['category'] : ''); ?>);
            script.setAttribute('data-category-id', <?php echo shiro_json(isset($gc['category_id']) ? $gc['category_id'] : ''); ?>);
            script.setAttribute('data-mapping', <?php echo shiro_json(isset($gc['mapping']) ? $gc['mapping'] : 'pathname'); ?>);
            script.setAttribute('data-strict', '0');
            script.setAttribute('data-reactions-enabled', '1');
            script.setAttribute('data-emit-metadata', '0');
            script.setAttribute('data-input-position', 'bottom');
            script.setAttribute('data-theme', 'preferred_color_scheme');
            script.setAttribute('data-lang', <?php echo shiro_json(isset($gc['lang']) ? $gc['lang'] : 'zh-CN'); ?>);
            script.crossOrigin = 'anonymous';
            script.async = true;
            target.appendChild(script);
        };

        if (!('IntersectionObserver' in window)) {
            loadGiscus();
            return;
        }

        var observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                observer.disconnect();
                loadGiscus();
            }
        }, { rootMargin: '200px' });
        observer.observe(target);
    })();
    </script>
</div>
<?php endif; ?>

<?php else: ?>
<!-- Native Typecho Comments -->
<?php if ($this->allow('comment')): ?>
<div class="section-divider" id="comments">
    <h3 class="text-lg font-medium text-slate-700 mb-6"><?php $this->commentsNum(shiro_lang('comments.zero'), shiro_lang('comments.one'), shiro_lang('comments.many')); ?></h3>

    <?php $comments = $this->comments(); ?>
    <?php if ($comments->have()): ?>
    <div class="comment-list space-y-6">
        <?php while ($comments->next()): ?>
        <div class="comment-item" id="<?php $comments->theId(); ?>">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <?php $comments->gravatar(40, '', '', 'rounded-full'); ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-medium text-slate-700"><?php echo shiro_comment_author($comments); ?></span>
                        <time class="text-slate-400" datetime="<?php $comments->date('c'); ?>"><?php $comments->date(); ?></time>
                    </div>
                    <div class="mt-1 text-slate-600 prose-shiro prose-sm">
                        <?php $comments->content(); ?>
                    </div>
                    <div class="mt-2">
                        <?php $comments->reply(shiro_lang('comments.reply')); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Comment Pagination -->
    <?php $comments->pageNav(shiro_lang('page.prev'), shiro_lang('page.next')); ?>
    <?php endif; ?>

    <!-- Comment Form -->
    <div class="comment-form mt-8" id="respond">
        <h4 class="text-base font-medium text-slate-700 mb-4"><?php echo shiro_escape(shiro_lang('comments.leave')); ?></h4>
        <form method="post" action="<?php echo shiro_escape(shiro_safe_url(shiro_capture_method($this, 'commentUrl'), '#respond')); ?>" class="space-y-4">
            <?php if ($this->user->hasLogin()): ?>
            <p class="text-sm text-slate-500">
                <?php echo shiro_escape(shiro_lang('comments.logged_in_as')); ?> <strong><?php echo shiro_escape($this->user->screenName); ?></strong>
            </p>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <input type="text" name="author" placeholder="<?php echo shiro_escape(shiro_lang('comments.name')); ?>" value="<?php echo shiro_remember_value($this, 'author'); ?>" required />
                <input type="email" name="mail" placeholder="<?php echo shiro_escape(shiro_lang('comments.email')); ?>" value="<?php echo shiro_remember_value($this, 'mail'); ?>" required />
                <input type="url" name="url" placeholder="<?php echo shiro_escape(shiro_lang('comments.url')); ?>" value="<?php echo shiro_remember_value($this, 'url'); ?>" />
            </div>
            <?php endif; ?>
            <textarea name="text" placeholder="<?php echo shiro_escape(shiro_lang('comments.placeholder')); ?>" required></textarea>
            <div>
                <button type="submit" class="focus-elegant btn-ink"><?php echo shiro_escape(shiro_lang('comments.submit')); ?></button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
