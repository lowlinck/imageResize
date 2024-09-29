<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ImageUploadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_upload()
    {
        $response = $this->post('/upload-images', [
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ],
        ]);

        // Измените статус или перенаправление в зависимости от логики вашего контроллера
        $response->assertStatus(200);  // или $response->assertRedirect('/');

        $this->assertDatabaseCount('tasks', 2);
    }
}

