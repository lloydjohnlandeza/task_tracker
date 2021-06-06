<template>
  <app-layout v-bind="$props">
    <v-card
      class="mt-1 mx-auto my-12 grey lighten-3"
      elevation="0"
    >
      <v-card-title>
        <div class="ma-auto text-center" v-if="isEmpty">
          <div class="ma-auto">No data available.</div>
          <div class="ma-auto text-body-1">Create a data first under Tasks tab for the graph to display</div>
        </div>
        <span v-else>Tasks Chart</span>
      </v-card-title>
      <task-chart
        v-if="!isEmpty"
        :styles="myStyles"
        :options="{ responsive: true, maintainAspectRatio: false }"
        :chart-data="datacollection" 
      />
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
        data: [],
        height: 70,
      }
    },
    methods: {
      fillData () {
        const { data, labels, backgroundColor } = this.chart_data
        this.data = data
        this.datacollection = {
          labels: labels,
          datasets: [{
            data: data,
            fill: false,
            backgroundColor: backgroundColor,
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
          height: `${this.height}vh`,
          position: 'relative',
        }
      },
      isEmpty () {
        const { data } = this
        for (let index = 0; index < data.length; index++) {
          const element = data[index];
          if (element != 0) {
            return false 
          }
        }
        return true
      },
    },
  }
</script>

<style lang="scss">
</style>