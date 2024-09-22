<template>
  <div id="receive-table">
    <advanced-datatable :options="tableOptions">
      <template slot="approved" slot-scope="row">
        <span>
            <a href="javascript:" @click="doApproved(row.item.OrderNo)"><i class="ti-check"></i></a>
        </span>
      </template>
      <template slot="action" slot-scope="row">
        <span>
             <router-link class="btn btn-primary" :to="{path:'dashboard/edit-approve?orderNo='+row.item.OrderNo}">
                          <i class="ti-pencil">Edit And Approve</i>
             </router-link>
        </span>
      </template>
    </advanced-datatable>
    <add-survey-modal @changeStatus="changeStatus" v-if="loading"/>
  </div>
</template>
<script>
import {Common} from "../../mixins/common";
import {bus} from '../../app'
import {baseurl} from "../../base_url";
export default {
  mixins: [Common],
  data() {
    return {
      loading: false,
      tableOptions: {
        source: 'dashboard/pending-orders',
        search: true,
        slots: [6,7],
        hideColumn: [],
        slotsName: ['approved','action'],
        sortable: [],
        pages: [20, 50, 100],
        addHeader: ['Approved','Action']
      }
    }
  },
  mounted() {
    bus.$on('open-receivable-tab', () => {
      console.log('this is receivable component')
    })
  },
  methods: {
    changeStatus() {
      this.loading = false
    },
    doApproved(orderNo) {
      this.approveAlert(() => {
        this.axiosPost('dashboard/pending-orders/store',{
          orderNo: orderNo
        },(response) => {
          this.infoSuccess('Success',response.message)
          bus.$emit('refresh-datatable');
        },(error) => {
          console.log(error)
          this.infoFailed('Failed!',error.data.response.message)
        })
      })
    },
    doSurvey(orderNo) {
      this.loading = true;
      setTimeout(() => {
        bus.$emit('survey-event', orderNo);
      })
    },
    viewDetails(orderNo) {
      this.loading = true;
      setTimeout(() => {
        bus.$emit('details-event', orderNo);
      })
    },
  },
  destroyed() {
    bus.$off('open-receivable-tab')
  }
}
</script>
<style>
#receive-table table tr td:nth-child(5), #receive-table table tr td:nth-child(6) {
  text-align: right !important;
}

#receive-table table tr td:nth-child(7),#receive-table table tr td:nth-child(8) {
  text-align: center !important;
}
</style>