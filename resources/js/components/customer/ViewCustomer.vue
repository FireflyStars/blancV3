<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid bg-color p-0" v-if="showcontainer">
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
                                                    { display:'Marylebone', value: 'MARYLEBONE' },
                                                    { display:'Chelsea', value: 'CHELSEA' },
                                                    { display:'Notting Hill', value: 'NOTTING HILL' },
                                                    { display:'Delivery', value: 'DELIVERY' },
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
                                            <div class="form-group m-0 customer-type" v-if="contact_details_edit">
                                             <select-options
                                                v-model="form.CustomerPayemenProfile"
                                                :options="[
                                                    { display:'Pay As You Go', value: 0 },
                                                    { display:'On Account', value: 1 },
                                                ]"
                                                :label="'Customer payement profile'"
                                                :name="'CustomerPayemenProfile'">
                                             </select-options>
                                           </div>
                                           <div class="from-group" v-else>
                                                <label for="">Customer payement profile</label>
                                                <div class="customer-type py-2 bg-color rounded-3">
                                                    <span class="d-flex align-items-center justify-content-center rounded-pill  ms-3" v-html="form.CustomerPayemenProfile == '0' ? 'Pay As You Go' : 'On Account'">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3" v-if="form.customerType == 'B2B'">
                                        <div class="customer-company w-45 p-0 justify-content-between">
                                                <label class="form-label d-block m-0" for="email">Company legal name</label>
                                                <input v-if="contact_details_edit" type="text" v-model="form.CompanyName" class="form-control custom-input" placeholder="Branch / Company name">
                                                <div v-else class="w-100 py-2 rounded-3 bg-color px-3">
                                                    {{ form.CompanyName }} &nbsp;
                                                </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3">
                                        <div class="customer-contact w-55 d-flex justify-content-between">
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="last_name">{{ form.customerType == 'B2C' ? 'Last name *' : 'Representative last name *' }}</label>
                                                <input v-if="contact_details_edit" type="text" v-model="form.lastName" class="form-control customer-type custom-input" placeholder="Last name">
                                                <div v-else class="customer-type py-2 rounded-3 bg-color px-3" v-html="form.lastName == '' ? '&nbsp;' : form.lastName">
                                                </div>
                                            </div>
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="first_name">{{ form.customerType == 'B2C' ? 'First name  *' : 'Representative first name' }}</label>
                                                <input v-if="contact_details_edit" type="text" v-model="form.firstName" class="form-control custom-input" placeholder="First name">
                                                <div v-else style="min-height: 40px;" class="customer-type py-2 rounded-3 bg-color px-3" v-html="form.firstName == '' ? '&nbsp;' : form.firstName">
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
                                                        :modelValue="form.phoneCountryCode"
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
                                    <div>
                                        <div class="mt-3 d-flex">
                                            <div class="customer-email w-55 justify-content-between">
                                                <label class="form-label d-block m-0" for="email">{{ form.customerType == 'B2C' ? "Email" : form.accountType == 'Main' ? 'Representative email address' : 'Business email address' }}</label>
                                                <input v-if="contact_details_edit" type="text" v-model="form.email" class="form-control custom-input" placeholder="Email">
                                                <div v-else class="w-100 py-2 rounded-3 bg-color px-3">
                                                    {{ form.email }} &nbsp;
                                                </div>
                                            </div>
                                            <div  class="customer-type w-45">
                                                <div class="form-group m-0 customer-type" >
                                                    <label class="form-label d-block m-0">Kiosk number</label>
                                                    <input v-if="contact_details_edit" type="text" v-model="form.kioskNumber" class="form-control custom-input" placeholder="Enter kiosk card number">
                                                    <div v-else class="w-100 py-2 rounded-3 bg-color px-3">
                                                        {{ form.kioskNumber }} &nbsp;
                                                    </div>
                                                </div>
                                            </div>

                                        <div class="mt-3" v-if="contact_details_edit">
                                            <button class="btn btn btn-success each-save-btn" @click="validateAndSaveContactDetails">Save</button>
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
                                    <div class="d-flex mt-3 align-items-end">
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
                                        <div class="w-45 d-flex justify-content-end" v-if="address_edit">
                                            <button class="btn btn btn-success each-save-btn" @click="validateAndSaveContactAddress">Save</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="page-section">
                                    <h3 class="title m-0">{{ form.customerType == 'B2B' ? 'Notes' : 'Customer Notes' }} <span class="gotham-rounded-book primary-color ms-3 font-16 cursor-pointer text-decoration-underline" @click="customer_note_edit = !customer_note_edit">Edit</span></h3>
                                    <div class="w-75 mt-3">
                                        <textarea v-model="form.customerNote" name="customer_note" rows="4" class="form-control" :readonly="!customer_note_edit"></textarea>
                                    </div>
                                    <div class="d-flex" v-if="customer_note_edit">
                                        <div class="w-25 mt-3">
                                            <button class="btn btn-success each-save-btn" @click="saveCustomerNote">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <transition name="list" appear v-if="step =='payment'">
                            <div class="payment cust-page-content m-auto pt-5">
                                <div class="payment-method-section" v-if="form.accountType !='Master'">
                                    <h3 class="title">Payment method <span v-if="!creditCardCustomer" class="gotham-rounded-book primary-color ms-3 font-16 cursor-pointer text-decoration-underline" @click="toggleCreditCard()">Edit</span></h3>

                                    <div class="page-section">
                                          <img v-if="creditCardCustomer&&form.cardId!=null" src="/images/trash.svg" style="float: right;" @click="DeleteCreditCardCustomer()"/>
                                          <div class="credit-card d-flex justify-content-between">
                                            <div class="form-group col-3 cardholder mb-0">
                                                <label for="">Payment method</label>
                                                <div class="w-100 py-2 rounded-2 bg-color px-3">
                                                    <span v-if="!add_payement">{{ form.paymentMethod }}&nbsp;</span>
                                                    <div  v-if="add_payement">
                                                        <select-options
                                                            v-model="form.paymentMethod"
                                                            :options="[
                                                                { display:'Credit Card', value: 'Credit Card' },
                                                                { display:'BACS', value: 'BACS' },
                                                            ]"
                                                            :placeholder="'Select'"
                                                            :name="'paymentMethod'">
                                                        </select-options>
                                                    </div>
                                                </div>
                                            </div>
                                           </div>
                                        <div class="credit-card mt-3 d-flex justify-content-between" v-if="form.paymentMethod=='Credit Card'">
                                            <div class="form-group col-3 cardholder mb-0">
                                                <label for="">Cardholder name</label>
                                                <div class="w-100 py-2 rounded-2 bg-color px-3">
                                                    <span v-if="!add_payement">{{ form.cardHolderName }}&nbsp;</span>
                                                    <input v-if="add_payement" type="text" v-model="form.cardHolderName" class="form-control" placeholder="Name">
                                                </div>
                                            </div>
                                            <div class="form-group col-4 carddetails mb-0">
                                                <label for="">Card details</label>
                                                <div class="input-group bg-color d-flex mb-0">
                                                    <span class="input-group-text border-none">
                                                        <i class="credit-card-icon" :class="cardBrand"></i>
                                                    </span>
                                                    <div class="py-2 rounded-2 bg-color px-3">
                                                    <span v-if="!add_payement">{{ form.cardDetails }}&nbsp;</span>
                                                    <input v-if="add_payement"  v-model="form.cardDetails" class="form-control" type="tel" placeholder="Enter card details">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-2 cardexpdate mb-0">
                                                <label for="">Expiration date</label>
                                                <div class="w-100 py-2 rounded-2 bg-color px-3">
                                                    <span v-if="!add_payement">{{ form.cardExpDate }}&nbsp;</span>
                                                    <input v-if="add_payement" type="text" v-model="form.cardExpDate" class="form-control"  placeholder="mm/yy">
                                                </div>
                                            </div>
                                            <div class="form-group col-2 cardexpdate mb-0">
                                                <label for="">CVV</label>
                                                <div class="w-100 py-2 rounded-2 bg-color px-3">
                                                    <span v-if="!add_payement">{{ form.cardCVV }}&nbsp;</span>
                                                    <input v-if="add_payement" type="text" v-model="form.cardCVV" class="form-control" placeholder="CVV">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-end mt-4" v-if="add_payement && form.paymentMethod=='Credit Card'">
                                            <div class="col-2">
                                                <button class="btn btn-dark w-100" @click="AddCreditCardCustomer()">Save</button>
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
                                                            :modelValue="form.companyPhoneCountryCode"
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
                                                    <label for="discount_credit">Discount Level (%)</label>
                                                    <!--
                                                    <div class="w-100 py-2 bg-color px-3 rounded-3">
                                                        {{ form.discountLevel }}%
                                                    </div>
                                                    -->
                                                    <div class="form-group">
                                                        <input :disabled="current_user && ![1,4].includes(current_user.role_id)"  type="text" class="form-control w-auto" v-model="form.discountLevel" @keyup="setCustomerDiscount"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group mb-0 payment-method">
                                                    <label for="discount_credit">Credit amount</label>
                                                    <div class="w-100 py-2 bg-color px-3 rounded-3">
                                                        <b>£</b> {{ form.discountCredit }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4" v-if="current_user && [1,4].includes(current_user.role_id)">
                                                <div class="form-group col-6">
                                                    <label for="add_credit">Add credit</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text fw-bold">£</span>
                                                        <input :disabled="current_user && ![1,4].includes(current_user.role_id)" type="text" v-model="credit_to_add"  class="form-control" id="add_credit" placeholder="0.00" @keyup="addCustomerCredit">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="new-invoice-details-panel" v-if="form.accountType !='Sub' && form.CustomerPayemenProfile == 1">
                                <!-- <div class="invoice-details-panel" v-if="form.accountType !='Main' && form.CustomerPayemenProfile == 1 "> -->
                                    <h3 class="title d-flex">
                                        Invoice details
                                        <CheckBox  v-model="form.receiptToVatInvoice" class="ms-5"><slot>Attach e-Receipts to VAT Invoice</slot></CheckBox>
                                    </h3>
                                    <div class="page-section bacs">
                                        <div class="form-group mb-0 company-legal-name">
                                            <label for="company_legal_name">Company legal name</label>
                                            <input type="text" v-model="form.companyName" class="form-control">
                                        </div>
                                        <div class="d-flex mt-3">
                                            <div class="customer-contact w-55 d-flex justify-content-between">
                                                <div class="form-group m-0">
                                                    <label class="form-label d-block m-0" for="first_name">Invoice recipient*</label>
                                                    <input type="text" v-model="form.invoiceName " class="form-control custom-input" placeholder="First name">
                                                </div>
                                                <div class="form-group m-0">
                                                    <label class="form-label d-block m-0" for="first_name">&nbsp;</label>
                                                    <input type="text" v-model="form.invoiceFirstName " class="form-control custom-input" placeholder="Last name">
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
                                                            :modelValue="form.companyPhoneCountryCode"
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
                                        <!-- ////// -->
                                        <div class="d-flex mt-3">
                                            <div class="customer-contact w-55 justify-content-between">
                                                    <label class="form-label d-block m-0" for="first_name">Invoice Email address 1*</label>
                                                    <input type="text" v-model="form.invoiceAddressEmail1 " class="form-control custom-input" placeholder="Invoice Email address 1">
                                            </div>
                                        </div>
                                        <div class="d-flex mt-3">
                                            <div class="customer-contact w-55 justify-content-between">
                                                    <label class="form-label d-block m-0" for="first_name">Invoice Email address 2</label>
                                                    <input type="text" v-model="form.invoiceAddressEmail2" class="form-control custom-input" placeholder="Invoice Email address 2">
                                            </div>

                                        </div>
                                        <!-- /////// -->
                                    </div>
                                </div>
                                <div class="billing-address-panel" v-if="form.accountType !='Sub' && form.CustomerPayemenProfile == 1">
                                <!-- <div class="invoice-details-panel" v-if="form.accountType !='Main' && form.CustomerPayemenProfile == 1 "> -->
                                    <h3 class="title d-flex">
                                        Billing address
                                        <span class="gotham-rounded-book primary-color ms-3 font-16 cursor-pointer text-decoration-underline" @click="billing_address_edit = !billing_address_edit">Edit</span>
                                    </h3>
                                    <div class="page-section bacs">
                                        <div class="form-group mb-0 company-legal-name">
                                            <label for="company_legal_name" >Billing address *</label>
                                            <input type="text" v-model="form.billingAddress1" class="form-control" placeholder="Address 1" v-if="billing_address_edit" >
                                            <div v-else class="w-100 py-2 rounded-3 bg-color px-3">
                                                    {{ form.billingAddress1 }} &nbsp;
                                            </div>
                                        </div>
                                        <div class="form-group mb-0 mt-3 company-legal-name">
                                            <input type="text" v-model="form.billingAddress2" class="form-control" placeholder="Address 2" v-if="billing_address_edit">
                                            <div v-else class="w-100 py-2 rounded-3 bg-color px-3">
                                                    {{ form.billingAddress2 }} &nbsp;
                                            </div>
                                        </div>

                                        <div class="d-flex mt-3">
                                            <div class="w-55 d-flex justify-content-between">
                                                <div class="form-group m-0">
                                                    <label for="post_code">Search postcode *</label>
                                                    <div class="input-group" v-if="billing_address_edit">
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
                                                        <input v-if="billing_address_edit" type="text" ref="companyPostCode" class="form-control custom-input" v-model="form.billingPostcode">
                                                    </div>
                                                    <div v-else class="customer-type py-2 bg-color rounded-3">
                                                    {{ form.billingPostcode }} &nbsp;
                                                   </div>
                                                </div>
                                                <div class="form-group m-0">
                                                    <label for="customer_city">City *</label>
                                                    <input v-if="billing_address_edit" type="text" v-model="form.billingCity" class="form-control custom-input">
                                                    <div v-else class="customer-type py-2 bg-color rounded-3">
                                                        {{ form.billingCity }} &nbsp;
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-45 d-flex justify-content-end mt-3" v-if="billing_address_edit">
                                                <button style="height:46px" class="btn btn btn-success each-save-btn" @click="validateAndSaveBillingAddress">Save</button>
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
                                            <th>Created on</th>
                                            <th>By</th>
                                            <th>Delivery date</th>
                                            <th>Items</th>
                                            <th>Order Status</th>
                                            <th>Total</th>
                                            <th class="text-center">E-Reciept</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in currentOrders" :key="index" @click="selectrow(item)">
                                            <td valign="middle">{{ item.order_id }}</td>
                                            <td valign="middle">{{ item.destination }}</td>
                                            <td valign="middle">{{ item.items_received }}</td>
                                            <td></td>
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
                                        <tr v-for="(item, index) in pastOrders" :key="index" @click="selectrow(item)">
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
                                                        <!--<pre>{{item}}</pre>-->
                                                        <h4 class="sub-title col-12">{{ item.title }}</h4>
                                                        <p class="m-0 col-12">{{ item.description }}</p>
                                                    </div>
                                                    <div class="col-3" :class="item.type == 'switch' ? 'd-flex justify-content-end align-items-start': ''">
                                                        <switch-btn class="ms-auto each-cust-pref" :data-category="group.key" v-if="item.type == 'switch'" v-model="item.value" :data-index="item.id" :data-value="item.value"></switch-btn>
                                                        <div class="preference-radio" v-else>
                                                            <label class="custom-radio" v-for="(value, key) in item.dropdown_values.split('\r\n')" :key="key">{{ value }}
                                                                <input type="radio" :checked="item.value == value ? true : false" :value="value" v-model="item.value" :name="'pref_'+item.id" class="each-cust-pref" :data-category="group.key" :data-index="item.id" :data-value="item.value">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="item-block py-4 border-bottom">
                                                <button class="btn btn-success each-save-btn" @click="saveCustomerPreferences(group.key)">Save {{group.key.toLowerCase()}}</button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="blocks">
                                        <h3 class="title mb-3">Recurring Booking</h3>
                                        <div class="page-section" >
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <div class="col-8">
                                                        <h4 class="sub-title col-12">Recurring</h4>
                                                    </div>
                                                    <div class="col-3 d-flex justofy-content-end">
                                                        <switch-btn class="ms-auto" v-model="form.deliveryByday"></switch-btn>
                                                    </div>
                                                </div>
                                            </div>
                                                <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
                                            <div class="set-booking-pannel " v-if="viewRecurring == false"> <!--  v-if="viewRecurring == false" -->
                                                <div class="item-block p-3 border-bottom" v-if="form.deliveryByday == '1'">
                                                    <h4 class="sub-title col-12">Pickup & delivery days</h4>
                                                    <p>Please pick number of days we will visit the customer, with matching slots.</p>
                                                    <div class="d-flex p-2">
                                                        <div class="pickup-day rounded-circle"
                                                            v-for="(pickupDay, index) in pickupDays" :key="index"
                                                            :class="{ 'active': pickupDay.active }"
                                                            @click="addPickupDay(index)"
                                                            >
                                                            {{ pickupDay.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="item-block p-3 border-bottom" v-if="form.deliveryByday == '1'">
                                                    <h4 class="sub-title col-12" v-if="!Array.isArray(timeslots)">Pickup & delivery slots </h4>
                                                    <div class="d-flex flex-wrap" v-if="Array.isArray(timeslots) == false">
                                                        <div class="col-4 px-1 mt-2" v-for="(slot, index) in form.pickupSlots" :key="index">
                                                            <select-options
                                                                v-model="slot.value"
                                                                :options="timeslots[slot.key]"
                                                                :placeholder="'Select'"
                                                                :label="slot.label"
                                                                :name="slot.label">
                                                            </select-options>
                                                        </div>
                                                    </div>
                                                    <div v-if="form.pickupSlots.length > 0 && Array.isArray(timeslots) == true">
                                                        <p v-if="form.pickupSlots.length > 0">Dates Recurring :</p>
                                                        <div  class="item-block d-flex py-3 px-5 border-bottom bg-color" :class="{ 'border-bottom': (index < form.pickupSlots) }" v-for="(slot, index) in form.pickupSlots" :key="index">
                                                            <div class="col-6 fw-bold"> {{ slot.day }} </div>
                                                            <div class="col-6 fw-bold text-end"> {{  getSlotDisplay(slot.value) }} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-100 p-3"  v-if="form.deliveryByday == '1' && Array.isArray(timeslots) == false ">
                                                    <button class="btn btn-success each-save-btn" @click="saveRecurring">Save Recurring</button>
                                                </div>
                                            </div>
                                                </transition>
                                                 <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
                                            <div class="" style="padding:2rem;" v-if="viewRecurring == true && form.deliveryByday == '1'">
                                                <h4 class="sub-title col-12">{{form.firstName!=null&&form.firstName.trim()!=''?`${form.firstName}’s `:''}}recurring booking is <span class="primary-color"><span v-if="form.pickupSlots.length==1">once</span><span v-if="form.pickupSlots.length==2">twice</span><span v-if="form.pickupSlots.length==3">three times</span><span v-if="form.pickupSlots.length==4">four times</span><span v-if="form.pickupSlots.length==5">five times</span><span v-if="form.pickupSlots.length==6">six times</span> week</span></h4>
                                                <div class="item-block d-flex py-3 px-5 border-bottom bg-color" :class="{ 'border-bottom': (index < form.pickupSlots.length-1) }" v-for="(slot, index) in form.pickupSlots" :key="index">
                                                    <div class="col-6 fw-bold"> {{ slot.day }} </div>
                                                    <div class="col-6 fw-bold text-end"> {{  getSlotDisplayByValue(slot.key, slot.value) }} </div>
                                                </div>
                                                <div class="w-100 mt-3" v-if="pauseRecurring">
                                                    <p class="fw-bold">{{form.pauseDateFrom!=''?`Paused from: ${fdate(form.pauseDateFrom)}`:'' }} {{form.pauseDateFrom==''&&form.pauseDateTo!=''?`Paused till: ${fdate(form.pauseDateTo)}`:form.pauseDateTo!=''?`to ${fdate(form.pauseDateTo)}`:``}}</p>
                                                </div>
                                                <div class="d-flex gap-3 mt-3 justify-content-end">
                                                     <div class="">
                                                          <button class="btn  btn-outline-danger" @click="form.deliveryByday=0">Cancel</button>
                                                     </div>
                                                    <div class="">
                                                    <button class="btn btn-dark each-save-btn " v-if="!pauseRecurring" @click="openPauseRecurringModal">Pause</button>
                                                    <button class="btn  btn-dark each-save-btn" v-else @click="unpauseRecurringFunc">Unpause</button>
                                                    </div>
                                                </div>
                                            </div>
                                                 </transition>
                                        </div>
                                    </div>
                                    <div class="blocks">
                                        <h3 class="title mb-3">Delivery instructions <span class="gotham-rounded-book primary-color ms-3 font-16 cursor-pointer text-decoration-underline" @click="delivery_instructions_edit = !delivery_instructions_edit">Edit</span></h3>
                                        <div class="page-section p-4">
                                            <div class="d-flex justify-content-between">
                                                <div class="payment-method">
                                                    <div class="form-group mb-0">
                                                        <label for="">Deliver to</label>
                                                        <div class="form-group m-0" v-if="delivery_instructions_edit">
                                                            <input type="text" class="form-control custom-input" v-model="form.altTypeDelivery"/>
                                                        </div>
                                                        <div class="w-100 bg-color py-2 rounded-3 px-3" v-else>
                                                            {{ form.altTypeDelivery }} &nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-0 payment-method">
                                                    <label for="">Name</label>
                                                    <div class="form-group m-0" v-if="delivery_instructions_edit">
                                                            <input type="text" class="form-control custom-input" v-model="form.altName"/>
                                                        </div>
                                                    <div class="w-100 bg-color py-2 rounded-3 px-3" v-else>
                                                        {{ form.altName }} &nbsp;
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="w-100">
                                                    <label>Phone Number</label>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="phone-country-code" v-if="delivery_instructions_edit">
                                                        <select-options
                                                            v-model="form.altPhoneCountryCode"
                                                            :modelValue="form.altPhoneCountryCode"
                                                            :options="phoneCodesSorted"
                                                            :width = "'100px'"
                                                            :name="'altPhoneCountryCode'">
                                                        </select-options>
                                                    </div>
                                                    <div class="phone-country-code" v-else>
                                                        <div class="w-100 bg-color py-2 px-4 rounded-3">
                                                            {{ form.altPhoneCountryCode }} &nbsp;
                                                        </div>
                                                    </div>
                                                    <div class="form-group payment-method ms-2">
                                                        <div class="form-group m-0" v-if="delivery_instructions_edit">
                                                            <input type="text" class="form-control custom-input" v-model="form.altPhoneNumber"/>
                                                        </div>
                                                        <div class="w-100 bg-color py-2 rounded-3 px-3" v-else>
                                                            {{ form.altPhoneNumber }} &nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="form-group m-0">
                                                    <label for="" class="form-label d-block m-0">Driver instructions</label>
                                                    <textarea name="customer_note" rows="4" class="form-control" v-model="form.altDriverInstruction" :readonly="!delivery_instructions_edit" :class="{'bg-color':!delivery_instructions_edit,'border-none':!delivery_instructions_edit}"></textarea>
                                                </div>
                                            </div>
                                            <div class="w-100 pt-4" v-if="delivery_instructions_edit">
                                                <button class="btn btn-success each-save-btn" @click="saveDeliveryInstructions">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="blocks"></div>
                                    <div class="blocks">
                                        <h3 class="title mb-3">Communication</h3>
                                        <div class="page-section">
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex">
                                                    <div class="col-9">
                                                        <h4 class="sub-title col-12">Marketing</h4>
                                                        <p class="m-0 col-12">Customer agrees to receiving Marketing emails from us</p>
                                                    </div>
                                                    <div class="col-3 d-flex align-items-center">
                                                        <switch-btn class="ms-auto each-cust-pref" v-model="form.acceptSMSMarketing"></switch-btn>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="item-block py-3">
                                                <div class="d-flex mt-3">
                                                    <div class="col-9">
                                                        <h4 class="sub-title col-12">Bi-Monthly VAT Invoices</h4>
                                                        <p class="m-0 col-12">Customer wishes to receive monthly email VAT receipts</p>
                                                    </div>
                                                    <div class="col-3 d-flex align-items-center">
                                                        <switch-btn class="ms-auto each-cust-pref" v-model="form.acceptMarketing"></switch-btn>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-100 p-4">
                                                <button class="btn btn-success each-save-btn" @click="saveCommunication">Save Communication</button>
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
                                                <td valign=middle class="text-nowrap link_account"><a href="javascript:void(0)" @click="goCustomerView(item.id)">{{ item.lastName }}, {{item.firstName}}</a></td>
                                                <td valign=middle class="text-nowrap fw-bold">{{ item.accountType }}</td>
                                                <td valign=middle class="text-nowrap">{{ formatPhone(item.phone) }}</td>
                                                <td valign=middle class="text-nowrap">{{ item.email }}</td>
                                                <td valign=middle class="text-nowrap">{{ item.date }}</td>
                                                <td valign=middle class="fw-bold text-nowrap">£ {{ item.spent }}</td>
                                                <td valign=middle>
                                                    <svg v-if="form.accountType == 'Main'" width="30" height="30" fill="#47454B" @click="removeLinkedAccount(item.id)" class="unlink cursor-pointer" version="1.1" viewBox="0 0 700 700" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                                        <g>
                                                        <path d="m452.2 362.38 56.953 56.953-19.824 19.824-74.871-74.871h-47.656v-32.312h15.68l-35.727-35.168h-63.676v-33.039h30.574l-35.449-35.449h-12.203c-13.785-0.042969-27.016 5.4102-36.77 15.148-9.75 9.7422-15.219 22.969-15.191 36.754 0.027344 13.781 5.5469 26.988 15.336 36.688 9.6133 9.8555 22.863 15.305 36.625 15.066h67.762v32.312h-67.762c-22.273 0.33203-43.676-8.6641-59.023-24.809-15.648-15.84-24.426-37.207-24.426-59.473s8.7773-43.633 24.426-59.473c11.098-11.586 25.414-19.586 41.102-22.961l-57.23-56.895 19.824-19.824 74.871 75.152h0.39062l32.312 32.312h-0.44922l35.449 35.449 33.32 33.039 35.449 35.449 30.465 30.07zm41.105-22.902c15.648-15.84 24.426-37.207 24.426-59.473s-8.7773-43.633-24.426-59.473c-15.398-16.043-36.793-24.934-59.023-24.527h-67.48v32.312h67.762-0.003906c13.785-0.042969 27.016 5.4102 36.77 15.148 9.75 9.7422 15.219 22.969 15.191 36.754-0.027343 13.781-5.5469 26.988-15.336 36.688-3.5586 3.5781-7.5898 6.6484-11.984 9.1289l23.297 23.238v0.003907c3.8672-2.9609 7.4844-6.2383 10.809-9.8008z"/>
                                                        </g>
                                                    </svg>
                                                    <svg v-if="form.accountType == 'Sub'" width="30" height="30" fill="#47454B" @click="removeLinkedAccount(form.id_customer)" class="unlink cursor-pointer" version="1.1" viewBox="0 0 700 700" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                                        <g>
                                                        <path d="m452.2 362.38 56.953 56.953-19.824 19.824-74.871-74.871h-47.656v-32.312h15.68l-35.727-35.168h-63.676v-33.039h30.574l-35.449-35.449h-12.203c-13.785-0.042969-27.016 5.4102-36.77 15.148-9.75 9.7422-15.219 22.969-15.191 36.754 0.027344 13.781 5.5469 26.988 15.336 36.688 9.6133 9.8555 22.863 15.305 36.625 15.066h67.762v32.312h-67.762c-22.273 0.33203-43.676-8.6641-59.023-24.809-15.648-15.84-24.426-37.207-24.426-59.473s8.7773-43.633 24.426-59.473c11.098-11.586 25.414-19.586 41.102-22.961l-57.23-56.895 19.824-19.824 74.871 75.152h0.39062l32.312 32.312h-0.44922l35.449 35.449 33.32 33.039 35.449 35.449 30.465 30.07zm41.105-22.902c15.648-15.84 24.426-37.207 24.426-59.473s-8.7773-43.633-24.426-59.473c-15.398-16.043-36.793-24.934-59.023-24.527h-67.48v32.312h67.762-0.003906c13.785-0.042969 27.016 5.4102 36.77 15.148 9.75 9.7422 15.219 22.969 15.191 36.754-0.027343 13.781-5.5469 26.988-15.336 36.688-3.5586 3.5781-7.5898 6.6484-11.984 9.1289l23.297 23.238v0.003907c3.8672-2.9609 7.4844-6.2383 10.809-9.8008z"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="d-flex " v-if="form.accountType == 'Main'">
                                        <button @click="createSubAccount" class="border-btn add-new-account d-flex justify-content-between align-items-center me-3">
                                            <svg class="me-3" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="12" fill="black"/>
                                                <path d="M11.0897 14.472C11.0897 14.984 11.4897 15.384 12.0017 15.384C12.4977 15.384 12.9137 14.984 12.9137 14.472V12.248H15.1857C15.6657 12.248 16.0657 11.864 16.0657 11.368C16.0657 10.888 15.6657 10.488 15.1857 10.488H12.9137V8.264C12.9137 7.752 12.4977 7.352 12.0017 7.352C11.4897 7.352 11.0897 7.752 11.0897 8.264V10.488H8.81769C8.33769 10.488 7.93769 10.888 7.93769 11.368C7.93769 11.864 8.33769 12.248 8.81769 12.248H11.0897V14.472Z" fill="white"/>
                                            </svg>
                                            Create a SubAccount
                                        </button>
                                        <button @click="showSearchPanel(0)" class="border-btn add-existing-account d-flex justify-content-between align-items-center">
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
                                            Add existing SubAccount
                                        </button>
                                        <Search ref="searchpanel" :customerID = "form.customerID"  @selectedSubAccount="selectedSubAccount"/>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <modal ref="pauseRecurringModal">
        <template #bheader>
            <div class="bmodal-header py-5 text-center">Pause this reccuring booking</div>
        </template>
        <template #bcontent>
            <div class="bmodal-content p-4">
                <p class="text-center">You can pause this reccuring booking anytime - please note the cut-off time to cancel a recurring booking is 4pm on the day before the scheduled date.</p>
                <p class="fw-bold mt-3 text-center">Simply enter the dates when you'd like the recurring booking to be paused here below:</p>
                <div class="d-flex mt-3">
                    <div class="form-group col-6">
                        <date-picker v-model="form.pauseDateFrom" name="start_date" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Pause Starts" :disabledToDate="getCurDateTime()"></date-picker>
                    </div>
                    <div class="form-group col-6">
                        <date-picker v-model="form.pauseDateTo" name="end_date" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Pause Ends" :disabledToDate="getCurDateTime()"></date-picker>
                    </div>
                </div>
            </div>
        </template>
        <template #mbuttons>
            <div class="row mx-0 justify-content-center my-5 py-3">
                <div class="col-3"><button class="btn btn-outline-success w-100" @click="pauseRecurringFunc">Pause</button></div>
                <div class="col-2"></div>
                <div class="col-3"><button class="btn btn-outline-danger w-100" @click="closePauseRecurringModal">Cancel</button></div>
            </div>
        </template>
    </modal>
    <new-sub-account-form :phoneCodesSorted="phoneCodesSorted" :form="form" :show_conf="show_model_SubAccount" @close="show_model_SubAccount=false"></new-sub-account-form>
</template>

<script>
    import BreadCrumb from '../layout/BreadCrumb'
    import SideBar from '../layout/SideBar'
    import { useRouter, useRoute } from 'vue-router';
    import {ref,onMounted, nextTick, computed, watch ,inject,onUpdated } from 'vue';
    import SelectOptions from '../test/SelectOptions';
    import MultipleEmail from '../test/MultipleEmail';
    import CheckBox from '../miscellaneous/CheckBox';
    import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
    import SwitchBtn from '../miscellaneous/SwitchNumberBtn.vue';
    import Search from './Search';
    import Modal from '../miscellaneous/Modal.vue';
    import DatePicker from '../miscellaneous/DatePicker.vue';
    import NewSubAccountForm from '../miscellaneous/NewSubAccountForm.vue';
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
            Modal,
            DatePicker,
            NewSubAccountForm
        },
        setup(props,context){
            const route = useRoute();
            const store = useStore();
            const show_model_SubAccount = ref(false);
            const form = ref({
                customerID: '',
                id_customer:'',
                Name: '',
                accountType: 'Main',
                customerType: '',
                typeDelivery: '',
                programmeType: '',
                CustomerPayemenProfile: '',
                CompanyName: '',
                kioskNumber: '',
                firstName: '',
                lastName: '',
                phoneCountryCode: '+44',
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
                acceptSMSMarketing: 1,
                acceptMarketing: 0,
                // payment tab
                alreadyLinkedToAccount: true,
                paymentMethod: '',
                cardHolderName: '',
                cardDetails: '',
                cardExpDate: '',
                cardCVV: '',
                cardId:'',
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
                //billing address
                billingAddress1:'',
                billingAddress2:'',
                billingPostcode:'',
                billingCity:'',
                //invoice details
                companyNme:'',
                invoiceAddressEmail1:'',
                invoiceAddressEmail2:'',
                invoiceFirstName:'',
                invoiceName:'',
                // preferences tab
                preferences: [],
                deliveryByday: '0',
                pauseDateTo: '',
                pauseDateFrom: '',
                pickupSlots:[],

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
            const postcode = ref();
            const cardBrand = ref('cc-unknown');
            const contact_details_edit = ref(false);
            const pauseRecurringModal = ref(null);
            const add_payement = ref(false);
            const address_edit = ref(false);
            const customer_note_edit = ref(false);
            const delivery_instructions_edit = ref(false);
            const companyPostCode = ref();
            const searchpanel = ref(null);
            const showcontainer=ref(false);
            const searchCustomer=ref(false);
            const creditCardCustomer=ref(false);
            const currentOrders=ref([]);
            const pastOrders=ref([]);
            const timeout =ref('');
            let name_regex = /^[a-zA-Z ]*$/;
            const cardErrors = ref({});
            const cardFormat = inject('cardFormat');
            const paths=ref([
                { name:'Customer', route:'Customer'},
            ]);

            const viewRecurring = ref(true);
            const pauseRecurring = ref(false);
            const current_user = ref({});
            const credit_to_add = ref(0);
            const billing_address_edit = ref(false);

            const timeslot_def=ref([
                {
                    value:1,
                    display:'8-10 am',
                    available:false
                },
                {
                    value:3,
                    display:'10-12 pm',
                    available:false
                },
                {
                    value:5,
                    display:'12-2 pm',
                    available:false
                },
                {
                    value:7,
                    display:'2-4 pm',
                    available:false
                },
                {
                    value:9,
                    display:'4-6 pm',
                    available:false
                },
                {
                    value:11,
                    display:'6-8 pm',
                    available:false
                },
                {
                    value:13,
                    display:'8-8 pm',
                    available:true
                }
            ]);


            onMounted(()=>{
                nextTick(()=>{
                    showcontainer.value=true;
                });

                getCustomerFullDetail();

                axios.post('/get-current-user',{})
                    .then((res)=>{
                        current_user.value = res.data.user;
                    }).catch((err)=>{

                    }).finally(()=>{

                    });

                setTimeout(() => {
                    /*
                    const customerAddress = new google.maps.places.Autocomplete(postcode.value);
                    customerAddress.addListener("place_changed", () => {
                        const place = customerAddress.getPlace();
                        setCustomerAddress(place.address_components);
                    });
                    //*/
                }, 500);
            });
            const getCustomerFullDetail = ()=>{
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading customer data...']);
                axios.post('/get-customer-full-detail', {
                    customer_id: route.params.customer_id
                }).then((res)=>{
                    // linked accounts
                    if(res.data.customer.accountType == "Main"){
                           const filteredListSub = res.data.customer.linkedAccounts.filter((e) => e.accountType === "Sub");
                           form.value.linkedAccounts = filteredListSub;
                        }
                    else if(res.data.customer.accountType == "Sub"){
                           const filteredListMain = res.data.customer.linkedAccounts.filter((e) => e.accountType === "Main");
                           form.value.linkedAccounts = filteredListMain;
                    }

                    form.value.CustomerPayemenProfile = res.data.customer.OnAccount
                    form.value.CompanyName = res.data.customer.CompanyName
                    form.value.id_customer = res.data.customer.id

                    if(res.data.customer.card == null){
                        creditCardCustomer.value = false ;
                    } else {
                        creditCardCustomer.value = true ;
                    }

                    if( res.data.customer.billing){
                            form.value.billingAddress1 =  res.data.customer.billing.address1;
                            form.value.billingAddress2 = res.data.customer.billing.address2;
                            form.value.billingCity =  res.data.customer.billing.Town;
                            form.value.billingPostcode = res.data.customer.billing.postcode;
                    };
                    if( res.data.customer.invoice){
                            form.value.companyNme =  res.data.customer.invoice.company;
                            form.value.invoiceName = res.data.customer.invoice.name;
                            form.value.invoiceFirstName = res.data.customer.invoice.firstname;
                            form.value.invoiceAddressEmail1 =  res.data.customer.invoice.email;
                            form.value.invoiceAddressEmail2 = res.data.customer.invoice.email;
                    };

                    form.value.discountCredit = res.data.customer.credit;
                    form.value.creditAmount = res.data.customer.credit;
                    form.value.customerID = res.data.customer.CustomerID;
                    form.value.Name = res.data.customer.Name;
                    form.value.booking = res.data.customer.booking;
                    form.value.totalSpent = res.data.customer.totalSpent;
                    form.value.accountType = res.data.customer.accountType;
                    form.value.customerType = res.data.customer.customerType;
                    form.value.typeDelivery = res.data.customer.typeDelivery;
                    form.value.programmeType = res.data.customer.programmeType;
                    form.value.kioskNumber = res.data.customer.kioskNumber;
                    form.value.firstName = res.data.customer.firstName;
                    form.value.lastName = res.data.customer.lastName;
                    var phone = getPhone(res.data.customer.phone);

                    if(phone.code.includes('+')){
                        form.value.phoneCountryCode = phone.code;
                    }else {
                        form.value.phoneCountryCode = "+"+phone.code;
                    }

                    form.value.phoneNumber = phone.number;
                    form.value.email = res.data.customer.email;
                    // address part in account details tab
                    if(res.data.customer.address!=null){
                    form.value.postCode = res.data.customer.address.postCode;
                    form.value.city = res.data.customer.address.city;
                    form.value.state = res.data.customer.address.state;
                    form.value.county = res.data.customer.address.county;
                    form.value.country = res.data.customer.address.country;
                    form.value.deliveryAddress1 = res.data.customer.address.address1;
                    form.value.deliveryAddress2 = res.data.customer.address.address2;
                    }
                    form.value.customerNote = res.data.customer.CustomerNotes;
                    // payment tab

                    form.value.paymentMethod  = res.data.customer.paymentMethod == 1 ? 'Credit Card' : 'BACS';

                    if(res.data.customer.paymentMethod == 1 && res.data.customer.card){

                        form.value.cardHolderName =  res.data.customer.card.cardHolderName;
                        form.value.cardDetails = res.data.customer.card.cardNumber;
                        form.value.cardExpDate =  res.data.customer.card.expDate;
                        form.value.cardId = res.data.customer.card.id;
                        form.value.cardCVV = '***';
                        if(res.data.customer.card.type == 'Visa'){
                            cardBrand.value = 'cc-visa';
                        }else if(res.data.customer.card.type == 'Mastercard'){
                            cardBrand.value = 'cc-mastercard';
                        }else if(res.data.customer.card.type == 'Amex'){
                            cardBrand.value = 'cc-amex';
                        }else if(res.data.customer.card.type == 'discover'){
                            cardBrand.value = 'cc-discover';
                        }else {
                             cardBrand.value = 'cc-unknown';
                        }
                    };
                    currentOrders.value = res.data.customer.currentOrders;
                    pastOrders.value = res.data.customer.pastOrders;
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
                    form.value.discountLevel = res.data.customer.discount;
                    // applyDiscountToSub: false,


                    // // preferences tab
                    Object.keys(res.data.customer.preferences).forEach((item)=>{
                        form.value.preferences.push({
                            data: res.data.customer.preferences[item],
                            key: item,
                        })
                    })
                    if(res.data.customer.deliveryPreferences){
                        form.value.altTypeDelivery = res.data.customer.deliveryPreferences.altTypeDelivery;
                        form.value.altName = res.data.customer.deliveryPreferences.altName;
                        form.value.altPhoneCountryCode = res.data.customer.deliveryPreferences.altPhoneCountryCode;
                        form.value.altPhoneNumber = res.data.customer.deliveryPreferences.altPhoneNumber;
                        form.value.altDriverInstruction = res.data.customer.deliveryPreferences.altDriverInstruction;
                    }
                    form.value.acceptMarketing = res.data.customer.acceptMarketing.toString();
                    form.value.acceptSMSMarketing = res.data.customer.acceptSMSMarketing.toString();
                    form.value.deliveryByday = res.data.customer.deliveryByDay.toString();
                    form.value.pauseDateTo = res.data.customer.pauseDateTo ?? "";
                    form.value.pauseDateFrom = res.data.customer.pauseDateFrom ?? "";
                    if(form.value.deliveryByday == '1'){
                        if((form.value.pauseDateFrom != '' && (new Date(form.value.pauseDateFrom) > new Date() ))|| (form.value.pauseDateTo != '' && (new Date(form.value.pauseDateTo) > new Date()))){       
                            pauseRecurring.value = true;
                        }
                        pickupDays.value = res.data.available_days;
                        timeslots.value = res.data.available_slots;
                        if(res.data.customer.DeliveryMon){
                            form.value.pickupSlots.push({
                                value: res.data.customer.DeliveryMon,
                                day: 'Monday',
                                key: 'DeliveryMon',
                                label: 'Select Mon slot',
                            })
                            if(pickupDays.value.length > 0){
                                pickupDays.value[0].active = true
                            } 
                        }
                        if(res.data.customer.DeliveryTu){
                            form.value.pickupSlots.push({
                                value: res.data.customer.DeliveryTu,
                                day: 'Tuesday',
                                key: 'DeliveryTu',
                                label: 'Select Tue slot',
                            })
                            if(pickupDays.value.length > 0){
                                pickupDays.value[1].active = true
                            }
                        }
                        if(res.data.customer.DeliveryWed){
                            form.value.pickupSlots.push({
                                value: res.data.customer.DeliveryWed,
                                day: 'Wednesday',
                                key: 'DeliveryWed',
                                label: 'Select Wed slot',
                            })
                            if(pickupDays.value.length > 0){
                                pickupDays.value[2].active = true
                            }
                        }
                        if(res.data.customer.DeliveryTh){
                            form.value.pickupSlots.push({
                                value: res.data.customer.DeliveryTh,
                                day: 'Thursday',
                                key: 'DeliveryTh',
                                label: 'Select Thu slot',
                            })
                            if(pickupDays.value.length > 0){
                                pickupDays.value[3].active = true
                            }
                        }
                        if(res.data.customer.DeliveryFri){
                            form.value.pickupSlots.push({
                                value: res.data.customer.DeliveryFri,
                                day: 'Friday',
                                key: 'DeliveryFri',
                                label: 'Select Fri slot',
                            })
                            if(pickupDays.value.length > 0){
                                pickupDays.value[4].active = true
                            }
                        }
                        if(res.data.customer.DeliverySat){
                            form.value.pickupSlots.push({
                                value: res.data.customer.DeliverySat,
                                day: 'Saturday',
                                key: 'DeliverySat',
                                label: 'Select Sat slot',
                            })
                            if(pickupDays.value.length > 0){
                                pickupDays.value[5].active = true
                            }
                        }
                    }else{
                        viewRecurring.value = false;
                    }

                    paths.value.push(
                        { name: (res.data.customer.firstName==null?'':res.data.customer.firstName) +' ' + res.data.customer.lastName , route:'ViewCustomer', params:{ customer_id: res.data.customer.id }}
                    );

                    //current_user.value = res.data.customer.current_user;
                }).catch((error)=>{
                    //console.log(error);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })
            }

            const getSlotDisplay = (value)=>{
                console.log(value[0])
                const temp = timeslot_def.value.filter((item) => { return item.value == value[0]})[0];
                if( temp ){
                    return temp.display;
                }else{
                    return '';
                }
            }

            const getSlotDisplayByValue = (key, value)=>{
                const temp = timeslots.value[key].filter((item) => { return item.value == value })[0];
                if( temp ){
                    return temp.display;
                }else{
                    return '';
                }
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
                if(nav=='order_management'){
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading customer order details...']);
                    axios.post('/get-customer-order-details',{
                        customer_id:route.params.customer_id
                    }).then((res)=>{
                        currentOrders.value = res.data.currentOrders;
                        pastOrders.value = res.data.pastOrders;
                    }).catch((err)=>{

                    }).finally(()=>{
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
                }
            }
            const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
                            [item.value, item])).values()].sort((a, b)=>{
                    return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
            });
            watch(()=>form.value.paymentMethod,(current_value, previous_value)=>{
                if(current_value == 'BACS'){
                    nextTick(() => {
                        /*
                        const companyAddress = new google.maps.places.Autocomplete(companyPostCode.value);
                        companyAddress.addListener("place_changed", () => {
                            const place = companyAddress.getPlace();
                            setCompanyAddress(place.address_components);
                        });
                        */
                    });
                }
            })
            // handler when you click search customer button
            const showSearchPanel = (type)=>{
                searchpanel.value.openSearchPanel(type);
            }
            // handler when the customer selected in search result
            const selectedSubAccount = (data)=>{
                form.value.linkedAccounts = [...form.value.linkedAccounts,
                    {
                        id:     data.id,
                        name:   data.name,
                        firstName : data.firstName,
                        lastName : data.lastName,
                        accountType: 'Sub',
                        phone:  data.phone,
                        email:  data.email,
                        date:   data.date,
                        spent:  data.spent
                    }
                ]
                // add function create sub account
                axios.post('/create-customer-sub-account',{
                         customer_data: data,
                         customer_id : form.value.customerID,
                         CustomerPayemenProfile : form.value.CustomerPayemenProfile,
                         typeDelivery : form.value.typeDelivery
                    }).then((res)=>{
                    }).catch((error)=>{
                        console.log(error);
                    })
            };
            // handler when you unlink sub account from linked accounts
            const removeLinkedAccount = (id)=>{
               axios.post('/unlink-Account', {
                        customer_id: id,
                    }).then((res)=>{
                        if(res.data.message == "OK"){
                            form.value.linkedAccounts = form.value.linkedAccounts.filter((item)=>{
                            return item.id != id;
                          });
                        } else {
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: res.data.error , ttl:5, type:'danger' });
                        }
                    }).catch((errors)=>{
                        console.log(errors)
                    });
            }
            const formatPhone = (phoneString)=>{
                if(phoneString != "" && phoneString != undefined){
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
               /*
               watch(()=>form.value.discountCredit,(current_value, previous_value)=>{

               clearTimeout(timeout.value);
               timeout.value = setTimeout(function(){

                    axios.post('/add-credit-customer', { credit :(current_value==''?0:current_value) , customer_id : route.params.customer_id } ).then((response)=>{
                        if(response.data==0 || response.data==1){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                            {
                                message:"Customer credit updated",
                                ttl: 3,
                                type: 'success'
                            });
                        }
                    }).catch((errors)=>{
                        console.log(errors);
                    })
                    }
                   , 500)
               })
               */

                watch(()=>form.value.cardHolderName,(current_value,previous_value)=>{
                    if(current_value == 'Visa'){
                            cardBrand.value = 'cc-visa';
                        }else if(current_value == 'Mastercard'){
                            cardBrand.value = 'cc-mastercard';
                        }else if(current_value == 'Amex'){
                            cardBrand.value = 'cc-amex';
                        }else if(current_value == 'discover'){
                            cardBrand.value = 'cc-discover';
                        }else {
                             cardBrand.value = 'cc-unknown';
                        }
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

            watch(()=>form.value.accountType,(current_value, previous_value)=>{
                form.value.accountType = current_value
            })

            // handler when you click a create sub account button
            const createSubAccount = ()=>{
                show_model_SubAccount.value = true;

            }


            function toggleCreditCard(){
                this.add_payement = !this.add_payement;
            }


            function AddCreditCardCustomer(){

                //if(form.value.cardHolderName != "" && form.value.cardDetails != ""  && form.value.cardExpDate != "" &&  form.value.cardCVV != "" && form.value.paymentMethod != ""){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Add Card Customer...']);
                axios.post('/add-credit-card', form.value ).then((res)=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    form.value.cardId =  res.data
                    creditCardCustomer.value = true
                    this.add_payement =false;
                    window.location.reload();
                }).catch((error)=>{

                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                        message:"Error saving card details",
                        ttl: 5,
                        type: 'danger'
                    });

                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })
             /*   }else{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                        message:"Credit card details missing",
                        ttl: 5,
                        type: 'danger'
                    });
                }
*/


            }

            function DeleteCreditCardCustomer(){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Delete Card Customer...']);
                axios.post('/delete-credit-card',{ id : form.value.cardId} ).then((res)=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    form.value.cardHolderName ='',
                    form.value.cardDetails ='' ,
                    form.value.cardExpDate ='',
                    form.value.cardCVV ='',
                    creditCardCustomer.value = false
                }).catch((error)=>{
                    console.log(error);
                })
            }

            function addCustomerCredit(event){
                if(event.keyCode==13){
                    if(parseInt(credit_to_add.value)){
                        axios.post('/add-credit-customer', { credit :credit_to_add.value , customer_id : route.params.customer_id } ).then((response)=>{

                                form.value.discountCredit = response.data.credit;
                                credit_to_add.value = 0;
                                    /*
                                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                                    {
                                        message:"Customer credit updated",
                                        ttl: 3,
                                        type: 'success'
                                    });
                                    */

                            }).catch((errors)=>{
                                console.log(errors);
                            })
                    }
                }
            }


            function setCustomerDiscount(event){
                if(event.keyCode==13){
                    if(parseInt(form.value.discountLevel)){
                        axios.post('/set-customer-discount',{
                            discount:form.value.discountLevel,
                            customer_id:route.params.customer_id
                        }).then((res)=>{
                            if(res.data.updated==1 || res.data.updated==0){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                    message:"Customer discount updated",
                                        ttl: 3,
                                        type: 'success'
                                });
                            }
                        }).catch((err)=>{

                        }).finally(()=>{

                        });
                    }
                }
            }

            function validateAndSaveContactDetails(){
                let err = [];

                const email_regex=/[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/g;

                if(form.value.lastName==''){
                    err.push('Please enter contact lastName');
                }

                // if(form.value.phoneNumber==''){
                //     err.push('Please enter phone number');
                // }

                if(form.value.email!='' && !email_regex.test(form.value.email.toLowerCase())){
                    err.push('Please enter a valid email address');
                }

                if(err.length > 0){
                    err.forEach(function(v,i){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: v,
                            ttl: 5,
                            type: 'danger'
                        });
                    })
                }else{
                    axios.post('/update-customer-contact',{
                        customer_id:route.params.customer_id,
                        type_delivery:form.value.typeDelivery,
                        customer_type:form.value.customerType,
                        programme_type:form.value.programmeType,
                        kiosk_number:form.value.kioskNumber,
                        firstname:form.value.firstName,
                        lastname:form.value.lastName,
                        phone_country_code:form.value.phoneCountryCode,
                        phone_num:form.value.phoneNumber,
                        email:form.value.email,
                        CustomerPayemenProfile:form.value.CustomerPayemenProfile,
                        CompanyName:form.value.CompanyName,
                    }).then((res)=>{
                        if(res.data.updated){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                message: 'Contact details updated',
                                ttl: 5,
                                type: 'success'
                            });
                            contact_details_edit.value = false;
                        }
                    }).catch((err)=>{
                        console.log(err);
                    }).finally(()=>{

                    });
                }
            }


            function validateAndSaveContactAddress(){
                let err = [];

                if(form.value.postCode==''){
                    err.push("Please enter Postcode");
                }
                if(form.value.city==''){
                    err.push("Please enter City");
                }
                if(form.value.deliveryAddress1==''){
                    err.push("Please enter Address 1");
                }

                if(err.length > 0){
                    err.forEach(function(v,i){
                         store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: v,
                            ttl: 5,
                            type: 'danger'
                        });
                    });
                }else{
                    axios.post('/update-customer-address',{
                        customer_id:route.params.customer_id,
                        postcode:form.value.postCode,
                        county:form.value.county,
                        city:form.value.city,
                        address1:form.value.deliveryAddress1,
                        address2:form.value.deliveryAddress2,
                        typedelivery:form.value.typeDelivery
                    }).then((res)=>{
                        if(res.data.new_address_id){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                message: 'Customer address updated',
                                ttl: 5,
                                type: 'success'
                            });
                            address_edit.value = false;
                        }
                    }).catch((err)=>{
                        console.log(err);
                    }).finally(()=>{

                    });

                }
            }
            setTimeout(() => {
                        const customerAddress = new google.maps.places.Autocomplete(postcode.value,
                            {
                                componentRestrictions: { country: "uk" },
                                fields: ["address_components", "geometry"],
                            }
                        );
                        customerAddress.addListener("place_changed", () => {
                            const place = customerAddress.getPlace();
                            form.value.customerLat = place.geometry.location.lat();
                            form.value.customerLon = place.geometry.location.lng();
                            setCustomerAddress(place.address_components);
                        });
            }, 1);

            function validateAndSaveBillingAddress(){
                let err = [];

                if(form.value.billingAddress1 ==''){
                    err.push("Please enter Address 1");
                }
                if(form.value.billingCity ==''){
                    err.push("Please enter City");
                }
                if(form.value.billingPostcode==''){
                    err.push("Please enter PostCode");
                }

                if(err.length > 0){
                    err.forEach(function(v,i){
                         store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: v,
                            ttl: 5,
                            type: 'danger'
                        });
                    });
                }else{
                    axios.post('/update-customer-billing-address',{
                        customer_id:route.params.customer_id,
                        postcode:form.value.billingPostcode,
                        county:form.value.county,
                        city:form.value.billingCity,
                        address1:form.value.billingAddress1,
                        address2:form.value.billingAddress2,
                        customerLat:form.value.customerLat,
                        customerLon:form.value.customerLon,
                    }).then((res)=>{
                        if(res.data){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                message: 'Customer address updated',
                                ttl: 5,
                                type: 'success'
                            });
                            billing_address_edit.value = false;
                        }
                    }).catch((err)=>{
                        console.log(err);
                    }).finally(()=>{

                    });

                }
            }

            function saveCustomerNote(){
                axios.post('/update-customer-note',{
                    customer_id:route.params.customer_id,
                    notes:form.value.customerNote,
                }).then((res)=>{
                    if(res.data.updated==1 || res.data.updated==0){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                message: 'Customer notes updated',
                                ttl: 5,
                                type: 'success'
                            });
                        customer_note_edit.value = false;
                    }
                }).catch((err)=>{

                }).finally(()=>{

                });
            }


            function saveCustomerPreferences(group){
                let els = Object.values(document.querySelectorAll('.each-cust-pref'));
                let authorisation_pref = {};
                let finish_pref = {};
                let params = {};

                if(els.length > 0){
                    els.forEach(function(el,index){
                        let category = el.getAttribute('data-category');
                        let id = el.getAttribute('data-index');
                        let value = el.getAttribute('data-value');
                        if(category=='Authorisations'){
                            authorisation_pref[id] = value;
                        }
                        if(category=='Finish instructions'){
                            finish_pref[id] = value;
                        }
                    });


                    params['customer_id'] = route.params.customer_id;

                    if(group=='Authorisations'){
                        params['preferences'] = JSON.stringify(authorisation_pref);
                    }

                    if(group=='Finish instructions'){
                        params['preferences'] = JSON.stringify(finish_pref);
                    }


                    axios.post('/save-customer-preferences',params)
                        .then((res)=>{
                            if(res.data.updated){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                    message: 'Customer '+group.toLowerCase()+' updated',
                                    ttl: 5,
                                    type: 'success'
                                });
                            }
                        }).catch((err)=>{
                            console.log(err);
                        }).finally(()=>{

                        });
                }else{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                        message: 'Error saving customer preferences',
                        ttl: 5,
                        type: 'danger'
                    });
                }
            }

            function saveDeliveryInstructions(){
                let err = [];

                /*if(form.value.altTypeDelivery==''){
                    err.push("Please enter type delivery");
                }

                if(form.value.altName==''){
                    err.push("Please enter contact name");
                }

                if(form.value.altPhoneCountryCode==''){
                    err.push("Please enter contact country code");
                }

                if(form.value.altPhoneNumber==''){
                    err.push("Please enter contact phone number");
                }*/

                if(err.length > 0){
                    err.forEach(function(err_msg,index){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: err_msg,
                            ttl: 5,
                            type: 'danger'
                        });
                    });
                }else{
                    axios.post('/save-customer-delivery-instructions',{
                        customer_id:route.params.customer_id,
                        type_delivery:form.value.altTypeDelivery,
                        name:form.value.altName,
                        country_code:form.value.altPhoneCountryCode,
                        phone_num:form.value.altPhoneNumber,
                        driver_instructions:form.value.altDriverInstruction
                    }).then((res)=>{
                        if(res.data.updated){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                message: 'Customer delivery instructions updated',
                                ttl: 5,
                                type: 'success'
                            });
                        }
                    }).catch((err)=>{

                    }).finally(()=>{

                    });
                }
            }
            const saveCommunication = ()=>{
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Updating Customer Communication...']);
                axios.post('/save-customer-communication',{
                    customerId: route.params.customer_id,
                    acceptSMSMarketing: form.value.acceptSMSMarketing,
                    acceptMarketing: form.value.acceptMarketing,
                }).then((res)=>{
                    if(res.data.success){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: 'Customer Communication updated',
                            ttl: 5,
                            type: 'success'
                        });
                    }
                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }
            const pickupDays = ref([]);
            const timeslots = ref([]);
            const addPickupDay = (index)=>{
                let pickupDay = pickupDays.value[index];
                if(pickupDay.active){
                    pickupDays.value[index].active = false;
                    form.value.pickupSlots = form.value.pickupSlots.filter((item)=>{
                        return item.key != pickupDay.key;
                    });
                }else{
                    pickupDays.value[index].active = true;
                    let day = '';
                    if(pickupDay.key == 'DeliveryMon'){
                        day = 'Monday';
                    }else if(pickupDay.key == 'DeliveryTu'){
                        day = 'Tuesday';
                    }else if(pickupDay.key == 'DeliveryWed'){
                        day = 'Wednesday';
                    }else if(pickupDay.key == 'DeliveryTh'){
                        day = 'Thursday';
                    }else if(pickupDay.key == 'DeliveryFri'){
                        day = 'Friday';
                    }else if(pickupDay.key == 'DeliverySat'){
                        day = 'Saturday';
                    }
                    form.value.pickupSlots.push({
                        value: '',
                        day: day,
                        key: pickupDay.key,
                        label: 'Select '+pickupDay.longName+' slot',
                    });
                }
            }
            watch(()=>form.value.deliveryByday, (cur_val, pre_val)=>{
                if(cur_val == '0'){
                    viewRecurring.value = false;
                    pickupDays.value = [];
                    form.value.pickupSlots = [];
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Cancel Customer Recurring...']);
                    axios.post('/save-customer-recurring',{
                        customerId: route.params.customer_id,
                        deliveryByday: cur_val,
                        pickupSlots: [],
                    }).then((res)=>{
                        if(res.data.success){
                            viewRecurring.value = true;
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                message: 'Done',
                                ttl: 5,
                                type: 'success'
                            });
                        }
                    }).catch((err)=>{
                        console.log(err);
                    }).finally(()=>{
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
                }
                if(pickupDays.value.length == 0 && cur_val == '1'){
                    viewRecurring.value = false;
                    if(form.value.postCode == ''){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Post Code', ttl:5, type:'danger' });
                    }else{
                        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Fetching timeslot...']);
                        axios.post('/get-recurring-booking-timeslot', { postCode: form.value.postCode}).then((res)=>{
                            timeslots.value = res.data.available_slots;
                            pickupDays.value = res.data.available_days;
                        }).catch((error)=>{
                            console.log(error);
                        }).finally(()=>{
                            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                        })
                    }
                }
            })
            const saveRecurring = ()=>{
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Updating Customer Recurring...']);
                axios.post('/save-customer-recurring',{
                    customerId: route.params.customer_id,
                    deliveryByday: parseInt(form.value.deliveryByday),
                    pickupSlots: form.value.pickupSlots,
                }).then((res)=>{
                    if(res.data.success){
                        viewRecurring.value = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: 'Customer Recurring updated',
                            ttl: 5,
                            type: 'success'
                        });
                    }
                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }
            const openPauseRecurringModal = ()=>{

                if((form.value.pauseDateTo != '' && (new Date(form.value.pauseDateTo) < new Date()))){       
                            form.value.pauseDateTo = ''
                }
                if((form.value.pauseDateFrom != '' && (new Date(form.value.pauseDateFrom) < new Date()))){       
                            form.value.pauseDateFrom = ''
                }
                pauseRecurringModal.value.showModal();
            }
            const closePauseRecurringModal = ()=>{
                pauseRecurringModal.value.closeModal();
            }
            const pauseRecurringFunc = ()=>{
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Pause Recurring...']);
                axios.post('/pause-customer-recurring',{
                    customerId: route.params.customer_id,
                    pauseDateTo: form.value.pauseDateTo,
                    pauseDateFrom: form.value.pauseDateFrom,
                }).then((res)=>{
                    if(res.data){
                        pauseRecurring.value = true;
                        pauseRecurringModal.value.closeModal();
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: 'Done',
                            ttl: 5,
                            type: 'success'
                        });
                    }
                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }
            const unpauseRecurringFunc = ()=>{
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Unpause Recurring...']);
                axios.post('/unpause-customer-recurring',{
                    customerId: route.params.customer_id,
                }).then((res)=>{
                    console.log('unpausing..');
                    if(res.data){
                        pauseRecurring.value = false;
                        form.value.pauseDateTo = '';
                        form.value.pauseDateFrom = '';
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: 'Done',
                            ttl: 5,
                            type: 'success'
                        });
                    }
                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }

            const fdate=(Ymd)=>{
                let datepart=Ymd.split('-');
                const date = new Date(datepart[0], datepart[1]-1, datepart[2]);  // 2009-11-10
                const month = date.toLocaleString('default', { month: 'long' });
                return `${parseInt(datepart[2])} ${month} ${datepart[0]}`;
            }

            function goCustomerView(customerId){
                window.location='/view-customer/'+customerId;
            }
            function selectrow(order){
                router.push('/checkout/'+ order.order_id);
            }
            function getCurDateTime(){

                let cur_dt = new Date();
                let dd = String(cur_dt.getDate()). padStart(2, '0');
                let mm = String(cur_dt.getMonth() + 1). padStart(2, '0');
                let yyyy = String(cur_dt.getFullYear());
                return yyyy+'-'+mm+'-'+dd;

            }

            return {
                fdate,
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
                cardBrand,
                AddCreditCardCustomer,
                add_payement,
                cardErrors,
                DeleteCreditCardCustomer,
                creditCardCustomer,
                current_user,
                addCustomerCredit,
                credit_to_add,
                setCustomerDiscount,
                validateAndSaveContactDetails,
                validateAndSaveContactAddress,
                customer_note_edit,
                saveCustomerNote,
                saveCustomerPreferences,
                saveDeliveryInstructions,
                delivery_instructions_edit,
                saveCommunication,
                viewRecurring,
                pauseRecurring,
                addPickupDay,
                pickupDays,
                timeslots,
                saveRecurring,
                pauseRecurringModal,
                getSlotDisplayByValue,
                pauseRecurringFunc,
                unpauseRecurringFunc,
                closePauseRecurringModal,
                openPauseRecurringModal,
                show_model_SubAccount,
                createSubAccount,
                billing_address_edit,
                validateAndSaveBillingAddress,
                toggleCreditCard,
                goCustomerView,
                selectrow,
                timeslot_def,
                getSlotDisplay,
                getCurDateTime
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
        min-height: 40px;
    }
    .w-75{
        width: 75%;
    }
    .w-55{
        width: 55%;
    }
    .w-25{
        width:25%;
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

.each-save-btn{
    color:#fff;
}

.each-save-btn:hover{
    background:#333;
    color:#fff;
}
.pickup-day{
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
    width: 38px;
    height: 38px;
    background: #E0E0E0;
    font-size: 14px;
    line-height: 17px;
    color: #47454B;
    cursor: pointer;
}
.pickup-day.active{
    color: white;
    background: #42A71E;
}
.bmodal-header{
    font:bold 22px "Gilroy";
    color:#F4003D;
    background:#FFEFED;
}
.form-control:disabled{
    background-color: #F8F8F8;
}
.link_account a{
    text-decoration : none
}
</style>
