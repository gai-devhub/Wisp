<?php

namespace Database\Seeders;

use App\Models\MediaFiles;
use App\Models\User;
use App\Models\WishMessages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /** Dummy image filenames for random assignment */
    private array $dummyImages = [
        'img-1.jpg', 'img-2.png', 'img-3.jpg', 'img-4.png', 'img-5.jpg',
        'photo-a.png', 'photo-b.jpg', 'photo-c.png', 'avatar-1.jpg', 'avatar-2.png',
    ];

    /** Dummy music filenames for random assignment */
    private array $dummyMusic = [
        'track-1.mp3', 'track-2.mp3', 'song-a.mp3', 'song-b.mp3', 'music-1.mp3',
        'background-1.mp3', 'background-2.mp3', 'melody-1.mp3', 'melody-2.mp3', 'ambient.mp3',
    ];

    public function run(): void
    {
        $userIds = $this->createUsers();
        $this->createMessages($userIds);
    }

    /** Create 30 users: wisptestuser1@gmail.com / testuser1wisp ... wisptestuser30@gmail.com / testuser30wisp */
    private function createUsers(): array
    {
        $userIds = [];
        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'name' => 'WISP Test User ' . $i,
                'username' => 'wisptestuser' . $i,
                'email' => 'wisptestuser' . $i . '@gmail.com',
                'password' => Hash::make('testuser' . $i . 'wisp'),
                'email_verified_at' => now(),
                'role' => 'user',
                'status' => 'active',
            ]);
            $userIds[] = $user->id;
        }
        return $userIds;
    }

    /** Create 87 messages randomly assigned to the 30 users, with random media. No links, templates, or sent activities. */
    private function createMessages(array $userIds): void
    {
        $types = WishMessages::MESSAGE_TYPES;
        $greetings = ['Hello', 'Hi there', 'Dear', 'Hey', 'Hello there'];
        $messages = [
            'Wishing you all the best on this special day.',
            'Thinking of you and sending warm wishes.',
            'Hope this message finds you well and happy.',
            'Sending love and best wishes your way.',
            'May your day be filled with joy and laughter.',
            'Thank you for being such an amazing person.',
            'Here\'s to many more wonderful moments together.',
        ];

        for ($i = 0; $i < 87; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $recipientName = 'Recipient ' . ($i + 1) . ' ' . fake()->lastName();
            $receivingDate = fake()->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d');

            $msg = WishMessages::create([
                'user_id' => $userId,
                'message_type' => $types[array_rand($types)],
                'title' => 'Message ' . ($i + 1) . ' - ' . fake()->words(2, true),
                'recipient_name' => $recipientName,
                'recipient_special_name' => fake()->firstName(),
                'greeting' => $greetings[array_rand($greetings)],
                'message' => $messages[array_rand($messages)] . ' ' . fake()->sentence(),
                'last_note' => fake()->sentence(),
                'receiving_date' => $receivingDate,
                'sender_name' => fake()->firstName(),
                'expiry_hours' => 24,
                'is_published' => false,
            ]);

            MediaFiles::create([
                'user_id' => $userId,
                'wish_message_id' => $msg->id,
                'recipient_image' => $this->dummyImages[array_rand($this->dummyImages)],
                'background_music' => $this->dummyMusic[array_rand($this->dummyMusic)],
            ]);
        }
    }
}
