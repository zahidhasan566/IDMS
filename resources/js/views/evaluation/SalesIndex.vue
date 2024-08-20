<template>
  <div class="content" id="jobCard">
    <div class="container-fluid">
      <breadcrumb :options="['Evaluation Service sales']">
        <router-link class="btn btn-primary" :to="{path: `${baseUrl}`+'evaluation/service-sales-details'}">Add Service Sales</router-link>
        <!--                <button class="btn btn-primary" @click="addModal()"></button>-->
      </breadcrumb>
      <div class="row" style="padding:8px 0px;">
        <div class="col-md-4">
          <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
        </div>
      </div>
      <advanced-datatable :options="tableOptions">
<!--        <template slot="action" slot-scope="row">-->
<!--          <router-link class="btn btn-primary" :to="{path: `${baseUrl}`+'evaluation/service-sales-details?evaluationId='+row.item.EvalutionId}"><i class="ti-pencil-alt">Edit</i></router-link>-->
<!--        </template>-->
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
        source: 'evaluation/service-sales',
        search: true,
        slots: [6],
        hideColumn: ['Id'],
        slotsName: [],
        sortable: [4],
        pages: [20, 50, 100],
        addHeader: []
      },
      loading: false,
      cpLoading: false,
      tagLoading:false,
      // baseUrl: Object.freeze(baseurl)
      baseUrl: baseurl
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
    encodeConvert(val){
      let convertVal = btoa(val);
      return convertVal
    },
    exportData() {
      bus.$emit('export-data','service4p-list-'+moment().format('YYYY-MM-DD'))
    }
  }
}
</script>

<style scoped>

#jobCard table tr td:nth-child(12){
  width: 145px !important;
}
</style>
