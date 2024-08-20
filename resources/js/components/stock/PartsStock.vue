<template>
  <div>
    <div class="row" style="padding:8px 0px;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <advanced-datatable :options="tableOptions" :customers="customers">
    </advanced-datatable>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import {bus} from "../../app";
import moment from "moment";

export default {
  mixins: [Common],
  data() {
    return {
      tableOptions: {
        source: 'stock/spare-parts-stock',
        search: true,
        slots: [],
        hideColumn: [],
        slotsName: [],
        sortable: [],
        pages: [20, 50, 100],
        addHeader: [],
        filterOption: true,
        showFilter: ['customers'],
        colSize: ['col-md-12'],
        textRight: [0,5,6,7,8]
      },
      loading: false,
      cpLoading: false,
      tagLoading:false,
      customers:[]
    }
  },
  mounted() {
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
    this.getData()
  },
  destroyed() {
    bus.$off('export-data')
  },
  methods: {
    changeStatus() {
      this.loading = false
    },
    getData() {
      this.axiosGet('product-promotion/load-customer', (response) => {
        this.customers = response.data
      })
    },
    exportData() {
      bus.$emit('export-data','stock-spare-parts-list-'+moment().format('YYYY-MM-DD'))
    }
  }
}
</script>

<style scoped>

</style>