<template>
  <app-layout v-bind="$props">
    Welcome {{user}}
  </app-layout>
</template>

<script>
  import AppLayout from '../../components/AppLayout'
  export default {
    components: {
      AppLayout,
    },
    data () {
      return {
      }
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
      user: {
        type: Object,
        required: false,
      },
    },
  }
</script>

<style lang="scss">
</style>