<template>
    <div class="order-item-header d-flex justify-content-between cursor-pointer mt-2" @click="show = !show">
        <div class="order-num d-flex align-items-center">
            <span class="order-pay" :class="Order.paid"></span>
            <span class="ms-3">Order {{ Order.order_id }} ({{Order.Status.toLowerCase()}})</span>
        </div>
        <div class="order-date-panel d-flex align-items-center">
            <span class="order-status">{{ Order.order_text }}:</span>
            <span class="order-date ms-1 me-2">
                {{ Order.order_date }}
            </span>
            <svg v-if="show" width="16" height="8" viewBox="0 0 16 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.6996 7.71894C15.8918 7.54879 16 7.31749 16 7.07732C16 6.83627 15.8919 6.60497 15.6996 6.43481L8.73403 0.265873C8.54176 0.0957178 8.28141 0 8.00904 0C7.73667 0 7.47632 0.0957132 7.28406 0.265873L0.318501 6.43481C0.118228 6.6032 0.00306129 6.83715 6.19888e-05 7.08177C-0.00294113 7.32636 0.105208 7.5621 0.30148 7.73491C0.496747 7.90773 0.763116 8.00344 1.03949 7.9999C1.31586 7.99637 1.57923 7.89444 1.7695 7.7172L8.00907 2.18986L14.2486 7.7172C14.4409 7.88735 14.7013 7.98395 14.9736 7.98395C15.246 7.98484 15.5073 7.88913 15.6996 7.71897L15.6996 7.71894Z" fill="#F8F8F8"/>
            </svg>
            <svg v-else width="16" height="8" viewBox="0 0 16 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.300417 0.281057C0.108154 0.451213 0 0.682512 0 0.922681C0 1.16373 0.108149 1.39503 0.300417 1.56519L7.26597 7.73413C7.45824 7.90428 7.71859 8 7.99096 8C8.26333 8 8.52368 7.90429 8.71594 7.73413L15.6815 1.56519C15.8818 1.3968 15.9969 1.16285 15.9999 0.918234C16.0029 0.673641 15.8948 0.437896 15.6985 0.265086C15.5033 0.0922713 15.2369 -0.00344372 14.9605 9.53674e-05C14.6841 0.00363445 14.4208 0.105556 14.2305 0.282804L7.99093 5.81014L1.75136 0.282804C1.5591 0.112648 1.29875 0.0160456 1.02638 0.0160456C0.754004 0.0151596 0.49268 0.110895 0.300417 0.281057Z" fill="#F8F8F8"/>
            </svg>
        </div>
    </div>
    <div class="order-item-body" v-if="show">
        <div class="order-brief-info-section mt-2">
            <div class="d-flex">
                <div class="col-6">
                    <p class="order-sub-title m-0">
                        {{ Order.order_left_text }} <span v-if="Order.left_edit" class="ms-2 cursor-pointer text-underline">Edit</span>
                    </p>
                    <p class="m-0">{{ Order.order_left_date }}</p>
                    <p class="m-0">{{ Order.order_left_time }}</p>
                </div>
                <div class="col-6 ps-5 border-left">
                    <p class="order-sub-title m-0">
                        {{ Order.order_right_text }} <span v-if="Order.right_edit" class="ms-2 cursor-pointer text-underline">Edit</span>
                    </p>
                    <p class="mb-0">{{ Order.order_right_date }}</p>
                    <p class="mb-0">{{ Order.order_right_time }}</p>
                </div>
            </div>
        </div>
        <div v-if="show && type_order != 'scheduledOrders'">
        <div  class="sub-order-section mt-2" v-for="(subOrder, index) in subOrders" :key="index">
            <div class="sub-order-header">
                <label class="custom-checkbox">Sub order {{ getSubOrder(subOrder).key }}
                    <input type="checkbox" @change="subOrderCheck($event, getSubOrder(subOrder).id)">
                    <span class="checkmark"></span>
                </label>
            </div>
            <div v-if="type_order != 'scheduledOrders'" class="sub-order-body">
                <table class="table border-none">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Brand</th>
                            <th>Barcode</th>
                            <th>Location</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in subOrder" :key="index">
                            <td valign=middle>
                                <div class="d-flex align-items-center" v-if="item.colors != ''">
                                    <span class="d-block color-icon me-2"    v-for="(color, index) in item.colors.split(',')" :key="index" :style="{ 'background': color.toLowerCase().trim() }"></span>
                                    {{ item.item_name }}
                                </div>
                            </td>
                            <td valign=middle>{{ item.brand ? item.brand : "--" }}</td>
                            <td valign=middle>{{ item.barcode }}</td>
                            <td valign=middle>
                                <div class="location-icon rounded-pill d-flex align-items-center" :style="{'background-color': item.location_color}">
                                    <svg v-if="item.process == 1" class="me-2" width="12" height="12" viewBox="0 0 12 12" :fill="'#'+item.circle_color" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+item.circle_color"/>
                                        <circle cx="6" cy="6" r="5" :stroke="'#'+item.circle_color" stroke-width="2"/>
                                    </svg>
                                    <svg v-else class="me-2" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+item.circle_color"/>
                                        <circle cx="6" cy="6" r="5" :stroke="'#'+item.circle_color" stroke-width="2"/>
                                    </svg>
                                    <span>{{ item.location }}</span>
                                </div>
                            </td>
                            <td valign=middle v-if="item.underquote">
                                <div class="d-flex align-items-center">
                                    <span class="price underquote">£{{ item.price }}</span>
                                    <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="8.0459" cy="8.15381" r="5.5" stroke="#F4003D"/>
                                        <path d="M7.62983 9.72293C7.63617 9.34883 7.68689 9.04764 7.782 8.81938C7.87711 8.59111 8.0816 8.32322 8.39547 8.01569C8.70933 7.705 8.91065 7.48624 8.99942 7.35943C9.09136 7.22945 9.16111 7.08837 9.20866 6.93619C9.25939 6.78084 9.28475 6.60489 9.28475 6.40832C9.28475 6.01203 9.17854 5.70134 8.96613 5.47624C8.75689 5.24797 8.46363 5.13384 8.08636 5.13384C7.70591 5.13384 7.39839 5.24322 7.16378 5.46197C6.93235 5.67756 6.81346 5.97082 6.80712 6.34175H6.24121C6.24755 5.82181 6.42351 5.40649 6.76907 5.0958C7.11464 4.78193 7.55374 4.625 8.08636 4.625C8.63166 4.625 9.06124 4.7851 9.37511 5.10531C9.69214 5.42234 9.85066 5.85351 9.85066 6.39881C9.85066 6.74121 9.77298 7.063 9.61764 7.36419C9.46229 7.6622 9.16745 8.01094 8.73311 8.4104C8.37486 8.71159 8.19573 9.1491 8.19573 9.72293H7.62983ZM7.55374 11.3113C7.55374 11.2035 7.58703 11.1131 7.6536 11.0402C7.72335 10.9641 7.82005 10.9261 7.94369 10.9261C8.06416 10.9261 8.15927 10.9641 8.22902 11.0402C8.30194 11.1131 8.3384 11.2035 8.3384 11.3113C8.3384 11.4159 8.30194 11.5047 8.22902 11.5776C8.15927 11.6473 8.06416 11.6822 7.94369 11.6822C7.82005 11.6822 7.72335 11.6473 7.6536 11.5776C7.58703 11.5047 7.55374 11.4159 7.55374 11.3113Z" fill="#F4003D"/>
                                    </svg>
                                </div>
                            </td>
                            <td valign=middle v-else>
                                <span class="price">£{{ item.price.toFixed(2) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
    <div class="order-item-footer mt-2" v-if="show && type_order != 'scheduledOrders'">
        <div class="d-flex justify-content-between">
            <div class="col-6 d-flex">
                <button class="border-btn" @click="redirectToCheckOut(Order.order_id)">View Order</button>
                <button class="border-btn ms-2" @click="openModal">Print Ticket(s)</button>
            </div>
            <div class="col-6 text-end">
                <button class="fullfil-btn" @click="FulfillSubOrder">
                    Fulfill Sub-order(s)
                </button>
            </div>
        </div>
    </div>
    <FulfillConfirmation  :show_conf="show_model_Fulfil" @close="show_model_Fulfil=false"></FulfillConfirmation>
    <qz-print ref="qz_printer"></qz-print>
</template>
<script>
import { ref } from 'vue';
import QzPrint from "../QzPrint";
import { useRouter} from "vue-router";
import FulfillConfirmation from "../miscellaneous/FulfillConfirmation";
export default {
    name: 'OrderItem',
    components:{
        QzPrint,
        FulfillConfirmation
    },
    setup(props){
        const router = useRouter();
        const show = ref(false);
        const Order = ref({});
        const selectedSubOrders = ref([]);
        const show_model_Fulfil = ref(false)
        show.value = props.show;
        const type_order = ref('');
        type_order.value = props.type_order
        if(props.type_order == 'scheduledOrders'){
           Order.value = Object.values(Object.values(props.subOrders)[0])[0];
        }else {
          Order.value = Object.values(Object.values(props.subOrders)[0])[0];
        }
        

        const getSubOrder = (subOrder)=>{
            var subOrderValue = {
                id: '',
                key: '',
            };
            Object.values(subOrder).forEach(item => {
                subOrderValue.id = item.sub_order_id;
                subOrderValue.key = item.sub_order;
            });
            return subOrderValue;
        }
        const subOrderCheck = (event, subOrderId)=>{
            if(event.target.checked)
                selectedSubOrders.value.push(subOrderId)
            else
                selectedSubOrders.value = selectedSubOrders.value.filter(item=> { return item != subOrderId } );
        }
        function redirectToCheckOut(order_id){
         router.push('/checkout/'+order_id);
       }
       function FulfillSubOrder(){
        //show_model_Fulfil.value = true
        //console.log("selectedSubOrders." , selectedSubOrders.value , show_model_Fulfil.value)
       }
        return {
            show,
            Order,
            getSubOrder,
            subOrderCheck,
            redirectToCheckOut,
            FulfillSubOrder,
            show_model_Fulfil,
            type_order
        }
    },
    methods:{
        openModal(){
            this.$refs.qz_printer.loadPrinterModal(this.selectedSubOrders , "")
        }
    },
    props:{
        show: {
            type: Boolean,
            default: false,
        },
        subOrders: Object,
        type_order : String

    }
}
</script>
<style lang="scss" scoped>
    .order-item-header{
        padding: 5px 10px;
        background: #000000;
        opacity: 0.6;
        border-radius: 5px;
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
        .order-num{
            span{
                font-size: 14px;
                line-height: 140%;
                color: #F8F8F8;
                text-transform: capitalize;
            }
        }
        .order-status,
        .order-date{
            font-size: 12px;
            line-height: 140%;
            align-items: flex-end;
            color: #F8F8F8;
        }
        .order-date{

        }
    }
    .order-item-body{
        .order-brief-info-section{
            padding: 15px 30px;
            background: #FFFFFF;
            box-shadow: 0px 0px 6px rgba(153, 153, 153, 0.25);
            border-radius: 5px;
            p{
                font-family: 'Gotham Rounded Book';
                font-size: 12px;
                line-height: 14px;
                span{
                    font-size: 8px;
                    line-height: 10px;
                    color: #42A71E;
                }
            }
            .order-sub-title{
                font-family: 'Gotham Rounded';
                font-size: 12px;
                line-height: 14px;
                color: #000000;
            }
            .border-left{
                border-left: 1px solid #E0E0E0;
            }
        }
        .sub-order-section{
            padding: 10px;
            background: #EEEEEE;
            border-radius: 6px;
            .sub-order-header{
                .custom-checkbox{
                    display: block;
                    position: relative;
                    padding-left: 20px;
                    cursor: pointer;
                    font-family: "Gotham Rounded";
                    font-size: 12px;
                    line-height: 140%;
                    color: #47454B;
                    -webkit-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    user-select: none;
                    input {
                        position: absolute;
                        opacity: 0;
                        cursor: pointer;
                        height: 0;
                        width: 0;
                    }
                    .checkmark {
                        position: absolute;
                        top: 3px;
                        left: 0;
                        height: 12px;
                        width: 12px;
                        border-radius: 1px;
                        background: #F8F8F8;
                        border: 1px solid #C3C3C3;
                    }
                    /* When the checkbox is checked, add a blue background */
                    input:checked ~ .checkmark {
                        background-color: #47454B;
                        border: none;
                    }

                    /* Create the checkmark/indicator (hidden when not checked) */
                    .checkmark:after {
                        content: "";
                        position: absolute;
                        display: none;
                    }

                    /* Show the checkmark when checked */
                    input:checked ~ .checkmark:after {
                        display: block;
                    }
                    .checkmark:after {
                        left: 4px;
                        top: 1px;
                        width: 5px;
                        height: 8px;
                        border: solid white;
                        border-width: 0 2px 2px 0;
                        -webkit-transform: rotate(45deg);
                        -ms-transform: rotate(45deg);
                        transform: rotate(45deg);
                    }
                }
            }
            .sub-order-body{
                .table > :not(caption) > * > *{
                    border-bottom-width: 0;
                }
                .table > :not(:first-child){
                    border-top: none;
                }
                th,
                td{
                    font-family: 'Gotham Rounded Book';
                    font-style: normal;
                    font-size: 12px;
                    line-height: 140%;
                    color: #47454B;
                }
                td{
                    font-family: 'Gotham Rounded';
                    .price{
                        font-family: 'Gotham Rounded';
                        font-style: normal;
                        font-size: 12px;
                        line-height: 140%;
                        color: #47454B;
                    }
                    .price.underquote{
                        color: #C3C3C3;
                    }
                }
                tr{
                    border: none;
                }
                .color-icon{
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    border: 1px solid #F8F8F8;
                    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
                }
                .location-icon{
                    padding: 5px 16px 5px 8px;
                    width: fit-content;
                }
            }
        }
    }
    .order-item-footer{
        .border-btn{
            width: 117px;
            border: 1px solid #47454B;
            border-radius: 4px;
            font-family: 'Gotham Rounded';
            font-size: 12px;
            line-height: 140%;
            color: #000000;
            background: #FFFFFF;
            &:hover{
                background:  #47454B;
                color: #FFFFFF;
                transition: all .3s ease-in;
            }
        }
        .fullfil-btn{
            width: 180px;
            padding: 6px;
            font-family: 'Gotham Rounded';
            font-size: 12px;
            line-height: 140%;
            color: #42A71E;
            border: 1px solid #42A71E;
            border-radius: 4px;
            background: white;
            &:hover{
                background:  #42A71E;
                color: #FFFFFF;
                transition: all .3s ease-in;
            }
        }
    }
</style>
