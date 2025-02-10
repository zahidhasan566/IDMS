<template>
  <div class="container-fluid" id="users">
    <breadcrumb :options="['Spare Parts Invoices']">
      <router-link :to="{name: 'InvoiceSpareParts'}" class="btn btn-primary">Create New Invoice</router-link>
    </breadcrumb>
    <div class="row" style="padding:8px 0px;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <advanced-datatable :options="tableOptions">
      <template slot="action" slot-scope="row">
        <button type="button" @click="printInvoice(row.item.InvoiceID)" style="height: 18px;padding: 0px 3px 18px 3px;" class="btn btn-primary btn-sm"><i class="fas fa-print"></i></button>
      </template>
    </advanced-datatable>
    <add-edit-user @changeStatus="changeStatus" v-if="loading"/>
  </div>
</template>
<script >

import {bus} from "../../app";
import {Common} from "../../mixins/common";
import moment from "moment";

export default {
  mixins: [Common],
  data() {
    return {
      tableOptions: {
        source: 'invoice-spare-parts/list',
        search: true,
        slots: [9],
        hideColumn: ['InvoiceID'],
        slotsName: ['action'],
        sortable: [2],
        pages: [20, 50, 100],
        addHeader: ['Action']
      },
      loading: false,
      cpLoading: false
    }
  },
  mounted() {
    document.title = 'Spare Parts Invoice List | DMS';
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  methods: {
    printInvoice(invoiceNo) {
      this.$router.push({name: 'InvoiceSparePartsPrint',params: {invoiceId:invoiceNo}})
    },
    changeStatus() {
      this.loading = false
    },
    addDeptModal(row = '') {
      this.loading = true;
      setTimeout(() => {
        bus.$emit('add-edit-user', row);
      })
    },
    exportData() {
      bus.$emit('export-data','spare-parts-invoice-list-'+moment().format('YYYY-MM-DD'))
    }
  }
}
</script>

<style>
#users table tr td:nth-child(1),table tr td:nth-child(2),table tr td:nth-child(3),table tr td:nth-child(4),table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7) {
  text-align: left;
}
</style>
