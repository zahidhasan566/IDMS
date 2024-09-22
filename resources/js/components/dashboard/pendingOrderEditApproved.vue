<template>
  <div class="container-fluid">
    <breadcrumb :options="['Edit & Approved Pending Orders']">

      <router-link :to="{name: 'Dashboard'}" class="btn btn-info btn-sm">Back</router-link>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form class="form-horizontal" id="formProduction" @submit.prevent="handleSubmit(onSubmit) " @keydown.enter="$event.preventDefault()">
                  <div class="card">
                    <div class="card-body">
                      <div class="row" >
                        <div class="col-md-12">
                          <div class="row">
                            <div class="table-responsive" >
                              <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                <thead class="thead-dark" v-if="headers.length > 0">
                                <tr>
                                  <th v-for="(item, index) in headers" v-if="index !== 1 && index !== 15">
                                    {{ item.replace(/_/g, ' ', " $1").trim() }}
                                  </th>
                                  <th>Current Quantity</th>
                                  <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, index) in contents">
                                  <td v-for="(item2, index) in headers" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' " v-if="index !== 1 && index !== 15">
                                    {{ item[item2] }}
                                  </td>
                                  <td>
                                    <input type="number" id="qty"
                                           placeholder="Qty"
                                           v-model="item.currentQty"
                                           @keyup="calculate(item,index)" min="0">
                                  </td>
                                  <td>{{ Number(item.total).toFixed(2) }}</td>
                                </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" style="text-align: end; margin-top: 10px;">
                      <div class="form-group row mb-0">
                        <div class="col-sm-12 text-right">
                          <submit-form name="Submit"/>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </ValidationObserver>
              <br>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
    </div>
  </div>
</template>

<script>
import {baseurl} from '../../base_url'
import {Common} from "../../mixins/common";
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import {isEmpty} from "lodash/lang";
import {bus} from "../../app";

export default {
  name: "EvaluationReport",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      allDealer: [],
      headers: [],
      contents: [],
      totalPrice:0,
      list: [],
      query: '',
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      form: new Form({
        currentQuantity:0,
      }),
      errors: [],
      isLoading: false,
      buttonShow: false,
      PreLoader: false,
      submitStatus: false,
    }
  },
  created() {
    //
  },
  computed:{
    //
  },
  mounted() {
    document.title = 'Edit & Approved| DMS';
    this.getAllEvaluation()

  },
  methods: {
    getAllEvaluation(){
      this.isLoading = true
      var submitUrl = "api/dashboard/edit-approve?orderNo="+this.$route.query.orderNo
      this.form.post(baseurl + submitUrl, this.config()).then(response => {
        if (response.data.data.length > 0){
          response.data.data.map((row) => {
            row.currentQty = 0
            row.total = 0
          })
          this.contents = response.data.data
          this.headers = Object.keys(response.data.data[0])
          this.isLoading = false
          this.calScore()

        }else {
          this.contents = []
          this.isLoading = false
        }
      }).catch(e => {
        //
      });

    },
    calculate(val,index) {
      console.log(index,val)
      val.total = (parseFloat(val.UnitPrice) + parseFloat(val.VAT)) * parseFloat(val.currentQty)
    },
    onSubmit() {
      this.PreLoader = true;
      this.$store.commit('submitButtonLoadingStatus', true);
      this.axiosPost('orders/store-parts',{
        products:this.form.products
      }, (response) => {
        if (response.status === 'success') {
          this.successNoti(response.message);
          this.$store.commit('submitButtonLoadingStatus', false);
          this.PreLoader = false;
          this.closeModal();
          this.getAllSpareParts();
        } else {
          this.PreLoader = false;
          this.$store.commit('submitButtonLoadingStatus', false);
          this.errorNoti(response.message);
        }
      })
    },
    formatHeading(item) {
      let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
      let title = item.replace(rex, '$1$4 $2$3$5')
      return title.replace('_',' ')
    },
    isInt(value) {
      return !isNaN(parseInt(value * 1))
    },
    config() {
      let token = localStorage.getItem('token');
      return {
        headers: {Authorization: `Bearer ${token}`}
      };
    },
  }
}
</script>

<style scoped>
#customer_form .form-control {
  font-size: 10px;
  height: 29px;
}
#customer_form .form-group {
  margin-bottom: 0;
}
#customer_form label {
  font-size: 11px!important;
}
.form-divider {
  padding: 6px 5px 5px 5px;
  border: 1px solid #4d87f64f;
  border-radius: 13px;
  margin: 0 auto;
}
#invoice2 .auto-complete2 {
  position: relative;
  display: block;
}
#invoice2 .auto-complete2 ul {
  list-style: none;
  margin: 0;
  padding: 5px 0 0 0px;
  position: absolute;
  width: 100%;
  border: 1px solid #0000000d;
  background: #ffffff;
  max-height: 200px;
  overflow-y: scroll;
  z-index: 999;
}
#invoice2 .auto-complete2 ul li{
  border-bottom: 1px solid #b7b7b7;
  background: #cbc4c4;
  padding: 5px;
  cursor: pointer;
}
#invoice2 .auto-complete2 ul li a{
  color: #000000;
}
#invoice2 .auto-complete2 ul li:hover{
  background: #fff3cd;
}
#invoice2 :focus{
  background: #fff3cd;
}
.form-divider-title {
  position: relative;
  top: -20px;
}

.form-divider-title p {
  position: absolute;
  padding: 0 25px;
  background: #ffffff;
  text-transform: uppercase;
  font-weight: bold;
  color: #000000  !important;
  font-size: 12px;
}
.tableFixHead {
  overflow-y: auto; /* make the table scrollable if height is more than 200 px  */
  height: 200px; /* gives an initial height of 200px to the table */
}
.tableFixHead thead th {
  position: sticky; /* make the table heads sticky */
  top: 0px; /* table head will be placed from the top of the table and sticks to it */
}
table {
  border-collapse: collapse; /* make the table borders collapse to each other */
  width: 100%;
}
th,
td {
  padding: 8px 16px;
  border: 1px solid #ccc;
}
th {
  background: #000000;
}
@media print{
  .dealerPrint{
    color:#000000 !important;
  }


}

</style>