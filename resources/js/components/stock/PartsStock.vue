<template>
  <div>
    <div class="row" style="padding:8px 0px;">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
      </div>
    </div>
    <advanced-datatable :options="tableOptions" :customers="customers">
        <template slot="Product Code" slot-scope="row">
            <a href="javascript:" @click="openModal(row.item)">{{row.item.ProductCode}}</a>
        </template>
    </advanced-datatable>

      <div v-if="receiveHistoryModal">
          <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                      <div class="modal-header">
                          <div class="modal-title modal-title-font" id="exampleModalLabel">Receive History</div>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeModal">
                              Close
                          </button>
                      </div>
                              <div class="modal-body">

                                  <div class="table-responsive" v-if="receiveHistory.length>0">
                                      <table
                                              class="table table-bordered table-striped nowrap dataTable no-footer dtr-inline table-sm">
                                          <thead class="thead-dark">
                                          <tr>
                                              <th style="width:20%">Receive ID</th>
                                              <th style="width:9%"> Invoice No</th>
                                              <th style="width:9%">Invoice Date</th>
                                              <th style="width:9%">Receive Date</th>
                                              <th style="width:9%">Product Code</th>
                                              <th style="width:9%">Receive Qnty</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <tr v-for="(item,index) in receiveHistory"
                                              :key="index">
                                              <td>{{item.ReceiveID}}</td>
                                              <td>{{item.InvoiceNo}}</td>
                                              <td>{{item.InvoiceDate}}</td>
                                              <td>{{item.ReceiveDate}}</td>
                                              <td>{{item.ProductCode}}</td>
                                              <td style="text-align: end">{{item.ReceivedQnty}}</td>

                                          </tr>

                                          </tbody>
                                      </table>
                                  </div>

                              </div>

                  </div>
              </div>
          </div>
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
        source: 'stock/spare-parts-stock',
        search: true,
        slots: [1],
        hideColumn: [],
        slotsName: ['Product Code'],
        sortable: [],
        pages: [20, 50, 100],
        addHeader: [],
        filterOption: true,
        showFilter: ['customers'],
        colSize: ['col-md-12'],
        textRight: [0,5,6,7,8]
      },
      loading: false,
      cpLoading: false,
      tagLoading:false,
      customers:[],
      receiveHistoryModal:false,
      receiveHistory:[]
    }
  },
  mounted() {
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
    this.getData()
  },
  destroyed() {
    bus.$off('export-data')
  },
  methods: {
    changeStatus() {
      this.loading = false
    },
    getData() {
      this.axiosGet('product-promotion/load-customer', (response) => {
        this.customers = response.data
      })
    },
      closeModal() {
          $("#add-edit-dept").modal("toggle");
      },
      openModal(data) {
          this.receiveHistoryModal= true
          this.axiosPost('stock/get-spare-parts-receive-history',{
              MasterCode:data.MasterCode,
              ProductCode:data.ProductCode
          }, (response) => {
              $("#add-edit-dept").modal("toggle");
              this.receiveHistory=  response.receiveHistory
          }, (error) => {
              this.errorNoti(error);
          })

      },
    exportData() {
      bus.$emit('export-data','stock-spare-parts-list-'+moment().format('YYYY-MM-DD'))
    }
  }
}
</script>

<style scoped>

</style>