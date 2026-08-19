<?php

namespace Tests\Feature;

use App\Models\Puskesmas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PuskesmasBulkActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puskesmas_unit_layanan_index_has_bulk_selection_controls(): void
    {
        $puskesmas = Puskesmas::factory()->create([
            'nama' => 'Puskesmas Contoh',
            'jenis' => 'puskesmas',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'puskesmas_id' => $puskesmas->id,
            'is_active' => true,
        ]);
        $admin->assignRole('admin-puskesmas');

        $this->actingAs($admin)
            ->get(route('puskesmas.unit-layanan.index'))
            ->assertOk()
            ->assertSee('id="pilih-semua"')
            ->assertSee('name="dipilih[]"')
            ->assertSee('id="form-aksi-massal"')
            ->assertSee('Hapus Terpilih')
            ->assertDontSee('Nonaktifkan Terpilih');
    }

    public function test_admin_puskesmas_unit_layanan_bulk_delete_requires_selection(): void
    {
        $puskesmas = Puskesmas::factory()->create([
            'nama' => 'Puskesmas Contoh',
            'jenis' => 'puskesmas',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'puskesmas_id' => $puskesmas->id,
            'is_active' => true,
        ]);
        $admin->assignRole('admin-puskesmas');

        $this->actingAs($admin)
            ->from(route('puskesmas.unit-layanan.index'))
            ->post(route('puskesmas.unit-layanan.aksi-massal'), [
                'aksi' => 'hapus',
            ])
            ->assertRedirect(route('puskesmas.unit-layanan.index'))
            ->assertSessionHas('error', 'Pilih minimal satu unit layanan untuk dihapus.');
    }

    public function test_admin_puskesmas_pertanyaan_index_has_bulk_delete_only_action(): void
    {
        $puskesmas = Puskesmas::factory()->create([
            'nama' => 'Puskesmas Contoh',
            'jenis' => 'puskesmas',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'puskesmas_id' => $puskesmas->id,
            'is_active' => true,
        ]);
        $admin->assignRole('admin-puskesmas');

        $this->actingAs($admin)
            ->get(route('puskesmas.pertanyaan.index'))
            ->assertOk()
            ->assertSee('Hapus Terpilih')
            ->assertDontSee('Nonaktifkan Terpilih');
    }
}
