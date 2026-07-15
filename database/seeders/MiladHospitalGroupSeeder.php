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
            ['first_name' => 'میرمحمد', 'last_name' => 'جلالی', 'phone' => '09151234567', 'normal_stock_count' => 623, 'prefered_stock_count' => 207],
            ['first_name' => 'اکبر', 'last_name' => 'مهرموحد', 'phone' => '09191234567', 'normal_stock_count' => 758, 'prefered_stock_count' => 252],
            ['first_name' => 'ربابه', 'last_name' => 'سلیمانی', 'phone' => '09181234567', 'normal_stock_count' => 315, 'prefered_stock_count' => 105],
            ['first_name' => 'زهرا', 'last_name' => 'اخوان', 'phone' => '09171234567', 'normal_stock_count' => 825, 'prefered_stock_count' => 275],
            ['first_name' => 'سیاووش', 'last_name' => 'ولی زاده', 'phone' => '09161234567', 'normal_stock_count' => 525, 'prefered_stock_count' => 175],
            ['first_name' => 'اسماعیل', 'last_name' => 'مشتاق', 'phone' => '09141234567', 'normal_stock_count' => 450, 'prefered_stock_count' => 150],
            ['first_name' => 'حامد', 'last_name' => 'خرازی', 'phone' => '09131234567', 'normal_stock_count' => 195, 'prefered_stock_count' => 65],
            ['first_name' => 'علیرضا', 'last_name' => 'لطیفی تبریزی', 'phone' => '09121234567', 'normal_stock_count' => 622, 'prefered_stock_count' => 208],
            ['first_name' => 'میرجلال', 'last_name' => 'جلالی', 'phone' => '09113422292', 'normal_stock_count' => 975, 'prefered_stock_count' => 325],
            ['first_name' => 'احمد', 'last_name' => 'اکرامی', 'phone' => '09111414408', 'normal_stock_count' => 712, 'prefered_stock_count' => 238],
        ];

        foreach ($members as $index => $member) {
            $nationalcode = str_pad((string) (1000000000 + $index), 10, '0', STR_PAD_LEFT);

            $user = User::firstOrCreate(
                ['phone' => $member['phone']],
                [
                    'first_name' => $member['first_name'],
                    'last_name' => $member['last_name'],
                    'nationalcode' => $nationalcode,
                    'password' => bcrypt(substr($member['phone'], -4)),
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
