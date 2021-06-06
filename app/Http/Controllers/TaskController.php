<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use Inertia\Inertia;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\Order;
use App\Exports\TasksExport;
use App\Exports\TaskExportCsv;
use App\Http\Requests\SwapOrderRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Auth;
use File;
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $taskStatuses = $this->getTaskStatuses();
      $tasks = Task::where('parent_id', 0)
                  ->where('user_id', Auth::user()->id)
                  ->join('orders', 'orders.id', '=', 'tasks.order_id')
                  ->orderBy('orders.order' , 'asc')
                  ->orderBy('tasks.updated_at' , 'desc')
                  ->with(['deep_sub_tasks' => function ($q) {
                    $q->join('orders', 'orders.id', '=', 'tasks.order_id');
                    $q->orderBy('orders.order' , 'asc');
                    $q->orderBy('tasks.updated_at' , 'desc');
                    $q->select('tasks.*');
                  }])
                  ->select('tasks.*')
                  ->get();

      return Inertia::render('Task/Index', [
        'head' => [
          'title' => 'My Tasks'
        ],
        'initialTasks' => $tasks,
        'statuses' => $taskStatuses['statuses'],
        'colors' => $taskStatuses['colors']
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request) {
      $parent_id = $request->input('parent_id') ? $request->input('parent_id') : 0;
      // find or create status
      $status = $this->firstOrCreateStatus($request->input('status'));
      // create task
      $created = Task::create([
        'parent_id' => $request->input('parent_id', 0),
        'task_status_id' => $status->id,
        'user_id' =>  Auth::user()->id,
        'task' => $request->input('task')
      ]);
      $created_order = $this->createOrder($created);
      $created->order_id = $created_order->id;
      $created->save();
      // load relation
      $created->deep_sub_tasks;
      return $created;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, $id)
    {
        // find or create status
        $status = $this->firstOrCreateStatus($request->input('status'));
        // update task
        $update = Task::find($id);
        $update->task = $request->input('task');
        $update->task_status_id = $status->id;
        $update->parent_id = $request->input('parent_id', 0);
        $update->save();
        return $update;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
      $task = Task::find($id);
      if ($task) {
        $task->delete();
      }
      return response()->json([
        'message' => 'Delete Successful',
        'id' => $id,
        'task' => $task
      ]);
    }
    /**
     * Restore Deleted Ids
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id) {
      // find task
      $restored = Task::withTrashed()->findOrFail($id);
      $restored->restore();
      // create new order for restored task
      $restored_order = $this->createOrder($restored);
      $restored->order_id = $restored_order->id;
      $restored->save();
      return response()->json([
        'message' => 'Task successfully restored',
        'id' => $id
      ]);
    }
    /**
     * Swap Order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function swapOrder(SwapOrderRequest $request) {
      $firstTask = $request->input('firstTask');
      $secondTask = $request->input('secondTask');
      Task::where('id', $firstTask['id'])->update([
        'order_id'=> $secondTask['order_id']
      ]);
      Task::where('id', $secondTask['id'])->update([
        'order_id'=> $firstTask['order_id']
      ]);
      return redirect('/tasks');
    }
    /**
     * Display listing of deleted tasks
     * @return \Illuminate\Http\Response
     */
    public function viewDeleted ($parent_id) {
      if ($parent_id === 'main') { $parent_id = 0; }
      else if ((int)$parent_id === 0) {
        // redirect to 404
        return abort(404);
      }
      $tasks = Task::withTrashed()
                ->where('deleted_at', '!=', null)
                ->where('parent_id', $parent_id)
                ->orderBy('deleted_at', 'desc')
                ->get();
      $taskStatuses = $this->getTaskStatuses();

      return Inertia::render('Task/Deleted', [
        'head' => [
          'title' => 'Deleted Tasks'
        ],
        'initialTasks' => $tasks,
        'statuses' => $taskStatuses['statuses'],
        'colors' => $taskStatuses['colors'],
        'parent_id' => $parent_id
      ]);
    }

    /**
     * Download tasks as excel.
     *
     * @return ExcelFile
     */
    public function exportExcel () {
      return Excel::download(new TasksExport, 'tasks.xlsx');
    }
    /**
     * Download tasks as json.
     *
     * @return File
     */
    public function exportJson () {
      $tasks = Task::where('parent_id', 0)
                  ->where('user_id', Auth::user()->id)
                  ->join('orders', 'orders.id', '=', 'tasks.order_id')
                  ->orderBy('orders.order' , 'asc')
                  ->orderBy('tasks.updated_at' , 'desc')
                  ->with(['deep_sub_tasks' => function ($q) {
                    $q->join('orders', 'orders.id', '=', 'tasks.order_id');
                    $q->orderBy('orders.order' , 'asc');
                    $q->orderBy('tasks.updated_at' , 'desc');
                    $q->select('tasks.*');
                  }])
                  ->select('tasks.*')
                  ->get();
      $file_name = 'tasks_'. time() .'.json';
      Storage::disk('public')->put($file_name, $tasks);
      $headers =[
          'Content-Type' => 'application/json',
      ];
      return Storage::disk('public')->download($file_name, $file_name, $headers);
    }
    /**
     * Download tasks as csv.
     *
     * @return File
     */
    public function exportCsv () {
      return (new TaskExportCsv)->download('tasks.csv');
    }
    /**
     * Create order based on created task.
     * @param  Task
     * @return Order
     */
    private function createOrder ($created) {
      // find max order of the tasks
      $max = Order::where('task_parent_id', $created->parent_id)->max('order');
      $max = !$max ? 1 : $max + 1;
      return Order::create([
        'task_parent_id' =>  $created->parent_id,
        'task_id' => $created->id,
        'order' => $max
      ]);
    }

    private function getTaskStatuses () {
      $taskStatuses = TaskStatus::select('status', 'color')
                      ->get();
      $statuses = [];
      $colors = [];
      $options = ['Pending', 'Cancel', 'Complete'];
      foreach ($taskStatuses as $key => $value) {
        if (in_array($value->status, $options))
        $statuses[] = $value->status;
        $colors[$value->status] = $value->color;
      }
      return ['statuses' => $statuses, 'colors' => $colors];
    }

    private function firstOrCreateStatus ($status) {
      // find or create status
      $status = TaskStatus::select('id')->firstOrCreate(
        ['status' =>  ucwords($status)],
        ['color' =>  $this->generateRandomColor()]
      );
      return $status;
    }

    private function generateRandomColor () {
      $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
      $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
      return $color;
    }
}
