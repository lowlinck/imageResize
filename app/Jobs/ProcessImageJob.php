<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;
    protected $manager; // Добавлено свойство manager

    public function __construct(Task $task)
    {
// Правильное присвоение менеджера как свойства класса
        $this->manager = new ImageManager(new Driver());
        $this->task = $task;
    }

    public function handle()
    {
        try {
            $this->task->update(['status' => 'in_progress', 'progress' => 0]);

//        dd(storage_path('app/private/' . $this->task->image_path));
//// Открываем изображение через менеджер
            $image = $this->manager->read(storage_path('app/private/' . $this->task->image_path));

// Изменяем размер изображения
            $image->scale(400, 600);

// Сохраняем обработанное изображение
            $newPath =  basename($this->task->image_path);

            $image->toJpeg()->save(storage_path('app/public/' . $newPath));

// Обновляем путь к изображению
            $this->task->update([
                'status' => 'success',
                'progress' => 100,
                'image_path' => $newPath,
            ]);

// Удаляем временный файл
            Storage::delete($this->task->image_path);
        } catch (\Exception $e) {
            $this->task->update(['status' => 'failed']);
            \Log::error('Image processing failed: ' . $e->getMessage());
        }
    }
}
