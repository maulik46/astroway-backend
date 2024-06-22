<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminPages;

class ChangeMenuConfiguration extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $astroRecord = AdminPages::where('pageName', 'Astrologers')->first();
        $menuId = $astroRecord->id;
        AdminPages::where('id', $menuId)->update(['route' => null]);
        // AstroMall
        $aArraySet = array('astrologerReview', 'gifts', 'skills', 'astrologerCategories', 'commissions');
        AdminPages::whereIn('route', $aArraySet)->delete();
        $aValue = [
            'pageName' => 'Manage astrologers',
            'pageGroup' => $menuId,
            'icon' => null,
            'route' => 'astrologers',
            'displayOrder' => null
        ];
        AdminPages::create($aValue);

        $aValue = [
            'pageName' => 'Pending Requests',
            'pageGroup' => $menuId,
            'icon' => null,
            'route' => 'pending-requests',
            'displayOrder' => null
        ];
        AdminPages::create($aValue);

        $aValue = [
            'pageName' => 'Reviews',
            'pageGroup' => $menuId,
            'icon' => null,
            'route' => 'astrologerReview',
            'displayOrder' => null
        ];
        AdminPages::create($aValue);

        $aValue = [
            'pageName' => 'Gifts',
            'pageGroup' => $menuId,
            'icon' => null,
            'route' => 'gifts',
            'displayOrder' => null
        ];
        AdminPages::create($aValue);

        $aValue = [
            'pageName' => 'Skills',
            'pageGroup' => $menuId,
            'icon' => null,
            'route' => 'skills',
            'displayOrder' => null
        ];
        AdminPages::create($aValue);

        $aValue = [
            'pageName' => 'Categories',
            'pageGroup' => $menuId,
            'icon' => null,
            'route' => 'astrologerCategories',
            'displayOrder' => null
        ];
        AdminPages::create($aValue);

        $aValue = [
            'pageName' => 'Commissionn rate for calls/ chats',
            'pageGroup' => $menuId,
            'icon' => null,
            'route' => 'commissions',
            'displayOrder' => null
        ];
        AdminPages::create($aValue);
    }
}
