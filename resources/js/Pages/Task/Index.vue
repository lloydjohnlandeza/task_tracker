<template>
  <app-layout
    :head="head"
    :appName="appName"
    :user="user"
  >
    <v-card
      elevation="2"
    >
      <v-card-title>
        <v-toolbar
          flat
        >
          <v-toolbar-title>My tasks</v-toolbar-title>
          <v-spacer />
          <v-btn
            color="primary"
            class="mb-2 mr-2"
            @click="download"
          >
            Download Data
          </v-btn>
          <v-btn
            color="primary"
            class="mb-2"
            @click="dialog = true"
          >
            New Task
          </v-btn>
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
          :key="task.id"
          :currentTask="task"
          :tasks="task.deep_sub_tasks"
          :deleteTask="deleteTask"
          :onEdit="onEdit"
          :onAddSubtask="onAddSubtask"
        />
      </v-list>
    </v-card>
    <v-dialog
      v-model="dialog"
      persistent
      max-width="500px"
    >
      <task-form
        ref="taskForm"
        :statuses="statuses"
        :loading="isSubmitting"
        :errors="validationErrors"
        :task="task"
        :parent_id="parent_id"
        @cancel="onCancel"
        @save="onSave"
      />
    </v-dialog>
  </app-layout>
</template>

<script>
  import AppLayout from '../../components/AppLayout'
  import TaskForm from '../../components/TaskForm'
  import RecursiveList from '../../components/RecursiveList'
  import axios from 'axios'
  export default {
    components: {
      AppLayout,
      TaskForm,
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
      async createTask (params) {
        this.isSubmitting = true
        const { taskForm } = this.$refs
        try {
          const { data } = await axios.post('/tasks', params).catch((err) => {
            throw(err)
          })
          this.tasks.push(data)
          this.dialog = false
          taskForm && taskForm.clearForm()
        } catch (err) {
          const { response } = err
          if (response && response.data && response.data.errors) {
            this.validationErrors = response.data.errors
          } else {
            console.log(err)
          }
        }
        this.isSubmitting = false
      },
      async createSubTask (params, parent_id) {
        this.isSubmitting = true
        const { taskForm } = this.$refs
        params = {
          ...params,
          parent_id,
        }
        try {
          const { data } = await axios.post('/tasks', params).catch((err) => {
            throw(err)
          })
          let outdatedParentTask = this.parent(this.tasks, 'id', parent_id)
          outdatedParentTask.deep_sub_tasks = [ ...outdatedParentTask.deep_sub_tasks, data]
          this.dialog = false
          taskForm && taskForm.clearForm()
        } catch (err) {
          const { response } = err
          if (response && response.data && response.data.errors) {
            this.validationErrors = response.data.errors
          } else {
            console.log(err)
          }
        }
        this.isSubmitting = false
      },
      async updateTask (params) {
        this.isSubmitting = true
        const { task, $refs } = this
        const { taskForm } = $refs
        try {
          const { data } = await axios.put(`/tasks/${task.id}`, { ...params, parent_id: task.parent_id }).catch((err) => {
            throw(err)
          })
          let outdatedTask = this.parent(this.tasks, 'id', task.id)
          outdatedTask.task = data.task
          outdatedTask.status = data.status
          this.dialog = false
          this.task = null
          taskForm && taskForm.clearForm()
        } catch (err) {
          const { response } = err
          if (response && response.data && response.data.errors) {
            this.validationErrors = response.data.errors
          } else {
            console.log(err)
          }
        }
        this.isSubmitting = false
      },
      async deleteTask (selectedTask) {
        const { isDeleting } = this
        if (isDeleting) {
          return
        }
        this.isDeleting = true
        this.task = selectedTask
        try {
          await axios.delete(`/tasks/${selectedTask.id}`).catch((err) => {
            throw(err)
          })
          this.deleteItems(this.tasks, selectedTask.id)
        } catch (err) {
          console.log(err)
        }
        this.isDeleting = false
        this.task = null
      },
      onSave (params) {
        const { task, parent_id } = this
        // if parent id is not null call create subtask
        if (parent_id) { return this.createSubTask(params, parent_id) }
        // if task is null  call create endpoint
        else if (task === null) { return this.createTask(params) }
        // else call update endpoint
        else { return this.updateTask(params)}
      },
      onEdit (task) {
        this.task = task
        this.dialog = true
      },
      onCancel () {
        this.dialog = false
        this.task = null
        this.validationErrors = {}
      },
      async onAddSubtask (selectedTask) {
        this.parent_id = selectedTask.id
        this.dialog = true
      },
      parent (array, key, id) {
        let o
        array.some(function iter(a) {
          if (a[key] === id) {
            o = a
            return true
          }
          return Array.isArray(a.deep_sub_tasks) && iter && a.deep_sub_tasks.some(iter)
        })
        return o
      },
      deleteItems(array, id) {
        var i = array.length
        while (i--) {
          if (array[i].id === id) {
            array.splice(i, 1)
            return
          }
          array[i].deep_sub_tasks && this.deleteItems(array[i].deep_sub_tasks, id)
        }
      },
      download () {
        window.open('/tasks/download', '_blank')
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
      statuses: {
        type: Array,
        required: true,
      },
      order: Array,
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