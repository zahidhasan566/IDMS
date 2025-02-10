<template>
  <div class="container-fluid" id="users">
    <breadcrumb :options="['Inquiry Conversion Summary Report']">
    </breadcrumb>
    <div class="row" style="padding:8px 0;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <general-datatable :options="tableOptions" :customers="customers">
        <template slot="action" slot-scope="row">
            <router-link class="btn btn-primary" style="font-size: 12px;width:65px;padding: 2px 0px" target='_blank' :to="{path: `${baseurl}`+'inquiry-print?action_type=print&item='+encodeConvert(row.item.InquiryId)}"><i class="fa fa-print">Print</i></router-link>
        </template>
    </general-datatable>
  </div>
</template>
<script>

import {bus} from "../../app";
import {Common} from "../../mixins/common";
import moment from "moment";
import General from "../../components/datatable/General.vue";
import {baseurl} from "../../base_url";

export default {
  components: {General},
  mixins: [Common],
  data() {
    return {
      roles: [],
      tableOptions: {
        source: 'inquiry/conversion-summary-report',
        search: false,
        slots: [15],
        hideColumn: ['CountData'],
        slotsName: ['action'],
        filterOption: true,
        showFilter: ['startDate','endDate','customers','products'],
        colSize: ['col-lg-2','col-lg-2','col-lg-2'],
        sortable: [],
        pages: [20, 50, 100],
        addHeader: ['Action'],
      },
      customers: [],
      baseurl: baseurl
    }
  },
  mounted() {
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  methods: {
    getData() {
      this.axiosGet('inquiry/load-supportingData',(response) => {
        this.customers = response.customer
        this.products = response.products
      })
    },
      encodeConvert(val){
          let convertVal = btoa(val);
          return convertVal
      },
    exportData() {
      bus.$emit('export-data','inquiry-conversion-summary-report-'+moment().format('YYYY-MM-DD'))
    }
  }
}
</script>

<style>
#users table tr td:nth-child(1),table tr td:nth-child(2),table tr td:nth-child(3),table tr td:nth-child(4),table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7) {
  text-align: left;
}
</style>
