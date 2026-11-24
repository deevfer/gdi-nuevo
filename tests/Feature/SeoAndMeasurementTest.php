<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageInvestors;
use App\Filament\Pages\ManageNavigation;
use App\Filament\Pages\SeoDashboard;
use App\Filament\Resources\Insights\Pages\EditInsight;
use App\Models\Insight;
use App\Models\PageSection;
use App\Models\SeoAudit;
use App\Models\SeoSetting;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SeoAndMeasurementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        SeoSetting::create(['id' => 1, 'data' => ['public_url' => 'https://www.gdivalores.com']]);
    }

    public function test_pages_publish_seo_and_social_metadata(): void
    {
        $this->get('/inversionistas')
            ->assertOk()
            ->assertSee('<title>Inversionistas | GDI Valores</title>', false)
            ->assertSee('<meta name="description" content="Acceso institucional', false)
            ->assertSee('<link rel="canonical" href="https://www.gdivalores.com/inversionistas">', false)
            ->assertSee('<meta property="og:image" content="https://www.gdivalores.com/', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('"@type":"FinancialService"', false)
            ->assertSee('"@type":"BreadcrumbList"', false);
    }

    public function test_site_is_never_indexed_outside_production(): void
    {
        $this->get('/')
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertHeader('X-Robots-Tag', 'noindex, follow');

        $this->get('/robots.txt')->assertSee("Disallow: /\n", false);
    }

    public function test_production_allows_indexing_and_respects_each_page(): void
    {
        $this->app['env'] = 'production';

        $this->get('/inversionistas')->assertSee('<meta name="robots" content="index, follow, max-image-preview:large">', false);
        $this->get('/robots.txt')->assertSee('Sitemap: https://www.gdivalores.com/sitemap.xml', false)->assertSee('Disallow: /admin', false);

        $seo = PageSection::where('page', 'investors')->where('section', 'seo')->first();
        $seo->update(['content' => [...$seo->content, 'robots_index' => false]]);

        $this->get('/inversionistas')->assertSee('<meta name="robots" content="noindex, follow">', false);
        $this->get('/sitemap.xml')->assertDontSee('/inversionistas</loc>', false)->assertSee('/estrategias-de-inversion</loc>', false);
    }

    public function test_sitemap_and_llms_list_pages_and_articles(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>https://www.gdivalores.com/contacto</loc>', false)
            ->assertSee('<loc>https://www.gdivalores.com/insights/gestion-disciplinada-de-liquidez</loc>', false);

        $this->get('/llms.txt')->assertOk()->assertSee('# GDI Valores')->assertSee('[Estrategias de inversión]');
    }

    public function test_articles_publish_article_schema_and_can_opt_out_of_indexing(): void
    {
        $this->get('/insights/gestion-disciplinada-de-liquidez')
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('"@type":"Article"', false);

        Insight::where('slug', 'gestion-disciplinada-de-liquidez')->update(['robots_index' => false]);
        $this->get('/sitemap.xml')->assertDontSee('gestion-disciplinada-de-liquidez');
    }

    public function test_fund_faqs_with_answers_become_faq_schema(): void
    {
        $this->get('/estrategias-de-inversion')->assertDontSee('"@type":"FAQPage"', false);

        $funds = PageSection::where('page', 'strategies')->where('section', 'funds')->first();
        $content = $funds->content;
        $content['items'][0]['faqs'][0]['answer'] = 'Optimizar la gestión de liquidez.';
        $funds->update(['content' => $content]);

        $this->get('/estrategias-de-inversion')->assertSee('"@type":"FAQPage"', false)->assertSee('Optimizar la gestión de liquidez.');
    }

    public function test_verification_tags_are_published(): void
    {
        SeoSetting::find(1)->update(['data' => ['public_url' => 'https://www.gdivalores.com', 'search_console_verification' => 'abc123', 'custom_verification_tags' => '<meta name="yandex-verification" content="xyz"><script>alert(1)</script>']]);

        $this->get('/')
            ->assertSee('<meta name="google-site-verification" content="abc123">', false)
            ->assertSee('<meta name="yandex-verification" content="xyz">', false)
            ->assertDontSee('alert(1)', false);
    }

    public function test_measurement_tags_wait_for_cookie_consent(): void
    {
        SiteSetting::current()->update([
            'google_analytics_id' => 'G-ABC123',
            'google_tag_manager_id' => 'GTM-XYZ',
            'google_ads_id' => 'AW-123456',
            'google_ads_lead_label' => 'LeadLabel1',
            'meta_pixel_id' => '1234567890',
            'linkedin_enabled' => true,
            'linkedin_partner_id' => '555',
        ]);

        $this->get('/')
            ->assertSee("gtag('consent', 'default'", false)
            ->assertSee('data-gdi-consent="analytics" data-gdi-google-loader="true" async data-src="https://www.googletagmanager.com/gtag/js?id=G-ABC123"', false)
            ->assertSee('data-gdi-consent="all"', false)
            ->assertSee('window.GDI_ADS', false)
            ->assertSee('LeadLabel1', false)
            ->assertSee('window.GDI_META', false)
            ->assertSee('1234567890', false)
            ->assertSee('window.GDI_LINKEDIN', false)
            ->assertSee('id="cookie-panel"', false);
    }

    public function test_invalid_tracking_ids_are_ignored(): void
    {
        SiteSetting::current()->update(['google_analytics_id' => 'UA-123', 'meta_pixel_id' => '<script>']);

        $this->get('/')->assertDontSee('UA-123')->assertDontSee('GDI_META', false);
    }

    public function test_menu_and_footer_come_from_the_cms(): void
    {
        $header = PageSection::where('page', 'global')->where('section', 'header')->first();
        $header->update(['content' => ['brand_name' => 'GDI Valores', 'links' => [['label' => 'Fondos', 'url' => '/estrategias-de-inversion']]]]);

        $shared = $this->get('/')->viewData('sharedContent');

        $this->assertSame('Fondos', $shared['header']['links'][0]['label']);
        $this->assertSame('Miembro del ecosistema Aura Capital', $shared['footer']['bottom_note']);
    }

    public function test_contact_returns_the_lead_id_for_pixels(): void
    {
        $this->postJson('/contacto', ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Hola'])
            ->assertCreated()
            ->assertJsonStructure(['id', 'message']);
    }

    public function test_recaptcha_is_required_when_enabled(): void
    {
        SiteSetting::current()->update(['recaptcha_enabled' => true, 'recaptcha_site_key' => 'site', 'recaptcha_secret_key' => 'secret']);
        Http::fake(['www.google.com/recaptcha/*' => Http::response(['success' => true, 'action' => 'contact', 'score' => 0.9])]);

        $payload = ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Hola'];

        $this->postJson('/contacto', $payload)->assertUnprocessable()->assertJsonValidationErrors('g-recaptcha-response');
        $this->postJson('/contacto', [...$payload, 'g-recaptcha-response' => 'token'])->assertCreated();
        $this->get('/contacto')->assertSee('recaptcha/api.js?render=site', false);
    }

    public function test_preview_shows_unsaved_changes_without_saving_them(): void
    {
        $this->actingAs(User::factory()->create());

        $result = Livewire::test(ManageInvestors::class)
            ->set('data.hero.title', 'Título en borrador')
            ->call('renderPreview')
            ->effects['returns'][0];

        $this->assertSame(200, $result['status']);
        $this->assertSame('/inversionistas', $result['path']);
        $this->assertStringContainsString('Título en borrador', $result['html']);
        $this->assertNotSame('Título en borrador', PageSection::where('page', 'investors')->where('section', 'hero')->first()->content['title']);
    }

    public function test_article_preview_works_for_unpublished_drafts(): void
    {
        $this->actingAs(User::factory()->create());
        $article = Insight::first();
        $article->update(['is_published' => false]);

        $result = Livewire::test(EditInsight::class, ['record' => $article->getRouteKey()])
            ->set('data.title', 'Borrador de artículo')
            ->call('renderPreview')
            ->effects['returns'][0];

        $this->assertSame(200, $result['status']);
        $this->assertStringContainsString('Borrador de artículo', $result['html']);
        $this->assertFalse($article->fresh()->is_published);
    }

    public function test_seo_dashboard_scores_the_site(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(SeoDashboard::class)->call('analyze')->assertHasNoErrors();

        $audit = SeoAudit::latest('id')->first();
        $this->assertGreaterThanOrEqual(1, $audit->score);
        $this->assertSame(13, $audit->page_count);
        $this->get('/admin/seo-dashboard')->assertOk()->assertSee('Optimización por página');
    }

    public static function settingsPages(): array
    {
        return [['/admin/site-settings'], ['/admin/seo-settings'], ['/admin/seo-dashboard'], ['/admin/manage-navigation']];
    }

    #[DataProvider('settingsPages')]
    public function test_settings_pages_render(string $url): void
    {
        $this->actingAs(User::factory()->create())->get($url)->assertOk();
    }

    public function test_navigation_page_saves_shared_content(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageNavigation::class)
            ->set('data.footer.bottom_note', 'Nota editada')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Nota editada', PageSection::where('page', 'global')->where('section', 'footer')->first()->content['bottom_note']);
    }
}
