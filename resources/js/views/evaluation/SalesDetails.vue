<template>
  <div class="container-fluid">
    <breadcrumb :options="['Evaluation sales']">
      <router-link :to="{name: 'evaluationSalesDetails'}" class="btn btn-primary btn-sm">Back</router-link>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(closeSubmit)">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 160px">Dealer Details</p>
                        </div>
                        <div class="col-12 col-md-3">
                          <ValidationProvider name="dealer" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Dealer <span style="color: red">*</span></label>
                              <multiselect v-model="dealer"
                                           :options="allDealer"
                                           data-index="1"
                                           :multiple="false"
                                           @input="dealerDistrict"
                                           :close-on-select="true"
                                           :clear-on-select="false"
                                           :preserve-search="true"
                                           placeholder="Dealer"
                                           label="CustomerName" track-by="CustomerCode">

                              </multiselect>
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="District" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>District</label>
                              <input type="text" class="form-control" readonly name="District" v-model="form.district" style="min-height: 35px">
                              <div class="error" v-if="form.errors.has('district')" v-html="form.errors.get('district')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                        <ValidationProvider name="Evaluation By" mode="eager" rules="required" v-slot="{ errors }" >
                          <div class="form-group">
                            <label>Evaluation By</label>
                            <input type="text" class="form-control"  data-required ="true" name="EvaluationBy" v-model="form.evaluationBy"  style="min-height: 35px" >
                            <div class="error" v-if="form.errors.has('evaluationBy')" v-html="form.errors.get('evaluationBy')" />
                            <span class="error-message"> {{ errors[0] }}</span>
                          </div>
                        </ValidationProvider>
                      </div>
                        <div class="col-12 col-md-2">
                        <ValidationProvider name="OpenDate" mode="eager" rules="required" v-slot="{ errors }">
                          <div class="form-group">
                            <label>Open Date</label>
                            <date-picker v-model="form.openDate" valueType="format"></date-picker>
                            <div class="error" v-if="form.errors.has('openDate')" v-html="form.errors.get('openDate')" />
                            <span class="error-message"> {{ errors[0] }}</span>
                          </div>

                        </ValidationProvider>
                      </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="DateOfBirth" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Evaluation Date</label>
                              <date-picker v-model="form.evaluationDate" valueType="format"></date-picker>
                              <div class="error" v-if="form.errors.has('evaluationDate')" v-html="form.errors.get('evaluationDate')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>

                          </ValidationProvider>
                        </div>

                      </div>
                    </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-divider m-b-15">
                        <div class="form-divider-title">
                            <p >Sales Details   <br>
                              <small style="color: #3f4143">(Justification Criteria: 5=Very Good, 4=Good, 3=Average, 2=Poor, 1=Very Poor, 0=Not Available/Applicable)</small></p>
                        </div>
                        <br>
                        <br>

                        <div class="row">
                          <div class="col-md-12">
                            <div class="table-responsive">
                              <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                <thead class="thead-dark">
                                <tr>
                                  <th class="text-center">Sales Head</th>
                                  <th class="text-center">Target</th>
                                  <th class="text-center" style="width: 10%">Actual</th>
                                  <th class="text-center">Weight</th>
                                  <th class="text-center" style="width: 10%">Score</th>
                                  <th style="width: 35%"  class="text-center">Justifications / Observations
                                    <br> <small>(need to explain here with details for justification)</small>
                                  </th>
                                </tr>
                                </thead>
                                <tbody v-for="(items, i) in headerDatas"  v-if="contents.ServiceHeadID == headerDatas.ServiceHeadID">

                                  <tr style="background:lightgray">
                                    <td colspan="6" class="text-center" v-model="items.SeriveHeadID">
                                      <h6 >{{items.ServiceHead}}</h6>
                                    </td>
                                  </tr>
                                  <tr v-for="(item, i) in contents" v-if="item.ServiceHeadID == items.ServiceHeadID" >

                                    <td>{{item.SeriveSubHead}}</td>
                                    <td class="text-center">{{item.Target}}</td>
                                    <td>
                                      <ValidationProvider name="actual" mode="eager" rules="required" v-slot="{ errors }">
                                        <div class="form-group">
                                          <input type="number" data-required="true" class="form-control" name="actual" v-model="item.actual" min="0" max="5" @input="calEvaluationScore(item)" required>
                                          <div class="error" v-if="form.errors.has('actual')" v-html="form.errors.get('actual')" />
                                          <span class="error-message"> {{ errors[0] }}</span>
                                        </div>

                                      </ValidationProvider>
                                    </td>
                                    <td class="text-center">{{item.Weight}}</td>
                                    <td>
                                      <input type="number" class="form-control" readonly name="score" v-model="item.score" min="0" max="5">
                                    </td>
                                    <td>
                                      <input type="text" class="form-control" name="observations" v-model="item.observations">
                                      <div class="error" v-if="form.errors.has('observations')" v-html="form.errors.get('observations')" />
                                      <span class="error-message"> {{ errors[0] }}</span>
                                    </td>
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
                          </div>
                        </div>
                        <br>
                      </div>
                    </div>
                  </div>
                  <br>
                  <button type="submit" class="btn btn-primary float-right submit_on_enter" >Submit</button>
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

export default {
  name: "Invoice",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {

      allDealer: [],
      headerDatas: [],
      headers: [],
      contents: [],
      totalScore: 0,
      list: [],
      query: '',
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      actual:[],
      grade:0,
      observations:[],
      dealer:'',
      form: new Form({

        district:'',
        evaluationBy:'',
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
    document.title = 'Evaluation sales | DMS';
    this.getDealers()
    this.getAllEvaluation()

  },
  methods: {
    getAllEvaluation(){
      this.isLoading = true
      this.form.Export = '';
      var submitUrl=''
      submitUrl = 'api/evaluation/service-sales-details'
      this.form.post(baseurl + submitUrl, this.config()).then(response => {
        if (response.data.data.length > 0){
          this.contents = []
          this.headerDatas = response.data.headerData
          this.headers = Object.keys(response.data.data[0])
          response.data.data.forEach((item) => {
            item.actual = 0;
            item.observations = '';
            this.contents.push(item)
          })
          this.isLoading = false
        }else {
          this.contents = []
          this.exportShow = true;
          this.isLoading = false
        }
      }).catch(e => {
        //
      });
      // if (this.$route.query.evaluationId>0){
      //    submitUrl = "api/evaluation/service-sales-details?evaluationId="+this.$route.query.evaluationId
      //   this.form.post(baseurl + submitUrl, this.config()).then(response => {
      //     console.log(response)
      //   }).catch(e => {
      //     //
      //   });
      //
      // }else{
      // }
    },

    getDealers() {
      let instance = this;
      this.axiosGet('evaluation/get-all-dealer', function (response) {
        instance.allDealer = response.dealer;
      }, function (error) {
      });
    },
    dealerDistrict(val) {
      let district = val.CustomerCode;
      this.axiosPost('evaluation/region-data', {
        dealerCode: district
      }, (response) => {
        this.form.district = response.data[0].RegionName
      })
    },
    calEvaluationScore(item){
      item.score =parseFloat ((item.actual/item.Target)*(item.Weight)).toFixed(2)
      if (item.score){
        this.list = []
        this.contents.map((val) => {
          this.list.push(val.score)
        });
      }else {
        this.list = []
      }
      this.calTotalScore(this.list);
    },
    calTotalScore(val){
      let total =0;
      for(let i=0;i<val.length;i++){
        if(parseFloat(val[i]))
          total += parseFloat(val[i]);
      }
      this.totalScore= parseFloat(total).toFixed(2);
    },
    onSubmit(){
        this.PreLoader = true;
        this.axiosPost('evaluation/store', {
          evaluations: this.contents,
          allData: this.form,
          dealer: this.dealer,
        }, (response) => {
          if (response.status === 'success') {
            this.PreLoader = false;
            this.successNoti(response.message);
            this.allData = []
            this.headers = '';
          } else {
            this.PreLoader = false;
            this.errorNoti(response.message);
          }
        })
    },
    closeSubmit(){
      if (this.totalScore <= 50){
        this.infoAlert('Close Evaluation', 'Are you sure?','Yes, Close Evaluation.', () => {
          this.onSubmit()
        });
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
  color: #4d79f6b5  !important;
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

</style>