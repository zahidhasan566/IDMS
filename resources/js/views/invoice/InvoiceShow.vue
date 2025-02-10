<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Invoice Show']">
        <router-link :to="{name: 'InvoiceList'}" class="btn btn-primary btn-sm">Back</router-link>
      </breadcrumb>
      <div class="row">
        <div class="col-xl-12">
          <div class="row">
            <div class="col-md-6">
              <h4 class="page-title" style="font-size: 14px">Invoice Details</h4>
              <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                  <tbody class="thead-dark">
                    <tr>
                      <th style="width: 50%">Chassis No : </th>
                      <th style="width: 50%">{{ invoice.chassisno }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Invoice Date : </th>
                      <th style="width: 50%">{{ invoice.InvoiceDate }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Invoice Id : </th>
                      <th style="width: 50%">{{ invoice.InvoiceId }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Unit Price : </th>
                      <th style="width: 50%">{{ invoice.unitprice }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Color : </th>
                      <th style="width: 50%">{{ invoice.color }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">RPM : </th>
                      <th style="width: 50%">{{ invoice.rpm }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Wheel Base : </th>
                      <th style="width: 50%">{{ invoice.wheelbase }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Tire Size Front : </th>
                      <th style="width: 50%">{{ invoice.tiresizefront }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Seats : </th>
                      <th style="width: 50%">{{ invoice.seats }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">No of Axel : </th>
                      <th style="width: 50%">{{ invoice.noofaxel }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Maker Name : </th>
                      <th style="width: 50%">{{ invoice.makername }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Engine Type : </th>
                      <th style="width: 50%">{{ invoice.enginetype }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Customer Name : </th>
                      <th style="width: 50%">{{ invoice.customername }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Customer Email : </th>
                      <th style="width: 50%">{{ invoice.EMail }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Mother Name : </th>
                      <th style="width: 50%">{{ invoice.mothername }}</th>
                    </tr>
                    <tr>
                      <th style="width: 50%">Present Address : </th>
                      <th style="width: 50%">{{ invoice.preaddress }}</th>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-6">
              <h4 class="page-title" style="font-size: 14px">Invoice Details</h4>
              <div class="table-responsive" >
                <table class="table table-bordered table-striped table-hover dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                  <tbody class="thead-dark">
                  <tr>
                    <th style="width: 50%">Engine No : </th>
                    <th style="width: 50%">{{ invoice.engineno }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Invoice Time : </th>
                    <th style="width: 50%">{{ invoice.invoicetime }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Product : </th>
                    <th style="width: 50%">{{ invoice.productname }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Discount : </th>
                    <th style="width: 50%">{{ invoice.discount }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Fuel Used : </th>
                    <th style="width: 50%">{{ invoice.fuelused }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Cubic Capacity : </th>
                    <th style="width: 50%">{{ invoice.cubiccapacity }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Weight : </th>
                    <th style="width: 50%">{{ invoice.weight }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Tire Size Rear : </th>
                    <th style="width: 50%">{{ invoice.tiresizerear }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">No of Tyre : </th>
                    <th style="width: 50%">{{ invoice.nooftyre }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Class Of Vehicle : </th>
                    <th style="width: 50%">{{ invoice.classofvehicle }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Maker Country : </th>
                    <th style="width: 50%">{{ invoice.makercountry }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Import Year : </th>
                    <th style="width: 50%">{{ invoice.importyear }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Customer Mobile : </th>
                    <th style="width: 50%">{{ invoice.mobileno }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Father Name : </th>
                    <th style="width: 50%">{{ invoice.fathername }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">Permanent Address : </th>
                    <th style="width: 50%">{{ invoice.peraddress }}</th>
                  </tr>
                  <tr>
                    <th style="width: 50%">NID : </th>
                    <th style="width: 50%">{{ invoice.nid }}</th>
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
import {baseurl} from '../../base_url'
import moment from "moment";
import {Common} from "../../mixins/common";
export default {
  name: "List",
  mixins: [Common],
  data() {
    return {
      invoice: {}
    }
  },
  created() {
    this.getSingleInvoice()
  },

  mounted() {
    document.title = 'Invoice Show | Bill';
  },
  methods: {
    getSingleInvoice(){
      axios.get(baseurl + `api/get-single-invoice/${this.$route.params.InvoiceId}`,this.config()).then((response)=>{
        console.log(response.data)
        this.invoice = response.data.invoice
      });
    },
    config() {
      let token = localStorage.getItem('token');
      return {
        headers: {Authorization: `Bearer ${token}`}
      };
    },
  },
}
</script>

<style scoped>
p{
  font-size: 18px!important;
}
</style>
