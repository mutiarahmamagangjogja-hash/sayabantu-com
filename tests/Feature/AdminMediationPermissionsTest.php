<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMediationPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_confirm_mediation_outcome_without_deciding_party_proposals(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])->get(route('admin.pengaduan'));

        $response->assertOk()
            ->assertSee('Tidak Tercapai Kesepakatan')
            ->assertSee('Selesaikan Mediasi')
            ->assertSee('!proposal?.customerApproved || !proposal?.mitraApproved')
            ->assertDontSee('Setujui Usulan (Customer)')
            ->assertDontSee('Ajukan Perubahan')
            ->assertDontSee('function approveCustomerProposal');
    }

    public function test_customer_and_mitra_see_failed_mediation_as_recorded_not_waiting_for_admin_decision(): void
    {
        $customerResponse = $this->get(route('customer.pengaduan'));
        $mitraResponse = $this->get(route('mitra.dashboard'));

        $customerResponse->assertOk()
            ->assertSee('Mediasi Tidak Tercapai')
            ->assertSee('Admin telah mencatat hasil mediasi')
            ->assertDontSee('Keputusan Admin');

        $mitraResponse->assertOk()
            ->assertSee('Mediasi Tidak Tercapai')
            ->assertSee('Admin telah mencatat hasil mediasi')
            ->assertDontSee('Keputusan Admin')
            ->assertDontSee('keputusan otoritatif');
    }
}
