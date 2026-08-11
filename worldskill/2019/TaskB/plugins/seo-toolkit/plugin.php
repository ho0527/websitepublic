<?php
/**
 * 外掛：SEO Toolkit
 *
 * 功能：
 *   1. 統一產生 <title>（頁面標題 + 站名）
 *   2. 輸出 description / canonical / robots / Open Graph / Twitter Card / JSON-LD
 *   3. 提供 sitemap.xml 與 robots.txt 兩個網址
 */

declare(strict_types=1);

use App\Core\App;
use App\Core\Html;
use App\Core\PluginManager;
use App\Core\Url;

return static function (PluginManager $hooks, App $app): void {
    $siteTitle = $app->setting('site_title');

    // 1. 標題樣板：「頁面標題 | 站名」，首頁本身已含站名則不重複
    $hooks->addFilter('seo_title', static function (string $title) use ($siteTitle): string {
        return str_contains($title, $siteTitle) ? $title : $title . ' | ' . $siteTitle;
    });

    // 2. <head> 內的中繼標籤
    $hooks->addAction('head_meta', static function (array $page) use ($app, $siteTitle): void {
        $description = Html::excerpt($page['description'] ?? '', 160);
        $canonical   = Url::current((string) ($page['canonical'] ?? ''));
        // og:image 需要絕對網址
        $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $ogImage = ($page['ogImage'] ?? '') !== ''
            ? $scheme . '://' . $host . Url::asset((string) $page['ogImage'])
            : '';
        ?>
        <meta name="description" content="<?= Html::e($description) ?>">
        <meta name="robots" content="index, follow, max-image-preview:large">
        <link rel="canonical" href="<?= Html::e($canonical) ?>">
        <meta property="og:site_name" content="<?= Html::e($siteTitle) ?>">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="en_GB">
        <meta property="og:title" content="<?= Html::e($page['title'] ?? $siteTitle) ?>">
        <meta property="og:description" content="<?= Html::e($description) ?>">
        <meta property="og:url" content="<?= Html::e($canonical) ?>">
        <?php if ($ogImage !== ''): ?>
            <meta property="og:image" content="<?= Html::e($ogImage) ?>">
        <?php endif; ?>
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?= Html::e($page['title'] ?? $siteTitle) ?>">
        <meta name="twitter:description" content="<?= Html::e($description) ?>">
        <script type="application/ld+json">
        <?= json_encode([
            '@context'    => 'https://schema.org',
            '@type'       => 'TouristAttraction',
            'name'        => $siteTitle,
            'description' => $app->setting('site_description'),
            'url'         => Url::current(''),
            'areaServed'  => 'Kazan, Republic of Tatarstan, Russia',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
        </script>
        <?php
    });

    // 3. sitemap.xml 與 robots.txt
    $hooks->addFilter('front_route', static function (bool $handled, string $path) use ($app): bool {
        if ($handled) {
            return true;
        }

        if ($path === 'sitemap.xml') {
            header('Content-Type: application/xml; charset=UTF-8');

            $urls = [['loc' => Url::current(''), 'priority' => '1.0']];
            foreach ($app->museums->published() as $museum) {
                $urls[] = ['loc' => Url::current($museum['slug']), 'priority' => '0.9'];
            }
            $urls[] = ['loc' => Url::current('museums'), 'priority' => '0.8'];
            $urls[] = ['loc' => Url::current('news'), 'priority' => '0.8'];
            foreach ($app->categories->all() as $category) {
                $urls[] = ['loc' => Url::current('news/' . $category['slug']), 'priority' => '0.7'];
            }
            foreach ($app->posts->latest(200) as $post) {
                $urls[] = [
                    'loc'      => Url::current('news/' . $post['category_slug'] . '/' . $post['slug']),
                    'priority' => '0.6',
                    'lastmod'  => Html::date($post['updated_at'], 'Y-m-d'),
                ];
            }
            $urls[] = ['loc' => Url::current('contact'), 'priority' => '0.5'];

            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($urls as $url) {
                echo "  <url>\n";
                echo '    <loc>' . Html::e($url['loc']) . "</loc>\n";
                if (!empty($url['lastmod'])) {
                    echo '    <lastmod>' . Html::e($url['lastmod']) . "</lastmod>\n";
                }
                echo '    <priority>' . Html::e($url['priority']) . "</priority>\n";
                echo "  </url>\n";
            }
            echo '</urlset>';

            return true;
        }

        if ($path === 'robots.txt') {
            header('Content-Type: text/plain; charset=UTF-8');
            echo "User-agent: *\n";
            echo 'Disallow: ' . Url::to('admin') . "\n";
            echo 'Sitemap: ' . Url::current('sitemap.xml') . "\n";

            return true;
        }

        return false;
    });
};
