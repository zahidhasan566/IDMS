<template>
  <div id="receive-table">
    <advanced-datatable :options="tableOptions">
      <template slot="approved" slot-scope="row">
        <span>
            <a href="javascript:" @click="doApproved(row.item.OrderNo)"><i class="ti-check"></i></a>
        </span>
      </template>
      <template slot="reject" slot-scope="row">
        <span>
            <a href="javascript:" @click="doReject(row.item.OrderNo)"><i class="ti-close" style="color: red "></i></a>
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
  </div>
</template>
<script>
import {Common} from "../../mixins/common";
import {bus} from '../../app'
export default {
  mixins: [Common],
  data() {
    return {
      loading: false,
      tableOptions: {
        source: 'dashboard/pending-orders',
        search: true,
        slots: [6,7,8],
        hideColumn: [],
        slotsName: ['approved','reject','action'],
        sortable: [],
        pages: [20, 50, 100],
        addHeader: ['Approved','Reject','Action']
      },
      actionType :''
    }
  },
  mounted() {

  },
  methods: {
    changeStatus() {
      this.loading = false
    },
    doApproved(orderNo) {
      this.actionType='approved'
      this.approveAlert(() => {
        this.axiosPost('dashboard/pending-orders/store',{
          orderNo: orderNo,
          actionType:this.actionType
        },(response) => {
          this.infoSuccess('Success',response.message)
          bus.$emit('refresh-datatable');
        },(error) => {
          this.infoFailed('Failed!',error.data.response.message)
        })
      })
    },
    doReject(orderNo) {
      this.actionType='reject'
      this.deleteAlert(() => {
        this.axiosPost('dashboard/pending-orders/store',{
          orderNo: orderNo,
          actionType:this.actionType
        },(response) => {
          this.infoSuccess('Success',response.message)
          bus.$emit('refresh-datatable');
        },(error) => {
          this.infoFailed('Failed!',error.data.response.message)
        })
      })
    },

  },
  destroyed() {
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