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
                                            { display:'Main Account', value: 'Main' }, 
                                            { display:'Master Account', value: 'Master' },
                                            { display:'Sub Account', value: 'Sub' }
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
                                                    { display:'Marylebone', value: 'Marylebone' }, 
                                                    { display:'Chelsea', value: 'Chelsea' },
                                                    { display:'Notting Hill', value: 'Notting Hill' },
                                                    { display:'Delivery', value: 'Delivery' },
                                                ]"
                                                :placeholder="'Delivery Type'"
                                                :name="'typeDelivery'">
                                            </select-options>
                                        </div>
                                    </div>
                                    <div class="customer-type mt-3">
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
                                    <div class="d-flex mt-3">
                                        <div class="customer-contact w-55 d-flex justify-content-between">
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="first_name">{{ form.customerType == 'B2C' ? 'Contact' : 'Company representative' }}</label>
                                                <input type="text" v-model="form.firstName" class="form-control custom-input" placeholder="First name">
                                            </div>
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="first_name">&nbsp;</label>
                                                <input type="text" v-model="form.lastName" class="form-control custom-input" placeholder="Last name">
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
                                    <div class="customer-email w-55 mt-3">
                                        <div class="form-group m-0">
                                            <label class="form-label d-block m-0" for="email">Email</label>
                                            <input type="text" v-model="form.email" class="form-control custom-input" placeholder="Email">
                                        </div>
                                    </div>
                                </div>
                                <div class="page-section">
                                    <h3 class="title m-0">Address</h3>
                                    <div class="d-flex mt-3">
                                        <div class="w-55 d-flex justify-content-between">
                                            <div class="form-group m-0">
                                                <label for="post_code">Search postcode</label>
                                                <input type="text" v-model="form.postCode" class="form-control custom-input">
                                            </div>
                                            <div class="form-group m-0">
                                                <label for="customer_city">City</label>
                                                <input type="text" v-model="form.city" class="form-control custom-input">
                                            </div>
                                        </div>
                                        <div class="ps-5">
                                            <div class="form-group m-0">
                                                <label for="customer_country">Country</label>
                                                <input type="text" v-model="form.country" class="form-control custom-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-55 mt-3">
                                        <div class="form-group mb-0">
                                            <label for="customer_address1">Delivery address</label>
                                            <input type="text" v-model="form.deliveryAddress1" placeholder="Address line 1" class="form-control custom-input">
                                            <input type="text" v-model="form.deliveryAddress1" placeholder="Address line 2" class="form-control custom-input mt-3">
                                        </div>
                                    </div>
                                </div>
                                <div class="page-section">
                                    <h3 class="title m-0">Customer Notes</h3>
                                    <div class="w-75 mt-3">
                                        <textarea v-model="form.customerNote" name="customer_note" rows="4" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <transition name="list" appear v-if="step =='payment'">
                            <div class="payment cust-page-content m-auto pt-5">
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
                                                <label for="">Cardholder name</label>
                                                <input type="text" placeholder="Name" v-model="form.cardHolderName" required class="form-control">
                                            </div>
                                            <div class="form-group col-4 carddetails mb-0">
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
                                            <div class="form-group col-2 cardexpdate mb-0">
                                                <label for="">Expiration date</label>
                                                <input type="text" ref="cardExpInput" placeholder="mm/yy" :class="{ 'error': cardErrors.cardExpiry}" v-model="form.cardExpDate" maxlength="10" class="form-control" v-cardformat:formatCardExpiry>
                                                <div v-if="cardErrors.cardExpiry" class="error">
                                                    <small>{{ cardErrors.cardExpiry }}</small>
                                                </div>
                                            </div>
                                            <div class="form-group col-2 cardexpdate mb-0">
                                                <label for="">CVV</label>
                                                <input type="text" ref="cardCvcInput" :class="{ 'error': cardErrors.cardCvc}" placeholder="CVV" v-model="form.cardCVV" class="form-control" v-cardformat:formatCardCVC>
                                                <div v-if="cardErrors.cardCvc" class="error">
                                                    <small>{{ cardErrors.cardCvc }}</small>
                                                </div>                                                
                                            </div>
                                        </div>
                                    </transition>
                                </div>
                                <transition name="list" appear v-if="form.paymentMethod == 'BACS'">
                                    <div>
                                        <h3 class="title d-flex">
                                            VAT Invoice details
                                            <CheckBox v-model="form.sameAsMaster" class="ms-5"><slot>Same as master</slot></CheckBox>
                                            <CheckBox v-model="form.sameAsContactDetails" class="ms-5"><slot>Same as contact details</slot></CheckBox>
                                            <CheckBox v-model="form.receiptToVatInvoice" class="ms-5"><slot>Attach e-Receipts to VAT Invoice</slot></CheckBox>
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
                                                        <input type="text" v-model="form.companyPostCode" class="form-control custom-input">
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
                                </transition>
                                <h3 class="title d-flex">Discounts <CheckBox v-model="form.applyDiscountToSub" class="ms-5"><slot>Apply to sub-accounts</slot></CheckBox></h3>
                                <div class="page-section">
                                    <div class="d-flex">
                                        <div class="col-6">
                                            <div class="form-group mb-0 payment-method">
                                                <select-options 
                                                    v-model="form.discountLevel" 
                                                    :options="[ 
                                                        { display:'Credit Card', value: 'Credit Card' }, 
                                                        { display:'BACS', value: 'BACS' },
                                                    ]"
                                                    :placeholder="'Select'"
                                                    :label="'Discount level'"
                                                    :name="'discountLevel'">
                                                </select-options>                                    
                                            </div>                                    
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group col-6">
                                                <label for="discount_credit">Add credit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text fw-bold">£</span>
                                                    <input type="text" v-model="form.discountCredit" class="form-control" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="title d-flex">Credits <CheckBox v-model="form.applyCreditToSub" class="ms-5"><slot>Apply to sub-accounts</slot></CheckBox></h3>
                                <div class="page-section">
                                    <div class="form-group col-3">
                                        <label for="discount_credit">Add credit</label>
                                        <div class="input-group">
                                            <span class="input-group-text fw-bold">£</span>
                                            <input type="text" v-model="form.CreditsCredit" class="form-control" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <transition name="list" appear v-if="step =='preferences'">
                            <div class="cust-page-content preferences m-auto pt-5">
                                <div class="d-flex justify-content-between mt-3">
                                    <div class="blocks">
                                        <h3 class="title mb-3">Finish instructions</h3>
                                        <div class="page-section">
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Shirts with crease</h4>
                                                    <switch-btn v-model="form.shirtsWithCrease"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">Customer wants shirts always pressed with creases on the sleeves</p>
                                            </div>
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Shirts folded</h4>
                                                    <switch-btn v-model="form.shirtsFolded"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">
                                                    Customer wants shirts always folded
                                                </p>
                                            </div>
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Debobbling</h4>
                                                    <switch-btn v-model="form.debobbling"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">
                                                    Customer wants jumpers and coats fully debobbled
                                                </p>
                                            </div>
                                            <div class="item-block py-3">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Minor repairs (up to £20)</h4>
                                                    <switch-btn v-model="form.minorRepairs"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">
                                                    Customer authorises us to proceed with making minor repairs such as small tears, missing buttons or moth holes
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="blocks">
                                        <h3 class="title mb-3">Authorisations</h3>
                                        <div class="page-section">
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Cleaning partner</h4>
                                                    <switch-btn v-model="form.cleaningPartner"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">
                                                    Customer authorises us to send items out to a partner.
                                                </p>
                                            </div>
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Risky clean</h4>
                                                    <switch-btn v-model="form.riskyClean"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">
                                                    Customer authorises us to proceed if a risk has been identified with one of his items - return unprocessed.
                                                </p>
                                            </div>
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Tailoring Approval</h4>
                                                    <switch-btn v-model="form.tailoringApproval"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">
                                                    Customer authorises us to proceed if a risk has been identified with one of his items - return unprocessed.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <div class="blocks">
                                        <h3 class="title mb-3">Communication</h3>
                                        <div class="page-section">
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Marketing</h4>
                                                    <switch-btn v-model="form.marketingEmail"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">
                                                    Customer agrees to receiving Marketing emails from us
                                                </p>
                                            </div>
                                            <div class="item-block py-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="sub-title">Bi-Monthly VAT Invoices</h4>
                                                    <switch-btn v-model="form.VATEmail"></switch-btn>
                                                </div>
                                                <p class="m-0 col-8">
                                                    Customer wishes to receive monthly email VAT receipts
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="blocks">
                                        <h3 class="title mb-3">Delivery instructions</h3>
                                        <div class="page-section p-4">
                                            <div class="d-flex justify-content-between">
                                                <div class="payment-method">
                                                    <select-options 
                                                        v-model="form.altTypeDelivery" 
                                                        :options="[ 
                                                            { display:'Marylebone', value: 'Marylebone' }, 
                                                            { display:'Chelsea', value: 'Chelsea' },
                                                            { display:'Notting Hill', value: 'Notting Hill' },
                                                            { display:'Delivery', value: 'Delivery' },
                                                        ]"
                                                        :placeholder="'Select'"
                                                        :label="'Deliver to'"
                                                        :name="'delivery_to'">
                                                    </select-options>
                                                </div>
                                                <div class="form-group m-0 payment-method">
                                                    <label class="form-label d-block m-0" for="alt_name">Name</label>
                                                    <input type="text" v-model="form.altName" class="form-control custom-input" placeholder="Last name">
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="w-100">
                                                    <label>Phone Number</label>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="phone-country-code">
                                                        <select-options 
                                                            v-model="form.altPhoneCountryCode" 
                                                            :options="phoneCodesSorted"
                                                            :width = "'100px'"
                                                            :name="'phoneCountryCode'">
                                                        </select-options>
                                                    </div>
                                                    <div class="form-group ms-2">
                                                        <input type="text" v-model="form.altPhoneNumber" class="form-control custom-input">
                                                    </div>
                                                </div>                                            
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="form-group m-0">
                                                    <label for="" class="form-label d-block m-0">Any customer instructions for our drivers</label>
                                                    <textarea v-model="form.altDriverInstruction" name="customer_note" rows="4" class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <transition name="list" appear v-if="step =='linked_account'">
                            <div class="cust-page-content m-auto pt-5">
                                <div class="page-section">Linked accounts</div>
                                <div class="page-section"></div>
                                <div class="page-section"></div>
                            </div>
                        </transition>
                        <div class="cust-page-footer d-flex align-items-center justify-content-end">
                            <a class="btn-cancel" href="javascript:;" @click="cancel">Cancel</a>
                            <a class="btn-next" href="javascript:;" @click="next">Next</a>
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
    import { useRouter, useRoute } from 'vue-router';
    import {ref,onMounted, nextTick, computed, watch, inject } from 'vue';
    import SelectOptions from '../test/SelectOptions';
    import MultipleEmail from '../test/MultipleEmail';
    import CheckBox from '../miscellaneous/CheckBox';
    import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
    import SwitchBtn from '../miscellaneous/SwitchBtn.vue';

    import {
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        TOASTER_GET_ALL,
        TOASTER_REMOVE_TOAST,
        LOADER_MODULE,
        DISPLAY_LOADER,
        HIDE_LOADER,
    } from "../../store/types/types";
    export default {
        name: "NewOrder",
        components:{
            BreadCrumb,
            SideBar,
            SelectOptions,
            CheckBox,
            SwitchBtn,
            MultipleEmail
        },
        setup(props,context){
            const form = ref({
                accountType: 'Main',
                customerType: '',
                typeDelivery: '',
                firstName: '',
                lastName: '',
                phoneCountryCode: '+44',
                phoneNumber: '',
                email: '',
                postCode: '',
                city: '',
                country: '',
                deliveryAddress1: '',
                deliveryAddress2: '',
                customerNote: '',
                // payment tab
                paymentMethod: '',
                cardHolderName: '',
                cardDetails: '',
                cardExpDate: '',
                cardCVV: '',
                sameAsMaster: false,
                sameAsContactDetails: false,
                receiptToVatInvoice: false,
                companyLegalName: '',
                companyRepFirstName: '',
                companyRepLastName: '',
                companyPhoneCountryCode: '+44',
                companyPhoneNumber: '',
                companyEmails: [],
                companyPostCode: '',
                companyCity: '',
                companyCountry: '',
                companyAddress1: '',
                companyAddress2: '',
                discountLevel: '',
                applyDiscountToSub: false,
                discountCredit: '',
                applyCreditToSub: false,
                CreditsCredit: '',
                // preferences tab
                shirtsWithCrease: false,
                shirtsFolded: false,
                debobbling: false,
                minorRepairs: true,
                marketingEmail: false,
                VATEmail: false,
                cleaningPartner: true,
                riskyClean: false,
                tailoringApproval: false,

                altTypeDelivery: '',
                altName: '',
                altPhoneCountryCode: '+44',
                altPhoneNumber: '',
                altDriverInstruction: '',
            })
            const router = useRouter();
            const step = ref('account_details');
            const showcontainer=ref(false);
            const cardFormat = inject('cardFormat');
            onMounted(()=>{
                nextTick(()=>{
                    showcontainer.value=true;
                });
            });
            const selectNav = (nav)=>{
                step.value = nav;
            }
            const cancel = ()=>{
                alert('it is not implemented yet. :)')
            }
            const next = ()=>{
                if(step.value == 'account_details')
                    step.value = 'payment';
                else if( step.value == 'payment' )
                    step.value = 'preferences';
                else if( step.value == 'preferences' )
                    step.value = 'linked_account';
            }
            const paths=ref([
                { name:'Customer', route:'Customer'},
                { name:'New Customer', route:'NewCustomer'}
            ]);
            const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
                            [item.value, item])).values()].sort((a, b)=>{
                    return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
            });
            const cardErrors = ref({});
            watch(()=>form.value.cardDetails,(current_value, previous_value)=>{
                if(cardFormat.validateCardNumber(current_value)){
                    delete cardErrors.value.cardNumber;
                }else{
                    cardErrors.value.cardNumber = "Invalid Credit Card Number.";
                }
            })
            watch(()=>form.value.cardExpDate,(current_value, previous_value)=>{
                if(cardFormat.validateCardExpiry(current_value)){
                    delete cardErrors.value.cardExpiry;
                }else{
                    cardErrors.value.cardExpiry = "Invalid Expiration Date.";
                }
            })
            watch(()=>form.value.cardCVV,(current_value, previous_value)=>{
                if(cardFormat.validateCardExpiry(current_value)){
                    delete cardErrors.value.cardCvc;
                }else{
                    cardErrors.value.cardCvc = "Invalid Expiration Date.";
                }
            })
            return {
                form,
                step,
                showcontainer,
                paths,
                selectNav,
                next,
                cancel,
                phoneCodesSorted,
                cardErrors
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
