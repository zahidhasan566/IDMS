<template>
  <div id="receive-table">
    <general-datatable :options="tableOptions">
      <template slot="invoiceNo" slot-scope="row">
        <a href="javascript:" @click="viewDetails(row.item.InvoiceNo)">{{ row.item.InvoiceNo }}</a>
      </template>
      <template slot="invoiceDate" slot-scope="row">
        <span>{{ dateFormat(row.item.InvoiceDate) }}</span>
      </template>
      <template slot="deliveryDate" slot-scope="row">
        <span>{{ dateFormat(row.item.DeliveryDate) }}</span>
      </template>
      <template slot="orderDate" slot-scope="row">
        <span>{{ dateFormat(row.item.OrderDate) }}</span>
      </template>
      <template slot="discount" slot-scope="row">
        <span>{{ Number(row.item.Discount) }}</span>
      </template>
<!--      <template slot="total" slot-scope="row">-->
<!--        <span>{{ numberWithCommas(row.item.Total) }}</span>-->
<!--      </template>-->
      <template slot="action" slot-scope="row">
          <a href="javascript:" @click="doReceive(row.item.InvoiceNo)"><i class="ti-check"></i></a>
      </template>
<!--      <template slot="survey" slot-scope="row">-->
<!--        <span v-if="row.item.IsReceiveSurvey === 'N' && row.item.InvoiceNo.includes('HC')">-->
<!--            <a href="javascript:" @click="doSurvey(row.item.InvoiceNo)"><i class="ti-support"></i></a>-->
<!--        </span>-->
<!--      </template>-->
    </general-datatable>
    <add-survey-modal @changeStatus="changeStatus" v-if="loading"/>
    <receive-details-modal @changeStatus="changeStatus" v-if="loading"/>
    <damage-receive-modal @changeStatus="changeStatus" v-if="loading"/>
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
        source: 'dashboard/receivables',
        search: true,
        slots: [0,1, 2, 3, 4, 5, 6, 7],
        hideColumn: ['IsReceiveSurvey','CountData'],
        slotsName: ['invoiceNo','invoiceDate', 'deliveryDate', 'orderDate', 'discount', 'total', 'action', 'survey'],
        sortable: [],
        pages: [20, 50, 100],
        addHeader: ['Action']
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
    doReceive(invoiceNo) {
      // this.approveAlert(() => {
      //   this.axiosPost('dashboard/receivables/store',{
      //     invoiceNo: invoiceNo
      //   },(response) => {
      //     this.infoSuccess('Success',response.message)
      //     bus.$emit('refresh-datatable');
      //   },(error) => {
      //     console.log(error)
      //     this.infoFailed('Failed!',error.data.response.message)
      //   })
      // })
      this.loading = true;
      setTimeout(() => {
        bus.$emit('damage-event', invoiceNo);
      })
    },
    doSurvey(invoiceNo) {
      this.loading = true;
      setTimeout(() => {
        bus.$emit('survey-event', invoiceNo);
      })
    },
    viewDetails(invoiceNo) {
      this.loading = true;
      setTimeout(() => {
        bus.$emit('details-event', invoiceNo);
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