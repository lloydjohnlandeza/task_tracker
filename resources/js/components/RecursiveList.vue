<template>
  <div>
    <v-subheader
      draggable
      @dragover.prevent
      @dragenter.prevent
      @drop='onDrop($event, currentTask)'
      @dragstart='startDrag($event, currentTask)'
    >
      <v-toolbar
        flat
        class="custom-toolbar-grid cursor-move"
      >
        <span>{{currentTask.task}}</span>
        <v-menu
          bottom
          left
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              x-small
              v-bind="attrs"
              v-on="on"
              text
              :color="colors[currentTask.status]"
            >
              {{currentTask.status}}
            </v-btn>
          </template>

          <v-list>
            <v-list-item
              v-for="(status, i) in statuses"
              :key="i"
              link
              :disabled="status === currentTask.status"
              @click="updateStatus(currentTask, status)"
            >
              {{status}}
            </v-list-item>
          </v-list>
        </v-menu>
        <div
          v-if="!isShowingDeletedTasks"
          class="d-flex"
        >
          <v-speed-dial
            direction="left"
            class="ml-auto"
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
                  @click="openSubtaskDialog(currentTask)"
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
                  @click="openEditDialog(currentTask)"
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
            class="mr-auto"
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
        :deleteTask="deleteTask"
        :onViewDeleted="onViewDeleted"
        :openEditDialog="openEditDialog"
        :openSubtaskDialog="openSubtaskDialog"
        :onRestoreTask="onRestoreTask"
        :swapTask="swapTask"
        :statuses="statuses"
        :updateStatus="updateStatus"
        :colors="colors"
      />
    </ul>
  </div>
</template>


<script>
export default {
  name: 'RecursiveList',
  data() {
    return {
    }
  },
  methods: {
    startDrag (e, task) {
      e.dataTransfer.setData('task', JSON.stringify({
        id: task.id,
        order_id: task.order_id,
        parent_id: task.parent_id,
      }))
    },
    onDrop (e, task) {
      try {
        this.swapTask(JSON.parse(e.dataTransfer.getData('task')), task)
      } catch (err) {
        console.error(err)
      }
    },
  },
  props: {
    currentTask: Object,
    tasks: Array,
    onViewDeleted: {
      type: Function,
      default: () => {},
    },
    deleteTask: {
      type: Function,
      default: () => {},
    },
    updateStatus: {
      type: Function,
      default: () => {},
    },
    openEditDialog: {
      type: Function,
      default: () => {},
    },
    openSubtaskDialog: {
      type: Function,
      default: () => {},
    },
    onRestoreTask: {
      type: Function,
      default: () => {},
    },
    swapTask: {
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
    statuses: {
      type: Array,
      default: () => [],
    },
    colors: {
      type: Object,
      default: () => {},
    },
  },
}
</script>
