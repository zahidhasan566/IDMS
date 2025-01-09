<template>
  <div class="container-fluid" id="users">
    <breadcrumb :options="['Money Receipt']">
      <button class="btn btn-primary" @click="addDeptModal()">Create New</button>
    </breadcrumb>
    <div class="row" style="padding:8px 0px;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <advanced-datatable :options="tableOptions">
      <template slot="active" slot-scope="row">
        <span v-if="row.item.InvoiceStatus === 'Y'">Not Invoiced</span>
        <span v-else>Invoiced</span>
      </template>
      <template slot="action" slot-scope="row">
        <button type="button" @click="printInvoice(row.item.MoneyRecNo)" style="height: 18px;padding: 0px 3px 18px 3px;" class="btn btn-primary btn-sm"><i class="fas fa-print"></i></button>
      </template>
    </advanced-datatable>
    <advance-money-receipt :types="types" @changeStatus="changeStatus" v-if="loading"/>
  </div>
</template>
<script>

import {bus} from "../../app";
import {Common} from "../../mixins/common";
import moment from "moment";

export default {
  mixins: [Common],
  name: 'MoneyReceiptAdvance',
  data() {
    return {
      tableOptions: {
        source: 'money-receipt/advance',
        search: true,
        slots: [5,6],
        hideColumn: [],
        slotsName: ['active', 'action'],
        sortable: [2],
        pages: [20, 50, 100],
        addHeader: ['Action']
      },
      types: [],
      loading: false,
      cpLoading: false
    }
  },
  mounted() {
    document.title = 'Advance Money Receipt | DMS';
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
    this.getData()
  },
  methods: {
    getData() {
      this.axiosGet('money-receipt/get-advance-types',(response) => {
        this.types = response.types
      });
    },
    addDeptModal() {
      this.loading = true;
      setTimeout(() => {
        bus.$emit('advance-money-receipt-event');
      })
    },
    printInvoice(moneyRecNo) {
      this.$router.push({name: 'AdvancePrint',params: {moneyRecNo:moneyRecNo}})
    },
    changeStatus() {
      this.loading = false
    },
    exportData() {
      bus.$emit('export-data','advance-money-receipt-'+moment().format('YYYY-MM-DD'))
    }
  }
}
</script>

<style>
#users table tr td:nth-child(1),table tr td:nth-child(2),table tr td:nth-child(3),table tr td:nth-child(4),table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7) {
  text-align: left;
}
</style>
