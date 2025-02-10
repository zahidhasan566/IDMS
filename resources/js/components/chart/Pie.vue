<script>
import { Pie } from "vue-chartjs";
export default {
  props: ['labels','dataSets'],
  extends: Pie,
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [
          {
            backgroundColor: [],
            data: []
          }
        ]
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false
      }
    }
  },
  mounted() {
    console.log(this.dataSets,'child')
    this.chartData.labels = this.labels;
    this.chartData.datasets = this.dataSets;
    this.renderChart(this.chartData, this.chartOptions);
  },
};
</script>
<style lang="scss">
</style>
