<template>
    <div class="container-fluid">
        <breadcrumb :options="{title}">
            <router-link class="btn btn-primary" :to="{name:'Stock'}">Back</router-link>
        </breadcrumb>
        <div class="row" v-if="actionType==='add'">
            <div class="col-12 col-md-6">
                <ValidationProvider name="Invoice Adjustment File" mode="eager" rules="required"
                                    v-slot="{ errors }">

                    <label style="font-weight:bold" for="inputExcelFile">* Stock File (Excel) <span
                            class="error">*</span></label>
                    <input type="file" ref="inputFile" @change="readExcelFile($event)" class="btn btn-info btn-sm">
                    <span class="error-message"> {{ errors[0] }}</span>

                </ValidationProvider>
            </div>
            <div class="col-md-3">
                <label style="font-weight:bold" for="downloadExcelFile">Sample Spare Parts Stock  File: <span
                        class="error">*</span></label>
                <a href="#" style="float: right;padding: 6px;margin-right: 5px" @click="downloadDemoExcel"
                   class="btn btn-success btn-sm">Download Sample</a>
            </div>
        </div>

        <div class="row" style="padding-top: 15px" v-if="form.ExcelData.importStatus">
            <div class="row col-md-12">
                <div class="col-md-4"><p>Uploaded Data: </p></div>
                <div class="col-md-2"></div>
                <div class="col-md-6">
                </div>


            </div>
            <div class="row col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                        <thead class="thead-dark">
                        <tr>
                            <th>SL</th>
                            <th>Product Code</th>
                            <th>Part No</th>
                            <th>Product Name</th>
                            <th>Current Stock</th>
                            <th>Real Count</th>
                            <th>Adjustment Stock</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(excelData, i) in form.ExcelData" :key="i" v-if="form.ExcelData.length>0">
                            <td scope="row">{{ ++i }}</td>
                            <td>{{ excelData.Product_Code }}</td>
                            <td>{{ excelData.Part_No }}</td>
                            <td>{{ excelData.Product_Name }}</td>
                            <td><input type="text" class="form-control" :readOnly="actionType==='edit'" v-model="excelData.Current_Stock"></td>
                            <td><input type="text" class="form-control" :readOnly="actionType==='edit'" v-model="excelData.Real_Count"></td>
                            <td><input type="text" class="form-control" :readOnly="actionType==='edit'" v-model="excelData.Adjustment_Stock"></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm"
                                        @click="removeRow(i)"><i
                                        class="ti-close"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12" style="padding-top: 15px;text-align:end">
                    <button type="button" id="submitSparePartsStock" style="padding:6px" class="btn btn-success btn-sm" @click="onSubmit">{{buttonText}}
                    </button>
                </div>
            </div>

        </div>
        <div>
            <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2"
                    bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
        </div>
    </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import XLSX from "xlsx";
import moment from "moment";

export default {
    mixins: [Common],
    data() {
        return {
            ceilings: [],
            businesses: [],
            Depots: [],
            results: [],
            title:'',
            query: "",
            editMode: false,
            isLoading: false,
            form: new Form({
                // ExcelData: [],
                ExcelData: [{
                    Product_Code: '',
                    Part_No: '',
                    Product_Name: '',
                    Unit_Price: 0,
                    Current_Stock: 0,
                    Real_Count: 0,
                    Adjustment_Stock: 0,
                    importStatus:false,
                }],
                masterCode:'',
                adjustmentInvoiceNo:''

            }),
            loadTempData: [],
            allDealer: [],
            errors: [],
            returnExcelData:[],
            buttonShow: false,
            masterCode: '',
            actionType: '',
            buttonText:'',
            PreLoader: false,
        }
    },
    created() {
    },
    mounted() {
        this.title= 'Add Spare Parts Stock'
        this.actionType = 'add'
        this.buttonText = 'Submit'
    },
    methods: {
        decodeConvert(val){
            let convertVal = atob(val);
            return convertVal
        },
        getSupportingData(val){
            if (val.length > 2){
                this.selectBox = false;
                axios.get(baseurl + "api/invoice-spare-parts/get-adjustment-dealer-data?CustomerCode=" + val , this.config()).then(response => {
                    this.allDealer = response.data.allDealers
                }).catch(e => {

                });
            }
        },
        checkFieldValue(){
            let tempProductCode = this.form.ExcelData[0]['Product_Code']
            let tempAdjustmentStock = this.form.ExcelData[0]['Adjustment_Stock']
            let tempPartNo = this.form.ExcelData[0]['Part_No']
            let tempCurrentStock = this.form.ExcelData[0]['Current_Stock']
            let tempRealCount = this.form.ExcelData[0]['Real_Count']
            let tempUnitPrice = this.form.ExcelData[0]['Unit_Price']
            // console.log(this.form.ExcelData)
            if(this.form.masterCode===''){
                this.errors.push('Master Code Needed')
                this.$toaster.error('Master Code Needed');
            }

                // else if(tempProductCode==='' || tempAdjustmentStock==='' || tempProductCode==='' || tempPartNo===''
                // && tempCurrentStock===''  || tempRealCount==='' || tempUnitPrice===''){
                //     this.$toaster.error('At Least One Product Needed');
                //     this.errors.push(1)
            // }
            else{
                this.errors=[]
            }
        },
        removeRow(i) {
            this.form.ExcelData.splice(i-1, 1)
            if (this.errors[i-1] !== undefined) {
                this.errors.splice(i-1, 1)
            }
        },
        readExcelFile(e) {
            var files = e.target.files, f = files[0];
            var reader = new FileReader();
            reader.onload = (e) => {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, {type: 'array'});
                let sheetName = workbook.SheetNames[0]
                let worksheet = workbook.Sheets[sheetName];
                this.form.ExcelData = XLSX.utils.sheet_to_json(worksheet);
                this.form.ExcelData.importStatus = true
            };
            reader.readAsArrayBuffer(f);
        },
        downloadDemoExcel() {
            axios.get(baseurl + "api/stock/stock-export-demo-excel", this.config()).then((res) => {
                const downloadAnchor = document.createElement("a");
                downloadAnchor.setAttribute("href", res.data);
                downloadAnchor.setAttribute("download", "stock_file_upload_format.xls");
                document.body.appendChild(downloadAnchor);
                downloadAnchor.click();
                //remove anchor download
                document.body.removeChild(downloadAnchor);
            })
                .catch((error) => {
                    console.log(error);
                });
        },
        onSubmit() {
            this.PreLoader = true;
            var submitUrl = 'api/stock/store-flagship-spare-parts';
            if(this.errors.length === 0){
                $("#submitSparePartsStock").hide();
                this.form.post(baseurl + submitUrl, this.config()).then(response => {
                    if(response){
                        this.PreLoader = false;
                        this.successNoti(response.data.message);
                        this.form.ExcelData = []
                        if (this.actionType === 'edit') {
                            this.$router.push({name: 'Stock',})
                        }
                        else{
                            this.$router.go(this.$router.currentRoute)
                        }


                    }
                }).catch(e => {
                    this.PreLoader = false;
                    this.errorNoti(e);
                });
            }

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