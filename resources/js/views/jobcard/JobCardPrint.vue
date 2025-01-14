<template>
    <div class="content" id="jobCard" style="font-family: Helvetica, Arial, sans-serif; !important;">
        <div class="container-fluid">


            <table id="jobCardPrint">
                <thead style="background:none">
                <tr>
                    <th style="text-align: left; width: 33%;">
                      <img :src="`${'https://dms.ifadmotors.com'+'/assets/images/reLogo.png'}`" style="height: 100px;width: 100px">
                    </th>
                    <th style="width: 33%;color:#000000; text-align: center;"><h2>JOB CARD</h2></th>
                    <th style="text-align: right; width: 33%;">
                        <img :src="`${'https://dms.ifadmotors.com'+'/assets/images/logo-svg.png'}`" style="height: 30px;width: 100px">
                    </th>
                </tr>
                </thead>
            </table>

            <table id="jobCardPrintFirstBody" style="width:100%">
                <tbody>
                <tr>
                    <td class="bold" style="width: 16%">Dealer Name</td>
                    <td style="width: 16%;text-align: center">
                        {{ dealerName }}
                    </td>
                    <td class="bold" style="width: 16%;">Dealer Code</td>
                    <td style="width: 16%;text-align: center">
                        {{ serviceCenterCode }}
                    </td>
                    <td class="bold" style="width: 16%">Location</td>
                    <td style="width: 16%;text-align: center">
                        {{ dealerLocation }}
                    </td>
                </tr>

                <tr>
                    <td class="bold">Job Date <br>জবের তারিখ</td>
                    <td style="text-align: center">{{ jobDate }}</td>
                    <td class="bold">Serial No <br>সিরিয়াল নং</td>
                    <td style="text-align: center">{{ serial }}</td>
                    <td class="bold">Job Card No <br>জব কার্ড নং</td>
                    <td style="text-align: center">{{ jobCardNo }}</td>
                </tr>

                <tr>
                    <td class="bold">Customer Name <br>কাস্টমার নাম</td>
                    <td style="text-align: center">{{ customerName }}</td>
                    <td class="bold">Mobile No <br>মোবাইল নং</td>
                    <td style="text-align: center"> {{ mobileNo }}</td>
                    <td class="bold">Customer Entry Time<br>কাস্টমার প্রবেশের সময়</td>
                    <td style="text-align: center"> {{ moment(IDate).format('YYYY-MM-DD hh:mm A') }}</td>
                </tr>

                <tr>
                    <td class="bold">Address <br>ঠিকানা</td>
                    <td style="text-align: center">{{ address }}</td>
                    <td class="bold">Sold Date <br>বিক্রয়ের তারিখ</td>
                    <td style="text-align: center">{{ purchaseDate }}</td>
                    <td class="bold">Job Estimated Time <br>জব আনুমানিক সময়</td>
                    <td style="text-align: center">
                         <span v-if="parseInt(timeReqMin)>0">
                              {{ timeReqMin }}
                        </span>
                    </td>

                </tr>
                <tr>

                    <td class="bold">Model <br>মডেল</td>
                    <td colspan="1" style="text-align: center"> {{ model }}</td>
                    <td class="bold">Mileage <br>মাইলেজ</td>
                    <td style="text-align: center">{{ mileage }}</td>
                    <td class="bold">Job Start Time <br>জব শুরুর সময়</td>
                    <td style="text-align: center">
                        <span>
                              {{ moment(startTime).format('YYYY-MM-DD hh:mm A') }}
                        </span>
                    </td>
                </tr>

                <tr>

                    <td class="bold">Chassis No <br>চেসিস নং</td>
                    <td style="text-align: center">{{ chassisNo }}</td>
                    <td class="bold">Engine No <br>ইঞ্জিন নং</td>
                    <td style="text-align: center">{{ engineNo }}</td>
                    <td class="bold">Job End Time <br>জব শেষের সময়</td>
                    <td style="text-align: center">
                          <span v-if="parseInt(endTime)>2000">
                              {{ moment(endTime).format('YYYY-MM-DD hh:mm A') }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="bold"> Diagnosis Status</td>
                    <td style="text-align: center">
                        <span v-if="ytdStatus==='Y'">
                            Yes
                        </span>
                        <span v-else>
                            No
                        </span>
                    </td>
<!--                    <td class="bold">FI Status</td>-->
<!--                    <td colspan="1" style="text-align: center">-->
<!--                           <span v-if="fiStatus==='Y'">-->
<!--                            Yes-->
<!--                        </span>-->
<!--                        <span v-else>-->
<!--                            No-->
<!--                        </span>-->
<!--                    </td>-->
                    <td class="bold">Bay No</td>
                    <td style="text-align: center">
                        {{ bayName }}
                    </td>

                </tr>

                <tr>
                    <td class="bold">Job Type <br>জবের ধরন</td>
                    <td style="text-align: center">

                        <span v-if="jobTypeName==='Free Service'">
                            {{jobTypeName }}- {{scheduleTitle}}
                        </span>
                        <span v-else>
                            {{ jobTypeName }}
                        </span>

                    </td>
                    <td class="bold">Job Status</td>
                    <td style="text-align: center">
                        {{ jobStatus }}
                    </td>
                    <td class="bold">Technician Name <br>টেকনিশিয়ানের নাম</td>
                    <td style="text-align: center">{{ technicianName }}</td>


                </tr>


                </tbody>
            </table>

            <table id="problmepart" style="margin-top: 20px;width:100%">
                <tbody>
                <tr>
                    <td style="width: 66%; background-color: #CCCCCC;
                        text-transform: uppercase; font-weight: bold; font-size: 11px;text-align: center">Problem
                        Details <br> সমস্যার
                        বর্ণনা
                    </td>
                    <td style="width: 34%; text-transform: uppercase; background-color: #CCCCCC;
                        font-weight: bold; font-size: 11px;text-align: center">
                        Motorbike's external condition / মোটরবাইকের বাহ্যিক অবস্থা
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;">
                        <span v-for="(singleDetail, index) in problemDetail">
                            {{ singleDetail.ProblemDetailsName }} <br>
                        </span>
                        <span v-if="otherProblem!=null">
                           other problem -  {{otherProblem}}
                        </span>
                    </td>
                    <td rowspan="5" style="text-align: center">
                        <img style="margin-top: 10px;" width="150" height="auto"
                             :src="`${baseUrl+'assets/images/ref1.jpeg'}`">
                        <img style="margin-top: 10px; margin-bottom: 10px;" width="150" height="auto"
                             :src="`${baseUrl+'assets/images/ref2.jpeg'}`">
                    </td>
                </tr>
                <tr>
                    <td style="width: 66%; background-color: #CCCCCC;
                        text-transform: uppercase; height: 30px;
                         font-weight: bold; font-size: 11px;text-align: center">Failure Analysis & Diagnosis <br>সমস্যা
                        বিশ্লেষণ ও
                        ডায়াগনোসিস
                    </td>
                </tr>
                <tr>
                    <td style="width: 66%;text-align: left;">
                        {{motorcycleOuterCondition}}
                    </td>
                </tr>
                <tr>
                    <td style="width: 66%; background-color: #CCCCCC;
                        text-transform: uppercase; height: 30px;
                        font-weight: bold; font-size: 11px;text-align: center">Remedy & Result <br>প্রতিকার ও ফলাফল
                    </td>
                </tr>
                <tr>
                    <td style="width: 66%;text-align: left;">
                            {{reasonAndProblemDetails}}
                    </td>
                </tr>
                </tbody>
            </table>

            <table id="invoicetable" style="margin-top: 20px;width:100%">
                <thead>
                <tr style="text-transform: uppercase; background-color: #CCCCCC;
                        font-weight: bold; font-size: 11px; text-align: center;color: #000000">
                    <td>SL</td>
                    <td>Service Name</td>
                    <td>Service Code</td>
                    <td>Quantity</td>
                    <td>Unit Price</td>
                    <td>Total Price</td>
                    <td>Service Charge</td>
                    <td>Discount(%)</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(serviceField,serviceIndex) in serviceFields"
                    :key="serviceIndex">
                    <td>
                        {{ serviceIndex + 1 }}

                    </td>
                    <td>
                        {{ serviceField.workName }}
                    </td>
                    <td>
                        {{ serviceField.updateWorkCode }}
                    </td>
                    <td style="text-align: end">
                        {{ serviceField.quantity }}
                    </td>
                    <td style="text-align: end">
                        {{ serviceField.unitPrice }}
                    </td>
                    <td style="text-align: end">
                        {{ serviceField.totalPrice }}
                    </td>
                    <td style="text-align: end">
                        {{ serviceField.serviceCharge }}
                    </td>
                    <td style="text-align: end">
                        {{ serviceField.serviceDiscount }}
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align:right;;font-weight:bold">
                        Discount Price
                    </td>
                    <td colspan="7" style="font-weight:bold;text-align:right;">
                        {{ serviceFieldTotalDiscount }}
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align:right;font-weight:bold">
                         Total
                    </td>
                    <td colspan="7" style="font-weight:bold;text-align: end">
                        {{ serviceFieldGrandTotal }}
                    </td>
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
                <tbody>
                <tr v-for="(partsField,partsIndex) in partsFields"
                    :key="partsIndex">
                    <td>{{ partsIndex + 1 }}</td>
                    <td>
                        {{ partsField.partsName }}
                    </td>
                    <td style="text-align: end;">
                        {{ partsField.updatePartsCode }}

                    </td>
                    <td style="text-align: end">
                        {{ partsField.quantity }}
                    </td>
                    <td style="text-align: end">
                        {{ partsField.unitPrice }}
                    </td>
                    <td style="text-align: end">
                        {{ partsField.totalPrice }}
                    </td>
                    <td style="text-align: end">
                        {{ partsField.serviceCharge }}
                    </td>
                    <td style="text-align: end">
                        {{ partsField.discount }}
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
                         Total
                    </td>
                    <td colspan="2" style="text-align:right;font-weight:bold">
                        {{ partsFieldGrandTotal }}
                    </td>
                </tr>
                </tbody>
            </table>

            <table id="grandTotal" style="margin-top: 20px;width:100%">
                <tbody>
                <tr>
                    <td style="text-align:right;font-weight:bold;background: rgb(204, 204, 204)"> Grand  Total</td>
                    <td style="text-align:right;font-weight:bold;background: rgb(204, 204, 204)">
                        {{ parseFloat(serviceFieldGrandTotal)  + parseFloat(partsFieldGrandTotal)}}
                    </td>
                </tr>
                </tbody>
            </table>


            <table id="invoicetable" style="margin-top: 20px;width:100%">
                <tbody>
                <tr>
                    <td colspan="16" style="width: 100%; background-color: #CCCCCC;
                        text-transform: uppercase; height: 30px;
                         font-weight: bold; font-size: 11px;
                         text-align: center;">
                        Pre-Delivery Check Point / ডেলিভারির আগের চেক-আপ সমূহ
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 8px;">According to Job Card,All Tasks have been done <br>জব কার্ড
                        অনুযায়ী সকল কাজ সম্পন্ন করা হয়েছে?
                    </td>
                    <td style="text-align: center">
                        Yes/হ্যা
                    </td>
                    <td style="text-align: center">No/না</td>

                    <td colspan="8">Next Service Schedule Reminder Sticker <br>পরবর্তী সার্ভিস শিডিউলের স্টিকার লাগানো
                        হয়েছে?
                    </td>
                    <td style="text-align: center">Yes/হ্যা</td>
                    <td style="text-align: center">No/না</td>
                </tr>


                <tr>
                    <td colspan="16" style="width: 66%; background-color: #CCCCCC;
                        text-transform: uppercase; height: 30px;
                         font-weight: bold; font-size: 11px;
                         text-align: center;">
                        Customer Feedback / কাস্টমার ফিডব্যাক
                    </td>
                </tr>
                <tr>
                    <td colspan="8">
                        <div id="box">
                        </div>
                        <div style="float: left; width: 60px; line-height: 35px;">
                        </div>
                    </td>

                    <td colspan="2">
                        <div id="box"></div>
                        <div style="float: left; width: 60px; line-height: 35px;">Good</div>
                    </td>

                    <td colspan="4">
                        <div id="box"></div>
                        <div style="float: left; line-height: 35px;">Very Good</div>
                    </td>

                    <td colspan="2">
                        <div id="box"></div>
                        <div style="float: left; width: 60px; line-height: 35px;">Excellent</div>
                    </td>
                </tr>
                </tbody>
            </table>

            <table id="invoicetable" style="margin-top: 20px;width:100%">
                <tbody>
                <tr>
                    <td style="width: 25%;" rowspan="3">
                        <img width="100"
                             :src="signatureSupervisor"
                             alt="">
                    </td>
                    <td style="width: 25%;" rowspan="3"></td>
                    <td style="text-align: center;">
                        সার্ভিস গ্রহণের পূর্বে
                        <!-- Before Service Signature -->
                    </td>
                    <td style="text-align: center;">
                        সার্ভিস গ্রহণের পরে
                        <!-- After Service Signature -->
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        উপরে উল্লেখিত তথ্য অনুযায়ী সার্ভিস ও প্রয়োজনানুযায়ী পার্টস সহকারে কাজ করার জন্য অনুমতি প্রদান
                        করিলাম।
                        <!-- According to above mentioned service & parts information, I am agreed to proceed the service work. -->
                    </td>
                    <td style="text-align: center;">
                        উপরে উল্লেখিত সকল তথ্য অনুযায়ী মোটরবাইকটি মেরামত করা হয়েছে, যা সঠিকভাবে গ্রহন করছি।
                        <!-- According to above-mentioned repair works, I am receiving the bike correctly. -->
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;height: 70px">
                        <img width="100"
                             :src="signatureBefore" alt="">

                    </td>
                    <td style="text-align: center;">
                        <img width="100"
                             :src="signatureAfter" alt="">
                    </td>
                </tr>
                <tr>
                    <td style="height: 50px;"></td>
                    <td style="height: 50px;"></td>
                </tr>
                <tr style="background-color: #CCCCCC;
                        text-transform: uppercase; height: 30px;
                         font-weight: bold; font-size: 11px;
                         text-align: center;">
                    <td style="text-align:center">Authorized Signature <br>অনুমোদিত স্বাক্ষর</td>
                    <td style="text-align:center">Accounts Signature <br>অ্যাকাউন্টস স্বাক্ষর</td>
                    <td colspan="2" style="text-align:center">Customer Signature <br>কাস্টমার স্বাক্ষর</td>
                </tr>
                </tbody>
            </table>
            <p style="background:#CCCCCC; padding:5px;font-size:11px"><span
                    style="font-weight:bold">Dealer Name: </span>{{ customerName }}</p>
            <p style="color:red;font-size:11px">Diagnosis Status will not be served if any extra Electrical device is installed in
                Motorcycle</p>
            <p style="color:green;font-size:11px">Please print this page only if necessary. Go Green!</p>
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
            serviceCenterCode: '',
            chassisNo: '',
            allchassisNo: [],
            dealerName:'',
            bayName: '',
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
            jobTypeName: '',
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
            problemDetail: [],
            otherProblem: '',
            reasonAndProblemDetails: '',
            motorcycleOuterCondition:'',
            timeReqMin: 0,
            timeTaken: 0,
            startTime: '',
            endTime: 0,
            discountType: '',
            discount: 0,
            staffId: '',

            read: true,
            allTechnician: [],
            technicianName: '',
            allBay: [],
            allProblem: [],
            IDate: '',

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
            allReference: [],
            dealerLocation: '',
            signatureBefore: '',
            signatureAfter: '',
            signatureSupervisor: '',
            scheduleTitle:'',
            imgSource: ''
        }
    },
    mounted() {
        this.jobCardNo = this.$route.query.jobCardNo
        let instance = this;
        let JobCardNo = this.decodeConvert(this.$route.query.jobCardNo)
        this.axiosGet('jobCard/get/jobCard/modal/' + JobCardNo, function (response) {
            instance.title = 'Update Job Card';
            instance.buttonText = "Update";
            instance.buttonShow = true;
            instance.actionType = 'edit';
            if (response.existingJobCard[0]) {

                //Job Card
                let existingJobCardInfo = response.existingJobCard[0]
                instance.jobCardNo = existingJobCardInfo.JobCardNo
                instance.jobDate = moment(existingJobCardInfo.JobDate).format('YYYY-MM-DD')
                instance.chassisNo = existingJobCardInfo.ChassisNo
                instance.dealerName = existingJobCardInfo.DealerName
                instance.bayName = existingJobCardInfo.BayName
                instance.serviceCenterCode = existingJobCardInfo.ServiceCenterCode
                instance.engineNo = existingJobCardInfo.EngineNo
                instance.IDate = existingJobCardInfo.IDate
                instance.customerName = existingJobCardInfo.CustomerName
                instance.mobileNo = existingJobCardInfo.MobileNo
                instance.brand = existingJobCardInfo.Brand
                instance.model = existingJobCardInfo.Model
                instance.jobTypeName = existingJobCardInfo.JobTypeName
                instance.technicianName = existingJobCardInfo.TechnicianName
                instance.purchaseDate = moment(existingJobCardInfo.PurchaseDate).format('YYYY-MM-DD')
                instance.underWarrenty = existingJobCardInfo.UnderWarrenty
                instance.problemDetail = response.existingJobCard
                instance.motorcycleOuterCondition = existingJobCardInfo.MotorcycleOuterCondition
                instance.dealerLocation = existingJobCardInfo.dealerLocation

                instance.signatureBefore = response.signatureBefore
                instance.signatureAfter = response.signatureAfter
                instance.signatureSupervisor = response.signatureSupervisor

                instance.otherProblem = existingJobCardInfo.ProblemDetails
                instance.scheduleTitle = existingJobCardInfo.ScheduleTitle

                instance.registrationNo = existingJobCardInfo.RegistrationNo
                // instance.bookingNo=existingJobCardInfo.
                instance.serial = existingJobCardInfo.SerialNo
                instance.mileage = existingJobCardInfo.Mileage

                instance.technicianCode = [{
                    Details: existingJobCardInfo.TechnicianCode + '-' + existingJobCardInfo.TechnicianName,
                    TechnicianCode: existingJobCardInfo.TechnicianCode
                }]
                instance.bayCode = [{
                    Details: existingJobCardInfo.BayCode + '-' + existingJobCardInfo.BayName,
                    BayCode: existingJobCardInfo.BayCode
                }]
                instance.jobStatus = existingJobCardInfo.JobStatus
                instance.jobType = {
                    JobTypeName: existingJobCardInfo.JobTypeName,
                    Id: existingJobCardInfo.JobTypeId
                }
                let updateJobCardServiceName = ''
                let updateJobCardServiceId = ''
                if (existingJobCardInfo.ScheduleTitle != null && parseInt(existingJobCardInfo.FreeSScheduleID) !== 0) {
                    updateJobCardServiceName = existingJobCardInfo.ScheduleTitle
                    updateJobCardServiceId = existingJobCardInfo.FreeSScheduleID
                    //instance.checkLastServiceHistory(existingJobCardInfo.ChassisNo);
                    instance.childJobTypeStatusReadOnly = false
                } else {
                    updateJobCardServiceName = existingJobCardInfo.JobTypeName
                    updateJobCardServiceId = existingJobCardInfo.JobTypeId
                    instance.childJobTypeStatusReadOnly = true
                }
                instance.ServiceNo = {
                    JobTypeName: updateJobCardServiceName,
                    Id: updateJobCardServiceId
                }
                instance.ytdStatus = existingJobCardInfo.YTD_status
                // instance.ydTFile=existingJobCardInfo.
                instance.fiStatus = existingJobCardInfo.FI_Status
                instance.address = existingJobCardInfo.Address
                // instance.failureAnalysis=existingJobCardInfo.
                let problemEditArray = []
                response.existingJobCard.forEach((item) => {
                    if (item.ProblemDetailsName && item.ProblemDetailsId) {
                        let newObj = {
                            ProblemStatement: item.ProblemDetailsName,
                            PSID: item.ProblemDetailsId
                        }
                        problemEditArray.push(newObj)
                    }

                })
                instance.problemId = problemEditArray.length > 0 ? problemEditArray : []

                instance.otherProblem = existingJobCardInfo.ProblemDetails
                instance.reasonAndProblemDetails = existingJobCardInfo.ReasonProlemRepairDetails
                instance.timeReqMin = existingJobCardInfo.TimeRequired
                instance.timeTaken = existingJobCardInfo.TimeTaken
                instance.startTime = existingJobCardInfo.JobStartTime
                instance.endTime = existingJobCardInfo.JobEndTime
                instance.discountType = existingJobCardInfo.DiscountType
                instance.discount = existingJobCardInfo.DiscountPercent
                instance.staffId = existingJobCardInfo.ACIEmployeeId
                if (existingJobCardInfo.LocalMechanicsCode) {
                    instance.reference = {
                        MechanicsDetails: existingJobCardInfo.LocalMechanicsCode + '-' + existingJobCardInfo.LocalMechanicsName,
                        MechanicsCode: existingJobCardInfo.LocalMechanicsCode
                    }
                }

                //Spare Parts
                let spare_parts = response.existingJobCardPartsInfo;
                if (spare_parts.length > 0) {
                    instance.partsFields.splice(0, 1)
                    spare_parts.forEach((item) => {
                        if (item.ItemCode) {
                            instance.partsFields.push({
                                partsName: item.ProductName,
                                updatePartsCode: item.ItemCode,
                                currentStock: item.CurrentStock ? item.CurrentStock : 0,
                                quantity: item.Quantity ? item.Quantity : 0,
                                unitPrice: item.UnitPrice ? item.UnitPrice : 0,
                                totalPrice: item.Quantity * item.UnitPrice,
                                serviceCharge: item.ServiceCharge,
                                discount: item.Discount,
                            })
                        }

                    })
                }
                instance.calculateAllPartsInfo()
                let editService = response.existingJobCardServiceInfo;
                if (editService.length > 0) {
                    instance.serviceFields.splice(0, 1)
                    editService.forEach((serviceItem) => {
                        if (serviceItem.ItemCode) {
                            instance.serviceFields.push({
                                workName: serviceItem.WorkName,
                                updateWorkCode: serviceItem.ItemCode,
                                totalPrice: serviceItem.TotalPrice,
                                quantity: serviceItem.Quantity,
                                unitPrice: serviceItem.UnitPrice,
                                serviceCharge: serviceItem.ServiceCharge,
                                serviceDiscount: serviceItem.Discount
                            })
                        }

                    })
                }
                instance.calculateAllServiceInfo()
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
        encodeConvert(val) {
            let convertVal = btoa(val);
            return convertVal
        },
        decodeConvert(val) {
            let convertVal = atob(val);
            return convertVal
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
                tempPartsFieldTotalPrice += item.totalPrice ? parseFloat(item.totalPrice) : 0;
                tempPartsFieldTotalServiceCharge += item.serviceCharge ? parseFloat(item.serviceCharge) : 0;
                tempDiscount += item.discount ? item.discount <= 100 ? (parseFloat(item.totalPrice) * parseFloat(item.discount)) / 100 : 0 : 0;
            });
            this.partsFieldTotalPrice = parseFloat(this.partsFieldTotalPrice + tempPartsFieldTotalPrice).toFixed(2);
            this.partsFieldTotalServiceCharge = parseFloat(this.partsFieldTotalServiceCharge + tempPartsFieldTotalServiceCharge).toFixed(2);
            this.partsFieldTotalDiscount = parseFloat(this.partsFieldTotalDiscount + tempDiscount).toFixed(2)
            this.partsFieldGrandTotal = parseFloat((parseFloat(this.partsFieldTotalPrice) + parseFloat(this.partsFieldTotalServiceCharge)) - parseFloat(this.partsFieldTotalDiscount)).toFixed(2)
        },
        calculateAllServiceInfo() {
            let tempServiceFieldTotalDiscount = 0;
            let tempServiceFieldGrandTotal = 0;
            this.serviceFieldTotalDiscount = 0;
            this.serviceFieldGrandTotal = 0;

            this.serviceFields.forEach(function (item, index) {
                tempServiceFieldGrandTotal += item.unitPrice ? parseFloat(item.unitPrice) : 0;
                tempServiceFieldTotalDiscount += item.serviceDiscount ? item.serviceDiscount <= 100 ? (parseFloat(item.unitPrice) * parseFloat(item.serviceDiscount)) / 100 : 0 : 0;
            });
            this.serviceFieldTotalDiscount = parseFloat(tempServiceFieldTotalDiscount).toFixed(2);
            this.serviceFieldGrandTotal = parseFloat(parseFloat(tempServiceFieldGrandTotal) - parseFloat(this.serviceFieldTotalDiscount)).toFixed(2);

        },
        addModal(row = '') {
            this.loading = true;
            setTimeout(() => {
                bus.$emit('add-edit-jobCard', row);
            })
        },
        closeJobCard(jobCardNo) {
            this.infoAlert('Close JobCard', 'Are you sure?', 'Yes, Close JobCard.', () => {
                this.postData(jobCardNo);
            })
        },
        postData(jobCardNo) {
            let submitUrl = 'jobCard/job-close';
            this.axiosPost(submitUrl, {
                jobCardNo: jobCardNo,
            }, (response) => {
                this.successNoti(response.message);
                bus.$emit('refresh-datatable');
            }, (error) => {
                this.errorNoti(error);
            })
        },

        exportData() {
            bus.$emit('export-data', 'job-card-list-' + moment().format('YYYY-MM-DD'))
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

#jobCardPrintFirstBody .bold {
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