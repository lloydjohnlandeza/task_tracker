<template>
  <div>
    <v-subheader>
      <v-toolbar
        flat
        class="custom-toolbar-grid"
      >
        <span>{{currentTask.task}}</span>
        <span class="text-center text-capitalize">{{currentTask.status}}</span>
        <div
          v-if="!isShowingDeletedTasks"
          class="d-flex"
        >
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
              </v-tooltip>
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
                  @click="onDeleteTask(currentTask)"
                >
                  <v-icon>
                    mdi-delete
                  </v-icon>
                </v-btn>
              </template>
              <span>Delete Task</span>
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
                  v-if="currentTask.parent_id"
                  @click="onViewDeleted(currentTask.id)"
                >
                  <v-icon>
                    mdi-eye
                  </v-icon>
                </v-btn>
              </template>
              <span>View Deleted Subtask</span>
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
        <div class="d-flex" v-else>
          <v-tooltip v-if="isShowingDeletedTasks" bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                v-bind="attrs"
                v-on="on"
                x-small
                text
                fab
                class="elevation-0 mx-auto"
                @click="onRestoreTask(currentTask)"
              >
                <v-icon>
                  mdi-delete-restore
                </v-icon>
              </v-btn>
            </template>
            <span>Restore Deleted</span>
          </v-tooltip>
        </div>
      </v-toolbar>
    </v-subheader>
    <ul>
      <recursive-list
        v-for="(task) in tasks"
        :isShowingDeletedTasks="isShowingDeletedTasks"
        :subtask_name="subtask_name"
        :key="task.id"
        :currentTask="task"
        :tasks="task[subtask_name]"
        :onDeleteTask="onDeleteTask"
        :onViewDeleted="onViewDeleted"
        :onEdit="onEdit"
        :onAddSubtask="onAddSubtask"
        :onRestoreTask="onRestoreTask"
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
    onViewDeleted: {
      type: Function,
      default: () => {},
    },
    onDeleteTask: {
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
    onRestoreTask: {
      type: Function,
      default: () => {},
    },
    subtask_name: {
      type: String,
      default: 'deep_sub_tasks',
    },
    isShowingDeletedTasks: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
