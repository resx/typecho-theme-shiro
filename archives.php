<?php
/**
 * 归档
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');

$db = Typecho_Db::get();
$prefix = $db->getPrefix();

$rows = $db->fetchAll($db->select('cid', 'title', 'slug', 'created', 'type')
    ->from($prefix . 'contents')
    ->where('status = ?', 'publish')
    ->where('type = ?', 'post')
    ->order('created', Typecho_Db::SORT_DESC));

$yearStats = [];
foreach ($rows as $row) {
    $year = date('Y', $row['created']);
    if (!isset($yearStats[$year])) $yearStats[$year] = 0;
    $yearStats[$year]++;
}
?>

<div class="archive-page">
    <header class="text-center mb-8">
        <h1 class="page-title"><?php echo shiro_lang('nav.archives'); ?></h1>
        <p class="text-sm text-slate-500 mt-2"><?php echo count($rows); ?> <?php echo shiro_lang('archive.posts_total'); ?> · <?php echo count($yearStats); ?> 年</p>
    </header>

<?php if (!empty($rows)): ?>
    <nav class="archive-year-nav" aria-label="Archive years">
<?php foreach ($yearStats as $year => $count): ?>
        <a href="#year-<?php echo shiro_escape($year); ?>">
            <span><?php echo shiro_escape($year); ?></span>
            <small><?php echo (int)$count; ?></small>
        </a>
<?php endforeach; ?>
    </nav>

    <div class="space-y-12 max-w-2xl mx-auto">
    <?php $currentYear = ''; ?>
    <?php foreach ($rows as $row): ?>
        <?php $year = date('Y', $row['created']); ?>
        <?php if ($year !== $currentYear): ?>
            <?php if ($currentYear !== ''): ?>
            </div></div>
            <?php endif; ?>
            <div id="year-<?php echo shiro_escape($year); ?>">
            <h2 class="section-heading">
                <i class="iconfont icon-calendar text-slate-400"></i>
                <?php echo $year; ?>
            </h2>
            <div class="space-y-4">
            <?php $currentYear = $year; ?>
        <?php endif; ?>
        <?php
        $row['year'] = date('Y', $row['created']);
        $row['month'] = date('m', $row['created']);
        $row['day'] = date('d', $row['created']);
        if (!isset($row['category'])) $row['category'] = '';
        if (!isset($row['directory'])) $row['directory'] = '';
        ?>
        <article class="archive-item group">
            <time class="archive-date"><?php echo date('Y/m/d', $row['created']); ?></time>
            <h3 class="archive-title">
                <a href="<?php echo shiro_escape(Typecho_Router::url('post', $row, Helper::options()->index)); ?>" class="link-underline"><?php echo shiro_escape($row['title']); ?></a>
            </h3>
        </article>
    <?php endforeach; ?>
    <?php if ($currentYear !== ''): ?>
    </div></div>
    <?php endif; ?>
    </div>
<?php else: ?>
    <div class="text-center py-12 text-slate-500">
        <p><?php echo shiro_lang('empty'); ?></p>
    </div>
<?php endif; ?>
</div>

<?php $this->need('footer.php'); ?>
