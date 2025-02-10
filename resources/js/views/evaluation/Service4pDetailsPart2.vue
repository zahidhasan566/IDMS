<template>
    <div class="container-fluid">
        <breadcrumb :options="['Evaluation Service 4p Second Part']">
            <router-link :to="{name: 'InvoiceList'}" class="btn btn-primary btn-sm">Back</router-link>
        </breadcrumb>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="datatable" v-if="!isLoading">
                        <div class="card-body" id="customer_form">
                            <ValidationObserver v-slot="{ handleSubmit }">
                                <form @submit.prevent="handleSubmit(onSubmit)">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-divider m-b-15">
                                                <div class="form-divider-title">
                                                    <p>Problem Details</p>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <p style="background-color: #26b99ae0;color: #FFFFFF;text-align: center">
                                                                Successfully Complete 1st Part. Now please complete the
                                                                second part.</p>
                                                            <div class="table-responsive">
                                                                <table
                                                                        class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                                                    <thead style="background: #000000 !important; color: #FFFFFF!important;">

                                                                    <tr>
                                                                        <td>SL</td>
                                                                        <td>Requirement</td>
                                                                        <td>Reason</td>
                                                                        <td>What Happen</td>
                                                                        <td>What To Do</td>
                                                                        <td>Deadline</td>
                                                                        <td>Person In-charge</td>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody v-for="(singleAllServiceEvaluationProblemDetail,Index) in allServiceEvaluationProblemDetails">
                                                                    <tr>
                                                                        <td>{{ Index + 1 }}</td>
                                                                        <td>
                                                                            {{ singleAllServiceEvaluationProblemDetail.RequirmentName }}
                                                                        </td>
                                                                        <td><input type="text" class="form-control"
                                                                                   data-required="true"
                                                                                   name="reason"
                                                                                   v-model="singleAllServiceEvaluationProblemDetail.reason"
                                                                                   style="min-height: 35px"></td>
                                                                        <td><input type="text" class="form-control"
                                                                                   data-required="true"
                                                                                   name="reason"
                                                                                   v-model="singleAllServiceEvaluationProblemDetail.whatHappend"
                                                                                   style="min-height: 35px"></td>
                                                                        <td><input type="text" class="form-control"
                                                                                   data-required="true"
                                                                                   name="reason"
                                                                                   v-model="singleAllServiceEvaluationProblemDetail.whatToDo"
                                                                                   style="min-height: 35px"></td>
                                                                        <td>
                                                                            <date-picker
                                                                                    v-model="singleAllServiceEvaluationProblemDetail.deadline"
                                                                                    style="min-height: 35px"
                                                                                    valueType="format"></date-picker>
                                                                        </td>
                                                                        <td><input type="text" class="form-control"
                                                                                   data-required="true"
                                                                                   name="reason"
                                                                                   v-model="singleAllServiceEvaluationProblemDetail.personIncharge"
                                                                                   style="min-height: 35px"></td>
                                                                    </tr>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-primary float-right submit_on_enter">Submit
                                    </button>
                                </form>
                            </ValidationObserver>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40"
                    objectbg="#999793" opacity="80" name="circular"></loader>
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
    components: {DatePicker},
    data() {
        return {
            allServiceEvaluationProblemDetails: [],
            evaluationId: '',
            errors: [],
            isLoading: false,
            buttonShow: false,
            PreLoader: false,
        }
    },
    mounted() {
        this.getData()
    },
    methods: {

        getData() {
            this.evaluationId = this.$route.params.evaluationId
            //this.evaluationId = 8398
            this.axiosGet('evaluation/get-service-4p-part-2-supporting-data/' + this.evaluationId, (response) => {
                try {
                    this.allServiceEvaluationProblemDetails = response.part2Requirements
                } catch (e) {
                    console.log(e)
                }

            }, function (error) {
            });
        },


        onSubmit() {
            this.axiosPost('evaluation/service4p-store-part2', {
                evaluationId: this.evaluationId,
                allServiceEvaluationProblemDetails: this.allServiceEvaluationProblemDetails,
            }, (response) => {
                if (response.status === 'Success') {
                    this.successNoti(response.message);
                    this.$router.push({name: 'Service4pIndex'})
                } else {
                    this.errorNoti(response.message);
                }
            })
        },

        formatHeading(item) {
            let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
            let title = item.replace(rex, '$1$4 $2$3$5')
            return title.replace('_', ' ')
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
    font-size: 11px !important;
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
    color: #4d79f6b5 !important;
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