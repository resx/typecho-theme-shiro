<?php
/**
 * 分类
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');

$db = Typecho_Db::get();
$prefix = $db->getPrefix();

// Get all categories with posts
$cats = $db->fetchAll(
    $db->select('mid', 'name', 'slug', 'count')
        ->from($prefix . 'metas')
        ->where('type = ?', 'category')
        ->order('order', Typecho_Db::SORT_ASC)
);

$postsByCat = [];
if (!empty($cats)) {
    $catIds = array_map('intval', array_column($cats, 'mid'));
    $catIds = array_filter($catIds);

    if (!empty($catIds)) {
        $rows = $db->fetchAll(
            $db->select('r.mid', 'c.cid', 'c.title', 'c.slug', 'c.created', 'c.type')
                ->from($prefix . 'contents c')
                ->join($prefix . 'relationships r', 'c.cid = r.cid')
                ->where('r.mid IN (' . implode(',', $catIds) . ')')
                ->where('c.status = ?', 'publish')
                ->where('c.type = ?', 'post')
                ->order('c.created', Typecho_Db::SORT_DESC)
        );

        foreach ($rows as $row) {
            $postsByCat[(int)$row['mid']][] = $row;
        }
    }
}
?>

<div class="categories-page">
    <header class="text-center mb-8">
        <h1 class="page-title"><?php echo shiro_lang('nav.categories'); ?></h1>
    </header>

<?php if (!empty($cats)): ?>
    <div class="space-y-12 max-w-2xl mx-auto">
<?php foreach ($cats as $cat):
    $posts = isset($postsByCat[(int)$cat['mid']]) ? $postsByCat[(int)$cat['mid']] : [];
    if (empty($posts)) continue;
?>
        <div id="cat-<?php echo shiro_escape($cat['slug']); ?>">
            <h2 class="section-heading">
                <i class="iconfont icon-folder text-slate-400"></i>
                <?php echo shiro_escape($cat['name']); ?>
                <span class="text-sm text-slate-400 font-normal ml-1">(<?php echo (int)$cat['count']; ?>)</span>
            </h2>
            <div class="space-y-3 pl-6 border-l-2 border-slate-100">
<?php foreach ($posts as $post):
    $post['year'] = date('Y', $post['created']);
    $post['month'] = date('m', $post['created']);
    $post['day'] = date('d', $post['created']);
    $post['category'] = $cat['slug'];
    $post['directory'] = '';
?>
                <article class="archive-item group">
                    <time class="archive-date"><?php echo date('Y/m/d', $post['created']); ?></time>
                    <h3 class="archive-title">
                        <a href="<?php echo shiro_escape(Typecho_Router::url('post', $post, Helper::options()->index)); ?>" class="link-underline"><?php echo shiro_escape($post['title']); ?></a>
                    </h3>
                </article>
<?php endforeach; ?>
            </div>
        </div>
<?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-12 text-slate-500">
        <p><?php echo shiro_lang('empty'); ?></p>
    </div>
<?php endif; ?>
</div>

<?php $this->need('footer.php'); ?>
