
<tr>
  <td style="text-align: left;">{{ $task->task }}</td>
  <td style="background-color: {{isset($status_color[$task->status]->color) ? $status_color[$task->status]->color : ''}}">{{ ucfirst($task->status) }}</td>
</tr>
@if (count($task->deep_sub_tasks) > 0)
    @foreach($task->deep_sub_tasks as $subtask)
      @include('exports.task_list', ['task' => $subtask])
    @endforeach
@endif