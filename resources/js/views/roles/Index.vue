<template>
  <div class="container-fluid" id="users">
    <breadcrumb :options="['Roles']">
      <button class="btn btn-primary" @click="addDeptModal()">Add Role</button>
    </breadcrumb>
    <div class="row" style="padding:8px 0px;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <advanced-datatable :options="tableOptions">
      <template slot="action" slot-scope="row">
        <a href="javascript:" @click="addDeptModal(row.item)"> <i class="ti-pencil-alt"></i></a>
      </template>
    </advanced-datatable>
    <add-edit-role @changeStatus="changeStatus" v-if="loading"/>
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
        source: 'roles/list',
        search: true,
        slots: [5],
        hideColumn: [],
        slotsName: ['action'],
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
