<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$options = Helper::options();
$sealEnabled = isset($options->sealEnabled) ? $options->sealEnabled : '1';
$sealText = isset($options->sealText) ? $options->sealText : '白';
$darkDefault = isset($options->darkModeDefault) ? $options->darkModeDefault : 'light';
$darkDefault = in_array($darkDefault, ['system', 'light', 'dark'], true) ? $darkDefault : 'light';
$darkToggle = isset($options->darkModeToggle) ? $options->darkModeToggle : '1';
$searchEnabled = isset($options->searchEnabled) ? $options->searchEnabled : '1';
$rssEnabled = isset($options->rssEnabled) ? $options->rssEnabled : '0';
$progressBar = isset($options->progressBar) ? $options->progressBar : '1';
$faviconMode = isset($options->faviconMode) ? $options->faviconMode : 'seal';
$faviconMode = $faviconMode === 'custom' ? 'custom' : 'seal';
$faviconUrl = shiro_safe_url(isset($options->faviconUrl) ? $options->faviconUrl : '/favicon.ico', '/favicon.ico');
$menuItems = shiro_get_menu();
$currentPath = $this->request->getRequestUri();
$pageTitle = shiro_build_page_title($this);
$cleanDesc = shiro_clean_description($this);
$ogImg = shiro_og_image($this);
$siteUrl = shiro_site_url();
$feedUrl = shiro_feed_url();
$currentPermalink = shiro_permalink_url($this);
$authorName = shiro_author_name($this);
?>
<!doctype html>
<html lang="<?php echo shiro_escape($options->lang ?: 'zh-CN'); ?>" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <meta name="generator" content="Typecho" />
    <!-- FOUC prevention -->
    <script>(function(){var d=<?php echo shiro_json($darkDefault); ?>;window.__themeDefault=d;var s=d==='system'?['system','light','dark']:['light','dark'];var sv;try{sv=localStorage.getItem('theme')}catch(e){}if(!sv||s.indexOf(sv)===-1)sv=d;var h=document.documentElement;h.setAttribute('data-theme-state',sv);var k=sv==='dark'||(sv!=='light'&&window.matchMedia('(prefers-color-scheme:dark)').matches);if(k)h.classList.add('dark');h.style.colorScheme=k?'dark':'light';})()</script>
    <?php $googleFontsUrl = "https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&display=swap&family=Yuji+Syuku&display=optional&family=Zen+Old+Mincho:wght@400;600&display=swap&family=Noto+Serif+JP:wght@400;600&display=swap&family=Noto+Serif+SC:wght@400;600&display=swap&family=Cormorant+Garamond:wght@400;600&display=swap&family=Fira+Code:wght@400;500&display=swap"; ?>
    <link rel="stylesheet" href="<?php echo shiro_escape($googleFontsUrl); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Title -->
    <title><?php echo shiro_escape($pageTitle); ?></title>

    <!-- SEO Meta -->
<?php if ($cleanDesc): ?>
    <meta name="description" content="<?php echo shiro_escape($cleanDesc); ?>" />
<?php endif; ?>
    <meta name="author" content="<?php echo shiro_escape($authorName ?: $options->title); ?>" />

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo shiro_escape($pageTitle); ?>" />
<?php if ($cleanDesc): ?>
    <meta property="og:description" content="<?php echo shiro_escape($cleanDesc); ?>" />
<?php endif; ?>
    <meta property="og:url" content="<?php echo shiro_escape($currentPermalink); ?>" />
    <meta property="og:type" content="<?php echo $this->is('post') ? 'article' : 'website'; ?>" />
    <meta property="og:site_name" content="<?php echo shiro_escape($options->title); ?>" />
<?php if ($ogImg): ?>
    <meta property="og:image" content="<?php echo shiro_escape($ogImg); ?>" />
<?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="<?php echo $ogImg ? 'summary_large_image' : 'summary'; ?>" />
    <meta name="twitter:title" content="<?php echo shiro_escape($pageTitle); ?>" />
<?php if ($cleanDesc): ?>
    <meta name="twitter:description" content="<?php echo shiro_escape($cleanDesc); ?>" />
<?php endif; ?>
<?php if ($ogImg): ?>
    <meta name="twitter:image" content="<?php echo shiro_escape($ogImg); ?>" />
<?php endif; ?>

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo shiro_escape($currentPermalink); ?>" />

    <!-- Favicon -->
<?php if ($faviconMode === 'seal'): ?>
    <link rel="icon" href="data:image/svg+xml,<?php echo rawurlencode(shiro_favicon_svg($sealText)); ?>" />
<?php else: ?>
    <link rel="icon" href="<?php echo shiro_escape($faviconUrl); ?>" />
<?php endif; ?>

    <!-- RSS -->
<?php if ($rssEnabled === '1'): ?>
    <link rel="alternate" type="application/rss+xml" title="<?php echo shiro_escape($options->title); ?>" href="<?php echo shiro_escape($feedUrl); ?>" />
<?php endif; ?>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo themeAsset('assets/css/style.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo themeAsset('assets/css/iconfont.css'); ?>" />
    <link rel="stylesheet" href="<?php echo themeAsset('assets/css/typecho-patch.css'); ?>" />
<?php
$themeColor = shiro_theme_color(isset($options->themeColor) ? $options->themeColor : '');
$customCSS = isset($options->customCSS) ? htmlspecialchars_decode($options->customCSS) : '';
if (!empty($themeColor) && $themeColor !== '#b0171a'): ?>
    <style>:root{--color-seal:<?php echo shiro_escape($themeColor); ?>}</style>
<?php endif; ?>
<?php if (!empty($customCSS)): ?>
    <style><?php echo $customCSS; ?></style>
<?php endif; ?>

<?php if ($this->is('post')): ?>
    <?php
    $ldDatePublished = date('c', $this->created);
    $ldDateModified = date('c', $this->modified);
    ?>
    <script type="application/ld+json"><?php echo shiro_json([
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $this->title,
        'datePublished' => $ldDatePublished,
        'dateModified' => $ldDateModified,
        'author' => ['@type' => 'Person', 'name' => $authorName],
        'publisher' => ['@type' => 'Organization', 'name' => $options->title],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $currentPermalink],
        'image' => $ogImg ?: null,
    ]); ?></script>
    <script type="application/ld+json"><?php echo shiro_json([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => $options->title, 'item' => $siteUrl],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $this->title],
        ],
    ]); ?></script>
<?php elseif ($this->is('index')): ?>
    <script type="application/ld+json"><?php echo shiro_json([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $options->title,
        'url' => $siteUrl,
        'description' => $options->description,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => rtrim($siteUrl, '/') . '/?s={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ]); ?></script>
<?php endif; ?>
</head>

<body class="h-full fog-bg text-slate-800">
<?php if ($sealEnabled === '1'): ?>
    <!-- SVG Filters for Seal -->
    <svg width="0" height="0" class="absolute pointer-events-none" aria-hidden="true">
        <defs>
            <filter id="seal-roughness" x="-20%" y="-20%" width="140%" height="140%">
                <feTurbulence type="turbulence" baseFrequency="0.05" numOctaves="2" result="noise" />
                <feDisplacementMap in="SourceGraphic" in2="noise" scale="3" />
            </filter>
            <filter id="text-erosion">
                <feTurbulence type="fractalNoise" baseFrequency="0.15" numOctaves="1" result="noise" />
                <feDisplacementMap in="SourceGraphic" in2="noise" scale="1.5" />
            </filter>
        </defs>
    </svg>
<?php endif; ?>

<?php if ($progressBar === '1' && ($this->is('post') || $this->is('page'))): ?>
    <div id="progressBar" class="progress-bar" aria-hidden="true"></div>
<?php endif; ?>

    <div class="min-h-full px-4 pt-14 pb-6 md:pb-5">
        <!-- Paper Card -->
        <div class="paper relative mx-auto w-full max-w-4xl rounded-xl px-5 sm:px-8 md:px-12 lg:px-14 pt-10 pb-6 md:pt-12 md:pb-8">

            <!-- Header -->
            <header class="relative" data-pagefind-ignore>
                <div class="relative flex items-start justify-center">
                    <div class="text-center">
                        <div class="relative inline-block">
<?php if ($this->is('index')): ?>
                            <h1 class="site-title">
                                <a href="<?php echo shiro_escape($siteUrl); ?>"><?php echo shiro_escape($options->title); ?></a>
                            </h1>
<?php else: ?>
                            <p class="site-title">
                                <a href="<?php echo shiro_escape($siteUrl); ?>"><?php echo shiro_escape($options->title); ?></a>
                            </p>
<?php endif; ?>
<?php if ($sealEnabled === '1'): ?>
                            <!-- Seal -->
                            <div class="absolute left-full top-1/2 -translate-y-1/2 ml-1 sm:ml-4 origin-left scale-75 sm:scale-110">
                                <svg width="52" height="52" viewBox="0 0 100 100" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15,12 Q50,5 85,12 Q95,50 88,88 Q50,95 12,88 Q5,50 15,12 Z" fill="var(--color-seal)" filter="url(#seal-roughness)" opacity="0.92" />
                                    <text x="50" y="50" text-anchor="middle" dominant-baseline="central" font-size="42" fill="rgba(255,255,255,0.92)" filter="url(#text-erosion)" class="seal-text select-none"><?php echo shiro_escape($sealText); ?></text>
                                </svg>
                            </div>
<?php endif; ?>
                        </div>
<?php if ($options->description): ?>
                        <p class="site-subtitle"><?php echo shiro_escape($options->description); ?></p>
<?php endif; ?>
                        <div class="mt-3 sm:mt-4 flex justify-center gap-2">
<?php if ($rssEnabled === '1'): ?>
                            <a class="group header-pill-btn" href="<?php echo shiro_escape($feedUrl); ?>" target="_blank">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="6.18" cy="17.82" r="2.18"/><path d="M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56zm0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9z"/></svg>
                                <span>RSS</span>
                            </a>
<?php endif; ?>
<?php if ($darkToggle === '1'): ?>
                            <button id="themeToggle" class="group header-pill-btn" aria-label="<?php echo shiro_escape(shiro_lang('theme.' . $darkDefault)); ?>" data-label-system="<?php echo shiro_escape(shiro_lang('theme.system')); ?>" data-label-light="<?php echo shiro_escape(shiro_lang('theme.light')); ?>" data-label-dark="<?php echo shiro_escape(shiro_lang('theme.dark')); ?>">
                                <svg data-icon="system" class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" stroke-width="1.5"/><path d="M8 21h8M12 17v4" stroke-width="1.5" stroke-linecap="round"/></svg>
                                <svg data-icon="light" class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4" stroke-width="1.5"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke-width="1.5" stroke-linecap="round"/></svg>
                                <svg data-icon="dark" class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
<?php endif; ?>
<?php if ($searchEnabled === '1'): ?>
                            <button id="searchToggle" class="group header-pill-btn" aria-label="<?php echo shiro_escape(shiro_lang('search.button')); ?>">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <span class="sr-only"><?php echo shiro_lang('search.button'); ?></span>
                            </button>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Navigation -->
<?php if (!empty($menuItems)): ?>
            <nav class="mt-8 md:mt-10" aria-label="<?php echo shiro_escape(shiro_lang('nav.menu')); ?>" data-pagefind-ignore>
                <!-- Mobile Toggle -->
                <div class="flex items-center justify-center md:hidden">
                    <button id="menuBtn" class="focus-elegant inline-flex items-center gap-2 rounded-full border border-slate-200 bg-paper/95 px-4 py-2 text-sm text-slate-700 shadow-xs" aria-expanded="false" aria-controls="mobileMenu">
                        <span class="text-base leading-none tracking-widest"><?php echo shiro_lang('nav.menu'); ?></span>
                        <svg id="menuChevron" class="size-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:block">
                    <ul class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-base text-slate-700 px-4">
<?php foreach ($menuItems as $i => $item):
    $isActive = ($item['url'] === '/' && $this->is('index')) || ($item['url'] !== '/' && strpos($currentPath, rtrim($item['url'], '/')) === 0);
?>
                        <li><a class="focus-elegant inline-flex items-center gap-1.5 rounded-md px-2 py-1 hover:text-seal transition-colors<?php if ($isActive): ?> text-seal<?php endif; ?>" <?php echo shiro_link_attrs($item['url'], isset($item['target']) ? $item['target'] : ''); ?>><?php if (!empty($item['icon'])): ?><i class="iconfont <?php echo shiro_escape($item['icon']); ?>"></i><?php endif; ?><?php echo shiro_escape($item['name']); ?></a></li>
<?php if ($i < count($menuItems) - 1): ?>
                        <li class="nav-divider text-slate-400 select-none" role="separator" aria-hidden="true">/</li>
<?php endif; ?>
<?php endforeach; ?>
                    </ul>
                </div>

                <!-- Mobile Menu Panel -->
                <div id="mobileMenu" class="menu-panel md:hidden mt-4" data-open="false">
                    <ul class="mx-auto max-w-sm max-h-menu overflow-y-auto rounded-2xl border border-slate-200 bg-paper/95 shadow-xs">
<?php foreach ($menuItems as $i => $item):
    $isActive = ($item['url'] === '/' && $this->is('index')) || ($item['url'] !== '/' && strpos($currentPath, rtrim($item['url'], '/')) === 0);
?>
                        <li><a class="focus-elegant flex items-center justify-center gap-2 px-6 py-3 text-slate-800 hover:text-seal hover:bg-seal/5 transition-colors<?php if ($isActive): ?> text-seal<?php endif; ?>" <?php echo shiro_link_attrs($item['url'], isset($item['target']) ? $item['target'] : ''); ?>><?php if (!empty($item['icon'])): ?><i class="iconfont <?php echo shiro_escape($item['icon']); ?>"></i><?php endif; ?><?php echo shiro_escape($item['name']); ?></a></li>
<?php if ($i < count($menuItems) - 1): ?>
                        <li class="h-px bg-slate-200/70" role="separator" aria-hidden="true"></li>
<?php endif; ?>
<?php endforeach; ?>
                    </ul>
                </div>
            </nav>
<?php endif; ?>

            <!-- Main Content -->
            <main class="section-divider">
