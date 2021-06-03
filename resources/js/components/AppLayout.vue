<template>
  <v-app>
    <inertia-head v-bind="head" />
    <slot />
  </v-app>
</template>


<script>
export default {
  props: {
    head: {
      type: Object,
      default: () => {
        return {
          title: 'App Tracker',
          description: 'App Tracker'
        }
      }
    }
  }
}
</script>