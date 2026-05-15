<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php
$options = Helper::options();
$tocEnabled = isset($options->tocEnabled) ? $options->tocEnabled : '1';
$wordCount = shiro_word_count($this->text);
$readingTime = shiro_reading_time($this->text);
$postCover = shiro_post_cover($this, false);
$tagItems = shiro_tag_items($this);
$tagNames = array_map(function ($tag) {
    return $tag['name'];
}, $tagItems);
$authorName = shiro_author_name($this);
?>

<article class="content-article" id="post-content">
    <!-- Breadcrumb -->
    <nav class="text-xs text-slate-400 mb-4 text-center" aria-label="breadcrumb">
        <a href="<?php echo shiro_escape(shiro_site_url()); ?>" class="hover:text-seal transition-colors"><?php echo shiro_lang('nav.home'); ?></a>
        <span class="mx-1">/</span>
        <?php echo shiro_category_links($this, ', ', 'hover:text-seal transition-colors'); ?>
        <span class="mx-1">/</span>
        <span class="text-slate-500"><?php echo shiro_escape($this->title); ?></span>
    </nav>

    <header class="text-center mb-8">
        <h1 class="page-title mb-4"><?php echo shiro_escape($this->title); ?></h1>
        <div class="meta-line text-sm text-slate-500 mb-4 flex items-center justify-center gap-4 flex-wrap">
            <span class="inline-flex items-center gap-1.5"><i class="iconfont icon-calendar"></i><time datetime="<?php $this->date('c'); ?>"><?php $this->date(); ?></time></span>
            <span class="inline-flex items-center gap-1.5"><i class="iconfont icon-folder"></i><?php echo shiro_category_links($this, ', '); ?></span>
            <span class="inline-flex items-center gap-1.5"><i class="iconfont icon-book"></i><?php echo $wordCount; ?> 字 · <?php echo $readingTime; ?> 分钟</span>
        </div>
    </header>

<?php if ($postCover): ?>
    <figure class="post-cover">
        <img src="<?php echo shiro_escape($postCover); ?>" alt="" loading="eager" decoding="async" fetchpriority="high" />
    </figure>
<?php endif; ?>

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

    <div class="prose-shiro" data-pagefind-body id="article-body">
        <?php
        ob_start();
        $this->content();
        $postContent = ob_get_clean();
        echo shiro_enhance_content_images($postContent, true);
        ?>
    </div>

    <!-- Export & Copyright -->
    <div class="mt-8 flex items-center gap-3 text-sm">
        <button id="exportMd" class="focus-elegant btn-ink text-xs" title="导出 Markdown">
            <i class="iconfont icon-external"></i> 导出 Markdown
        </button>
        <button id="copyMd" class="focus-elegant btn-ink text-xs" title="复制 Markdown">
            <i class="iconfont icon-book"></i> 复制 Markdown
        </button>
    </div>

    <!-- Tags -->
<?php if (!empty($tagItems)): ?>
    <div class="mt-8 flex flex-wrap gap-2">
<?php foreach ($tagItems as $tag): ?>
        <a <?php echo shiro_link_attrs($tag['url']); ?> class="tag-pill">
            <span class="size-1.5 rounded-full bg-slate-400 mr-2"></span>
            <?php echo shiro_escape($tag['name']); ?>
        </a>
<?php endforeach; ?>
    </div>
<?php endif; ?>

    <!-- Related Posts -->
<?php
$relatedPosts = shiro_related_posts($this->cid, $tagNames);
if (!empty($relatedPosts)):
?>
    <div class="section-divider">
        <h3 class="text-base font-medium text-slate-700 mb-4"><?php echo shiro_lang('post.related'); ?></h3>
        <ul class="space-y-2">
<?php foreach ($relatedPosts as $rp): ?>
            <li class="flex items-baseline gap-3">
                <time class="flex-shrink-0 text-xs text-slate-400 tabular-nums"><?php echo date('m-d', $rp['created']); ?></time>
                <a href="<?php echo shiro_escape(shiro_content_url($rp)); ?>" class="text-slate-700 hover:text-seal transition-colors"><?php echo shiro_escape($rp['title']); ?></a>
            </li>
<?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

    <!-- Post Navigation -->
    <div class="section-divider flex justify-between items-center text-sm md:text-base">
        <?php echo shiro_adjacent_post_link($this, 'prev'); ?>
        <?php echo shiro_adjacent_post_link($this, 'next'); ?>
    </div>

    <!-- Comments -->
    <?php $this->need('comments.php'); ?>
</article>

<!-- Post metadata for JS -->
<script>
window.__postMeta = {
    title: <?php echo shiro_json($this->title); ?>,
    url: <?php echo shiro_json(shiro_permalink_url($this)); ?>,
    author: <?php echo shiro_json($authorName); ?>,
    siteName: <?php echo shiro_json($options->title); ?>
};
</script>

<?php $this->need('footer.php'); ?>
