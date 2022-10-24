<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
        <div class="container-fluid h-100 bg-color" v-if="showcontainer">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax">
                <side-bar></side-bar>
                <div class="col  p-0 m-0">
                    <bread-crumb :paths="paths"></bread-crumb>
                    <div class="main-view-2 row">
                        <div class="col">
                        <h1 class="tile_h1">New order</h1>
<!--
                         <div class="row">
                            <div class="col-2">
                                <button class="btn btn-outline-dark body_small_bold" @click="showConfirmModal">
                                    Show modal
                                </button>
                            </div>
                        </div>
-->
                        <div class="row">
                            <div class="col">
                        <div class="barcode-scan col p-0" @click="featureUnavailable('Scan barcode')"><svg width="41" height="40" viewBox="0 0 41 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32.542 0.000139651C32.1562 0.00726752 31.8482 0.324459 31.8482 0.712927C31.8482 1.09962 32.1562 1.41682 32.542 1.42571H38.9173V7.9245V7.92272C38.9173 8.31832 39.2342 8.63729 39.6253 8.63729C40.0165 8.63729 40.3351 8.31832 40.3351 7.92272V0.714566C40.3351 0.320752 40.02 0 39.6289 0L32.542 0.000139651ZM1.35145 0.035779C1.16206 0.0339971 0.98152 0.11062 0.846995 0.244269C0.714246 0.377917 0.639908 0.561458 0.639908 0.750359V7.88553C0.636368 8.07798 0.708935 8.2633 0.841684 8.40051C0.974433 8.53772 1.15674 8.61435 1.3479 8.61435C1.53907 8.61435 1.72137 8.53772 1.85412 8.40051C1.98687 8.2633 2.05944 8.07798 2.0559 7.88553V1.46155H8.51096C8.70212 1.46511 8.8862 1.39205 9.02248 1.25841C9.15877 1.12476 9.23488 0.941217 9.23488 0.748758C9.23488 0.556298 9.15877 0.372762 9.02248 0.239109C8.8862 0.103677 8.70212 0.0306196 8.51096 0.0359661L1.35145 0.035779ZM4.88963 5.7399V16.4319H6.30562V5.7399H4.88963ZM7.7216 5.7399V16.4319H10.5553V5.7399H7.7216ZM12.6759 5.7399V16.4319H14.0937V5.7399H12.6759ZM15.5097 5.7399V16.4319H16.9257V5.7399H15.5097ZM19.0532 5.7399V16.4319H21.8798V5.7399H19.0532ZM24.0075 5.7399V16.4319H25.4235V5.7399H24.0075ZM26.8395 5.7399V16.4319H28.2555V5.7399H26.8395ZM30.3777 5.7399V16.4319H33.2114V5.7399H30.3777ZM34.6274 5.7399V16.4319H36.0434V5.7399H34.6274ZM1.27727 19.2844V19.2827C0.893185 19.313 0.602902 19.6444 0.622386 20.0329C0.641856 20.4195 0.965761 20.7207 1.35163 20.7082H39.5813C39.9654 20.6993 40.2734 20.3839 40.2734 19.9955C40.2734 19.6088 39.9654 19.2916 39.5813 19.2827H1.35163H1.27729L1.27727 19.2844ZM4.88982 23.5629V34.255L6.3058 34.2532V23.5612L4.88982 23.5629ZM7.72178 23.5629V34.255H10.5555V23.5629H7.72178ZM12.6761 23.5629V34.255H14.0939V23.5629H12.6761ZM15.5099 23.5629V34.255L16.9258 34.2532V23.5612L15.5099 23.5629ZM19.0534 23.5629V34.255H21.88V23.5629H19.0534ZM24.0077 23.5629V34.255H25.4237V23.5629H24.0077ZM26.8397 23.5629V34.255H28.2556V23.5629H26.8397ZM30.3778 23.5629V34.255L33.2116 34.2532V23.5612L30.3778 23.5629ZM34.6276 23.5629V34.255H36.0435V23.5629H34.6276ZM1.34134 31.3537C1.15373 31.3554 0.974959 31.4321 0.842192 31.5675C0.711215 31.7029 0.638644 31.8847 0.640415 32.0754V39.2835C0.640415 39.4742 0.714753 39.6559 0.847502 39.7896C0.982023 39.925 1.16256 39.9999 1.35195 39.9999H8.43911C8.6285 40.0034 8.81258 39.9304 8.94887 39.795C9.08515 39.6613 9.16126 39.4778 9.16126 39.2871C9.16126 39.0946 9.08515 38.9111 8.94887 38.7774C8.81258 38.642 8.6285 38.5689 8.43911 38.5743H2.05655V32.0755C2.05832 31.883 1.98398 31.6995 1.84946 31.5623C1.71494 31.4269 1.53263 31.352 1.34147 31.3538L1.34134 31.3537ZM39.6191 31.3893C39.4315 31.3911 39.2509 31.4695 39.1199 31.6031C38.9889 31.7386 38.9164 31.9221 38.9181 32.111V38.5368H32.4649C32.079 38.5457 31.7728 38.8611 31.7728 39.2496C31.7728 39.6363 32.0791 39.9535 32.4649 39.9624H39.6295C40.0207 39.9606 40.3358 39.6398 40.3358 39.246V32.1109C40.3375 31.9184 40.2632 31.7349 40.1269 31.5994C39.9924 31.4622 39.8101 31.3874 39.6189 31.3892L39.6191 31.3893Z" fill="black"/>
                        </svg>
                            Scan bag barcode to pre-fill order details
                        </div>
                            </div>
                        </div>

                        <div class="panel-wrapper row">

                            <div class="panel-col-1 col  ">
                                <customer-details-panel @setcustomerid="setCustomerID"></customer-details-panel>
                                <div class="panel">
                                    <h2 class="subtitle">Payment</h2>
                                        <div class="col-12" v-if="cur_cust && card_details && card_details.id">
                                            <div class="row">
                                                <div class="payment_subheading col-4">Type</div>
                                                <div class="payment_subheading col-6">Details</div>
                                                <div class="payment_subheading col-2">Exp</div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-4">
                                                    <div class="payment_text">Pay as you go</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="payment_text d-flex align-items-center">
                                                        <img src="/images/mastercard.svg" class="payment-icon"/>
                                                        {{card_details.cardNumber}}
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="payment_text">{{card_details.dateexpiration}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="col-12">
                                            <div class="row mb-2">
                                                <div class="form-group mb-0 col-6 payment-method">
                                                    <select-options
                                                        v-model="paymentMethod"
                                                        :options="[
                                                            { display:'Credit Card', value: 'Credit Card' },
                                                            { display:'BACS', value: 'BACS' },
                                                        ]"
                                                        :placeholder="'Select'"
                                                        :label="'Payment Method'"
                                                        :name="'paymentMethod'">
                                                    </select-options>
                                                </div>
                                            </div>
                                            <transition>
                                                <div class="row" appear v-if="paymentMethod=='Credit Card'" id="credit_card_div">
                                                <div class="credit-card col-12">

                                                    <div class="row mb-2">
                                                        <div class="form-group col-12 cardholder">
                                                            <label for="">Cardholder name</label>
                                                            <input type="text" placeholder="Name" v-model="form.cardHolderName" required class="form-control">
                                                            <div v-if="cardErrors.cardHolderName" class="error">
                                                                <small>{{ cardErrors.cardHolderName }}</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="form-group col-12 carddetails">
                                                            <label for="">Card details</label>
                                                            <div class="input-group mb-0" :class="{ 'error': cardErrors.cardNumber}">
                                                                <span class="input-group-text">
                                                                    <i class="credit-card-icon" :class="cardBrandClass"></i>
                                                                </span>
                                                                <input ref="cardNumInput" :class="{ 'error': cardErrors.cardNumber}" :data-error="(cardErrors.cardNumber)?true:false" v-model="form.cardDetails" type="tel" v-cardformat:formatCardNumber class="form-control" placeholder="Enter card details">
                                                            </div>
                                                            <div v-if="cardErrors.cardNumber" class="error">
                                                                <small>{{ cardErrors.cardNumber }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="form-group col-6 cardexpdate mb-0">
                                                            <label for="">Expiration date</label>
                                                            <input type="text" ref="cardExpInput" placeholder="mm/yy" :class="{ 'error': cardErrors.cardExpiry}" v-model="form.cardExpDate" maxlength="10" class="form-control" v-cardformat:formatCardExpiry>
                                                            <div v-if="cardErrors.cardExpiry" class="error">
                                                                <small>{{ cardErrors.cardExpiry }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-3 cardexpdate mb-0">
                                                            <label for="">CVC</label>
                                                            <input type="text" ref="cardCvcInput" :class="{ 'error': cardErrors.cardCvc}" placeholder="CVC" v-model="form.cardCVV" class="form-control" v-cardformat:formatCardCVC>
                                                            <div v-if="cardErrors.cardCvc" class="error">
                                                                <small>{{ cardErrors.cardCvc }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <label>&nbsp;</label>
                                                            <button class="btn btn-default w-100 py-0" id="save_card_details" @click="saveCardDetails">Save</button>
                                                        </div>

                                                    </div>


                                                <!-- <div class="form-group">
                                                    <button class="btn btn-success" @click="checkCard">Check Card</button>
                                                </div> -->
                                                </div>
                                                </div>
                                            </transition>

                                        </div>

                                </div>
                            </div>


                         <transition name="popinout">
                            <!-- Step 1 : Input data -->
                            <div class="panel-col-2 col" :class="{'d-none':process_step!=1}">

                                <div class="panel">
                                    <h2 class="subtitle">Order Details</h2>
                                    <div class="row border-bottom m-0">
                                        <div class="col p-0"><h2 class="slot-text">Delivery details</h2></div>
                                        <div class="col p-0 justify-content-end d-flex rec_switch" v-if="showRecurring"><switch-btn v-model="isRecurring" label-left="Recurring"></switch-btn></div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="" :class="{'col-4':isRecurring,'col-12':!isRecurring}">
                                            <div class="row">
                                                <div class="col">
                                                    <!--
                                            <select-options classnames="deliverymethod" v-model="deliverymethod"  placeholder="Choose a method" :options="deliverymethods" name="delivery_method" hint=""  label="Delivery method"></select-options>
                                            -->
                                            <label >Choose a delivery method </label>
                                            <div id="select_div" v-if="!isRecurring">
                                                
                                                <select id="method_select" v-model="deliverymethod" >
                                                    <option value="" disabled hidden>Select </option>
                                                    <option v-for="(method,index) in deliverymethods" :value="method.value" :disabled="method.disabled" style="font-family: Verdana;">
                                                        {{method.display}}
                                                    </option>
                                                </select>
                                               
                                            </div>

                                            <!--
                                                :disabled="deliverymethod_disabled"
                                            -->
                                                </div>
                                            </div>
                                            <transition name="popinout">
                                            <div class="row mt-3" v-if="deliverymethod=='in_store_collection'">
                                                <div class="col-12 mb-4">
                                                    <div class="row">
                                                        <div class="col">
                                                            <select-options v-model="store_name" id="isc_store_name" :disabled="store_name_disabled" :options="storenames" label="Choose a store" placeholder="Choose store" classnames="storenames"></select-options>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <!--<date-picker v-model="isc_dropoff" name="isc_dropoff" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Drop off" :disabledToDate="yesterday"></date-picker>
                                                    -->
                                                    <label for="isc_dropoff">Drop off date</label>
                                                    <input type="text" class="form-control" :value="getCurDateTime('date')" readonly id="isc_dropoff"/>
                                                </div>
                                                <div class="col-3">
                                                    <label for="isc_dropoff_time">Drop off time</label>
                                                    <input type="text" class="form-control" :value="getCurDateTime('time')" readonly id="isc_dropoff_time"/>

                                                    <!--
                                                    <time-slot-picker v-model="isc_dropoff_timeslot"   name="isc_dropoff_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>
                                                    -->
                                                </div>

                                                <div class="col-3">
                                                    <date-picker v-model="isc_pickup" name="isc_pickup" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Collection date" :disabledToDate="getCurDateTime('datedb')" @loadtranche="checkStorePickup" :disabledSunday="true" :disabled="isc_pickup_disabled" :disabledDates="holidays"></date-picker>
                                                </div>
                                                <div class="col-3">
                                                    <time-slot-picker v-model="isc_pickup_timeslot" :disabled="isc_pickup_timeslot_disabled"   name="isc_pickup_timeslot" :available-slots="[11]"  label="Collection Time" :isStore="true"></time-slot-picker>
                                                </div>
                                            </div>
                                            </transition>


                                            <transition name="popinout">
                                                <div class="row mt-3" v-if="deliverymethod=='delivery_only'">
                                                    <div class="col-3">
                                                        <!--
                                                        <date-picker v-model="do_dropoff" name="do_dropoff" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Drop off" :disabledToDate="yesterday"></date-picker>
                                                        -->
                                                        <label for="do_dropoff">date</label>
                                                        <input type="text" class="form-control" :value="getCurDateTime('date')" readonly id="do_dropoff"/>
                                                    </div>
                                                    <div class="col-3">
                                                        <!--
                                                        <time-slot-picker v-model="do_dropoff_timeslot"   name="do_dropoff_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>
                                                        -->
                                                        <label for="do_dropoff_time">time</label>
                                                        <input type="text" class="form-control" :value="getCurDateTime('time')" readonly id="do_dropoff_time"/>
                                                    </div>

                                                    <div class="col-3">
                                                        <date-picker :disabled="do_delivery_disabled" v-model="do_delivery" name="do_delivery" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Delivery" :disabledToDate="addRemoveDays('add',0,cur_date)" @loadtranche="loadtranche('do_delivery')" ref="do_delivery_datepicker" :disabled-sunday="true" :available-days="available_days" :disabledDates="holidays"></date-picker>
                                                    </div>
                                                    <div class="col-3">
                                                        <time-slot-picker v-model="do_delivery_timeslot"   name="do_delivery_timeslot" :available-slots="do_delivery_tranche"  label="time"></time-slot-picker>
                                                    </div>
                                                </div>
                                            </transition>

                                            <transition name="popinout">
                                                <div class="row mt-3" v-if="deliverymethod=='home_delivery'&&!isRecurring">
                                                    <div class="col-3">
                                                        <date-picker v-model="hd_pickup" name="hd_pickup" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Pick up date" :disabledToDate="yesterday" :disabledFromDate="hd_delivery"  @loadtranche="loadtranche('hd_pickup')" ref="hd_pickup_datepicker" :disabledSunday="true" :available-days="available_days" :disabledDates="holidays"></date-picker>
                                                    </div>
                                                    <div class="col-3">
                                                        <time-slot-picker v-model="hd_pickup_timeslot"   name="hd_pickup_timeslot" :available-slots="hd_pickup_tranche"  label="Pick up time"></time-slot-picker>
                                                    </div>

                                                    <div class="col-3">
                                                        <date-picker v-model="hd_delivery" name="hd_delivery" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Delivery date" :disabledToDate="addRemoveDays('add',3,hd_pickup)"   @loadtranche="loadtranche('hd_delivery')" ref="hd_delivery_datepicker" :disabledSunday="true" :available-days="available_days" :disabledDates="holidays"></date-picker>
                                                    </div>
                                                    <div class="col-3">
                                                        <time-slot-picker v-model="hd_delivery_timeslot"   name="hd_delivery_timeslot" :available-slots="hd_delivery_tranche"  label="Delivery time"></time-slot-picker>
                                                    </div>
                                                </div>
                                            </transition>

                                            <transition name="popinout">
                                                <div class="row mt-3" v-if="deliverymethod=='shipping'">
                                                    <div class="col-3">
                                                        <!--<date-picker v-model="shp_received" name="shp_received" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Received" :disabledToDate="yesterday"></date-picker>-->
                                                        <label for="shp_received">Received</label>
                                                        <input type="text" class="form-control" :value="getCurDateTime('date')" readonly id="shp_received"/>
                                                    </div>
                                                    <div class="col-3">
                                                        <!--<time-slot-picker v-model="shp_received_timeslot"   name="shp_received_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>-->
                                                        <label for="shp_received_time"></label>
                                                        <input type="text" class="form-control" :value="getCurDateTime('time')" readonly id="shp_received_time"/>
                                                    </div>

                                                    <div class="col-3"><date-picker v-model="shp_delivery" name="shp_delivery" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Delivery" :disabledToDate="shp_min_date"></date-picker>
                                                    </div>

                                                </div>
                                            </transition>
                                        </div>
                                        <div :class="{'col-8':isRecurring}" v-if="isRecurring">
                                            <transition name="popinout">
                                                <div>
                                                    <recurring-form v-model="recurring_data" :postcode="(cur_cust?cur_cust.postcode:'')" ref="recur_form" :cust="cur_cust?cur_cust:null"></recurring-form>
                                                </div>
                                            </transition>
                                        </div>
                                    </div>

                                    <div class="row border-bottom m-0" v-if="deliverymethod !='' &&deliverymethod !='in_store_collection'">
                                        <div class="col mt-4 p-0"><h2 class="slot-text">Address details</h2></div>
                                    </div>
                                    <div class="row mt-3" v-if="!isRecurring && (deliverymethod=='shipping' || deliverymethod=='delivery_only' || deliverymethod=='home_delivery')">

                                        <div class="col-6 body_medium mt-3">
                                            <h3 class="body_medium">Delivery Address</h3>
                                            <div class="row mb-3">
                                                <div class="col-12 form-group">
                                                    <input type="text" v-model="shp_address1" class="form-control" placeholder="Address1"/>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 form-group">
                                                    <input type="text" class="form-control" placeholder="Postcode" v-model="shp_postcode"/>
                                                </div>
                                                <div class="col-6 form-group  pr-0">
                                                    <input type="text" class="form-control" placeholder="Town" v-model="shp_town"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="deliverymethod=='shipping'" class="col-2"></div>
                                         <div class="col-4 mt-3" v-if="deliverymethod=='shipping'">
                                            <h3 class="body_medium">Delivery partner</h3>
                                            <div class="row mx-0">
                                                 <select-options  v-model="shipping_partner_id" placeholder="Choose a partner" :options="shipping_partners" name="shipping_partner_id" hint=""  label=""></select-options>
                                            </div>
                                         </div>
                                         <div class="col-6 mt-3" v-if="deliverymethod=='delivery_only' || deliverymethod=='home_delivery'">
                                              <h3 class="body_medium">&nbsp;</h3>
                                             <div class="row mx-0 form-group mb-2">
                                                 <textarea class="form-control" v-model="customer_instructions" rows="3" :readonly="isRecurring"></textarea>
                                            </div>
                                            <div class="row mx-0 form-group mb-4 align-items-center">
                                                <input type="checkbox" id="save_instruction_check" class="float-left mr-3" v-model="save_instruction_check"/>
                                                <span class="col px-0">Save instructions for next time</span>
                                            </div>

                                            <div class="row mx-0 form-group">
                                                <h3 class="body_medium">Alternate contact</h3>
                                                 <input type="text" class="form-control" v-model="alternate_contact"/>
                                            </div>
                                         </div>
                                    </div>
                                    <div class="row mt-3" v-else-if="isRecurring && deliverymethod !='in_store_collection'">
                                         <h3 class="body_medium">Delivery Address</h3>
                                        <div class="body_medium mt-3" v-if="cur_cust">
                                            <span v-if="cur_cust.address1 && cur_cust.address1!=''">{{cur_cust.address1}}</span>
                                            <br/>
                                            <span v-if="cur_cust.postcode && cur_cust.postcode!=''">{{cur_cust.postcode}}</span><span v-if="cur_cust.Town && cur_cust.Town!=''">, {{cur_cust.Town}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            </transition>
                            <!-- Step 2 - SUMMARY -->
                             <transition name="popinout">

                                <div class="panel-col-2 col" v-if="process_step==2">

                                    <div class="panel">
                                        <div class="row">
                                            <div class="col">
                                                <h2 class="subtitle d-table" id="step_2_title">Order Details <a class="ml-3" id="edit_order_link" @click="changeStep(1)">Edit</a></h2>

                                                <div id="order_exp_type" class="float-left d-table" v-if="deliverymethod=='in_store_collection' && order_express!=0">
                                                    <span v-if="order_express==1">Express 24</span>
                                                    <span v-if="order_express==6">Express 48</span>
                                                </div>

                                            </div>
                                            <div class="col" v-if="deliverymethod=='in_store_collection'">
                                                <span class="float-right each-summary-delivery-type">{{CapitalizeString(store_name)}}</span>
                                                <img class="float-right each-summary-icon" src="/images/picto_store.svg" />
                                            </div>


                                            <div class="col" v-if="deliverymethod=='delivery_only' || deliverymethod=='home_delivery'">
                                                <span class="float-right each-summary-delivery-type">Delivery<span v-if="deliverymethod=='delivery_only'"> only</span></span>
                                                <img class="float-right each-summary-icon" src="/images/truck.svg" />
                                            </div>
                                            <div class="col" v-if="deliverymethod=='shipping'">
                                                <span class="float-right each-summary-delivery-type">Shipping</span>
                                                <img class="float-right each-summary-icon" src="/images/homepage_icon.svg" />
                                            </div>
                                        </div>
                                        <div class="row border-bottom m-0 mb-3">
                                            <div class="col p-0"><h2 class="slot-text">Slot</h2></div>
                                        </div>

                                        <div class="row" v-if="!isRecurring">
                                            <div class="col-6">
                                                <span class="medium-grey each-timeslot-label body_medium d-block mb-2">
                                                    <span v-if="['in_store_collection','delivery_only'].includes(deliverymethod)">Drop off</span>
                                                    <span v-if="deliverymethod=='home_delivery'">Pick up time</span>
                                                    <span v-if="deliverymethod=='shipping'">Received</span>
                                                </span>
                                                <div class="row">
                                                    <div class="col-2">
                                                        <img src="/images/calendar_icon.svg" class="each-timeslot-icon" v-if="!isRecurring && deliverymethod !='' && deliverymethod!='shipping'"/>
                                                        <img src="/images/express.svg" class="each-timeslot-icon" v-if="deliverymethod=='shipping'"/>
                                                    </div>
                                                    <div class="col-10 pl-0">
                                                        <div class="body_medium dark-grey"  v-if="deliverymethod=='in_store_collection'">
                                                            <span>{{formatDate(cur_date)}}</span><br/>
                                                            <span>{{formatTime(cur_date)}}</span>
                                                        </div>
                                                        <div class="body_medium dark-grey"  v-if="deliverymethod=='delivery_only'">
                                                            <span>{{formatDate(cur_date)}}</span><br/>
                                                            <span>{{formatTime(cur_date)}}</span>
                                                        </div>
                                                         <div class="body_medium dark-grey"  v-if="deliverymethod=='home_delivery'">
                                                            <span>{{formatDate(hd_pickup)}}</span><br/>
                                                            <span>{{all_timeslots[hd_pickup_timeslot]}}</span>
                                                        </div>
                                                        <div class="body_medium dark-grey"  v-if="deliverymethod=='shipping'" id="shp_received_txt">
                                                            <span>{{formatDate(cur_date)}}</span><br/>
                                                            <span>{{formatTime(cur_date)}}</span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <span class="medium-grey each-timeslot-label body_medium d-block mb-2">
                                                    <span  v-if="deliverymethod=='in_store_collection'">Pickup</span>
                                                    <span  v-if="deliverymethod=='delivery_only'">Delivery slot</span>
                                                    <span  v-if="deliverymethod=='home_delivery'">Delivery time</span>
                                                    <span  v-if="deliverymethod=='shipping'">Delivery time</span>
                                                </span>
                                                <div class="row" :class="{'align-items-center':deliverymethod=='shipping'}">
                                                    <div class="col-2">
                                                        <img src="/images/calendar_icon.svg" class="each-timeslot-icon"/>
                                                    </div>
                                                    <div class="col-10 pl-0">
                                                        <div class="body_medium dark-grey"  v-if="deliverymethod=='in_store_collection'">
                                                            <span>{{formatDate(isc_pickup)}}</span><br/>
                                                            <span>{{all_timeslots[isc_pickup_timeslot]}}</span>
                                                        </div>
                                                        <div class="body_medium dark-grey"  v-if="deliverymethod=='delivery_only'">
                                                            <span>{{formatDate(do_delivery)}}</span><br/>
                                                            <span>{{all_timeslots[do_delivery_timeslot]}}</span>
                                                        </div>
                                                        <div class="body_medium dark-grey"  v-if="deliverymethod=='home_delivery'">
                                                            <span>{{formatDate(hd_delivery)}}</span><br/>
                                                            <span>{{all_timeslots[hd_delivery_timeslot]}}</span>
                                                        </div>
                                                        <div class="body_medium dark-grey"  v-if="deliverymethod=='shipping'">
                                                            <span>{{formatDate(shp_delivery)}}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div v-if="isRecurring" class="row">
                                            <div class="col-6 mb-4" v-for="(a,i) in recurring_data">
                                                <div class="row">
                                                    <div class="col-12 medium-grey each-timeslot-label body_medium d-block mb-1">
                                                        Pickup and delivery slot {{i+1}}
                                                    </div>
                                                    <div class="col-2">
                                                            <img src="/images/recurrence_icon.svg" class="each-timeslot-icon"/>
                                                    </div>
                                                    <div class="col-10 body_regular dark-grey">
                                                        <span>{{recurring_days[a.value]}}</span><br/>
                                                        <span>{{all_timeslots[a.slot]}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                         <div class="row border-bottom mx-0 mt-5 mb-3" v-if="isRecurring || (deliverymethod!='' && deliverymethod!='in_store_collection')">
                                            <div class="col p-0"><h2 class="slot-text">Details</h2></div>
                                        </div>
                                        <div class="row mt-3" v-if="isRecurring || (deliverymethod!='' && deliverymethod!='in_store_collection')">
                                            <div class="col-6">
                                                <h4 class="body_regular medium-grey">Delivery address</h4>
                                                <div v-if="cur_cust" class="body_regular dark-grey">
                                                    <span v-if="cur_cust.address1!=''">{{cur_cust.address1}}<br/></span>
                                                    <span v-if="cur_cust.postcode!=''">{{cur_cust.postcode}}</span>
                                                </div>
                                            </div>
                                            <div class="col-6"  v-if="isRecurring || ['home_delivery','delivery_only'].includes(deliverymethod)">
                                                <h4 class="body_regular medium-grey mb-3">Delivery instructions</h4>
                                                <div class="row mx-0 form-group">
                                                    <textarea class="form-control" readonly v-model="customer_instructions"></textarea>
                                                </div>


                                                <div class="row mx-0 mt-4 form-group" v-if="deliverymethod=='delivery_only'">
                                                    <h4 class="body_regular medium-grey mb-3">Alternate contact</h4>
                                                    <textarea class="form-control" readonly v-model="alternate_contact"></textarea>
                                                </div>

                                            </div>

                                            <div class="col-6"  v-if="deliverymethod=='shipping'">
                                                <h4 class="body_regular medium-grey mb-2">Delivery partner</h4>
                                                <p class="body_regular dark-grey">{{getDisplayFromSelectArray(shipping_partners,shipping_partner_id)}}</p>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </transition>


                            <div class="row mx-0 mt-5 mb-4 justify-content-end align-items-center">
                                <div class="d-none">{{cur_cust}}</div>
                                <div class="col-2">
                                    <a href="javascript:void(0)" id="cancel_new_order" @click="cancel">Cancel</a>
                                </div>
                                <div class="col-2 px-0">
                                    <button class="btn btn-grey w-100" @click="validateDetails" :disabled="proceedtodetailing_disabled">Create Order</button>
                                </div>
                            </div>

                            <modal ref="orderConfirmModal" @closeModal='reloadPage'>
                                <template #bheader>
                                    <div class="bmodal-header py-5 d-flex w-100 justify-content-center align-items-center">
                                        <div id="order_created_txt">Order n<sup>o</sup> {{neworder_id}} created</div>
                                        <img src="/images/check.svg"/>
                                    </div>
                                </template>
                                <template #bcontent>
                                    <div class="row mx-0 bmodal-content">
                                        <div class="col-12 py-5 text-center">Do you want to proceed<br/>to detailing?</div>
                                    </div>
                                </template>
                                <template #mbuttons>
                                    <div class="row mx-0 justify-content-center mt-3 pb-4">
                                        <div class="col-5">
                                            <button class="btn btn-outline-danger w-100" @click="$router.push({name:'LandingPage'})">NO, Booking only</button>
                                        </div>
                                        <div class="col-5">
                                            <button class="btn btn-outline-success w-100" @click="updateOrderAndDetail">YES, Detail order</button>
                                        </div>
                                    </div>
                                </template>
                            </modal>

                        </div>
                    </div>

                    </div>

                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import BreadCrumb from '../layout/BreadCrumb'
    import SideBar from '../layout/SideBar'
    import {featureUnavailable} from "../helpers/helpers";
    import SelectOptions from '../miscellaneous/SelectOptions'
    import SwitchBtn from '../miscellaneous/SwitchBtn'
    import {useStore} from 'vuex';
    import DatePicker from '../miscellaneous/DatePicker';
    import TimeSlotPicker from '../miscellaneous/TimeSlotPicker';
    import CustomerDetailsPanel from './CustomerDetailsPanel';
    import Modal from '../miscellaneous/Modal.vue';

    import { useRouter, useRoute } from 'vue-router';

    import {ref,onMounted,nextTick,computed,watch,inject} from 'vue';

    import {
        NEWORDER_GET_CUSTOMER,
        NEWORDER_CUR_CUSTOMER,
        NEWORDER_MODULE,
        NEWORDER_PRELOAD_FORM,
        NEWORDER_PRELOAD_FORM_GET,
        NEWORDER_PRELOAD_ORDER_GET,
        SHIPPING_MODULE,
        GET_PARTNERS,
        SHIPPING_LOAD_LIST,
        TOASTER_MODULE,
        TOASTER_ADD_TOAST,
        TOASTER_MESSAGE,
        TOASTER_GET_ALL,
        TOASTER_REMOVE_TOAST,
        NEWORDER_GET_ALL_TIMESLOTS,
        LOADER_MODULE,
        DISPLAY_LOADER,
        HIDE_LOADER,
    } from "../../store/types/types";
import RecurringForm from '../miscellaneous/RecurringForm.vue';
import axios from 'axios';
    export default {
        name: "NewOrder",
        components:{BreadCrumb,SideBar,SelectOptions,SwitchBtn,DatePicker,TimeSlotPicker,CustomerDetailsPanel,RecurringForm,Modal},
        setup(props,context){
            const router = useRouter();

            const showcontainer=ref(false);
            const deliverymethod =ref('');

            //isc : in store collection
            //const isc_dropoff =ref('');
            //const isc_dropoff_timeslot=ref(0);
            const isc_pickup=ref('');
            const isc_pickup_disabled=ref(false);
            const isc_pickup_timeslot=ref(0);
            const isc_pickup_timeslot_disabled=ref(false);
            const store_name_disabled=ref(false);
            const isc_pickup_tranche=ref([]);
            const proceedtodetailing_disabled=ref(false);
            const no_main_booking=ref(false);

            //do : delivery only
            //const do_dropoff =ref('');
            //const do_dropoff_timeslot=ref(0);
            const do_delivery=ref('');
            const do_delivery_disabled=ref(false);
            const do_delivery_timeslot=ref(0);
            const do_delivery_tranche = ref([]);
            const do_delivery_datepicker = ref();

            //hd : home delivery
            const hd_pickup =ref('');
            const hd_pickup_timeslot=ref(0);
            const hd_pickup_tranche = ref([]);
            const hd_pickup_datepicker = ref();
            const hd_delivery=ref('');
            const hd_delivery_timeslot=ref(0);
            const hd_delivery_tranche = ref([]);
            const hd_delivery_datepicker = ref();


            //shp : shipping
            //const shp_received =ref('');
            //const shp_received_timeslot=ref(0);
            const shp_delivery=ref('');
            const shipping_partner_id = ref('');
            const shp_min_date = ref('');

            //General address
            const shp_address1=ref('');
            const shp_postcode = ref('');
            const shp_town = ref('');


            const deliverymethod_disabled=ref(false);
            const isRecurring=ref(false);
            const recurring_date = ref([]);

            const CustomerID=ref('');
            const cur_customer = ref({});


            const paths=ref([{name:'Order',route:'LandingPage'},{name:'New Order',route:'NewOrder'}]);
            const store=useStore();

            const alternate_contact = ref('');
            const customer_instructions = ref('');
            const save_instruction_check = ref(false);
            const cust_type_delivery = ref('');

            const recurring_data = ref([]);

            const yesterday = ref('');

            const cur_date = ref('');

            const order_express = ref('');
            const available_days = ref([]);
            const available_cust_slots = ref([]);


            const all_timeslots_def = store.getters[`${NEWORDER_MODULE}${NEWORDER_GET_ALL_TIMESLOTS}`];

            const all_timeslots = ref([]);
            const all_timeslots_arr = [];

            all_timeslots_def.forEach(function(v,i){
                all_timeslots_arr[v.value] = v.display
            });

            all_timeslots.value = all_timeslots_arr;

            const recur_form = ref();

            const neworder_id = ref('');

            const card_details = ref({});

            const d = new Date();
            //console.log(d.getHours());

            d.setDate(d.getDate() - 1);

            let dd = String(d.getDate()). padStart(2, '0');
            let mm = String(d.getMonth() + 1). padStart(2, '0'); //January is 0!
            var yyyy = d.getFullYear();

            cur_date.value = d.getDate();

            //New ORDER object
            const new_order_obj = ref({});

            yesterday.value = yyyy+'-'+mm+'-'+dd;

            store.dispatch(`${NEWORDER_MODULE}${NEWORDER_PRELOAD_FORM}`);
            onMounted(()=>{
                nextTick(()=>{
                    showcontainer.value=true;
                });

            });
            const deliverymethods=ref([
                {
                    value:'in_store_collection',
                    display:'Collect from Store',
                    disabled: false
                },
                {
                    value:'home_delivery',
                    display:'Pickup & Delivery',
                    disabled: deliverymethod_disabled,
                },
                {
                    value:'delivery_only',
                    display:'Delivery Only',
                    disabled: deliverymethod_disabled,
                },
                {
                    value:'shipping',
                    display:'Shipping',
                    disabled: deliverymethod_disabled,
                },
            ]);


            const storenames = ref([]);
            const store_name = ref('');
            let stores = ['CHELSEA','MARYLEBONE','NOTTING HILL','SOUTH KEN'];

            stores.forEach(function(v,i){
                let key = {};

                key['value'] = v;
                key['display'] =  CapitalizeString(v);
                storenames.value.push(key);
            });


            function firstLetterToUppercase(str2){
                let str = str2.toString().toLowerCase();
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function CapitalizeString(str2){
                const str = str2.toString().toLowerCase().split(" ");
                    for (var i = 0; i < str.length; i++) {
                        str[i] = str[i].charAt(0).toUpperCase() + str[i].slice(1);
                    }   
                return str.join(" ");
            }
            const showRecurring = ref(true);
            const process_step = ref(1);


            watch(()=>deliverymethod.value,(val)=>{
                let updated_date = new Date();

                cur_date.value = updated_date;

                if(val=='home_delivery' || val==''){
                    showRecurring.value=true;
                }else{
                    showRecurring.value=false;
                }

                if(val=='shipping'){
                   shp_min_date.value = getCurDateTime('datedb');
                }

                if(val=='in_store_collection'){
                    let today = new Date();

                    let date_plus_3_stamp = today.setDate(today.getDate() + 3);

                    let date_plus_3 = new Date(date_plus_3_stamp);

                    let date_plus_3_day = date_plus_3.getDay();

                    if(date_plus_3_day==0){
                        date_plus_3_stamp =  date_plus_3.setDate(today.getDate() + 1);
                        date_plus_3 = new Date(date_plus_3_stamp);
                    }


                    let collectionDate= date_plus_3.toISOString().slice(0, 10);

                    let days = 0;

                    let checkholiday=true;
                    while(checkholiday){
                        let holiday=holidays.value.filter(obj=>obj==collectionDate);

                        if(holiday.length>0){
                                let Today = new Date(collectionDate);
                                days=days+1;
                                collectionDate = Today.setDate(Today.getDate() + days);
                                collectionDate=new Date(collectionDate).toISOString().slice(0, 10);
                        }else{
                            checkholiday=false;
                        }
                    }

                    isc_pickup.value = collectionDate;

                    isc_pickup_timeslot.value = 11;
                }
            });


            watch(() =>isRecurring.value, (current_val, previous_val) => {
                    if(current_val===true) {
                        deliverymethod.value = '';
                        deliverymethod_disabled.value=true;
                    }else {
                        deliverymethod_disabled.value=false;
                        deliverymethod.value = '';
                    }
                });

            /*const FORM=computed(()=>store.getters[`${NEWORDER_MODULE}${NEWORDER_PRELOAD_FORM_GET}`]);
            watch(() =>_.cloneDeep(FORM.value), (current_val, previous_val) => {
                current_val.TypeDeliveries.forEach(item=>{
                    deliverymethods.value.push({value:item,display:item});
                })
            })*/
            const order=computed(()=>store.getters[`${NEWORDER_MODULE}${NEWORDER_PRELOAD_ORDER_GET}`]);


           //To remove
            function proceedToDetailling() {
                //router.push('/order-content/57907');
            }



            const cur_cust = computed(()=>{
                const current_customer = store.getters[`${NEWORDER_MODULE}${NEWORDER_CUR_CUSTOMER}`];


                if(current_customer){

                    deliverymethod.value='';
                    store_name.value='';
                    isc_pickup_timeslot.value=0;
                    isc_pickup_timeslot_disabled.value=false;
                    store_name_disabled.value=false;
                    deliverymethod_disabled.value=false;
                    isc_pickup.value='';
                    isc_pickup_disabled.value=false;
                    do_delivery.value='';
                    do_delivery_disabled.value=false;
                    no_main_booking.value=false;
                    proceedtodetailing_disabled.value=false;

                    shp_address1.value = current_customer.address1;
                    shp_postcode.value = current_customer.postcode;
                    shp_town.value = current_customer.Town;
                    CustomerID.value = current_customer.CustomerID;
                    //customer_instructions.value = current_customer.commentDelivery;
                    cust_type_delivery.value = (current_customer.TypeDelivery=='DELIVERY'?'Atelier':current_customer.TypeDelivery);

                    card_details.value = current_customer.card_details;

                    if(current_customer.delivery_preference){
                        customer_instructions.value = current_customer.delivery_preference.OtherInstruction;
                        alternate_contact.value = current_customer.delivery_preference.PhoneNumber;
                    }

                    //console.log(current_customer.main_account.TypeDelivery);

                    if(typeof current_customer.main_account!="undefined"){
                        if(current_customer.main_account.TypeDelivery!="DELIVERY"){
                            deliverymethod.value='in_store_collection';
                            store_name.value=current_customer.main_account.TypeDelivery;
                            isc_pickup_timeslot.value=11;
                            isc_pickup_timeslot_disabled.value=true;
                            store_name_disabled.value=true;

                            deliverymethod_disabled.value=true;
                            let Today = new Date();
                            let days=3;
                            let collectionDate = Today.setDate(Today.getDate() + days);
                            collectionDate=new Date(collectionDate).toISOString().slice(0, 10);
                            let checkholiday=true;
                            while(checkholiday){
                               let holiday= current_customer.holidays.filter(obj=>obj.date==collectionDate);
                               if(holiday.length>0){
                                     Today = new Date();
                                     days=days+1;
                                     collectionDate = Today.setDate(Today.getDate() + days);
                                     collectionDate=new Date(collectionDate).toISOString().slice(0, 10);
                               }else{
                                   checkholiday=false;
                               }
                            }
                            isc_pickup.value=collectionDate;
                            isc_pickup_disabled.value=true;

                        }else{
                            if(current_customer.main_account.recent_deliveryask==null){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                    message:"No booking on mail account",
                                    ttl:5,
                                    type:'danger'
                                });
                            }

                             deliverymethod.value='delivery_only';
                              deliverymethod_disabled.value=true;
                              do_delivery_disabled.value=true;
                              if(current_customer.main_account.recent_deliveryask!=null){
                              do_delivery.value=current_customer.main_account.recent_deliveryask.date;
                                do_delivery_timeslot.value = current_customer.main_account.recent_deliveryask.slot;

                              }else{
                                  //no_main_booking.value=true;

                              }

                        }

                    }
                    //store.dispatch(`${NEWORDER_MODULE}${NEW_ORDER_SET_TRANCHE_POSTCODE}`,current_customer.postcode);
                    available_days.value = current_customer.available_days;
                    available_cust_slots.value = current_customer.available_slots;


                    if(current_customer.current_user.store==1 && typeof current_customer.main_account=="undefined"){
                        deliverymethod.value = 'home_delivery';
                    }
                    if(current_customer.current_user.store >1){
                        deliverymethod.value = 'in_store_collection';

                        //2: MB / 3:CHELSEA / 4:SOUTH KEN / 5:NOTTING HILL
                        let stores = [];
                        stores[2] = 'MARYLEBONE';
                        stores[3] = 'CHELSEA';
                        stores[4] = 'SOUTH KEN';
                        stores[5] = 'NOTTING HILL';

                        if(stores[current_customer.current_user.store]){
                            store_name.value = stores[current_customer.current_user.store];
                        }
                        isc_pickup_timeslot.value = 11;
                    }
                }

                return current_customer;
            });

            const holidays = ref([]);
            function getHolidays(){
                axios.post('/get-holidays',{})
                    .then((res)=>{
                        holidays.value = res.data.dates;
                    }).catch((err)=>{

                    }).finally(()=>{

                    });
            }

            getHolidays();

            watch(()=>no_main_booking.value,(current_val,previous_val)=>{
                if(current_val==true){
                    proceedtodetailing_disabled.value=true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                                            {
                                                message: 'No booking on main account.',
                                                ttl: 6,
                                                type: 'danger'
                                            });
                }
            });
            watch(() =>cust_type_delivery.value, (current_val, previous_val) => {
                //store_name.value = current_val.toString().toUpperCase();
            });

/*
            watch(()=>_.cloneDeep(cur_cust.postcode),(current_val,previous_val)=>{
                console.log('previous',previous_val);
                console.log('current',current_val);
            });
*/

            store.dispatch(`${SHIPPING_MODULE}${SHIPPING_LOAD_LIST}`);
            const shipping_partners = computed(()=>store.getters[`${SHIPPING_MODULE}${GET_PARTNERS}`]);

            function validateDetails(){

               if(process_step.value==1){


                   let err = false;
                   let err_txt = [];



                   if(CustomerID.value==''){
                       err = true;
                       err_txt.push('No customer selected');
                   }
                   else if(isRecurring.value){
                       if(recurring_data.value.length==0){
                           err = true;
                           err_txt.push('Recurring booking empty');
                       }
                   }
                   else if(!isRecurring.value && deliverymethod.value==''){
                       err = true;
                       err_txt.push('Please choose a delivery method');

                   }else{

                       let err_method = validateByDeliveryMethod(deliverymethod.value);

                        err_txt = err_txt.concat(err_method);


                   }

                if(!card_details.value || card_details.value.id=='' || typeof(card_details.value.id)=='undefined'){
                   /*
                   if(paymentMethod.value==''){
                       err_txt.push("Payment method not set");
                       err = true;
                   }*/

                   if(paymentMethod.value=='Credit Card'){
                       let err_cc = validateCardDetails();
                       err_txt = err_txt.concat(err_cc);
                   }
                }

                   //console.log(err_txt);

                   if(err_txt.length > 0){
                       const toasts = store.getters[`${TOASTER_MODULE}${TOASTER_GET_ALL}`];
                       if(toasts.length > 0){
                           toasts.forEach(function(v,i){
                               //console.log(v.id);
                               store.commit(`${TOASTER_MODULE}${TOASTER_REMOVE_TOAST}`,v.id);
                           });

                       }

                       err_txt.forEach(function(v,i){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                            {
                                message: v,
                                ttl: 3,
                                type: 'danger'
                            });
                       })

                   }else{



                    const new_order = {};
                    new_order.CustomerID = CustomerID.value;
                    new_order.AddressID = cur_cust.value.AddressID;
                    new_order.deliverymethod = deliverymethod.value;
                    new_order.address1 = shp_address1.value;
                    new_order.postcode = shp_postcode.value;
                    new_order.town = shp_town.value;
                    new_order.store_name = store_name.value;
                    new_order.payment_method = paymentMethod.value;

                    let arr = [];

                    arr['in_store_collection'] = ['isc_pickup','isc_pickup_timeslot'];

                    arr['delivery_only'] = ['do_delivery','do_delivery_timeslot','customer_instructions','alternate_contact','save_instruction_check'];

                    arr['home_delivery'] = ['hd_pickup','hd_pickup_timeslot','hd_delivery','hd_delivery_timeslot','customer_instructions','alternate_contact','save_instruction_check'];

                    arr['shipping'] = ['shp_delivery','shipping_partner_id'];



                    if(typeof(arr[deliverymethod.value])!='undefined'){
                        new_order['delivery_params'] = JSON.stringify(arr[deliverymethod.value]);

                        arr[deliverymethod.value].forEach(function(v,i){
                            const var_tmp = eval(v);
                            new_order[v] = var_tmp.value;
                        });
                    }

                    if(isRecurring.value){
                        new_order.deliverymethod = 'recurring';
                        new_order.recurring_data = JSON.stringify(recurring_data.value);
                        new_order['delivery_params'] = '';
                    }

                    new_order.dropoff_stamp = formatDateToDb(cur_date.value);

                    new_order['sub_account_cust'] = false;
                    new_order['sub_account_booking_id'] = 0;
                    new_order['sub_account_booking_type'] = '';


                    if(cur_cust.value.main_account && cur_cust.value.main_account.recent_deliveryask){
                        new_order['sub_account_cust'] = true;
                        new_order['sub_account_booking_id'] = cur_cust.value.main_account.recent_deliveryask.id;
                        new_order['sub_account_booking_type'] = cur_cust.value.main_account.recent_deliveryask.type;
                    }


                    new_order_obj.value = new_order;

                   evaluateOrderExpress(new_order);

                    process_step.value=2;

                   }
               }else if(process_step.value==2){
                   store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Creating order'], {root: true});
                   axios.post('/create-new-order',{
                       new_order:new_order_obj.value
                   }).then((res)=>{
                       if(res.data.new_order_id > 0){
                           let new_order_id = res.data.new_order_id;
                           neworder_id.value = new_order_id;
                            showConfirmModal();

                           //router.push('/order-content/'+new_order_id);
                       }
                   }).catch((err)=>{

                   }).finally(()=>{
                       store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`, {}, {root: true});
                   });

               }
            }




            function evaluateOrderExpress(order){

                let time_diff = 0;
                let order_exp = 0;



                if(order.deliverymethod=='in_store_collection'){
                    let dt_from = new Date(cur_date.value);
                    dt_from.setHours(18,0,0);

                    let cur_day_num = dt_from.getDay();
                    let minus_day = 0;

                    let isc_pickup_time = getTimeFromSlot(isc_pickup_timeslot.value);
                    new_order_obj.value['isc_pickup_time'] = isc_pickup_time;

                    let dt_to = new Date(isc_pickup.value+' '+isc_pickup_time);

                     //Dropoff is Friday and Collection is Monday or Dropoff is Saturday
                    if((cur_day_num==5 && dt_to.getDay()==1) || cur_day_num==6){
                        minus_day = 1;
                    }

                    time_diff =  (dt_to.getTime() - dt_from.getTime() - (minus_day*24*3600000)) / 3600000; //3600 * 1000 milliseconds
                }



                if(time_diff <=24){
                    order_exp = 1; //EXPRESS24
                }else if(time_diff > 24 && time_diff <= 48){
                    order_exp = 6; //EXPRESS48
                }

                order_express.value = order_exp;

                new_order_obj.value['express'] = order_exp;

            }


            function formatDateToDb(dt){
                let d = new Date(dt);
                let mm = d.getMonth()+1;
                let dformat = [ d.getFullYear(),mm.toString().padStart(2, '0'),
               d.getDate().toString().padStart(2, '0')].join('-')+' '+
              [d.getHours().toString().padStart(2,0),
               d.getMinutes().toString().padStart(2,0),
               d.getSeconds().toString().padStart(2,0)].join(':');

               return dformat;
            }

            function getTimeFromSlot(slot){
                let slot_from_arr = [];
                let hr = 0;

                for (let i = 1; i <= 13; i+=2) {
                    hr = i+7;
                    if(i==13){
                        hr = 8; //To confirm for 8-8 slot
                    }

                    slot_from_arr[i] = hr.toString().padStart(2,'0')+':00:00';

                }

                return slot_from_arr[slot];
            }



            function validateByDeliveryMethod(val){
                let err_arr = [];
                if(val=='in_store_collection'){

                    if(isc_pickup.value==''){
                        err_arr.push('Pickup date is empty');
                    }
                    if(isc_pickup_timeslot.value==0){
                        err_arr.push('Pickup time is empty');
                    }
                    if(store_name.value==''){
                        err_arr.push('Please choose a store name');
                    }

                    if(isc_pickup.value!='' && isc_pickup_timeslot.value !=0){
                        let dt_txt = isc_pickup.value+' '+getTimeFromSlot(isc_pickup_timeslot.value);

                        let dt_pickup = new Date(dt_txt);

                        let dt_dropoff = new Date(cur_date.value);

                        let diff = (dt_pickup.getTime() - dt_dropoff.getTime())/(3600*1000);

                        if(diff < 0){
                            err_arr.push('Pickup date and time cannot be less than drop off');
                        }
                    }

                    /*
                    if(isc_dropoff.value > isc_pickup.value){
                        err_arr.push('Dropoff date is greater than pickup date');
                    }
                    */
                }

                if(val=='delivery_only'){
                    /*
                    if(do_dropoff.value==''){
                        err_arr.push('Dropoff date is empty');
                    }
                    if(do_dropoff_timeslot.value==0){
                        err_arr.push('Dropoff time is empty');
                    }
                    */
                    if(do_delivery.value==''){
                        err_arr.push('Delivery date is empty');
                    }
                    if(do_delivery_timeslot.value==0){
                        err_arr.push('Delivery time is empty');
                    }
                    /*
                    if(do_dropoff.value > do_delivery.value){
                        err_arr.push('Dropoff date is greater than delivery date');
                    }
                    */
                }

                if(val=='home_delivery'){
                    if(hd_pickup.value==''){
                        err_arr.push('Pickup date is empty');
                    }
                    if(hd_pickup_timeslot.value==0){
                        err_arr.push('Pickup time is empty');
                    }
                    if(hd_delivery.value==''){
                        err_arr.push('Delivery date is empty');
                    }
                    if(hd_delivery_timeslot.value==0){
                        err_arr.push('Delivery time is empty');
                    }
                    if(hd_pickup.value > hd_delivery.value){
                        err_arr.push('Pickup date is greater than delivery date');
                    }
                }

                if(val=='shipping'){
                    /*
                    if(shp_received.value==''){
                        err_arr.push('Shipping received date is empty');
                    }
                    if(shp_received_timeslot.value==0){
                        err_arr.push('Shipping received time is empty');
                    }
                    if(shp_received.value > shp_delivery.value){
                        err_arr.push('Shipping delivery date is greater than received date');
                    }
                    */
                    if(shp_delivery.value==''){
                        err_arr.push('Shipping delivery date is empty');
                    }
                    if(shp_address1.value==''){
                        err_arr.push('Shipping address1 is empty');
                    }
                    if(shp_postcode.value==''){
                        err_arr.push('Shipping postcode is empty');
                    }
                    if(shipping_partner_id.value==''){
                        err_arr.push('Shipping partner not selected');
                    }
                }

                if(val !='in_store_collection'){
                    if(!shp_address1.value || shp_address1.value==''){
                        err_arr.push('No address1 for this customer');
                    }
                    if(!shp_postcode.value || shp_postcode.value==''){
                        err_arr.push('No postcode for this customer');
                    }
                }

                return err_arr;
            }

            function changeStep(num){
                if(num==1){
                    let dt = new Date();
                    cur_date.value = dt;
                }

                process_step.value = num;
            }


            function setCustomerID(val){
                if(CustomerID !=''){
                     if(isRecurring.value && recur_form.value){

                        recur_form.value.returnedData([]);
                    }
                }

                CustomerID.value=val;
                if(val==''){
                    process_step.value=1;
                }
            }

            //Filters

            function formatDate(date_txt){
                let weekdays = [];
                weekdays[0] = 'Sunday';
                weekdays[1] = 'Monday';
                weekdays[2] = 'Tuesday';
                weekdays[3] = 'Wednesday';
                weekdays[4] = 'Thursday';
                weekdays[5] = 'Friday';
                weekdays[6] = 'Satuday';

                let dt = new Date(date_txt);

                let day_num =  dt.getDay();
                let dt_dd = String(dt.getDate()). padStart(2, '0');
                let dt_mm = String(dt.getMonth() + 1). padStart(2, '0');

                return weekdays[day_num]+" "+dt_dd+"/"+dt_mm;
            }


            function formatTime(date_txt){
                let dt = new Date(date_txt);

                let hh = parseInt(dt.getHours());
                let hh_am_pm = hh;

                if(hh > 12){
                    hh_am_pm = hh - 12;
                }


                let mm = dt.getMinutes().toString().padStart(2,0);

                return hh_am_pm+":"+mm+" "+(hh >= 12?"pm":"am");
                //return dt.toLocaleString('en-GB', { hour: 'numeric', minute: 'numeric', hour12: true });
            }

            function getDisplayFromSelectArray(arr,value){
                let label = "";
                arr.forEach(function(v,i){
                    if(v.value==value){
                        label = v.display;
                    }
                });

                return label;
            }

            //End filters


            let recur_day_map = [];

            recur_day_map['DeliveryMon'] = 'Monday';
            recur_day_map['DeliveryTu'] = 'Tuesday';
            recur_day_map['DeliveryWed'] = 'Wednesday';
            recur_day_map['DeliveryTh'] = 'Thursday';
            recur_day_map['DeliveryFri'] = 'Friday';
            recur_day_map['DeliverySat'] = 'Saturday';

            const recurring_days = ref([]);
            recurring_days.value = recur_day_map;


            function getCurDateTime(type){
                let cur_dt = new Date(cur_date.value);

                let dd = String(cur_dt.getDate()). padStart(2, '0');
                let mm = String(cur_dt.getMonth() + 1). padStart(2, '0');
                let yyyy = String(cur_dt.getFullYear());

                let hh = cur_dt.getTime();

               if(type=='date'){
                   return dd+'/'+mm+'/'+yyyy.substring(2);
               }

               if(type=='datedb'){
                   return yyyy+'-'+mm+'-'+dd;
               }

               if(type=='time'){
                   let hh = cur_dt.getHours();


                   return String(cur_dt.getHours()).padStart(2,0)+':'+String(cur_dt.getMinutes()).padStart(2,0)+':'+String(cur_dt.getSeconds()).padStart(2,0);
               }

            }


            //Watch dates and load tranches

            /*
            let dates_to_watch = ['do_delivery','hd_pickup','hd_delivery','shp_delivery'];


            dates_to_watch.forEach(function(v,i){
                 const var_tmp_date = eval(v);

                watch(() =>var_tmp_date.value, (current_val, previous_val) => {
                    if(current_val != previous_val && shp_postcode.value !=''){

                        store.dispatch(`${NEWORDER_MODULE}${NEW_ORDER_SET_TRANCHE_POSTCODE}`,shp_postcode.value);
                    }
                });
            });
            */

           function loadtranche(comp){
                const var_tmp_date = eval(comp);
                //console.log(var_tmp_date.value);

                let mapped_timeslot_tranche = comp+'_tranche';
                const temp_var_tranche = eval(mapped_timeslot_tranche);

                let all_tranches = [1,3,5,7,9,11];
                let comp_ref_name = comp+'_datepicker';
                const comp_ref = eval(comp_ref_name);

                if(shp_postcode.value !=''){

                    /*
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading available slots'], {root: true});

                    axios.post('/get-tranche-by-postcode',{
                        postcode: shp_postcode.value
                    }).then((response)=>{
                        let tranches = response.data.available_slots;

                        all_tranches.forEach(function(v,i){
                            if(tranches[v] && tranches[v].includes(var_tmp_date.value)){
                                if(!available_tranches.includes(v)){
                                    available_tranches.push(v);
                                }
                            }
                        });

                        //console.log(available_tranches);

                        temp_var_tranche.value = available_tranches;

                    }).catch((error)=>{
                       console.log(error);
                    }).finally(()=>{
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`, {}, {root: true});
                    });
                    */


                    let cur_date = new Date(var_tmp_date.value);
                    let day_index = cur_date.getDay();

                    let available_tranches = [];

                    if(typeof available_cust_slots.value[day_index] !='undefined'){
                        let tranches = available_cust_slots.value[day_index];

                        tranches.forEach(function(v,i){
                            available_tranches.push(parseInt(v));
                        })
                    }

                    temp_var_tranche.value = available_tranches;

                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                        message:"Available slots set",
                        ttl:3,
                        type:'success',
                    });


                }else{
                    //console.log('resetPicker called');
                    var_tmp_date.value = '';

                    comp_ref.value.resetPicker();

                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                    {
                        message: 'No postcode set',
                        ttl: 3,
                        type: 'danger'
                    });
                }

           }


           function checkStorePickup(){
               /*
               let cur_date_db = getCurDateTime('datedb');

               isc_pickup_tranche.value = [1,3,5,7,9,11,13];

               if(isc_pickup.value==cur_date_db){
                    let slot_from_arr = [];
                    let dt = new Date(cur_date.value);

                    let cur_time = parseInt(dt.getHours());

                    let hr = 0;

                    for (let i = 1; i <= 13; i+=2) {
                        hr = i+7;
                        if(i==13){
                            hr = 8; //To confirm for 8-8 slot
                        }

                        if(hr > cur_time){
                            slot_from_arr.push(i);
                        }
                    }

                    isc_pickup_tranche.value = slot_from_arr;
               }
               */
              isc_pickup_tranche.value[9];
           }


            function addRemoveDays(action,num_days,date_txt){
                const d = new Date(date_txt);

                if(action=='add'){
                    d.setDate(d.getDate() + num_days);
                }

                if(action=='remove'){
                    d.setDate(d.getDate() - num_days);
                }

                let dd = String(d.getDate()). padStart(2, '0');
                let mm = String(d.getMonth() + 1). padStart(2, '0');
                let yyyy = String(d.getFullYear());

                return yyyy+'-'+mm+'-'+dd;

            }

            const orderConfirmModal = ref();

            function showConfirmModal(){
                orderConfirmModal.value.showModal();
            }

            const reloadPage = ()=>{
                window.location.reload();
            }


            //Payment details
            const cardFormat = inject('cardFormat');
            const paymentMethod = ref("");


            const form = ref({
                cardHolderName: '',
                cardDetails: '',
                cardExpDate: '',
                cardCVV: '',
            });

            const cardErrors = ref({});
            //Validation when cardholder name changes

            let name_regex = /^[a-zA-Z ]*$/;
            watch(()=>form.value.cardHolderName,(current_value,previous_value)=>{
                if(current_value.replace(/\s/g,'')=='' || !name_regex.test(current_value)){
                    cardErrors.value.cardHolderName = "Invalid cardholder name.";
                }else{
                    delete cardErrors.value.cardHolderName;
                }
            })


              // validation when the card detail changes
            watch(()=>form.value.cardDetails,(current_value, previous_value)=>{
                if(cardFormat.validateCardNumber(current_value)){
                    delete cardErrors.value.cardNumber;
                }else{
                    cardErrors.value.cardNumber = "Invalid Credit Card Number.";
                }
            })
            // validation when the card exp changes
            watch(()=>form.value.cardExpDate,(current_value, previous_value)=>{
                if(cardFormat.validateCardExpiry(current_value)){
                    delete cardErrors.value.cardExpiry;
                }else{
                    cardErrors.value.cardExpiry = "Invalid Expiration Date.";
                }
            })
            // validation when the card exp changes
            watch(()=>form.value.cardCVV,(current_value, previous_value)=>{
                if(cardFormat.validateCardCVC(current_value)){
                    delete cardErrors.value.cardCvc;
                }else{
                    cardErrors.value.cardCvc = "Invalid CVV.";
                }
            })

            /*
            watch(()=>CustomerID.value,(current_value,previous_value)=>{
                console.log('watching customerID',current_value);
                if(current_value=='' || typeof(current_value)=='undefined'){
                    card_details.value = {};
                }
            });
            */

            function validateCardDetails(save){
                let card_err_el = document.querySelectorAll('#credit_card_div .error');
                let err = false;
                let err_txt = [];
                if(save && CustomerID.value==''){
                    err = true;
                    err_txt.push("Customer not set");
                }else if(form.value.cardHolderName.replace(/\s/g,'')=='' || form.value.cardDetails.replace(/\s/g,'')=='' || form.value.cardExpDate.replace(/\s/g,'')=='' || form.value.cardCVV.replace(/\s/g,'')==''){
                    err = true;
                    err_txt.push("Card details missing");
                }else if(card_err_el.length > 0){
                    err = true;
                    err_txt.push("Invalid card details");
                }

                return err_txt;
            }

            function saveCardDetails(){


                let card_err_el = document.querySelectorAll('#credit_card_div .error');

                let err_txt = validateCardDetails(true);

                if(err_txt.length > 0){
                    err_txt.forEach(function(v,i){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                        {
                            message: v,
                            ttl: 3,
                            type: 'danger'
                        });
                    });
                }else{
                     store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Saving card details'], {root: true});

                    form.value.CustomerID = CustomerID.value;

                    axios.post('/save-order-card-details',form.value)
                        .then((res)=>{
                            if(res.data.err!=''){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                                {
                                    message: res.data.err,
                                    ttl: 3,
                                    type: 'danger'
                                });
                            }else{
                                store.dispatch(`${NEWORDER_MODULE}${NEWORDER_GET_CUSTOMER}`,{CustomerID:CustomerID.value});
                                card_details.value = res.data.card;
                            }
                        }).catch((err)=>{
                            console.log(err);
                        }).finally(()=>{
                            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`, {}, {root: true});
                        });
                }
            }

            function updateOrderAndDetail(){
                axios.post('/update-order-to-detailing',{
                    order_id:neworder_id.value
                }).then((res)=>{
                    if(res.data.updated){
                        router.push('/order-content/'+neworder_id.value);
                    }
                }).catch((err)=>{

                }).finally(()=>{

                });

            }

            // cancel to create order
            const cancel = ()=>{
                router.push({
                    name: 'LandingPage'
                });
            }

            return {
                showcontainer,
                paths,
                featureUnavailable,
                deliverymethod,
                deliverymethod_disabled,
                deliverymethods,
                isRecurring,
                //isc_dropoff,
                //isc_dropoff_timeslot,
                isc_pickup,
                isc_pickup_disabled,
                do_delivery_disabled,
                store_name_disabled,
                proceedtodetailing_disabled,
                deliverymethod_disabled,
                isc_pickup_timeslot,
                isc_pickup_timeslot_disabled,
                isc_pickup_tranche,
                //do_dropoff,
                //do_dropoff_timeslot,
                do_delivery,
                do_delivery_timeslot,
                do_delivery_tranche,
                do_delivery_datepicker,
                hd_pickup,
                hd_pickup_timeslot,
                hd_pickup_tranche,
                hd_pickup_datepicker,
                hd_delivery,
                hd_delivery_timeslot,
                hd_delivery_tranche,
                hd_delivery_datepicker,
                //shp_received,
                //shp_received_timeslot,
                shp_delivery,
                order,
                proceedToDetailling,
                showRecurring,
                cur_cust,
                shp_address1,
                shp_postcode,
                shp_town,
                cur_customer,
                shipping_partners,
                shipping_partner_id,
                validateDetails,
                process_step,
                changeStep,
                yesterday,
                CustomerID,
                setCustomerID,
                alternate_contact,
                customer_instructions,
                save_instruction_check,
                cust_type_delivery,
                all_timeslots,
                formatDate,
                formatTime,
                getDisplayFromSelectArray,
                recurring_data,
                recurring_days,
                getCurDateTime,
                cur_date,
                loadtranche,
                order_express,
                storenames,
                store_name,
                firstLetterToUppercase,
                CapitalizeString,
                new_order_obj,
                checkStorePickup,
                shp_min_date,
                addRemoveDays,
                recur_form,
                orderConfirmModal,
                showConfirmModal,
                neworder_id,
                reloadPage,
                paymentMethod,
                form,
                cardErrors,
                saveCardDetails,
                card_details,
                updateOrderAndDetail,
                available_days,
                available_cust_slots,
                holidays,
                cancel
            }
        },
        data(){
            return {
                cardBrand: null,
            }
        },
        computed: {
            cardBrandClass(){
                return this.getBrandClass(this.cardBrand);
            }
        },
        methods:{
            getBrandClass: (brand)=>{
                let icon = '';
                brand = brand || 'unknown';
                let cardBrandToClass = {
                    'visa': 'cc-visa',
                    'maestro': 'cc-maestro',
                    'mastercard': 'cc-mastercard',
                    'dankort': 'cc-dankort',
                    'amex': 'cc-amex',
                    'discover': 'cc-discover',
                    'dinersclub': 'cc-dinersclub',
                    'unionpay': 'cc-unionpay',
                    'jcb': 'cc-jcb',
                    'unknown': 'cc-unknown',
                };
                if (cardBrandToClass[brand]) {
                    icon = cardBrandToClass[brand];
                };
                return icon;
            }
        }
    }
</script>

<style scoped>

    .panel>h2:first-child{
        line-height: 22px;
        margin-bottom: 30px;
    }
    .rec_switch .switch-wrapper{
        margin-bottom: 0.5rem;
    }
    .detailsection{
        margin-top: 30px;
    }

    .btn-grey{
        background: #E0E0E0;
    }

    .btn-grey:hover{
        background: #42A71E;
    }

    h3.body_medium{
        color:#868686;
    }

    #edit_order_link{
        font: normal 16px "Gotham Rounded";
        color:#42A71E;
        margin-left:1rem;
    }

    #edit_order_link:hover{
        cursor:pointer;
        text-decoration: none;
    }

    #save_instruction_check{
        width:20px;
        height:20px;
        margin-right: 10px;
    }

    #save_instruction_check:checked{
        background: #333;
    }

    .each-summary-icon{
        height: 24px;
        margin-right:10px;
    }

    .each-summary-delivery-type{
        font:normal 16px/24px "Gotham Rounded";
    }

    .slot-text{
        font:normal 20px "Gotham Rounded";
    }

    .subtitle{
        font: normal 22px "Gilroy";
        font-weight: 800;
        margin-bottom: 30px;
    }

    .each-timeslot-icon{
        height: 3em;
        margin-left:-8px;
    }

    #shp_received_txt{
        margin-left: -8px;
    }

    #step_2_title{
        float:left;
        margin-right:20px;
    }

    #order_exp_type{
        font:normal 12px "Gotham Rounded";
        color: #eb5757;
        border-radius: 70px;
        padding:5px 16px;
        background: rgba(245, 171, 171, 0.7);
        margin-top:3px;
    }

    .bmodal-header{
        background: rgba(66, 167, 30, 0.2);
        font: normal 22px "Gilroy Extra bold";
    }

    #order_created_txt{
        margin-right: 10px;
    }

    .bmodal-content{
        font-size:18px;
    }
    .payment_subheading,
    .payment_text{
        font:normal 16px "Gotham Rounded";
    }

    .payment_subheading{
        color:#868686;
    }

    #save_card_details{
        height:37.6px;
        background: #E0E0E0;
    }

    #save_card_details:hover{
        background: #42A71E;
    }

    .payment-icon{
        margin-right:10px;
    }

</style>
<style lang="scss">
.form-group{
    .error{
        small{
            color: #EB5757;
        }
    }
}
input.error:focus{
    outline: none;
    border-color: #EB5757;
}

#method_select{
    background-color: #fff;
    border: 0.5px solid #E0E0E0;
    box-sizing: border-box;
    border-radius: 5px;
    padding: 0 36px 0 16px;
    height: 40px;
    cursor: pointer;
    align-items: center;
    position: relative;
    font-size:16px;

    -webkit-appearance: none;
        -moz-appearance: none;
        -o-appearance: none;
        appearance: none;
        font:normal 16px "Gotham Rounded Light";
}

#method_select option{
    padding: 7px 0 11px 16px;
    height: 40px;
    font-family:  "Gotham Rounded Light";
}


#select_div{
    display: table;
    position:relative;
    font:normal 16px "Gotham Rounded";
}

#select_div::before,
#select_div::after{
    content: " ";
    height: 3px;
    display: block;
    width: 13px;
    background: #868686;
    border-radius: 10px;
    transform: rotate(40deg);
    top:18px;
    right: 22px;
    position: absolute;
    z-index:2;
}

#select_div::after{
    transform: rotate(-40deg);
    right: 13px;
}



</style>
