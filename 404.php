<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<article class="content-article">
    <header class="text-center mb-8">
        <h1 class="page-title mb-4">404</h1>
    </header>
    <div class="prose-shiro text-center py-12">
        <p class="text-slate-500 text-lg"><?php echo shiro_lang('empty'); ?></p>
        <p class="mt-4">
            <a href="<?php echo shiro_escape(shiro_site_url()); ?>" class="focus-elegant btn-ink">
                &larr; <?php echo shiro_lang('nav.home'); ?>
            </a>
        </p>
    </div>
</article>

<?php $this->need('footer.php'); ?>
