<template>
  <app-layout v-bind="$props">
    <v-card
      class="mt-1 mx-auto my-12 grey lighten-3"
      elevation="0"
    >
      <v-card-title>
        <span>Tasks Chart</span>
      </v-card-title>
      <task-chart :styles="myStyles" :options="{ responsive: true, maintainAspectRatio: false }" :chart-data="datacollection"></task-chart>
    </v-card>
  </app-layout>
</template>

<script>
  import AppLayout from '../../components/AppLayout'
  import TaskChart from '../../components/TaskChart'
  export default {
    components: {
      AppLayout,
      TaskChart,
    },
    data () {
      return {
        datacollection: {},
        height: 500,
      }
    },
    methods: {
      fillData () {
        const { data, labels } = this.chart_data
        this.datacollection = {
          labels: labels,
          datasets: [{
            data: data,
            fill: false,
            backgroundColor: [
              '#B9F6CA',
              '#FFFF8D',
              '#FF9E80',
            ],
            borderColor: '#eee',
            tension: 0.1,
          }],
        }
      },
      getRandomInt () {
        return Math.floor(Math.random() * (50 - 5 + 1)) + 5
      },
    },
    mounted () {
      this.fillData()
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
      chart_data: {
        type: Object,
        required: true,
      },
    },
    computed: {
      myStyles () {
        return {
          height: `${this.height}px`,
          position: 'relative',
        }
      },
    },
  }
</script>

<style lang="scss">
</style>