<table>
    <thead>
    <tr>
        <th style="width: 50px; font-weight: bold;">Task</th>
        <th style="font-weight: bold;">Status</th>
    </tr>
    </thead>
    <tbody>
      @foreach($tasks as $task)
        @include('exports.task_list', ['task' => $task])
      @endforeach
    </tbody>
</table>
