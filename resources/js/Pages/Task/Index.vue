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
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            dark
            class="mb-2"
            @click="dialog = true"
          >
            New Task
          </v-btn>
        </v-toolbar>
      </v-card-title>
      <v-list>
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
    
    <!-- <v-data-table
      :headers="taskHeaders"
      :items="tasks"
      :single-expand="singleExpand"
      :expanded.sync="expanded"
      hide-default-footer
      :footer-props="{
        itemsPerPageOptions	: [-1]
      }"
      item-key="id"
      show-expand
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar
          flat
        >
          <v-toolbar-title>My tasks</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            dark
            class="mb-2"
            @click="dialog = true"
          >
            New Task
          </v-btn>
        </v-toolbar>
      </template>
      <template v-slot:body="{ items }">
        <tbody>
          <tr :key="key" v-for="(item, key) in items">
            <td :key="tdKey" v-for="(header, tdKey) in taskHeaders">
              <span v-if="header.value === 'data-table-expand'">
                <div class="d-flex">
                  <v-btn
                    text
                    fab
                    x-small
                    :loading="isDeleting && item.id === task.id"
                    @click="deleteTask(item)"
                  >
                    <v-icon dark>
                      mdi-delete
                    </v-icon>
                  </v-btn>
                  <v-btn
                    text
                    fab
                    x-small
                    @click="onEdit(item)"
                  >
                    <v-icon dark>
                      mdi-pencil
                    </v-icon>
                  </v-btn>
                  <v-btn
                    text
                    fab
                    x-small
                    @click="onAddSubtask(item)"
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
                </div>
              </span>
              <span v-else-if="header.value === 'status'">
                {{item[header.value]}}{{item.sub_tasks}}
              </span>
              <span v-else>
                {{item[header.value]}} 
              </span>
            </td>
          </tr>
        </tbody>
      </template>
    </v-data-table> -->

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
      test () {
        console.log('hi')
      },
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
        console.log(params)
        try {
          const { data } = await axios.post('/tasks', params).catch((err) => {
            throw(err)
          })
          console.log(data)
          // this.tasks.push(data)
          this.dialog = false
          taskForm && taskForm.clearForm()
        } catch (err) {
          const { response } = err
          if (response && response.data && response.data.errors) {
            this.validationErrors = response.data.errors
          }
        }
        this.isSubmitting = false
      },
      async updateTask (params) {
        this.isSubmitting = true
        const { task, $refs } = this
        const { taskForm } = $refs
        try {
          const { data } = await axios.put(`/tasks/${task.id}`, params).catch((err) => {
            throw(err)
          })
          const newTasks = [...this.tasks]
          const found = newTasks.findIndex(task => {
            return task.id === task.id
          })
          if (found !== -1) {
            newTasks[found] = data
            this.tasks = newTasks
          }
          this.dialog = false
          this.task = null
          taskForm && taskForm.clearForm()
        } catch (err) {
          const { response } = err
          if (response && response.data && response.data.errors) {
            this.validationErrors = response.data.errors
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
          const newTasks = [...this.tasks]
          const found = newTasks.findIndex(task => {
            return task.id === selectedTask.id
          })
          if (found !== -1) {
            newTasks.splice(found, 1)
            this.tasks = newTasks
          }
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
  ul {
    list-style: none;
  }
</style>