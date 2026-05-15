<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Shiro Theme for Typecho
 * A pure and simple theme migrated from Hexo.
 *
 * @package Shiro
 * @author Acris
 * @version 1.0.0
 * @link https://github.com/Acris/hexo-theme-shiro
 */

// ─── i18n ────────────────────────────────────────────────────────────────────

function shiro_lang($key)
{
    static $strings = null;
    if ($strings === null) {
        $lang = defined('LANG') ? LANG : 'zh_CN';
        $file = __DIR__ . '/languages/' . $lang . '.php';
        if (!file_exists($file)) {
            $file = __DIR__ . '/languages/zh_CN.php';
        }
        $strings = include $file;
    }
    return isset($strings[$key]) ? $strings[$key] : $key;
}

// ─── Asset Helper ────────────────────────────────────────────────────────────

function themeAsset($path)
{
    static $versions = [];

    $path = ltrim($path, '/');
    if (!isset($versions[$path])) {
        $file = __DIR__ . '/' . $path;
        $versions[$path] = is_file($file) ? (string)filemtime($file) : '';
    }

    $ver = $versions[$path];
    $base = Helper::options()->themeUrl . '/' . $path;
    return $ver ? $base . '?v=' . $ver : $base;
}

// ─── Utility Functions ───────────────────────────────────────────────────────

function shiro_escape($value)
{
    return htmlspecialchars(shiro_decode_entities($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function shiro_decode_entities($value)
{
    return html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function shiro_escape_xml($value)
{
    return htmlspecialchars(shiro_decode_entities($value), ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
}

function shiro_capture_method($object, $method, array $args = [])
{
    if (!is_object($object) || !method_exists($object, $method)) {
        return '';
    }

    ob_start();
    $result = call_user_func_array([$object, $method], $args);
    $output = ob_get_clean();

    return $output !== '' ? $output : (string)$result;
}

function shiro_normalize_html_text($html)
{
    $tokens = preg_split('/(<[^>]+>)/', (string)$html, -1, PREG_SPLIT_DELIM_CAPTURE);
    if ($tokens === false) return '';

    foreach ($tokens as $i => $token) {
        if ($token === '' || $token[0] === '<') continue;
        $tokens[$i] = shiro_escape($token);
    }

    return implode('', $tokens);
}

function shiro_plain_text($value)
{
    $text = strip_tags(shiro_decode_entities($value));
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function shiro_html_attr($attrs, $name)
{
    $pattern = '/\b' . preg_quote($name, '/') . '\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s"\'<>]+))/i';
    if (!preg_match($pattern, (string)$attrs, $match)) {
        return '';
    }

    if (isset($match[2]) && $match[2] !== '') return $match[2];
    if (isset($match[3]) && $match[3] !== '') return $match[3];
    return isset($match[4]) ? $match[4] : '';
}

function shiro_footer_html($value)
{
    $html = shiro_decode_entities($value);
    if (trim($html) === '') return '';

    $tokens = preg_split('/(<\s*\/?\s*[a-z][^>]*>)/iu', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    if ($tokens === false) return shiro_escape($html);

    $output = '';
    foreach ($tokens as $token) {
        if ($token === '') continue;

        if ($token[0] !== '<') {
            $output .= shiro_escape($token);
            continue;
        }

        if (preg_match('/^<\s*\/\s*(a|strong|b|em|i|code)\s*>$/i', $token, $match)) {
            $output .= '</' . strtolower($match[1]) . '>';
            continue;
        }

        if (preg_match('/^<\s*br\s*\/?\s*>$/i', $token)) {
            $output .= '<br />';
            continue;
        }

        if (preg_match('/^<\s*(strong|b|em|i|code)\b[^>]*>$/i', $token, $match)) {
            $output .= '<' . strtolower($match[1]) . '>';
            continue;
        }

        if (preg_match('/^<\s*a\b([^>]*)>$/i', $token, $match)) {
            $rawAttrs = $match[1];
            $href = shiro_safe_url(shiro_html_attr($rawAttrs, 'href'), '');
            $attrs = '';

            if ($href) {
                $attrs .= ' href="' . shiro_escape($href) . '"';

                $target = shiro_safe_target(shiro_html_attr($rawAttrs, 'target'));
                if ($target) {
                    $attrs .= ' target="' . shiro_escape($target) . '"';
                    if ($target === '_blank') {
                        $attrs .= ' rel="noopener noreferrer"';
                    }
                }
            }

            $title = shiro_html_attr($rawAttrs, 'title');
            if ($title !== '') {
                $attrs .= ' title="' . shiro_escape($title) . '"';
            }

            $output .= '<a' . $attrs . '>';
        }
    }

    return trim($output);
}

function shiro_icp_beian_url()
{
    return 'https://beian.miit.gov.cn/';
}

function shiro_police_beian_url($text)
{
    $plain = preg_replace('/\s+/', '', shiro_plain_text($text));
    if (preg_match('/\d{8,}/', $plain, $match)) {
        return 'https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . rawurlencode($match[0]);
    }

    return 'https://www.beian.gov.cn/portal/index.do';
}

function shiro_footer_record_link($text, $url)
{
    $text = shiro_plain_text($text);
    if ($text === '') return '';

    return '<a ' . shiro_link_attrs($url, '_blank') . '>' . shiro_escape($text) . '</a>';
}

function shiro_normalize_entities($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = shiro_normalize_entities($value);
        }
        return $data;
    }

    if (is_string($data)) {
        return shiro_decode_entities($data);
    }

    return $data;
}

function shiro_json($data)
{
    return json_encode(
        shiro_normalize_entities($data),
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_APOS
        | JSON_HEX_AMP
        | JSON_HEX_QUOT
    );
}

function shiro_safe_url($url, $fallback = '#')
{
    $url = trim((string)$url);
    if ($url === '' || preg_match('/[\x00-\x1F\x7F]/', $url)) {
        return $fallback;
    }

    $decoded = strtolower(shiro_decode_entities($url));
    if (preg_match('/^\s*(javascript|data|vbscript):/i', $decoded)) {
        return $fallback;
    }

    if (
        preg_match('#^(https?:)?//#i', $url)
        || preg_match('#^(mailto|tel):#i', $url)
        || $url[0] === '/'
        || $url[0] === '#'
        || preg_match('#^[a-z0-9._~!$&\'()*+,;=:@%/-]+$#i', $url)
    ) {
        return $url;
    }

    return $fallback;
}

function shiro_safe_target($target)
{
    $target = strtolower(trim((string)$target));
    return in_array($target, ['_blank', '_self', '_parent', '_top'], true) ? $target : '';
}

function shiro_link_attrs($url, $target = '')
{
    $target = shiro_safe_target($target);
    $attrs = 'href="' . shiro_escape(shiro_safe_url($url)) . '"';
    if ($target) {
        $attrs .= ' target="' . shiro_escape($target) . '"';
        if ($target === '_blank') {
            $attrs .= ' rel="noopener noreferrer"';
        }
    }
    return $attrs;
}

function shiro_theme_color($color)
{
    $color = trim((string)$color);
    return preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color) ? $color : '#b0171a';
}

function shiro_disqus_shortname($shortname)
{
    $shortname = trim((string)$shortname);
    return preg_match('/^[a-z0-9-]+$/i', $shortname) ? $shortname : '';
}

function shiro_google_analytics_id($id)
{
    $id = trim((string)$id);
    return preg_match('/^G-[A-Z0-9-]+$/i', $id) ? $id : '';
}

function shiro_icon_class($icon)
{
    $icon = trim((string)$icon);
    return preg_match('/^icon-[a-z0-9-]+$/i', $icon) ? $icon : '';
}

function shiro_parse_config_lines($raw, $minParts = 2)
{
    $items = [];
    $lines = explode("\n", (string)$raw);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;

        $parts = array_map('trim', str_getcsv($line));
        if (count($parts) < $minParts) continue;
        $items[] = $parts;
    }
    return $items;
}

function shiro_first_image($content)
{
    if (empty($content)) return '';
    if (preg_match('/<img[^>]+src\s*=\s*["\']([^"\']+)["\']/i', $content, $match)) {
        return shiro_safe_url($match[1], '');
    }
    return '';
}

function shiro_clean_description($widget)
{
    $options = Helper::options();
    $raw = '';
    if (method_exists($widget, 'fields') && $widget->fields->description) {
        $raw = $widget->fields->description;
    } elseif (isset($widget->text)) {
        $raw = Typecho_Common::subStr(strip_tags($widget->text), 0, 200, '...');
    }
    if (empty($raw)) {
        $raw = $options->description ?: '';
    }
    $text = preg_replace('/\s+/', ' ', strip_tags($raw));
    $text = trim($text);
    if (empty($text)) return '';
    return mb_strlen($text) > 200 ? mb_substr($text, 0, 200) . '...' : $text;
}

function shiro_copyright_year($since)
{
    $current = date('Y');
    $since = preg_replace('/\D/', '', (string)$since);
    if ($since && (string)$since !== $current) {
        return $since . "\xe2\x80\x93" . $current;
    }
    return $current;
}

function shiro_build_page_title($widget)
{
    $options = Helper::options();
    $siteTitle = $options->title ?: '';

    if ($widget->is('index')) {
        return $siteTitle;
    }
    if (isset($widget->title) && $widget->title) {
        return $widget->title . ' | ' . $siteTitle;
    }
    if ($widget->is('archive')) {
        $prefix = shiro_lang('nav.archives');
        if ($widget->is('category')) {
            $prefix = $widget->getArchiveTitle();
        } elseif ($widget->is('tag')) {
            $prefix = $widget->getArchiveTitle();
        }
        return $prefix . ' | ' . $siteTitle;
    }
    return $siteTitle;
}

function shiro_og_image($widget)
{
    $cover = shiro_post_cover($widget, false);
    if ($cover) return shiro_absolute_url($cover);

    $content = isset($widget->content) ? $widget->content : '';
    $src = shiro_first_image($content);
    if (empty($src)) return '';
    return shiro_absolute_url($src);
}

function shiro_absolute_url($src)
{
    if (strpos($src, '//') === 0) return 'https:' . $src;
    if (!preg_match('#^https?://#', $src)) {
        $base = rtrim(Helper::options()->siteUrl, '/');
        return $base . '/' . ltrim($src, '/');
    }
    return $src;
}

function shiro_field_value($widget, $names)
{
    if (!isset($widget->fields)) return '';
    foreach ((array)$names as $name) {
        if (isset($widget->fields->{$name}) && trim((string)$widget->fields->{$name}) !== '') {
            return trim((string)$widget->fields->{$name});
        }
    }
    return '';
}

function shiro_post_cover($widget, $fallbackToContent = true)
{
    $cover = shiro_field_value($widget, ['cover', 'thumbnail', 'thumb', 'image']);
    if ($cover) return shiro_safe_url($cover, '');

    if ($fallbackToContent) {
        $content = isset($widget->content) ? $widget->content : '';
        return shiro_first_image($content);
    }

    return '';
}

function shiro_prop($source, $key, $default = '')
{
    if (is_array($source) && isset($source[$key])) {
        return $source[$key];
    }
    if (is_object($source) && isset($source->{$key})) {
        return $source->{$key};
    }
    return $default;
}

function shiro_site_url()
{
    return shiro_safe_url(Helper::options()->siteUrl, '/');
}

function shiro_feed_url()
{
    return shiro_safe_url(Helper::options()->feedUrl, shiro_site_url());
}

function shiro_permalink_url($widget)
{
    $url = isset($widget->permalink) ? $widget->permalink : '';
    if (!$url) {
        $url = shiro_capture_method($widget, 'permalink');
    }
    return shiro_safe_url($url, shiro_site_url());
}

function shiro_author_name($widget)
{
    if (isset($widget->author) && is_object($widget->author) && isset($widget->author->screenName)) {
        return $widget->author->screenName;
    }
    return '';
}

function shiro_category_links($widget, $separator = ', ', $class = '')
{
    $items = [];
    $categories = [];

    if (isset($widget->categories) && is_array($widget->categories)) {
        $categories = $widget->categories;
    }

    foreach ($categories as $category) {
        if (!is_array($category) && !is_object($category)) continue;

        $name = shiro_prop($category, 'name');
        if ($name === '') continue;

        $url = shiro_prop($category, 'permalink');
        $slug = shiro_prop($category, 'slug');
        if (!$url && $slug) {
            $url = Typecho_Router::url('category', ['slug' => $slug], Helper::options()->index);
        }

        $attrs = shiro_link_attrs($url ?: '#');
        if ($class) {
            $attrs .= ' class="' . shiro_escape($class) . '"';
        }
        $items[] = '<a ' . $attrs . '>' . shiro_escape($name) . '</a>';
    }

    if (!empty($items)) {
        return implode($separator, $items);
    }

    ob_start();
    $widget->category($separator);
    return shiro_normalize_html_text(ob_get_clean());
}

function shiro_tag_items($widget)
{
    $items = [];
    $tags = [];

    if (isset($widget->tags) && is_array($widget->tags)) {
        $tags = $widget->tags;
    }

    foreach ($tags as $tag) {
        if (!is_array($tag) && !is_object($tag)) continue;

        $name = shiro_prop($tag, 'name');
        if ($name === '') continue;

        $url = shiro_prop($tag, 'permalink');
        $slug = shiro_prop($tag, 'slug');
        if (!$url && $slug) {
            $url = Typecho_Router::url('tag', ['slug' => $slug], Helper::options()->index);
        }
        if (!$url) {
            $url = Typecho_Common::url('tag/' . rawurlencode(shiro_decode_entities($name)) . '/', Helper::options()->index);
        }

        $items[] = [
            'name' => shiro_decode_entities($name),
            'url' => $url,
        ];
    }

    if (!empty($items)) return $items;

    $tagStr = shiro_capture_method($widget, 'tags', ['|||', false, '']);
    foreach (explode('|||', $tagStr) as $tagName) {
        $tagName = trim(shiro_decode_entities($tagName));
        if ($tagName === '') continue;

        $items[] = [
            'name' => $tagName,
            'url' => Typecho_Common::url('tag/' . rawurlencode($tagName) . '/', Helper::options()->index),
        ];
    }

    return $items;
}

function shiro_tag_names($widget)
{
    $names = [];
    foreach (shiro_tag_items($widget) as $tag) {
        $name = trim((string)$tag['name']);
        if ($name !== '') $names[] = $name;
    }
    return $names;
}

function shiro_comment_author($comment)
{
    $name = shiro_prop($comment, 'author');
    if ($name === '') {
        return shiro_normalize_html_text(shiro_capture_method($comment, 'author'));
    }

    $url = shiro_safe_url(shiro_prop($comment, 'url'), '');
    if ($url) {
        return '<a ' . shiro_link_attrs($url, '_blank') . '>' . shiro_escape($name) . '</a>';
    }

    return shiro_escape($name);
}

function shiro_remember_value($widget, $key)
{
    return shiro_escape(shiro_capture_method($widget, 'remember', [$key]));
}

function shiro_adjacent_post($widget, $direction = 'prev')
{
    $created = isset($widget->created) ? (int)$widget->created : 0;
    if (!$created) return null;

    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $isPrev = $direction === 'prev';

    return $db->fetchRow(
        $db->select('cid', 'title', 'slug', 'created', 'type')
            ->from($prefix . 'contents')
            ->where('status = ?', 'publish')
            ->where('type = ?', 'post')
            ->where('created ' . ($isPrev ? '<' : '>') . ' ?', $created)
            ->order('created', $isPrev ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC)
            ->limit(1)
    );
}

function shiro_adjacent_post_link($widget, $direction = 'prev')
{
    $post = shiro_adjacent_post($widget, $direction);
    if (!$post) return '<div></div>';

    $url = shiro_content_url($post);
    $title = shiro_escape($post['title']);

    if ($direction === 'next') {
        return '<a href="' . shiro_escape($url) . '" class="post-nav-link group text-right" title="' . $title . '">'
            . '<span class="post-nav-title">' . $title . '</span> <span aria-hidden="true">&rarr;</span></a>';
    }

    return '<a href="' . shiro_escape($url) . '" class="post-nav-link group text-left" title="' . $title . '">'
        . '<span aria-hidden="true">&larr;</span> <span class="post-nav-title">' . $title . '</span></a>';
}

function shiro_truncate_html($html, $length = 200, $suffix = '...')
{
    $plainLen = mb_strlen(strip_tags($html));
    if ($plainLen <= $length) return $html;

    $count = 0;
    $result = '';
    $openTags = [];
    $tokens = preg_split('/(<[^>]+>)/u', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($tokens as $token) {
        if ($count >= $length) break;

        if (preg_match('/^<\s*\/\s*([a-zA-Z0-9]+)\s*>$/', $token, $m)) {
            // Closing tag
            $result .= $token;
            $idx = array_search(strtolower($m[1]), $openTags);
            if ($idx !== false) array_splice($openTags, $idx, 1);
        } elseif (preg_match('/^<\s*([a-zA-Z0-9]+)([^>]*)>$/', $token, $m)) {
            // Opening tag
            $result .= $token;
            $selfClosing = preg_match('/\/\s*$/', $m[2]) || in_array(strtolower($m[1]), ['br', 'hr', 'img', 'input']);
            if (!$selfClosing) {
                array_unshift($openTags, strtolower($m[1]));
            }
        } else {
            // Text node
            $remaining = $length - $count;
            $textLen = mb_strlen($token);
            if ($textLen <= $remaining) {
                $result .= $token;
                $count += $textLen;
            } else {
                $result .= mb_substr($token, 0, $remaining) . $suffix;
                $count += $remaining;
            }
        }
    }

    // Close any open tags
    foreach ($openTags as $tag) {
        $result .= '</' . $tag . '>';
    }

    return $result;
}

function shiro_word_count($content)
{
    $text = strip_tags($content);
    $text = preg_replace('/\s+/', '', $text);
    return mb_strlen($text);
}

function shiro_enhance_content_images($content, $firstImageEager = false)
{
    if (empty($content) || stripos($content, '<img') === false) {
        return $content;
    }

    $index = 0;
    return preg_replace_callback('/<img\b([^>]*)>/i', function ($matches) use (&$index, $firstImageEager) {
        $index++;
        $attrs = rtrim($matches[1]);
        $selfClosing = false;

        if (substr($attrs, -1) === '/') {
            $selfClosing = true;
            $attrs = rtrim(substr($attrs, 0, -1));
        }

        $extra = '';
        $isFirst = $firstImageEager && $index === 1;

        if (!preg_match('/\sloading\s*=/i', $attrs)) {
            $extra .= ' loading="' . ($isFirst ? 'eager' : 'lazy') . '"';
        }
        if (!preg_match('/\sdecoding\s*=/i', $attrs)) {
            $extra .= ' decoding="async"';
        }
        if ($isFirst && !preg_match('/\sfetchpriority\s*=/i', $attrs)) {
            $extra .= ' fetchpriority="high"';
        }

        return '<img' . $attrs . $extra . ($selfClosing ? ' />' : '>');
    }, $content);
}

function shiro_reading_time($content, $wpm = 400)
{
    $count = shiro_word_count($content);
    $minutes = max(1, ceil($count / $wpm));
    return $minutes;
}

function shiro_related_posts($cid, $tags, $limit = 5)
{
    if (empty($tags)) return [];

    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    $tagNames = is_array($tags) ? $tags : explode(',', $tags);
    $tagNames = array_map('trim', $tagNames);
    $tagNames = array_filter($tagNames);
    if (empty($tagNames)) return [];

    try {
        // Quote tag names manually
        $quotedNames = array_map(function ($n) {
            return "'" . str_replace("'", "''", $n) . "'";
        }, $tagNames);
        $nameList = implode(',', $quotedNames);

        $mids = $db->fetchAll(
            $db->select('mid')->from($prefix . 'metas')
                ->where('type = ?', 'tag')
                ->where("name IN ({$nameList})")
        );
        $midValues = array_column($mids, 'mid');
        if (empty($midValues)) return [];

        // Get related post cids
        $midList = implode(',', array_map('intval', $midValues));
        $rows = $db->fetchAll(
            $db->select('cid')->from($prefix . 'relationships')
                ->where("mid IN ({$midList})")
        );
        // Deduplicate
        $relatedCids = array_unique(array_column($rows, 'cid'));
        $relatedCids = array_values(array_diff($relatedCids, [$cid]));
        if (empty($relatedCids)) return [];

        $cidList = implode(',', array_map('intval', array_slice($relatedCids, 0, $limit * 2)));
        return $db->fetchAll(
            $db->select('cid', 'title', 'slug', 'created', 'type')
                ->from($prefix . 'contents')
                ->where("cid IN ({$cidList})")
                ->where('status = ?', 'publish')
                ->where('type = ?', 'post')
                ->order('created', Typecho_Db::SORT_DESC)
                ->limit($limit)
        );
    } catch (Exception $e) {
        return [];
    }
}

function shiro_excerpt($content, $length = 200)
{
    $text = strip_tags($content);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length) . '...';
    }
    return $text;
}

function shiro_get_menu()
{
    static $items = null;
    if ($items !== null) return $items;

    $options = Helper::options();
    $raw = isset($options->menuItems) ? htmlspecialchars_decode($options->menuItems) : '';
    if (empty(trim($raw))) {
        $items = [
            ['name' => '首页', 'url' => '/', 'icon' => 'icon-home'],
            ['name' => '归档', 'url' => '/archives/', 'icon' => 'icon-archive'],
        ];
        return $items;
    }

    $items = [];
    foreach (shiro_parse_config_lines($raw, 2) as $parts) {
        $url = shiro_safe_url($parts[1], '');
        if ($url === '') continue;

        $item = [
            'name' => $parts[0],
            'url' => $url,
        ];
        if (!empty($parts[2])) {
            $icon = shiro_icon_class($parts[2]);
            if ($icon) $item['icon'] = $icon;
        }
        if (!empty($parts[3])) {
            $target = shiro_safe_target($parts[3]);
            if ($target) $item['target'] = $target;
        }
        $items[] = $item;
    }
    return $items;
}

function shiro_get_links()
{
    static $links = null;
    if ($links !== null) return $links;

    $options = Helper::options();
    $raw = isset($options->links) ? htmlspecialchars_decode($options->links) : '';
    $links = [];

    foreach (shiro_parse_config_lines($raw, 2) as $parts) {
        $url = shiro_safe_url($parts[1], '');
        if ($url === '') continue;

        $links[] = [
            'name' => $parts[0],
            'url' => $url,
            'avatar' => isset($parts[2]) ? shiro_safe_url($parts[2], '') : '',
            'desc' => isset($parts[3]) ? $parts[3] : '',
        ];
    }

    return $links;
}

function shiro_giscus_config()
{
    static $config = null;
    if ($config !== null) return $config;

    $options = Helper::options();
    $raw = isset($options->giscusConfig) ? htmlspecialchars_decode($options->giscusConfig) : '';
    $allowedKeys = ['repo', 'repo_id', 'category', 'category_id', 'mapping', 'lang'];
    $config = [];

    foreach (explode("\n", $raw) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '=') === false) continue;

        list($key, $value) = array_map('trim', explode('=', $line, 2));
        if (!in_array($key, $allowedKeys, true)) continue;
        $config[$key] = preg_replace('/[\r\n]+/', '', $value);
    }

    if (!empty($config['repo']) && !preg_match('/^[A-Za-z0-9_.-]+\/[A-Za-z0-9_.-]+$/', $config['repo'])) {
        $config['repo'] = '';
    }

    $allowedMappings = ['pathname', 'url', 'title', 'og:title', 'specific', 'number'];
    if (empty($config['mapping']) || !in_array($config['mapping'], $allowedMappings, true)) {
        $config['mapping'] = 'pathname';
    }

    if (empty($config['lang']) || !preg_match('/^[A-Za-z0-9_-]+$/', $config['lang'])) {
        $config['lang'] = 'zh-CN';
    }

    return $config;
}

// ─── Favicon SVG Generator ──────────────────────────────────────────────────

function shiro_favicon_svg($text = '白')
{
    $color = '#b0171a';
    $escaped = shiro_escape_xml($text);
    return '<svg width="52" height="52" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
        . '<defs>'
        . '<filter id="seal-roughness" x="-20%" y="-20%" width="140%" height="140%">'
        . '<feTurbulence type="turbulence" baseFrequency="0.05" numOctaves="2" result="noise"/>'
        . '<feDisplacementMap in="SourceGraphic" in2="noise" scale="3"/></filter>'
        . '<filter id="text-erosion">'
        . '<feTurbulence type="fractalNoise" baseFrequency="0.15" numOctaves="1" result="noise"/>'
        . '<feDisplacementMap in="SourceGraphic" in2="noise" scale="1.5"/></filter>'
        . '</defs>'
        . '<path d="M15,12 Q50,5 85,12 Q95,50 88,88 Q50,95 12,88 Q5,50 15,12 Z" fill="' . $color . '" filter="url(#seal-roughness)" opacity="0.92"/>'
        . '<text x="50" y="50" text-anchor="middle" dominant-baseline="central" '
        . 'font-family="\'Yuji Syuku\',\'Zen Old Mincho\',\'Noto Serif JP\',serif" font-size="42" '
        . 'fill="rgba(255,255,255,0.92)" filter="url(#text-erosion)" style="user-select:none">'
        . $escaped . '</text></svg>';
}

// ─── Theme Configuration ─────────────────────────────────────────────────────

function themeConfig($form)
{
    ?>
    <style>
    #tab-container{margin:0 0 15px}
    #tab-nav{display:flex;list-style:none;padding:0;margin:0;border-bottom:2px solid #e5e7eb}
    #tab-nav li a{display:block;padding:10px 20px;font-size:14px;color:#64748b;text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .2s}
    #tab-nav li a:hover{color:#333}
    #tab-nav li a.active{color:#b0171a;border-bottom-color:#b0171a;font-weight:600}
    .tab-pane{display:none}
    .tab-pane.active{display:block}
    </style>
    <div id="tab-container">
        <ul id="tab-nav"></ul>
        <div id="tab-content"></div>
    </div>
    <?php

    $sealEnabled = new Typecho_Widget_Helper_Form_Element_Radio(
        'sealEnabled',
        ['1' => '启用', '0' => '禁用'],
        '1', '印章', '标题旁的红色印章装饰，保存后前台可预览效果'
    );
    $form->addInput($sealEnabled);

    $sealText = new Typecho_Widget_Helper_Form_Element_Text(
        'sealText', null, '白',
        '印章文字', '建议填写单个汉字，如：白、墨、雪、風'
    );
    $form->addInput($sealText);

    $faviconMode = new Typecho_Widget_Helper_Form_Element_Radio(
        'faviconMode',
        ['seal' => '与印章联动', 'custom' => '自定义路径'],
        'seal', 'Favicon 模式', '"与印章联动"会根据印章文字自动生成浏览器标签图标'
    );
    $form->addInput($faviconMode);

    $faviconUrl = new Typecho_Widget_Helper_Form_Element_Text(
        'faviconUrl', null, '/favicon.ico',
        'Favicon 地址', '仅在上方选择"自定义路径"时生效，填写图标文件的 URL'
    );
    $form->addInput($faviconUrl);

    $since = new Typecho_Widget_Helper_Form_Element_Text(
        'since', null, '',
        '建站年份', '填写后页脚显示为 "2020–2026" 格式，留空只显示当前年份'
    );
    $form->addInput($since);

    $rssEnabled = new Typecho_Widget_Helper_Form_Element_Radio(
        'rssEnabled',
        ['1' => '显示', '0' => '隐藏'],
        '0', 'RSS 按钮', '头部显示 RSS 订阅入口'
    );
    $form->addInput($rssEnabled);

    // ── 导航与外观 ──

    $defaultMenu = "首页,/,icon-home\n归档,/archives/,icon-archive\n分类,/categories/,icon-folder\n标签,/tags/,icon-tag";
    $menuItems = new Typecho_Widget_Helper_Form_Element_Textarea(
        'menuItems', null, $defaultMenu,
        '菜单配置', '每行一个，格式：<b>名称,链接,图标,打开方式</b>（后两项可省略）<br><br>示例：<br><code>首页,/,icon-home</code><br><code>GitHub,https://github.com,icon-github,_blank</code><br><br>可用图标：icon-home · icon-archive · icon-folder · icon-tag · icon-link · icon-user · icon-github · icon-mail · icon-book · icon-image · icon-heart · icon-rss · icon-calendar · icon-external'
    );
    $form->addInput($menuItems);

    $darkModeDefault = new Typecho_Widget_Helper_Form_Element_Select(
        'darkModeDefault',
        ['system' => '跟随系统', 'light' => '浅色', 'dark' => '深色'],
        'light', '默认主题模式', '用户首次访问时的默认配色方案'
    );
    $form->addInput($darkModeDefault);

    $darkModeToggle = new Typecho_Widget_Helper_Form_Element_Radio(
        'darkModeToggle',
        ['1' => '显示', '0' => '隐藏'],
        '1', '主题切换按钮', '是否允许访客切换明暗模式'
    );
    $form->addInput($darkModeToggle);

    // ── 文章阅读 ──

    $excerptLength = new Typecho_Widget_Helper_Form_Element_Text(
        'excerptLength', null, '200',
        '摘要长度', '首页文章卡片自动截断的字符数'
    );
    $form->addInput($excerptLength);

    $tocEnabled = new Typecho_Widget_Helper_Form_Element_Radio(
        'tocEnabled',
        ['1' => '启用', '0' => '禁用'],
        '1', '文章目录（TOC）', '文章页侧边栏是否显示目录导航'
    );
    $form->addInput($tocEnabled);

    $tocDepth = new Typecho_Widget_Helper_Form_Element_Select(
        'tocDepth',
        ['2' => 'h2', '3' => 'h2 + h3', '4' => 'h2 + h3 + h4'],
        '3', '目录深度'
    );
    $form->addInput($tocDepth);

    $tocMinHeadings = new Typecho_Widget_Helper_Form_Element_Text(
        'tocMinHeadings', null, '3',
        '最少标题数', '文章中至少有多少个标题才显示目录'
    );
    $form->addInput($tocMinHeadings);

    $progressBar = new Typecho_Widget_Helper_Form_Element_Radio(
        'progressBar',
        ['1' => '启用', '0' => '禁用'],
        '1', '阅读进度条', '文章页顶部显示阅读进度'
    );
    $form->addInput($progressBar);

    $backToTop = new Typecho_Widget_Helper_Form_Element_Radio(
        'backToTop',
        ['1' => '启用', '0' => '禁用'],
        '1', '返回顶部按钮'
    );
    $form->addInput($backToTop);

    // ── 评论搜索 ──

    $searchEnabled = new Typecho_Widget_Helper_Form_Element_Radio(
        'searchEnabled',
        ['1' => '启用', '0' => '禁用'],
        '1', 'AJAX 搜索', '头部显示搜索按钮，支持实时搜索文章'
    );
    $form->addInput($searchEnabled);

    $searchLimit = new Typecho_Widget_Helper_Form_Element_Text(
        'searchLimit', null, '10',
        '搜索结果数量', '每次搜索最多返回的结果条数'
    );
    $form->addInput($searchLimit);

    $commentsProvider = new Typecho_Widget_Helper_Form_Element_Select(
        'commentsProvider',
        ['native' => 'Typecho 原生评论', 'disqus' => 'Disqus', 'giscus' => 'giscus (GitHub Discussions)'],
        'native', '评论系统', '选择后下方会显示对应的配置项'
    );
    $form->addInput($commentsProvider);

    $disqusShortname = new Typecho_Widget_Helper_Form_Element_Text(
        'disqusShortname', null, '',
        'Disqus Shortname', '在 <a href="https://disqus.com/admin/create/" target="_blank">Disqus</a> 创建站点后获取的 shortname'
    );
    $form->addInput($disqusShortname);

    $defaultGiscus = "repo=\nrepo_id=\ncategory=\ncategory_id=\nmapping=pathname\nlang=zh-CN";
    $giscusConfig = new Typecho_Widget_Helper_Form_Element_Textarea(
        'giscusConfig', null, $defaultGiscus,
        'giscus 配置', '每行一项，格式 key=value。前往 <a href="https://giscus.app/" target="_blank">giscus.app</a> 生成配置后填入：<br><code>repo=owner/repo</code><br><code>repo_id=R_xxx</code><br><code>category=Announcements</code><br><code>category_id=DIC_xxx</code><br><code>mapping=pathname</code><br><code>lang=zh-CN</code>'
    );
    $form->addInput($giscusConfig);

    // ── 页脚与友链 ──

    $footerText = new Typecho_Widget_Helper_Form_Element_Textarea(
        'footerText', null, 'Elegant theme by <a href="https://github.com/Acris/hexo-theme-shiro" target="_blank">Shiro</a>',
        '页脚文案', '显示在版权年份下方，支持受限 HTML：<code>a</code>、<code>strong</code>、<code>em</code>、<code>code</code>、<code>br</code>。链接仅保留安全的 href / target / title 属性'
    );
    $form->addInput($footerText);

    $icpBeian = new Typecho_Widget_Helper_Form_Element_Text(
        'icpBeian', null, '',
        'ICP备案号', '填写后显示在页脚，并链接到工信部备案管理系统。示例：京ICP备12345678号-1'
    );
    $form->addInput($icpBeian);

    $policeBeian = new Typecho_Widget_Helper_Form_Element_Text(
        'policeBeian', null, '',
        '公安备案号', '填写后显示在页脚，并链接到全国互联网安全管理服务平台；主题会从备案号中提取数字生成详情链接。示例：京公网安备 11010802020134号'
    );
    $form->addInput($policeBeian);

    $googleAnalyticsId = new Typecho_Widget_Helper_Form_Element_Text(
        'googleAnalyticsId', null, '',
        'Google Analytics', '填写 GA4 Measurement ID，格式如 <code>G-XXXXXXXXXX</code>，留空则不加载'
    );
    $form->addInput($googleAnalyticsId);

    $themeColor = new Typecho_Widget_Helper_Form_Element_Text(
        'themeColor', null, '#b0171a',
        '主题强调色', '印章和链接高亮的颜色，默认朱红 <code>#b0171a</code>'
    );
    $form->addInput($themeColor);

    $customCSS = new Typecho_Widget_Helper_Form_Element_Textarea(
        'customCSS', null, '',
        '自定义 CSS', '直接写 CSS 代码，优先级最高，可覆盖主题默认样式。留空则不加载'
    );
    $form->addInput($customCSS);

    $customJS = new Typecho_Widget_Helper_Form_Element_Textarea(
        'customJS', null, '',
        '自定义 JS', '直接写 JavaScript 代码，加载在页脚。留空则不加载'
    );
    $form->addInput($customJS);

    // 友链
    $defaultLinks = "Shiro,https://github.com/Acris/hexo-theme-shiro,,A pure and simple theme";
    $links = new Typecho_Widget_Helper_Form_Element_Textarea(
        'links', null, $defaultLinks,
        '友链', '每行一个，格式：<b>名称,链接,头像URL,描述</b>（头像和描述可省略）<br><br>示例：<br><code>小明,https://example.com,https://example.com/avatar.jpg,一个有趣的博客</code><br><code>小红,https://example.org</code><br><br>在"友链"自定义页面模板中展示'
    );
    $form->addInput($links);
    ?>
    <script src="<?php echo themeAsset('assets/js/admin.js'); ?>" type="text/javascript"></script>
    <?php
}

// ─── Theme Init ──────────────────────────────────────────────────────────────

function themeInit($archive)
{
    // Handle AJAX search requests
    if ($archive->request->is('do=shiro_search')) {
        shiro_ajax_search($archive);
        exit;
    }
    if ($archive->request->is('do=shiro_search_index')) {
        shiro_search_index();
        exit;
    }

    Helper::options()->commentsPageSize = 20;
}

// ─── AJAX Search Handler ─────────────────────────────────────────────────────

function shiro_content_url($row)
{
    return Typecho_Router::url(
        $row['type'],
        [
            'slug' => $row['slug'],
            'cid' => $row['cid'],
            'category' => '',
            'year' => date('Y', $row['created']),
            'month' => date('m', $row['created']),
            'day' => date('d', $row['created'])
        ],
        Helper::options()->index
    );
}

function shiro_search_excerpt($text, $length = 120)
{
    $text = strip_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}

function shiro_search_result_from_row($row)
{
    return [
        'title' => $row['title'],
        'url' => shiro_content_url($row),
        'excerpt' => shiro_search_excerpt($row['text']),
        'date' => date('Y-m-d', $row['created']),
    ];
}

function shiro_search_index()
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=300');

    $rows = $db->fetchAll(
        $db->select('cid', 'title', 'text', 'created', 'modified', 'slug', 'type')
            ->from($prefix . 'contents')
            ->where('status = ?', 'publish')
            ->where('(type = ? OR type = ?)', 'post', 'page')
            ->order('created', Typecho_Db::SORT_DESC)
    );

    $items = [];
    $updated = 0;

    foreach ($rows as $row) {
        $plain = shiro_search_excerpt($row['text'], 5000);
        $updated = max($updated, isset($row['modified']) ? (int)$row['modified'] : (int)$row['created']);
        $items[] = [
            'title' => $row['title'],
            'url' => shiro_content_url($row),
            'excerpt' => shiro_search_excerpt($row['text']),
            'date' => date('Y-m-d', $row['created']),
            'searchText' => trim($row['title'] . ' ' . $plain),
        ];
    }

    echo shiro_json([
        'items' => $items,
        'total' => count($items),
        'updated' => $updated,
    ]);
}

function shiro_ajax_search($archive)
{
    $keyword = isset($archive->request->q) ? trim($archive->request->q) : '';
    $keyword = mb_substr($keyword, 0, 64);
    $response = ['results' => [], 'total' => 0, 'keyword' => $keyword];

    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');

    if (mb_strlen($keyword) < 2) {
        echo shiro_json($response);
        return;
    }

    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $searchLimit = isset(Helper::options()->searchLimit) ? (int)Helper::options()->searchLimit : 10;
    $searchLimit = max(1, min(30, $searchLimit));

    $keyword = '%' . Typecho_Common::filterSearchQuery($keyword) . '%';

    $select = $db->select('cid', 'title', 'text', 'created', 'slug', 'type')
        ->from($prefix . 'contents')
        ->where('status = ?', 'publish')
        ->where('(type = ? OR type = ?)', 'post', 'page')
        ->where('(title LIKE ? OR text LIKE ?)', $keyword, $keyword)
        ->order('created', Typecho_Db::SORT_DESC)
        ->limit($searchLimit);

    $rows = $db->fetchAll($select);
    $results = [];

    foreach ($rows as $row) {
        $results[] = shiro_search_result_from_row($row);
    }

    $response['results'] = $results;
    $response['total'] = count($results);

    echo shiro_json($response);
}

// ─── Custom Pagination ───────────────────────────────────────────────────────

function shiro_page_nav($widget, $prevText = '', $nextText = '')
{
    $prevText = $prevText ?: shiro_lang('page.prev');
    $nextText = $nextText ?: shiro_lang('page.next');
    $widget->pageNav($prevText, $nextText, 1, '...', [
        'wrapTag' => 'nav',
        'wrapClass' => 'pagination flex flex-wrap items-center justify-center gap-6 sm:gap-8 text-sm md:text-base font-medium',
        'itemTag' => '',
        'currentClass' => 'current',
        'prevClass' => 'prev',
        'nextClass' => 'next',
    ]);
}
