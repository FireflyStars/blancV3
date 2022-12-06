<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color p-0" v-if="showcontainer">
            <div class="d-flex align-content-stretch align-items-stretch flex-row hmax">
                <side-bar></side-bar>
                <div class="w-100">
                    <bread-crumb :paths="paths"></bread-crumb>
                    <div class="page-fluid cust-page m-0">
                        <div class="page-header">
                            <h1 class="d-flex align-items-center m-0">
                                New Customer
                                <span class="d-flex align-items-center justify-content-center rounded-pill ms-3 mt-2"
                                    :class="{ 'b2c-icon': form.customerType == 'B2C', 'b2b-icon': form.customerType == 'B2B' }"
                                >
                                {{ form.customerType == 'B2B' ? 'B2B' : form.customerType == 'B2C' ? 'B2C' : '' }}
                                </span>
                            </h1>
                        </div>
                        <ul class="full-nav d-flex p-0 m-0">
                            <li class="full-nav-item title border-right col-3 d-flex align-items-center justify-content-center"
                                :class="{ active: step =='account_details'}"
                                @click="selectNav('account_details')"
                            >
                                <svg class="icon" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    v-if="step == 'payment' || step == 'preferences' || step == 'linked_account'"
                                >
                                    <circle cx="10.9058" cy="10" r="9" stroke="#42A71E" stroke-width="2"/>
                                    <g clip-path="url(#clip0_807_2682)">
                                        <path d="M10.6555 15.0751L4.58057 9.07506L6.23053 7.42505L10.6555 11.925L19.5806 2.92505L21.2305 4.57506L10.6555 15.0751Z" fill="#05944F"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_807_2682">
                                            <rect width="18" height="18" fill="white" transform="translate(3.90576)"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <svg v-if="step != 'payment' && step != 'preferences' && step != 'linked_account'" class="icon" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10.9058" cy="10" r="9" stroke="#47454B" stroke-width="2"/>
                                </svg>
                                Account details
                            </li>
                            <li class="full-nav-item title border-right col-3 d-flex align-items-center justify-content-center"
                                :class="{ active: step =='payment'}"
                                @click="selectNav('payment')"
                            >
                                <svg class="icon" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    v-if="step == 'preferences' || step == 'linked_account'"
                                >
                                    <circle cx="10.9058" cy="10" r="9" stroke="#42A71E" stroke-width="2"/>
                                    <g clip-path="url(#clip0_807_2682)">
                                        <path d="M10.6555 15.0751L4.58057 9.07506L6.23053 7.42505L10.6555 11.925L19.5806 2.92505L21.2305 4.57506L10.6555 15.0751Z" fill="#05944F"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_807_2682">
                                            <rect width="18" height="18" fill="white" transform="translate(3.90576)"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <svg v-if="step != 'preferences' && step != 'linked_account'" class="icon" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10.9058" cy="10" r="9" stroke="#47454B" stroke-width="2"/>
                                </svg>
                                Payment
                            </li>
                            <li class="full-nav-item title border-right col-3 d-flex align-items-center justify-content-center"
                                :class="{ active: step =='preferences'}"
                                @click="selectNav('preferences')"
                            >
                                <svg class="icon" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    v-if="step == 'linked_account'"
                                >
                                    <circle cx="10.9058" cy="10" r="9" stroke="#42A71E" stroke-width="2"/>
                                    <g clip-path="url(#clip0_807_2682)">
                                        <path d="M10.6555 15.0751L4.58057 9.07506L6.23053 7.42505L10.6555 11.925L19.5806 2.92505L21.2305 4.57506L10.6555 15.0751Z" fill="#05944F"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_807_2682">
                                            <rect width="18" height="18" fill="white" transform="translate(3.90576)"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <svg v-if="step != 'linked_account'" class="icon" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10.9058" cy="10" r="9" stroke="#47454B" stroke-width="2"/>
                                </svg>
                                Preferences
                            </li>
                            <li class="full-nav-item title col-3 d-flex align-items-center justify-content-center"
                                :class="{ active: step =='linked_account'}"
                                @click="selectNav('linked_account')"
                            >
                                <svg class="icon" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10.9058" cy="10" r="9" stroke="#47454B" stroke-width="2"/>
                                </svg>
                                Linked account(s)
                            </li>
                        </ul>
                        <transition name="list" appear v-if="step =='account_details'">
                            <div class="cust-page-content m-auto pt-5">
                                <div class="account-type mb-3">
                                    <select-options
                                        v-model="form.accountType"
                                        :options="[
                                            { display:'Main Account', value: 'Main' }
                                            // { display:'Sub Account', value: 'Sub' }
                                        ]"
                                        :classnames ="'bg-transparent border-none font-gilroy font-22 ps-0'"
                                        :name="'accountType'">
                                    </select-options>
                                </div>
                                <div class="page-section">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h3 class="title m-0">Contact details</h3>
                                        <div class="type-delivery">
                                            <select-options
                                                v-model="form.typeDelivery"
                                                :options="[
                                                    { display:'Marylebone', value: 'MARYLEBONE' },
                                                    { display:'Chelsea', value: 'CHELSEA' },
                                                    { display:'Notting Hill', value: 'NOTTING HILL' },
                                                    { display:'South Ken', value: 'SOUTH KEN' },
                                                    { display:'Delivery', value: 'DELIVERY' },
                                                ]"
                                                :placeholder="'Delivery Type'"
                                                :name="'typeDelivery'">
                                            </select-options>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3">
                                        <div class="w-55 d-flex justify-content-between">
                                            <div class="customer-type">
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
                                            <div class="customer-type">
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
                                        </div>
                                        <div class="customer-type w-45">
                                            <select-options
                                                v-model="form.CustomerPayemenProfile"
                                                :options="[
                                                    { display:'Pay As You Go', value: 0 },
                                                    { display:'On Account', value: 1 },
                                                ]"
                                                :label="'Customer payment profile'"
                                                :name="'CustomerPayemenProfile'">
                                            </select-options>
                                        </div>                                          
                                    </div>
                                    <div class="d-flex mt-3" v-if="form.customerType == 'B2B'">
                                        <div class="customer-company w-45 p-0 justify-content-between">
                                                <label class="form-label d-block m-0" for="email">Company legal name</label>
                                                <input  type="text" v-model="form.CompanyName" class="form-control custom-input" placeholder="Branch / Company name">
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3">
                                        <div class="customer-contact w-55 d-flex justify-content-between">
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="last_name">{{ form.customerType == 'B2C' ? 'Last name *' : 'Representative last name *' }}</label>
                                                <input type="text" v-model="form.lastName" class="form-control custom-input" placeholder="Last name">
                                            </div>
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="first_name">{{ form.customerType == 'B2C' ? 'First name ' : 'Representative first name' }}</label>
                                                <input type="text" v-model="form.firstName" class="form-control custom-input" placeholder="First name">
                                            </div>
                                        </div>
                                        <div class="customer-phone w-45">
                                            <div>
                                                <label>Phone Number</label>
                                            </div>
                                            <div class="d-flex">
                                                <div class="phone-country-code">
                                                    <select-options
                                                        v-model="form.phoneCountryCode"
                                                        :options="phoneCodesSorted"
                                                        :width = "'100px'"
                                                        :name="'phoneCountryCode'">
                                                    </select-options>
                                                </div>
                                                <div class="form-group ms-2">
                                                    <input type="text" v-model="form.phoneNumber" class="form-control custom-input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex">
                                        <div class="customer-email w-55 justify-content-between">
                                            <label class="form-label d-block m-0" for="email">{{ form.customerType == 'B2C' ? "Email" : form.accountType == 'Main' ? 'Representative email address' : 'Business email address' }}</label>
                                            <input type="text" v-model="form.email" class="form-control custom-input" placeholder="Email">
                                        </div>
                                        <div  class="customer-type w-45">
                                            <div class="form-group m-0 customer-type">
                                                <label class="form-label d-block m-0">Kiosk number</label>
                                                <input type="text" v-model="form.kioskNumber" class="form-control custom-input" placeholder="Enter kiosk card number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="page-section">
                                    <h3 class="title m-0">Address</h3>
                                    <div class="d-flex mt-3 justify-content-between">
                                        <div class="form-group m-0 col-5">
                                            <label for="post_code">{{ form.typeDelivery == 'DELIVERY' ? 'Search postcode *' : 'Search postcode' }}</label>
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
                                                <input type="text" ref="postcode" class="form-control custom-input" v-model="form.postCode" id="addressAutocompleteRef">
                                            </div>
                                        </div>
                                        <div class="form-group col-3 m-0">
                                            <label for="customer_city">{{ form.typeDelivery == 'DELIVERY' ? 'City *' : 'City' }}</label>
                                            <input type="text" v-model="form.city" class="form-control custom-input">
                                        </div>
                                        <div class="form-group col-3 m-0">
                                            <label for="customer_country">Country</label>
                                            <input type="text" v-model="form.country" class="form-control custom-input">
                                        </div>
                                    </div>
                                    <div class="w-55 mt-3">
                                        <div class="form-group mb-0">
                                            <label for="customer_address1">{{ form.typeDelivery == 'DELIVERY' ? 'Delivery address *' : 'Delivery address' }}</label>
                                            <input type="text" v-model="form.deliveryAddress1" placeholder="Address line 1" class="form-control custom-input">
                                            <input type="text" v-model="form.deliveryAddress2" placeholder="Address line 2" class="form-control custom-input mt-3">
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-3">
                                        <div class="w-55">
                                            <label>Charge Delivery</label>
                                            <p>Auto-charge delivery fees for any order value &lt; £25 (applies to this customer and any sub-accounts)</p>
                                        </div>
                                        <div class="col-2 d-flex justify-content-end">
                                            <SwitchBtn class="ms-auto" v-model="form.chargeDelivery"></SwitchBtn>
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
                                        <div class="form-group mb-0 payment-method">
                                            <select-options
                                                v-model="form.paymentMethod"
                                                :options="[
                                                    { display:'Credit Card', value: 'Credit Card' },
                                                    { display:'BACS', value: 'BACS' },
                                                ]"
                                                :placeholder="'Select'"
                                                :label="'Payment Method'"
                                                :name="'paymentMethod'">
                                            </select-options>
                                        </div>
                                        <transition name="list" appear >
                                            <div class="credit-card mt-5 d-flex justify-content-between" v-if="form.paymentMethod == 'Credit Card'">
                                                <div class="form-group col-3 cardholder mb-0">
                                                    <label for="">Cardholder name *</label>
                                                    <input type="text" placeholder="Name" v-model="form.cardHolderName" required class="form-control">
                                                </div>
                                                <div class="form-group col-4 carddetails mb-0">
                                                    <label for="">Card details *</label>
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
                                                <div class="form-group col-2 cardexpdate mb-0">
                                                    <label for="">Expiration date *</label>
                                                    <input type="text" ref="cardExpInput" placeholder="mm/yy" :class="{ 'error': cardErrors.cardExpiry}" v-model="form.cardExpDate" maxlength="10" class="form-control" v-cardformat:formatCardExpiry>
                                                    <div v-if="cardErrors.cardExpiry" class="error">
                                                        <small>{{ cardErrors.cardExpiry }}</small>
                                                    </div>
                                                </div>
                                                <div class="form-group col-2 cardexpdate mb-0">
                                                    <label for="">CVC *</label>
                                                    <input type="text" ref="cardCvcInput" :class="{ 'error': cardErrors.cardCvc}" placeholder="CVC" v-model="form.cardCVV" class="form-control" v-cardformat:formatCardCVC>
                                                    <div v-if="cardErrors.cardCvc" class="error">
                                                        <small>{{ cardErrors.cardCvc }}</small>
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group">
                                                    <button class="btn btn-success" @click="checkCard">Check Card</button>
                                                </div> -->
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                                <!-- <AccountImportModal ref="importModal"/> -->
                                <!-- <transition name="list" appear v-if="form.paymentMethod == 'BACS' || (form.alreadyLinkedToAccount && form.customerType == 'B2B')">
                                    <div class="invoice-details-panel">
                                        <h3 class="title d-flex">
                                            VAT Invoice details
                                            <CheckBox  v-if="form.accountType !='Master'" v-model="form.attachReceiptToVatInvoice" class="ms-5"><slot>Attach e-Receipts to VAT Invoice</slot></CheckBox>
                                        </h3>
                                        <div class="page-section bacs">
                                            <div class="form-group mb-0 company-legal-name">
                                                <label for="company_legal_name">Company legal name *</label>
                                                <input type="text" v-model="form.companyLegalName" class="form-control">
                                            </div>
                                            <div class="d-flex mt-3">
                                                <div class="customer-contact w-55 d-flex justify-content-between">
                                                    <div class="form-group m-0">
                                                        <label class="form-label d-block m-0" for="first_name">Company representative *</label>
                                                        <input type="text" v-model="form.companyRepLastName" class="form-control custom-input" placeholder="First name">
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label class="form-label d-block m-0" for="first_name">&nbsp;</label>
                                                        <input type="text" v-model="form.companyRepFirstName" class="form-control custom-input" placeholder="Last name">
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
                                                <div class="form-group">
                                                    <label>Email address 1 *</label>
                                                    <input type="email" v-model="form.invoiceEmail1" placeholder="Email" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label>Email address 2</label>
                                                    <input type="email" v-model="form.invoiceEmail2" placeholder="Email" class="form-control">
                                                </div>
                                               <KeepAlive>
                                                    <MultipleEmail v-model="form.invoiceEmails"></MultipleEmail>
                                                </KeepAlive>
                                            </div>
                                            <div class="w-55 mt-4">
                                                <label for="">Billing address *</label>
                                                <input type="text" v-model="form.companyAddress1" placeholder="Address line 1" class="form-control custom-input">
                                                <input type="text" v-model="form.companyAddress2" placeholder="Address line 2" class="form-control custom-input mt-3">
                                            </div>
                                            <div class="d-flex mt-3">
                                                <div class="w-55 d-flex justify-content-between">
                                                    <div class="form-group m-0">
                                                        <label for="post_code">Search postcode *</label>
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
                                                        <label for="customer_city">City *</label>
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
                                </transition> -->
                                <div class="discount-credit-panel" v-if="form.accountType !='Master'">
                                    <h3 class="title d-flex">Discounts 
                                        <!-- <CheckBox v-model="form.applyDiscountToSub" class="ms-5"><slot>Apply to sub-accounts</slot></CheckBox> -->
                                    </h3>
                                    <div class="page-section">
                                        <div class="form-group mb-0 payment-method">
                                            <label for="discount_credit">Discount Level</label>
                                            <div class="input-group">
                                                <span class="input-group-text fw-bold">%</span>
                                                <input :disabled="current_user && ![1,4].includes(current_user.role_id)" type="text" v-model="form.discountLevel" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                    <h3 v-if="current_user && (current_user.role_id == 1 || current_user.role_id == 4)" class="title d-flex">Credits 
                                        <!-- <CheckBox v-model="form.applyCreditToSub" class="ms-5"><slot>Apply to sub-accounts</slot></CheckBox> -->
                                    </h3>
                                    <div class="page-section" v-if="current_user && [1,4].includes(current_user.role_id)">
                                        <div class="form-group payment-method">
                                            <label for="discount_credit">Add credit</label>
                                            <div class="input-group">
                                                <span class="input-group-text fw-bold">£</span>
                                                <input :disabled="current_user && ![1,4].includes(current_user.role_id)" type="text" v-model="form.addCredit" class="form-control" placeholder="0.00">  
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
                                            <input type="text" v-model="form.companyLegalName" class="form-control">
                                        </div>
                                        <div class="d-flex mt-3">
                                            <div class="customer-contact w-55 d-flex justify-content-between">
                                                <div class="form-group m-0">
                                                    <label class="form-label d-block m-0" for="first_name">Invoice recipient*</label>
                                                    <input type="text" v-model="form.invoiceName" class="form-control custom-input" placeholder="Name">
                                                </div>
                                                <div class="form-group m-0">
                                                    <label class="form-label d-block m-0" for="first_name">&nbsp;</label>
                                                    <input type="text" v-model="form.invoiceFirstname" class="form-control custom-input" placeholder="First name">
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
                                        <div class="d-flex mt-3">
                                            <div class="customer-contact w-55 justify-content-between">
                                                    <label class="form-label d-block m-0" for="first_name">Invoice Email address 3</label>
                                                    <input type="text" v-model="form.invoiceAddressEmail3" class="form-control custom-input" placeholder="Invoice Email address 3">
                                            </div>
                                      
                                        </div>
                                        <div class="d-flex mt-3">
                                            <div class="customer-contact w-55 justify-content-between">
                                                    <label class="form-label d-block m-0" for="first_name">Invoice Email address 4</label>
                                                    <input type="text" v-model="form.invoiceAddressEmail4" class="form-control custom-input" placeholder="Invoice Email address 4">
                                            </div>
                                      
                                        </div>
                                        <!-- /////// -->
                                    </div>
                                </div>
                                <div class="billing-address-panel" v-if="form.accountType !='Sub' && form.CustomerPayemenProfile == 1">
                                <!-- <div class="invoice-details-panel" v-if="form.accountType !='Main' && form.CustomerPayemenProfile == 1 "> -->
                                    <h3 class="title d-flex">
                                        Billing address
                                    </h3>
                                    <div class="page-section bacs">
                                        <div class="form-group mb-0 company-legal-name">
                                            <label for="company_legal_name">Billing address *</label>
                                            <input type="text" v-model="form.billingAddress1" class="form-control" placeholder="Address line 1">
                                        </div>
                                        <div class="form-group mb-0 mt-3 company-legal-name">
                                            <input type="text" v-model="form.billingAddress2" class="form-control" placeholder="Address line 2">
                                        </div>

                                        <div class="d-flex mt-3">
                                            <div class="w-55 d-flex justify-content-between">
                                                <div class="form-group m-0">
                                                    <label for="post_code">Search postcode *</label>
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
                                                        <input type="text" ref="companyPostCode" class="form-control custom-input" v-model="form.billingPostcode" placeholder="postcode">
                                                    </div>
                                                </div>
                                                <div class="form-group m-0">
                                                    <label for="customer_city">City *</label>
                                                    <input type="text" v-model="form.billingCity" class="form-control custom-input" placeholder="city">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                        <h3 class="title mb-3">Recurring Booking</h3>
                                        <div class="page-section">
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <div class="col-8">
                                                        <h4 class="sub-title col-12">Recurring</h4>
                                                    </div>
                                                    <div class="col-3">
                                                        <switch-btn class="ms-auto" v-model="form.deliveryByday"></switch-btn>
                                                    </div>
                                                </div>
                                            </div>
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
                                                <h4 class="sub-title col-12">Pickup & delivery slots </h4>
                                                <div class="d-flex flex-wrap">
                                                    <div class="col-4 px-1 mt-2" v-for="(slot, index) in form.pickupSlots" :key="index">
                                                        <select-options class="text-capitalize"
                                                            v-model="slot.value"
                                                            :options="timeslots[slot.key]"
                                                            :placeholder="'Select'"
                                                            :label="slot.label"
                                                            :name="slot.label">
                                                        </select-options>                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
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
                                        </div>
                                    </div>                                    
                                    <div class="blocks">
                                        <h3 class="title mb-3">Delivery instructions</h3>
                                        <div class="page-section p-4">
                                            <div class="d-flex justify-content-between">
                                                <div class="payment-method">
                                                    <select-options 
                                                        :classnames="'bg-color'"
                                                        v-model="form.altTypeDelivery"
                                                        :options="[
                                                            { display:'Concierge', value: 'Concierge' },
                                                            { display:'Neighbour', value: 'Neighbour' },
                                                            { display:'Housekeeper', value: 'Housekeeper' },
                                                            { display:'N/A', value: 'N/A' },
                                                        ]"
                                                        :placeholder="'Select'"
                                                        :label="'Deliver to'"
                                                        :name="'delivery_to'">
                                                    </select-options>
                                                </div>
                                                <div class="form-group m-0 payment-method">
                                                    <label class="form-label d-block m-0" for="alt_name">Name</label>
                                                    <input type="text" v-model="form.altName" class="form-control custom-input bg-color" placeholder="Last name">
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="w-100">
                                                    <label>Phone Number</label>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="phone-country-code">
                                                        <select-options
                                                            :classnames="'bg-color'"
                                                            v-model="form.altPhoneCountryCode"
                                                            :options="phoneCodesSorted"
                                                            :width = "'100px'"
                                                            :name="'phoneCountryCode'">
                                                        </select-options>
                                                    </div>
                                                    <div class="form-group ms-2">
                                                        <input type="text" v-model="form.altPhoneNumber" class="form-control custom-input bg-color h-100">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="form-group m-0">
                                                    <label for="" class="form-label d-block m-0">Any customer instructions for our drivers</label>
                                                    <textarea v-model="form.altDriverInstruction" name="customer_note" rows="4" class="form-control bg-color"></textarea>
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
                                                <td valign=middle class="text-nowrap">{{ item.lastName }}, {{item.firstName}}</td>
                                                <td valign=middle class="text-nowrap fw-bold">{{ item.accountType }}</td>
                                                <td valign=middle class="text-nowrap">{{ formatPhone(item.phone) }}</td>
                                                <td valign=middle class="text-nowrap">{{ item.email }}</td>
                                                <td valign=middle class="text-nowrap">{{ item.date }}</td>
                                                <td valign=middle class="fw-bold text-nowrap">£ {{ item.spent }}</td>
                                                <td valign=middle>
                                                    <svg width="30" v-if="index !=0" height="30" fill="#47454B" @click="removeLinkedAccount(item.id)" class="unlink" version="1.1" viewBox="0 0 700 700" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                                        <g>
                                                        <path d="m452.2 362.38 56.953 56.953-19.824 19.824-74.871-74.871h-47.656v-32.312h15.68l-35.727-35.168h-63.676v-33.039h30.574l-35.449-35.449h-12.203c-13.785-0.042969-27.016 5.4102-36.77 15.148-9.75 9.7422-15.219 22.969-15.191 36.754 0.027344 13.781 5.5469 26.988 15.336 36.688 9.6133 9.8555 22.863 15.305 36.625 15.066h67.762v32.312h-67.762c-22.273 0.33203-43.676-8.6641-59.023-24.809-15.648-15.84-24.426-37.207-24.426-59.473s8.7773-43.633 24.426-59.473c11.098-11.586 25.414-19.586 41.102-22.961l-57.23-56.895 19.824-19.824 74.871 75.152h0.39062l32.312 32.312h-0.44922l35.449 35.449 33.32 33.039 35.449 35.449 30.465 30.07zm41.105-22.902c15.648-15.84 24.426-37.207 24.426-59.473s-8.7773-43.633-24.426-59.473c-15.398-16.043-36.793-24.934-59.023-24.527h-67.48v32.312h67.762-0.003906c13.785-0.042969 27.016 5.4102 36.77 15.148 9.75 9.7422 15.219 22.969 15.191 36.754-0.027343 13.781-5.5469 26.988-15.336 36.688-3.5586 3.5781-7.5898 6.6484-11.984 9.1289l23.297 23.238v0.003907c3.8672-2.9609 7.4844-6.2383 10.809-9.8008z"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="d-flex" v-if="form.accountType == 'Main'">
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
                                            Add Existing SubAccount
                                        </button>
                                        <Search ref="searchpanel" :customerID = "form.customerID" @selectedSubAccount="selectedSubAccount"/>
                                    </div>
                                    <div class="d-flex" v-else>
                                        <button @click="showSearchPanel(1)" class="border-btn add-existing-account d-flex justify-content-between align-items-center">
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
                                            Link to Master
                                        </button>
                                        <Search ref="searchpanel" @selectedSubAccount="selectedSubAccount"/>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <div class="cust-page-footer d-flex align-items-center justify-content-end">
                            <a class="btn-cancel" href="javascript:;" @click="cancel">Cancel</a>
                            <a class="btn-next" href="javascript:;" @click="next">{{ step == 'linked_account' ? 'Create Customer' : 'Next' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <new-sub-account-form :phoneCodesSorted="phoneCodesSorted" :form="form" :show_conf="show_model_SubAccount" @close="show_model_SubAccount=false"></new-sub-account-form>
</template>

<script>
    import BreadCrumb from '../layout/BreadCrumb'
    import SideBar from '../layout/SideBar'
    import { useRouter , useRoute } from 'vue-router';
    import {ref,onMounted, nextTick, watch, inject } from 'vue';
    import SelectOptions from '../test/SelectOptions';
    import MultipleEmail from '../test/MultipleEmail';
    import CheckBox from '../miscellaneous/CheckBox';
    import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
    import SwitchBtn from '../miscellaneous/SwitchNumberBtn';
    import Search from './Search';
    import AccountImportModal from './AccountImportModal';
    import Swal from 'sweetalert2';
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
    import { useStore } from 'vuex';
import axios from 'axios';
    export default {
        name: "NewCustomer",
        components:{
            BreadCrumb,
            SideBar,
            SelectOptions,
            CheckBox,
            SwitchBtn,
            MultipleEmail,
            Search,
            AccountImportModal,
            NewSubAccountForm   
        },
        setup(){
            const store = useStore();
            const show_model_SubAccount = ref(false);
            const form = ref({
                customerID: '',
                accountType: 'Main',
                customerType: 'B2C',
                typeDelivery: 'DELIVERY',
                programmeType: 'Standard',
                kioskNumber: '',
                CustomerPayemenProfile: 0,
                CompanyName: '',
                firstName: '',
                lastName: '',
                phoneCountryCode: '+44',
                phoneNumber: '',
                email: '',
                postCode: '',
                city: '',
                state: '',
                county: '',
                country: 'GB',
                customerLat: '',
                customerLon: '',
                deliveryAddress1: '',
                deliveryAddress2: '',
                chargeDelivery: 1,
                customerNote: '',
                acceptSMSMarketing: '1',
                acceptMarketing: '0',                
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
                companyPhoneCountryCode: '+44',
                companyPhoneNumber: '',
                invoiceEmail1: '',
                invoiceEmail2: '',
                invoiceAddressEmail1:'',
                invoiceAddressEmail2:'',
                invoiceAddressEmail3:'',
                invoiceAddressEmail4:'',
                invoiceFirstname:'',
                invoiceName:'',
                companyPostCode: '',
                companyCity: '',
                companyCounty: '',
                companyState: '',
                companyCountry: 'GB',
                companyLat: '',
                companyLon: '',
                companyAddress1: '',
                companyAddress2: '',
                discountLevel: '',
                applyDiscountToSub: false,
                creditAmount: 0,
                addCredit: 0,
                //billing address
                billingAddress1:'',
                billingAddress2:'',
                billingPostcode:'',
                billingCity:'',
                // preferences
                preferences: [],
                deliveryByday: '0',
                pickupSlots:[],
                altTypeDelivery: '',
                altName: '',
                altPhoneCountryCode: '+44',
                altPhoneNumber: '',
                altDriverInstruction: '',
                linkedAccounts: []
            })
            const router = useRouter();
            const route=useRoute();
            const step = ref('account_details');
            // const pickupAvailableDays = ref([]);
            const pickupDays = ref([]);
            const timeslots = ref([]);
            const current_user = ref({});

            const addPickupDay = (index)=>{
                let pickupDay = pickupDays.value[index];
                if(pickupDay.active){
                    pickupDays.value[index].active = false;
                    form.value.pickupSlots = form.value.pickupSlots.filter((item)=>{
                        return item.key != pickupDay.key;
                    });
                }else{
                    pickupDays.value[index].active = true;
                    form.value.pickupSlots.push({
                        value: '',
                        key: pickupDay.key,
                        label: 'Select '+pickupDay.longName+' slot',
                    });
                }
            }
            watch(()=>form.value.deliveryByday, (cur_val, pre_val)=>{
                if(cur_val == '0'){
                    pickupDays.value.forEach((item)=>{
                        item.active = false;
                    });
                    form.value.pickupSlots = [];
                }else{
                    if(form.value.postCode == ''){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Post Code', ttl:5, type:'danger' });
                        form.value.deliveryByday = '0';
                    }else{
                        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Fetching timeslot...']);
                        axios.post('/get-recurring-booking-timeslot', { postCode: form.value.postCode}).then((res)=>{
                            console.log(res.data);
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
            const searchpanel = ref(null);
            const postcode = ref(null);
            const companyPostCode = ref(null);
            const showcontainer=ref(false);
            const searchCustomer=ref(false);
            const cardFormat = inject('cardFormat');

            // const importModal = ref(null);
            const paths=ref([
                { name:'Customer', route:'Customer'},
                { name:'New Customer', route:'NewCustomer'}
            ]);
            const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
                            [item.value, item])).values()].sort((a, b)=>{
                    return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
            });
            const cardErrors = ref({});

            onMounted(()=>{
                axios.post('/get-current-user',{})
                    .then((res)=>{
                        current_user.value = res.data.user;
                    }).catch((err)=>{

                    }).finally(()=>{

                });
                axios.post('/get-customer-preferences').then((res)=>{
                    Object.keys(res.data).forEach((item)=>{
                        form.value.preferences.push({
                            data: res.data[item],
                            key: item,
                        })
                    })
                }).catch((error)=>{
                    console.log(error);
                })
                nextTick(()=>{
                    showcontainer.value = true;
                });
                if(localStorage.getItem('stepActived')){
                    step.value = localStorage.getItem('stepActived')
                    localStorage.removeItem('stepActived')
                    form.value = JSON.parse(localStorage.getItem('formData'));
                    localStorage.removeItem('formData');
                    if(localStorage.getItem('subCustomerInfo')){
                        form.value.linkedAccounts = [...form.value.linkedAccounts, JSON.parse(localStorage.getItem('subCustomerInfo'))]
                        if(form.value.linkedAccounts.length == 0){
                            form.value.linkedAccounts = [
                                { id: 0, name: form.value.firstName + ', '+ form.value.lastName, firstName:form.value.firstName, lastName: form.value.lastName, accountType: 'Main', phone: '', email: '', date: '', spent: '' }
                            ];
                        }
                        localStorage.removeItem('subCustomerInfo');
                    }
                }else{
                    form.value.linkedAccounts = [
                        { id: 0, name: '', accountType: 'Main',firstName:'' ,lastName:'', phone: '', email: '', date: '', spent: 0 }
                    ];
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
                }
            });
            // google address autocomplete for delivery address
            const setCustomerAddress = ( address_components )=>{
                form.value.deliveryAddress1 = [
                    ( address_components[0] && address_components[0].short_name ) || "",
                    ( address_components[1] && address_components[1].short_name) || "",
                    ( address_components[2] && address_components[2].short_name) || "",
                ].join(" ");                        
                address_components.forEach(component => {
                    const type = component.types[0];
                    if(type == "postal_code"){
                        form.value.postCode = component.long_name
                    }else if(type == "country"){
                        form.value.country = component.short_name
                    }else if(type == "locality"){
                        form.value.city = component.long_name
                    }else if(type == "administrative_area_level_1"){
                        form.value.state = component.long_name
                    }else if(type == "street_number"){
                    
                    }
                });
            }
            // google address autocomplete for company address
            const setCompanyAddress = ( address_components )=>{
                form.value.companyAddress1 = [
                    ( address_components[0] && address_components[0].short_name ) || "",
                    ( address_components[1] && address_components[1].short_name) || "",
                    ( address_components[2] && address_components[2].short_name) || "",
                ].join(" ");                      
                address_components.forEach(component => {
                    const type = component.types[0];
                    if( type == "postal_code" ){
                        form.value.companyPostCode = component.long_name
                    }else if(type == "country"){
                        form.value.companyCountry = component.short_name
                    }else if(type == "locality"){ // || component.types.includes("sublocality") || component.types.includes("sublocality_level_1")
                        form.value.companyCity = component.long_name
                    }else if(type == "administrative_area_level_1"){
                        form.value.companyState = component.long_name
                    }else if(type == "street_number"){
                          
                    }
                });
            }
            // cancel to create account
            const cancel = ()=>{
                router.push({
                    name: 'Customer'
                });
            }
            const formatPhone = (phoneString)=>{
                if(phoneString != "" && phoneString != undefined ){
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
            // set nav when you click tabs
            const selectNav = (nav)=>{
                if(step.value == 'account_details'){
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
                    if(form.value.customerType == 'B2B' && form.value.accountType == 'Main' && form.value.alreadyLinkedToAccount){
                        form.value.paymentMethod = 'BACS';
                    }
                    if(form.value.lastName == ''){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please Enter Last Name', ttl:5, type:'danger' });
                        return;
                    }
                    if(form.value.email != ''){
                        if( !emailValidation(form.value.email) ){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email address', ttl:5, type:'danger' });
                            return;
                        }
                    }
                    if(form.value.typeDelivery == 'DELIVERY'){
                        if(form.value.postCode == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Post Code', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.deliveryAddress1 == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter delivery Address', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.city == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter city', ttl:5, type:'danger' });
                            return;
                        }
                    }
                    step.value = nav;
                }else if( step.value == 'payment' ){
                    let error = false;
                    if(form.value.paymentMethod == 'Credit Card'){
                        if(form.value.cardHolderName == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Cardholder name', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.cardDetails == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter card number', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.cardExpDate == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter exp date', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.cardCvc == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter CVC', ttl:5, type:'danger' });
                            return;
                        }
                    }
                    if(form.value.CustomerPayemenProfile == 1 && form.value.accountType != "Sub" ){
                       //invoce details
                       if(form.value.invoiceAddressEmail1 != '' || form.value.invoiceAddressEmail2 != '' || form.value.invoiceAddressEmail3 != '' || form.value.invoiceAddressEmail4 != '' || form.value.invoiceFirstname != '' || form.value.invoiceName != '' ||  form.value.companyPhoneNumber != '' || form.value.companyLegalName){

                            if(form.value.invoiceAddressEmail1 != ''){
                                if( !emailValidation(form.value.invoiceAddressEmail1) ){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email for Email Address 1', ttl:5, type:'danger' });
                                        error = true;
                                        return;
                                }
                            }else{
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Email Address 1', ttl:5, type:'danger' });
                                error = true;
                            }  
                            
                            if(form.value.invoiceName == ''){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Invoice Recipient', ttl:5, type:'danger' });
                                error = true;
                            }
                   
                            if(form.value.invoiceAddressEmail2 != ''){
                                if( !emailValidation(form.value.invoiceAddressEmail2) ){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email for Email Address 2', ttl:5, type:'danger' });
                                        error = true;
                                        return;
                                }
                            } 
                            if(form.value.invoiceAddressEmail3 != ''){
                                if( !emailValidation(form.value.invoiceAddressEmail3) ){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email for Email Address 3', ttl:5, type:'danger' });
                                        error = true;
                                        return;
                                }
                            } 
                            if(form.value.invoiceAddressEmail4 != ''){
                                if( !emailValidation(form.value.invoiceAddressEmail4) ){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email for Email Address 4', ttl:5, type:'danger' });
                                        error = true;
                                        return;
                                }
                            } 
                        }

                        //Billing address
                        if(form.value.billingAddress1 != '' || form.value.billingAddress2 != '' || form.value.billingCity != '' || form.value.billingPostcode != '' ){
                        if(form.value.billingAddress1 == ''){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Address 1', ttl:5, type:'danger' });
                                error = true;
                                return;
                            }
                            if(form.value.billingCity == ''){
                                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter city ', ttl:5, type:'danger' });
                                    error = true;
                                    return;
                            }
                            if(form.value.billingPostcode == ''){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Postcode ', ttl:5, type:'danger' });
                                error = true;
                                return;
                            } 
                        }
                    }
                    if(!error)
                     step.value = nav;
                }else if( step.value == 'preferences' ){
                    let error = false;
                    if(form.value.pickupSlots.length){
                        form.value.pickupSlots.forEach((item, index)=>{
                            if(item.value == 0){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: item.label, ttl:5, type:'danger' });
                                error = true;
                            }
                        })
                    }
                    if(!error)
                        step.value = nav;
                }else{
                    step.value = nav;
                }
                if(nav == 'linked_account'){     
                    if(form.value.accountType == 'Main'){
                        form.value.linkedAccounts[0].accountType = 'Main';
                    }
                }
            }
            watch(()=>form.value.lastName, (cur_val, pre_val)=>{
                form.value.companyRepLastName = cur_val;
            })
            
            // move on to next step when you click next button
            const next = ()=>{
                if(step.value == 'account_details'){
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
                    if(form.value.customerType == 'B2B' && form.value.accountType == 'Main' && form.value.alreadyLinkedToAccount){
                        form.value.paymentMethod == 'BACS';
                    }
                    if(form.value.lastName == ''){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please Enter Last Name', ttl:5, type:'danger' });
                        return;
                    }                    
                    if(form.value.email != ''){
                        if( !emailValidation(form.value.email) ){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email address', ttl:5, type:'danger' });
                            return;
                        }
                    }
                    if(form.value.typeDelivery == 'DELIVERY'){
                        if(form.value.postCode == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Post Code', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.deliveryAddress1 == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter delivery Address', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.city == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter city', ttl:5, type:'danger' });
                            return;
                        }
                    }
                    step.value = 'payment';
                }else if( step.value == 'payment' ){
                    let error = false;
                    if(form.value.paymentMethod == 'Credit Card'){
                        if(form.value.cardHolderName == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Cardholder name', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.cardDetails == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter card number', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.cardExpDate == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter exp date', ttl:5, type:'danger' });
                            return;
                        }
                        if(form.value.cardCvc == ''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter CVC', ttl:5, type:'danger' });
                            return;
                        }
                    }
                    if(form.value.CustomerPayemenProfile == 1 && form.value.accountType != "Sub" ){
                       //invoce details
                       if(form.value.invoiceAddressEmail1 != '' || form.value.invoiceAddressEmail2 != '' || form.value.invoiceFirstname != '' || form.value.invoiceName != '' ||  form.value.companyPhoneNumber != '' || form.value.companyLegalName){

                            if(form.value.invoiceAddressEmail1 != ''){
                                if( !emailValidation(form.value.invoiceAddressEmail1) ){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email for Email Address 1', ttl:5, type:'danger' });
                                        error = true;
                                        return;
                                }
                            }else{
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Email Address 1', ttl:5, type:'danger' });
                                error = true;
                            }  
                            
                            if(form.value.invoiceName == ''){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Invoice Recipient', ttl:5, type:'danger' });
                                error = true;
                            }
                   
                            if(form.value.invoiceAddressEmail2 != ''){
                                if( !emailValidation(form.value.invoiceAddressEmail2) ){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email for Email Address 2', ttl:5, type:'danger' });
                                        error = true;
                                        return;
                                }
                            }
                            if(form.value.invoiceAddressEmail3 != ''){
                                if( !emailValidation(form.value.invoiceAddressEmail3) ){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email for Email Address 2', ttl:5, type:'danger' });
                                        error = true;
                                        return;
                                }
                            } 
                            if(form.value.invoiceAddressEmail4 != ''){
                                if( !emailValidation(form.value.invoiceAddressEmail4) ){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email for Email Address 2', ttl:5, type:'danger' });
                                        error = true;
                                        return;
                                }
                            } 
                        }

                        //Billing address
                        if(form.value.billingAddress1 != '' || form.value.billingAddress2 != '' || form.value.billingCity != '' || form.value.billingPostcode != '' ){
                        if(form.value.billingAddress1 == ''){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Address 1', ttl:5, type:'danger' });
                                error = true;
                                return;
                            }
                            if(form.value.billingCity == ''){
                                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter city ', ttl:5, type:'danger' });
                                    error = true;
                                    return;
                            }
                            if(form.value.billingPostcode == ''){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter Postcode ', ttl:5, type:'danger' });
                                error = true;
                                return;
                            } 
                        }
                    }

                    if(!error)
                     step.value = 'preferences';
                }else if( step.value == 'preferences' ){
                    let error = false;                   
                    if(form.value.pickupSlots.length){
                        form.value.pickupSlots.forEach((item, index)=>{
                            if(item.value == 0){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: item.label, ttl:5, type:'danger' });
                                error = true;
                            }
                        })
                    }
                    if(form.value.accountType == 'Main'){
                        form.value.linkedAccounts[0].accountType = 'Main';
                    }
                    if(!error)
                        step.value = 'linked_account';
                }else{
                    createCustomer();
                }
            }
            const emailValidation = (email)=>{
                var re =  /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/g;
                return re.test(email);
            }
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
            // showing extra panel when the payment method changes
            watch(()=>form.value.paymentMethod,(current_value, previous_value)=>{
                if(current_value == 'BACS'){
                    nextTick(() => {
                        // importModal.value.openModal();
                        const companyAddress = new google.maps.places.Autocomplete(companyPostCode.value,
                            { 
                                componentRestrictions: { country: "uk" },
                                fields: ["address_components", "geometry"],
                            }                            
                        );
                        companyAddress.addListener("place_changed", () => {
                            const place = companyAddress.getPlace();
                            form.value.companyLat = place.geometry.location.lat();
                            form.value.companyLon = place.geometry.location.lng();
                            setCompanyAddress(place.address_components);
                        });
                    });
                }
            })
            // updating for linked_account tab when firstName and lastName changes
            watch(()=>form.value.firstName, (cur_val, pre_val)=>{
                form.value.linkedAccounts[0].name = cur_val + ', '+ form.value.lastName;
                form.value.linkedAccounts[0].firstName = cur_val;
            });
            watch(()=>form.value.lastName, (cur_val, pre_val)=>{
                form.value.linkedAccounts[0].name = form.value.firstName + ', '+ cur_val;
                form.value.linkedAccounts[0].lastName = cur_val;
            });
            watch(()=>form.value.email, (cur_val, pre_val)=>{
                form.value.linkedAccounts[0].email = cur_val;
            });
            // handler when you click a create sub account button
            const createSubAccount = ()=>{
                show_model_SubAccount.value = true;
                
            }

            // handler for when you click a create customer
            const createCustomer =()=>{
                axios.post('/check-customer-unique', form.value).then((res)=>{
                    if(res.data.unique){
                        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Creating Customer...']);
                        axios.post('/create-customer', form.value).then((response)=>{
                            if(response.data.error){
                                if(Array.isArray(response.data.error)){
                       
                                    Object.values(response.data.error).forEach((item)=>{
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: item[0], ttl:5, type:'danger' });
                                    })
                                }else{
                                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: response.data.error, ttl:5, type:'danger' });
                                }
                            }else{
                                       if(route.params.name  == "NewOrder" ){
                                            router.push({
                                                name: 'NewOrder',
                                                params:{
                                                    customer_id: response.data
                                                }
                                            });
                                        } else {
                                            router.push({
                                                name: 'CustomerDetail',
                                                params:{
                                                    customer_id: response.data
                                                }
                                            });
                                        }
                                
                            }
                        }).catch((errors)=>{
                            console.log(errors);
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: errors.response.data.message, ttl:5, type:'danger' });
                        }).finally(()=>{
                            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                        })
                    }else{
                        Swal.fire({
                            title: 'Duplicated Info',
                            text: "We find the same name on customer",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#42A71E',
                            cancelButtonColor: '#E8581B',
                            cancelButtonText: 'No',        
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Creating Customer...']);
                                axios.post('/create-customer', form.value).then((response)=>{
                                    if(response.data.error){
                                 
                                        if(Array.isArray(response.data.error)){
                                            Object.values(response.data.error).forEach((item)=>{
                                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: item[0], ttl:5, type:'danger' });
                                            })
                                        }else{
                                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: response.data.error, ttl:5, type:'danger' });
                                        }
                                    }else{

                                        if(route.params.name  == "NewOrder" ){
                                            router.push({
                                                name: 'NewOrder',
                                                params:{
                                                    customer_id: response.data
                                                }
                                            });
                                        } else {
                                            router.push({
                                                name: 'CustomerDetail',
                                                params:{
                                                    customer_id: response.data
                                                }
                                            });
                                        }
                                    }
                                }).catch((errors)=>{
                                    console.log(errors);
                                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: errors.response.data.message, ttl:5, type:'danger' });
                                }).finally(()=>{
                                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                                })                                
                            }
                        })    
                    }
                }).catch((error)=>{

                })

            }
            // handler when you click search customer button
            const showSearchPanel = (type)=>{
                if(form.value.accountType == 'Main'){
                    searchpanel.value.openSearchPanel(type);
                }
            }
            // handler when the customer selected in search result
            const selectedSubAccount = (data)=>{
                if(form.value.accountType == 'Main'){
                    form.value.linkedAccounts = [...form.value.linkedAccounts,
                        {
                            id:     data.id,
                            name:   data.name,
                            firstName : data.firstName,
                            lastName : data.lastName,
                            accountType: data.accountType,
                            phone:  data.phone,
                            email:  data.email,
                            date:   data.date,
                            spent:  data.spent,
                            customerId:  data.customerId,
                        }
                    ]
                }
            };
            // handler when you unlink sub account from linked accounts
            const removeLinkedAccount = (id)=>{
                form.value.linkedAccounts = form.value.linkedAccounts.filter((item)=>{
                    return item.id != id;
                });
            }
            const checkCard = ()=>{
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Check credit card...']);
                axios.post('check-stripe', form.value).then((res)=>{
                    console.log(res.data)
                }).catch((error)=>{
                    console.log(error);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })
            }
            return {
                form,
                step,
                showcontainer,
                searchCustomer,
                paths,
                postcode,
                companyPostCode,
                phoneCodesSorted,
                cardErrors,
                pickupDays,
                timeslots,
                // importModal,
                searchpanel,
                createSubAccount,
                createCustomer,
                selectNav,
                addPickupDay,
                next,
                cancel,
                removeLinkedAccount,
                showSearchPanel,
                selectedSubAccount,
                checkCard,
                formatPhone,
                show_model_SubAccount,
                current_user
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
    .unlink{
        cursor: pointer;
    }
    .unlink:hover{
        fill: #42A71E;
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

.form-control:focus{
    box-shadow: none !important;
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
      .b2b-icon{
        background: #4E58E7;
      }
  }
}
.full-nav{
  height: 70px;
  border-top: 1px solid #C3C3C3;
  .full-nav-item{
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
    // padding: 1.875rem 5rem 1.875rem;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
    margin-bottom: 30px;
    // input[type="text"]{

    // }
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
.input-group-text{
    background-color: white;
    border-right: none;
}
.input-group{
    input{
        border-left: none;
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
.form-control:disabled{
    background-color: #F8F8F8;
}
</style>
