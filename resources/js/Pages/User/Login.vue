<template>
  <app-layout v-bind="$props">
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
                type="email"
                :error="errors.password && errors.email"
                :error-messages="errors.password && errors.email ? errors.email : ''"
              />
              <v-text-field
                v-model="form.password"
                outlined
                label="Password"
                type="password"
                :hide-details="!errors.password"
                :error="errors.password && errors.email"
                :error-messages="errors.password"
                @:keyup.enter="submit"
              />
            </v-card-text>
            <v-divider />
            <v-card-actions :class="{ 'pa-3': $vuetify.breakpoint.smAndUp }">
              <v-flex justify-center>
                <v-btn
                  color="primary"
                  :loading="loading"
                  :large="$vuetify.breakpoint.smAndUp"
                  block
                  @click="submit"
                >
                  Login
                </v-btn>
                <p class="mb-0 mt-2 body-2 text-center">
                  <span class="font-italic">Don't have an account yet?</span>
                  <v-btn
                    class="pa-0 text-capitalize"
                    color="green"
                    text
                    @click="$inertia.get('/register')"
                  >Create one</v-btn>
                </p> 
              </v-flex>
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