<template>
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4 class="page-title">iHelpBD</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">Send Foton Call</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body" v-if="!isLoading">
                            <ValidationObserver v-slot="{ handleSubmit }">
                                <form @submit.prevent="handleSubmit(onSubmit)" @keydown.enter="$event.preventDefault()">
                                    <div class="row">
                                        <div class="col-md-2">
                                        <ValidationProvider name="dateFrom" mode="eager" v-slot="{ errors }" rules="required">
                                            <div class="form-group">
                                            <label>Date From<span class="error">*</span></label>
                                                <date-picker v-model="form.dateFrom" valueType="format"></date-picker>
                                                <div class="error" v-if="form.errors.has('dateFrom')" v-html="form.errors.get('dateFrom')" />
                                            <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                        </div>
                                        <div class="col-md-2">
                                            <ValidationProvider name="dateTo" mode="eager" v-slot="{ errors }" rules="required">
                                                <div class="form-group">
                                                    <label>Date To<span class="error">*</span></label>
                                                    <date-picker v-model="form.dateTo" valueType="format"></date-picker>
                                                    <div class="error" v-if="form.errors.has('dateTo')" v-html="form.errors.get('dateTo')" />
                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                </div>
                                            </ValidationProvider>
                                        </div> 
                                    </div>
                                    <div class="col-md-2" style="margin-top: 30px">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </form>
                            </ValidationObserver> 
                        </div>
                        <div v-else>
                            <skeleton-loader :row="14"/>
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
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    mixins: [Common, baseurl],
    components: { DatePicker },

    data() {
        return {
            form: new Form({
                dateFrom : moment().format('yyyy-MM-DD'),
                dateTo : moment().format('yyyy-MM-DD'),
            }),
            isLoading:false
        }
    },
    methods: {
        onSubmit(){
            this.isLoading = true
            this.form.post(baseurl + "api/iHelpBD/send-foton-call", this.config()).then(response => {
                this.successNoti(response.data.message);
                this.isLoading = false
            }).catch(error => {
                this.isLoading = false
                if (error.response) {
                    this.errorNoti(error.response.data.message);
                } else if (error.request) {
                    this.errorNoti("No response received from the server.");
                } else {
                    this.errorNoti("An error occurred: " + error.message);
                }
            });
        },
    }
   
}
</script>
