<?php
/**
 * 友链
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');

$links = shiro_get_links();
?>

<article class="content-article">
    <header class="text-center mb-8">
        <h1 class="page-title"><?php echo shiro_escape($this->title); ?></h1>
<?php if (!empty($links)): ?>
        <p class="text-sm text-slate-500 mt-2"><?php echo count($links); ?> links</p>
<?php endif; ?>
    </header>

<?php if ($this->content): ?>
    <div class="prose-shiro mb-8">
        <?php
        ob_start();
        $this->content();
        echo shiro_enhance_content_images(ob_get_clean(), true);
        ?>
    </div>
<?php endif; ?>

<?php if (!empty($links)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<?php foreach ($links as $link): ?>
        <a <?php echo shiro_link_attrs($link['url'] ?? '', '_blank'); ?> class="group flex items-center gap-4 rounded-lg border border-slate-100 px-4 py-3 transition-colors hover:border-seal/20 hover:bg-seal/3">
<?php if (!empty($link['avatar'])): ?>
            <img src="<?php echo shiro_escape($link['avatar']); ?>" alt="" class="size-10 rounded-full object-cover flex-shrink-0" loading="lazy" decoding="async" />
<?php else: ?>
            <div class="size-10 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 text-slate-400 text-sm font-medium">
                <?php echo shiro_escape(mb_substr($link['name'] ?? '?', 0, 1)); ?>
            </div>
<?php endif; ?>
            <div class="min-w-0">
                <div class="font-medium text-slate-700 group-hover:text-seal transition-colors truncate"><?php echo shiro_escape($link['name'] ?? ''); ?></div>
<?php if (!empty($link['desc'])): ?>
                <div class="text-sm text-slate-500 truncate"><?php echo shiro_escape($link['desc']); ?></div>
<?php endif; ?>
            </div>
        </a>
<?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-8 text-slate-500">
        <p><?php echo shiro_lang('empty'); ?></p>
    </div>
<?php endif; ?>
</article>

<?php $this->need('footer.php'); ?>
