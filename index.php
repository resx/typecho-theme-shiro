<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php
$options = Helper::options();
$excerptLength = isset($options->excerptLength) ? (int)$options->excerptLength : 200;
?>

<?php if ($this->have()): ?>
<?php $postIndex = 0; ?>
<?php while ($this->next()): ?>
<?php $postCover = shiro_post_cover($this, false); ?>
<?php if ($postIndex > 0): ?>
    <div class="my-8 flex items-center justify-center gap-3" aria-hidden="true">
        <div class="h-px flex-1 bg-linear-to-r from-transparent to-slate-300 max-w-divider"></div>
        <svg class="h-4 w-10 shrink-0 text-slate-300" viewBox="0 0 40 10">
            <circle cx="20" cy="5" r="3" fill="currentColor" opacity="0.9" />
            <circle cx="4" cy="5" r="1.5" fill="currentColor" opacity="0.6" />
            <circle cx="36" cy="5" r="1.5" fill="currentColor" opacity="0.6" />
        </svg>
        <div class="h-px flex-1 bg-linear-to-l from-transparent to-slate-300 max-w-divider"></div>
    </div>
<?php endif; ?>
    <article class="content-article">
        <header class="text-center mb-4">
            <h2 class="card-title">
                <a href="<?php echo shiro_escape(shiro_permalink_url($this)); ?>"><?php echo shiro_escape($this->title); ?></a>
            </h2>
            <div class="meta-line text-sm text-slate-500 mt-2">
                <time datetime="<?php $this->date('c'); ?>"><?php $this->date(); ?></time>
                <span class="mx-1.5 text-slate-300">·</span>
                <?php echo shiro_category_links($this, ', '); ?>
            </div>
        </header>
<?php if ($postCover): ?>
        <a class="post-card-cover" href="<?php echo shiro_escape(shiro_permalink_url($this)); ?>" aria-hidden="true" tabindex="-1">
            <img src="<?php echo shiro_escape($postCover); ?>" alt="" loading="<?php echo $postIndex === 0 ? 'eager' : 'lazy'; ?>" decoding="async"<?php if ($postIndex === 0): ?> fetchpriority="high"<?php endif; ?> />
        </a>
<?php endif; ?>
        <div class="prose-shiro">
            <?php
            $showReadMore = false;
            if ($this->hidden): ?>
                <p><?php echo shiro_lang('post.password'); ?></p>
            <?php else:
                $fullContent = $this->content;
                $rawText = $this->text;
                $morePos = strpos($rawText, '<!--more-->');
                if ($morePos !== false) {
                    // Typecho inserts an anchor at <!--more--> position
                    // Look for the more marker in rendered HTML
                    $moreMarker = '<!--more-->';
                    $htmlMorePos = strpos($fullContent, $moreMarker);
                    if ($htmlMorePos !== false) {
                        echo shiro_enhance_content_images(substr($fullContent, 0, $htmlMorePos));
                    } else {
                        // Fallback: output content and let CSS handle overflow
                        echo shiro_enhance_content_images($fullContent);
                    }
                    $showReadMore = true;
                } else {
                    $plainLen = mb_strlen(strip_tags($fullContent));
                    if ($plainLen > $excerptLength) {
                        // Output full rendered content but truncate visually
                        // Use a helper to safely truncate HTML
                        echo shiro_enhance_content_images(shiro_truncate_html($fullContent, $excerptLength));
                        $showReadMore = true;
                    } else {
                        echo shiro_enhance_content_images($fullContent);
                    }
                }
            endif; ?>
        </div>
<?php if ($showReadMore): ?>
        <div class="mt-6 text-center">
            <a href="<?php echo shiro_escape(shiro_permalink_url($this)); ?>" class="focus-elegant btn-ink">
                <?php echo shiro_lang('index.read_more'); ?>
            </a>
        </div>
<?php endif; ?>
    </article>
<?php $postIndex++; ?>
<?php endwhile; ?>
<?php else: ?>
    <div class="text-center py-12 text-slate-500">
        <p><?php echo shiro_lang('empty'); ?></p>
    </div>
<?php endif; ?>

    <!-- Pagination -->
<?php $this->pageNav(shiro_lang('page.prev'), shiro_lang('page.next'), 1, '...'); ?>

<?php $this->need('footer.php'); ?>
