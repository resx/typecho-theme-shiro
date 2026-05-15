<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$options = Helper::options();
$backToTop = isset($options->backToTop) ? $options->backToTop : '1';
$searchEnabled = isset($options->searchEnabled) ? $options->searchEnabled : '1';
$darkToggle = isset($options->darkModeToggle) ? $options->darkModeToggle : '1';
$progressBar = isset($options->progressBar) ? $options->progressBar : '1';
$tocEnabled = isset($options->tocEnabled) ? $options->tocEnabled : '1';
$tocDepth = isset($options->tocDepth) ? $options->tocDepth : '3';
$tocMinHeadings = isset($options->tocMinHeadings) ? $options->tocMinHeadings : '3';
$gaId = shiro_google_analytics_id(isset($options->googleAnalyticsId) ? $options->googleAnalyticsId : '');
$commentsProvider = isset($options->commentsProvider) ? $options->commentsProvider : 'native';
$commentsProvider = in_array($commentsProvider, ['native', 'disqus', 'giscus'], true) ? $commentsProvider : 'native';
$since = isset($options->since) ? $options->since : '';
$footerText = isset($options->footerText) ? shiro_footer_html($options->footerText) : '';
$icpBeian = isset($options->icpBeian) ? shiro_plain_text($options->icpBeian) : '';
$policeBeian = isset($options->policeBeian) ? shiro_plain_text($options->policeBeian) : '';
$footerRecords = array_filter([
    shiro_footer_record_link($icpBeian, shiro_icp_beian_url()),
    shiro_footer_record_link($policeBeian, shiro_police_beian_url($policeBeian)),
]);
$menuItems = shiro_get_menu();
$isContentPage = $this->is('post') || $this->is('page');
?>
            </main>

            <!-- Footer -->
            <footer class="section-divider" data-pagefind-ignore>
                <div class="text-center text-sm text-slate-500 leading-relaxed">
                    <p>&copy; <span class="oldstyle-nums"><?php echo shiro_copyright_year($since); ?></span> <?php echo shiro_escape($options->title); ?></p>
<?php if (!empty($footerText)): ?>
                    <p class="font-eng text-footnote tracking-wide opacity-90"><?php echo $footerText; ?></p>
<?php endif; ?>
<?php if (!empty($footerRecords)): ?>
                    <p class="font-eng text-footnote tracking-wide opacity-90"><?php echo implode(' <span class="mx-1 text-slate-300">/</span> ', $footerRecords); ?></p>
<?php endif; ?>
                </div>
            </footer>
        </div>

        <!-- Note Text -->
        <aside class="mt-4 text-center" aria-hidden="true" role="presentation">
            <p class="site-note font-title text-xs sm:text-sm text-slate-400/70 tracking-note font-light select-none" lang="ja">
                白は、余白の名。
            </p>
        </aside>
    </div>

<?php if ($backToTop === '1'): ?>
    <button id="backToTop" class="back-to-top" data-visible="false" aria-label="<?php echo shiro_escape(shiro_lang('back_to_top')); ?>">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
        </svg>
    </button>
<?php endif; ?>

<?php if ($searchEnabled === '1'): ?>
    <!-- Search Modal -->
    <div id="searchModal" class="search-modal" data-open="false" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="searchModalTitle">
        <div class="search-modal__backdrop" data-search-close></div>
        <div class="search-modal__panel" role="document">
            <div class="search-modal__header">
                <h2 id="searchModalTitle" class="search-modal__title"><?php echo shiro_escape(shiro_lang('search.button')); ?></h2>
                <button type="button" id="searchClose" class="search-modal__close" data-search-close aria-label="<?php echo shiro_escape(shiro_lang('search.close')); ?>">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"></path>
                    </svg>
                </button>
            </div>
            <div class="search-modal__body">
                <div class="p-4">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input id="searchInput" type="search" placeholder="<?php echo shiro_escape(shiro_lang('search.placeholder')); ?>" autocomplete="off" />
                    </div>
                </div>
                <div id="searchResults" class="overflow-y-auto max-h-[60vh]"></div>
            </div>
        </div>
    </div>
<?php endif; ?>

    <!-- Scripts -->
<?php
$i18nData = [
    'clipboard_copy' => shiro_lang('clipboard_copy'),
    'clipboard_copied' => shiro_lang('clipboard_copied'),
    'gallery_visit_source' => shiro_lang('gallery_visit_source'),
    'gallery_view_image' => shiro_lang('gallery_view_image'),
    'search' => [
        'button' => shiro_lang('search.button'),
        'placeholder' => shiro_lang('search.placeholder'),
        'close' => shiro_lang('search.close'),
        'zero_results' => shiro_lang('search.zero_results'),
        'one_result' => shiro_lang('search.one_result'),
        'many_results' => shiro_lang('search.many_results'),
        'loading' => shiro_lang('search.loading'),
    ],
];
?>
    <script>window.__i18n = <?php echo shiro_json($i18nData); ?>;</script>

<?php if ($isContentPage): ?>
    <script defer src="<?php echo themeAsset('assets/js/lightgallery.js'); ?>"></script>
<?php endif; ?>
    <script defer src="<?php echo themeAsset('assets/js/clipboard.js'); ?>"></script>
    <script defer src="<?php echo themeAsset('assets/js/tab-title.js'); ?>"></script>

<?php if ($isContentPage): ?>
    <script defer src="<?php echo themeAsset('assets/js/post-enhance.js'); ?>"></script>
<?php endif; ?>

<?php if ($isContentPage && $commentsProvider === 'native'): ?>
    <script defer src="<?php echo themeAsset('assets/js/comment-ajax.js'); ?>"></script>
<?php endif; ?>

<?php if ($tocEnabled === '1' && $isContentPage): ?>
    <script>window.__tocConfig = <?php echo shiro_json(['depth' => (int)$tocDepth, 'minHeadings' => (int)$tocMinHeadings]); ?>;</script>
    <script defer src="<?php echo themeAsset('assets/js/toc.js'); ?>"></script>
<?php endif; ?>

<?php $needsProgressJs = ($progressBar === '1' && $isContentPage) || $backToTop === '1'; ?>
<?php if ($needsProgressJs): ?>
    <script defer src="<?php echo themeAsset('assets/js/progress.js'); ?>"></script>
<?php endif; ?>

<?php if ($darkToggle === '1'): ?>
    <script defer src="<?php echo themeAsset('assets/js/theme-toggle.js'); ?>"></script>
<?php endif; ?>

<?php if (!empty($menuItems)): ?>
    <script defer src="<?php echo themeAsset('assets/js/mobile-menu.js'); ?>"></script>
<?php endif; ?>

<?php if ($searchEnabled === '1'): ?>
    <script>window.__searchUrl = <?php echo shiro_json($options->index); ?>;</script>
    <script>window.__searchIndexUrl = <?php echo shiro_json($options->index . '?do=shiro_search_index'); ?>;window.__searchLimit = <?php echo (int)max(1, min(30, isset($options->searchLimit) ? (int)$options->searchLimit : 10)); ?>;</script>
    <script defer src="<?php echo themeAsset('assets/js/search.js'); ?>"></script>
<?php endif; ?>

<?php if (!empty($gaId)): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo shiro_escape($gaId); ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config',<?php echo shiro_json($gaId); ?>);</script>
<?php endif; ?>

<?php
$customJS = isset($options->customJS) ? htmlspecialchars_decode($options->customJS) : '';
if (!empty($customJS)): ?>
    <script><?php echo $customJS; ?></script>
<?php endif; ?>
</body>
</html>
