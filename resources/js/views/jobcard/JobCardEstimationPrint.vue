<template>
    <div class="content" id="jobCard">
        <div class="container-fluid">


            <table id="jobCardPrint">
                <thead style="background:none">
                <tr>
                    <th style="text-align: left; width: 33%;">
                        <a :href="`${baseUrl}`">
                            <img :src="`${baseUrl+'public/assets/images/yamahalogo.jpg'}`">
                        </a>
                    </th>
                    <th style="width: 33%;color:#000000; text-align: center;"><h2>ESTIMATED JOB CARD</h2></th>
                    <th style="text-align: right; width: 33%;">
                        <a :href="`${baseUrl}`">
                            <img :src="`${baseUrl+'public/assets/images/service.png'}`">
                            <img :src="`${baseUrl+'public/assets/images/aci.jpg'}`">
                        </a>
                    </th>
                </tr>
                </thead>
            </table>

            <table id="jobCardPrintFirstBody" style="width:100%">
                <tbody>
                <tr>
                    <td class="bold">Customer Name <br>কাস্টমার নাম</td>
                    <td style="text-align: center">{{customerName}}</td>
                    <td class="bold">Chassis No <br>চেসিস নং</td>
                    <td style="text-align: center">{{chassisNo}}</td>
                    <td class="bold">Model <br>মডেল</td>
                    <td style="text-align: center"> {{model}}</td>
                </tr>
                <tr>
                    <td class="bold">Mobile No <br>মোবাইল নং</td>
                    <td style="text-align: center"> {{mobileNo}}</td>
                    <td class="bold">Engine No <br>ইঞ্জিন নং</td>
                    <td style="text-align: center">{{engineNo}}</td>
                    <td class="bold">	Estimation Date <br>জবের তারিখ</td>
                    <td style="text-align: center">{{jobDate}}</td>

                </tr>

                </tbody>
            </table>

            <table id="invoicetable" style="margin-top: 20px;width:100%">
                <thead>
                <tr style="text-transform: uppercase; background-color: #CCCCCC;
                        font-weight: bold; font-size: 11px; text-align: center;color: #000000">
                    <td>SL</td>
                    <td>Part Name</td>
                    <td>Part No</td>
                    <td>Quantity</td>
                    <td>Unit Price</td>
                    <td>Total Price</td>
                    <td>Service Charge</td>
                    <td>Discount(%)</td>

                </tr>
                </thead>
                <tbody >
                <tr v-for="(partsField,partsIndex) in partsFields"
                    :key="partsIndex">
                    <td>{{partsIndex + 1}}</td>
                    <td>
                    {{partsField.product.ProductName}}
                    </td>
                    <td style="text-align: end;">
                     {{partsField.ItemCode}}

                    </td>
                    <td style="text-align: end">
                       {{partsField.Quantity}}
                    </td>
                    <td style="text-align: end">
                      {{partsField.UnitPrice}}
                    </td>
                    <td style="text-align: end">
                    {{partsField.TotalPrice}}
                    </td>
                    <td style="text-align: end">
                        {{partsField.ServiceCharge}}
                    </td>
                    <td style="text-align: end">
                        {{partsField.Discount}}
                    </td>

                </tr>
                <tr>
                    <td colspan="6" style="text-align:right;font-weight:bold">
                        Total Price: {{ partsFieldTotalPrice }}
                    </td>
                    <td style="text-align:right;;font-weight:bold">
                        Service Charge: {{ partsFieldTotalServiceCharge }}
                    </td>
                    <td style="text-align:right;;font-weight:bold">
                        Discount Price: {{ partsFieldTotalDiscount }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align:right;font-weight:bold">
                        Grand total
                    </td>
                    <td colspan="2" style="text-align:right;font-weight:bold">
                        {{ partsFieldGrandTotal }}
                    </td>
                </tr>
                </tbody>
            </table>
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
            jobCardNo: '',
            // baseUrl: Object.freeze(baseurl)
            baseUrl: baseurl,
            title: '',
            buttonText: '',
            status: '',
            confirm: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            errors: [],
            duplicateErrors: [],
            dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            jobDate: moment().format('YYYY-MM-DD'),
            serviceCenterCode:'',
            chassisNo: '',
            allchassisNo: [],
            bayName:'',
            freeServices: [],
            engineNo: '',
            customerName: '',
            mobileNo: '',
            brand: '',
            model: '',
            purchaseDate: '',
            underWarrenty: '',
            registrationNo: '',
            bookingNo: '',
            serial: '',
            mileage: 0,
            technicianCode: '',
            bayCode: '',
            jobStatus: '',
            jobTypeName:'',
            allJobType: [],
            parentJobType: [],
            childJobType: [],
            childJobTypeStatusReadOnly: false,
            jobType: '',
            ServiceNo: '',
            ytdStatus: '',
            ydTFile: '',
            AttachmentFlag: 0,
            fiStatus: '',
            address: '',
            failureAnalysis: '',
            problemId: '',
            problemDetail:'',
            otherProblem: '',
            reasonAndProblemDetails: '',
            timeReqMin: 0,
            timeTaken: 0,
            startTime: 0,
            endTime: 0,
            discountType: '',
            discount: 0,
            staffId: '',

            read: true,
            allTechnician: [],
            technicianName:'',
            allBay: [],
            allProblem: [],

            partsFields: [],
            partsFieldTotalPrice: 0,
            partsFieldTotalServiceCharge: 0,
            partsFieldTotalDiscount: 0,
            partsFieldGrandTotal: 0,

            serviceFields: [],
            serviceFieldTotalDiscount: 0,
            serviceFieldGrandTotal: 0,
            reference: '',

            serviceHistoryDealerName: '',
            serviceHistoryLastServiceType: '',
            serviceHistoryLastScheduleTitle: '',
            serviceHistoryLastServiceDate: '',
            serviceHistoryLastFeedbackRating: '',
            parts: [],
            works: [],
            allJobStatus: [],
            workName: '',
            workRate: 0,
            comments: '',
            active: '',
            workCode: '',
            allReference: []
        }
    },
    mounted() {
        this.jobCardNo = this.$route.query.jobCardEstimationNo
        let instance = this;
        let JobCardNo = this.$route.query.jobCardEstimationNo
        this.axiosGet('jobCard/get/estimation-job-card/' + JobCardNo, function (response) {
            instance.title = 'Update Job Card';
            instance.buttonText = "Update";
            instance.buttonShow = true;
            instance.actionType = 'edit';
            console.log(response)
            if (response.data[0]) {
                let existingInfo  = response.data[0];
                instance.customerName = existingInfo.CustomerName;
                instance.chassisNo = existingInfo.Chassisno;
                instance.model = existingInfo.ModelName;
                instance.mobileNo = existingInfo.ModelName;
                instance.engineNo = existingInfo.EngineNo;
                instance.jobDate = existingInfo.EstiamtionDate;
                instance.partsFields = existingInfo.jobcard_estimation_details;
                instance.calculateAllPartsInfo()
                console.log( instance.partsFields)
            }
        }, function (error) {

        });

    },
    destroyed() {
        bus.$off('export-data')
    },
    methods: {
        changeStatus() {
            this.loading = false
        },
        print() {
            this.d.print(this.$el, [this.cssText])
        },
        calculateAllPartsInfo() {
            let tempPartsFieldTotalPrice = 0;
            let tempPartsFieldTotalServiceCharge = 0;
            let tempDiscount = 0;
            this.partsFieldTotalPrice = 0;
            this.partsFieldTotalServiceCharge = 0;
            this.partsFieldGrandTotal = 0;
            this.partsFieldTotalDiscount = 0;

            this.partsFields.forEach(function (item, index) {
                tempPartsFieldTotalPrice += item.TotalPrice ? parseFloat(item.TotalPrice) : 0;
                tempPartsFieldTotalServiceCharge += item.ServiceCharge ? parseFloat(item.ServiceCharge) : 0;
                tempDiscount += item.Discount ? item.Discount <= 100 ? (parseFloat(item.TotalPrice) * parseFloat(item.Discount)) / 100 : 0 : 0;
            });
            this.partsFieldTotalPrice = parseFloat(this.partsFieldTotalPrice + tempPartsFieldTotalPrice).toFixed(2);
            this.partsFieldTotalServiceCharge = parseFloat(this.partsFieldTotalServiceCharge + tempPartsFieldTotalServiceCharge).toFixed(2);
            this.partsFieldTotalDiscount = parseFloat(this.partsFieldTotalDiscount + tempDiscount).toFixed(2)
            this.partsFieldGrandTotal = parseFloat((parseFloat(this.partsFieldTotalPrice) + parseFloat(this.partsFieldTotalServiceCharge)) - parseFloat(this.partsFieldTotalDiscount)).toFixed(2)
        }

    }
}
</script>

<style scoped>

#jobCardPrint {
    width: 100%;
}

#jobCardPrint table {
    width: 100%;
    background: none !important
}

#jobCardPrint table h2 {
    font-size: 22px;
    line-height: 60px;
}

#jobCardPrint table th {
    padding: 0px;
    margin: 0px;
}

#jobCardPrintFirstBody {
    border-collapse: collapse;
}

#jobCardPrintFirstBody table {
    width: 100px;
}
#jobCardPrintFirstBody .bold{
    font-weight: bold;
}

#jobCardPrintFirstBody table tr, td {
    border: 1px solid #000000;
}
</style>

<style>
#jobCard table tr td:nth-child(12) {
    width: 145px !important;
}
</style>