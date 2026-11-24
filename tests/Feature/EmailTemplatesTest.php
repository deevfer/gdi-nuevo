<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageEmails;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class EmailTemplatesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        SiteSetting::current()->update(['mail_enabled' => true, 'mail_mailer' => 'array', 'mail_to_address' => 'equipo@example.com']);
    }

    private function sentMessages()
    {
        return Mail::mailer()->getSymfonyTransport()->messages();
    }

    public function test_contact_sends_branded_notice_and_confirmation(): void
    {
        $this->postJson('/contacto', ['name' => 'Ana López', 'email' => 'ana@example.com', 'subject' => 'Consulta general', 'message' => 'Hola'])->assertCreated();

        [$lead, $confirmation] = [$this->sentMessages()[0]->getOriginalMessage(), $this->sentMessages()[1]->getOriginalMessage()];

        $this->assertSame('Nuevo mensaje desde el sitio · Consulta general', $lead->getSubject());
        $this->assertSame('ana@example.com', $lead->getReplyTo()[0]->getAddress());
        $this->assertStringContainsString('images/gdi/correo/logo-blanco.png', $lead->getHtmlBody());
        $this->assertStringContainsString('Ana López', $lead->getHtmlBody());

        $this->assertSame('Recibimos su mensaje · GDI Valores', $confirmation->getSubject());
        $this->assertSame('equipo@example.com', $confirmation->getReplyTo()[0]->getAddress());
    }

    public function test_texts_edited_in_the_cms_are_used_and_confirmation_can_be_disabled(): void
    {
        PageSection::create(['page' => 'emails', 'section' => 'lead', 'content' => ['subject' => 'Lead web', 'title' => '']]);
        PageSection::create(['page' => 'emails', 'section' => 'confirmation', 'content' => ['enabled' => false]]);

        $this->postJson('/contacto', ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Hola'])->assertCreated();

        $this->assertCount(1, $this->sentMessages());
        $lead = $this->sentMessages()[0]->getOriginalMessage();
        $this->assertSame('Lead web · Ana', $lead->getSubject());
        // Un campo vacío vuelve al texto por defecto.
        $this->assertStringContainsString('Nuevo mensaje', $lead->getHtmlBody());
    }

    public function test_no_email_is_sent_while_sending_is_off(): void
    {
        SiteSetting::current()->update(['mail_enabled' => false]);

        $this->postJson('/contacto', ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Hola'])->assertCreated();

        $this->assertCount(0, $this->sentMessages());
    }

    public function test_email_previews_require_login(): void
    {
        $this->get('/admin/correos/vista-previa/confirmacion')->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create());
        $this->get('/admin/correos/vista-previa/aviso-al-equipo')->assertOk()->assertSee('Ana López')->assertSee('Ver en el CMS');
        $this->get('/admin/correos/vista-previa/confirmacion')->assertOk()->assertSee('Recibimos su mensaje');
        $this->get('/admin/correos/vista-previa/otro')->assertNotFound();
    }

    public function test_cms_preview_shows_the_email_being_edited_without_saving(): void
    {
        $this->actingAs(User::factory()->create());

        $result = Livewire::test(ManageEmails::class)
            ->set('data.confirmation.title', 'Gracias por escribir')
            ->call('renderPreview', 'data.confirmation.title')
            ->effects['returns'][0];

        $this->assertSame('/admin/correos/vista-previa/confirmacion', $result['path']);
        $this->assertStringContainsString('Gracias por escribir', $result['html']);
        $this->assertNull(PageSection::where('page', 'emails')->where('section', 'confirmation')->first());
    }

    public function test_cms_saves_email_texts(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageEmails::class)
            ->assertSet('data.lead.subject', 'Nuevo mensaje desde el sitio')
            ->set('data.lead.subject', 'Nuevo contacto web')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Nuevo contacto web', PageSection::where('page', 'emails')->where('section', 'lead')->first()->content['subject']);
        $this->get('/admin/manage-emails')->assertOk();
    }
}
