<template>
  <div class="container-fluid" id="users">
    <breadcrumb :options="['SDMS Invoice List']">
    </breadcrumb>
    <div class="row" style="padding:8px 0;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <advanced-datatable :options="tableOptions" :customers="customers">
      <template slot="invoiceNo" slot-scope="row">
        <a href="javascript:" @click="printNow(row.item.InvoiceNo)"><i class="fa fa-print"></i> {{ row.item.InvoiceNo }}</a>
      </template>
    </advanced-datatable>
    <SdmsInvoicePrint v-show="false"/>
  </div>
</template>
<script>

import {bus} from "../../app";
import {Common} from "../../mixins/common";
import moment from "moment";
import General from "../../components/datatable/General.vue";
import SdmsInvoicePrint from "./SdmsInvoicePrint.vue";

export default {
  components: {General,SdmsInvoicePrint},
  mixins: [Common],
  data() {
    return {
      roles: [],
      tableOptions: {
        source: 'sdms-report/invoice-list',
        search: false,
        slots: [1],
        hideColumn: [],
        slotsName: ['invoiceNo'],
        filterOption: true,
        showFilter: ['startDate','endDate','customers'],
        colSize: ['col-lg-2','col-lg-2','col-lg-2'],
        sortable: [],
        pages: [1000, 2000, 3000],
        addHeader: []
      },
      customers: [],
      isPrint: false,
      invoiceNo: ''
    }
  },
  mounted() {
    document.title = 'SDMS Invoice Report | DMS';
    this.getData()
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  methods: {
    getData() {
      this.axiosGet('product-promotion/load-customer',(response) => {
        this.customers = response.data
      })
    },
    exportData() {
      bus.$emit('export-data','sdms-invoice-report-'+moment().format('YYYY-MM-DD'))
    },
    printNow(invoiceNo) {
      bus.$emit('print-invoice',invoiceNo)
    }
  }
}
</script>

<style>
#users table tr td:nth-child(1),table tr td:nth-child(2),table tr td:nth-child(3),table tr td:nth-child(4),table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7) {
  text-align: left;
}
</style>
