<template>
  <div class="container-fluid">
    <breadcrumb :options="['Dealer Invoice Receive Survey Report']">
      <button type="button" class="btn btn-success btn-sm" @click="exportReport">Export to Excel</button>
    </breadcrumb>

    <div class="row">
      <div class="col-xl-12">
        <div class="card">
          <div class="datatable">
            <div class="card-body">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(filterAllReport)" @keydown.enter="$event.preventDefault()">
                  <div class="row">
                    <div class="col-md-2">
                      <ValidationProvider name="DateFrom" mode="eager" v-slot="{ errors }" rules="required">
                        <div class="form-group">
                          <label>Date From<span class="error">*</span></label>
                          <date-picker v-model="form.DateFrom" valueType="format"></date-picker>
                          <div class="error" v-if="form.errors.has('DateFrom')" v-html="form.errors.get('DateFrom')"/>
                          <span class="error-message"> {{ errors[0] }}</span>
                        </div>
                      </ValidationProvider>
                    </div>
                    <div class="col-md-2">
                      <ValidationProvider name="DateTo" mode="eager" v-slot="{ errors }" rules="required">
                        <div class="form-group">
                          <label>Date To<span class="error">*</span></label>
                          <date-picker v-model="form.DateTo" valueType="format"></date-picker>
                          <div class="error" v-if="form.errors.has('DateTo')" v-html="form.errors.get('DateTo')"/>
                          <span class="error-message"> {{ errors[0] }}</span>
                        </div>
                      </ValidationProvider>
                    </div>
                    <div class="col-md-2" style="margin-top: 30px">
                      <button type="submit" class="btn btn-success"><i class="mdi mdi-filter"></i>Filter</button>
                    </div>
                  </div>
                </form>
              </ValidationObserver>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card" v-if="isLoading">
          <template v-for="(dataSet,i) in dataSets">
            <barchart :labels="labels" :datasets="dataSet.answers"></barchart>
          </template>
        </div>
      </div>
    </div>
    <data-export/>
  </div>
</template>

<script>
import {baseurl} from '../../base_url'
import {Common} from "../../mixins/common";
import XLSX from "xlsx";
import {bus} from "../../app";
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
  name: "Report",
  mixins: [Common],
  components: {DatePicker},
  data() {
    return {
      labels: [
        'Poor (1 Out of 5) ',
        'Average (2 Out of 5) ',
        'Good (3 Out of 5) ',
        'Very Good (4 Out of 5) ',
        'Excellent (5 Out of 5) ',
        'Achieving Point in '
      ],
      dataSets: [],
      businesses: [],
      sample_for: [],
      headers: [],
      contents: [],
      Depots: [],
      customer: [],
      jobTypes: [],
      jobStatus: [],
      ReportName: '',
      user: '',
      chartShow: false,
      loaderTag: false,
      baseUrl: baseurl,
      form: new Form({
        DateFrom: moment().startOf('month').format('yyyy-MM-DD'),
        DateTo: moment().format('yyyy-MM-DD'),
        CustomerCode: '',
        JobStatus: '',
        JobType: '',
        Query: '',
        Export: '',
        pagination: {
          current_page: 1,
          from: 1,
          to: 1,
          total: 1,
        },
      }),
      isLoading: false,
      errors: [],
      exportShow: false,
    }
  },
  created() {
    //
  },
  mounted() {
    this.filterAllReport();
  },
  methods: {
    filterAllReport() {
      this.labels = [
        'Poor (1 Out of 5) ',
        'Average (2 Out of 5) ',
        'Good (3 Out of 5) ',
        'Very Good (4 Out of 5) ',
        'Excellent (5 Out of 5) ',
        'Achieving Point in '
      ],
      this.isLoading = false
      this.form.Export = '';
      this.form.post(baseurl + "api/reports/dealer-invoice-survey-report-data", this.config()).then(response => {

        this.chartShow = true
        var dealer_survey_report_value = response.data;

        try {
          let dataSet = []

          response.data.forEach((question,key) => {
            let ques = dataSet.find((q) => {
              return q.SurveyQuestionID === question.SurveyQuestionID
            })
            if (ques) {
              ques.answers[0].data.push(Number(question.Achivement).toFixed(2))
            } else {
              question.answers = [{}]
              question.answers[0].data = []
              question.answers[0].backgroundColor = ['#a49d02','#0263a4','#02a40d','#5502a4','#0258a4','#02a499']
              question.answers[0].label = question.SurveyQuestion
              question.answers[0].borderColor = '#6488EA'
              question.answers[0].borderWidth = 1
              question.answers[0].data.push(Number(question.Achivement).toFixed(2))
              dataSet.push(question)
            }
          })
          console.log(dataSet.length)
          dataSet.forEach((d,i) => {
            this.labels[i] = this.labels[i] + d.answers[0].data[i]+' %'
          })

          this.dataSets = dataSet
          console.log(dataSet, "dataSet")
          this.isLoading = true
        } catch (e) {
          console.log(e)
        }
      }).catch(e => {
        //
      });
    },
    exportReport() {
      this.form.Export = 'Y';
      this.form.post(baseurl + "api/reports/dealer-invoice-survey-report-data", this.config()).then(response => {
        let dataSets = response.data.data;
        if (dataSets.length > 0) {
          let columns = Object.keys(dataSets[0]);
          columns = columns.filter((item) => item !== 'row_num');
          let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
          columns = columns.map((item) => {
            let title = item.replace(rex, '$1$4 $2$3$5')
            return {title, key: item}
          });
          //this.generateExport(dataSets, columns, 'Job Card Report')
          bus.$emit('data-table-import', dataSets, columns, 'Dealer Invoice Receive Survey Report')
        }
      }).catch(e => {
        //
      });
    },
    encodeConvert(val) {
      let convertVal = btoa(val);
      return convertVal
    },
    getData() {
      this.axiosGet('app-supporting-data', (response) => {
        this.user = response.user.UserId;
      }, (error) => {
        this.errorNoti(error)
      })
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
#ceilingModal .form-control {
  font-size: 10px;
  height: 25px;
}

#ceilingModal label {
  font-size: 11px !important;
}

.form-divider {
  padding: 6px 0px 5px 5px;
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

#invoice2 .auto-complete2 ul li {
  border-bottom: 1px solid #b7b7b7;
  background: #cbc4c4;
  padding: 5px;
  cursor: pointer;
}

#invoice2 .auto-complete2 ul li a {
  color: #000000;
}

#invoice2 .auto-complete2 ul li:hover {
  background: #fff3cd;
}

#invoice2 :focus {
  background: #fff3cd;
}

</style>