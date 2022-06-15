<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color p-0" v-if="showcontainer">
            <div class="d-flex align-content-stretch align-items-stretch flex-row hmax">
                <side-bar></side-bar>
                <div class="w-100">
                    <bread-crumb :paths="paths"></bread-crumb>
                    <div class="page-fluid cust-page m-0">
                        <ul class="full-nav d-flex p-0 m-0">
                            <li class="full-nav-item title border-right d-flex align-items-center justify-content-center"
                                :class="{ active: step =='account_details'}"
                                @click="selectNav('account_details')"
                            >
                                Account details
                            </li>
                            <li class="full-nav-item title border-right d-flex align-items-center justify-content-center"
                                :class="{ active: step =='payment'}"
                                @click="selectNav('payment')"
                            >
                                Payment
                            </li>
                            <li class="full-nav-item title border-right d-flex align-items-center justify-content-center"
                                :class="{ active: step =='order_management'}"
                                @click="selectNav('order_management')"
                            >
                                Order Management
                            </li>
                            <li class="full-nav-item title border-right d-flex align-items-center justify-content-center"
                                :class="{ active: step =='preferences'}"
                                @click="selectNav('preferences')"
                            >
                                Preferences
                            </li>
                            <li class="full-nav-item title d-flex align-items-center justify-content-center"
                                :class="{ active: step =='linked_account'}"
                                @click="selectNav('linked_account')"
                            >
                                Linked account(s)
                            </li>
                        </ul>
                        <transition name="list" appear v-if="step =='account_details'">
                            <div class="cust-page-content m-auto pt-5">
                                <div class="account-details-header d-flex mb-5">
                                    <div class="name-section">
                                        <h1 class="gilory-extra-bold font-36">{{form.Name}}</h1>
                                        <div class="booking-icon rounded-pill d-flex align-items-center justify-content-center">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" >
                                                <path d="M5.37598 11.5244C5.37598 15.1009 8.15655 18.0002 11.5866 18.0002C14.2001 18.0002 16.4366 16.3169 17.3532 13.9334" stroke="#42A71E" stroke-linecap="round"/>
                                                <path d="M4 12.2178L5.37613 10.7829L6.75227 12.2178" stroke="#42A71E" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M17.624 11.4756C17.624 7.89908 14.8435 4.99976 11.4134 4.99976C8.79991 4.99976 6.56343 6.68306 5.64679 9.06664" stroke="#42A71E" stroke-linecap="round"/>
                                                <path d="M19 10.7822L17.6239 12.2171L16.2477 10.7822" stroke="#42A71E" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            {{ form.booking }}
                                        </div>
                                    </div>
                                    <div class="spent-section ms-5">
                                        £{{ form.totalSpent }} <span>Total spent</span>
                                    </div>
                                </div>
                                <div class="page-section">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h3 class="title m-0">Contact details <span class="gotham-rounded-book primary-color ms-3 font-16 cursor-pointer text-decoration-underline" @click="contact_details_edit = !contact_details_edit">Edit</span></h3>
                                        <div class="type-delivery" v-if="contact_details_edit">
                                            <select-options 
                                                v-model="form.typeDelivery" 
                                                :options="[ 
                                                    { display:'Marylebone', value: 'Marylebone' }, 
                                                    { display:'Chelsea', value: 'Chelsea' },
                                                    { display:'Notting Hill', value: 'Notting Hill' },
                                                    { display:'Delivery', value: 'Delivery' },
                                                ]"
                                                :placeholder="'Delivery Type'"
                                                :name="'typeDelivery'">
                                            </select-options>
                                        </div>
                                        <div class="type-delivery gotham-rounded-medium rounded-pill py-2 border-black text-center" v-else>
                                            {{ form.typeDelivery }}
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3">
                                        <div class="w-55 d-flex justify-content-between">
                                            <div class="customer-type" v-if="contact_details_edit">
                                                <select-options 
                                                    v-model="form.customerType" 
                                                    :options="[ 
                                                        { display:'B2B', value: 'B2B' }, 
                                                        { display:'B2C', value: 'B2C' }
                                                    ]"
                                                    :label="'Customer Type'"
                                                    :name="'customerType'">
                                                </select-options>
                                            </div>
                                            <div class="from-group" v-else>
                                                <label for="">Customer type</label>
                                                <div class="customer-type py-2 bg-color rounded-3">
                                                    <span class="d-flex align-items-center justify-content-center rounded-pill b2c-icon ms-3"
                                                        :class="{ 'b2c-icon': form.customerType == 'B2C', 'b2b-icon': form.customerType == 'B2B' }">
                                                        {{ form.customerType }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="customer-type" v-if="contact_details_edit">
                                                <select-options 
                                                    v-model="form.programmeType" 
                                                    :options="[ 
                                                        { display:'Standard', value: 'Standard' }, 
                                                        { display:'Gold', value: 'VIP GOLD' },
                                                        { display:'Special Care', value: 'VIP RED' }
                                                    ]"
                                                    :label="'Programme type'"
                                                    :name="'ProgrammeType'">
                                                </select-options>
                                            </div>
                                            <div class="from-group" v-else>
                                                <label for="">Programme type</label>
                                                <div class="customer-type py-2 bg-color rounded-3">
                                                    <span class="d-flex align-items-center justify-content-center rounded-pill programme-icon ms-3">
                                                        {{ form.programmeType }}
                                                    </span>
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="w-45">
                                            <div class="form-group m-0 customer-type" >
                                                <label class="form-label d-block m-0">Kiosk number</label>
                                                <input v-if="contact_details_edit" type="text" v-model="form.kioskNumber" class="form-control custom-input" placeholder="Enter kiosk card number">
                                                <div v-else class="w-100 py-2 rounded-3 bg-color px-3">
                                                    {{ form.kioskNumber }} &nbsp;
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3">
                                        <div class="customer-contact w-55 d-flex justify-content-between">
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="first_name">{{ form.customerType == 'B2C' ? 'Contact' : 'Company representative' }}</label>
                                                <input v-if="contact_details_edit" type="text" v-model="form.firstName" class="form-control custom-input" placeholder="First name">
                                                <div v-else class="customer-type py-2 rounded-3 bg-color px-3" v-html="form.firstName == '' ? '&nbsp;' : form.firstName">
                                                </div>
                                            </div>
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="first_name">&nbsp;</label>
                                                <input v-if="contact_details_edit" type="text" v-model="form.lastName" class="form-control customer-type custom-input" placeholder="Last name">
                                                <div v-else class="customer-type py-2 rounded-3 bg-color px-3" v-html="form.lastName == '' ? '&nbsp;' : form.lastName">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="customer-phone w-45">
                                            <div>
                                                <label>Phone Number</label>
                                            </div>
                                            <div class="d-flex">
                                                <div class="phone-country-code" v-if="contact_details_edit">
                                                    <select-options 
                                                        v-model="form.phoneCountryCode" 
                                                        :options="phoneCodesSorted"
                                                        :width = "'100px'"
                                                        :name="'phoneCountryCode'">
                                                    </select-options>
                                                </div>
                                                <div v-else style="width: 80px;" class="phone-country-code py-2 rounded-3 bg-color text-center">
                                                    {{ form.phoneCountryCode }}&nbsp;
                                                </div>
                                                <div class="form-group ms-2" v-if="contact_details_edit">
                                                    <input type="text" v-model="form.phoneNumber" class="form-control custom-input">
                                                </div>
                                                <div v-else class="w-100 ms-2 py-2 rounded-3 bg-color px-3">
                                                    {{ form.phoneNumber }}&nbsp;
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="customer-email w-55 mt-3">
                                        <div class="form-group m-0">
                                            <label class="form-label d-block m-0" for="email">{{ form.customerType == 'B2C' ? "Email" : form.accountType == 'Main' ? 'Representative email address' : 'Business email address' }}</label>
                                            <input v-if="contact_details_edit" type="text" v-model="form.email" class="form-control custom-input" placeholder="Email">
                                            <div v-else class="w-100 py-2 rounded-3 bg-color px-3">
                                                {{ form.email }} &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="page-section">
                                    <h3 class="title m-0">Address <span class="gotham-rounded-book primary-color ms-3 font-16 cursor-pointer text-decoration-underline" @click="address_edit = !address_edit">Edit</span></h3>
                                    <div class="d-flex mt-3">
                                        <div class="w-55 d-flex justify-content-between">
                                            <div class="form-group m-0 col-6">
                                                <label for="post_code">Search postcode</label>
                                                <div class="input-group" v-if="address_edit">
                                                    <span class="input-group-text">
                                                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_1034_1828)">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.1605 14.8985L22.5405 20.2785C22.7296 20.4677 22.8357 20.7243 22.8356 20.9919C22.8356 21.2594 22.7292 21.5159 22.54 21.705C22.3507 21.8941 22.0941 22.0003 21.8266 22.0002C21.5591 22.0001 21.3026 21.8937 21.1135 21.7045L15.7335 16.3245C14.1252 17.5702 12.1027 18.1564 10.0776 17.9639C8.05244 17.7713 6.17669 16.8145 4.83194 15.2881C3.48719 13.7617 2.77445 11.7803 2.83871 9.74705C2.90297 7.71378 3.73941 5.78136 5.17787 4.34291C6.61632 2.90445 8.54874 2.06801 10.582 2.00375C12.6153 1.93949 14.5967 2.65223 16.1231 3.99698C17.6495 5.34173 18.6063 7.21748 18.7988 9.24263C18.9913 11.2678 18.4051 13.2902 17.1595 14.8985H17.1605ZM10.8355 15.9995C12.4268 15.9995 13.9529 15.3674 15.0781 14.2421C16.2033 13.1169 16.8355 11.5908 16.8355 9.9995C16.8355 8.4082 16.2033 6.88208 15.0781 5.75686C13.9529 4.63164 12.4268 3.9995 10.8355 3.9995C9.24416 3.9995 7.71804 4.63164 6.59282 5.75686C5.4676 6.88208 4.83546 8.4082 4.83546 9.9995C4.83546 11.5908 5.4676 13.1169 6.59282 14.2421C7.71804 15.3674 9.24416 15.9995 10.8355 15.9995V15.9995Z" fill="#C3C3C3"/>
                                                        </g>
                                                        <defs>
                                                        <clipPath id="clip0_1034_1828">
                                                        <rect width="20.0009" height="20.0004" fill="white" transform="translate(2.83472 1.99976)"/>
                                                        </clipPath>
                                                        </defs>
                                                        </svg>
                                                    </span>
                                                    <input type="text" ref="postcode" class="form-control custom-input" v-model="form.postCode" id="addressAutocompleteRef">
                                                </div>
                                                <div v-else class="w-100 py-2 bg-color rounded-3 px-3">
                                                    {{ form.postCode }} &nbsp;
                                                </div>
                                            </div>
                                            <div class="form-group m-0 col-5">
                                                <label for="customer_city">City</label>
                                                <input v-if="address_edit" type="text" v-model="form.city" class="form-control custom-input">
                                                <div v-else class="w-100 py-2 rounded-2 bg-color px-3">
                                                    {{ form.city }} &nbsp;
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ps-5" v-if="address_edit">
                                            <div class="form-group m-0">
                                                <label for="customer_country">Country</label>
                                                <input type="text" v-model="form.country" class="form-control custom-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-55 mt-3">
                                        <div class="form-group mb-0">
                                            <label for="customer_address1">Delivery address</label>
                                            <input type="text" v-model="form.deliveryAddress1" placeholder="Address line 1" class="form-control custom-input" v-if="address_edit">
                                            <div v-else class="w-100 py-2 rounded-2 bg-color px-3">
                                                {{ form.deliveryAddress1 }} &nbsp;
                                            </div>
                                            <input type="text" v-model="form.deliveryAddress2" placeholder="Address line 2" class="form-control custom-input mt-3" v-if="address_edit">
                                            <div v-else class="w-100 py-2 rounded-2 bg-color px-3 mt-3">
                                                {{ form.deliveryAddress2 }} &nbsp;
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="page-section">
                                    <h3 class="title m-0">{{ form.customerType == 'B2B' ? 'Notes' : 'Customer Notes' }}</h3>
                                    <div class="w-75 mt-3">
                                        <textarea v-model="form.customerNote" name="customer_note" rows="4" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <transition name="list" appear v-if="step =='payment'">
                            <div class="payment cust-page-content m-auto pt-5">
                                <div class="payment-method-section" v-if="form.accountType !='Master'">
                                    <h3 class="title">Payment method</h3>
                                    <div class="page-section">
                                        <div class="credit-card mt-5 d-flex justify-content-between">
                                            <div class="form-group col-3 cardholder mb-0">
                                                <label for="">Cardholder name</label>
                                                <div class="w-100 py-2 rounded-2 bg-color px-3">
                                                    {{ form.cardHolderName }} &nbsp;
                                                </div>
                                            </div>
                                            <div class="form-group col-4 carddetails mb-0">
                                                <label for="">Card details</label>
                                                <div class="input-group bg-color d-flex mb-0">
                                                    <span class="input-group-text border-none">
                                                        <i class="credit-card-icon" :class="cardBrand"></i>
                                                    </span>
                                                    <div class="py-2 rounded-2 bg-color px-3">
                                                        {{ form.cardDetails }} &nbsp;
                                                    </div>                                                    
                                                </div>
                                            </div>
                                            <div class="form-group col-2 cardexpdate mb-0">
                                                <label for="">Expiration date</label>
                                                <div class="w-100 py-2 rounded-2 bg-color px-3">
                                                        {{ form.cardExpDate }} &nbsp;
                                                </div>     
                                            </div>
                                            <div class="form-group col-2 cardexpdate mb-0">
                                                <label for="">CVV</label>
                                                <div class="w-100 py-2 rounded-2 bg-color px-3">
                                                        {{ form.cardCvc }} &nbsp;
                                                </div>                 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice-details-panel" v-if="1 != 1">
                                    <h3 class="title d-flex">
                                        VAT Invoice details
                                        <CheckBox v-model="form.sameAsMaster" class="ms-5" v-if="form.accountType !='Master'"><slot>Same as master</slot></CheckBox>
                                        <CheckBox v-model="form.sameAsContactDetails" class="ms-5"><slot>Same as contact details</slot></CheckBox>
                                        <CheckBox  v-if="form.accountType !='Master'" v-model="form.receiptToVatInvoice" class="ms-5"><slot>Attach e-Receipts to VAT Invoice</slot></CheckBox>
                                    </h3>
                                    <div class="page-section bacs">
                                        <div class="form-group mb-0 company-legal-name">
                                            <label for="company_legal_name">Company legal name</label>
                                            <input type="text" v-model="form.companyLegalName" class="form-control">
                                        </div>
                                        <div class="d-flex mt-3">
                                            <div class="customer-contact w-55 d-flex justify-content-between">
                                                <div class="form-group m-0">
                                                    <label class="form-label d-block m-0" for="first_name">Company representative</label>
                                                    <input type="text" v-model="form.companyRepFirstName" class="form-control custom-input" placeholder="First name">
                                                </div>
                                                <div class="form-group m-0">
                                                    <label class="form-label d-block m-0" for="first_name">&nbsp;</label>
                                                    <input type="text" v-model="form.companyRepLastName" class="form-control custom-input" placeholder="Last name">
                                                </div>
                                            </div>
                                            <div class="customer-phone w-45">
                                                <div>
                                                    <label>Phone Number</label>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="phone-country-code">
                                                        <select-options 
                                                            v-model="form.companyPhoneCountryCode" 
                                                            :options="phoneCodesSorted"
                                                            :width = "'100px'"
                                                            :name="'phoneCountryCode'">
                                                        </select-options>
                                                    </div>
                                                    <div class="form-group ms-2">
                                                        <input type="text" v-model="form.companyPhoneNumber" class="form-control custom-input">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="w-55 mt-3">
                                            <KeepAlive>
                                                <MultipleEmail v-model="form.companyEmails"></MultipleEmail>
                                            </KeepAlive>
                                        </div>
                                        <div class="w-55 mt-4">
                                            <label for="">Billing address</label>
                                            <input type="text" v-model="form.companyAddress1" placeholder="Address line 1" class="form-control custom-input">
                                            <input type="text" v-model="form.companyAddress2" placeholder="Address line 2" class="form-control custom-input mt-3">                                                
                                        </div>
                                        <div class="d-flex mt-3">
                                            <div class="w-55 d-flex justify-content-between">
                                                <div class="form-group m-0">
                                                    <label for="post_code">Search postcode</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g clip-path="url(#clip0_1034_1828)">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.1605 14.8985L22.5405 20.2785C22.7296 20.4677 22.8357 20.7243 22.8356 20.9919C22.8356 21.2594 22.7292 21.5159 22.54 21.705C22.3507 21.8941 22.0941 22.0003 21.8266 22.0002C21.5591 22.0001 21.3026 21.8937 21.1135 21.7045L15.7335 16.3245C14.1252 17.5702 12.1027 18.1564 10.0776 17.9639C8.05244 17.7713 6.17669 16.8145 4.83194 15.2881C3.48719 13.7617 2.77445 11.7803 2.83871 9.74705C2.90297 7.71378 3.73941 5.78136 5.17787 4.34291C6.61632 2.90445 8.54874 2.06801 10.582 2.00375C12.6153 1.93949 14.5967 2.65223 16.1231 3.99698C17.6495 5.34173 18.6063 7.21748 18.7988 9.24263C18.9913 11.2678 18.4051 13.2902 17.1595 14.8985H17.1605ZM10.8355 15.9995C12.4268 15.9995 13.9529 15.3674 15.0781 14.2421C16.2033 13.1169 16.8355 11.5908 16.8355 9.9995C16.8355 8.4082 16.2033 6.88208 15.0781 5.75686C13.9529 4.63164 12.4268 3.9995 10.8355 3.9995C9.24416 3.9995 7.71804 4.63164 6.59282 5.75686C5.4676 6.88208 4.83546 8.4082 4.83546 9.9995C4.83546 11.5908 5.4676 13.1169 6.59282 14.2421C7.71804 15.3674 9.24416 15.9995 10.8355 15.9995V15.9995Z" fill="#C3C3C3"/>
                                                            </g>
                                                            <defs>
                                                            <clipPath id="clip0_1034_1828">
                                                            <rect width="20.0009" height="20.0004" fill="white" transform="translate(2.83472 1.99976)"/>
                                                            </clipPath>
                                                            </defs>
                                                            </svg>
                                                        </span>
                                                        <input type="text" ref="companyPostCode" class="form-control custom-input" v-model="form.companyPostCode">
                                                    </div>                                                        
                                                </div>
                                                <div class="form-group m-0">
                                                    <label for="customer_city">City</label>
                                                    <input type="text" v-model="form.companyCity" class="form-control custom-input">
                                                </div>
                                            </div>
                                            <div class="ps-5">
                                                <div class="form-group m-0">
                                                    <label for="customer_country">Country</label>
                                                    <input type="text" v-model="form.companyCountry" class="form-control custom-input">
                                                </div>
                                            </div>
                                        </div>                                          
                                    </div>
                                </div>
                                <div class="discount-credit-panel">
                                    <h3 class="title d-flex">Discounts and credit</h3>
                                    <div class="page-section">
                                        <div class="d-flex">
                                            <div class="col-4">
                                                <div class="form-group mb-0 payment-method">
                                                    <label for="discount_credit">Discount Level</label>
                                                    <div class="w-100 py-2 bg-color px-3 rounded-3">
                                                        {{ form.discountLevel }}%
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group mb-0 payment-method">
                                                    <label for="discount_credit">Credit amount</label>
                                                    <div class="w-100 py-2 bg-color px-3 rounded-3">
                                                        <b>£</b> {{ form.creditAmount }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group col-6">
                                                    <label for="add_credit">Add credit</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text fw-bold">£</span>
                                                        <input type="text" v-model="form.discountCredit"  class="form-control" id="add_credit" placeholder="0.00">
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <transition name="list" appear v-if="step == 'order_management'">
                            <div class="cust-page-content mt-5 m-auto order-panel">
                                <h3 class="title mb-3">Current orders</h3>
                                <table class="table table-hover current-orders bg-white">
                                    <thead class="bg-color">
                                        <tr>
                                            <th>Order N°</th>
                                            <th>Destination</th>
                                            <th>Items accepted</th>
                                            <th>Delivery date</th>
                                            <th>Items</th>
                                            <th>Order Status</th>
                                            <th>Total</th>
                                            <th class="text-center">E-Reciept</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in currentOrders" :key="index">
                                            <td valign="middle">{{ item.order_id }}</td>
                                            <td valign="middle">{{ item.destination }}</td>
                                            <td valign="middle">{{ item.items_received }}</td>
                                            <td valign="middle" class="fw-bold">{{ item.deliv }}</td>
                                            <td valign="middle">{{ item.items }}</td>
                                            <td valign="middle">
                                                <div class="location-icon rounded-pill d-flex align-items-center px-2" :style="{'background-color': item.location_color}">
                                                    <svg v-if="item.process == 1" class="me-2" width="12" height="12" viewBox="0 0 12 12" :fill="'#'+item.circle_color" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+item.circle_color"/>
                                                        <circle cx="6" cy="6" r="5" :stroke="'#'+item.circle_color" stroke-width="2"/>
                                                    </svg>
                                                    <svg v-else class="me-2" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+item.circle_color"/>
                                                        <circle cx="6" cy="6" r="5" :stroke="'#'+item.circle_color" stroke-width="2"/>
                                                    </svg>                                
                                                    <span class="text-capitalize">{{ item.location }}</span>
                                                </div>
                                            </td>
                                            <td valign="middle">
                                                <div class="d-flex align-items-center fw-bold">
                                                    <span class="d-block me-3 order-pay" :class="item.paid"></span>
                                                    £{{ item.total }}
                                                </div>                                                
                                            </td>
                                            <td valign="middle"><span class="m-auto e-receipt-icon cursor-pointer"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <h3 class="title mb-3 mt-5">Past orders</h3>
                                <table class="table table-hover past-orders bg-white">
                                    <thead class="bg-color">
                                        <tr>
                                            <th>Order N°</th>
                                            <th>Destination</th>
                                            <th>Items accepted</th>
                                            <th>Delivery date</th>
                                            <th>Items</th>
                                            <th>Order Status</th>
                                            <th>Total</th>
                                            <th class="text-center">VAT Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in pastOrders" :key="index">
                                            <td valign="middle">{{ item.order_id }}</td>
                                            <td valign="middle">{{ item.destination }}</td>
                                            <td valign="middle">{{ item.items_received }}</td>
                                            <td valign="middle">{{ item.deliv }}</td>
                                            <td valign="middle">{{ item.items }}</td>
                                            <td valign="middle">
                                                <div class="location-icon rounded-pill d-flex align-items-center px-2" :style="{'background-color': item.location_color}">
                                                    <svg v-if="item.process == 1" class="me-2" width="12" height="12" viewBox="0 0 12 12" :fill="'#'+item.circle_color" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+item.circle_color"/>
                                                        <circle cx="6" cy="6" r="5" :stroke="'#'+item.circle_color" stroke-width="2"/>
                                                    </svg>
                                                    <svg v-else class="me-2" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+item.circle_color"/>
                                                        <circle cx="6" cy="6" r="5" :stroke="'#'+item.circle_color" stroke-width="2"/>
                                                    </svg>                                
                                                    <span class="text-capitalize">{{ item.location }}</span>
                                                </div>
                                            </td>
                                            <td valign="middle">
                                                <div class="d-flex align-items-center fw-bold">
                                                    <span class="d-block me-3 order-pay" :class="item.paid"></span>
                                                    £{{ item.total }}
                                                </div>
                                            </td>
                                            <td valign="middle"><span class="m-auto vat-invoice-icon cursor-pointer"></span></td>
                                        </tr>
                                    </tbody>                                    
                                </table>
                            </div>
                        </transition>
                        <transition name="list" appear v-if="step =='preferences'">
                            <div class="cust-page-content preferences m-auto pt-5">
                                <div class="d-flex justify-content-between mt-3 flex-wrap">
                                    <div class="blocks" v-for="(group, index) in form.preferences" :key="index">
                                        <h3 class="title mb-3">{{ group.key }}</h3>
                                        <div class="page-section">
                                            <div class="item-block py-3 border-bottom" v-for="(item, key) in group.data" :key="key">
                                                <div class="d-flex justify-content-between">
                                                    <div class="col-8">
                                                        <h4 class="sub-title col-12">{{ item.title }}</h4>
                                                        <p class="m-0 col-12">{{ item.description }}</p>
                                                    </div>
                                                    <div class="col-3" :class="item.type == 'switch' ? 'd-flex justify-content-end align-items-start': ''">
                                                        <switch-btn class="ms-auto" v-if="item.type == 'switch'" v-model="item.value"></switch-btn>
                                                        <div class="preference-radio" v-else>
                                                            <label class="custom-radio" v-for="(value, key) in item.dropdown_values.split('\r\n')" :key="key">{{ value }}
                                                                <input type="radio" :checked="item.value == value ? true : false" :value="value" v-model="item.value" :name="'pref_'+item.id">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="blocks">
                                        <h3 class="title mb-3">Delivery instructions</h3>
                                        <div class="page-section p-4">
                                            <div class="d-flex justify-content-between">
                                                <div class="payment-method">
                                                    <div class="form-group mb-0">
                                                        <label for="">Deliver to</label>
                                                        <div class="w-100 bg-color py-2 rounded-3 px-3">
                                                            {{ form.altTypeDelivery }} &nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-0 payment-method">
                                                    <label for="">Name</label>
                                                    <div class="w-100 bg-color py-2 rounded-3 px-3">
                                                        {{ form.altName }} &nbsp;
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="w-100">
                                                    <label>Phone Number</label>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="phone-country-code">
                                                        <div class="w-100 bg-color py-2 px-4 rounded-3">
                                                            {{ form.altPhoneCountryCode }} &nbsp;
                                                        </div>                                                        
                                                    </div>
                                                    <div class="form-group payment-method ms-2">
                                                        <div class="w-100 bg-color py-2 rounded-3 px-3">
                                                            {{ form.altPhoneNumber }} &nbsp;
                                                        </div>                                                        
                                                    </div>
                                                </div>                                            
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="form-group m-0">
                                                    <label for="" class="form-label d-block m-0">Driver instructions</label>
                                                    <textarea readonly name="customer_note" rows="4" class="form-control border-none bg-color" v-html="form.altDriverInstruction"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <transition name="list" appear v-if="step =='linked_account'">
                            <div class="cust-page-content m-auto pt-5">
                                <h3 class="title mb-3">Linked account(s)</h3>
                                <div class="account-list-section">
                                    <table class="table linked-account-table bg-white">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Contact</th>
                                                <th class="text-nowrap">Account type</th>
                                                <th class="text-nowrap">Phone</th>
                                                <th class="text-nowrap">Email</th>
                                                <th class="text-nowrap">Date added</th>
                                                <th class="text-nowrap">Spend to date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in form.linkedAccounts" :key="index">
                                                <td valign=middle class="text-nowrap">{{ item.name }}</td>
                                                <td valign=middle class="text-nowrap fw-bold">{{ item.accountType }}</td>
                                                <td valign=middle class="text-nowrap">{{ formatPhone(item.phone) }}</td>
                                                <td valign=middle class="text-nowrap">{{ item.email }}</td>
                                                <td valign=middle class="text-nowrap">{{ item.date }}</td>
                                                <td valign=middle class="fw-bold text-nowrap">£ {{ item.spent }}</td>
                                                <td valign=middle>
                                                    <svg width="30" height="30" fill="#47454B" @click="removeLinkedAccount(item.id)" class="unlink cursor-pointer" version="1.1" viewBox="0 0 700 700" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                                        <g>
                                                        <path d="m452.2 362.38 56.953 56.953-19.824 19.824-74.871-74.871h-47.656v-32.312h15.68l-35.727-35.168h-63.676v-33.039h30.574l-35.449-35.449h-12.203c-13.785-0.042969-27.016 5.4102-36.77 15.148-9.75 9.7422-15.219 22.969-15.191 36.754 0.027344 13.781 5.5469 26.988 15.336 36.688 9.6133 9.8555 22.863 15.305 36.625 15.066h67.762v32.312h-67.762c-22.273 0.33203-43.676-8.6641-59.023-24.809-15.648-15.84-24.426-37.207-24.426-59.473s8.7773-43.633 24.426-59.473c11.098-11.586 25.414-19.586 41.102-22.961l-57.23-56.895 19.824-19.824 74.871 75.152h0.39062l32.312 32.312h-0.44922l35.449 35.449 33.32 33.039 35.449 35.449 30.465 30.07zm41.105-22.902c15.648-15.84 24.426-37.207 24.426-59.473s-8.7773-43.633-24.426-59.473c-15.398-16.043-36.793-24.934-59.023-24.527h-67.48v32.312h67.762-0.003906c13.785-0.042969 27.016 5.4102 36.77 15.148 9.75 9.7422 15.219 22.969 15.191 36.754-0.027343 13.781-5.5469 26.988-15.336 36.688-3.5586 3.5781-7.5898 6.6484-11.984 9.1289l23.297 23.238v0.003907c3.8672-2.9609 7.4844-6.2383 10.809-9.8008z"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="d-flex">
                                        <button class="border-btn add-new-account d-flex justify-content-between align-items-center me-3">
                                            <svg class="me-3" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="12" fill="black"/>
                                                <path d="M11.0897 14.472C11.0897 14.984 11.4897 15.384 12.0017 15.384C12.4977 15.384 12.9137 14.984 12.9137 14.472V12.248H15.1857C15.6657 12.248 16.0657 11.864 16.0657 11.368C16.0657 10.888 15.6657 10.488 15.1857 10.488H12.9137V8.264C12.9137 7.752 12.4977 7.352 12.0017 7.352C11.4897 7.352 11.0897 7.752 11.0897 8.264V10.488H8.81769C8.33769 10.488 7.93769 10.888 7.93769 11.368C7.93769 11.864 8.33769 12.248 8.81769 12.248H11.0897V14.472Z" fill="white"/>
                                            </svg>
                                            Create new account
                                        </button>
                                        <button @click="showSearchPanel" class="border-btn add-existing-account d-flex justify-content-between align-items-center">
                                            <svg class="me-3" width="24" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_550_21813)">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.8257 14.8987L22.2057 20.2787C22.3948 20.468 22.501 20.7246 22.5009 20.9921C22.5008 21.2596 22.3945 21.5161 22.2052 21.7052C22.016 21.8943 21.7594 22.0005 21.4919 22.0004C21.2244 22.0003 20.9678 21.894 20.7787 21.7047L15.3987 16.3247C13.7905 17.5704 11.768 18.1566 9.74287 17.9641C7.71772 17.7716 5.84198 16.8148 4.49723 15.2884C3.15248 13.7619 2.43973 11.7806 2.504 9.74729C2.56826 7.71402 3.4047 5.7816 4.84315 4.34315C6.2816 2.9047 8.21402 2.06826 10.2473 2.004C12.2806 1.93973 14.2619 2.65248 15.7884 3.99723C17.3148 5.34198 18.2716 7.21772 18.4641 9.24287C18.6566 11.268 18.0704 13.2905 16.8247 14.8987H16.8257ZM10.5007 15.9997C12.092 15.9997 13.6182 15.3676 14.7434 14.2424C15.8686 13.1172 16.5007 11.591 16.5007 9.99974C16.5007 8.40845 15.8686 6.88232 14.7434 5.7571C13.6182 4.63189 12.092 3.99974 10.5007 3.99974C8.90944 3.99974 7.38332 4.63189 6.2581 5.7571C5.13289 6.88232 4.50074 8.40845 4.50074 9.99974C4.50074 11.591 5.13289 13.1172 6.2581 14.2424C7.38332 15.3676 8.90944 15.9997 10.5007 15.9997Z" fill="#47454B"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_550_21813">
                                            <rect width="20.0009" height="20.0004" fill="white" transform="translate(2.5 2)"/>
                                            </clipPath>
                                            </defs>
                                            </svg>
                                            Add existing account
                                        </button>
                                        <Search ref="searchpanel"  @selectedSubAccount="selectedSubAccount"/>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import BreadCrumb from '../layout/BreadCrumb'
    import SideBar from '../layout/SideBar'
    import { useRouter, useRoute } from 'vue-router';
    import {ref,onMounted, nextTick, computed, watch } from 'vue';
    import SelectOptions from '../test/SelectOptions';
    import MultipleEmail from '../test/MultipleEmail';
    import CheckBox from '../miscellaneous/CheckBox';
    import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
    import SwitchBtn from '../miscellaneous/SwitchNumberBtn.vue';
    import Search from './Search';

    import {
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        TOASTER_GET_ALL,
        TOASTER_REMOVE_TOAST,
        LOADER_MODULE,
        DISPLAY_LOADER,
        HIDE_LOADER,
    } from "../../store/types/types";
    import axios from 'axios';
    import { useStore } from 'vuex';
    export default {
        name: "ViewCustomer",
        components:{
            BreadCrumb,
            SideBar,
            SelectOptions,
            CheckBox,
            SwitchBtn,
            MultipleEmail,
            Search,
        },
        setup(props,context){
            const route = useRoute();
            const store = useStore();
            const form = ref({
                customerID: '',
                Name: '',
                accountType: 'Main',
                customerType: '',
                typeDelivery: '',
                programmeType: '',
                kioskNumber: '',
                firstName: '',
                lastName: '',
                phoneCountryCode: '',
                phoneNumber: '',
                email: '',
                postCode: '',
                city: '',
                state: '',
                county: '',
                country: '',
                customerLat: '',
                customerLon: '',
                deliveryAddress1: '',
                deliveryAddress2: '',
                customerNote: '',
                // payment tab
                alreadyLinkedToAccount: true,
                paymentMethod: '',
                cardHolderName: '',
                cardDetails: '',
                cardExpDate: '',
                cardCVV: '',
                attachReceiptToVatInvoice: false,
                companyLegalName: '',
                companyRepFirstName: '',
                companyRepLastName: '',
                companyPhoneCountryCode: '',
                companyPhoneNumber: '',
                invoiceEmail1: '',
                invoiceEmail2: '',
                // invoiceEmails: [],
                companyPostCode: '',
                companyCity: '',
                companyCounty: '',
                companyState: '',
                companyCountry: '',
                companyLat: '',
                companyLon: '',
                companyAddress1: '',
                companyAddress2: '',
                discountLevel: '',
                discountCredit:'',
                applyDiscountToSub: false,
                creditAmount: 0,
                addCredit: 0,
                // preferences tab
                preferences: [],

                altTypeDelivery: '',
                altName: '',
                altPhoneCountryCode: '',
                altPhoneNumber: '',
                altDriverInstruction: '',
                linkedAccounts: []
            })
            const router = useRouter();
            const step = ref('');
            if(localStorage.getItem('step')){
                step.value = localStorage.getItem('step');
            }else{
                step.value = 'account_details';
            }
            const postcode = ref(null);
            const cardBrand = ref('');
            const contact_details_edit = ref(false);
            const address_edit = ref(false);
            const companyPostCode = ref(null);
            const searchpanel = ref(null);
            const showcontainer=ref(false);
            const searchCustomer=ref(false);
            const currentOrders=ref([]);
            const pastOrders=ref([]);
            const timeout =ref('');
            const paths=ref([
                { name:'Customer', route:'Customer'},
            ]);            
            onMounted(()=>{
                nextTick(()=>{
                    showcontainer.value=true;
                }); 
                getCustomerFullDetail();
                setTimeout(() => {
                    const customerAddress = new google.maps.places.Autocomplete(postcode.value);
                    customerAddress.addListener("place_changed", () => {
                        const place = customerAddress.getPlace();
                        setCustomerAddress(place.address_components);
                    });  
                }, 500);
            });
            const getCustomerFullDetail = ()=>{
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading customer data...']);
                axios.post('/get-customer-full-detail', {
                    customer_id: route.params.customer_id
                }).then((res)=>{
                   
                    form.value.discountCredit = res.data.credit;
                    form.value.creditAmount = res.data.credit;
                    
                    paths.value.push(
                        { name: res.data.firstName +' ' + res.data.lastName , route:'ViewCustomer', params:{ customer_id: res.data.id }}
                    );
                    form.value.customerID = res.data.customerID;
                    form.value.Name = res.data.Name;
                    form.value.booking = res.data.booking;
                    form.value.totalSpent = res.data.totalSpent;
                    form.value.accountType = '',
                    form.value.customerType = res.data.customerType;
                    form.value.typeDelivery = res.data.typeDelivery;
                    form.value.programmeType = res.data.programmeType;
                    form.value.kioskNumber = res.data.kioskNumber;
                    form.value.firstName = res.data.firstName;
                    form.value.lastName = res.data.lastName;
                    var phone = getPhone(res.data.phone);
                    form.value.phoneCountryCode = phone.code;
                    form.value.phoneNumber = phone.number;
                    form.value.email = res.data.email;
                    // address part in account details tab
                    form.value.postCode = res.data.address.postCode;
                    form.value.city = res.data.address.city;
                    form.value.state = res.data.address.state;
                    form.value.county = res.data.address.county;
                    form.value.country = res.data.address.country;
                    form.value.deliveryAddress1 = res.data.address.address1;
                    form.value.deliveryAddress2 = res.data.address.address2;
                    form.value.customerNote = res.data.customerNote;
                    // payment tab
                    form.value.paymentMethod  = res.data.paymentMethod == 1 ? 'Credit Card' : 'BACS';
                    if(res.data.paymentMethod == 1 && res.data.card){
                        form.value.cardHolderName = res.data.card ?? res.data.card.cardHolderName;
                        form.value.cardDetails = res.data.card ?? res.data.card.cardNumber;
                        form.value.cardExpDate = res.data.card ?? res.data.card.expDate;
                        form.value.cardCVV = '***';
                        if(res.data.card.type == 'Visa'){
                            cardBrand.value = 'cc-visa';
                        }else if(res.data.card.type == 'Mastercard'){
                            cardBrand.value = 'cc-mastercard';
                        }else if(res.data.card.type == 'Amex'){
                            cardBrand.value = 'cc-amex';
                        }else if(res.data.card.type == 'discover'){
                            cardBrand.value = 'cc-discover';
                        }
                    }
                    currentOrders.value = res.data.currentOrders;
                    pastOrders.value = res.data.pastOrders;
                    // companyLegalName: '',
                    // companyRepFirstName: '',
                    // companyRepLastName: '',
                    // companyPhoneCountryCode: '',
                    // companyPhoneNumber: '',
                    // invoiceEmail1: '',
                    // invoiceEmail2: '',
                    // // invoiceEmails: [],
                    // companyPostCode: '',
                    // companyCity: '',
                    // companyCounty: '',
                    // companyState: '',
                    // companyCountry: '',
                    // companyLat: '',
                    // companyLon: '',
                    // companyAddress1: '',
                    // companyAddress2: '',
                    form.value.discountLevel = res.data.discount;
                    
                    
                    // applyDiscountToSub: false,
                    

                    // // preferences tab
                    Object.keys(res.data.preferences).forEach((item)=>{
                        form.value.preferences.push({
                            data: res.data.preferences[item],
                            key: item,
                        })
                    })
                    if(res.data.deliveryPreference){
                        form.value.altTypeDelivery = res.data.deliveryPreference.altTypeDelivery;
                        form.value.altName = res.data.deliveryPreference.altName;
                        form.value.altPhoneCountryCode = res.data.deliveryPreference.altPhoneCountryCode;
                        form.value.altPhoneNumber = res.data.deliveryPreference.altPhoneNumber;
                        form.value.altDriverInstruction = res.data.deliveryPreference.altDriverInstruction;
                    }
                    // linked accounts
                    form.value.linkedAccounts = res.data.linkedAccounts;
                }).catch((error)=>{
                    console.log(error);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })
            }
            const setCustomerAddress = ( address_components )=>{
                address_components.forEach(component => {
                    if(component.types.includes("postal_code")){
                        form.value.postCode = component.long_name
                    }else if(component.types.includes("country")){
                        form.value.country = component.long_name
                    }else if(component.types.includes("locality")){ // || component.types.includes("sublocality") || component.types.includes("sublocality_level_1")
                        form.value.city = component.long_name
                    }else if(component.types.includes("street_number")){
                        form.value.deliveryAddress1 = component.long_name
                    }
                });
            }
            const setCompanyAddress = ( address_components )=>{
                address_components.forEach(component => {
                    if(component.types.includes("postal_code")){
                        form.value.companyPostCode = component.long_name
                    }else if(component.types.includes("country")){
                        form.value.companyCountry = component.long_name
                    }else if(component.types.includes("locality")){ // || component.types.includes("sublocality") || component.types.includes("sublocality_level_1")
                        form.value.companyCity = component.long_name
                    }else if(component.types.includes("street_number")){
                        form.value.companyAddress1 = component.long_name
                    }
                });
            }
            const getPhone = (phoneString)=>{
                if(phoneString != ""){
                    if(phoneString.split('"').length == 1){
                        return phoneString;
                    }else{
                        var phone = phoneString.split('"')[1];
                        if(phone.split("|").length > 1){
                            var area_code = phone.split("|")[0];
                            var number = phone.split("|")[1];
                            return { code: area_code, number: number};
                        }else
                            return { code: '', number: phone};
                    }
                }else{
                    
                    return { code: '', number: ''};
                }  
            }
            const selectNav = (nav)=>{
                step.value = nav;
            }
            const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
                            [item.value, item])).values()].sort((a, b)=>{
                    return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
            });
            watch(()=>form.value.paymentMethod,(current_value, previous_value)=>{
                if(current_value == 'BACS'){
                    nextTick(() => {
                        const companyAddress = new google.maps.places.Autocomplete(companyPostCode.value);
                        companyAddress.addListener("place_changed", () => {
                            const place = companyAddress.getPlace();
                            setCompanyAddress(place.address_components);
                        });  
                    });
                }
            })
            // handler when you click search customer button
            const showSearchPanel = ()=>{
                searchpanel.value.openSearchPanel();
            }
            // handler when the customer selected in search result
            const selectedSubAccount = (data)=>{
                form.value.linkedAccounts = [...form.value.linkedAccounts, 
                    { 
                        id:     data.id, 
                        name:   data.name, 
                        accountType: 'Sub',
                        phone:  data.phone, 
                        email:  data.email, 
                        date:   data.date, 
                        spent:  data.spent 
                    }
                ]
            };
            // handler when you unlink sub account from linked accounts
            const removeLinkedAccount = (id)=>{
                form.value.linkedAccounts = form.value.linkedAccounts.filter((item)=>{
                    return item.id != id;
                });
            } 
            const formatPhone = (phoneString)=>{
                if(phoneString != ""){
                    if(phoneString.split('"').length == 1){
                        return phoneString;
                    }else{
                        var phone = phoneString.split('"')[1];
                        if(phone.split("|").length > 1){
                            var area_code = phone.split("|")[0];
                            var number = phone.split("|")[1];
                            return '+' + area_code.replace(/\D/g, '') + ' ' + number.replace(/ /g, '').replace(/(\d{3})(\d{3})(\d{3,4})/, "$1 $2 $3");
                        }else
                            return phone.replace(/\D/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{3,4})/, "+$1 $2 $3 $3");
                    }
                }else{
                    return '';
                }
            }  
               watch(()=>form.value.discountCredit,(current_value, previous_value)=>{
               clearTimeout(timeout.value);
               timeout.value = setTimeout(function(){
                   
                    axios.post('/add-credit-customer', { credit : form.value.discountCredit , customer_id : route.params.customer_id } ).then((response)=>{
                    console.log("response")
                    }).catch((errors)=>{
                        console.log(errors);
                    
                    })  
                    }
                   , 500)    
               })

            return {
                form,
                step,
                contact_details_edit,
                address_edit,
                showcontainer,
                searchCustomer,
                paths,
                selectNav,
                postcode,
                companyPostCode,
                phoneCodesSorted,
                currentOrders,
                pastOrders,
                searchpanel,
                showSearchPanel,
                selectedSubAccount,
                removeLinkedAccount,
                formatPhone,
                cardBrand
            }
        },
    }
</script>
<style scoped lang="scss">
    .hmax{
        padding-top: 0;
    }
    .linked-account-table thead tr{
        border: 1px solid #E0E0E0;
        background: #F8F8F8;
    }
    .linked-account-table th{
        font-family: 'Gotham Rounded Medium';
        font-weight: 400;
    }
    .linked-account-table tbody > :first-child{
        border-top: 2px solid #E0E0E0;
    }
    .trash{
        cursor: pointer;
    }
    .border-btn{
        padding: 18px;
        border: 1px solid #47454B;
        border-radius: 4px;
        font-family: 'Gotham Rounded Medium';
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 140%;
    }
    .border-btn:hover{
        background: white;
        transition: background 0.3s ease-in;
    }
    .booking-icon{
        width: 114px;
        text-align: center;
        font-size: 12px;
        line-height: 14px;
        padding: 2px 16px;
        background: rgba(66, 167, 30, 0.2);
        color: #42A71E;
    }
    .spent-section{
        padding: 20px 20px;
        color: #F8F8F8;
        border-left-width: 7px;
        border-left-color: #42A71E;
        border-left-style: solid;
        border-radius: 4px;
        background: #000000;
        font-size: 20px;
        font-weight: bold;
        line-height: 140%;
        span{
            font-size: 16px;
            margin-left: 15px;
        }
    }
</style>
<style lang="scss">
.list-enter-from{
    opacity: 0;
    transform: scale(0.6);
}
.list-enter-to{
    opacity: 1;
    transform: scale(1);
}
.list-enter-active{
    transition: all 1s ease;
}

.list-leave-from{
    opacity: 1;
    transform: scale(1);
}
.list-leave-to{
    opacity: 0;
    transform: scale(0.6);
}
.list-leave-active{
    transition: all 1s ease;
    position: absolute;
    width: 100%;
}
.list-move{
    transition:all 0.9s ease;
} 
.cust-page{
  .title{
    font-family: 'Gilroy';
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;
  }
}
.page-header{
  height: 80px;
  padding: 20px 70px;
  h1{
      font-family: 'Gilroy';
      font-style: normal;
      font-weight: 800;
      font-size: 36px;
      line-height: 106%;
      color: #000000;
  }
}
.b2c-icon,
.b2b-icon{
width: 50px;
height: 25px;
font-family: 'Gotham Rounded';
font-style: normal;
font-weight: 400;
font-size: 12px;
line-height: 140%;        
background: #47454B;
color: #FFF;
}
.programme-icon{
    width: 83px;
    height: 25px;
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 400;
    font-size: 12px;
    line-height: 140%;        
    background: #DBBA44;
    color: #FFF;
}
.b2b-icon{
background: #4E58E7;
}
.order-pay{
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.order-pay.unpaid{
    background: #EB5757;
}
.order-pay.paid{
    background: #42A71E;
}
.unlink:hover{
    fill: #42A71E;
}
// custome radio button
.preference-radio{
/* Customize the label (the container) */
    .custom-radio {
        display: block;
        position: relative;
        padding-left: 24px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 14px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        /* Hide the browser's default radio button */
        input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
            /* When the radio button is checked, add a blue background */
            &:checked ~ .checkmark {
                border: solid 1px #42A71E;
                /* Show the indicator (dot/circle) when checked */
                &:after {
                    display: block;
                }
            }
        }
        /* On mouse-over, add a grey background color */
        // &:hover input ~ .checkmark {
        //     background-color: #ccc;
        // }
        .checkmark:after {
            top: 3px;
            left: 3px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background:#42A71E;
        }        
    }
    /* Create a custom radio button */
    .checkmark {
        position: absolute;
        top: 1px;
        left: 0;
        height: 18px;
        width: 18px;
        border-radius: 50%;
        border: solid 1px #000;
        background-color: white;
        /* Create the indicator (the dot/circle - hidden when not checked) */
        &:after {
            content: "";
            position: absolute;
            display: none;
        }
    }
}
.full-nav{
  height: 70px;
  border-top: 1px solid #C3C3C3;
  .full-nav-item{
        flex: 1 1 auto; 
      cursor: pointer;
      position: relative;
      .icon{
          margin-right: 30px;
      }
      &::after{
          content: "";
          position: absolute;
          bottom: 0;
          width: 100%;
          height: 1px;
          background: #C3C3C3;
      }
      &.active,
      &:hover{
          background: rgba(217, 237, 210, 0.2);
          transition: background .3s ease-in-out;
      }
      &.active::after,
      &:hover::after{
          height: 4px;
          background: #42A71E;
          transition: background .3s ease-in-out;
      }
  }
  .border-right{
      border-right: 1px solid #C3C3C3;
  }
}
.cust-page-content{
  width: 1000px;
  margin-top: 3.125rem;
  .account-type{
      min-width: 12rem;
      width: max-content;
      .border-none{
          border: none;
          outline: none;
      } 
      .font-gilroy{
        font-family: 'Gilroy';
        font-weight: 800;
      } 
      .font-22{
        font-size: 22px;
        line-height: 110%;
      }
  }
  .title{
    font-family: 'Gilroy';
    font-style: normal;
    font-weight: 800;
    font-size: 1.375rem;
    line-height: 110%;
    label{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 140%;
    }
  }
    span.chkbox {
        border: solid #868686;
    }  
    .company-legal-name{
        width: 325px;
    }
  .page-section{
    padding: 1.875rem 5rem 1.875rem;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
    margin-bottom: 30px;
    input[type="text"]:focus,
    input[type="tel"]:focus,
    input[type="email"]:focus{
        outline: 2px #000000 solid;
        border-color: #000000;
        box-shadow: none;
    }
    .type-delivery{
        width: 200px;
    }
    .customer-type,
    .payment-method{
        width: 220px;
    }
    .w-75{
        width: 75%;
    }
    .w-55{
        width: 55%;
    }
    .w-45{
        width: 45%;
        padding-left: 2.5rem;
    }
  }
}
.payment.cust-page-content{
    .page-section{
        padding: 1.875rem;
    }
}
.preferences.cust-page-content{
    width: 1410px;
    .blocks{
        width: 48%;
        padding: 0;
        .page-section{
            padding: 0;
            width: 100%;
        }
        .item-block{
            padding: 30px;
            .switch{
                background-color: #EB5757;
            }
            .switch.on{
                background-color: #42A71E;
            }
        }
        .sub-title{
            font-family: 'Gotham Rounded Medium';
            font-style: normal;
            font-weight: 400;
            font-size: 16px;
            line-height: 140%;
        }
    }
}
.cust-page-footer{
  padding: 1.563rem;
  .btn-cancel{
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 400;
    font-size: 1rem;
    line-height: 19px;
    color: #47454B;
    margin-right: 50px;
  }
  .btn-next{
    padding: 15px 60px;
    background: #47454B;
    border-radius: 4px;
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 140%;
    color: #FFFFFF;
    text-decoration: none;
  }
}
.input-group.error{
    .input-group-text,
    input,
    input.error{
        border-color: #EB5757;
    }
    input.error:focus{
        outline: none;
        border-color: #EB5757;
    }
}
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
</style>
