<template>
  <div>
    <div class="modal fade" id="survey-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal" id="form" @submit.prevent="handleSubmit(onSubmit)" v-if="dataShow">
              <div class="modal-body">
                <div class="row" v-for="(question,index) in questions" :key="index">
                  <div class="col-12 col-md-12">
                    <div class="form-group">
                      <label>{{ question.SurveyQuestion}}</label>
                      <div class="b-questions">
                        <div v-for="(ans,i) in question.Answers" :key="i" style="padding: 0 10px">
                          <input type="radio" :name="question.SurveyQuestionID"
                                 v-model.number="formData[question.SurveyQuestionID]"
                                 :value="parseInt(ans.SurveyAnswerID)" required>
                            {{ ans.SurveyAnswer }}
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 col-md-12">
                    <div class="form-group">
                      <label>আপনার মূল্যবান মন্তব্য</label>
                      <div class="b-questions">
                          <textarea style="width: 100%" v-model="comment"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <submit-form v-if="buttonShow" :name="buttonText"/>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
          </ValidationObserver>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
import Receivable from "../../components/dashboard/Receivable.vue"

export default {
  mixins: [Common],
  components: {Receivable},
  data() {
    return {
      receivable: false,
      title: 'Invoice Receive Survey Form',
      questions: [],
      answers: [],
      dataSet: [],
      comment:'',
      formData: {

            // group1: '',
            // group2: '',
            // group3: '',
            // group4: '',
            // group5: '',
        },
      invoiceNo: '',
      allowedVisible: false,
      buttonText: 'Submit',
      buttonShow: true,
      dataShow: false
    }
  },
  computed: {},
  mounted() {
    $('#survey-modal').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('survey-event', (invoiceNo) => {
      this.invoiceNo =invoiceNo;
      if (invoiceNo) {
        this.axiosGet('dashboard/invoice-receive-survey-data', (response) => {
          this.questions = response.questions
          this.answers = response.answers
          this.questions.forEach((question) => {
            question.Answers = this.answers.filter((ans) => {
              return ans.SurveyQuestionID === question.SurveyQuestionID
            })
          })
          this.dataShow = true
        }, function (error) {

        });
      }
      $("#survey-modal").modal("toggle");
    })
  },
  destroyed() {
    bus.$off('add-edit-user')
  },
  methods: {
    onSubmit() {
        console.log('survey', this.formData)
      this.axiosPost('dashboard/do-survey',{
        invoiceNo:this.invoiceNo,
        SurveyAnswerIDs:this.formData,
        SurveyComment:this.comment
      }, (response) => {
        if (response.status === 'success') {
          this.successNoti(response.message);
          $("#survey-modal").modal("hide");
          this.receivable = true
          bus.$emit('refresh-datatable');
        } else {
          this.errorNoti(response.message);
        }
      })
    },
  }
}
</script>

<style scoped>
.b-questions {
  font-family: 'Arial', sans-serif !important;
  display: flex;padding: 10px 0;font-size: 15px;
}
</style>
