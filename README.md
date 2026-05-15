# Typecho Theme Shiro

Shiro for Typecho 是一个从 Hexo 主题 [hexo-theme-shiro](https://github.com/Acris/hexo-theme-shiro) 迁移并扩展而来的 Typecho 博客主题。它保留原主题“纯净、留白、纸感、印章”的视觉气质，同时围绕 Typecho 的模板体系补齐搜索、评论、独立页面、后台配置、性能优化和安全输出治理。

> 原 Hexo 主题作者为 [Acris Liu](https://github.com/Acris)，原项目描述为 “A pure and simple theme for Hexo.”。本项目不是原 Hexo 主题的官方 Typecho 版本。

## 项目状态

- 平台：Typecho 主题
- 主题名：Shiro
- 当前版本：1.0.0
- 兼容方向：Typecho 1.2.x 优先
- 原项目：[Acris/hexo-theme-shiro](https://github.com/Acris/hexo-theme-shiro)
- 原项目许可证：MIT

## 特性

### 外观与交互

- 纸张质感布局、留白排版、印章 SVG 装饰
- 暗色模式三态切换：跟随系统 / 浅色 / 深色
- 自定义印章文字、favicon、主题强调色
- 移动端导航菜单、当前页高亮、返回顶部按钮
- 阅读进度条、离开页面时的动态标签页标题
- 内置 iconfont 图标，无需额外图标服务

### 文章阅读

- 文章封面字段：`cover` / `thumbnail` / `thumb` / `image`
- 字数统计、预计阅读时间、面包屑导航
- 文章目录 TOC：桌面侧边栏、移动端折叠内联目录
- 代码复制按钮、Markdown 导出、Markdown 复制
- Mermaid 和 KaTeX 按内容检测后按需加载
- LightGallery 图片灯箱按需加载
- 上下篇文章导航、基于标签的相关文章推荐
- 复制正文时自动追加版权信息

### 搜索与评论

- 静态搜索索引优先，失败后回退 AJAX 搜索
- 搜索结果前端缓存，请求取消，结果数量后台可配置
- 快捷键：`Ctrl+K`、`/`、`Esc`
- 评论系统三选一：Typecho 原生评论、Disqus、giscus
- Disqus / giscus 接近评论区时懒加载

### 独立页面

主题内置以下自定义页面模板：

| 文件 | 用途 |
| --- | --- |
| `archives.php` | 归档，按年份分组并显示年份跳转 |
| `categories.php` | 分类，按分类列出文章 |
| `tags.php` | 标签，按文章数量调整权重 |
| `links.php` | 友链 |

Typecho 后台创建独立页面时，选择对应模板即可启用。

### 后台配置

后台设置被拆分为 5 个 Tab：

- 基本设置：印章、favicon、建站年份、RSS
- 导航与外观：菜单、暗色模式
- 文章阅读：摘要长度、TOC、阅读进度、返回顶部
- 评论搜索：搜索、评论系统、Disqus、giscus
- 页脚与友链：页脚文案、备案信息、GA、主题色、自定义 CSS/JS、友链

页脚支持受限 HTML，可用于链接和简单强调；危险标签、危险属性和不安全 URL 会被清理。ICP备案号和公安备案号为独立字段，填写后自动显示并链接到对应备案查询网站。

### 性能优化

- 静态资源使用 `filemtime()` 版本号，避免每次请求计算文件 hash
- 菜单、友链、giscus 配置使用请求内静态缓存
- 搜索索引带短时 HTTP 缓存
- 文章页才加载文章增强脚本
- LightGallery、Mermaid、KaTeX、Disqus、giscus 均按需加载
- 非原生评论系统不加载原生评论 AJAX 脚本
- 分类页批量查询文章，避免 N+1 查询
- 首页和正文图片自动懒加载，首张关键图提升优先级

### 安全与健壮性

- 标题、分类、标签、站点信息、友链等可见文本统一“先实体解码，再安全转义”
- JSON 输出统一使用 HEX 转义，降低内联脚本风险
- URL、target、图标 class、主题色、GA ID、Disqus shortname 等配置做白名单校验
- AJAX 搜索固定 JSON Content-Type，并追加 `X-Content-Type-Options: nosniff`
- 页脚 HTML 使用白名单标签和属性，不直接输出任意 HTML

## 安装

1. 下载或克隆本仓库。
2. 将主题目录放入 Typecho 的 `usr/themes/` 目录，例如：

```text
usr/themes/typecho-theme-shiro
```

3. 登录 Typecho 后台，进入 `控制台 -> 外观`，启用 Shiro。
4. 进入主题设置，按需配置导航、评论、搜索、页脚和友链。

建议目录名保持为 `typecho-theme-shiro` 或 `shiro`。如果你更改目录名，Typecho 通常仍可识别，但请避免路径中包含特殊字符。

## 配置说明

### 导航菜单

每行一个菜单项：

```text
名称,链接,图标,打开方式
```

示例：

```text
首页,/,icon-home
归档,/archives/,icon-archive
GitHub,https://github.com,icon-github,_blank
```

后两项可省略。`打开方式` 仅支持 `_blank`、`_self`、`_parent`、`_top`。

### 文章封面

在文章自定义字段中添加任意一个字段即可：

```text
cover
thumbnail
thumb
image
```

主题会优先使用这些字段作为文章页封面、首页卡片封面和社交分享图。

### giscus

后台填写 key-value 格式：

```text
repo=owner/repo
repo_id=R_xxx
category=Announcements
category_id=DIC_xxx
mapping=pathname
lang=zh-CN
```

### 友链

每行一个友链：

```text
名称,链接,头像URL,描述
```

示例：

```text
Shiro,https://github.com/Acris/hexo-theme-shiro,,A pure and simple theme
```

头像和描述可省略。

### 页脚与备案

- 页脚文案支持受限 HTML：`a`、`strong`、`b`、`em`、`i`、`code`、`br`
- 链接仅保留安全的 `href`、`target`、`title`
- ICP 备案号填写后链接到工信部备案管理系统
- 公安备案号填写后会提取备案数字并链接到全国互联网安全管理服务平台

示例：

```html
Elegant theme by <a href="https://github.com/Acris/hexo-theme-shiro" target="_blank">Shiro</a>
```

## 文件结构

| 文件 / 目录 | 说明 |
| --- | --- |
| `functions.php` | 主题配置、工具函数、搜索接口、后台配置 |
| `header.php` / `footer.php` | 页面头部、SEO、资源加载、页脚 |
| `index.php` | 首页文章列表 |
| `post.php` | 文章详情页 |
| `page.php` | 默认独立页面 |
| `archive.php` | 归档、分类、标签、搜索结果页 |
| `comments.php` | 评论系统 |
| `404.php` | 404 页面 |
| `archives.php` / `categories.php` / `tags.php` / `links.php` | 自定义独立页面模板 |
| `assets/css/` | 样式文件 |
| `assets/js/` | 前端交互脚本 |
| `languages/` | 多语言文本 |

## 原项目与致谢

本主题的视觉方向和基础灵感来自：

- 原项目：[hexo-theme-shiro](https://github.com/Acris/hexo-theme-shiro)
- 原作者：[Acris Liu](https://github.com/Acris)
- 原项目描述：A pure and simple theme for Hexo.
- 原项目许可证：MIT

感谢原作者提供 Shiro 的设计与开源实现。本项目在 Typecho 模板体系下重新实现，并加入了 Typecho 后台配置、搜索、评论、备案、性能优化和安全输出治理。

## 许可证

原 Hexo 主题使用 MIT License。当前 Typecho 迁移版本继续使用 MIT License 开源。

如果你基于本项目继续修改或发布，请保留原作者与本项目的署名信息。
