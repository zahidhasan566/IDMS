<template>
  <div class="container-fluid">
    <breadcrumb :options="['Dashboard']"></breadcrumb>
    <div class="card">
      <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#dashboard" role="tab">
              <span> <i class="ti-home"></i> Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#orders" role="tab" @click="openTab('orders')">
              <span> <i class="ti-hand-drag"></i> Orders</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#receivable" role="tab" @click="openTab('receivable')">
              <span> <i class="ti-layout-list-thumb"></i> Receivable</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#logistics" role="tab" @click="openTab('logistics')">
              <span> <i class="ti-bar-chart"></i> Logistics</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#currentBalance" role="tab" @click="openTab('currentBalance')">
              <span> <i class="ti-money"></i> Current Balance</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#pendingOrders" role="tab" @click="openTab('pendingOrders')">
              <span> <i class="ti-more-alt"></i> Pending Orders</span>
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active p-3" id="dashboard" role="tabpanel">
            <p class="mb-0">
              Welcome to the DMS Panel.
            </p>
          </div>
          <div class="tab-pane p-3" id="orders" role="tabpanel">
            <Orders v-if="tabProps.orders"/>
          </div>
          <div class="tab-pane p-3" id="receivable" role="tabpanel">
            <Receivable v-if="tabProps.receivable"/>
          </div>
          <div class="tab-pane p-3" id="logistics" role="tabpanel">
            <Logistics v-if="tabProps.logistics"/>
          </div>
          <div class="tab-pane p-3" id="currentBalance" role="tabpanel">
              <div class="table-responsive scrollable" v-if="tabProps.currentBalance">
                  <table class="table table-bordered table-striped nowrap dataTable no-footer dtr-inline table-sm">
                      <thead class="thead-dark">
                      <tr>
                          <th>Type</th>
                          <th>Current Balance</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr>
                          <td>Bike</td>
                          <td> {{numberWithCommas(parseInt(-1*bikeTotalBalance))}}</td>
                      </tr>
                      <tr>
                          <td>Spare Parts</td>
                          <td>{{numberWithCommas(parseInt(-1* sparePartsTotalBalance))}} </td>
                      </tr>
                      </tbody>
                  </table>
              </div>
          </div>
          <div class="tab-pane p-3" id="pendingOrders" role="tabpanel">
            <PendingOrders v-if="tabProps.pendingOrders"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import Orders from "../../components/dashboard/Orders.vue"
import Receivable from "../../components/dashboard/Receivable.vue"
import Sales from "../../components/dashboard/Sales.vue"
import Services from "../../components/dashboard/Services.vue"
import PendingOrders from "../../components/dashboard/PendingOrders.vue"
import Logistics from "../../components/dashboard/Logistics.vue"
import CurrentBalance from "../../components/dashboard/CurrentBalance.vue"
import {bus} from "../../app";
import moment from "moment/moment";

export default {
  mixins: [Common],
  components: {Orders, Receivable, PendingOrders, Logistics, CurrentBalance},
  data() {
    return {
      tabProps: {
        dashboard: true,
        orders: false,
        receivable: false,
        sales: false,
        services: false,
        warranty: false,
        logistics: false,
        currentBalance: false,
        pendingOrders: false,
      },
        prevBalance:0,
        bikeTotalBalance:0,
        sparePartsTotalBalance:0,
      currentBalanceData:[]
    }
  },
  created() {
  },
  mounted() {
  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  methods: {
    allTables() {
      this.tabProps = {
        dashboard: false,
        orders: false,
        receivable: false,
        sales: false,
        services: false,
        warranty: false,
        logistics: false,
        currentBalance: false,
        pendingOrders: false,
        bikeLedgerBalance:[],
        sparePartsBalance:[],
        totalBalance:0
      }
    },
    getCurrentBalance(){
        let instance  = this
        this.axiosGet('dashboard/current-balance', function (response) {
            instance.bikeTotalBalance = instance.currentBalanceCalculation(response.bike)
            instance.sparePartsTotalBalance = instance.currentBalanceCalculation(response.spareParts)

        }, function (error) {

      });
    },
    currentBalanceCalculation(balanceInfo){
        try {
            this.masterInfo = balanceInfo.masterInfo
            this.customerLedgerOpening = balanceInfo.customer_ledger_opening
            let customerLedgerDetails = []
            balanceInfo.customer_ledger_details.forEach((item) => {
                let detail = customerLedgerDetails.find((i) => {
                    return i.TransNo === item.TransNo
                })
                if (detail) {
                    detail.invoiceInfo.push({
                        invoiceNo: item.InvoiceNo,
                        invoiceAmount: item.InvoiceAmount,
                        referenceNo: item.ReferenceNo,
                        paidAmount: item.PaidAmount
                    })
                } else {
                    item.invoiceInfo = []
                    item.invoiceInfo.push({
                        invoiceNo: item.InvoiceNo,
                        invoiceAmount: item.InvoiceAmount,
                        referenceNo: item.ReferenceNo,
                        paidAmount: item.PaidAmount
                    })
                    customerLedgerDetails.push(item)
                }
                this.customerLedgerDetails = customerLedgerDetails
            })
            let openingBalance = this.customerLedgerOpening[0].Opening
            this.customerLedgerDetails.map((ledger,key) => {
                if (key === 0) {
                    ledger.balance = Number(openingBalance) + Number(ledger.Amount)
                    this.prevBalance = ledger.balance
                } else {
                    ledger.balance = Number(ledger.Amount) + Number(this.prevBalance)
                    this.prevBalance = ledger.balance
                }
            })

            return this.prevBalance;
        } catch (e) {
            console.log(e)
    }
    },
    openTab(page) {
      this.allTables()
      if (page === 'dashboard') this.tabProps.dashboard = true
      if (page === 'orders') this.tabProps.orders = true
      if (page === 'receivable') this.tabProps.receivable = true
      if (page === 'logistics') this.tabProps.logistics = true
      if (page === 'pendingOrders') this.tabProps.pendingOrders = true
      if (page === 'currentBalance') {
        this.getCurrentBalance()
        this.tabProps.currentBalance = true
      }
      bus.$emit(`open-${page}-tab`)
    }
  }
}
</script>

<style>

</style>