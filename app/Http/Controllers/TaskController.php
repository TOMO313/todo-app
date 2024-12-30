<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    public function getCompletedTask()
    {
        $userId = Auth::id();
        $completedCount = Task::where('user_id', $userId)->where('is_completed', true)->count();
        $notCompletedCount = Task::where('user_id', $userId)->where('is_completed', false)->count();

        $data = ['completedCount' => $completedCount, 'notCompletedCount' => $notCompletedCount]; //blade側で2回@jsをしなくてはならないため、PHP配列として格納

        return view('tasks/index', compact('data'));
    }

    public function taskStore(TaskRequest $request, Task $task)
    {
        //フォームリクエストによるバリデーションは、バリデーション済みデータの取得を行う
        $validated = $request->validated();

        $task->user_id = Auth::id();
        $task->task_title = $validated['task_title'];
        $task->task_description = $validated['task_description'];
        $task->start_date = $validated['start_date'];
        $task->end_date = $validated['end_date']; //date("Y-m-d H:i:s", strtotime("{$request->input('end_date')} + 1 day"));は、date()の第1引数で形式を指定、第2引数のstrtotime()でFullCalendarの終了日1日ズレを修正(""の中で変数を使う場合は{$}と記述)。とあるが、1日ズレ無し。
        $task->task_color = $request->input('task_color');
        $task->is_completed = false; //DB上は0がfalse、1がtrue
        $task->save();

        return redirect(route('dashboard'));
    }

    public function getTask(Request $request, Task $task)
    {
        $start_date = date("Y-m-d H:i:s", $request->input('start_date') / 1000); //valueOf()がミリ秒単位に変換しているため秒に変換
        $end_date = date("Y-m-d H:i:s", $request->input('end_date') / 1000);

        //query()を使うとコードの見栄えが良くなるし、DB処理の開始が分かりやすい
        $tasks = $task->query()
            //select()は指定したカラムのみ取得
            ->select(
                //https://fullcalendar.io/docs/event-parsing、response.dataに渡されるプロパティを指定(as title)
                'id',
                'task_title as title',
                'task_description as description',
                'start_date as start',
                'end_date as end',
                'task_color as backgroundColor',
            )
            ->where('start_date', '>', $start_date)
            ->where('end_date', '<', $end_date)
            ->where('user_id', '=', Auth::id())
            ->where('is_completed', '=', false)
            ->get();

        return response()->json($tasks);
    }

    public function updateTask(TaskRequest $request)
    {
        $validated = $request->validated();
        $task = Task::find($request->input('task_id'));
        $task->task_title = $validated['task_title'];
        $task->task_description = $validated['task_description'];
        $task->start_date = $validated['start_date'];
        $task->end_date = $validated['end_date'];
        $task->task_color = $request->input('task_color');
        $task->is_completed = filter_var($request->input('is_completed'), FILTER_VALIDATE_BOOLEAN); //filter_var(フィルタリングする値, 適用するフィルター)。FILTER_VLIDATE_BOOLEANは"1"、"true"、"on"、"yes"の場合にtrue、それ以外はfalseを返す。
        $task->save();

        return redirect(route('dashboard'));
    }

    public function deleteTask(Request $request)
    {
        Task::find($request->input('delete_task_id'))->delete();

        return redirect(route('dashboard'));
    }
}
