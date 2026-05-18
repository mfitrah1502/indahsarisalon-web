<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Treatment;

class TreatmentTargetAudienceTest extends TestCase
{
    /**
     * Test target audience matching for registered users.
     */
    public function test_matches_user_with_various_target_audiences(): void
    {
        // 1. Semua (General)
        $tGeneral = new Treatment(['target_audience' => 'Semua (General)']);
        $this->assertTrue($tGeneral->matchesUser(null), 'Guest should see general treatments');
        
        $uRegular = new User();
        $uRegular->setAttribute('cached_total_spending', 0);
        $this->assertTrue($tGeneral->matchesUser($uRegular), 'Regular user should see general treatments');

        // 2. Komunitas (Grup Awal)
        $tCommunity = new Treatment(['target_audience' => 'Komunitas (Grup Awal)']);
        $this->assertFalse($tCommunity->matchesUser(null), 'Guest should NOT see community treatments');
        $this->assertTrue($tCommunity->matchesUser($uRegular), 'Logged-in user should see community treatments');

        // 3. Silver Member (strictly matching only Silver tier)
        $tSilver = new Treatment(['target_audience' => 'Silver Member']);
        
        $uSilver = new User();
        $uSilver->setAttribute('cached_total_spending', 1000000); // Tier: Silver
        
        $uGold = new User();
        $uGold->setAttribute('cached_total_spending', 2000000); // Tier: Gold

        $this->assertFalse($tSilver->matchesUser($uRegular), 'Regular user should NOT see Silver treatments');
        $this->assertTrue($tSilver->matchesUser($uSilver), 'Silver user should see Silver treatments');
        $this->assertFalse($tSilver->matchesUser($uGold), 'Gold user should NOT see Silver treatments (strictly Silver tier only)');

        // 4. Gold Member (strictly matching only Gold tier)
        $tGold = new Treatment(['target_audience' => 'Gold Member']);
        $this->assertFalse($tGold->matchesUser($uSilver), 'Silver user should NOT see Gold treatments');
        $this->assertTrue($tGold->matchesUser($uGold), 'Gold user should see Gold treatments');

        // 5. Platinum Member (strictly matching only Platinum tier)
        $tPlatinum = new Treatment(['target_audience' => 'Platinum Member']);
        
        $uPlatinum = new User();
        $uPlatinum->setAttribute('cached_total_spending', 3000000); // Tier: Platinum

        $this->assertFalse($tPlatinum->matchesUser($uGold), 'Gold user should NOT see Platinum treatments');
        $this->assertTrue($tPlatinum->matchesUser($uPlatinum), 'Platinum user should see Platinum treatments');
    }
}
