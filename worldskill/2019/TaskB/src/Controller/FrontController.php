<?php
/**
 * 前台控制器
 *
 * 路由對應：
 *   ''                                  首頁
 *   'museums'                           全部博物館列表
 *   'news'                              全部新聞
 *   'news/<category>'                   分類新聞
 *   'news/<category>/<post>'            單篇新聞
 *   'contact'                           聯絡我們
 *   'sitemap.xml' / 'robots.txt'        由 SEO 外掛掛鉤處理
 *   '<museum-slug>'                     博物館頁（最後才比對）
 */

declare(strict_types=1);

namespace App\Controller;

use App\Core\App;
use App\Core\Html;
use App\Core\Url;

final class FrontController
{
    public function __construct(private App $app)
    {
    }

    public function handle(array $segments, string $path): void
    {
        // 讓外掛有機會攔截特定網址（例如 SEO 外掛的 sitemap.xml、robots.txt）
        $handled = $this->app->hooks->applyFilters('front_route', false, $path);
        if ($handled === true) {
            return;
        }

        $first = $segments[0] ?? '';

        match (true) {
            $first === ''         => $this->home(),
            $first === 'museums'  => $this->museumIndex(),
            $first === 'news'     => $this->news(array_slice($segments, 1)),
            $first === 'contact'  => $this->contact(),
            default               => $this->museumOrNotFound($first),
        };
    }

    /* ------------------------------------------------------------------ 頁面 */

    /** 首頁：桌機左右兩欄（左新聞、右封面與圖片），行動裝置上下堆疊 */
    private function home(): void
    {
        $perPage = max(1, (int) $this->app->setting('posts_per_page', '6'));

        $this->view('home', [
            // 首頁顯示「所有分類」的最新文章
            'latestPosts'      => $this->app->posts->latest($perPage),
            'selectedMuseums'  => $this->app->museums->published(true),
            'otherMuseums'     => $this->app->museums->published(false),
            'seasonalPosts'    => $this->seasonalPosts(3),
            'coverImage'       => $this->app->setting('home_cover_image'),
            'galleryImages'    => Html::lines($this->app->setting('home_gallery')),
        ], [
            'title'       => $this->app->setting('site_title') . ' — ' . $this->app->setting('site_tagline'),
            'description' => $this->app->setting('site_description'),
            'canonical'   => '',
            'bodyClass'   => 'page-home',
            'ogImage'     => $this->app->setting('home_cover_image'),
        ]);
    }

    /** 全部博物館 */
    private function museumIndex(): void
    {
        $this->view('archive-museums', [
            'selectedMuseums' => $this->app->museums->published(true),
            'otherMuseums'    => $this->app->museums->published(false),
        ], [
            'title'       => 'All museums in Kazan',
            'description' => 'The complete list of museums featured on ' . $this->app->setting('site_title') . '.',
            'canonical'   => 'museums',
            'bodyClass'   => 'page-museums',
        ]);
    }

    /** 新聞：全部 / 分類 / 單篇 */
    private function news(array $segments): void
    {
        $categorySlug = $segments[0] ?? '';
        $postSlug     = $segments[1] ?? '';

        // /news/  → 全部新聞
        if ($categorySlug === '') {
            $page    = max(1, (int) ($_GET['page'] ?? 1));
            $perPage = max(1, (int) $this->app->setting('posts_per_page', '6'));

            $this->view('archive-news', [
                'category'   => null,
                'posts'      => $this->app->posts->paginate($page, $perPage),
                'categories' => $this->app->categories->all(),
                'page'       => $page,
                'totalPages' => (int) ceil(max(1, $this->app->posts->countPublished()) / $perPage),
                'baseRoute'  => 'news',
            ], [
                'title'       => 'News from the museums of Kazan',
                'description' => 'All news posts from ' . $this->app->setting('site_title') . '.',
                'canonical'   => 'news',
                'bodyClass'   => 'page-news',
            ]);

            return;
        }

        $category = $this->app->categories->findBySlug($categorySlug);
        if ($category === null) {
            $this->notFound();

            return;
        }

        // /news/<category>/<post> → 單篇文章
        if ($postSlug !== '') {
            $post = $this->app->posts->findBySlug($postSlug);
            if ($post === null || $post['category_slug'] !== $categorySlug) {
                $this->notFound();

                return;
            }

            $this->view('single-post', [
                'post'     => $post,
                'siblings' => $this->app->posts->siblings($post),
                'related'  => array_slice(
                    array_filter(
                        $this->app->posts->latest(4, (int) $post['category_id']),
                        static fn (array $item) => (int) $item['id'] !== (int) $post['id']
                    ),
                    0,
                    3
                ),
            ], [
                'title'       => $post['title'],
                'description' => $post['excerpt'] !== '' ? $post['excerpt'] : Html::excerpt($post['content']),
                'canonical'   => 'news/' . $categorySlug . '/' . $post['slug'],
                'bodyClass'   => 'page-single-post',
                'ogImage'     => $post['featured_image'],
            ]);

            return;
        }

        // /news/<category> → 分類列表
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, (int) $this->app->setting('posts_per_page', '6'));

        $this->view('archive-news', [
            'category'   => $category,
            'posts'      => $this->app->posts->paginate($page, $perPage, (int) $category['id']),
            'categories' => $this->app->categories->all(),
            'page'       => $page,
            'totalPages' => (int) ceil(max(1, $this->app->posts->countPublished((int) $category['id'])) / $perPage),
            'baseRoute'  => 'news/' . $category['slug'],
        ], [
            'title'       => $category['name'] . ' — news',
            'description' => $category['description'],
            'canonical'   => 'news/' . $category['slug'],
            'bodyClass'   => 'page-news page-news-category',
        ]);
    }

    /** 聯絡我們（靜態表單送往 Formspree） */
    private function contact(): void
    {
        $this->view('page-contact', [
            'formAction'  => $this->app->setting('contact_form_action'),
            'introText'   => $this->app->setting('contact_intro_text'),
            'successText' => $this->app->setting('contact_success_text'),
            'errorText'   => $this->app->setting('contact_error_text'),
            'contactMail' => $this->app->setting('contact_email'),
        ], [
            'title'       => 'Contact us',
            'description' => 'Contact ' . $this->app->setting('site_title') . ' about tickets, opening hours and group visits.',
            'canonical'   => 'contact',
            'bodyClass'   => 'page-contact',
        ]);
    }

    /** 博物館頁；找不到就 404 */
    private function museumOrNotFound(string $slug): void
    {
        $museum = $this->app->museums->findBySlug($slug);
        if ($museum === null) {
            $this->notFound();

            return;
        }

        $isSelected = (int) $museum['is_selected'] === 1;

        // 精選博物館才顯示該館分類的新聞
        $museumPosts = ($isSelected && $museum['category_id'] !== null)
            ? $this->app->posts->latest(4, (int) $museum['category_id'])
            : [];

        $this->view($isSelected ? 'museum-selected' : 'museum-general', [
            'museum'      => $museum,
            'museumPosts' => $museumPosts,
            'gallery'     => Html::lines($museum['gallery']),
            'otherMuseums'=> array_slice(array_filter(
                $this->app->museums->published(),
                static fn (array $item) => (int) $item['id'] !== (int) $museum['id']
            ), 0, 3),
        ], [
            'title'       => $museum['title'],
            'description' => $museum['excerpt'] !== '' ? $museum['excerpt'] : Html::excerpt($museum['content']),
            'canonical'   => $museum['slug'],
            'bodyClass'   => $isSelected ? 'page-museum page-museum-selected' : 'page-museum page-museum-general',
            'ogImage'     => $museum['featured_image'],
        ]);
    }

    private function notFound(): void
    {
        http_response_code(404);
        $this->view('404', [], [
            'title'       => 'Page not found',
            'description' => 'The page you were looking for does not exist.',
            'canonical'   => '',
            'bodyClass'   => 'page-404',
        ]);
    }

    /* ------------------------------------------------------------------ 工具 */

    /** 取得「Seasonal Events」分類的最新文章 */
    private function seasonalPosts(int $limit): array
    {
        $category = $this->app->categories->findBySlug('seasonal-events');

        return $category === null ? [] : $this->app->posts->latest($limit, (int) $category['id']);
    }

    /**
     * 渲染前台頁面：先產生內容區塊，再套入版面骨架
     *
     * @param array $page 頁面中繼資料（標題、描述、canonical…）
     */
    private function view(string $template, array $data, array $page): void
    {
        $page = array_merge([
            'title'       => $this->app->setting('site_title'),
            'description' => $this->app->setting('site_description'),
            'canonical'   => '',
            'bodyClass'   => '',
            'ogImage'     => $this->app->setting('home_cover_image'),
        ], $page);

        $data['app']  = $this->app;
        $data['page'] = $page;

        $content = $this->app->theme->render($template, $data);

        echo $this->app->theme->render('layout', [
            'app'     => $this->app,
            'page'    => $page,
            'content' => $content,
        ]);
    }
}
