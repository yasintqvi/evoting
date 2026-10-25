<?php

namespace Database\Seeders;

use App\Enums\GroupStatus;
use App\Enums\GroupType;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class MiladHospitalGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::query()->first();

        if (!$owner) {
            $this->command?->error('هیچ کاربری در دیتابیس یافت نشد؛ ابتدا یک کاربر ادمین بسازید.');

            return;
        }

        $group = Group::firstOrCreate(
            ['title' => 'بیمارستان میلاد'],
            [
                'description' => 'گروه اعضای بیمارستان میلاد',
                'owner_id' => $owner->id,
                'status' => GroupStatus::ENABLE,
                'type' => GroupType::SPECIAL,
                'logo' => 'assets/img/group.jpg',
                'normal_stock_count' => 6000,
                'prefered_stock_count' => 2000,
                'prefered_stock_weight' => 7,
            ]
        );

        $group->update([
            'normal_stock_count' => 6000,
            'prefered_stock_count' => 2000,
            'prefered_stock_weight' => 7,
        ]);

        $group->users()->syncWithoutDetaching([
            $owner->id => [
                'normal_stock_count' => 0,
                'prefered_stock_count' => 0,
            ],
        ]);

        $members = [
            ['first_name' => 'میرمحمد', 'last_name' => 'جلالی', 'phone' => '09111318776', 'nationalcode' => '2738354181', 'normal_stock_count' => 623, 'prefered_stock_count' => 207],
            ['first_name' => 'اکبر', 'last_name' => 'مهرموحد', 'phone' => '09111414251', 'nationalcode' => '3871505722', 'normal_stock_count' => 758, 'prefered_stock_count' => 252],
            ['first_name' => 'ربابه', 'last_name' => 'سلیمانی', 'phone' => '09111362747', 'nationalcode' => '4193613374', 'normal_stock_count' => 315, 'prefered_stock_count' => 105],
            ['first_name' => 'زهرا', 'last_name' => 'اخوان', 'phone' => '09111413169', 'nationalcode' => '2739185111', 'normal_stock_count' => 825, 'prefered_stock_count' => 275],
            ['first_name' => 'سیاووش', 'last_name' => 'ولی زاده', 'phone' => '09113410292', 'nationalcode' => '2229076078', 'normal_stock_count' => 525, 'prefered_stock_count' => 175],
            ['first_name' => 'اسماعیل', 'last_name' => 'مشتاق', 'phone' => '09111418214', 'nationalcode' => '2690784696', 'normal_stock_count' => 450, 'prefered_stock_count' => 150],
            ['first_name' => 'حامد', 'last_name' => 'خرازی', 'phone' => '09111310133', 'nationalcode' => '5188823111', 'normal_stock_count' => 195, 'prefered_stock_count' => 65],
            ['first_name' => 'علیرضا', 'last_name' => 'لطیفی تبریزی', 'phone' => '09123193420', 'nationalcode' => '0070817499', 'normal_stock_count' => 622, 'prefered_stock_count' => 208],
            ['first_name' => 'میرجلال', 'last_name' => 'جلالی', 'phone' => '09113422292', 'nationalcode' => '2739184379', 'normal_stock_count' => 975, 'prefered_stock_count' => 325],
            ['first_name' => 'احمد', 'last_name' => 'اکرامی', 'phone' => '09123161681', 'nationalcode' => '2721487167', 'normal_stock_count' => 712, 'prefered_stock_count' => 238],
        ];

        foreach ($members as $member) {
            $user = User::updateOrCreate(
                ['phone' => $member['phone']],
                [
                    'first_name' => $member['first_name'],
                    'last_name' => $member['last_name'],
                    'nationalcode' => $member['nationalcode'],
                ]
            );

            $group->users()->syncWithoutDetaching([
                $user->id => [
                    'normal_stock_count' => $member['normal_stock_count'],
                    'prefered_stock_count' => $member['prefered_stock_count'],
                ],
            ]);
        }

        $this->command?->info('گروه بیمارستان میلاد با '.count($members).' عضو با موفقیت ساخته شد.');
    }
}
