
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Jobs\ProcessImageJob;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $tasks = [];

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

        // Возвращение на страницу с передачей task_ids
        return redirect()->route('home')->with('task_ids', $tasks);
    }
}
