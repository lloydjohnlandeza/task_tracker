<template>
  <div>
    <v-subheader>
      <v-toolbar
        flat
      >
        <span>{{currentTask.task}}</span>
        <v-spacer />
        <v-btn
          text
          fab
          x-small
          @click="deleteTask(currentTask)"
        >
          <v-icon dark>
            mdi-delete
          </v-icon>
        </v-btn>
        <v-btn
          text
          fab
          x-small
          @click="onEdit(currentTask)"
        >
          <v-icon dark>
            mdi-pencil
          </v-icon>
        </v-btn>
        <v-btn
          text
          fab
          x-small
          @click="onAddSubtask(currentTask)"
        >
          <v-icon dark>
            mdi-file-tree
          </v-icon>
        </v-btn>
        <v-btn
          text
          fab
          x-small
        >
          <v-icon dark>
            mdi-reorder-horizontal
          </v-icon>
        </v-btn>
        <v-btn
          text
          fab
          x-small
        >
          <v-icon dark>
            mdi-chevron-down
          </v-icon>
        </v-btn>
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
