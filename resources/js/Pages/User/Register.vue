<template>
  <app-layout
    :head="head"
    :appName="appName"
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
                  Register
                </h3>
              </v-layout>
            </v-card-title>
            <v-divider />
            <v-card-text>
              <v-text-field
                v-model="form.email"
                outlined
                label="Email"
                type="email"
                :error-messages="errors.email"
              />
              <v-text-field
                v-model="form.name"
                outlined
                label="Name"
                type="text"
                :error-messages="errors.name"
              />
              <v-text-field
                v-model="form.password"
                outlined
                label="Password"
                type="password"
                :error-messages="errors.password"
              />
              <v-text-field
                v-model="form.password_confirmation"
                outlined
                label="Confirm Password"
                type="password"
                :hide-details="!errors.password_confirmation"
                :error-messages="errors.password_confirmation"
              />
            </v-card-text>
            <v-divider />
            <v-card-actions :class="{ 'pa-3': $vuetify.breakpoint.smAndUp }">
              <v-spacer />
              <v-btn
                class="mr-2"
                color="info"
                text
                @click="$inertia.get('/login')"
              >
                Back
              </v-btn>
              <v-btn
                color="primary"
                :loading="loading"
                :large="$vuetify.breakpoint.smAndUp"
                @click="submit"
              >
                Register
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
          name: null,
          password: null,
          email: null,
          password_confirmation: null,
        },
        loading: false,
      }
    },
    methods: {
      async submit () {
        const { form } = this
        this.loading = true
        this.$inertia.post('register', form, {
          preserveScroll: true,
          onSuccess: () => {
            this.loading = false
          },
          onError: errors => {
            console.error(errors)
            this.loading = false
          },
        })
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
    },
  }
</script>

<style lang="scss">
</style>