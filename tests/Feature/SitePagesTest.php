<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageContact;
use App\Filament\Pages\ManageInvestors;
use App\Filament\Pages\ManageStrategies;
use App\Models\ContactMessage;
use App\Models\Insight;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SitePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
    }

    public static function publicPages(): array
    {
        return [
            'inversionistas' => ['/inversionistas', 'investors', 'Acceso institucional a estrategias administradas'],
            'estrategias' => ['/estrategias-de-inversion', 'strategies', 'GDI Income & Liquidity Fund – USD'],
            'insights' => ['/insights', 'insights', 'Mercado de capitales como alternativa de financiamiento corporativo'],
            'contacto' => ['/contacto', 'contact', 'Envíenos un mensaje'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_new_pages_render_with_cms_content(string $url, string $pageName, string $expectedText): void
    {
        $response = $this->get($url)->assertOk();

        $this->assertSame($pageName, $response->viewData('pageName'));
        $this->assertStringContainsString($expectedText, json_encode($response->viewData('pageContent'), JSON_UNESCAPED_UNICODE));
    }

    public function test_seo_titles_come_from_the_cms(): void
    {
        $this->get('/inversionistas')->assertSee('<title>Inversionistas | GDI Valores</title>', false);
        $this->get('/contacto')->assertSee('<title>Contacto | GDI Valores</title>', false);
    }

    public function test_insights_listing_separates_the_featured_article(): void
    {
        $content = $this->get('/insights')->viewData('pageContent');

        $this->assertSame('mercado-de-capitales-financiamiento-corporativo', $content['featured']['slug']);
        $this->assertCount(5, $content['articles']);
        $this->assertNotContains($content['featured']['slug'], array_column($content['articles'], 'slug'));
    }

    public function test_unpublished_and_future_articles_are_hidden(): void
    {
        Insight::where('slug', 'gestion-disciplinada-de-liquidez')->update(['is_published' => false]);
        Insight::where('slug', 'gobierno-corporativo-en-vehiculos-de-inversion')->update(['published_at' => now()->addWeek()]);

        $slugs = array_column($this->get('/insights')->viewData('pageContent')['articles'], 'slug');

        $this->assertNotContains('gestion-disciplinada-de-liquidez', $slugs);
        $this->assertNotContains('gobierno-corporativo-en-vehiculos-de-inversion', $slugs);
        $this->get('/insights/gestion-disciplinada-de-liquidez')->assertNotFound();
    }

    public function test_article_page_renders_and_unknown_slug_is_404(): void
    {
        $response = $this->get('/insights/due-diligence-en-emisiones-de-deuda-corporativa')->assertOk();

        $this->assertSame('insight', $response->viewData('pageName'));
        $this->assertSame('Due diligence en emisiones de deuda corporativa', $response->viewData('pageContent')['article']['title']);
        $this->assertCount(3, $response->viewData('pageContent')['related']);

        $this->get('/insights/no-existe')->assertNotFound();
    }

    public function test_contact_form_stores_the_message_and_notifies_the_team(): void
    {
        $this->setNotificationEmail('equipo@example.com');

        $this->postJson('/contacto', [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'company' => 'Empresa S.A.',
            'phone' => '+502 5555-5555',
            'subject' => 'Consulta general',
            'message' => 'Quisiera más información.',
        ])->assertCreated()->assertJsonStructure(['message']);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'ana@example.com',
            'subject' => 'Consulta general',
            'read_at' => null,
        ]);

        // phpunit.xml usa el mailer "array": los correos quedan en memoria.
        // Aviso al equipo y confirmación a la persona.
        $sent = Mail::mailer()->getSymfonyTransport()->messages();
        $this->assertCount(2, $sent);
        $this->assertSame('equipo@example.com', $sent[0]->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertSame('ana@example.com', $sent[1]->getEnvelope()->getRecipients()[0]->getAddress());
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $this->postJson('/contacto', ['email' => 'no-es-email'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_form_rejects_bots_that_fill_the_trap_field(): void
    {
        $this->postJson('/contacto', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'spam',
            'website' => 'http://spam.example',
        ])->assertUnprocessable();

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_form_is_rate_limited(): void
    {
        $payload = ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Hola'];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/contacto', $payload)->assertCreated();
        }

        $this->postJson('/contacto', $payload)->assertTooManyRequests();
    }

    public static function adminPages(): array
    {
        return [
            ['/admin/manage-investors'],
            ['/admin/manage-strategies'],
            ['/admin/manage-insights'],
            ['/admin/manage-contact'],
            ['/admin/insights'],
            ['/admin/insights/create'],
            ['/admin/contact-messages'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_admin_pages_render_for_logged_in_users(string $url): void
    {
        $this->actingAs(User::factory()->create())->get($url)->assertOk();
    }

    public function test_admin_menu_follows_the_site_order(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSeeInOrder([
                'Inicio',
                'Plataforma',
                'Financiamiento estructurado',
                'Inversionistas',
                'Estrategias de inversión',
                'Insights',
                'Contacto',
                'Artículos de Insights',
                'Mensajes de contacto',
            ]);
    }

    public function test_admin_can_open_an_article_and_a_message(): void
    {
        $user = User::factory()->create();
        $message = ContactMessage::create(['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Hola']);

        $this->actingAs($user)->get('/admin/insights/'.Insight::first()->id.'/edit')->assertOk();
        $this->actingAs($user)->get('/admin/contact-messages/'.$message->id)->assertOk()->assertSee('Hola');

        $this->assertNotNull($message->fresh()->read_at, 'Abrir el mensaje debe marcarlo como leído.');
    }

    public static function cmsPages(): array
    {
        return [
            'inversionistas' => [ManageInvestors::class, 'investors', '/inversionistas'],
            'estrategias' => [ManageStrategies::class, 'strategies', '/estrategias-de-inversion'],
            'contacto' => [ManageContact::class, 'contact', '/contacto'],
        ];
    }

    #[DataProvider('cmsPages')]
    public function test_saving_from_the_cms_updates_the_public_page(string $page, string $key, string $url): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test($page)
            ->set('data.hero.title', 'Título editado desde el CMS')
            ->call('save')
            ->assertHasNoErrors();

        $hero = PageSection::where('page', $key)->where('section', 'hero')->first()->content;
        $this->assertSame('Título editado desde el CMS', $hero['title']);
        $this->assertNotEmpty($hero['description'], 'Guardar no debe borrar los demás campos.');

        $this->assertSame('Título editado desde el CMS', $this->get($url)->viewData('pageContent')['hero']['title']);
    }

    private function setNotificationEmail(string $email): void
    {
        // En pruebas el envío usa el mailer "array", que guarda los correos en memoria.
        SiteSetting::current()->update(['mail_enabled' => true, 'mail_mailer' => 'array', 'mail_to_address' => $email]);
    }
}
