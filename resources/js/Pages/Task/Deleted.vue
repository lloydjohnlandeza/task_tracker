<template>
  <app-layout v-bind="$props">
    <v-card
      elevation="2"
    >
      <v-card-title>
        <v-toolbar
          flat
        >
          <v-toolbar-title class="overflow-visible">
            <v-btn
              text
              fab
              small
              class="ml-n3"
              @click="() => $inertia.visit('/tasks')"
            >
              <v-icon>
                mdi-chevron-left
              </v-icon>
            </v-btn>
            My Deleted Tasks
          </v-toolbar-title>
        </v-toolbar>
      </v-card-title>
      <v-list>
        <v-subheader>
          <v-toolbar
            flat
            class="custom-toolbar-grid"
          >
            <span class="font-weight-bold">Task Name</span>
            <span class="text-center font-weight-bold">Status</span>
            <span class="text-center font-weight-bold">Actions</span>
          </v-toolbar>
        </v-subheader>  
        <recursive-list
          v-for="task in tasks"
          isShowingDeletedTasks
          subtask_name="deleted_deep_sub_tasks"
          :key="task.id"
          :currentTask="task"
          :tasks="task.deleted_deep_sub_tasks"
          :onRestoreTask="onRestoreTask"
        />
      </v-list>
    </v-card>
    <v-dialog
      v-model="dialog"
      persistent
      max-width="500px"
    >
    </v-dialog>
  </app-layout>
</template>

<script>
  import AppLayout from '../../components/AppLayout'
  import RecursiveList from '../../components/RecursiveList'
  import axios from 'axios'
  export default {
    components: {
      AppLayout,
      RecursiveList,
    },
    mounted () {
      this.tasks = this.initialTasks
    },
    data () {
      return {
        dialog: false,
        expanded: [],
        singleExpand: false,
        taskHeaders: [
          {
            text: 'Task',
            align: 'start',
            sortable: false,
            value: 'task',
          },
          {
            text: 'Status',
            sortable: false,
            value: 'status',
          },
          { text: '', value: 'data-table-expand' },
        ],
        isSubmitting: false,
        isDeleting: false,
        validationErrors: {},
        task: null,
        tasks: [],
        parent_id: null,
      }
    },
    methods: {
      async onRestoreTask ({ id }) {
        try {
          await axios.put(`/tasks/${id}/restore`).catch((err) => {
            throw(err)
          })
          this.deleteItem(this.tasks, id)
        } catch (err) {
          console.log(err)
        }
        const { isDeleting } = this
        if (isDeleting) {
          return
        }
      },
      /**
       * @param  (array: this.tasks reference, id: task id)
       */
      deleteItem(array, id) {
        var i = array.length
        while (i--) {
          if (array[i].id === id) {
            array.splice(i, 1)
            return
          }
          array[i].deep_sub_tasks && this.deleteItem(array[i].deep_sub_tasks, id)
        }
      },
    },
    props: {
      head: {
        type: Object,
        required: false,
      },
      appName: {
        type: String,
        default: 'App Tracker',
      },
      errors: {
        type: Object,
        required: false,
      },
      user: {
        type: Object,
        required: true,
      },
      initialTasks: {
        type: Array,
        required: true,
      },
    },
  }
</script>

<style lang="scss">
  .custom-toolbar-grid {
    .v-toolbar__content {
      display: grid;
      grid-template-columns: 1fr 6.25rem 6.25rem;
      grid-gap: 1rem;
    }
  }
</style>