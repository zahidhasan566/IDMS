<template>
  <div class="container-fluid" id="users">
    <breadcrumb :options="['Role Permission']">
      <button class="btn btn-primary" @click="addDeptModal()">Add Permission</button>
    </breadcrumb>
    <div class="row" style="padding:8px 0;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <advanced-datatable :options="tableOptions" :roles="roles">
      <template slot="action" slot-scope="row">
        <a href="javascript:" @click="addDeptModal(row.item)"> <i class="ti-pencil-alt"></i></a>
      </template>
    </advanced-datatable>
    <add-edit-role-permission :roles="roles" @changeStatus="changeStatus" v-if="loading"/>
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
        source: 'role-permission/list',
        search: false,
        slots: [2],
        hideColumn: [],
        slotsName: ['action'],
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
    this.getRoles()
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  methods: {
    getRoles() {
      this.axiosGet('roles/all',(response) => {
        this.roles = response.data
      })
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
