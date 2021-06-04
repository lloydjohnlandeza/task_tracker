<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use Inertia\Inertia;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\Order;
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
                  ->join('orders', 'orders.id', '=', 'tasks.order_id')
                  ->orderBy('orders.order' , 'asc')
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

    // private function getTasksWithSubtask () {
    //   $tasks = Task::where('parent_id', 0)
    //   ->join('orders', 'orders.id', '=', 'tasks.order_id')
    //   ->orderBy('orders.order' , 'asc')
    //   ->get();
    //   foreach ($tasks as $key => $task) {
    //     // dd();
    //     $task->sub_tasks = $task->sub_tasks();
    //     dd($task);
    //     // $task->sub_tasks = $this->getSubtask($task);
    //   }
    //   // dd($tasks);
    // }
    // private function getSubtask ($task) {
    //   if (!$task) {
    //     return $task;
    //   }
    //   $task->sub_tasks = $this->getSubtask($task->getRelation('sub_tasks'));
    //   return $task;
    // }
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
