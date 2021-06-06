<template>
  <v-app>
    <inertia-head v-bind="headPrefix" />
    <template v-if="user === null">
      <slot />
    </template>
    <template v-else>
      <v-app-bar
        app
        color="white"
        flat
      >
        <v-container class="py-0 fill-height">
          <v-btn
            v-for="(link, key) in links"
            text
            :key="key"
            @click="() => $inertia.visit(link.url)"
          >
            {{ link.name }}
          </v-btn>
          <v-spacer></v-spacer>
          <v-responsive max-width="260">
            <v-btn
              class="d-block ml-auto"
              text
              @click="() => $inertia.post('/logout')"
            >
              Logout
            </v-btn>
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
    </template>
    <confirm ref="confirm" />
    <snackbar ref="snackbar" />
  </v-app>
</template>

<script>
import Confirm from './Confirm'
import Snackbar from './Snackbar.vue'
export default {
  mounted () {
    this.$root.$confirm = this.$refs.confirm.open
    this.$root.$snackbar = this.$refs.snackbar.open

  },
  components: {
    Confirm,
    Snackbar,
  },
  data: () => ({
    links: [
      {
        name: 'Dashboard',
        url: '/',
      },
      {
        name: 'Tasks',
        url: '/tasks',
      },
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
      default: 'App Tracker',
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