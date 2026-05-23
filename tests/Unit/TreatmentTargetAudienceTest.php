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
        // --- 1. GENERAL AUDIENCE ---
        $tGeneralLegacy = new Treatment(['target_audience' => 'Semua (General)']);
        $tGeneralNew = new Treatment(['target_audience' => 'general']);
        
        $this->assertTrue($tGeneralLegacy->matchesUser(null), 'Guest should see legacy general treatments');
        $this->assertTrue($tGeneralNew->matchesUser(null), 'Guest should see new general treatments');
        
        $uRegular = new User();
        $uRegular->setAttribute('cached_total_spending', 0);
        $this->assertTrue($tGeneralLegacy->matchesUser($uRegular), 'Regular user should see legacy general treatments');
        $this->assertTrue($tGeneralNew->matchesUser($uRegular), 'Regular user should see new general treatments');

        // --- 2. SILVER AUDIENCE ---
        $tSilverLegacy = new Treatment(['target_audience' => 'Silver Member']);
        $tSilverNew = new Treatment(['target_audience' => 'silver']);
        
        $uSilver = new User();
        $uSilver->setAttribute('cached_total_spending', 1000000); // Tier: Silver
        
        $uGold = new User();
        $uGold->setAttribute('cached_total_spending', 2000000); // Tier: Gold

        $this->assertFalse($tSilverLegacy->matchesUser($uRegular), 'Regular user should NOT see legacy Silver treatments');
        $this->assertFalse($tSilverNew->matchesUser($uRegular), 'Regular user should NOT see new Silver treatments');
        $this->assertTrue($tSilverLegacy->matchesUser($uSilver), 'Silver user should see legacy Silver treatments');
        $this->assertTrue($tSilverNew->matchesUser($uSilver), 'Silver user should see new Silver treatments');
        $this->assertFalse($tSilverLegacy->matchesUser($uGold), 'Gold user should NOT see legacy Silver treatments');
        $this->assertFalse($tSilverNew->matchesUser($uGold), 'Gold user should NOT see new Silver treatments');

        // --- 3. GOLD AUDIENCE ---
        $tGoldLegacy = new Treatment(['target_audience' => 'Gold Member']);
        $tGoldNew = new Treatment(['target_audience' => 'gold']);
        
        $this->assertFalse($tGoldLegacy->matchesUser($uSilver), 'Silver user should NOT see legacy Gold treatments');
        $this->assertFalse($tGoldNew->matchesUser($uSilver), 'Silver user should NOT see new Gold treatments');
        $this->assertTrue($tGoldLegacy->matchesUser($uGold), 'Gold user should see legacy Gold treatments');
        $this->assertTrue($tGoldNew->matchesUser($uGold), 'Gold user should see new Gold treatments');

        // --- 4. PLATINUM AUDIENCE ---
        $tPlatinumLegacy = new Treatment(['target_audience' => 'Platinum Member']);
        $tPlatinumNew = new Treatment(['target_audience' => 'platinum']);
        
        $uPlatinum = new User();
        $uPlatinum->setAttribute('cached_total_spending', 3000000); // Tier: Platinum

        $this->assertFalse($tPlatinumLegacy->matchesUser($uGold), 'Gold user should NOT see legacy Platinum treatments');
        $this->assertFalse($tPlatinumNew->matchesUser($uGold), 'Gold user should NOT see new Platinum treatments');
        $this->assertTrue($tPlatinumLegacy->matchesUser($uPlatinum), 'Platinum user should see legacy Platinum treatments');
        $this->assertTrue($tPlatinumNew->matchesUser($uPlatinum), 'Platinum user should see new Platinum treatments');

        // --- 5. COMMUNITY AUDIENCE ---
        $tCommunityLegacy = new Treatment(['target_audience' => 'Komunitas (Grup Awal)']);
        $tCommunityLegacy2 = new Treatment(['target_audience' => 'Komunitas']);
        $tCommunityNew = new Treatment(['target_audience' => 'community']);

        $this->assertFalse($tCommunityLegacy->matchesUser(null), 'Guest should NOT see legacy Komunitas (Grup Awal) treatments');
        $this->assertFalse($tCommunityLegacy2->matchesUser(null), 'Guest should NOT see legacy Komunitas treatments');
        $this->assertFalse($tCommunityNew->matchesUser(null), 'Guest should NOT see new community treatments');

        $this->assertTrue($tCommunityLegacy->matchesUser($uRegular), 'Logged-in user should see legacy Komunitas (Grup Awal) treatments');
        $this->assertTrue($tCommunityLegacy2->matchesUser($uRegular), 'Logged-in user should see legacy Komunitas treatments');
        $this->assertTrue($tCommunityNew->matchesUser($uRegular), 'Logged-in user should see new community treatments');
    }
}
