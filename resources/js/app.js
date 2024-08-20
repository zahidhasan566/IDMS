
require('./bootstrap');
require('./validation/index');

import Vue from 'vue'
import App from './views/App'
import router from './router/index';
import store from './store/index'
import {baseurl} from './base_url';

//import v-form
import { Form } from 'vform'
window.Form = Form;

// main origin
Vue.prototype.mainOrigin = baseurl

import Toaster from 'v-toaster'
import 'v-toaster/dist/v-toaster.css'
Vue.use(Toaster, {timeout: 5000})

//Vue Multiselect
import Multiselect from 'vue-multiselect'
Vue.component('multiselect', Multiselect)

//Vue Datepicker
import { Datepicker } from '@livelybone/vue-datepicker';
Vue.component('datepicker', Datepicker);
import '@livelybone/vue-datepicker/lib/css/index.css'

//moment
import moment from 'moment'

//import sweetalert
import Swal from 'sweetalert2';
window.Swal = Swal;

//Print This
window.printThis = require('print-this');

//pre loader
import loader from "vue-ui-preloader";
Vue.use(loader);

//html2canvas
import VueHtml2Canvas from 'vue-html2canvas';
Vue.use(VueHtml2Canvas);

import vSelect from "vue-select";
Vue.component("v-select", vSelect);


Vue.prototype.moment = moment



export const bus = new Vue();


Vue.component('skeleton-loader', require('./components/loaders/Straight').default);
Vue.component('submit-form', require('./components/buttons/Submit').default);
Vue.component('submit-form-2', require('./components/buttons/Submit2').default);
Vue.component('datatable', require('./components/datatable/Index').default);
Vue.component('advanced-datatable', require('./components/datatable/Advanced').default);
Vue.component('general-datatable', require('./components/datatable/General').default);
Vue.component('datatable', require('./components/datatable/Datatable').default);
Vue.component('data-export', require('./components/datatable/Export').default);
Vue.component('breadcrumb', require('./components/layouts/Breadcrumb').default);
Vue.component('barchart', require('./components/chart/Bar').default);
Vue.component('piechart', require('./components/chart/Pie').default);

Vue.component('add-edit-user',require('./components/users/AddEditModal').default)
Vue.component('add-edit-dealer-user',require('./components/dealerusers/AddEditModal.vue').default)
Vue.component('add-survey-modal',require('./components/dashboard/SurveyModal.vue').default)
Vue.component('receive-details-modal',require('./components/dashboard/ReceiveDetailsModal.vue').default)
Vue.component('order-details-modal',require('./components/dashboard/OrderDetailsModal.vue').default)
Vue.component('reset-password',require('./components/users/Editpassword').default)
//Job Card
Vue.component('add-edit-jobCard-bay', require('./components/jobCard/BayAddEditModal.vue').default);
Vue.component('add-edit-stock-allocation', require('./components/stock/AllocationAddEditModal.vue').default);
Vue.component('add-edit-job-card-work', require('./components/jobCard/WorkAddEditModal.vue').default);
Vue.component('add-edit-job-card-technician', require('./components/jobCard/TechnicianAddEditModal.vue').default);
Vue.component('resignation-job-card-technician', require('./components/jobCard/TechnicianResignationModal.vue').default);
Vue.component('training-job-card-technician', require('./components/jobCard/TechnicianTrainingModal.vue').default);
Vue.component('add-edit-inquiry-follow-up', require('./components/inquiry/FollowUp.vue').default);
Vue.component('add-edit-job-card', require('./components/jobCard/JobCardAddEdit.vue').default);
Vue.component('add-edit-follow-up', require('./components/jobCard/FollowupAddEditModal.vue').default);
Vue.component('add-edit-user-customer', require('./components/jobCard/UserCustomerAddEditModal.vue').default);
Vue.component('add-edit-paid-service-schedule', require('./components/jobCard/PaidServiceAddEditModal.vue').default);
//credit payment
Vue.component('add-credit-payment', require('./components/creditPayment/creditPaymentModal.vue').default);

//custom pagination component
Vue.component('pagination', require('./components/partial/PaginationComponent.vue').default);
Vue.component('report-pagination', require('./components/partial/ReportPaginationComponent.vue').default);
Vue.component('add-edit-role', require('./components/roles/AddEditModal.vue').default);
Vue.component('add-edit-role-permission', require('./components/roles/AddEditPermissionModal.vue').default);

Vue.component('add-edit-promotion', require('./components/promotion/AddEditModal.vue').default);
Vue.component('add-edit-testrideagents', require('./components/testRide/TestRideAgentAddEditModal.vue').default);
Vue.component('add-edit-testrider', require('./components/testRide/TestRiderAddEditModal.vue').default);
Vue.component('add-edit-notify', require('./components/transportNotification/NotificationAddEditModal.vue').default);
Vue.component('add-edit-brta-registration', require('./components/logistics/BrtaRegistrationStatusAddEditModal.vue').default);

const app = new Vue({
    el: '#app',
    store: store,
    components: {App},
    router,
});
