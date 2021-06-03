<template>
  <app-layout
    :head="head"
  >
    <v-container class="ma-auto">
      <v-layout wrap>
        <v-flex
          sm12
          md6
          offset-md3
        >
          <v-card
            elevation="4"
            light
            tag="section"
            :loading="loading"
          >
            <v-card-title>
              <v-layout
                align-center
                justify-space-between
              >
                <h3 class="headline">
                  {{ appName }}
                </h3>
              </v-layout>
            </v-card-title>
            <v-divider />
            <v-card-text>
              <v-text-field
                v-model="form.email"
                outlined
                label="Email"
                type="text"
                :error-messages="errors.email"
              />
              <v-text-field
                v-model="form.password"
                outlined
                label="Password"
                type="password"
                :hide-details="!errors.password"
                :error-messages="errors.password"
              />
            </v-card-text>
            <v-divider />
            <v-card-actions :class="{ 'pa-3': $vuetify.breakpoint.smAndUp }">
              <v-btn
                color="info"
                text
              >
                Forgot password?
              </v-btn>
              <v-spacer />
              <v-btn
                color="info"
                :loading="loading"
                :large="$vuetify.breakpoint.smAndUp"
                @click="submit"
              >
                <v-icon left>
                  mdi-lock
                </v-icon>
                Login
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
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
        form: {
          password: null,
          email: null,
        },
        loading: false,
      }
    },
    methods: {
      async submit () {
        const { form } = this
        this.loading = true
        this.$inertia.post('login', form, {
          preserveScroll: true,
          onSuccess: () => {
            this.loading = false
          },
          onError: errors => {
            console.log(errors, 'hi')
            this.loading = false
          },
        })
      },
    },
    props: {
      test: String,
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
    },
  }
</script>

<style lang="scss">
</style>