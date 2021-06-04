<template>
  <v-app v-if="user === null">
    <inertia-head v-bind="headPrefix" />
    <slot />
  </v-app>
  <v-app v-else>
    <inertia-head v-bind="headPrefix" />
    <v-app-bar
      app
      color="white"
      flat
    >
      <v-container class="py-0 fill-height">
        <v-avatar
          class="mr-10"
          color="grey darken-1"
          size="32"
        ></v-avatar>

        <v-btn
          v-for="link in links"
          :key="link"
          text
        >
          {{ link }}
        </v-btn>
        <v-spacer></v-spacer>
        <v-responsive max-width="260">
          <v-text-field
            dense
            flat
            hide-details
            rounded
            solo-inverted
          ></v-text-field>
        </v-responsive>
      </v-container>
    </v-app-bar>
    <v-main class="grey lighten-3">
      <v-container>
        <v-row>
          <v-col>
            <slot />
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  data: () => ({
    links: [
      'Dashboard',
      'Messages',
      'Profile',
      'Updates',
    ],
  }),
  props: {
    head: {
      type: Object,
      default: () => {
        return {
          title: '',
          description: '',
        }
      },
    },
    appName: {
      type: String,
      required: true,
    },
    user: {
      type: Object,
      default: null,
    },
  },
  computed: {
    headPrefix () {
      const { head, appName } = this
      return {
        ...head,
        title: head.title !== '' ? `${head.title} | ${appName}` : appName,
      }
    },
  },
}
</script>