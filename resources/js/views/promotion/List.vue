<template>
  <div class="container-fluid" id="lists">
    <breadcrumb :options="['Promotion List']">
      <button class="btn btn-primary" @click="addDeptModal()">Add Promotion</button>
    </breadcrumb>
    <div class="row" style="padding:8px 0;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <advanced-datatable :options="tableOptions">
      <template slot="status" slot-scope="row">
        <span v-if="row.item.Status === '1'">Active</span>
        <span v-else>Inactive</span>
      </template>
      <template slot="action" slot-scope="row">
        <a href="javascript:" @click="addDeptModal(row.item)"> <i class="ti-pencil-alt"></i></a>
      </template>
    </advanced-datatable>
    <add-edit-promotion @changeStatus="changeStatus" v-if="loading"/>
  </div>
</template>
<script>

import {bus} from "../../app";
import {Common} from "../../mixins/common";
import moment from "moment";

export default {
  mixins: [Common],
  data() {
    return {
      roles: [],
      tableOptions: {
        source: 'product-promotion/list',
        search: true,
        slots: [5,7],
        hideColumn: ['PromoId'],
        slotsName: ['status','action'],
        filterOption: false,
        showFilter: ['roles'],
        colSize: ['col-lg-2'],
        sortable: [],
        pages: [20, 50, 100],
        addHeader: ['Action']
      },
      loading: false,
      cpLoading: false
    }
  },
  mounted() {
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  methods: {
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
      bus.$emit('export-data','role-list-'+moment().format('YYYY-MM-DD'))
    }
  }
}
</script>

<style>
#users table tr td:nth-child(1),table tr td:nth-child(2),table tr td:nth-child(3),table tr td:nth-child(4),table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7) {
  text-align: left;
}
</style>
