<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Must Stock List']">
      </breadcrumb>
      <div class="row" style="padding:8px 0px;">
        <div class="col-md-4">
          <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
        </div>
      </div>
      <advanced-datatable :options="tableOptions" :customers="customers">
      </advanced-datatable>
    </div>
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
        source: 'stock/msl-list',
        search: true,
        slots: [],
        hideColumn: [],
        slotsName: [],
        sortable: [4],
        pages: [20, 50, 100],
        addHeader: [],
        filterOption: true,
        showFilter: ['customers'],
        colSize: ['col-md-12']
      },
      loading: false,
      cpLoading: false,
      tagLoading: false,
      customers: []
    }
  },
  mounted() {
    bus.$off('changeStatus', function () {
      this.changeStatus()
    })
    this.getData()
  },
  destroyed() {
    bus.$off('export-data')
  },
  methods: {
    getData() {
      this.axiosGet('product-promotion/load-customer', (response) => {
        this.customers = response.data
      })
    },
    changeStatus() {
      this.loading = false
    },
    exportData() {
      bus.$emit('export-data', 'msl-stock-list-' + moment().format('YYYY-MM-DD'))
    },
  }
}
</script>

<style scoped>

</style>