<template>
  <div class="container-fluid">
    <breadcrumb :options="['Evaluation Report']">
      <button @click="print" class="btn btn-primary btn-sm small">
        <i class="mdi mdi-printer"></i> Print
      </button>
      <router-link :to="{name: 'AllEvaluationReport'}" class="btn btn-info btn-sm">Back</router-link>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="form-divider-title">
                          <p style="width: 160px">Dealer Details </p>
                        </div>
                        <br>
                        <br>
                        <div class="table-responsive">
                          <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                            <thead class="thead-dark">
                            <tr>
                              <th v-for="(item, index) in topHeaders">
                                {{formatHeading(item.toString())}}
                              </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, index) in topContents" >
                              <td v-for="(item2, index) in topHeaders" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' ">
                                {{ item[item2] }}
                              </td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div class="row" v-if="!isEmpty(manpowers)" >
                    <div class="col-md-12">
                      <div class="row">
                        <div class="form-divider-title">
                          <p style="width: 200px" > Service Manpower</p>
                        </div>
                        <br>
                        <br>
                            <div class="table-responsive">
                              <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                <thead class="thead-dark">
                                <tr>
                                  <th class="text-center">Service Supervisor</th>
                                  <th class="text-center">Sr. Technician</th>
                                  <th class="text-center">Technician</th>
                                  <th class="text-center">Assistant Technician/Helper</th>
                                  <th class="text-center">Spare Parts Man</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr >
                                  <td class="text-right">
                                    {{ manpowers[0].ManpowerCount }}
                                  </td>
                                  <td  class="text-right">
                                    {{ manpowers[1].ManpowerCount }}
                                  </td>
                                  <td  class="text-right">
                                    {{ manpowers[2].ManpowerCount }}
                                  </td>
                                  <td class="text-right">
                                    {{ manpowers[3].ManpowerCount }}
                                  </td>
                                  <td class="text-right">
                                    {{ manpowers[4].ManpowerCount }}
                                  </td>

                                </tr>
                                </tbody>

                              </table>
                            </div>
                      </div>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div class="row" >
                  <div class="col-md-12">
                    <div class="row">
                      <div class="form-divider-title">
                        <p style="width: 200px" > Sales Details </p>
                        <br>
                        <small style="color: #3f4143">(Justification Criteria: 5=Very Good, 4=Good, 3=Average, 2=Poor, 1=Very Poor,
                          0=Not Available/Applicable)</small>
                      </div>
                            <div class="table-responsive"  v-if="isEmpty(manpowers)">
                              <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                <thead class="thead-dark">
                                <tr>
                                  <th class="text-center">Sales Head</th>
                                  <th class="text-center">Target</th>
                                  <th class="text-center" >Actual</th>
                                  <th class="text-center">Weight</th>
                                  <th class="text-center" >Score</th>
                                  <th style="width: 35%"  class="text-center">Justifications / Observations
                                    <br> <small>(need to explain here with details for justification)</small>
                                  </th>
                                </tr>
                                </thead>
                                <tbody v-for="(items, i) in headerDatas"  v-if="contents.ServiceHeadId === headerDatas.ServiceHeadID">
                                  <tr style="background:lightgray">
                                    <td colspan="6" class="text-center" v-model="items.SeriveHeadID">
                                      <h6>{{items.ServiceHead}}</h6>
                                    </td>
                                  </tr>
                                  <tr v-for="(item, i) in contents" v-if="item.ServiceHeadId === items.ServiceHeadID">
                                    <td>{{item.SeriveSubHead}}</td>
                                    <td class="text-right">{{item.Target}}</td>
                                    <td class="text-right">{{item.Actual}}</td>
                                    <td class="text-right">{{item.Weight}}</td>
                                    <td class="text-right">{{item.Score}}</td>
                                    <td>{{item.observations}}</td>
                                  </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                  <td style="text-align: right; font-weight: bold;" colspan="4">
                                   <h6>100</h6>
                                  </td>
                                  <td rowspan="2"  class="text-right"><h6>{{ totalScore }}</h6></td>
                                  <td rowspan="2"></td>
                                </tr>
                                <tr>
                                  <td rowspan colspan="4" class="text-right"><h6>Score</h6></td>
                                </tr>
                                </tfoot>
                              </table>
                            </div>
                            <div class="table-responsive"  v-else>
                              <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                <thead class="thead-dark">
                                <tr>
                                  <th class="text-center">Specification(Requirements only)	</th>
                                  <th class="text-center">Status</th>
                                  <th class="text-center">Points</th>
                                  <th class="text-center">Reason</th>
                                  <th class="text-center">What Happen</th>
                                  <th class="text-center">What To Do</th>
                                  <th class="text-center">Deadline</th>
                                  <th class="text-center">Person In charge</th>
                                </tr>
                                </thead>
                                <tbody v-for="(items, i) in headerDatas"  v-if="contents.ServiceHeadId === headerDatas.ServiceHeadID">
                                  <tr style="background:lightgray">
                                    <td colspan="8" class="text-center" v-model="items.SeriveHeadID">
                                      <h6>{{items.ServiceHead}}</h6>
                                    </td>
                                  </tr>
                                  <tr v-for="(item, i) in contents" v-if="item.ServiceHeadId === items.ServiceHeadID">
                                    <td>{{item.RequirmentName}}</td>
                                    <td class="text-right">{{item.RequirmentDescription}}</td>
                                    <td class="text-right">{{item.Actual}}</td>
                                    <td class="text-right">{{item.Reason}}</td>
                                    <td class="text-right">{{item.WhatHappen}}</td>
                                    <td class="text-right">{{item.WhatToDo}}</td>
                                    <td class="text-right">{{item.Deadline}}</td>
                                    <td>{{item.PersonIncharge}}</td>
                                  </tr>
                                </tbody>
                                <tfoot v-if="isEmpty(manpowers)">
                                <tr>
                                  <td style="text-align: right; font-weight: bold;" colspan="4">
                                   <h6>100</h6>
                                  </td>
                                  <td rowspan="2"  class="text-right"><h6>{{ totalScore }}</h6></td>
                                  <td rowspan="2"></td>
                                </tr>
                                <tr>
                                  <td rowspan colspan="4" class="text-right"><h6>Score</h6></td>
                                </tr>
                                </tfoot>
                              </table>
                            </div>
                    </div>
                  </div>
                </div>
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
      headerDatas: [],
      headers: [],
      contents: [],
      manpowers: [],
      topContents: [],
      topHeaders: [],
      totalScore: 0,
      list: [],
      query: '',
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      Dealer:'',
      EvalutedBy:'',
      EvaluationDate:'',
      OpenDate:'',
      District:'',
      EvalutionType:'',
      form: new Form({
        district:'',
        openDate : moment().format('yyyy-MM-DD'),
        evaluationDate : moment().format('yyyy-MM-DD'),
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
    document.title = 'Evaluation Report Details| DMS';
    this.getAllEvaluation()

  },
  methods: {
    isEmpty,
    getAllEvaluation(){
      this.isLoading = true
      var submitUrl = "api/evaluation/details-report?evaluationId="+this.$route.query.evaluationId
        this.form.post(baseurl + submitUrl, this.config()).then(response => {
         // console.log(response.data.data)
          if (response.data.data.length > 0){
            this.topHeaders = Object.keys(response.data.dealer[0])
            this.topContents = response.data.dealer
            this.manpowers = response.data.manpower
            this.contents = response.data.data
            this.headerDatas = response.data.headerData
            this.allDealer = response.data.dealer[0]
            this.headers = Object.keys(response.data.data[0])
            this.Dealer = response.data.data[0].Dealer
            this.District = response.data.data[0].District
            this.EvalutedBy = response.data.data[0].EvalutedBy
            this.EvaluationDate = response.data.data[0].EvaluationDate
            this.EvalutionType = response.data.data[0].EvalutionType
            this.OpenDate = response.data.data[0].OpenDate
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
    print(){
      window.print()
    },
    calScore(){
        if (this.contents.length > 0) {
          this.contents.forEach((item) => {
            this.totalScore += Number(item.Score)
          })
        }
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