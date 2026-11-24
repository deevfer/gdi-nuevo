<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\EditProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_are_webmasters_by_default(): void
    {
        $this->assertSame(UserRole::Webmaster, User::factory()->create()->role);
        $this->assertSame(UserRole::Webmaster, User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'secreto123'])->fresh()->role);
    }

    public function test_role_cannot_be_mass_assigned(): void
    {
        $user = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'secreto123', 'role' => 'super_admin']);

        $this->assertSame(UserRole::Webmaster, $user->fresh()->role);
    }

    public function test_only_the_super_admin_sees_the_users_module(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/users')->assertForbidden();
        $this->actingAs(User::factory()->create())->get('/admin')->assertOk()->assertDontSee('admin/users');

        $this->actingAs(User::factory()->superAdmin()->create())->get('/admin/users')->assertOk();
    }

    public function test_webmasters_cannot_create_or_edit_users(): void
    {
        $other = User::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->get('/admin/users/create')->assertForbidden();
        $this->get("/admin/users/{$other->id}/edit")->assertForbidden();
    }

    public function test_super_admin_creates_webmasters(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Laura Webmaster', 'email' => 'laura@example.com', 'password' => 'clave-segura-1', 'password_confirmation' => 'clave-segura-1'])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'laura@example.com')->first();
        $this->assertSame(UserRole::Webmaster, $user->role);
        $this->assertTrue(Hash::check('clave-segura-1', $user->password));
    }

    public function test_password_must_be_confirmed_and_long_enough(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Laura', 'email' => 'laura@example.com', 'password' => 'corta', 'password_confirmation' => 'otra'])
            ->call('create')
            ->assertHasFormErrors(['password']);
    }

    public function test_super_admin_edits_any_user_and_empty_password_keeps_the_current_one(): void
    {
        $webmaster = User::factory()->create(['password' => 'original-123']);
        $this->actingAs(User::factory()->superAdmin()->create());

        Livewire::test(EditUser::class, ['record' => $webmaster->getRouteKey()])
            ->fillForm(['name' => 'Nombre nuevo', 'password' => '', 'password_confirmation' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $webmaster->refresh();
        $this->assertSame('Nombre nuevo', $webmaster->name);
        $this->assertTrue(Hash::check('original-123', $webmaster->password));
    }

    public function test_super_admin_deletes_webmasters_but_never_the_super_admin(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $webmaster = User::factory()->create();
        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->assertActionHidden(TestAction::make('delete')->table($admin))
            ->assertActionVisible(TestAction::make('delete')->table($webmaster));
        $this->assertFalse($admin->can('delete', $admin));

        Livewire::test(EditUser::class, ['record' => $webmaster->getRouteKey()])->callAction(DeleteAction::class);
        $this->assertModelMissing($webmaster);
    }

    public function test_each_webmaster_edits_only_their_own_profile(): void
    {
        $webmaster = User::factory()->create(['password' => 'actual-123']);
        $this->actingAs($webmaster)->get('/admin/profile')->assertOk();

        Livewire::test(EditProfile::class)
            ->fillForm(['name' => 'Mi nombre', 'currentPassword' => 'actual-123', 'password' => 'nueva-clave-1', 'passwordConfirmation' => 'nueva-clave-1'])
            ->call('save')
            ->assertHasNoFormErrors();

        $webmaster->refresh();
        $this->assertSame('Mi nombre', $webmaster->name);
        $this->assertTrue(Hash::check('nueva-clave-1', $webmaster->password));
        $this->assertSame(UserRole::Webmaster, $webmaster->role);
    }

    public function test_command_assigns_a_single_super_admin(): void
    {
        $first = User::factory()->superAdmin()->create();
        $second = User::factory()->create(['email' => 'nuevo@example.com']);

        $this->artisan('gdi:super-admin', ['email' => 'nuevo@example.com'])->assertSuccessful();

        $this->assertSame(UserRole::SuperAdmin, $second->fresh()->role);
        $this->assertSame(UserRole::Webmaster, $first->fresh()->role);
    }
}
