<?php
/**
 * 标签
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');

$db = Typecho_Db::get();
$prefix = $db->getPrefix();
$tagRows = $db->fetchAll(
    $db->select('mid', 'name', 'slug', 'count')
        ->from($prefix . 'metas')
        ->where('type = ?', 'tag')
        ->where('count > ?', 0)
        ->order('count', Typecho_Db::SORT_DESC)
);
$maxCount = 1;
foreach ($tagRows as $tag) {
    $maxCount = max($maxCount, (int)$tag['count']);
}
?>

<div class="tags-page">
    <header class="text-center mb-8">
        <h1 class="page-title"><?php echo shiro_lang('nav.tags'); ?></h1>
<?php if (!empty($tagRows)): ?>
        <p class="text-sm text-slate-500 mt-2"><?php echo count($tagRows); ?> tags</p>
<?php endif; ?>
    </header>

<?php if (!empty($tagRows)): ?>
    <div class="tag-cloud">
<?php foreach ($tagRows as $tag):
    $weight = 0.85 + min(0.45, ((int)$tag['count'] / $maxCount) * 0.45);
    $url = Typecho_Common::url('tag/' . rawurlencode($tag['slug']) . '/', Helper::options()->index);
?>
        <a href="<?php echo shiro_escape($url); ?>" class="tag-pill tag-cloud-item" style="--tag-weight:<?php echo shiro_escape(number_format($weight, 2, '.', '')); ?>">
            <span>#<?php echo shiro_escape($tag['name']); ?></span>
            <span class="text-xs text-slate-400">(<?php echo (int)$tag['count']; ?>)</span>
        </a>
<?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-12 text-slate-500">
        <p><?php echo shiro_lang('empty'); ?></p>
    </div>
<?php endif; ?>
</div>

<?php $this->need('footer.php'); ?>
