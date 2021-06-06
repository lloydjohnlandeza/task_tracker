<template>
  <app-layout v-bind="$props">
    <v-card
      elevation="2"
      :loading="isLoading"
    >
      <v-card-title>
        <v-toolbar
          flat
        >
          <v-toolbar-title>My tasks</v-toolbar-title>
        </v-toolbar>
        <v-btn
          color="primary"
          class="mb-2 mr-2"
          @click="download"
        >
          Download
        </v-btn>

        <v-btn
          color="primary"
          class="mb-2 mr-2"
          @click="() => $inertia.visit('tasks/main/deleted')"
        >
          View Deleted
        </v-btn>
        <v-btn
          color="primary"
          class="mb-2"
          @click="dialog = true"
        >
          New Task
        </v-btn>
      </v-card-title>
      <v-list class="">
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
        <template v-if="tasks.length > 0">
          <recursive-list
            v-for="task in tasks" 
            :key="task.id"
            :currentTask="task"
            :tasks="task.deep_sub_tasks"
            :onViewDeleted="(id) => $inertia.visit(`tasks/${id}/deleted`)"
            :deleteTask="deleteTask"
            :openEditDialog="openEditDialog"
            :openSubtaskDialog="openSubtaskDialog"
            :swapTask="swapTask"
            :statuses="statuses"
            :updateStatus="updateStatus"
            :colors="colors"
          />
        </template>
        <v-card-title class="justify-center grey--text" v-else>
          <div class="text-center font-italic font-weight-light">No Task Yet</div>
        </v-card-title>
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
        :loading="isLoading"
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
        isLoading: false,
        validationErrors: {},
        task: null,
        tasks: [],
        parent_id: null,
      }
    },
    methods: {  
      openEditDialog (task) {
        this.task = task
        this.dialog = true
      },
      openSubtaskDialog (selectedTask) {
        this.parent_id = selectedTask.id
        this.dialog = true
      },
      // on dialog save
      onSave (params) {
        const { task, parent_id } = this
        // if parent id is not null call create subtask
        if (parent_id) { return this.createSubTask(params, parent_id) }
        // if task is null  call create endpoint
        else if (task === null) { return this.createTask(params) }
        // else call update endpoint
        else { return this.updateTask(params)}
      },
      // on dialog cancel
      onCancel () {
        this.dialog = false
        this.task = null
        this.parent_id = null
        this.clearForm()
      },
      async createTask (params) {
        this.isLoading = true
        try {
          const { data } = await axios.post('/tasks', params).catch((err) => {
            throw(err)
          })
          this.tasks.push(data)
          this.dialog = false
        } catch (err) {
          const { response } = err
          if (response && response.data && response.data.errors) {
            this.validationErrors = response.data.errors
          } else {
            console.log(err)
          }
        }
        this.isLoading = false
      },
      async createSubTask (params, parent_id) {
        this.isLoading = true
        params = {
          ...params,
          parent_id,
        }
        try {
          const { data } = await axios.post('/tasks', params).catch((err) => {
            throw(err)
          })
          let outdatedParentTask = this.getParent(this.tasks, 'id', parent_id)
          outdatedParentTask.deep_sub_tasks = [ ...outdatedParentTask.deep_sub_tasks, data]
          this.dialog = false
          this.resetSelectedTask()
        } catch (err) {
          const { response } = err
          if (response && response.data && response.data.errors) {
            this.validationErrors = response.data.errors
          } else {
            console.log(err)
          }
          this.resetSelectedTask()
        }
        this.isLoading = false
      },
      async updateTask (params) {
        this.isLoading = true
        const { task } = this
        try {
          const { data } = await axios.put(`/tasks/${task.id}`, { ...params, parent_id: task.parent_id }).catch((err) => {
            throw(err)
          })
          let outdatedTask = this.getParent(this.tasks, 'id', task.id)
          outdatedTask.task = data.task
          outdatedTask.status = data.status
          this.resetSelectedTask()
          this.dialog = false
        } catch (err) {
          const { response } = err
          if (response && response.data && response.data.errors) {
            this.validationErrors = response.data.errors
          } else {
            console.log(err)
            this.resetSelectedTask()
          }
        }
        this.isLoading = false
      },
      async updateStatus (task, status) {
        this.task = task
        await this.$nextTick()
        this.updateTask({
          task: task.task,
          status: status,
        })
      },
      async swapTask (firstTask, secondTask) {
        if (firstTask.parent_id !== secondTask.parent_id) {
          this.$root.$snackbar('Task should have the same parent to be re-order')
          return
        }
        const params = {
          firstTask: {
            id: firstTask.id,
            order_id: firstTask.order_id,
          },
          secondTask: {
            id: secondTask.id,
            order_id: secondTask.order_id,
          },
        }
        try {
          this.loading = true
          this.$inertia.put('/tasks/reorder', params, {
            preserveScroll: true,
            onSuccess: () => {
              this.loading = false
            },
            onError: errors => {
              console.error(errors)
              this.loading = false
            },
          })
        } catch (err) {
          console.log(err)
          this.loading = true
        }
      },
      async deleteTask (selectedTask) {
        const title = 'Delete Task'
        let description = 'Are you sure you want to delete this task?'
        if (selectedTask.parent_id !== 0) {
          description = `${description} Parent task will be deleted as well`
        }
        if (!await this.confirmAction(title, description)) {
          return
        }
        this.task = selectedTask
        this.isLoading = true
        try {
          await axios.delete(`/tasks/${selectedTask.id}`).catch((err) => {
            throw(err)
          })
          this.deleteItem(this.tasks, selectedTask.id)
          this.resetSelectedTask()
        } catch (err) {
          console.log(err)
          this.resetSelectedTask()
        }
        this.isLoading = false
      },
      clearForm () {
        const { $refs } = this
        const { taskForm } = $refs
        this.validationErrors = {}
        taskForm && taskForm.clearForm()
      },
      resetSelectedTask () {
        this.task = null
        this.parent_id = null
      },
      // get parent base on task id
      getParent (array, key, id) {
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
      download () {
        window.open('/tasks/download', '_blank')
      },
      async confirmAction (title, description) {
        return await this.$root.$confirm(title, description)
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
      colors: {
        type: Object,
        required: true,
      },
    },
    watch: {
      initialTasks: {
        deep: true,
        handler(newVal) {
          this.tasks = newVal
        },
      },
      dialog: {
        handler (newVal) {
          if (!newVal) {
            this.clearForm()
          }
        },
      },
    },
  }
</script>

<style scoped lang="scss">
  .table-header {
    display: grid;
    grid-template-columns: 1fr auto;
  }
</style>