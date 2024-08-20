<template>
  <div id="orders-table">
      <advanced-datatable :options="tableOptions">

        <template slot="action" slot-scope="row">
          <a href="javascript:" @click="viewDetails(row.item)"> <i class="ti-eye"> Details</i></a>

        </template>
      </advanced-datatable>
      <order-details-modal @changeStatus="changeStatus" v-if="loading"/>
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
        source: 'dashboard/orders',
        search: true,
        slots: [4,5],
        hideColumn: [],
        slotsName: ['action'],
        sortable: [5],
        pages: [20, 50, 100],
        addHeader: ['Action']
      },
      loading: false,
      cpLoading: false,
      tagLoading:false,
    }
  },
  mounted() {
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  destroyed() {
    bus.$off('export-data')
  },
  methods: {
    changeStatus() {
      this.loading = false
    },
    viewDetails(row = '') {
      console.log(row)
      this.loading = true;
      setTimeout(() => {
        bus.$emit('order-details-event', row.OrderNo);
      })
    },
  }
}
</script>

<style scoped>

</style>