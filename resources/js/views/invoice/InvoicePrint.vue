<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Invoice Print']">
        <router-link :to="{name: 'InvoiceList'}" class="btn btn-primary btn-sm">Back</router-link>
      </breadcrumb>

      <div class="row">
        <div class="col-xl-12">
          <div class="row">
            <div class="col-md-12">
                <div style="display: flex">
                    <div class="col-md-6"> <img :src="`${baseUrl+'assets/images/reLogo.png'}`" style="height: auto;width: 100px"></div>
                    <div class="col-md-6" style="text-align: end;"> <img :src="`${baseUrl+'assets/images/logo-svg.png'}`" style="height: auto;width: 100px"></div>
                </div>
              <div class="first" style="margin-top: 200px">
                <p style="text-align: center;font-weight: bold;border:1px solid;width: 400px; margin: 0 auto;border-radius: 15px;font-size: 22px">Money Receipt</p>
                <br>
                <div class="col-md-12">
                  <div style="display:flex;">
                    <div class="col-md-7">
                      <p style="font-size: 18px;font-weight:bold">Invoice No : {{invoice.invoiceno}}</p>
                    </div>
                    <div class="col-md-5" style="text-align: right">
                      <p style="font-size: 18px;font-weight:bold">Date : {{invoice.InvoiceDate}}</p>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Received sum of BDT :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.unitprice - invoice.discount}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">In Words : </p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{inWords(parseInt(invoice.unitprice - invoice.discount))}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">From :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.customername}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Address :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.preaddress}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">As price against sale of :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.productname}}</span>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div style="display: flex">
                    <div class="col-md-6">
                      <div style="text-align: left;">
                        <p style="font-size:18px;margin:0;font-weight:bold">E/N : {{invoice.engineno}}</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div style="text-align: right;">
                        <p style="font-size:18px;margin:0;font-weight:bold">C/N : {{invoice.chassisno}}</p>
                      </div>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div style="display: flex">
                    <div class="col-md-6">
                      <div style="text-align: left;">
                        <p style="margin:0">
                          <span style="display: inline-block;border-top: 1px solid;width: 200px;text-align: center"></span>
                        </p>
                        <p style="font-size:20px;margin:0">Customer signature</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div style="text-align: right;">
                        <p style="margin:0">
                          <span style="display: inline-block;border-top: 1px solid;width: 200px;text-align: center"></span>
                        </p>
                        <p style="font-size:20px;margin:0">Authorised Signature</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div style="page-break-before:always">&nbsp;</div>

              <div class="second" style="margin-top: 200px">
                <p style="text-align: center;font-weight: bold;border:1px solid;width: 400px; margin: 0 auto;border-radius: 15px;font-size: 22px">Agreement Paper</p>
                <br>
                <p>
                  We hereby are declearing that we have sold the following motor cycle to <span>{{invoice.customername}}</span> Address
                  <span>{{invoice.preaddress}}</span> Father's Name <span>{{invoice.fathername}}</span> on Date : <span>{{invoice.InvoiceDate}}</span>. We have provided all the documents that required
                  for Registration. If there are any difficulties about the documents we provided in future then we will bear the
                  responsibilities. The infornation is
                </p>
                <br>
                <div class="col-md-12">
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">C/N : </p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.chassisno}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">E/N :  </p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.engineno}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Import from :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.makercountry}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Import Year :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.importyear}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Color :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.color}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">CC :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.cubiccapacity}}</span>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div style="display: flex">
                    <div class="col-md-6">
                      <div style="text-align: left;">

                      </div>
                    </div>
                    <div class="col-md-6">
                      <div style="text-align: right;">
                        <p style="margin:0">
                          <span style="display: inline-block;border-top: 1px solid;width: 200px;text-align: center"></span>
                        </p>
                        <p style="font-size:18px;margin:0">Authorised Seal & Signature</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div style="page-break-before:always">&nbsp;</div>

              <div class="third" style="margin-top: 200px">
                <p style="text-align: center;font-weight: bold;border:1px solid;width: 400px; margin: 0 auto;border-radius: 15px;font-size: 22px"> Invoice</p>
                <br>
                <div class="col-md-12">
                  <div style="display:flex;">
                    <div class="col-md-7">
                      <p style="font-size: 18px;font-weight:bold">Invoice No : {{invoice.invoiceno}}</p>
                    </div>
                    <div class="col-md-5" style="text-align: right">
                      <p style="font-size: 18px;font-weight:bold">Date : {{invoice.InvoiceDate}}</p>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Mr./Mrs/M/s :  </p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.customername}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Fathers Name :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.fathername}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Address :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.preaddress}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Phone :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.mobileno}}</span>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div style="display: flex">
                    <div class="col-md-12">
                      <table class="table table-bordered table-striped table-hover dt-responsive nowrap dataTable no-footer dtr-inline">
                        <thead class="thead-dark">
                          <tr style="color: black!important;background: white;font-size: 20px">
                            <th class="text-center">SN</th>
                            <th class="text-center">Particulars</th>
                            <th class="text-center">BDT</th>
                            <th class="text-center">PCS</th>
                            <th class="text-center">Total</th>
                          </tr>
                        </thead>
                        <tbody>
<!--                        <tr v-for="(detail, i) in details" :key="detail.id" v-if="details.length" style="font-size: 14px">-->
                        <tr style="font-size: 20px">
                          <td class="text-center">1</td>
                          <td class="text-center">{{invoice.productname}}</td>
                          <td class="text-center">{{invoice.unitprice}}</td>
                          <td class="text-center">1</td>
                          <td class="text-center">{{invoice.unitprice}}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr style="font-size: 20px">
                          <td colspan="3"></td>
                          <td class="text-center">Total</td>
                          <td class="text-center">{{invoice.unitprice}}</td>
                        </tr>
                        <tr style="font-size: 20px">
                          <td colspan="3"></td>
                          <td class="text-center">Discount</td>
                          <td class="text-center">{{invoice.discount}}</td>
                        </tr>
                        <tr style="font-size: 20px">
                          <td colspan="3"></td>
                          <td class="text-center">Net</td>
                          <td class="text-center">{{invoice.unitprice - invoice.discount}}</td>
                        </tr>
                        </tfoot>
                      </table>
                      <p>In word: {{inWords(parseInt(invoice.unitprice - invoice.discount))}}</p>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div style="display: flex">
                    <div class="col-md-6">
                      <div style="text-align: left;">
                        <p style="margin:0">
                          <span style="display: inline-block;border-top: 1px solid;width: 200px;text-align: center"></span>
                        </p>
                        <p style="font-size:20px;margin:0">Customer signature</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div style="text-align: right;">
                        <p style="margin:0">
                          <span style="display: inline-block;border-top: 1px solid;width: 200px;text-align: center"></span>
                        </p>
                        <p style="font-size:20px;margin:0">Authorised Seal & Sign</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div style="page-break-before:always">&nbsp;</div>

              <div class="four" style="margin-top: 170px">
                <p style="text-align: center;font-weight: bold;border:1px solid;width: 400px; margin: 0 auto;border-radius: 15px;font-size: 22px">Sales Certificate</p>
                <br>
                <p style="margin: 0">To</p>
                <p style="margin: 0">Assistant Director</p>
                <p style="margin: 0">BRTA</p>
                <p>Sub: Regarding new Motor cycle Registration</p>
                <p style="margin: 0">Sir, We hereby declare that the under mentioned {{invoice.productname}} 350 CC model Royel Enfield Motorcycle
                  is imported by IFAD Motors Ltd. Plot 7 (new), Tejgaon Industrial Area, Dhaka-1208. The motorcycle has been sold to Name - {{invoice.customername}},
                  Father Name - {{invoice.fathername}}, Address - {{invoice.preaddress}} through our registered dealer {{invoice.CustomerName}}. We request you to register this motorcycle against our commercial invoice no. {{invoice.invoiceno}}. The
                  information about this vehicle is mentioned below :</p>
                <br>
                <div class="col-md-12 salesCertificate">
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Class of vehicle :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.classofvehicle}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Maker Name :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.makername}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Makers Country :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.makercountry}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Color :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.color}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px"> Year of Manufacture :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.importyear}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Number of cylinders :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.numberofcylinders}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px;">C/N :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.chassisno}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">E/N :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.engineno}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Fuel used :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.fuelused}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Horse Power :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.horsepower}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">RPM :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.rpm}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Cubic capacilty :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.cubiccapacity}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Seats including driver :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.seats}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Wheel Base :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.wheelbase}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Weight :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.weight}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">No. of Stroke :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.enginetype}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">No of Tyre :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.nooftyre}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">No of Axel :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.noofaxel}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Tire Size Front :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.tiresizefront}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Tire Size Rear :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px">{{invoice.tiresizerear}}</span>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div style="display: flex">
                    <div class="col-md-6">
                      <div style="text-align: left;">

                      </div>
                    </div>
                    <div class="col-md-6">
                      <div style="text-align: right;">
                        <p style="margin:0">
                          <span style="display: inline-block;border-top: 1px solid;width: 200px;font-size: 20px;text-align: center"></span>
                        </p>
                        <p style="font-size:18px;margin:0">Signature</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div style="page-break-before:always">&nbsp;</div>

              <div class="five" style="margin-top: 200px">
                <p style="text-align: center;font-weight: bold;border:1px solid;width: 400px; margin: 0 auto;border-radius: 15px;font-size: 22px">Gate Pass</p>
                <br>
                <div class="col-md-12">
                  <div style="display:flex;">
                    <div class="col-md-7">
                      <p style="font-size: 20px;font-weight:bold">Invoice No : {{invoice.invoiceno}}</p>
                    </div>
                    <div class="col-md-5" style="text-align: right">
                      <p style="font-size: 18px;font-weight:bold">Date : {{invoice.InvoiceDate}}</p>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Name :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight: bold">{{invoice.customername}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Address :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight: bold">{{invoice.preaddress}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Phone :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.mobileno}}</span>
                    </div>
                  </div>
                  <div style="display:flex;">
                    <div class="col-md-3">
                      <p style="font-size: 20px">Dealer :</p>
                    </div>
                    <div class="col-md-9">
                      <span style="display: inline-block;border-bottom: 1px solid;width: 650px;font-size: 20px;font-weight:bold">{{invoice.CustomerName}}</span>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div style="display: flex">
                    <div class="col-md-12">
                      <table class="table table-bordered table-striped table-hover dt-responsive nowrap dataTable no-footer dtr-inline">
                        <thead>
                        <tr style="color: black!important;background: white;font-size: 18px">
                          <th class="text-center">SN</th>
                          <th class="text-center">Description</th>
                          <th class="text-center">Unit Price</th>
                          <th class="text-center">QTY</th>
                          <th class="text-center">Remarks</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--                        <tr v-for="(detail, i) in details" :key="detail.id" v-if="details.length" style="font-size: 14px">-->
                        <tr style="font-size: 18px">
                          <td class="text-center">1</td>
                          <td class="text-left">
                            <span style="text-align: left;font-weight: bold" >Name : {{invoice.productname}}</span><br>
                            <span style="font-weight: bold">E/N : {{invoice.engineno}}</span><br>
                            <span style="font-weight: bold">C/N : {{invoice.chassisno}}</span><br>
                          </td>
                          <td class="text-center">1</td>
                          <td class="text-center">{{invoice.unitprice}}</td>
                        </tr>
                        </tbody>
                      </table>
                      <p>N.B: This pass is valid for the day of issue only</p>
                    </div>
                  </div>
                  <br>
                  <br>
                  <div style="display: flex">
                    <div class="col-md-6">
                      <div style="text-align: left;">
                        <p style="margin:0">
                          <span style="display: inline-block;border-top: 1px solid;width: 200px;text-align: center"></span>
                        </p>
                        <p style="font-size:20px;margin:0">Received</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div style="text-align: right;">
                        <p style="margin:0">
                          <span style="display: inline-block;border-top: 1px solid;width: 200px;text-align: center"></span>
                        </p>
                        <p style="font-size:20px;margin:0">Authorised Signature</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {baseurl} from '../../base_url'
import moment from "moment";
import {Common} from "../../mixins/common";
export default {
  name: "List",
  mixins: [Common],
  data() {
    return {
      invoice: {},
        baseUrl: baseurl,
    }
  },
  created() {
    this.getSingleInvoice()
  },

  mounted() {
    document.title = 'Invoice Print | Bill';
  },
  methods: {
    getSingleInvoice(){
      axios.get(baseurl + `api/get-single-invoice/${this.$route.params.InvoiceId}`,this.config()).then((response)=>{
        console.log(response.data)
        this.invoice = response.data.invoice
        setTimeout(function(){
          window.print()
        },2000)
      });
    },
    config() {
      let token = localStorage.getItem('token');
      return {
        headers: {Authorization: `Bearer ${token}`}
      };
    },
  },
}
</script>

<style scoped>
p{
  font-size: 20px!important;
}
.salesCertificate p{
  margin: 0;
}
</style>
