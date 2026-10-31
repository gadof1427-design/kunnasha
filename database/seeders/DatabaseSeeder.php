<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed a small demo notebook so the platform can be tried right after `php artisan migrate --seed`.
     * Demo login: demo@kunnasha.test / password123
     *
     * Uses the models directly (no factories), so it also runs in production where Faker
     * is not installed, and it does nothing when the demo data already exists.
     */
    public function run(): void
    {
        if (User::where('email', 'demo@kunnasha.test')->exists()) {
            return;
        }

        $demo = User::create([
            'name' => 'حساب تجريبي',
            'username' => 'demo',
            'email' => 'demo@kunnasha.test',
            'password' => 'password123',
        ]);

        $noura = User::create([
            'name' => 'نورة',
            'username' => 'noura',
            'email' => 'noura@kunnasha.test',
            'password' => 'password123',
        ]);

        /** @var array<int, array{0: User, 1: string, 2: ?string, 3: string}> $quotes */
        $quotes = [
            [$demo, 'العلم صيدٌ والكتابة قيده، قيّد صيودك بالحبال الواثقة.', 'الإمام الشافعي', '#fff8cf'],
            [$noura, 'على قدر أهل العزم تأتي العزائم، وتأتي على قدر الكرام المكارم.', 'المتنبي', '#7c0221'],
            [$demo, 'أعزّ مكانٍ في الدنى سرجُ سابحٍ، وخيرُ جليسٍ في الزمان كتابُ.', 'المتنبي', '#292929'],
            [$noura, 'إذا لم تستطع شيئًا فدعه، وجاوزه إلى ما تستطيع.', 'عمرو بن معديكرب', '#f9e0b0'],
            [$demo, 'الوقت كالسيف، إن لم تقطعه قطعك.', null, '#b6233e'],
            [$noura, 'من جدّ وجد، ومن زرع حصد.', null, '#FFFFFF'],
        ];

        foreach ($quotes as $index => [$user, $content, $author, $color]) {
            $publishedAt = now()->subDays(count($quotes) - $index);

            $user->quotes()->forceCreate([
                'content' => $content,
                'author' => $author,
                'color' => $color,
                'created_at' => $publishedAt,
                'updated_at' => $publishedAt,
            ]);
        }
    }
}
