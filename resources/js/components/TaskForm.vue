<template>
  <v-card>
    <v-card-title>
      <span class="text-h5">{{formTitle}}</span>
    </v-card-title>
    <v-card-text>
      <v-container>
        <v-text-field
          v-model="form.task"
          :error-messages="errors.task"
          outlined
          label="Task"
          type="text"
        />
        <v-combobox
          v-model="form.status"
          :error-messages="errors.status"
          :items="items"
          :search-input.sync="search"
          persistent-hint
          hint="Type to enter custom status"
          clearable
          outlined
          label="Status"
        >
        <template v-slot:selection="{ index, item }">
          <span class="text-capitalize" :key="index">{{ item }}</span>
        </template>
        <template v-slot:item="{ index, item }">
          <span class="text-capitalize" :key="index">{{ item }}</span>
        </template>
        </v-combobox>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn
        color="blue darken-1"
        text
        @click="cancel"
      >
        Cancel
      </v-btn>
      <v-btn
        color="blue darken-1"
        text
        :loading="loading"
        :disabled="search"
        @click="save"
      >
        Save
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
  export default {
    components: {
    },
    mounted () {
    },
    data () {
      return {
        form: {
          task: '',
          status: '',
        },
        search: null,
      }
    },
    computed: {
      formTitle () {
        const { task, parent_id } = this
        if (parent_id) {
          return 'Create Sub-task'
        }
        if (!task) {
          return 'New Task'
        }
        return 'Edit Task' 
      },
      items () {
        let newStatuses = [ ...this.statuses ]
        const search = this.search ? this.search : ''
        const isNotOnList = newStatuses.findIndex(i => i.toLowerCase() === search.toLowerCase()) === -1
        if (isNotOnList && search) {
          return [
            ...newStatuses,
            search,
          ]
        }
        return newStatuses
      },
    },
    methods: {
      cancel () {
        this.$emit('cancel')
      },
      save () {
        const { form } = this
        this.$emit('save', form)
      },
      clearForm () {
        this.form = {
          task: '',
          status: '',
        }
        this.search = null
      },
    },
    props: {
      task: {
        type: Object,
        required: false,
      },
      tasks: {
        type: Array,
        default: () => [],
      },
      parent_id: {
        type: Number,
        default: null,
      },
      statuses: {
        type: Array,
        default: () => [],
      },
      errors: {
        type: Object,
        default: () => {},
      },
      loading: {
        type: Boolean,
        required: false,
      },
    },
    watch: {
      task: {
        immediate: true,
        handler(newVal) {
          this.clearForm()
          if (!newVal) {
            return
          }
          this.form = {
            task: newVal.task,
            status: newVal.status,
          }
        },
      },
    },
  }
</script>