<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use Inertia\Inertia;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\Order;
use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $statuses = TaskStatus::select('status')
                  ->where('status', 'pending')
                  ->orWhere('status', 'complete')
                  ->orWhere('status', 'cancel')
                  ->pluck('status')->toArray();
      $tasks = Task::where('parent_id', 0)
                  ->where('user_id', Auth::user()->id)
                  ->join('orders', 'orders.id', '=', 'tasks.order_id')
                  ->orderBy('orders.order' , 'asc')
                  ->orderBy('tasks.updated_at' , 'desc')
                  ->with('deep_sub_tasks')
                  ->get();
      return Inertia::render('Task/Index', [
        'head' => [
          'title' => 'My Tasks'
        ],
        'initialTasks' => $tasks,
        'order' => Order::all(),
        'statuses' => $statuses
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
      $parent_id = $request->input('parent_id') ? $request->input('parent_id') : 0;
      // find or create status
      $status = TaskStatus::select('id')->firstOrCreate([
        'status' =>  $request->input('status')
      ]);

      // create task
      $created = Task::create([
        'parent_id' =>  $parent_id,
        'task_status_id' => $status->id,
        'user_id' =>  Auth::user()->id,
        'task' => $request->input('task')
      ]);
      $created_order = $this->createOrder($created);
      $created->order_id = $created_order->id;
      $created->save();
      $created->order = $created_order->order;
      // load relation
      $created->deep_sub_tasks;
      return $created;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $parent_id = $request->input('parent_id') ? $request->input('parent_id') : 0;
        // find or create status
        $status = TaskStatus::select('id')->firstOrCreate([
          'status' =>  $request->input('status')
        ]);
        // update task
        $update = Task::find($id);
        $update->task = $request->input('task');
        $update->task_status_id = $status->id;
        $update->parent_id = $parent_id;
        $update->save();
        return $update;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Task::find($id)->delete();
      return response()->json([
        'message' => 'Delete Successful',
        'id' => $id
      ]);
    }
    /**
     * Restore Deleted Ids
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
      Task::withTrashed()->find($id)->restore();
      return response()->json([
        'message' => 'Task successfully restored',
        'id' => $id
      ]);
    }
    /**
     * Display listing of deleted tasks
     * @return \Illuminate\Http\Response
     */
    public function viewDeleted ($parent_id) {
      if ($parent_id === 'main') {
        $parent_id = 0;
      } else if ((int)$parent_id === 0) {
        // redirect to 404
        return abort(404);
      }
      $tasks = Task::withTrashed()
                ->where('deleted_at', '!=', null)
                ->where('parent_id', $parent_id)
                ->orderBy('deleted_at', 'desc')
                ->get();
      return Inertia::render('Task/Deleted', [
        'head' => [
          'title' => 'Deleted Tasks'
        ],
        'initialTasks' => $tasks,
      ]);
    }

    /**
     * Download tasks as excel.
     *
     * @return ExcelFile
     */
    public function export () {
      return Excel::download(new TasksExport, 'tasks.xlsx');
    }

    /**
     * Create order based on created task.
     * @param  Task
     * @return \Illuminate\Http\Response
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
}
