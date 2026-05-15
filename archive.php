<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php
$options = Helper::options();

// Determine archive type for title
$archiveTitle = '';
$archiveIcon = 'icon-archive';
if ($this->is('category')) {
    $archiveTitle = shiro_lang('nav.categories') . ': ' . $this->getArchiveTitle();
    $archiveIcon = 'icon-folder';
} elseif ($this->is('tag')) {
    $archiveTitle = shiro_lang('nav.tags') . ': ' . $this->getArchiveTitle();
    $archiveIcon = 'icon-tag';
} elseif ($this->is('search')) {
    $archiveTitle = shiro_lang('search.button') . ': ' . $this->getArchiveTitle();
    $archiveIcon = 'icon-search';
} elseif ($this->is('author')) {
    $archiveTitle = $this->getArchiveTitle();
    $archiveIcon = 'icon-user';
} else {
    $archiveTitle = shiro_lang('nav.archives');
}
?>

<div class="archive-page">
    <header class="text-center mb-8">
        <h1 class="page-title"><?php echo shiro_escape($archiveTitle); ?></h1>
    </header>

<?php if ($this->have()): ?>
    <div class="space-y-12 max-w-2xl mx-auto">
    <?php $currentYear = ''; ?>
    <?php while ($this->next()): ?>
        <?php $year = $this->date('Y'); ?>
        <?php if ($year !== $currentYear): ?>
            <?php if ($currentYear !== ''): ?>
            </div></div>
            <?php endif; ?>
            <div>
            <h2 class="section-heading">
                <i class="iconfont icon-calendar text-slate-400"></i>
                <?php echo $year; ?>
            </h2>
            <div class="space-y-4">
            <?php $currentYear = $year; ?>
        <?php endif; ?>
        <article class="archive-item group">
            <time class="archive-date"><?php $this->date('Y/m/d'); ?></time>
            <h3 class="archive-title">
                <a href="<?php echo shiro_escape(shiro_permalink_url($this)); ?>" class="link-underline"><?php echo shiro_escape($this->title); ?></a>
            </h3>
        </article>
    <?php endwhile; ?>
    <?php if ($currentYear !== ''): ?>
    </div></div>
    <?php endif; ?>
    </div>
<?php else: ?>
    <div class="text-center py-12 text-slate-500">
        <p><?php echo shiro_lang('empty'); ?></p>
    </div>
<?php endif; ?>

    <!-- Pagination -->
    <?php $this->pageNav(shiro_lang('page.prev'), shiro_lang('page.next'), 1, '...'); ?>
</div>

<?php $this->need('footer.php'); ?>
