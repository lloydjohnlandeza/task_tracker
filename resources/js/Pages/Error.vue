<template>
  <app-layout :head="{ title: '404 Page' }">
    <v-container class="ma-auto">
      <h3 class="text-center headline">
        {{title}}
      </h3>
      <p class="text-center">
        {{description}}
      </p>
    </v-container>
  </app-layout>
</template>

<script>
import AppLayout from '../components/AppLayout'

export default {
  components: {
    AppLayout,
  },
  props:{
    status: Number,
  },
  computed: {
    title() {
      return {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden',
      }[this.status]
    },
    description() {
      return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
      }[this.status]
    },
  },
}
</script>