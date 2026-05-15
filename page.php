<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php
$options = Helper::options();
$tocEnabled = isset($options->tocEnabled) ? $options->tocEnabled : '1';
?>

<article class="content-article">
    <header class="text-center mb-8">
        <h1 class="page-title mb-4"><?php echo shiro_escape($this->title); ?></h1>
    </header>

<?php if ($tocEnabled === '1'): ?>
    <!-- Inline TOC (small screens < xl) -->
    <div id="tocInline" class="toc-inline xl:hidden mb-8" data-pagefind-ignore>
        <button class="toc-toggle" aria-expanded="false" aria-controls="tocInlineBody">
            <span class="toc-toggle-title"><?php echo shiro_lang('toc.title'); ?></span>
            <svg class="toc-chevron size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z" clip-rule="evenodd" />
            </svg>
        </button>
        <div class="toc-body" id="tocInlineBody" data-open="false"></div>
    </div>

    <!-- Sidebar TOC (xl+ screens) -->
    <aside id="tocSidebar" class="toc-sidebar hidden xl:block" aria-label="<?php echo shiro_lang('toc.title'); ?>" data-pagefind-ignore>
        <div class="toc-sidebar-inner">
            <p class="toc-sidebar-title"><?php echo shiro_lang('toc.title'); ?></p>
            <div class="toc-body"></div>
        </div>
    </aside>
<?php endif; ?>

    <div class="prose-shiro" data-pagefind-body>
        <?php
        ob_start();
        $this->content();
        echo shiro_enhance_content_images(ob_get_clean(), true);
        ?>
    </div>

    <!-- Comments -->
    <?php $this->need('comments.php'); ?>
</article>

<?php $this->need('footer.php'); ?>
