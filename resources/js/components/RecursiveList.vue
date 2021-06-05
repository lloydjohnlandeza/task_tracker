<template>
  <div>
    <v-subheader>
      <v-toolbar
        flat
        class="custom-toolbar-grid"
      >
        <span>{{currentTask.task}}</span>
        <span class="text-center text-capitalize">{{currentTask.status}}</span>
        <div class="d-flex">
          <v-speed-dial
            direction="left"
          >
            <template v-slot:activator>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    v-bind="attrs"
                    v-on="on"
                    x-small
                    text
                    fab
                    class="elevation-0"
                  >
                    <v-icon>
                      mdi-pencil
                    </v-icon>
                  </v-btn>
                </template>
                <span>Click to see actions</span>
              </v-tooltip> -->
            </template>
            <!-- add subtask fab -->
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  fab
                  x-small
                  v-bind="attrs"
                  v-on="on"
                  color="primary"
                  @click="onAddSubtask(currentTask)"
                >
                  <v-icon>
                    mdi-file-tree
                  </v-icon>
                </v-btn>
              </template>
              <span>Add Sub-task</span>
            </v-tooltip>
            <!-- edit task fab -->
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  fab
                  x-small
                  color="primary"
                  v-bind="attrs"
                  v-on="on"
                  @click="onEdit(currentTask)"
                >
                  <v-icon>
                    mdi-pencil
                  </v-icon>
                </v-btn>
              </template>
              <span>Edit Task</span>
            </v-tooltip>
            <!-- delete task fab -->
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  fab
                  x-small
                  color="primary"
                  v-bind="attrs"
                  v-on="on"
                  @click="deleteTask(currentTask)"
                >
                  <v-icon>
                    mdi-delete
                  </v-icon>
                </v-btn>
              </template>
              <span>Delete Task</span>
            </v-tooltip>
          </v-speed-dial>
          <v-btn
            text
            fab
            x-small
          >
            <v-icon>
              mdi-reorder-horizontal
            </v-icon>
          </v-btn>
          <v-btn
            text
            fab
            x-small
          >
            <v-icon>
              mdi-chevron-down
            </v-icon>
          </v-btn>
        </div>
      </v-toolbar>
    </v-subheader>
    <ul>
      <recursive-list
        v-for="(task) in tasks"
        :key="task.id"
        :currentTask="task"
        :tasks="task.deep_sub_tasks"
        :deleteTask="deleteTask"
        :onEdit="onEdit"
        :onAddSubtask="onAddSubtask"
      />
    </ul>
  </div>
</template>


<script>
export default {
  name: 'RecursiveList',
  props: {
    currentTask: Object,
    tasks: Array,
    deleteTask: {
      type: Function,
      default: () => {},
    },
    onEdit: {
      type: Function,
      default: () => {},
    },
    onAddSubtask: {
      type: Function,
      default: () => {},
    },
  },
}
</script>
