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
                                            <label class="form-label d-block m-0" for="email">{{ form.customerType == 'B2C' ? "Email" : form.accountType == 'Main' ? 'Representative email address' : 'Business email address' }}</label>
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
                                </div>
                                <transition name="list" appear v-if="form.paymentMethod == 'BACS' || (form.alreadyLinkedToAccount && form.customerType == 'B2B')">
                                    <div class="invoice-details-panel">
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
                                <div class="discount-credit-panel" v-if="form.accountType !='Master'">
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
                                <h3 class="title mb-3">Linked account(s)</h3>
                                <div class="account-list-section">
                                    <table class="table table-hover linked-account-table bg-white">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Contact</th>
                                                <th class="text-nowrap">Account type</th>
                                                <th class="text-nowrap">Discount level</th>
                                                <th class="text-nowrap">Spend limit</th>
                                                <th class="text-nowrap">Credit amount</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td valign=middle class="text-nowrap">Salvador, Henri</td>
                                                <td valign=middle class="text-nowrap fw-bold">Master</td>
                                                <td valign=middle></td>
                                                <td valign=middle></td>
                                                <td valign=middle></td>
                                                <td valign=middle>
                                                    <svg @click="removeLinkedAccount(1)" class="trash" width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16.6354 2.6936C16.3165 2.34182 15.835 2.15596 15.1456 2.1256H11.7314C11.7401 1.58256 11.7002 0.814981 11.2594 0.358425C11.0345 0.12391 10.7293 0 10.3784 0H6.70345C6.32757 0 6.01904 0.119337 5.78993 0.355099C5.35375 0.800844 5.3367 1.51478 5.34876 2.1256H1.91544C1.13497 2.1256 0.676751 2.25575 0.380281 2.56095C-0.0330309 2.98632 -0.0151513 3.61669 0.012292 4.57013C0.0210239 4.84997 0.0301717 5.16474 0.0301717 5.51692C0.0301717 5.74645 0.215621 5.93273 0.445978 5.93273H1.55868L2.38447 19.5683C2.39819 19.7878 2.58031 19.9587 2.79944 19.9587H14.2782C14.4978 19.9587 14.6799 19.7878 14.6932 19.5687L15.5194 5.93315H16.615C16.8425 5.93315 17.0275 5.75061 17.0308 5.52316C17.0333 5.31359 17.0446 5.10486 17.0554 4.9007C17.0995 4.05245 17.1419 3.25244 16.6354 2.6936ZM6.38411 0.936397C6.42819 0.892321 6.50927 0.831613 6.70345 0.831613H10.3784C10.5414 0.831613 10.6145 0.887332 10.6619 0.935149C10.8836 1.16509 10.9064 1.73599 10.9002 2.12519H6.18037C6.17247 1.7019 6.17954 1.14596 6.38411 0.936397ZM13.8869 19.1271H3.19155L2.39195 5.93315H14.6865L13.8869 19.1271ZM16.2246 4.85704C16.2204 4.93729 16.2163 5.01962 16.2125 5.10153H1.95037H0.858043C0.854716 4.90236 0.849311 4.71816 0.844737 4.54601C0.823531 3.83 0.808978 3.31398 0.976964 3.14017C1.09672 3.01751 1.40358 2.95763 1.91669 2.95763C6.48765 2.95763 10.9405 2.95763 15.1273 2.95722C15.5655 2.97718 15.8579 3.07364 16.0196 3.25327C16.2932 3.55515 16.2599 4.18717 16.2246 4.85704ZM11.4416 17.4115C11.4416 17.4032 11.4416 17.3944 11.4424 17.3861L12.0287 7.71072C12.0428 7.48161 12.2441 7.30572 12.469 7.32069C12.6894 7.334 12.8595 7.51737 12.8595 7.73525C12.8595 7.74399 12.8591 7.7523 12.8586 7.76103L12.2724 17.4364C12.2586 17.6564 12.0757 17.8265 11.8574 17.8265C11.8491 17.8265 11.8403 17.826 11.832 17.8256C11.6112 17.8123 11.4416 17.629 11.4416 17.4115ZM4.13709 7.76852C4.13626 7.75979 4.13626 7.75147 4.13626 7.74274C4.13626 7.52485 4.30591 7.34107 4.52671 7.32776C4.75582 7.31071 4.95291 7.4891 4.96704 7.71779L5.55333 17.3919C5.55375 17.4007 5.55416 17.409 5.55416 17.4181C5.55416 17.6356 5.38452 17.8194 5.16372 17.8323C5.15499 17.8331 5.14667 17.8331 5.13753 17.8331C4.92006 17.8331 4.73627 17.6635 4.72338 17.4431L4.13709 7.76852ZM8.12302 17.419L8.11553 7.64253C8.11512 7.41217 8.30098 7.22672 8.53093 7.22589H8.53134C8.76128 7.22589 8.94673 7.41176 8.94715 7.64128L8.95463 17.4181C8.95546 17.6481 8.7696 17.834 8.53883 17.834C8.30972 17.834 8.12385 17.6473 8.12302 17.419Z" fill="#868686"/>
                                                    </svg>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign=middle class="text-nowrap">Salvador, Henri</td>
                                                <td valign=middle class="text-nowrap">Sub</td>
                                                <td valign=middle>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control text-center" placeholder="Discount level">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td valign=middle>
                                                    <div class="input-group">
                                                        <span class="input-group-text">£</span>
                                                        <input type="text" class="form-control text-center" placeholder="0.00">
                                                    </div>
                                                </td>
                                                <td valign=middle>
                                                    <div class="input-group">
                                                        <span class="input-group-text">£</span>
                                                        <input type="text" class="form-control text-center" placeholder="0.00">
                                                    </div>
                                                </td>
                                                <td valign=middle>
                                                    <svg @click="removeLinkedAccount(2)" class="trash" width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16.6354 2.6936C16.3165 2.34182 15.835 2.15596 15.1456 2.1256H11.7314C11.7401 1.58256 11.7002 0.814981 11.2594 0.358425C11.0345 0.12391 10.7293 0 10.3784 0H6.70345C6.32757 0 6.01904 0.119337 5.78993 0.355099C5.35375 0.800844 5.3367 1.51478 5.34876 2.1256H1.91544C1.13497 2.1256 0.676751 2.25575 0.380281 2.56095C-0.0330309 2.98632 -0.0151513 3.61669 0.012292 4.57013C0.0210239 4.84997 0.0301717 5.16474 0.0301717 5.51692C0.0301717 5.74645 0.215621 5.93273 0.445978 5.93273H1.55868L2.38447 19.5683C2.39819 19.7878 2.58031 19.9587 2.79944 19.9587H14.2782C14.4978 19.9587 14.6799 19.7878 14.6932 19.5687L15.5194 5.93315H16.615C16.8425 5.93315 17.0275 5.75061 17.0308 5.52316C17.0333 5.31359 17.0446 5.10486 17.0554 4.9007C17.0995 4.05245 17.1419 3.25244 16.6354 2.6936ZM6.38411 0.936397C6.42819 0.892321 6.50927 0.831613 6.70345 0.831613H10.3784C10.5414 0.831613 10.6145 0.887332 10.6619 0.935149C10.8836 1.16509 10.9064 1.73599 10.9002 2.12519H6.18037C6.17247 1.7019 6.17954 1.14596 6.38411 0.936397ZM13.8869 19.1271H3.19155L2.39195 5.93315H14.6865L13.8869 19.1271ZM16.2246 4.85704C16.2204 4.93729 16.2163 5.01962 16.2125 5.10153H1.95037H0.858043C0.854716 4.90236 0.849311 4.71816 0.844737 4.54601C0.823531 3.83 0.808978 3.31398 0.976964 3.14017C1.09672 3.01751 1.40358 2.95763 1.91669 2.95763C6.48765 2.95763 10.9405 2.95763 15.1273 2.95722C15.5655 2.97718 15.8579 3.07364 16.0196 3.25327C16.2932 3.55515 16.2599 4.18717 16.2246 4.85704ZM11.4416 17.4115C11.4416 17.4032 11.4416 17.3944 11.4424 17.3861L12.0287 7.71072C12.0428 7.48161 12.2441 7.30572 12.469 7.32069C12.6894 7.334 12.8595 7.51737 12.8595 7.73525C12.8595 7.74399 12.8591 7.7523 12.8586 7.76103L12.2724 17.4364C12.2586 17.6564 12.0757 17.8265 11.8574 17.8265C11.8491 17.8265 11.8403 17.826 11.832 17.8256C11.6112 17.8123 11.4416 17.629 11.4416 17.4115ZM4.13709 7.76852C4.13626 7.75979 4.13626 7.75147 4.13626 7.74274C4.13626 7.52485 4.30591 7.34107 4.52671 7.32776C4.75582 7.31071 4.95291 7.4891 4.96704 7.71779L5.55333 17.3919C5.55375 17.4007 5.55416 17.409 5.55416 17.4181C5.55416 17.6356 5.38452 17.8194 5.16372 17.8323C5.15499 17.8331 5.14667 17.8331 5.13753 17.8331C4.92006 17.8331 4.73627 17.6635 4.72338 17.4431L4.13709 7.76852ZM8.12302 17.419L8.11553 7.64253C8.11512 7.41217 8.30098 7.22672 8.53093 7.22589H8.53134C8.76128 7.22589 8.94673 7.41176 8.94715 7.64128L8.95463 17.4181C8.95546 17.6481 8.7696 17.834 8.53883 17.834C8.30972 17.834 8.12385 17.6473 8.12302 17.419Z" fill="#868686"/>
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
                                        <button @click="searchCustomer = !searchCustomer" class="border-btn add-existing-account d-flex justify-content-between align-items-center">
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
                                        <Search v-model="searchCustomer"/>
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
    export default {
        name: "NewCustomer",
        components:{
            BreadCrumb,
            SideBar,
            SelectOptions,
            CheckBox,
            SwitchBtn,
            MultipleEmail,
            Search
        },
        setup(props,context){
            const form = ref({
                accountType: 'Main',
                customerType: 'B2C',
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
                alreadyLinkedToAccount: true,
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
                linkedAccounts: [
                    { name: '', accountType: '', discountLevel: '', spendLimit: '', creditAmount: '' }
                ]
            })
            const router = useRouter();
            const step = ref('linked_account');
            const showcontainer=ref(false);
            const searchCustomer=ref(false);
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
                // router.back();
            }
            const next = ()=>{
                if(step.value == 'account_details'){
                    if(form.value.customerType == 'B2B' && form.value.accountType == 'Main' && form.value.alreadyLinkedToAccount){
                        form.value.paymentMethod = 'BACS';
                    }
                    step.value = 'payment';
                }else if( step.value == 'payment' ){
                    step.value = 'preferences';
                }else if( step.value == 'preferences' ){
                    step.value = 'linked_account';
                }
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
                    cardErrors.value.cardCvc = "Invalid CVV.";
                }
            })
            const removeLinkedAccount = (customerID)=>{
                alert("it's not done yet")
            }
            return {
                form,
                step,
                showcontainer,
                searchCustomer,
                paths,
                selectNav,
                next,
                cancel,
                removeLinkedAccount,
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
