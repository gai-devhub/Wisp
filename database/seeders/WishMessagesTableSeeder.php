<?php

namespace Database\Seeders;

use App\Models\WishMessages;
use App\Models\User;
use Illuminate\Database\Seeder;

class WishMessagesTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        
        if ($user) {
            WishMessages::create([
                'user_id' => $user->id,
                'title' => 'Sarah\'s Birthday',
                'recipient_name' => 'Sarah Johnson',
                'recipient_special_name' => 'Sarah',
                'greeting' => 'Hello Princess',
                'message' => 'On your special day, I wish you all the joy, success, and happiness in the world. You deserve nothing but the best today and always! May this year bring you closer to your dreams and fill your life with beautiful moments.',
                'last_note' => 'Happy Birthday Love!',
                'sender_name' => 'John',
                'receiving_date' => now()->format('Y-m-d'),
                'slug' => 'sarah-johnson-' . substr(md5(uniqid()), 0, 6),
                'expiry_hours' => 24,
                'expires_at' => now()->addHours(24),
                'is_published' => true,
            ]);
        }
    }
}