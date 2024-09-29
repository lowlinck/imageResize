<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Models\Task;
use App\Jobs\ProcessImageJob;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function upload(UploadRequest $request)
    {
        $validatedData = $request->validated();

        $tasks = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Сохранение во временное хранилище
                $path = $image->store('temp');

                // Создание задачи
                $task = Task::create([
                    'status' => 'pending',
                    'progress' => 0,
                    'image_path' => $path,
                ]);

                // Отправка задания в очередь
                ProcessImageJob::dispatch($task);

                $tasks[] = $task->id;
            }
        } else {
            return back()->withErrors(['images' => 'No images were uploaded.']);
        }

        // Возвращение на страницу с передачей task_ids
        return Inertia::render('Welcome', ['taskIdsFromSession' => $tasks]);
    }
}
